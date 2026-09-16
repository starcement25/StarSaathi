import React, { useState } from 'react'
import { Image, Platform, Text, TextInput, TouchableOpacity, View } from 'react-native'
import Toast from 'react-native-toast-message'
import DeviceInfo from 'react-native-device-info'
import AsyncStorage from '@react-native-async-storage/async-storage'
import SafeView from '../../helper/SafeView'
import { moderateScale } from '../../helper/Window'
import { Colors } from '../../assets/Colors'
import { Icons } from '../../assets/Icons'
import toastConfig from '../../helper/ToastConfig'
import UrlStorage from '../../storage/UrlStorage'
import Loader from '../../common/Loader'
import { encryptToHex } from '../../helper/Crypto'
import { httpPostCallWithXmlResponseDecrypted } from '../../helper/HttpCalling'
import AlertForDuplicateNumber from '../../auth/AlertForDuplicateNumber'

const LoginScreen = (props) => {
    const [dealerId, setDealerId] = useState('')
    const [mobileNumber, setMobileNumber] = useState('')
    const [isLoading, setIsLoading] = useState(false)
    const [isWarningShow, setIsWarningShow] = useState(false)
    const [count, setCount] = useState(0)

    const checkData = () => {
        if (!dealerId) {
            Toast.show({ type: 'error', text1: 'Sorry', text2: 'Please enter dealer ID' })
            return
        }
        if (dealerId.length < 5) {
            Toast.show({ type: 'error', text1: 'Sorry', text2: 'Please enter valid dealer ID' })
            return
        }
        if (!mobileNumber) {
            Toast.show({ type: 'error', text1: 'Sorry', text2: 'Please enter registered mobile number' })
            return
        }
        if (mobileNumber.length !== 10) {
            Toast.show({ type: 'error', text1: 'Sorry', text2: 'Please enter valid mobile number' })
            return
        }
        requestForCheckNumber()
    }

    const requestForCheckNumber = async () => {
        try {
            setIsLoading(true)
            const requestOptions = { method: "GET", redirect: "follow" }
            fetch(UrlStorage.BaseUrlList.Saathi.base_url_saathi + "/duplicate_phone_alert.php?customer_id=" + dealerId, requestOptions)
                .then((response) => response.json())
                .then((result) => {
                    if (result.status) {
                        if (result.duplicate) {
                            setIsLoading(false)
                            setCount(result.total_records)
                            setIsWarningShow(true)
                        } else
                            requestForLogin()
                    } else {
                        setIsLoading(false)
                        Toast.show({ type: 'error', text1: 'Sorry', text2: result.sms_alert || 'Try again..', })
                    }
                })
                .catch((error) => {
                    setIsLoading(false)
                    Toast.show({ type: 'error', text1: 'Network Error', text2: 'Please try again later', })
                })
        } catch (error) {
            setIsLoading(false)
            Toast.show({ type: 'error', text1: 'Network Error', text2: 'Please try again later', })
        }
    }

    const requestForLogin = async () => {
        try {
            setIsLoading(true)
            const deviceId = `${DeviceInfo.getModel()}:${DeviceInfo.getDeviceId()}`
            const encryptedPayload = {
                nickname: encryptToHex('star'),
                phonenumber: encryptToHex(mobileNumber.trim()),
                deviceid: encryptToHex(deviceId),
                dealer_id: encryptToHex(dealerId.trim()),
            }
            const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.AuthURL.login_url
            const decryptedResponseString = await httpPostCallWithXmlResponseDecrypted(url, JSON.stringify(encryptedPayload))
            if (!decryptedResponseString || decryptedResponseString === 'Network Failure')
                throw new Error('Network Failure')
            let cleanResponse = decryptedResponseString
            cleanResponse = cleanResponse.trim()
            const firstBrace = cleanResponse.indexOf('{')
            const lastBrace = cleanResponse.lastIndexOf('}')
            if (firstBrace === -1 || lastBrace === -1)
                throw new Error('Invalid JSON response')
            cleanResponse = cleanResponse.substring(firstBrace, lastBrace + 1)
            const response = JSON.parse(cleanResponse)
            if (response.process_status && response.process_status.toLowerCase() === 'yes') {
                UrlStorage.ParameterList.BasicData.nick_name = 'star'
                UrlStorage.ParameterList.BasicData.emp_code = response.emp_code
                UrlStorage.ParameterList.BasicData.customer_code = response.emp_code
                UrlStorage.ParameterList.BasicData.user_type = response.user_type
                UrlStorage.ParameterList.BasicData.incremental_download = 'yes'
                UrlStorage.ParameterList.BasicData.broker_id = response.broker_id
                UrlStorage.ParameterList.BasicData.emp_id = dealerId
                UrlStorage.ParameterList.BasicData.emp_mobile_number = mobileNumber
                UrlStorage.ParameterList.BasicData.belong_dealer_name = response.belong_dealer_name
                UrlStorage.ParameterList.BasicData.belong_dealer_dns_code = response.belong_dealer_dns_code
                UrlStorage.ParameterList.BasicData.belong_dealer_code = response.belong_dealer_code
                UrlStorage.ParameterList.BasicData.customerDetails = response
                await AsyncStorage.setItem('user_info', JSON.stringify(response))
                await AsyncStorage.setItem('user_details_dealerId', dealerId)
                await AsyncStorage.setItem('user_details_mobileNumber', mobileNumber)
                setIsLoading(false)
                props.navigation.navigate('OTPScreen')
            } else {
                setIsLoading(false)
                Toast.show({ type: 'error', text1: 'Sorry', text2: response.process_message || 'Try again..', })
            }
        } catch (error) {
            setIsLoading(false)
            Toast.show({ type: 'error', text1: 'Network Error', text2: 'Please try again later', })
        }
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.white} dismissOnPress={Platform.OS == 'ios'} >
            <View style={{ height: '100%', width: '100%', backgroundColor: '#FFFFFF', alignItems: 'center', justifyContent: 'space-between', paddingVertical: moderateScale(40), paddingHorizontal: moderateScale(20), }} >
                <View style={{ width: '100%', alignItems: 'center' }}>
                    <Image source={Icons.LogoSquare} style={{ width: moderateScale(200), height: moderateScale(200) }} />
                    <View style={{ height: moderateScale(60) }} />
                    <View style={{ width: '100%', gap: moderateScale(8) }}>
                        <Text style={{ color: '#1E1E1E', fontSize: moderateScale(14), fontWeight: '500' }}> Dealer ID </Text>
                        <View style={{ width: '100%', height: moderateScale(44), borderRadius: moderateScale(10), borderColor: '#E5E5E5', borderWidth: 1, paddingHorizontal: moderateScale(10), }} >
                            <TextInput
                                placeholder="Enter Dealer ID"
                                placeholderTextColor="#AFAFAF"
                                style={{ flex: 1, color: '#1E1E1E', fontSize: moderateScale(14) }}
                                value={dealerId}
                                onChangeText={setDealerId}
                            />
                        </View>
                    </View>
                    <View style={{ height: moderateScale(20) }} />
                    <View style={{ width: '100%', gap: moderateScale(8) }}>
                        <Text style={{ color: '#1E1E1E', fontSize: moderateScale(14), fontWeight: '500' }}> Registered Mobile Number </Text>
                        <View style={{ width: '100%', height: moderateScale(44), borderRadius: moderateScale(10), borderColor: '#E5E5E5', borderWidth: 1, paddingHorizontal: moderateScale(10), }} >
                            <TextInput
                                placeholder="Enter Mobile Number"
                                placeholderTextColor="#AFAFAF"
                                style={{ flex: 1, color: '#1E1E1E', fontSize: moderateScale(14) }}
                                value={mobileNumber}
                                onChangeText={(text) => setMobileNumber(text.replace(/[^0-9]/g, ''))}
                                keyboardType="number-pad"
                                maxLength={10}
                            />
                        </View>
                    </View>
                </View>
                <TouchableOpacity onPress={checkData} activeOpacity={0.95} style={{ width: '100%', height: moderateScale(40), borderRadius: moderateScale(10), backgroundColor: Colors.main, alignItems: 'center', justifyContent: 'center', }} >
                    <Text style={{ color: Colors.white, fontSize: moderateScale(14), fontWeight: '500' }}> Continue </Text>
                </TouchableOpacity>
            </View>
            <Toast config={toastConfig} />
            <AlertForDuplicateNumber isVisible={isWarningShow} onClose={() => setIsWarningShow(false)} count={count} loginPress={() => {
                setIsWarningShow(false)
                requestForLogin()
            }} />
            {isLoading && <Loader />}
        </SafeView>
    )
}

export default LoginScreen
