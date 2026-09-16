import React, { useEffect, useState } from 'react'
import { View, Text, FlatList } from 'react-native'
import SafeView from '../../../helper/SafeView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import DataStorage from '../../../storage/DataStorage'
import formatINR from '../../../helper/formatINR'
import AgeingHeaderView from '../../../common/AgeingHeaderView'
import moment from 'moment'

export default function OverdueInvoicesListScreen(props) {
  const [dataSet, setDataSet] = useState(DataStorage.dayWiseAgeingList)
  const [totalAmount, setTotalAmount] = useState(0)

  const [crAmount, setCrAmount] = useState(0)
  const [crCount, setCrCount] = useState(0)
  const [drAmount, setDrAmount] = useState(0)
  const [drCount, setDrCount] = useState(0)

  useEffect(() => {
    var amount = 0
    var crAmount = 0
    var drAmount = 0
    var crCount = 0
    var drCount = 0
    for (var i = 0; i < dataSet.length; i++) {
      if (dataSet[i].cr_dr == 'CR') {
        amount -= dataSet[i].amount
        crAmount += dataSet[i].amount
        crCount++
      } else {
        amount += dataSet[i].amount
        drAmount += dataSet[i].amount
        drCount++
      }
    }
    setTotalAmount(amount)
    setCrAmount(crAmount)
    setCrCount(crCount)
    setDrAmount(drAmount)
    setDrCount(drCount)
  }, [])

  return (
    <SafeView backgroundColor={'#F4F6FA'} bar={false} statusbarColor={Colors.main}>
      <AgeingHeaderView title={"Uncleared Invoices"} subTitle={'Due on ' + moment(new Date()).format('DD MMM, YYYY')} Information={false} />
      <View style={{ flex: 1, backgroundColor: '#F4F6FA' }}>
        <View style={{ backgroundColor: '#ffffff', borderRadius: moderateScale(14), padding: moderateScale(16), shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.07, shadowRadius: 10, elevation: 5, marginHorizontal: moderateScale(10), marginTop: moderateScale(10) }}>
          <View style={{ flexDirection: 'row', alignItems: 'center', gap: moderateScale(6) }}>
            <Text style={{ fontSize: moderateScale(12), fontWeight: '500', color: '#444444', flex: 1 }}> Total Outstanding </Text>
            <Text style={{ fontSize: moderateScale(16), fontWeight: '700', color: '#E74C3C', paddingRight: moderateScale(5) }}> ₹{formatINR(totalAmount)} </Text>
          </View>
          <View style={{ marginVertical: moderateScale(10), width: '100%', height: 1, backgroundColor: '#EEE' }} />
          <View style={{ flexDirection: 'row', alignItems: 'center', gap: moderateScale(6) }}>
            <Text style={{ fontSize: moderateScale(12), fontWeight: '500', color: '#444444', flex: 1 }}> No. of Transactions </Text>
            <Text style={{ fontSize: moderateScale(16), fontWeight: '700', color: '#000000', paddingRight: moderateScale(5) }}> {dataSet.length} </Text>
          </View>
        </View>
        <View style={{ height: moderateScale(10) }} />
        <View style={{ width: '100%', flex: 1, paddingHorizontal: moderateScale(5) }}>
          <FlatList
            data={dataSet}
            keyExtractor={(item, idx) => `${item.id}-${idx}`}
            showsVerticalScrollIndicator={false}
            ListHeaderComponent={
              <View style={{ width: '100%', flexDirection: 'row', backgroundColor: '#ffe3e3ff', alignItems: 'center', justifyContent: 'center', padding: moderateScale(3) }}>
                <Text style={{ fontSize: moderateScale(11), fontWeight: '500', color: Colors.black, flex: 1, textAlign: 'center' }}>Company{'\n'}Code</Text>
                <Text style={{ fontSize: moderateScale(11), fontWeight: '500', color: Colors.black, flex: 1.5, textAlign: 'center' }}>Document{'\n'}Details</Text>
                <Text style={{ fontSize: moderateScale(11), fontWeight: '500', color: Colors.black, flex: .75, textAlign: 'center' }}>Amount{'\n'}CR/DR</Text>
                <Text style={{ fontSize: moderateScale(11), fontWeight: '500', color: Colors.black, flex: 1.25, textAlign: 'center' }}>Amount (₹)</Text>
              </View>
            }
            renderItem={({ item, index }) => {
              return <View style={{ flexDirection: 'column' }}>
                <View style={{ width: '100%', flexDirection: 'row', backgroundColor: '#FFF', alignItems: 'center', justifyContent: 'center', padding: moderateScale(3) }}>
                  <Text style={{ fontSize: moderateScale(11), color: Colors.black, flex: 1, textAlign: 'center' }}>{item.companyCode}</Text>
                  <Text style={{ fontSize: moderateScale(11), color: Colors.black, flex: 1.5 }}>{item.invoiceId + ' (' + item.invoiceNarration + ')' + '\n' + item.invoiceDate}</Text>
                  <Text style={{ fontSize: moderateScale(11), fontWeight: '500', color: item.cr_dr == 'DR' ? '#E74C3C' : '#06ad3bff', flex: .75, textAlign: 'center' }}>{item.cr_dr}</Text>
                  <Text style={{ fontSize: moderateScale(11), fontWeight: '500', color: item.cr_dr == 'DR' ? '#E74C3C' : '#06ad3bff', flex: 1.25, textAlign: 'right', paddingRight: moderateScale(5) }}>₹{formatINR(item.amount)}</Text>
                </View>
                <View style={{ width: '100%', height: 1, backgroundColor: '#eee' }} />
              </View>
            }}
          />
        </View>
        <View style={{ height: moderateScale(10) }} />
      </View>
    </SafeView>
  )
}