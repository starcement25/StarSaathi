import React, { useEffect, useState, useCallback, useRef } from 'react';
import { View, Text, ScrollView, TouchableOpacity, Animated, Dimensions, Image, Platform, } from 'react-native';
import SafeView from '../../../helper/SafeView';
import { moderateScale } from '../../../helper/Window';
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView';
import { Colors } from '../../../assets/Colors';
import PerformanceFilterView from '../../../common/PerformanceFilterView';
import PerformanceCalenderView from '../../../common/PerformanceCalenderView';
import UrlStorage from '../../../storage/UrlStorage';
import { useNavigation } from '@react-navigation/native';
import { Icons } from '../../../assets/Icons';
import LinearGradient from 'react-native-linear-gradient';
import moment from 'moment';
import DataStorage from '../../../storage/DataStorage';
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi';
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView';

const SCREEN_HEIGHT = Dimensions.get('window').height / 1.25;

const monthMap = {
    Jan: "01", Feb: "02", Mar: "03", Apr: "04",
    May: "05", Jun: "06", Jul: "07", Aug: "08",
    Sep: "09", Oct: "10", Nov: "11", Dec: "12",
};

const MONTH_NAME = [
    'January', 'February', 'March',
    'April', 'May', 'June',
    'July', 'August', 'September',
    'October', 'November', 'December'
]


// Color Set Percentage bar
const MORE_THAN_100_PERCENTAGE = ['#00D492', '#009966']
const LESS_THAN_100_PERCENTAGE = ['#E8BE76', '#FF7700']
const EQUAL_0_PERCENTAGE = ['#FF6467', '#E7000B']



const extractYearMonth = (label) => {
    if (!label) return null;
    const parts = label.split(' ');
    const monthStr = parts[0];
    const month = monthMap[monthStr];
    if (!month) return null;
    let year = null;
    if (label.includes('FY')) {
        const fy = label.replace('FY ', '').split('-');
        const m = Number(month);
        year = m > 3 ? fy[0] : `20${fy[1]}`;
    } else {
        year = parts[1];
    }
    if (!year) return null;
    return { year, month };
};

const formatVal = (v) => `${Math.round(v)}`;

const getNiceMax = (rawMax) => {
    if (rawMax <= 0) return 100;
    const magnitude = Math.pow(10, Math.floor(Math.log10(rawMax)));
    const headroom = rawMax * 1.18;
    const step = magnitude / 2;
    return Math.ceil(headroom / step) * step;
};

