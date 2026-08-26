import React from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Image,
} from 'react-native';
import { Colors } from '../../../assets/Colors';
import { Icons } from '../../../assets/Icons';
import { moderateScale } from '../../../helper/Window';

const ProgressBar = ({ step, total }) => (
  <View style={styles.progressRow}>
    {Array.from({ length: total }).map((_, i) => (
      <View
        key={i}
        style={[styles.progressSegment, { backgroundColor: i < step ? Colors.main : '#E0E0E0' }]}
      />
    ))}
  </View>
);

const CheckDot = ({ done }) => (
  <View style={[styles.checkDot, { backgroundColor: done ? '#1a7a3c' : '#E0E0E0' }]}>
    {/* {done && <Text style={styles.checkMark}>✓</Text>} */}
               {done && <Image style={{height: 14, width: 14, tintColor: '#fff'}} source={Icons.CheckNew} />}
    
  </View>
);

const STATUS_COLORS = {
  'Order Dispatched': {
    backgroundColor: '#E3F2FD',
    textColor: '#1565C0',
    borderColor: '#90CAF9',
  },
  'In Transit': {
    backgroundColor: '#FFF8E1',
    textColor: '#F57F17',
    borderColor: '#FFE082',
  },
  Delivered: {
    backgroundColor: '#E8F5E9',
    textColor: '#2E7D32',
    borderColor: '#A5D6A7',
  },
  Cancelled: {
    backgroundColor: '#FFEBEE',
    textColor: '#C62828',
    borderColor: '#EF9A9A',
  },
  Pending: {
    backgroundColor: '#F3E5F5',
    textColor: '#6A1B9A',
    borderColor: '#CE93D8',
  },
};

const getStatusStyle = status =>
  STATUS_COLORS[status] || {
    backgroundColor: '#F5F5F5',
    textColor: '#616161',
    borderColor: '#E0E0E0',
  };

