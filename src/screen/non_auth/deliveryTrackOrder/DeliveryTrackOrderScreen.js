import React, { useState } from 'react'
import { View, Text, ScrollView, TouchableOpacity, StyleSheet, Image, } from 'react-native'
import OrderCard from './OrderCard'
import SafeView from '../../../helper/SafeView'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Colors } from '../../../assets/Colors'
import { Icons } from '../../../assets/Icons'

const DeliveryTrackOrderScreen = ({ navigation }) => {
  const [orders, setOrders] = useState([])
  const [expandedId, setExpandedId] = useState(null)

  const handleToggle = (id) => {
    setExpandedId((prev) => (prev === id ? null : id))
  }

  const handleShowDetails = (order) => {
    navigation.navigate('DeliveryTrackOrderDetails', { order, setOrders })
  }

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
                  <Image style={{ height: 16, width: 16, tintColor: Colors.main }} source={Icons.Calender} />
                  <Text style={styles.dateText}>{date}</Text>
                </TouchableOpacity>
              ))}
            </View>
          </View>
          {orders.map((order) => (
            <OrderCard key={order.id} order={order} expanded={expandedId === order.id} onToggle={() => handleToggle(order.id)} onShowDetails={() => handleShowDetails(order)} />
          ))}
          <View style={styles.bottomPad} />
        </ScrollView>
      </View>
    </SafeView>
  )
}

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
})

export default DeliveryTrackOrderScreen