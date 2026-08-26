import React, { useEffect, useRef, useState } from 'react'
import { Alert, Animated, FlatList, Image, Keyboard, Platform, StyleSheet, Text, TextInput, ToastAndroid, TouchableOpacity, TouchableWithoutFeedback, View, StatusBar } from 'react-native'
import { Colors } from '../../../assets/Colors'
import { Icons } from '../../../assets/Icons'
import { moderateScale } from '../../../helper/Window'
import SafeView from '../../../helper/SafeView'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import Loader from '../../../common/Loader'
import SelectSubDealerCheckBox from '../../../common/SelectSubDealerCheckBox'
import { getMySubDealerList } from '../../../storage/database/GetDataFromTable'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

// ─── Constants ───────────────────────────────────────────────────────────────
const PRIMARY = '#E41B14'
const PRIMARY_LIGHT = '#FFF0EF'
const CARD_BG = '#FFFFFF'
const PAGE_BG = '#F6F7FB'
const TEXT_DARK = '#1A1A2E'
const TEXT_MID = '#6B7280'
const TEXT_SOFT = '#9CA3AF'
const BORDER = '#E5E7EB'
const SUCCESS = '#10B981'
const SUCCESS_BG = '#ECFDF5'

// ─── Cross-platform toast ─────────────────────────────────────────────────────
const showToast = (message) => {
    if (Platform.OS === 'android') ToastAndroid.show(message, ToastAndroid.SHORT)
    else Alert.alert('Notice', message)
}

