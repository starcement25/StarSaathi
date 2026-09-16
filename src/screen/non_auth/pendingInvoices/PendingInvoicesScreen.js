import React, { useEffect, useState } from 'react'
import { FlatList, Image, Text, TouchableOpacity, View } from 'react-native'
import DateTimePicker from '@react-native-community/datetimepicker'
import SafeView from '../../../helper/SafeView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Icons } from '../../../assets/Icons'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import moment from 'moment'
import Loader from '../../../common/Loader'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const PendingInvoicesScreen = (props) => {
    const [appOrderList, setAppOrderList] = useState([])
    const [startDate, setStartDate] = useState(moment().subtract(7, 'days').format('DD-MM-YYYY'))
    const [endDate, setEndDate] = useState(moment(new Date()).format('DD-MM-YYYY'))
    const [loading, setLoading] = useState(moment(new Date()).format('DD-MM-YYYY'))
    const [authChecker, setAuthChecker] = useState(false)
    const [showStartDatePicker, setShowStartDatePicker] = useState(false)
    const [showEndDatePicker, setShowEndDatePicker] = useState(false)
    const [tempStartDate, setTempStartDate] = useState(moment().subtract(7, 'days').toDate())
    const [tempEndDate, setTempEndDate] = useState(new Date())

    useEffect(() => {
        DataStorage.typeOfUse == 1 ? requestForPendingInvoiceSBS() : requestForPendingInvoiceCement()
    }, [])

    useEffect(() => {
        DataStorage.typeOfUse == 1 ? requestForPendingInvoiceSBS() : requestForPendingInvoiceCement()
    }, [startDate, endDate])

    const requestForPendingInvoiceCement = async () => {
        const requestOptions = { method: "GET", redirect: "follow" }
        setLoading(true)
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.InvoiceURL.invoice_list_url
        url = url + "?customer_code=" + UrlStorage.ParameterList.BasicData.emp_code + "&from_date=" + moment(startDate, 'DD-MM-YYYY').format('YYYY-MM-DD') + "&to_date=" + moment(endDate, 'DD-MM-YYYY').format('YYYY-MM-DD')
        await fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                if (result.process_status == 'YES')
                    setAppOrderList(result.data)
                else
                    setAppOrderList([])
            })
            .catch((error) => {
                setAppOrderList([])
            })
        setLoading(false)
    }

    const requestForPendingInvoiceSBS = async () => {
        const requestOptions = { method: "GET", redirect: "follow" }
        setLoading(true)
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.InvoiceURL.invoice_list_url
        url = url + "?customer_code=" + UrlStorage.ParameterList.BasicData.emp_code + "&from_date=" + moment(startDate, 'DD-MM-YYYY').format('YYYY-MM-DD') + "&to_date=" + moment(endDate, 'DD-MM-YYYY').format('YYYY-MM-DD')
        await fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                if (result.process_status == 'YES')
                    setAppOrderList(result.data)
                else
                    setAppOrderList([])
            })
            .catch((error) => {
                setAppOrderList([])
            })
        setLoading(false)
    }

    const handleStartDateConfirm = (event, selectedDate) => {
        setShowStartDatePicker(false)
        if (selectedDate) {
            setTempStartDate(selectedDate)
            const formattedDate = moment(selectedDate).format('DD-MM-YYYY')
            setStartDate(formattedDate)
            if (moment(selectedDate).isAfter(moment(endDate, 'DD-MM-YYYY'))) {
                const newEndDate = moment(selectedDate).format('DD-MM-YYYY')
                setEndDate(newEndDate)
                setTempEndDate(selectedDate)
            }
        }
    }

    const handleEndDateConfirm = (event, selectedDate) => {
        setShowEndDatePicker(false)
        if (selectedDate) {
            setTempEndDate(selectedDate)
            const formattedDate = moment(selectedDate).format('DD-MM-YYYY')
            setEndDate(formattedDate)
        }
    }

    const openStartDatePicker = () => {
        setShowStartDatePicker(true)
        setShowEndDatePicker(false)
    }

    const openEndDatePicker = () => {
        setShowEndDatePicker(true)
        setShowStartDatePicker(false)
    }

    const openPdf = (item) => {
        props.navigation.navigate('PdfViewScreen', { pdfUrl: item.dwd_url, page_title: 'Pending Invoices PDF', type: 'url' })
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Pending Invoices" backPath=" " />
                <View style={{ width: "100%", padding: moderateScale(20), gap: moderateScale(20), flex: 1 }}>
                    <View style={{ width: "100%", alignItems: "flex-start", gap: moderateScale(10) }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500" }}>Select Date Range</Text>
                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", gap: moderateScale(20) }}>
                            <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={openStartDatePicker}>
                                <View style={{ flexDirection: "row", gap: moderateScale(10), alignItems: "center", width: "100%", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), padding: moderateScale(10) }}>
                                    <Image source={Icons.Calender} style={{ width: moderateScale(18), height: moderateScale(18), tintColor: DataStorage.primaryColorCode }} />
                                    <Text style={{ color: Colors.text, fontSize: moderateScale(14) }}>{startDate}</Text>
                                </View>
                            </TouchableOpacity>
                            <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={openEndDatePicker}>
                                <View style={{ flexDirection: "row", gap: moderateScale(10), alignItems: "center", width: "100%", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), padding: moderateScale(10) }}>
                                    <Image source={Icons.Calender} style={{ width: moderateScale(18), height: moderateScale(18), tintColor: DataStorage.primaryColorCode }} />
                                    <Text style={{ color: Colors.text, fontSize: moderateScale(14) }}>{endDate}</Text>
                                </View>
                            </TouchableOpacity>
                        </View>
                    </View>
                    <FlatList
                        data={appOrderList}
                        keyExtractor={(item) => item.id}
                        showsVerticalScrollIndicator={false}
                        decelerationRate="fast"
                        renderItem={({ item }) => (
                            <TouchableOpacity activeOpacity={0.95} style={{ marginBottom: moderateScale(14), }} onPress={() => { }}>
                                <View style={{ width: "100%", overflow: "hidden", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), backgroundColor: "#FFFFFF", elevation: 10, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: (0), y: (4) } }}>
                                    <View style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(6), alignItems: "flex-start" }}>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Invoice NO:</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500", flex: 1 }}>{item.invoice_no}</Text>
                                            <TouchableOpacity onPress={() => { openPdf(item) }}>
                                                <View style={{ flexDirection: "row", alignItems: "center", gap: moderateScale(4), paddingHorizontal: moderateScale(6), padding: moderateScale(4), borderRadius: moderateScale(6), backgroundColor: DataStorage.primaryColorCode }}>
                                                    <Image source={Icons.Pdf} style={{ width: moderateScale(11), height: moderateScale(13), resizeMode: 'contain' }} />
                                                    <Text style={{ color: Colors.white, fontSize: moderateScale(10) }}>PDF</Text>
                                                </View>
                                            </TouchableOpacity>
                                        </View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Invoice Date:</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{moment(item.invoice_date, 'YYYY-MM-DD').format('MMM DD, YYYY')}</Text>
                                        </View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Deliver No.</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{item.delivery_no}</Text>
                                        </View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Sales Order No.</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{item.sale_order_no}</Text>
                                        </View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>App Order No.</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{item.app_order_no}</Text>
                                        </View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Product Name</Text>
                                            <Text style={{ flex: 1, color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{item.product_name}</Text>
                                        </View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Invoice QTY</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{item.invoice_qty}</Text>
                                        </View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Destination</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{item.destination}</Text>
                                        </View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Truck No.</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{item.truck_no}</Text>
                                        </View>
                                    </View>
                                </View>
                            </TouchableOpacity>
                        )}
                    />
                </View>
                {showStartDatePicker && <View style={{ width: '100%', height: '100%', position: 'absolute', alignItems: 'center', justifyContent: 'center', backgroundColor: '#00000060' }}>
                    <View style={{ backgroundColor: '#FFF', padding: 10, borderRadius: 10 }}>
                        <DateTimePicker value={tempStartDate} mode="date" display="default" maximumDate={new Date()} onChange={handleStartDateConfirm} />
                    </View>
                </View>}
                {showEndDatePicker && <View style={{ width: '100%', height: '100%', position: 'absolute', alignItems: 'center', justifyContent: 'center', backgroundColor: '#00000060' }}>
                    <View style={{ backgroundColor: '#FFF', padding: 10, borderRadius: 10 }}>
                        <DateTimePicker value={tempEndDate} mode="date" display="default" maximumDate={new Date()} minimumDate={moment(startDate, 'DD-MM-YYYY').toDate()} onChange={handleEndDateConfirm} />
                    </View>
                </View>}
            </View>
            {loading && <Loader />}
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

export default PendingInvoicesScreen