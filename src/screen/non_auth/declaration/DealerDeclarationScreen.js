import React, { useCallback, useEffect, useState, useRef } from "react";
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    ScrollView,
    StyleSheet,
    Modal,
    FlatList,
    Platform,
    KeyboardAvoidingView,
} from "react-native";
import SBSCommonHeaderView from "../../../common/SBSCommonHeaderView";
import SafeView from "../../../helper/SafeView";
import { Colors } from "../../../assets/Colors";
import Toast from "react-native-toast-message";
import UrlStorage from "../../../storage/UrlStorage";
import Loader from "../../../common/Loader";
import toastConfig from "../../../helper/ToastConfig";
import { AuthCheckingApi } from "../../../auth/AuthCheckingApi";
import AuthNotVerifyPopupView from "../../../auth/AuthNotVerifyPopupView";

const SCROLL_CONTENT_STYLE = { paddingBottom: 20 };

const MonthItem = React.memo(({ item, onPress }) => {
    const handlePress = useCallback(() => onPress(item), [item, onPress]);
    return (
        <TouchableOpacity style={styles.monthItem} onPress={handlePress} activeOpacity={0.7}>
            <View style={styles.monthItemContent}>
                <Text style={styles.monthText}>{item.month_name}</Text>
                <View style={styles.monthIcon}>
                    <Text style={styles.chevronIcon}>›</Text>
                </View>
            </View>
        </TouchableOpacity>
    );
});

const monthKeyExtractor = (item) => item.value;

