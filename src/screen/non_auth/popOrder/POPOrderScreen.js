import React, { useEffect, useRef, useState } from 'react'
import { FlatList, Image, StyleSheet, Text, TextInput, TouchableOpacity, View, ActivityIndicator } from 'react-native'
import SafeView from '../../../helper/SafeView'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { moderateScale } from '../../../helper/Window'
import { Icons } from '../../../assets/Icons'
import { Colors } from '../../../assets/Colors'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import Toast from 'react-native-toast-message'
import toastConfig from '../../../helper/ToastConfig'
import axios from 'axios'
import Loader from '../../../common/Loader'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'
import MonthYearPicker from '../../../common/MonthYearPicker'
import moment from 'moment'

const POPOrderScreen = (props) => {
    const [appOrder, setAppOrder] = useState(false)
    const [loading, setLoading] = useState(false)
    const [popOrderList, setPopOrderList] = useState([])
    const [productList, setProductList] = useState([])
    const [pageNo, setPageNo] = useState(1)
    const [hasMore, setHasMore] = useState(true)
    const [historyLoading, setHistoryLoading] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)
    const [showPicker, setShowPicker] = useState(false);
    const [year_month, setyear_month] = useState(new moment(new Date()).format('YYYY-MM'))

    const handleChange = (date) => {
        setShowPicker(false);
        var year_month = moment(new Date(date)).format('YYYY-MM')
        setyear_month(year_month)
    };

    const inputRefs = useRef([])

    useEffect(() => {
        requestForProductList()
        fetchPopOrderHistory(1, false, year_month)
    }, [])

    useEffect(() => {
        fetchPopOrderHistory(1, false, year_month)
    }, [year_month])

    const fetchAuthData = async () => {
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
        }
    }

    const loadMoreHistory = () => {
        fetchPopOrderHistory(pageNo, true, year_month)
    }

    const requestForProductList = async () => {
        const requestOptions = { method: 'GET', redirect: 'follow' }
        let url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.ProductURL.pop_product_list_url
        if(UrlStorage.ParameterList.BasicData.user_type.toLowerCase()=='broker'){
url += '?customer_code=' + UrlStorage.ParameterList.BasicData.selectedCustomerCode + '&user_type=' + UrlStorage.ParameterList.BasicData.selectedCustomerType
        }else{
url += '?customer_code=' + UrlStorage.ParameterList.BasicData.emp_id + '&user_type=' + UrlStorage.ParameterList.BasicData.user_type
        }
        
        
        console.log(url);
        
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            const res = await fetch(url, requestOptions)
            const result = await res.json()

            if (result.process_status === 'YES') {
                setProductList(
                    result.pop_product_date.map((item) => ({
                        ...item,
                        count: '',
                    }))
                )
            }
        } catch (e) { }
        setLoading(false)
    }

    const fetchPopOrderHistory = async (page = 1, isLoadMore = false, year_month) => {
        if (historyLoading || (!hasMore && isLoadMore)) return

        setHistoryLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        console.log('HIT3');
        try {
            var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.ProductURL.show_pop_order_list
             if(UrlStorage.ParameterList.BasicData.user_type.toLowerCase()=='broker'){
                url = url + "?customer_code=" + UrlStorage.ParameterList.BasicData.selectedCustomerCode + "&page_no=" + page + "&year_month=" + year_month
            }else{
                url = url + "?customer_code=" + UrlStorage.ParameterList.BasicData.emp_code + "&page_no=" + page + "&year_month=" + year_month
            }
            
            const res = await axios.get(url)

            const result = res.data
            if (result?.process_status?.toLowerCase() === 'yes' && result.track_pop_order_data) {
                const list = typeof result.track_pop_order_data === 'string' ? JSON.parse(result.track_pop_order_data) : result.track_pop_order_data

                const mapped = list.map(item => ({
                    order_id: item.order_id,
                    main_order_id: item.main_order_id,
                    order_date: item.order_date,
                    customer_name: item.customer_name,
                    address: item.address,
                    prod_display_name: item.prod_display_name,
                    qty: `${item.qty} (Pcs)`,
                    total_amount: `₹ ${Number(item.total_amount || 0).toFixed(2)}`,
                    order_status: item.order_status,
                    image: item.image,
                }))

                setPopOrderList(prev => page === 1 ? mapped : [...prev, ...mapped])

                setPageNo(page + 1)
                setHasMore(mapped.length > 0)
            } else {
                if (page === 1) setPopOrderList([])
                setHasMore(false)
            }
        } catch (e) {
        } finally {
            setHistoryLoading(false)
        }
    }

    const onChangeTextHandler = (text, index) => {
        setProductList((prev) =>
            prev.map((item, i) =>
                i === index ? { ...item, count: text.replace(/[^0-9]/g, '') } : item
            )
        )
    }

    const incrementHandler = (_, index) => {
        setProductList((prev) =>
            prev.map((item, i) =>
                i === index ? { ...item, count: String(Number(item.count || 0) + 1) } : item
            )
        )
    }

    const decrementHandler = (_, index) => {
        setProductList((prev) =>
            prev.map((item, i) =>
                i === index ? { ...item, count: Number(item.count) > 0 ? String(Number(item.count) - 1) : '', } : item
            )
        )
    }

    const filterClick = () => {
        setShowPicker(true)
    }

    const checkDataAndGotoCartPage = () => {
        const arr = []

        for (let i = 0; i < productList.length; i++) {
            const qty = Number(productList[i].count)

            if (!isNaN(qty) && qty > 0) {
                arr.push({ ...productList[i], count: qty })
            }
        }

        if (arr.length === 0) {
            Toast.show({ type: 'error', text1: 'Sorry', text2: 'Please at least select one product', })
            return
        }

        for (let i = 0; i < arr.length; i++) {
            if (arr[i].count < Number(arr[i].min_order_qty)) {
                alert(`Minimum order qty for ${arr[i].prod_desc} is ${arr[i].min_order_qty}`)
                return
            }
        }

        DataStorage.popProductList = arr
        props.navigation.navigate('CartScreen', { arr })
    }

    const renderProductItem = (item, index) => {
        return (
            <TouchableOpacity activeOpacity={0.95} style={{ flex: 0.50, margin: moderateScale(5) }} >
                <View style={{ width: "100%", backgroundColor: "#FFFFFF", borderWidth: moderateScale(1), borderColor: "#DCDDDF", elevation: 4, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: ({ x: 0 }, { y: 6 }), borderRadius: moderateScale(10), overflow: "hidden", gap: moderateScale(4) }}>
                    <Image source={{ uri: item.prod_image }} style={{ width: "100%", height: moderateScale(100) }} />
                    <View style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(6) }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(12), fontWeight: "600" }} numberOfLines={2}>{item.prod_desc}</Text>
                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", gap: moderateScale(10) }}>
                            <View style={{ paddingVertical: moderateScale(4), paddingHorizontal: moderateScale(8), borderRadius: moderateScale(6), backgroundColor: DataStorage.primaryColorCode }}>
                                <Text style={{ color: "#ffffff", fontSize: moderateScale(12), fontWeight: "400" }}>₹{item.price_per_piece}</Text>
                            </View>
                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(12), fontWeight: "400" }}>GST {item.GST_rate}%</Text>
                        </View>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "700" }}><Text style={{ color: "#7D7D7D", fontSize: moderateScale(12), fontWeight: "400" }}>Min Order Qty </Text>{item.min_order_qty}</Text>
                        <View style={{ paddingHorizontal: moderateScale(10), borderRadius: moderateScale(10), borderWidth: moderateScale(1), borderColor: DataStorage.primaryColorCode, flexDirection: "row", alignItems: "center", justifyContent: "space-between", gap: moderateScale(12) }}>
                            <TouchableOpacity activeOpacity={0.95} onPress={() => { decrementHandler(item, index) }}>
                                <Image source={Icons.MinusSign} style={{ width: moderateScale(12), height: moderateScale(12), tintColor: DataStorage.primaryColorCode }} />
                            </TouchableOpacity>
                            <TextInput
                                value={item.count.toString() == '0' ? '' : item.count.toString()}
                                onChangeText={(text) => onChangeTextHandler(text, index)}
                                keyboardType='number-pad'
                                placeholder='QTY'
                                placeholderTextColor={Colors.grey}
                                ref={(ref) => (inputRefs[index] = ref)}
                                numberOfLines={1}
                                style={{ color: "#333", width: moderateScale(50), fontSize: moderateScale(12), textAlign: "center", height: moderateScale(40) }} />
                            <TouchableOpacity activeOpacity={0.95} onPress={() => { incrementHandler(item, index) }}>
                                <Image source={Icons.AddSign} style={{ width: moderateScale(12), height: moderateScale(16), tintColor: DataStorage.primaryColorCode }} />
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
            </TouchableOpacity>
        )
    }

    const renderOrderItem = (item, index) => {
        return (
            <View style={styles.card}>
                <View style={styles.container}>
                    {item.image == '' || item.image == null ? <View style={{ width: 80, alignItems: 'center', justifyContent: 'center' }}>
                        <Image source={require('../../../assets/icons/noimage.png')} style={{ width: 30, height: 30 }} />
                    </View> :
                        <Image source={{ uri: item.image }} style={styles.image} />}

                    <View style={styles.rightContent}>
                        <View style={styles.row}>
                            <Text style={styles.label}>Main Order Id :</Text>
                            <Text style={styles.value}>{item.main_order_id}</Text>
                        </View>

                        <View style={styles.row}>
                            <Text style={styles.label}>Order Id :</Text>
                            <Text style={[styles.value, styles.bold]}>{item.order_id}</Text>
                        </View>

                        <View style={styles.row}>
                            <Text style={styles.label}>Date :</Text>
                            <Text style={styles.value}>{item.order_date}</Text>
                        </View>

                        <View style={styles.row}>
                            <Text style={styles.label}>Product :</Text>
                            <Text style={styles.value}>{item.prod_display_name}</Text>
                        </View>

                        <View style={styles.row}>
                            <Text style={styles.label}>Qty :</Text>
                            <Text style={styles.value}>{item.qty}</Text>
                        </View>

                        <View style={styles.row}>
                            <Text style={styles.label}>Total Amount :</Text>
                            <Text style={styles.value}>{item.total_amount}</Text>
                        </View>

                        <View style={styles.row}>
                            <Text style={styles.label}>Address :</Text>
                            <Text style={styles.value}>{item.address}</Text>
                        </View>

                        <View style={styles.row}>
                            <Text style={styles.label}>Order Status :</Text>
                            <Text style={styles.value}>{item.order_status}</Text>
                        </View>
                    </View>
                </View>
            </View>
        );
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Pop Product" backPath=" " Cart={false} Filter={appOrder} handlePerformanceFilterOpen={filterClick} />
                <View style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(10), flex: 1 }}>

                    <View style={{ width: "100%", flex: 1 }}>
                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "center" }}>
                            <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={() => setAppOrder(false)}>
                                <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: appOrder === false ? DataStorage.primaryColorCode : DataStorage.transColorCode, borderWidth: moderateScale(1), borderColor: "#DCDDDF" }}>
                                    <Text style={{ color: appOrder === false ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600" }}>POP Order</Text>
                                </View>
                            </TouchableOpacity>
                            <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={() => setAppOrder(true)}>
                                <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: appOrder ? DataStorage.primaryColorCode : DataStorage.transColorCode, borderWidth: moderateScale(1), borderColor: "#DCDDDF" }}>
                                    <Text style={{ color: appOrder ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600" }}>Order History</Text>
                                </View>
                            </TouchableOpacity>
                        </View>
                        <View style={{ width: "100%", flex: 1, backgroundColor: "#FFFFFF", borderBottomWidth: moderateScale(1), borderLeftWidth: moderateScale(1), borderRightWidth: moderateScale(1), borderColor: "#DCDDDF", padding: moderateScale(10) }}>
                            {appOrder ? (
                                popOrderList?.length > 0 ? (
                                    <FlatList
                                        data={popOrderList}
                                        key="history"
                                        keyExtractor={(_, i) => i.toString()}
                                        showsVerticalScrollIndicator={false}
                                        decelerationRate="fast"
                                        contentContainerStyle={{ paddingBottom: moderateScale(10) }}
                                        onEndReached={loadMoreHistory}
                                        onEndReachedThreshold={0.3}
                                        renderItem={({ item, index }) => renderOrderItem(item, index)}
                                        ListFooterComponent={
                                            historyLoading ? (
                                                <View style={{ paddingVertical: moderateScale(16), alignItems: 'center' }}>
                                                    <ActivityIndicator size="small" color={DataStorage.primaryColorCode} />
                                                    <Text style={{ color: Colors.grey, fontSize: moderateScale(12), marginTop: moderateScale(4) }}>
                                                        Loading more...
                                                    </Text>
                                                </View>
                                            ) : null
                                        }
                                    />
                                ) : (
                                    <View style={emptyStyles.wrapper}>
                                        <View style={emptyStyles.iconCircle}>
                                            <Image source={Icons.CementOrder} style={[emptyStyles.icon, { tintColor: DataStorage.primaryColorCode }]} />
                                        </View>
                                        <Text style={emptyStyles.title}>No Orders Yet</Text>
                                        <Text style={emptyStyles.subtitle}>You haven't placed any POP orders.{'\n'}Start by selecting products!</Text>
                                    </View>
                                )
                            ) : (
                                productList?.length > 0 ? (
                                    <FlatList
                                        data={productList}
                                        key="product"
                                        numColumns={2}
                                        keyExtractor={(_, i) => i.toString()}
                                        showsVerticalScrollIndicator={false}
                                        decelerationRate="fast"
                                        contentContainerStyle={{ paddingBottom: moderateScale(10) }}
                                        columnWrapperStyle={{ justifyContent: 'space-between' }}
                                        renderItem={({ item, index }) => renderProductItem(item, index)}
                                    />
                                ) : (
                                    <View style={emptyStyles.wrapper}>
                                        <View style={emptyStyles.iconCircle}>
                                            <Image source={Icons.Cart} style={[emptyStyles.icon, { tintColor: DataStorage.primaryColorCode }]} />
                                        </View>
                                        <Text style={emptyStyles.title}>No Products Available</Text>
                                        <Text style={emptyStyles.subtitle}>There are no POP products available{'\n'}at the moment. Check back later!</Text>
                                    </View>
                                )
                            )}
                        </View>
                    </View>
                    {!appOrder && productList?.some(item => Number(item.count) > 0) && (
                        <TouchableOpacity activeOpacity={0.95} onPress={() => { checkDataAndGotoCartPage() }}>
                            <View style={{ width: "100%", paddingHorizontal: moderateScale(15) }}>
                                <View style={{ width: "100%", height: moderateScale(40), flexDirection: "row", gap: moderateScale(8), backgroundColor: DataStorage.primaryColorCode, borderRadius: moderateScale(10), alignItems: "center", justifyContent: "center" }}>
                                    <Text style={{ color: Colors.white, fontSize: moderateScale(16), fontWeight: '500' }}>Check Out</Text>
                                </View>
                            </View>
                        </TouchableOpacity>
                    )}
                </View>

            </View>
            {loading && <Loader />}
            <Toast config={toastConfig} />
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />

            <MonthYearPicker
                visible={showPicker}
                onConfirm={handleChange}
                onCancel={() => setShowPicker(false)}
            />
        </SafeView>
    )
}

