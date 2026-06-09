import React, { useEffect, useState, useCallback, useRef } from 'react';
import { View, Text, ScrollView, TouchableOpacity, Animated, Dimensions, } from 'react-native';
import SafeView from '../../../helper/SafeView';
import { moderateScale } from '../../../helper/Window';
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView';
import { Colors } from '../../../assets/Colors';
import PerformanceFilterView from '../../../common/PerformanceFilterView';
import PerformanceCalenderView from '../../../common/PerformanceCalenderView';
import UrlStorage from '../../../storage/UrlStorage';
import { useNavigation } from '@react-navigation/native';

const { height: SCREEN_HEIGHT } = Dimensions.get('window');

const monthMap = {
  Jan: "01", Feb: "02", Mar: "03", Apr: "04",
  May: "05", Jun: "06", Jul: "07", Aug: "08",
  Sep: "09", Oct: "10", Nov: "11", Dec: "12",
};

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

const AnimatedBar = ({
  tgtValue, achValue, maxValue,
  barWidth, chartHeight, index, onPress,
}) => {
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
          <Animated.View style={{ width: barWidth, height: tgtPx, backgroundColor: '#33B5E6', borderTopLeftRadius: 3, borderTopRightRadius: 3, }} />
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
          <Animated.View style={{ width: barWidth, height: achPx, backgroundColor: '#E41B14', borderTopLeftRadius: 3, borderTopRightRadius: 3, }} />
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
const ProductWiseProductScreen = (props) => {
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
    setSelected(formatted);
    const res = extractYearMonth(formatted);
    if (res) getGraphData(res.year, res.month);
  }, []);

  const getData = useCallback((selectedValue) => {
    const res = extractYearMonth(selectedValue);
    if (!res) return;
    getGraphData(res.year, res.month);
  }, []);

  const getGraphData = async (year, month) => {
    setLoading(true);
    setChartData([]);

    const baseUrl = UrlStorage.BaseUrlList.Saathi.base_url_saathi.replace(/\/$/, '');
    const apiPath = UrlStorage.NonAuthURL.Saathi.DownloadDatabaseAPI.item_wise_target_achievement_SAP_data.replace(/^\//, '');
    const url = `${baseUrl}/${apiPath}` + `?customer_code=${UrlStorage.ParameterList.BasicData.user_type == 'broker' ? UrlStorage.ParameterList.BasicData.customerDetails.SAP_code : UrlStorage.ParameterList.BasicData.emp_id}` + `&year=${year}&month=${month}`;

    try {
      const response = await fetch(url, { method: "GET" });
      if (!response.ok) { setChartData([]); return; }
      const result = await response.json();

      if (result?.target_ach_data?.length > 0) {
        const filtered = result.target_ach_data.filter(
          item => !item.itemcode?.toLowerCase()?.includes("total")
        );
        const allValues = filtered.flatMap(item => [
          parseFloat(item.TGTQTY) || 0,
          parseFloat(item.ACHQTY) || 0,
        ]);
        const rawMax = Math.max(...allValues, 1);
        // FIX 3: nice round max with breathing room
        setMaxValue(getNiceMax(rawMax));

        const formatted = filtered.map(item => ({
          label: item.itemname.replace('STAR CEMENT', '').replace(/\bSTAR\b/g, '').trim(),
          tgt: parseFloat(item.TGTQTY) || 0,
          ach: parseFloat(item.ACHQTY) || 0,
        }));
        setAllData(filtered);
        setChartData(formatted);
      } else {
        setChartData([]);
      }
    } catch (e) {
      setChartData([]);
    } finally {
      setLoading(false);
    }
  };

  const handleMonthSelect = useCallback((item) => {
    const month = monthMap[item.month];
    const newSelected = Number(month) > 3 ? `${item.month} ${item.year.replace("FY ", "").split("-")[0]}` : `${item.month} ${item.year.replace("FY ", "").split("-")[1]}`;
    setSelected(newSelected);
    setTimeout(() => getData(newSelected), 80);
  }, [getData]);

  const handleBarScroll = (e) => {
    const x = e.nativeEvent.contentOffset.x;
    labelScrollRef.current?.scrollTo({ x, animated: false });
  };

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <View style={{ flex: 1 }}>
        <SBSCommonHeaderView
          title={selected}
          backPath=" "
          Filter={false}
          Calendar={true}
          navigation={navigation}
          props={props}
          handlePerformanceFilterOpen={() => setPerformanceFilterOpen(true)}
          handlePerformanceCalendarOpen={() => setPerformanceCalendarOpen(true)}
        />

        <View style={{ flex: 1, padding: moderateScale(12) }}>
          <View style={{ flexDirection: 'row', justifyContent: 'center', alignItems: 'center', height: LEGEND_H, }}>
            {[
              { color: '#33B5E6', label: 'Target' },
              { color: '#E41B14', label: 'Achievement' },
            ].map(({ color, label }) => (
              <View key={label} style={{ flexDirection: 'row', alignItems: 'center', marginHorizontal: 10 }}>
                <View style={{ width: 14, height: 14, borderRadius: 3, backgroundColor: color, marginRight: 6 }} />
                <Text style={{ fontSize: 12, color: '#333', fontWeight: '500' }}>{label}</Text>
              </View>
            ))}
          </View>

          <View style={{ flex: 1, backgroundColor: '#FFF', borderWidth: 1, borderColor: '#DCDDDF', borderRadius: moderateScale(10), paddingTop: moderateScale(10), paddingBottom: 6, paddingHorizontal: moderateScale(6), }}>
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
                            chartHeight={CHART_HEIGHT}
                            onPress={() => navigation.navigate('ProductWisePerformanceDetails', { allData })} />
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
        </View>
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
    </SafeView>
  );
};

export default ProductWiseProductScreen;