export default function DealerDeclarationScreen({ navigation }) {
    // Form fields
    const [dealerName, setDealerName] = useState("");
    const [branch, setBranch] = useState("");
    const [liftingQty, setLiftingQty] = useState("");
    const [selectedMonth, setSelectedMonth] = useState("");
    const [selectedMonthValue, setSelectedMonthValue] = useState("");

    // Screen data loaded once
    const [monthList, setMonthList] = useState([]);
    const [screenReady, setScreenReady] = useState(false);

    // UI state
    const [cutOffMsg, setCutoffMsg] = useState("");
    const [loading, setLoading] = useState(false);
    const [monthModalVisible, setMonthModalVisible] = useState(false);
    const [cutOffModalVisible, setCutOffModalVisible] = useState(false);
    const [authChecker, setAuthChecker] = useState(false);

    const isMounted = useRef(true);
    const timeoutRef = useRef(null);

    useEffect(() => {
        isMounted.current = true;
        loadInitialData();
        return () => {
            isMounted.current = false;
            if (timeoutRef.current) clearTimeout(timeoutRef.current);
        };
    }, []);

    // Load dealer details + month list in parallel, then apply results together.
    // Previously, separate fetches each called setState individually — on iOS every
    // setState during mount triggers a layout pass which can revoke first responder
    // (keyboard) from a TextInput the user just tapped. Promise.all + a single
    // update batch eliminates those extra layout passes.
    const loadInitialData = async () => {
        setLoading(true);
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
        try {
            const [dealerRes, monthRes] = await Promise.all([
                fetch(
                    UrlStorage.BaseUrlList.Saathi.base_url_saathi +
                    UrlStorage.NonAuthURL.Saathi.OtherURL.api_get_exclusive_dealername +
                    "?customer_id=" + UrlStorage.ParameterList.BasicData.emp_id,
                    { method: "GET" }
                ),
                fetch(
                    UrlStorage.BaseUrlList.Saathi.base_url_saathi +
                    UrlStorage.NonAuthURL.Saathi.OtherURL.api_exclusive_get_month_list +
                    "?customer_id=" + UrlStorage.ParameterList.BasicData.emp_id,
                    { method: "GET" }
                ),
            ]);

            const dealerData = await dealerRes.json();
            const monthData = await monthRes.json();

            if (!isMounted.current) return;

            setDealerName(dealerData?.customer_name ?? "");
            setBranch(dealerData?.branch_name ?? "");
            setMonthList(monthData?.months ?? []);
            setScreenReady(true);
        } catch (error) {
            console.error("loadInitialData error:", error);
            if (isMounted.current) setScreenReady(true);
        } finally {
            if (isMounted.current) setLoading(false);
        }
    };

    const showToast = useCallback((type, text1, text2) => {
        Toast.show({ type, text1, text2 });
    }, []);

    const validateForm = useCallback(() => {
        if (!dealerName) { showToast("error", "Error", "Dealer Name cannot be empty"); return false; }
        if (!selectedMonth) { showToast("error", "Error", "Please select a Month"); return false; }
        if (!branch) { showToast("error", "Error", "Branch cannot be empty"); return false; }
        if (!liftingQty) { showToast("error", "Error", "Lifting Quantity cannot be empty"); return false; }
        return true;
    }, [dealerName, selectedMonth, branch, liftingQty, showToast]);

    const submitDeclaration = useCallback(async () => {
        if (!validateForm()) return;

        const authOk = await AuthCheckingApi();
        if (!authOk) {
            setAuthChecker(true);
            return false;
        }

        setLoading(true);
        const url =
            UrlStorage.BaseUrlList.Saathi.base_url_saathi +
            UrlStorage.NonAuthURL.Saathi.OtherURL.api_exclusive_data_save;

        const body = {
            customer_id: UrlStorage.ParameterList.BasicData.emp_id,
            user_type: UrlStorage.ParameterList.BasicData.user_type,
            month: selectedMonth,
            dealer_name: dealerName,
            branch,
            lifting_qty: liftingQty,
        };

        try {
            const response = await fetch(url, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(body),
            });
            const result = await response.json();

            if (result?.process_status === "Yes") {
                showToast("success", "Success", "Dealer Declaration Submitted Successfully");
                navigation.pop();
            } else {
                showToast(
                    "error",
                    result?.process_message ?? "Error",
                    result?.error ?? "Something went wrong. Please try again later"
                );
            }
        } catch (error) {
            showToast("error", "Error", "Something went wrong. Please try again later");
        } finally {
            if (isMounted.current) setLoading(false);
        }
    }, [validateForm, dealerName, branch, liftingQty, showToast, navigation]);

    const handleSelectMonth = useCallback((item) => {
        setMonthModalVisible(false);
        setSelectedMonth(item.month_name);
        setCutoffMsg(item.message ?? "");

        if (item.is_applied || !item.cutoff) {
            timeoutRef.current = setTimeout(() => {
                if (isMounted.current) setCutOffModalVisible(true);
            }, 300);
        }
    }, []);

    const handleClose = useCallback(() => navigation.pop(), [navigation]);

    const openMonthModal = useCallback(() => {
        setCutOffModalVisible(false);
        timeoutRef.current = setTimeout(() => {
            if (isMounted.current) setMonthModalVisible(true);
        }, 250);
    }, []);

    const renderMonthItem = useCallback(
        ({ item }) => <MonthItem item={item} onPress={handleSelectMonth} />,
        [handleSelectMonth]
    );

    return (
        <SafeView style={styles.container} statusbarColor={Colors.main}>
            <SBSCommonHeaderView title="Dealer Declaration" />

            {/* KeyboardAvoidingView wraps both the scroll area and the submit button.
                On iOS, any layout recalculation inside a ScrollView that is NOT inside
                a KeyboardAvoidingView will revoke first responder from a focused
                TextInput, causing the keyboard to flash and dismiss. */}
            <KeyboardAvoidingView
                style={styles.flex1}
                behavior={Platform.OS === "ios" ? "padding" : undefined}
                keyboardVerticalOffset={Platform.OS === "ios" ? 88 : 0}
            >
                <ScrollView
                    style={styles.scrollContainer}
                    contentContainerStyle={SCROLL_CONTENT_STYLE}
                    keyboardShouldPersistTaps="handled"
                    keyboardDismissMode="none"
                    removeClippedSubviews={false}
                >
                    <View style={styles.letterheadSection}>
                        <Text style={styles.recipientLabel}>To</Text>
                        <Text style={styles.recipientValue}>ASM/RSM/ZH</Text>
                    </View>

                    <View style={styles.greetingSection}>
                        <Text style={styles.greetingLabel}>Dear Sir,</Text>
                        <Text style={styles.descriptionText}>
                            {" "}This is to inform you that I am continuing as an Exclusive dealer of STAR Cement and not lifting cement for any other company.{" "}
                        </Text>
                    </View>

                    <View style={styles.formSection}>
                        <View style={styles.formGroup}>
                            <Text style={styles.fieldLabel}>Dealer Name</Text>
                            <TextInput
                                style={styles.input}
                                value={dealerName}
                                onChangeText={setDealerName}
                                placeholder="Enter Dealer Name"
                                placeholderTextColor={Colors.grey}
                                autoCorrect={false}
                                autoCapitalize="words"
                            />
                        </View>

                        <View style={styles.formGroup}>
                            <Text style={styles.fieldLabel}>Branch</Text>
                            <TextInput
                                style={styles.input}
                                value={branch}
                                onChangeText={setBranch}
                                placeholder="Enter Branch"
                                placeholderTextColor={Colors.grey}
                                autoCorrect={false}
                            />
                        </View>

                        <View style={styles.formGroup}>
                            <Text style={styles.fieldLabel}>Lifting qty (in MT)</Text>
                            <TextInput
                                style={styles.input}
                                value={liftingQty}
                                onChangeText={setLiftingQty}
                                placeholder="Enter Lifting Qty"
                                placeholderTextColor={Colors.grey}
                                keyboardType="numeric"
                                maxLength={8}
                            />
                        </View>

                        <View style={styles.monthSelectorGroup}>
                            <Text style={styles.fieldLabel}>Select Month</Text>
                            <TouchableOpacity
                                style={[styles.monthSelector, selectedMonth ? styles.monthSelectorActive : null]}
                                onPress={() => setMonthModalVisible(true)}
                                activeOpacity={0.7}
                            >
                                <Text style={selectedMonth ? styles.monthSelectorTextActive : styles.monthSelectorText}>
                                    {" "}{selectedMonth || "Tap to select month"}{" "}
                                </Text>
                                <Text style={styles.monthSelectorArrow}>▼</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </ScrollView>

                <View style={styles.submitButtonContainer}>
                    <TouchableOpacity
                        style={[styles.submitBtn, !screenReady && styles.submitBtnDisabled]}
                        onPress={submitDeclaration}
                        activeOpacity={0.85}
                        disabled={!screenReady}
                    >
                        <Text style={styles.submitText}>Submit Declaration</Text>
                    </TouchableOpacity>
                </View>
            </KeyboardAvoidingView>

            {/* Month picker — bottom sheet modal */}
            <Modal
                visible={monthModalVisible}
                transparent
                animationType="slide"
                onRequestClose={() => setMonthModalVisible(false)}
                hardwareAccelerated={true}
            >
                <View style={styles.monthModalOverlay}>
                    <TouchableOpacity
                        style={styles.modalBackdrop}
                        activeOpacity={1}
                        onPress={() => setMonthModalVisible(false)}
                    />
                    <View style={styles.monthModalContainer}>
                        <View style={styles.monthModalHeader}>
                            <Text style={styles.monthModalTitle}>Select Month</Text>
                            <TouchableOpacity
                                onPress={() => setMonthModalVisible(false)}
                                hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
                                activeOpacity={0.7}
                            >
                                <Text style={styles.closeIcon}>✕</Text>
                            </TouchableOpacity>
                        </View>
                        <FlatList
                            data={monthList}
                            keyExtractor={monthKeyExtractor}
                            renderItem={renderMonthItem}
                            initialNumToRender={8}
                            maxToRenderPerBatch={8}
                            windowSize={5}
                            removeClippedSubviews={false}
                            ItemSeparatorComponent={() => <View style={styles.itemSeparator} />}
                            scrollEventThrottle={16}
                            keyboardShouldPersistTaps="handled"
                        />
                    </View>
                </View>
            </Modal>

            {/* Cut-off / already applied alert modal */}
            <Modal
                visible={cutOffModalVisible}
                transparent
                animationType="fade"
                onRequestClose={() => setCutOffModalVisible(false)}
                hardwareAccelerated={true}
            >
                <View style={styles.alertModalOverlay}>
                    <View style={styles.alertModalContainer}>
                        <View style={styles.alertHeader}>
                            <View style={styles.alertIconContainer}>
                                <Text style={styles.alertIcon}>⚠</Text>
                            </View>
                            <Text style={styles.alertTitle}>{cutOffMsg}</Text>
                        </View>

                        <Text style={styles.alertMessage}>
                            {" "}Selected month is not valid. Please choose another to continue.{" "}
                        </Text>

                        <View style={styles.alertButtonRow}>
                            <TouchableOpacity
                                style={styles.alertSecondaryBtn}
                                onPress={handleClose}
                                activeOpacity={0.8}
                            >
                                <Text style={styles.alertSecondaryBtnText}>Close</Text>
                            </TouchableOpacity>

                            <TouchableOpacity
                                style={styles.alertPrimaryBtn}
                                onPress={openMonthModal}
                                activeOpacity={0.8}
                            >
                                <Text style={styles.alertPrimaryBtnText}>Select Again</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
            </Modal>

            <Toast config={toastConfig} />
            {loading && <Loader />}
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    );
}

