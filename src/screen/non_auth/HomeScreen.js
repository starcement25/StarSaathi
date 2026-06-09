import React, { useCallback, useEffect, useState } from 'react'
import { Alert, BackHandler, Image, Platform, Text, TouchableOpacity, View } from 'react-native'
import Toast from 'react-native-toast-message'
import { useFocusEffect, } from '@react-navigation/native'
import SafeView from '../../helper/SafeView'
import { Colors } from '../../assets/Colors'
import { moderateScale } from '../../helper/Window'
import { Icons } from '../../assets/Icons'
import DataStorage from '../../storage/DataStorage'
import toastConfig from '../../helper/ToastConfig'
import Loader from '../../common/Loader'
import UrlStorage from '../../storage/UrlStorage'
import AuthNotVerifyPopupView from '../../auth/AuthNotVerifyPopupView'
import { AuthCheckingApi } from '../../auth/AuthCheckingApi'

const HomeScreen = (props) => {
  const [isSBS, setIsSBS] = useState(false)
  const [isCement, setIsCement] = useState(false)
  const [loading, setLoading] = useState(false)
  const [authChecker, setAuthChecker] = useState(false)

  useEffect(() => {
    DataStorage.typeOfUse = 1 && getDealer();
    //  setTimeout(()=>{
    //   gotoDashboardScreen(false)
    //  },1000)
  }, [])

  useFocusEffect(
    useCallback(() => {
      const onBackPress = () => {
        Alert.alert(
          "Exit App",
          "Are you sure you want to exit the app?",
          [
            { text: "Cancel", onPress: () => null, style: "cancel" },
            { text: "Yes", onPress: () => BackHandler.exitApp() },
          ],
          { cancelable: false }
        );
        return true;
      };

      const backHandler = BackHandler.addEventListener(
        "hardwareBackPress",
        onBackPress
      );

      return () => backHandler.remove();
    }, [])
  );

  const getDealer = async () => {
    setLoading(true)

    var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }

    const myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");
    const requestOptions = {
      method: "POST",
      redirect: "follow",
      headers: myHeaders,
      body: JSON.stringify({ dealer_id: UrlStorage.ParameterList.BasicData.emp_id })
    };
    var url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.order.dealer_details
    await fetch(url, requestOptions)
      .then((response) => {
        return response.json(); // ✅ return the parsed JSON Promise
      })
      .then((result) => {
        DataStorage.isSbsRegister = result?.data?.for_sbs
        DataStorage.isCementRegister = result?.data?.for_cement
        setIsSBS(result?.data?.for_sbs)
        setIsCement(result?.data?.for_cement)
      })
      .catch((error) => {
      });
    setLoading(false)
  }

  const gotoDashboardScreen = (isSelectSBS) => {
    if (isSelectSBS) {
      DataStorage.typeOfUse = 1
      DataStorage.primaryColorCode = '#8FC031'
      DataStorage.gradientColorCode = ['#8FC031', '#23A63C']
      DataStorage.transColorCode = "#F5F8EF"
      DataStorage.menuColorCode = "#F5F8EF"
    } else {
      DataStorage.typeOfUse = 2
      DataStorage.primaryColorCode = '#FF0900'
      DataStorage.gradientColorCode = ['#FF0900', '#FF0900']
      DataStorage.transColorCode = "#FFF1F0"
      DataStorage.menuColorCode = "#F5F8EF"
    }
    DataStorage.isFirstOpen = true
    props.navigation.navigate("SBSDashboardScreen");
  }

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.white}>
      <View style={{ width: "100%", height: "100%", backgroundColor: "#FFFFFF", alignItems: "center", justifyContent: "center", padding: moderateScale(20) }}>
        <View style={{ width: "100%", flex: 1 }}>
          <View style={{ width: "100%", alignItems: "center", justifyContent: "center" }}>
            <Text style={{ color: Colors.text, fontWeight: "600", fontSize: moderateScale(18) }}>Home</Text>
            <View style={{ height: moderateScale(30) }}></View>
            <Text style={{ color: Colors.text, fontWeight: "600", fontSize: moderateScale(22) }}>Welcome to Star Saathi</Text>
            <View style={{ height: moderateScale(10) }}></View>
            <Text style={{ color: Colors.textgrey, fontWeight: "500", fontSize: moderateScale(14) }}>Please Select your business type</Text>
            <Image source={Icons.LogoCircle} style={{ marginTop: moderateScale(20), width: "100%", height: moderateScale(150), resizeMode: 'contain' }} />
            {!loading && <View style={{ width: "100%", flexDirection: "row", alignItems: 'center', justifyContent: 'center', gap: moderateScale(20), marginTop: moderateScale(50), }}>
              {isSBS && <TouchableOpacity onPress={() => { gotoDashboardScreen(true) }} activeOpacity={0.95} style={{ borderWidth: 1, borderColor: '#f0f0f0', flex: 0.5, ...Platform.select({ ios: { shadowColor: "#000", shadowOffset: { width: 0, height: 40 }, shadowOpacity: 0.08, shadowRadius: 12, }, android: { elevation: 10 }, }), backgroundColor: 'white', borderRadius: moderateScale(20) }}>
                <View style={{ width: "100%", height: moderateScale(160), gap: moderateScale(10), padding: moderateScale(20), borderRadius: moderateScale(20), borderWidth: moderateScale(1), borderColor: '#0000', backgroundColor: "#8FC03126", alignItems: "center", justifyContent: "center" }}>
                  <Image source={Icons.SBS} style={{ width: moderateScale(110), height: moderateScale(65) }} />
                  <Text style={{ color: Colors.text, fontWeight: "500", fontSize: moderateScale(14) }}>SBS</Text>
                </View>
              </TouchableOpacity>}
              {isCement && <TouchableOpacity onPress={() => { gotoDashboardScreen(false) }} activeOpacity={0.95} style={{ borderWidth: 1, borderColor: '#f0f0f0', flex: 0.5, ...Platform.select({ ios: { shadowColor: "#000", shadowOffset: { width: 0, height: 40 }, shadowOpacity: 0.08, shadowRadius: 12, }, android: { elevation: 10 }, }), backgroundColor: 'white', borderRadius: moderateScale(20) }} >
                <View style={{ width: "100%", height: moderateScale(160), gap: moderateScale(10), padding: moderateScale(20), borderRadius: moderateScale(20), borderWidth: moderateScale(1), borderColor: '#0000', backgroundColor: "#E41B141A", alignItems: "center", justifyContent: "center" }}>
                  <Image source={Icons.Cement} style={{ width: moderateScale(90), height: moderateScale(80) }} />
                  <Text style={{ color: Colors.text, fontWeight: "500", fontSize: moderateScale(14) }}>Cement</Text>
                </View>
              </TouchableOpacity>}
            </View>}
            <View style={{ height: moderateScale(40) }}></View>
          </View>
        </View>
        {/* <TouchableOpacity activeOpacity={0.95} style={{ width: "100%", alignItems: "center" }} onPress={() => gotoDashboardScreen()}>
          <View style={{ width: "100%", height: moderateScale(40), borderRadius: moderateScale(10), backgroundColor: buttonColor, alignItems: "center", justifyContent: "center", }}>
            <Text style={{ color: Colors.white, fontSize: moderateScale(14), fontWeight: "500" }}>Confirm</Text>
          </View>
        </TouchableOpacity> */}
      </View>
      <Toast config={toastConfig} />
      {loading && <Loader />}
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  )
}

export default HomeScreen