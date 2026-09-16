import React, { useEffect, useRef, useState } from 'react'
import { FlatList, Image, Text, TextInput, TouchableOpacity, View, StyleSheet, Modal } from 'react-native'
import SafeView from '../../../helper/SafeView'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Icons } from '../../../assets/Icons'
import { Colors } from '../../../assets/Colors'
import { useNavigation } from '@react-navigation/native'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import Loader from '../../../common/Loader'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'

const ListingHistoryScreen = (props) => {
  const navigation = useNavigation()
  const [liftingStatus, setLiftingStatus] = useState("Pending")
  const [loading, setLoading] = useState(false)
  const [liftingList, setLiftingList] = useState([])
  const [monthPickerVisible, setMonthPickerVisible] = useState(false)
  const [authChecker, setAuthChecker] = useState(false)
  const [selectedYearMonth, setSelectedYearMonth] = useState(() => {
    const d = new Date()
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`
  })

  const handlePerformanceFilterOpen = () => {
    setMonthPickerVisible(true)
  }

  useEffect(() => {
    if (liftingStatus === "Pending")
      DataStorage.typeOfUse == 1 ? requestPendingHistoryForSBS(selectedYearMonth) : requestPendingHistory(selectedYearMonth)
    else if (liftingStatus === "Approved")
      DataStorage.typeOfUse == 1 ? requestApprovedHistoryForSBS(selectedYearMonth) : requestApprovedHistory(selectedYearMonth)
    else
      DataStorage.typeOfUse == 1 ? requestRejectedHistoryForSBS(selectedYearMonth) : requestRejectedHistory(selectedYearMonth)
  }, [liftingStatus, selectedYearMonth])

  const makeApiCall = async (url, apiName) => {
    setLoading(true)
    var a = await AuthCheckingApi()
    if (!a) {
      setAuthChecker(true)
      setLoading(false)
      return false
    }
    try {
      const requestOptions = { method: "GET", headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', }, timeout: 30000, }
      const response = await fetch(url, requestOptions)
      if (!response.ok)
        throw new Error(`HTTP error! status: ${response.status}`)
      const result = await response.json()
      if (result.process_status == 'YES')
        setLiftingList(result)
      else
        setLiftingList([])
    } catch (error) { } finally {
      setLoading(false)
    }
  }

  const requestPendingHistory = async () => {
    try {
      const customerCode = UrlStorage.ParameterList?.BasicData?.emp_id
      if (!customerCode)
        throw new Error('Customer code not available')
      const baseUrl = UrlStorage.BaseUrlList?.Saathi?.base_url_saathi
      const endpoint = UrlStorage.NonAuthURL?.Saathi?.LiftingURL?.pending_lifting_history_list_for_sub_dealer_url
      const completeUrl = `${baseUrl}${endpoint}?sub_dealer_cust_code=${encodeURIComponent(customerCode)}&year_month=${selectedYearMonth}`
      await makeApiCall(completeUrl, 'Pending History')
    } catch (error) {
      setLoading(false)
    }
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
                  <TouchableOpacity onPress={() => setSelectedMonth(item.value)} style={[styles.option, selectedMonth === item.value && styles.selectedOption,]} >
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
                  <TouchableOpacity onPress={() => setSelectedYear(item)} style={[styles.option, selectedYear === item && styles.selectedOption,]} >
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

  const requestApprovedHistory = async () => {
    try {
      const customerCode = UrlStorage.ParameterList?.BasicData?.emp_id
      if (!customerCode)
        throw new Error('Customer code not available')
      const baseUrl = UrlStorage.BaseUrlList?.Saathi?.base_url_saathi
      const endpoint = UrlStorage.NonAuthURL?.Saathi?.LiftingURL?.accept_lifting_history_list_for_sub_dealer_url
      const completeUrl = `${baseUrl}${endpoint}?sub_dealer_cust_code=${encodeURIComponent(customerCode)}&year_month=${selectedYearMonth}`
      await makeApiCall(completeUrl, 'Approved History')
    } catch (error) {
      setLoading(false)
    }
  }

  const requestRejectedHistory = async () => {
    try {
      const customerCode = UrlStorage.ParameterList?.BasicData?.emp_id
      if (!customerCode)
        throw new Error('Customer code not available')
      const baseUrl = UrlStorage.BaseUrlList?.Saathi?.base_url_saathi
      const endpoint = UrlStorage.NonAuthURL?.Saathi?.LiftingURL?.reject_lifting_history_list_for_sub_dealer_url
      const completeUrl = `${baseUrl}${endpoint}?sub_dealer_cust_code=${encodeURIComponent(customerCode)}&year_month=${selectedYearMonth}`
      await makeApiCall(completeUrl, 'Rejected History')
    } catch (error) {
      setLoading(false)
    }
  }

  const requestPendingHistoryForSBS = async () => {
    try {
      const customerCode = UrlStorage.ParameterList?.BasicData?.emp_code
      if (!customerCode)
        throw new Error('Customer code not available')
      const baseUrl = UrlStorage.BaseUrlList?.Saathi?.base_url_saathi
      const endpoint = UrlStorage.NonAuthURL?.Saathi?.LiftingURL?.pending_lifting_history_list_for_sub_dealer_url
      const completeUrl = `${baseUrl}${endpoint}?sub_dealer_cust_code=${encodeURIComponent(customerCode)}&year_month=${selectedYearMonth}`
      await makeApiCall(completeUrl, 'Pending History')
    } catch (error) {
      setLoading(false)
    }
  }

  const requestApprovedHistoryForSBS = async () => {
    try {
      const customerCode = UrlStorage.ParameterList?.BasicData?.emp_code
      if (!customerCode)
        throw new Error('Customer code not available')
      const baseUrl = UrlStorage.BaseUrlList?.Saathi?.base_url_saathi
      const endpoint = UrlStorage.NonAuthURL?.Saathi?.LiftingURL?.accept_lifting_history_list_for_sub_dealer_url
      const completeUrl = `${baseUrl}${endpoint}?sub_dealer_cust_code=${encodeURIComponent(customerCode)}&year_month=${selectedYearMonth}`
      await makeApiCall(completeUrl, 'Approved History')
    } catch (error) {
      setLoading(false)
    }
  }

  const requestRejectedHistoryForSBS = async () => {
    try {
      const customerCode = UrlStorage.ParameterList?.BasicData?.emp_code
      if (!customerCode)
        throw new Error('Customer code not available')
      const baseUrl = UrlStorage.BaseUrlList?.Saathi?.base_url_saathi
      const endpoint = UrlStorage.NonAuthURL?.Saathi?.LiftingURL?.reject_lifting_history_list_for_sub_dealer_url
      const completeUrl = `${baseUrl}${endpoint}?sub_dealer_cust_code=${encodeURIComponent(customerCode)}&year_month=${selectedYearMonth}`
      await makeApiCall(completeUrl, 'Rejected SBS History')
    } catch (error) {
      setLoading(false)
    }
  }

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white, flexDirection: 'column' }}>
        <SBSCommonHeaderView title="Lifting History" backPath=" " Filter={true} Add={true} navigation={navigation} props={props} handlePerformanceFilterOpen={handlePerformanceFilterOpen} />
        <View style={{ width: "100%", flex: 1, padding: moderateScale(20), flexDirection: 'column', }}>
          <View style={{ width: '100%', height: '100%' }}>
            <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "center" }}>
              <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={() => setLiftingStatus("Pending")}>
                <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: liftingStatus === "Pending" ? DataStorage.primaryColorCode : DataStorage.transColorCode, borderWidth: moderateScale(1), borderColor: "#DCDDDF" }}>
                  <Text style={{ color: liftingStatus === "Pending" ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600" }}>Pending</Text>
                </View>
              </TouchableOpacity>
              <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={() => setLiftingStatus("Approved")}>
                <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: liftingStatus === "Approved" ? DataStorage.primaryColorCode : DataStorage.transColorCode, borderWidth: moderateScale(1), borderColor: "#DCDDDF" }}>
                  <Text style={{ color: liftingStatus === "Approved" ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600" }}>Approved</Text>
                </View>
              </TouchableOpacity>
              <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={() => setLiftingStatus("Rejected")}>
                <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: liftingStatus === "Rejected" ? DataStorage.primaryColorCode : DataStorage.transColorCode, borderWidth: moderateScale(1), borderColor: "#DCDDDF" }}>
                  <Text style={{ color: liftingStatus === "Rejected" ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600" }}>Rejected</Text>
                </View>
              </TouchableOpacity>
            </View>
            <View style={{ width: "100%", flex: 1, backgroundColor: "#FFFFFF", borderWidth: moderateScale(1), borderColor: "#DCDDDF", padding: moderateScale(10), borderBottomRightRadius: moderateScale(10), borderBottomLeftRadius: moderateScale(10) }}>
              <View style={{ height: moderateScale(40), width: "100%", flexDirection: "row", alignItems: "center", gap: moderateScale(4), paddingHorizontal: moderateScale(10), borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10) }}>
                <Image source={Icons.Search} style={{ width: moderateScale(18), height: moderateScale(18), tintColor: DataStorage.primaryColorCode }} />
                <TextInput placeholder='Search Sub dealer / RSSD' placeholderTextColor={"#A7A7A7"} style={{ flex: 1, color: Colors.text, fontSize: moderateScale(14) }}></TextInput>
              </View>
              <View style={{ width: '100%', flex: 1 }}>
                <FlatList
                  data={liftingList || []}
                  keyExtractor={(item, index) => item.id?.toString() || index.toString()}
                  showsVerticalScrollIndicator={false}
                  decelerationRate="fast"
                  ListEmptyComponent={() => (
                    !loading && (
                      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', padding: moderateScale(20) }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(14) }}> No {liftingStatus.toLowerCase()} records found </Text>
                      </View>
                    )
                  )}
                  renderItem={({ item }) => (
                    <TouchableOpacity activeOpacity={0.95} style={{ paddingVertical: moderateScale(10), }} >
                      <View style={{ width: "100%", overflow: "hidden", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), backgroundColor: "#FFFFFF", elevation: 10, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: (0), y: (4) } }}>
                        <View style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(6), }}>
                          <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                            <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500" }}> {item.order_number || 'SS0626914'} </Text>
                            <View style={{ flexDirection: "row", alignItems: "center", gap: moderateScale(10) }}>
                              <View style={{ paddingVertical: moderateScale(3), paddingHorizontal: moderateScale(10), borderRadius: moderateScale(10), backgroundColor: "#C4B54433" }}>
                                <Text style={{ color: "#BFAF30", fontSize: moderateScale(12) }}> {liftingStatus} </Text>
                              </View>
                            </View>
                          </View>
                          <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {item.dealer_name || 'Sati Trading co Dikom'} </Text>
                          <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {item.product_details || 'AAC Block 5000 Pcs.'} </Text>
                          <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {item.phone_number || '3000646365'} </Text>
                          <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}> {item.created_date || '9th April 2025 06:34 AM'} </Text>
                        </View>
                      </View>
                    </TouchableOpacity>
                  )}
                />
              </View>
            </View>
          </View>
          {UrlStorage.ParameterList.BasicData.user_type == "broker" || UrlStorage.ParameterList.BasicData.user_type.toLowerCase() == "dealer" ? null : <TouchableOpacity onPress={() => props.navigation.navigate("AddNewLiftingScreen")} style={{ width: moderateScale(50), height: moderateScale(50), alignItems: 'center', justifyContent: 'center', backgroundColor: Colors.main, alignSelf: 'flex-end', zIndex: 1000, borderRadius: moderateScale(100), bottom: moderateScale(60), right: moderateScale(10) }}>
            <Image source={Icons.Add} style={{ height: moderateScale(20), width: moderateScale(20) }} />
          </TouchableOpacity>}
        </View>
      </View>
      <MonthYearPicker visible={monthPickerVisible} currentSelectedDate={selectedYearMonth} onClose={() => setMonthPickerVisible(false)} onConfirm={(date) => {
        setMonthPickerVisible(false)
        setSelectedYearMonth(date)
      }} />
      {loading && <Loader />}
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  )
}

export default ListingHistoryScreen

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
})