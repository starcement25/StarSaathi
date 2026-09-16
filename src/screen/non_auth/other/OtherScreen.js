import React, { useEffect, useState } from "react"
import { ActivityIndicator, FlatList, Text, View } from "react-native"
import SafeView from "../../../helper/SafeView"
import SBSCommonHeaderView from "../../../common/SBSCommonHeaderView"
import { Colors } from "../../../assets/Colors"
import DataStorage from "../../../storage/DataStorage"
import BasicStorage from "../../../storage/BasicStorage"
import { moderateScale } from "../../../helper/Window"
import UrlStorage from "../../../storage/UrlStorage"
import WebView from "react-native-webview"
import { AuthCheckingApi } from "../../../auth/AuthCheckingApi"
import AuthNotVerifyPopupView from "../../../auth/AuthNotVerifyPopupView"

const OtherScreen = () => {
  const [title, setTitle] = useState("")
  const [itemList, setItemList] = useState([])
  const [authChecker, setAuthChecker] = useState(false)

  useEffect(() => {
    if (DataStorage.select_type_of_option == "t_and_c_1")
      setTitle("Terms & Conditions")
    if (DataStorage.select_type_of_option == "p_p_1")
      setTitle("Privacy Policy")
    if (DataStorage.select_type_of_option == "r_p_1")
      setTitle("Refund Policy")
    if (DataStorage.select_type_of_option == "about_us_1")
      setTitle("About Us")
    if (DataStorage.select_type_of_option == "t_and_c_1")
      requestForTC()
    if (DataStorage.select_type_of_option == "p_p_1")
      requestForPP()
    if (DataStorage.select_type_of_option == "r_p_1")
      requestForRP()
    if (DataStorage.select_type_of_option == "about_us_1")
      requestForAboutUs()
  }, [])

  const requestForTC = async () => {
    const requestOptions = { method: "GET", redirect: "follow", }
    var a = await AuthCheckingApi()
    if (!a) {
      setAuthChecker(true)
      return false
    }
    var url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.other.t_c_url
    fetch(url, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        setItemList(result)
      }).catch((error) => { })
  }

  const requestForPP = async () => {
    const requestOptions = { method: "GET", redirect: "follow", }
    var a = await AuthCheckingApi()
    if (!a) {
      setAuthChecker(true)
      return false
    }
    var url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.other.p_p_url
    fetch(url, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        setItemList(result)
      }).catch((error) => { })
  }

  const requestForRP = async () => {
    const requestOptions = { method: "GET", redirect: "follow", }
    var a = await AuthCheckingApi()
    if (!a) {
      setAuthChecker(true)
      return false
    }
    var url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.other.r_p_url
    fetch(url, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        setItemList(result)
      }).catch((error) => { })
  }

  const requestForAboutUs = async () => {
    const requestOptions = { method: "GET", redirect: "follow", }
    var a = await AuthCheckingApi()
    if (!a) {
      setAuthChecker(true)
      return false
    }
    var url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.other.about_us_url
    fetch(url, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        setItemList(result)
      }).catch((error) => { })
  }

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main} >
      <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }} >
        <SBSCommonHeaderView title={title} backPath="" />

        <View style={{ width: "100%", flex: 1, padding: moderateScale(20) }}>
          {DataStorage.select_type_of_option == "t_and_c" ? <Text style={{ fontSize: moderateScale(14), color: "#333" }}> {BasicStorage.t_and_c} </Text> : null}
          {DataStorage.select_type_of_option == "p_p_1" ? <View style={{ flex: 1 }}>
            <WebView
              source={{ uri: UrlStorage.BaseUrlList.Saathi.base_url_saathi + "/privacy.html" }}
              style={{ flex: 1 }}
              javaScriptEnabled
              domStorageEnabled
              startInLoadingState
              showsVerticalScrollIndicator={false}
              renderLoading={() => (<ActivityIndicator size="large" color="white" style={{ flex: 1 }} />)}
              onLoad={() => { }}
              onLoadEnd={() => { }}
            />
          </View> : null}
          {DataStorage.select_type_of_option == "r_p" ? <Text style={{ fontSize: moderateScale(14), color: "#333" }}> {BasicStorage.r_p} </Text> : null}
          {DataStorage.select_type_of_option == "t_and_c_1" || DataStorage.select_type_of_option == "p_p" || DataStorage.select_type_of_option == "r_p_1" || DataStorage.select_type_of_option == "about_us_1" ? <View style={{ width: "100%" }}>
            <FlatList
              data={itemList}
              keyExtractor={(item) => item.id}
              showsVerticalScrollIndicator={false}
              decelerationRate="fast"
              renderItem={({ item }) => (
                <Text style={{ fontSize: moderateScale(14), color: "#333" }} > {item.content} </Text>
              )}
            />
          </View> : null}
        </View>
      </View>
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  )
}

export default OtherScreen
