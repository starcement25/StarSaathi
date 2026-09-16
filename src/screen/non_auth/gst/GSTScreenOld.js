import React, { useEffect, useState, useMemo, useCallback } from 'react'
import { ActivityIndicator, FlatList, Keyboard, Modal, ScrollView, StyleSheet, Text, TextInput, TouchableOpacity, View } from 'react-native'
import SafeView from '../../../helper/SafeView'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Colors } from '../../../assets/Colors'
import DataStorage from '../../../storage/DataStorage'
import { moderateScale } from '../../../helper/Window'
import UrlStorage from '../../../storage/UrlStorage'
import { pick, types } from '@react-native-documents/picker'
import Toast from 'react-native-toast-message'
import { WebView } from 'react-native-webview'
import Loader from '../../../common/Loader'

const GSTQuestionPopup = React.memo(({ visible, primaryColor, onBackPress, selected, cust_type, onDealerDumpHasNotSameGST, onSubDealerHasNotGST, onDealerDumpHasSameGST, onSubDealerHasGST }) => (
    <Modal visible={visible} transparent animationType="fade" onRequestClose={() => { }} >
        <View style={styles.modalOverlay}>
            <TouchableOpacity style={StyleSheet.absoluteFill} activeOpacity={1} onPress={onBackPress} />
            <View style={styles.modalCard}>
                <Text style={styles.modalTitle}>GSTIN Verification</Text>
                <Text style={styles.modalMessage}> Is the Address registered under GSTIN? </Text>
                <View style={styles.modalButtonRow}>
                    <TouchableOpacity activeOpacity={0.7} style={styles.modalButtonSecondary} onPress={() => {
                        if (selected)
                            onDealerDumpHasSameGST()
                        else
                            onSubDealerHasNotGST()
                    }}>
                        <Text style={styles.modalButtonSecondaryText}>NO</Text>
                    </TouchableOpacity>
                    <TouchableOpacity activeOpacity={0.7} style={[styles.modalButtonPrimary, { backgroundColor: primaryColor }]} onPress={() => {
                        if (selected)
                            onDealerDumpHasNotSameGST()
                        else
                            onSubDealerHasGST()
                    }}>
                        <Text style={styles.modalButtonPrimaryText}>YES</Text>
                    </TouchableOpacity>
                </View>
            </View>
        </View>
    </Modal>
))

const GSTQuestionDealerPopup = React.memo(({ visible, primaryColor, onBackPress, mentionName, onChangeMentionName, onMentionNameSubmit, mentionNameError }) => (
    <Modal visible={visible} transparent animationType="fade" onRequestClose={() => { }} >
        <View style={styles.modalOverlay}>
            <TouchableOpacity style={StyleSheet.absoluteFill} activeOpacity={1} />
            <View style={styles.modalCard}>
                <TouchableOpacity onPress={onBackPress} style={styles.modalBackButton} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
                    <Text style={styles.modalBackButtonText}>{'< Back'}</Text>
                </TouchableOpacity>
                <View style={{ width: '100%', marginTop: moderateScale(8) }}>
                    <Text style={styles.label}>Mention Name of Proprietor/Partner/Director or Authorized Signatory</Text>
                    <View style={styles.inputContainer}>
                        <TextInput
                            placeholder="Mention Name"
                            placeholderTextColor="#A7A7A7"
                            maxLength={15}
                            style={styles.input}
                            value={mentionName}
                            onChangeText={onChangeMentionName}
                        />
                        {!!mentionNameError && <Text style={styles.errorText}>{mentionNameError}</Text>}
                    </View>
                </View>
                <View style={styles.modalButtonRow}>
                    <TouchableOpacity activeOpacity={0.7} style={[styles.modalButtonPrimary, { backgroundColor: primaryColor }]} onPress={() => { onMentionNameSubmit() }}>
                        <Text style={styles.modalButtonPrimaryText}>Submit</Text>
                    </TouchableOpacity>
                </View>
            </View>
        </View>
    </Modal>
))

