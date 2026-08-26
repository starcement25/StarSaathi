import React, { useEffect, useState } from "react";
import { Image, Platform, Text, TextInput, TouchableOpacity, View, PermissionsAndroid } from "react-native";
import DeviceInfo from "react-native-device-info";
import Toast from "react-native-toast-message";
import AsyncStorage from "@react-native-async-storage/async-storage";
import SafeView from "../../helper/SafeView";
import { Colors } from "../../assets/Colors";
import { moderateScale } from "../../helper/Window";
import { Icons } from "../../assets/Icons";
import toastConfig from "../../helper/ToastConfig";
import UrlStorage from "../../storage/UrlStorage";
import Loader from "../../common/Loader";
import DataStorage from "../../storage/DataStorage";
import { encryptToHex } from '../../helper/Crypto';
import { httpPostCallWithXmlResponseDecrypted } from '../../helper/HttpCalling';
import axios from 'axios';
import messaging from '@react-native-firebase/messaging'
const OTPScreen = (props) => {
    const [otp, setOtp] = useState(["", "", "", ""]);
    const [isFocused, setIsFocused] = useState(Array(otp.length).fill(false));
    const inputRefs = [];
    const [isLoading, setIsLoading] = useState(false);
    const [registrationid, setregistrationid] = useState('');

    useEffect(() => {
        const setupNotifications = async () => {
            if (Platform.OS === 'android' && Platform.Version >= 33) {
                await PermissionsAndroid.request(
                    PermissionsAndroid.PERMISSIONS.POST_NOTIFICATIONS
                );
            }

            const authStatus = await messaging().requestPermission();
            const enabled =
                authStatus === messaging.AuthorizationStatus.AUTHORIZED ||
                authStatus === messaging.AuthorizationStatus.PROVISIONAL;

            if (enabled) {
                try {
                    const fcmToken = await messaging().getToken();
                    setregistrationid(fcmToken)
                    console.log('[FCM] Token:', fcmToken);
                    // save/send fcmToken to your backend or state here
                } catch (e) {
                    console.log('[FCM] getToken error:', e);
                }
            } else {
                console.log('[FCM] Permission denied by user.');
            }
        };

        setupNotifications();

        const unsubscribeForeground = messaging().onMessage(async remoteMessage => {
            console.log('[FCM] Foreground message:', remoteMessage);
        });

        const unsubscribeOpen = messaging().onNotificationOpenedApp(remoteMessage => {
            console.log('[FCM] Opened from background:', remoteMessage);
        });

        messaging()
            .getInitialNotification()
            .then(remoteMessage => {
                if (remoteMessage) {
                    console.log('[FCM] Opened from quit state:', remoteMessage);
                }
            });

        // Optional: listen for token refresh (tokens can rotate)
        const unsubscribeTokenRefresh = messaging().onTokenRefresh(newToken => {
            console.log('[FCM] Token refreshed:', newToken);
            // update backend with newToken
        });

        return () => {
            unsubscribeForeground();
            unsubscribeOpen();
            unsubscribeTokenRefresh();
        };
    }, []);


    const handleInputChange = (text, index) => {
        const newOtp = [...otp];
        newOtp[index] = text;
        setOtp(newOtp);
        if (text && index < 3) {
            inputRefs[index + 1].focus();
        }
    };

    const handleKeyPress = (e, index) => {
        if (e.nativeEvent.key === "Backspace" && index > 0 && !otp[index]) {
            if (otp[index] == "") {
                const newFocused = [...isFocused];
                newFocused[index] = false;
                setIsFocused(newFocused);
            }
            inputRefs[index - 1].focus();
        } else {
            if (e.nativeEvent.key != "Backspace" && index > 0 && !otp[index]) {
                const newFocused = [...isFocused];
                newFocused[index + 1] = true;
                setIsFocused(newFocused);
            }
        }
    };

    const checkData = () => {
        if (otp.join("") == "") {
            Toast.show({ type: "error", text1: "Sorry", text2: "Please enter your OTP", });
        } else if (otp.join("").length != 4) {
            Toast.show({ type: "error", text1: "Sorry", text2: "Please enter your OTP", });
        } else {
            setIsLoading(true);
            requestForOtpVerification();
        }
    };
    const parseResponseSafely = (data) => {
        if (typeof data === 'object') {
            return data;
        }

        if (typeof data === 'string') {
            const firstBrace = data.indexOf('{');
            const lastBrace = data.lastIndexOf('}');

            if (firstBrace === -1 || lastBrace === -1) {
                throw new Error('Invalid JSON format');
            }

            const cleanJson = data.substring(firstBrace, lastBrace + 1);
            return JSON.parse(cleanJson);
        }

        throw new Error('Unknown response type');
    };

    const updateRegistration = async () => {
        try {
            const url = `${UrlStorage.BaseUrlList.SBS.base_url_sbs}${UrlStorage.NonAuthURL.SBS.dashboard.update_user_sbs}`;
            const formBody = new URLSearchParams();
            formBody.append('deviceId', DeviceInfo.getModel() + ":" + DeviceInfo.getDeviceId());
            formBody.append('emp_code', UrlStorage.ParameterList.BasicData.emp_code);
            formBody.append('registrationid', registrationid);
            formBody.append('device_type', Platform.OS === 'ios' ? "iOS" : "ANDROID");
            formBody.append('app_version', DeviceInfo.getVersion());
            formBody.append('dealer_id', UrlStorage.ParameterList.BasicData.emp_id);
            formBody.append('customer_code', UrlStorage.ParameterList.BasicData.emp_code);
            console.log(registrationid);

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Authorization': UrlStorage.ParameterList.BasicData.user_type == 'broker' ? `SAP_SP` : `SAP_DEALER` + `${UrlStorage.ParameterList.BasicData.emp_id}`,
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formBody.toString(),
            });

            const json = await response.json();

            return json;
        } catch (e) {
            return null;
        }
    };

     const updateRegistration1 = async () => {
        try {
            const url = `${UrlStorage.BaseUrlList.Saathi.base_url_saathi}${UrlStorage.NonAuthURL.Saathi.DashboardURL.update_firebase_token_url}`;
            const formBody = new URLSearchParams();
            formBody.append('deviceId', DeviceInfo.getModel() + ":" + DeviceInfo.getDeviceId());
            formBody.append('emp_code', UrlStorage.ParameterList.BasicData.emp_code);
            formBody.append('registrationid', registrationid);
            formBody.append('device_type', Platform.OS === 'ios' ? "iOS" : "ANDROID");
            formBody.append('app_version', DeviceInfo.getVersion());
            formBody.append('dealer_id', UrlStorage.ParameterList.BasicData.emp_id);
            formBody.append('customer_code', UrlStorage.ParameterList.BasicData.emp_code);
            console.log(registrationid);

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Authorization': UrlStorage.ParameterList.BasicData.user_type == 'broker' ? `SAP_SP` : `SAP_DEALER` + `${UrlStorage.ParameterList.BasicData.emp_id}`,
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formBody.toString(),
            });

            const json = await response.json();

            return json;
        } catch (e) {
            return null;
        }
    };


    const requestForOtpVerification = async () => {
        try {
            setIsLoading(true);

            const deviceId = `${DeviceInfo.getModel()}:${DeviceInfo.getDeviceId()}`;
            const encryptedPayload = {
                nickname: encryptToHex(UrlStorage.ParameterList.BasicData.nick_name),
                phonenumber: encryptToHex(UrlStorage.ParameterList.BasicData.emp_mobile_number),
                deviceid: encryptToHex(deviceId),
                device_type: encryptToHex(Platform.OS === 'ios' ? "iOS" : "ANDROID"),
                app_version: encryptToHex(DeviceInfo.getVersion()),
                dealer_id: encryptToHex(UrlStorage.ParameterList.BasicData.emp_id),
                the_otp: encryptToHex(otp.join('')),
            };

            const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.AuthURL.otp_verification_url;
            const decryptedResponseString = await httpPostCallWithXmlResponseDecrypted(url, JSON.stringify(encryptedPayload));
            console.log("repponse===", decryptedResponseString);
            console.log("repponse===", encryptedPayload);


            if (!decryptedResponseString || decryptedResponseString === 'Network Failure') {
                throw new Error('Network Failure');
            }

            const response = parseResponseSafely(decryptedResponseString)

            if (response.process_status && response.process_status.toLowerCase() === 'yes') {
                UrlStorage.ParameterList.BasicData.user_type = response.user_type;
                UrlStorage.ParameterList.BasicData.belong_dealer_code = response.belong_dealer_code;
                UrlStorage.ParameterList.BasicData.belong_dealer_dns_code = response.belong_dealer_dns_code;
                UrlStorage.ParameterList.BasicData.belong_dealer_name = response.belong_dealer_name;
                UrlStorage.ParameterList.BasicData.dns_emp_code = response.dns_emp_code;
                UrlStorage.ParameterList.BasicData.emp_code = response.emp_code;
                UrlStorage.ParameterList.BasicData.customer_code = response.emp_code;

                if (UrlStorage?.BaseUrlList?.Saathi?.base_url_saathi?.includes("dev")) {
                    const url = response.the_profile_image_url?.replace("http://", "https://");
                    UrlStorage.ParameterList.BasicData.image_url = url?.replace("https://dev.starsaathi.com", "https://starsaathi.com/SAP")
                    await AsyncStorage.setItem("image_url", url?.replace("https://dev.starsaathi.com", "https://starsaathi.com/SAP"))
                } else {
                    const url = response.the_profile_image_url?.replace("http://", "https://");
                    UrlStorage.ParameterList.BasicData.image_url = url?.replace("https://starsaathi.com", "https://starsaathi.com/SAP")
                    await AsyncStorage.setItem("image_url", url?.replace("https://starsaathi.com", "https://starsaathi.com/SAP"))
                }

                UrlStorage.ParameterList.BasicData.is_survey_form_submitted = response.is_survey_form_submitted;

                UrlStorage.ParameterList.BasicData.customerDetails = response;

                await AsyncStorage.setItem('is_login', '1');
                await AsyncStorage.setItem('auth_token', response.token);
                await AsyncStorage.setItem('user_info', JSON.stringify(response));
                DataStorage.isFirstOpen = true;

                setIsLoading(false);
                updateRegistration()
                updateRegistration1()
                props.navigation.reset({
                    index: 0,
                    routes: [{ name: 'HomeScreen' }],
                });
            } else {
                setIsLoading(false);
                Toast.show({ type: 'error', text1: 'Sorry', text2: response.process_message || 'Invalid OTP', });
            }
        } catch (error) {
            setIsLoading(false);
            Toast.show({ type: 'error', text1: 'Network Error', text2: 'Please try again later', });
        }
    };

    const requestForResendOTP = async () => {
        try {
            setIsLoading(true);

            const deviceId = `${DeviceInfo.getModel()}:${DeviceInfo.getDeviceId()}`;

            const encryptedPayload = {
                nickname: encryptToHex('star'),
                phonenumber: encryptToHex(UrlStorage.ParameterList.BasicData.emp_mobile_number),
                deviceid: encryptToHex(deviceId),
                dealer_id: encryptToHex(UrlStorage.ParameterList.BasicData.emp_id),
            };
            console.log(encryptedPayload);


            const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.AuthURL.login_url;
            console.log(url);


            const decryptedResponseString = await httpPostCallWithXmlResponseDecrypted(url, JSON.stringify(encryptedPayload));

            if (!decryptedResponseString || decryptedResponseString === 'Network Failure') {
                throw new Error('Network Failure');
            }

            let cleanResponse = decryptedResponseString;
            console.log(cleanResponse);


            cleanResponse = cleanResponse.trim();

            const firstBrace = cleanResponse.indexOf('{');
            const lastBrace = cleanResponse.lastIndexOf('}');

            if (firstBrace === -1 || lastBrace === -1) {
                throw new Error('Invalid JSON response');
            }

            cleanResponse = cleanResponse.substring(firstBrace, lastBrace + 1);

            const response = JSON.parse(cleanResponse);
            console.log(response);


            setIsLoading(false);

            if (response.process_status === 'YES') {
                Toast.show({ type: 'success', text1: 'Success', text2: response.process_message, });
            } else {
                Toast.show({ type: 'error', text1: 'Sorry', text2: response.process_message, });
            }
        } catch (e) {
            console.log(e);

            setIsLoading(false);
            Toast.show({ type: 'error', text1: 'Network Error', text2: 'Please try again later', });
        }
    };

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main} dismissOnPress={Platform.OS == 'ios'} >
            <View style={{ width: "100%", height: "100%", backgroundColor: "#FFFFFF", alignItems: "center", justifyContent: "center", padding: moderateScale(20), }} >
                <View style={{ width: "100%", flex: 1 }}>
                    <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between", }} >
                        <TouchableOpacity onPress={() => props.navigation.goBack()} style={{ shadowColor: Colors.main, shadowRadius: moderateScale(20), shadowOffset: { width: 0, height: 2 }, elevation: 6, width: moderateScale(36), height: moderateScale(36), borderRadius: moderateScale(20), backgroundColor: Colors.main, alignItems: "center", justifyContent: "center", }} >
                            <Image source={Icons.Back} style={{ width: moderateScale(15), height: moderateScale(15), tintColor: "#FFFFFF", }} />
                        </TouchableOpacity>
                        <Text style={{ color: Colors.text, fontWeight: "600", fontSize: moderateScale(18), }} >
                            Verify Mobile
                        </Text>
                        <View style={{ width: moderateScale(36), height: moderateScale(36) }} >
                            {/* <Image source={Icons.Back} style={{ width: moderateScale(15), height: moderateScale(15), tintColor: "#FFFFFF" }} /> */}
                        </View>
                    </View>
                    <View style={{ height: moderateScale(40) }}></View>
                    <View style={{ width: "100%", alignItems: "center" }}>
                        <Image source={Icons.OTP} style={{ width: moderateScale(150), height: moderateScale(150) }} />
                        <View style={{ height: moderateScale(60) }}></View>
                        <View style={{ width: "100%", marginTop: moderateScale(20), alignItems: "center", paddingHorizontal: moderateScale(10), }} >
                            <Text style={{ color: Colors.text, fontWeight: "600", fontSize: moderateScale(15), }} >
                                Please enter the 4 digit code sent to you
                            </Text>
                            <View style={{ height: moderateScale(20) }} />
                            <View style={{ paddingHorizontal: moderateScale(10), flexDirection: "row", width: "100%", alignItems: "center", justifyContent: "center", gap: moderateScale(10), }} >
                                {otp.map((digit, index) => (
                                    <TextInput
                                        key={index}
                                        style={{ borderWidth: 1, height: moderateScale(40), width: moderateScale(40), borderRadius: 10, borderColor: isFocused[index] ? "#FFD6D4" : "#DEDEDE", backgroundColor: "#FFF", color: "#000", fontSize: 18, textAlign: "center", }}
                                        value={digit}
                                        onChangeText={(text) => handleInputChange(text, index)}
                                        keyboardType="number-pad"
                                        maxLength={1}
                                        ref={(ref) => (inputRefs[index] = ref)}
                                        onKeyPress={(e) => handleKeyPress(e, index)}
                                        onFocus={() => {
                                            let updated = [...isFocused];
                                            updated[index] = true;
                                            setIsFocused(updated);
                                        }}
                                        onBlur={() => {
                                            let updated = [...isFocused];
                                            updated[index] = false;
                                            setIsFocused(updated);
                                        }}
                                    />
                                ))}
                            </View>
                            <View style={{ height: moderateScale(20) }} />
                            <TouchableOpacity activeOpacity={0.95} onPress={() => { requestForResendOTP() }} style={{ width: "100%" }} >
                                <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "center", gap: moderateScale(6), }} >
                                    <Image source={Icons.Resend} style={{ width: moderateScale(20), height: moderateScale(20), }} />
                                    <Text style={{ color: Colors.text, fontWeight: "500", fontSize: moderateScale(15), textAlign: "center", }} >
                                        Resend Code
                                    </Text>
                                </View>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
                <TouchableOpacity activeOpacity={0.95} style={{ width: "100%", alignItems: "center" }} onPress={() => { checkData() }} >
                    <View style={{ width: "100%", height: moderateScale(40), borderRadius: moderateScale(10), backgroundColor: Colors.main, alignItems: "center", justifyContent: "center", }} >
                        <Text style={{ color: Colors.white, fontSize: moderateScale(14), fontWeight: "500", }} >
                            Confirm
                        </Text>
                    </View>
                </TouchableOpacity>
            </View>
            <Toast config={toastConfig} />
            {isLoading && <Loader />}
        </SafeView>
    );
};

export default OTPScreen;
