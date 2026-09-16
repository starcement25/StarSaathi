import React, { useEffect, useState } from 'react'
import { Text, TextInput, TouchableOpacity, View } from 'react-native'
import SafeView from '../../../helper/SafeView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import DataStorage from '../../../storage/DataStorage'

const PaymentScreen = (props) => {
  const [productList, setProductList] = useState(DataStorage.popProductList)
  const [totalPrice, setTotalPrice] = useState(0)
  const { arr } = props.route.params

  const [selectedPayment, setSelectedPayment] = useState("Net Banking")

  useEffect(() => {
    let total = 0
    for (let i = 0; i < productList.length; i++) {
      total += parseFloat(productList[i].price_per_piece * productList[i].count + productList[i].price_per_piece * productList[i].count * productList[i].GST_rate / 100)
    }
    setTotalPrice(total)
  }, [productList])

  const paymentOptions = ['Net Banking', 'UPI', 'Credit Card', 'Debit Card']

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
        <SBSCommonHeaderView title="Pop Product" backPath=" " />
        <View style={{ width: "100%", padding: moderateScale(20), gap: moderateScale(20), flex: 1 }}>
          <View style={{ width: "100%", gap: moderateScale(8) }}>
            <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}> Enter amount you want to pay </Text>
            <View style={{ width: "100%", height: moderateScale(44), borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10) }}>
              <TextInput
                placeholder='Enter amount'
                style={{ flex: 1, color: "#000", fontSize: moderateScale(14) }}
                value={parseFloat(totalPrice).toFixed(2)}
                editable={false}
              />
            </View>
          </View>
          <View style={{ width: "100%", gap: moderateScale(8) }}>
            <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}> Select payment method </Text>
            {paymentOptions.map((option, index) => (
              <TouchableOpacity onPress={() => setSelectedPayment(option)} key={index} activeOpacity={0.8} style={{ width: "100%", borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), padding: moderateScale(10), flexDirection: "row", alignItems: "center", gap: moderateScale(10) }} >
                <View style={{ width: moderateScale(24), height: moderateScale(24), borderRadius: moderateScale(12), borderWidth: moderateScale(1), borderColor: "#8FC031", justifyContent: "center", alignItems: "center" }}>
                  {selectedPayment === option && <View style={{ width: moderateScale(12), height: moderateScale(12), borderRadius: moderateScale(6), backgroundColor: "#8FC031" }} />}
                </View>
                <Text style={{ color: Colors.text, fontSize: moderateScale(14) }}>{option}</Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>
        <TouchableOpacity onPress={() => { props.navigation.navigate("AddressScreen", { selectedPayment, arr }) }} activeOpacity={0.95} >
          <View style={{ width: "100%", paddingHorizontal: moderateScale(15), marginBottom: moderateScale(20) }}>
            <View style={{ width: "100%", height: moderateScale(40), flexDirection: "row", gap: moderateScale(8), backgroundColor: Colors.main, borderRadius: moderateScale(10), alignItems: "center", justifyContent: "center" }}>
              <Text style={{ color: Colors.white, fontSize: moderateScale(16), fontWeight: 500 }}>Continue</Text>
            </View>
          </View>
        </TouchableOpacity>
      </View>
    </SafeView>
  )
}

export default PaymentScreen
