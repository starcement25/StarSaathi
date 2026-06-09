import React, { useState } from 'react';
import { View, Text, TouchableOpacity, FlatList, Platform, } from 'react-native';
import SafeView from '../../../helper/SafeView';
import { Colors } from '../../../assets/Colors';
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView';
import { moderateScale } from '../../../helper/Window';

const TABS = [
  { label: 'All', key: 'All' },
  { label: '0–30 Days', key: '0-30' },
  { label: '31–60 Days', key: '31-60' },
  { label: '60+ Days', key: '60+' },
];

const ALL_INVOICES = [
  { id: 'INV12345', date: '10 Feb 2026', amount: '₹ 20,000', ageDays: 35, ageLabel: 'Age: 35 Days', ageType: 'normal', },
  { id: 'INV12346', date: '01 Jan 2026', amount: '₹ 50,000', ageDays: 78, ageLabel: 'Overdue 75+ Days', ageType: 'overdue', },
  { id: 'INV12347', date: '15 Feb 2026', amount: '₹ 12,500', ageDays: 15, ageLabel: 'Age: 15 Days', ageType: 'normal', },
  { id: 'INV12348', date: '20 Jan 2026', amount: '₹ 8,000', ageDays: 45, ageLabel: 'Age: 45 Days', ageType: 'normal', },
  { id: 'INV12349', date: '15 Feb 2026', amount: '₹ 12,500', ageDays: 15, ageLabel: 'Age: 15 Days', ageType: 'normal', },
  { id: 'INV12350', date: '20 Jan 2026', amount: '₹ 8,000', ageDays: 45, ageLabel: 'Age: 45 Days', ageType: 'normal', },
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

  const [min, max] = getTabRange(activeTab);
  const filtered = ALL_INVOICES.filter((inv) => inv.ageDays >= min && inv.ageDays <= max);

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

        <View style={{ paddingHorizontal: moderateScale(16), marginBottom: moderateScale(4) }}>
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
        />
      </View>
    </SafeView>
  );
}