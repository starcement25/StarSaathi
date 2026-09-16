import React, { useEffect, useState } from 'react'
import { FlatList, Text, TouchableOpacity, View } from 'react-native'
import moment from 'moment'
import SafeView from '../../../helper/SafeView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import Loader from '../../../common/Loader'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const AgeingScreen = () => {
    const [ageingList, setAgeingList] = useState([])
    const [loading, setLoading] = useState()
    const [authChecker, setAuthChecker] = useState(false)

    useEffect(() => {
        DataStorage.typeOfUse === 1 ? requestForAgeingListSBS() : requestForAgeingListCement()
    }, [])

    const requestForAgeingListCement = async () => {
        setLoading(true)
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        const requestOptions = { method: "GET", redirect: "follow" }
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.AgeingURL.dealer_wise_ageing_list_url
        url = url + "?customer_code=" + UrlStorage.ParameterList.BasicData.emp_id
        await fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                if (result.process_status == 'YES')
                    setAgeingList(result.ageing_data)
                setLoading(false)
            })
            .catch((error) => {
                setLoading(false)
            })
    }

    const requestForAgeingListSBS = async () => {
        setLoading(true)
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        const requestOptions = { method: "GET", redirect: "follow" }
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.AgeingURL.dealer_wise_ageing_list_url
        url = url + "?customer_code=" + UrlStorage.ParameterList.BasicData.emp_id
        await fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                if (result.process_status == 'YES')
                    setAgeingList(result.ageing_data)
                setLoading(false)
            })
            .catch((error) => {
                setLoading(false)
            })
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Ageing" backPath=" " />
                <View style={{ width: "100%", marginTop: moderateScale(10), gap: moderateScale(16), flex: 1 }}>
                    <FlatList
                        data={ageingList}
                        keyExtractor={(item) => item.id}
                        showsVerticalScrollIndicator={false}
                        decelerationRate="fast"
                        renderItem={({ item }) => (
                            <TouchableOpacity activeOpacity={0.95} style={{ paddingVertical: moderateScale(6), paddingHorizontal: moderateScale(20) }} onPress={() => { }}>
                                <View style={{ width: "100%", overflow: "hidden", borderWidth: moderateScale(1), borderColor: "#DCDDDF", borderRadius: moderateScale(10), backgroundColor: "#FFFFFF", elevation: 10, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: { x: (0), y: (4) } }}>
                                    <View style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(6), }}>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Document No</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{item.document_no}</Text>
                                        </View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Document Date</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>{moment(item.document_date, 'YYYY-MM-DD').format('MMM DD, YYYY')}</Text>
                                        </View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", }}>
                                            <Text style={{ color: "#7D7D7D", fontSize: moderateScale(13), width: moderateScale(120) }}>Invoice Amount</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "500" }}>₹{item.inv_amount}</Text>
                                        </View>
                                    </View>
                                </View>
                            </TouchableOpacity>
                        )} />
                </View>
            </View>
            {loading && <Loader />}
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

export default AgeingScreen