import React, { useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  StyleSheet,
  SafeAreaView,
  StatusBar,
  Image,
} from 'react-native';
import OrderCard from './OrderCard';
import SafeView from '../../../helper/SafeView';
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView';
import { Colors } from '../../../assets/Colors';
import { Icons } from '../../../assets/Icons';


export const ORDERS = [
  {
    id: 'ORDER-1',
    productType: 'PPC',
    quantity: '1.25 MT',
    status: 'Order Dispatched',
    step: 2,
    totalSteps: 5,
    stepLabel: 'Dispatched',
    orderDate: '12-05-2026',
    vehicleNo: 'WB1254',
    invoice: 'INVOICE-1',
    product: 'TEST CEMENT',
    totalQty: '1.25 MT',
    shipmentQty: '1.250 MT',
    shipToParty: 'UDAYANSH TRADING, HAIBARGAON - 78200...',
    orderFrom: 'STAR SAATHI',
    orderType: 'depot',
    plantName: 'DCNEL, NAGAON',
    invoiceNumber: 'INVOICE-1',
    dispatchTime: '19-May-2026 10:55 AM',
    insidePlantGate: '19-May-2026 10:54 AM',
    driverName: 'HIKMAT',
    driverPhone: '9365251206',
    epodRequired: true,
    deliveryReport: null,
    timeline: [
      { label: 'ORDER PLACED', time: '19-May-2026 10:29 AM', done: true },
      { label: 'ORDER CONFIRM', time: '19-May-2026 10:29 AM', done: true },
      { label: 'VEHICLE ALLOCATED', time: '', done: false, vehicleNo: '' },
      { label: 'INVOICE GENERATED', time: '', done: false },
      { label: 'DELIVERED', time: '--', done: false },
    ],
  },
  {
    id: 'ORDER-2',
    productType: 'PPC',
    quantity: '1.00 MT',
    status: 'In Transit',
    step: 3,
    totalSteps: 5,
    stepLabel: 'On The Way',
    orderDate: '10-05-2026',
    vehicleNo: 'AS01AC7842',
    invoice: 'INVOICE-2',
    product: 'TEST CEMENT',
    totalQty: '1.00 MT',
    shipmentQty: '1.000 MT',
    shipToParty: 'RAMKRISHNA TRADERS, GUWAHATI - 78100...',
    orderFrom: 'STAR SAATHI',
    orderType: 'depot',
    plantName: 'DCNEL, NAGAON',
    invoiceNumber: 'INVOICE-2',
    dispatchTime: '18-May-2026 08:30 AM',
    insidePlantGate: '18-May-2026 08:20 AM',
    driverName: 'BIKRAM DAS',
    driverPhone: '9435012847',
    epodRequired: true,
    deliveryReport: {
      vehicleNo: 'AS 01 AC 7842',
      vehicleType: '10-Wheeler Truck',
      driverName: 'Bikram Das',
      driverPhone: '+91 94350 12847',
      confirmedBy: 'Assam Road Lines Pvt. Ltd.',
      orderedQty: '10 MT',
      orderedBags: 200,
      confirmedQty: '9.5 MT',
      confirmedBags: 190,
      shortageQty: '-0.5 MT (5.0%)',
      shortageBags: 10,
      remark:
        'Road condition near Nagaon was poor, 10 bags damaged during transit. Unloaded 190 bags in good condition.',
      deliveredOn: '24 May 2026, 07:22 AM',
    },
    timeline: [
      { label: 'ORDER PLACED', time: '18-May-2026 07:00 AM', done: true },
      { label: 'ORDER CONFIRM', time: '18-May-2026 07:05 AM', done: true },
      { label: 'VEHICLE ALLOCATED', time: '18-May-2026 07:45 AM', done: true, vehicleNo: 'AS01AC7842' },
      { label: 'INVOICE GENERATED', time: '', done: false },
      { label: 'DELIVERED', time: '', done: false },
    ],
  },
  {
    id: 'ORDER-3',
    productType: 'PPC',
    quantity: '0.80 MT',
    status: 'Delivered',
    step: 5,
    totalSteps: 5,
    stepLabel: 'Delivered',
    orderDate: '08-05-2026',
    vehicleNo: 'WB5678',
    invoice: 'INVOICE-3',
    product: 'TEST CEMENT',
    totalQty: '0.80 MT',
    shipmentQty: '0.800 MT',
    shipToParty: 'MAHESH CEMENT STORE, JORHAT - 78500...',
    orderFrom: 'STAR SAATHI',
    orderType: 'depot',
    plantName: 'DCNEL, NAGAON',
    invoiceNumber: 'INVOICE-3',
    dispatchTime: '15-May-2026 09:00 AM',
    insidePlantGate: '15-May-2026 08:50 AM',
    driverName: 'RAVI SHARMA',
    driverPhone: '9876543210',
    epodRequired: true,
    deliveryReport: null,
    timeline: [
      { label: 'ORDER PLACED', time: '15-May-2026 06:00 AM', done: true },
      { label: 'ORDER CONFIRM', time: '15-May-2026 06:10 AM', done: true },
      { label: 'VEHICLE ALLOCATED', time: '15-May-2026 07:00 AM', done: true, vehicleNo: 'WB5678' },
      { label: 'INVOICE GENERATED', time: '15-May-2026 07:00 AM', done: true },
      { label: 'DELIVERED', time: '16-May-2026 03:30 PM', done: true },
    ],
  },
  {
    id: 'ORDER-4',
    productType: 'PPC',
    quantity: '0.60 MT',
    status: 'In Transit',
    step: 4,
    totalSteps: 5,
    stepLabel: 'Dispatched',
    orderDate: '11-05-2026',
    vehicleNo: 'WB9090',
    invoice: 'INVOICE-4',
    product: 'TEST CEMENT',
    totalQty: '0.60 MT',
    shipmentQty: '0.600 MT',
    shipToParty: 'PANKAJ TRADERS, SILCHAR - 78800...',
    orderFrom: 'STAR SAATHI',
    orderType: 'depot',
    plantName: 'DCNEL, NAGAON',
    invoiceNumber: 'INVOICE-4',
    dispatchTime: '19-May-2026 11:00 AM',
    insidePlantGate: '19-May-2026 10:55 AM',
    driverName: 'SURESH KUMAR',
    driverPhone: '9012345678',
    epodRequired: true,
    deliveryReport: null,
    timeline: [
      { label: 'ORDER PLACED', time: '19-May-2026 09:00 AM', done: true },
      { label: 'ORDER CONFIRM', time: '19-May-2026 09:10 AM', done: true },
      { label: 'VEHICLE ALLOCATED', time: '19-May-2026 10:00 AM', done: true, vehicleNo: 'WB9090' },
      { label: 'INVOICE GENERATED', time: '19-May-2026 10:00 AM', done: true },
      { label: 'DELIVERED', time: '--', done: false },
    ],
  },
  {
    id: 'ORDER-5',
    productType: 'PPC',
    quantity: '1.50 MT',
    status: 'Delivered',
    step: 5,
    totalSteps: 5,
    stepLabel: 'Delivered',
    orderDate: '07-05-2026',
    vehicleNo: 'AS02BC1234',
    invoice: 'INVOICE-5',
    product: 'TEST CEMENT',
    totalQty: '1.50 MT',
    shipmentQty: '1.500 MT',
    shipToParty: 'AMIT CEMENT HOUSE, DIBRUGARH - 78600...',
    orderFrom: 'STAR SAATHI',
    orderType: 'depot',
    plantName: 'DCNEL, NAGAON',
    invoiceNumber: 'INVOICE-5',
    dispatchTime: '12-May-2026 10:00 AM',
    insidePlantGate: '12-May-2026 09:50 AM',
    driverName: 'DEEPAK RAI',
    driverPhone: '8765432109',
    epodRequired: false,
    deliveryReport: null,
    timeline: [
      { label: 'ORDER PLACED', time: '12-May-2026 07:00 AM', done: true },
      { label: 'ORDER CONFIRM', time: '12-May-2026 07:15 AM', done: true },
      { label: 'VEHICLE ALLOCATED', time: '12-May-2026 08:30 AM', done: true, vehicleNo: 'AS02BC1234' },
      { label: 'INVOICE GENERATED', time: '12-May-2026 08:30 AM', done: true },
      { label: 'DELIVERED', time: '13-May-2026 02:00 PM', done: true },
    ],
  },
];

