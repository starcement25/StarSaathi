import React, { useEffect, useState } from "react"
import { FlatList, Image, Text, TextInput, View, TouchableOpacity, Switch, ScrollView, Alert, Platform, ActivityIndicator, } from "react-native"
import DateTimePicker from "@react-native-community/datetimepicker"
import SafeView from "../../../helper/SafeView"
import { Colors } from "../../../assets/Colors"
import SBSCommonHeaderView from "../../../common/SBSCommonHeaderView"
import { moderateScale } from "../../../helper/Window"
import { Icons } from "../../../assets/Icons"
import UrlStorage from "../../../storage/UrlStorage"
import Toast from "react-native-toast-message"
import toastConfig from "../../../helper/ToastConfig"
import AuthNotVerifyPopupView from "../../../auth/AuthNotVerifyPopupView"
import { AuthCheckingApi } from "../../../auth/AuthCheckingApi"

const KismatKiBoriScreen = (props) => {
  const [isLoading, setIsLoading] = useState(false)
  const [houseOwnerName, setHouseOwnerName] = useState("")
  const [houseOwnerPhoneNumber, setHouseOwnerPhoneNumber] = useState("")
  const [dateOfPurchase, setDateOfPurchase] = useState(new Date())
  const [showDatePicker, setShowDatePicker] = useState(false)
  const [isDateSelected, setIsDateSelected] = useState(false)
  const [quantity, setQuantity] = useState("")
  const [couponGiven, setCouponGiven] = useState(false)
  const [couponList, setCouponList] = useState([])
  const [minQuantity, setMinQuantity] = useState(0)
  const [authChecker, setAuthChecker] = useState(false)

  const formatDate = (date) => {
    const day = date.getDate().toString().padStart(2, "0")
    const month = (date.getMonth() + 1).toString().padStart(2, "0")
    const year = date.getFullYear()
    return `${day}/${month}/${year}`
  }

  useEffect(() => {
    const fetchMinQuantity = async () => {
      var a = await AuthCheckingApi()
      if (!a) {
        setAuthChecker(true)
        return false
      }
      try {
        const requestOptions = { method: "GET", redirect: "follow" }
        const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.OtherURL.KismatKiBoriPermissionUrl + "?customer_id=" + UrlStorage.ParameterList.BasicData.emp_id
        const response = await fetch(url, requestOptions)
        const result = await response.json()
        if (!result.process_sts == "YES")
          throw new Error("Failed to fetch min quantity")
        setMinQuantity(result.bag_quantity)
      } catch (error) {
        setMinQuantity(0)
      }
    }
    fetchMinQuantity()
  }, [])

  const onDateChange = (event, selectedDate) => {
    const currentDate = selectedDate || dateOfPurchase
    if (Platform.OS === "android") setShowDatePicker(false)
    if (event.type === "set") {
      setDateOfPurchase(currentDate)
      setIsDateSelected(true)
    }
  }

  const showDatePickerModal = () => setShowDatePicker(true)

  const addNewCoupon = () => {
    const enteredQuantity = parseInt(quantity) || 0
    const allowedCoupons = Math.floor(enteredQuantity / minQuantity)
    if (allowedCoupons > couponList.length) {
      const newCoupon = { id: Date.now().toString(), code: "", }
      setCouponList([...couponList, newCoupon])
    } else {
      Toast.show({ type: "error", text1: "Sorry", text2: "Limit Reached..You cannot add more coupons", })
    }
  }

  const removeCoupon = (id) => {
    setCouponList(couponList.filter((coupon) => coupon.id !== id))
  }

  const updateCouponCode = (id, code) => {
    setCouponList(couponList.map((coupon) => coupon.id === id ? { ...coupon, code } : coupon))
  }

  const handleToggleChange = (value) => {
    setCouponGiven(value)
    if (value && couponList.length === 0)
      addNewCoupon()
    else if (!value)
      setCouponList([])
  }

  const validateForm = () => {
    if (!houseOwnerName.trim()) {
      Toast.show({ type: "error", text1: "Sorry", text2: "Please enter house owner name.", })
      return false
    }
    if (!houseOwnerPhoneNumber.trim()) {
      Toast.show({ type: "error", text1: "Sorry", text2: "Please enter house owner phone number.", })
      return false
    }
    if (houseOwnerPhoneNumber.length !== 10) {
      Toast.show({ type: "error", text1: "Sorry", text2: "Phone number should be 10 digits ", })
      return false
    }
    if (!isDateSelected) {
      Toast.show({ type: "error", text1: "Sorry", text2: "Please select date of purchase", })
      return false
    }
    if (!quantity.trim()) {
      Toast.show({ type: "error", text1: "Sorry", text2: "Please enter quantity", })
      return false
    }
    if (couponGiven && couponList.some((coupon) => !coupon.code.trim())) {
      Toast.show({ type: "error", text1: "Sorry", text2: "Please enter coupon code or diable it", })
      return false
    }
    return true
  }

  const resetForm = () => {
    setHouseOwnerName("")
    setHouseOwnerPhoneNumber("")
    setDateOfPurchase(new Date())
    setIsDateSelected(false)
    setQuantity("")
    setCouponGiven(false)
    setCouponList([])
  }

  const handleSubmit = async () => {
    if (!validateForm()) return
    setIsLoading(true)
    var a = await AuthCheckingApi()
    if (!a) {
      setAuthChecker(true)
      return false
    }
    const formData = {
      houseOwnerName: houseOwnerName.trim(),
      houseOwnerPhoneNumber: houseOwnerPhoneNumber.trim(),
      dateOfPurchase: formatDate(dateOfPurchase),
      quantity: parseInt(quantity),
      couponGiven,
      coupons: couponList.filter((coupon) => coupon.code.trim() !== ""),
    }
    const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.OtherURL.add_sikkim_consumer_scheme
    try {
      const response = await fetch(url, { method: "POST", headers: { "Content-Type": "application/json", }, body: JSON.stringify(formData), })
      if (!response.ok)
        throw new Error("Network response was not ok")
      const data = await response.json()
      Alert.alert("Success", "Entry submitted successfully!", [
        {
          text: "OK",
          onPress: () => {
            resetForm()
            if (props.navigation) props.navigation.goBack()
          },
        },
      ])
    } catch (error) {
      Alert.alert("Error", "Something went wrong while submitting.")
    } finally {
      setIsLoading(false)
    }
  }

  const renderCouponItem = ({ item, index }) => (
    <View style={styles.couponItemContainer}>
      <View style={styles.couponInputContainer}>
        <TextInput
          style={styles.couponInput}
          placeholder="Enter coupon code"
          placeholderTextColor={"#ccc"}
          value={item.code}
          onChangeText={(text) => updateCouponCode(item.id, text)}
        />
      </View>
      {index > 0 && (
        <TouchableOpacity onPress={() => removeCoupon(item.id)}>
          <Image source={Icons.DeleteIcon} style={styles.removeIcon} />
        </TouchableOpacity>
      )}
    </View>
  )

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main} >
      <View style={styles.container}>
        <SBSCommonHeaderView title="Kismat Ki Bori Scheme" backPath=" " />
        <ScrollView style={styles.scrollContainer} nestedScrollEnabled showsVerticalScrollIndicator={false} contentContainerStyle={{ paddingBottom: moderateScale(20) }} >
          <View style={styles.formContainer}>
            <View style={styles.fieldContainer}>
              <Text style={styles.fieldLabel}> House Owner Name <Text style={styles.required}>*</Text> </Text>
              <View style={styles.inputContainer}>
                <TextInput
                  style={styles.textInput}
                  placeholder="Enter owner name"
                  placeholderTextColor={"#ccc"}
                  value={houseOwnerName}
                  onChangeText={setHouseOwnerName}
                />
              </View>
            </View>
            <View style={styles.fieldContainer}>
              <Text style={styles.fieldLabel}> Mobile Number <Text style={styles.required}>*</Text> </Text>
              <View style={styles.inputContainer}>
                <TextInput
                  style={styles.textInput}
                  placeholder="Enter mobile number"
                  placeholderTextColor={"#ccc"}
                  value={houseOwnerPhoneNumber}
                  onChangeText={setHouseOwnerPhoneNumber}
                  keyboardType="numeric"
                  maxLength={10}
                />
              </View>
            </View>
            <View style={styles.fieldContainer}>
              <Text style={styles.fieldLabel}> Date of Purchase <Text style={styles.required}>*</Text> </Text>
              <TouchableOpacity style={styles.datePickerContainer} onPress={showDatePickerModal} >
                <Text style={[styles.datePickerText, { color: isDateSelected ? "#333" : "#ccc" },]} > {isDateSelected ? formatDate(dateOfPurchase) : "Select date"} </Text>
                <Image source={Icons.Calender} style={styles.calendarIcon} />
              </TouchableOpacity>
            </View>
            <View style={styles.fieldContainer}>
              <Text style={styles.fieldLabel}> Quantity (in Bags) <Text style={styles.required}>*</Text> </Text>
              <View style={styles.inputContainer}>
                <TextInput
                  style={styles.textInput}
                  placeholder="Enter number of bags"
                  placeholderTextColor={"#ccc"}
                  value={quantity}
                  onChangeText={setQuantity}
                  keyboardType="numeric"
                />
              </View>
            </View>
            <View style={styles.switchContainer}>
              <Text style={styles.switchLabel}>Lucky Draw Coupon Given</Text>
              <Switch value={couponGiven} onValueChange={handleToggleChange} trackColor={{ false: "#ccc", true: "#8FC031" }} thumbColor={couponGiven ? "#fff" : "#e9e9e9"} />
            </View>
            {couponGiven && (
              <View style={styles.couponSection}>
                <View style={styles.couponHeaderContainer}>
                  <Text style={styles.fieldLabel}> Coupon Entry Section <Text style={styles.required}>*</Text> </Text>
                  <TouchableOpacity onPress={addNewCoupon}>
                    <Text style={styles.addCouponText}> + Add Another Coupon </Text>
                  </TouchableOpacity>
                </View>
                <FlatList
                  data={couponList}
                  keyExtractor={(item) => item.id}
                  renderItem={renderCouponItem}
                  ItemSeparatorComponent={() => (
                    <View style={{ height: moderateScale(10) }} />
                  )}
                  scrollEnabled={false}
                />
              </View>
            )}
          </View>
        </ScrollView>
        {showDatePicker && <DateTimePicker value={dateOfPurchase} mode="date" display={Platform.OS === "ios" ? "spinner" : "default"} onChange={onDateChange} maximumDate={new Date()} minimumDate={new Date(2020, 0, 1)} />}
        <View style={styles.buttonContainer}>
          <TouchableOpacity style={[styles.submitButton, { opacity: isLoading ? 0.6 : 1 }]} onPress={handleSubmit} disabled={isLoading} >
            {isLoading ? <ActivityIndicator color="#fff" /> : <Text style={styles.submitButtonText}>Submit</Text>}
          </TouchableOpacity>
        </View>
        <Toast config={toastConfig} />
      </View>
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  )
}

