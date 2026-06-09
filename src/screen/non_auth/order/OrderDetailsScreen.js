import React, { useEffect, useState, useCallback, useMemo, useRef } from 'react'
import { Image, ScrollView, Text, TextInput, TouchableOpacity, View, StyleSheet } from 'react-native'
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

    // Memoized constants
    const forTypeList = useMemo(() => {
        const baseList = [{ id: 1, title: 'Multiple' }, { id: 2, title: 'Single' },]

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
                await requestForCementShippingToSubDealer()
            }
        }
        fetchInitialData()
    }, [isSBS])

    // Freight change handler
    useEffect(() => {
        const handleFreightChange = async () => {
            setDumpName('')
            setTruckName('')

            if (freight === 1) {
                setPopupStates(prev => ({ ...prev, forType: true }))
                if (isSBS) {
                    await requestForSbsTruckList(shipItem?.customer_code || UrlStorage.ParameterList.BasicData.selectedCustomerCode)
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

    const selectShipToSelfItem = useCallback(async (item) => {
        setDealerSubDealerId(item.customer_code)
        setConsigneeName(item.customer_name)
        setConsigneeAddress(item.address)
        setPhoneNumber(item.phone_no)
        setShipItem(item)
        setCode(item.customer_code)
        await requestForSbsTruckList(shipItem?.customer_code || UrlStorage.ParameterList.BasicData.selectedCustomerCode)

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
    }, [isSBS])

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
        setTimeout(() => {
            setPopupStates(prev => ({ ...prev, truck: true }))
        }, 100)
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
            const response = await fetch(url)
            const result = await response.json()
            if (result.process_status === 'YES') {
                setShippingToList(result.dealer_data)
                setDataSet(result.dealer_data)
                console.log("self",result?.dealer_data[0]);
                
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
            const response = await fetch(url)
            const result = await response.json()

            if (result.process_status === 'YES' && result.sub_dealer_data && result.sub_dealer_data.length > 0) {
                const sortedData = [...result.sub_dealer_data, ...sortedData1].sort((a, b) => a.customer_name.trim().toLowerCase().localeCompare(b.customer_name.trim().toLowerCase()));
                setShippingToList(sortedData)
                setDataSet(sortedData)
                console.log(sortedData[0]);
                
                setSubDealersAvailable(true)
            } else {
                try {
                    const localSubDealers = await getMySubDealerList(UrlStorage.ParameterList.BasicData.user_type, UrlStorage.ParameterList.BasicData.customerDetails.customer_code)

                    if (localSubDealers.length > 0) {
                        setShippingToList(localSubDealers)
                        setDataSet(localSubDealers)
                        setSubDealersAvailable(true)
                    } else {
                        setShippingToList([])
                        setDataSet([])
                        setSubDealersAvailable(false)
                        Toast.show({ type: 'info', text1: 'No Sub Dealers', text2: 'No sub dealers available' })
                    }
                } catch (dbError) {
                    setShippingToList([])
                    setDataSet([])
                    setSubDealersAvailable(false)
                }
            }
        } catch (error) {

            // ⚠️ If API fails, also try local database as fallback
            try {
                const localSubDealers = await getMySubDealerList(UrlStorage.ParameterList.BasicData.user_type, UrlStorage.ParameterList.BasicData.selectedCustomerCode)

                if (localSubDealers.length > 0) {
                    setShippingToList(localSubDealers)
                    setDataSet(localSubDealers)
                    setSubDealersAvailable(true)
                } else {
                    setShippingToList([])
                    setDataSet([])
                    setSubDealersAvailable(false)
                }
            } catch (dbError) {
                setShippingToList([])
                setDataSet([])
                setSubDealersAvailable(false)
            }
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
            const url = `${UrlStorage.BaseUrlList.SBS.base_url_sbs}${UrlStorage.NonAuthURL.SBS.order.ship_to}?cust_code=${UrlStorage.ParameterList.BasicData.selectedCustomerCode}&user_type=Dealer`
            const response = await fetch(url)
            const result = await response.json()

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
            const response = await fetch(url)
            const result = await response.json()

            if (result.length > 0) {
                setShippingToList(result)
                setDataSet(result)
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
            <ShipToSelfListPopupView isVisible={popupStates.shipToSelf} dataList={dataSet} closePopup={closeAllPopups} selectItem={selectShipToSelfItem} isDealer={selected === 1} />
            <ForTypeListPopupView isVisible={popupStates.forType} dataList={forTypeList} closePopup={closeAllPopups} selectItem={selectForTypeItem} />
            <DumpListPopupView isSBS={isSBS} isVisible={popupStates.dump} dataList={dataSet} closePopup={closeAllPopups} selectItem={selectDumpListItem} />
            <TruckListPopupView isSBS={isSBS} isVisible={popupStates.truck} dataList={dataSet} closePopup={closeAllPopups} selectItem={selectTruckListItem} />
            <DestinationAddressListPopupView isVisible={popupStates.destinationAddress} dataList={dataSet} closePopup={closeAllPopups} selectItem={selectDestinationAddressItem} />
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
    bottomSpacer: { height: moderateScale(16) }
})

export default OrderDetailsScreen