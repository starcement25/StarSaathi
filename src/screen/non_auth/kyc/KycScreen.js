import React, { useState, useRef, useEffect } from "react"
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Platform, Animated } from "react-native"
import DateTimePicker from "@react-native-community/datetimepicker"
import SafeView from "../../../helper/SafeView"
import SBSCommonHeaderView from "../../../common/SBSCommonHeaderView"
import { Colors } from "../../../assets/Colors"
import { moderateScale } from "../../../helper/Window"
import Toast from "react-native-toast-message"
import toastConfig from "../../../helper/ToastConfig"
import UrlStorage from "../../../storage/UrlStorage"
import Loader from "../../../common/Loader"
import { AuthCheckingApi } from "../../../auth/AuthCheckingApi"
import AuthNotVerifyPopupView from "../../../auth/AuthNotVerifyPopupView"

const KycScreen = (props) => {
  const { navigation } = props
  const [whatsapp, setWhatsapp] = useState("")
  const [dob, setDob] = useState("")
  const [marriageDate, setMarriageDate] = useState("")
  const [email, setEmail] = useState("")
  const [showPicker, setShowPicker] = useState(false)
  const [pickerMode, setPickerMode] = useState("date")
  const [pickerFor, setPickerFor] = useState("")
  const [loading, setLoading] = useState(true)
  const [isKycBefore, setIsKycBefore] = useState(false)
  const [authChecker, setAuthChecker] = useState(false)
  const [kycDetails, setKycDetails] = useState()

  const fieldsAnim = [
    useRef(new Animated.Value(0)).current,
    useRef(new Animated.Value(0)).current,
    useRef(new Animated.Value(0)).current,
    useRef(new Animated.Value(0)).current,
    useRef(new Animated.Value(0)).current,
  ]

  useEffect(() => {
    requestKycDataCement(UrlStorage.ParameterList.BasicData.emp_code)
  }, [])

  const showDatePicker = (field) => {
    setPickerFor(field)
    setPickerMode("date")
    setShowPicker(true)
  }

  const onDateChange = (event, selectedDate) => {
    setShowPicker(false)
    if (event.type === "set" && selectedDate) {
      const today = new Date()
      if (pickerFor === "dob" && selectedDate > today) {
        alert("Date of Birth cannot be a future date")
        return
      }
      const formatted = selectedDate.toISOString().split("T")[0]
      if (pickerFor === "dob") setDob(formatted)
      if (pickerFor === "marriage") setMarriageDate(formatted)
    }
  }

  const requestKycDataCement = async (emp_code) => {
    setLoading(true)
    const requestOptions = { method: "GET", redirect: "follow", }
    var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.KYCURL.KYC_details_url + "?emp_code=" + emp_code
    var a = await AuthCheckingApi()
    if (!a) {
      setAuthChecker(true)
      setLoading(false)
      return false
    }
    await fetch(url, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        if (result.process_status == "NO")
          setIsKycBefore(false)
        else {
          setIsKycBefore(true)
          setKycDetails(result)
          setWhatsapp(result?.employee_kyc_data[0].whatsapp_no)
          setDob(result?.employee_kyc_data[0].dob)
          setMarriageDate(result?.employee_kyc_data[0].dom)
          setEmail(result?.employee_kyc_data[0].email_id)
        }
        const animations = fieldsAnim.map((anim) =>
          Animated.timing(anim, { toValue: 1, duration: 500, useNativeDriver: true, })
        )
        Animated.stagger(150, animations).start()
      })
      .catch((error) => {
      })
    setLoading(false)
  }

  const updateKycDetails = async () => {
    var a = await AuthCheckingApi()
    if (!a) {
      setAuthChecker(true)
      setLoading(false)
      return false
    }
    const formdata = new FormData()
    formdata.append("emp_code", UrlStorage.ParameterList.BasicData.emp_code)
    formdata.append("whatsapp_no", whatsapp)
    formdata.append("dob", dob)
    formdata.append("dom", marriageDate)
    formdata.append("email_id", email)
    const requestOptions = { method: "POST", body: formdata, redirect: "follow", }
    var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.KYCURL.update_KYC_url
    fetch(url, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        if (result?.process_status == "YES") {
          Toast.show({ type: "success", text1: "Success", text2: result.process_message, })
          setTimeout(() => {
            props.navigation.pop()
          }, 1000)
        } else
          Toast.show({ type: "error", text1: "Error", text2: result.process_message ?? "Something went wrong. Please try Again later", })
      })
      .catch((error) => {
      })
  }

  const handleSubmit = () => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!whatsapp)
      Toast.show({ type: "error", text1: "Sorry", text2: "Please enter whatsapp number", })
    else if (whatsapp.length != 10)
      Toast.show({ type: "error", text1: "Sorry", text2: "Please enter 10 digit whatsapp number", })
    else if (!dob)
      Toast.show({ type: "error", text1: "Sorry", text2: "Please enter date of birth", })
    else if (!email)
      Toast.show({ type: "error", text1: "Sorry", text2: "Please enter Email", })
    else if (!regex.test(email))
      Toast.show({ type: "error", text1: "Sorry", text2: "Please enter valid Email", })
    else
      updateKycDetails()
  }

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main} >
      <View style={{ flex: 1, backgroundColor: Colors.white }}>
        <SBSCommonHeaderView title="KYC Form" backPath=" " Filter={false} Calendar={false} navigation={navigation} props={props} />
        <View style={{ flex: 1, padding: moderateScale(20) }}>
          <Animated.View style={{ opacity: fieldsAnim[0], transform: [{ translateY: fieldsAnim[0].interpolate({ inputRange: [0, 1], outputRange: [-20, 0], }), },], }} >
            <Text style={styles.label}>WhatsApp Number</Text>
            <TextInput style={styles.input} maxLength={10} placeholder="Enter WhatsApp number" keyboardType="phone-pad" value={whatsapp} placeholderTextColor={Colors.grey} onChangeText={setWhatsapp} />
          </Animated.View>
          <Animated.View style={{ opacity: fieldsAnim[1], transform: [{ translateY: fieldsAnim[1].interpolate({ inputRange: [0, 1], outputRange: [-20, 0], }), },], }} >
            <Text style={styles.label}>Date of Birth</Text>
            <TouchableOpacity style={styles.input} onPress={() => showDatePicker("dob")} >
              <Text style={{ color: dob ? Colors.text : Colors.grey }}> {dob || "Select Date of Birth"} </Text>
            </TouchableOpacity>
          </Animated.View>
          <Animated.View style={{ opacity: fieldsAnim[2], transform: [{ translateY: fieldsAnim[2].interpolate({ inputRange: [0, 1], outputRange: [-20, 0], }), },], }} >
            <Text style={styles.label}>Date of Marriage</Text>
            <TouchableOpacity style={styles.input} onPress={() => showDatePicker("marriage")} >
              <Text style={{ color: marriageDate ? Colors.text : Colors.grey }}> {marriageDate || "Select Date of Marriage"} </Text>
            </TouchableOpacity>
          </Animated.View>
          <Animated.View style={{ opacity: fieldsAnim[3], transform: [{ translateY: fieldsAnim[3].interpolate({ inputRange: [0, 1], outputRange: [-20, 0], }), },], }} >
            <Text style={styles.label}>Email ID</Text>
            <TextInput style={styles.input} placeholder="Enter your email" keyboardType="email-address" placeholderTextColor={Colors.grey} value={email} onChangeText={setEmail} />
          </Animated.View>
          <Animated.View style={{ opacity: fieldsAnim[4], transform: [{ translateY: fieldsAnim[4].interpolate({ inputRange: [0, 1], outputRange: [-20, 0], }), },], }} >
            <TouchableOpacity style={styles.button} onPress={handleSubmit}>
              <Text style={styles.buttonText}> {isKycBefore ? "Update" : "Submit"} </Text>
            </TouchableOpacity>
          </Animated.View>
        </View>
      </View>
      {showPicker && <DateTimePicker value={new Date()} mode={pickerMode} display={Platform.OS === "ios" ? "spinner" : "default"} maximumDate={pickerFor === "dob" ? new Date() : undefined} onChange={onDateChange} style={{ flex: 1, backgroundColor: Colors.white }} />}
      <Toast config={toastConfig} />
      {loading && <Loader />}
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  )
}

const styles = StyleSheet.create({
  label: { fontSize: moderateScale(14), fontWeight: "600", marginBottom: moderateScale(6), marginTop: moderateScale(12), color: Colors.black, },
  input: { borderWidth: 1, borderColor: Colors.border, borderRadius: 10, color: Colors.black, padding: moderateScale(12), backgroundColor: Colors.white, fontSize: moderateScale(14), marginBottom: moderateScale(12), },
  button: { backgroundColor: Colors.main, marginTop: moderateScale(24), paddingVertical: moderateScale(12), borderRadius: 12, alignItems: "center", shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, },
  buttonText: { color: Colors.white, fontSize: moderateScale(16), fontWeight: "700", },
})

export default KycScreen