const GSTEntryPopup = React.memo(({ visible, primaryColor, gstNumber, gstNumberError, gstDocument, submitting, onChangeGSTNumber, onPickDocument, onSubmit, onBackPress }) => (
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

const GSTDeclarationPopup = React.memo(({ visible, primaryColor, onConfirm, onBackPress, gstInfoUrl }) => {
    const [isChecked, setIsChecked] = React.useState(false)
    React.useEffect(() => {
        if (!visible) setIsChecked(false)
    }, [visible])
    const injectedJS = `
        (function() {
            var m = document.querySelector('meta[name=viewport]');
            if (!m) {
                m = document.createElement('meta');
                m.setAttribute('name', 'viewport');
                document.getElementsByTagName('head')[0].appendChild(m);
            }
            m.setAttribute('content', 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no');
            function retry(img) {
                var s = img.src;
                if (!s) return;
                img.src = '';
                img.src = s + (s.indexOf('?') >= 0 ? '&' : '?') + '_t=' + Date.now();
            }
    
            function initImages() {
                var imgs = document.querySelectorAll('img');
                Array.prototype.forEach.call(imgs, function(img) {
                    if (!img.complete || img.naturalWidth === 0) { retry(img); }
                    img.addEventListener('error', function() { retry(img); }, { once: true });
                });
            }
    
            function start() {
                initImages();
                if (!document.body) return;
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mu) {
                        Array.prototype.forEach.call(mu.addedNodes, function(node) {
                            if (node.nodeName === 'IMG') {
                                node.addEventListener('error', function() { retry(node); }, { once: true });
                            } else if (node.querySelectorAll) {
                                Array.prototype.forEach.call(node.querySelectorAll('img'), function(i) {
                                    i.addEventListener('error', function() { retry(i); }, { once: true });
                                });
                            }
                        });
                    });
                });
                observer.observe(document.body, { childList: true, subtree: true });
            }
    
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', start);
            } else {
                start();
            }
    
            if (window.ReactNativeWebView) {
                window.ReactNativeWebView.postMessage('viewport:' + window.innerWidth);
            }
        })();
        true;
    `
    return (
        <Modal visible={visible} transparent animationType="fade" onRequestClose={onBackPress}>
            <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', alignItems: 'center', paddingHorizontal: moderateScale(14) }}>
                <View style={{ width: '100%', backgroundColor: Colors.white, borderRadius: moderateScale(14), paddingVertical: moderateScale(20) }}>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(16), fontWeight: '700', marginBottom: moderateScale(10), paddingHorizontal: moderateScale(20) }}>Declaration</Text>
                    <View style={styles.webviewContainer}>
                        <WebView
                            source={{ uri: gstInfoUrl }}
                            style={styles.webview}
                            startInLoadingState
                            renderLoading={() => (
                                <ActivityIndicator style={StyleSheet.absoluteFill} color={primaryColor} />
                            )}
                            originWhitelist={['*']}
                            mixedContentMode="always"
                            domStorageEnabled={true}
                            javaScriptEnabled={true}
                            javaScriptCanOpenWindowsAutomatically={true}
                            allowUniversalAccessFromFileURLs={true}
                            allowFileAccessFromFileURLs={true}
                            thirdPartyCookiesEnabled={true}
                            sharedCookiesEnabled={true}
                            scalesPageToFit={true}
                            setBuiltInZoomControls={false}
                            injectedJavaScript={injectedJS}
                            onMessage={(event) => { }}
                            cacheEnabled={false}
                            cacheMode="LOAD_NO_CACHE"
                        />
                    </View>

                    <TouchableOpacity activeOpacity={0.7} style={styles.checkboxRow} onPress={() => setIsChecked(prev => !prev)} >
                        <View style={[styles.checkboxBox, isChecked && { backgroundColor: primaryColor, borderColor: primaryColor }]}>
                            {isChecked && <Text style={styles.checkboxTick}>✓</Text>}
                        </View>
                        <Text style={styles.checkboxLabel}> Yes I declare </Text>
                    </TouchableOpacity>
                    <View style={{ flexDirection: 'row', gap: moderateScale(12), marginTop: moderateScale(20), paddingHorizontal: moderateScale(20) }}>
                        <TouchableOpacity activeOpacity={0.7} style={styles.modalButtonSecondary} onPress={onBackPress}>
                            <Text style={styles.modalButtonSecondaryText}>Back</Text>
                        </TouchableOpacity>
                        <TouchableOpacity activeOpacity={0.7} disabled={!isChecked}
                            style={[
                                styles.modalButtonPrimary,
                                { backgroundColor: isChecked ? primaryColor : '#C7C7C7' }
                            ]} onPress={onConfirm} >
                            <Text style={styles.modalButtonPrimaryText}>Confirm</Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </View>
        </Modal>
    )
})

