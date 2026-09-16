import React, { useEffect, useState } from 'react'
import { Alert, FlatList, Image, Text, TouchableOpacity, View, StyleSheet } from 'react-native'
import Toast from 'react-native-toast-message'
import toastConfig from '../../../helper/ToastConfig'
import SafeView from '../../../helper/SafeView'
import Loader from '../../../common/Loader'
import { Colors } from '../../../assets/Colors'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import UrlStorage from '../../../storage/UrlStorage'
import { getAllDataFrom_branch_schemes_PDF_Sub_dealer } from '../../../storage/database/GetDataFromTable'
import { moderateScale } from '../../../helper/Window'
import { Icons } from '../../../assets/Icons'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const SBSSchemeScreen = (props) => {
    const [loading, setLoading] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)
    const [listOfScheme, setListOfScheme] = useState([])

    useEffect(() => {
        if (UrlStorage.ParameterList.BasicData.user_type == "broker" || UrlStorage.ParameterList.BasicData.user_type.toLowerCase() == "dealer")
            checkUserType()
        else
            checkUserTypeSubDealer()
    }, [])

    const checkUserType = async () => {
        const myHeaders = new Headers()
        if (UrlStorage.ParameterList.BasicData.user_type.toLowerCase() == "broker") {
            myHeaders.append("Authorization", UrlStorage.ParameterList.BasicData.user_type == 'broker' ? `SAP_SP` : `SAP_DEALER` + `${UrlStorage.ParameterList.BasicData.emp_id}`)
            myHeaders.append("Content-Type", "application/json")
        } else {
            myHeaders.append("Authorization", UrlStorage.ParameterList.BasicData.user_type == 'broker' ? `SAP_SP` : `SAP_DEALER` + `${UrlStorage.ParameterList.BasicData.emp_id}`)
            myHeaders.append("Content-Type", "application/json")
        }
        const requestOptions = { method: "GET", redirect: "follow", headers: myHeaders }
        var url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.Scheme.scheme_list + `?dealer_id=${UrlStorage.ParameterList.BasicData.emp_id}`
        setLoading(true)
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        await fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                if (Array.isArray(result))
                    setListOfScheme(result)
                else if (result.data && Array.isArray(result.data))
                    setListOfScheme(result.data)
                else
                    setListOfScheme([])
                setLoading(false)
            })
            .catch((error) => {
                Toast.show({ type: 'error', text1: 'Error', text2: 'Failed to load schemes' })
                setLoading(false)
            })
    }

    const checkUserTypeSubDealer = async () => {
        try {
            setLoading(true)
            const arr = await getAllDataFrom_branch_schemes_PDF_Sub_dealer("")
            var a = []
            for (var i = 0; i < arr.length; i++) {
                if (arr[i].PDF_file_name != null)
                    a.push(arr[i])
            }
            setListOfScheme(a)
            setLoading(false)
        } catch (err) {
            setLoading(false)
        }
    }

    const formatDate = (dateString) => {
        if (!dateString) return ''
        const date = new Date(dateString)
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
    }

    const isSchemeActive = (startDate, endDate) => {
        if (!startDate || !endDate) return false
        const now = new Date()
        const start = new Date(startDate)
        const end = new Date(endDate)
        return now >= start && now <= end
    }

    const validateAndSanitizeUrl = (url) => {
        if (!url || url.trim() === '')
            return null
        let sanitizedUrl = url.trim()
        if (sanitizedUrl.startsWith('http://'))
            sanitizedUrl = sanitizedUrl.replace('http://', 'https://')
        else if (!sanitizedUrl.startsWith('https://'))
            sanitizedUrl = `https://${sanitizedUrl}`
        try {
            new URL(sanitizedUrl)
            return sanitizedUrl
        } catch (e) {
            return null
        }
    }

    const renderSchemeCard = ({ item, index }) => {
        const isActive = isSchemeActive(item.start_date, item.end_date)
        return (
            <TouchableOpacity activeOpacity={0.85} style={styles.gridItem} onPress={() => {
                const folderPath = item.pdf_url || item.url
                if (!folderPath || folderPath.trim() === '') {
                    Alert.alert('Error', 'PDF URL not available for this scheme')
                    return
                }
                const sanitizedUrl = validateAndSanitizeUrl(folderPath)
                if (!sanitizedUrl) {
                    Alert.alert('Error', 'Invalid PDF URL. Please contact support.')
                    return
                }
                props.navigation.navigate('PdfViewScreen', { pdfUrl: sanitizedUrl, pdfLink: sanitizedUrl, page_title: item.scheme || `Scheme ${index + 1}`, type: 'url' })
            }} >
                <View style={[styles.card, isActive && styles.activeCard]}>
                    <View style={styles.imageContainer}>
                        <Image source={Icons.CementScheme} style={styles.schemeImage} resizeMode="contain" />
                    </View>
                    <View style={styles.infoContainer}>
                        <Text numberOfLines={2} style={styles.schemeName} > {item.scheme || `Scheme ${index + 1}`} </Text>
                        {item.start_date && item.end_date && <Text style={styles.dateText} numberOfLines={1}> {formatDate(item.start_date)} - {formatDate(item.end_date)} </Text>}
                    </View>
                </View>
            </TouchableOpacity>
        )
    }

    const renderEmptyState = () => (
        <View style={styles.emptyContainer}>
            <Image source={Icons.CementScheme} style={styles.emptyImage} resizeMode="contain" />
            <Text style={styles.emptyTitle}>No Schemes Available</Text>
            <Text style={styles.emptySubtitle}> There are currently no schemes to display.{'\n'} Please check back later. </Text>
        </View>
    )

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={styles.container}>
                <SBSCommonHeaderView title="All Schemes" backPath=" " />
                <View style={styles.contentContainer}>
                    {listOfScheme?.length === 0 && !loading ? renderEmptyState() : <FlatList
                        data={listOfScheme}
                        numColumns={2}
                        key="two-column-grid"
                        keyExtractor={(item, index) => item.sl_no?.toString() || index.toString()}
                        showsVerticalScrollIndicator={false}
                        contentContainerStyle={styles.listContent}
                        columnWrapperStyle={styles.columnWrapper}
                        renderItem={renderSchemeCard} />}
                </View>
            </View>
            <Toast config={toastConfig} />
            {loading && <Loader />}
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

