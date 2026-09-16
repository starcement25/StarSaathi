import React, { useEffect, useState } from 'react'
import { Alert, FlatList, Image, ScrollView, Text, TextInput, TouchableOpacity, TouchableWithoutFeedback, View } from 'react-native'
import DateTimePicker from '@react-native-community/datetimepicker'
import Modal from 'react-native-modal'
import SafeView from '../../../helper/SafeView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Icons } from '../../../assets/Icons'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import moment from 'moment'
import Toast from 'react-native-toast-message'
import toastConfig from '../../../helper/ToastConfig'
import { useNavigation } from '@react-navigation/native'
import Loader from '../../../common/Loader'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const OrderEnquiryScreen = (props) => {
    const navigation = useNavigation()
    const [loading, setLoading] = useState(false)
    const [startDate, setStartDate] = useState(moment(new Date()).format('DD-MM-YYYY'))
    const [endDate, setEndDate] = useState(moment(new Date()).format('DD-MM-YYYY'))
    const [data, setData] = useState([])
    const [typeOfDatePick, setTypeOfDatePick] = useState('0')
    const [isDateTimePicker, setIsDateTimePicker] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)
    const [productList, setProductList] = useState([])
    const [productName, setProductName] = useState()
    const [productCode, setProductCode] = useState()
    const [dnsProductCode, setDnsProductCode] = useState()
    const [quantityBag, setQuantityBag] = useState()
    const [dateOfRequirement, setDateOfRequirement] = useState()
    const [remarks, setRemarks] = useState()
    const [isOpenPopup, setIsOpenPopup] = useState(false)

    useEffect(() => {
        if (UrlStorage.ParameterList.BasicData.user_type.toLocaleLowerCase() == 'dealer' || UrlStorage.ParameterList.BasicData.user_type == 'broker')
            DataStorage.typeOfUse == 1 ? requestForAllListofOrderEnquirySBS() : requestForAllListofOrderEnquiryCement()
        else
            DataStorage.typeOfUse == 1 ? requestForProductListForSBS() : requestForProductListForCement()
    }, [])

    const requestForAllListofOrderEnquiryCement = async (start, end) => {
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.OrderQueryURL.dealer_order_query_list_url + `?customer_code=${UrlStorage.ParameterList.BasicData.emp_code}&start_date=${moment(start ?? startDate, 'DD-MM-YYYY').format('YYYY-MM-DD')}&end_date=${moment(end ?? endDate, 'DD-MM-YYYY').format('YYYY-MM-DD')}`
        const requestOptions = { method: "GET" }
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        await fetch(url, requestOptions)
            .then(async (response) => await response.json())
            .then((result) => {
                if (result.process_status == 'YES')
                    setData(result.order_query_data)
                else {
                    Alert.alert('Sorry', startDate == endDate ? 'No order enquiry available on ' + moment(startDate, 'DD-MM-YYYY').format('DD MMM, YYYY') :
                        'No order enquiry available between ' + moment(startDate, 'DD-MM-YYYY').format('DD MMM, YYYY') + ' and ' + moment(endDate, 'DD-MM-YYYY').format('DD MMM, YYYY'), [
                        { text: 'Ok', onPress: () => { }, style: 'cancel', }
                    ])
                    setData([])
                }
            })
            .catch((error) => { })
    }

    const requestForAllListofOrderEnquirySBS = async (start, end) => {
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.OrderQueryURL.dealer_order_query_list_url + `?customer_code=${UrlStorage.ParameterList.BasicData.emp_code}&start_date=${moment(start ?? startDate, 'DD-MM-YYYY').format('YYYY-MM-DD')}&end_date=${moment(end ?? endDate, 'DD-MM-YYYY').format('YYYY-MM-DD')}`
        const requestOptions = { method: "GET" }
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        await fetch(url, requestOptions)
            .then(async (response) => await response.json())
            .then((result) => {
                if (result.process_status == 'YES')
                    setData(result.order_query_data)
                else {
                    Alert.alert('Sorry', startDate == endDate ? 'No order enquiry available on ' + moment(startDate, 'DD-MM-YYYY').format('DD MMM, YYYY') :
                        'No order enquiry available between ' + moment(startDate, 'DD-MM-YYYY').format('DD MMM, YYYY') + ' and ' + moment(endDate, 'DD-MM-YYYY').format('DD MMM, YYYY'), [
                        { text: 'Ok', onPress: () => { }, style: 'cancel', }
                    ])
                    setData([])
                }
            })
            .catch((error) => { })
    }

    const requestForProductListForCement = async () => {
        setLoading(true)
        const requestOptions = { method: "GET", redirect: "follow" }
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DashboardURL.product_data_list_url
        url = url + '?emp_code=' + UrlStorage.ParameterList.BasicData.emp_code + '&user_type=' + UrlStorage.ParameterList.BasicData.user_type
        await fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                if (result.process_status == 'YES')
                    setProductList(result.product_date)
                else
                    setProductList([])
                setLoading(false)
            }).catch((error) => { })
    }

    const requestForProductListForSBS = async () => {
        setLoading(true)
        const requestOptions = { method: "GET", redirect: "follow" }
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DashboardURL.product_data_list_url
        url = url + '?emp_code=' + UrlStorage.ParameterList.BasicData.emp_code + '&user_type=' + UrlStorage.ParameterList.BasicData.user_type
        await fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                if (result.process_status == 'YES')
                    setProductList(result.product_date)
                else
                    setProductList([])
                setLoading(false)
            }).catch((error) => { })
    }

    const checkDataForNewOrderEnquiry = () => {
        if (productName == '')
            Toast.show({ type: 'error', text1: 'Sorry', text2: 'Please select Product.' })
        else if (quantityBag == '')
            Toast.show({ type: 'error', text1: 'Sorry', text2: 'Please enter Product Quantity.' })
        else if (dateOfRequirement == '')
            Toast.show({ type: 'error', text1: 'Sorry', text2: 'Please select Date of Requirement.' })
        else if (remarks == '')
            Toast.show({ type: 'error', text1: 'Sorry', text2: 'Please enter Enquiry Remarks.' })
        else
            requestForNewOrderEnquiry()
    }

    const requestForNewOrderEnquiry = async () => {
        setLoading(true)
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        const formdata = new FormData()
        formdata.append("customer_id", UrlStorage.ParameterList.BasicData.emp_id)
        formdata.append("order_query_data[0][order_id]", 'RS' + UrlStorage.ParameterList.BasicData.emp_id + moment(new Date()).format('YYYYMMDDHHmmss') + productCode)
        formdata.append("order_query_data[0][linked_dealer_code]", UrlStorage.ParameterList.BasicData.belong_dealer_code)
        formdata.append("order_query_data[0][dns_prod_code]", productCode)
        formdata.append("order_query_data[0][prod_name]", productName)
        formdata.append("order_query_data[0][qty_bags]", quantityBag)
        formdata.append("order_query_data[0][query_date]", moment(dateOfRequirement, 'DD-MM-YYYY').format('YYYY-MM-DD'))
        formdata.append("order_query_data[0][date_of_lifting]", moment(dateOfRequirement, 'DD-MM-YYYY').format('YYYY-MM-DD'))
        formdata.append("order_query_data[0][remarks]", remarks)
        const requestOptions = { method: "POST", body: formdata, redirect: "follow" }
        fetch(UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.OrderQueryURL.rssd_order_query_create_url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                if (result.process_status == 'NO')
                    Toast.show({ type: 'error', text1: 'Sorry', text2: result.process_message })
                else {
                    Toast.show({ type: 'success', text1: 'Success', text2: result.process_message })
                    gotoBackPage()
                }
                setLoading(false)
            }).catch((error) => { })
    }

    const gotoBackPage = () => {
        const timer = setTimeout(() => {
            navigation.goBack()
        }, 2000)
    }

    const onChange = (event, selectedDate) => {
        setIsDateTimePicker(false)
        if (event.type === "dismissed" || !selectedDate)
            return
        if (typeOfDatePick == 'start') {
            const start_date = moment(new Date(selectedDate)).format('DD-MM-YYYY')
            setStartDate(start_date)
            DataStorage.typeOfUse == 1 ? requestForAllListofOrderEnquirySBS(start_date, endDate) : requestForAllListofOrderEnquiryCement(start_date, endDate)
        } else if (typeOfDatePick == 'end') {
            const end_date = moment(new Date(selectedDate)).format('DD-MM-YYYY')
            setEndDate(end_date)
            DataStorage.typeOfUse == 1 ? requestForAllListofOrderEnquirySBS(startDate, end_date) : requestForAllListofOrderEnquiryCement(startDate, end_date)
        } else if (typeOfDatePick == 'requirement')
            setDateOfRequirement(moment(new Date(selectedDate)).format('DD-MM-YYYY'))
        setTypeOfDatePick('0')
    }

    const openDatePicker = (type) => {
        setIsDateTimePicker(false)
        setTypeOfDatePick(type)
        setTimeout(() => {
            setIsDateTimePicker(true)
        }, 50)
    }

    const renderOrderEnquiry = ({ item }) => {
        return (
            <View style={{ flex: 1, margin: moderateScale(7), elevation: 5, backgroundColor: '#FFF', flexDirection: 'column', borderRadius: moderateScale(5) }}>
                <View style={{ height: moderateScale(5) }} />
                <View style={{ flexDirection: 'row', width: '100%', paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(5), alignItems: 'center' }}>
                    <Text style={{ flex: 1, fontSize: moderateScale(12), color: '#888' }}>Product Name</Text>
                    <Text style={{ flex: 2, fontSize: moderateScale(14), color: '#333' }}>{item.prod_name}</Text>
                </View>
                <View style={{ flexDirection: 'row', width: '100%', paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(5), alignItems: 'center' }}>
                    <Text style={{ flex: 1, fontSize: moderateScale(12), color: '#888' }}>Qty Bags</Text>
                    <Text style={{ flex: 2, fontSize: moderateScale(14), color: '#333' }}>{item.qty_bags}</Text>
                </View>
                <View style={{ flexDirection: 'row', width: '100%', paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(5), alignItems: 'center' }}>
                    <Text style={{ flex: 1, fontSize: moderateScale(12), color: '#888' }}>Date and Time</Text>
                    <Text style={{ flex: 2, fontSize: moderateScale(14), color: '#333' }}>{item.date_and_time}</Text>
                </View>
                <View style={{ flexDirection: 'row', width: '100%', paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(5), alignItems: 'center' }}>
                    <Text style={{ flex: 1, fontSize: moderateScale(12), color: '#888' }}>RSSD Name</Text>
                    <Text style={{ flex: 2, fontSize: moderateScale(14), color: '#333' }}>{item.rssd_name}</Text>
                </View>
                <View style={{ flexDirection: 'row', width: '100%', paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(5), alignItems: 'center' }}>
                    <Text style={{ flex: 1, fontSize: moderateScale(12), color: '#888' }}>Date of Requirement</Text>
                    <Text style={{ flex: 2, fontSize: moderateScale(14), color: '#333' }}>{item.date_of_lifting}</Text>
                </View>
                <View style={{ flexDirection: 'row', width: '100%', paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(5), alignItems: 'center' }}>
                    <Text style={{ flex: 1, fontSize: moderateScale(12), color: '#888' }}>Remarks</Text>
                    <Text style={{ flex: 2, fontSize: moderateScale(14), color: '#333' }}>{item.remarks}</Text>
                </View>
                <View style={{ flexDirection: 'row', width: '100%', paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(5), alignItems: 'center' }}>
                    <Text style={{ flex: 1, fontSize: moderateScale(12), color: '#888' }}>Status</Text>
                    <Text style={{ flex: 2, fontSize: moderateScale(14), color: '#333' }}>{item.status_from_app}</Text>
                </View>
                <View style={{ height: moderateScale(5) }} />
            </View>
        )
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Order Enquiry" backPath=" " />
                <View style={{ flex: 1, width: "100%" }}>
                    {UrlStorage.ParameterList.BasicData.user_type.toLocaleLowerCase() == 'dealer' || UrlStorage.ParameterList.BasicData.user_type == 'broker' ? <View style={{ flex: 1 }}>
                        <View style={{ width: '100%', flexDirection: 'row', paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(10), backgroundColor: '#E41B1410' }}>
                            <TouchableOpacity onPress={() => openDatePicker('start')} style={{ flex: 1, flexDirection: 'row' }}>
                                <Text style={{ color: '#555', fontSize: moderateScale(14) }}>Start Date : </Text>
                                <Text style={{ color: '#000', fontSize: moderateScale(14) }}>{startDate}</Text>
                            </TouchableOpacity>
                            <TouchableOpacity onPress={() => openDatePicker('end')} style={{ flex: 1, flexDirection: 'row' }}>
                                <Text style={{ color: '#555', fontSize: moderateScale(14) }}>End Date : </Text>
                                <Text style={{ color: '#000', fontSize: moderateScale(14) }}>{endDate}</Text>
                            </TouchableOpacity>
                        </View>
                        <View style={{ flex: 1, paddingTop: moderateScale(10) }}>
                            <FlatList
                                data={data}
                                keyExtractor={(item) => item.id}
                                showsVerticalScrollIndicator={false}
                                decelerationRate="fast"
                                renderItem={renderOrderEnquiry}
                                contentContainerStyle={{ paddingBottom: moderateScale(20) }}
                                ListEmptyComponent={() => (
                                    <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', paddingVertical: moderateScale(50) }}>
                                        <Text style={{ color: Colors.text, fontSize: moderateScale(16), textAlign: 'center' }}> No enquiries found </Text>
                                    </View>
                                )}
                            />
                        </View>
                    </View> : <View style={{ flex: 1 }}>
                        <ScrollView style={{ flex: 1 }} showsVerticalScrollIndicator={false} contentContainerStyle={{ flexGrow: 1 }} >
                            <View style={{ width: "100%", padding: moderateScale(20), gap: moderateScale(16) }}>
                                <View style={{ width: "100%", gap: moderateScale(8) }}>
                                    <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}>Linked Dealer</Text>
                                    <View style={{ width: "100%", height: moderateScale(45), justifyContent: 'center', borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10) }}>
                                        <Text style={{ color: "#666", fontSize: moderateScale(14) }} >{UrlStorage.ParameterList.BasicData.belong_dealer_name}</Text>
                                    </View>
                                </View>
                                <View style={{ width: "100%", gap: moderateScale(8) }}>
                                    <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}>Product Name</Text>
                                    <TouchableOpacity onPress={() => { setIsOpenPopup(true) }} style={{ width: "100%", height: moderateScale(45), flexDirection: "row", alignItems: "center", justifyContent: "space-between", borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10) }}>
                                        <Text placeholder='|' style={{ flex: 1, color: "#666", fontSize: moderateScale(14) }}>{productName || 'Select Product'}</Text>
                                        <Image source={Icons.DownArrow} style={{ width: moderateScale(16), height: moderateScale(16), tintColor: DataStorage.primaryColorCode }} />
                                    </TouchableOpacity>
                                </View>
                                <View style={{ width: "100%", gap: moderateScale(8) }}>
                                    <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}>Quantity Bag</Text>
                                    <View style={{ width: "100%", borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10) }}>
                                        <TextInput
                                            placeholder='Enter quantity'
                                            style={{ width: "100%", color: "#666", fontSize: moderateScale(14), paddingVertical: moderateScale(12) }}
                                            keyboardType='number-pad'
                                            value={quantityBag}
                                            onChangeText={(text) => { setQuantityBag(text) }}
                                        />
                                    </View>
                                </View>
                                <View style={{ width: "100%", gap: moderateScale(8) }}>
                                    <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}>Date of Requirement</Text>
                                    <TouchableOpacity onPress={() => openDatePicker('requirement')} style={{ width: "100%", height: moderateScale(45), flexDirection: "row", alignItems: "center", justifyContent: "space-between", borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10) }}>
                                        <Text placeholder='|' style={{ flex: 1, color: "#666", fontSize: moderateScale(14) }}>{dateOfRequirement || 'Select Date'}</Text>
                                        <Image source={Icons.Calender} style={{ width: moderateScale(20), height: moderateScale(20), tintColor: DataStorage.primaryColorCode }} />
                                    </TouchableOpacity>
                                </View>
                                <View style={{ width: "100%", gap: moderateScale(8) }}>
                                    <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}>Enter Remarks</Text>
                                    <View style={{ width: "100%", minHeight: moderateScale(120), borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10) }}>
                                        <TextInput
                                            placeholder='Enter remarks'
                                            style={{ width: "100%", color: "#666", fontSize: moderateScale(14), textAlignVertical: 'top', paddingVertical: moderateScale(12), minHeight: moderateScale(100) }}
                                            multiline={true}
                                            numberOfLines={4}
                                            value={remarks}
                                            onChangeText={(text) => { setRemarks(text) }}
                                        />
                                    </View>
                                </View>
                            </View>
                        </ScrollView>
                        <TouchableOpacity activeOpacity={0.95} onPress={() => { checkDataForNewOrderEnquiry() }}>
                            <View style={{ width: "100%", paddingHorizontal: moderateScale(15), marginBottom: moderateScale(20) }}>
                                <View style={{ width: "100%", height: moderateScale(40), flexDirection: "row", gap: moderateScale(8), backgroundColor: DataStorage.primaryColorCode, borderRadius: moderateScale(10), alignItems: "center", justifyContent: "center" }}>
                                    <Text style={{ color: Colors.white, fontSize: moderateScale(16), fontWeight: "500" }}>Submit</Text>
                                </View>
                            </View>
                        </TouchableOpacity>
                    </View>}
                </View>
            </View>
            {isDateTimePicker && <DateTimePicker value={new Date()} mode="date" display="default" onChange={onChange} />}
            <Modal isVisible={isOpenPopup} style={{ margin: 0 }} customBackdrop={
                <TouchableWithoutFeedback onPress={() => { setIsOpenPopup(false) }}>
                    <View style={{ flex: 1, backgroundColor: "black" }} />
                </TouchableWithoutFeedback>
            }>
                <View style={{ width: '100%', height: '100%', flexDirection: 'column-reverse' }}>
                    <View style={{ width: "100%", maxHeight: '75%', backgroundColor: Colors.main, borderTopLeftRadius: moderateScale(20), borderTopRightRadius: moderateScale(20) }}>
                        <View style={{ width: "100%", padding: moderateScale(20), backgroundColor: "#E41B14", flexDirection: "row", alignItems: "center", justifyContent: "space-between", borderTopLeftRadius: moderateScale(10), borderTopRightRadius: moderateScale(10) }}>
                            <Text style={{ color: "#FFFFFF", fontSize: moderateScale(16), fontWeight: "600" }}>Select Product</Text>
                        </View>
                        <View style={{ width: "100%", backgroundColor: "#ffffff", paddingVertical: moderateScale(25), borderTopRightRadius: moderateScale(25), borderTopLeftRadius: moderateScale(25), flex: 1 }}>
                            <View style={{ width: "100%", gap: moderateScale(8), paddingHorizontal: moderateScale(10) }}>
                                <View style={{ width: "100%", flexDirection: 'row', borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10), alignItems: 'center' }}>
                                    <TextInput
                                        placeholder='Search Product'
                                        style={{ flex: 1, color: "#666", fontSize: moderateScale(14), paddingVertical: moderateScale(12) }}
                                    />
                                    <Image source={Icons.Search} style={{ width: moderateScale(20), height: moderateScale(20), tintColor: Colors.main }} />
                                </View>
                            </View>
                            <View style={{ height: moderateScale(10) }} />
                            <FlatList
                                data={productList}
                                keyExtractor={(item) => item.id}
                                showsVerticalScrollIndicator={false}
                                decelerationRate="fast"
                                style={{ flex: 1 }}
                                renderItem={({ item, index }) => {
                                    return (
                                        <TouchableOpacity activeOpacity={0.95} onPress={() => {
                                            setProductName(item.prod_desc)
                                            setProductCode(item.prod_code)
                                            setDnsProductCode(item.dns_prod_code)
                                            setIsOpenPopup(false)
                                        }}>
                                            <View style={{ width: "100%", paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(13), flexDirection: "row", alignItems: "center", justifyContent: "space-between", backgroundColor: (index % 2 != 0 ? DataStorage.transColorCode : "#FFFFFF") }}>
                                                <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500", textTransform: "uppercase" }}>{item.prod_desc}</Text>
                                            </View>
                                        </TouchableOpacity>
                                    )
                                }}
                            />
                        </View>
                    </View>
                </View>
            </Modal>
            <Toast config={toastConfig} />
            {loading && <Loader />}
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

export default OrderEnquiryScreen