// ─── Helper: initial avatar color ─────────────────────────────────────────────
const AVATAR_COLORS = ['#6366F1', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899']
const avatarColor = (name = '') => AVATAR_COLORS[name.charCodeAt(0) % AVATAR_COLORS.length]

// ═══════════════════════════════════════════════════════════════════════════════
// AllocationBottomSheet
// ═══════════════════════════════════════════════════════════════════════════════
const AllocationBottomSheet = ({ visible, onClose, subDealers = [], assignedItem, assignedInvoiceItem, available_allocation_qty, onConfirm, isSubmitting }) => {
    const translateY = useRef(new Animated.Value(600)).current
    const backdropOpacity = useRef(new Animated.Value(0)).current
    const keyboardOffset = useRef(new Animated.Value(0)).current
    const [activeIndex, setActiveIndex] = useState(0)
    const [quantities, setQuantities] = useState({})

    const allocationQty = Number(available_allocation_qty) || 0
    const invoiceQty = Number(assignedInvoiceItem?.invqty) || 0
    const productName = assignedItem?.prod_display_name || ''

    const getDealerKey = (d) => d?.customer_code ?? d?.id ?? d?.customer_name ?? ''

    const currentDealer = subDealers[activeIndex]
    const currentDealerKey = currentDealer ? getDealerKey(currentDealer) : ''
    const currentQtyValue = quantities[currentDealerKey] ?? ''

    const totalAllocated = Object.values(quantities).reduce((s, v) => s + (parseFloat(v) || 0), 0)
    const remainingQty = allocationQty - totalAllocated
    const pct = allocationQty > 0 ? Math.min(totalAllocated / allocationQty, 1) : 0

    const othersTotal = (key) => Object.entries(quantities).reduce((s, [k, v]) => k === key ? s : s + (parseFloat(v) || 0), 0)

    const isCurrentOverLimit = (() => {
        const val = parseFloat(currentQtyValue) || 0
        return val > 0 && (othersTotal(currentDealerKey) + val) > allocationQty
    })()

    const isDealerOverLimit = (key) => {
        const val = parseFloat(quantities[key]) || 0
        return val > 0 && (othersTotal(key) + val) > allocationQty
    }

    useEffect(() => {
        if (visible) {
            setActiveIndex(0); setQuantities({})
            Animated.parallel([
                Animated.spring(translateY, { toValue: 0, useNativeDriver: true, tension: 65, friction: 11 }),
                Animated.timing(backdropOpacity, { toValue: 1, duration: 300, useNativeDriver: true }),
            ]).start()
        } else {
            keyboardOffset.setValue(0)
            Animated.parallel([
                Animated.timing(translateY, { toValue: 600, duration: 260, useNativeDriver: true }),
                Animated.timing(backdropOpacity, { toValue: 0, duration: 260, useNativeDriver: true }),
            ]).start()
        }
    }, [visible])

    useEffect(() => {
        const showEv = Platform.OS === 'android' ? 'keyboardDidShow' : 'keyboardWillShow'
        const hideEv = Platform.OS === 'android' ? 'keyboardDidHide' : 'keyboardWillHide'
        const onShow = (e) => Animated.timing(keyboardOffset, { toValue: 0, duration: Platform.OS === 'android' ? 160 : 250, useNativeDriver: true }).start()
        const onHide = () => Animated.timing(keyboardOffset, { toValue: 0, duration: Platform.OS === 'android' ? 160 : 250, useNativeDriver: true }).start()
        const s = Keyboard.addListener(showEv, onShow)
        const h = Keyboard.addListener(hideEv, onHide)
        return () => { s.remove(); h.remove() }
    }, [])

    const handleAllocate = () => {

        for (var i = 0; i < subDealers.length; i++) {
            const key = getDealerKey(subDealers[i])
            const qty = parseFloat(quantities[key]) || 0
            if (qty <= 0) {
                var message = 'Please enter a valid quantity greater than 0'
                if (subDealers.length > 1) {
                    message = message + ' for every sub-dealer.'
                } else {
                    message = message + '.'
                }
                showToast(message);
                return
            }
            if (isCurrentOverLimit) {
                showToast(`Maximum allowed: ${Math.max(0, allocationQty - othersTotal(currentDealerKey))} MT.`)
                return
            }
        }

        Keyboard.dismiss()
        onConfirm(assignedItem, quantities)
    }

    const handleClose = () => { Keyboard.dismiss(); onClose() }

    return (
        <View style={[StyleSheet.absoluteFillObject, { pointerEvents: visible ? 'auto' : 'none' }]}>
            <TouchableWithoutFeedback onPress={handleClose}>
                <Animated.View style={[bsStyles.backdrop, { opacity: backdropOpacity }]} />
            </TouchableWithoutFeedback>

            <Animated.View style={[bsStyles.sheet, { transform: [{ translateY: Animated.add(translateY, keyboardOffset) }] }]}>

                {/* Handle */}
                <View style={bsStyles.handle} />

                {/* Title row */}
                <View style={bsStyles.titleRow}>
                    <Text style={bsStyles.sheetTitle}>Allocate Stock</Text>
                    <TouchableOpacity onPress={handleClose} style={bsStyles.closeBtn}>
                        <Text style={bsStyles.closeBtnText}>✕</Text>
                    </TouchableOpacity>
                </View>

                {/* Product pill */}
                <View style={bsStyles.productPill}>
                    <View style={bsStyles.productDot} />
                    <Text style={bsStyles.productPillText} numberOfLines={1}>{productName}</Text>
                </View>

                {/* Sub-dealer tabs */}
                <FlatList
                    horizontal
                    data={subDealers}
                    showsHorizontalScrollIndicator={false}
                    keyExtractor={(item, i) => getDealerKey(item) || String(i)}
                    style={{ marginBottom: moderateScale(16) }}
                    renderItem={({ item, index }) => {
                        const key = getDealerKey(item)
                        const isActive = index === activeIndex
                        const hasError = isDealerOverLimit(key)
                        return (
                            <TouchableOpacity style={[bsStyles.tab, isActive && bsStyles.tabActive, hasError && bsStyles.tabError]} onPress={() => { Keyboard.dismiss(); setActiveIndex(index) }} >
                                <View style={[bsStyles.tabAvatar, { backgroundColor: avatarColor(item?.customer_name || '') }]}>
                                    <Text style={bsStyles.tabAvatarText}>
                                        {(item?.customer_name || '?').charAt(0).toUpperCase()}
                                    </Text>
                                </View>
                                <Text style={[bsStyles.tabText, isActive && bsStyles.tabTextActive, hasError && bsStyles.tabTextError]} numberOfLines={1}>
                                    {item?.customer_name || `Dealer ${index + 1}`}
                                </Text>
                                {hasError && <View style={bsStyles.tabErrorDot} />}
                            </TouchableOpacity>
                        )
                    }}
                />

                {/* Allocation progress */}
                <View style={bsStyles.progressCard}>
                    <View style={bsStyles.progressRow}>
                        <View style={bsStyles.progressStat}>
                            <Text style={bsStyles.progressStatVal}>{allocationQty}</Text>
                            <Text style={bsStyles.progressStatLbl}>Available MT</Text>
                        </View>
                        <View style={bsStyles.progressBarWrap}>
                            <View style={bsStyles.progressBarBg}>
                                <Animated.View style={[bsStyles.progressBarFill, { width: `${pct * 100}%`, backgroundColor: totalAllocated > allocationQty ? PRIMARY : SUCCESS }]} />
                            </View>
                            <Text style={bsStyles.progressPct}>{Math.round(pct * 100)}% used</Text>
                        </View>
                        <View style={[bsStyles.progressStat, { alignItems: 'flex-end' }]}>
                            <Text style={[bsStyles.progressStatVal, remainingQty < 0 && { color: PRIMARY }]}>
                                {remainingQty < 0 ? 0 : remainingQty}
                            </Text>
                            <Text style={bsStyles.progressStatLbl}>Remaining MT</Text>
                        </View>
                    </View>
                </View>

                {/* Qty input */}
                <View style={bsStyles.inputCard}>
                    <View style={bsStyles.inputLabelRow}>
                        <Text style={bsStyles.inputLabel}>Enter Quantity for</Text>
                        <Text style={bsStyles.inputDealerName} numberOfLines={1}>
                            {currentDealer?.customer_name}
                        </Text>
                    </View>
                    <View style={[bsStyles.inputRow, isCurrentOverLimit && bsStyles.inputRowError]}>
                        <TextInput
                            style={bsStyles.input}
                            keyboardType="numeric"
                            value={currentQtyValue}
                            onChangeText={(val) => setQuantities(prev => ({ ...prev, [currentDealerKey]: val.replace(/[^0-9.]/g, '') }))}
                            placeholder="0.00"
                            placeholderTextColor={TEXT_SOFT}
                        />
                        <View style={bsStyles.unitBadge}>
                            <Text style={bsStyles.unitText}>MT</Text>
                        </View>
                    </View>
                    {isCurrentOverLimit && (
                        <Text style={bsStyles.errorHint}>
                            ⚠ Max allowed: {Math.max(0, allocationQty - othersTotal(currentDealerKey))} MT
                        </Text>
                    )}
                    <Text style={bsStyles.invoiceHint}>Invoice Qty: {invoiceQty} MT</Text>
                </View>

                {/* Buttons */}
                <View style={bsStyles.btnRow}>
                    <TouchableOpacity style={bsStyles.cancelBtn} onPress={handleClose}>
                        <Text style={bsStyles.cancelBtnText}>Cancel</Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                        style={[bsStyles.allocateBtn, (isCurrentOverLimit || isSubmitting) && bsStyles.allocateBtnDisabled]}
                        onPress={handleAllocate}
                        activeOpacity={(isCurrentOverLimit || isSubmitting) ? 1 : 0.85}
                        disabled={isSubmitting}
                    >
                        <Text style={bsStyles.allocateBtnText}>
                            {isSubmitting ? 'Confirming...' : 'Confirm Allocation'}
                        </Text>
                    </TouchableOpacity>
                </View>
            </Animated.View>
        </View>
    )
}

// ═══════════════════════════════════════════════════════════════════════════════
// Main Screen
// ═══════════════════════════════════════════════════════════════════════════════
const AssignedScreen = (props) => {

    const [orderInfo, setOrderInfo] = useState({});
    const [invoiceInfo, setInvoiceInfo] = useState({});
    const [available_allocation_qty, set_available_allocation_qty] = useState(0);
    const [assigned, setAssigned] = useState(false)
    const [assignedFilterOpen, setAssignedFilterOpen] = useState(false)
    const [loading, setLoading] = useState(true)
    const [searchText, setSearchText] = useState('')
    const [assignedList, setAssignedList] = useState([])
    const [subDealerData, setSubDealerData] = useState([])
    const [selectedDate, setSelectedDate] = useState(`${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(2, '0')}`)
    const [allocationData, setAllocationData] = useState()
    const [subDealerSheetVisible, setSubDealerSheetVisible] = useState(false)
    const [selectedSubDealers, setSelectedSubDealers] = useState([])
    const [allocationSheetVisible, setAllocationSheetVisible] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)
    const [selectedAssignedItem, setSelectedAssignedItem] = useState(null)
    const [isSubmitting, setIsSubmitting] = useState(false)

    useEffect(() => {
        !assigned ? getAssignedHistoryForCement() : getAllocatedHistoryForCement()
    }, [assigned, selectedDate])

    useEffect(() => { requestForCementShippingToSubDealer() }, [])
    const fetchAuthData = async () => {
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
        }
    }

    // const getAssignedHistoryForCement = async () => {
    //     setLoading(true)
    //     var a = await AuthCheckingApi();
    //     if (!a) {
    //         setAuthChecker(true)
    //         setLoading(false)
    //         return false
    //     }
    //     try {
    //         const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.LiftingURL.dispatched_order_data_list_url + '?month_year=' + selectedDate + '&customer_code=' + (UrlStorage.ParameterList.BasicData.user_type == 'broker' ?UrlStorage.ParameterList.BasicData.selectedCustomerCode:  UrlStorage.ParameterList.BasicData.emp_id)
    //         console.log(url);

    //         const r = await fetch(url, { method: 'GET' })
    //         const result = await r.json()
    //         setAssignedList( result.process_status === 'YES' ? result.dispatched_order_data.map(i => { const allInvoices = i.dispatched_invoice_data?.flat() || []; const canAllocate = allInvoices.length > 0 && allInvoices.every(item => item.allocation_complete === 'YES'); return { ...i, isOpen: false, canAllocate: canAllocate, }; }) : [] );

    //         //setAssignedList(result.process_status === 'YES' ? result.dispatched_order_data.map(i => ({ ...i, isOpen: false })) : [])
    //     } catch (e) { }
    //     setLoading(false)
    // }
    const getAssignedHistoryForCement = async () => {
        setLoading(true);

        var a = await AuthCheckingApi();

        if (!a) {
            setAuthChecker(true);
            setLoading(false);
            return false;
        }

        try {
            var a = ''
            if (UrlStorage.ParameterList.BasicData.user_type.toLocaleLowerCase() == 'RSSD'.toLocaleLowerCase()) {
                console.log('RSSD');
                a = UrlStorage.NonAuthURL.Saathi.LiftingURL.dispatched_order_data_list_RSSD_url
            } else {
                console.log('not RSSD. ' + UrlStorage.ParameterList.BasicData.user_type);
                a = UrlStorage.NonAuthURL.Saathi.LiftingURL.dispatched_order_data_list_url
            }
            var url =
                UrlStorage.BaseUrlList.Saathi.base_url_saathi +
                a +
                '?month_year=' +
                selectedDate +
                '&customer_code=' +
                (
                    UrlStorage.ParameterList.BasicData.user_type == 'broker'
                        ? UrlStorage.ParameterList.BasicData.selectedCustomerCode
                        : UrlStorage.ParameterList.BasicData.emp_id
                );

            console.log(url);

            const r = await fetch(url, { method: 'GET' });
            const result = await r.json();

            if (result.process_status === 'YES') {

                const rssdAllocationDays = parseInt(
                    result.rssd_allocation_days,
                    10
                ) || 0;

                // Today - use only date, not time
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                // For 11 days, valid dates are today and previous 10 days.
                // Example:
                // Today = 11-Aug
                // Valid from = 01-Aug
                const allocationStartDate = new Date(today);
                allocationStartDate.setDate(
                    allocationStartDate.getDate() - (rssdAllocationDays - 1)
                );

                const assignedData = result.dispatched_order_data.map(i => {

                    const allInvoices =
                        i.dispatched_invoice_data?.flat() || [];

                    // Existing allocation check
                    const canAllocate =
                        allInvoices.length > 0 &&
                        allInvoices.every(
                            item => item.allocation_complete === 'YES'
                        );

                    // New allocation date check
                    const canAllocate2 =
                        allInvoices.length > 0 &&
                        allInvoices.every(item => {

                            if (!item.invdt) {
                                return false;
                            }

                            // invdt format: YYYY-MM-DD
                            const [year, month, day] =
                                item.invdt.split('-').map(Number);

                            const invoiceDate = new Date(
                                year,
                                month - 1,
                                day
                            );

                            invoiceDate.setHours(0, 0, 0, 0);

                            return (
                                invoiceDate >= allocationStartDate &&
                                invoiceDate <= today
                            );
                        });

                    return {
                        ...i,
                        isOpen: false,
                        canAllocate: canAllocate,
                        canAllocate2: canAllocate2,
                    };
                });

                setAssignedList(assignedData);

            } else {
                setAssignedList([]);
            }

        } catch (e) {
            console.log('getAssignedHistoryForCement error:', e);
            setAssignedList([]);
        }

        setLoading(false);
    };

    const getAllocatedHistoryForCement = async () => {
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.LiftingURL.allocation_data_invoice_url + '?year_month=' + selectedDate + '&customer_id=' + UrlStorage.ParameterList.BasicData.emp_id + '&user_type=' + UrlStorage.ParameterList.BasicData.user_type
            console.log(url);

            const r = await fetch(url)
            const result = await r.json()
            if (result.process_status === 'YES') {
                setAllocationData(result.allocation_data)
                const unique = [...new Set(result.allocation_data.map(i => i.counter_name))]
                setAssignedList(unique.map(name => ({ name, isOpen: false })))
            } else setAssignedList([])
        } catch (e) { }
        setLoading(false)
    }

    const requestForCementShippingToSubDealer = async () => {
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {

            //const url = `${UrlStorage.BaseUrlList.Saathi.base_url_saathi}${UrlStorage.NonAuthURL.Saathi.OrderURL1.dealer_data_list_url}?emp_code=${UrlStorage.ParameterList.BasicData.user_type != 'broker' ? UrlStorage.ParameterList.BasicData.selectedCustomerCode : UrlStorage.ParameterList.BasicData.customerDetails.customer_code}&user_type=sub dealer&login_type=${UrlStorage.ParameterList.BasicData.user_type}`

            // const result = await (await fetch(url)).json()
            const local = await getMySubDealerList(UrlStorage.ParameterList.BasicData.user_type, UrlStorage.ParameterList.BasicData.user_type == 'broker' ? UrlStorage.ParameterList.BasicData.customerDetails.customer_code : UrlStorage.ParameterList.BasicData.selectedCustomerCode, true)

            setSubDealerData(local)

            // if (result.process_status === 'YES' && result.sub_dealer_data?.length > 0) {
            //     setSubDealerData([...result.sub_dealer_data].sort((a, b) => a.customer_name.trim().toLowerCase().localeCompare(b.customer_name.trim().toLowerCase())))
            // } else {

            // }
        } catch (e) {
            try {
                const local = await getMySubDealerList(UrlStorage.ParameterList.BasicData.user_type, UrlStorage.ParameterList.BasicData.user_type == 'broker' ? UrlStorage.ParameterList.BasicData.customerDetails.customer_code : UrlStorage.ParameterList.BasicData.selectedCustomerCode)
                setSubDealerData(local)
            } catch (dbErr) { setSubDealerData([]) }
        }
        setLoading(false)
    }

    const getDealerKey = (d) => d?.customer_code ?? d?.id ?? d?.customer_name ?? ''

    const confirmOrder = async (orderDetails, quantities) => {
        if (isSubmitting) return
        setIsSubmitting(true)
        try {
            var a = await AuthCheckingApi();
            if (!a) {
                setAuthChecker(true)
                setLoading(false)
                return false
            }
            const fd = new FormData()
            for (var i = 0; i < selectedSubDealers.length; i++) {
                const key = getDealerKey(selectedSubDealers[i])
                const qty = parseFloat(quantities[key]) || 0
                fd.append('allocation_data[' + i + '][APPORDERNO]', invoiceInfo.apporderno)
                fd.append('allocation_data[' + i + '][order_id]', orderDetails.order_id)
                fd.append('allocation_data[' + i + '][inv_no]', invoiceInfo.invno)
                fd.append('allocation_data[' + i + '][prod_desc]', invoiceInfo.prod_display_name)
                fd.append('allocation_data[' + i + '][qty]', String(qty))
                fd.append('allocation_data[' + i + '][inv_qty]', invoiceInfo.invqty)
                fd.append('allocation_data[' + i + '][inv_date]', invoiceInfo.invdt)
                fd.append('allocation_data[' + i + '][sub_dealer_id]', selectedSubDealers[i].SAP_code)
                fd.append('allocation_data[' + i + '][customer_id]', UrlStorage.ParameterList.BasicData.user_type.toLowerCase() === 'broker' ? UrlStorage.ParameterList.BasicData.customerDetails.SAP_code : UrlStorage.ParameterList.BasicData.emp_id)
            }

            const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.LiftingURL.lifting_approve_url
            console.log(url)
            console.log('fd', fd)

            const result = await (await fetch(url, { method: 'POST', body: fd })).json()

            if (result?.process_status?.toLowerCase() === 'yes') {
                setAllocationSheetVisible(false)
                Alert.alert('Success', 'Allocation confirmed successfully.')
                setAssignedList([])
                getAssignedHistoryForCement()
            } else Alert.alert('Error', result?.process_message || 'Unknown error')
        } catch (e) { Alert.alert('Error', 'Please contact admin.') }
        finally {
            setIsSubmitting(false)
        }
    }

    const requestForOldSetLiftingData = async () => {

        try {
            var a = await AuthCheckingApi();
            if (!a) {
                setAuthChecker(true)
                setLoading(false)
                return false
            }
            const fd = new FormData()
            fd.append('invoice_no', invoiceInfo.invno)
            fd.append('app_orderno', orderInfo.order_id)
            fd.append('customer_code', UrlStorage.ParameterList.BasicData.user_type.toLowerCase() === 'broker' ? UrlStorage.ParameterList.BasicData.customerDetails.customer_code : UrlStorage.ParameterList.BasicData.emp_code)
            const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.LiftingURL.download_invoice_url

            const result = await (await fetch(url, { method: 'POST', body: fd })).json()

            if (result?.process_status?.toLowerCase() === 'yes') {
                setLoading(false)
                set_available_allocation_qty(result.dispatched_invoice_data[0].available_allocation_qty);
                setAllocationSheetVisible(true)
            } else Alert.alert('Error', result?.process_message || 'Unknown error')
        } catch (e) { Alert.alert('Error', 'Please contact admin.') }
    }

    const getAllocationsByName = (name) => allocationData?.filter(i => i?.counter_name?.toLowerCase() === name?.toLowerCase())

    const DetailsOpenHandler = (index) => setAssignedList(prev => prev.map((item, idx) => ({ ...item, isOpen: idx === index ? !item.isOpen : false })))

    const handleAllocatePress = (item, sub) => {
        setSelectedAssignedItem(item)
        setSelectedSubDealers([])
        setSubDealerSheetVisible(true)
        setOrderInfo(item)
        setInvoiceInfo(sub)
    }

    const handleSubDealerSelected = (dealerOrArray) => {
        const dealers = Array.isArray(dealerOrArray) ? dealerOrArray : [dealerOrArray]
        setSelectedSubDealers(dealers)
        setSubDealerSheetVisible(false)
        setLoading(true)
        requestForOldSetLiftingData()
    }

    function formatMonthYear(input) {
        const [year, month] = input.split('-')
        const names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        return `${names[parseInt(month, 10) - 1]} ${year}`
    }

    // ── Month-Year Picker ─────────────────────────────────────────────────────
    const MonthYearPicker = ({ visible, onClose, onConfirm, currentSelectedDate }) => {
        const today = new Date()
        const parseDate = (s) => {
            if (!s) return { month: today.getMonth() + 1, year: today.getFullYear() }
            const [y, m] = s.split('-')
            return { month: parseInt(m, 10), year: parseInt(y, 10) }
        }
        const parsed = parseDate(currentSelectedDate)
        const [selMonth, setSelMonth] = useState(parsed.month)
        const [selYear, setSelYear] = useState(parsed.year)
        const monthRef = useRef(null)
        const yearRef = useRef(null)
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'].map((l, i) => ({ label: l, value: i + 1 }))
        const years = Array.from({ length: today.getFullYear() - 2000 + 1 }, (_, i) => 2000 + i).reverse()

        if (!visible) return null
        return (
            <View style={[StyleSheet.absoluteFillObject, { zIndex: 999 }]}>
                <TouchableWithoutFeedback onPress={onClose}>
                    <View style={{ ...StyleSheet.absoluteFillObject, backgroundColor: 'rgba(0,0,0,0.5)' }} />
                </TouchableWithoutFeedback>
                <View style={pickerStyles.sheet}>
                    <View style={pickerStyles.handle} />
                    <Text style={pickerStyles.title}>Select Period</Text>
                    <View style={pickerStyles.pickerRow}>
                        <FlatList
                            ref={monthRef}
                            data={months}
                            keyExtractor={i => i.value.toString()}
                            showsVerticalScrollIndicator={false}
                            style={{ flex: 1 }}
                            contentContainerStyle={{ paddingVertical: 8 }}
                            getItemLayout={(_, index) => ({ length: 52, offset: 52 * index, index })}
                            onLayout={() => {
                                const idx = selMonth - 1
                                setTimeout(() => monthRef.current?.scrollToIndex({ index: idx, animated: false }), 50)
                            }}
                            renderItem={({ item }) => (
                                <TouchableOpacity style={[pickerStyles.option, selMonth === item.value && pickerStyles.optionActive]} onPress={() => setSelMonth(item.value)} >
                                    <Text style={[pickerStyles.optionText, selMonth === item.value && pickerStyles.optionTextActive]}>
                                        {item.label}
                                    </Text>
                                </TouchableOpacity>
                            )}
                        />
                        <View style={pickerStyles.divider} />
                        <FlatList
                            ref={yearRef}
                            data={years}
                            keyExtractor={i => i.toString()}
                            showsVerticalScrollIndicator={false}
                            style={{ flex: 1 }}
                            contentContainerStyle={{ paddingVertical: 8 }}
                            getItemLayout={(_, index) => ({ length: 52, offset: 52 * index, index })}
                            onLayout={() => {
                                const idx = years.indexOf(selYear)
                                if (idx !== -1) setTimeout(() => yearRef.current?.scrollToIndex({ index: idx, animated: false }), 50)
                            }}
                            renderItem={({ item }) => (
                                <TouchableOpacity style={[pickerStyles.option, selYear === item && pickerStyles.optionActive]} onPress={() => setSelYear(item)} >
                                    <Text style={[pickerStyles.optionText, selYear === item && pickerStyles.optionTextActive]}>
                                        {item}
                                    </Text>
                                </TouchableOpacity>
                            )}
                        />
                    </View>
                    <View style={pickerStyles.btnRow}>
                        <TouchableOpacity style={pickerStyles.cancelBtn} onPress={onClose}>
                            <Text style={pickerStyles.cancelText}>Cancel</Text>
                        </TouchableOpacity>
                        <TouchableOpacity style={pickerStyles.confirmBtn} onPress={() => onConfirm(`${selYear}-${String(selMonth).padStart(2, '0')}`)} >
                            <Text style={pickerStyles.confirmText}>Apply</Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </View>
        )
    }

    // ── Render ────────────────────────────────────────────────────────────────
    const filteredList = assignedList.filter(item => {
        const term = searchText.toLowerCase()
        if (!term) return true
        return (item?.order_id || item?.name || '').toLowerCase().includes(term) || (item?.prod_display_name || '').toLowerCase().includes(term)
    })

    return (
        <SafeView backgroundColor={PAGE_BG} bar={false} statusbarColor={PRIMARY}>
            <StatusBar barStyle="light-content" backgroundColor={PRIMARY} />

            <View style={{ flex: 1, backgroundColor: PAGE_BG }}>

                {/* ── Header ── */}
                <View style={s.header}>
                    <TouchableOpacity onPress={() => props.navigation.pop()} style={s.backBtn}>
                        <Image source={Icons.Back} style={s.backIcon} />
                    </TouchableOpacity>
                    <View style={{ flex: 1, alignItems: 'center' }}>
                        <Text style={s.headerTitle}>Assigned</Text>
                        <Text style={s.headerSub}>{formatMonthYear(selectedDate)}</Text>
                    </View>
                    <TouchableOpacity onPress={() => setAssignedFilterOpen(true)} style={s.filterBtn}>
                        <Image source={Icons.Filter} style={s.filterIcon} />
                    </TouchableOpacity>
                </View>

                {/* ── Tabs ── */}
                <View style={s.tabBar}>
                    {['Assigned', 'Allocated'].map((label, i) => {
                        const active = (i === 1) === assigned
                        return (
                            <TouchableOpacity key={label} style={[s.tabItem, active && s.tabItemActive]} onPress={() => setAssigned(i === 1)}>
                                <Text style={[s.tabLabel, active && s.tabLabelActive]}>{label}</Text>
                                {active && <View style={s.tabIndicator} />}
                            </TouchableOpacity>
                        )
                    })}
                </View>

                {/* ── Search ── */}
                <View style={s.searchWrap}>
                    <Image source={Icons.Search} style={s.searchIcon} />
                    <TextInput
                        style={s.searchInput}
                        placeholder={assigned ? 'Search counter...' : 'Search order or product...'}
                        placeholderTextColor={TEXT_SOFT}
                        value={searchText}
                        onChangeText={setSearchText}
                    />
                    {searchText.length > 0 && (
                        <TouchableOpacity onPress={() => setSearchText('')} style={s.searchClear}>
                            <Text style={{ color: TEXT_MID, fontSize: 14 }}>✕</Text>
                        </TouchableOpacity>
                    )}
                </View>

                {/* ── List ── */}
                {filteredList.length === 0 && !loading ? (
                    <View style={s.emptyState}>
                        <Text style={s.emptyIcon}>📦</Text>
                        <Text style={s.emptyTitle}>No Data Found</Text>
                        <Text style={s.emptySub}>Try adjusting the month or search term</Text>
                    </View>
                ) : (
                    <FlatList
                        data={filteredList}
                        keyExtractor={(_, i) => i.toString()}
                        showsVerticalScrollIndicator={false}
                        contentContainerStyle={{ padding: moderateScale(14), paddingBottom: 40 }}
                        renderItem={({ item, index }) => {
                            if (!assigned) {
                                // ── Assigned card ──
                                const isOpen = item.isOpen
                                return (
                                    <View style={s.card}>
                                        <TouchableOpacity activeOpacity={0.9} onPress={() => DetailsOpenHandler(index)}>
                                            <View style={s.cardInner}>
                                                <View style={s.cardLeft}>
                                                    <View style={s.orderIdRow}>
                                                        <Text style={[s.orderId, { color: item.canAllocate ? '#10B981' : '#1A1A2E', }]}>{item?.order_id}</Text>
                                                        <View style={s.statusBadge}>
                                                            <View style={s.statusDot} />
                                                            <Text style={s.statusText}>{item?.STATUS || 'Active'}</Text>
                                                        </View>
                                                    </View>
                                                    <Text style={s.productTitle} numberOfLines={1}>{item?.prod_display_name}</Text>
                                                    <View style={s.cardMetaRow}>
                                                        <Text style={s.metaChip}>{item?.order_date}</Text>
                                                        {/* //<Text style={s.metaChip}>{item?.freight}</Text> */}
                                                    </View>
                                                    <View style={s.cardMetaRow}>
                                                        {/* <Text style={s.metaChip}>{item?.order_date}</Text> */}
                                                        <Text style={s.metaChip}>{item?.freight}</Text>
                                                    </View>
                                                    <View style={s.cardFooterRow}>
                                                        <Text style={s.destText} numberOfLines={1}>{item?.destination_name}</Text>
                                                        <View style={s.qtyBadge}>
                                                            <Text style={s.qtyBadgeText}>{item?.qty} MT</Text>
                                                        </View>
                                                    </View>
                                                </View>
                                                <View style={[s.chevronBtn, isOpen && s.chevronBtnActive]}>
                                                    <Image
                                                        source={isOpen ? Icons.UpArrow : Icons.DownArrow}
                                                        style={[s.chevronIcon, { tintColor: isOpen ? '#fff' : PRIMARY }]}
                                                    />
                                                </View>
                                            </View>
                                        </TouchableOpacity>

                                        {isOpen && item?.dispatched_invoice_data?.length > 0 && (
                                            <View style={s.invoiceSection}>
                                                <Text style={s.invoiceSectionTitle}>Invoice Details</Text>
                                                {item.dispatched_invoice_data.map((sub, i) => (
                                                    <View key={i} style={s.invoiceCard}>
                                                        <View style={s.invoiceTopRow}>
                                                            <View>
                                                                <Text style={s.invoiceLabel}>Invoice No</Text>
                                                                <Text style={{ fontSize: moderateScale(13), color: sub.allocation_complete == 'NO' ? '#1A1A2E' : '#10B981', fontWeight: '600' }}>{sub.invno}</Text>
                                                            </View>
                                                            <View style={{ alignItems: 'flex-end' }}>
                                                                <Text style={s.invoiceLabel}>Date</Text>
                                                                <Text style={s.invoiceValue}>{sub.invdt}</Text>
                                                            </View>
                                                        </View>
                                                        <View style={s.invoiceBottomRow}>
                                                            <View style={s.invoiceQtyWrap}>
                                                                <Text style={s.invoiceQtyLabel}>Qty</Text>
                                                                <Text style={s.invoiceQtyVal}>{sub.invqty} MT</Text>
                                                            </View>
                                                            {item.canAllocate2 && <TouchableOpacity style={s.allocateBtn} onPress={() => handleAllocatePress(item, sub)}>
                                                                <Text style={s.allocateBtnText}>Allocate →</Text>
                                                            </TouchableOpacity>}
                                                        </View>
                                                    </View>
                                                ))}
                                            </View>
                                        )}
                                    </View>
                                )
                            } else {
                                const isOpen = item.isOpen
                                return (
                                    <TouchableOpacity activeOpacity={0.95} style={{ paddingTop: moderateScale(10), paddingHorizontal: moderateScale(10) }} onPress={() => DetailsOpenHandler(index)}>
                                        <View style={{ width: "100%", overflow: "hidden", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), backgroundColor: "#FFFFFF", elevation: 10, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: 0, y: 4 } }}>
                                            <View style={{ alignContent: 'center', flexDirection: 'row', flex: 1, paddingVertical: 10 }}>
                                                <Text style={{ color: Colors.text, paddingHorizontal: 10 }}>{item?.name}</Text>
                                                {isOpen ? (
                                                    <View style={{ width: moderateScale(20), height: moderateScale(20), borderWidth: moderateScale(1), backgroundColor: DataStorage.primaryColorCode, borderColor: DataStorage.primaryColorCode, alignItems: "center", justifyContent: "center", position: 'absolute', end: 10, top: 10 }}>
                                                        <Image source={Icons.UpArrow} style={{ width: moderateScale(10), height: moderateScale(10), tintColor: "#FFFFFF" }} />
                                                    </View>
                                                ) : (
                                                    <View style={{ width: moderateScale(20), height: moderateScale(20), borderWidth: moderateScale(1), borderColor: DataStorage.primaryColorCode, alignItems: "center", justifyContent: "center", position: 'absolute', end: 10, top: 10 }}>
                                                        <Image source={Icons.DownArrow} style={{ width: moderateScale(10), height: moderateScale(10), tintColor: DataStorage.primaryColorCode }} />
                                                    </View>
                                                )}
                                            </View>
                                            {isOpen && (
                                                <FlatList
                                                    data={getAllocationsByName(item?.name)}
                                                    keyExtractor={(_, i) => i.toString()}
                                                    renderItem={({ item: subItem }) => (
                                                        <View style={{ padding: moderateScale(10), marginHorizontal: moderateScale(5), gap: moderateScale(10), backgroundColor: DataStorage.transColorCode, borderRadius: moderateScale(8), marginVertical: moderateScale(5) }}>
                                                            <View style={{ flexDirection: "row", alignItems: "center" }}>
                                                                <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Product:</Text>
                                                                <Text numberOfLines={2} style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500", width: moderateScale(150) }}>{subItem?.prod_desc}</Text>
                                                            </View>
                                                            <View style={{ flexDirection: "row", alignItems: "center" }}>
                                                                <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Allocation Qty:</Text>
                                                                <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{subItem?.allocation_qty}</Text>
                                                            </View>
                                                            <View style={{ flexDirection: "row", alignItems: "center" }}>
                                                                <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Trans Date:</Text>
                                                                <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{subItem?.date_and_time}</Text>
                                                            </View>
                                                            <View style={{ flexDirection: "row", alignItems: "center" }}>
                                                                <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Counter Name:</Text>
                                                                <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{subItem?.counter_name}</Text>
                                                            </View>
                                                            <View style={{ flexDirection: "row", alignItems: "center" }}>
                                                                <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Invoice No:</Text>
                                                                <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{subItem?.inv_no}</Text>
                                                            </View>
                                                            <View style={{ flexDirection: "row", alignItems: "center" }}>
                                                                <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Invoice Date:</Text>
                                                                <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{subItem?.inv_date}</Text>
                                                            </View>
                                                        </View>
                                                    )}
                                                />
                                            )}
                                        </View>
                                    </TouchableOpacity>
                                )
                            }
                        }}
                    />
                )}
            </View>

            <SelectSubDealerCheckBox
                isVisible={subDealerSheetVisible}
                dataList={subDealerData}
                closePopup={() => setSubDealerSheetVisible(false)}
                selectItem={handleSubDealerSelected}
                isDealer={false}
            />

            <AllocationBottomSheet
                visible={allocationSheetVisible}
                onClose={() => setAllocationSheetVisible(false)}
                isSubmitting={isSubmitting}
                subDealers={selectedSubDealers}
                assignedItem={selectedAssignedItem}
                assignedInvoiceItem={invoiceInfo}
                available_allocation_qty={available_allocation_qty}
                onConfirm={confirmOrder}
            />

            {assignedFilterOpen && (
                <MonthYearPicker
                    visible={assignedFilterOpen}
                    currentSelectedDate={selectedDate}
                    onClose={() => setAssignedFilterOpen(false)}
                    onConfirm={(val) => { setAssignedFilterOpen(false); setSelectedDate(val) }}
                />
            )}

            {loading && <Loader />}
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

