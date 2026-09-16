import React, { useState, useCallback, useMemo } from 'react'
import { FlatList, Image, Text, TouchableOpacity, View, StyleSheet } from 'react-native'
import SafeView from '../../../helper/SafeView'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { moderateScale } from '../../../helper/Window'
import { Colors } from '../../../assets/Colors'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import Loader from '../../../common/Loader'
import { CommonActions } from '@react-navigation/native'
import Toast from 'react-native-toast-message'
import toastConfig from '../../../helper/ToastConfig'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

let dayjs = null
try { dayjs = require('dayjs') } catch (e) { dayjs = null }

const RemoteImage = ({ uri, style, fallbackSource }) => {
  const safeUri = uri ? String(uri).replace('http://', 'https://') : null
  if (safeUri)
    return <Image source={{ uri: safeUri }} style={style} resizeMode="contain" />
  if (fallbackSource)
    return <Image source={fallbackSource} style={style} resizeMode="contain" />
  return <View style={[style, { backgroundColor: '#f0f0f0' }]} />
}

const ListItem = React.memo(({ item, transColorCode, primaryColorCode, typeOfUse }) => {
  const imageUri = item?.img_path?.[0]
  const uom = typeOfUse === 1 ? item?.UOM1 : 'MT'
  return (
    <View style={[styles.itemContainer, { backgroundColor: transColorCode, borderColor: '#DCDDDF' }]}>
      <View style={styles.itemLeft}>
        <RemoteImage uri={imageUri} fallbackSource={require('../../../assets/icons/noimage.png')} style={styles.itemImage} />
        <Text style={styles.itemDesc} numberOfLines={2}> {item?.prod_desc ?? ''} </Text>
      </View>
      <View style={[styles.qtyBadge, { backgroundColor: primaryColorCode }]}>
        <Text style={styles.qtyText}>{item?.count ?? 0} {uom}</Text>
      </View>
    </View>
  )
})

