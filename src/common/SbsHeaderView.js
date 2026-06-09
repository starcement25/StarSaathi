import React, { useEffect, useState } from 'react'
import { Image, Text, TouchableOpacity, View } from 'react-native'
import { Icons } from '../assets/Icons'
import { moderateScale } from '../helper/Window'
import DataStorage from '../storage/DataStorage'
import UrlStorage from '../storage/UrlStorage'
import { Colors } from '../assets/Colors'

const SbsHeaderView = ({ handleOpenSBSMenu, openDealerList, customerDetails, handleRefresh, navigateToNotification }) => {
    const [title, setTitle] = useState()
    useEffect(() => {
        if (!customerDetails?.customer_name || !UrlStorage.ParameterList.BasicData.selectedCustomerCode) {
            if (DataStorage.typeOfUse == 1) {
                setTitle("Star Saathi - SBS")
            } else {
                setTitle("Star Saathi - Cement")
            }
        } else {
            setTitle(customerDetails?.customer_name)
        }
    }, [customerDetails?.customer_name])
    return (
        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between", paddingVertical: moderateScale(10), paddingHorizontal: moderateScale(10) }}>
            <TouchableOpacity style={{ padding: 8 }} activeOpacity={0.95} onPress={() => { handleOpenSBSMenu() }}>
                <Image source={Icons.Menu} style={{ width: moderateScale(15), height: moderateScale(15), tintColor: "#FFFFFF" }} />
            </TouchableOpacity>
            <Text numberOfLines={1} style={{ position: 'absolute', marginHorizontal: moderateScale(70), left: 0, right: 0, color: Colors.white, fontWeight: '600', fontSize: moderateScale(14), textAlign: 'center', zIndex: 10, }} > {title} </Text>
            <View style={{ flexDirection: 'row' }}>
                <TouchableOpacity activeOpacity={0.95} onPress={() => { navigateToNotification() }}>
                    <Image source={Icons.Notification} style={{ width: moderateScale(20), height: moderateScale(20), tintColor: "#FFFFFF" }} />
                </TouchableOpacity>
                {UrlStorage.ParameterList.BasicData.user_type == "broker" && <TouchableOpacity style={{ marginStart: 10 }} activeOpacity={0.95} onPress={() => { openDealerList() }}>
                    <Image source={Icons.AddIcon} style={{ width: moderateScale(20), height: moderateScale(20), tintColor: "#FFFFFF" }} />
                </TouchableOpacity>}
                <TouchableOpacity style={{ marginStart: moderateScale(10) }} activeOpacity={0.95} onPress={() => { handleRefresh() }}>
                    <Image source={Icons.RefreshIcon} style={{ width: moderateScale(22), height: moderateScale(22), tintColor: "#FFFFFF" }} />
                </TouchableOpacity>
            </View>
        </View>
    )
}

export default SbsHeaderView
