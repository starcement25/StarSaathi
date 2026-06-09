import React, { useState, useRef, useEffect } from "react";
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Platform, Animated, Alert, } from "react-native";
import DateTimePicker from "@react-native-community/datetimepicker";
import SafeView from "../../../helper/SafeView";
import SBSCommonHeaderView from "../../../common/SBSCommonHeaderView";
import { Colors } from "../../../assets/Colors";
import { moderateScale } from "../../../helper/Window";
import Toast from "react-native-toast-message";
import toastConfig from "../../../helper/ToastConfig";
import UrlStorage from "../../../storage/UrlStorage";
import Loader from "../../../common/Loader";
import SingleSelectPopupView from "../../../common/SingleSelectPopupView";
import { AuthCheckingApi } from "../../../auth/AuthCheckingApi";
import AuthNotVerifyPopupView from "../../../auth/AuthNotVerifyPopupView";

const AddNewLiftingScreen = (props) => {
    const { navigation } = props;
    const [linkedDealer, setLinkedDealer] = useState("");
    const [productName, setProductName] = useState("");
    const [productCode, setProductCode] = useState("");
    const [quantityInBags, setQuantityInBags] = useState("");
    const [dateOfLifting, setDateOfLifting] = useState("");
    const [challanNumber, setChallanNumber] = useState("");
    const [productList, setProductList] = useState([]);

    const [showPicker, setShowPicker] = useState(false);
    const [pickerMode, setPickerMode] = useState("date");

    const [isProductPopupVisiable, setIsProductPopupVisiable] = useState(false);

    const [pickerFor, setPickerFor] = useState("");
    const [loading, setLoading] = useState(false);
    const [authChecker, setAuthChecker] = useState(false);

    const fieldsAnim = [
        useRef(new Animated.Value(0)).current,
        useRef(new Animated.Value(0)).current,
        useRef(new Animated.Value(0)).current,
        useRef(new Animated.Value(0)).current,
        useRef(new Animated.Value(0)).current,
        useRef(new Animated.Value(0)).current,
    ];

    useEffect(() => {
        requestForLiftingChecking()
        const animations = fieldsAnim.map((anim) =>
            Animated.timing(anim, {
                toValue: 1,
                duration: 500,
                useNativeDriver: true,
            })
        );
        Animated.stagger(150, animations).start();

        setLinkedDealer(UrlStorage.ParameterList.BasicData.belong_dealer_name)
        requestForCementProductList()
    }, []);
    const fetchAuthData = async () => {
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
        }
    }

    const dateToMilliSecond = (dateString) => {
        if (!dateString) return 0;
        return new Date(dateString).getTime();
    };
    const requestForLiftingChecking = async () => {
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

        fetch(UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.LiftingURL.lifting_validation_url + "?customer_code=" + UrlStorage.ParameterList.BasicData.customerDetails.dns_emp_code + "&user_type=" + UrlStorage.ParameterList.BasicData.user_type, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                setLoading(false)
                if (result.process_status == "NO") {
                    Alert.alert('Sorry', 'Please contact Admin', [
                        {
                            text: 'Cancel',
                            onPress: () => navigation.goBack(),
                            style: 'cancel',
                        },
                    ]);
                } else {
                    const validation_from = dateToMilliSecond(result.lifting_validation_data[0].validation_from);
                    const validation_to = dateToMilliSecond(result.lifting_validation_data[0].validation_to);
                    const validation_last_date = dateToMilliSecond(result.lifting_validation_data[0].validation_last_date);
                    const current_date = dateToMilliSecond(result.lifting_validation_data[0].current_date);
                    let continue_status = "No";
                    if (current_date > 0 && current_date >= validation_from && current_date <= validation_last_date) {
                        continue_status = "Yes";
                    } else {
                        continue_status = "No";
                        Alert.alert('Sorry', 'Please contact Admin', [
                            {
                                text: 'Ok',
                                onPress: () => navigation.goBack(),
                                style: 'cancel',
                            },
                        ]);
                    }
                }
            }).catch((error) => { });
    }
    const showDatePicker = (field) => {
        setPickerFor(field);
        setPickerMode("date");
        setShowPicker(true);
    };

    const onDateChange = (event, selectedDate) => {
        // if(Platform.OS=='android')
        setShowPicker(false);
        if (event.type === "set" && selectedDate) {
            const formatted = selectedDate.toISOString().split("T")[0];
            if (pickerFor === "Lifting") setDateOfLifting(formatted);
        }
    };

    const requestForCementProductList = async () => {
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
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DashboardURL.product_data_list_url
        url = url + '?emp_code=' + UrlStorage.ParameterList.BasicData.emp_code + '&user_type=' + UrlStorage.ParameterList.BasicData.user_type

        await fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                if (result.process_status == 'YES') {
                    setProductList(result.product_date.map((item) => ({ ...item, count: 0 })))
                } else {
                    setProductList([])
                }
            }).catch((error) => { });
        setLoading(false)
    }

    const closePopup = () => {
        setIsProductPopupVisiable(false)
    }
    const selectItem = (item) => {
        setProductName(item.prod_desc)
        setProductCode(item.prod_code)
        setIsProductPopupVisiable(false)
    }

    const updateDetails = async () => {
        setLoading(true)
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }

        const formdata = new FormData();
        formdata.append("linked_dealer_cust_code", UrlStorage.ParameterList.BasicData.belong_dealer_dns_code);
        formdata.append("sub_dealer_cust_code", UrlStorage.ParameterList.BasicData.customerDetails.dns_emp_code);
        formdata.append("prod_code", productCode);
        formdata.append("tot_bag_qty", quantityInBags);
        formdata.append("lifting_date", dateOfLifting);
        formdata.append("challan_no", challanNumber);

        const requestOptions = {
            method: "POST",
            body: formdata,
            redirect: "follow",
        };
        var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.LiftingURL.add_lifting_url;

        fetch(url, requestOptions)
            .then((response) => response.json())
            .then((result) => {
                setLoading(false)
                if (result?.process_status == "YES") {
                    Toast.show({ type: "success", text1: "Success", text2: result.process_message, });
                    setTimeout(() => {
                        props.navigation.pop();
                    }, 1000);
                } else {
                    Toast.show({ type: "error", text1: "Error", text2: result.process_message ?? "Something went wrong. Please try Again later", });
                }
            }).catch((error) => { });
    };

    const handleSubmit = () => {
        if (!productName) {
            Toast.show({ type: "error", text1: "Sorry", text2: "Please select product name", });
        } else if (!quantityInBags) {
            Toast.show({ type: "error", text1: "Sorry", text2: "Please enter Quantity in Bags", });
        } else if (!dateOfLifting) {
            Toast.show({ type: "error", text1: "Sorry", text2: "Please select Date of Lifting", });
        } else if (!challanNumber) {
            Toast.show({ type: "error", text1: "Sorry", text2: "Please enter Challan Number", });
        } else {
            updateDetails();
        }
    };

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ flex: 1, backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Add Lifting" backPath=" " Filter={false} Calendar={false} navigation={navigation} props={props} />

                <View style={{ flex: 1, padding: moderateScale(20) }}>

                    <Animated.View style={{ opacity: fieldsAnim[0], transform: [{ translateY: fieldsAnim[0].interpolate({ inputRange: [0, 1], outputRange: [-20, 0], }), },], }}>
                        <Text style={styles.label}>Linked Dealer</Text>
                        <TextInput style={styles.input} maxLength={10} editable={false} value={linkedDealer} placeholderTextColor={Colors.grey} />
                    </Animated.View>

                    <Animated.View style={{ opacity: fieldsAnim[1], transform: [{ translateY: fieldsAnim[1].interpolate({ inputRange: [0, 1], outputRange: [-20, 0], }), },], }}>
                        <Text style={styles.label}>Product Name</Text>
                        <TouchableOpacity style={styles.inputText} onPress={() => { setIsProductPopupVisiable(true) }}>
                            <Text style={{ color: productName ? Colors.text : Colors.grey }}>{productName || "Select Product"}</Text>
                        </TouchableOpacity>
                    </Animated.View>

                    {/* Qty Bag */}
                    <Animated.View style={{ opacity: fieldsAnim[2], transform: [{ translateY: fieldsAnim[2].interpolate({ inputRange: [0, 1], outputRange: [-20, 0], }), },], }}>
                        <Text style={styles.label}>Quantity in Bags</Text>
                        <TextInput style={styles.input} maxLength={10} keyboardType="number-pad" placeholder="Quantity in Bags" value={quantityInBags} placeholderTextColor={Colors.grey} onChangeText={setQuantityInBags} />
                    </Animated.View>

                    {/* Date of Lifting */}
                    <Animated.View style={{ opacity: fieldsAnim[3], transform: [{ translateY: fieldsAnim[3].interpolate({ inputRange: [0, 1], outputRange: [-20, 0], }), },], }}>
                        <Text style={styles.label}>Date of Lifting</Text>
                        <TouchableOpacity style={styles.inputText} onPress={() => { showDatePicker("Lifting") }}>
                            <Text style={{ color: dateOfLifting ? Colors.text : Colors.grey }}>{dateOfLifting || "Date of Lifting"}</Text>
                        </TouchableOpacity>
                    </Animated.View>

                    {/* Challan Number */}
                    <Animated.View style={{ opacity: fieldsAnim[3], transform: [{ translateY: fieldsAnim[3].interpolate({ inputRange: [0, 1], outputRange: [-20, 0], }), },], }}>
                        <Text style={styles.label}>Enter Challan Number</Text>
                        <TextInput style={styles.input} placeholder="Enter Challan Number" placeholderTextColor={Colors.grey} value={challanNumber} onChangeText={setChallanNumber} />
                    </Animated.View>

                    <Animated.View style={{ opacity: fieldsAnim[4], transform: [{ translateY: fieldsAnim[4].interpolate({ inputRange: [0, 1], outputRange: [-20, 0], }), },], }}>
                        <TouchableOpacity style={styles.button} onPress={handleSubmit}>
                            <Text style={styles.buttonText}>Submit</Text>
                        </TouchableOpacity>
                    </Animated.View>
                </View>
            </View>
            {showPicker && (
                <>
                    <DateTimePicker
                        value={new Date()}
                        mode={pickerMode}
                        display={Platform.OS === "ios" ? "spinner" : "default"}
                        maximumDate={pickerFor === "dob" ? new Date() : undefined}
                        onChange={onDateChange}
                        style={{ flex: 1, backgroundColor: Colors.white }}
                    />
                </>
            )}
            <Toast config={toastConfig} />
            <SingleSelectPopupView isVisible={isProductPopupVisiable} dataList={productList} closePopup={closePopup} selectItem={selectItem} />
            {loading && <Loader />}
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    );
};

const styles = StyleSheet.create({
    label: { fontSize: moderateScale(14), fontWeight: "600", marginBottom: moderateScale(6), marginTop: moderateScale(12), color: Colors.black, },
    input: { borderWidth: 1, borderColor: '#999', borderRadius: 10, height: moderateScale(40), color: Colors.black, paddingHorizontal: moderateScale(10), textAlignVertical: 'center', backgroundColor: Colors.white, fontSize: moderateScale(14), marginBottom: moderateScale(12), },
    inputText: { borderWidth: 1, borderColor: '#999', borderRadius: 10, height: moderateScale(40), color: Colors.black, padding: moderateScale(10), textAlignVertical: 'center', backgroundColor: Colors.white, fontSize: moderateScale(14), marginBottom: moderateScale(12), },
    button: { backgroundColor: Colors.main, marginTop: moderateScale(24), paddingVertical: moderateScale(12), borderRadius: 12, alignItems: "center", shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 4, },
    buttonText: { color: Colors.white, fontSize: moderateScale(16), fontWeight: "700", },
});

export default AddNewLiftingScreen;
