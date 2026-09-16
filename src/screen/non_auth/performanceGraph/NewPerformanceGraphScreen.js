import React, { useEffect, useState, useRef } from 'react'
import { TouchableOpacity, View, Text, Platform, Animated, FlatList, Modal, StyleSheet, Image, ScrollView, Dimensions } from 'react-native'
import { BarChart } from 'react-native-gifted-charts'
import SafeView from '../../../helper/SafeView'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Colors } from '../../../assets/Colors'
import { useNavigation } from '@react-navigation/native'
import DataStorage from '../../../storage/DataStorage'
import LinearGradient from 'react-native-linear-gradient'
import UrlStorage from '../../../storage/UrlStorage'
import ShipToSelfListPopupView from '../order/popup/ShipToSelfListPopupView'
import Loader from '../../../common/Loader'
import { getMySubDealerList } from '../../../storage/database/GetDataFromTable'
import { Icons } from '../../../assets/Icons'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const GRAPH_BAR_COLOR_CURRENT_FY_TARGET = '#4A90E2'
const GRAPH_BAR_COLOR_CURRENT_FY_ACHIEVEMENTS = '#50E3C2'
const GRAPH_BAR_COLOR_PREVIOUS_FY_TARGET = '#0076fe'
const GRAPH_BAR_COLOR_PREVIOUS_FY_ACHIEVEMENTS = '#00fa85'
const GRAPH_BAR_COLOR_COMPARE_PREVIOUS_FY_ACHIEVEMENTS = '#FF7700'
const GRAPH_BAR_COLOR_COMPARE_CURRENT_FY_ACHIEVEMENTS = '#50E3C2'

const MORE_THAN_100_PERCENTAGE = ['#00D492', '#009966']
const LESS_THAN_100_PERCENTAGE = ['#E8BE76', '#FF7700']
const EQUAL_0_PERCENTAGE = ['#FF6467', '#E7000B']

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

