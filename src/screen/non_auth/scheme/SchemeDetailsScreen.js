import React, { useEffect, useMemo, useState } from 'react'
import { View, Text, TouchableOpacity, Modal, ActivityIndicator, Dimensions, Platform, StyleSheet, Image } from 'react-native'
import Pdf from 'react-native-pdf'
import RNBlobUtil from 'react-native-blob-util'
import { LineChart } from 'react-native-gifted-charts'
import SafeView from '../../../helper/SafeView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import { Icons } from '../../../assets/Icons'
import UrlStorage from '../../../storage/UrlStorage'

const { width } = Dimensions.get('window')

const PDF_BASE_URL = UrlStorage.BaseUrlList.Saathi.base_url_saathi + '/schemes/'

const SchemeDetailsScreen = ({ route, navigation }) => {
  const [showGraph, setShowGraph] = useState(false)
  const [pdfPath, setPdfPath] = useState(null)
  const [loadingPdf, setLoadingPdf] = useState(true)

  const { schemeName, pdfUrl, slabType, applicableQty, slabDetailsJson, achievementsJson, } = route.params

  useEffect(() => {
    preparePdf()
  }, [])

  const preparePdf = async () => {
    try {
      setLoadingPdf(true)
      if (!pdfUrl)
        return
      const fullUrl = pdfUrl.startsWith('http') ? pdfUrl : PDF_BASE_URL + pdfUrl
      const fileName = fullUrl.split('/').pop()
      const { fs, config } = RNBlobUtil
      const dir = Platform.OS === 'ios' ? fs.dirs.DocumentDir : fs.dirs.CacheDir
      const localPath = `${dir}/${fileName}`
      const exists = await fs.exists(localPath)
      if (exists) {
        setPdfPath(`file://${localPath}`)
        setLoadingPdf(false)
        return
      }
      const res = await config({ path: localPath, fileCache: true, trustAllCerts: true, }).fetch('GET', fullUrl)
      setPdfPath(`file://${res.path()}`)
    } catch (e) {
    } finally {
      setLoadingPdf(false)
    }
  }

  const { chartData, axisMax, yLevels, } = useMemo(() => {
    let entries = []
    let maxLift = 0
    try {
      const ach = achievementsJson ? JSON.parse(achievementsJson) : []
      if (ach.length === 1) {
        const qty = Number(ach[0].lifting_qty || 0)
        entries.push({ value: 0, label: '' })
        entries.push({ value: qty, label: formatDate(ach[0].date), })
        maxLift = qty
      } else {
        ach.forEach((a, i) => {
          const qty = Number(a.lifting_qty || 0)
          entries.push({ value: qty, label: formatDate(a.date), })
          if (qty > maxLift) maxLift = qty
        })
      }
    } catch (e) { }
    if (entries.length === 0) {
      entries = [
        { value: 0, label: '' },
        { value: 60, label: 'Sample' },
      ]
      maxLift = 60
    }
    let levels = [0]
    let axisMaxFinal = maxLift
    if (slabType === 'single' && applicableQty > 0) {
      axisMaxFinal = Math.ceil(maxLift / applicableQty) * applicableQty
      for (let v = applicableQty; v <= axisMaxFinal; v += applicableQty) {
        levels.push(v)
      }
    } else if (slabType === 'multiple') {
      let slabMax = 0
      try {
        const slabObj = JSON.parse(slabDetailsJson || '{}')
        const slabs = slabObj.slabs || []
        slabs.forEach(s => {
          const m = Math.round(Number(s.lifting_max || 0))
          if (m > 0) {
            levels.push(m)
            if (m > slabMax) slabMax = m
          }
        })
      } catch (e) { }
      levels.sort((a, b) => a - b)
      axisMaxFinal = slabMax > maxLift ? slabMax : maxLift
      if (axisMaxFinal <= 0) axisMaxFinal = 10
    } else {
      if (axisMaxFinal <= 0) axisMaxFinal = 10
    }
    return {
      chartData: entries,
      axisMax: axisMaxFinal,
      yLevels: levels,
    }
  }, [])

  return (
    <SafeView backgroundColor={Colors.white}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <View style={{ width: moderateScale(30), height: moderateScale(30), borderRadius: moderateScale(20), borderWidth: moderateScale(1), borderColor: "#FFFFFF", alignItems: "center", justifyContent: "center", zIndex: 100 }} >
            <Image source={Icons.Back} style={{ width: moderateScale(12), height: moderateScale(12), tintColor: "#FFFFFF", }} />
          </View>
        </TouchableOpacity>
        <Text numberOfLines={1} style={styles.headerTitle}> {schemeName} </Text>
        <TouchableOpacity onPress={() => setShowGraph(true)}>
          <Text style={styles.headerIcon}>ⓘ</Text>
        </TouchableOpacity>
      </View>
      <View style={{ flex: 1 }}>
        {loadingPdf && <View style={styles.pdfLoader}>
          <ActivityIndicator size="large" color={Colors.main} />
          <Text style={{ marginTop: 8 }}>Loading PDF…</Text>
        </View>}
        {pdfPath && <Pdf source={{ uri: pdfPath }} trustAllCerts={true} style={{ flex: 1 }} onError={e => { }} />}
      </View>
      <Modal visible={showGraph} transparent animationType="fade">
        <View style={styles.modalBg}>
          <View style={styles.modalCard}>
            <Text style={styles.modalTitle}>Scheme Overview</Text>
            <Text style={styles.modalDesc}> Your scheme achievement trend. </Text>
            <LineChart
              data={chartData}
              width={width - 150}
              height={240}
              initialSpacing={0}
              spacing={60}
              endSpacing={30}
              color={Colors.main}
              thickness={3}
              curved={chartData.length > 2}
              showDataPoint
              dataPointsRadius={4}
              dataPointsColor={Colors.main}
              minValue={0}
              maxValue={axisMax}
              noOfSections={4}
              xAxisLabelTextStyle={{ color: '#666', fontSize: 11, }}
              yAxisTextStyle={{ color: '#666', fontSize: 12 }}
              hideRules={false}
              rulesColor="#e6e6e6"
              showVerticalLines={false}
              xAxisColor="#ccc"
              yAxisColor="#ccc" />
            <TouchableOpacity onPress={() => setShowGraph(false)} style={styles.okBtn} >
              <Text style={styles.okText}>OK</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </SafeView>
  )
}

const formatDate = d => {
  if (!d || d.length < 10) return ''
  return d.substring(8, 10) + '-' + d.substring(5, 7)
}

const styles = StyleSheet.create({
  header: { height: moderateScale(56), backgroundColor: Colors.main, flexDirection: 'row', alignItems: 'center', paddingHorizontal: moderateScale(12), },
  headerIcon: { color: '#fff', fontSize: 18, },
  headerTitle: { flex: 1, color: '#fff', fontSize: 16, fontWeight: '600', textAlign: 'center', marginHorizontal: 10, },
  pdfLoader: { ...StyleSheet.absoluteFillObject, justifyContent: 'center', alignItems: 'center', backgroundColor: '#fff', },
  modalBg: { flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'center', padding: 20, },
  modalCard: { backgroundColor: '#fff', borderRadius: 12, padding: 16, },
  modalTitle: { fontSize: 16, fontWeight: '600', textAlign: 'center', },
  modalDesc: { fontSize: 13, color: '#666', textAlign: 'center', marginBottom: 10, },
  okBtn: { marginTop: 20, backgroundColor: Colors.main, paddingVertical: 12, borderRadius: 8, alignItems: 'center', },
  okText: { color: '#fff', fontWeight: '600', },
})

export default SchemeDetailsScreen