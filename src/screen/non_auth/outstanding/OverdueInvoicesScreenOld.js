import React, { useEffect, useState } from 'react';
import { View, Text, TouchableOpacity, FlatList, Platform, } from 'react-native';
import SafeView from '../../../helper/SafeView';
import { Colors } from '../../../assets/Colors';
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView';
import { moderateScale } from '../../../helper/Window';
import moment from 'moment';

const TABS = [
  { label: 'All', key: 'All' },
  { label: '0–30 Days', key: '0-30' },
  { label: '31–60 Days', key: '31-60' },
  { label: '60+ Days', key: '60+' },
];

const ALL_INVOICES = [
  { id: 'INV12345', date: '30 Mar 2026', amount: '20000', ageDays: 35, ageLabel: 'Age: 35 Days', ageType: 'normal', type: 'dr' },
  { id: 'INV12346', date: '21 Mar 2026', amount: '50000', ageDays: 78, ageLabel: 'Overdue 75+ Days', ageType: 'overdue', type: 'dr' },
  { id: 'INV12347', date: '15 Mar 2026', amount: '15500', ageDays: 15, ageLabel: 'Age: 15 Days', ageType: 'normal', type: 'dr' },
  { id: 'INV12348', date: '02 Mar 2026', amount: '24500', ageDays: 45, ageLabel: 'Age: 45 Days', ageType: 'normal', type: 'cr' },
  { id: 'INV12349', date: '27 Feb 2026', amount: '51000', ageDays: 15, ageLabel: 'Age: 15 Days', ageType: 'normal', type: 'dr' },
  { id: 'INV12350', date: '20 Feb 2026', amount: '68000', ageDays: 45, ageLabel: 'Age: 45 Days', ageType: 'normal', type: 'cr' },
  { id: 'INV12345', date: '15 Feb 2026', amount: '20000', ageDays: 35, ageLabel: 'Age: 35 Days', ageType: 'normal', type: 'dr' },
  { id: 'INV12346', date: '09 Feb 2026', amount: '59000', ageDays: 78, ageLabel: 'Overdue 75+ Days', ageType: 'overdue', type: 'dr' },
  { id: 'INV12347', date: '31 Jan 2026', amount: '15500', ageDays: 15, ageLabel: 'Age: 15 Days', ageType: 'normal', type: 'dr' },
  { id: 'INV12348', date: '25 Jan 2026', amount: '81000', ageDays: 45, ageLabel: 'Age: 45 Days', ageType: 'normal', type: 'cr' },
  { id: 'INV12345', date: '19 Jan 2026', amount: '60000', ageDays: 35, ageLabel: 'Age: 35 Days', ageType: 'normal', type: 'dr' },
  { id: 'INV12346', date: '05 Jan 2026', amount: '50000', ageDays: 78, ageLabel: 'Overdue 75+ Days', ageType: 'overdue', type: 'dr' },
  { id: 'INV12347', date: '01 Jan 2026', amount: '15500', ageDays: 15, ageLabel: 'Age: 15 Days', ageType: 'normal', type: 'dr' },
];

const PALETTE = {
  bg: '#F4F6FA',
  card: '#FFFFFF',
  accent: '#C0392B',
  accentLight: '#FDECEA',
  overdueBg: '#C0392B',
  overdueText: '#FFFFFF',
  normalBg: '#FFF8E1',
  normalText: '#B7791F',
  text: '#1A1D23',
  subtext: '#7B8290',
  border: '#E8ECF2',
  tabActiveBg: '#C0392B',
  tabInactiveBg: '#FFFFFF',
  divider: '#EEF1F7',
  shadow: '#B0BAD0',
};

function getTabRange(tab) {
  if (tab === 'All') return [0, Infinity];
  if (tab === '0-30') return [0, 30];
  if (tab === '31-60') return [31, 60];
  if (tab === '60+') return [61, Infinity];
  return [0, Infinity];
}

