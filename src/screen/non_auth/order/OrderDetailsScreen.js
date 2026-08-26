import React, { useEffect, useState, useCallback, useMemo, useRef } from 'react'
import { Image, Modal, ScrollView, Text, TextInput, TouchableOpacity, View, StyleSheet, Keyboard, TouchableWithoutFeedback } from 'react-native'
import { pick, types, isErrorWithCode, errorCodes } from '@react-native-documents/picker'
import SafeView from '../../../helper/SafeView'
import { Colors } from '../../../assets/Colors'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { moderateScale } from '../../../helper/Window'
import DataStorage from '../../../storage/DataStorage'
import { Icons } from '../../../assets/Icons'
import UrlStorage from '../../../storage/UrlStorage'
import ShipToSelfListPopupView from './popup/ShipToSelfListPopupView'
import DestinationAddressListPopupView from './popup/DestinationAddressListPopupView'
import ForTypeListPopupView from './popup/ForTypeListPopupView'
import Toast from 'react-native-toast-message'
import toastConfig from '../../../helper/ToastConfig'
import DumpListPopupView from './popup/DumpListPopupView'
import Loader from '../../../common/Loader'
import TruckListPopupView from './popup/TruckListPopupView'
import { getAllDataFrom_customer_masterSubDealer, getDestiList, getMySubDealerList } from '../../../storage/database/GetDataFromTable'
import { clearDestinationMaster } from '../../../storage/database/DeleteAllDataInTable'
import { insertDataIn_destination_master } from '../../../storage/database/InsertDataInTable'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const checkCustomerGSTStatus = async (customerCode) => {
    try {
        const url = `${UrlStorage.BaseUrlList.Saathi.base_url_saathi}/check_customer_gst_document.php?customer_id=${customerCode}`
        const response = await fetch(url)
        const result = await response.json()

        console.log(result);


        return {
            success: result?.status === 'YES',
            hasGST: result?.is_present === 'YES',
            gstNumber: result?.gst_no || '',
            uploadDoc: result?.upload_doc || ''
        }
    } catch (error) {
        return { success: false, hasGST: false }
    }
}

const uploadGSTDetails = async ({ customerCode, gstNumber, document }) => {
    try {
        const formData = new FormData()
        formData.append('customer_id', customerCode)
        formData.append('gst_no', gstNumber)

        if (document) {
            formData.append('upload_doc', {
                uri: document.uri,
                type: document.type || 'application/octet-stream',
                name: document.name || `gst_doc_${Date.now()}`
            })
        }

        const url = `${UrlStorage.BaseUrlList.Saathi.base_url_saathi}/upload_customer_gst_document.php`
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        })
        const result = await response.json()

        return {
            success: result?.status === 'YES' || result?.success === true
        }
    } catch (error) {
        return { success: false }
    }
}

const GST_REGEX = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/

// Valid GSTIN state codes (as per CBIC)
const VALID_STATE_CODES = [
    '01', '02', '03', '04', '05', '06', '07', '08', '09', '10',
    '11', '12', '13', '14', '15', '16', '17', '18', '19', '20',
    '21', '22', '23', '24', '25', '26', '27', '28', '29', '30',
    '31', '32', '33', '34', '35', '36', '37'//, '38', '97', '99'
    // 01-37 standard states/UTs, 38 = Ladakh, 97 = Other Territory, 99 = Centre Jurisdiction
]

// 4th character of PAN indicates holder type
const VALID_PAN_HOLDER_TYPES = ['P', 'C', 'H', 'F', 'A', 'T', 'B', 'L', 'J', 'G']

const GST_CHECKSUM_CODEPOINTS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'

const computeGSTChecksum = (gstinFirst14) => {
    let factor = 2
    let sum = 0
    const len = GST_CHECKSUM_CODEPOINTS.length

    for (let i = gstinFirst14.length - 1; i >= 0; i--) {
        const code = GST_CHECKSUM_CODEPOINTS.indexOf(gstinFirst14[i])
        let digit = factor * code
        digit = Math.floor(digit / len) + (digit % len)
        sum += digit
        factor = factor === 2 ? 1 : 2
    }

    const checksum = (len - (sum % len)) % len
    return GST_CHECKSUM_CODEPOINTS[checksum]
}

const validateGSTNumber = (value) => {
    if (!value || value.trim().length === 0) {
        return 'GSTIN number is required'
    }

    const gst = value.trim().toUpperCase()

    if (gst.length !== 15) {
        return 'GSTIN number must be 15 characters'
    }

    if (/\s/.test(value)) {
        return 'GSTIN number must not contain spaces'
    }

    if (!GST_REGEX.test(gst)) {
        return 'Please enter a valid GSTIN number'
    }

    // State code check (first 2 digits)
    const stateCode = gst.substring(0, 2)
    if (!VALID_STATE_CODES.includes(stateCode)) {
        return 'Invalid state code in GSTIN number'
    }

    // PAN embedded in GSTIN (characters 3-12)
    const pan = gst.substring(2, 12)
    const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]$/
    if (!panRegex.test(pan)) {
        return 'Invalid PAN structure within GSTIN number'
    }

    // 4th character of PAN = holder type, must be a known category
    // const holderType = pan[3]
    // if (!VALID_PAN_HOLDER_TYPES.includes(holderType)) {
    //     return 'Invalid PAN holder type in GSTIN number'
    // }

    // 14th character must always be 'Z'
    if (gst[13] !== 'Z') {
        return '14th character of GSTIN number must be Z'
    }

    // 13th character = entity/registration number, must be 1-9 or A-Z
    // if (!/[1-9A-Z]/.test(gst[12])) {
    //     return 'Invalid entity code in GSTIN number'
    // }

    // Checksum validation (15th character)
    // const expectedChecksum = computeGSTChecksum(gst.substring(0, 14))
    // if (gst[14] !== expectedChecksum) {
    //     return 'GSTIN number checksum is invalid'
    // }

    return ''
}

// Memoized Radio Button Component
const RadioButton = React.memo(({ selected, color }) => (
    <View style={[styles.radioOuter, { borderColor: selected ? color : "#d9d9d9" }]}>
        <View style={[styles.radioInner, { backgroundColor: selected ? color : "#d9d9d9" }]} />
    </View>
))

// Memoized Form Field Component
const FormField = React.memo(({ label, children }) => (
    <View style={styles.formField}>
        <Text style={styles.label}>{label}</Text>
        {children}
    </View>
))

// ============================================================
// GSTIN POPUPS
// ============================================================

