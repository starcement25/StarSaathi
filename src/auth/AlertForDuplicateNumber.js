import React, { useState } from 'react'
import { Text, TouchableOpacity, View, Dimensions, StyleSheet } from 'react-native'
import Modal from 'react-native-modal'
import { moderateScale } from '../helper/Window'
import { Colors } from '../assets/Colors'
import { useNavigation } from '@react-navigation/native'
import Svg, { Circle, Path } from 'react-native-svg'

const AlertForDuplicateNumber = (props) => {
    const navigation = useNavigation()
    const { width } = Dimensions.get('window')
    const modalWidth = Math.min(width * 0.85, 380)
    const [shouldNavigate, setShouldNavigate] = useState(false)

    const handleLoginPress = () => {
        setShouldNavigate(true)
        if (props.onClose) props.onClose()
    }

    const handleModalHide = () => {
        if (shouldNavigate) {
            setShouldNavigate(false)
            navigation.navigate('LoginScreen')
        }
    }

    const ShieldIcon = () => (
        <Svg width={moderateScale(32)} height={moderateScale(32)} viewBox="0 0 24 24" fill="none">
            <Path
                d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"
                stroke="#fff"
                strokeWidth="1.5"
                fill="#fff"
                fillOpacity="0.75"
            />
            <Path
                d="M12 8v4M12 16h.01"
                stroke="#fff"
                strokeWidth="2.2"
                strokeLinecap="round"
                fill="none"
            />
        </Svg>
    )

    const InfoIcon = () => (
        <Svg width={moderateScale(15)} height={moderateScale(15)} viewBox="0 0 24 24" fill="none">
            <Circle cx="12" cy="12" r="10" stroke="#E7000B" strokeWidth="2" />
            <Path
                d="M12 8v4M12 16h.01"
                stroke="#E7000B"
                strokeWidth="2"
                strokeLinecap="round"
            />
        </Svg>
    )

    return (
        <Modal
            isVisible={props.isVisible}
            style={styles.modal}
            backdropOpacity={0.6}
            useNativeDriver={true}
            hideModalContentWhileAnimating={true}
            animationIn="zoomIn"
            animationOut="zoomOut"
            animationInTiming={300}
            animationOutTiming={300}
            onModalHide={handleModalHide}
        >
            <View style={styles.overlay}>
                <View style={[styles.card, { width: modalWidth }]}>

                    {/* ── Header ── */}
                    <View style={styles.header}>
                        <View style={styles.iconCircle}>
                            <ShieldIcon />
                        </View>
                        <Text style={styles.title}>Warning</Text>
                    </View>

                    {/* ── Body ── */}
                    <View style={styles.body}>
                        <Text style={styles.message}>
                            The mobile no. is registered with another customer. Please confirm to login.
                        </Text>

                        <View style={styles.divider} />

                        <View style={styles.infoStrip}>
                            <View style={styles.infoIcon}>
                                <InfoIcon />
                            </View>
                            <Text style={styles.infoText}>
                                Contact to admin for keep your account secure
                            </Text>
                        </View>
                    </View>

                    {/* ── Button ── */}
                    <View style={styles.footer}>
                        <TouchableOpacity
                            onPress={props.loginPress}
                            activeOpacity={0.8}
                            style={styles.button}
                        >
                            <Text style={styles.buttonText}>Log In Anyway</Text>
                        </TouchableOpacity>
                    </View>

                </View>
            </View>
        </Modal>
    )
}

const styles = StyleSheet.create({
    modal: {
        margin: 0,
    },
    overlay: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
        paddingHorizontal: moderateScale(16),
    },
    card: {
        backgroundColor: '#FFFFFF',
        borderRadius: moderateScale(16),
        // Shadow split from overflow — no overflow:hidden needed
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.2,
        shadowRadius: 12,
        elevation: 8,
    },
    header: {
        backgroundColor: '#E7000B',
        borderTopLeftRadius: moderateScale(16),
        borderTopRightRadius: moderateScale(16),
        paddingVertical: moderateScale(28),
        paddingHorizontal: moderateScale(20),
        alignItems: 'center',
        gap: moderateScale(14),
    },
    iconCircle: {
        width: moderateScale(66),
        height: moderateScale(66),
        borderRadius: moderateScale(33),
        backgroundColor: 'rgba(255, 255, 255, 0.18)',
        alignItems: 'center',
        justifyContent: 'center',
    },
    title: {
        fontSize: moderateScale(20),
        fontWeight: '600',
        color: '#FFFFFF',
        letterSpacing: 0.3,
    },
    body: {
        paddingHorizontal: moderateScale(24),
        paddingTop: moderateScale(24),
        paddingBottom: moderateScale(4),
    },
    message: {
        fontSize: moderateScale(13),
        lineHeight: moderateScale(22),
        color: '#555555',
        textAlign: 'center',
        fontWeight: '400',
        marginBottom: moderateScale(20),
    },
    divider: {
        height: 1,
        backgroundColor: '#F0F0F0',
        marginBottom: moderateScale(20),
    },
    infoStrip: {
        flexDirection: 'row',
        alignItems: 'flex-start',
        backgroundColor: '#FFF3F3',
        borderLeftWidth: 3,
        borderLeftColor: '#E7000B',
        paddingHorizontal: moderateScale(12),
        paddingVertical: moderateScale(10),
        marginBottom: moderateScale(20),
    },
    infoIcon: {
        marginRight: moderateScale(8),
        marginTop: moderateScale(2),
    },
    infoText: {
        fontSize: moderateScale(12),
        color: '#555555',
        lineHeight: moderateScale(18),
        flex: 1,
    },
    footer: {
        paddingHorizontal: moderateScale(24),
        paddingBottom: moderateScale(24),
    },
    button: {
        height: moderateScale(48),
        backgroundColor: '#E7000B',
        borderRadius: moderateScale(12),
        alignItems: 'center',
        justifyContent: 'center',
    },
    buttonText: { color: '#FFFFFF', fontWeight: '600', fontSize: moderateScale(16), letterSpacing: 0.3, },
})

export default AlertForDuplicateNumber
