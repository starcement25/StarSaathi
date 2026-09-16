import React, { useCallback, useEffect, useState } from "react"
import { View, Text, TouchableOpacity, StyleSheet, Modal, FlatList, } from "react-native"
import SBSCommonHeaderView from "../../../common/SBSCommonHeaderView"
import SafeView from "../../../helper/SafeView"
import { Colors } from "../../../assets/Colors"
import Toast from "react-native-toast-message"
import UrlStorage from "../../../storage/UrlStorage"
import Loader from "../../../common/Loader"
import toastConfig from "../../../helper/ToastConfig"
import { AuthCheckingApi } from "../../../auth/AuthCheckingApi"
import AuthNotVerifyPopupView from "../../../auth/AuthNotVerifyPopupView"
import { moderateScale } from "../../../helper/Window"
import DataStorage from "../../../storage/DataStorage"
const MonthItem = React.memo(({ item, onPress }) => {
    const handlePress = useCallback(() => onPress(item), [item, onPress])
    return (
        <TouchableOpacity style={styles.monthItem} onPress={handlePress} activeOpacity={0.7}>
            <View style={styles.monthItemContent}>
                <Text style={styles.monthText}>{item.month_name}</Text>
                <View style={styles.monthIcon}>
                    <Text style={styles.chevronIcon}>›</Text>
                </View>
            </View>
        </TouchableOpacity>
    )
})
const YearItem = React.memo(({ item, onPress }) => {
    const handlePress = useCallback(() => onPress(item), [item, onPress])
    return (
        <TouchableOpacity style={styles.monthItem} onPress={handlePress} activeOpacity={0.7}>
            <View style={styles.monthItemContent}>
                <Text style={styles.monthText}>{item.year}</Text>
                <View style={styles.monthIcon}>
                    <Text style={styles.chevronIcon}>›</Text>
                </View>
            </View>
        </TouchableOpacity>
    )
})
const HistoryItem = React.memo(({ item }) => {
    return (
        <View style={{ paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(7) }}>
            <View style={{ width: "100%", overflow: "hidden", borderWidth: moderateScale(1), padding: moderateScale(10), borderColor: "#DCDDDF", borderRadius: moderateScale(10), backgroundColor: "white", elevation: 4, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: 0, y: 4 } }}>
                <View style={{ width: "100%", flexDirection: "row", alignItems: "center" }}>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 1.25 }}> Dealer Name </Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {":  "} </Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 2 }}> {item.Dealer_Name || ''} </Text>
                </View>
                <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(4) }}>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 1.25 }}> Branch Name </Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {":  "} </Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 2 }}> {item.Branch || ''} </Text>
                </View>
                <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(4) }}>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 1.25 }}> Declaration Month </Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {":  "} </Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 2 }}> {item.Month || ''} </Text>
                </View>
                <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(4) }}>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 1.25 }}> Lifting Qty </Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {":  "} </Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 2 }}> {item.Lifting_Qty || ''} </Text>
                </View>
                <>
                    <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(4) }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 1.25 }}> ASM Name </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {":  "} </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 2 }}> {item.Approved_By_ASM || ''} </Text>
                    </View>
                    <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(4) }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 1.25 }}> ASM Approve Status </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {":  "} </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 2 }}> {item.ASM_Approval_Status || ''} </Text>
                    </View>
                    {item.ASM_Approval_Status.toLowerCase() != 'pending' ? <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(4) }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 1.25 }}> ASM Approve Date </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {":  "} </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 2 }}> {item.ASM_Approved_Date || ''} </Text>
                    </View> : null}
                </>
                <>
                    <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(4) }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 1.25 }}> RSM Name </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {":  "} </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 2 }}> {item.Approved_By_RSM || ''} </Text>
                    </View>
                    <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(4) }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 1.25 }}> RSM Approve Status </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {":  "} </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 2 }}> {item.RSM_Approval_Status || ''} </Text>
                    </View>
                    {item.RSM_Approval_Status.toLowerCase() != 'pending' ? <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(4) }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 1.25 }}> RSM Approve Date </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {":  "} </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 2 }}> {item.RSM_Approved_Date || ''} </Text>
                    </View> : null}
                </>
                {item.Status.toLowerCase() == 'pending' ? <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(4) }}>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 1.25 }}> Status </Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {":  "} </Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 2 }}> {item.Status || ''} </Text>
                </View> : null}
                {item.Status.toLowerCase() == 'reject' ? <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(4) }}>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 1.25 }}> Reject Reason </Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {":  "} </Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), flex: 2 }}> {item.Reason_for_rejection || ''} </Text>
                </View> : null}
            </View>
        </View>
    )
})
export default function DealerDeclarationHistoryScreen({ navigation }) {
    const [loading, setLoading] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)
    const [selectedMonth, setSelectedMonth] = useState("")
    const [selectedYear, setSelectedYear] = useState("")
    const monthList = [{ month_name: 'April' }, { month_name: 'May' }, { month_name: 'June' }, { month_name: 'July' }, { month_name: 'August' }, { month_name: 'September' }, { month_name: 'October' }, { month_name: 'November' }, { month_name: 'December' }, { month_name: 'January' }, { month_name: 'February' }, { month_name: 'March' },]
    const yearList = [{ year: 2025 }, { year: 2026 }, { year: 2027 },]
    const [monthModalVisible, setMonthModalVisible] = useState(false)
    const [yearModalVisible, setYearModalVisible] = useState(false)
    const [isFilter, setIsFilter] = useState(false)
    const [isFilterData, setIsFilterData] = useState(false)
    const [allDataList, setAllDataList] = useState([])
    const [filterList, setFilterList] = useState([])
    const [status, setStatus] = useState('')
    const monthKeyExtractor = (item) => item.value
    const yearKeyExtractor = (item) => item.value

    useEffect(() => {
        checkAuth()
    }, [])

    useEffect(() => {
        const a = allDataList.filter((item) => item.Status.toLowerCase() === status.toLowerCase())
        setFilterList(a)
    }, [status])

    const filterData = () => {
        var check = false
        const a = allDataList.filter((item) => {
            var checkMonth = false
            var checkYear = false
            if (selectedMonth != "" && selectedYear != "") {
                checkMonth = item.Month.includes(selectedMonth)
                checkYear = item.Month.includes(selectedYear)
                check = true
            } else if (selectedMonth != "" && selectedYear == "") {
                checkMonth = item.Month.includes(selectedMonth)
                checkYear = true
                check = true
            } else if (selectedMonth == "" && selectedYear != "") {
                checkMonth = true
                checkYear = item.Month.includes(selectedYear)
                check = true
            } else
                Toast.show({ type: 'error', text1: 'Sorry', text2: 'Please select anything for filter' })
            return checkMonth && checkYear
        })
        setFilterList(a)
        setIsFilter(!isFilter)
        setIsFilterData(check)
    }

    const checkAuth = async () => {
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        requestForAllList()
    }

    const requestForAllList = () => {
        setLoading(true)
        const requestOptions = { method: "GET", redirect: "follow" }
        fetch(UrlStorage.BaseUrlList.Saathi.base_url_saathi + "/dealer_exclusice_f2.php?customer_id=" + UrlStorage.ParameterList.BasicData.emp_id, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                setAllDataList(result.data)
                setStatus('pending')
                setLoading(false)
            })
            .catch((error) => { })
    }

    const handleSelectMonth = useCallback((item) => {
        setMonthModalVisible(false)
        setSelectedMonth(item.month_name)
    }, [])

    const handleSelectYear = useCallback((item) => {
        setYearModalVisible(false)
        setSelectedYear(item.year)
    }, [])

    const renderMonthItem = useCallback(
        ({ item }) => <MonthItem item={item} onPress={handleSelectMonth} />,
        [handleSelectMonth]
    )

    const renderYearItem = useCallback(
        ({ item }) => <YearItem item={item} onPress={handleSelectYear} />,
        [handleSelectYear]
    )

    const renderHistoryItem = useCallback(
        ({ item }) => <HistoryItem item={item} />,
        []
    )

    const handlePerformanceFilterOpen = () => {
        setIsFilterData(false)
        setFilterList(allDataList)
        setIsFilter(!isFilter)
    }

    const resetFilter = () => {
        setIsFilter(false)
        setIsFilterData(false)
        const a = allDataList.filter((item) => item.Status.toLowerCase() === status.toLowerCase())
        setFilterList(a)
    }

    return (
        <SafeView style={{ flex: 1, backgroundColor: "#f8f9fa" }} statusbarColor={Colors.main}>
            <SBSCommonHeaderView title="Dealer Declaration History" Filter={true} handlePerformanceFilterOpen={handlePerformanceFilterOpen} />
            <View style={{ flex: 1, flexDirection: 'column', padding: moderateScale(10) }}>
                {isFilter ? <>
                    <View>
                        <Text style={styles.fieldLabel}>Select Year</Text>
                        <TouchableOpacity style={[styles.monthSelector, selectedYear ? styles.monthSelectorActive : null]} onPress={() => setYearModalVisible(true)} activeOpacity={0.7} >
                            <Text style={selectedMonth ? styles.monthSelectorTextActive : styles.monthSelectorText}> {" "}{selectedYear || "Tap to select year"}{" "} </Text>
                            <Text style={styles.monthSelectorArrow}>▼</Text>
                        </TouchableOpacity>
                    </View>
                    <View style={{ marginTop: moderateScale(10) }}>
                        <Text style={styles.fieldLabel}>Select Month</Text>
                        <TouchableOpacity style={[styles.monthSelector, selectedMonth ? styles.monthSelectorActive : null]} onPress={() => setMonthModalVisible(true)} activeOpacity={0.7} >
                            <Text style={selectedMonth ? styles.monthSelectorTextActive : styles.monthSelectorText}> {" "}{selectedMonth || "Tap to select month"}{" "} </Text>
                            <Text style={styles.monthSelectorArrow}>▼</Text>
                        </TouchableOpacity>
                    </View>
                    <TouchableOpacity onPress={() => { filterData() }} activeOpacity={0.95} style={{ width: '100%', height: moderateScale(40), marginTop: moderateScale(10), borderRadius: moderateScale(10), backgroundColor: Colors.main, alignItems: 'center', justifyContent: 'center', }} >
                        <Text style={{ color: Colors.white, fontSize: moderateScale(14), fontWeight: '500' }}> Continue </Text>
                    </TouchableOpacity>
                </> : <>
                    {isFilterData ? <View style={{ width: '100%', flexDirection: 'row-reverse', marginBottom: moderateScale(5) }}>
                        <TouchableOpacity onPress={() => { resetFilter() }}>
                            <Text style={{ color: Colors.text, textDecorationLine: 'underline', textDecorationColor: Colors.text, fontStyle: 'italic' }}>Clear Filter</Text>
                        </TouchableOpacity>
                    </View> : <View style={{ width: '100%', flexDirection: 'row', gap: moderateScale(5) }}>
                        <TouchableOpacity onPress={() => { setStatus('pending') }} activeOpacity={0.95} style={{ flex: 1, height: moderateScale(40), borderRadius: moderateScale(10), backgroundColor: status == 'pending' ? Colors.main : Colors.white, alignItems: 'center', justifyContent: 'center', elevation: 2, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: (0), y: (4) } }} >
                            <Text style={{ color: status != 'pending' ? Colors.main : Colors.white, fontSize: moderateScale(14), fontWeight: '500' }}> Pending </Text>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={() => { setStatus('approved') }} activeOpacity={0.95} style={{ flex: 1, height: moderateScale(40), borderRadius: moderateScale(10), backgroundColor: status == 'approved' ? Colors.main : Colors.white, alignItems: 'center', justifyContent: 'center', elevation: 2, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: (0), y: (4) } }} >
                            <Text style={{ color: status != 'approved' ? Colors.main : Colors.white, fontSize: moderateScale(14), fontWeight: '500' }}> Approved </Text>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={() => { setStatus('rejected') }} activeOpacity={0.95} style={{ flex: 1, height: moderateScale(40), borderRadius: moderateScale(10), backgroundColor: status == 'rejected' ? Colors.main : Colors.white, alignItems: 'center', justifyContent: 'center', elevation: 2, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: (0), y: (4) } }} >
                            <Text style={{ color: status != 'rejected' ? Colors.main : Colors.white, fontSize: moderateScale(14), fontWeight: '500' }}> Rejected </Text>
                        </TouchableOpacity>
                    </View>}
                </>}
                <FlatList
                    data={filterList}
                    keyExtractor={(item) => item.value}
                    style={{ marginTop: isFilterData ? 0 : 10 }}
                    renderItem={renderHistoryItem}
                    ItemSeparatorComponent={() => <View style={{}} />}
                />
            </View>
            <Modal visible={monthModalVisible} transparent animationType="slide" onRequestClose={() => setMonthModalVisible(false)} hardwareAccelerated={true} >
                <View style={styles.monthModalOverlay}>
                    <TouchableOpacity style={styles.modalBackdrop} activeOpacity={1} onPress={() => setMonthModalVisible(false)} />
                    <View style={styles.monthModalContainer}>
                        <View style={styles.monthModalHeader}>
                            <Text style={styles.monthModalTitle}>Select Month</Text>
                            <TouchableOpacity onPress={() => setMonthModalVisible(false)} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }} activeOpacity={0.7} >
                                <Text style={styles.closeIcon}>✕</Text>
                            </TouchableOpacity>
                        </View>
                        <FlatList
                            data={monthList}
                            keyExtractor={monthKeyExtractor}
                            renderItem={renderMonthItem}
                            initialNumToRender={8}
                            maxToRenderPerBatch={8}
                            windowSize={5}
                            removeClippedSubviews={false}
                            ItemSeparatorComponent={() => <View style={styles.itemSeparator} />}
                            scrollEventThrottle={16}
                            keyboardShouldPersistTaps="handled"
                        />
                    </View>
                </View>
            </Modal>
            <Modal visible={yearModalVisible} transparent animationType="slide" onRequestClose={() => setYearModalVisible(false)} hardwareAccelerated={true} >
                <View style={styles.monthModalOverlay}>
                    <TouchableOpacity style={styles.modalBackdrop} activeOpacity={1} onPress={() => setYearModalVisible(false)} />
                    <View style={styles.monthModalContainer}>
                        <View style={styles.monthModalHeader}>
                            <Text style={styles.monthModalTitle}>Select Year</Text>
                            <TouchableOpacity onPress={() => setYearModalVisible(false)} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }} activeOpacity={0.7} >
                                <Text style={styles.closeIcon}>✕</Text>
                            </TouchableOpacity>
                        </View>
                        <FlatList
                            data={yearList}
                            keyExtractor={yearKeyExtractor}
                            renderItem={renderYearItem}
                            initialNumToRender={8}
                            maxToRenderPerBatch={8}
                            windowSize={5}
                            removeClippedSubviews={false}
                            ItemSeparatorComponent={() => <View style={styles.itemSeparator} />}
                            scrollEventThrottle={16}
                            keyboardShouldPersistTaps="handled"
                        />
                    </View>
                </View>
            </Modal>
            <Toast config={toastConfig} />
            {loading && <Loader />}
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

