import React, { useCallback, useEffect, useState } from 'react';
import { View, Text, ScrollView, StyleSheet, SafeAreaView, Dimensions, TouchableWithoutFeedback, } from 'react-native';
import SafeView from '../../../helper/SafeView';
import { Colors } from '../../../assets/Colors';
import SbsHeaderView from '../../../common/SbsHeaderView';
import { useNavigation } from '@react-navigation/native';
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView';
import Loader from '../../../common/Loader';
import UrlStorage from '../../../storage/UrlStorage';
import moment from 'moment';
import ReactNativeModal from 'react-native-modal';
import PerformanceCalenderView from '../../../common/PerformanceCalenderView';
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi';
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView';

const { width: SCREEN_WIDTH } = Dimensions.get('window');

const COLORS = {
  purple50: '#EEEDFE',
  purple800: '#3C3489',
  green50: '#EAF3DE',
  green800: '#3B6D11',
  red50: '#FCEBEB',
  red800: '#A32D2D',
  amber50: '#FAEEDA',
  amber800: '#854F0B',
  gray100: '#D3D1C7',
  gray800: '#444441',
  background: '#FFFFFF',
  backgroundSecondary: '#F5F5F3',
  textPrimary: '#1A1A18',
  textSecondary: '#6B6B68',
  border: 'rgba(0,0,0,0.1)',
};
const monthMap = {
  Jan: "01", Feb: "02", Mar: "03", Apr: "04",
  May: "05", Jun: "06", Jul: "07", Aug: "08",
  Sep: "09", Oct: "10", Nov: "11", Dec: "12",
};
const PerformanceGraphSLCT = (props) => {
  const navigation = useNavigation()

  const [data, setData] = useState();
  const [loading, setLoading] = useState(true);
  const [authChecker, setAuthChecker] = useState(false);
  const [bars, setBars] = useState([
    { label: 'LM to date', value: 0 },
    { label: 'Last month', value: 0 },
    { label: 'LY same mo.', value: 0 },
    { label: 'Current', value: 0, highlight: true },
  ]);
  const [performanceCalendarOpen, setPerformanceCalendarOpen] = useState(false)

  useEffect(() => {
    requestForGraphData('2025-26', '03')
  }, []);

  const fetchAuthData = async () => {
    var a = await AuthCheckingApi();
    if (!a) {
      setAuthChecker(true)
      setLoading(false)
    }
  }

  const requestForGraphData = async (fy, fm) => {
    const myHeaders = new Headers();
    myHeaders.append("accept", "application/json");
    myHeaders.append("X-API-Key", "11876c3c8741becbeb8fc03cda195484faf212bc2e6b235173569be36f1fcdd4");

    const requestOptions = {
      method: "GET",
      headers: myHeaders,
      redirect: "follow"
    };
    var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }

    var emp_code = UrlStorage.ParameterList.BasicData.user_type != 'broker' ? UrlStorage?.ParameterList?.BasicData?.emp_id : UrlStorage.ParameterList.BasicData.customerDetails.SAP_code

    fetch("https://slctapi.myvtd.site/api/v1/bi/analytics/" + emp_code + "?financial_year=" + fy + "&financial_month=" + fm, requestOptions)
      .then((response) => response.json())
      .then((result) => {

        setData(result)
        var arr = [
          { label: 'LM to date', value: result.lm_till_date_qty_mt },
          { label: 'Last month', value: result.last_month_qty_mt },
          { label: 'LY same mo.', value: result.ly_same_month },
          { label: 'Current', value: result.current_month_qty_mt, highlight: true },
        ]

        setBars(arr)
        setLoading(false)

      })
      .catch((error) => { });
  }

  function formatName(name) {
    return name
      .toLowerCase()
      .split(' ')
      .map(w => w.charAt(0).toUpperCase() + w.slice(1))
      .join(' ');
  }

  function getInitials(name) {
    return name
      .split(' ')
      .map(w => w[0])
      .join('')
      .slice(0, 3);
  }

  function formatDate(iso) {
    const d = new Date(iso);
    return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
  }

  function GrowthBadge({ value }) {
    const positive = value >= 0;
    return (
      <View style={[styles.growthBadge, { backgroundColor: positive ? COLORS.green50 : COLORS.red50 }]}>
        <Text style={[styles.growthText, { color: positive ? COLORS.green800 : COLORS.red800 }]}>
          {positive ? '+' : ''}{value.toFixed(1)}%
        </Text>
      </View>
    );
  }

  // Simple bar chart using plain Views
  function MiniBarChart() {
    const max = Math.max(...bars.map(b => b.value));

    return (
      <View>
        <View style={styles.barChartContainer}>
          {bars.map((bar, i) => (
            <View key={i} style={styles.barColumn}>
              <Text style={styles.barValue}>{bar.value}</Text>
              <View style={styles.barTrack}>
                <View
                  style={[
                    styles.barFill,
                    {
                      height: `${(bar.value / max) * 100}%`,
                      backgroundColor: bar.highlight ? COLORS.purple800 : 'rgba(83,74,183,0.35)',
                    },
                  ]}
                />
              </View>
              <Text style={styles.barLabel}>{bar.label}</Text>
            </View>
          ))}
        </View>
      </View>
    );
  }

  const name = formatName(data?.CUSTOMER_NAME ?? '');
  const initials = getInitials(data?.CUSTOMER_NAME ?? '');

  const handleMonthSelect = useCallback((item) => {
    const month = monthMap[item.month];
    const newSelected = item.year.toString().replace('FY ', '').replace('-20', '-');
    setLoading(true)
    requestForGraphData(newSelected, month)
  }, []);

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <View style={{ width: '100%', height: '100%' }}>
        <SBSCommonHeaderView
          title="Performance Graph SLCT"
          backPath=" "
          Filter={true}
          navigation={navigation}
          props={props}
          handlePerformanceFilterOpen={() => setPerformanceCalendarOpen(true)}
        />
        {loading ? null : <ScrollView style={styles.scroll} contentContainerStyle={styles.container} showsVerticalScrollIndicator={false} >
          {/* Header */}
          <View style={styles.card}>
            <View style={styles.profileRow}>
              <View style={styles.avatar}>
                <Text style={styles.avatarText}>{initials}</Text>
              </View>
              <View style={styles.profileInfo}>
                <Text style={styles.customerName}>{name}</Text>
                <Text style={styles.customerId}>#{data?.CUSTOMER_CODE}</Text>
              </View>
              <View style={styles.statusBadge}>
                <Text style={styles.statusText}>{data?.CUSTOMER_STATUS}</Text>
              </View>
            </View>
            <View style={styles.divider} />
            <View style={styles.dateRow}>
              <Text style={styles.dateText}>Since {formatDate(data?.CUSTOMER_CREATION_DATE)}</Text>
              <Text style={styles.dateDot}>·</Text>
              <Text style={styles.dateText}>Last lift: {formatDate(data?.last_lifting_date)}</Text>
            </View>
          </View>

          {/* Lifetime & YTD */}
          <View style={styles.metricGrid}>
            <View style={[styles.metricCard, { marginRight: 6 }]}>
              <Text style={styles.metricLabel}>LIFETIME VOLUME</Text>
              <Text style={styles.metricValue}>{data?.lifetime_value_qty_mt?.toLocaleString()}</Text>
              <Text style={styles.metricUnit}>MT total</Text>
            </View>
            <View style={[styles.metricCard, { marginLeft: 6 }]}>
              <Text style={styles.metricLabel}>YTD VOLUME</Text>
              <Text style={styles.metricValue}>{data?.ytd_qty_mt?.toLocaleString()}</Text>
              <Text style={styles.metricUnit}>MT this year</Text>
            </View>
          </View>

          {/* Current Month */}
          <View style={styles.card}>
            <Text style={styles.sectionLabel}>CURRENT MONTH</Text>
            <View style={styles.currentMonthRow}>
              <View>
                <Text style={styles.bigNumber}>
                  {data?.current_month_qty_mt}
                  <Text style={styles.bigNumberUnit}> MT</Text>
                </Text>
              </View>
              <View style={styles.avgBlock}>
                <Text style={styles.avgLabel}>6-month avg</Text>
                <Text style={styles.avgValue}>{data?.last_6m_avg?.toFixed(1)} MT</Text>
              </View>
            </View>

            <View style={styles.growthRow}>
              <View style={styles.growthCell}>
                <Text style={styles.growthCellLabel}>vs LM full</Text>
                <GrowthBadge value={data?.grth_wrt_lm_full_month ?? 0} />
              </View>
              <View style={styles.growthDivider} />
              <View style={styles.growthCell}>
                <Text style={styles.growthCellLabel}>vs LM to date</Text>
                <GrowthBadge value={data?.growth_wrt_lm_till_date ?? 0} />
              </View>
              <View style={styles.growthDivider} />
              <View style={styles.growthCell}>
                <Text style={styles.growthCellLabel}>vs LY same mo.</Text>
                <GrowthBadge value={data?.growth_wrt_lysm_full_month ?? 0} />
              </View>
            </View>
          </View>

          {/* Bar Chart */}
          <View style={styles.card}>
            <Text style={styles.sectionLabel}>VOLUME TREND</Text>
            <MiniBarChart />
          </View>

          {/* Comparison Table */}
          <View style={styles.card}>
            <Text style={styles.sectionLabel}>PERIOD COMPARISON</Text>
            {[
              { label: 'LM till date', value: `${data?.lm_till_date_qty_mt} MT` },
              { label: 'Last month (full)', value: `${data?.last_month_qty_mt} MT` },
              { label: 'LY same month (full)', value: `${data?.ly_same_month} MT` },
              { label: '6-month average', value: `${parseFloat(data?.last_6m_avg).toFixed(1)} MT` },
            ].map((row, i, arr) => (
              <View key={i} style={[styles.tableRow, i < arr.length - 1 && styles.tableRowBorder]}>
                <Text style={styles.tableLabel}>{row.label}</Text>
                <Text style={styles.tableValue}>{row.value}</Text>
              </View>
            ))}
          </View>
        </ScrollView>}
      </View>
      {loading ? <Loader /> : null}

      <PerformanceCalenderView
        isVisible={performanceCalendarOpen}
        closeLoginPopup={() => setPerformanceCalendarOpen(false)}
        onMonthSelect={handleMonthSelect}
      />
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  );
}

