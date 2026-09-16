import React from 'react'
import { Alert, Image, Text, TouchableOpacity, View } from 'react-native'
import { moderateScale } from '../helper/Window'
import { Icons } from '../assets/Icons'
import { useNavigation } from '@react-navigation/native'

const SBSCommonHeaderView = (props) => {
  const {
    title, Information = false, Add = false, Filter = false, Cart = false, Calendar = false,
    handlePerformanceFilterOpen, handlePerformanceCalendarOpen, handleLiftingAddOpen,
    gotoLink, backPath, } = props
  const navigation = useNavigation()

  const handleBackPress = () => {
    try {
      if (backPath === "payment")
        Alert.alert(
          "Exit Transaction",
          "Do you want to exit the transaction?",
          [
            { text: "No", style: "cancel" },
            { text: "Yes", onPress: () => navigation.pop() },
          ],
          { cancelable: true }
        )
      else
        navigation.goBack()
    } catch (error) { }
  }

  return (
    <View style={{ width: "100%", height: moderateScale(60), padding: moderateScale(10), backgroundColor: "#E41B14", flexDirection: "row", alignItems: "center", justifyContent: "space-between", }} >
      <TouchableOpacity activeOpacity={0.95} onPress={handleBackPress}>
        <View style={{ width: moderateScale(30), height: moderateScale(30), borderRadius: moderateScale(20), borderWidth: moderateScale(1), borderColor: "#FFFFFF", alignItems: "center", justifyContent: "center", }} >
          <Image source={Icons.Back} style={{ width: moderateScale(12), height: moderateScale(12), tintColor: "#FFFFFF", }} />
        </View>
      </TouchableOpacity>
      <Text style={{ color: "#FFFFFF", fontSize: moderateScale(14), fontWeight: "600", paddingStart: Add ? moderateScale(40) : 0 }}> {title} </Text>
      <View style={{ flexDirection: "row", gap: moderateScale(10) }}>
        {Information ? <TouchableOpacity onPress={() => gotoLink()}>
          <Image source={Icons.Information} style={{ width: moderateScale(20), height: moderateScale(20) }} />
        </TouchableOpacity> : <View style={{ width: moderateScale(20) }} />}
        {Calendar ? <TouchableOpacity activeOpacity={0.95} onPress={() => handlePerformanceCalendarOpen(true)}>
          <Image source={Icons.Calender} style={{ width: moderateScale(20), height: moderateScale(20), tintColor: "#FFFFFF" }} />
        </TouchableOpacity> : null}
        {Add ? <TouchableOpacity activeOpacity={0.95} onPress={() => navigation.navigate("AssignedScreen")}>
          <Image source={Icons.Add} style={{ width: moderateScale(20), height: moderateScale(20), tintColor: "#FFFFFF" }} />
        </TouchableOpacity> : null}
        {Filter ? <TouchableOpacity activeOpacity={0.95} onPress={() => handlePerformanceFilterOpen(true)}>
          <Image source={Icons.Filter} style={{ width: moderateScale(20), height: moderateScale(20) }} />
        </TouchableOpacity> : null}
        {Cart ? <Image source={Icons.Cart} style={{ width: moderateScale(20), height: moderateScale(20) }} /> : null}
      </View>
    </View>
  )
}

export default SBSCommonHeaderView