const GSTNotePopup = React.memo(({ visible, primaryColor, onCloseNotePopup }) => (
    <Modal visible={visible} transparent animationType="fade" onRequestClose={() => { }} >
        <View style={styles.modalOverlay}>
            <TouchableOpacity style={StyleSheet.absoluteFill} activeOpacity={1} onPress={onCloseNotePopup} />
            <View style={styles.modalCard}>
                <Text style={styles.modalTitle}>Please Note: </Text>
                <Text style={styles.modalMessage}> If this is your additional place of business or godown, please register the same with GST department </Text>
                <Text style={styles.modalMessage}> Ship to address GSTIN will be shown as unregistered Party in Tax invoice. </Text>
                <View style={styles.modalButtonRow}>
                    <TouchableOpacity activeOpacity={0.7} style={[styles.modalButtonPrimary, { backgroundColor: primaryColor }]} onPress={() => { onCloseNotePopup() }}>
                        <Text style={styles.modalButtonPrimaryText}>OK</Text>
                    </TouchableOpacity>
                </View>
            </View>
        </View>
    </Modal>
))

const GST_REGEX = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z][0-9A-Z][0-9A-Z]$/
const VALID_STATE_CODES = [
    '01', '02', '03', '04', '05', '06', '07', '08', '09', '10',
    '11', '12', '13', '14', '15', '16', '17', '18', '19', '20',
    '21', '22', '23', '24', '25', '26', '27', '28', '29', '30',
    '31', '32', '33', '34', '35', '36', '37'
]
const validateGSTNumber = (value) => {
    if (!value || value.trim().length === 0)
        return 'GSTIN number is required'
    const gst = value.trim().toUpperCase()
    if (gst.length !== 15)
        return 'GSTIN number must be 15 characters'
    if (/\s/.test(value))
        return 'GSTIN number must not contain spaces'
    if (!GST_REGEX.test(gst))
        return 'Please enter a valid GSTIN number'
    const stateCode = gst.substring(0, 2)
    if (!VALID_STATE_CODES.includes(stateCode))
        return 'Invalid state code in GSTIN number'
    const pan = gst.substring(2, 12)
    const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]$/
    if (!panRegex.test(pan))
        return 'Invalid PAN structure within GSTIN number'
    return ''
}
const runTimeValidateGSTNumber = (value) => {
    if (!value || value.trim().length === 0)
        return 'GSTIN number is required'
    const gst = value.trim().toUpperCase()
    if (/\s/.test(value))
        return 'GSTIN number must not contain spaces'
    if (value.trim().length == 15) {
        if (!GST_REGEX.test(gst))
            return 'Please enter a valid GSTIN number'
    }
    if (value.trim().length >= 2) {
        const stateCode = gst.substring(0, 2)
        if (!VALID_STATE_CODES.includes(stateCode))
            return 'Invalid state code in GSTIN number'
    }
    if (value.trim().length >= 12) {
        const pan = gst.substring(2, 12)
        const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]$/
        if (!panRegex.test(pan))
            return 'Invalid PAN structure within GSTIN number'
    }
    return ''
}