const DeliveryTrackOrderScreen = ({ navigation }) => {
  const [orders, setOrders] = useState(ORDERS);
  const [expandedId, setExpandedId] = useState(null);

  const handleToggle = (id) => {
    setExpandedId((prev) => (prev === id ? null : id));
  };

  const handleShowDetails = (order) => {
    // Pass setOrders so the details screen can update status after approval
    navigation.navigate('DeliveryTrackOrderDetails', { order, setOrders });
  };

  return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: '100%', height: '100%' }}>
                <SBSCommonHeaderView title="Delivery Track Order" backPath=" " />

      <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
        <View style={styles.dateRangeSection}>
          <Text style={styles.dateRangeLabel}>Select Date Range</Text>
          <View style={styles.dateRangeRow}>
            {['09-05-2026', '16-05-2026'].map((date, i) => (
              <TouchableOpacity key={i} style={styles.dateBox}>
                <Image style={{height: 16, width: 16, tintColor: Colors.main}} source={Icons.Calender} />
                <Text style={styles.dateText}>{date}</Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>

        {orders.map((order) => (
          <OrderCard
            key={order.id}
            order={order}
            expanded={expandedId === order.id}
            onToggle={() => handleToggle(order.id)}
            onShowDetails={() => handleShowDetails(order)}
          />
        ))}

        <View style={styles.bottomPad} />
      </ScrollView>
      </View>
    </SafeView>
  );
};

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: '#C8102E', },
  header: { backgroundColor: '#C8102E', flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: 16, paddingVertical: 14, },
  headerBack: { padding: 4, },
  headerBackText: { color: '#fff', fontSize: 22, },
  headerTitle: { color: '#fff', fontSize: 17, fontWeight: '700', },
  headerFilter: { padding: 4, },
  headerFilterText: { color: '#fff', fontSize: 20, },
  container: { flex: 1, backgroundColor: '#F5F5F5', paddingHorizontal: 12, },
  dateRangeSection: { marginTop: 14, marginBottom: 12, },
  dateRangeLabel: { fontSize: 13, fontWeight: '700', color: '#111', marginBottom: 8, },
  dateRangeRow: { flexDirection: 'row', gap: 10, },
  dateBox: { flex: 1, backgroundColor: '#fff', borderRadius: 8, borderWidth: 1, borderColor: '#E0E0E0', paddingHorizontal: 12, paddingVertical: 10, flexDirection: 'row', alignItems: 'center', gap: 8, },
  dateIcon: { fontSize: 16, },
  dateText: { fontSize: 13, color: '#111', fontWeight: '500', },
  bottomPad: { height: 24, },
});

export default DeliveryTrackOrderScreen;