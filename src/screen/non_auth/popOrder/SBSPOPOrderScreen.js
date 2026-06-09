import React, { useEffect, useState } from "react";
import { FlatList, Image, StyleSheet, Text, TextInput, TouchableOpacity, View, } from "react-native";
import SafeView from "../../../helper/SafeView";
import SBSCommonHeaderView from "../../../common/SBSCommonHeaderView";
import { moderateScale } from "../../../helper/Window";
import { Icons } from "../../../assets/Icons";
import { Colors } from "../../../assets/Colors";
import DataStorage from "../../../storage/DataStorage";
import UrlStorage from "../../../storage/UrlStorage";
import Toast from "react-native-toast-message";
import toastConfig from "../../../helper/ToastConfig";
import Loader from "../../../common/Loader";
import { AuthCheckingApi } from "../../../auth/AuthCheckingApi";
import AuthNotVerifyPopupView from "../../../auth/AuthNotVerifyPopupView";

const SBSPOPOrderScreen = (props) => {
  const [appOrder, setAppOrder] = useState(false);
  const [loading, setLoading] = useState(false);
  const [authChecker, setAuthChecker] = useState(false);
  const [popOrderList, setPopOrderList] = useState([]);
  const [productList, setProductList] = useState([]);
  const inputRefs = [];

  useEffect(() => {
    requestForProductList();
    requestForOrderHistory();
  }, []);

  const fetchAuthData = async () => {
    var a = await AuthCheckingApi();
    if (!a) {
      setAuthChecker(true)
      setLoading(false)
    }
  }

  const requestForProductList = async () => {
    const requestOptions = {
      method: "GET",
      redirect: "follow",
    };
    var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
    var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.ProductURL.pop_product_list_url;
    url = url + "?customer_code=" + UrlStorage.ParameterList.BasicData.emp_id + "&user_type=" + UrlStorage.ParameterList.BasicData.user_type;
    setLoading(true);
    await fetch(url, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        if (result.process_status == "YES") {
          setProductList(
            result.pop_product_date.map((item) => ({ ...item, count: 0 }))
          );
        }
      })
      .catch((error) => { });
    setLoading(false);
  };
  const onChangeTextHandler = (text, index) => {
    setProductList((prevList) =>
      prevList.map((item, i) => (i === index ? { ...item, count: text } : item))
    );
  };
  const decrementHandler = (item, index) => {
    setProductList((prevList) =>
      prevList.map((item, i) =>
        i === index ? { ...item, count: parseInt(item.count) != 0 || parseInt(item.count) != "" ? parseInt(item.count) - 1 : parseInt(item.count), } : item
      )
    );
  };
  const incrementHandler = (item, index) => {
    setProductList((prevList) =>
      prevList.map((item, i) =>
        i === index ? { ...item, count: item.count == "" ? 1 : parseInt(item.count) + 1 } : item
      )
    );
  };
  const requestForOrderHistory = async () => {
    const requestOptions = {
      method: "GET",
      redirect: "follow",
    };
    var a = await AuthCheckingApi();
        if (!a) {
            setAuthChecker(true)
            setLoading(false)
            return false
        }
    var url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.ProductURL.show_pop_order_list;
    url = url + "?customer_code=" + UrlStorage.ParameterList.BasicData.emp_code;
    setLoading(true);
    await fetch(url, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        if (result.process_status == "YES") {
          setPopOrderList([]);
        } else {
          setPopOrderList([]);
        }
      })
      .catch((error) => {
        setPopOrderList([]);
      });
    setLoading(false);
  };

  const checkDataAndGotoCartPage = () => {
    var arr = [];
    for (var i = 0; i < productList.length; i++) {
      if (productList[i].count != "" || productList[i].count != 0) {
        arr.push(productList[i]);
      }
    }
    DataStorage.popProductList = arr;
    if (arr.length > 0) {
      let isValid = true;

      for (let i = 0; i < arr.length; i++) {
        if (arr[i].count < arr[i].min_order_qty) {
          isValid = false;
          alert(`Minimum order qty for ${arr[i].prod_desc} is ${arr[i].min_order_qty}`);
          break;
        }
      }

      if (isValid) {
        props.navigation.navigate("CartScreen", { arr });
      }
    } else {
      Toast.show({ type: "error", text1: "Sorry", text2: "Please atlease select one product", });
    }
  };

  const renderProductItem = (item, index) => {
    return (
      <TouchableOpacity onPress={() => { props.navigation.navigate(item.screen) }} activeOpacity={0.95} style={{ flex: 0.5, margin: moderateScale(5) }} >
        <View style={{ width: "100%", backgroundColor: "#FFFFFF", borderWidth: moderateScale(1), borderColor: "#DCDDDF", elevation: 4, shadowColor: DataStorage.primaryColorCode, shadowRadius: moderateScale(20), shadowOffset: ({ x: 0 }, { y: 6 }), borderRadius: moderateScale(10), overflow: "hidden", gap: moderateScale(4), }} >
          <Image source={{ uri: item.prod_image }} style={{ width: "100%", height: moderateScale(100) }} />
          <View style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(6), }} >
            <Text style={{ color: Colors.text, fontSize: moderateScale(12), fontWeight: "600", }} numberOfLines={2} >
              {item.prod_desc}
            </Text>
            <View style={{ width: "100%", flexDirection: "row", alignItems: "center", gap: moderateScale(10), }} >
              <View style={{ paddingVertical: moderateScale(4), paddingHorizontal: moderateScale(8), borderRadius: moderateScale(6), backgroundColor: DataStorage.primaryColorCode, }} >
                <Text style={{ color: "#ffffff", fontSize: moderateScale(12), fontWeight: "400", }} >
                  ₹{item.price_per_piece}
                </Text>
              </View>
              <Text style={{ color: "#7D7D7D", fontSize: moderateScale(12), fontWeight: "400", }} >
                GST {item.GST_rate}%
              </Text>
            </View>
            <Text style={{ color: Colors.text, fontSize: moderateScale(13), fontWeight: "700", }} >
              <Text style={{ color: "#7D7D7D", fontSize: moderateScale(12), fontWeight: "400", }} >
                Min Order Qty{" "}
              </Text>
              {item.min_order_qty}
            </Text>
            <View style={{ paddingHorizontal: moderateScale(10), borderRadius: moderateScale(10), borderWidth: moderateScale(1), borderColor: DataStorage.primaryColorCode, flexDirection: "row", alignItems: "center", justifyContent: "space-between", gap: moderateScale(12), }} >
              <TouchableOpacity onPress={() => { decrementHandler(item, index); }} activeOpacity={0.95} >
                <Image source={Icons.MinusSign} style={{ width: moderateScale(12), height: moderateScale(12), tintColor: DataStorage.primaryColorCode, }} />
              </TouchableOpacity>
              <TextInput
                value={item.count.toString() == "0" ? "" : item.count.toString()}
                onChangeText={(text) => onChangeTextHandler(text, index)}
                keyboardType="number-pad"
                placeholder="QTY"
                placeholderTextColor={Colors.grey}
                ref={(ref) => (inputRefs[index] = ref)}
                numberOfLines={1}
                style={{ color: "#333", width: moderateScale(50), fontSize: moderateScale(12), textAlign: "center", height: moderateScale(40), }} />
              <TouchableOpacity onPress={() => { incrementHandler(item, index); }} activeOpacity={0.95} >
                <Image source={Icons.AddSign} style={{ width: moderateScale(12), height: moderateScale(16), tintColor: DataStorage.primaryColorCode, }} />
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </TouchableOpacity>
    );
  };
  const renderOrderItem = (item, index) => {
    return (
      <View style={styles.card}>
        <View style={styles.container}>
          <Image source={item.imageSource} style={styles.image} />

          <View style={styles.rightContent}>
            <View style={styles.row}>
              <Text style={styles.label}>Main Order Id :</Text>
              <Text style={styles.value}>{item.mainOrderId}</Text>
            </View>

            <View style={styles.row}>
              <Text style={styles.label}>Order Id :</Text>
              <Text style={[styles.value, styles.bold]}>{item.orderId}</Text>
            </View>

            <View style={styles.row}>
              <Text style={styles.label}>Date :</Text>
              <Text style={styles.value}>{item.date}</Text>
            </View>

            <View style={styles.row}>
              <Text style={styles.label}>Product :</Text>
              <Text style={styles.value}>{item.product}</Text>
            </View>

            <View style={styles.row}>
              <Text style={styles.label}>Qty :</Text>
              <Text style={styles.value}>{item.qty}</Text>
            </View>

            <View style={styles.row}>
              <Text style={styles.label}>Total Amount :</Text>
              <Text style={styles.value}>{item.totalAmount}</Text>
            </View>

            <View style={styles.row}>
              <Text style={styles.label}>Address :</Text>
              <Text style={styles.value}>{item.address}</Text>
            </View>

            <View style={styles.row}>
              <Text style={styles.label}>Order Status :</Text>
              <Text style={styles.value}>{item.orderStatus}</Text>
            </View>
          </View>
        </View>
      </View>
    );
  }

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main} >
      <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }} >
        <SBSCommonHeaderView title="Pop Product" backPath=" " Cart={false} />
        <View style={{ width: "100%", padding: moderateScale(10), gap: moderateScale(10), flex: 1, }} >
          <View style={{ width: "100%", flex: 1 }}>
            <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "center", }} >
              <TouchableOpacity onPress={() => setAppOrder(false)} activeOpacity={0.95} style={{ flex: 1 }} >
                <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: appOrder === false ? DataStorage.primaryColorCode : DataStorage.transColorCode, borderWidth: moderateScale(1), borderColor: "#DCDDDF", }} >
                  <Text style={{ color: appOrder === false ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600", }} >
                    POP Order
                  </Text>
                </View>
              </TouchableOpacity>
              <TouchableOpacity onPress={() => setAppOrder(true)} activeOpacity={0.95} style={{ flex: 1 }} >
                <View style={{ width: "100%", padding: moderateScale(10), alignItems: "center", justifyContent: "center", borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), backgroundColor: appOrder ? DataStorage.primaryColorCode : DataStorage.transColorCode, borderWidth: moderateScale(1), borderColor: "#DCDDDF", }} >
                  <Text style={{ color: appOrder ? Colors.white : Colors.text, fontSize: moderateScale(14), fontWeight: "600", }} >
                    Order History
                  </Text>
                </View>
              </TouchableOpacity>
            </View>
            <View style={{ width: "100%", flex: 1, backgroundColor: "#FFFFFF", borderBottomWidth: moderateScale(1), borderLeftWidth: moderateScale(1), borderRightWidth: moderateScale(1), borderColor: "#DCDDDF", padding: moderateScale(10), }} >
              <FlatList
                data={appOrder ? popOrderList : productList}
                numColumns={2}
                keyExtractor={(item, index) => index}
                showsVerticalScrollIndicator={false}
                decelerationRate="fast"
                columnWrapperStyle={{ justifyContent: "space-between" }}
                renderItem={({ item, index }) => (
                  <>
                    {appOrder ? renderOrderItem(item, index) : renderProductItem(item, index)}
                  </>
                )}
              />
            </View>
          </View>
          <TouchableOpacity onPress={() => { checkDataAndGotoCartPage() }} activeOpacity={0.95} >
            <View style={{ width: "100%", paddingHorizontal: moderateScale(15) }} >
              <View style={{ width: "100%", height: moderateScale(40), flexDirection: "row", gap: moderateScale(8), backgroundColor: DataStorage.primaryColorCode, borderRadius: moderateScale(10), alignItems: "center", justifyContent: "center", }} >
                <Text style={{ color: Colors.white, fontSize: moderateScale(16), fontWeight: 500, }} >
                  Check Out
                </Text>
              </View>
            </View>
          </TouchableOpacity>
        </View>
      </View>
      {loading && <Loader />}
      <Toast config={toastConfig} />
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  );
};

export default SBSPOPOrderScreen;

const styles = StyleSheet.create({
  card: { margin: 5, backgroundColor: "#fff", borderRadius: 5, elevation: 5, shadowColor: "#000", shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.2, shadowRadius: 3, },
  container: { flexDirection: "row", backgroundColor: "#fff", margin: 3, },
  image: { width: 80, height: 80, alignSelf: "center", resizeMode: "cover", },
  rightContent: { flex: 1, padding: 9, justifyContent: "center", },
  row: { flexDirection: "row", alignItems: "center", paddingVertical: 3, },
  label: { fontSize: 10, color: "grey", textTransform: "uppercase", },
  value: { flex: 1, fontSize: 12, color: "black", marginLeft: 5, },
  bold: { fontWeight: "bold", },
});
