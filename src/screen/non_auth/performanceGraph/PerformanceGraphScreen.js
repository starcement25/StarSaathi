import React, { useEffect, useState, useRef } from 'react'
import { TouchableOpacity, View, Text, Platform, Animated, FlatList, Modal, StyleSheet } from 'react-native'
import { BarChart } from 'react-native-gifted-charts';
import SafeView from '../../../helper/SafeView'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Colors } from '../../../assets/Colors'
import { useNavigation } from '@react-navigation/native'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage';
import ShipToSelfListPopupView from '../order/popup/ShipToSelfListPopupView';
import Loader from '../../../common/Loader';
import { getMySubDealerList } from '../../../storage/database/GetDataFromTable'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi';
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView';

// ─── Constants ────────────────────────────────────────────────────────────────

const FINANCIAL_MONTHS = [
    { code: '04', label: 'April' },
    { code: '05', label: 'May' },
    { code: '06', label: 'June' },
    { code: '07', label: 'July' },
    { code: '08', label: 'August' },
    { code: '09', label: 'September' },
    { code: '10', label: 'October' },
    { code: '11', label: 'November' },
    { code: '12', label: 'December' },
    { code: '01', label: 'January' },
    { code: '02', label: 'February' },
    { code: '03', label: 'March' },
]

const MONTH_SHORT = {
    '01': 'Jan', '02': 'Feb', '03': 'Mar',
    '04': 'Apr', '05': 'May', '06': 'Jun',
    '07': 'Jul', '08': 'Aug', '09': 'Sep',
    '10': 'Oct', '11': 'Nov', '12': 'Dec'
}

const MONTH_ORDER = ['04', '05', '06', '07', '08', '09', '10', '11', '12', '01', '02', '03']

function getCurrentFYLabel() {
    const now = new Date()
    const month = now.getMonth() + 1
    const year = now.getFullYear()
    if (month >= 4) return { label: `FY ${year}-${String(year + 1).slice(2)}`, startYear: year }
    return { label: `FY ${year - 1}-${String(year).slice(2)}`, startYear: year - 1 }
}

function getPreviousFYLabel(currentStartYear) {
    const y = currentStartYear - 1
    return { label: `FY ${y}-${String(y + 1).slice(2)}`, startYear: y }
}

// ─── Component ────────────────────────────────────────────────────────────────

