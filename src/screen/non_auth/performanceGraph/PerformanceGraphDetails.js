import React, { useState, useEffect, useRef } from "react"
import { View, Text, FlatList, StyleSheet, Animated, } from "react-native"
import SafeView from "../../../helper/SafeView"
import { Colors } from "../../../assets/Colors"
import SBSCommonHeaderView from "../../../common/SBSCommonHeaderView"
import UrlStorage from "../../../storage/UrlStorage"

const PerformanceGraphDetails = ({ navigation, route }) => {
    const { barData, appOrder, subDealerId } = route?.params || {}
    const [selectedTab, setSelectedTab] = useState(appOrder == 1 ? "previousYear" : "currentYear")

    const getPairedData = (tab) => {
        const activeData = barData?.[tab] || []
        const pairedData = []
        if (activeData.length && activeData[0]?.target !== undefined) {
            return activeData.map(item => ({
                month: item.label,
                target: item.target ?? 0,
                achievement: item.value ?? 0,
            }))
        }
        for (let i = 0; i < barData.length; i += 2) {
            const first = barData[i]
            const second = barData[i + 1]
            pairedData.push({
                month: first?.label ?? '-',
                target: first?.value ?? 0,
                achievement: second?.value ?? 0,
            })
        }
        return pairedData
    }

    const [pairedData, setPairedData] = useState(getPairedData(selectedTab))
    const animatedValues = useRef([]).current

    useEffect(() => {
        const newData = getPairedData(selectedTab)
        setPairedData(newData)
        animatedValues.length = 0
        newData.forEach(() => animatedValues.push(new Animated.Value(0)))
        const animations = newData.map((_, index) =>
            Animated.timing(animatedValues[index], {
                toValue: 1,
                duration: 300,
                delay: index * 10,
                useNativeDriver: true,
            })
        )
        Animated.stagger(30, animations).start()
    }, [selectedTab])

    const renderItem = ({ item, index }) => (
        <Animated.View
            style={[
                styles.row,
                {
                    opacity: animatedValues[index] || 1,
                    transform: [
                        { translateY: (animatedValues[index] || new Animated.Value(1)).interpolate({ inputRange: [0, 1], outputRange: [20, 0], }), },
                    ],
                },]} >
            <Text style={styles.cell}>{item.month}</Text>
            <Text style={styles.cell}>{item.target.toFixed(2)}</Text>
            <Text style={styles.cell}>{item.achievement.toFixed(2)}</Text>
        </Animated.View>
    )

    const totalTarget = pairedData.reduce((sum, item) => sum + item.target, 0)
    const totalAchieved = pairedData.reduce((sum, item) => sum + item.achievement, 0)

    const renderFooter = () => (
        <View style={[styles.row, styles.totalRow]}>
            <Text style={[styles.cell, styles.totalLabel]}>Total</Text>
            <Text style={[styles.cell, styles.totalValue]}>{totalTarget.toFixed(2)}</Text>
            <Text style={[styles.cell, styles.totalValue]}>{totalAchieved.toFixed(2)}</Text>
        </View>
    )

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <SBSCommonHeaderView title={subDealerId == UrlStorage.ParameterList.BasicData.emp_code ? "Details" : subDealerId?.customer_code} backPath=" " navigation={navigation} />
            <View style={[styles.row, styles.tableHeader]}>
                <Text style={[styles.cell, styles.headerCell]}>Month</Text>
                <Text style={[styles.cell, styles.headerCell]}>Target (MT)</Text>
                <Text style={[styles.cell, styles.headerCell]}>Achieved (MT)</Text>
            </View>
            <FlatList
                data={pairedData}
                renderItem={renderItem}
                keyExtractor={(_, index) => index.toString()}
                ListFooterComponent={renderFooter}
            />
        </SafeView>
    )
}

const styles = StyleSheet.create({
    tabContainer: { flexDirection: "row", marginVertical: 10 },
    tab: { flex: 1, paddingVertical: 10, borderBottomWidth: 2, borderBottomColor: "transparent", alignItems: "center", },
    activeTab: { borderBottomColor: "red" },
    tabText: { fontSize: 14, color: "gray" },
    activeTabText: { color: "red", fontWeight: "bold" },
    row: { flexDirection: "row", borderBottomWidth: 1, borderBottomColor: "#ddd", paddingVertical: 8 },
    cell: { flex: 1, textAlign: "center", fontSize: 14, color: "#000" },
    tableHeader: { backgroundColor: "#f0f0f0" },
    headerCell: { fontWeight: "bold", color: "black" },
    totalRow: { backgroundColor: "#ffeaea", borderTopWidth: 2, borderTopColor: "red" },
    totalLabel: { fontWeight: "bold", color: "black" },
    totalValue: { fontWeight: "bold", color: "red" },
})

export default PerformanceGraphDetails
