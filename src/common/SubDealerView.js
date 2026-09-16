import React, { useState } from 'react'
import { FlatList, Image, Text, TextInput, TouchableOpacity, TouchableWithoutFeedback, View } from 'react-native'
import Modal from 'react-native-modal'
import { moderateScale } from '../helper/Window'
import { Colors } from '../assets/Colors'
import { Icons } from '../assets/Icons'
import { useNavigation } from '@react-navigation/native'

const SubDealerList = [
    { id: 1, }, { id: 2, }, { id: 3, }, { id: 4, }, { id: 5, },
    { id: 6, }, { id: 7, }, { id: 8, }, { id: 9, }, { id: 10, },
    { id: 11, }, { id: 12, }, { id: 13, }, { id: 14, }, { id: 15, },
    { id: 16, }, { id: 17, },
]

const SubDealerView = (props) => {
    const navigation = useNavigation()
    const [checked, setChecked] = useState(false)
    const CheckedHandler = () => {
        setChecked(!checked)
    }
    return (
        <Modal
            isVisible={props.isVisible}
            style={{ margin: 0 }}
            customBackdrop={
                <TouchableWithoutFeedback onPress={() => { props.closeLoginPopup() }}>
                    <View style={{ flex: 1, backgroundColor: "black" }} />
                </TouchableWithoutFeedback>
            }>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.main }}>
                <View style={{ width: "100%", padding: moderateScale(20), backgroundColor: "#E41B14", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                    <TouchableOpacity onPress={() => { props.closeLoginPopup() }}>
                        <View style={{ width: moderateScale(30), height: moderateScale(30), borderRadius: moderateScale(20), borderWidth: moderateScale(1), borderColor: "#FFFFFF", alignItems: "center", justifyContent: "center" }}>
                            <Image source={Icons.Back} style={{ width: moderateScale(12), height: moderateScale(12), tintColor: "#FFFFFF" }} />
                        </View>
                    </TouchableOpacity>
                    <Text style={{ color: "#FFFFFF", fontSize: moderateScale(16), fontWeight: "600" }}>Please select a Sub-Dealer</Text>
                    <View style={{ width: moderateScale(30) }}></View>
                </View>
                <View style={{ width: "100%", flex: 1, backgroundColor: "#ffffff", paddingVertical: moderateScale(20), borderTopRightRadius: moderateScale(30), borderTopLeftRadius: moderateScale(30) }}>
                    <View style={{ width: "100%", flex: 1 }}>
                        <View style={{ width: "100%", paddingHorizontal: moderateScale(20) }}>
                            <View style={{ width: "100%", flexDirection: "row", alignItems: "center", gap: moderateScale(4), paddingHorizontal: moderateScale(10), borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10) }}>
                                <Image source={Icons.Search} style={{ width: moderateScale(18), height: moderateScale(18) }} />
                                <TextInput placeholder='Search' placeholderTextColor={"#A7A7A7"} style={{ flex: 1, color: Colors.text, fontSize: moderateScale(14) }}></TextInput>
                            </View>
                        </View>
                        <FlatList
                            data={SubDealerList}
                            keyExtractor={(item) => item.id}
                            showsVerticalScrollIndicator={false}
                            decelerationRate="fast"
                            renderItem={({ item, index }) => {
                                return (
                                    <TouchableOpacity activeOpacity={0.95} onPress={CheckedHandler} key={index.id}>
                                        <View style={{ paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(13), width: "100%", flexDirection: "row", alignItems: "center", gap: moderateScale(20), backgroundColor: (index % 2 != 0 ? "#F5F8EF" : "#FFFFFF") }}>
                                            {checked ? <View style={{ width: moderateScale(20), height: moderateScale(20), borderRadius: moderateScale(2), borderWidth: moderateScale(2), borderColor: "#8FC031", alignItems: "center", justifyContent: "center" }}>
                                                <Image source={Icons.Check} style={{ width: moderateScale(16), height: moderateScale(16) }} />
                                            </View>
                                                : <View style={{ width: moderateScale(20), height: moderateScale(20), borderRadius: moderateScale(2), borderWidth: moderateScale(2), borderColor: "#A7A7A7" }}></View>}
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500", textTransform: "uppercase" }}>All In One Solution</Text>
                                        </View>
                                    </TouchableOpacity>
                                )
                            }} />
                    </View>
                    <TouchableOpacity activeOpacity={0.95} onPress={() => { navigation.navigate("AllocationScreen") }}>
                        <View style={{ width: "100%", paddingHorizontal: moderateScale(15) }}>
                            <View style={{ width: "100%", height: moderateScale(40), flexDirection: "row", gap: moderateScale(8), backgroundColor: "#8FC031", borderRadius: moderateScale(10), alignItems: "center", justifyContent: "center" }}>
                                <Text style={{ color: Colors.white, fontSize: moderateScale(16), fontWeight: 500 }}>Submit</Text>
                            </View>
                        </View>
                    </TouchableOpacity>
                    <View style={{ height: moderateScale(20) }} />
                </View>
            </View>
        </Modal>
    )
}

export default SubDealerView
