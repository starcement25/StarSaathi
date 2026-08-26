import React, { useState, useMemo } from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  StyleSheet,
  Platform,
  Image,
} from 'react-native';
import DateTimePicker from '@react-native-community/datetimepicker';
import SafeView from '../../../helper/SafeView';
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView';
import { Colors } from '../../../assets/Colors';
import { Icons } from '../../../assets/Icons';

// ─── Dummy Data ───────────────────────────────────────────────────────────────
const DUMMY_COMPLAINTS = [
  {
    id: '1',
    orderNo: 'ORDER-1',
    orderDate: '12-05-2026',
    invoiceNo: 'INVOICE-1',
    orderQty: '1250 MT',
    dispatchQty: '1200 MT',
    complainStatus: 'Under Review',
    complain: 'Shortage in quantity',
    solveRemarks: null,
    _dateObj: new Date(2026, 4, 12), // for filtering
  },
  {
    id: '2',
    orderNo: 'ORDER-2',
    orderDate: '11-05-2026',
    invoiceNo: 'INVOICE-2',
    orderQty: '1000 MT',
    dispatchQty: '950 MT',
    complainStatus: 'Resolved',
    complain: 'Damaged bags on delivery',
    solveRemarks: 'Replacement arranged for 5 bags.',
    _dateObj: new Date(2026, 4, 11),
  },
  {
    id: '3',
    orderNo: 'ORDER-3',
    orderDate: '10-05-2026',
    invoiceNo: 'INVOICE-3',
    orderQty: '800 MT',
    dispatchQty: '800 MT',
    complainStatus: 'Pending',
    complain: 'Delayed delivery beyond ETA',
    solveRemarks: null,
    _dateObj: new Date(2026, 4, 10),
  },
  {
    id: '4',
    orderNo: 'ORDER-4',
    orderDate: '09-05-2026',
    invoiceNo: 'INVOICE-4',
    orderQty: '600 MT',
    dispatchQty: '580 MT',
    complainStatus: 'Resolved',
    complain: 'Wrong product type delivered',
    solveRemarks: 'Correct product re-dispatched on 13-05-2026.',
    _dateObj: new Date(2026, 4, 9),
  },
  {
    id: '5',
    orderNo: 'ORDER-5',
    orderDate: '08-05-2026',
    invoiceNo: 'INVOICE-5',
    orderQty: '1500 MT',
    dispatchQty: '1450 MT',
    complainStatus: 'Under Review',
    complain: 'Shortage in quantity',
    solveRemarks: null,
    _dateObj: new Date(2026, 4, 8),
  },
  {
    id: '6',
    orderNo: 'ORDER-6',
    orderDate: '07-05-2026',
    invoiceNo: 'INVOICE-6',
    orderQty: '2000 MT',
    dispatchQty: '1980 MT',
    complainStatus: 'Rejected',
    complain: 'Vehicle arrived late',
    solveRemarks: 'Delay due to road blockage. Not actionable.',
    _dateObj: new Date(2026, 4, 7),
  },
];

// ─── Status badge config ──────────────────────────────────────────────────────
const STATUS_STYLE = {
  'Under Review': { bg: '#FFF3E0', text: '#E65100', border: '#FFCC80' },
  'Resolved':     { bg: '#E8F5E9', text: '#2E7D32', border: '#A5D6A7' },
  'Pending':      { bg: '#FFF8E1', text: '#F57F17', border: '#FFE082' },
  'Rejected':     { bg: '#FFEBEE', text: '#C62828', border: '#EF9A9A' },
  default:        { bg: '#F5F5F5', text: '#555',    border: '#E0E0E0' },
};

// ─── Helpers ──────────────────────────────────────────────────────────────────
const formatDate = (date) => {
  const dd   = String(date.getDate()).padStart(2, '0');
  const mm   = String(date.getMonth() + 1).padStart(2, '0');
  const yyyy = date.getFullYear();
  return `${dd}-${mm}-${yyyy}`;
};

// ─── Complaint Card ───────────────────────────────────────────────────────────
const ComplaintCard = ({ item }) => {
  const statusStyle = STATUS_STYLE[item.complainStatus] || STATUS_STYLE.default;

  const Row = ({ label, value, isStatus = false, isChevron = false }) => (
    <View style={cardStyles.row}>
      <Text style={cardStyles.label}>{label}</Text>
      <View style={cardStyles.valueWrap}>
        {isStatus ? (
          <View style={[cardStyles.statusBadge, { backgroundColor: statusStyle.bg, borderColor: statusStyle.border }]}>
            <Text style={[cardStyles.statusText, { color: statusStyle.text }]}>{value}</Text>
          </View>
        ) : (
          <Text style={cardStyles.value}>{value || '–'}</Text>
        )}
        {isChevron && (
          <Text style={cardStyles.chevron}>›</Text>
        )}
      </View>
    </View>
  );

  return (
    <View style={cardStyles.card}>
      <Row label="Order No."       value={item.orderNo} />
      <Row label="Order Date"      value={item.orderDate} />
      <Row label="Invoice No."     value={item.invoiceNo} />
      <Row label="Order Qty"       value={item.orderQty} />
      <Row label="Dispatch Qty"    value={item.dispatchQty} isChevron />
      <Row label="Complain Status" value={item.complainStatus} isStatus />
      <Row label="Complain"        value={item.complain} />
      <Row label="Solve Remarks"   value={item.solveRemarks} />
    </View>
  );
};