const styles = StyleSheet.create({
    flex1: { flex: 1 },
    container: { flex: 1, backgroundColor: "#f8f9fa" },
    scrollContainer: { flex: 1, paddingHorizontal: 16 },
    letterheadSection: { marginTop: 24, marginBottom: 28, paddingBottom: 20, borderBottomWidth: 1.5, borderBottomColor: "#e0e0e0", },
    recipientLabel: { fontSize: 12, color: "#757575", letterSpacing: 0.5, marginBottom: 4, fontWeight: "500" },
    recipientValue: { fontSize: 18, fontWeight: "700", color: "#1a1a1a", letterSpacing: 0.3 },
    greetingSection: { marginBottom: 32 },
    greetingLabel: { fontSize: 14, fontWeight: "600", color: "#1a1a1a", marginBottom: 8 },
    descriptionText: { fontSize: 13.5, color: "#424242", lineHeight: 20, letterSpacing: 0.2 },
    formSection: { marginBottom: 8 },
    formGroup: { marginBottom: 20 },
    fieldLabel: { fontSize: 12.5, color: "#1a1a1a", marginBottom: 8, letterSpacing: 0.3 },
    input: { height: 44, borderRadius: 1, backgroundColor: "#fff", paddingHorizontal: 14, fontSize: 14, color: "#1a1a1a", borderWidth: 0.5, borderColor: "#e0e0e0", },
    monthSelectorGroup: { marginBottom: 20, marginTop: 24 },
    monthSelector: { height: 44, borderRadius: 8, backgroundColor: "#fff", paddingHorizontal: 14, borderWidth: 0.5, borderColor: "#e0e0e0", flexDirection: "row", alignItems: "center", justifyContent: "space-between", },
    monthSelectorActive: { borderColor: "#363f45", backgroundColor: "#f5f5f5" },
    monthSelectorText: { fontSize: 14, color: "#999" },
    monthSelectorTextActive: { fontSize: 14, color: "#1a1a1a" },
    monthSelectorArrow: { fontSize: 10, color: "#757575", fontWeight: "700" },
    submitButtonContainer: { paddingHorizontal: 16, paddingBottom: 16, paddingTop: 12, backgroundColor: "#fff", borderTopWidth: 1, borderTopColor: "#e0e0e0", },
    submitBtn: { height: 48, backgroundColor: "#363f45", alignItems: "center", justifyContent: "center", borderRadius: 8, elevation: 2, },
    submitBtnDisabled: { opacity: 0.5 },
    submitText: { color: "#fff", fontWeight: "700", fontSize: 15, letterSpacing: 0.3 },
    monthModalOverlay: { flex: 1, justifyContent: "flex-end" },
    modalBackdrop: { ...StyleSheet.absoluteFillObject, backgroundColor: "rgba(0,0,0,0.55)", },
    monthModalContainer: { width: "100%", backgroundColor: "#fff", borderTopLeftRadius: 24, borderTopRightRadius: 24, maxHeight: "85%", },
    monthModalHeader: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", paddingHorizontal: 20, paddingVertical: 16, borderBottomWidth: 1, borderBottomColor: "#f0f0f0", },
    monthModalTitle: { fontSize: 16, fontWeight: "700", color: "#1a1a1a" },
    closeIcon: { fontSize: 22, color: "#999", fontWeight: "600" },
    monthItem: { paddingVertical: 0, backgroundColor: "#fff" },
    monthItemContent: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", paddingHorizontal: 20, paddingVertical: 14, },
    monthText: { fontSize: 14.5, color: "#1a1a1a", fontWeight: "500", flex: 1 },
    monthIcon: { width: 24, height: 24, alignItems: "center", justifyContent: "center" },
    chevronIcon: { fontSize: 18, color: "#ccc", fontWeight: "300" },
    itemSeparator: { height: 1, backgroundColor: "#f5f5f5", marginHorizontal: 20 },
    alertModalOverlay: { flex: 1, backgroundColor: "rgba(0,0,0,0.55)", justifyContent: "center", alignItems: "center", paddingHorizontal: 20, },
    alertModalContainer: { width: "100%", backgroundColor: "#fff", borderRadius: 16, padding: 24, elevation: 12, },
    alertHeader: { flexDirection: "row", alignItems: "center", marginBottom: 16 },
    alertIconContainer: { width: 44, height: 44, borderRadius: 22, backgroundColor: "#fff3e0", alignItems: "center", justifyContent: "center", marginRight: 12, },
    alertIcon: { fontSize: 24, fontWeight: "700" },
    alertTitle: { fontSize: 15, fontWeight: "700", color: "#1a1a1a", flex: 1, lineHeight: 22 },
    alertMessage: { fontSize: 13.5, color: "#424242", lineHeight: 20, marginBottom: 24, letterSpacing: 0.2 },
    alertButtonRow: { flexDirection: "row", justifyContent: "flex-end", gap: 10 },
    alertSecondaryBtn: { paddingVertical: 11, paddingHorizontal: 24, borderRadius: 8, borderWidth: 1.5, borderColor: "#e0e0e0", backgroundColor: "#f8f9fa", },
    alertSecondaryBtnText: { fontSize: 14, fontWeight: "600", color: "#424242" },
    alertPrimaryBtn: { paddingVertical: 11, paddingHorizontal: 24, borderRadius: 8, backgroundColor: "#363f45" },
    alertPrimaryBtnText: { fontSize: 14, fontWeight: "600", color: "#fff" },
})