import React, { useEffect, useRef, useState } from 'react'
import { FlatList, Modal, StyleSheet, Text, TextInput, TouchableOpacity, View } from 'react-native'
import SafeView from '../../../helper/SafeView'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import UrlStorage from '../../../storage/UrlStorage'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const RssdLiftingAllocationScreen = () => {
    const [loading, setLoading] = useState()
    const [assignedFilterOpen, setAssignedFilterOpen] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)
    const [liftingList, setLiftingList] = useState([])
    const [filteredList, setFilteredList] = useState([])
    const [searchText, setSearchText] = useState("")
    const [selectedDate, setSelectedDate] = useState(`${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(2, "0")}`)

    useEffect(() => {
        getLiftingAllocationDetails()
    }, [selectedDate])

    const getLiftingAllocationDetails = async (page_no) => {
        setLoading(true)
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        let baseUrl = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.AllocationHistoryURL.allocation_history_list_url + `?customer_id=${UrlStorage.ParameterList.BasicData.emp_id}&user_type=${UrlStorage.ParameterList.BasicData.user_type}&page_no=${page_no}&year_month=${selectedDate}`
        const requestOptions = { method: "GET", redirect: "follow" }
        await fetch(baseUrl, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                if (result?.process_status == "YES") {
                    setLiftingList(result?.allocation_data)
                    setFilteredList(result?.allocation_data)
                } else {
                    setLiftingList([])
                    setFilteredList([])
                }
            }).catch((error) => { })
        setLoading(false)
    }

    const handleSearch = (text) => {
        setSearchText(text)
        if (text.trim() === "") {
            setFilteredList(liftingList)
        } else {
            const filtered = liftingList.filter(item =>
                item.prod_desc?.toLowerCase().includes(text.toLowerCase())
            )
            setFilteredList(filtered)
        }
    }

    const AssignedFilterOpenHandler = () => {
        setAssignedFilterOpen(!assignedFilterOpen)
    }

    const MonthYearPicker = ({ visible, onClose, onConfirm, currentSelectedDate }) => {
        const today = new Date()
        const currentMonth = today.getMonth() + 1
        const currentYear = today.getFullYear()
        const parseSelectedDate = (dateString) => {
            if (dateString) {
                const [year, month] = dateString.split("-")
                return {
                    month: parseInt(month, 10),
                    year: parseInt(year, 10)
                }
            }
            return { month: currentMonth, year: currentYear }
        }
        const parsedDate = parseSelectedDate(currentSelectedDate)
        const [selectedMonth, setSelectedMonth] = useState(parsedDate.month)
        const [selectedYear, setSelectedYear] = useState(parsedDate.year)
        const monthRef = useRef(null)
        const yearRef = useRef(null)
        const months = [
            { label: "Jan", value: 1 },
            { label: "Feb", value: 2 },
            { label: "Mar", value: 3 },
            { label: "Apr", value: 4 },
            { label: "May", value: 5 },
            { label: "Jun", value: 6 },
            { label: "Jul", value: 7 },
            { label: "Aug", value: 8 },
            { label: "Sep", value: 9 },
            { label: "Oct", value: 10 },
            { label: "Nov", value: 11 },
            { label: "Dec", value: 12 },
        ]
        const years = Array.from({ length: currentYear - 2000 + 1 }, (_, i) => 2000 + i).reverse()
        const onModalShow = () => {
            const updatedParsedDate = parseSelectedDate(currentSelectedDate)
            setSelectedMonth(updatedParsedDate.month)
            setSelectedYear(updatedParsedDate.year)
            setTimeout(() => {
                if (monthRef.current) {
                    monthRef.current.scrollToIndex({
                        index: updatedParsedDate.month - 1,
                        animated: true
                    })
                }
                if (yearRef.current) {
                    const yearIndex = years.indexOf(updatedParsedDate.year)
                    if (yearIndex !== -1) {
                        yearRef.current.scrollToIndex({
                            index: yearIndex,
                            animated: true
                        })
                    }
                }
            }, 100)
        }

        return (
            <Modal visible={visible} animationType="slide" transparent onShow={onModalShow}>
                <View style={styles.overlay}>
                    <View style={styles.modal}>
                        <Text style={styles.title}>Select Month & Year</Text>
                        <View style={styles.pickerRow}>
                            <FlatList
                                ref={monthRef}
                                showsVerticalScrollIndicator={false}
                                data={months}
                                keyExtractor={(item) => item.value.toString()}
                                style={{ flex: 1 }}
                                contentContainerStyle={{ paddingVertical: 10 }}
                                renderItem={({ item }) => (
                                    <TouchableOpacity style={[styles.option, selectedMonth === item.value && styles.selectedOption,]} onPress={() => setSelectedMonth(item.value)} >
                                        <Text style={[styles.optionText, selectedMonth === item.value && styles.selectedText,]} > {item.label} </Text>
                                    </TouchableOpacity>
                                )}
                                getItemLayout={(data, index) => ({ length: 50, offset: 50 * index, index })}
                            />
                            <FlatList
                                ref={yearRef}
                                data={years}
                                showsVerticalScrollIndicator={false}
                                keyExtractor={(item) => item.toString()}
                                style={{ flex: 1 }}
                                contentContainerStyle={{ paddingVertical: 10 }}
                                renderItem={({ item }) => (
                                    <TouchableOpacity style={[styles.option, selectedYear === item && styles.selectedOption,]} onPress={() => setSelectedYear(item)} >
                                        <Text style={[styles.optionText, selectedYear === item && styles.selectedText,]} > {item} </Text>
                                    </TouchableOpacity>
                                )}
                                getItemLayout={(data, index) => ({ length: 50, offset: 50 * index, index })}
                            />
                        </View>

                        <View style={styles.buttons}>
                            <TouchableOpacity style={styles.cancel} onPress={onClose}>
                                <Text>Cancel</Text>
                            </TouchableOpacity>
                            <TouchableOpacity
                                style={styles.confirm}
                                onPress={() => {
                                    const formatted = `${selectedYear}-${String(selectedMonth).padStart(2, "0")}`
                                    onConfirm(formatted)
                                }} >
                                <Text style={{ color: "#fff" }}>Confirm</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
            </Modal>
        )
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ flex: 1, backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Sub Dealer Allocation" Filter={true} handlePerformanceFilterOpen={AssignedFilterOpenHandler} />
                <View style={styles.searchContainer}>
                    <TextInput placeholder="Search by product..." placeholderTextColor="#888" value={searchText} onChangeText={handleSearch} style={styles.searchInput} />
                </View>
                {filteredList.length > 0 ? <FlatList
                    data={filteredList}
                    keyExtractor={(item, index) => index.toString()}
                    showsVerticalScrollIndicator={false}
                    decelerationRate="fast"
                    renderItem={({ item }) => {
                        return (
                            <View style={styles.card}>
                                <View style={styles.cell}>
                                    <Text style={styles.label}>Product</Text>
                                    <Text style={styles.value}>{item.prod_desc}</Text>
                                </View>
                                <View style={styles.cell}>
                                    <Text style={styles.label}>Allocated Qty</Text>
                                    <Text style={styles.value}>{item.allocation_qty}</Text>
                                </View>
                                <View style={styles.cell}>
                                    <Text style={styles.label}>Transaction Date</Text>
                                    <Text style={styles.value}>{item.date_and_time}</Text>
                                </View>
                                <View style={styles.cell}>
                                    <Text style={styles.label}>Counter Name</Text>
                                    <Text style={styles.value}>{item.counter_name}</Text>
                                </View>
                                <View style={styles.cell}>
                                    <Text style={styles.label}>Invoice No</Text>
                                    <Text style={styles.value}>{item.inv_no}</Text>
                                </View>
                                <View style={styles.cell}>
                                    <Text style={styles.label}>Invoice Date</Text>
                                    <Text style={styles.value}>{item.inv_date}</Text>
                                </View>
                            </View>
                        )
                    }}
                /> : <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
                    <Text style={{ fontSize: 16, color: '#999' }}>No records found</Text>
                </View>}
            </View>
            {assignedFilterOpen && <MonthYearPicker visible={assignedFilterOpen} currentSelectedDate={selectedDate} onClose={() => setAssignedFilterOpen(false)} onConfirm={(value) => {
                setAssignedFilterOpen(false)
                setSelectedDate(value)
            }} />}
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />

        </SafeView>
    )
}

