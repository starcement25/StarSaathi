import React, { useState, useEffect } from 'react'
import { FlatList, Text, TextInput, TouchableOpacity, TouchableWithoutFeedback, View } from 'react-native'
import Modal from 'react-native-modal'
import DataStorage from '../storage/DataStorage'
import { Colors } from '../assets/Colors'
import { moderateScale } from '../helper/Window'

const SelectSubDealerCheckBox = (props) => {
    const [searchText, setSearchText] = useState("")
    const [filteredData, setFilteredData] = useState(props.dataList || [])
    const [selectedItems, setSelectedItems] = useState([])

    useEffect(() => {
        if (!searchText.trim())
            setFilteredData(props.dataList)
        else {
            const lowerSearch = searchText.toLowerCase()
            const filtered = props.dataList.filter(item =>
                item.customer_name?.toLowerCase().includes(lowerSearch)
            )
            setFilteredData(filtered)
        }
    }, [searchText, props.dataList])

    const getItemKey = (item) =>
        item.customer_code ?? item.SAP_code ?? item.customer_name

    const toggleSelection = (item) => {
        const key = getItemKey(item)
        const exists = selectedItems.some(selected => getItemKey(selected) === key)
        if (exists)
            setSelectedItems(prev => prev.filter(selected => getItemKey(selected) !== key))
        else
            setSelectedItems(prev => [...prev, item])
    }

    const handleCancel = () => {
        setSearchText("")
        setSelectedItems([])
        props.closePopup()
    }

    const handleSubmit = () => {
        props.selectItem(selectedItems)
        setSearchText("")
        setSelectedItems([])
    }

    return (
        <Modal
            isVisible={props.isVisible}
            style={{ margin: 0 }}
            customBackdrop={
                <TouchableWithoutFeedback onPress={handleCancel}>
                    <View style={{ flex: 1, backgroundColor: "black" }} />
                </TouchableWithoutFeedback>
            } >
            <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
                <TouchableOpacity style={{ width: '100%', height: '25%' }} onPress={handleCancel} >
                    <View style={{ width: '100%', height: '100%' }} />
                </TouchableOpacity>
                <View style={{ width: "100%", height: '75%', backgroundColor: Colors.main, borderTopLeftRadius: moderateScale(20), borderTopRightRadius: moderateScale(20) }}>
                    <View style={{ width: "100%", padding: moderateScale(16), backgroundColor: "#E41B14", flexDirection: "row", alignItems: "center", justifyContent: "space-between", borderTopLeftRadius: moderateScale(20), borderTopRightRadius: moderateScale(20) }}>
                        <Text style={{ color: "#FFFFFF", fontSize: moderateScale(16), fontWeight: "600" }}> {"Select Sub Dealer Name"} </Text>
                    </View>
                    <View style={{ padding: moderateScale(15), backgroundColor: "#fff" }}>
                        <TextInput
                            placeholder="Search by name..."
                            value={searchText}
                            onChangeText={setSearchText}
                            style={{ height: moderateScale(40), borderWidth: 1, borderColor: "#ccc", borderRadius: moderateScale(10), paddingHorizontal: moderateScale(10), fontSize: moderateScale(14), color: Colors.text }} />
                    </View>
                    <View style={{ width: "100%", flex: 1, backgroundColor: "#ffffff", paddingVertical: moderateScale(10), }}>
                        <FlatList
                            data={filteredData}
                            keyExtractor={(item, index) => item.id?.toString() || index.toString()}
                            showsVerticalScrollIndicator={false}
                            decelerationRate="fast"
                            renderItem={({ item, index }) => {
                                const isSelected = selectedItems.some(selected => getItemKey(selected) === getItemKey(item))
                                return (
                                    <TouchableOpacity activeOpacity={0.95} onPress={() => toggleSelection(item)} >
                                        <View style={{ width: "100%", paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(13), flexDirection: "row", alignItems: "center", backgroundColor: (index % 2 !== 0 ? DataStorage.transColorCode : "#FFFFFF") }}>
                                            <View style={{ width: 20, height: 20, borderRadius: 4, borderWidth: 1.5, borderColor: "#E41B14", marginRight: 12, alignItems: "center", justifyContent: "center", backgroundColor: isSelected ? "#E41B14" : "#fff" }}>
                                                {isSelected && <Text style={{ color: "#fff", fontSize: 12 }}>✓</Text>}
                                            </View>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500", textTransform: "uppercase" }}> {item.customer_name?.replaceAll("&amp", '&') + (item?.SAP_code ? " - " + item?.SAP_code : "")} </Text>
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
                    <View style={{ flexDirection: "row", justifyContent: "space-between", padding: moderateScale(15), backgroundColor: "#fff", borderTopWidth: 1, borderColor: "#ddd" }}>
                        <TouchableOpacity onPress={handleCancel} style={{ flex: 1, marginRight: 10, paddingVertical: 12, borderRadius: 8, backgroundColor: "#ccc", alignItems: "center" }} >
                            <Text style={{ color: "#000", fontWeight: "600" }}>Cancel</Text>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={handleSubmit} style={{ flex: 1, marginLeft: 10, paddingVertical: 12, borderRadius: 8, backgroundColor: "#E41B14", alignItems: "center" }} >
                            <Text style={{ color: "#fff", fontWeight: "600" }}>Submit</Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </View>
        </Modal>
    )
}

export default SelectSubDealerCheckBox