const OrderConfirmScreen = (props) => {
  const { selectedProducts } = props?.route?.params ?? { selectedProducts: [] }
  const [loading, setLoading] = useState(false)
  const [authChecker, setAuthChecker] = useState(false)
  const { productQtyAddedList = [], transColorCode = "#FFFFFF", primaryColorCode = Colors.main, typeOfUse, freight, for_type, destination_address, destination_address_code, dump_obj = {}, dealer_truck, plant_name, delivery_remarks, } = DataStorage

  const formatTimestamp = (d = new Date()) => {
    if (dayjs) return dayjs(d).format('YYYYMMDDHHmmss')
    const pad = (n) => String(n).padStart(2, '0')
    return `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`
  }

  const navigateToDashboard = () => {
    props.navigation.dispatch(
      CommonActions.reset({
        index: 0,
        routes: [{ name: 'SBSDashboardScreen' }]
      })
    )
  }

  const requestForCementOrder = useCallback(async () => {
    if (loading) return
    setLoading(true)
    var a = await AuthCheckingApi()
    if (!a) {
      setAuthChecker(true)
      setLoading(false)
      return false
    }
    try {
      const formdata = new FormData()
      const empCode = UrlStorage?.ParameterList?.BasicData?.emp_code ?? ''
      const freightStr = freight === 1 ? 'FOR' : freight === 2 ? 'EXW' : 'DOT'
      const consigneePhone = DataStorage.consignee_phone_number ?? ''
      const shipItemCustomerCode = DataStorage?.ship_item?.customer_code ?? ''
      const dumpName = dump_obj?.dump_name ?? ''
      const dumpCode = dump_obj?.dump_code ?? ''
      for (let i = 0; i < (productQtyAddedList?.length || 0); i++) {
        const p = productQtyAddedList[i]
        const prodCode = p?.prod_code ?? ''
        const apporderno = '0' + empCode + formatTimestamp(new Date()) + prodCode
        formdata.append(`order_data[${i}][order_for]`, DataStorage.consignee_address ?? '')
        formdata.append(`order_data[${i}][qty]`, p?.count ?? 0)
        formdata.append(`order_data[${i}][freight]`, freightStr)
        formdata.append(`order_data[${i}][phone_no]`, consigneePhone)
        formdata.append(`order_data[${i}][order_for_type]`, DataStorage.shipTo ?? '')
        formdata.append(`order_data[${i}][Delivery_point]`, DataStorage.for_type ?? '')
        formdata.append(`order_data[${i}][Remarks_text]`, DataStorage.delivery_remarks ?? '')
        formdata.append(`order_data[${i}][destination_name]`, DataStorage.destination_address ?? '')
        formdata.append(`order_data[${i}][destination_code]`, DataStorage.destination_address_code ?? '')
        formdata.append(`order_data[${i}][destination_address]`, DataStorage.destination_address ?? '')
        formdata.append(`order_data[${i}][prod_code]`, prodCode)
        if (UrlStorage.ParameterList.BasicData.user_type != 'broker')
          formdata.append(`order_data[${i}][customer_code]`, UrlStorage.ParameterList.BasicData.selectedCustomerCode)
        else
          formdata.append(`order_data[${i}][customer_code]`, UrlStorage.ParameterList.BasicData.customerDetails.customer_code)
        formdata.append(`order_data[${i}][apporderno]`, apporderno)
        formdata.append(`order_data[${i}][dump_status]`, freight === 1 ? 'NO' : 'YES')
        formdata.append(`order_data[${i}][erporderdt]`, '')
        formdata.append(`order_data[${i}][dump_name]`, dumpName)
        formdata.append(`order_data[${i}][dump_code]`, dumpCode)
        formdata.append(`order_data[${i}][sub_dealer_code]`, shipItemCustomerCode)
        formdata.append(`order_data[${i}][dealer_truck]`, DataStorage.dealer_truck ?? '')
      }
      formdata.append('user_type', UrlStorage?.ParameterList?.BasicData?.user_type ?? '')
      formdata.append('login_user_id', empCode)
      const apiUrl = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.OrderURL.new_order_create_url
      const response = await fetch(apiUrl, { method: 'POST', body: formdata })
      const statusCode = response.status
      const text = await response.text()
      let parsed = null
      try {
        parsed = JSON.parse(text)
      } catch (e) {
      }
      if (statusCode === 200) {
        Toast.show({ type: 'success', text1: 'Success', text2: parsed?.message || 'Order Placed Successfully' })
        setTimeout(() => {
          navigateToDashboard()
        }, 1000)
      } else {
        const message = parsed?.message || parsed?.process_message || 'Something went wrong.'
        Toast.show({ type: 'error', text1: 'Error', text2: message })
      }
    } catch (err) {
      Toast.show({ type: 'error', text1: 'Network Error', text2: err.message || 'Failed to place order.' })
    } finally {
      setLoading(false)
    }
  }, [loading, productQtyAddedList, freight, dump_obj])

  const createSBSOrder = useCallback(async () => {
    if (loading) return
    setLoading(true)
    var a = await AuthCheckingApi()
    if (!a) {
      setAuthChecker(true)
      setLoading(false)
      return false
    }
    try {
      const payload = {
        user_type: UrlStorage.ParameterList.BasicData.user_type,
        login_user_id: UrlStorage.ParameterList.BasicData.selectedCustomerCode,
        order_data: [{
          order_for_type: DataStorage.shipTo === 'self' ? 'Self' : 'Sub Dealer',
          consignee_name_of: DataStorage.consignee_name,
          consignee_address_arr_of: DataStorage.consignee_address,
          sub_dealer_code: DataStorage.dealer_sub_dealer_id ?? UrlStorage.ParameterList.BasicData.emp_code,
          customer_code: UrlStorage.ParameterList.BasicData.user_type != 'broker' ? DataStorage.customer_code : UrlStorage.ParameterList.BasicData.selectedCustomerCode,
          freight: freight === 1 ? 'FOR' : freight === 2 ? 'EXW' : 'DOT',
          dealer_truck: freight === 1 && DataStorage.for_type === "DOT" ? DataStorage.dealer_truck : null,
          delivery_point: freight === 1 ? DataStorage.for_type : '',
          destination_code: DataStorage.destination_address_code,
          destination_name: DataStorage.destination_address,
          destination_address: DataStorage.destination_address,
          phone_no: DataStorage.consignee_phone_number ?? '',
          remarks_text: DataStorage.delivery_remarks,
          product_data: selectedProducts,
          dump_code: DataStorage.dump_obj?.dump_code,
          dump_name: DataStorage.dump_obj?.dump_name,
        }],
      }
      const apiUrl = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.order.create_order
      const response = await fetch(apiUrl, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload), })
      let data = null
      try {
        data = await response.json()
      } catch (e) {
      }
      if (response.ok) {
        Toast.show({ type: 'success', text1: 'Success', text2: 'Order Placed Successfully' })
        setTimeout(() => {
          navigateToDashboard()
        }, 1000)
      } else {
        const message = data?.process_message || 'Something Went Wrong'
        Toast.show({ type: 'error', text1: 'Error', text2: message })
      }
    } catch (err) {
      Toast.show({ type: 'error', text1: 'Network Error', text2: 'Please check your connection.' })
    } finally {
      setLoading(false)
    }
  }, [loading, selectedProducts, freight])

  const onConfirm = useCallback(() => {
    if (loading) return
    if (typeOfUse === 1)
      createSBSOrder()
    else
      requestForCementOrder()
  }, [loading, typeOfUse, createSBSOrder, requestForCementOrder])

  const keyExtractor = useCallback((item, index) =>
    String(item?.prod_code ?? item?.id ?? index), []
  )
  const renderItem = useCallback(({ item }) => (
    <ListItem
      item={item}
      transColorCode={transColorCode}
      primaryColorCode={primaryColorCode}
      typeOfUse={typeOfUse}
    />
  ), [transColorCode, primaryColorCode, typeOfUse])

  const dataList = useMemo(() =>
    productQtyAddedList || [], [productQtyAddedList]
  )

  return (
    <SafeView backgroundColor="#FFFFFF" bar={false} statusbarColor={Colors.main}>
      <View style={styles.screen}>
        <SBSCommonHeaderView title="Order Confirmation" backPath=" " />

        <FlatList
          data={dataList}
          renderItem={renderItem}
          keyExtractor={keyExtractor}
          showsVerticalScrollIndicator={false}
          ItemSeparatorComponent={() => <View style={{ height: moderateScale(10) }} />}
          initialNumToRender={6}
          maxToRenderPerBatch={6}
          windowSize={9}
          style={{ flex: 1 }}
          contentContainerStyle={{ paddingBottom: moderateScale(140) }}
          keyboardShouldPersistTaps="handled"
          ListHeaderComponent={(
            <View style={styles.headerContainer}>
              <Text style={styles.sectionTitle}>Consignee Name</Text>
            </View>
          )}
          ListFooterComponent={(
            <View style={styles.detailsContainer}>
              <Text style={styles.sectionTitle}>Details</Text>
              <View style={styles.detailBox}>
                <View style={styles.rowBetween}>
                  <Text style={styles.label}>Ship To</Text>
                  <View style={[styles.badge, { backgroundColor: primaryColorCode }]}>
                    <Text style={styles.badgeText}>{DataStorage.shipTo}</Text>
                  </View>
                </View>
                <View style={styles.field}>
                  <Text style={styles.fieldLabel}>Consignee Name:</Text>
                  <Text style={styles.fieldValue}>{DataStorage.consignee_name.replaceAll("&amp", '&')}</Text>
                </View>
                <View style={styles.field}>
                  <Text style={styles.fieldLabel}>Consignee Address:</Text>
                  <Text style={styles.fieldValue}>{DataStorage.consignee_address}</Text>
                </View>
                <View style={styles.field}>
                  <Text style={styles.fieldLabel}>Freight:</Text>
                  <Text style={styles.fieldValue}> {freight === 1 ? `For, ${for_type}` : freight === 2 ? 'EXW' : 'DOT'} </Text>
                </View>
                <View style={styles.field}>
                  <Text style={styles.fieldLabel}>Destination Address:</Text>
                  <Text style={styles.fieldValue}>{destination_address}</Text>
                </View>
                {freight !== 1 && <View style={styles.field}>
                  <Text style={styles.fieldLabel}> {freight === 2 ? 'Dump Name:' : (freight === 1 && DataStorage.for_type === "DOT") ? 'Truck Name:' : ''} </Text>
                  <Text style={styles.fieldValue}> {freight === 2 ? (dump_obj?.dump_name ?? '') : dealer_truck} </Text>
                </View>}
                {(freight === 1 && typeOfUse === 1) && <View style={styles.field}>
                  <Text style={styles.fieldLabel}>Plant Name:</Text>
                  <Text style={styles.fieldValue}>{plant_name ?? "No Plant Selected"}</Text>
                </View>}
                <View style={styles.field}>
                  <Text style={styles.fieldLabel}>Delivery Remarks:</Text>
                  <Text style={styles.fieldValue}>{delivery_remarks}</Text>
                </View>
              </View>
            </View>
          )}
        />
        <TouchableOpacity activeOpacity={0.95} disabled={loading} onPress={onConfirm} >
          <View style={styles.confirmWrapper}>
            <View style={[styles.confirmButton, loading && styles.disabledButton, { backgroundColor: primaryColorCode }]}>
              <Text style={styles.confirmText}> {loading ? 'Placing Order...' : 'Confirm Order'} </Text>
            </View>
          </View>
        </TouchableOpacity>
        <View style={{ height: moderateScale(20) }} />
        {loading && <Loader />}
      </View>
      <Toast config={toastConfig} />
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  )
}

