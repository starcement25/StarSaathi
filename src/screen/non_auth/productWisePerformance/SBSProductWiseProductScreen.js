import React, { useEffect, useState, useCallback } from 'react'
import { View, Text, Platform } from 'react-native'
import SafeView from '../../../helper/SafeView'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Colors } from '../../../assets/Colors'
import PerformanceFilterView from '../../../common/PerformanceFilterView'
import PerformanceCalenderView from '../../../common/PerformanceCalenderView'
import { BarChart } from 'react-native-gifted-charts'
import UrlStorage from '../../../storage/UrlStorage'
import { useNavigation } from '@react-navigation/native'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const SBSProductWiseProductScreen = (props) => {
    const navigation = useNavigation();
    const [loading, setLoading] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)
    const [height, setHeight] = useState(0)
    const [barData, setBardata] = useState([]);
    const [selected, setSelected] = useState('');
    const [maxValue, setMaxValue] = useState(0);
    const [allData, setAllData] = useState([]);

    const monthMap = {
        Jan: "01", Feb: "02", Mar: "03", Apr: "04",
        May: "05", Jun: "06", Jul: "07", Aug: "08",
        Sep: "09", Oct: "10", Nov: "11", Dec: "12",
    };

    const [performanceFilterOpen, setPerformanceFilterOpen] = useState(false)
    const [performanceCalendarOpen, setPerformanceCalendarOpen] = useState(false)

    const openPerformanceFilter = () => setPerformanceFilterOpen(true)
    const closePerformanceFilter = () => setPerformanceFilterOpen(false)

    const openPerformanceCalendar = () => setPerformanceCalendarOpen(true)
    const closePerformanceCalendar = () => setPerformanceCalendarOpen(false)

    useEffect(() => {
        const currentDate = new Date();
        const formatted = currentDate.toLocaleDateString("en-US", { month: "short", year: "numeric" });
        setSelected(formatted)
        const [monthStr, yearStr] = formatted.split(" ")
        getGraphData(yearStr, monthMap[monthStr]);
    }, [])

    const fetchAuthData = async () => {
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
        }
    }

    const getData = useCallback((selectedValue) => {
        if (!selectedValue) return
        const parts = selectedValue.split(' ');
        if (parts.length < 2) return
        const monthStr = parts[0];
        const yearStr = parts[1];
        const month = monthMap[monthStr];
        if (month) getGraphData(yearStr, month);
    }, [])

    const getLabelHeight = (text, fontSize = 12) => {
        const avgCharWidth = fontSize * 0.6;
        return text.length * avgCharWidth;
    };

    const getGraphData = async (year, month) => {
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        const myHeaders = new Headers();
        myHeaders.append("Authorization", UrlStorage.ParameterList.BasicData.user_type == 'broker' ? `SAP_SP` : `SAP_DEALER` + ` ${UrlStorage.ParameterList.BasicData.emp_id}`);
        myHeaders.append("Content-Type", "application/json")
        const requestOptions = { method: "GET", redirect: "follow", headers: myHeaders }
        let url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.performance.item_wise_performance
        url = url + '?customer_code=' + UrlStorage.ParameterList.BasicData.emp_id + `&year=${year}&month=${month}`

        try {
            const response = await fetch(url, requestOptions)
            const result = await response.json()
            if (result?.target_ach_data?.length > 0) {
                const filteredData = result?.target_ach_data?.filter(item =>
                    !item.itemcode?.toLowerCase()?.includes("total")
                )
                let h = 0
                for (let i = 0; i < filteredData.length; i++) {
                    if (getLabelHeight(filteredData[i].itemname, 14) > h) {
                        h = getLabelHeight(filteredData[i].itemname, 10)
                    }
                }
                setHeight(h)
                const allValues = filteredData.flatMap(item => [
                    parseFloat(item.TGTQTY) || 0,
                    parseFloat(item.ACHQTY) || 0
                ])
                const maxValue = Math.max(...allValues)
                const nearest100 = Math.ceil(maxValue / 100) * 100
                setMaxValue(nearest100)


                const formattedData = filteredData.map(item => [
                    {
                        value: parseFloat(item.TGTQTY),
                        label: item.itemname.replace('STAR CEMENT', '').replace(/\bSTAR\b/g, '').trim() + ' ',
                        spacing: 2,
                        labelWidth: h,
                        labelTextStyle: { color: "#000", fontSize: 10, transform: [{ rotate: '-20deg' }], textAlign: 'center', left: -h / 2, top: moderateScale(120) },
                        frontColor: "#33B5E6"
                    },
                    {
                        value: parseFloat(item.ACHQTY),
                        frontColor: "#E41B14"
                    }
                ]).flat()
                setAllData(filteredData)
                setBardata(formattedData)
            } else {
                setBardata([])
            }
        } catch (error) {
            setBardata([])
        } finally {
            setLoading(false)
        }
    }

    const handleMonthSelect = useCallback((item) => {
        const month = monthMap[item.month];
        const newSelected = Number(month) > 3 ? item?.month + " " + item?.year.replace("FY ", "").split("-")[0] : item?.month + " " + item?.year.replace("FY ", "").split("-")[1];
        setSelected(newSelected);
        setTimeout(() => getData(newSelected), 80);
    }, [getData])

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ flex: 1, backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title={selected} backPath=" " Filter={false} Calendar={true} navigation={navigation} props={props} handlePerformanceFilterOpen={openPerformanceFilter} handlePerformanceCalendarOpen={openPerformanceCalendar} />

                <View style={{ flex: 1, padding: moderateScale(20) }}>
                    <View style={{ flex: 1, backgroundColor: "#FFFFFF", borderWidth: 1, borderColor: "#DCDDDF", padding: moderateScale(15), paddingStart: 0, borderRadius: moderateScale(10), }}>
                        {barData.length > 0 ? (
                            <View style={{ width: '100%', overflow: 'hidden' }}>
                                <BarChart
                                    data={barData}
                                    barWidth={30}
                                    height={Platform.OS == 'ios' ? moderateScale(400) : moderateScale(500)}
                                    barBorderTopRightRadius={5}
                                    barBorderTopLeftRadius={5}
                                    spacing={15}
                                    initialSpacing={50}
                                    hideRules={false}
                                    rulesColor="lightgray"
                                    rulesThickness={1}
                                    xAxisThickness={1}
                                    yAxisThickness={1}
                                    onPress={() => navigation.navigate('ProductWisePerformanceDetails', { allData })}
                                    yAxisTextStyle={{ color: 'gray', fontSize: 10 }}
                                    maxValue={maxValue}
                                    xAxisLabelsHeight={120}
                                    showValuesAsTopLabel
                                    scrollAnimation={true}
                                    topLabelTextStyle={{ color: "#000", fontSize: 9, width: 50, textAlign: 'center', marginBottom: 5 }}
                                    noOfSections={5}
                                    showXAxisIndices={true}
                                    isAnimated
                                    animationDuration={500}
                                />
                            </View>
                        ) : (
                            <Text style={{ color: '#000', flex: 1, textAlign: 'center', textAlignVertical: 'center' }}> {loading ? 'Loading...' : 'No data Found'} </Text>
                        )}
                    </View>

                    <View style={{ height: moderateScale(10) }} />
                    <View style={{ flexDirection: "row", alignItems: "center", gap: moderateScale(20) }}>
                        <View style={{ flexDirection: "row", alignItems: "center", gap: moderateScale(10) }}>
                            <View style={{ width: 20, height: 20, borderRadius: 4, backgroundColor: "#33B5E6" }} />
                            <Text style={{ color: Colors.text, fontSize: 12, fontWeight: "500" }}> Targets (MT) </Text>
                        </View>
                        <View style={{ flexDirection: "row", alignItems: "center", gap: moderateScale(10) }}>
                            <View style={{ width: 20, height: 20, borderRadius: 4, backgroundColor: '#E41B14' }} />
                            <Text style={{ color: Colors.text, fontSize: 12, fontWeight: "500" }}> Achievements (MT) </Text>
                        </View>
                    </View>
                </View>
            </View>

            <PerformanceFilterView
                isVisible={performanceFilterOpen}
                closeLoginPopup={closePerformanceFilter}
            />
            <PerformanceCalenderView
                isVisible={performanceCalendarOpen}
                closeLoginPopup={closePerformanceCalendar}
                onMonthSelect={handleMonthSelect}
            />
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

export default SBSProductWiseProductScreen
