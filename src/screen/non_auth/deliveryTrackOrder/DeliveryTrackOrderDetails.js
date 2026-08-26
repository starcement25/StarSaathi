import React, { useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  StyleSheet,
  Modal,
  TextInput,
  Linking,
  Alert,
  Platform,
  Image,
} from 'react-native';
import SafeView from '../../../helper/SafeView';
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView';
import { Colors } from '../../../assets/Colors';
import { Icons } from '../../../assets/Icons';
import { moderateScale } from '../../../helper/Window';


const IconTruck = ({ size = 16, color = '#555' }) => (
  <Text style={{ fontSize: size, color }}>🚚</Text>
);

const IconPDF = ({ size = 20, color = '#C8102E' }) => (
  <Image style={{height: size, width: size,}} source={Icons.PDFNew} />
);

const IconCamera = ({ size = 22, color = '#B0B0B0' }) => (
  <Text style={{ fontSize: size, color }}>📷</Text>
);

const IconInfo = ({ size = 16, color = '#1565C0' }) => (
  <Text style={{ fontSize: size, color }}>ℹ</Text>
);

const IconWarning = ({ size = 16, color = '#C8102E' }) => (
  <Text style={{ fontSize: size, color }}>⚠</Text>
);

const IconMessage = ({ size = 18, color = '#fff' }) => (
  <Text style={{ fontSize: size, color }}>💬</Text>
);

const IconShipTo = ({ size = 18, color = '#555' }) => (
  <Image style={{height: size, width: size,}} source={Icons.ShipToParty} />
);

const IconPerson = ({ size = 18, color = '#555' }) => (
 <Image style={{height: size, width: size,}} source={Icons.OrderIcon} />
);

const IconOrderType = ({ size = 18, color = '#555' }) => (
 <Image style={{height: size, width: size,}} source={Icons.OrderTypeIcon} />
);

const IconLocation = ({ size = 18, color = '#555' }) => (
  <Image style={{height: size, width: size,}} source={Icons.PlantNameIcon} />
);

const IconInvoice = ({ size = 18, color = '#555' }) => (
<Image style={{height: size, width: size,}} source={Icons.InvoiceIcon} />
);

const IconClock = ({ size = 18, color = '#555' }) => (
  <Image style={{height: size, width: size,}} source={Icons.TimeIcon} />
);

const IconFactory = ({ size = 18, color = '#555' }) => (
 <Image style={{height: size, width: size,}} source={Icons.PlantIcon} />
);

const STATUS_COLORS = {
  'Order Dispatched': { bg: '#E8F5E9', text: '#2E7D32', border: '#A5D6A7' },
  'In Transit':       { bg: '#E3F2FD', text: '#1565C0', border: '#90CAF9' },
  'Delivered':        { bg: '#E8F5E9', text: '#2E7D32', border: '#A5D6A7' },
  default:            { bg: '#F5F5F5', text: '#555',    border: '#E0E0E0' },
};

const InfoRow = ({ icon, label, value, noBorder = false }) => (
  <View style={[styles.infoRow, noBorder && { borderBottomWidth: 0 }]}>
    <View style={styles.infoRowLeft}>
      <View style={styles.infoIconWrap}>{icon}</View>
      <Text style={styles.infoLabel}>{label}</Text>
    </View>
    <Text style={styles.infoValue} numberOfLines={2}>{value}</Text>
  </View>
);

const TimelineStep = ({ step, isLast }) => {
  const done = step.done;
  return (
    <View style={styles.timelineStepRow}>
      <View style={styles.timelineConnectorCol}>
        <View style={[styles.timelineDot, done ? styles.timelineDotDone : styles.timelineDotPending]}>
          {done && <Image style={{height: 14, width: 14, tintColor: '#fff'}} source={Icons.CheckNew} />}
          {/* {!done && <Text style={styles.timelinePendingTick}>✓</Text>} */}
        </View>
        {!isLast && (
          <View style={[styles.timelineLine, done ? styles.timelineLineDone : styles.timelineLinePending]} />
        )}
      </View>
      <View style={styles.timelineContent}>
        <Text style={[styles.timelineLabel, !done && styles.timelineLabelPending]}>
          {step.label}
        </Text>
        <Text style={[styles.timelineTime, !done && styles.timelineTimePending]}>
          {step.time}
        </Text>
        {step.vehicleNo && (
          <View style={styles.vehicleBadge}>
            <IconTruck size={12} color="#555" />
            <Text style={styles.vehicleBadgeText}>{step.vehicleNo}</Text>
          </View>
        )}
      </View>
      {/* <View style={styles.timelineRightIcon}>
        {done ? (
          <View style={styles.timelineRightDone}>
            <IconCheck size={12} color="#fff" />
          </View>
        ) : (
          <View style={styles.timelineRightPending}>
            <Text style={{ fontSize: 10, color: '#999' }}>✓</Text>
          </View>
        )}
      </View> */}
    </View>
  );
};

