import React, { useState, useEffect } from 'react'
import { FlatList, Text, TextInput, TouchableOpacity, TouchableWithoutFeedback, View } from 'react-native'
import Modal from 'react-native-modal'
import { Dropdown } from 'react-native-element-dropdown'
import { moderateScale } from '../helper/Window'
import { Colors } from '../assets/Colors'
import DataStorage from '../storage/DataStorage'
import { getAllDataFrom_customer_master1, getAllBranchCodeAndName } from '../storage/database/GetDataFromTable'
const PALETTE = {
    bg: '#F4F6FA',
    card: '#FFFFFF',
    accent: '#C0392B',
    accentLight: '#FDECEA',
    overdueBg: '#C0392B',
    overdueText: '#FFFFFF',
    normalBg: '#FFF8E1',
    normalText: '#B7791F',
    text: '#1A1D23',
    subtext: '#7B8290',
    border: '#E8ECF2',
    tabActiveBg: '#C0392B',
    tabInactiveBg: '#FFFFFF',
    divider: '#EEF1F7',
    shadow: '#B0BAD0',
}

const ShipToSelfListPopupViewNew = (props) => {
    const [searchText, setSearchText] = useState("")
    const [DataSet, setDataSet] = useState([])
    const [branchDataSet, setBranchDataSet] = useState([])
    const [filteredData, setFilteredData] = useState([])
    const categoryList = props.categoryList ?? []
    const [type, setType] = useState(categoryList[0]?.permision_type ?? '')
    const [typeBranch, setTypeBranch] = useState('')
    const isMulti = categoryList.length > 1
    const userList = props.dealerList ?? []

    useEffect(() => {
        if (!type && categoryList.length > 0) {
            setType(categoryList[0].permision_type)
        }
    }, [categoryList, type])

    useEffect(() => {
        const check = async () => {
            setDataSet([])
            setFilteredData([])
            setSearchText('')
            var arr = []
            if (DataStorage.typeOfUse === 1) {
                for (var a = 0; a < userList.length; a++) {
                    if (userList[a].cust_type.toLowerCase() == type.toLowerCase())
                        arr.push(userList[a])
                }
            } else
                arr = await getAllDataFrom_customer_master1(type, typeBranch)
            setDataSet(arr)
            setFilteredData(arr)
        }
        check()
    }, [type, typeBranch])

    useEffect(() => {
        const branchData = async () => {
            var arrBranch = []
            arrBranch = await getAllBranchCodeAndName()
            setBranchDataSet(arrBranch)
        }
        branchData()
    }, [])

    useEffect(() => {
        if (!searchText.trim()) {
            setFilteredData(DataSet)
        } else {
            const lowerSearch = searchText.toLowerCase()
            const filtered = DataSet.filter(item =>
                item.customer_name?.toLowerCase().includes(lowerSearch) ||
                item.SAP_code?.toString().toLowerCase().includes(lowerSearch)
            )
            setFilteredData(filtered)
        }
    }, [searchText, DataSet])

    const close = () => {
        setSearchText("")
        props.closePopup()
    }

    const branchDropdownData = branchDataSet.map((item, index) => ({
        ...item,
        dropdownKey: `${item.branch_code ?? index}`,
        dropdownLabel: `${item.branch_name ?? ''}`,
    }))

    return (
        <Modal isVisible={props.isVisible} style={{ margin: 0 }} onBackdropPress={close} onBackButtonPress={close}
            customBackdrop={
                <TouchableWithoutFeedback onPress={close}>
                    <View style={{ flex: 1, backgroundColor: "black" }} />
                </TouchableWithoutFeedback>
            } >
            <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
                <TouchableOpacity style={{ width: '100%', height: '25%' }} onPress={close} >
                    <View style={{ width: '100%', height: '100%' }} />
                </TouchableOpacity>
                <View style={{ width: "100%", height: '75%', backgroundColor: Colors.main, borderTopLeftRadius: moderateScale(20), borderTopRightRadius: moderateScale(20) }}>
                    <View style={{ width: "100%", padding: moderateScale(16), backgroundColor: "#E41B14", flexDirection: "row", alignItems: "center", justifyContent: "space-between", borderTopLeftRadius: moderateScale(20), borderTopRightRadius: moderateScale(20) }}>
                        <Text style={{ color: "#FFFFFF", fontSize: moderateScale(16), fontWeight: "600" }}>
                            {"Select " + type + " Name "}
                        </Text>
                    </View>
                    <View style={{ paddingHorizontal: moderateScale(15), backgroundColor: "#fff", paddingTop: moderateScale(10) }}>
                        {isMulti ? <View style={{ width: '100%', flexDirection: 'row' }}>
                            {categoryList.map((cat, i) => {
                                return (
                                    <TouchableOpacity onPress={() => setType(cat.permision_type)} style={{ flex: 1, alignItems: 'center', paddingVertical: moderateScale(8), borderRadius: moderateScale(2), backgroundColor: type == cat.permision_type ? PALETTE.accent : PALETTE.card, borderWidth: type == cat.permision_type ? 0 : 1, borderColor: PALETTE.border, shadowColor: type == cat.permision_type ? PALETTE.accent : PALETTE.shadow, shadowOffset: { width: 0, height: type == cat.permision_type ? 4 : 1 }, shadowOpacity: type == cat.permision_type ? 0.25 : 0.07, shadowRadius: type == cat.permision_type ? 8 : 3, elevation: type == cat.permision_type ? 5 : 1, }}>
                                        <Text style={{ fontSize: moderateScale(11), fontWeight: type == cat.permision_type ? '700' : '500', color: type == cat.permision_type ? '#fff' : PALETTE.subtext, letterSpacing: 0.2, textAlign: 'center', }}>{cat.permision_name}</Text>
                                    </TouchableOpacity>
                                )
                            })}
                        </View> : null}
                    </View>
                    <View style={{ paddingHorizontal: moderateScale(15), backgroundColor: "#fff", paddingTop: moderateScale(10) }}>
                        <Dropdown
                            style={{ height: moderateScale(44), borderWidth: 1, borderColor: PALETTE.border, borderRadius: moderateScale(8), paddingHorizontal: moderateScale(12), backgroundColor: PALETTE.card }}
                            containerStyle={{ borderRadius: moderateScale(8), borderColor: PALETTE.border, overflow: 'hidden' }}
                            placeholderStyle={{ color: PALETTE.subtext, fontSize: moderateScale(13) }}
                            selectedTextStyle={{ color: PALETTE.text, fontSize: moderateScale(13) }}
                            inputSearchStyle={{ height: moderateScale(40), borderRadius: moderateScale(6), borderColor: PALETTE.border, color: PALETTE.text, fontSize: moderateScale(13) }}
                            itemTextStyle={{ color: PALETTE.text, fontSize: moderateScale(13) }}
                            activeColor={PALETTE.accentLight}
                            data={branchDropdownData}
                            search
                            maxHeight={moderateScale(300)}
                            labelField="dropdownLabel"
                            valueField="dropdownKey"
                            placeholder="Select branch"
                            searchPlaceholder="Search branch name or code..."
                            value={typeBranch ? String(typeBranch) : ''}
                            onChange={item => setTypeBranch(String(item.branch_code ?? ''))}
                            flatListProps={{ keyboardShouldPersistTaps: 'handled' }}
                        />
                    </View>
                    <View style={{ padding: moderateScale(15), backgroundColor: "#fff" }}>
                        <TextInput placeholder="Search dealer name or code..." value={searchText} onChangeText={setSearchText} style={{ height: moderateScale(40), borderWidth: 1, borderColor: "#ccc", borderRadius: moderateScale(10), paddingHorizontal: moderateScale(10), fontSize: moderateScale(14), color: Colors.text }} />
                    </View>
                    <View style={{ width: "100%", flex: 1, backgroundColor: "#ffffff", paddingVertical: moderateScale(10), }}>
                        <FlatList
                            data={filteredData}
                            keyExtractor={(item, index) => item.id?.toString() || index.toString()}
                            showsVerticalScrollIndicator={false}
                            decelerationRate="fast"
                            keyboardShouldPersistTaps="handled"
                            renderItem={({ item, index }) => (
                                <TouchableOpacity activeOpacity={0.95} onPress={() => props.selectItem(item)}>
                                    <View style={{ width: "100%", paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(13), flexDirection: "row", alignItems: "center", justifyContent: "space-between", backgroundColor: (index % 2 !== 0 ? DataStorage.transColorCode : "#FFFFFF") }}>
                                        <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500", textTransform: "uppercase" }}>
                                            {item.customer_name?.replaceAll("&amp", '&') + (item?.SAP_code ? " - " + item?.SAP_code : "")}
                                        </Text>
                                    </View>
                                </TouchableOpacity>
                            )}
                            ListEmptyComponent={() => (
                                <View style={{ padding: moderateScale(20), alignItems: "center" }}>
                                    <Text style={{ color: Colors.text, fontSize: moderateScale(14) }}>No results found</Text>
                                </View>
                            )}
                        />
                    </View>
                </View>
            </View>
        </Modal>
    )
}

export default ShipToSelfListPopupViewNew
