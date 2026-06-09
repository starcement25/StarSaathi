import React from 'react'
import { FlatList, Text, TouchableOpacity, TouchableWithoutFeedback, View } from 'react-native'
import Modal from 'react-native-modal'
import { moderateScale } from '../../../../helper/Window'
import { Colors } from '../../../../assets/Colors'
import DataStorage from '../../../../storage/DataStorage'

const ForTypeListPopupView = (props) => {
    return (
        <Modal
            isVisible={props.isVisible}
            style={{ margin: 0 }}
            customBackdrop={
                <TouchableWithoutFeedback onPress={() => props.closePopup()}>
                    <View style={{ flex: 1, backgroundColor: "black" }} />
                </TouchableWithoutFeedback>
            } >
            <View style={{ width: '100%', height: '100%', flexDirection: 'column' }}>
                <TouchableOpacity style={{ width: '100%', flex: 1 }} onPress={() => props.closePopup()} activeOpacity={1} >
                    <View style={{ width: '100%', height: '100%' }} />
                </TouchableOpacity>
                <View style={{ width: "100%", backgroundColor: Colors.main, borderTopLeftRadius: moderateScale(20), borderTopRightRadius: moderateScale(20) }}>
                    <View style={{ width: "100%", padding: moderateScale(20), backgroundColor: "#E41B14", flexDirection: "row", alignItems: "center", justifyContent: "space-between", borderTopLeftRadius: moderateScale(10), borderTopRightRadius: moderateScale(10) }}>
                        <Text style={{ color: "#FFFFFF", fontSize: moderateScale(16), fontWeight: "600" }}>
                            Select FOR Type
                        </Text>
                    </View>
                    <View style={{ width: "100%", backgroundColor: "#ffffff", paddingVertical: moderateScale(25), borderTopRightRadius: moderateScale(25), borderTopLeftRadius: moderateScale(25) }}>
                        <FlatList
                            data={props.dataList}
                            keyExtractor={(item) => item.id.toString()}
                            showsVerticalScrollIndicator={false}
                            decelerationRate="fast"
                            renderItem={({ item, index }) => {
                                return (
                                    <TouchableOpacity activeOpacity={0.95} onPress={() => props.selectItem(item)} >
                                        <View style={{ width: "100%", paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(13), flexDirection: "row", alignItems: "center", justifyContent: "space-between", backgroundColor: (index % 2 !== 0 ? DataStorage.transColorCode : "#FFFFFF") }}>
                                            <Text style={{ color: Colors.text, fontSize: moderateScale(14), fontWeight: "500", textTransform: "uppercase" }}>
                                                {item.title}
                                            </Text>
                                        </View>
                                    </TouchableOpacity>
                                );
                            }}
                        />
                    </View>
                </View>
            </View>
        </Modal>
    )
}

export default ForTypeListPopupView