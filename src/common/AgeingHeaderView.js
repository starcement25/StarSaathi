import React, { useEffect } from 'react';
import { Alert, BackHandler, Image, Text, TouchableOpacity, View } from 'react-native';
import { moderateScale } from '../helper/Window';
import { Icons } from '../assets/Icons';
import { useNavigation } from '@react-navigation/native';
import UrlStorage from '../storage/UrlStorage';

const AgeingHeaderView = (props) => {
    const { title, subTitle = '', Information = false } = props;
    const navigation = useNavigation();

    const handleBackPress = () => {
        navigation.goBack();
    };

    return (
        <View style={{ width: "100%", height: moderateScale(60), padding: moderateScale(10), backgroundColor: "#E41B14", flexDirection: "row", alignItems: "center", justifyContent: "center", }} >
            <TouchableOpacity activeOpacity={0.95} onPress={handleBackPress}>
                <View style={{ width: moderateScale(30), height: moderateScale(30), borderRadius: moderateScale(20), borderWidth: moderateScale(1), borderColor: "#FFFFFF", alignItems: "center", justifyContent: "center", }} >
                    <Image source={Icons.Back} style={{ width: moderateScale(12), height: moderateScale(12), tintColor: "#FFFFFF", }} />
                </View>
            </TouchableOpacity>
            <View style={{ flex: 1, flexDirection: 'column', paddingHorizontal: moderateScale(15), justifyContent: 'center' }}>
                <Text style={{ color: "#FFFFFF", fontSize: moderateScale(16), fontWeight: "600" }}>
                    {title}
                </Text>
                {subTitle != '' ? <Text style={{ color: "#FFFFFF", fontSize: moderateScale(10), fontWeight: "400" }}>
                    {subTitle}
                </Text> : null}
            </View>
            <View style={{ flexDirection: "row", gap: moderateScale(10) }}>
                {Information ? <TouchableOpacity onPress={() => gotoLink()}>
                    <Image source={Icons.Information} style={{ width: moderateScale(20), height: moderateScale(20) }} />
                </TouchableOpacity> : null}
            </View>
        </View>
    );
};

export default AgeingHeaderView;
