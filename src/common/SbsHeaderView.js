import React, { useEffect, useState } from 'react'
import { Image, Text, TouchableOpacity, View } from 'react-native'
import { Icons } from '../assets/Icons'
import { moderateScale } from '../helper/Window'
import DataStorage from '../storage/DataStorage'
import UrlStorage from '../storage/UrlStorage'
import { Colors } from '../assets/Colors'

const SbsHeaderView = ({ handleOpenSBSMenu, openDealerList, customerDetails, handleRefresh, navigateToNotification, handleContactPerson, notificationCount }) => {
    const [title, setTitle] = useState()
    useEffect(() => {
        if (!customerDetails?.customer_name || !UrlStorage.ParameterList.BasicData.selectedCustomerCode) {
            if (DataStorage.typeOfUse == 1)
                setTitle("Star Saathi - SBS")
            else
                setTitle("Star Saathi - Cement")
        } else
            setTitle(customerDetails?.customer_name)
    }, [customerDetails?.customer_name])
    return (
        <View style={{ width: "100%", flexDirection: "row", alignItems: "center", justifyContent: "space-between", paddingVertical: moderateScale(10), paddingHorizontal: moderateScale(10) }}>
            <TouchableOpacity style={{ padding: 8 }} activeOpacity={0.95} onPress={() => { handleOpenSBSMenu() }}>
                <Image source={Icons.Menu} style={{ width: moderateScale(15), height: moderateScale(15), tintColor: "#FFFFFF" }} />
            </TouchableOpacity>
            <Text numberOfLines={1} style={{ position: 'absolute', marginHorizontal: moderateScale(70), left: 0, right: 0, color: Colors.white, fontWeight: '600', fontSize: moderateScale(14), textAlign: 'center' }} > {title} </Text>
            <View style={{ flexDirection: 'row' }}>
                <TouchableOpacity onPress={() => { navigateToNotification() }} style={{ width: moderateScale(20), height: moderateScale(20) }}>
                    <Image source={Icons.Notification} style={{ width: moderateScale(20), height: moderateScale(20), tintColor: "#FFFFFF" }} />
                    {notificationCount > 0 &&
                        <View style={{ position: 'absolute', flexDirection: 'row-reverse', width: '100%', marginTop: -moderateScale(3) }}>
                            <View style={{ width: moderateScale(10), height: moderateScale(10), backgroundColor: Colors.white, borderRadius: moderateScale(10), alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ fontSize: moderateScale(8), fontWeight: '500', color: '#000' }}>{notificationCount}</Text>
                            </View>
                        </View>}
                </TouchableOpacity>
                {UrlStorage.ParameterList.BasicData.user_type == "broker" && <TouchableOpacity style={{ marginStart: 10 }} activeOpacity={0.95} onPress={() => { openDealerList() }}>
                    <Image source={Icons.AddIcon} style={{ width: moderateScale(20), height: moderateScale(20), tintColor: "#FFFFFF" }} />
                </TouchableOpacity>}
                <TouchableOpacity style={{ marginStart: moderateScale(10) }} activeOpacity={0.95} onPress={() => { handleRefresh() }}>
                    <Image source={Icons.RefreshIcon} style={{ width: moderateScale(22), height: moderateScale(22), tintColor: "#FFFFFF" }} />
                </TouchableOpacity>
                {UrlStorage.ParameterList.BasicData.user_type != "broker" && <TouchableOpacity style={{ marginStart: moderateScale(10) }} activeOpacity={0.95} onPress={() => { handleContactPerson() }}>
                    <Image source={Icons.StartCallIcon} style={{ width: moderateScale(20), height: moderateScale(20), tintColor: "#FFFFFF" }} />
                </TouchableOpacity>}
            </View>
        </View>
    )
}

export default SbsHeaderView