export default AssignedScreen

// ═══════════════════════════════════════════════════════════════════════════════
// Styles
// ═══════════════════════════════════════════════════════════════════════════════
const s = StyleSheet.create({
    header: { backgroundColor: PRIMARY, flexDirection: 'row', alignItems: 'center', paddingHorizontal: moderateScale(16), paddingVertical: moderateScale(14), paddingTop: moderateScale(18), },
    backBtn: { width: moderateScale(36), height: moderateScale(36), borderRadius: moderateScale(18), backgroundColor: 'rgba(255,255,255,0.18)', alignItems: 'center', justifyContent: 'center', },
    backIcon: { width: moderateScale(12), height: moderateScale(12), tintColor: '#fff' },
    headerTitle: { color: '#fff', fontSize: moderateScale(17), fontWeight: '700', letterSpacing: 0.3 },
    headerSub: { color: 'rgba(255,255,255,0.75)', fontSize: moderateScale(12), marginTop: 1 },
    filterBtn: { width: moderateScale(36), height: moderateScale(36), borderRadius: moderateScale(18), backgroundColor: 'rgba(255,255,255,0.18)', alignItems: 'center', justifyContent: 'center', },
    filterIcon: { width: moderateScale(18), height: moderateScale(18), tintColor: '#fff' },

    // Tab bar
    tabBar: { flexDirection: 'row', backgroundColor: '#fff', paddingHorizontal: moderateScale(16), borderBottomWidth: 1, borderBottomColor: BORDER, },
    tabItem: { flex: 1, alignItems: 'center', paddingVertical: moderateScale(12), position: 'relative', },
    tabItemActive: {},
    tabLabel: { fontSize: moderateScale(14), color: TEXT_SOFT, fontWeight: '500' },
    tabLabelActive: { color: PRIMARY, fontWeight: '700' },
    tabIndicator: { position: 'absolute', bottom: 0, left: '20%', right: '20%', height: 3, borderRadius: 2, backgroundColor: PRIMARY, },

    // Search
    searchWrap: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', marginHorizontal: moderateScale(14), marginTop: moderateScale(14), marginBottom: moderateScale(4), borderRadius: moderateScale(12), paddingHorizontal: moderateScale(14), borderWidth: 1, borderColor: BORDER, height: moderateScale(46), },
    searchIcon: { width: moderateScale(16), height: moderateScale(16), tintColor: TEXT_SOFT, marginRight: 10 },
    searchInput: { flex: 1, fontSize: moderateScale(14), color: TEXT_DARK },
    searchClear: { padding: 4 },

    // Empty state
    emptyState: { flex: 1, alignItems: 'center', justifyContent: 'center', paddingBottom: 80 },
    emptyIcon: { fontSize: 48, marginBottom: 12 },
    emptyTitle: { fontSize: moderateScale(16), fontWeight: '700', color: TEXT_DARK, marginBottom: 6 },
    emptySub: { fontSize: moderateScale(13), color: TEXT_MID },

    // Card
    card: { backgroundColor: CARD_BG, borderRadius: moderateScale(16), marginBottom: moderateScale(12), borderWidth: 1, borderColor: BORDER, overflow: 'hidden', elevation: 3, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.07, shadowRadius: 8, },
    cardInner: { flexDirection: 'row', alignItems: 'center', padding: moderateScale(14), gap: moderateScale(10), },
    cardLeft: { flex: 1, gap: moderateScale(6) },

    orderIdRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
    orderId: { fontSize: moderateScale(15), fontWeight: '700', color: TEXT_DARK },
    statusBadge: { flexDirection: 'row', alignItems: 'center', backgroundColor: SUCCESS_BG, paddingHorizontal: moderateScale(8), paddingVertical: moderateScale(3), borderRadius: 20, gap: 4, },
    statusDot: { width: 6, height: 6, borderRadius: 3, backgroundColor: SUCCESS },
    statusText: { fontSize: moderateScale(11), color: SUCCESS, fontWeight: '600' },

    productTitle: { fontSize: moderateScale(13), color: TEXT_MID, fontWeight: '500' },
    cardMetaRow: { flexDirection: 'row', gap: 8 },
    metaChip: { fontSize: moderateScale(11), color: TEXT_MID },
    cardFooterRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
    destText: { fontSize: moderateScale(12), color: TEXT_MID, flex: 1 },
    qtyBadge: { backgroundColor: PRIMARY_LIGHT, paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(3), borderRadius: 20, },
    qtyBadgeText: { fontSize: moderateScale(12), color: PRIMARY, fontWeight: '700' },

    chevronBtn: { width: moderateScale(30), height: moderateScale(30), borderRadius: moderateScale(8), borderWidth: 1.5, borderColor: PRIMARY, alignItems: 'center', justifyContent: 'center', },
    chevronBtnActive: { backgroundColor: PRIMARY, borderColor: PRIMARY },
    chevronIcon: { width: moderateScale(10), height: moderateScale(10) },

    // Invoice section
    invoiceSection: { borderTopWidth: 1, borderTopColor: BORDER, padding: moderateScale(12), gap: moderateScale(10), backgroundColor: '#FAFAFA', },
    invoiceSectionTitle: { fontSize: moderateScale(12), fontWeight: '700', color: TEXT_MID, textTransform: 'uppercase', letterSpacing: 0.8, marginBottom: 2, },
    invoiceCard: { backgroundColor: '#fff', borderRadius: moderateScale(12), padding: moderateScale(12), borderWidth: 1, borderColor: BORDER, gap: moderateScale(10), },
    invoiceTopRow: { flexDirection: 'row', justifyContent: 'space-between' },
    invoiceLabel: { fontSize: moderateScale(11), color: TEXT_SOFT, marginBottom: 2 },
    invoiceValue: { fontSize: moderateScale(13), color: TEXT_DARK, fontWeight: '600' },
    invoiceBottomRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
    invoiceQtyWrap: { flexDirection: 'row', alignItems: 'baseline', gap: 4 },
    invoiceQtyLabel: { fontSize: moderateScale(12), color: TEXT_MID },
    invoiceQtyVal: { fontSize: moderateScale(16), fontWeight: '800', color: TEXT_DARK },
    allocateBtn: { backgroundColor: PRIMARY, paddingVertical: moderateScale(8), paddingHorizontal: moderateScale(16), borderRadius: moderateScale(10), },
    allocateBtnText: { color: '#fff', fontSize: moderateScale(13), fontWeight: '700' },

    // Allocated card
    avatarCircle: { width: moderateScale(42), height: moderateScale(42), borderRadius: moderateScale(21), alignItems: 'center', justifyContent: 'center', marginRight: moderateScale(4), },
    avatarInitial: { color: '#fff', fontSize: moderateScale(16), fontWeight: '800' },
    counterName: { fontSize: moderateScale(15), fontWeight: '700', color: TEXT_DARK },
    allocCount: { fontSize: moderateScale(12), color: TEXT_MID, marginTop: 2 },

    allocCard: { backgroundColor: '#fff', borderRadius: moderateScale(12), padding: moderateScale(12), borderWidth: 1, borderColor: BORDER, gap: moderateScale(10), },
    allocHeader: { flexDirection: 'row', alignItems: 'flex-start', justifyContent: 'space-between', gap: 8 },
    allocProduct: { flex: 1, fontSize: moderateScale(13), fontWeight: '600', color: TEXT_DARK },
    allocQtyBadge: { backgroundColor: PRIMARY_LIGHT, paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(4), borderRadius: 20, flexShrink: 0, },
    allocQtyText: { fontSize: moderateScale(12), color: PRIMARY, fontWeight: '700' },
    allocMeta: { flexDirection: 'row', justifyContent: 'space-between' },
    allocMetaItem: { flex: 1, alignItems: 'center' },
    allocMetaLabel: { fontSize: moderateScale(10), color: TEXT_SOFT, marginBottom: 2, textAlign: 'center' },
    allocMetaVal: { fontSize: moderateScale(12), color: TEXT_DARK, fontWeight: '600', textAlign: 'center' },
})

