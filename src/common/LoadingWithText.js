import React from 'react'
import { ActivityIndicator, Text, View } from 'react-native'
import { moderateScale } from '../helper/Window'

const LoadingWithText = (props) => {
    return (
        <View style={{ position: 'absolute', top: 0, left: 0, right: 0, bottom: 0, backgroundColor: 'rgba(0,0,0,0.4)', alignItems: 'center', justifyContent: 'center', zIndex: 1000 }}>
            <View style={{ width: '80%', backgroundColor: '#FFF', borderRadius: moderateScale(5), flexDirection: 'row', alignItems: 'center', justifyContent: 'center', paddingHorizontal: moderateScale(20), paddingVertical: moderateScale(20) }}>
                <ActivityIndicator size={"large"} color={"red"} />
                <View style={{ width: moderateScale(20) }} />
                <Text style={{ fontSize: moderateScale(14), color: '#000', fontStyle: 'italic', flex: 1 }}>{props.message}</Text>
            </View>
        </View>
    )
}

export default LoadingWithText
