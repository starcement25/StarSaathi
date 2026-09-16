import React, { useEffect, useState } from 'react'
import { TouchableOpacity, View, Text, Platform } from 'react-native'
import { BarChart } from 'react-native-gifted-charts'
import SafeView from '../../../helper/SafeView'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Colors } from '../../../assets/Colors'
import { useNavigation } from '@react-navigation/native'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import ShipToSelfListPopupView from '../order/popup/ShipToSelfListPopupView'
import Loader from '../../../common/Loader'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const SBSPerformanceGraphScreen = (props) => {
    const navigation = useNavigation()
    const [appOrder, setAppOrder] = useState(false)
    const [hideGraph, setHideGraph] = useState(true)
    const [loading, setLoading] = useState(false)
    const [subDealerData, setSubDealerData] = useState([])
    const [subDealerId, setDealerSubDealerId] = useState(UrlStorage.ParameterList.BasicData.emp_code)
    const [barData, setBarData] = useState({ currentYear: [], previousYear: [] })
    const [currentMaxValue, setCurrentMaxValue] = useState(0)
    const [prevMaxValue, setPrevMaxValue] = useState(0)
    const [performanceFilterOpen, setPerformanceFilterOpen] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)
    const [currentYearChartData, setCurrentYearChartData] = useState([])
    const [previousYearChartData, setPreviousYearChartData] = useState([])

    useEffect(() => {
        (UrlStorage.ParameterList.BasicData.user_type == 'broker' || UrlStorage.ParameterList.BasicData.user_type.toLocaleLowerCase() == 'dealer') && requestForCementSubDealer()
    }, [])

    useEffect(() => {
        getGraphData(subDealerId.customer_code ?? UrlStorage.ParameterList.BasicData.emp_id)
    }, [subDealerId])

    useEffect(() => {
        if (barData.currentYear.length > 0) {
            const currentFlattened = barData.currentYear.flatMap((item) => [
                { value: item.value, label: item.label, frontColor: "#50E3C2", barWidth: 20, spacing: 4, },
                { value: item.target, label: '', frontColor: "#4A90E2", barWidth: 20, }
            ])
            setCurrentYearChartData(currentFlattened)
        }
        if (barData.previousYear.length > 0) {
            const previousFlattened = barData.previousYear.flatMap((item) => [
                { value: item.value, label: item.label, frontColor: "#50E3C2", barWidth: 20, spacing: 4, },
                { value: item.target, label: '', frontColor: "#4A90E2", barWidth: 20, }
            ])
            setPreviousYearChartData(previousFlattened)
        }
    }, [barData])

    const requestForCementSubDealer = async () => {
        setLoading(true)
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            let url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.OrderURL1.dealer_data_list_url
            url += "?emp_code=" + subDealerId + "&user_type=Sub Dealer&login_type=" + UrlStorage.ParameterList.BasicData.user_type
            const response = await fetch(url)
            const result = await response.json()
            if (result.process_status == 'YES' && result?.sub_dealer_data?.length > 0)
                setSubDealerData(result.sub_dealer_data)
        } catch (error) { }
        setTimeout(() => {
            setLoading(false)
        }, 3000)
    }

    const getGraphData = async (emp_code) => {
        setLoading(true)
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        const myHeaders = new Headers()
        myHeaders.append("Authorization", UrlStorage.ParameterList.BasicData.user_type == 'broker' ? `SAP_SP` : `SAP_DEALER` + ` ${emp_code}`)
        myHeaders.append("Content-Type", "application/json")
        try {
            let url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.BaseUrlList.SBS.sbs_performance
            url += `?dealer_id=${emp_code}`
            const requestOptions = { method: "GET", redirect: "follow", headers: myHeaders }
            const response = await fetch(url, requestOptions)
            const text = await response.json()
            if (!text?.currentfy_analytics?.length) {
                setHideGraph(true)
                return
            } else
                setHideGraph(false)
            const extractValues = (item) => {
                const keys = Object.keys(item)
                const achievementKey = keys.find(key => key.includes('achievement'))
                const targetKey = keys.find(key => key.includes('target'))
                return {
                    achievement: Number(item[achievementKey]) || 0,
                    target: Number(item[targetKey]) || 0
                }
            }
            const currentYear = text.currentfy_analytics.map((item) => {
                const { achievement, target } = extractValues(item)
                return {
                    label: item.month,
                    value: achievement,
                    frontColor: "#4A90E2",
                    target: target,
                }
            })
            const previousYear = text.prevfy_analytics.map((item) => {
                const { achievement, target } = extractValues(item)
                return {
                    label: item.month,
                    value: achievement,
                    frontColor: "#50E3C2",
                    target: target,
                }
            })
            setBarData({ currentYear, previousYear, })
            const currentMax = Math.max(...currentYear.map((d) => d.value), ...currentYear.map((d) => d.target), 100)
            const prevMax = Math.max(...previousYear.map((d) => d.value), ...previousYear.map((d) => d.target), 100)
            setCurrentMaxValue(currentMax)
            setPrevMaxValue(prevMax)
        } catch (error) {
        }
        setTimeout(() => {
            setLoading(false)
        }, 3000)
    }

    const handlePerformanceFilterOpen = () => setPerformanceFilterOpen(!performanceFilterOpen)
    const selectShipToSelfItem = (item) => { setDealerSubDealerId(item), handlePerformanceFilterOpen() }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Performance Graph" backPath=" " />
                <View style={{ width: "100%", flex: 1, padding: moderateScale(20) }}>
                    <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "center" }}>
                        <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={() => setAppOrder(false)}>
                            <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: appOrder === false ? DataStorage.primaryColorCode : DataStorage.transColorCode, borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderBottomWidth: 0 }}>
                                <Text style={{ color: appOrder === false ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600" }}> Current Year </Text>
                            </View>
                        </TouchableOpacity>
                        <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={() => setAppOrder(true)}>
                            <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: appOrder ? DataStorage.primaryColorCode : DataStorage.transColorCode, borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderBottomWidth: 0 }}>
                                <Text style={{ color: appOrder ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600" }}> Previous Year </Text>
                            </View>
                        </TouchableOpacity>
                    </View>
                    {(!loading && !hideGraph) ? <View style={{ flex: 1, backgroundColor: "#FFFFFF", borderLeftWidth: moderateScale(1), borderRightWidth: moderateScale(1), borderBottomWidth: moderateScale(1), borderTopWidth: 0, borderColor: "#DCDDDF", padding: moderateScale(15), paddingStart: 0, borderBottomRightRadius: moderateScale(10), borderBottomLeftRadius: moderateScale(10), justifyContent: 'flex-end' }}>
                        <View style={{ width: '100%', overflow: 'hidden', justifyContent: 'flex-end', flex: 1 }}>
                            <BarChart
                                key={appOrder ? 'previous' : 'current'}
                                data={appOrder ? previousYearChartData : currentYearChartData}
                                barBorderTopRightRadius={5}
                                barBorderTopLeftRadius={5}
                                spacing={25}
                                onPress={() => navigation.navigate('PerformanceGraphDetails', { barData, appOrder, subDealerId })}
                                hideRules={false}
                                rulesColor="lightgray"
                                rulesThickness={1}
                                xAxisThickness={1}
                                yAxisThickness={1}
                                yAxisTextStyle={{ color: 'gray', fontSize: 10 }}
                                labelsDistanceFromXaxis={1}
                                xAxisLabelTextStyle={{ color: 'gray', textAlign: 'center', fontSize: 10, width: 40 }}
                                height={Platform.OS === 'ios' ? moderateScale(400) : moderateScale(500)}
                                showValuesAsTopLabel
                                scrollAnimation={true}
                                topLabelTextStyle={{ color: '#000', fontSize: 9, width: 50, height: 10, textAlign: 'center', marginBottom: 5, }}
                                maxValue={appOrder ? prevMaxValue : currentMaxValue}
                                noOfSections={5}
                                isAnimated
                                animationDuration={1000}
                                showVerticalLines={false}
                                showYAxisIndices={false}
                                showXAxisIndices={true}
                                yAxisSide="left"
                            />
                        </View>
                    </View> : (!loading && <Text style={{ textAlign: 'center', textAlignVertical: 'center', flex: 1, top: 150, bottom: 100, start: 0, end: 0, color: '#000', position: 'absolute' }}>No Records Found</Text>)}
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
                <ShipToSelfListPopupView isVisible={performanceFilterOpen} dataList={subDealerData} closePopup={handlePerformanceFilterOpen} selectItem={selectShipToSelfItem} isDealer={false} />
            </View>
            {loading && <Loader />}
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

export default SBSPerformanceGraphScreen