const styles = {
  container: { flex: 1, backgroundColor: Colors.white },
  scrollContainer: { flex: 1 },
  formContainer: { padding: moderateScale(20) },
  fieldContainer: { marginBottom: moderateScale(20) },
  fieldLabel: { color: "#373E46", fontSize: moderateScale(14), fontWeight: "500", marginBottom: moderateScale(10), },
  required: { color: Colors.main },
  inputContainer: { width: "100%", height: moderateScale(45), borderColor: "#aaa", borderWidth: moderateScale(1), borderRadius: moderateScale(5), paddingHorizontal: moderateScale(10), justifyContent: "center", },
  textInput: { width: "100%", height: "100%", color: "#333", fontSize: moderateScale(14), },
  datePickerContainer: { width: "100%", height: moderateScale(45), borderColor: "#aaa", borderWidth: moderateScale(1), borderRadius: moderateScale(5), paddingHorizontal: moderateScale(10), flexDirection: "row", alignItems: "center", },
  datePickerText: { flex: 1, fontSize: moderateScale(14) },
  calendarIcon: { height: moderateScale(24), width: moderateScale(24), tintColor: Colors.main, },
  switchContainer: { flexDirection: "row", alignItems: "center", justifyContent: "space-between", marginBottom: moderateScale(20), },
  switchLabel: { color: "#373E46", fontSize: moderateScale(14), fontWeight: "500", },
  couponSection: { marginTop: moderateScale(10) },
  couponHeaderContainer: { flexDirection: "row", alignItems: "center", justifyContent: "space-between", marginBottom: moderateScale(15), },
  addCouponText: { color: "#00CB7A", fontSize: moderateScale(14), fontWeight: "500", },
  couponItemContainer: { flexDirection: "row", alignItems: "center" },
  couponInputContainer: { flex: 1, height: moderateScale(40), borderColor: "#aaa", borderWidth: moderateScale(1), borderRadius: moderateScale(5), paddingHorizontal: moderateScale(10), justifyContent: "center", },
  couponInput: { width: "100%", height: "100%", color: "#333", fontSize: moderateScale(14), },
  removeIcon: { height: moderateScale(20), width: moderateScale(20), tintColor: "#ff4444", marginLeft: moderateScale(10), },
  buttonContainer: { padding: moderateScale(20), paddingTop: moderateScale(10), },
  submitButton: { height: moderateScale(45), backgroundColor: Colors.main, borderRadius: moderateScale(5), justifyContent: "center", alignItems: "center", },
  submitButtonText: { color: "white", fontSize: moderateScale(16), fontWeight: "500", },
}

export default KismatKiBoriScreen
