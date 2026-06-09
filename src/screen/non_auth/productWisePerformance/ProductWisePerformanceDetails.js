import React, { useState, useEffect, useRef } from "react";
import { Text, FlatList, StyleSheet, Animated, LayoutAnimation, UIManager, Platform, } from "react-native";
import SafeView from "../../../helper/SafeView";
import { Colors } from "../../../assets/Colors";
import SBSCommonHeaderView from "../../../common/SBSCommonHeaderView";
import { AuthCheckingApi } from "../../../auth/AuthCheckingApi";
import AuthNotVerifyPopupView from "../../../auth/AuthNotVerifyPopupView";

/* Enable LayoutAnimation on Android */
if (Platform.OS === "android" && UIManager.setLayoutAnimationEnabledExperimental) {
    UIManager.setLayoutAnimationEnabledExperimental(true);
}

const DECIMALS = 2;

/* ---------- Helpers ---------- */
const toNum = (v) => {
    const n = Number(v);
    return Number.isFinite(n) ? n : 0;
};
const fmt = (v) => toNum(v).toFixed(DECIMALS);

const ProductWisePerformanceDetails = ({ navigation, route }) => {
    const { allData } = route?.params || {};
    const [data] = useState(Array.isArray(allData) ? allData : []);
    const [authChecker, setAuthChecker] = useState(false);

    /* ---------- Animations ---------- */
    const headerAnim = useRef(new Animated.Value(0)).current;
    const listAnim = useRef(new Animated.Value(0)).current;
    const footerAnim = useRef(new Animated.Value(0)).current;
    const rowAnimations = useRef([]).current;

    useEffect(() => {
        /* Prepare row anims */
        rowAnimations.length = 0;
        data.forEach(() => rowAnimations.push(new Animated.Value(0)));

        /* Smooth layout pass */
        LayoutAnimation.configureNext(LayoutAnimation.Presets.easeInEaseOut);

        Animated.sequence([
            // Header
            Animated.timing(headerAnim, {
                toValue: 1,
                duration: 300,
                useNativeDriver: true,
            }),

            // FlatList container
            Animated.timing(listAnim, {
                toValue: 1,
                duration: 300,
                useNativeDriver: true,
            }),

            // Rows stagger
            Animated.stagger(
                40,
                rowAnimations.map(anim =>
                    Animated.timing(anim, {
                        toValue: 1,
                        duration: 280,
                        useNativeDriver: true,
                    })
                )
            ),

            // Footer
            Animated.timing(footerAnim, {
                toValue: 1,
                duration: 250,
                useNativeDriver: true,
            }),
        ]).start();
    }, [data]);
    const fetchAuthData = async () => {
        var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
        }
    }

    /* ---------- Render Row ---------- */
    const renderItem = ({ item, index }) => {
        const anim = rowAnimations[index] || new Animated.Value(1);

        return (
            <Animated.View style={[styles.row, {
                opacity: anim,
                transform: [
                    { translateY: anim.interpolate({ inputRange: [0, 1], outputRange: [18, 0], }), },
                    { scale: anim.interpolate({ inputRange: [0, 1], outputRange: [0.97, 1], }), },
                ],
            },]} >
                <Text style={styles.cell}>{item?.itemname || "-"}</Text>
                <Text style={styles.cell}>SCL</Text>
                <Text style={styles.cell}>{fmt(item?.TGTQTY)}</Text>
                <Text style={styles.cell}>{fmt(item?.ACHQTY)}</Text>
            </Animated.View>
        );
    };

    /* ---------- Totals ---------- */
    const totalTarget = data.reduce((s, i) => s + toNum(i?.TGTQTY), 0);
    const totalAchieved = data.reduce((s, i) => s + toNum(i?.ACHQTY), 0);

    const renderFooter = () => (
        <Animated.View
            style={[
                styles.row,
                styles.totalRow,
                {
                    opacity: footerAnim,
                    transform: [
                        { translateY: footerAnim.interpolate({ inputRange: [0, 1], outputRange: [16, 0], }), },
                    ],
                },]} >
            <Text style={[styles.cell, styles.totalLabel]}>Total</Text>
            <Text style={styles.cell} />
            <Text style={[styles.cell, styles.totalValue]}>{fmt(totalTarget)}</Text>
            <Text style={[styles.cell, styles.totalValue]}>{fmt(totalAchieved)}</Text>
        </Animated.View>
    );

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <SBSCommonHeaderView title="Details" backPath=" " navigation={navigation} />

            {/* Animated Header Row */}
            <Animated.View
                style={[
                    styles.row,
                    styles.tableHeader,
                    {
                        opacity: headerAnim,
                        transform: [
                            { translateY: headerAnim.interpolate({ inputRange: [0, 1], outputRange: [-12, 0], }), },
                        ],
                    },]} >
                <Text style={[styles.cell, styles.headerCell]}>Prod. Name</Text>
                <Text style={[styles.cell, styles.headerCell]}>Type</Text>
                <Text style={[styles.cell, styles.headerCell]}>Target</Text>
                <Text style={[styles.cell, styles.headerCell]}>Achievement</Text>
            </Animated.View>

            {/* Animated FlatList container */}
            <Animated.View
                style={{
                    flex: 1,
                    opacity: listAnim,
                    transform: [
                        { translateY: listAnim.interpolate({ inputRange: [0, 1], outputRange: [20, 0], }), },
                    ],
                }} >
                <FlatList
                    data={data}
                    renderItem={renderItem}
                    keyExtractor={(_, index) => index.toString()}
                    ListFooterComponent={renderFooter}
                    showsVerticalScrollIndicator={false} />
            </Animated.View>
            <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
        </SafeView>
    );
};

const styles = StyleSheet.create({
    row: { flexDirection: "row", borderBottomWidth: 1, borderBottomColor: "#ddd", paddingVertical: 10, paddingHorizontal: 6, backgroundColor: "#fff", },
    cell: { flex: 1, textAlign: "center", fontSize: 12, color: "#000", },
    tableHeader: { backgroundColor: "#f0f0f0", borderBottomWidth: 2, },
    headerCell: { fontWeight: "bold", color: "#000", },
    totalRow: { backgroundColor: "#fff5f5", borderTopWidth: 2, borderTopColor: "#E41B14", marginTop: 4, },
    totalLabel: { fontWeight: "bold", color: "#000", },
    totalValue: { fontWeight: "bold", color: "#E41B14", },
});

export default ProductWisePerformanceDetails;
