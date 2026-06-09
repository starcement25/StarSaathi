import { useEffect, useState } from "react";
import SafeView from "../../../helper/SafeView"
import UrlStorage from "../../../storage/UrlStorage";
import Toast from "react-native-toast-message";
import toastConfig from "../../../helper/ToastConfig";
import { FlatList, View } from "react-native";
import { Colors } from "../../../assets/Colors";
import SBSCommonHeaderView from "../../../common/SBSCommonHeaderView";
import { AuthCheckingApi } from "../../../auth/AuthCheckingApi";
import AuthNotVerifyPopupView from "../../../auth/AuthNotVerifyPopupView";

const NotificationScreen = (props) => {
    const [notificationList, setNotificationList] = useState()
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
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.NotificationURL.notification_list_url + "?the_branch_code=" + '' + "&the_id=" + UrlStorage.ParameterList.BasicData.emp_code
        await fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                try {
                    if (result.length > 0) {
                        setNotificationList();
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
                            <View>
                                <Text>Notification</Text>
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