const RaiseDisputeModal = ({ visible, onClose, onConfirm }) => {
  const [shortageQty, setShortageQty] = useState('');
  const [remarks, setRemarks]         = useState('');
  const [images, setImages]           = useState([null, null, null, null, null]);

  const handleConfirm = () => {
    if (!shortageQty.trim()) {
      Alert.alert('Validation', 'Please enter Shortage Qty.');
      return;
    }
    onConfirm({ shortageQty, remarks, images });
  };

  return (
    <Modal visible={visible} transparent animationType="slide" onRequestClose={onClose}>
      <View style={styles.modalOverlay}>
        <View style={styles.raiseDisputeSheet}>

          <View style={styles.sheetHandle} />

          <View style={styles.sheetHeader}>
            <Text style={styles.sheetTitle}>Raise Dispute</Text>
            <TouchableOpacity onPress={onClose} style={styles.sheetCloseBtn} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
              <Text style={styles.sheetCloseIcon}>✕</Text>
            </TouchableOpacity>
          </View>

          <ScrollView showsVerticalScrollIndicator={false} keyboardShouldPersistTaps="handled">
            <View style={styles.fieldBlock}>
              <Text style={styles.fieldLabel}>Shortage Qty (MT)</Text>
              <TextInput
                style={styles.textInput}
                value={shortageQty}
                onChangeText={setShortageQty}
                keyboardType="decimal-pad"
                placeholder=""
                placeholderTextColor="#ccc"
              />
            </View>

            <View style={styles.fieldBlock}>
              <Text style={styles.fieldLabel}>Remarks</Text>
              <View style={styles.textAreaWrap}>
                <TextInput
                  style={styles.textArea}
                  value={remarks}
                  onChangeText={t => setRemarks(t.slice(0, 500))}
                  multiline
                  numberOfLines={5}
                  placeholder="Write your remarks here..."
                  placeholderTextColor="#C0C0C0"
                  textAlignVertical="top"
                />
                <Text style={styles.charCount}>{remarks.length}/500</Text>
              </View>
            </View>

            <View style={styles.fieldBlock}>
              <Text style={styles.fieldLabel}>Upload Images (Proof)</Text>
              <Text style={styles.uploadSubLabel}>JPG, PNG up to 5 MB (Max 5 images)</Text>
              <View style={styles.imageSlots}>
                {images.map((img, idx) => (
                  <TouchableOpacity key={idx} style={styles.imageSlot} onPress={() => {}}>
                    {img ? (
                      <Image source={{ uri: img }} style={styles.imagePreview} />
                    ) : (
                      <IconCamera size={22} color="#B0B0B0" />
                    )}
                  </TouchableOpacity>
                ))}
              </View>
              <View style={styles.uploadHint}>
                <IconInfo size={14} color="#1565C0" />
                <Text style={styles.uploadHintText}>Please upload clear images of the issue.</Text>
              </View>
            </View>

            <View style={styles.disputeActionRow}>
              <TouchableOpacity style={styles.btnCancelOutline} onPress={onClose}>
                <Text style={styles.btnCancelText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.btnConfirmDispute} onPress={handleConfirm}>
                <Text style={styles.btnConfirmDisputeText}>Confirm Dispute</Text>
              </TouchableOpacity>
            </View>

            <View style={{ height: 20 }} />
          </ScrollView>
        </View>
      </View>
    </Modal>
  );
};


