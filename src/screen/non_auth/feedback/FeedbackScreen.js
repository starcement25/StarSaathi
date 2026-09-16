import React, { useEffect, useState } from 'react'
import { Image, Text, TextInput, TouchableOpacity, View } from 'react-native'
import Toast from 'react-native-toast-message'
import SafeView from '../../../helper/SafeView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Icons } from '../../../assets/Icons'
import DataStorage from '../../../storage/DataStorage'
import UrlStorage from '../../../storage/UrlStorage'
import toastConfig from '../../../helper/ToastConfig'
import { AuthCheckingApi } from '../../../auth/AuthCheckingApi'
import AuthNotVerifyPopupView from '../../../auth/AuthNotVerifyPopupView'

const FeedbackScreen = (props) => {
    const [ratingCount, setRatingCount] = useState(0)
    const [starColorCode, setStarColorCode] = useState('#000')
    const [review, setReview] = useState('')
    const [authChecker, setAuthChecker] = useState(false)

    useEffect(() => {
        switch (ratingCount) {
            case 1:
                setStarColorCode('#F00')
                break
            case 2:
                setStarColorCode('#FF6200')
                break
            case 3:
                setStarColorCode('#FF9D00')
                break
            case 4:
                setStarColorCode('#BBFF00')
                break
            case 5:
                setStarColorCode('#0F0')
                break
        }
    }, [ratingCount])

    const checkData = () => {
        if (ratingCount == 0)
            Toast.show({ type: 'error', text1: 'Sorry', text2: 'Please select your rating first.' })
        else if (review.trim() == '')
            Toast.show({ type: 'error', text1: 'Sorry', text2: 'Please share your feedback' })
        else
            requestForShareFeedback()
    }

    const requestForShareFeedback = async () => {
        const formdata = new FormData()
        formdata.append("customer_id", UrlStorage.ParameterList.BasicData.emp_id)
        formdata.append("emp_code", DataStorage.feedback_customer_id)
        formdata.append("visit_datetime", DataStorage.feedback_visit_date)
        formdata.append("survey_rating", ratingCount.toString())
        formdata.append("remarks", review)
        var a = await AuthCheckingApi()
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        const requestOptions = { method: "POST", body: formdata, redirect: "follow" }
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.SalesVisitFeedbackURL.sales_visit_feedback_create_url
        fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                if (result.process_status == 'NO')
                    Toast.show({ type: 'error', text1: 'Sorry', text2: result.process_message })
                else {
                    Toast.show({ type: 'success', text1: 'Success', text2: result.process_message })
                    setTimeout(() => {
                        props.navigation.pop()
                    }, 1500)
                }
            })
            .catch((error) => {
            })
    }

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Sales Visit Feedback" backPath=" " />
                <View style={{ width: "100%", padding: moderateScale(20), gap: moderateScale(16), flex: 1 }}>
                    <Text style={{ color: Colors.text, fontSize: moderateScale(20), fontWeight: "600", textAlign: "center" }}>Hello,</Text>
                    <Text style={{ color: "#7D7D7D", fontSize: moderateScale(16), textAlign: "center" }}>Based on your overall experience you had with our sales person, </Text>
                    <Text style={{ color: DataStorage.primaryColorCode, fontSize: moderateScale(18), fontWeight: "600", textAlign: "center" }}>Please Rate Us</Text>
                    <View style={{ marginVertical: moderateScale(14), width: "100%", flexDirection: "row", gap: moderateScale(15), alignItems: "center", justifyContent: "center" }}>
                        <TouchableOpacity onPress={() => setRatingCount(1)}>
                            <>
                                {ratingCount >= 1 ?
                                    <Image source={Icons.ActiveStarIcon} style={{ width: moderateScale(36), height: moderateScale(36), tintColor: starColorCode }} /> :
                                    <Image source={Icons.InactiveStarIcon} style={{ width: moderateScale(36), height: moderateScale(36), tintColor: '#aaa' }} />
                                }
                            </>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={() => setRatingCount(2)}>
                            <>
                                {ratingCount >= 2 ?
                                    <Image source={Icons.ActiveStarIcon} style={{ width: moderateScale(36), height: moderateScale(36), tintColor: starColorCode }} /> :
                                    <Image source={Icons.InactiveStarIcon} style={{ width: moderateScale(36), height: moderateScale(36), tintColor: '#aaa' }} />
                                }
                            </>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={() => setRatingCount(3)}>
                            <>
                                {ratingCount >= 3 ?
                                    <Image source={Icons.ActiveStarIcon} style={{ width: moderateScale(36), height: moderateScale(36), tintColor: starColorCode }} /> :
                                    <Image source={Icons.InactiveStarIcon} style={{ width: moderateScale(36), height: moderateScale(36), tintColor: '#aaa' }} />
                                }
                            </>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={() => setRatingCount(4)}>
                            <>
                                {ratingCount >= 4 ?
                                    <Image source={Icons.ActiveStarIcon} style={{ width: moderateScale(36), height: moderateScale(36), tintColor: starColorCode }} /> :
                                    <Image source={Icons.InactiveStarIcon} style={{ width: moderateScale(36), height: moderateScale(36), tintColor: '#aaa' }} />
                                }
                            </>
                        </TouchableOpacity>
                        <TouchableOpacity onPress={() => setRatingCount(5)}>
                            <>
                                {ratingCount >= 5 ?
                                    <Image source={Icons.ActiveStarIcon} style={{ width: moderateScale(36), height: moderateScale(36), tintColor: starColorCode }} /> :
                                    <Image source={Icons.InactiveStarIcon} style={{ width: moderateScale(36), height: moderateScale(36), tintColor: '#aaa' }} />
                                }
                            </>
                        </TouchableOpacity>
                    </View>
                    <View style={{ width: "100%", gap: moderateScale(8) }}>
                        <Text style={{ color: "#1E1E1E", fontSize: moderateScale(14), fontWeight: "500" }}>Is there is any way me could improve our service? Please specify below</Text>
                        <View style={{ width: "100%", height: moderateScale(140), borderRadius: moderateScale(10), borderColor: "#E5E5E5", borderWidth: moderateScale(1), paddingHorizontal: moderateScale(10) }}>
                            <TextInput placeholder='|' style={{ width: "100%", color: "#444", fontSize: moderateScale(14) }} onChangeText={setReview} value={review} />
                        </View>
                    </View>
                </View>
                <TouchableOpacity activeOpacity={0.95} onPress={() => { checkData() }}>
                    <View style={{ width: "100%", paddingHorizontal: moderateScale(15), marginBottom: moderateScale(20) }}>
                        <View style={{ width: "100%", height: moderateScale(40), flexDirection: "row", gap: moderateScale(8), backgroundColor: DataStorage.primaryColorCode, borderRadius: moderateScale(10), alignItems: "center", justifyContent: "center" }}>
                            <Text style={{ color: Colors.white, fontSize: moderateScale(16), fontWeight: 500 }}>Submit</Text>
                        </View>
                    </View>
                </TouchableOpacity>
            </View>
            <Toast config={toastConfig} />
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    )
}

export default FeedbackScreen