const AnimatedBar = ({ tgtValue, achValue, maxValue, barWidth, chartHeight, index, onPress, }) => {
    const tgtAnim = useRef(new Animated.Value(0)).current;
    const achAnim = useRef(new Animated.Value(0)).current;

    useEffect(() => {
        tgtAnim.setValue(0);
        achAnim.setValue(0);
        const delay = index * 50;
        Animated.parallel([
            Animated.timing(tgtAnim, { toValue: 1, duration: 450, delay, useNativeDriver: false }),
            Animated.timing(achAnim, { toValue: 1, duration: 450, delay: delay + 60, useNativeDriver: false }),
        ]).start();
    }, [tgtValue, achValue, maxValue]);

    const safeMax = maxValue > 0 ? maxValue : 1;

    const tgtBarH = Math.max(0, (tgtValue / safeMax) * chartHeight);
    const achBarH = Math.max(0, (achValue / safeMax) * chartHeight);

    const tgtPx = tgtAnim.interpolate({
        inputRange: [0, 1], outputRange: [0, tgtBarH],
    });
    const achPx = achAnim.interpolate({
        inputRange: [0, 1], outputRange: [0, achBarH],
    });

    const LABEL_H = 14; // fixed height for the value label above each bar

    return (
        <TouchableOpacity onPress={onPress} activeOpacity={0.75} style={{ flexDirection: 'row', alignItems: 'flex-end', marginHorizontal: 5 }} >
            <View style={{ alignItems: 'center', width: barWidth }}>
                <View style={{ height: chartHeight + LABEL_H, justifyContent: 'flex-end' }}>
                    {tgtValue > 0 ? (
                        <Animated.View style={{ marginBottom: tgtPx, height: LABEL_H, justifyContent: 'flex-end', position: 'absolute', bottom: 0, left: 0, right: 0, alignItems: 'center', }}>
                            <Text style={{ width: 30, fontSize: 8, color: '#222', lineHeight: 9, textAlign: 'center' }}>
                                {formatVal(tgtValue)}
                            </Text>
                        </Animated.View>
                    ) : null}
                    <Animated.View style={{ width: barWidth, height: tgtPx, backgroundColor: '#4A90E2', borderTopLeftRadius: 3, borderTopRightRadius: 3, }} />
                </View>
            </View>

            <View style={{ width: 2 }} />

            <View style={{ alignItems: 'center', width: barWidth }}>
                <View style={{ height: chartHeight + LABEL_H, justifyContent: 'flex-end' }}>
                    {achValue > 0 ? (
                        <Animated.View style={{ position: 'absolute', bottom: 0, left: 0, right: 0, alignItems: 'center', marginBottom: achPx, height: LABEL_H, justifyContent: 'flex-end', }}>
                            <Text style={{ fontSize: 8, color: '#222', lineHeight: 9 }}>
                                {formatVal(achValue)}
                            </Text>
                        </Animated.View>
                    ) : null}
                    <Animated.View style={{ width: barWidth, height: achPx, backgroundColor: '#50E3C2', borderTopLeftRadius: 3, borderTopRightRadius: 3, }} />
                </View>
            </View>
        </TouchableOpacity>
    );
};

const LABEL_H = 14; // keep in sync with AnimatedBar

const YAxis = ({ maxValue, chartHeight, noOfSections }) => {
    const steps = Array.from({ length: noOfSections + 1 }, (_, i) =>
        Math.round((maxValue / noOfSections) * (noOfSections - i))
    );
    return (
        <View style={{ width: 46, paddingRight: 6, paddingTop: LABEL_H }}>
            <View style={{ height: chartHeight, justifyContent: 'space-between' }}>
                {steps.map((val, i) => (
                    <Text key={i} style={{ fontSize: 9, color: 'gray', textAlign: 'right' }}>
                        {formatVal(val)}
                    </Text>
                ))}
            </View>
        </View>
    );
};

const GridLines = ({ chartHeight, noOfSections }) => (
    <View style={{ position: 'absolute', top: 0, left: 0, right: 0, height: chartHeight }} pointerEvents="none" >
        {Array.from({ length: noOfSections + 1 }, (_, i) => (
            <View key={i} style={{ position: 'absolute', top: (chartHeight / noOfSections) * i, left: 0, right: 0, height: 1, backgroundColor: 'rgba(0,0,0,0.09)', }} />
        ))}
    </View>
);

const XLabels = ({ chartData, barWidth, labelAreaHeight, scrollRef }) => (
    <ScrollView ref={scrollRef} horizontal scrollEnabled={false} showsHorizontalScrollIndicator={false} contentContainerStyle={{ flexDirection: 'row', paddingHorizontal: 6 }} >
        {chartData.map((item, index) => {
            const groupW = barWidth * 2;
            return (
                <View key={`lbl-${index}`} style={{ width: groupW + 1, height: labelAreaHeight, marginHorizontal: 5, alignItems: 'center', justifyContent: 'flex-start', paddingTop: 4, overflow: 'visible', }} >
                    <View style={{ width: labelAreaHeight, transform: [{ rotate: '90deg' }, { translateX: (labelAreaHeight - groupW) / 2 },], alignItems: 'center', }}>
                        <Text numberOfLines={2} style={{ fontSize: 8, color: '#333', fontWeight: '400' }}>
                            {item.label}
                        </Text>
                    </View>
                </View>
            );
        })}
    </ScrollView>
);

