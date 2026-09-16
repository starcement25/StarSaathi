import { ScrollView, Text, TouchableOpacity, View, FlatList, Image } from "react-native"
import SafeView from "../../../helper/SafeView"
import { Colors } from "../../../assets/Colors"
import { moderateScale } from "../../../helper/Window"
import { useEffect, useState } from "react"
import UrlStorage from "../../../storage/UrlStorage"
import toastConfig from "../../../helper/ToastConfig"
import Toast from "react-native-toast-message"
import Loader from "../../../common/Loader"
import moment from "moment"
import formatINR from "../../../helper/formatINR"
import AgeingHeaderView from "../../../common/AgeingHeaderView"
import { Icons } from "../../../assets/Icons"

const NUM_COLUMNS = 3

const OutstandingSummaryScreen = (props) => {
  const [segments, setSegments] = useState([])
  const [type, setType] = useState('combined')
  const [loading, setLoading] = useState(true)
  const [dayList, setDayList] = useState([])
  const [isShow, setIsShow] = useState(false)
  const [title, setTitle] = useState('')

  useEffect(() => {
    requestForBucket()
  }, [type])

  const requestForBucket = () => {
    const requestOptions = {
      method: "GET",
      redirect: "follow"
    }

    fetch(UrlStorage.BaseUrlList.Saathi.base_url_saathi + "/day_range_list.php", requestOptions)
      .then((response) => response.json())
      .then((result) => requestForAgeInformation(result.data))
      .catch((error) => { })
  }

  const requestForAgeInformation = (bucketList) => {
    setLoading(true)
    const requestOptions = {
      method: "GET",
      redirect: "follow"
    }
    var url = ''
    var startday = 0, endday = 1000
    if (UrlStorage.ParameterList.BasicData.user_type.toLowerCase() == 'broker')
      url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + "/customer_ageing_details_api.php?type=" + type + "&customer_id=" + UrlStorage.ParameterList.BasicData.customerDetails.SAP_code + "&startday=" + startday + "&endday=" + endday
    else
      url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + "/customer_ageing_details_api.php?type=" + type + "&customer_id=" + UrlStorage.ParameterList.BasicData.emp_id + "&startday=" + startday + "&endday=" + endday
    fetch(url, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        var finalResult = []
        for (var i = 0; i < bucketList.length; i++) {
          var start = bucketList[i].start_day - 1
          var end = bucketList[i].end_day == 0 ? 1000 : bucketList[i].end_day - 1
          var bucketObj = { label: bucketList[i].end_day != 0 ? bucketList[i].start_day + ' - ' + bucketList[i].end_day + ' Days' : (bucketList[i].start_day + 1) + '+ Days', color: '#E74C3C', value: 0.0, formattedValue: `₹${Number(0.0).toLocaleString('en-IN')}` }
          var dayWiseOutstandingList = []
          var totalAmount = 0
          for (var k = 0; k < result.data.length; k++) {
            var amount = 0
            var day = parseInt(result.data[k].title.split(' ')[0])
            if (day >= start && day <= end) {
              var obj = { label: result.data[k].title, subLabel: result.data[k].title + " overdue", }
              var list = []
              var a = 0
              for (var l = 0; l < result.data[k].data.length; l++) {
                var o = result.data[k].data[l]
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
              var isShow = false
              obj = { ...obj, list, amount, isShow }
              if (amount != 0)
                dayWiseOutstandingList.push(obj)
              bucketObj.value = totalAmount
              bucketObj.formattedValue = `₹${parseFloat(totalAmount).toLocaleString('en-IN')}`
            }
          }
          bucketObj = { ...bucketObj, dayWiseOutstandingList }
          finalResult.push(bucketObj)
        }
        setSegments(finalResult)
        setLoading(false)
      })
      .catch((error) => {
        setLoading(false)
      })
  }

  const renderSegmentCard = ({ item, index }) => {
    const isLastInRow = (index + 1) % NUM_COLUMNS === 0
    return (
      <TouchableOpacity
        onPress={() => {
          setDayList(item.dayWiseOutstandingList)
          setIsShow(item.label == title ? !isShow : true)
          setTitle(item.label)
        }}
        activeOpacity={0.85} style={{ flex: 1, overflow: 'hidden', backgroundColor: '#ffffff', borderRadius: moderateScale(10), shadowColor: item.color, shadowOffset: { width: 0, height: 3 }, shadowOpacity: 0.2, shadowRadius: 6, elevation: 4, marginEnd: isLastInRow ? 0 : moderateScale(8), marginBottom: moderateScale(10), alignItems: 'center', borderTopWidth: moderateScale(4), borderTopColor: item.color, }} >
        <View style={{ paddingHorizontal: moderateScale(4), paddingTop: moderateScale(8), paddingBottom: moderateScale(10), alignItems: 'center', width: '100%' }}>
          <Text style={{ fontSize: moderateScale(10), fontWeight: '700', color: item.color, textAlign: 'center', letterSpacing: 0.3 }} numberOfLines={1} adjustsFontSizeToFit > {item.label} </Text>
          <View style={{ width: '60%', height: 0.8, backgroundColor: '#e0e0e0', marginVertical: moderateScale(5) }} />
          <Text style={{ fontSize: moderateScale(12), fontWeight: '800', color: '#1a1a1a', textAlign: 'center' }} numberOfLines={1} adjustsFontSizeToFit > ₹{formatINR(item.value)} </Text>
        </View>
      </TouchableOpacity>
    )
  }

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <AgeingHeaderView title={"Age-wise Outstanding"} subTitle={'As on ' + moment(new Date()).format('DD MMM, YYYY')} Information={false} />
      <ScrollView style={{ width: "100%", height: "100%", backgroundColor: '#F4F6F9', paddingHorizontal: moderateScale(15) }} nestedScrollEnabled showsVerticalScrollIndicator={false} >
        <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
          <View style={{ width: '100%', flexDirection: 'row', paddingHorizontal: moderateScale(5), paddingVertical: moderateScale(5) }}>
            <TouchableOpacity onPress={() => setType('combined')} style={{ flex: 1, alignItems: 'center', paddingVertical: moderateScale(8), borderRadius: moderateScale(2), backgroundColor: type == 'combined' ? '#C0392B' : '#FFFFFF', borderWidth: type == 'combined' ? 0 : 1, borderColor: '#E8ECF2', shadowColor: type == 'combined' ? '#C0392B' : '#B0BAD0', shadowOffset: { width: 0, height: type == 'combined' ? 4 : 1 }, shadowOpacity: type == 'combined' ? 0.25 : 0.07, shadowRadius: type == 'combined' ? 8 : 3, elevation: type == 'combined' ? 5 : 1, }}>
              <Text style={{ fontSize: moderateScale(11), fontWeight: type == 'combined' ? '700' : '500', color: type == 'combined' ? '#fff' : '#AAA', letterSpacing: 0.2, textAlign: 'center', }}>Combined</Text>
            </TouchableOpacity>
            <TouchableOpacity onPress={() => setType('scl')} style={{ flex: 1, alignItems: 'center', paddingVertical: moderateScale(8), borderRadius: moderateScale(2), backgroundColor: type == 'scl' ? '#C0392B' : '#FFFFFF', borderWidth: type == 'scl' ? 0 : 1, borderColor: '#E8ECF2', shadowColor: type == 'scl' ? '#C0392B' : '#B0BAD0', shadowOffset: { width: 0, height: type == 'scl' ? 4 : 1 }, shadowOpacity: type == 'scl' ? 0.25 : 0.07, shadowRadius: type == 'scl' ? 8 : 3, elevation: type == 'scl' ? 5 : 1, }}>
              <Text style={{ fontSize: moderateScale(11), fontWeight: type == 'scl' ? '700' : '500', color: type == 'scl' ? '#fff' : '#AAA', letterSpacing: 0.2, textAlign: 'center', }}>SCL</Text>
            </TouchableOpacity>
            <TouchableOpacity onPress={() => setType('scnel')} style={{ flex: 1, alignItems: 'center', paddingVertical: moderateScale(8), borderRadius: moderateScale(2), backgroundColor: type == 'scnel' ? '#C0392B' : '#FFFFFF', borderWidth: type == 'scnel' ? 0 : 1, borderColor: '#E8ECF2', shadowColor: type == 'scnel' ? '#C0392B' : '#B0BAD0', shadowOffset: { width: 0, height: type == 'scnel' ? 4 : 1 }, shadowOpacity: type == 'scnel' ? 0.25 : 0.07, shadowRadius: type == 'scnel' ? 8 : 3, elevation: type == 'scnel' ? 5 : 1, }}>
              <Text style={{ fontSize: moderateScale(11), fontWeight: type == 'scnel' ? '700' : '500', color: type == 'scnel' ? '#fff' : '#AAA', letterSpacing: 0.2, textAlign: 'center', }}>SCNEL</Text>
            </TouchableOpacity>
          </View>
          <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: moderateScale(5), marginBottom: moderateScale(10) }}>
            <Text style={{ marginHorizontal: moderateScale(10), fontSize: moderateScale(11), color: '#888', fontWeight: '600', letterSpacing: 0.5 }}> As on {moment(new Date()).format('DD MMM, YYYY')} </Text>
            <View style={{ flex: 1, height: 0.8, backgroundColor: '#d0d0d0' }} />
          </View>
          <FlatList
            data={segments}
            keyExtractor={(_, index) => index.toString()}
            renderItem={renderSegmentCard}
            numColumns={NUM_COLUMNS}
            scrollEnabled={false}
            columnWrapperStyle={{ justifyContent: 'space-between' }}
          />
          <View style={{ backgroundColor: '#ffffff', borderRadius: moderateScale(14), padding: moderateScale(16), shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.07, shadowRadius: 10, elevation: 5, width: '100%', }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: moderateScale(6) }}>
              <Text style={{ fontSize: moderateScale(15), fontWeight: '700', color: '#1a1a1a', flex: 1 }}> Invoice Ageing Summary </Text>
              <Text style={{ fontSize: moderateScale(15), fontWeight: '700', color: '#E74C3C' }}> ₹{formatINR(segments.reduce((s, r) => s + r.value, 0))} </Text>
            </View>
          </View>
          {isShow ? <FlatList
            data={dayList}
            keyExtractor={(_, index) => index.toString()}
            renderItem={({ item, index }) => {
              return <View style={{ backgroundColor: '#ffffff', borderRadius: moderateScale(14), shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.07, shadowRadius: 10, elevation: 1, marginVertical: moderateScale(4) }}>
                <TouchableOpacity
                  onPress={() => {
                    setDayList(prev => prev.map((d, i) => i === index ? { ...d, isShow: d.isShow ? false : true } : { ...d, isShow: false }))
                  }}
                  style={{ flexDirection: 'row', alignItems: 'center', padding: moderateScale(16), gap: moderateScale(6) }}>
                  <Text style={{ fontSize: moderateScale(16), fontWeight: '600', color: '#444444' }}> {item.label}</Text>
                  <Text style={{ fontSize: moderateScale(10), fontWeight: '400', color: '#666' }}>( {item.subLabel} )</Text>
                  <View style={{ flex: 1 }} />
                  <Text style={{ fontSize: moderateScale(18), fontWeight: '700', color: '#222' }}> ₹{formatINR(item.amount)} </Text>
                  <Image source={Icons.rightArrow} style={{ width: 15, height: 15, tintColor: '#222' }} />
                </TouchableOpacity>
                {item.isShow == 0 ? null : <>
                  <FlatList
                    data={item.list}
                    keyExtractor={(_, index) => index.toString()}
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
                  <View style={{ height: 10 }} />
                </>}
              </View>
            }}
          /> : null}
        </View>
      </ScrollView>
      <Toast config={toastConfig} />
      {loading ? <Loader /> : null}
    </SafeView>
  )
}

export default OutstandingSummaryScreen