// ── Bottom sheet styles ───────────────────────────────────────────────────────
const bsStyles = StyleSheet.create({
    backdrop: { ...StyleSheet.absoluteFillObject, backgroundColor: 'rgba(0,0,0,0.5)' },
    sheet: { position: 'absolute', left: 0, right: 0, bottom: 0, backgroundColor: '#fff', borderTopLeftRadius: moderateScale(28), borderTopRightRadius: moderateScale(28), paddingHorizontal: moderateScale(20), paddingBottom: moderateScale(36), paddingTop: moderateScale(12), elevation: 24, shadowColor: '#000', shadowOffset: { width: 0, height: -4 }, shadowOpacity: 0.15, shadowRadius: 16, },
    handle: { width: 40, height: 4, borderRadius: 2, backgroundColor: '#DDD', alignSelf: 'center', marginBottom: moderateScale(16) },
    titleRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: moderateScale(12) },
    sheetTitle: { fontSize: moderateScale(18), fontWeight: '800', color: TEXT_DARK },
    closeBtn: { width: 30, height: 30, borderRadius: 15, backgroundColor: '#F3F4F6', alignItems: 'center', justifyContent: 'center' },
    closeBtnText: { fontSize: 13, color: TEXT_MID, fontWeight: '700' },
    productPill: { flexDirection: 'row', alignItems: 'center', backgroundColor: PRIMARY_LIGHT, borderRadius: 20, paddingHorizontal: moderateScale(12), paddingVertical: moderateScale(6), alignSelf: 'flex-start', marginBottom: moderateScale(16), gap: 6, },
    productDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: PRIMARY },
    productPillText: { fontSize: moderateScale(13), color: PRIMARY, fontWeight: '600', maxWidth: 240 },

    // Tabs
    tab: { flexDirection: 'row', alignItems: 'center', width: moderateScale(130), paddingVertical: moderateScale(8), paddingHorizontal: moderateScale(10), marginRight: moderateScale(8), borderRadius: moderateScale(10), borderWidth: 1.5, borderColor: BORDER, gap: 6, backgroundColor: '#FAFAFA', },
    tabActive: { borderColor: PRIMARY, backgroundColor: PRIMARY_LIGHT },
    tabError: { borderColor: PRIMARY, backgroundColor: '#FFF0EF' },
    tabAvatar: { width: 22, height: 22, borderRadius: 11, alignItems: 'center', justifyContent: 'center', flexShrink: 0 },
    tabAvatarText: { color: '#fff', fontSize: 10, fontWeight: '800' },
    tabText: { flex: 1, fontSize: moderateScale(12), color: TEXT_MID, fontWeight: '500' },
    tabTextActive: { color: PRIMARY, fontWeight: '700' },
    tabTextError: { color: PRIMARY },
    tabErrorDot: { width: 6, height: 6, borderRadius: 3, backgroundColor: PRIMARY, flexShrink: 0 },

    // Progress
    progressCard: { backgroundColor: '#F9FAFB', borderRadius: moderateScale(14), padding: moderateScale(14), marginBottom: moderateScale(14), borderWidth: 1, borderColor: BORDER, },
    progressRow: { flexDirection: 'row', alignItems: 'center', gap: 10 },
    progressStat: { alignItems: 'flex-start' },
    progressStatVal: { fontSize: moderateScale(18), fontWeight: '800', color: TEXT_DARK },
    progressStatLbl: { fontSize: moderateScale(10), color: TEXT_SOFT, marginTop: 1 },
    progressBarWrap: { flex: 1, alignItems: 'center', gap: 4 },
    progressBarBg: { width: '100%', height: 8, borderRadius: 4, backgroundColor: '#E5E7EB', overflow: 'hidden' },
    progressBarFill: { height: '100%', borderRadius: 4 },
    progressPct: { fontSize: moderateScale(10), color: TEXT_MID, fontWeight: '600' },

    // Input
    inputCard: { backgroundColor: '#F9FAFB', borderRadius: moderateScale(14), padding: moderateScale(14), marginBottom: moderateScale(16), borderWidth: 1, borderColor: BORDER, gap: moderateScale(10), },
    inputLabelRow: { flexDirection: 'row', alignItems: 'center', gap: 4, flexWrap: 'wrap' },
    inputLabel: { fontSize: moderateScale(13), color: TEXT_MID },
    inputDealerName: { fontSize: moderateScale(13), fontWeight: '700', color: TEXT_DARK, flex: 1 },
    inputRow: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', borderRadius: moderateScale(10), borderWidth: 1.5, borderColor: BORDER, overflow: 'hidden', },
    inputRowError: { borderColor: PRIMARY },
    input: { flex: 1, paddingHorizontal: moderateScale(14), paddingVertical: moderateScale(12), fontSize: moderateScale(20), fontWeight: '700', color: TEXT_DARK, },
    unitBadge: { paddingHorizontal: moderateScale(14), paddingVertical: moderateScale(12), backgroundColor: '#F3F4F6', borderLeftWidth: 1, borderLeftColor: BORDER, },
    unitText: { fontSize: moderateScale(14), fontWeight: '700', color: TEXT_MID },
    errorHint: { fontSize: moderateScale(12), color: PRIMARY, fontWeight: '500' },
    invoiceHint: { fontSize: moderateScale(12), color: TEXT_SOFT },

    // Buttons
    btnRow: { flexDirection: 'row', gap: moderateScale(10) },
    cancelBtn: { flex: 1, paddingVertical: moderateScale(14), borderRadius: moderateScale(12), alignItems: 'center', backgroundColor: '#F3F4F6', borderWidth: 1, borderColor: BORDER, },
    cancelBtnText: { fontSize: moderateScale(14), color: TEXT_MID, fontWeight: '600' },
    allocateBtn: { flex: 2, paddingVertical: moderateScale(14), borderRadius: moderateScale(12), alignItems: 'center', backgroundColor: PRIMARY, },
    allocateBtnDisabled: { backgroundColor: '#E5E7EB' },
    allocateBtnText: { fontSize: moderateScale(14), color: '#fff', fontWeight: '700', letterSpacing: 0.3 },
})