// Popup 1 - GSTIN question (non-dismissible)
const GSTQuestionPopup = React.memo(({ visible, primaryColor, onYes, onNo, onBackPress, selected, cust_type, onYesDealer }) => (
    <Modal visible={visible} transparent animationType="fade" onRequestClose={() => { }} >
        <View style={styles.modalOverlay}>
            <TouchableOpacity style={StyleSheet.absoluteFill} activeOpacity={1} onPress={onBackPress} />
            <View style={styles.modalCard}>
                <Text style={styles.modalTitle}>GSTIN Verification</Text>
                {/* <Text style={styles.modalMessage}>
                    {selected == 1 ? "Is the GSTIN of (APOB) additional place of business  same?" : cust_type == null ? "Is the Sub-Dealer/RSAR GSTIN registered?" : 'Is the Ship-to-Party RSAR GSTIN registered?'}
                </Text> */}
                <Text style={styles.modalMessage}>
                    {selected == 1 ? "Is Additional Place of business the same as per GSTIN certificate?" : cust_type == null ? "Is the Sub-Dealer/RSAR GSTIN registered?" : 'Is the Ship-to-Party RSAR GSTIN registered?'}
                </Text>
                {/* <Text style={styles.modalMessage}>
                    {selected == 1 ? "Is the GSTIN of Additional Place of Business (APOB) same?" : cust_type == null ? "Is the Sub-Dealer/RSAR GSTIN registered?" : 'Is the Ship-to-Party RSAR GSTIN registered?'}
                </Text> */}
                <View style={styles.modalButtonRow}>
                    <TouchableOpacity activeOpacity={0.7} style={styles.modalButtonSecondary} onPress={() => {
                        if (selected == 1)
                            onYes()
                        else
                            onNo()
                    }}>
                        <Text style={styles.modalButtonSecondaryText}>NO</Text>
                    </TouchableOpacity>
                    <TouchableOpacity activeOpacity={0.7} style={[styles.modalButtonPrimary, { backgroundColor: primaryColor }]} onPress={() => {
                        if (selected == 1)
                            onYesDealer()
                        else
                            onYes()
                    }}>
                        <Text style={styles.modalButtonPrimaryText}>YES</Text>
                    </TouchableOpacity>
                </View>
            </View>
        </View>
    </Modal>
))

// Popup 2 - Enter GSTIN details (YES flow)
const GSTEntryPopup = React.memo(({
    visible,
    primaryColor,
    gstNumber,
    gstNumberError,
    gstDocument,
    submitting,
    onChangeGSTNumber,
    onPickDocument,
    onSubmit,
    onBackPress
}) => (
    <Modal visible={visible} transparent animationType="fade" onRequestClose={() => { }}>
        <View style={styles.modalOverlay}>
            <View style={styles.modalCard}>
                <Text style={styles.modalTitle}>Enter your GSTIN Details</Text>

                <View style={{ width: '100%', marginTop: moderateScale(12) }}>
                    <Text style={styles.label}>GSTIN Number</Text>
                    <View style={styles.inputContainer}>
                        <TextInput
                            placeholder="e.g. 22AAAAA0000A1Z5"
                            placeholderTextColor="#A7A7A7"
                            value={gstNumber}
                            autoCapitalize="characters"
                            maxLength={15}
                            style={styles.input}
                            onChangeText={onChangeGSTNumber}
                        />
                    </View>
                    {!!gstNumberError && <Text style={styles.errorText}>{gstNumberError}</Text>}
                </View>

                <View style={{ width: '100%', marginTop: moderateScale(16) }}>
                    <Text style={styles.label}>Upload GSTIN Document</Text>
                    <TouchableOpacity activeOpacity={0.7} style={styles.dropdownContainer} onPress={onPickDocument}>
                        <Text style={[styles.dropdownText, !gstDocument && styles.placeholderText]} numberOfLines={1}>
                            {gstDocument?.name || "Select PDF / JPG / PNG"}
                        </Text>
                    </TouchableOpacity>
                </View>
                <View style={styles.modalButtonRow}>
                    <TouchableOpacity activeOpacity={0.7} style={styles.modalButtonSecondary} onPress={onBackPress}>
                        <Text style={styles.modalButtonSecondaryText}>Back</Text>
                    </TouchableOpacity>
                    <TouchableOpacity activeOpacity={0.7} style={[styles.modalButtonPrimary, { backgroundColor: primaryColor }]} onPress={onSubmit} disabled={submitting}>
                        <Text style={styles.modalButtonPrimaryText}>Submit</Text>
                    </TouchableOpacity>
                </View>
            </View>
        </View>
    </Modal>
))

// Popup - GSTIN turnover declaration (NO flow)
const GSTDeclarationPopup = React.memo(({ visible, primaryColor, onConfirm, onBackPress }) => (
    <Modal visible={visible} transparent animationType="fade" onRequestClose={() => { }}>
        <View style={styles.modalOverlay}>
            <View style={styles.modalCard}>
                <Text style={styles.modalTitle}>Declaration</Text>
                <Text style={styles.modalMessage}>
                    I confirm that turnover is below the taxable limit as specified under GSTIN, 2017.
                </Text>
                <View style={styles.modalButtonRow}>
                    <TouchableOpacity activeOpacity={0.7} style={styles.modalButtonSecondary} onPress={onBackPress}>
                        <Text style={styles.modalButtonSecondaryText}>Back</Text>
                    </TouchableOpacity>
                    <TouchableOpacity activeOpacity={0.7} style={[styles.modalButtonPrimary, { backgroundColor: primaryColor }]} onPress={onConfirm}>
                        <Text style={styles.modalButtonPrimaryText}>Confirm</Text>
                    </TouchableOpacity>
                </View>
            </View>
        </View>
    </Modal>
))


// Popup - GSTIN turnover declaration (NO flow)
const GSTNotDeclarationForRSARPopup = React.memo(({ visible, primaryColor, onConfirm, onClose }) => (
    <Modal visible={visible} transparent animationType="fade" onRequestClose={onClose}>

        <TouchableOpacity activeOpacity={1} style={styles.modalOverlay} onPress={onClose}>
            <TouchableOpacity activeOpacity={1} style={styles.modalCard} onPress={() => { }}>

                <Text style={styles.modalTitle}>GSTIN Declaration Pending</Text>
                <Text style={styles.modalMessage}>
                    The GSTIN declaration for selected (Ship-to Party) has not yet submitted. Please ask the RSAR to complete the declaration in the App. Order placement will be enabled once the declaration is submitted.
                </Text>
                <View style={styles.modalButtonRow}>
                    <TouchableOpacity activeOpacity={0.7} style={[styles.modalButtonPrimary, { backgroundColor: primaryColor }]} onPress={onConfirm}>
                        <Text style={styles.modalButtonPrimaryText}>Confirm</Text>
                    </TouchableOpacity>
                </View>

            </TouchableOpacity>
        </TouchableOpacity>

    </Modal>
))
const GSTNotDeclarationForSeflPopup = React.memo(({ visible, primaryColor, onConfirm, onClose }) => (
    <Modal visible={visible} transparent animationType="fade" onRequestClose={onClose}>
        <TouchableOpacity activeOpacity={1} style={styles.modalOverlay} onPress={onClose}>
            <TouchableOpacity activeOpacity={1} style={styles.modalCard} onPress={() => { }}>
                <Text style={styles.modalTitle}>GSTIN Declaration Pending</Text>
                <Text style={styles.modalMessage}>
                    The GSTIN declaration for selected business or godown has not yet submitted. Please complete the GSTIN declaration in the App. Order placement will be enabled once the declaration is submitted.
                </Text>
                <View style={styles.modalButtonRow}>
                    <TouchableOpacity activeOpacity={0.7} style={[styles.modalButtonPrimary, { backgroundColor: primaryColor }]} onPress={onConfirm}>
                        <Text style={styles.modalButtonPrimaryText}>Confirm</Text>
                    </TouchableOpacity>
                </View>
            </TouchableOpacity>
        </TouchableOpacity>
    </Modal>
))