const MONTH_NAME = {
    '01': 'January', '02': 'February', '03': 'March',
    '04': 'April', '05': 'May', '06': 'June',
    '07': 'July', '08': 'August', '09': 'September',
    '10': 'October', '11': 'November', '12': 'December'
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

const NewPerformanceGraphScreen = (props) => {
    const navigation = useNavigation()
    const [loading, setLoading] = useState(true)
    const [allParsedData, setAllParsedData] = useState([])
    const [barData, setBardata] = useState([])
    const [maxValue, setMaxValue] = useState(100)
    const [hideGraph, setHideGraph] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)
    const [subDealerData, setSubDealerData] = useState([])
    const [subDealerId, setDealerSubDealerId] = useState(UrlStorage.ParameterList.BasicData.emp_code)
    const [subDealerFilterOpen, setSubDealerFilterOpen] = useState(false)
    const [filterMode, setFilterMode] = useState('full')
    const [selectedYear, setSelectedYear] = useState(null)
    const [selectedMonth, setSelectedMonth] = useState(null)
    const [yearModalVisible, setYearModalVisible] = useState(false)
    const [monthModalVisible, setMonthModalVisible] = useState(false)
    const [confirmClearModalVisible, setConfirmClearModalVisible] = useState(false)
    const [chartKey, setChartKey] = useState(0)
    const fadeAnim = useRef(new Animated.Value(1)).current
    const slideAnim = useRef(new Animated.Value(0)).current
    const currentFY = getCurrentFYLabel()
    const previousFY = getPreviousFYLabel(currentFY.startYear)
    const yearOptions = [currentFY, previousFY]
    const [currentYearTotalTarget, setCurrentYearTotalTarget] = useState(0)
    const [currentYearTotalAchievement, setCurrentYearTotalAchievement] = useState(0)
    const [currentYearPercentageOfPerformance, setCurrentYearPercentageOfPerformance] = useState(0)
    const [currentYearDifferentOfTotalAndAchievement, setCurrentYearDifferentOfTotalAndAchievement] = useState(0)
    const [currentYearDataSet, setCurrentYearDataSet] = useState([])
    const [previousYearTotalTarget, setPreviousYearTotalTarget] = useState(0)
    const [previousYearTotalAchievement, setPreviousYearTotalAchievement] = useState(0)
    const [previousYearPercentageOfPerformance, setPreviousYearPercentageOfPerformance] = useState(0)
    const [previousYearDifferentOfTotalAndAchievement, setPreviousYearDifferentOfTotalAndAchievement] = useState(0)
    const [previousYearDataSet, setPreviousYearDataSet] = useState([])
    const [compareDataSet, setCompareDataSet] = useState([])
    const [totalTarget, setTotalTarget] = useState(0)
    const [achievement, setAchievement] = useState(0)
    const [avgPerformance, setAvgPerformance] = useState(0)
    const [different, setDifferent] = useState(0)
    const [dataSet, setDataSet] = useState([])
    const [yearModal, setYearModal] = useState(false)
    const [title, setTitle] = useState('Current FY')

    useEffect(() => {
        const init = async () => {
            setLoading(true)
            if (UrlStorage.ParameterList.BasicData.user_type === 'broker' || UrlStorage.ParameterList.BasicData.user_type === 'Dealer')
                await requestForCementSubDealer()
            if (UrlStorage.ParameterList.BasicData.user_type === 'broker') { } else {
                if (DataStorage.typeOfUse == 1)
                    await fetchAndParseDataSBS(UrlStorage.ParameterList.BasicData.emp_code)
                else
                    await fetchAndParseData(UrlStorage.ParameterList.BasicData.emp_code)
            }
        }
        init()
    }, [])

    useEffect(() => {
        if (DataStorage.typeOfUse == 1)
            fetchAndParseDataSBS(subDealerId?.customer_code ?? UrlStorage.ParameterList.BasicData.emp_code)
        else
            fetchAndParseData(UrlStorage.ParameterList.BasicData.emp_code)
    }, [subDealerId])

    function getValidMonthCodes(startYear) {
        const now = new Date()
        const currentMonth = String(now.getMonth() + 1).padStart(2, '0')
        const currentYear = now.getFullYear()
        if (startYear < currentFY.startYear) return MONTH_ORDER
        return MONTH_ORDER.filter(code => {
            const monthYear = (parseInt(code) >= 4) ? startYear : startYear + 1
            if (monthYear < currentYear) return true
            if (monthYear > currentYear) return false
            return parseInt(code) <= parseInt(currentMonth)
        })
    }

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

    const requestForCementSubDealer = async () => {
        try {
            var a = await AuthCheckingApi()
            if (!a) {
                setAuthChecker(true)
                setLoading(false)
                return false
            }
            let url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.OrderURL1.dealer_data_list_url + `?emp_code=${UrlStorage.ParameterList.BasicData.emp_code}` + `&user_type=sub dealer` + `&login_type=${UrlStorage.ParameterList.BasicData.user_type}`
            const response = await fetch(url)
            const result = await response.json()
            if (result.process_status === 'YES' && Array.isArray(result.sub_dealer_data) && result.sub_dealer_data.length > 0)
                setSubDealerData(result.sub_dealer_data)
            else {
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
        var a = await AuthCheckingApi()
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

    const fetchAndParseDataSBS = async (emp_code) => {
        setLoading(true)
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            let url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.BaseUrlList.SBS.sbs_performance
            url += `?dealer_id=${emp_code}`
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

    function parseEmployeeData(rawResponse) {
        const lines = rawResponse.trim().split('\n').slice(2)
        const employeeMap = {}
        var currentYearTotalTarget = 0, currentYearTotalAchievement = 0, currentYearPercentageOfPerformance = 0, currentYearDifferentOfTotalAndAchievement = 0
        var cyArray = []
        var previousYearTotalTarget = 0, previousYearTotalAchievement = 0, previousYearPercentageOfPerformance = 0, previousYearDifferentOfTotalAndAchievement = 0
        var pyArray = []
        var compareArray = []
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
                const cyPercentage = (cyAchievement / cyTarget) * 100
                var cyDifferent = 0
                if (cyTarget > cyAchievement)
                    cyDifferent = cyTarget - cyAchievement
                else
                    cyDifferent = cyAchievement - cyTarget
                var obj = {
                    id: monthCode,
                    achievement: cyAchievement,
                    target: cyTarget,
                    percentage: cyPercentage,
                    different: cyDifferent,
                    isGrowth: cyPercentage >= 100
                }
                cyArray.push(obj)
                currentYearTotalTarget = currentYearTotalTarget + cyTarget
                currentYearTotalAchievement = currentYearTotalAchievement + cyAchievement
                const pyPercentage = (pyAchievement / pyTarget) * 100
                var pyDifferent = 0
                if (pyTarget > pyAchievement)
                    pyDifferent = pyTarget - pyAchievement
                else
                    pyDifferent = pyAchievement - pyTarget
                var pyObj = {
                    id: monthCode,
                    achievement: pyAchievement,
                    target: pyTarget,
                    percentage: pyPercentage,
                    different: pyDifferent,
                    isGrowth: pyPercentage >= 100
                }
                pyArray.push(pyObj)
                previousYearTotalTarget = previousYearTotalTarget + pyTarget
                previousYearTotalAchievement = previousYearTotalAchievement + pyAchievement
                const comparePercentage = (cyAchievement / pyAchievement) * 100
                var compareDifferent = 0
                if (cyAchievement > pyAchievement)
                    compareDifferent = cyAchievement - pyAchievement
                else
                    compareDifferent = pyAchievement - cyAchievement
                var compareObj = {
                    id: monthCode,
                    achievement: cyAchievement,
                    target: pyAchievement,
                    percentage: comparePercentage,
                    different: compareDifferent,
                    isGrowth: comparePercentage >= 100
                }
                compareArray.push(compareObj)
                if (!employeeMap[empId]) {
                    employeeMap[empId] = { empId, productId, productName, data: {} }
                }
                employeeMap[empId].data[monthCode] = { cyTarget, cyAchievement, pyTarget, pyAchievement }
            }
        })
        currentYearPercentageOfPerformance = (currentYearTotalAchievement / currentYearTotalTarget) * 100
        if (currentYearTotalTarget > currentYearTotalAchievement)
            currentYearDifferentOfTotalAndAchievement = currentYearTotalTarget - currentYearTotalAchievement
        else
            currentYearDifferentOfTotalAndAchievement = currentYearTotalAchievement - currentYearTotalTarget
        setCurrentYearTotalTarget(currentYearTotalTarget)
        setCurrentYearTotalAchievement(currentYearTotalAchievement)
        setCurrentYearPercentageOfPerformance(currentYearPercentageOfPerformance)
        setCurrentYearDifferentOfTotalAndAchievement(currentYearDifferentOfTotalAndAchievement)
        const validCYMonths = getValidMonthCodes(currentFY.startYear)
        const filteredCYArray = cyArray.filter(item => validCYMonths.includes(item.id))
        const sortedCYArray = [...filteredCYArray].sort((a, b) =>
            MONTH_ORDER.indexOf(a.id) - MONTH_ORDER.indexOf(b.id)
        )
        setCurrentYearDataSet(sortedCYArray)
        setTotalTarget(currentYearTotalTarget)
        setAchievement(currentYearTotalAchievement)
        setAvgPerformance(currentYearPercentageOfPerformance)
        setDifferent(currentYearDifferentOfTotalAndAchievement)
        setDataSet(sortedCYArray)
        previousYearPercentageOfPerformance = (previousYearTotalAchievement / previousYearTotalTarget) * 100
        if (previousYearTotalTarget > previousYearTotalAchievement)
            previousYearDifferentOfTotalAndAchievement = previousYearTotalTarget - previousYearTotalAchievement
        else
            previousYearDifferentOfTotalAndAchievement = previousYearTotalAchievement - previousYearTotalTarget
        setPreviousYearTotalTarget(previousYearTotalTarget)
        setPreviousYearTotalAchievement(previousYearTotalAchievement)
        setPreviousYearPercentageOfPerformance(previousYearPercentageOfPerformance)
        setPreviousYearDifferentOfTotalAndAchievement(previousYearDifferentOfTotalAndAchievement)
        setPreviousYearDataSet([...pyArray].sort((a, b) => {
            return MONTH_ORDER.indexOf(a.id) - MONTH_ORDER.indexOf(b.id)
        }))
        setCompareDataSet([...compareArray].sort((a, b) => {
            return MONTH_ORDER.indexOf(a.id) - MONTH_ORDER.indexOf(b.id)
        }))

        return Object.values(employeeMap).map(emp => {
            const currentYear = []
            const previousYear = []
            const compareYear = []
            MONTH_ORDER.forEach(monthCode => {
                const entry = emp.data[monthCode] || { cyTarget: 0, cyAchievement: 0, pyTarget: 0, pyAchievement: 0 }
                const label = MONTH_SHORT[monthCode]
                currentYear.push(
                    { value: entry.cyTarget, label, spacing: 4, labelWidth: 45, labelTextStyle: { color: 'gray', fontSize: 10 }, frontColor: GRAPH_BAR_COLOR_CURRENT_FY_TARGET },
                    { value: entry.cyAchievement, frontColor: GRAPH_BAR_COLOR_CURRENT_FY_ACHIEVEMENTS, spacing: 15 }
                )
                previousYear.push(
                    { value: entry.pyTarget, label, spacing: 4, labelWidth: 45, labelTextStyle: { color: 'gray', fontSize: 10 }, frontColor: GRAPH_BAR_COLOR_PREVIOUS_FY_TARGET },
                    { value: entry.pyAchievement, frontColor: GRAPH_BAR_COLOR_PREVIOUS_FY_ACHIEVEMENTS, spacing: 15 }
                )
                compareYear.push(
                    { value: entry.pyAchievement, label, spacing: 4, labelWidth: 45, labelTextStyle: { color: 'gray', fontSize: 10 }, frontColor: GRAPH_BAR_COLOR_COMPARE_PREVIOUS_FY_ACHIEVEMENTS },
                    { value: entry.cyAchievement, frontColor: GRAPH_BAR_COLOR_COMPARE_CURRENT_FY_ACHIEVEMENTS, spacing: 15 }
                )
            })
            const cyMax = Math.max(...currentYear.map(d => d.value), 1)
            const pyMax = Math.max(...previousYear.map(d => d.value), 1)
            const compMax = Math.max(...compareYear.map(d => d.value), 1)
            return {
                ...emp,
                currentYear,
                previousYear,
                compareYear,
                cyMaxVal: Math.ceil((cyMax * 1.3) / 100) * 100,
                pyMaxVal: Math.ceil((pyMax * 1.3) / 100) * 100,
                compMaxVal: Math.ceil((compMax * 1.3) / 100) * 100,
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
        if (!empData) {
            setHideGraph(true)
            return
        }
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

    const handleFilterPress = () => {
        if (filterMode === 'single')
            setConfirmClearModalVisible(true)
        else
            setYearModalVisible(true)
    }

    const selectShipToSelfItem = (item) => {
        setDealerSubDealerId(item)
        setSubDealerFilterOpen(false)
    }

    const changeFYDataSet = (item) => {
        if (item.id == 1) {
            setTotalTarget(currentYearTotalTarget)
            setAchievement(currentYearTotalAchievement)
            setAvgPerformance(currentYearPercentageOfPerformance)
            setDifferent(currentYearDifferentOfTotalAndAchievement)
            setDataSet(currentYearDataSet)
            const emp_code = subDealerId?.customer_code ?? UrlStorage.ParameterList.BasicData.emp_code
            const empData = allParsedData.find(e => e.empId === emp_code) ?? allParsedData[0]
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
        } else if (item.id == 2) {
            setTotalTarget(previousYearTotalTarget)
            setAchievement(previousYearTotalAchievement)
            setAvgPerformance(previousYearPercentageOfPerformance)
            setDifferent(previousYearDifferentOfTotalAndAchievement)
            setDataSet(previousYearDataSet)
            const emp_code = subDealerId?.customer_code ?? UrlStorage.ParameterList.BasicData.emp_code
            const empData = allParsedData.find(e => e.empId === emp_code) ?? allParsedData[0]
            if (!empData) {
                setHideGraph(true)
                setBardata([])
                return
            }
            setHideGraph(false)
            animateChartChange(() => {
                setBardata(empData.previousYear)
                setMaxValue(empData.pyMaxVal > 0 ? empData.pyMaxVal : 100)
                setChartKey(k => k + 1)
                setFilterMode('full')
                setSelectedYear(null)
                setSelectedMonth(null)
            })
        } else if (item.id == 3) {
            setDataSet(compareDataSet)
            const emp_code = subDealerId?.customer_code ?? UrlStorage.ParameterList.BasicData.emp_code
            const empData = allParsedData.find(e => e.empId === emp_code) ?? allParsedData[0]
            if (!empData) {
                setHideGraph(true)
                setBardata([])
                return
            }
            setHideGraph(false)
            animateChartChange(() => {
                setBardata(empData.compareYear)
                setMaxValue(empData.compMaxVal > 0 ? empData.compMaxVal : 100)
                setChartKey(k => k + 1)
                setFilterMode('full')
                setSelectedYear(null)
                setSelectedMonth(null)
            })
        }
        setTitle(item.title)
        setYearModal(false)
    }

    const colorSet = (percentage) => {
        if (parseInt(percentage) >= 100)
            return MORE_THAN_100_PERCENTAGE
        else if (parseInt(percentage) == 0)
            return EQUAL_0_PERCENTAGE
        else
            return LESS_THAN_100_PERCENTAGE
    }

    const colorForBar = (title) => {
        switch (title) {
            case 'Current FY':
                return [GRAPH_BAR_COLOR_CURRENT_FY_TARGET, GRAPH_BAR_COLOR_CURRENT_FY_ACHIEVEMENTS]
            case 'Previous FY':
                return [GRAPH_BAR_COLOR_PREVIOUS_FY_TARGET, GRAPH_BAR_COLOR_PREVIOUS_FY_ACHIEVEMENTS]
            case 'Current FY vs Previous FY':
                return [GRAPH_BAR_COLOR_COMPARE_PREVIOUS_FY_ACHIEVEMENTS, GRAPH_BAR_COLOR_COMPARE_CURRENT_FY_ACHIEVEMENTS]
        }
    }

    const renderItem = ({ item, index }) => (
        <View style={{ width: '100%', flexDirection: 'row', paddingHorizontal: moderateScale(15), paddingVertical: moderateScale(10), borderTopWidth: moderateScale(.5), borderBottomWidth: moderateScale(.5), borderColor: '#E2E8F0' }}>
            <Image source={item.isGrowth ? require('../../../assets/new_icon/Container_3.png') : require('../../../assets/new_icon/Container_4.png')} style={{ height: moderateScale(40), width: moderateScale(40) }} />
            <View style={{ width: moderateScale(10) }} />
            <View style={{ flex: 1, flexDirection: 'column' }}>
                <View style={{ width: '100%', flexDirection: 'row' }}>
                    <View style={{ flex: 1, flexDirection: 'column' }}>
                        <Text style={{ color: '#000000', fontWeight: '800', fontSize: moderateScale(20) }}>{MONTH_NAME[item.id]}</Text>
                        <View style={{ height: moderateScale(5) }} />
                        <Text style={{ color: '#000000', fontSize: moderateScale(12) }}>{title == 'Current FY vs Previous FY' ? 'LY' : 'Target'} - {parseFloat(item.target).toFixed(2)} MT</Text>
                        <Text style={{ color: '#000000', fontSize: moderateScale(12) }}>{title == 'Current FY vs Previous FY' ? 'TY' : 'Ach'} - {parseFloat(item.achievement).toFixed(2)} MT</Text>
                    </View>
                    <View style={{ paddingHorizontal: moderateScale(10), height: moderateScale(35), paddingVertical: moderateScale(3), flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: item.isGrowth ? '#A4F4CF' : '#FFC9C9', backgroundColor: item.isGrowth ? '#ECFDF5' : '#FEF2F2', borderRadius: moderateScale(5) }}>
                        <Image source={item.isGrowth ? require('../../../assets/new_icon/up.png') : require('../../../assets/new_icon/down.png')} style={{ width: moderateScale(20), height: moderateScale(20) }} />
                        <View style={{ width: moderateScale(10) }} />
                        <Text style={{ color: item.isGrowth ? '#007A55' : '#C10007', fontWeight: '800', fontSize: moderateScale(18) }}>{(isNaN(parseFloat(item.percentage)) || !isFinite(parseFloat(item.percentage))) ? "---" : `${parseFloat(item.percentage).toFixed(2)} `} %</Text>
                    </View>
                </View>
                <View style={{ height: moderateScale(10) }} />
                <View style={{ width: '100%', height: 10, borderRadius: moderateScale(10), backgroundColor: '#eee' }}>
                    <LinearGradient
                        colors={colorSet(item.percentage)}
                        style={{ height: 10, width: parseInt(item.percentage) < 100 ? '' + parseInt(item.percentage) + '%' + '' : '100%', borderRadius: moderateScale(10) }}
                        start={{ x: 0, y: 0 }}
                        end={{ x: 1, y: 1 }} />
                </View>
                <View style={{ height: moderateScale(7) }} />
                <View style={{ width: '100%', flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
                    <View style={{ flex: 1, flexDirection: 'column' }}>
                        <Text style={{ color: '#62748E', fontSize: moderateScale(12) }}>Difference</Text>
                    </View>
                    <View style={{ paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(3), flexDirection: 'row', alignItems: 'center', backgroundColor: item.isGrowth ? '#ECFDF5' : '#FEF2F2', borderRadius: moderateScale(5) }}>
                        <Text style={{ color: item.isGrowth ? '#007A55' : '#C10007', fontWeight: '800', fontSize: moderateScale(18) }}>{parseInt(item.percentage) < 100 ? '-' : '+'}{parseInt(item.different)} MT</Text>
                        <View style={{ width: moderateScale(10) }} />
                        <Text style={{ color: item.isGrowth ? '#009966' : '#E7000B', fontSize: moderateScale(12) }}>{parseInt(item.percentage) < 100 ? 'Shortfall' : 'Surplus'}</Text>
                    </View>
                </View>
            </View>
            <View style={{ width: moderateScale(10) }} />
        </View>
    )

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={DataStorage.typeOfUse == 1 ? '#8FC227' : Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Month-wise Performance" backPath=" " Filter={false} navigation={navigation} props={props} handlePerformanceFilterOpen={handleFilterPress} />
                <View style={{ width: '100%', height: moderateScale(55), backgroundColor: '#E41B14', alignItems: 'center', justifyContent: 'center', paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(5) }}>
                    <TouchableOpacity onPress={() => setYearModal(true)} style={{ width: '100%', height: '100%', borderRadius: moderateScale(10), borderWidth: 1, borderColor: '#FFFFFF50', backgroundColor: '#FFFFFF30', alignItems: 'center', justifyContent: 'center', flexDirection: 'row', paddingHorizontal: moderateScale(20) }}>
                        <Text style={{ color: '#FFFFFF', fontSize: moderateScale(16), fontWeight: '500' }}>{title}</Text>
                        <View style={{ flex: 1 }} />
                        <Image source={Icons.DownArrow} tintColor={'#FFFFFF'} style={{ width: moderateScale(15), height: moderateScale(15) }} />
                    </TouchableOpacity>
                </View>
                <View style={{ width: '100%', height: moderateScale(10), backgroundColor: '#E41B14', alignItems: 'center', justifyContent: 'center', paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(5) }} />
                <ScrollView>
                    <View style={{ width: '100%', flexDirection: 'column', paddingHorizontal: moderateScale(15) }}>
                        <View style={{ height: moderateScale(15) }} />
                        {title == 'Current FY vs Previous FY' ? null : <>
                            <View style={{ width: '100%', backgroundColor: '#FFF', elevation: 2, borderRadius: moderateScale(12), flexDirection: 'column', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                                <View style={{ height: moderateScale(10) }} />
                                <View style={{ paddingHorizontal: moderateScale(15) }}>
                                    <Image source={require('../../../assets/new_icon/Container.png')} style={{ width: moderateScale(40), height: moderateScale(40) }} />
                                </View>
                                <View style={{ height: moderateScale(5) }} />
                                <View style={{ paddingHorizontal: moderateScale(15) }}>
                                    <Text style={{ color: '#62748E', textTransform: 'uppercase', fontWeight: '500', fontSize: moderateScale(12) }}>Total Target (MT)</Text>
                                </View>
                                <View style={{ height: moderateScale(5) }} />
                                <View style={{ paddingHorizontal: moderateScale(15) }}>
                                    <Text style={{ color: '#155DFC', textTransform: 'uppercase', fontWeight: '900', fontSize: Platform.OS == 'ios' ? moderateScale(26) : moderateScale(30) }}>{parseFloat(totalTarget).toFixed(2)}</Text>
                                </View>
                                <View style={{ height: moderateScale(20) }} />
                                <View style={{ height: moderateScale(10), borderBottomLeftRadius: moderateScale(20), borderBottomRightRadius: moderateScale(20), width: '100%', backgroundColor: '#155DFC' }} />
                            </View>
                            <View style={{ height: moderateScale(15) }} />
                            <View style={{ width: '100%', flexDirection: 'row' }}>
                                <View style={{ flex: 1 }}>
                                    <View style={{ width: '100%', backgroundColor: '#FFF', elevation: 2, borderRadius: moderateScale(12), flexDirection: 'column', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                                        <View style={{ height: moderateScale(10) }} />
                                        <View style={{ paddingHorizontal: moderateScale(15) }}>
                                            <Image source={require('../../../assets/new_icon/Container_1.png')} style={{ width: moderateScale(40), height: moderateScale(40) }} />
                                        </View>
                                        <View style={{ height: moderateScale(5) }} />
                                        <View style={{ paddingHorizontal: moderateScale(15) }}>
                                            <Text style={{ color: '#62748E', textTransform: 'uppercase', fontWeight: '500', fontSize: moderateScale(12) }}>Achievement (MT)</Text>
                                        </View>
                                        <View style={{ height: moderateScale(5) }} />
                                        <View style={{ paddingHorizontal: moderateScale(15) }}>
                                            <Text style={{ color: '#009966', textTransform: 'uppercase', fontWeight: '900', fontSize: Platform.OS == 'ios' ? moderateScale(26) : moderateScale(30) }}>{parseFloat(achievement).toFixed(2)}</Text>
                                        </View>
                                        <View style={{ height: moderateScale(5) }} />
                                        <View style={{ paddingHorizontal: moderateScale(15) }}>
                                            <Text style={{ color: '#009966', fontWeight: '500', fontSize: moderateScale(16) }}>{(isNaN(parseFloat(avgPerformance)) || !isFinite(parseFloat(avgPerformance))) ? "---" : `${parseFloat(avgPerformance).toFixed(2)} `} % of Target</Text>
                                        </View>
                                        <View style={{ height: moderateScale(20) }} />
                                        <View style={{ height: moderateScale(10), borderBottomLeftRadius: moderateScale(20), borderBottomRightRadius: moderateScale(20), width: '100%', backgroundColor: '#009966' }} />
                                    </View>
                                </View>
                                <View style={{ width: moderateScale(10) }} />
                                <View style={{ flex: 1 }}>
                                    <View style={{ width: '100%', backgroundColor: '#FFF', elevation: 2, borderRadius: moderateScale(12), flexDirection: 'column', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                                        <View style={{ height: moderateScale(10) }} />
                                        <View style={{ paddingHorizontal: moderateScale(15) }}>
                                            <Image source={require('../../../assets/new_icon/Container_2.png')} style={{ width: moderateScale(40), height: moderateScale(40) }} />
                                        </View>
                                        <View style={{ height: moderateScale(5) }} />
                                        <View style={{ paddingHorizontal: moderateScale(15) }}>
                                            <Text style={{ color: '#62748E', textTransform: 'uppercase', fontWeight: '500', fontSize: moderateScale(12) }}>Avg. Performance</Text>
                                        </View>
                                        <View style={{ height: moderateScale(5) }} />
                                        <View style={{ paddingHorizontal: moderateScale(15) }}>
                                            <Text style={{ color: '#F54900', textTransform: 'uppercase', fontWeight: '900', fontSize: Platform.OS == 'ios' ? moderateScale(26) : moderateScale(30) }}>{(isNaN(parseFloat(avgPerformance)) || !isFinite(parseFloat(avgPerformance))) ? "---" : `${parseFloat(avgPerformance).toFixed(2)} `} %</Text>
                                        </View>
                                        <View style={{ height: moderateScale(5) }} />
                                        <View style={{ paddingHorizontal: moderateScale(15) }}>
                                            <Text style={{ color: '#E7000B', fontWeight: '500', fontSize: moderateScale(16) }}>{parseFloat(different).toFixed(0)} MT {parseInt(avgPerformance) < 100 ? 'Shortfall' : 'Surplus'}</Text>
                                        </View>
                                        <View style={{ height: moderateScale(20) }} />
                                        <View style={{ height: moderateScale(10), borderBottomLeftRadius: moderateScale(20), borderBottomRightRadius: moderateScale(20), width: '100%', backgroundColor: '#F54900' }} />
                                    </View>
                                </View>
                            </View>
                            <View style={{ height: moderateScale(15) }} />
                        </>}
                        <View style={{ width: '100%', backgroundColor: '#FFF', elevation: 2, borderRadius: moderateScale(12), flexDirection: 'column', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                            <Text style={{ color: '#000000', fontSize: moderateScale(24), fontWeight: '600', paddingHorizontal: moderateScale(20), paddingTop: moderateScale(20) }}>Performance Graph</Text>
                            <View style={{ width: '100%', flexDirection: 'row', alignItems: 'center', paddingHorizontal: moderateScale(20), paddingTop: moderateScale(10) }}>
                                <View style={{ width: moderateScale(12), height: moderateScale(12), borderRadius: moderateScale(12), backgroundColor: colorForBar(title)[0] }} />
                                <Text style={{ color: '#000000', fontSize: moderateScale(14), paddingLeft: moderateScale(5) }}>{title == 'Current FY vs Previous FY' ? 'Last Year\nAchievements (MT)' : 'Targets (MT)'}</Text>
                                <View style={{ width: moderateScale(20) }} />
                                <View style={{ width: moderateScale(12), height: moderateScale(12), borderRadius: moderateScale(12), backgroundColor: colorForBar(title)[1] }} />
                                <Text style={{ color: '#000000', fontSize: moderateScale(14), paddingLeft: moderateScale(5) }}>{title == 'Current FY vs Previous FY' ? 'Current Year\nAchievements (MT)' : 'Achievements (MT)'}</Text>
                            </View>
                            <View style={{ height: moderateScale(20) }} />
                            <BarChart
                                key={chartKey}
                                data={barData}
                                barWidth={filterMode === "full" ? moderateScale(25) : moderateScale(50)}
                                barBorderTopRightRadius={4}
                                barBorderTopLeftRadius={4}
                                spacing={filterMode === "full" ? moderateScale(20) : moderateScale(60)}
                                onPress={() => { }}
                                hideRules={false}
                                rulesColor="lightgray"
                                rulesThickness={1}
                                xAxisThickness={.1}
                                yAxisThickness={.1}
                                yAxisTextStyle={{ color: 'gray', fontSize: 10 }}
                                labelsDistanceFromXaxis={10}
                                xAxisLabelTextStyle={{ color: 'gray', textAlign: 'center', fontSize: 10, }}
                                height={Platform.OS === 'ios' ? moderateScale(350) : moderateScale(350)}
                                width={Dimensions.get('screen').width - 100}
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
                            <View style={{ height: moderateScale(20) }} />
                        </View>
                        <View style={{ height: moderateScale(15) }} />
                        <View style={{ width: '100%', backgroundColor: '#FFF', elevation: 2, borderRadius: moderateScale(12), flexDirection: 'column', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                            <View style={{ width: '100%', borderTopLeftRadius: moderateScale(12), borderTopRightRadius: moderateScale(12), backgroundColor: '#F8FAFC', padding: moderateScale(20) }}>
                                <Text style={{ color: '#000', fontWeight: '800', fontSize: moderateScale(24) }}>Monthly Breakup</Text>
                                <Text style={{ color: '#45556C', fontSize: moderateScale(14) }}>Detailed performance analysis</Text>
                            </View>
                            <View style={{ width: '100%', height: 1, backgroundColor: '#FEF2F2' }} />
                            <View style={{ width: '100%' }}>
                                <FlatList
                                    data={dataSet}
                                    renderItem={renderItem}
                                    keyExtractor={(_, index) => index.toString()}
                                />
                            </View>
                        </View>
                        <View style={{ height: moderateScale(15) }} />
                    </View>
                </ScrollView>
                <ShipToSelfListPopupView isVisible={subDealerFilterOpen} dataList={subDealerData} closePopup={() => setSubDealerFilterOpen(false)} selectItem={selectShipToSelfItem} isDealer={false} />
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
                        <Text style={[styles.cardSubtitle, { textAlign: 'center', marginBottom: moderateScale(24) }]}> Do you want to select a different month, or reset to full year view? </Text>
                        <View style={{ flexDirection: 'row', gap: moderateScale(12), width: '100%' }}>
                            <TouchableOpacity activeOpacity={0.85} style={[styles.dialogBtn, { backgroundColor: '#FAFAFA', borderWidth: 1, borderColor: '#E0E0E0' }]} onPress={() => {
                                setConfirmClearModalVisible(false)
                                applyFullCurrentYear()
                            }} >
                                <Text style={[styles.dialogBtnText, { color: Colors.text }]}>Clear</Text>
                            </TouchableOpacity>
                            <TouchableOpacity activeOpacity={0.9} style={[styles.dialogBtn, { backgroundColor: DataStorage.primaryColorCode, elevation: 6, shadowColor: '#000', shadowOpacity: 0.2, shadowRadius: 6, shadowOffset: { width: 0, height: 4 } }]} onPress={() => {
                                setConfirmClearModalVisible(false)
                                setTimeout(() => setYearModalVisible(true), 300)
                            }} >
                                <Text style={[styles.dialogBtnText, { color: Colors.white }]}>Confirm</Text>
                            </TouchableOpacity>
                        </View>
                    </TouchableOpacity>
                </TouchableOpacity>
            </Modal>
            <Modal visible={yearModal} transparent animationType="fade" onRequestClose={() => { setYearModal(false) }} >
                <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
                    <TouchableOpacity style={{ width: '100%', flex: 1 }} onPress={() => { setYearModal(false) }} >
                        <View style={{ width: '100%', height: '100%', backgroundColor: '#0006' }} />
                    </TouchableOpacity>
                    <View style={{ width: "100%", backgroundColor: Colors.main }}>
                        <View style={{ width: "100%", padding: moderateScale(16), backgroundColor: "#E41B14", flexDirection: "row", alignItems: "center", justifyContent: "space-between", borderTopLeftRadius: moderateScale(20), borderTopRightRadius: moderateScale(20) }}>
                            <Text style={{ color: "#FFFFFF", fontSize: moderateScale(16), fontWeight: "600" }}> Select Your Option </Text>
                        </View>
                        <View style={{ width: "100%", backgroundColor: "#ffffff", paddingVertical: moderateScale(10), }}>
                            <FlatList
                                data={[{ id: 1, title: 'Current FY', }, { id: 2, title: 'Previous FY', }, { id: 3, title: 'Current FY vs Previous FY', }]}
                                keyExtractor={(item, index) => item.id?.toString() || index.toString()}
                                showsVerticalScrollIndicator={false}
                                decelerationRate="fast"
                                renderItem={({ item, index }) => {
                                    return (
                                        <TouchableOpacity activeOpacity={0.95} onPress={() => { changeFYDataSet(item) }} >
                                            <View style={{ width: "100%", paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(13), flexDirection: "row", alignItems: "center", justifyContent: "space-between", backgroundColor: (index % 2 !== 0 ? DataStorage.transColorCode : "#FFFFFF") }}>
                                                <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500" }}> {item.title} </Text>
                                            </View>
                                        </TouchableOpacity>
                                    )
                                }}
                            />
                        </View>
                    </View>
                </View>
            </Modal>
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

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

export default NewPerformanceGraphScreen