const cardStyles = StyleSheet.create({
  card: {
    backgroundColor: '#fff',
    borderRadius: 12,
    marginHorizontal: 12,
    marginBottom: 12,
    paddingHorizontal: 16,
    paddingVertical: 4,
    elevation: 1,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.06,
    shadowRadius: 3,
  },
  row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 11,
    borderBottomWidth: 1,
    borderBottomColor: '#F5F5F5',
  },
  label: {
    fontSize: 13,
    color: '#666',
    fontWeight: '400',
    flex: 1,
  },
  valueWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'flex-end',
    flex: 1.2,
  },
  value: {
    fontSize: 13,
    fontWeight: '700',
    color: '#111',
    textAlign: 'right',
    flexShrink: 1,
  },
  chevron: {
    fontSize: 18,
    color: '#888',
    marginLeft: 4,
    fontWeight: '300',
  },
  statusBadge: {
    borderWidth: 1,
    borderRadius: 20,
    paddingHorizontal: 12,
    paddingVertical: 4,
  },
  statusText: {
    fontSize: 12,
    fontWeight: '700',
  },
});

// ─── Main Screen ──────────────────────────────────────────────────────────────
const DeliveryComplainScreen = ({ navigation }) => {
  const defaultFrom = new Date(2026, 4, 9);  // 09-05-2026
  const defaultTo   = new Date(2026, 4, 16); // 16-05-2026

  const [fromDate,     setFromDate]     = useState(defaultFrom);
  const [toDate,       setToDate]       = useState(defaultTo);
  const [showFromPicker, setShowFromPicker] = useState(false);
  const [showToPicker,   setShowToPicker]   = useState(false);

  // Filter list by date range
  const filteredComplaints = useMemo(() => {
    return DUMMY_COMPLAINTS.filter(item => {
      const d = item._dateObj;
      return d >= fromDate && d <= toDate;
    });
  }, [fromDate, toDate]);

  const handleFromChange = (event, selectedDate) => {
    setShowFromPicker(Platform.OS === 'ios');
    if (selectedDate) {
      // from date must not exceed toDate
      if (selectedDate <= toDate) {
        setFromDate(selectedDate);
      }
    }
  };

  const handleToChange = (event, selectedDate) => {
    setShowToPicker(Platform.OS === 'ios');
    if (selectedDate) {
      // to date must not be before fromDate
      if (selectedDate >= fromDate) {
        setToDate(selectedDate);
      }
    }
  };

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <View style={{ flex: 1 }}>
        <SBSCommonHeaderView title="Delivery Complain" backPath=" " />

        <ScrollView
          style={styles.container}
          showsVerticalScrollIndicator={false}
          contentContainerStyle={styles.contentContainer}
        >
          {/* ── Date Range Filter ──────────────────────────────────────────── */}
          <View style={styles.dateSection}>
            <Text style={styles.dateLabel}>Select Date Range</Text>
            <View style={styles.dateRow}>
              {/* From Date */}
              <TouchableOpacity
                style={styles.dateBox}
                onPress={() => setShowFromPicker(true)}
                activeOpacity={0.75}
              >
                <Image
                  style={styles.calendarIcon}
                  source={Icons.Calender}
                />
                <Text style={styles.dateText}>{formatDate(fromDate)}</Text>
              </TouchableOpacity>

              {/* To Date */}
              <TouchableOpacity
                style={styles.dateBox}
                onPress={() => setShowToPicker(true)}
                activeOpacity={0.75}
              >
                <Image
                  style={styles.calendarIcon}
                  source={Icons.Calender}
                />
                <Text style={styles.dateText}>{formatDate(toDate)}</Text>
              </TouchableOpacity>
            </View>
          </View>

          {/* ── Complaint List ─────────────────────────────────────────────── */}
          {filteredComplaints.length > 0 ? (
            filteredComplaints.map(item => (
              <ComplaintCard key={item.id} item={item} />
            ))
          ) : (
            <View style={styles.emptyState}>
              <Text style={styles.emptyIcon}>📋</Text>
              <Text style={styles.emptyTitle}>No Complaints Found</Text>
              <Text style={styles.emptySubtitle}>
                No complaints found for the selected date range.
              </Text>
            </View>
          )}

          <View style={{ height: 24 }} />
        </ScrollView>

        {/* Date Pickers */}
        {showFromPicker && (
          <DateTimePicker
            value={fromDate}
            mode="date"
            display={Platform.OS === 'ios' ? 'spinner' : 'default'}
            onChange={handleFromChange}
            maximumDate={toDate}
          />
        )}
        {showToPicker && (
          <DateTimePicker
            value={toDate}
            mode="date"
            display={Platform.OS === 'ios' ? 'spinner' : 'default'}
            onChange={handleToChange}
            minimumDate={fromDate}
          />
        )}
      </View>
    </SafeView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  contentContainer: {
    paddingTop: 4,
  },

  // ── Date section ──
  dateSection: {
    paddingHorizontal: 12,
    paddingVertical: 14,
  },
  dateLabel: {
    fontSize: 13,
    fontWeight: '700',
    color: '#111',
    marginBottom: 10,
  },
  dateRow: {
    flexDirection: 'row',
    gap: 10,
  },
  dateBox: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: '#fff',
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#E0E0E0',
    paddingHorizontal: 12,
    paddingVertical: 11,
  },
  calendarIcon: {
    height: 18,
    width: 18,
    tintColor: Colors.main,
  },
  dateText: {
    fontSize: 13,
    color: '#111',
    fontWeight: '500',
  },

  // ── Empty state ──
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
    paddingHorizontal: 32,
  },
  emptyIcon: {
    fontSize: 48,
    marginBottom: 14,
  },
  emptyTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: '#333',
    marginBottom: 6,
  },
  emptySubtitle: {
    fontSize: 13,
    color: '#888',
    textAlign: 'center',
    lineHeight: 20,
  },
});

export default DeliveryComplainScreen;