import React, { useState } from 'react'
import { FlatList, Image, Platform, Text, TouchableOpacity, View } from 'react-native'
import Modal from 'react-native-modal'
import { moderateScale } from '../helper/Window'
import { Colors } from '../assets/Colors'
import { Icons } from '../assets/Icons'
import PerformanceMonthView from './PerformanceMonthView'
import DataStorage from '../storage/DataStorage'
import moment from "moment"

const currentYear = moment(new Date()).year()
const CalendarList = [
    { id: 1, title: `FY ${currentYear}-${currentYear + 1}`, show: 'Current FY' },
    { id: 2, title: `FY ${currentYear - 1}-${currentYear}`, show: 'Previous FY' },
]

const PerformanceCalenderView = (props) => {
    const { isVisible, closeLoginPopup, onMonthSelect } = props
    const [monthOpen, setMonthOpen] = useState(false)
    const [selectedYear, setSelectedYear] = useState(null)

    const openMonth = (item) => {
        setSelectedYear(item.title)
        setMonthOpen(true)
    }

    const closeMonth = () => setMonthOpen(false)

    return (
        <Modal isVisible={isVisible} style={{ margin: 0 }} backdropOpacity={0.5} >
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.main, marginTop: Platform.OS == 'ios' ? moderateScale(80) : 0 }}>
                <View style={{ width: "100%", padding: moderateScale(20), backgroundColor: "#E41B14", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                    <TouchableOpacity onPress={() => { props.closeLoginPopup() }}>
                        <View style={{ width: moderateScale(30), height: moderateScale(30), borderRadius: moderateScale(20), borderWidth: moderateScale(1), borderColor: "#FFFFFF", alignItems: "center", justifyContent: "center" }}>
                            <Image source={Icons.Back} style={{ width: moderateScale(12), height: moderateScale(12), tintColor: "#FFFFFF" }} />
                        </View>
                    </TouchableOpacity>
                    <Text style={{ color: "#FFFFFF", fontSize: moderateScale(16), fontWeight: "600" }}>Select Year</Text>
                    <View style={{ width: moderateScale(30) }} />
                </View>

                <View style={{ width: "100%", flex: 1, backgroundColor: "#ffffff", paddingVertical: moderateScale(20), borderTopRightRadius: moderateScale(30), borderTopLeftRadius: moderateScale(30) }}>
                    <FlatList
                        data={CalendarList}
                        keyExtractor={(item) => String(item.id)}
                        showsVerticalScrollIndicator={false}
                        renderItem={({ item, index }) => (
                            <TouchableOpacity activeOpacity={0.95} onPress={() => openMonth(item)}>
                                <View style={{ width: "100%", paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(13), flexDirection: "row", alignItems: "center", backgroundColor: (index % 2 !== 0 ? DataStorage.transColorCode : "#FFFFFF") }}>
                                    <View style={{ flexDirection: "row", alignItems: "center", gap: moderateScale(20), flex: 1 }}>
                                        <Image source={Icons.Calender} style={{ width: moderateScale(24), height: moderateScale(24), tintColor: DataStorage.primaryColorCode }} />
                                        <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500", textTransform: "uppercase", flex: 1 }}>{item.show}</Text>
                                    </View>
                                    <View style={{ width: moderateScale(20), height: moderateScale(20), borderWidth: moderateScale(1), borderColor: DataStorage.primaryColorCode, alignItems: "center", justifyContent: "center" }} >
                                        <Image source={Icons.Forward} style={{ width: moderateScale(10), height: moderateScale(10), tintColor: DataStorage.primaryColorCode }} />
                                    </View>
                                </View>
                            </TouchableOpacity>
                        )}
                    />
                </View>
                {monthOpen ? <PerformanceMonthView isVisible={monthOpen} closeMonthPopup={closeMonth} closeYearPopup={closeLoginPopup} onMonthSelect={onMonthSelect} selectedYear={selectedYear} /> : null}
            </View>
        </Modal>
    )
}

export default PerformanceCalenderView
