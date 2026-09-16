import React, { useState } from 'react'
import { FlatList, Image, Text, TextInput, TouchableOpacity, View } from 'react-native'
import SafeView from '../../../helper/SafeView'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import { Icons } from '../../../assets/Icons'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const TradersList = [
  { id: 1, title: "All in one solutions" },
  { id: 2, title: "Bhola Traders" },
  { id: 3, title: "Mola Traders" },
]

const AllocationScreen = (props) => {
  const [quantity, setQuantity] = useState(0)
  const [touched, setTouched] = useState(false)
  const [authChecker, setAuthChecker] = useState(false)
  const [select, setSelect] = useState(TradersList[0]?.id)

  const IncrementHandler = () => {
    setQuantity(prev => prev + 1)
    setTouched(true)
  }

  const DecrementHandler = () => {
    setQuantity(prev => (prev > 0 ? prev - 1 : 0))
    setTouched(true)
  }

  const onChangeTextHandler = (val) => {
    const num = parseInt(val)
    if (!isNaN(num)) {
      setQuantity(num)
      setTouched(true)
    } else
      setQuantity(0)
  }

  const handleCategory = (id) => {
    setSelect(id)
  }

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <View style={{ width: "100%", height: "100%", backgroundColor: Colors.main, flexDirection: 'column' }}>
        <SBSCommonHeaderView title="Allocation" backPath=" " />
        <View style={{ width: "100%", flex: 1, padding: moderateScale(10), paddingVertical: moderateScale(30), backgroundColor: Colors.white, borderTopRightRadius: moderateScale(30), borderTopLeftRadius: moderateScale(30) }}>
          <View style={{ width: "100%", marginBottom: moderateScale(20) }}>
            <FlatList
              data={TradersList}
              horizontal={true}
              keyExtractor={(item) => item.id}
              showsHorizontalScrollIndicator={false}
              renderItem={({ item }) => {
                const isSelected = select === item.id
                return (
                  <TouchableOpacity onPress={() => handleCategory(item.id)}>
                    <View style={{ paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(8), backgroundColor: isSelected ? "#8FC031" : "#F5F8EF", borderWidth: moderateScale(1), borderColor: isSelected ? "#8FC031" : "#DCDDDF", alignItems: "center", justifyContent: "center", marginRight: moderateScale(5), borderRadius: moderateScale(10), }} >
                      <Text style={{ color: isSelected ? "#FFFFFF" : "#000000", fontWeight: isSelected ? "600" : "400", fontSize: moderateScale(15), }} > {item.title} </Text>
                    </View>
                  </TouchableOpacity>
                )
              }}
            />
          </View>
          <View style={{ width: "100%", borderRadius: moderateScale(10), padding: moderateScale(10), gap: moderateScale(10), backgroundColor: "#8FC031" }}>
            <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
              <Text style={{ color: Colors.white, fontSize: moderateScale(12) }}>Remaining allocation Qty:</Text>
              <Text style={{ color: Colors.white, fontSize: moderateScale(15), fontWeight: "700" }}>140000</Text>
            </View>
            <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
              <Text style={{ color: Colors.white, fontSize: moderateScale(12) }}>Invoice Qty:</Text>
              <Text style={{ color: Colors.white, fontSize: moderateScale(15), fontWeight: "700" }}>4000</Text>
            </View>
          </View>
          <View style={{ width: "100%", gap: moderateScale(10), flex: 1 }}>
            <View style={{ width: "100%", borderRadius: moderateScale(10), padding: moderateScale(10), paddingHorizontal: moderateScale(20), flexDirection: "row", alignItems: 'center', justifyContent: "space-between" }}>
              <View style={{ flexDirection: "row", alignItems: "center", gap: moderateScale(10) }}>
                <Image source={Icons.SBS} style={{ width: moderateScale(60), height: moderateScale(40) }} />
                <Text style={{ color: Colors.text, fontSize: moderateScale(15), fontWeight: "600" }}>AAC Block</Text>
              </View>
              <View style={{ paddingVertical: moderateScale(4), paddingHorizontal: moderateScale(10), borderRadius: moderateScale(10), borderWidth: moderateScale(1), borderColor: "#DCDDDF", flexDirection: "row", alignItems: "center", gap: moderateScale(14) }}>
                <TouchableOpacity activeOpacity={0.95} onPress={DecrementHandler}>
                  <Text style={{ color: "#8FC031", fontSize: moderateScale(26), fontWeight: "600" }}>-</Text>
                </TouchableOpacity>
                <TextInput value={touched && quantity > 0 ? quantity.toString() : ''} onChangeText={onChangeTextHandler} keyboardType='number-pad' placeholder='QTY (PIc)' style={{ color: "#A7A7A7", fontSize: moderateScale(14), textAlign: "center", fontSize: moderateScale(14) }}></TextInput>
                <TouchableOpacity activeOpacity={0.95} onPress={IncrementHandler}>
                  <Text style={{ color: "#8FC031", fontSize: moderateScale(26), fontWeight: "600" }}>+</Text>
                </TouchableOpacity>
              </View>
            </View>
          </View>
          <TouchableOpacity activeOpacity={0.95} onPress={() => { props.navigation.navigate("SBSDashboardScreen") }}>
            <View style={{ width: "100%", paddingHorizontal: moderateScale(15) }}>
              <View style={{ width: "100%", height: moderateScale(40), flexDirection: "row", gap: moderateScale(8), backgroundColor: "#8FC031", borderRadius: moderateScale(10), alignItems: "center", justifyContent: "center" }}>
                <Text style={{ color: Colors.white, fontSize: moderateScale(16), fontWeight: 500 }}>Allocate</Text>
              </View>
            </View>
          </TouchableOpacity>
        </View>
      </View>
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  )
}

export default AllocationScreen