function InvoiceCard({ item }) {
  const isOverdue = item.ageType === 'overdue';

  return (
    <View style={{ backgroundColor: PALETTE.card, borderRadius: moderateScale(14), marginBottom: moderateScale(12), borderLeftWidth: moderateScale(4), borderLeftColor: isOverdue ? PALETTE.accent : '#C5CAE9', shadowColor: PALETTE.shadow, shadowOffset: { width: 0, height: moderateScale(3) }, shadowOpacity: 0.12, shadowRadius: moderateScale(8), elevation: 3, }} >
      <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: moderateScale(16), paddingTop: moderateScale(14), paddingBottom: moderateScale(12), }} >
        <View style={{ flexDirection: 'row', alignItems: 'center', gap: moderateScale(10) }}>
          <View style={{ width: moderateScale(40), height: moderateScale(40), borderRadius: moderateScale(10), backgroundColor: isOverdue ? PALETTE.accentLight : '#EEF2FF', alignItems: 'center', justifyContent: 'center', }} >
            <Text style={{ fontSize: moderateScale(18) }}>🧾</Text>
          </View>
          <View>
            <Text style={{ fontSize: moderateScale(15), fontWeight: '700', color: PALETTE.text, letterSpacing: 0.2, }} > {item.id} </Text>
            <Text style={{ fontSize: moderateScale(12), color: PALETTE.subtext, marginTop: moderateScale(2), }} > {item.date} </Text>
          </View>
        </View>

        <View style={{ paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(5), borderRadius: moderateScale(20), backgroundColor: isOverdue ? PALETTE.overdueBg : PALETTE.normalBg, }} >
          <Text style={{ fontSize: moderateScale(11), fontWeight: '600', color: isOverdue ? PALETTE.overdueText : PALETTE.normalText, letterSpacing: 0.2, }} > {item.ageLabel} </Text>
        </View>
      </View>

      <View style={{ height: 1, backgroundColor: PALETTE.divider, marginHorizontal: moderateScale(16), }} />

      <View style={{ paddingHorizontal: moderateScale(16), paddingVertical: moderateScale(12), }} >
        <Text style={{ fontSize: moderateScale(11), color: PALETTE.subtext, marginBottom: moderateScale(2), }} > Amount Due </Text>
        <Text style={{ fontSize: moderateScale(20), fontWeight: '800', color: isOverdue ? PALETTE.accent : PALETTE.text, letterSpacing: -0.3, }} > {item.amount} </Text>
      </View>
    </View>
  );
}

