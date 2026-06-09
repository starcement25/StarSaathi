import React from 'react'
import { Image, Text, View } from 'react-native'
import SafeView from '../../../helper/SafeView'
import { Colors } from '../../../assets/Colors'
import { moderateScale } from '../../../helper/Window'
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView'
import { Icons } from '../../../assets/Icons'

const MasonLiftingScreen = (props) => {
    return (
        <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
            <View style={{ width: "100%", height: "100%", backgroundColor: Colors.white }}>
                <SBSCommonHeaderView title="Mason Lifting" backPath=" " />
                <View style={{ width: "100%", padding: moderateScale(20), gap: moderateScale(20), flex: 1, alignItems: "center" }}>
                    <Image source={Icons.MasonLifting} style={{ width: moderateScale(220), height: moderateScale(220) }} />
                    <View style={{ width: "100%", padding: moderateScale(20), backgroundColor: "#FFFFFF", borderRadius: moderateScale(20), elevation: 10 }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(16), fontWeight: "600", textAlign: "center" }}>Pending Liftings</Text>
                    </View>
                    <View style={{ width: "100%", padding: moderateScale(20), backgroundColor: "#FFFFFF", borderRadius: moderateScale(20), elevation: 10 }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(16), fontWeight: "600", textAlign: "center" }}>Approved Liftings</Text>
                    </View>
                    <View style={{ width: "100%", padding: moderateScale(20), backgroundColor: "#FFFFFF", borderRadius: moderateScale(20), elevation: 10 }}>
                        <Text style={{ color: Colors.text, fontSize: moderateScale(16), fontWeight: "600", textAlign: "center" }}>Rejected Liftings</Text>
                    </View>
                </View>
            </View>
        </SafeView>
    )
}

export default MasonLiftingScreen