const OrderCard = ({ order, expanded, onToggle, onShowDetails }) => {
const statusStyle = getStatusStyle(order.status);
  return (
    <View style={styles.card}>
      {/* Card Header */}
      <View style={styles.cardHeader}>
        <View style={styles.iconBox}>
           <Image style={{height: 24, width: 24,}} source={Icons.Package} />
        </View>

        <View style={styles.orderInfo}>
                  <Text style={styles.orderId}>{order.id}</Text>
                  <View style={styles.metaRow}>
                      
                      <View style={styles.item}>
                        <View style={styles.dot} />
                          <Text style={styles.metaType}>{order.productType}</Text>
                      </View>
                      <Text style={styles.metaQty}>{order.quantity}</Text>
                  </View>
        </View>

        <View style={[styles.statusBadge, {  backgroundColor: statusStyle.backgroundColor,
      borderColor: statusStyle.borderColor,}]}>
          <Text style={[styles.statusText, { color:statusStyle.textColor }]}>{order.status}</Text>
        </View>

        <TouchableOpacity onPress={onToggle} style={styles.chevron}>
          <Image style={{height: 16, width: 16,}} source={expanded ? Icons.ArrowUp : Icons.ArrowDown} />
        </TouchableOpacity>
      </View>

      {/* Progress */}
      <ProgressBar step={order.step} total={order.totalSteps} />
      <Text style={styles.stepLabel}>
        Step {order.step} of {order.totalSteps} · {order.stepLabel}
      </Text>

      {/* Expanded Content */}
      {expanded && (
        <View style={styles.expandedSection}>
          {/* Order Meta */}
          <View style={styles.metaGrid}>
            <View style={styles.metaItem}>
              <Text style={styles.metaItemLabel}>Order Date</Text>
              <Text style={styles.metaItemValue}>{order.orderDate}</Text>
            </View>
            <View style={styles.metaItem}>
              <Text style={styles.metaItemLabel}>Vehicle No.</Text>
              <Text style={styles.metaItemValue}>{order.vehicleNo}</Text>
            </View>
            <View style={styles.metaItem}>
              <Text style={styles.metaItemLabel}>Invoice</Text>
              <Text style={styles.metaItemValue}>{order.invoice}</Text>
            </View>
          </View>

          {/* Mini Timeline */}
          {order.timeline.map((item, idx) => (
            <View key={idx} style={styles.timelineRow}>
              <View style={styles.timelineLeft}>
                <CheckDot done={item.done} />
                {idx < order.timeline.length - 1 && (
                  <View style={[styles.timelineLine, { backgroundColor: item.done ? '#1a7a3c' : '#E0E0E0' }]} />
                )}
              </View>
              <View style={styles.timelineContent}>
                <Text style={[styles.timelineLabel, { color: item.done ? '#111' : '#BBB' }]}>
                  {item.label}
                </Text>
                <Text style={styles.timelineTime}>{item.time}</Text>
                {item.vehicleNo && (
                  <View style={styles.vehicleTag}>
                    <Text style={styles.vehicleTagText}>🚚 {item.vehicleNo}</Text>
                  </View>
                )}
              </View>
            </View>
          ))}

          {/* ePOD Warning */}
          {order.epodRequired && (order.step > 4) && (
            <View style={styles.epodWarning}>
              <Text style={styles.epodWarningIcon}>⚠️</Text>
              <Text style={styles.epodWarningText}>
                <Text style={styles.epodWarningBold}>ePOD Confirmation Required</Text>
                {' – Confirm delivery to unlock billing'}
              </Text>
            </View>
          )}

          {/* Show Details Button */}
          <TouchableOpacity style={styles.showDetailsBtn} onPress={onShowDetails}>
            <Text style={styles.showDetailsBtnText}>ⓘ  Show Details</Text>
          </TouchableOpacity>
        </View>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#fff',
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#EBEBEB',
    marginBottom: 10,
    paddingHorizontal: 14,
    paddingTop: 14,
    paddingBottom: 12,
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  iconBox: {
    width: 38,
    height: 38,
    borderRadius: 8,
    backgroundColor: '#EEF1FA',
    alignItems: 'center',
    justifyContent: 'center',
  },
  iconText: {
    fontSize: 18,
  },
  orderInfo: {
    flex: 1,
  },
  orderId: {
    fontSize: 14,
    fontWeight: '700',
    color: '#111',
  },
  metaRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 5,
    marginTop: 2,
  },
  dot: {
    width: 7,
    height: 7,
    borderRadius: 4,
    margin: 2,
    backgroundColor: '#4A5DBF',
  },
  metaType: {
    fontSize: 11,
    color: '#4A5DBF',
    fontWeight: '500',
    margin: 2
  },
  metaQty: {
    fontSize: 11,
    color: '#111',
    fontWeight: '700',
  },
  statusBadge: {
    borderRadius: 6,
    borderWidth: 1,
    paddingHorizontal: 8,
    paddingVertical: 3,
  },
  statusText: {
    fontSize: 11,
    fontWeight: '700',
  },
  chevron: {
    padding: 4,
  },
  chevronText: {
    fontSize: 13,
    color: '#888',
  },
  progressRow: {
    flexDirection: 'row',
    gap: 3,
    marginTop: 10,
  },
  progressSegment: {
    flex: 1,
    height: moderateScale(6),
    borderRadius: 2,
  },
  stepLabel: {
    fontSize: 11,
    color: '#999',
    marginTop: 4,
  },
  expandedSection: {
    marginTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#F0F0F0',
    paddingTop: 12,
  },
  metaGrid: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  metaItem: {},
  metaItemLabel: {
    fontSize: 10,
    color: '#AAA',
  },
  metaItemValue: {
    fontSize: 12,
    fontWeight: '700',
    color: '#111',
    marginTop: 2,
  },
  timelineRow: {
    flexDirection: 'row',
    marginBottom: 0,
  },
  timelineLeft: {
    alignItems: 'center',
    width: 24,
    marginRight: 10,
  },
  checkDot: {
    width: 22,
    height: 22,
    borderRadius: 11,
    alignItems: 'center',
    justifyContent: 'center',
  },
  checkMark: {
    color: '#fff',
    fontSize: 11,
    fontWeight: '700',
  },
  timelineLine: {
    width: 2,
    flex: 1,
    minHeight: 12,
  },
  timelineContent: {
    flex: 1,
    paddingBottom: 10,
  },
  timelineLabel: {
    fontSize: 12,
    fontWeight: '700',
  },
  timelineTime: {
    fontSize: 10,
    color: '#AAA',
    marginTop: 1,
  },
  vehicleTag: {
    backgroundColor: '#F3F4F6',
    borderWidth: 1,
    borderColor: '#DDD',
    borderRadius: 4,
    paddingHorizontal: 6,
    paddingVertical: 2,
    alignSelf: 'flex-start',
    marginTop: 3,
  },
  vehicleTagText: {
    fontSize: 10,
    color: '#555',
  },
  epodWarning: {
    backgroundColor: '#FFFBE6',
    borderWidth: 1,
    borderColor: '#FFD77A',
    borderRadius: 8,
    padding: 10,
    flexDirection: 'row',
    alignItems: 'flex-start',
    gap: 6,
    marginBottom: 10,
  },
  epodWarningIcon: {
    fontSize: 14,
  },
  epodWarningText: {
    fontSize: 11,
    color: '#7A5A00',
    flex: 1,
    lineHeight: 16,
  },
  epodWarningBold: {
    fontWeight: '700',
  },
  showDetailsBtn: {
    backgroundColor: '#C8102E',
    borderRadius: 8,
    paddingVertical: 11,
    alignItems: 'center',
  },
  showDetailsBtnText: {
    color: '#fff',
    fontSize: 13,
    fontWeight: '700',
  },
  item:{backgroundColor: '#E3F2FD', flexDirection: 'row', alignItems: 'center',justifyContent:'center', paddingHorizontal: moderateScale(4), borderRadius: moderateScale(4)}
});

export default OrderCard;