export default function OverdueInvoicesScreen({ navigation }) {
  const [activeTab, setActiveTab] = useState('All');
  const [type, setType] = useState('scl')
  const [totalCR, setTotalCR] = useState(0)
  const [totalDR, setTotalDR] = useState(0)

  const [min, max] = getTabRange(activeTab);
  const filtered = ALL_INVOICES.filter((inv) => inv.ageDays >= min && inv.ageDays <= max);
  const formatAmount = (amount) => {
    return '₹ ' + parseFloat(amount).toLocaleString('en-IN', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  };
  useEffect(()=>{
    var cr=0
    var dr=0
    for(var i=0;i<filtered.length;i++){
      if(filtered[i].type=='cr')
        cr=cr+parseFloat(filtered[i].amount)
      if(filtered[i].type=='dr')
        dr=dr+parseFloat(filtered[i].amount)
    }
    setTotalCR(cr)
    setTotalDR(dr)
  },[])
  return (
    <SafeView backgroundColor={PALETTE.bg} bar={false} statusbarColor={Colors.main}>
      <SBSCommonHeaderView title="Overdue Invoices" backPath=" " />

      <View style={{ flex: 1, backgroundColor: PALETTE.bg }}>
        {/* <SummaryStrip invoices={ALL_INVOICES} /> */}
        <View style={{ flexDirection: 'row', paddingHorizontal: moderateScale(16), paddingVertical: moderateScale(10), gap: moderateScale(8), }} >
          {TABS.map((tab) => {
            const isActive = activeTab === tab.key;
            return (
              <TouchableOpacity
                key={tab.key}
                onPress={() => setActiveTab(tab.key)}
                activeOpacity={0.8}
                style={{ flex: 1, alignItems: 'center', paddingVertical: moderateScale(8), borderRadius: moderateScale(8), backgroundColor: isActive ? PALETTE.accent : PALETTE.card, borderWidth: isActive ? 0 : 1, borderColor: PALETTE.border, shadowColor: isActive ? PALETTE.accent : PALETTE.shadow, shadowOffset: { width: 0, height: isActive ? 4 : 1 }, shadowOpacity: isActive ? 0.25 : 0.07, shadowRadius: isActive ? 8 : 3, elevation: isActive ? 5 : 1, }} >
                <Text style={{ fontSize: moderateScale(11), fontWeight: isActive ? '700' : '500', color: isActive ? '#fff' : PALETTE.subtext, letterSpacing: 0.2, textAlign: 'center', }} numberOfLines={1} adjustsFontSizeToFit > {tab.label} </Text>
              </TouchableOpacity>
            );
          })}
        </View>

        <View style={{ width: '100%', flexDirection: 'row', paddingHorizontal: moderateScale(15) }}>
          <TouchableOpacity onPress={() => setType('scl')} style={{ flex: 1, alignItems: 'center', paddingVertical: moderateScale(8), borderRadius: moderateScale(8), backgroundColor: type == 'scl' ? PALETTE.accent : PALETTE.card, borderWidth: type == 'scl' ? 0 : 1, borderColor: PALETTE.border, shadowColor: type == 'scl' ? PALETTE.accent : PALETTE.shadow, shadowOffset: { width: 0, height: type == 'scl' ? 4 : 1 }, shadowOpacity: type == 'scl' ? 0.25 : 0.07, shadowRadius: type == 'scl' ? 8 : 3, elevation: type == 'scl' ? 5 : 1, }}>
            <Text style={{ fontSize: moderateScale(11), fontWeight: type == 'scl' ? '700' : '500', color: type == 'scl' ? '#fff' : PALETTE.subtext, letterSpacing: 0.2, textAlign: 'center', }}>SCL</Text>
          </TouchableOpacity>
          <View style={{ width: moderateScale(15) }} />
          <TouchableOpacity onPress={() => setType('scnel')} style={{ flex: 1, alignItems: 'center', paddingVertical: moderateScale(8), borderRadius: moderateScale(8), backgroundColor: type == 'scnel' ? PALETTE.accent : PALETTE.card, borderWidth: type == 'scnel' ? 0 : 1, borderColor: PALETTE.border, shadowColor: type == 'scnel' ? PALETTE.accent : PALETTE.shadow, shadowOffset: { width: 0, height: type == 'scnel' ? 4 : 1 }, shadowOpacity: type == 'scnel' ? 0.25 : 0.07, shadowRadius: type == 'scnel' ? 8 : 3, elevation: type == 'scnel' ? 5 : 1, }}>
            <Text style={{ fontSize: moderateScale(11), fontWeight: type == 'scnel' ? '700' : '500', color: type == 'scnel' ? '#fff' : PALETTE.subtext, letterSpacing: 0.2, textAlign: 'center', }}>SCNEL</Text>
          </TouchableOpacity>
        </View>
        <View style={{ height: moderateScale(10) }} />

        <View style={{ width: '100%', flexDirection: 'row', backgroundColor: '#C0392B90' }}>
          <Text style={{ flex: 1, color: '#000000', fontSize: moderateScale(13), textAlign: 'center', paddingVertical: moderateScale(4) }}>Invoice No</Text>
          <View style={{ width: 1, backgroundColor: '#FFF' }} />
          <Text style={{ flex: 1, color: '#000000', fontSize: moderateScale(13), textAlign: 'center', paddingVertical: moderateScale(4) }}>Date</Text>
          <View style={{ width: 1, backgroundColor: '#FFF' }} />
          <Text style={{ flex: 1, color: '#000000', fontSize: moderateScale(13), textAlign: 'center', paddingVertical: moderateScale(4) }}>{type.toUpperCase()} (₹) DR</Text>
          <View style={{ width: 1, backgroundColor: '#FFF' }} />
          <Text style={{ flex: 1, color: '#000000', fontSize: moderateScale(13), textAlign: 'center', paddingVertical: moderateScale(4) }}>{type.toUpperCase()} (₹) CR</Text>
        </View>

        <View style={{ width: '100%', flex: 1 }}>
          <FlatList
            data={filtered}
            keyExtractor={(item, idx) => `${item.id}-${idx}`}
            showsVerticalScrollIndicator={false}
            renderItem={({ item }) => {
              return <View style={{ flexDirection: 'column' }}>
                <View style={{ width: '100%', flexDirection: 'row', backgroundColor: '#FFF' }}>
                  <Text style={{ flex: 1, color: '#444', fontSize: moderateScale(13), textAlign: 'center', paddingVertical: moderateScale(4) }}>{item.id}</Text>
                  <View style={{ width: 1, backgroundColor: '#aaa' }} />
                  <Text style={{ flex: 1, color: '#444', fontSize: moderateScale(13), textAlign: 'center', paddingVertical: moderateScale(4) }}>{moment(item.date, 'DD MMM YYYY').format('DD/MM/YYYY')}</Text>
                  <View style={{ width: 1, backgroundColor: '#aaa' }} />
                  <Text style={{ flex: 1, color: '#444', fontSize: moderateScale(13), textAlign: 'center', paddingVertical: moderateScale(4) }}>{item.type == 'dr' ? formatAmount(item.amount) : '-'}</Text>
                  <View style={{ width: 1, backgroundColor: '#aaa' }} />
                  <Text style={{ flex: 1, color: '#444', fontSize: moderateScale(13), textAlign: 'center', paddingVertical: moderateScale(4) }}>{item.type == 'cr' ? formatAmount(item.amount) : '-'}</Text>
                </View>
                <View style={{ width: '100%', height: 1, backgroundColor: '#aaa' }} />
              </View>
            }}
          />
        </View>

        <View style={{ width: '100%', flexDirection: 'row', backgroundColor: '#C0392B90' }}>
          <Text style={{ flex: 2, color: '#000000', fontSize: moderateScale(14), textAlign: 'center', paddingVertical: moderateScale(4),fontWeight:'500' }}>Total Outstanding</Text>
          <View style={{ width: 1, backgroundColor: '#FFF' }} />
          <Text style={{ flex: 1, color: '#000000', fontSize: moderateScale(14), textAlign: 'center', paddingVertical: moderateScale(4),fontWeight:'500' }}>{formatAmount(totalDR)}</Text>
          <View style={{ width: 1, backgroundColor: '#FFF' }} />
          <Text style={{ flex: 1, color: '#000000', fontSize: moderateScale(14), textAlign: 'center', paddingVertical: moderateScale(4),fontWeight:'500' }}>{formatAmount(totalCR)}</Text>
        </View>
        {/* <View style={{ paddingHorizontal: moderateScale(16), marginBottom: moderateScale(4) }}>
          <Text style={{ fontSize: moderateScale(12), color: PALETTE.subtext, fontWeight: '500' }}>
            {filtered.length} {filtered.length === 1 ? 'invoice' : 'invoices'} found
          </Text>
        </View>

        <FlatList
          data={filtered}
          keyExtractor={(item, idx) => `${item.id}-${idx}`}
          contentContainerStyle={{ paddingHorizontal: moderateScale(16), paddingTop: moderateScale(6), paddingBottom: Platform.OS === 'ios' ? moderateScale(30) : moderateScale(140), flexGrow: 1, }}
          showsVerticalScrollIndicator={false}
          ListEmptyComponent={
            <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center', paddingTop: moderateScale(60), }} >
              <View style={{ width: moderateScale(72), height: moderateScale(72), borderRadius: moderateScale(36), backgroundColor: PALETTE.successBg, alignItems: 'center', justifyContent: 'center', marginBottom: moderateScale(14), }} >
                <Text style={{ fontSize: moderateScale(32) }}>📭</Text>
              </View>
              <Text style={{ color: PALETTE.text, fontSize: moderateScale(15), fontWeight: '700', marginBottom: moderateScale(4), }} > No invoices here </Text>
              <Text style={{ color: PALETTE.subtext, fontSize: moderateScale(13), textAlign: 'center', lineHeight: moderateScale(20), }} > No invoices fall in this age range.{'\n'}Try selecting a different tab. </Text>
            </View>
          }
          renderItem={({ item }) => <InvoiceCard item={item} />}
        /> */}
      </View>
    </SafeView>
  );
}