export default RssdLiftingAllocationScreen

const styles = StyleSheet.create({
    overlay: { flex: 1, backgroundColor: "rgba(0,0,0,0.5)", justifyContent: "flex-end", },
    modal: { backgroundColor: "#fff", borderTopLeftRadius: 20, borderTopRightRadius: 20, padding: 20, height: "60%", },
    title: { fontSize: 18, fontWeight: "600", marginBottom: 10, textAlign: "center" },
    pickerRow: { flexDirection: "row", flex: 1 },
    option: { padding: 12, alignItems: "center", height: 50, justifyContent: "center" },
    selectedOption: { backgroundColor: "#E41B14", borderRadius: 8 },
    optionText: { fontSize: 16, color: "#000" },
    selectedText: { color: "#fff", fontWeight: "bold" },
    buttons: { flexDirection: "row", justifyContent: "space-between", marginTop: 15 },
    cancel: { flex: 1, padding: 12, backgroundColor: "#ccc", borderRadius: 8, alignItems: "center", marginRight: 10 },
    confirm: { flex: 1, padding: 12, backgroundColor: "#E41B14", borderRadius: 8, alignItems: "center" },
    allocationOverlay: { flex: 1, backgroundColor: "rgba(0,0,0,0.5)", justifyContent: "center", alignItems: "center", },
    allocationModal: { backgroundColor: "#fff", borderRadius: 20, padding: 20, width: "90%", height: moderateScale(400), maxHeight: "70%", },
    cell: { flex: 1, flexDirection: 'row', paddingHorizontal: 6, },
    allocationTitle: { fontSize: 18, fontWeight: "700", textAlign: "center", marginBottom: 20, color: Colors.text, },
    allocationContent: { flex: 1, gap: 15, },
    allocationRow: { flexDirection: "row", paddingVertical: 8, },
    allocationLabel: { fontSize: 14, color: "#7D7D7D", width: moderateScale(100), fontWeight: "500", },
    allocationValue: { fontSize: 14, color: Colors.text, fontWeight: "500", flex: 1, },
    quantityInputRow: { flexDirection: "row", alignItems: "center", paddingVertical: 8, },
    quantityInput: { flex: 1, height: 45, borderWidth: 1, borderColor: "#DCDDDF", borderRadius: 8, paddingHorizontal: 12, fontSize: 14, color: Colors.text, marginLeft: 10, backgroundColor: "#F9F9F9", },
    mtLabel: { fontSize: 14, color: "#7D7D7D", marginLeft: 10, fontWeight: "500", },
    allocationDataRow: { flexDirection: "row", justifyContent: "space-between", gap: 15, },
    allocationDataItem: { flex: 1, },
    allocationDataLabel: { fontSize: 12, color: "#7D7D7D", marginBottom: 5, fontWeight: "500", },
    allocationDataInput: { height: 40, borderWidth: 1, borderColor: "#DCDDDF", borderRadius: 8, paddingHorizontal: 12, fontSize: 14, color: Colors.text, backgroundColor: "#F5F5F5", },
    allocationButtons: { flexDirection: "row", justifyContent: "space-between", marginTop: 20, gap: 15, },
    allocationCancel: { flex: 1, padding: 12, backgroundColor: "#F0F0F0", borderRadius: 8, alignItems: "center", borderWidth: 1, borderColor: "#DCDDDF", },
    allocationCancelText: { fontSize: 14, color: "#666", fontWeight: "500", },
    allocationConfirm: { flex: 1, padding: 12, backgroundColor: "#E41B14", borderRadius: 8, alignItems: "center", },
    allocationConfirmText: { fontSize: 14, color: "#fff", fontWeight: "600", },
    modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'center', padding: 20, },
    modalContent: { backgroundColor: '#fff', borderRadius: 12, padding: 16, },
    tabItem: { padding: 10, marginHorizontal: 5, borderBottomWidth: 2, borderBottomColor: 'transparent', },
    activeTab: { borderBottomColor: Colors.main, },
    tabText: { fontSize: 12, color: '#333', },
    activeTabText: { color: Colors.main, fontWeight: 'bold', },
    heading: { fontSize: 16, fontWeight: 'bold', color: Colors.main, marginVertical: 10, },
    row: { flexDirection: 'row', alignItems: 'center', marginVertical: 10, },
    productName: { flex: 1, fontSize: 14, color: '#000', fontWeight: '500', },
    input: { width: 80, height: 40, borderWidth: 1, borderColor: '#aaa', borderRadius: 6, paddingHorizontal: 8, marginHorizontal: 10, color: '#000' },
    mt: { fontSize: 14, fontWeight: '500', color: '#000' },
    info: { fontSize: 13, marginVertical: 4, color: '#000' },
    allocateBtn: { backgroundColor: Colors.main, padding: 12, borderRadius: 8, alignItems: 'center', marginTop: 15, },
    allocateText: { color: '#fff', fontWeight: 'bold', },
    closeBtn: { marginTop: 10, alignItems: 'center', },
    closeText: { color: 'red', },
    card: { backgroundColor: '#fff', padding: 12, margin: 10, borderRadius: 10, elevation: 3, shadowColor: '#000', shadowOpacity: 0.1, shadowRadius: 4, },
    label: { fontSize: 14, fontWeight: '600', marginBottom: 4, color: '#333', flex: 0.5, },
    value: { fontWeight: '400', color: '#555', alignItems: 'flex-start', flex: 0.5 },
    searchContainer: { paddingHorizontal: 12, paddingVertical: 8, },
    searchInput: { backgroundColor: "#F5F5F5", borderRadius: 8, paddingHorizontal: 12, height: 40, borderWidth: 1, borderColor: "#ddd", color: "#000", },
})