const OrderDetailsScreen = ({ route, navigation }) => {
    const scrollViewRef = useRef(null)
    const { selectedProducts } = route?.params || {}

    // State management
    const [dataSet, setDataSet] = useState([])
    const [selected, setSelected] = useState(0)
    const [loading, setLoading] = useState(false)
    const [freight, setFreight] = useState(0)

    // Lists
    const [shippingToList, setShippingToList] = useState([])
    const [destinationAddressList, setDestinationAddressList] = useState([])
    const [dumpDataList, setDumpDataList] = useState([])
    const [truckList, setTruckList] = useState([])
    const [plant, setPlant] = useState([])

    // Popup states
    const [popupStates, setPopupStates] = useState({
        shipToSelf: false,
        destinationAddress: false,
        dump: false,
        truck: false,
        forType: false
    })

    // Form data
    const [subDealersAvailable, setSubDealersAvailable] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)
    const [dealerSubDealerId, setDealerSubDealerId] = useState('')
    const [consigneeName, setConsigneeName] = useState('')
    const [consigneeAddress, setConsigneeAddress] = useState('')
    const [destinationAddress, setDestinationAddress] = useState('')
    const [destinationAddressCode, setDestinationAddressCode] = useState('')
    const [destinationAddressType, setDestinationAddressType] = useState('')
    const [phoneNo, setPhoneNumber] = useState('')
    const [deliveryRemarks, setDeliveryRemarks] = useState('')
    const [forType, setForType] = useState('')
    const [dumpName, setDumpName] = useState('')
    const [truckName, setTruckName] = useState('')
    const [dumpObj, setDumpObj] = useState({})
    const [shipItem, setShipItem] = useState({})
    const [code, setCode] = useState(UrlStorage.ParameterList.BasicData.emp_code)

    const pendingGSTItemRef = useRef(null)
    const [showGSTQuestionPopup, setShowGSTQuestionPopup] = useState(false)
    const [showGSTEntryPopup, setShowGSTEntryPopup] = useState(false)
    const [showGSTDeclarationPopup, setShowGSTDeclarationPopup] = useState(false)
    const [gstNumber, setGstNumber] = useState('')
    const [gstNumberError, setGstNumberError] = useState('')
    const [gstDocument, setGstDocument] = useState(null)
    const [isGSTINRequired, setIsGSTINRequired] = useState(false)
    const [showGSTNotDeclarationForRSARPopup, setShowGSTNotDeclarationForRSARPopup] = useState(false)
    const [showGSTNotDeclarationForDealerPopup, setShowGSTNotDeclarationForDealerPopup] = useState(false)

    // Memoized constants
    const forTypeList = useMemo(() => {
        const baseList = [{ id: 1, title: 'Multiple' }, { id: 2, title: 'Single' },]
        console.log(truckList);

        // Only add DOT if truck list is available
        if (truckList.length > 0) {
            baseList.push({ id: 3, title: 'DOT' })
        }

        return baseList
    }, [truckList.length])

    const resetOrderForm = useCallback(() => {
        setDealerSubDealerId('')
        setConsigneeName('')
        setConsigneeAddress('')
        setDestinationAddress('')
        setDestinationAddressCode('')
        setDestinationAddressType('')
        setPhoneNumber('')
        setDeliveryRemarks('')
        setForType('')
        setDumpName('')
        setTruckName('')
        setDumpObj({})
        setShipItem({})
        setFreight(0)
        setPlant([])
        setDumpDataList([])
        setTruckList([])
        setDestinationAddressList([])
        setDataSet([])
        closeAllPopups()
    }, [closeAllPopups])


    const isSBS = useMemo(() => DataStorage.typeOfUse === 1, [])
    const primaryColor = useMemo(() => DataStorage.primaryColorCode, [])

    useEffect(() => {
        const fetchInitialData = async () => {
            if (isSBS) {
                await requestForSbsShippingToSubDealer()
                await requestForSbsTruckList(shipItem?.customer_code || UrlStorage.ParameterList.BasicData.selectedCustomerCode)
            } else {
                console.log(UrlStorage.ParameterList.BasicData);
                console.log('3');
                await requestForCementShippingToSubDealer()
                if (UrlStorage.ParameterList.BasicData.user_type == 'broker') {
                    await requestForCementTruckList(shipItem?.SAP_code || UrlStorage.ParameterList.BasicData.customerDetails.SAP_code)
                } else {
                    await requestForCementTruckList(shipItem?.SAP_code || UrlStorage.ParameterList.BasicData.emp_id)
                }
            }
        }
        fetchInitialData()
    }, [isSBS])

    useEffect(() => {
        const handleFreightChange = async () => {
            setDumpName('')
            setTruckName('')

            if (freight === 1) {
                setPopupStates(prev => ({ ...prev, forType: true }))
                if (isSBS) {
                    await requestForSbsTruckList(shipItem?.customer_code || UrlStorage.ParameterList.BasicData.selectedCustomerCode)
                } else {
                    console.log('2', shipItem);
                    console.log('2', UrlStorage.ParameterList.BasicData);
                    if (UrlStorage.ParameterList.BasicData.user_type == 'broker') {
                        await requestForCementTruckList(shipItem?.SAP_code || UrlStorage.ParameterList.BasicData.customerDetails.SAP_code)
                    } else {
                        await requestForCementTruckList(shipItem?.SAP_code || UrlStorage.ParameterList.BasicData.emp_id)
                    }
                }
            } else if (freight === 2) {
                const customerCode = UrlStorage.ParameterList.BasicData.selectedCustomerCode
                if (isSBS) {
                    await requestForSbsDumpList(customerCode)
                } else {
                    await requestForCementDumpList(customerCode)
                }
            }
        }

        if (freight > 0) {
            handleFreightChange()
        }
    }, [freight, isSBS])

    useEffect(() => {
        requestForCheckGSTIN()
    }, [])

    const requestForCheckGSTIN = () => {
        const requestOptions = {
            method: "GET",
            redirect: "follow"
        };

        fetch("https://starsaathi.com/SAP/static_response_v1.php", requestOptions)
            .then((response) => response.json())
            .then((result) => {
                setIsGSTINRequired(result.process_message)
            })
            .catch((error) => console.error(error));
    }

    // Callbacks
    const clickOnShipToSelf = useCallback(async () => {
        resetOrderForm()
        setSelected(1)
        if (isSBS) {
            await requestForSbsShippingToSelf()
        } else {
            await requestForCementShippingToSelf()
        }
    }, [isSBS, resetOrderForm])

    const clickOnShipToSbuDealer = useCallback(async () => {
        resetOrderForm()
        setSelected(2)
        if (isSBS) {
            await requestForSbsShippingToSubDealer()
        } else {
            await requestForCementShippingToSubDealer()
        }
        setPopupStates(prev => ({ ...prev, shipToSelf: true }))
    }, [isSBS, resetOrderForm])

    const closeAllPopups = useCallback(() => {
        setPopupStates({
            shipToSelf: false,
            destinationAddress: false,
            dump: false,
            truck: false,
            forType: false
        })
    }, [])

    const filterDestinations = (destList, freightType) => {
        if (!freightType) {
            return destList;
        }
        const filtered = destList.filter(d => {
            if (!d.ex_for_type || d.ex_for_type.trim() === '') {
                return true;
            }
            const match = d.ex_for_type.toUpperCase().includes(freightType.toUpperCase());
            return match;
        });

        return filtered;
    };

    const continueOrderFlow = useCallback(async (item) => {
        await requestForSbsTruckList(shipItem?.customer_code || UrlStorage.ParameterList.BasicData.selectedCustomerCode)
        console.log('1');

          if (UrlStorage.ParameterList.BasicData.user_type == 'broker') {
                    await requestForCementTruckList(shipItem?.SAP_code || UrlStorage.ParameterList.BasicData.customerDetails.SAP_code)
                } else {
                    await requestForCementTruckList(shipItem?.SAP_code || UrlStorage.ParameterList.BasicData.emp_id)
                }

        if (isSBS) {
            const singleDest = [{
                code: item.destination_code,
                name: item.destination,
                type: ''
            }]
            setDestinationAddressList(singleDest)
            setDataSet(singleDest)
            setDestinationAddress(item.destination)
            setDestinationAddressCode(item.destination_code)

            setPopupStates({
                shipToSelf: false,
                destinationAddress: false,
                dump: false,
                truck: false,
                forType: false
            })
            return
        }

        try {
            setLoading(true)
            var a = await AuthCheckingApi();
            if (!a) {
                setAuthChecker(true)
                setLoading(false)
                return false
            }
            await clearDestinationMaster()
            const url = `${UrlStorage.BaseUrlList.Saathi.base_url_saathi}` + `${UrlStorage.NonAuthURL.Saathi.DownloadDatabaseAPI.destination_master_TXT_download_API}` + `?nick_name=START` + `&emp_code=${item.customer_code}` + `&incremental_download=no`
            const response = await fetch(url)
            const text = await response.text()
            const lines = text.trim().split('\n')
            const destinationRows = lines.slice(2).map(line => {
                const [destination_code, destination_name, customer_code, ex_for_type] = line.split('^')

                return {
                    destination_code,
                    destination_name,
                    ex_for_type
                }
            })

            await insertDataIn_destination_master(destinationRows)

            const allDestinations = await getDestiList()
            setDestinationAddressList(allDestinations)
            closeAllPopups()
        } catch (err) { } finally {
            setLoading(false)
        }
    }, [isSBS, shipItem, closeAllPopups])

    const checkGSTStatus = useCallback(async (item) => {
        pendingGSTItemRef.current = item
        closeAllPopups()

        try {
            setLoading(true)
            const response = await checkCustomerGSTStatus(item.customer_code)
            setLoading(false)

            if (!response?.success) {
                Toast.show({ type: 'error', text1: 'Sorry...', text2: 'Unable to verify GSTIN status. Please try again.' })
                return
            }

            if (response.hasGST) {
                await continueOrderFlow(item)
            } else {
                if (item.cust_type != 'Dealer')
                    setShowGSTQuestionPopup(true)
            }
        } catch (error) {
            setLoading(false)
            Toast.show({ type: 'error', text1: 'Sorry...', text2: 'Unable to verify GSTIN status. Please try again.' })
        }
    }, [continueOrderFlow, closeAllPopups])

    const handleGSTYes = useCallback(() => {
        setShowGSTQuestionPopup(false)
        setShowGSTEntryPopup(true)
    }, [])

    const handleGSTNo = useCallback(() => {
        setShowGSTQuestionPopup(false)
        setShowGSTDeclarationPopup(true)
    }, [])

    const handleChangeGSTNumber = useCallback((value) => {
        setGstNumber(value.toUpperCase())
        if (gstNumberError) setGstNumberError('')
    }, [gstNumberError])

    const handlePickGSTDocument = useCallback(async () => {
        try {
            Keyboard.dismiss();
            const [result] = await pick({
                type: [types.pdf, types.images],
            })
            setGstDocument(result)
        } catch (err) {
            if (isErrorWithCode(err) && err.code === errorCodes.OPERATION_CANCELED) {
                return
            }
            console.log('GSTIN document pick error:', err)
            Toast.show({ type: 'error', text1: 'Sorry...', text2: 'Unable to select document' })
        }
    }, [])

    const handleSubmitGSTDetails = useCallback(async () => {
        const error = validateGSTNumber(gstNumber)
        if (error) {
            setGstNumberError(error)
            return
        }
        if (!gstDocument) {
            Toast.show({ type: 'error', text1: 'Sorry...', text2: 'Please upload your GSTIN document' })
            return
        }

        try {
            setLoading(true)
            const response = await uploadGSTDetails({
                customerCode: pendingGSTItemRef.current?.customer_code,
                gstNumber,
                document: gstDocument
            })

            if (response?.success) {
                setShowGSTEntryPopup(false)
                setGstNumber('')
                setGstNumberError('')
                setGstDocument(null)
                Toast.show({ type: 'success', text1: 'Success...', text2: 'GSTIN number and document update successfully' })
                await continueOrderFlow(pendingGSTItemRef.current)
            } else {
                Toast.show({ type: 'error', text1: 'Sorry...', text2: 'Unable to upload GSTIN details. Please try again.' })
            }
        } catch (error) {
            Toast.show({ type: 'error', text1: 'Sorry...', text2: 'Unable to upload GSTIN details. Please try again.' })
        } finally {
            setLoading(false)
        }
    }, [gstNumber, gstDocument, continueOrderFlow])

    const handleGSTDeclarationConfirm = useCallback(async () => {
        setShowGSTDeclarationPopup(false)
        setShowGSTQuestionPopup(false)
        Toast.show({ type: 'success', text1: 'Success...', text2: 'GSTIN number and document update successfully' })
        await continueOrderFlow(pendingGSTItemRef.current)
    }, [continueOrderFlow])

    // ============================================================
    // selectShipToSelfItem now only populates customer info,
    // then hands off to the GSTIN layer, which hands off to continueOrderFlow
    // ============================================================
    const selectShipToSelfItem = useCallback(async (item) => {
        console.log(item);
        if (item.is_any == '' && isGSTINRequired) {
            closeAllPopups()
            if (selected == 1) {
                // Dealer
                setShowGSTNotDeclarationForDealerPopup(true)
            } else {
                // RSSD
                setShowGSTNotDeclarationForRSARPopup(true)
            }
        } else {
            setDealerSubDealerId(item.customer_code)
            setConsigneeName(item.customer_name)
            setConsigneeAddress(item.address)
            setPhoneNumber(item.phone_no)
            setShipItem(item)
            setCode(item.customer_code)
            closeAllPopups()
            continueOrderFlow(item)
        }
    }, [checkGSTStatus])
    const closePopupAndOpenSubDealer = () => {
        closeAllPopups()
        setShowGSTNotDeclarationForRSARPopup(false)
    }
    const closePopupAndOpenDealer = () => {
        closeAllPopups()
        setShowGSTNotDeclarationForDealerPopup(false)
        navigation.navigate('GSTScreen')
    }

    const selectDestinationAddressItem = useCallback((item) => {
        setDestinationAddress(item.destination_name || item.name)
        setDestinationAddressCode(item.destination_code || item.code)
        setDestinationAddressType(item.ex_for_type || item.type || "")
        closeAllPopups()
    }, [closeAllPopups])

    const selectForTypeItem = useCallback((item) => {
        setForType(item.title)
        closeAllPopups()
    }, [closeAllPopups])

    const selectDumpListItem = useCallback((item) => {
        setDumpName(item.dump_name)
        setDumpObj(item)
        closeAllPopups()
    }, [closeAllPopups])

    const selectTruckListItem = useCallback((item) => {
        setTruckName(item)
        closeAllPopups()
    }, [closeAllPopups])

    const openDestinationPopup = useCallback(() => {
        if (!destinationAddressList || destinationAddressList.length === 0) {
            Toast.show({ type: 'error', text1: 'Sorry...', text2: 'Please select Ship To first' })
            return
        }

        const freightType = freight === 1 ? "FOR" : freight === 2 ? "EXW" : freight === 3 ? "DOT" : ""

        const filteredList = filterDestinations(destinationAddressList, freightType)

        const normalizedList = filteredList.map(item => ({
            ...item,
            name: item.destination_name || item.name || "",
            code: item.destination_code || item.code || "",
            type: item.ex_for_type || item.type || ""
        }))

        setDataSet(normalizedList)
        setPopupStates(prev => ({ ...prev, destinationAddress: true }))
    }, [destinationAddressList, freight])

    const openDumpPopup = useCallback(() => {
        setDataSet(dumpDataList)
        setPopupStates(prev => ({ ...prev, dump: true }))
    }, [dumpDataList])

    const openTruckPopup = useCallback(() => {
        setDataSet(truckList)
        try {
            setTimeout(() => {
                setPopupStates(prev => ({ ...prev, truck: true }))
            }, 100)
        } catch (error) {
            console.log(error);

        }

    }, [truckList])

    const requestForCementShippingToSelf = async () => {
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            const url = `${UrlStorage.BaseUrlList.Saathi.base_url_saathi}${UrlStorage.NonAuthURL.Saathi.OrderURL1.dealer_data_list_url}?emp_code=${UrlStorage.ParameterList.BasicData.user_type != 'broker' ? UrlStorage.ParameterList.BasicData.selectedCustomerCode : UrlStorage.ParameterList.BasicData.customerDetails.customer_code}&user_type=Dealer&login_type=${UrlStorage.ParameterList.BasicData.user_type}`
            console.log(url);

            const response = await fetch(url)
            const result = await response.json()
            if (result.process_status === 'YES') {
                setShippingToList(result.dealer_data)
                setDataSet(result.dealer_data)
                console.log("self", result?.dealer_data[0]);
                setPopupStates(prev => ({ ...prev, shipToSelf: true }))
            } else {
                setShippingToList([])
            }
        } catch (error) {
        } finally {
            setLoading(false)
        }
    }
    const requestForCementShippingToSubDealer = async () => {
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            const result1 = await getAllDataFrom_customer_masterSubDealer();
            const sortedData1 = [...result1].sort((a, b) => a.customer_name.trim().toLowerCase().localeCompare(b.customer_name.trim().toLowerCase()));

            const url = `${UrlStorage.BaseUrlList.Saathi.base_url_saathi}${UrlStorage.NonAuthURL.Saathi.OrderURL1.dealer_data_list_url}?emp_code=${UrlStorage.ParameterList.BasicData.user_type != 'broker' ? UrlStorage.ParameterList.BasicData.selectedCustomerCode : UrlStorage.ParameterList.BasicData.customerDetails.customer_code}&user_type=sub dealer&login_type=${UrlStorage.ParameterList.BasicData.user_type}`
            console.log('The url is : ' + url);

            const response = await fetch(url)
            const result = await response.json()

            if (result.process_status === 'YES' && result.sub_dealer_data && result.sub_dealer_data.length > 0) {
                const sortedData = [...result.sub_dealer_data, ...sortedData1].sort((a, b) => a.customer_name.trim().toLowerCase().localeCompare(b.customer_name.trim().toLowerCase()));
                setShippingToList(sortedData)
                setDataSet(sortedData)
                setSubDealersAvailable(true)
            } else {
                setShippingToList([])
                setDataSet([])
                Toast.show({ type: 'info', text1: 'No Sub Dealers', text2: 'No sub dealers available' })
                // try {
                //     const localSubDealers = await getMySubDealerList(UrlStorage.ParameterList.BasicData.user_type, UrlStorage.ParameterList.BasicData.customerDetails.customer_code)

                //     if (localSubDealers.length > 0) {
                //         setShippingToList(localSubDealers)
                //         setDataSet(localSubDealers)
                //         setSubDealersAvailable(true)
                //     } else {
                //         setShippingToList([])
                //         setDataSet([])
                //         setSubDealersAvailable(false)
                //         Toast.show({ type: 'info', text1: 'No Sub Dealers', text2: 'No sub dealers available' })
                //     }
                // } catch (dbError) {
                //     setShippingToList([])
                //     setDataSet([])
                //     setSubDealersAvailable(false)
                // }
            }
        } catch (error) {
            setShippingToList([])
            setDataSet([])
            Toast.show({ type: 'info', text1: 'No Sub Dealers', text2: 'No sub dealers available' })
            // ⚠️ If API fails, also try local database as fallback
            // try {
            //     const localSubDealers = await getMySubDealerList(UrlStorage.ParameterList.BasicData.user_type, UrlStorage.ParameterList.BasicData.selectedCustomerCode)

            //     if (localSubDealers.length > 0) {
            //         setShippingToList(localSubDealers)
            //         setDataSet(localSubDealers)
            //         setSubDealersAvailable(true)
            //     } else {
            //         setShippingToList([])
            //         setDataSet([])
            //         setSubDealersAvailable(false)
            //     }
            // } catch (dbError) {
            //     setShippingToList([])
            //     setDataSet([])
            //     setSubDealersAvailable(false)
            // }
        } finally {
            setLoading(false)
        }
    }
    const requestForSbsShippingToSelf = async () => {
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            var url;
            if (UrlStorage.ParameterList.BasicData.user_type == 'broker') {
                url = `${UrlStorage.BaseUrlList.SBS.base_url_sbs}${UrlStorage.NonAuthURL.SBS.order.ship_to}?cust_code=${UrlStorage.ParameterList.BasicData.customerDetails.customer_code}&user_type=${UrlStorage.ParameterList.BasicData.selectedCustomerType}`
            } else {
                url = `${UrlStorage.BaseUrlList.SBS.base_url_sbs}${UrlStorage.NonAuthURL.SBS.order.ship_to}?cust_code=${UrlStorage.ParameterList.BasicData.selectedCustomerCode}&user_type=Dealer`
            }
            console.log('SBS DATA URL: ' + url);
            const response = await fetch(url)
            const result = await response.json()
            console.log(result);
            if (result.length > 0) {
                setShippingToList(result)
                setDataSet(result)
                setPopupStates(prev => ({ ...prev, shipToSelf: true }))
            } else {
                setShippingToList([])
            }
        } catch (error) {
        } finally {
            setLoading(false)
        }
    }

    const requestForSbsShippingToSubDealer = async () => {
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            const url = `${UrlStorage.BaseUrlList.SBS.base_url_sbs}${UrlStorage.NonAuthURL.SBS.order.ship_to}?cust_code=${UrlStorage.ParameterList.BasicData.selectedCustomerCode}&user_type=Sub Dealer`
            console.log('SBS DATA URL: ' + url);
            const response = await fetch(url)
            const result = await response.json()
            console.log(result);
            if (result.length > 0) {
                setShippingToList(result)
                setDataSet(result)
                console.log('SBS DATA : ' + result[0]);
                //setSubDealersAvailable(true)
            }
        } catch (error) {
        } finally {
            setLoading(false)
        }
    }

    const requestForCementDumpList = async (emp_code) => {
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            const url = `${UrlStorage.BaseUrlList.Saathi.base_url_saathi}${UrlStorage.NonAuthURL.Saathi.DownloadDatabaseAPI.branch_dump_TXT_download_API}?nick_name=${UrlStorage.ParameterList.BasicData.nick_name}&emp_code=${UrlStorage.ParameterList.BasicData.user_type != 'broker' ? emp_code : UrlStorage.ParameterList.BasicData.customerDetails.customer_code}&incremental_download=no&last_update_time=1971-01-01?10:10:10&data_download_time=1971-01-01?10:10:10`
            const response = await fetch(url)
            const result = await response.text()

            const lines = result.trim().split('\n')
            const arr = lines.slice(2).map(line => {
                const [branch_code, dump_code, dump_name, acedns, is_plant, download_time] = line.split('^')
                return { branch_code, dump_code, dump_name, acedns, is_plant, download_time }
            })

            if (arr.length > 0) {
                setDumpDataList(arr)
            } else {
                Toast.show({ type: 'error', text1: 'Sorry...', text2: 'No Dump Name Available' })
            }
        } catch (error) {
        } finally {
            setLoading(false)
        }
    }

    const requestForSbsDumpList = async (emp_code) => {
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            const url = `${UrlStorage.BaseUrlList.SBS.base_url_sbs}${UrlStorage.NonAuthURL.SBS.order.order_dump_list}?cust_code=${UrlStorage.ParameterList.BasicData.user_type != 'broker' ? emp_code : UrlStorage.ParameterList.BasicData.customerDetails.customer_code}`
            const response = await fetch(url)
            const result = await response.json()

            if (result.length > 0) {
                setDumpDataList(result)
            } else {
                Toast.show({ type: 'error', text1: 'Sorry...', text2: 'No Dump Name Available' })
            }
        } catch (error) {
        } finally {
            setLoading(false)
        }
    }

    const requestForSbsTruckList = async (emp_code) => {
        setLoading(true)
        try {
            const url = `${UrlStorage.BaseUrlList.SBS.base_url_sbs}${UrlStorage.NonAuthURL.SBS.order.check_truck_list}?cust_code=${UrlStorage.ParameterList.BasicData.user_type != 'broker' ? emp_code : UrlStorage.ParameterList.BasicData.customerDetails.customer_code}`
            console.log(url);

            const response = await fetch(url)
            const result = await response.json()

            if (result?.dealer_truck?.length > 0) {
                setTruckList(result.dealer_truck)
            } else {
                setTruckList([])
            }
        } catch (error) {
        } finally {
            setLoading(false)
        }
    }
    const requestForCementTruckList = async (emp_code) => {
        setLoading(true)
        try {
            console.log(emp_code);

            const formdata = new FormData();
            formdata.append("customer_code", emp_code);

            const requestOptions = {
                method: "POST",
                body: formdata,
                redirect: "follow"
            };

            fetch(UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.OrderURL1.truck_data_list_url, requestOptions)
                .then((response) => response.json())
                .then((result) => {
                    console.log(result);

                    if (result?.truck_data?.length > 0) {
                        var arr = []
                        result.truck_data.map(item => {
                            arr.push(item.truck_no)
                        })
                        setTruckList(arr)
                    } else {
                        setTruckList([])
                    }
                })
                .catch((error) => console.error(error));
        } catch (error) {
        } finally {
            setLoading(false)
        }
    }
    const getSbsPlantDetails = async (emp_code) => {
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        const requestOptions = {
            method: "GET", redirect: "follow",
            headers: { "Authorization": UrlStorage.ParameterList.BasicData.user_type == 'broker' ? `SAP_SP` : `SAP_DEALER` + `${UrlStorage.ParameterList.BasicData.emp_id}`, "Content-Type": "application/json", },
        }

        try {
            const url = `${UrlStorage.BaseUrlList.SBS.base_url_sbs}${UrlStorage.NonAuthURL.SBS.order.plant_details}?cust_code=${emp_code}`
            const response = await fetch(url, requestOptions)
            const result = await response.json()
            if (result?.data?.length > 0) {
                setPlant(result?.data)
            } else {
                setPlant([])
            }
        } catch (error) {
        } finally {
            setLoading(false)
        }
    }
    const getCementPlantDetails = async (customer_code) => {
        if (!customer_code) return

        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            const url = `${UrlStorage.BaseUrlList.Saathi.base_url_saathi}/api_get_plant_name.php?customer_code=${UrlStorage.ParameterList.BasicData.user_type != 'broker' ? customer_code : UrlStorage.ParameterList.BasicData.customerDetails.SAP_code}`

            const response = await fetch(url)
            const result = await response.json()

            if (result?.plant_name) {
                setPlant([{ plant_code: result.plant_code, plant_name: result.plant_name }])
            } else {
                setPlant([])
            }
        } catch (error) {
            setPlant([])
        } finally {
            setLoading(false)
        }
    }
    const checkData = useCallback(() => {
        const validations = [
            { condition: selected === 0, message: 'Please select ship to' },
            { condition: !consigneeName, message: 'Please enter consignee name' },
            { condition: !consigneeAddress, message: 'Please enter consignee address' },
            { condition: !destinationAddress, message: 'Please select destination address' },
            { condition: !phoneNo, message: 'Please enter phone number' },
            { condition: phoneNo.length != 10, message: 'Please enter 10 digit phone number' },
            { condition: !freight, message: 'Please select freight' },
            { condition: !dumpName && freight === 2, message: 'Please select Dump Name' },
            { condition: !truckName && freight === 1 && forType === 'DOT', message: 'Please select Dealer Truck Details' },
        ]

        for (const validation of validations) {
            if (validation.condition) {
                Toast.show({ type: 'error', text1: 'Sorry...', text2: validation.message })
                return
            }
        }

        // Save data
        DataStorage.shipTo = selected === 1 ? 'self' : 'sub dealer'
        DataStorage.dealer_sub_dealer_id = dealerSubDealerId || ""
        DataStorage.consignee_name = consigneeName || ""
        DataStorage.consignee_address = consigneeAddress || ""
        DataStorage.consignee_phone_number = phoneNo || ""
        DataStorage.destination_address = destinationAddress || ""
        DataStorage.destination_address_code = destinationAddressCode || ""
        DataStorage.destination_address_type = destinationAddressType || ""
        DataStorage.freight = freight
        DataStorage.for_type = forType || ""
        DataStorage.delivery_remarks = deliveryRemarks || ""
        DataStorage.ship_item = shipItem || ""
        DataStorage.dump_obj = dumpObj || ""
        DataStorage.dealer_truck = truckName || ""
        DataStorage.plant_name = plant[0]?.plant_name || "No Plant Selected"

        const products = selectedProducts.reduce((acc, item) => {
            acc[item.prod_code] = item.count
            return acc
        }, {})

        navigation.navigate("OrderConfirmScreen", { selectedProducts: products })
    }, [selected, consigneeName, consigneeAddress, destinationAddress, phoneNo, freight, dumpName, truckName, deliveryRemarks, dealerSubDealerId, destinationAddressCode, destinationAddressType, forType, shipItem, dumpObj, selectedProducts, navigation])

    const handleBackPress = () => {
        setShowGSTDeclarationPopup(false)
        setShowGSTEntryPopup(false)
        setShowGSTQuestionPopup(true)
    }
    const handleMainBackPress = () => {
        setShowGSTQuestionPopup(false)
        if (selected === 1)
            clickOnShipToSelf()
        if (selected === 2)
            clickOnShipToSbuDealer()
    }
    const onClosePopup = () => {
        setShowGSTNotDeclarationForDealerPopup(false)
        setShowGSTNotDeclarationForRSARPopup(false)
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <SBSCommonHeaderView title="Order Details" backPath=" " />
            <View style={styles.content}>
                <ScrollView ref={scrollViewRef} style={styles.scrollView} showsVerticalScrollIndicator={false} nestedScrollEnabled={false} keyboardShouldPersistTaps="handled" scrollEventThrottle={16} removeClippedSubviews={false} contentContainerStyle={styles.scrollContent} >
                    {/* Ship To */}
                    <FormField label="Ship To">
                        <View style={styles.row}>
                            <TouchableOpacity activeOpacity={0.7} style={[styles.flex1]} onPress={clickOnShipToSelf} >
                                <View style={styles.radioContainer}>
                                    <RadioButton selected={selected === 1} color={primaryColor} />
                                    <Text style={styles.radioText}>Self</Text>
                                </View>
                            </TouchableOpacity>

                            {/* {subDealersAvailable && ( */}
                            <TouchableOpacity activeOpacity={0.7} style={styles.flex1} onPress={clickOnShipToSbuDealer} >
                                <View style={styles.radioContainer}>
                                    <RadioButton selected={selected === 2} color={primaryColor} />
                                    <Text style={styles.radioText}>Sub Dealer</Text>
                                </View>
                            </TouchableOpacity>
                            {/* )} */}
                        </View>
                    </FormField>

                    {/* Consignee Name */}
                    <FormField label="Consignee Name">
                        <View style={styles.inputContainer} pointerEvents="none">
                            <TextInput placeholder='|' value={consigneeName.replaceAll("&amp;", '&')} editable={false} placeholderTextColor="#A7A7A7" style={styles.input} />
                        </View>
                    </FormField>

                    {/* Consignee Address */}
                    <FormField label="Consignee Address">
                        <View style={styles.textAreaContainer}>
                            <TextInput placeholder='|' value={consigneeAddress} multiline scrollEnabled={false} placeholderTextColor="#A7A7A7" style={styles.input} onChangeText={setConsigneeAddress} />
                        </View>
                    </FormField>

                    {/* Destination Address */}
                    <FormField label="Destination Address">
                        <TouchableOpacity activeOpacity={0.7} onPress={openDestinationPopup} style={styles.dropdownContainer} >
                            <Text style={[styles.dropdownText, !destinationAddress && styles.placeholderText]} numberOfLines={1} >
                                {destinationAddress || "Select destination address"}
                            </Text>
                            <Image source={Icons.DownArrow} style={[styles.dropdownIcon, { tintColor: primaryColor }]} />
                        </TouchableOpacity>
                    </FormField>

                    {/* Phone No */}
                    <FormField label="Phone No">
                        <View style={styles.inputContainer}>
                            <TextInput keyboardType='number-pad' placeholder='|' value={phoneNo} placeholderTextColor="#A7A7A7" style={styles.input} onChangeText={setPhoneNumber} maxLength={10} />
                        </View>
                    </FormField>

                    {/* Freight */}
                    <FormField label="Freight">
                        <View style={styles.row}>
                            <TouchableOpacity
                                activeOpacity={0.7}
                                style={styles.flex1}
                                onPress={async () => {
                                    if (freight === 1) {
                                        setPopupStates(prev => ({ ...prev, forType: true }))
                                        return
                                    }
                                    setFreight(1)
                                    setForType('')
                                    if (isSBS) {
                                        await getSbsPlantDetails(shipItem?.customer_code)
                                        await requestForSbsTruckList(shipItem?.customer_code || UrlStorage.ParameterList.BasicData.selectedCustomerCode)
                                    } else {
                                        await getCementPlantDetails(UrlStorage.ParameterList.BasicData.emp_id)
                                    }
                                }} >
                                <View style={styles.radioContainer}>
                                    <RadioButton selected={freight === 1} color={primaryColor} />
                                    <Text ellipsizeMode='tail' numberOfLines={1} style={[styles.radioText, truckList.length > 0 && { width: moderateScale(60) }]} >
                                        For {forType && `(${forType})`}
                                    </Text>
                                </View>
                            </TouchableOpacity>

                            <TouchableOpacity activeOpacity={0.7} style={styles.flex1} onPress={() => setFreight(2)} >
                                <View style={styles.radioContainer}>
                                    <RadioButton selected={freight === 2} color={primaryColor} />
                                    <Text style={styles.radioText}>EXW</Text>
                                </View>
                            </TouchableOpacity>

                            {/* {truckList.length > 0 && (
                                    <TouchableOpacity activeOpacity={0.7} style={styles.flex1} onPress={() => setFreight(3)} >
                                        <View style={styles.radioContainer}>
                                            <RadioButton selected={freight === 3} color={primaryColor} />
                                            <Text style={styles.radioText}>DOT</Text>
                                        </View>
                                    </TouchableOpacity>
                                )} */}
                        </View>
                    </FormField>

                    {/* Dump Name */}
                    {freight === 2 && dumpDataList.length > 0 && (
                        <FormField label="Dump Name">
                            <TouchableOpacity activeOpacity={0.7} onPress={openDumpPopup} style={styles.dropdownContainer} >
                                <Text style={[styles.dropdownText, !dumpName && styles.placeholderText]} numberOfLines={1} >
                                    {dumpName || "Select dump"}
                                </Text>
                                <Image source={Icons.DownArrow} style={[styles.dropdownIcon, { tintColor: primaryColor }]} />
                            </TouchableOpacity>
                        </FormField>
                    )}

                    {/* Dealer Truck */}
                    {freight === 1 && forType === 'DOT' && truckList.length > 0 && (
                        <FormField label="Dealer Truck">
                            <TouchableOpacity activeOpacity={0.7} onPress={openTruckPopup} style={styles.dropdownContainer} >
                                <Text style={[styles.dropdownText, !truckName && styles.placeholderText]} numberOfLines={1} >
                                    {truckName || "Select Dealer Truck"}
                                </Text>
                                <Image source={Icons.DownArrow} style={[styles.dropdownIcon, { tintColor: primaryColor }]} />
                            </TouchableOpacity>
                        </FormField>
                    )}
                    {/* For Plant  */}
                    {freight === 1 && (
                        <FormField label="Available Plant">
                            <TouchableOpacity activeOpacity={0.7} style={[styles.dropdownContainer, { backgroundColor: '#edebeb' }]} >
                                <Text style={[styles.dropdownText, (!plant || plant.length === 0) && styles.placeholderText]} numberOfLines={1} >
                                    {(plant && plant.length > 0 && plant[0]?.plant_name) ? plant[0].plant_name : "No plant available"}
                                </Text>
                            </TouchableOpacity>
                        </FormField>
                    )}

                    {/* Delivery Remarks */}
                    <FormField label="Delivery Remarks">
                        <View style={styles.textAreaContainer}>
                            <TextInput placeholder='|' value={deliveryRemarks} multiline={true} onChangeText={setDeliveryRemarks} placeholderTextColor="#A7A7A7" scrollEnabled={false} style={styles.input} />
                        </View>
                    </FormField>
                </ScrollView>
            </View>

            {/* Continue Button */}
            <TouchableOpacity activeOpacity={0.7} onPress={checkData}>
                <View style={styles.buttonWrapper}>
                    <View style={[styles.button, { backgroundColor: primaryColor }]}>
                        <Text style={styles.buttonText}>Continue</Text>
                    </View>
                </View>
            </TouchableOpacity>
            <View style={styles.bottomSpacer} />

            {/* Popups */}
            <ShipToSelfListPopupView isVisible={popupStates.shipToSelf} dataList={dataSet} closePopup={closeAllPopups} selectItem={selectShipToSelfItem} isDealer={selected === 1} gotoGSTPage={closePopupAndOpenDealer} />
            <ForTypeListPopupView isVisible={popupStates.forType} dataList={forTypeList} closePopup={closeAllPopups} selectItem={selectForTypeItem} />
            <DumpListPopupView isSBS={isSBS} isVisible={popupStates.dump} dataList={dataSet} closePopup={closeAllPopups} selectItem={selectDumpListItem} />
            <TruckListPopupView isSBS={isSBS} isVisible={popupStates.truck} dataList={dataSet} closePopup={closeAllPopups} selectItem={selectTruckListItem} />
            <DestinationAddressListPopupView isVisible={popupStates.destinationAddress} dataList={dataSet} closePopup={closeAllPopups} selectItem={selectDestinationAddressItem} />

            {/* GSTIN Verification Popups */}
            <GSTQuestionPopup
                visible={showGSTQuestionPopup}
                primaryColor={primaryColor}
                selected={selected}
                cust_type={shipItem.cust_type}
                onYes={handleGSTYes}
                onNo={handleGSTNo}
                onBackPress={handleMainBackPress}
                onYesDealer={handleGSTDeclarationConfirm}
            />
            <GSTEntryPopup
                visible={showGSTEntryPopup}
                primaryColor={primaryColor}
                gstNumber={gstNumber}
                gstNumberError={gstNumberError}
                gstDocument={gstDocument}
                submitting={loading}
                onChangeGSTNumber={handleChangeGSTNumber}
                onPickDocument={handlePickGSTDocument}
                onSubmit={handleSubmitGSTDetails}
                onBackPress={handleBackPress}
            />
            <GSTDeclarationPopup
                visible={showGSTDeclarationPopup}
                primaryColor={primaryColor}
                onConfirm={handleGSTDeclarationConfirm}
                onBackPress={handleBackPress}
            />

            <GSTNotDeclarationForRSARPopup
                visible={showGSTNotDeclarationForRSARPopup}
                primaryColor={primaryColor}
                onConfirm={closePopupAndOpenSubDealer}
                onClose={onClosePopup}
            />

            <GSTNotDeclarationForSeflPopup
                visible={showGSTNotDeclarationForDealerPopup}
                primaryColor={primaryColor}
                onConfirm={closePopupAndOpenDealer}
                onClose={onClosePopup}
            />

            <Toast config={toastConfig} />
            {loading && <Loader />}
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: Colors.white },
    content: { width: "100%", flex: 1, paddingHorizontal: moderateScale(20), },
    scrollView: { flex: 1, },
    scrollContent: { paddingTop: moderateScale(10), paddingBottom: moderateScale(20), },
    formField: { width: "100%", marginBottom: moderateScale(16) },
    label: { color: Colors.text, fontSize: moderateScale(14), marginBottom: moderateScale(8) },
    row: { width: "100%", flexDirection: "row", alignItems: "center", gap: moderateScale(12) },
    flex1: { flex: 1 },
    flex04: { flex: 0.4 },
    radioContainer: { flexDirection: "row", gap: moderateScale(10), alignItems: "center", width: "100%", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), padding: moderateScale(12), backgroundColor: Colors.white },
    radioOuter: { width: moderateScale(20), height: moderateScale(20), borderRadius: moderateScale(10), borderWidth: moderateScale(1), backgroundColor: "#FFFFFF", padding: moderateScale(2), justifyContent: 'center', alignItems: 'center' },
    radioInner: { width: moderateScale(14), height: moderateScale(14), borderRadius: moderateScale(7) },
    radioText: { color: Colors.text, fontSize: moderateScale(14), flex: 1 },
    inputContainer: { width: "100%", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), paddingHorizontal: moderateScale(12), height: moderateScale(44), justifyContent: 'center', backgroundColor: Colors.white },
    textAreaContainer: { width: "100%", minHeight: moderateScale(100), borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), paddingHorizontal: moderateScale(12), paddingVertical: moderateScale(10), backgroundColor: Colors.white },
    dropdownContainer: { width: "100%", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), paddingHorizontal: moderateScale(12), flexDirection: 'row', alignItems: 'center', height: moderateScale(44), backgroundColor: Colors.white },
    input: { color: "#000000", fontSize: moderateScale(14), padding: 0, margin: 0, textAlignVertical: 'top' },
    dropdownText: { flex: 1, color: "#000000", fontSize: moderateScale(14) },
    placeholderText: { color: "#A7A7A7" },
    dropdownIcon: { width: moderateScale(15), height: moderateScale(15), marginLeft: moderateScale(10) },
    buttonWrapper: { width: "100%", paddingHorizontal: moderateScale(20), paddingTop: moderateScale(10) },
    button: { width: "100%", height: moderateScale(48), borderRadius: moderateScale(10), alignItems: "center", justifyContent: "center" },
    buttonText: { color: Colors.white, fontSize: moderateScale(16), fontWeight: '600' },
    bottomSpacer: { height: moderateScale(16) },

    // GSTIN modal styles
    modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', alignItems: 'center', paddingHorizontal: moderateScale(24) },
    modalCard: { width: '100%', backgroundColor: Colors.white, borderRadius: moderateScale(14), padding: moderateScale(20) },
    modalTitle: { color: Colors.text, fontSize: moderateScale(16), fontWeight: '700', marginBottom: moderateScale(10) },
    modalMessage: { color: Colors.text, fontSize: moderateScale(14), lineHeight: moderateScale(20) },
    modalButtonRow: { flexDirection: 'row', gap: moderateScale(12), marginTop: moderateScale(20) },
    modalButtonPrimary: { flex: 1, height: moderateScale(44), borderRadius: moderateScale(10), alignItems: 'center', justifyContent: 'center' },
    modalButtonPrimaryText: { color: Colors.white, fontSize: moderateScale(14), fontWeight: '600' },
    modalButtonSecondary: { flex: 1, height: moderateScale(44), borderRadius: moderateScale(10), alignItems: 'center', justifyContent: 'center', borderWidth: moderateScale(1), borderColor: "#DCDDDF" },
    modalButtonSecondaryText: { color: Colors.text, fontSize: moderateScale(14), fontWeight: '600' },
    errorText: { color: '#D32F2F', fontSize: moderateScale(12), marginTop: moderateScale(6) },
})

export default OrderDetailsScreen