const pickerStyles = StyleSheet.create({
    sheet: { position: 'absolute', bottom: 0, left: 0, right: 0, backgroundColor: '#fff', borderTopLeftRadius: moderateScale(24), borderTopRightRadius: moderateScale(24), padding: moderateScale(20), height: '55%', elevation: 20, },
    handle: { width: 40, height: 4, borderRadius: 2, backgroundColor: '#DDD', alignSelf: 'center', marginBottom: moderateScale(16) },
    title: { fontSize: moderateScale(17), fontWeight: '800', color: TEXT_DARK, textAlign: 'center', marginBottom: moderateScale(16) },
    pickerRow: { flexDirection: 'row', flex: 1, gap: 10 },
    divider: { width: 1, backgroundColor: BORDER },
    option: { height: 52, alignItems: 'center', justifyContent: 'center', borderRadius: moderateScale(10), marginHorizontal: 6, marginVertical: 2 },
    optionActive: { backgroundColor: PRIMARY },
    optionText: { fontSize: moderateScale(15), color: TEXT_DARK },
    optionTextActive: { color: '#fff', fontWeight: '700' },
    btnRow: { flexDirection: 'row', gap: 12, marginTop: moderateScale(16) },
    cancelBtn: { flex: 1, padding: moderateScale(14), backgroundColor: '#F3F4F6', borderRadius: moderateScale(12), alignItems: 'center' },
    cancelText: { fontSize: moderateScale(14), color: TEXT_MID, fontWeight: '600' },
    confirmBtn: { flex: 1, padding: moderateScale(14), backgroundColor: PRIMARY, borderRadius: moderateScale(12), alignItems: 'center' },
    confirmText: { fontSize: moderateScale(14), color: '#fff', fontWeight: '700' },
})