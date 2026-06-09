import React from 'react'
import { FlatList, Image, Platform, Text, TouchableOpacity, View } from 'react-native'
import Modal from 'react-native-modal'
import { moderateScale } from '../helper/Window'
import { Colors } from '../assets/Colors'
import { Icons } from '../assets/Icons'
import DataStorage from '../storage/DataStorage'

const MonthList = [
    { id: 4, title: "April", text: "Apr" },
    { id: 5, title: "May", text: "May" },
    { id: 6, title: "June", text: "Jun" },
    { id: 7, title: "July", text: "Jul" },
    { id: 8, title: "August", text: "Aug" },
    { id: 9, title: "September", text: "Sep" },
    { id: 10, title: "October", text: "Oct" },
    { id: 11, title: "November", text: "Nov" },
    { id: 12, title: "December", text: "Dec" },
    { id: 1, title: "January", text: "Jan" },
    { id: 2, title: "February", text: "Feb" },
    { id: 3, title: "March", text: "Mar" },
];

const PerformanceMonthView = (props) => {
    const { isVisible, closeMonthPopup, closeYearPopup, onMonthSelect, selectedYear } = props

    const handleMonthPress = (item) => {
        // 1) Close month modal
        closeMonthPopup?.()
        // 2) Close year modal slightly after (prevents backdrop collision)
        setTimeout(() => closeYearPopup?.(), 150)
        // 3) Notify parent AFTER closures
        setTimeout(() => {
            onMonthSelect?.({
                month: item.text,
                year: selectedYear,
            })
        }, 220)
    }

    return (
        <Modal isVisible={isVisible} style={{ margin: 0 }} animationIn="slideInUp" animationOut="slideOutDown" backdropOpacity={0.5} onBackdropPress={closeMonthPopup} useNativeDriver useNativeDriverForBackdrop hideModalContentWhileAnimating >
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.main,marginTop:Platform.OS=='ios'? moderateScale(80):0 }}>
                {/* Header */}
                <View style={{ width: "100%", padding: moderateScale(20), backgroundColor: "#E41B14", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                    <TouchableOpacity onPress={closeMonthPopup}>
                        <View style={{ width: moderateScale(30), height: moderateScale(30), borderRadius: moderateScale(20), borderWidth: moderateScale(1), borderColor: "#FFFFFF", alignItems: "center", justifyContent: "center" }}>
                            <Image source={Icons.Back} style={{ width: moderateScale(12), height: moderateScale(12), tintColor: "#FFFFFF" }} />
                        </View>
                    </TouchableOpacity>
                    <Text style={{ color: "#FFFFFF", fontSize: moderateScale(16), fontWeight: "600" }}>
                        Select Month
                    </Text>
                    <View style={{ width: moderateScale(30) }} />
                </View>

                {/* Month list */}
                <View style={{ width: "100%", flex: 1, backgroundColor: "#ffffff", paddingVertical: moderateScale(20), borderTopRightRadius: moderateScale(30), borderTopLeftRadius: moderateScale(30) }}>
                    <FlatList
                        data={MonthList}
                        keyExtractor={(item) => String(item.id)}
                        showsVerticalScrollIndicator={false}
                        renderItem={({ item, index }) => (
                            <TouchableOpacity
                                activeOpacity={0.95}
                                onPress={() => handleMonthPress(item)}
                                style={{ width: "100%", paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(13), flexDirection: "row", alignItems: "center", gap: moderateScale(20), backgroundColor: index % 2 !== 0 ? DataStorage.transColorCode : "#FFFFFF", }} >
                                <Image source={Icons.Calender} style={{ width: moderateScale(24), height: moderateScale(24), tintColor: DataStorage.primaryColorCode }} />
                                <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500" }}>
                                    {item.title}
                                </Text>
                            </TouchableOpacity>
                        )}
                    />
                </View>
            </View>
        </Modal>
    )
}

export default PerformanceMonthView
