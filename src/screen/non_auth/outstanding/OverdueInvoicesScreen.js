import React, { useEffect, useState } from 'react';
import { View, Text, TouchableOpacity, FlatList, Image, } from 'react-native';
import SafeView from '../../../helper/SafeView';
import { Colors } from '../../../assets/Colors';
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView';
import { moderateScale } from '../../../helper/Window';
import moment from 'moment';
import DataStorage from '../../../storage/DataStorage';
import { Icons } from '../../../assets/Icons';
import formatINR from '../../../helper/formatINR';
import AgeingHeaderView from '../../../common/AgeingHeaderView';
import UrlStorage from '../../../storage/UrlStorage';
import Loader from '../../../common/Loader';

export default function OverdueInvoicesScreen(props) {
  const [daySet, setDaySet] = useState([])
  const [totalAmount, setTotalAmount] = useState(0)
  const [paymentAmount, setPaymentAmount] = useState(0)
  const [seletedCount, setSeletedCount] = useState(0)
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    requestForAgeInformation()
  }, [])

  const requestForAgeInformation = () => {
    setLoading(true)
    const requestOptions = {
      method: "GET",
      redirect: "follow"
    };
    var url = ''
    var startday = 0, endday = 0;
    var c1 = false
    if (DataStorage.ageingObj.endDate == 0) {
      c1 = true
      startday = 91
      endday = 2000
    } else {
      startday = DataStorage.ageingObj.label.split(' ')[0].split('-')[0]
      endday = DataStorage.ageingObj.label.split(' ')[0].split('-')[1]
    }
    if (UrlStorage.ParameterList.BasicData.user_type.toLowerCase() == 'broker') {
      url = "https://starsaathi.com/SAP/customer_ageing_details_api.php?type=" + DataStorage.type + "&customer_id=" + UrlStorage.ParameterList.BasicData.customerDetails.SAP_code + "&startday=" + startday + "&endday=" + endday
    } else {
      url = "https://starsaathi.com/SAP/customer_ageing_details_api.php?type=" + DataStorage.type + "&customer_id=" + UrlStorage.ParameterList.BasicData.emp_id + "&startday=" + startday + "&endday=" + endday
    }
    console.log(url);
    fetch(url, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        var arr = []
        var totalAmount = 0
        for (var i = 0; i < result.data.length; i++) {
          var amount = 0
          var obj = {
            label: result.data[i].title,
            subLabel: result.data[i].title +" overdue",
          }
          var list = []
          var a = 0
          for (var j = 0; j < result.data[i].data.length; j++) {
            var o = result.data[i].data[j]
            var checker = parseInt(o.dr) == 0
            var innerObj = {
              invoiceId: o.reference,
              amount: checker ? parseFloat(o.cr) : parseFloat(o.dr),
              invoiceDate: o.postingdate,
              companyCode: o.company_name,
              invoiceNarration: o.doctype_name,
              cr_dr: checker ? "CR" : "DR",
              isSelect: false
            }

            if (checker)
              a -= parseFloat(o.cr)
            else
              a += parseFloat(o.dr)

            list.push(innerObj)

          }

          amount += a
          totalAmount += a
          obj = { ...obj, list, amount }

          if (amount != 0)
            arr.push(obj)

        }

        setDaySet(arr)
        setTotalAmount(totalAmount)
        setLoading(false)
      })
      .catch((error) => {
        setLoading(false)
      });
  }

  return (
    <SafeView backgroundColor={'#F4F6FA'} bar={false} statusbarColor={Colors.main}>
      <AgeingHeaderView title={DataStorage.ageingObj.label + ' Details'} subTitle='' Information={false} />
      <View style={{ flex: 1, backgroundColor: '#F4F6FA' }}>
        <View style={{ backgroundColor: '#ffffff', borderRadius: moderateScale(14), padding: moderateScale(16), shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.07, shadowRadius: 10, elevation: 5, marginHorizontal: moderateScale(10), marginTop: moderateScale(10) }}>
          <View style={{ flexDirection: 'row', alignItems: 'center', gap: moderateScale(6) }}>
            <Text style={{ fontSize: moderateScale(12), fontWeight: '500', color: '#444444', flex: 1 }}> Total Outstanding ({DataStorage.ageingObj.label}) </Text>
            <Text style={{ fontSize: moderateScale(22), fontWeight: '700', color: '#E74C3C', paddingRight: moderateScale(15) }}> ₹{formatINR(totalAmount)} </Text>
          </View>
        </View>
        <View style={{ height: moderateScale(10) }} />
        <View style={{ width: '100%', flex: 1 }}>
          <FlatList
            data={daySet}
            keyExtractor={(item, idx) => `${item}-${idx}`}
            showsVerticalScrollIndicator={false}
            renderItem={({ item, index }) => {
              return <View style={{ backgroundColor: '#ffffff', borderRadius: moderateScale(14), padding: moderateScale(16), shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.07, shadowRadius: 10, elevation: 1, marginHorizontal: moderateScale(10), marginVertical: moderateScale(4) }}>
                <TouchableOpacity
                  onPress={() => {
                    DataStorage.dayWiseAgeingList = item.list
                    props?.navigation.navigate("OverdueInvoicesListScreen")
                  }}
                  style={{ flexDirection: 'row', alignItems: 'center', gap: moderateScale(6) }}>
                  <Text style={{ fontSize: moderateScale(16), fontWeight: '600', color: '#444444' }}> {item.label}</Text>
                  <Text style={{ fontSize: moderateScale(10), fontWeight: '400', color: '#666' }}>( {item.subLabel} )</Text>
                  <View style={{ flex: 1 }} />
                  <Text style={{ fontSize: moderateScale(18), fontWeight: '700', color: '#222' }}> ₹{formatINR(item.amount)} </Text>
                  <Image source={Icons.rightArrow} style={{ width: 15, height: 15, tintColor: '#222' }} />
                </TouchableOpacity>
              </View>
            }}
          />
        </View>
        <View style={{ height: moderateScale(10) }} />
        {seletedCount != 0 ? <>
          <View style={{ backgroundColor: '#ffffff', borderRadius: moderateScale(14), shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.07, shadowRadius: 10, elevation: 5, marginHorizontal: moderateScale(10), marginTop: moderateScale(10) }}>
            <View style={{ backgroundColor: '#ffffff', borderRadius: moderateScale(14), shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.07, shadowRadius: 10, elevation: 1, marginHorizontal: moderateScale(10), marginTop: moderateScale(10) }}>
              <View style={{ flexDirection: 'row', alignItems: 'center', gap: moderateScale(6), backgroundColor: '#E74C3C10', padding: moderateScale(16), borderRadius: moderateScale(14) }}>
                <Text style={{ fontSize: moderateScale(12), fontWeight: '500', color: '#444444', flex: 1 }}> Total Selected ({seletedCount}) </Text>
                <Text style={{ fontSize: moderateScale(22), fontWeight: '700', color: '#E74C3C' }}> ₹{Number(paymentAmount).toLocaleString('en-IN')} </Text>
              </View>
            </View>
            <View style={{ height: moderateScale(10) }} />
            <View style={{ flexDirection: 'row', alignItems: 'center', paddingHorizontal: moderateScale(16) }}>
              <View style={{ flex: 1, paddingVertical: moderateScale(8), borderWidth: 1, backgroundColor: '#E41B14', borderColor: '#E74C3C50', borderRadius: moderateScale(3), alignItems: 'center', justifyContent: 'center', shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.07, shadowRadius: 10, elevation: 1, }}>
                <Text style={{ fontSize: moderateScale(12), fontWeight: '700', color: '#FFF' }}>Pay Selected Invoice</Text>
              </View>
              <View style={{ width: moderateScale(6) }} />
              <View style={{ flex: 1, paddingVertical: moderateScale(8), borderWidth: 1, borderColor: '#E74C3C50', backgroundColor: '#FFF', borderRadius: moderateScale(3), alignItems: 'center', justifyContent: 'center', shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.07, shadowRadius: 10, elevation: 1, }}>
                <Text style={{ fontSize: moderateScale(12), fontWeight: '700', color: '#1a1a1a' }}>Download Invoice List</Text>
              </View>
            </View>
            <View style={{ height: moderateScale(10) }} />
          </View>
          <View style={{ height: moderateScale(10) }} />
        </> : null}
      </View>
      {loading ? <Loader /> : null}
    </SafeView>
  );
}