export default PerformanceGraphSLCT

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: '#F0EEE8', },
  scroll: { flex: 1, },
  container: { padding: 16, paddingBottom: 32, },

  // Card
  card: { backgroundColor: COLORS.background, borderRadius: 16, padding: 16, marginBottom: 12, borderWidth: 0.5, borderColor: COLORS.border, },

  // Profile header
  profileRow: { flexDirection: 'row', alignItems: 'center', gap: 12, marginBottom: 12, },
  avatar: { width: 48, height: 48, borderRadius: 24, backgroundColor: COLORS.purple50, alignItems: 'center', justifyContent: 'center', },
  avatarText: { fontSize: 14, fontWeight: '500', color: COLORS.purple800, },
  profileInfo: { flex: 1, },
  customerName: { fontSize: 16, fontWeight: '500', color: COLORS.textPrimary, },
  customerId: { fontSize: 12, color: COLORS.textSecondary, marginTop: 2, },
  statusBadge: { backgroundColor: COLORS.green50, paddingHorizontal: 10, paddingVertical: 4, borderRadius: 20, },
  statusText: { fontSize: 11, fontWeight: '500', color: COLORS.green800, },
  divider: { height: 0.5, backgroundColor: COLORS.border, marginBottom: 10, },
  dateRow: { flexDirection: 'row', gap: 6, alignItems: 'center', },
  dateText: { fontSize: 12, color: COLORS.textSecondary, },
  dateDot: { fontSize: 12, color: COLORS.textSecondary, },

  // Metric grid
  metricGrid: { flexDirection: 'row', marginBottom: 12, },
  metricCard: { flex: 1, backgroundColor: COLORS.backgroundSecondary, borderRadius: 12, padding: 14, },
  metricLabel: { fontSize: 10, color: COLORS.textSecondary, letterSpacing: 0.6, marginBottom: 4, },
  metricValue: { fontSize: 24, fontWeight: '500', color: COLORS.textPrimary, },
  metricUnit: { fontSize: 11, color: COLORS.textSecondary, marginTop: 2, },

  // Section label
  sectionLabel: { fontSize: 10, fontWeight: '500', color: COLORS.textSecondary, letterSpacing: 0.7, marginBottom: 12, },

  // Current month
  currentMonthRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-end', marginBottom: 14, },
  bigNumber: { fontSize: 36, fontWeight: '500', color: COLORS.textPrimary, lineHeight: 40, },
  bigNumberUnit: { fontSize: 16, color: COLORS.textSecondary, fontWeight: '400', },
  avgBlock: { alignItems: 'flex-end', },
  avgLabel: { fontSize: 11, color: COLORS.textSecondary, marginBottom: 2, },
  avgValue: { fontSize: 16, fontWeight: '500', color: COLORS.textPrimary, },

  // Growth row
  growthRow: { flexDirection: 'row', backgroundColor: COLORS.backgroundSecondary, borderRadius: 10, padding: 12, alignItems: 'center', },
  growthCell: { flex: 1, alignItems: 'center', gap: 6, },
  growthCellLabel: { fontSize: 10, color: COLORS.textSecondary, textAlign: 'center', },
  growthBadge: { paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6, },
  growthText: { fontSize: 13, fontWeight: '500', },
  growthDivider: { width: 0.5, height: 36, backgroundColor: COLORS.border, },

  // Bar chart
  barChartContainer: { flexDirection: 'row', height: 110, alignItems: 'flex-end', gap: 8, marginBottom: 8, },
  barColumn: { flex: 1, alignItems: 'center', height: '100%', },
  barValue: { fontSize: 10, color: COLORS.textSecondary, marginBottom: 4, },
  barTrack: { flex: 1, width: '60%', justifyContent: 'flex-end', },
  barFill: { width: '100%', borderRadius: 4, minHeight: 4, },
  barLabel: { fontSize: 9, color: COLORS.textSecondary, marginTop: 6, textAlign: 'center', },

  // Table
  tableRow: { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: 10, },
  tableRowBorder: { borderBottomWidth: 0.5, borderBottomColor: COLORS.border, },
  tableLabel: { fontSize: 13, color: COLORS.textSecondary, },
  tableValue: { fontSize: 13, fontWeight: '500', color: COLORS.textPrimary, },
});