const TransporterReportModal = ({ visible, report, onClose, onRaiseDispute, onApprove }) => {
  //if (!report) return null;
  return (
    <Modal visible={visible} transparent animationType="slide" onRequestClose={onClose}>
      <View style={styles.modalOverlay}>
        <View style={styles.reportSheet}>
          <View style={styles.sheetHandle} />
          <View style={styles.sheetHeader}>
            <View>
              <Text style={styles.sheetTitle}>Transporter Delivery Report</Text>
              <Text style={styles.confirmedByText}>Confirmed by: {report?.confirmedBy ?? "Assam Road Lines Pvt. Ltd."}</Text>
            </View>
            <TouchableOpacity onPress={onClose} style={styles.sheetCloseBtn} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
              <Text style={styles.sheetCloseIcon}>✕</Text>
            </TouchableOpacity>
          </View>

          <ScrollView showsVerticalScrollIndicator={false}>
            <View style={styles.vehicleDriverRow}>
              <View style={styles.vehicleDriverCard}>
                <View style={styles.vehicleDriverIcon}>
                  <Text style={{ fontSize: 18 }}>🚚</Text>
                </View>
                <View>
                  <Text style={styles.vehicleDriverSubLabel}>Vehicle No.</Text>
                  <Text style={styles.vehicleDriverMain}>{report?.vehicleNo ?? "AS 01 AC 7655"}</Text>
                  <Text style={styles.vehicleDriverSub}>{report?.vehicleType ?? "10-Wheeler Truck"}</Text>
                </View>
              </View>
              <View style={styles.vehicleDriverDivider} />
              <View style={styles.vehicleDriverCard}>
                <View style={styles.vehicleDriverIcon}>
                  <Image source={Icons.OrderIcon} style={{width: 20, height: 20}}/>
                </View>
                <View>
                  <Text style={styles.vehicleDriverSubLabel}>Driver</Text>
                  <Text style={styles.vehicleDriverMain}>{report?.driverName ?? "Bikram Das"}</Text>
                  <Text style={styles.vehicleDriverSub}>{report?.driverPhone ?? "9726565525"}</Text>
                </View>
              </View>
            </View>

            <View style={styles.reportDivider} />

            <View style={styles.qtySummarySection}>
              <View style={styles.qtySummaryHeader}>
                <Text style={{ fontSize: 14, marginRight: 6 }}>📦</Text>
                <Text style={styles.qtySummaryTitle}>Quantity Summary</Text>
              </View>
              <View style={styles.qtySummaryCards}>
                <View style={styles.qtySummaryCardOrdered}>
                  <Text style={styles.qtySummaryCardLabel}>Ordered</Text>
                  <Text style={styles.qtySummaryCardValue}>{report?.orderedQty ?? "10MT"}</Text>
                  <Text style={styles.qtySummaryCardBags}>{report?.orderedBags ?? "200"} bags</Text>
                </View>
                <View style={styles.qtySummaryCardConfirmed}>
                  <Text style={styles.qtySummaryCardLabelRed}>Confirmed</Text>
                  <Text style={styles.qtySummaryCardValueRed}>{report?.confirmedQty ?? "9.5MT"}</Text>
                  <Text style={[styles.qtySummaryCardBags, { color: '#999' }]}>
                    {report?.confirmedBags ?? "190"} bags
                  </Text>
                </View>
              </View>
            </View>

            <View style={styles.reportDivider} />

            <View style={styles.shortageRow}>
              <View style={styles.shortageLeft}>
                <IconWarning size={16} color="#C8102E" />
                <View style={{ marginLeft: 8 }}>
                  <Text style={styles.shortageTitle}>Shortage Reported</Text>
                  <Text style={styles.shortageBags}>{report?.shortageBags ?? "10"} bags short</Text>
                </View>
              </View>
              <Text style={styles.shortageValue}>{report?.shortageQty ?? "-0.5MT(5.0%)"}</Text>
            </View>

            <View style={styles.reportDivider} />

            <View style={styles.remarkSection}>
              <View style={styles.remarkHeader}>
                <Text style={{ fontSize: 14, marginRight: 6, color: '#888' }}>💬</Text>
                <Text style={styles.remarkLabel}>Transporter Remark</Text>
              </View>
              <Text style={styles.remarkText}>"{report?.remark ?? "Road was poor and hence some of the bags are damaged during transit. Unloaded rest of the bags safely"}"</Text>
            </View>

            <View style={styles.reportDivider} />

            <View style={styles.deliveredOnRow}>
              <Text style={styles.deliveredOnLabel}>Delivered on</Text>
              <Text style={styles.deliveredOnValue}>{report?.deliveredOn ?? "03 June 2026, 5:01 PM"}</Text>
            </View>

            <View style={styles.reportActionRow}>
              <TouchableOpacity style={styles.btnRaiseDisputeOutline} onPress={onRaiseDispute}>
                <Text style={styles.btnRaiseDisputeText}>Raise Dispute</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.btnApproveDelivery} onPress={onApprove}>
                <Text style={styles.btnApproveDeliveryText}>Approve Delivery</Text>
              </TouchableOpacity>
            </View>

            <View style={{ height: 24 }} />
          </ScrollView>
        </View>
      </View>
    </Modal>
  );
};