const PerformanceGraphScreen = (props) => {
    const navigation = useNavigation()

    // Graph state
    const [loading, setLoading] = useState(true)
    const [allParsedData, setAllParsedData] = useState([])
    const [barData, setBardata] = useState([])
    const [maxValue, setMaxValue] = useState(100)
    const [hideGraph, setHideGraph] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)

    // Sub-dealer
    const [subDealerData, setSubDealerData] = useState([])
    const [subDealerId, setDealerSubDealerId] = useState(UrlStorage.ParameterList.BasicData.emp_code)
    const [subDealerFilterOpen, setSubDealerFilterOpen] = useState(false)

    // Filter state
    // 'full'   → showing all 12 months of current FY
    // 'single' → showing a single selected month
    const [filterMode, setFilterMode] = useState('full')
    const [selectedYear, setSelectedYear] = useState(null)
    const [selectedMonth, setSelectedMonth] = useState(null)

    // Modals
    const [yearModalVisible, setYearModalVisible] = useState(false)
    const [monthModalVisible, setMonthModalVisible] = useState(false)
    const [confirmClearModalVisible, setConfirmClearModalVisible] = useState(false)

    // Animation
    const [chartKey, setChartKey] = useState(0)
    const fadeAnim = useRef(new Animated.Value(1)).current
    const slideAnim = useRef(new Animated.Value(0)).current

    // Year options
    const currentFY = getCurrentFYLabel()
    const previousFY = getPreviousFYLabel(currentFY.startYear)
    const yearOptions = [currentFY, previousFY]

    // ─── Init ──────────────────────────────────────────────────────────────────

    useEffect(() => {
        const init = async () => {
            setLoading(true)
            if (UrlStorage.ParameterList.BasicData.user_type === 'broker' || UrlStorage.ParameterList.BasicData.user_type === 'Dealer') {
                await requestForCementSubDealer()
            }
            if (UrlStorage.ParameterList.BasicData.user_type === 'broker') { } else {
                await fetchAndParseData(UrlStorage.ParameterList.BasicData.emp_code)
            }
        }
        init()
    }, [])

    const fetchAuthData = async () => {
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
        }
    }

    useEffect(() => {
        fetchAndParseData(subDealerId?.customer_code ?? UrlStorage.ParameterList.BasicData.emp_code)
    }, [subDealerId])

    // ─── Animate chart swap ────────────────────────────────────────────────────

    const animateChartChange = (callback) => {
        Animated.parallel([
            Animated.timing(fadeAnim, { toValue: 0, duration: 150, useNativeDriver: true }),
            Animated.timing(slideAnim, { toValue: -20, duration: 150, useNativeDriver: true }),
        ]).start(() => {
            callback()
            slideAnim.setValue(20)
            Animated.parallel([
                Animated.timing(fadeAnim, { toValue: 1, duration: 200, useNativeDriver: true }),
                Animated.timing(slideAnim, { toValue: 0, duration: 200, useNativeDriver: true }),
            ]).start()
        })
    }

    // ─── API ───────────────────────────────────────────────────────────────────

    const requestForCementSubDealer = async () => {
        try {
            var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
            let url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.OrderURL1.dealer_data_list_url + `?emp_code=${UrlStorage.ParameterList.BasicData.emp_code}` + `&user_type=sub dealer` + `&login_type=${UrlStorage.ParameterList.BasicData.user_type}`

            const response = await fetch(url)
            const result = await response.json()

            if (result.process_status === 'YES' && Array.isArray(result.sub_dealer_data) && result.sub_dealer_data.length > 0) {
                setSubDealerData(result.sub_dealer_data)
            } else {
                const local = await getMySubDealerList(UrlStorage.ParameterList.BasicData.user_type, UrlStorage.ParameterList.BasicData.emp_code)
                setSubDealerData(local)
            }
        } catch (error) {
            try {
                const local = await getMySubDealerList(UrlStorage.ParameterList.BasicData.user_type, UrlStorage.ParameterList.BasicData.emp_code)
                setSubDealerData(local)
            } catch { setSubDealerData([]) }
        }
    }

    const fetchAndParseData = async (emp_code) => {
        setLoading(true) 
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            let url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DownloadDatabaseAPI.product_wise_target_achievement_TXT_download_API
            url += `?nick_name=start&user_type=${UrlStorage.ParameterList.BasicData.user_type == 'broker' ? UrlStorage.ParameterList.BasicData.selectedCustomerType : UrlStorage.ParameterList.BasicData.user_type}&emp_code=${UrlStorage.ParameterList.BasicData.user_type == 'broker' ? UrlStorage.ParameterList.BasicData.customerDetails.customer_code : UrlStorage.ParameterList.BasicData.emp_code}`

            const response = await fetch(url)
            const text = await response.text()
            const parsed = parseEmployeeData(text)

            setAllParsedData(parsed)
            applyFullCurrentYear(parsed, emp_code)
        } catch (error) {
            setHideGraph(true)
        } finally {
            setLoading(false)
        }
    }

    // ─── Parse raw TXT data ────────────────────────────────────────────────────

    function parseEmployeeData(rawResponse) {
        const lines = rawResponse.trim().split('\n').slice(2)
        const employeeMap = {}

        lines.forEach(line => {
            const parts = line.split('^')
            if (parts.length >= 8) {
                const productId = parts[0]
                const productName = parts[1]
                const empId = parts[2]
                const monthCode = parts[3].padStart(2, '0')
                const cyTarget = parseFloat(parts[4]) || 0
                const cyAchievement = parseFloat(parts[5]) || 0
                const pyTarget = parseFloat(parts[6]) || 0
                const pyAchievement = parseFloat(parts[7]) || 0

                if (!employeeMap[empId]) {
                    employeeMap[empId] = { empId, productId, productName, data: {} }
                }
                employeeMap[empId].data[monthCode] = { cyTarget, cyAchievement, pyTarget, pyAchievement }
            }
        })

        return Object.values(employeeMap).map(emp => {
            const currentYear = []
            const previousYear = []

            MONTH_ORDER.forEach(monthCode => {
                const entry = emp.data[monthCode] || { cyTarget: 0, cyAchievement: 0, pyTarget: 0, pyAchievement: 0 }
                const label = MONTH_SHORT[monthCode]

                currentYear.push(
                    { value: entry.cyTarget, label, spacing: 4, labelWidth: 45, labelTextStyle: { color: 'gray', fontSize: 10 }, frontColor: '#4A90E2' },
                    { value: entry.cyAchievement, frontColor: '#50E3C2', spacing: 15 }
                )
                previousYear.push(
                    { value: entry.pyTarget, label, spacing: 4, labelWidth: 45, labelTextStyle: { color: 'gray', fontSize: 10 }, frontColor: '#4A90E2' },
                    { value: entry.pyAchievement, frontColor: '#50E3C2', spacing: 15 }
                )
            })

            const cyMax = Math.max(...currentYear.map(d => d.value), 1)
            const pyMax = Math.max(...previousYear.map(d => d.value), 1)

            return {
                ...emp,
                currentYear,
                previousYear,
                cyMaxVal: Math.ceil((cyMax * 1.3) / 100) * 100,
                pyMaxVal: Math.ceil((pyMax * 1.3) / 100) * 100,
            }
        })
    }


    const applyFullCurrentYear = (parsed = allParsedData, emp_code = subDealerId?.customer_code ?? UrlStorage.ParameterList.BasicData.emp_code) => {
        const empData = parsed.find(e => e.empId === emp_code) ?? parsed[0]
        if (!empData) {
            setHideGraph(true)
            setBardata([])
            return
        }

        setHideGraph(false)
        animateChartChange(() => {
            setBardata(empData.currentYear)
            setMaxValue(empData.cyMaxVal > 0 ? empData.cyMaxVal : 100)
            setChartKey(k => k + 1)
            setFilterMode('full')
            setSelectedYear(null)
            setSelectedMonth(null)
        })
    }

    const applySingleMonth = (year, month) => {
        const emp_code = subDealerId?.customer_code ?? UrlStorage.ParameterList.BasicData.emp_code
        const empData = allParsedData.find(e => e.empId === emp_code) ?? allParsedData[0]
        if (!empData) { setHideGraph(true); return }

        const fyData = year.startYear === currentFY.startYear ? empData.currentYear : empData.previousYear

        const monthIndex = MONTH_ORDER.indexOf(month.code)
        if (monthIndex === -1) return

        const targetBar = {
            ...fyData[monthIndex * 2],
            label: month.label,
            labelTextStyle: { color: 'gray', fontSize: 10 },
            labelWidth: 60,
        }
        const achievementBar = { ...fyData[monthIndex * 2 + 1] }

        const maxVal = Math.ceil((Math.max(targetBar.value, achievementBar.value, 1) * 1.3) / 100) * 100

        setHideGraph(false)
        animateChartChange(() => {
            setBardata([targetBar, achievementBar])
            setMaxValue(maxVal)
            setChartKey(k => k + 1)
            setFilterMode('single')
            setSelectedYear(year)
            setSelectedMonth(month)
        })
    }

    // ─── Filter button press ───────────────────────────────────────────────────

    const handleFilterPress = () => {
        if (filterMode === 'single') {
            // Already filtered → show confirm/clear alert
            setConfirmClearModalVisible(true)
        } else {
            // Full mode → open year picker
            setYearModalVisible(true)
        }
    }

    // ─── Sub-dealer ────────────────────────────────────────────────────────────

    const selectShipToSelfItem = (item) => {
        setDealerSubDealerId(item)
        setSubDealerFilterOpen(false)
    }

    // ─── Render ────────────────────────────────────────────────────────────────

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>

                <SBSCommonHeaderView
                    title="Performance Graph"
                    backPath=" "
                    Filter={true}
                    navigation={navigation}
                    props={props}
                    handlePerformanceFilterOpen={handleFilterPress}
                />

                {filterMode === 'single' && selectedYear && selectedMonth && (
                    <View style={{ flexDirection: 'row', alignItems: 'center', paddingHorizontal: moderateScale(20), marginTop: moderateScale(10) }}>
                        <View style={{ flexDirection: 'row', alignItems: 'center', backgroundColor: '#EEF4FF', borderRadius: moderateScale(20), paddingHorizontal: moderateScale(12), paddingVertical: moderateScale(5), borderWidth: 1, borderColor: '#C0D4F5', gap: moderateScale(6) }}>
                            <Text style={{ color: '#4A90E2', fontSize: moderateScale(12), fontWeight: '600' }}>
                                {selectedYear.label}  ·  {selectedMonth.label}
                            </Text>
                            <TouchableOpacity onPress={() => applyFullCurrentYear()} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }} >
                                <Text style={{ color: '#4A90E2', fontSize: moderateScale(14), fontWeight: '700' }}>✕</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                )}

                <View style={{ width: "100%", flex: 1, paddingHorizontal: moderateScale(20), paddingBottom: moderateScale(20) }}>

                    {/* Chart container */}
                    {!hideGraph ? (
                        <View style={{ flex: 1, backgroundColor: "#FFFFFF", borderWidth: moderateScale(1), borderColor: "#DCDDDF", padding: moderateScale(15), paddingStart: 0, marginTop: moderateScale(20), borderRadius: moderateScale(10), justifyContent: 'center' }}>
                            <View style={{ width: '100%', overflow: 'hidden', justifyContent: 'center', flex: 1 }}>
                                <Animated.View style={{ opacity: fadeAnim, transform: [{ translateX: slideAnim }] }}>
                                    <BarChart
                                        key={chartKey}
                                        data={barData}
                                        barWidth={filterMode === "full" ? moderateScale(25) : moderateScale(50)}
                                        barBorderTopRightRadius={4}
                                        barBorderTopLeftRadius={4}
                                        spacing={filterMode === "full" ? moderateScale(20) : moderateScale(60)}
                                        onPress={() => navigation.navigate('PerformanceGraphDetails', { barData, subDealerId })}
                                        hideRules={false}
                                        rulesColor="lightgray"
                                        rulesThickness={1}
                                        xAxisThickness={1}
                                        yAxisThickness={1}
                                        yAxisTextStyle={{ color: 'gray', fontSize: 10 }}
                                        labelsDistanceFromXaxis={10}
                                        xAxisLabelTextStyle={{ color: 'gray', textAlign: 'center', fontSize: 10, }}
                                        height={Platform.OS === 'ios' ? moderateScale(350) : moderateScale(400)}
                                        showValuesAsTopLabel
                                        scrollAnimation
                                        topLabelTextStyle={{ color: "#000", fontSize: 9, width: 50, textAlign: 'center', marginBottom: 5 }}
                                        maxValue={maxValue}
                                        noOfSections={5}
                                        isAnimated
                                        animationDuration={800}
                                        showVerticalLines={false}
                                        showYAxisIndices={false}
                                        showXAxisIndices
                                        yAxisSide='left'
                                    />
                                </Animated.View>
                            </View>
                        </View>
                    ) : (
                        <View style={{ flex: 1, borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), justifyContent: 'center', alignItems: 'center' }}>
                            <Text style={{ color: '#000', fontSize: moderateScale(14) }}>No Records Found</Text>
                        </View>
                    )}

                    <View style={{ height: moderateScale(10) }} />

                    <View style={{ width: "100%", flexDirection: "row", alignItems: "center", gap: moderateScale(20) }}>
                        <View style={{ flexDirection: "row", alignItems: "center", gap: moderateScale(10) }}>
                            <View style={{ width: moderateScale(20), height: moderateScale(20), borderRadius: moderateScale(4), backgroundColor: "#4A90E2" }} />
                            <Text style={{ color: Colors.text, fontSize: moderateScale(12), fontWeight: "500" }}>Targets (MT)</Text>
                        </View>
                        <View style={{ flexDirection: "row", alignItems: "center", gap: moderateScale(10) }}>
                            <View style={{ width: moderateScale(20), height: moderateScale(20), borderRadius: moderateScale(4), backgroundColor: "#50E3C2" }} />
                            <Text style={{ color: Colors.text, fontSize: moderateScale(12), fontWeight: "500" }}>Achievements (MT)</Text>
                        </View>
                    </View>
                </View>

                <ShipToSelfListPopupView
                    isVisible={subDealerFilterOpen}
                    dataList={subDealerData}
                    closePopup={() => setSubDealerFilterOpen(false)}
                    selectItem={selectShipToSelfItem}
                    isDealer={false}
                />
            </View>

            {loading && <Loader />}

            <Modal visible={yearModalVisible} transparent animationType="fade" onRequestClose={() => setYearModalVisible(false)}>
                <TouchableOpacity activeOpacity={1} style={styles.overlay} onPress={() => setYearModalVisible(false)}>
                    <TouchableOpacity activeOpacity={1} style={styles.card}>
                        <Text style={styles.cardTitle}>Select Financial Year</Text>
                        <Text style={styles.cardSubtitle}>Choose a year to filter the graph</Text>

                        <View style={{ width: '100%', marginTop: moderateScale(16) }}>
                            {yearOptions.map((yr, index) => (
                                <View key={yr.startYear}>
                                    {index > 0 && <View style={styles.separator} />}
                                    <TouchableOpacity activeOpacity={0.7} style={styles.optionRow} onPress={() => {
                                        setSelectedYear(yr)
                                        setYearModalVisible(false)
                                        setTimeout(() => setMonthModalVisible(true), 300)
                                    }} >
                                        <Text style={styles.optionText}>{yr.label}</Text>
                                        <Text style={styles.chevron}>›</Text>
                                    </TouchableOpacity>
                                </View>
                            ))}
                        </View>

                        <TouchableOpacity onPress={() => setYearModalVisible(false)} style={styles.cancelBtn}>
                            <Text style={styles.cancelText}>Cancel</Text>
                        </TouchableOpacity>
                    </TouchableOpacity>
                </TouchableOpacity>
            </Modal>

            <Modal visible={monthModalVisible} transparent animationType="slide" onRequestClose={() => setMonthModalVisible(false)}>
                <TouchableOpacity activeOpacity={1} style={styles.overlay} onPress={() => setMonthModalVisible(false)}>
                    <TouchableOpacity activeOpacity={1} style={[styles.card, { maxHeight: '75%' }]}>
                        <Text style={styles.cardTitle}>Select Month</Text>
                        <Text style={styles.cardSubtitle}>{selectedYear?.label}</Text>

                        <FlatList
                            data={FINANCIAL_MONTHS}
                            keyExtractor={item => item.code}
                            style={{ width: '100%', marginTop: moderateScale(12) }}
                            showsVerticalScrollIndicator={false}
                            ItemSeparatorComponent={() => <View style={styles.separator} />}
                            renderItem={({ item }) => (
                                <TouchableOpacity activeOpacity={0.7} style={styles.optionRow} onPress={() => {
                                    setMonthModalVisible(false)
                                    setTimeout(() => applySingleMonth(selectedYear, item), 300)
                                }} >
                                    <Text style={styles.optionText}>{item.label}</Text>
                                    <Text style={styles.chevron}>›</Text>
                                </TouchableOpacity>
                            )}
                        />

                        <TouchableOpacity onPress={() => setMonthModalVisible(false)} style={styles.cancelBtn}>
                            <Text style={styles.cancelText}>Cancel</Text>
                        </TouchableOpacity>
                    </TouchableOpacity>
                </TouchableOpacity>
            </Modal>
            <Modal visible={confirmClearModalVisible} transparent animationType="fade" onRequestClose={() => setConfirmClearModalVisible(false)}>
                <TouchableOpacity activeOpacity={1} style={styles.overlay} onPress={() => setConfirmClearModalVisible(false)}>
                    <TouchableOpacity activeOpacity={1} style={styles.card}>

                        <View style={styles.iconCircle}>
                            <Text style={{ fontSize: moderateScale(26) }}>📊</Text>
                        </View>

                        <Text style={styles.cardTitle}>Change Filter?</Text>

                        <Text style={[styles.cardSubtitle, { textAlign: 'center', marginBottom: moderateScale(4) }]}>
                            Currently viewing{'\n'}
                            <Text style={{ fontWeight: '700', color: Colors.text }}>
                                {selectedYear?.label}  ·  {selectedMonth?.label}
                            </Text>
                        </Text>

                        <Text style={[styles.cardSubtitle, { textAlign: 'center', marginBottom: moderateScale(24) }]}>
                            Do you want to select a different month, or reset to full year view?
                        </Text>

                        <View style={{ flexDirection: 'row', gap: moderateScale(12), width: '100%' }}>
                            {/* Clear → reset to full current year */}
                            <TouchableOpacity activeOpacity={0.85} style={[styles.dialogBtn, { backgroundColor: '#FAFAFA', borderWidth: 1, borderColor: '#E0E0E0' }]} onPress={() => {
                                setConfirmClearModalVisible(false)
                                applyFullCurrentYear()
                            }} >
                                <Text style={[styles.dialogBtnText, { color: Colors.text }]}>Clear</Text>
                            </TouchableOpacity>

                            {/* Confirm → fresh year selection */}
                            <TouchableOpacity
                                activeOpacity={0.9}
                                style={[styles.dialogBtn, { backgroundColor: DataStorage.primaryColorCode, elevation: 6, shadowColor: '#000', shadowOpacity: 0.2, shadowRadius: 6, shadowOffset: { width: 0, height: 4 } }]}
                                onPress={() => {
                                    setConfirmClearModalVisible(false)
                                    setTimeout(() => setYearModalVisible(true), 300)
                                }} >
                                <Text style={[styles.dialogBtnText, { color: Colors.white }]}>Confirm</Text>
                            </TouchableOpacity>
                        </View>
                    </TouchableOpacity>
                </TouchableOpacity>
            </Modal>
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

