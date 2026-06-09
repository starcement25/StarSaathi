import React, { useEffect, useState } from 'react'
import { FlatList, Text, TouchableOpacity, View } from 'react-native'
import Toast from 'react-native-toast-message'
import SafeView from '../../../helper/SafeView'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import toastConfig from '../../../helper/ToastConfig'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const FeedbackListScreen = (props) => {
    const [visitList, setVisitList] = useState([])
    const [authChecker, setAuthChecker] = useState(false)

    useEffect(() => {
        requestForVisitList()
    }, [])

    const fetchAuthData = async () => {
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
        }
    }

    const requestForVisitList = async () => {
        const requestOptions = {
            method: "GET",
            redirect: "follow"
        };
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.SalesVisitFeedbackURL.sales_visit_list_url
        url = url + "?customer_code=" + UrlStorage.ParameterList.BasicData.emp_id

        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }

        fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                if (result.process_status == 'NO') {
                    Toast.show({ type: 'error', text1: 'Sorry', text2: result.process_message })
                } else {
                    setVisitList(result.sales_team_visit_data)
                }
            })
            .catch((error) => {
            });
    }

    const gotoFeedbackScreen = (item) => {
        DataStorage.feedback_customer_id = item.emp_code
        DataStorage.feedback_visit_date = item.visit_datetime
        DataStorage.feedback_customer_name = item.emp_name
        props.navigation.navigate('FeedbackScreen')
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: '100%', height: '100%' }}>
                <SBSCommonHeaderView title="Sales Visit Feedback" backPath=" " />
                <View style={{ width: "100%", flex: 1 }}>
                    <FlatList
                        data={visitList}
                        keyExtractor={(item) => item.id}
                        showsVerticalScrollIndicator={false}
                        decelerationRate="fast"
                        renderItem={({ item, index }) => (
                            <View style={{ width: "100%", padding: moderateScale(10) }}>
                                <TouchableOpacity onPress={() => { gotoFeedbackScreen(item) }} style={{ width: '100%', elevation: 2, borderRadius: moderateScale(5), backgroundColor: '#FFF', flexDirection: 'column', padding: moderateScale(10), gap: moderateScale(5) }}>
                                    <View style={{ width: '100%', flexDirection: 'row' }}>
                                        <Text style={{ color: Colors.text, fontSize: 14, fontWeight: "600", flex: 1 }}>Employee Name</Text>
                                        <Text style={{ color: Colors.text, fontSize: 14, fontWeight: "600", flex: 2 }}>{item.emp_name}</Text>
                                    </View>
                                    <View style={{ width: '100%', flexDirection: 'row' }}>
                                        <Text style={{ color: Colors.text, fontSize: 14, fontWeight: "600", flex: 1 }}>Visit Date & Time</Text>
                                        <Text style={{ color: Colors.text, fontSize: 14, fontWeight: "600", flex: 2 }}>{item.visit_datetime}</Text>
                                    </View>
                                </TouchableOpacity>
                            </View>
                        )} />
                </View>
            </View>
            <Toast config={toastConfig} />
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

export default FeedbackListScreen
