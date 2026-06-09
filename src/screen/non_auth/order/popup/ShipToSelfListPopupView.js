import React, { useState, useEffect } from 'react'
import { FlatList, Text, TextInput, TouchableOpacity, TouchableWithoutFeedback, View } from 'react-native'
import Modal from 'react-native-modal'
import { moderateScale } from '../../../../helper/Window'
import { Colors } from '../../../../assets/Colors'
import DataStorage from '../../../../storage/DataStorage'

const ShipToSelfListPopupView = (props) => {
    const [searchText, setSearchText] = useState("")
    const [filteredData, setFilteredData] = useState(props.dataList || [])

    useEffect(() => {
        if (!searchText.trim()) {
            setFilteredData(props.dataList)
        } else {
            const lowerSearch = searchText.toLowerCase()
            const filtered = props.dataList.filter(item =>
                item.customer_name?.toLowerCase().includes(lowerSearch)
            )
            setFilteredData(filtered)
        }
        console.log(props?.dataList[0]);
        
    }, [searchText, props.dataList])

    const close = () => {
        setSearchText("")
        props.closePopup()
    }

    return (
        <Modal isVisible={props.isVisible} style={{ margin: 0 }} onBackdropPress={close} onBackButtonPress={close}
            customBackdrop={
                <TouchableWithoutFeedback onPress={() => { props.closePopup(), setSearchText("") }}>
                    <View style={{ flex: 1, backgroundColor: "black" }} />
                </TouchableWithoutFeedback>
            } >
            <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
                <TouchableOpacity style={{ width: '100%', height: '25%' }} onPress={() => { props.closePopup(), setSearchText("") }} >
                    <View style={{ width: '100%', height: '100%' }} />
                </TouchableOpacity>

                <View style={{ width: "100%", height: '75%', backgroundColor: Colors.main, borderTopLeftRadius: moderateScale(20), borderTopRightRadius: moderateScale(20) }}>
                    {/* Header */}
                    <View style={{ width: "100%", padding: moderateScale(16), backgroundColor: "#E41B14", flexDirection: "row", alignItems: "center", justifyContent: "space-between", borderTopLeftRadius: moderateScale(20), borderTopRightRadius: moderateScale(20) }}>
                        <Text style={{ color: "#FFFFFF", fontSize: moderateScale(16), fontWeight: "600" }}>
                            {props.isDealer ? "Select Dealer Name " : "Select Sub Dealer Name"}
                        </Text>
                    </View>

                    {/* Search Box */}
                    <View style={{ padding: moderateScale(15), backgroundColor: "#fff" }}>
                        <TextInput placeholder="Search by name..." value={searchText} onChangeText={setSearchText} style={{ height: moderateScale(40), borderWidth: 1, borderColor: "#ccc", borderRadius: moderateScale(10), paddingHorizontal: moderateScale(10), fontSize: moderateScale(14), color: Colors.text }} />
                    </View>

                    {/* List */}
                    <View style={{ width: "100%", flex: 1, backgroundColor: "#ffffff", paddingVertical: moderateScale(10), }}>
                        <FlatList
                            data={filteredData}
                            keyExtractor={(item, index) => item.id?.toString() || index.toString()}
                            showsVerticalScrollIndicator={false}
                            decelerationRate="fast"
                            renderItem={({ item, index }) => {
                                return (
                                    <TouchableOpacity activeOpacity={0.95} onPress={() => { props.selectItem(item) }} >
                                        <View style={{ width: "100%", paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(13), flexDirection: "row", alignItems: "center", justifyContent: "space-between", backgroundColor: (index % 2 !== 0 ? DataStorage.transColorCode : "#FFFFFF") }}>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500", textTransform: "uppercase" }}>
                                                {item.customer_name?.replaceAll("&amp;", '&') + (item?.SAP_code ?" - " + item?.SAP_code : "")}
                                            </Text>
                                        </View>
                                    </TouchableOpacity>
                                )
                            }}
                            ListEmptyComponent={() => (
                                <View style={{ padding: moderateScale(20), alignItems: "center" }}>
                                    <Text style={{ color: Colors.text, fontSize: moderateScale(14) }}> No results found </Text>
                                </View>
                            )}
                        />
                    </View>
                </View>
            </View>
        </Modal>
    )
}

export default ShipToSelfListPopupView
