import React, { useState } from 'react'
import { ScrollView, Text, TextInput, TouchableOpacity, View, Image } from 'react-native'
import SafeView from '../../../helper/SafeView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Icons } from '../../../assets/Icons'
import Toast from 'react-native-toast-message'
import toastConfig from '../../../helper/ToastConfig'
import UrlStorage from '../../../storage/UrlStorage'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const AddressScreen = (props) => {
  const { selectedPayment, arr } = props.route.params
  const [sameAsAbove, setSameAsAbove] = useState(false)
  const [acceptTerms, setAcceptTerms] = useState(false)
  const [authChecker, setAuthChecker] = useState(false)
  const [printAddress, setPrintAddress] = useState('')
  const [contactNo, setContactNo] = useState('')
  const [deliveryAddress, setDeliveryAddress] = useState('')
  const [pin, setPin] = useState('')
  const [feedback, setFeedback] = useState('')

  const validateForm = () => {
    if (!printAddress.trim()) {
      Toast.show({ type: 'error', text1: 'Validation Error', text2: 'Please enter Address' })
      return false
    }
    if (!contactNo.trim() || !/^\d{10}$/.test(contactNo)) {
      Toast.show({ type: 'error', text1: 'Validation Error', text2: 'Please enter a valid 10-digit Contact Number.' })
      return false
    }
    if (!deliveryAddress.trim()) {
      Toast.show({ type: 'error', text1: 'Validation Error', text2: 'Please enter Delivery Address.' })
      return false
    }
    if (!pin.trim() || !/^\d{6}$/.test(pin)) {
      Toast.show({ type: 'error', text1: 'Validation Error', text2: 'Please enter Address and PIN Code' })
      return false
    }
    if (!acceptTerms) {
      Toast.show({ type: 'error', text1: 'Validation Error', text2: 'You must accept terms & conditions.' })
      return false
    }
    return true
  }

  const handleContinue = async () => {
    if (!validateForm()) return
    if (!selectedPayment) {
      Toast.show({ type: 'error', text1: 'Payment Error', text2: 'Please choose a payment option', })
      return
    }
    var a = await AuthCheckingApi()
    if (!a) {
      setAuthChecker(true)
      setLoading(false)
      return false
    }
    const baseUrl = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.ProductURL.create_pop_order_url
    const formData = new FormData()
    try {
      for (var i = 0; i < arr.length; i++) {
        formData.append("order_data[" + i + "][dns_prod_code]", arr[i]?.dns_prod_code || '')
        formData.append("order_data[" + i + "][prod_desc]", arr[i]?.prod_desc || '')
        formData.append("order_data[" + i + "][qty]", arr[i]?.count || '')
        formData.append("order_data[" + i + "][prod_image]", arr[i]?.prod_image || '')
        formData.append("order_data[" + i + "][customer_code]", UrlStorage.ParameterList.BasicData.customer_code)
        formData.append("order_data[" + i + "][printed_address_pin]", pin)
        formData.append("order_data[" + i + "][contact_num_printed]", contactNo)
        formData.append("order_data[" + i + "][address]", printAddress)
        formData.append("order_data[" + i + "][pin]", pin)
        formData.append("order_data[" + i + "][remarks]", feedback)
        formData.append("order_data[" + i + "][payment_by]", selectedPayment)
        formData.append("order_data[" + i + "][dns_customer_code]", UrlStorage.ParameterList.BasicData.dns_emp_code)
      }
    } catch (error) {
    }
    formData.append('customer_code', UrlStorage.ParameterList.BasicData.customer_code)
    formData.append('user_type', UrlStorage.ParameterList.BasicData.user_type)
    formData.append('payment_by', selectedPayment)
    formData.append('address', printAddress)
    formData.append('pin', pin)
    formData.append('remarks', feedback)
    formData.append('printed_address_pin', pin)
    formData.append('contact_num_printed', contactNo)
    const dnsCode = UrlStorage.ParameterList.BasicData?.selectedCustomerCode?.dns_emp_code
    if (dnsCode) { formData.append('dns_customer_code', dnsCode) }
    try {
      const response = await fetch(baseUrl, { method: 'POST', body: formData, })
      const result = await response.json()
      if (result?.process_status === 'YES') {
        const link = result?.the_payment_url
        props.navigation.navigate('PaymentWebViewScreen', { link })
      } else {
        Toast.show({ type: 'error', text1: 'Order Failed', text2: result?.process_message || 'Something went wrong', })
      }
    } catch (error) {
      Toast.show({ type: 'error', text1: 'Network Error', text2: 'Please try again later', })
    }
  }

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
        <SBSCommonHeaderView title="Pop Product" backPath=" " />
        <ScrollView style={{ width: "100%" }} showsVerticalScrollIndicator={false}>
          <View style={{ width: "100%", padding: moderateScale(20), gap: moderateScale(16), flex: 1 }}>
            <View style={{ width: "100%", gap: moderateScale(8) }}>
              <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}> Address and PIN Code* </Text>
              <View style={{ width: "100%", height: moderateScale(100), borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10) }}>
                <TextInput
                  placeholder='Enter Address and PIN Code'
                  style={{ width: "100%", color: "#000", fontSize: moderateScale(14) }}
                  multiline
                  value={printAddress}
                  onChangeText={setPrintAddress}
                />
              </View>
            </View>
            <View style={{ width: "100%", gap: moderateScale(8) }}>
              <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}> Contact No. </Text>
              <View style={{ width: "100%", borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10) }}>
                <TextInput
                  placeholder='+91 0000000000'
                  style={{ width: "100%", color: "#000", fontSize: moderateScale(14), height: moderateScale(40) }}
                  keyboardType="number-pad"
                  maxLength={10}
                  value={contactNo}
                  onChangeText={setContactNo}
                />
              </View>
            </View>
            <TouchableOpacity activeOpacity={0.8} onPress={() => {
              if (!sameAsAbove)
                setDeliveryAddress(printAddress)
              else
                setDeliveryAddress('')
              setSameAsAbove(!sameAsAbove)
            }} style={{ width: "100%", flexDirection: "row", alignItems: "center", gap: moderateScale(10) }} >
              <View style={{ width: moderateScale(26), height: moderateScale(26), borderRadius: moderateScale(6), borderWidth: moderateScale(1), borderColor: "#8FC031", alignItems: "center", justifyContent: "center" }}>
                {sameAsAbove && <Image source={Icons.Check} style={{ width: moderateScale(20), height: moderateScale(20) }} />}
              </View>
              <Text style={{ color: "#1E1E1E", fontSize: moderateScale(13), fontWeight: "500" }}>Same as Above</Text>
            </TouchableOpacity>
            <View style={{ width: "100%", gap: moderateScale(8) }}>
              <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}>Enter Delivery Address*</Text>
              <View style={{ width: "100%", height: moderateScale(100), borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10) }}>
                <TextInput
                  placeholder='Enter Address'
                  style={{ width: "100%", color: "#000", fontSize: moderateScale(14) }}
                  multiline
                  value={deliveryAddress}
                  onChangeText={setDeliveryAddress}
                />
              </View>
            </View>
            <View style={{ width: "100%", gap: moderateScale(8) }}>
              <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}>Enter PIN Code*</Text>
              <View style={{ width: "100%", borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10) }}>
                <TextInput
                  placeholder='000000'
                  style={{ width: "100%", color: "#000", fontSize: moderateScale(14), height: moderateScale(40) }}
                  keyboardType="number-pad"
                  maxLength={6}
                  value={pin}
                  onChangeText={setPin}
                />
              </View>
            </View>
            <View style={{ width: "100%", gap: moderateScale(8) }}>
              <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}>Feedback (optional)</Text>
              <View style={{ width: "100%", height: moderateScale(100), borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10) }}>
                <TextInput
                  placeholder='Write your Feedback here...'
                  style={{ width: "100%", color: "#000", fontSize: moderateScale(14) }}
                  multiline
                  value={feedback}
                  onChangeText={setFeedback}
                />
              </View>
            </View>
            <TouchableOpacity activeOpacity={0.8} onPress={() => setAcceptTerms(!acceptTerms)} style={{ width: "100%", flexDirection: "row", alignItems: "center", gap: moderateScale(10) }} >
              <View style={{ width: moderateScale(26), height: moderateScale(26), borderRadius: moderateScale(6), borderWidth: moderateScale(1), borderColor: "#8FC031", alignItems: "center", justifyContent: "center" }}>
                {acceptTerms && <Image source={Icons.Check} style={{ width: moderateScale(20), height: moderateScale(20) }} />}
              </View>
              <Text style={{ color: "#1E1E1E", fontSize: moderateScale(13), fontWeight: "500" }}>Accept terms & conditions*</Text>
            </TouchableOpacity>
          </View>
        </ScrollView>
        <TouchableOpacity activeOpacity={0.95} onPress={handleContinue}>
          <View style={{ width: "100%", paddingHorizontal: moderateScale(15), marginBottom: moderateScale(20) }}>
            <View style={{ width: "100%", height: moderateScale(40), flexDirection: "row", gap: moderateScale(8), backgroundColor: Colors.main, borderRadius: moderateScale(10), alignItems: "center", justifyContent: "center" }}>
              <Text style={{ color: Colors.white, fontSize: moderateScale(16), fontWeight: 500 }}>Continue</Text>
            </View>
          </View>
        </TouchableOpacity>
      </View>
      <Toast config={toastConfig} />
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  )
}

export default AddressScreen