const DeliveryTrackOrderDetails = ({ navigation, route }) => {
  const { order, setOrders } = route.params;

  const [showEpodModal,    setShowEpodModal]    = useState(false);
  const [showDisputeModal, setShowDisputeModal] = useState(false);

  const statusColors = STATUS_COLORS[order.status] || STATUS_COLORS.default;

  const handleCallDriver = () => {
    const phone = `tel:${order.driverPhone}`;
    Linking.openURL(phone).catch(() =>
      Alert.alert('Error', 'Unable to make a call.')
    );
  };

  const handleDownloadPDF = () => {
    Alert.alert('Download', 'Transit PDF download initiated.');
  };

  const handleEpodPress = () => {
    setShowEpodModal(true);
  };

  const handleApproveDelivery = () => {
    setShowEpodModal(false);
    Alert.alert('Success', 'Delivery approved successfully!', [
      {
        text: 'OK',
        onPress: () => {
          if (setOrders) {
            setOrders(prev =>
              prev.map(o =>
                o.id === order.id
                  ? { ...o, status: 'Delivered', step: 7, stepLabel: 'Delivered' }
                  : o
              )
            );
          }
          navigation.goBack();
        },
      },
    ]);
  };

  const handleRaiseDispute = () => {
    setShowEpodModal(false);
    setShowDisputeModal(true);
  };

  const handleConfirmDispute = (disputeData) => {
    setShowDisputeModal(false);
    Alert.alert('Dispute Raised', 'Your dispute has been submitted successfully.', [
      { text: 'OK', onPress: () => navigation.goBack() },
    ]);
  };

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <View style={{ flex: 1 }}>
        <SBSCommonHeaderView title="Delivery Track Order Details" backPath=" " />

        <ScrollView style={styles.scrollContainer} contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false} >
          <View style={styles.orderHeaderCard}>
            <View style={styles.orderHeaderTop}>
              <View>
                <Text style={styles.orderNumberLabel}>Order Number</Text>
                <Text style={styles.orderNumberValue}>{order.id}</Text>
                <Text style={styles.orderDate}>{order.orderDate}</Text>
              </View>
              <View style={[styles.statusBadge, { backgroundColor: statusColors.bg, borderColor: statusColors.border }]}>
                <Text style={[styles.statusBadgeText, { color: statusColors.text }]}>
                  {order.status}
                </Text>
              </View>
            </View>
          </View>

          <View style={styles.card}>
            {order.timeline.map((step, idx) => (
              <TimelineStep
                key={idx}
                step={step}
                isLast={idx === order.timeline.length - 1}
              />
            ))}
          </View>

          <View style={styles.pdfButtonWrap}>
            <TouchableOpacity style={styles.pdfButton} onPress={handleDownloadPDF} activeOpacity={0.85}>
              <IconPDF size={20} color="#C8102E" />
              <Text style={styles.pdfButtonText}>Download Transit PDF</Text>
            </TouchableOpacity>
          </View>

          <View style={styles.callDriverCard}>
            <TouchableOpacity style={styles.callDriverInner} onPress={handleCallDriver} activeOpacity={0.75}>
              <View style={styles.callDriverLeft}>
                <View style={styles.callDriverIconWrap}>
                  <Image style={{height: 24, width: 24,}} source={Icons.CallIconn} />
                </View>
                <Text style={styles.callDriverName}>Call Driver ( {order.driverName} )</Text>
              </View>
              <View style={styles.callDriverRight}>
                 <Image style={{height: 24, width: 24,}} source={Icons.CallIconn} />
                <Text style={styles.callDriverPhone}>{order.driverPhone}</Text>
              </View>
            </TouchableOpacity>
          </View>

          <View style={styles.orderInfoSection}>
            <Text style={styles.sectionTitle}>Order Information</Text>

            <View style={styles.productRow}>
              <View style={styles.productImageBox}>
                <Image style={{height: 40, width: 40,}} source={Icons.Package} />
              </View>
              <View style={styles.productDetails}>
                <Text style={styles.productName}>{order.product}</Text>
                <View style={styles.productQtyRow}>
                  <View>
                    <Text style={styles.productQtyLabel}>Total Qty</Text>
                    <Text style={styles.productQtyValue}>{order.totalQty}</Text>
                  </View>
                  <View style={styles.productQtyDivider} />
                  <View>
                    <Text style={styles.productQtyLabel}>Shipment Qty</Text>
                    <Text style={styles.productQtyValue}>{order.shipmentQty}</Text>
                  </View>
                </View>
              </View>
            </View>

            <View style={styles.infoRowsDivider} />

            {/* Info rows */}
            <InfoRow icon={<IconShipTo size={18} color="#555" />} label="ShipToParty" value={order.shipToParty} />
            <InfoRow icon={<IconPerson size={18} color="#555" />} label="OrderFrom" value={order.orderFrom} />
            <InfoRow icon={<IconOrderType size={18} color="#555" />} label="Order Type" value={order.orderType} />
            <InfoRow icon={<IconLocation size={18} color="#555" />} label="Plant Name" value={order.plantName} />
            <InfoRow icon={<IconInvoice size={18} color="#555" />} label="Invoice Number" value={order.invoiceNumber} />
            <InfoRow icon={<IconClock size={18} color="#555" />} label="Dispatch Time" value={order.dispatchTime} />
            <InfoRow icon={<IconFactory size={18} color="#555" />} label="Inside Plant Gate" value={order.insidePlantGate} noBorder />
          </View>

          <View style={{ height: 100 }} />
        </ScrollView>

        {order.epodRequired && (
          <View style={styles.epodButtonContainer}>
            <TouchableOpacity style={styles.epodButton} onPress={handleEpodPress} activeOpacity={0.88}>
              <IconMessage size={18} color="#fff" />
              <Text style={styles.epodButtonText}>ePOD Confirmation</Text>
            </TouchableOpacity>
          </View>
        )}
      </View>

      <TransporterReportModal
        visible={showEpodModal}
        report={order.deliveryReport}
        onClose={() => setShowEpodModal(false)}
        onRaiseDispute={handleRaiseDispute}
        onApprove={handleApproveDelivery}
      />
      <RaiseDisputeModal
        visible={showDisputeModal}
        onClose={() => setShowDisputeModal(false)}
        onConfirm={handleConfirmDispute}
      />
    </SafeView>
  );
};