// ─── Styles ───────────────────────────────────────────────────────────────────

const styles = StyleSheet.create({
    overlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', alignItems: 'center', paddingHorizontal: moderateScale(24), },
    card: { width: '100%', backgroundColor: '#FFFFFF', borderRadius: moderateScale(16), paddingVertical: moderateScale(24), paddingHorizontal: moderateScale(20), alignItems: 'center', shadowColor: '#000', shadowOpacity: 0.15, shadowRadius: 20, shadowOffset: { width: 0, height: 10 }, elevation: 12, },
    cardTitle: { fontSize: moderateScale(17), fontWeight: '700', color: '#1A1A2E', marginBottom: moderateScale(4), textAlign: 'center', },
    cardSubtitle: { fontSize: moderateScale(13), color: '#888', lineHeight: moderateScale(19), },
    separator: { height: 1, backgroundColor: '#F0F0F0', width: '100%', },
    optionRow: { width: '100%', flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingVertical: moderateScale(14), paddingHorizontal: moderateScale(4), },
    optionText: { fontSize: moderateScale(15), color: '#1A1A2E', fontWeight: '500', },
    chevron: { fontSize: moderateScale(18), color: '#BBBBBB', },
    cancelBtn: { marginTop: moderateScale(16), paddingVertical: moderateScale(10), width: '100%', alignItems: 'center', },
    cancelText: { fontSize: moderateScale(14), color: '#888', fontWeight: '500', },
    iconCircle: { width: moderateScale(54), height: moderateScale(54), borderRadius: moderateScale(27), backgroundColor: 'rgba(74,144,226,0.12)', justifyContent: 'center', alignItems: 'center', marginBottom: moderateScale(12), },
    dialogBtn: { flex: 1, paddingVertical: moderateScale(12), borderRadius: moderateScale(10), alignItems: 'center', },
    dialogBtnText: { fontSize: moderateScale(13), fontWeight: '700', },
})

export default PerformanceGraphScreen