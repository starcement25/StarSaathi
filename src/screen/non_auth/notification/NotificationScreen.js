import { useEffect, useState } from "react";
import SafeView from "../../../helper/SafeView"
import UrlStorage from "../../../storage/UrlStorage";
import Toast from "react-native-toast-message";
import toastConfig from "../../../helper/ToastConfig";
import { FlatList, Platform, Text, View } from "react-native";
import { Colors } from "../../../assets/Colors";
import SBSCommonHeaderView from "../../../common/SBSCommonHeaderView";
import { AuthCheckingApi } from "../../../auth/AuthCheckingApi";
import AuthNotVerifyPopupView from "../../../auth/AuthNotVerifyPopupView";
import { moderateScale } from "../../../helper/Window";
import moment from "moment";

const NotificationScreen = (props) => {
    const [notificationList, setNotificationList] = useState([])
    const [loading, setLoading] = useState(false)
    const [authChecker, setAuthChecker] = useState(false)
    useEffect(() => {
        fetchNotificationList()
    }, [])

    const fetchAuthData = async () => {
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
        }
    }

    const fetchNotificationList = async () => {
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        const requestOptions = {
            method: "GET",
            redirect: "follow"
        };
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.NotificationURL.notification_list_url + "?the_branch_code=" + 'B0002' + "&the_id=" + UrlStorage.ParameterList.BasicData.emp_code
        console.log(url);

        await fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                console.log(result.notification_data.length);

                try {
                    if (result.notification_data.length > 0) {
                        setNotificationList(result.notification_data);
                    } else {
                        Toast.show({ type: 'error', text1: 'Sorry...', text2: 'No Notifications Available' })
                        setNotificationList([])
                    }
                } catch (error) { }
            }).catch((error) => { });
        setLoading(false)

    }
    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Notifications" backPath=" " navigation={props.navigation} props={props} />

                <FlatList
                    data={notificationList}
                    keyExtractor={(item) => item}
                    showsVerticalScrollIndicator={false}
                    decelerationRate="fast"
                    renderItem={({ item, index }) => {
                        return (
                            <View style={{ width: '100%', padding: moderateScale(5) }}>
                                <View style={{ width: "100%", backgroundColor: "#fff", ...Platform.select({ ios: { shadowColor: "#000", shadowOffset: { width: 0, height: 40 }, shadowOpacity: 0.08, shadowRadius: 12, }, android: { elevation: 10 }, }), borderRadius: moderateScale(10), paddingTop: moderateScale(14), paddingBottom: moderateScale(10), paddingHorizontal: moderateScale(12), }}>
                                    <View style={{ width: '100%', flexDirection: 'row' }}>
                                        <Text style={{ flex: 1, fontWeight: '500', fontSize: moderateScale(16), color: '#000' }}>{item.m_title}</Text>
                                        <Text style={{ fontSize: moderateScale(10), color: '#444' }}>{moment(item.n_date_time).format('hh:mm A, DD-MM-YYYY')}</Text>
                                    </View>
                                    <Text style={{ fontSize: moderateScale(14), color: '#444', top: moderateScale(3) }}>{item.m_message}</Text>
                                </View>
                            </View>
                        )
                    }}
                />
            </View>
            <Toast config={toastConfig} />
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )

}

export default NotificationScreen;