const styles = StyleSheet.create({
  screen: { flex: 1, width: '100%', backgroundColor: "#FFFFFF", paddingHorizontal: moderateScale(0) },
  headerContainer: { padding: moderateScale(20) },
  sectionTitle: { color: Colors.text, fontSize: moderateScale(14), marginBottom: moderateScale(6) },
  itemContainer: { marginHorizontal: 20, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', borderWidth: moderateScale(1), borderRadius: moderateScale(10), padding: moderateScale(10) },
  itemLeft: { flexDirection: 'row', alignItems: 'center', gap: moderateScale(10), maxWidth: moderateScale(260) },
  itemImage: { width: moderateScale(50), height: moderateScale(50) },
  itemDesc: { color: Colors.text, fontSize: moderateScale(13), maxWidth: moderateScale(170) },
  qtyBadge: { paddingVertical: moderateScale(6), paddingHorizontal: moderateScale(10), borderRadius: moderateScale(10) },
  qtyText: { color: "#FFFFFF", fontSize: moderateScale(15), fontWeight: '600' },
  detailsContainer: { padding: moderateScale(20), paddingBottom: moderateScale(40) },
  detailBox: { width: '100%', gap: moderateScale(8), backgroundColor: '#FFFFFF', borderWidth: moderateScale(1), borderColor: '#DCDDDF', borderRadius: moderateScale(10), padding: moderateScale(10) },
  rowBetween: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  label: { color: Colors.text, fontSize: moderateScale(13) },
  badge: { paddingVertical: moderateScale(3), paddingHorizontal: moderateScale(10), borderRadius: moderateScale(10) },
  badgeText: { color: "#FFFFFF", fontSize: moderateScale(14), fontWeight: '600', textTransform: 'capitalize' },
  field: { alignItems: 'flex-start', gap: moderateScale(4), marginBottom: moderateScale(10) },
  fieldLabel: { color: '#7D7D7D', fontSize: moderateScale(14) },
  fieldValue: { color: Colors.text, fontSize: moderateScale(14), fontWeight: '500' },
  confirmWrapper: { width: '100%', paddingHorizontal: moderateScale(15) },
  confirmButton: { width: '100%', height: moderateScale(40), borderRadius: moderateScale(10), alignItems: 'center', justifyContent: 'center' },
  confirmText: { color: "#FFFFFF", fontSize: moderateScale(16), fontWeight: '500' },
  disabledButton: { opacity: 0.6 },
})

export default OrderConfirmScreen