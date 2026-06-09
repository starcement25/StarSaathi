import React, { useEffect, useState } from 'react'
import { Image, Linking, Platform, Text, TouchableHighlight, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Icons } from '../../assets/Icons';
import { moderateScale } from '../../helper/Window';
import UrlStorage from '../../storage/UrlStorage';
import { Colors } from '../../assets/Colors';
import SafeView from '../../helper/SafeView';
import DataStorage from '../../storage/DataStorage';
import DeviceInfo from 'react-native-device-info';

const SplashScreen = () => {
  const navigation = useNavigation();
  const [isUpdate, setIsUpdate] = useState(false)
  useEffect(() => {
    const timer = setTimeout(() => {
      checkAppVersion()
    }, 3000);
    return () => clearTimeout(timer);
  }, []);

  const checkAppVersion = () => {
    const myHeaders = new Headers();
    myHeaders.append("Accept", "application/json");

    const requestOptions = {
      method: "GET",
      headers: myHeaders,
      redirect: "follow"
    };
    var url = '';
     url = UrlStorage.BaseUrlList.Saathi.base_url_saathi+"/show_latest_app_version_v2.php"
     console.log(url);
     
    fetch(url, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        if (result.process_status == "YES") {
          if (Platform.OS == 'ios') {
            if (DeviceInfo.getVersion() == result.ios_app_version) {
              DataStorage.isFirstOpen = true
              checkLogin()
            } else {
             setIsUpdate(true)
            }
          } else {
            if (DeviceInfo.getVersion() == result.android_app_version) {
              DataStorage.isFirstOpen = true
              checkLogin()
            } else {
              setIsUpdate(true)
            }
          }
        }
      })
      .catch((error) => { });
  }

  const checkLogin = async () => {
    var is_login = await AsyncStorage.getItem('is_login')
    if (is_login == 1) {
      try {
        var user_info = await AsyncStorage.getItem('user_info')
        var user_details_dealerId = await AsyncStorage.getItem('user_details_dealerId')
        var user_details_mobileNumber = await AsyncStorage.getItem('user_details_mobileNumber')
        var result = JSON.parse(user_info)

        UrlStorage.ParameterList.BasicData.nick_name = "STAR"
        UrlStorage.ParameterList.BasicData.emp_code = result.emp_code
        UrlStorage.ParameterList.BasicData.customer_code = result.emp_code
        UrlStorage.ParameterList.BasicData.user_type = result.user_type
        UrlStorage.ParameterList.BasicData.incremental_download = "yes"
        UrlStorage.ParameterList.BasicData.broker_id = result.broker_id
        UrlStorage.ParameterList.BasicData.emp_id = user_details_dealerId
        UrlStorage.ParameterList.BasicData.emp_mobile_number = user_details_mobileNumber
        UrlStorage.ParameterList.BasicData.belong_dealer_name = result.belong_dealer_name
        UrlStorage.ParameterList.BasicData.belong_dealer_dns_code = result.belong_dealer_dns_code
        UrlStorage.ParameterList.BasicData.belong_dealer_code = result.belong_dealer_code
        UrlStorage.ParameterList.BasicData.customerDetails = result
        const image = await AsyncStorage.getItem('image_url')
        UrlStorage.ParameterList.BasicData.image_url = image

        navigation.replace("HomeScreen");
      } catch (error) {
        navigation.replace("LoginScreen");
      }
    } else {
      navigation.replace("LoginScreen");
    }
  }

  const gotoStoreForUpdate = async () => {
    const androidUrl = "market://details?id=org.forcepower.starcement";
    const iosUrl = "itms-apps://apps.apple.com/app/id6754075343";

    const fallbackAndroid = "https://play.google.com/store/apps/details?id=org.forcepower.starcement";
    const fallbackIos = "https://apps.apple.com/app/id6754075343";

    const url = Platform.OS === "android" ? androidUrl : iosUrl;
    const fallback = Platform.OS === "android" ? fallbackAndroid : fallbackIos;

    try {
      await Linking.openURL(url);
    } catch {
      Linking.openURL(fallback);
    }
  }

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.white}>
      <View style={{ width: "100%", height: "100%", alignItems: "center", justifyContent: "center" }}>
        <Image source={Icons.LogoCircle} style={{ width: moderateScale(200), height: moderateScale(200) }} />
      </View>
      {isUpdate && <View style={{ width: '100%', height: '100%', backgroundColor: '#00000060', position: 'absolute', alignItems: 'center', justifyContent: 'center' }}>
        <View style={{ width: '90%', paddingVertical: 10, paddingHorizontal: 20, borderRadius: 10, backgroundColor: '#FFF', flexDirection: 'column' }}>
          <Text style={{ color: '#000', fontWeight: '500', fontSize: 16 }}>Update</Text>
          <View style={{ height: 10 }} />
          <Text style={{ color: '#555', fontSize: 14, paddingHorizontal: 5 }}>New version available. Please update your application.</Text>
          <View style={{ height: 15 }} />
          <View style={{ width: '100%', height: 35, alignItems: 'center', justifyContent: 'center' }}>
            <TouchableHighlight onPress={gotoStoreForUpdate} style={{ width: '60%', height: '100%', backgroundColor: Colors.red, borderRadius: 10, alignItems: 'center', justifyContent: 'center' }}>
              <Text style={{ fontSize: 16, color: Colors.white, fontWeight: '500' }}>Update Now</Text>
            </TouchableHighlight>
          </View>
        </View>
      </View>}

    </SafeView>
  )
}

export default SplashScreen