export default POPOrderScreen

const styles = StyleSheet.create(({
    card: { margin: 5, backgroundColor: "#fff", borderRadius: 5, elevation: 5, shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 3 },
    container: { flexDirection: "row", backgroundColor: "#fff", margin: 3 },
    image: { width: 80, height: 80, alignSelf: "center", resizeMode: "contain" },
    rightContent: { flex: 1, padding: 9, justifyContent: "center" },
    row: { flexDirection: "row", alignItems: "center", paddingVertical: 3 },
    label: { fontSize: 10, color: "grey", textTransform: "uppercase" },
    value: { flex: 1, fontSize: 12, color: "black", marginLeft: 5 },
    bold: { fontWeight: "bold" }
}))

const emptyStyles = StyleSheet.create({
    wrapper: { flex: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: moderateScale(30), gap: moderateScale(10), },
    iconCircle: { width: moderateScale(80), height: moderateScale(80), borderRadius: moderateScale(40), backgroundColor: '#F3F4F6', alignItems: 'center', justifyContent: 'center', marginBottom: moderateScale(6), },
    icon: { width: moderateScale(36), height: moderateScale(36), resizeMode: 'contain', },
    title: { fontSize: moderateScale(16), fontWeight: '700', color: Colors.text, textAlign: 'center', },
    subtitle: { fontSize: moderateScale(12), color: '#9CA3AF', textAlign: 'center', lineHeight: moderateScale(18), },
})