/* ─────────────────────────────────────────────
   MAIN SCREEN
───────────────────────────────────────────── */
const NewProductWiseProductScreen = (props) => {
    const navigation = useNavigation();
    const barScrollRef = useRef(null);
    const labelScrollRef = useRef(null);

    const [loading, setLoading] = useState(false);
    const [chartData, setChartData] = useState([]);
    const [selected, setSelected] = useState('');
    const [maxValue, setMaxValue] = useState(100);
    const [allData, setAllData] = useState([]);
    const [performanceFilterOpen, setPerformanceFilterOpen] = useState(false);
    const [performanceCalendarOpen, setPerformanceCalendarOpen] = useState(false);
    const [authChecker, setAuthChecker] = useState(false);

    const [detailsPerformance, setDetailsPerformance] = useState([]);
    const [salesMode, setSalesMode] = useState(0);
    const [totalTarget, setTotalTarget] = useState(0);
    const [totalAchievement, setTotalAchievement] = useState(0);
    const [avgPerformance, setAvgPerformance] = useState(0);

    const PREMIUM_SALES = ['14000214', '14000194']

    const BAR_WIDTH = 20;
    const NO_OF_SECTIONS = 5;
    const LABEL_AREA_H = 120;
    const X_AXIS_H = 1;
    const LEGEND_H = 40;
    const SCROLL_HINT_H = 18;
    const OUTER_PAD = moderateScale(12) * 2;
    const CARD_PAD = moderateScale(10) * 2;
    const HEADER_H = moderateScale(60);

    const CHART_HEIGHT = Math.max(
        160,
        SCREEN_HEIGHT
        - HEADER_H
        - LEGEND_H
        - OUTER_PAD
        - CARD_PAD
        - LABEL_H
        - X_AXIS_H
        - LABEL_AREA_H
        - SCROLL_HINT_H
        - 52
    );

    useEffect(() => {
        const currentDate = new Date();
        const formatted = currentDate.toLocaleDateString("en-US", {
            month: "short", year: "numeric",
        });
        setSelected(moment(currentDate).format('MMMM, YYYY'));
        const res = extractYearMonth(formatted);
        if (res) {
            if (DataStorage.typeOfUse == 1)
                getGraphDataSBS(res.year, res.month);
            else
                getGraphData(res.year, res.month);
        }
    }, []);

    const fetchAuthData = async () => {
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
        }
    }

    useEffect(() => {
        dataSetShow(allData)
    }, [salesMode])

    const getData = useCallback((selectedValue) => {
        const res = extractYearMonth(selectedValue);
        if (!res) return;
        if (DataStorage.typeOfUse == 1)
            getGraphDataSBS(res.year, res.month);
        else
            getGraphData(res.year, res.month);
    }, []);

    const getGraphData = async (year, month) => {
        setLoading(true);
        setChartData([]);

        const baseUrl = UrlStorage.BaseUrlList.Saathi.base_url_saathi.replace(/\/$/, '');
        const apiPath = UrlStorage.NonAuthURL.Saathi.DownloadDatabaseAPI.item_wise_target_achievement_SAP_data.replace(/^\//, '');
        const url = `${baseUrl}/${apiPath}` + `?customer_code=${UrlStorage.ParameterList.BasicData.user_type == 'broker' ? UrlStorage.ParameterList.BasicData.customerDetails.SAP_code : UrlStorage.ParameterList.BasicData.emp_id}` + `&year=${year}&month=${month}`;

        try {
            var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
            const response = await fetch(url, { method: "GET" });
            if (!response.ok) { setChartData([]); return; }
            const result = await response.json();
            setAllData(result)
            dataSetShow(result)
        } catch (e) {
            setChartData([]);
        } finally {
            setLoading(false);
        }
    };
    const getGraphDataSBS = async (year, month) => {
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
            const response = await fetch(url, { method: "GET", headers: myHeaders, });
            if (!response.ok) { setChartData([]); return; }
            const result = await response.json();

            setAllData(result)
            dataSetShow(result)
        } catch (e) {
            setChartData([]);
        } finally {
            setLoading(false);
        }
    }

    const dataSetShow = (result) => {
        if (result?.target_ach_data?.length > 0) {
            const filtered = result.target_ach_data.filter(
                item => {
                    const code = item.itemcode?.split(' ')[0];
                    if (item.itemcode?.toLowerCase()?.includes("total")) {
                        return false;
                    }
                    if (salesMode === 0) {
                        return !PREMIUM_SALES.includes(code);
                    } else {
                        return PREMIUM_SALES.includes(code);
                    }
                }
            );

            let totalTarget = 0;
            let totalAchievement = 0;

            const allValues = filtered.flatMap(item => [
                parseFloat(item.TGTQTY) || 0,
                parseFloat(item.ACHQTY) || 0,
            ]);
            const rawMax = Math.max(...allValues, 1);
            setMaxValue(getNiceMax(rawMax));

            const formatted = filtered.map(item => ({
                label: item.itemname.replace('STAR CEMENT', '').replace(/\bSTAR\b/g, '').trim(),
                tgt: parseFloat(item.TGTQTY) || 0,
                ach: parseFloat(item.ACHQTY) || 0,
            }));
            setChartData(formatted);

            const arr = filtered.map(item => {
                let target = parseFloat(item.TGTQTY) || 0;
                let achievement = parseFloat(item.ACHQTY) || 0;
                totalTarget += target
                totalAchievement += achievement
                let percentage = 0;

                if (target === 0 && achievement === 0) {
                    percentage = 0;
                } else if (target === 0) {
                    percentage = 100;
                } else {
                    percentage = (achievement / target) * 100;
                }

                return {
                    id: item.itemcode,
                    name: item.itemname,
                    target: target,
                    achievement: achievement,
                    percentage: percentage
                };
            });

            let avgPerformance = (totalAchievement / totalTarget) * 100;

            setDetailsPerformance(arr)
            setTotalTarget(totalTarget)
            setTotalAchievement(totalAchievement)
            setAvgPerformance(avgPerformance)
        } else {
            setChartData([]);
            setDetailsPerformance([])
            setTotalTarget(0)
            setTotalAchievement(0)
            setAvgPerformance(0)
        }
    }

    const handleMonthSelect = useCallback((item) => {
        console.log("year", item?.year)
        const month = monthMap[item.month];
        const newSelected = Number(month) > 3 ? `${item.month} ${item.year.replace("FY ", "").split("-")[0]}` : `${item.month} ${item.year.replace("FY ", "").split("-")[1]}`;
        const newSelected1 = Number(month) > 3 ? `${MONTH_NAME[Number(month) - 1]}, ${item.year.replace("FY ", "").split("-")[0]}` : `${MONTH_NAME[Number(month) - 1]}, ${item.year.replace("FY ", "").split("-")[1]}`;
        setSelected(newSelected1);
        setPerformanceCalendarOpen(false)
        setTimeout(() => getData(newSelected), 80);
    }, [getData]);

    const handleBarScroll = (e) => {
        const x = e.nativeEvent.contentOffset.x;
        labelScrollRef.current?.scrollTo({ x, animated: false });
    };

    const colorSet = (percentage) => {
        if (parseInt(percentage) >= 100)
            return MORE_THAN_100_PERCENTAGE
        else if (parseInt(percentage) == 0)
            return EQUAL_0_PERCENTAGE
        else
            return LESS_THAN_100_PERCENTAGE
    }

    const closePopup = () => {

    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={DataStorage.typeOfUse == 1 ? '#8FC227' : Colors.main}>
            <View style={{ flex: 1 }}>
                <SBSCommonHeaderView
                    title="Product Sales Performance"
                    backPath=""
                    Filter={false}
                    navigation={navigation}
                    props={props}
                    handlePerformanceFilterOpen={() => setPerformanceFilterOpen(true)}
                />
                <View style={{ width: '100%', height: moderateScale(55), backgroundColor: '#E41B14', alignItems: 'center', justifyContent: 'center', paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(5) }}>
                    <TouchableOpacity onPress={() => { setPerformanceCalendarOpen(true) }} style={{ width: '100%', height: '100%', borderRadius: moderateScale(10), borderWidth: 1, borderColor: '#FFFFFF50', backgroundColor: '#FFFFFF30', alignItems: 'center', justifyContent: 'center', flexDirection: 'row', paddingHorizontal: moderateScale(20) }}>
                        <Text style={{ color: '#FFFFFF', fontSize: moderateScale(16), fontWeight: '500' }}>{selected}</Text>
                        <View style={{ flex: 1 }} />
                        <Image source={Icons.DownArrow} tintColor={'#FFFFFF'} style={{ width: moderateScale(15), height: moderateScale(15) }} />
                    </TouchableOpacity>
                </View>
                <View style={{ width: '100%', height: moderateScale(10), backgroundColor: '#E41B14', alignItems: 'center', justifyContent: 'center', paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(5) }} />

                {DataStorage.typeOfUse == 1 ? null : <View style={{ width: '100%', alignItems: 'center', justifyContent: 'center' }}>
                    <View style={{ width: '80%', height: moderateScale(50), backgroundColor: '#F9F9F9', borderRadius: moderateScale(15), elevation: 2, flexDirection: 'row', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                        <View style={{ flex: 1, height: '100%', padding: moderateScale(5) }}>
                            <TouchableOpacity onPress={() => { setSalesMode(0) }} style={{ width: '100%', height: '100%', borderRadius: moderateScale(15), backgroundColor: salesMode == 0 ? '#E41B14' : '#0000', elevation: salesMode == 0 ? 2 : 0, alignItems: 'center', justifyContent: 'center', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                                <Text style={{ color: salesMode == 0 ? '#FFF' : '#000' }}>Sales</Text>
                            </TouchableOpacity>
                        </View>
                        <View style={{ flex: 1, height: '100%', padding: moderateScale(5) }}>
                            <TouchableOpacity onPress={() => { setSalesMode(1) }} style={{ width: '100%', height: '100%', borderRadius: moderateScale(15), backgroundColor: salesMode == 1 ? '#E41B14' : '#0000', elevation: salesMode == 1 ? 2 : 0, alignItems: 'center', justifyContent: 'center', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                                <Text style={{ color: salesMode == 1 ? '#FFF' : '#000' }}>Premium Sales</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>}
                <View style={{ height: moderateScale(15) }} />
                <ScrollView>
                    <View style={{ width: '100%', flexDirection: 'column', paddingHorizontal: moderateScale(15) }}>
                        <View style={{ height: moderateScale(15) }} />
                        <View style={{ width: '100%', backgroundColor: '#FFF', elevation: 2, borderRadius: moderateScale(12), flexDirection: 'column', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                            <View style={{ height: moderateScale(10) }} />
                            <View style={{ width: '100%', flexDirection: 'row' }}>
                                <View style={{ paddingHorizontal: moderateScale(10) }}>
                                    <Image source={require('../../../assets/new_icon/Container_P1.png')} style={{ width: moderateScale(50), height: moderateScale(50) }} />
                                </View>
                                <View style={{ flexDirection: 'column' }}>
                                    <Text style={{ color: '#62748E', textTransform: 'uppercase', fontWeight: '500', fontSize: moderateScale(12) }}>Total Target MT</Text>
                                    <View style={{ height: moderateScale(5) }} />
                                    <Text style={{ color: '#155DFC', textTransform: 'uppercase', fontWeight: '900', fontSize: Platform.OS == 'ios' ? moderateScale(26) : moderateScale(30) }}>{parseFloat(totalTarget).toFixed(2)}</Text>
                                </View>
                            </View>
                            <View style={{ height: moderateScale(10) }} />
                            <View style={{ height: moderateScale(10), borderBottomLeftRadius: moderateScale(20), borderBottomRightRadius: moderateScale(20), width: '100%', backgroundColor: '#155DFC' }} />
                        </View>
                        <View style={{ height: moderateScale(15) }} />
                        <View style={{ width: '100%', backgroundColor: '#FFF', elevation: 2, borderRadius: moderateScale(12), flexDirection: 'column', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                            <View style={{ height: moderateScale(10) }} />
                            <View style={{ width: '100%', flexDirection: 'row' }}>
                                <View style={{ paddingHorizontal: moderateScale(10) }}>
                                    <Image source={require('../../../assets/new_icon/Container_P2.png')} style={{ width: moderateScale(50), height: moderateScale(50) }} />
                                </View>
                                <View style={{ flexDirection: 'column' }}>
                                    <Text style={{ color: '#62748E', textTransform: 'uppercase', fontWeight: '500', fontSize: moderateScale(12) }}>Total Achievement MT</Text>
                                    <View style={{ height: moderateScale(5) }} />
                                    <Text style={{ color: '#009966', textTransform: 'uppercase', fontWeight: '900', fontSize: Platform.OS == 'ios' ? moderateScale(26) : moderateScale(30) }}>{parseFloat(totalAchievement).toFixed(2)}</Text>
                                    <View style={{ height: moderateScale(5) }} />
                                    <Text style={{ color: '#009966', textTransform: 'uppercase', fontWeight: '500', fontSize: moderateScale(12) }}>{parseFloat(avgPerformance).toFixed(2)}% of Target</Text>
                                </View>
                            </View>
                            <View style={{ height: moderateScale(10) }} />
                            <View style={{ height: moderateScale(10), borderBottomLeftRadius: moderateScale(20), borderBottomRightRadius: moderateScale(20), width: '100%', backgroundColor: '#009966' }} />
                        </View>
                        <View style={{ height: moderateScale(15) }} />
                        <View style={{ width: '100%', backgroundColor: '#FFF', elevation: 2, borderRadius: moderateScale(12), flexDirection: 'column', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                            <View style={{ height: moderateScale(10) }} />
                            <View style={{ width: '100%', flexDirection: 'row' }}>
                                <View style={{ paddingHorizontal: moderateScale(10) }}>
                                    <Image source={require('../../../assets/new_icon/Container_P3.png')} style={{ width: moderateScale(50), height: moderateScale(50) }} />
                                </View>
                                <View style={{ flexDirection: 'column' }}>
                                    <Text style={{ color: '#62748E', textTransform: 'uppercase', fontWeight: '500', fontSize: moderateScale(12) }}>Avg. Performance</Text>
                                    <View style={{ height: moderateScale(5) }} />
                                    <Text style={{ color: '#F54900', textTransform: 'uppercase', fontWeight: '900', fontSize: Platform.OS == 'ios' ? moderateScale(26) : moderateScale(30) }}>{(isNaN(parseFloat(avgPerformance))|| !isFinite(parseFloat(avgPerformance))) ? "---" : `${parseFloat(avgPerformance).toFixed(2)} `}%</Text>
                                    <View style={{ height: moderateScale(5) }} />
                                    <Text style={{ color: '#62748E', textTransform: 'uppercase', fontWeight: '500', fontSize: moderateScale(12) }}>{avgPerformance < 100 ? 'Below Target' : 'Above Target'}</Text>
                                </View>
                            </View>
                            <View style={{ height: moderateScale(10) }} />
                            <View style={{ height: moderateScale(10), borderBottomLeftRadius: moderateScale(20), borderBottomRightRadius: moderateScale(20), width: '100%', backgroundColor: '#F54900' }} />
                        </View>
                        <View style={{ height: moderateScale(15) }} />

                        <View style={{ width: '100%', backgroundColor: '#FFF', elevation: 2, borderRadius: moderateScale(12), flexDirection: 'column', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                            <Text style={{ color: '#000000', fontSize: moderateScale(24), fontWeight: '600', paddingHorizontal: moderateScale(20), paddingTop: moderateScale(20) }}>Product-wise Performance</Text>
                            <Text style={{ color: '#62748E', fontSize: moderateScale(14), paddingHorizontal: moderateScale(20), }}>Compare targets vs achievements</Text>
                            <View style={{ width: '100%', flexDirection: 'row', alignItems: 'center', paddingHorizontal: moderateScale(20), paddingTop: moderateScale(10) }}>
                                <View style={{ width: moderateScale(12), height: moderateScale(12), borderRadius: moderateScale(12), backgroundColor: '#4A90E2' }} />
                                <Text style={{ color: '#000000', fontSize: moderateScale(14), paddingLeft: moderateScale(5) }}>Targets</Text>
                                <View style={{ width: moderateScale(20) }} />
                                <View style={{ width: moderateScale(12), height: moderateScale(12), borderRadius: moderateScale(12), backgroundColor: '#50E3C2' }} />
                                <Text style={{ color: '#000000', fontSize: moderateScale(14), paddingLeft: moderateScale(5) }}>Achievements</Text>
                            </View>
                            <View style={{ height: moderateScale(20) }} />

                            <View style={{ flex: 1, backgroundColor: '#FFF', paddingTop: moderateScale(10), paddingBottom: 6, paddingHorizontal: moderateScale(6), }}>
                                {chartData.length > 0 ? (
                                    <View style={{ flex: 1 }}>
                                        <View style={{ flexDirection: 'row', flex: 1 }}>
                                            <YAxis
                                                maxValue={maxValue}
                                                chartHeight={CHART_HEIGHT}
                                                noOfSections={NO_OF_SECTIONS}
                                            />

                                            <View style={{ flex: 1 }}>
                                                <View style={{ height: CHART_HEIGHT + LABEL_H, position: 'relative' }}>
                                                    <View style={{ position: 'absolute', top: LABEL_H, left: 0, right: 0, height: CHART_HEIGHT, pointerEvents: 'none', }}>
                                                        <GridLines chartHeight={CHART_HEIGHT} noOfSections={NO_OF_SECTIONS} />
                                                    </View>

                                                    <ScrollView ref={barScrollRef} horizontal showsHorizontalScrollIndicator={false} onScroll={handleBarScroll} scrollEventThrottle={16} contentContainerStyle={{ flexDirection: 'row', alignItems: 'flex-end', paddingHorizontal: 4, height: CHART_HEIGHT + LABEL_H, }} >
                                                        {chartData.map((item, index) => (
                                                            <AnimatedBar
                                                                key={`${item.label}-${index}`}
                                                                index={index}
                                                                tgtValue={item.tgt}
                                                                achValue={item.ach}
                                                                maxValue={maxValue}
                                                                barWidth={BAR_WIDTH}
                                                                chartHeight={CHART_HEIGHT} />
                                                        ))}
                                                    </ScrollView>
                                                </View>

                                                {/* X-axis line */}
                                                <View style={{ height: X_AXIS_H, backgroundColor: '#bbb' }} />

                                                {/* FIX 4: Synced x-axis labels */}
                                                <XLabels
                                                    scrollRef={labelScrollRef}
                                                    chartData={chartData}
                                                    barWidth={BAR_WIDTH}
                                                    labelAreaHeight={LABEL_AREA_H}
                                                />

                                                {chartData.length > 4 && (
                                                    <Text style={{ fontSize: 9, color: '#ccc', textAlign: 'right', paddingRight: 4, height: SCROLL_HINT_H, lineHeight: SCROLL_HINT_H, }}>
                                                        ← scroll →
                                                    </Text>
                                                )}
                                            </View>
                                        </View>
                                    </View>
                                ) : (
                                    <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
                                        <Text style={{ color: '#888', fontSize: 14 }}>
                                            {loading ? 'Loading...' : 'No data found'}
                                        </Text>
                                    </View>
                                )}
                            </View>
                            <View style={{ height: moderateScale(10) }} />
                        </View>
                        <View style={{ height: moderateScale(15) }} />

                        <View style={{ width: '100%', backgroundColor: '#FFF', elevation: 2, borderRadius: moderateScale(12), flexDirection: 'column', shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, }}>
                            <Text style={{ color: '#000000', fontSize: moderateScale(24), fontWeight: '600', paddingHorizontal: moderateScale(20), paddingTop: moderateScale(20) }}>Detailed Performance</Text>
                            <Text style={{ color: '#62748E', fontSize: moderateScale(14), paddingHorizontal: moderateScale(20), }}>Individual product performance breakup</Text>
                            <View style={{ height: moderateScale(20) }} />
                            <View style={{ width: '100%', height: moderateScale(1), backgroundColor: '#EEE' }} />
                            <ScrollView horizontal showsHorizontalScrollIndicator={false}>
                                <View>

                                    <View style={{ flexDirection: 'row', height: moderateScale(50), backgroundColor: '#F9F9F9' }}>
                                        {['Product', 'Target\n(MT)', 'Achievement\n(MT)', '% Achievement'].map((title, i) => (
                                            <View key={i} style={{ width: title == 'Product' || title == '% Achievement' ? moderateScale(150) : moderateScale(120), justifyContent: 'center', alignItems: 'center' }}>
                                                <Text style={{ color: '#314158', fontSize: moderateScale(14), textAlign: 'center', fontWeight: '600' }}>
                                                    {title}
                                                </Text>
                                            </View>
                                        ))}
                                    </View>

                                    {/* Data Rows */}
                                    {detailsPerformance.map((item, index) => (
                                        <View key={index} style={{ flexDirection: 'row', paddingVertical: moderateScale(10), borderBottomWidth: 0.5, borderColor: '#ddd' }}>

                                            <View style={{ width: moderateScale(150), paddingHorizontal: moderateScale(10) }}>
                                                <Text style={{ color: '#0F172B', fontWeight: '500', fontSize: moderateScale(14) }}>{item.name}</Text>
                                            </View>

                                            <View style={{ width: moderateScale(120), alignItems: 'center' }}>
                                                <Text style={{ color: '#0F172B', fontSize: moderateScale(14) }}>{parseFloat(item.target).toFixed(2)}</Text>
                                            </View>

                                            <View style={{ width: moderateScale(120), alignItems: 'center' }}>
                                                <Text style={{ color: '#0F172B', fontWeight: '500', fontSize: moderateScale(14) }}>{parseFloat(item.achievement).toFixed(2)}</Text>
                                            </View>

                                            <View style={{ width: moderateScale(150), alignItems: 'center', flexDirection: 'row' }}>
                                                <View style={{ width: 80, height: 10, borderRadius: moderateScale(10), backgroundColor: '#AAA' }}>
                                                    <LinearGradient
                                                        colors={colorSet(item.percentage)}
                                                        style={{ height: 10, width: parseInt(item.percentage) < 100 ? '' + parseInt(item.percentage) + '%' + '' : '100%', borderRadius: moderateScale(10) }}
                                                        start={{ x: 0, y: 0 }}
                                                        end={{ x: 1, y: 1 }} />
                                                </View>
                                                <View style={{ width: moderateScale(10) }} />
                                                <Text style={{ color: colorSet(item.percentage)[1], fontWeight: '600', fontSize: moderateScale(14) }}>{parseFloat(item.percentage).toFixed(0)}%</Text>
                                            </View>

                                        </View>
                                    ))}

                                </View>
                            </ScrollView>
                        </View>
                        <View style={{ height: moderateScale(15) }} />

                    </View>
                </ScrollView>

            </View>

            <PerformanceFilterView
                isVisible={performanceFilterOpen}
                closeLoginPopup={() => setPerformanceFilterOpen(false)}
            />
            <PerformanceCalenderView
                isVisible={performanceCalendarOpen}
                closeLoginPopup={() => setPerformanceCalendarOpen(false)}
                onMonthSelect={handleMonthSelect}
            />
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    );
};

export default NewProductWiseProductScreen;