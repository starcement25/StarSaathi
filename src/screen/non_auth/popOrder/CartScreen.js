import React, { useEffect, useState } from 'react'
import { FlatList, Image, Text, TouchableOpacity, View } from 'react-native'
import SafeView from '../../../helper/SafeView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import DataStorage from '../../../storage/DataStorage'

const CartScreen = (props) => {
    const [productList, setProductList] = useState(DataStorage.popProductList)
    const [totalPrice, setTotalPrice] = useState(0)
    const { arr } = props.route.params

    useEffect(() => {
        var total = 0
        for (var i = 0; i < productList.length; i++) {
            total += parseFloat(productList[i].price_per_piece * productList[i].count + productList[i].price_per_piece * productList[i].count * productList[i].GST_rate / 100)
        }
        setTotalPrice(total)
    }, [productList])

    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Pop Product" backPath=" " />
                <View style={{ width: "100%", padding: moderateScale(20), gap: moderateScale(10), flex: 1 }}>
                    <FlatList
                        data={productList}
                        keyExtractor={(item) => item.id}
                        showsVerticalScrollIndicator={false}
                        decelerationRate="fast"
                        renderItem={({ item, index }) => (
                            <>
                                {item.count == 0 ? null : <View style={{ width: "100%", gap: moderateScale(10), padding: moderateScale(10), borderRadius: moderateScale(10), borderWidth: moderateScale(1), borderColor: "#DCDDDF", marginBottom: moderateScale(14) }}>
                                    <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                                        <View style={{ flexDirection: "row", alignItems: "center", justifyContent: "space-between", gap: moderateScale(10) }}>
                                            <Image source={{ uri: item.prod_image }} style={{ width: moderateScale(60), height: moderateScale(60), borderRadius: moderateScale(10), resizeMode: 'contain' }} />
                                            <View style={{ alignItems: "flex-start", gap: moderateScale(4) }}>
                                                <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500" }}>{item.prod_desc}</Text>
                                                <Text style={{ color: "#7D7D7D", fontSize: moderateScale(12) }}>{item.count} Pic</Text>
                                            </View>
                                        </View>
                                        <View style={{ paddingVertical: moderateScale(4), paddingHorizontal: moderateScale(6), borderRadius: moderateScale(10), borderWidth: moderateScale(1), borderColor: DataStorage.primaryColorCode, backgroundColor: DataStorage.transColorCode, flexDirection: "row", alignItems: "center", justifyContent: "space-between", gap: moderateScale(14) }}>
                                            <Text style={{ color: "#000000", fontSize: moderateScale(16), fontWeight: "500", paddingHorizontal: moderateScale(10) }}>{item.count}</Text>
                                        </View>
                                    </View>
                                    <View style={{ width: "100%", gap: moderateScale(8), padding: moderateScale(10), borderRadius: moderateScale(10), backgroundColor: DataStorage.transColorCode }}>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>Mrp</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(15), fontWeight: "500" }}>₹{parseFloat(item.price_per_piece).toFixed(2)}</Text>
                                        </View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>Price</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(15), fontWeight: "500" }}>₹{parseFloat(item.price_per_piece * item.count).toFixed(2)}</Text>
                                        </View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(13) }}>GST 18%</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(15), fontWeight: "500" }}>₹{parseFloat(item.price_per_piece * item.count * item.GST_rate / 100).toFixed(2)}</Text>
                                        </View>
                                        <View style={{ width: "100%", borderWidth: moderateScale(0.4), borderColor: "#7D7D7D", borderStyle: "dashed" }}></View>
                                        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(15), fontWeight: "600" }}>Total</Text>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(15), fontWeight: "600" }}>₹{parseFloat(item.price_per_piece * item.count + item.price_per_piece * item.count * item.GST_rate / 100).toFixed(2)}</Text>
                                        </View>
                                    </View>
                                </View>}
                            </>
                        )}
                    />
                </View>
                <View style={{ width: "100%", paddingHorizontal: moderateScale(15), marginBottom: moderateScale(20) }}>
                    <View style={{ width: "100%", height: moderateScale(44), flexDirection: "row", gap: moderateScale(8), backgroundColor: DataStorage.transColorCode, borderRadius: moderateScale(10), alignItems: "center", justifyContent: "space-between", paddingHorizontal: moderateScale(10) }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(16), fontWeight: 500 }}>Grand Total</Text>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(16), fontWeight: 500 }}>₹{parseFloat(totalPrice).toFixed(2)}</Text>
                    </View>
                </View>
                <TouchableOpacity activeOpacity={0.95} onPress={() => {
                    DataStorage.popProductList = productList
                    props.navigation.navigate("PaymentScreen", { arr })
                }}>
                    <View style={{ width: "100%", paddingHorizontal: moderateScale(15), marginBottom: moderateScale(20) }}>
                        <View style={{ width: "100%", height: moderateScale(40), flexDirection: "row", gap: moderateScale(8), backgroundColor: DataStorage.primaryColorCode, borderRadius: moderateScale(10), alignItems: "center", justifyContent: "center" }}>
                            <Text style={{ color: Colors.white, fontSize: moderateScale(16), fontWeight: 500 }}>Continue</Text>
                        </View>
                    </View>
                </TouchableOpacity>
            </View>
        </SafeView>
    )
}

export default CartScreen
