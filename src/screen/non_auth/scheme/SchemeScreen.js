import React, { useEffect, useState } from 'react'
import { View, Text, FlatList, TouchableOpacity, Image } from 'react-native'
import SafeView from '../../../helper/SafeView'
import Loader from '../../../common/Loader'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import { Icons } from '../../../assets/Icons'
import UrlStorage from '../../../storage/UrlStorage'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const SchemeScreen = ({ navigation }) => {
  const [loading, setLoading] = useState(false)
  const [authChecker, setAuthChecker] = useState(false)
  const [schemes, setSchemes] = useState([])

  useEffect(() => {
    fetchSchemes()
  }, [])

  const fetchSchemes = async () => {
    try {
      setLoading(true)
      var a = await AuthCheckingApi()
      if (!a) {
        setAuthChecker(true)
        setLoading(false)
        return false
      }
      let dealerId = ""
      if (UrlStorage?.ParameterList.BasicData.user_type.toLowerCase() == 'broker') {
        dealerId = UrlStorage.ParameterList.BasicData.customerDetails.SAP_code
      } else if (UrlStorage?.ParameterList.BasicData.user_type.toLowerCase() == 'dealer') {
        dealerId = UrlStorage.ParameterList.BasicData.emp_id
      } else {
        dealerId = UrlStorage.ParameterList.BasicData.emp_id
      }
      const url = `${UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.SchemeUrl.getScheme}` + `?dealer_id=${dealerId}&status=active`
      const res = await fetch(url)
      const json = await res.json()
      if (json.status !== 'success') {
        setSchemes([])
        return
      }
      const list = json?.data?.schemes || []
      const parsed = list.map(s => {
        let slabType = ''
        let applicableQty = 0
        let slabDetailsJson = ''
        let achievementsJson = ''
        if (s.slab_details) {
          slabType = s.slab_details.type || ''
          if (slabType === 'single')
            applicableQty = s.slab_details.applicable_qty || 0
          slabDetailsJson = JSON.stringify(s.slab_details)
        }
        if (s.achievements)
          achievementsJson = JSON.stringify(s.achievements)
        const PDF_BASE_URL = UrlStorage.BaseUrlList.Saathi.base_url_saathi + '/schemes/'
        return {
          schemeId: s.scheme_id,
          schemeName: s.scheme_name,
          category: s.category,
          status: s.status,
          pdfUrl: s.pdf_url ? PDF_BASE_URL + s.pdf_url : '',
          startDate: s.start_date,
          endDate: s.end_date,
          liftingStart: s.lifting_start,
          liftingEnd: s.lifting_end,
          daysRemaining: s.days_remaining || 0,
          slabType,
          applicableQty,
          slabDetailsJson,
          achievementsJson,
        }
      })
      setSchemes(parsed)
    } catch (e) {
      setSchemes([])
    } finally {
      setLoading(false)
    }
  }

  const renderItem = ({ item, index }) => (
    <TouchableOpacity onPress={() => navigation.navigate('SchemeDetailsScreen', item)} style={{ flex: 0.48, ...Platform.select({ ios: { shadowColor: "#000", shadowOffset: { width: 10, height: 10 }, shadowOpacity: 0.08, shadowRadius: 12, }, android: { elevation: 10 }, }), backgroundColor: 'white', borderRadius: moderateScale(20) }} activeOpacity={0.9} >
      <View style={{ backgroundColor: '#fff', borderRadius: moderateScale(10), padding: moderateScale(12), height: moderateScale(150), alignItems: 'center', }} >
        <Image source={Icons.CementScheme} style={{ width: 80, height: 80 }} />
        <Text numberOfLines={2} style={{ marginTop: 8, textAlign: 'center', fontWeight: '600', color: Colors.text, }} > {item.schemeName || `Scheme ${index + 1}`} </Text>
      </View>
    </TouchableOpacity>
  )

  return (
    <SafeView backgroundColor={Colors.white} statusbarColor={Colors.main}>
      <SBSCommonHeaderView title="All Scheme" backPath="back" />
      <View style={{ flex: 1, padding: moderateScale(10) }}>
        {schemes.length === 0 && !loading ? <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', }} >
          <Text style={{ color: '#666' }}>No Scheme Found</Text>
        </View> : <FlatList
          data={schemes}
          numColumns={2}
          keyExtractor={item => item.schemeId}
          columnWrapperStyle={{ justifyContent: 'space-between', marginBottom: moderateScale(10), marginHorizontal: moderateScale(5), marginTop: moderateScale(5) }}
          renderItem={renderItem} />}
      </View>
      {loading && <Loader />}
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  )
}

export default SchemeScreen