const GSTScreenOld = (props) => {
    const [isDealer, setIsDealer] = useState(UrlStorage.ParameterList.BasicData.user_type.toLowerCase() === 'dealer')
    const [userList, setUserList] = useState([])
    const [userList1, setUserList1] = useState([])
    const [showGSTQuestionPopup, setShowGSTQuestionPopup] = useState(false)
    const [showGSTQuestionDealerPopup, setShowGSTQuestionDealerPopup] = useState(false)
    const [showGSTEntryPopup, setShowGSTEntryPopup] = useState(false)
    const [showGSTDeclarationPopup, setShowGSTDeclarationPopup] = useState(false)
    const [showGSTNotePopup, setShowGSTNotePopup] = useState(false)
    const [custType, setCustType] = useState('')
    const [selectCustomerDetails, setSelectCustomerDetails] = useState({})
    const [gstNumber, setGstNumber] = useState('')
    const [gstNumberError, setGstNumberError] = useState('')
    const [mentionName, setMentionName] = useState('')
    const [gstDocument, setGstDocument] = useState(null)
    const [loading, setLoading] = useState(false)
    const [mentionNameError, setMentionNameError] = useState('')

    const primaryColor = useMemo(() => DataStorage.primaryColorCode, [])

    useEffect(() => {
        setUserList([])
        requestForUserList()
    }, [isDealer])

    const requestForUserList = () => {
        setLoading(true)
        const requestOptions = { method: "GET", redirect: "follow" }
        var type = isDealer ? 'dealer' : 'sub dealer'
        fetch(UrlStorage.BaseUrlList.Saathi.base_url_saathi + "/ship-to-party-master-txt-V3.php?emp_code=" + UrlStorage.ParameterList.BasicData.customerDetails.customer_code + "&user_type=" + type + "&login_type=" + UrlStorage.ParameterList.BasicData.user_type.toLowerCase(), requestOptions)
            .then((response) => response.json())
            .then((result) => {
                var arr = []
                var arr1 = []
                if (isDealer) {
                    for (var i = 0; i < result.dealer_data.length; i++) {
                        if (result.dealer_data[i].cust_type == 'Dealer')
                            arr.push(result.dealer_data[i])
                        else
                            arr1.push(result.dealer_data[i])
                    }
                } else {
                    for (var i = 0; i < result.sub_dealer_data.length; i++) {
                        if (result.sub_dealer_data[i].cust_type == 'RSSD') {
                            result.sub_dealer_data[i].dns_customer_code = UrlStorage.ParameterList.BasicData.emp_id
                            arr.push(result.sub_dealer_data[i])
                        } else
                            arr1.push(result.sub_dealer_data[i])
                    }
                }
                setUserList(arr)
                setUserList1(arr1)
                setLoading(false)
            })
            .catch((error) => {
                setLoading(false)
                Toast.show({ type: 'error', text1: 'Sorry...', text2: 'Something went wrong.\Try again later.' })
            })
    }

    const onDealerDumpHasNotSameGST = () => {
        setShowGSTQuestionPopup(false)
        setShowGSTEntryPopup(true)
    }
    const onDealerDumpHasSameGST = () => {
        setShowGSTQuestionPopup(false)
        setShowGSTQuestionDealerPopup(true)
    }

    const onSubDealerHasNotGST = () => {
        setShowGSTQuestionPopup(false)
        setShowGSTQuestionDealerPopup(true)
    }
    const onSubDealerHasGST = () => {
        setShowGSTQuestionPopup(false)
        setShowGSTEntryPopup(true)
    }

    const handleMainBackPress = () => {
        setShowGSTQuestionPopup(false)
    }

    const handleDealerBackPress = () => {
        setShowGSTQuestionPopup(true)
        setShowGSTQuestionDealerPopup(false)
        setMentionNameError('')
    }
    const onChangeMentionName = useCallback((value) => {
        setMentionName(value)
    }, [mentionName])

    const onMentionNameSubmit = () => {
        if (mentionName.trim() == '') {
            setMentionNameError('Please enter Mention Name')
            return
        }
        setShowGSTQuestionDealerPopup(false)
        setShowGSTDeclarationPopup(true)
    }

    const handleChangeGSTNumber = useCallback((value) => {
        setGstNumber(value.toUpperCase())
        setGstNumberError(runTimeValidateGSTNumber(value.toUpperCase()))
    }, [gstNumberError])

    const handlePickGSTDocument = useCallback(async () => {
        try {
            Keyboard.dismiss()
            const [result] = await pick({ type: [types.pdf, types.images], })
            setGstDocument(result)
        } catch (err) {
            if (isErrorWithCode(err) && err.code === errorCodes.OPERATION_CANCELED)
                return
            Toast.show({ type: 'error', text1: 'Sorry...', text2: 'Unable to select document' })
        }
    }, [])

    const handleSubmitGSTDetails = () => {
        const error = validateGSTNumber(gstNumber)
        if (error) {
            setGstNumberError(error)
            return
        }
        setShowGSTEntryPopup(false)
        requestForNewGSTDetailsSubmit()
    }

    const handleBackPress = () => {
        setShowGSTQuestionPopup(true)
        setShowGSTEntryPopup(false)
    }

    const requestForNewGSTDetailsSubmit = () => {
        setLoading(true)
        const formdata = new FormData()
        formdata.append("customer_id", selectCustomerDetails.dns_customer_code)
        formdata.append("gst_no", gstNumber)
        formdata.append("is_any", "1")
        const requestOptions = { method: "POST", body: formdata, redirect: "follow" }
        fetch(UrlStorage.BaseUrlList.Saathi.base_url_saathi + "/upload_customer_gst_document-v1.php ", requestOptions)
            .then((response) => response.json())
            .then((result) => {
                requestForUserList()
                setGstDocument(null)
                setGstNumber('')
            })
            .catch((error) => { })
    }

    const handleGSTDeclarationConfirm = () => {
        requestForNoGSTDetailsSubmit()
    }

    const handleDeclarationBackPress = () => {
        setShowGSTQuestionDealerPopup(true)
        setShowGSTDeclarationPopup(false)
    }

    const requestForNoGSTDetailsSubmit = () => {
        setLoading(true)
        const formdata = new FormData()
        formdata.append("customer_id", selectCustomerDetails.dns_customer_code)
        formdata.append("is_any", "0")
        const requestOptions = { method: "POST", body: formdata, redirect: "follow" }
        fetch(UrlStorage.BaseUrlList.Saathi.base_url_saathi + "/upload_customer_gst_document-v1.php ", requestOptions)
            .then((response) => response.json())
            .then((result) => {
                setShowGSTDeclarationPopup(false)
                setMentionName('')
                setShowGSTNotePopup(true)
                requestForUserList()
            })
            .catch((error) => { })
    }

    const onCloseNotePopup = () => {
        setShowGSTNotePopup(false)
    }

    const uploadGST = (item) => {
        setSelectCustomerDetails(item)
        setCustType(item.cust_type)
        setShowGSTQuestionPopup(true)
    }

    const renderDealerItem = ({ item, index }) => {
        return (
            <View style={{ flex: 1, flexDirection: 'column', borderRadius: moderateScale(3), borderWidth: moderateScale(1), borderColor: '#eee', backgroundColor: '#FFF', elevation: 2, margin: moderateScale(3) }}>
                <View style={{ width: '100%', flexDirection: 'row', minHeight: moderateScale(30), alignItems: 'center', padding: moderateScale(5) }}>
                    <Text style={{ flex: 1, color: '#666', fontSize: moderateScale(13) }}>{item.cust_type == 'Dealer' ? 'Sold to Party' : 'Ship to Party'} Code</Text>
                    <Text style={{ flex: 3, color: '#000', fontSize: moderateScale(13) }}>{item.cust_type == 'Dealer' ? UrlStorage.ParameterList.BasicData.emp_id : item.dns_customer_code}</Text>
                </View>
                <View style={{ width: '100%', flexDirection: 'row', minHeight: moderateScale(30), alignItems: 'center', padding: moderateScale(5), backgroundColor: '#E41B1410' }}>
                    <Text style={{ flex: 1, color: '#666', fontSize: moderateScale(13) }}>{item.cust_type == 'Dealer' ? 'Sold to Party' : 'Ship to Party'} Address</Text>
                    <Text style={{ flex: 3, color: '#000', fontSize: moderateScale(13) }}>{item.address}</Text>
                </View>
                {item.is_any == '' ? null : item.is_any == '1' ? <View style={{ width: '100%', flexDirection: 'row', minHeight: moderateScale(30), alignItems: 'center', padding: moderateScale(5) }}>
                    <Text style={{ flex: 1, color: '#666', fontSize: moderateScale(13) }}>GSTIN</Text>
                    <Text style={{ flex: 3, color: '#000', fontSize: moderateScale(14), fontWeight: '500' }}>{item.gst_no}</Text>
                </View> : <View style={{ width: '100%', flexDirection: 'row', minHeight: moderateScale(30), alignItems: 'center', justifyContent: 'center', padding: moderateScale(5) }}>
                    <Text style={{ fontSize: moderateScale(15), fontWeight: '500', color: '#E41B14' }}>GSTIN not declared {'(URP)'}</Text>
                </View>}
                {item.gst_no == '' && (item.is_any == '0' || item.is_any == '') ? <>
                    <View style={{ height: moderateScale(5) }} />
                    <View style={{ width: '100%', flexDirection: 'row', justifyContent: 'flex-end', paddingHorizontal: moderateScale(10) }}>
                        <TouchableOpacity onPress={() => { uploadGST(item) }} style={{ paddingHorizontal: moderateScale(25), paddingVertical: moderateScale(5), alignItems: 'center', justifyContent: 'center', backgroundColor: item.is_any == '' ? '#E41B14' : '#d1c005', borderRadius: moderateScale(5), elevation: 2 }}>
                            <Text style={{ color: item.is_any == '' ? '#FFF' : '#000', fontSize: moderateScale(15), fontWeight: '500' }}>{item.is_any == '' ? 'Update GSTIN' : 'Edit GSTIN'}</Text>
                        </TouchableOpacity>
                    </View>
                    <View style={{ height: moderateScale(5) }} />
                </> : null}
            </View>
        )
    }

    const renderSubDealerItem = ({ item, index }) => {
        return (
            <View style={{ flex: 1, flexDirection: 'column', borderRadius: moderateScale(3), borderWidth: moderateScale(1), borderColor: '#eee', backgroundColor: '#FFF', elevation: 2, margin: moderateScale(3) }}>
                <View style={{ width: '100%', flexDirection: 'row', minHeight: moderateScale(30), alignItems: 'center', padding: moderateScale(5) }}>
                    <Text style={{ flex: 1, color: '#666', fontSize: moderateScale(13) }}>{item.cust_type == 'RSSD' ? 'RSAR' : 'Ship to Party'} Code</Text>
                    <Text style={{ flex: 3, color: '#000', fontSize: moderateScale(13) }}>{item.dns_customer_code}</Text>
                </View>
                <View style={{ width: '100%', flexDirection: 'row', minHeight: moderateScale(30), alignItems: 'center', padding: moderateScale(5), backgroundColor: '#E41B1410' }}>
                    <Text style={{ flex: 1, color: '#666', fontSize: moderateScale(13) }}>Ship to Party Address</Text>
                    <Text style={{ flex: 3, color: '#000', fontSize: moderateScale(13) }}>{item.address}</Text>
                </View>
                {item.is_any == '' ? null : item.is_any == '1' ? <View style={{ width: '100%', flexDirection: 'row', minHeight: moderateScale(30), alignItems: 'center', padding: moderateScale(5), backgroundColor: '#E41B1410' }}>
                    <Text style={{ flex: 1, color: '#666', fontSize: moderateScale(13) }}>GSTIN</Text>
                    <Text style={{ flex: 3, color: '#000', fontSize: moderateScale(14), fontWeight: '500' }}>{item.gst_no}</Text>
                </View> : <View style={{ width: '100%', flexDirection: 'row', minHeight: moderateScale(30), alignItems: 'center', justifyContent: 'center', padding: moderateScale(5) }}>
                    <Text style={{ fontSize: moderateScale(15), fontWeight: '500', color: '#E41B14' }}>GSTIN not declared</Text>
                </View>}
                {item.gst_no == '' && (item.is_any == '0' || item.is_any == '') ? <>
                    <View style={{ height: moderateScale(5) }} />
                    <View style={{ width: '100%', flexDirection: 'row', justifyContent: 'flex-end', paddingHorizontal: moderateScale(10) }}>
                        <TouchableOpacity onPress={() => { uploadGST(item) }} style={{ paddingHorizontal: moderateScale(25), paddingVertical: moderateScale(5), alignItems: 'center', justifyContent: 'center', backgroundColor: item.is_any == '' ? '#E41B14' : '#d1c005', borderRadius: moderateScale(5), elevation: 2 }}>
                            <Text style={{ color: item.is_any == '' ? '#FFF' : '#000', fontSize: moderateScale(15), fontWeight: '500' }}>{item.is_any == '' ? 'Update GSTIN' : 'Edit GSTIN'}</Text>
                        </TouchableOpacity>
                    </View>
                    <View style={{ height: moderateScale(5) }} />
                </> : null}
            </View>
        )
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: '#eee' }}>
                <SBSCommonHeaderView title={'GSTIN Update Confirmation'} backPath=" " Information={false} />
                <View style={{ width: "100%", flex: 1, padding: moderateScale(5) }}>
                    <ScrollView showsHorizontalScrollIndicator={false} showsVerticalScrollIndicator={false}>
                        <View>
                            <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: moderateScale(5), marginBottom: moderateScale(10) }}>
                                <Text style={{ marginHorizontal: moderateScale(10), fontSize: moderateScale(11), color: '#333', fontWeight: '600', letterSpacing: 0.5 }}> Self </Text>
                                <View style={{ flex: 1, height: 0.8, backgroundColor: '#d0d0d0' }} />
                            </View>
                            <FlatList
                                data={userList}
                                keyExtractor={(item, index) => index}
                                showsVerticalScrollIndicator={false}
                                ItemSeparatorComponent={() => <View style={{ height: moderateScale(3) }} />}
                                renderItem={isDealer ? renderDealerItem : renderSubDealerItem}
                            />
                            <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: moderateScale(5), marginBottom: moderateScale(10) }}>
                                <Text style={{ marginHorizontal: moderateScale(10), fontSize: moderateScale(11), color: '#333', fontWeight: '600', letterSpacing: 0.5 }}> Ship to Party </Text>
                                <View style={{ flex: 1, height: 0.8, backgroundColor: '#d0d0d0' }} />
                            </View>
                            <FlatList
                                data={userList1}
                                keyExtractor={(item, index) => index}
                                showsVerticalScrollIndicator={false}
                                ItemSeparatorComponent={() => <View style={{ height: moderateScale(3) }} />}
                                renderItem={isDealer ? renderDealerItem : renderSubDealerItem}
                            />
                        </View>
                    </ScrollView>

                </View>
            </View>
            <GSTQuestionPopup visible={showGSTQuestionPopup} primaryColor={primaryColor} selected={isDealer} cust_type={custType} onBackPress={handleMainBackPress} onDealerDumpHasNotSameGST={onDealerDumpHasNotSameGST} onSubDealerHasNotGST={onSubDealerHasNotGST} onDealerDumpHasSameGST={onDealerDumpHasSameGST} onSubDealerHasGST={onSubDealerHasGST} />
            <GSTQuestionDealerPopup visible={showGSTQuestionDealerPopup} primaryColor={primaryColor} onBackPress={handleDealerBackPress} mentionName={mentionName} mentionNameError={mentionNameError} onChangeMentionName={onChangeMentionName} onMentionNameSubmit={onMentionNameSubmit} />
            <GSTEntryPopup visible={showGSTEntryPopup} primaryColor={primaryColor} gstNumber={gstNumber} gstNumberError={gstNumberError} gstDocument={gstDocument} submitting={loading} onChangeGSTNumber={handleChangeGSTNumber} onPickDocument={handlePickGSTDocument} onSubmit={handleSubmitGSTDetails} onBackPress={handleBackPress} />
            <GSTDeclarationPopup visible={showGSTDeclarationPopup} primaryColor={primaryColor} onConfirm={handleGSTDeclarationConfirm} onBackPress={handleDeclarationBackPress} gstInfoUrl={UrlStorage.BaseUrlList.Saathi.base_url_saathi + '/gst_declaration.php?action=get_customer&customer_id=' + selectCustomerDetails.dns_customer_code + '&mention_name=' + mentionName} />
            <GSTNotePopup visible={showGSTNotePopup} primaryColor={primaryColor} onCloseNotePopup={onCloseNotePopup} />
            {loading && <Loader />}
        </SafeView>
    )
}
const styles = StyleSheet.create({
    inputContainer: { width: "100%", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), paddingHorizontal: moderateScale(12), height: moderateScale(44), justifyContent: 'center', backgroundColor: Colors.white },
    label: { color: Colors.text, fontSize: moderateScale(14), marginBottom: moderateScale(8) },
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
    input: { color: "#000000", fontSize: moderateScale(14), padding: 0, margin: 0, textAlignVertical: 'center', height: '100%' },
    dropdownContainer: { width: "100%", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), paddingHorizontal: moderateScale(12), flexDirection: 'row', alignItems: 'center', height: moderateScale(44), backgroundColor: Colors.white },
    dropdownText: { flex: 1, color: "#000000", fontSize: moderateScale(14) },
    placeholderText: { color: "#A7A7A7" },
    modalBackButton: { alignSelf: 'flex-start', marginBottom: moderateScale(4) },
    modalBackButtonText: { fontSize: moderateScale(12), color: '#D32F2F' },
    webviewContainer: { width: '100%', height: moderateScale(300), overflow: 'hidden', borderWidth: moderateScale(1), borderColor: '#DCDDDF', marginBottom: moderateScale(14), paddingHorizontal: moderateScale(10) },
    webview: { flex: 1 },
    checkboxRow: { flexDirection: 'row', alignItems: 'flex-start', marginBottom: moderateScale(4), paddingHorizontal: moderateScale(20) },
    checkboxBox: { width: moderateScale(20), height: moderateScale(20), borderRadius: moderateScale(4), borderWidth: moderateScale(1.5), borderColor: '#DCDDDF', alignItems: 'center', justifyContent: 'center', marginRight: moderateScale(10), marginTop: moderateScale(1) },
    checkboxTick: { color: Colors.white, fontSize: moderateScale(13), fontWeight: '700' },
    checkboxLabel: { flex: 1, color: Colors.text, fontSize: moderateScale(14), lineHeight: moderateScale(20) },
})
export default GSTScreenOld