const styles = StyleSheet.create({
    container: { width: '100%', height: '100%', backgroundColor: Colors.white, },
    contentContainer: { width: "100%", flex: 1, backgroundColor: Colors.white, borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), paddingTop: moderateScale(16), },
    listContent: { paddingHorizontal: moderateScale(12), paddingBottom: moderateScale(20), },
    columnWrapper: { justifyContent: 'space-between', marginBottom: moderateScale(12), },
    gridItem: { width: '48%', },
    card: { backgroundColor: '#FFFFFF', borderRadius: moderateScale(16), padding: moderateScale(12), shadowColor: '#000', shadowOffset: { width: 0, height: 2, }, shadowOpacity: 0.08, shadowRadius: 8, elevation: 3, borderWidth: 1, borderColor: '#F0F0F0', height: '100%', },
    activeCard: { borderWidth: 1.5, },
    activeBadge: { position: 'absolute', top: moderateScale(10), right: moderateScale(10), backgroundColor: '#10B981', width: moderateScale(10), height: moderateScale(10), borderRadius: moderateScale(5), zIndex: 1, borderWidth: 2, borderColor: '#FFFFFF', },
    activeDot: { width: '100%', height: '100%', borderRadius: moderateScale(5), },
    imageContainer: { width: '100%', alignItems: 'center', justifyContent: 'center', paddingVertical: moderateScale(16), backgroundColor: '#F8F9FA', borderRadius: moderateScale(12), marginBottom: moderateScale(12), },
    schemeImage: { width: moderateScale(60), height: moderateScale(60), },
    infoContainer: { width: '100%', flex: 1, },
    schemeName: { fontSize: moderateScale(14), fontWeight: '700', color: Colors.text || '#1A1A1A', marginBottom: moderateScale(6), lineHeight: moderateScale(18), minHeight: moderateScale(36), },
    dateText: { fontSize: moderateScale(10), color: '#666666', fontWeight: '500', marginBottom: moderateScale(4), },
    branchText: { fontSize: moderateScale(10), color: '#888888', fontWeight: '500', marginTop: moderateScale(4), paddingTop: moderateScale(4), borderTopWidth: 1, borderTopColor: '#F0F0F0', },
    emptyContainer: { flex: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: moderateScale(40), },
    emptyImage: { width: moderateScale(120), height: moderateScale(120), opacity: 0.5, marginBottom: moderateScale(24), },
    emptyTitle: { fontSize: moderateScale(18), fontWeight: '700', color: '#1A1A1A', marginBottom: moderateScale(8), textAlign: 'center', },
    emptySubtitle: { fontSize: moderateScale(14), color: '#666666', textAlign: 'center', lineHeight: moderateScale(20), },
})

export default SBSSchemeScreen