const styles = StyleSheet.create({
    flex1: { flex: 1 },
    container: { flex: 1, backgroundColor: "#f8f9fa" },
    scrollContainer: { flex: 1, paddingHorizontal: 16 },

    letterheadSection: {
        marginTop: 24,
        marginBottom: 28,
        paddingBottom: 20,
        borderBottomWidth: 1.5,
        borderBottomColor: "#e0e0e0",
    },
    recipientLabel: { fontSize: 12, color: "#757575", letterSpacing: 0.5, marginBottom: 4, fontWeight: "500" },
    recipientValue: { fontSize: 18, fontWeight: "700", color: "#1a1a1a", letterSpacing: 0.3 },

    greetingSection: { marginBottom: 32 },
    greetingLabel: { fontSize: 14, fontWeight: "600", color: "#1a1a1a", marginBottom: 8 },
    descriptionText: { fontSize: 13.5, color: "#424242", lineHeight: 20, letterSpacing: 0.2 },

    formSection: { marginBottom: 8 },
    formGroup: { marginBottom: 20 },
    fieldLabel: { fontSize: 12.5, color: "#1a1a1a", fontWeight: "600", marginBottom: 8, letterSpacing: 0.3 },
    input: {
        height: 44,
        borderRadius: 8,
        backgroundColor: "#fff",
        paddingHorizontal: 14,
        fontSize: 14,
        color: "#1a1a1a",
        borderWidth: 1,
        borderColor: "#e0e0e0",
        fontWeight: "500",
    },

    monthSelectorGroup: { marginBottom: 20, marginTop: 24 },
    monthSelector: {
        height: 44,
        borderRadius: 8,
        backgroundColor: "#fff",
        paddingHorizontal: 14,
        borderWidth: 1.5,
        borderColor: "#e0e0e0",
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
    },
    monthSelectorActive: { borderColor: "#363f45", backgroundColor: "#f5f5f5" },
    monthSelectorText: { fontSize: 14, color: "#999", fontWeight: "500" },
    monthSelectorTextActive: { fontSize: 14, color: "#1a1a1a", fontWeight: "600" },
    monthSelectorArrow: { fontSize: 10, color: "#757575", fontWeight: "700" },

    submitButtonContainer: {
        paddingHorizontal: 16,
        paddingBottom: 16,
        paddingTop: 12,
        backgroundColor: "#fff",
        borderTopWidth: 1,
        borderTopColor: "#e0e0e0",
    },
    submitBtn: {
        height: 48,
        backgroundColor: "#363f45",
        alignItems: "center",
        justifyContent: "center",
        borderRadius: 8,
        elevation: 2,
    },
    submitBtnDisabled: { opacity: 0.5 },
    submitText: { color: "#fff", fontWeight: "700", fontSize: 15, letterSpacing: 0.3 },

    // Month modal — bottom sheet
    monthModalOverlay: { flex: 1, justifyContent: "flex-end" },
    modalBackdrop: {
        ...StyleSheet.absoluteFillObject,
        backgroundColor: "rgba(0,0,0,0.55)",
    },
    monthModalContainer: {
        width: "100%",
        backgroundColor: "#fff",
        borderTopLeftRadius: 24,
        borderTopRightRadius: 24,
        maxHeight: "85%",
    },
    monthModalHeader: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
        paddingHorizontal: 20,
        paddingVertical: 16,
        borderBottomWidth: 1,
        borderBottomColor: "#f0f0f0",
    },
    monthModalTitle: { fontSize: 16, fontWeight: "700", color: "#1a1a1a" },
    closeIcon: { fontSize: 22, color: "#999", fontWeight: "600" },
    monthItem: { paddingVertical: 0, backgroundColor: "#fff" },
    monthItemContent: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
        paddingHorizontal: 20,
        paddingVertical: 14,
    },
    monthText: { fontSize: 14.5, color: "#1a1a1a", fontWeight: "500", flex: 1 },
    monthIcon: { width: 24, height: 24, alignItems: "center", justifyContent: "center" },
    chevronIcon: { fontSize: 18, color: "#ccc", fontWeight: "300" },
    itemSeparator: { height: 1, backgroundColor: "#f5f5f5", marginHorizontal: 20 },

    // Alert modal — centred
    alertModalOverlay: {
        flex: 1,
        backgroundColor: "rgba(0,0,0,0.55)",
        justifyContent: "center",
        alignItems: "center",
        paddingHorizontal: 20,
    },
    alertModalContainer: {
        width: "100%",
        backgroundColor: "#fff",
        borderRadius: 16,
        padding: 24,
        elevation: 12,
    },
    alertHeader: { flexDirection: "row", alignItems: "center", marginBottom: 16 },
    alertIconContainer: {
        width: 44,
        height: 44,
        borderRadius: 22,
        backgroundColor: "#fff3e0",
        alignItems: "center",
        justifyContent: "center",
        marginRight: 12,
    },
    alertIcon: { fontSize: 24, fontWeight: "700" },
    alertTitle: { fontSize: 15, fontWeight: "700", color: "#1a1a1a", flex: 1, lineHeight: 22 },
    alertMessage: { fontSize: 13.5, color: "#424242", lineHeight: 20, marginBottom: 24, letterSpacing: 0.2 },
    alertButtonRow: { flexDirection: "row", justifyContent: "flex-end", gap: 10 },
    alertSecondaryBtn: {
        paddingVertical: 11,
        paddingHorizontal: 24,
        borderRadius: 8,
        borderWidth: 1.5,
        borderColor: "#e0e0e0",
        backgroundColor: "#f8f9fa",
    },
    alertSecondaryBtnText: { fontSize: 14, fontWeight: "600", color: "#424242" },
    alertPrimaryBtn: { paddingVertical: 11, paddingHorizontal: 24, borderRadius: 8, backgroundColor: "#363f45" },
    alertPrimaryBtnText: { fontSize: 14, fontWeight: "600", color: "#fff" },
});