// ─── Styles ───────────────────────────────────────────────────────────────────
const styles = StyleSheet.create({
  scrollContainer: { flex: 1, backgroundColor: '#F5F5F5', },
  scrollContent: { paddingHorizontal: 0, },
  orderHeaderCard: { backgroundColor: '#fff', paddingHorizontal: 16, paddingVertical: 14, marginBottom: 8, },
  orderHeaderTop: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', },
  orderNumberLabel: { fontSize: 12, color: '#888', marginBottom: 2, },
  orderNumberValue: { fontSize: 20, fontWeight: '800', color: '#111', letterSpacing: 0.3, },
  orderDate: { fontSize: 12, color: '#666', marginTop: 4, },
  statusBadge: { borderWidth: 1.5, borderRadius: 8, paddingHorizontal: 12, paddingVertical: 6, alignSelf: 'flex-start', marginTop: 2, },
  statusBadgeText: { fontSize: 13, fontWeight: '700', },
  card: { backgroundColor: '#fff', marginHorizontal: 0, marginBottom: 8, paddingHorizontal: 16, paddingVertical: 12, },
  timelineStepRow: { flexDirection: 'row', alignItems: 'flex-start', marginBottom: 0, },
  timelineConnectorCol: { width: 32, alignItems: 'center', marginRight: 10, },
  timelineDot: { width: 28, height: 28, borderRadius: 14, alignItems: 'center', justifyContent: 'center', zIndex: 1, },
  timelineDotDone: { backgroundColor: '#2E7D32', },
  timelineDotPending: { backgroundColor: '#E0E0E0', borderWidth: 1.5, borderColor: '#C0C0C0', },
  timelinePendingTick: { fontSize: 12, color: '#B0B0B0', fontWeight: '700', },
  timelineLine: { width: 2, flex: 1, minHeight: 18, },
  timelineLineDone: { backgroundColor: '#2E7D32', },
  timelineLinePending: { backgroundColor: '#E0E0E0', },
  timelineContent: { flex: 1, paddingBottom: 18, paddingTop: 4, },
  timelineLabel: { fontSize: 13, fontWeight: '700', color: '#111', },
  timelineLabelPending: { color: '#999', fontWeight: '600', },
  timelineTime: { fontSize: 12, color: '#666', marginTop: 2, },
  timelineTimePending: { color: '#B0B0B0', },
  vehicleBadge: { flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: '#DDD', borderRadius: 6, paddingHorizontal: 8, paddingVertical: 3, marginTop: 6, alignSelf: 'flex-start', gap: 5, backgroundColor: '#FAFAFA', },
  vehicleBadgeText: { fontSize: 12, fontWeight: '600', color: '#333', },
  timelineRightIcon: { width: 28, alignItems: 'center', paddingTop: 4, },
  timelineRightDone: { width: 22, height: 22, borderRadius: 11, backgroundColor: '#2E7D32', alignItems: 'center', justifyContent: 'center', },
  timelineRightPending: { width: 22, height: 22, borderRadius: 11, backgroundColor: '#E0E0E0', alignItems: 'center', justifyContent: 'center', },
  pdfButtonWrap: { paddingHorizontal: 16, marginBottom: 8, },
  pdfButton: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', borderWidth: 2, marginHorizontal: moderateScale(50), borderColor: '#C8102E', borderRadius: 10, paddingVertical: 13, backgroundColor: '#fff', gap: 10, },
  pdfButtonText: { fontSize: 15, fontWeight: '700', color: '#C8102E', },
  callDriverCard: { backgroundColor: '#fff', marginHorizontal: 0, marginBottom: 8, paddingHorizontal: 16, paddingVertical: 14, },
  callDriverInner: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', },
  callDriverLeft: { flexDirection: 'row', alignItems: 'center', gap: 10, },
  callDriverIconWrap: { width: 42, height: 42, borderRadius: 21, backgroundColor: '#E8F5E9', alignItems: 'center', justifyContent: 'center', },
  callDriverName: { fontSize: 14, fontWeight: '600', color: '#111', },
  callDriverRight: { flexDirection: 'row', alignItems: 'center', gap: 4, },
  callDriverPhone: { fontSize: 14, fontWeight: '600', color: '#000', },
  orderInfoSection: { backgroundColor: '#fff', marginHorizontal: 0, marginBottom: 8, paddingHorizontal: 16, paddingTop: 14, paddingBottom: 4, },
  sectionTitle: { fontSize: 15, fontWeight: '800', color: '#111', marginBottom: 12, },
  productRow: { flexDirection: 'row', alignItems: 'center', gap: 14, marginBottom: 14, },
  productImageBox: { width: 80, height: 70, borderRadius: 8, backgroundColor: '#E8EAF0', alignItems: 'center', justifyContent: 'center', },
  productDetails: { flex: 1, },
  productName: { fontSize: 15, fontWeight: '700', color: '#111', marginBottom: 8, },
  productQtyRow: { flexDirection: 'row', alignItems: 'center', gap: 16, },
  productQtyLabel: { fontSize: 11, color: '#888', marginBottom: 2, },
  productQtyValue: { fontSize: 14, fontWeight: '700', color: '#111', },
  productQtyDivider: { width: 1, height: 30, backgroundColor: '#E0E0E0', },
  infoRowsDivider: { height: 1, backgroundColor: '#F0F0F0', marginBottom: 4, },
  infoRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingVertical: 13, borderBottomWidth: 1, borderBottomColor: '#F0F0F0', },
  infoRowLeft: { flexDirection: 'row', alignItems: 'center', gap: 10, flex: 1, },
  infoIconWrap: { width: 28, alignItems: 'center', },
  infoLabel: { fontSize: 13, color: '#444', fontWeight: '500', },
  infoValue: { fontSize: 13, fontWeight: '700', color: '#111', textAlign: 'right', maxWidth: '50%', flexShrink: 1, },


  epodButtonContainer: { position: 'absolute', bottom: 0, left: 0, right: 0, backgroundColor: '#fff', paddingHorizontal: 16, paddingVertical: 12, paddingBottom: Platform.OS === 'ios' ? 28 : 12, borderTopWidth: 1, borderTopColor: '#F0F0F0', elevation: 8, shadowColor: '#000', shadowOffset: { width: 0, height: -2 }, shadowOpacity: 0.08, shadowRadius: 6, },
  epodButton: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', backgroundColor: '#C8102E', borderRadius: 10, paddingVertical: 15, gap: 10, },
  epodButtonText: { fontSize: 16, fontWeight: '700', color: '#fff', letterSpacing: 0.3, },

  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.45)', justifyContent: 'flex-end', },
  reportSheet: { backgroundColor: '#fff', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingHorizontal: 16, paddingTop: 10, paddingBottom: Platform.OS === 'ios' ? 34 : 16, maxHeight: '90%', },
  sheetHandle: { width: 40, height: 4, borderRadius: 2, backgroundColor: '#DDD', alignSelf: 'center', marginBottom: 14, },
  sheetHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 16, },
  sheetTitle: { fontSize: 17, fontWeight: '800', color: '#111', },
  sheetCloseBtn: { padding: 4, },
  sheetCloseIcon: { fontSize: 18, color: '#444', fontWeight: '600', },
  confirmedByText: { fontSize: 12, color: '#000', marginTop: 2, },

  vehicleDriverRow: { flexDirection: 'row', alignItems: 'center', marginBottom: 12, },
  vehicleDriverCard: { flex: 1, flexDirection: 'row', alignItems: 'center', gap: 10, },
  vehicleDriverIcon: { width: 36, height: 36, borderRadius: 18, backgroundColor: '#F5F5F5', alignItems: 'center', justifyContent: 'center', },
  vehicleDriverDivider: { width: 1, height: 44, backgroundColor: '#EEE', marginHorizontal: 10, },
  vehicleDriverSubLabel: { fontSize: 11, color: '#999', marginBottom: 2, },
  vehicleDriverMain: { fontSize: 14, fontWeight: '700', color: '#111', },
  vehicleDriverSub: { fontSize: 11, color: '#777', marginTop: 1, },
  reportDivider: { height: 1, backgroundColor: '#F0F0F0', marginVertical: 12, },

  qtySummarySection: { marginBottom: 4, },
  qtySummaryHeader: { flexDirection: 'row', alignItems: 'center', marginBottom: 10, },
  qtySummaryTitle: { fontSize: 13, fontWeight: '600', color: '#444', },
  qtySummaryCards: { flexDirection: 'row', gap: 10, },
  qtySummaryCardOrdered: { flex: 1, backgroundColor: '#F5F5F5', borderRadius: 10, padding: 12, },
  qtySummaryCardConfirmed: { flex: 1, backgroundColor: '#FFF0F0', borderRadius: 10, padding: 12, },
  qtySummaryCardLabel: { fontSize: 11, color: '#777', marginBottom: 4, },
  qtySummaryCardLabelRed: { fontSize: 11, color: '#C8102E', marginBottom: 4, fontWeight: '600', },
  qtySummaryCardValue: { fontSize: 22, fontWeight: '800', color: '#111', },
  qtySummaryCardValueRed: { fontSize: 22, fontWeight: '800', color: '#C8102E', },
  qtySummaryCardBags: { fontSize: 12, color: '#888', marginTop: 3, },

  shortageRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', },
  shortageLeft: { flexDirection: 'row', alignItems: 'center', gap: 6, },
  shortageTitle: { fontSize: 13, fontWeight: '700', color: '#C8102E', },
  shortageBags: { fontSize: 11, color: '#888', marginTop: 1, },
  shortageValue: { fontSize: 14, fontWeight: '700', color: '#C8102E', },

  remarkSection: {},
  remarkHeader: { flexDirection: 'row', alignItems: 'center', marginBottom: 6, },
  remarkLabel: { fontSize: 12, color: '#888', },
  remarkText: { fontSize: 13, color: '#444', fontStyle: 'italic', lineHeight: 20, },

  deliveredOnRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', },
  deliveredOnLabel: { fontSize: 12, color: '#888', },
  deliveredOnValue: { fontSize: 13, fontWeight: '600', color: '#111', },

  reportActionRow: { flexDirection: 'row', gap: 12, marginTop: 20, },
  btnRaiseDisputeOutline: { flex: 1, borderWidth: 1.5, borderColor: '#C8102E', borderRadius: 10, paddingVertical: 14, alignItems: 'center', justifyContent: 'center', backgroundColor: '#fff', },
  btnRaiseDisputeText: { fontSize: 14, fontWeight: '700', color: '#C8102E', },
  btnApproveDelivery: { flex: 1, backgroundColor: '#2E7D32', borderRadius: 10, paddingVertical: 14, alignItems: 'center', justifyContent: 'center', },
  btnApproveDeliveryText: { fontSize: 14, fontWeight: '700', color: '#fff', },

  raiseDisputeSheet: { backgroundColor: '#fff', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingHorizontal: 16, paddingTop: 10, paddingBottom: Platform.OS === 'ios' ? 34 : 16, maxHeight: '92%', },
  fieldBlock: { marginBottom: 16, },
  fieldLabel: { fontSize: 14, fontWeight: '700', color: '#111', marginBottom: 8, },
  textInput: { borderWidth: 1, borderColor: '#DDD', borderRadius: 8, paddingHorizontal: 12, paddingVertical: 12, fontSize: 14, color: '#111', backgroundColor: '#fff', },
  textAreaWrap: { borderWidth: 1, borderColor: '#DDD', borderRadius: 8, backgroundColor: '#fff', },
  textArea: { paddingHorizontal: 12, paddingTop: 12, paddingBottom: 28, fontSize: 14, color: '#111', minHeight: 110, },
  charCount: { position: 'absolute', bottom: 8, right: 12, fontSize: 11, color: '#999', },
  uploadSubLabel: { fontSize: 12, color: '#999', marginTop: -4, marginBottom: 10, },
  imageSlots: { flexDirection: 'row', gap: 8, marginBottom: 12, },
  imageSlot: { flex: 1, aspectRatio: 1, backgroundColor: '#F5F5F5', borderRadius: 8, borderWidth: 1, borderColor: '#E0E0E0', alignItems: 'center', justifyContent: 'center', },
  imagePreview: { width: '100%', height: '100%', borderRadius: 8, },
  uploadHint: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#E3F2FD', borderRadius: 8, padding: 10, gap: 8, },
  uploadHintText: { fontSize: 12, color: '#1565C0', fontWeight: '500', },
  disputeActionRow: { flexDirection: 'row', gap: 12, marginTop: 4, },
  btnCancelOutline: { flex: 1, borderWidth: 1.5, borderColor: '#C8102E', borderRadius: 10, paddingVertical: 14, alignItems: 'center', justifyContent: 'center', backgroundColor: '#fff', },
  btnCancelText: { fontSize: 14, fontWeight: '700', color: '#C8102E', },
  btnConfirmDispute: { flex: 1, backgroundColor: '#C8102E', borderRadius: 10, paddingVertical: 14, alignItems: 'center', justifyContent: 'center', },
  btnConfirmDisputeText: { fontSize: 14, fontWeight: '700', color: '#fff', },
});

export default DeliveryTrackOrderDetails;