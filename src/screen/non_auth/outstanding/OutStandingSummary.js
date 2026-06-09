import { ScrollView, Text, TouchableOpacity, View, FlatList } from "react-native"
import SafeView from "../../../helper/SafeView"
import { Colors } from "../../../assets/Colors"
import SBSCommonHeaderView from "../../../common/SBSCommonHeaderView"
import { moderateScale } from "../../../helper/Window"
import { useEffect, useState } from "react"
import UrlStorage from "../../../storage/UrlStorage"
import toastConfig from "../../../helper/ToastConfig"
import Toast from "react-native-toast-message"
import Loader from "../../../common/Loader"

const NUM_COLUMNS = 3;

const OutstandingSummaryScreen = (props) => {
  const colorCodes = [
    '#2ECC71',
    '#3498DB',
    '#9B59B6',
    '#F39C12',
    '#1ABC9C',
    '#E67E22',
    '#E74C3C',
    '#C0392B',
    '#7F8C8D'
  ]

  const [segments, setSegments] = useState([])
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true)
    const requestOptions = {
      method: "GET",
      redirect: "follow"
    };

    console.log("broker", UrlStorage.ParameterList.BasicData.customerDetails.customer_code);
    

    fetch(UrlStorage.BaseUrlList.Saathi.base_url_saathi + "/customer_ageing_api.php?customer_code=" + (UrlStorage.ParameterList.BasicData.user_type.toLowerCase() == 'broker' ? UrlStorage.ParameterList.BasicData.customerDetails.customer_code: UrlStorage.ParameterList.BasicData.customer_code), requestOptions)
      .then((response) => response.json())
      .then((result) => {
        //console.log(result)
        if (result.status) {
          var arr = [];
          for (i = 0; i < result.data.length; i++) {
            var obj = {}
            if (i == result.data.length - 1) {
              obj = {
                label: `${i == 0 ? 0 : parseInt(result.data[i - 1].title.toString().replace("Day", "").replace("Abv", ""))}+ Days`,
                value: parseInt(result.data[i].value),
                color: colorCodes[i],
                formattedValue: `₹${Number(parseFloat(result.data[i].value)).toLocaleString('en-IN')}`
              }
            } else {
              obj = {
                label: `${i == 0 ? 0 : parseInt(result.data[i - 1].title.toString().replace("Day", "").replace("Abv", "")) + 1}-${parseInt(result.data[i].title.toString().replace("Day", "").replace("Abv", ""))} Days`,
                value: parseInt(result.data[i].value),
                color: colorCodes[i],
                formattedValue: `₹${Number(parseFloat(result.data[i].value)).toLocaleString('en-IN')}`
              }
            }
            arr.push(obj)
          }
          setSegments(arr)
          //console.log("out-----", arr);

          setLoading(false)
        } else {
          Toast.show({ type: "error", text1: "Sorry...", text2: result.message });
        }

      })
      .catch((error) => console.error(error));
  }, [])

  const renderSegmentCard = ({ item, index }) => {
    const isLastInRow = (index + 1) % NUM_COLUMNS === 0;
    return (
      <TouchableOpacity
        // onPress={() => 
        //   props?.navigation.navigate("OverdueInvoicesScreen")
        // } 
        activeOpacity={0.85} style={{ flex: 1, overflow: 'hidden', backgroundColor: '#ffffff', borderRadius: moderateScale(10), shadowColor: item.color, shadowOffset: { width: 0, height: 3 }, shadowOpacity: 0.2, shadowRadius: 6, elevation: 4, marginEnd: isLastInRow ? 0 : moderateScale(8), marginBottom: moderateScale(10), alignItems: 'center', borderTopWidth: moderateScale(4), borderTopColor: item.color, }} >
        <View style={{ paddingHorizontal: moderateScale(4), paddingTop: moderateScale(8), paddingBottom: moderateScale(10), alignItems: 'center', width: '100%' }}>
          <Text style={{ fontSize: moderateScale(10), fontWeight: '700', color: item.color, textAlign: 'center', letterSpacing: 0.3 }} numberOfLines={1} adjustsFontSizeToFit > {item.label} </Text>
          <View style={{ width: '60%', height: 0.8, backgroundColor: '#e0e0e0', marginVertical: moderateScale(5) }} />
          <Text style={{ fontSize: moderateScale(12), fontWeight: '800', color: '#1a1a1a', textAlign: 'center' }} numberOfLines={1} adjustsFontSizeToFit > {item.formattedValue} </Text>
        </View>
      </TouchableOpacity>
    );
  };



  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <SBSCommonHeaderView title={"Invoice Ageing Summary"} backPath=" " />
      <ScrollView style={{ width: "100%", height: "100%", backgroundColor: '#F4F6F9', paddingHorizontal: moderateScale(15) }} nestedScrollEnabled showsVerticalScrollIndicator={false} >
        <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
          <View style={{ backgroundColor: '#ffffff', borderRadius: moderateScale(14), padding: moderateScale(16), shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.07, shadowRadius: 10, elevation: 5, width: '100%', marginTop: moderateScale(14), }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', marginBottom: moderateScale(14), gap: moderateScale(6) }}>
              <Text style={{ fontSize: moderateScale(15), fontWeight: '700', color: '#1a1a1a' }}> Invoice Ageing Summary </Text>
            </View>


            {segments.map((seg, i) => {
              var total = segments.reduce((s, r) => s + r.value, 0);
              if (total == 0) total = 1;
              const pct = Math.round((seg.value / total) * 100);

              return (
                <TouchableOpacity
                  key={i}
                  activeOpacity={0.7}
                  // onPress={() => props?.navigation.navigate("OverdueInvoicesScreen")}
                  style={{ flexDirection: 'row', alignItems: 'center', paddingVertical: moderateScale(8), borderBottomWidth: i !== segments.length - 1 ? 0.6 : 0, borderBottomColor: '#f0f0f0', gap: moderateScale(8) }}
                >
                  <View style={{ width: moderateScale(9), height: moderateScale(9), borderRadius: moderateScale(5), backgroundColor: seg.color }} />

                  <Text style={{ width: moderateScale(64), fontSize: moderateScale(11), color: '#333', fontWeight: '500' }}> {seg.label} </Text>

                  <View style={{ flex: 1, height: moderateScale(7), backgroundColor: '#ebebeb', borderRadius: moderateScale(4), overflow: 'hidden' }}>
                    <View style={{ width: `${pct}%`, height: '100%', backgroundColor: seg.color, borderRadius: moderateScale(4) }} />
                  </View>

                  <Text style={{ width: moderateScale(34), fontSize: moderateScale(11), color: '#888', textAlign: 'right' }}> {pct}% </Text>
                  <Text style={{ width: moderateScale(85), fontSize: moderateScale(11), fontWeight: '700', color: '#1a1a1a', textAlign: 'right' }} numberOfLines={1} adjustsFontSizeToFit minimumFontScale={0.7}> {seg.formattedValue} </Text>
                  <Text style={{ fontSize: moderateScale(14), color: '#aaa' }}>›</Text>
                </TouchableOpacity>
              );
            })}

            <View style={{ flexDirection: 'row', paddingVertical: moderateScale(10), marginTop: moderateScale(6), borderTopWidth: 0.6, borderTopColor: '#e0e0e0', alignItems: 'center', marginEnd: moderateScale(10) }}>
              <View style={{ width: moderateScale(9 + 8) }} />
              <Text style={{ width: moderateScale(64), fontSize: moderateScale(11), color: '#333', fontWeight: '500' }}>Total</Text>
              <View style={{ flex: 1 }} />
              <Text style={{ width: moderateScale(34), fontSize: moderateScale(11), color: '#888', textAlign: 'right' }}>100%</Text>
              <Text style={{ width: moderateScale(85), fontSize: moderateScale(11), fontWeight: '700', color: '#1a1a1a', textAlign: 'right' }} numberOfLines={1} adjustsFontSizeToFit minimumFontScale={0.7}>
                ₹{Number(segments.reduce((s, r) => s + r.value, 0)).toLocaleString('en-IN')}
              </Text>
              <Text style={{ fontSize: moderateScale(14), color: 'transparent' }}>›</Text>
            </View>
          </View>
          {/* <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: moderateScale(20), marginBottom: moderateScale(10) }}>
          <View style={{ flex: 1, height: 0.8, backgroundColor: '#d0d0d0' }} />
          <Text style={{ marginHorizontal: moderateScale(10), fontSize: moderateScale(11), color: '#888', fontWeight: '600', letterSpacing: 0.5 }}>
            BUCKET SUMMARY
          </Text>
          <View style={{ flex: 1, height: 0.8, backgroundColor: '#d0d0d0' }} />
        </View> */}
          {/* <FlatList
          data={segments}
          keyExtractor={(_, index) => index.toString()}
          renderItem={renderSegmentCard}
          numColumns={NUM_COLUMNS}
          scrollEnabled={false}
          columnWrapperStyle={{ justifyContent: 'space-between' }}
          style={{ marginBottom: moderateScale(24) }}
        /> */}
        </View>
      </ScrollView>
      <Toast config={toastConfig} />
      {loading ? <Loader /> : null}
    </SafeView>
  );
};

export default OutstandingSummaryScreen;