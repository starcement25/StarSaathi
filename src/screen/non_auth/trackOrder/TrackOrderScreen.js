import React, { useEffect, useState } from 'react'
import { FlatList, Image, Linking, Text, TouchableOpacity, View, ActivityIndicator } from 'react-native'
import DateTimePicker from '@react-native-community/datetimepicker';
import SafeView from '../../../helper/SafeView'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { moderateScale } from '../../../helper/Window'
import { Icons } from '../../../assets/Icons'
import { Colors } from '../../../assets/Colors'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import moment from 'moment';
import Loader from '../../../common/Loader';
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi';
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView';

const TrackOrderScreen = (props) => {
    const [orderList, setOrderList] = useState([])
    const [appOrder, setAppOrder] = useState(true)
    const [loading, setLoading] = useState(false)
    const [loadingMore, setLoadingMore] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)

    const [startDate, setStartDate] = useState('')
    const [endDate, setEndDate] = useState('')
    const [startDateForPicker, setStartDateForPicker] = useState(new Date())
    const [endDateForPicker, setEndDateForPicker] = useState(new Date())

    const [datePickerType, setDatePickerType] = useState('')
    const [isDateTimePicker, setIsDateTimePicker] = useState(false)
    const [isInitialLoad, setIsInitialLoad] = useState(true)

    const [currentPage, setCurrentPage] = useState(1)
    const [totalPages, setTotalPages] = useState(1)
    const [hasMore, setHasMore] = useState(false)
    const PAGE_SIZE = 30

    useEffect(() => {
        setCurrentPage(1)
        setOrderList([])

        if (isInitialLoad) {
            const { today, monthBack } = getTodayAndMonthBack();
            const startDateFormatted = moment(monthBack, 'YYYY-MM-DD').format('DD-MM-YYYY');
            const endDateFormatted = moment(today, 'YYYY-MM-DD').format('DD-MM-YYYY');

            setStartDate(startDateFormatted);
            setEndDate(endDateFormatted);
            setStartDateForPicker(moment(monthBack, 'YYYY-MM-DD').toDate());
            setEndDateForPicker(moment(today, 'YYYY-MM-DD').toDate());

            fetchOrderList(monthBack, today, 1);
            setIsInitialLoad(false);
        } else {

            const startDateFormatted = startDate ? moment(startDate, 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
            const endDateFormatted = endDate ? moment(endDate, 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
            fetchOrderList(startDateFormatted, endDateFormatted, 1);
        }
    }, [appOrder])

    const fetchAuthData = async () => {
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
        }
    }

    const fetchOrderList = (startDateParam, endDateParam, page = 1) => {
        if (appOrder) {
            let url;
            if (DataStorage.typeOfUse == 1) {
                url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.order.order_history + `?page=${page}&page_size=${PAGE_SIZE}`;
                const params = [];
                if (startDateParam && startDateParam !== '')
                    params.push('order_date_after=' + startDateParam);
                if (endDateParam && endDateParam !== '')
                    params.push('order_date_before=' + endDateParam);

                if (UrlStorage.ParameterList.BasicData.user_type?.toLowerCase() === 'broker')
                    params.push('customer_SAP_code=' + UrlStorage.ParameterList.BasicData.selectedCustomerCode);
                else
                    params.push('customer_SAP_code=' + UrlStorage.ParameterList.BasicData.emp_id);


                if (params.length > 0) {
                    url += '&' + params.join('&');
                }
            } else {
                url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.TrackOrderURL.other_order_list_url;
                url = url + "?the_id=" + (UrlStorage.ParameterList.BasicData.user_type == 'broker' ? UrlStorage.ParameterList.BasicData.customerDetails.customer_code : UrlStorage.ParameterList.BasicData.emp_code) + "&user_type=" + UrlStorage.ParameterList.BasicData.user_type + '&start_date=' + startDateParam + '&end_date=' + endDateParam;
            }
            console.log(url);

            requestForAppOrderList(url, page);
        } else {
            let url;
            if (DataStorage.typeOfUse == 1) {
                setOrderList([]);
                setCurrentPage(1);
                setTotalPages(1);
                setHasMore(false);
                url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.order.order_history_offline + `?page=${page}&page_size=${PAGE_SIZE}`;
                const params = [];
                if (startDateParam && startDateParam !== '')
                    params.push('filter_erporderdt_from=' + startDateParam);
                if (endDateParam && endDateParam !== '')
                    params.push('filter_erporderdt_to=' + endDateParam);

                if (UrlStorage.ParameterList.BasicData.user_type?.toLowerCase() === 'broker')
                    params.push('customer_SAP_code=1000001052' );//+ UrlStorage.ParameterList.BasicData.selectedCustomerCode
                else
                    params.push('customer_SAP_code=' + UrlStorage.ParameterList.BasicData.emp_id);


                if (params.length > 0) {
                    url += '&' + params.join('&');
                }

            } else {
                let url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.TrackOrderURL.other_offline_order_list_url;
                url = url + "?the_id=" + (UrlStorage.ParameterList.BasicData.user_type == 'broker' ? UrlStorage.ParameterList.BasicData.customerDetails.SAP_code : UrlStorage.ParameterList.BasicData.emp_code) + "&user_type=" + UrlStorage.ParameterList.BasicData.user_type + '&start_date=' + startDateParam + '&end_date=' + endDateParam;
            }
            requestForAppOrderList(url, page);
        }
    };

    const requestForAppOrderList = async (url, page = 1) => {
        const myHeaders = new Headers();
        DataStorage.typeOfUse === 1 && myHeaders.append("Authorization", UrlStorage.ParameterList.BasicData.user_type == 'broker' ? `SAP_SP` : `SAP_DEALER` + ` ${UrlStorage.ParameterList.BasicData.emp_id}`);
        myHeaders.append("Content-Type", "application/json");

        if (page === 1) {
            setLoading(true);
        } else {
            setLoadingMore(true);
        }

        try {
            const requestOptions = {
                method: "GET",
                redirect: "follow",
                headers: DataStorage.typeOfUse == 1 ? myHeaders : undefined
            };
            var a = await AuthCheckingApi();
            if (!a) {
                setAuthChecker(true)
                setLoading(false)
                return false
            }
console.log(url);

            const response = await fetch(url, requestOptions);
            const result = await response.json();
            if (DataStorage.typeOfUse == 1) {
                if (appOrder) {
                    if (result && Array.isArray(result.results) && result.results.length > 0) {
                        const transformedData = result.results.map((item) => {
                            const invDetail = item.inv_details && item.inv_details.length > 0 ? item.inv_details[0] : {};

                            const orderDetail = item.zorderdetailsp_sbs && item.zorderdetailsp_sbs.length > 0 ? item.zorderdetailsp_sbs[0] : {};

                            return {
                                ...item,
                                APPORDERNO: item.APPORDERNO || '',
                                ERPORDERNO: item.ERPORDERNO || '',
                                STATUS: item.STATUS || '',
                                order_date: item.order_date || '',
                                QTY: item.QTY || '',
                                UOM1: item.UOM1 || '',
                                prod_display_name: item.prod_display_name || '',
                                destination_address: item.destination_address || '',
                                freight: item.freight || '',
                                plant: orderDetail.plant_name || '',
                                order_invoice_data: item.zorderdetailsp_sbs ? item.zorderdetailsp_sbs.map(detail => ({
                                    invno: invDetail.INVNO || '-',
                                    invdt: invDetail.INVDT ? moment(invDetail.INVDT, 'YYYYMMDD').format('DD MMM YYYY') : '-',
                                    invQty: invDetail.INVQTY || '-',
                                    Unit: invDetail.UOM || '-',
                                    zOrder_unit: detail?.Unit || '',
                                    prod_display_name: item.prod_display_name || '-',
                                    Qty: detail.Qty || '-',
                                    truckno: invDetail.TRUCKNO || '-',
                                    driverno: invDetail.DRIVERNO || '-',
                                    plant_name: detail.plant_name || '-',
                                    sales_group: detail.sales_group || '-',
                                    company_code: detail.company_code || '-',

                                    DATE: detail.DATE || '',
                                    mail_sent_date_time: item.mail_sent_date_time || '',
                                    order_date: item.order_date || ''
                                })) : [],

                                isShowDetails: false
                            };
                        });

                        if (page === 1) {
                            setOrderList(transformedData);
                        } else {
                            setOrderList(prevList => [...prevList, ...transformedData]);
                        }

                        const totalCount = result.count || 0;
                        const calculatedTotalPages = Math.ceil(totalCount / PAGE_SIZE);
                        setTotalPages(calculatedTotalPages);
                        setCurrentPage(page);
                        setHasMore(page < calculatedTotalPages);
                    } else {
                        if (page === 1) {
                            setOrderList([]);
                        }
                        setCurrentPage(page);
                        setTotalPages(1);
                        setHasMore(false);
                    }
                } else {
                    console.log(result.results.length);
                    console.log(result.count);
                    
                    if (result && Array.isArray(result.results) && result.results.length > 0) {
                        console.log(result.results.length);
                        const transformedData = result.results.map((item) => {
                            const invDetail = item.inv_details && item.inv_details.length > 0 ? item.inv_details[0] : {};
                            return {
                                ...item,
                                APPORDERNO: item.id || '',
                                ERPORDERNO: item.challan_details?.ERPORDERNO || '',
                                STATUS: item.STATUS || '',
                                order_date: item.ERPORDERDT || '',
                                QTY: item.QTY || '',
                                UOM1: item.product_details?.UOM1 || '',
                                prod_display_name: item.prod_display_name || '',
                                destination_address: item.destination_name || '',
                                freight: item.freight || '',
                                plant: item.dump_plant_name || '',
                                truckNo:item.challan_details?.TRUCKNO||'',
                                order_invoice_data: item.zorderdetailsp_sbs ? item.zorderdetailsp_sbs.map(detail => ({
                                    invno: invDetail.INVNO || '-',
                                    invdt: invDetail.INVDT ? moment(invDetail.INVDT, 'YYYYMMDD').format('DD MMM YYYY') : '-',
                                    invQty: invDetail.INVQTY || '-',
                                    Unit: invDetail.UOM || '-',
                                    zOrder_unit: detail?.Unit || '',
                                    prod_display_name: item.prod_display_name || '-',
                                    Qty: detail.Qty || '-',
                                    truckno: invDetail.TRUCKNO || '-',
                                    driverno: invDetail.DRIVERNO || '-',
                                    plant_name: detail.plant_name || '-',
                                    sales_group: detail.sales_group || '-',
                                    company_code: detail.company_code || '-',

                                    DATE: detail.DATE || '',
                                    mail_sent_date_time: item.mail_sent_date_time || '',
                                    order_date: item.order_date || ''
                                })) : [],

                                isShowDetails: false
                            };
                        });

                        console.log(transformedData.length);
                        

                        if (page === 1) {
                            setOrderList(transformedData);
                        } else {
                            setOrderList(prevList => [...prevList, ...transformedData]);
                        }

                        const totalCount = result.count || 0;
                        const calculatedTotalPages = Math.ceil(totalCount / PAGE_SIZE);
                        setTotalPages(calculatedTotalPages);
                        setCurrentPage(page);
                        setHasMore(page < calculatedTotalPages);
                    } else {
                        if (page === 1) {
                            setOrderList([]);
                        }
                        setCurrentPage(page);
                        setTotalPages(1);
                        setHasMore(false);
                    }
                }

            } else {
                if (result.process_status === "YES" && Array.isArray(result.order_data) && result.order_data.length > 0) {
                    const orderData = result.order_data.map((item) => ({ ...item, isShowDetails: false }));
                    setOrderList(orderData);
                } else {
                    setOrderList([]);
                }
            }
        } catch (error) {
            if (page === 1) {
                setOrderList([]);
            }
            setCurrentPage(page);
            setTotalPages(1);
            setHasMore(false);
        } finally {
            setLoading(false);
            setLoadingMore(false);
        }
    };

    const loadMoreOrders = () => {
        if (DataStorage.typeOfUse == 1 && !loadingMore && !loading && hasMore) {
            const nextPage = currentPage + 1;
            const startDateFormatted = startDate ? moment(startDate, 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
            const endDateFormatted = endDate ? moment(endDate, 'DD-MM-YYYY').format('YYYY-MM-DD') : '';

            fetchOrderList(startDateFormatted, endDateFormatted, nextPage);
        }
    };

    const openCloseDetails = (item, index) => {
        try {
            let shouldToggle = false;

            if (DataStorage.typeOfUse != 1) {
                shouldToggle = item?.order_invoice_data?.length > 0 ? true : false
            } else {
                shouldToggle = true
            }

            if (shouldToggle) {
                const updatedList = orderList.map((listItem, i) => i === index ? { ...listItem, isShowDetails: !listItem.isShowDetails } : { ...listItem, isShowDetails: false });
                setOrderList(updatedList);
            }
        } catch (error) { }
    };

    const getTodayAndMonthBack = () => {
        const today = moment().format('YYYY-MM-DD');
        const monthBack = moment().subtract(1, 'months').format('YYYY-MM-DD');
        return { today, monthBack };
    };

    const onChange = (event, selectedDate) => {
        const currentDate = selectedDate || (datePickerType === 'start' ? startDateForPicker : endDateForPicker);

        // On Android, dismiss is called when user clicks outside or presses back
        // On iOS, we need to handle the confirm/cancel buttons differently
        if (event.type === "dismissed") {
            setIsDateTimePicker(false);
            setDatePickerType('');
            return;
        }
        if (datePickerType === 'start') {
            const formattedDate = moment(currentDate).format('DD-MM-YYYY');
            setStartDate(formattedDate);
            setStartDateForPicker(currentDate);
        } else if (datePickerType === 'end') {
            const formattedDate = moment(currentDate).format('DD-MM-YYYY');
            setEndDate(formattedDate);
            setEndDateForPicker(currentDate);
        }

        // Close the picker after selection
        setIsDateTimePicker(false);
        setDatePickerType('');
    };

    const searchData = () => {
        // Reset pagination
        setCurrentPage(1);
        setOrderList([]);

        const startDateFormatted = startDate && startDate !== 'Start Date' ? moment(startDate, 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
        const endDateFormatted = endDate && endDate !== 'End Date' ? moment(endDate, 'DD-MM-YYYY').format('YYYY-MM-DD') : '';

        fetchOrderList(startDateFormatted, endDateFormatted, 1);
    };

    const formatOrderDate = (isoString) => {
        if (!isoString) return '';

        const date = new Date(isoString);

        return date.toLocaleString('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true,
        });
    };

    const ExpandableData = ({ item, freight = "" }) => {
        const rawDate = DataStorage.typeOfUse == 1 ? (item?.DATE || item?.mail_sent_date_time || item?.order_date) : (item?.invdt || item?.inv_date);

        let formattedDate = '';
        if (rawDate) {
            try {
                const dateObj = new Date(rawDate);
                if (!isNaN(dateObj.getTime())) {
                    formattedDate = dateObj.toLocaleDateString("en-GB", {
                        day: "2-digit",
                        month: "long",
                        year: "numeric",
                    });
                } else {
                    formattedDate = rawDate;
                }
            } catch (error) {
                formattedDate = rawDate;
            }
        }
        const invoiceNo = DataStorage.typeOfUse == 1 ? (item?.invno ?? '-') : (item?.invno ?? '-');
        const prodName = item?.prod_display_name ?? "-"
        const invoiceDt = item?.invdt ?? "-"
        const invoiceQty = DataStorage.typeOfUse == 1 ? (item?.invQty ?? '') : (item?.invqty ?? '-');
        const Qty = (item?.Qty ?? '');
        const truckNo = DataStorage.typeOfUse == 1 ? (item?.truckno) : (item?.truckno ?? '-');
        const driverNo = DataStorage.typeOfUse == 1 ? (item?.driverno) : (item?.driverno ?? '-');
        const plant = item?.plant_name ?? item?.plant_name ?? '';
        const salesGroup = item?.sales_group ?? '';

        return (
            <View style={{ width: "100%", paddingVertical: moderateScale(5), paddingHorizontal: moderateScale(10), gap: moderateScale(3), backgroundColor: DataStorage.transColorCode, borderTopWidth: moderateScale(1), borderColor: "#DCDDDF" }}>
                <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                    <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Invoice No:</Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>
                        {invoiceNo}
                    </Text>
                </View>

                <View style={{ width: "100%", flexDirection: "row", alignItems: "center" }}>
                    <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Invoice Date:</Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>
                        {invoiceDt || '-'}
                    </Text>
                </View>

                {DataStorage.typeOfUse == 1 ? <View style={{ width: "100%", flexDirection: "row", alignItems: "center" }}>
                    <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>{`Invoice QTY`}:</Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>
                        {(invoiceQty + ` ${item?.Unit}` || '-')}
                    </Text>
                </View> : <View style={{ width: "100%", flexDirection: "row", alignItems: "center" }}>
                    <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>{`Invoice QTY`}:</Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>
                        {(invoiceQty)}
                    </Text>
                </View>}
                {DataStorage.typeOfUse == 1 && <View style={{ width: "100%", flexDirection: "row", alignItems: "center" }}>
                    <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Product Name:</Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500", width: moderateScale(200) }} numberOfLines={2}>
                        {(prodName || '-')}
                    </Text>
                </View>}
                {DataStorage.typeOfUse == 1 && <View style={{ width: "100%", flexDirection: "row", alignItems: "center" }}>
                    <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Product Qty:</Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500", width: moderateScale(200) }} numberOfLines={2}>
                        {(Qty + " " + item?.zOrder_unit || '-')}
                    </Text>
                </View>}

                <View style={{ width: "100%", flexDirection: "row", alignItems: "center" }}>
                    <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Truck No:</Text>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>
                        {truckNo}
                    </Text>
                </View>

                <View style={{ width: "100%", flexDirection: "row", alignItems: "center" }}>
                    <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Driver Contact:</Text>
                    <Text
                        onPress={() => {
                            const driverNumber = item?.driverno;
                            if (driverNumber && DataStorage.typeOfUse !== 1) {
                                Linking.openURL(`tel:${driverNumber}`);
                            }
                        }}
                        style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500", textDecorationLine: (item?.driverno && DataStorage.typeOfUse !== 1) ? 'underline' : 'none' }} >
                        {driverNo}
                    </Text>
                </View>

                {DataStorage.typeOfUse == 1 && (
                    <>
                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(4) }}>
                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>{freight === "FOR" ? "Plant Name:" : "Dump Name:"} </Text>
                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>
                                {plant || '-'}
                            </Text>
                        </View>

                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(2) }}>
                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Sales Group:</Text>
                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>
                                {salesGroup || '-'}
                            </Text>
                        </View>

                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", marginTop: moderateScale(2) }}>
                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Company:</Text>
                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>
                                SSBSL
                            </Text>
                        </View>
                    </>
                )}
            </View>
        );
    };

    const setColorCode = (item) => {
        if (DataStorage.typeOfUse == 1) {
            return "#8FC031"
        } else {
            if (item.status.toLowerCase() == 'Dispatched'.toLowerCase())
                return "#2E7D32"
            else if (item.status.toLowerCase() == 'DO approved'.toLowerCase())
                return "#afb40fff"
            else if (item.status.toLowerCase() == 'Order canceled'.toLowerCase())
                return "#C62828"
            else if (item.status.toLowerCase() == 'CREDIT CHECK FAILED'.toLowerCase())
                return "#E65100"
            else if (item.status.toLowerCase() == 'Order received'.toLowerCase())
                return "#999"
            else
                return "#8FC031"
        }
    }

    const renderOrderItem = ({ item, index }) => {
        const canShowDetails = item?.order_invoice_data?.length > 0 ? true : false

        return (
            <View style={{ paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(7) }}>
                <View style={{ width: "100%", overflow: "hidden", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), backgroundColor: "white", elevation: 4, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: 0, y: 4 } }}>
                    <TouchableOpacity activeOpacity={0.95} onPress={() => openCloseDetails(item, index)} style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(3) }} >
                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between", }}>
                            {appOrder ? <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500", }}>
                                {DataStorage.typeOfUse == 1 ? (item.APPORDERNO || '') : (item.apporderno || '')}
                            </Text> : (
                                item.erporderno && item.erporderno !== '' && (
                                    <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                                        {item.erporderno}
                                    </Text>
                                )
                            )}
                            <View style={{ flexDirection: "row", alignItems: "center", gap: moderateScale(10) }}>
                                <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                                    X {DataStorage.typeOfUse == 1 ? `${item.QTY || ''} ${item.UOM1 || ''}` : `${item.qty || ''} `}
                                </Text>
                                <View style={{ paddingVertical: moderateScale(3), paddingHorizontal: moderateScale(10), borderRadius: moderateScale(10), backgroundColor: setColorCode(item) }}>
                                    <Text style={{ color: Colors.white, fontSize: moderateScale(12), textTransform: 'capitalize' }}>
                                        {DataStorage.typeOfUse == 1 ? (item.STATUS || '') : (item.status || '')}
                                    </Text>
                                </View>
                                {canShowDetails && (
                                    <View style={{ width: moderateScale(20), height: moderateScale(20), borderWidth: moderateScale(1), borderColor: DataStorage.primaryColorCode, alignItems: "center", justifyContent: "center" }}>
                                        <Image source={item.isShowDetails ? Icons.UpArrow : Icons.DownArrow} style={{ width: moderateScale(10), height: moderateScale(10), tintColor: DataStorage.primaryColorCode }} />
                                    </View>
                                )}
                            </View>
                        </View>

                        {DataStorage.typeOfUse == 1 ? (
                            item.ERPORDERNO && item.ERPORDERNO !== '' && (
                                <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                                    {item.ERPORDERNO}
                                </Text>
                            )
                        ) : (<></>
                            // item.erporderno && item.erporderno !== '' && (
                            //     <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            //         {item.erporderno}
                            //     </Text>
                            // )
                        )}
                        {appOrder ? <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            {item?.erporderno ?? ''}
                        </Text> : <></>}
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            {item.prod_display_name || ''}
                        </Text>
                        {appOrder ? <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            {DataStorage.typeOfUse == 1 ? formatOrderDate(item.order_date) : (item.order_full_date_time || '')}
                        </Text> : <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            {DataStorage.typeOfUse == 1 ? formatOrderDate(item.order_date) : (item.erporderdt || '')}
                        </Text>}
                        {appOrder ? <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            <Text style={{ fontWeight: '600' }}>Destination</Text> : {item.destination_address || ''}
                        </Text> : <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            <Text style={{ fontWeight: '600' }}>Destination</Text> : {item.destination_name || ''}
                        </Text>}
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            <Text style={{ fontWeight: '600' }}>Freight</Text> : {item.freight || ''}
                        </Text>
                        {item.freight == 'FOR' ? <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            <Text style={{ fontWeight: '600' }}>Plant Name</Text> : {item.plant || ''}
                        </Text> : <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            <Text style={{ fontWeight: '600' }}>Dump Name</Text> : {item.plant || ''}
                        </Text>}
                    </TouchableOpacity>

                    {item.isShowDetails && (
                        <FlatList
                            data={(item?.order_invoice_data || [])}
                            keyExtractor={(subItem, subIndex) => `${index}-${subIndex}`}
                            showsVerticalScrollIndicator={false}
                            decelerationRate="fast"
                            renderItem={({ item: subItem }) => <ExpandableData item={subItem} />} />
                    )}
                </View>
            </View>
        );
    };

    const renderSBSOrderItem = ({ item, index }) => {
        return (
            <View style={{ paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(7) }}>
                <View style={{ width: "100%", overflow: "hidden", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), backgroundColor: "white", elevation: 4, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: 0, y: 4 } }}>
                    <TouchableOpacity activeOpacity={0.95} onPress={() => openCloseDetails(item, index)} style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(3) }} >
                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                            <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500" }}>
                                {appOrder?item?.APPORDERNO || '':'Sales order no : '+item.ERPORDERNO}
                            </Text>

                            <View style={{ flexDirection: "row", alignItems: "center", gap: moderateScale(10) }}>
                                <View style={{ paddingVertical: moderateScale(3), paddingHorizontal: moderateScale(10), borderRadius: moderateScale(10), backgroundColor: "#8FC031" }}>
                                    <Text style={{ color: Colors.white, fontSize: moderateScale(12), textTransform: 'capitalize' }}>
                                        {item?.STATUS || item?.status || ''}
                                    </Text>
                                </View>
                                {DataStorage.typeOfUse == 1&&!appOrder?null:
                                <View style={{ width: moderateScale(20), height: moderateScale(20), borderWidth: moderateScale(1), borderColor: DataStorage.primaryColorCode, alignItems: "center", justifyContent: "center" }}>
                                    <Image source={(item && item.isShowDetails) ? Icons.UpArrow : Icons.DownArrow} style={{ width: moderateScale(10), height: moderateScale(10), tintColor: DataStorage.primaryColorCode }} />
                                </View>}
                            </View>
                        </View>

                        {appOrder&item?.ERPORDERNO ? (
                            <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                                {item.ERPORDERNO}
                            </Text>
                        ) : null}

                        <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            {formatOrderDate(item?.order_date)}
                        </Text>

                        <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            <Text style={{ fontWeight: '600' }}>Destination</Text> : {item?.destination_address || ''}
                        </Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>
                            <Text style={{ fontWeight: '600' }}>Freight</Text> : {item?.freight || ''}
                        </Text>

                        {item?.freight == 'FOR' ? <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            <Text style={{ fontWeight: '600' }}>Plant Name</Text> : {item?.plant || ''}
                        </Text> : <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            <Text style={{ fontWeight: '600' }}>Dump Name</Text> : {item?.plant || ''}
                        </Text>}

                        {!appOrder&&item.STATUS.toLowerCase()=='dispatched' ? <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>
                            <Text style={{ fontWeight: '600' }}>Truck No.</Text> : {item?.truckNo || ''}
                        </Text> : null}
                    </TouchableOpacity>

                    {(item && item.isShowDetails) && item?.order_invoice_data.length > 0 && (
                        <FlatList
                            data={item?.order_invoice_data}
                            keyExtractor={(subItem, idx) => `${item?.APPORDERNO}-${subItem?.id ?? subItem?.prod_code ?? subItem?.ProductCode ?? idx}`}
                            showsVerticalScrollIndicator={false}
                            initialNumToRender={5}
                            maxToRenderPerBatch={10}
                            windowSize={5}
                            renderItem={({ item: subItem }) => <ExpandableData item={subItem} freight={item?.freight} />} />
                    )}
                </View>
            </View>
        );
    };

    const renderFooter = () => {
        if (!loadingMore) return null;

        return (
            <View style={{ paddingVertical: moderateScale(20), alignItems: 'center' }}>
                <ActivityIndicator size="small" color={DataStorage.primaryColorCode} />
                <Text style={{ marginTop: moderateScale(8), color: Colors.text, fontSize: moderateScale(13) }}>
                    Loading more orders...
                </Text>
            </View>
        );
    };

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Track Orders (Invoice Data)" backPath=" " />
                <View style={{ width: "100%", paddingHorizontal: moderateScale(15), paddingVertical: moderateScale(10), gap: moderateScale(5), flex: 1 }}>
                    <View style={{ width: "100%", alignItems: "flex-start", gap: moderateScale(10), marginBottom: moderateScale(10) }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500" }}>
                            Select Date Range
                        </Text>
                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", gap: moderateScale(10) }}>
                            <TouchableOpacity
                                activeOpacity={0.95}
                                style={{ flex: 1 }}
                                onPress={() => {
                                    setDatePickerType('start');
                                    setIsDateTimePicker(true);
                                }} >
                                <View style={{ flexDirection: "row", gap: moderateScale(10), alignItems: "center", width: "100%", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), padding: moderateScale(10) }}>
                                    <Image source={Icons.Calender} style={{ width: moderateScale(18), height: moderateScale(18), tintColor: DataStorage.primaryColorCode }} />
                                    <Text style={{ color: Colors.text, fontSize: moderateScale(14) }}>
                                        {startDate || 'Start Date'}
                                    </Text>
                                </View>
                            </TouchableOpacity>

                            <TouchableOpacity
                                activeOpacity={0.95}
                                style={{ flex: 1 }}
                                onPress={() => {
                                    setDatePickerType('end');
                                    setIsDateTimePicker(true);
                                }} >
                                <View style={{ flexDirection: "row", gap: moderateScale(10), alignItems: "center", width: "100%", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), padding: moderateScale(10) }}>
                                    <Image source={Icons.Calender} style={{ width: moderateScale(18), height: moderateScale(18), tintColor: DataStorage.primaryColorCode }} />
                                    <Text style={{ color: Colors.text, fontSize: moderateScale(14) }}>
                                        {endDate || 'End Date'}
                                    </Text>
                                </View>
                            </TouchableOpacity>

                            <TouchableOpacity activeOpacity={0.95} style={{ width: moderateScale(40) }} onPress={searchData} >
                                <View style={{ flexDirection: "row", gap: moderateScale(10), alignItems: "center", width: "100%", backgroundColor: DataStorage.primaryColorCode, borderRadius: moderateScale(10), padding: moderateScale(10) }}>
                                    <Image source={Icons.Search} style={{ width: moderateScale(18), height: moderateScale(18), tintColor: Colors.white }} />
                                </View>
                            </TouchableOpacity>
                        </View>
                    </View>

                    <View style={{ width: "100%", flex: 1 }}>
                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "center" }}>
                            <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={() => setAppOrder(true)}>
                                <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: appOrder ? DataStorage.primaryColorCode : DataStorage.transColorCode, borderWidth: moderateScale(1), borderColor: "#DCDDDF" }}>
                                    <Text style={{ color: appOrder ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600" }}>
                                        App Order
                                    </Text>
                                </View>
                            </TouchableOpacity>
                            <TouchableOpacity activeOpacity={0.95} style={{ flex: 1 }} onPress={() => setAppOrder(false)}>
                                <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: !appOrder ? DataStorage.primaryColorCode : DataStorage.transColorCode, borderWidth: moderateScale(1), borderColor: "#DCDDDF" }}>
                                    <Text style={{ color: !appOrder ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600" }}>
                                        Offline Order
                                    </Text>
                                </View>
                            </TouchableOpacity>
                        </View>

                        <View style={{ width: "100%", flex: 1, backgroundColor: "white", borderWidth: moderateScale(1), borderColor: "#DCDDDF" }}>
                            <FlatList
                                data={orderList}
                                keyExtractor={(item, index) => `order-${index}`}
                                showsVerticalScrollIndicator={false}
                                decelerationRate="fast"
                                renderItem={DataStorage.typeOfUse === 1 ? renderSBSOrderItem : renderOrderItem}
                                ListEmptyComponent={() => (
                                    <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', paddingVertical: moderateScale(50) }}>
                                        <Text style={{ color: Colors.text, fontSize: moderateScale(16), textAlign: 'center' }}>
                                            No orders found
                                        </Text>
                                    </View>
                                )}
                                ListFooterComponent={renderFooter}
                                onEndReached={loadMoreOrders}
                                onEndReachedThreshold={0.5}
                            />

                            {/* Pagination info for debugging - remove in production */}
                            {DataStorage.typeOfUse == 1 && orderList.length > 0 && (
                                <View style={{ padding: moderateScale(10), backgroundColor: '#f0f0f0', borderTopWidth: 1, borderTopColor: '#DCDDDF' }}>
                                    <Text style={{ color: Colors.text, fontSize: moderateScale(12), textAlign: 'center' }}>
                                        Page {currentPage} of {totalPages} | Showing {orderList.length} orders
                                    </Text>
                                </View>
                            )}
                        </View>
                    </View>
                </View>
                <View style={{ height: moderateScale(10) }} />
            </View>

            {isDateTimePicker && (
                <View style={{ width: '100%', height: '100%', position: 'absolute', alignItems: 'center', justifyContent: 'center', backgroundColor: '#00000060' }}>
                    <View style={{ backgroundColor: '#FFF', padding: 10, borderRadius: 10 }}>
                        <DateTimePicker
                            value={datePickerType === 'start' ? startDateForPicker : endDateForPicker}
                            mode="date"
                            display="default"
                            onChange={onChange}
                            maximumDate={new Date()}
                        />
                    </View>
                </View>
            )}
            {loading && <Loader />}
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    );
};

export default TrackOrderScreen;