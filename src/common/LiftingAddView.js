import React, { useState } from 'react'
import { FlatList, Image, Text, TextInput, TouchableOpacity, TouchableWithoutFeedback, View } from 'react-native'
import Modal from 'react-native-modal'
import { moderateScale } from '../helper/Window'
import { Colors } from '../assets/Colors'
import { Icons } from '../assets/Icons'
import { useNavigation } from '@react-navigation/native'
import SubDealerView from './SubDealerView'
import DataStorage from '../storage/DataStorage'


const AssignedList = [
    { id: 1, }, { id: 2, }, { id: 3, }, { id: 4, }, { id: 5, },
    { id: 6, }, { id: 7, }, { id: 8, }, { id: 9, }, { id: 10, },
    { id: 11, }, { id: 12, }, { id: 13, }, { id: 14, }, { id: 15, },
    { id: 16, }, { id: 17, },
]

const LiftingAddView = (props) => {
    const navigation = useNavigation();
    const [assigned, setAssigned] = useState(false)

    const [isDeatilsOpen, setIsDetailsOpen] = useState(false)
    const DetailsOpenHandler = () => {
        setIsDetailsOpen(!isDeatilsOpen)
    }
    const [assignedFilterOpen, setAssignedFilterOpen] = useState(false)
    const AssignedFilterOpenHandler = () => {
        setAssignedFilterOpen(!assignedFilterOpen)
    }
    const [allocated, setAllocated] = useState(false)
    const handleAllocatedOpen = () => {
        setAllocated(!allocated)
    }

    const [monthOpen, setMonthOpen] = useState(false)
    const MonthHandlerOpen = () => {
        setAllocated(!allocated)
        setMonthOpen(!monthOpen)
    }
    const MonthHandlerOpen1 = () => {
        props.closeLoginPopup()
    }

    return (
        <View>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.main }}>
                <View style={{ width: "100%", padding: moderateScale(20), backgroundColor: "#E41B14", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                    <TouchableOpacity onPress={() => { props.closeLoginPopup() }}>
                        <View style={{ width: moderateScale(30), height: moderateScale(30), borderRadius: moderateScale(20), borderWidth: moderateScale(1), borderColor: "#FFFFFF", alignItems: "center", justifyContent: "center" }}>
                            <Image source={Icons.Back} style={{ width: moderateScale(12), height: moderateScale(12), tintColor: "#FFFFFF" }} />
                        </View>
                    </TouchableOpacity>
                    <Text style={{ color: "#FFFFFF", fontSize: moderateScale(16), fontWeight: "600" }}>Assigned (Apr,2025)</Text>
                    <View style={{ width: moderateScale(30) }}>
                        <TouchableOpacity activeOpacity={0.95} onPress={AssignedFilterOpenHandler}>
                            <Image source={Icons.Filter} style={{ width: moderateScale(20), height: moderateScale(20) }} />
                        </TouchableOpacity>
                    </View>
                </View>
                <View style={{ width: "100%", flex: 1, backgroundColor: "#ffffff", paddingVertical: moderateScale(20), borderTopRightRadius: moderateScale(30), borderTopLeftRadius: moderateScale(30) }}>
                    <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "center", paddingHorizontal: moderateScale(10) }}>
                        <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={() => setAssigned(false)}>
                            <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: assigned === false ? DataStorage.primaryColorCode : "#DCDDDF", borderWidth: moderateScale(1), borderColor: assigned === false ? DataStorage.primaryColorCode : "#F5F8EF" }}>
                                <Text style={{ color: assigned === false ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600" }}>Assigned</Text>
                            </View>
                        </TouchableOpacity>
                        <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={() => setAssigned(true)}>
                            <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: assigned ? DataStorage.primaryColorCode : "#DCDDDF", borderWidth: moderateScale(1), borderColor: assigned ? DataStorage.primaryColorCode : "#F5F8EF" }}>
                                <Text style={{ color: assigned ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600" }}>Allocated</Text>
                            </View>
                        </TouchableOpacity>
                    </View>
                    <FlatList
                        data={AssignedList}
                        keyExtractor={(item) => item.id}
                        showsVerticalScrollIndicator={false}
                        decelerationRate="fast"
                        renderItem={({ item, index }) => {
                            return (
                                <TouchableOpacity activeOpacity={0.95} style={{ padding: moderateScale(10), }} onPress={DetailsOpenHandler}>
                                    <View style={{ width: "100%", overflow: "hidden", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), backgroundColor: "#FFFFFF", elevation: 10, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: (0), y: (4) } }}>
                                        <View style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(6), }}>
                                            <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                                                <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500" }}>SS0626914</Text>
                                                <View style={{ flexDirection: "row", alignItems: "center", gap: moderateScale(10) }}>
                                                    <View style={{ paddingVertical: moderateScale(3), paddingHorizontal: moderateScale(10), borderRadius: moderateScale(10), backgroundColor: DataStorage.primaryColorCode + "26" }}>
                                                        <Text style={{ color: DataStorage.primaryColorCode, fontSize: moderateScale(12) }}>Dispatched</Text>
                                                    </View>
                                                    <TouchableOpacity activeOpacity={0.95} onPress={DetailsOpenHandler}>
                                                        {isDeatilsOpen ?
                                                            <View style={{ width: moderateScale(20), height: moderateScale(20), borderWidth: moderateScale(1), backgroundColor: DataStorage.primaryColorCode, borderColor: DataStorage.primaryColorCode, alignItems: "center", justifyContent: "center" }} >
                                                                <Image source={Icons.UpArrow} style={{ width: moderateScale(10), height: moderateScale(10), tintColor: "#FFFFFF" }} />
                                                            </View> : <View style={{ width: moderateScale(20), height: moderateScale(20), borderWidth: moderateScale(1), borderColor: DataStorage.primaryColorCode, alignItems: "center", justifyContent: "center" }} >
                                                                <Image source={Icons.DownArrow} style={{ width: moderateScale(10), height: moderateScale(10) }} />
                                                            </View>}
                                                    </TouchableOpacity>
                                                </View>
                                            </View>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>Sati Trading co Dikom</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>9th April 2025 06:34 AM</Text>
                                            <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                                                <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>Dibrugarh</Text>
                                                <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}><Text style={{ color: "#7D7D7D", fontSize: moderateScale(12) }}>Qty: </Text>5000</Text>
                                            </View>
                                        </View>
                                        {isDeatilsOpen ? <View style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(10), backgroundColor: "#F5F8EF", borderWidth: moderateScale(1), borderColor: "#DCDDDF" }}>
                                            <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                                <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Invoice No:</Text>
                                                <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>F2360003075EX</Text>
                                            </View>
                                            <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                                <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Invoice Date:</Text>
                                                <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>April 10 2025</Text>
                                            </View>
                                            <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                                                <View style={{ flexDirection: "row", alignItems: "center", }}>
                                                    <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Invoice QTY(MT):</Text>
                                                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>3.000</Text>
                                                </View>
                                                <TouchableOpacity activeOpacity={0.95} onPress={() => handleAllocatedOpen(true)}>
                                                    <View style={{ paddingVertical: moderateScale(3), paddingHorizontal: moderateScale(10), borderRadius: moderateScale(10), backgroundColor: DataStorage.primaryColorCode }}>
                                                        <Text style={{ color: Colors.white, fontSize: moderateScale(12) }}>Allocate</Text>
                                                    </View>
                                                </TouchableOpacity>
                                            </View>
                                        </View> : null}
                                    </View>
                                </TouchableOpacity>
                            );
                        }}
                    />
                </View>
                <SubDealerView isVisible={allocated} closeLoginPopup={handleAllocatedOpen} closeLoginPopup1={MonthHandlerOpen1} />
            </View>
            {assignedFilterOpen ? <TouchableOpacity activeOpacity={0.95} onPress={AssignedFilterOpenHandler} style={{ position: "absolute", width: "100%", height: "100%", backgroundColor: "#00000087", alignItems: "center", justifyContent: "center" }}>
                <View style={{ backgroundColor: Colors.white, borderRadius: moderateScale(10), width: "60%", overflow: "hidden" }}>
                    <View style={{ width: "100%", padding: moderateScale(10), backgroundColor: Colors.main }}>
                        <Text style={{ textAlign: "center", color: Colors.white, fontSize: moderateScale(13), fontWeight: "500" }}>Star Sathi</Text>
                    </View>
                    <View style={{ width: "100%", flexDirection: "row-reverse", gap: moderateScale(20), padding: moderateScale(20) }}>
                        <TouchableOpacity activeOpacity={0.95} onPress={AssignedFilterOpenHandler}>
                            <Text style={{ textAlign: "center", color: DataStorage.primaryColorCode, fontSize: moderateScale(16), fontWeight: "500" }}>Ok</Text>
                        </TouchableOpacity>
                        <TouchableOpacity activeOpacity={0.95} onPress={AssignedFilterOpenHandler}>
                            <Text style={{ textAlign: "center", color: "#E41B14", fontSize: moderateScale(16), fontWeight: "500" }}>Cancel</Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </TouchableOpacity> : null}
        </View>
    )
}

export default LiftingAddView
