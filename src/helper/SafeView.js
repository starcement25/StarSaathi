import React, { useEffect, useState } from 'react'
import { SafeAreaView, StatusBar, KeyboardAvoidingView, Platform, View, Pressable, Keyboard, } from 'react-native'
import { useSafeAreaInsets } from 'react-native-safe-area-context'
import PropTypes from 'prop-types'
import { Colors } from '../assets/Colors'

export default function SafeView({ children, backgroundColor, bar, statusbarColor, dismissOnPress = false, avoidKeyboard = true, isValid = false, }) {
  const insets = useSafeAreaInsets()
  const [keyboardVisible, setKeyboardVisible] = useState(false)
  const [keyboardHeight, setKeyboardHeight] = useState(0)

  useEffect(() => {
    const showListener = Keyboard.addListener('keyboardDidShow', (event) => {
      setKeyboardVisible(true)
      setKeyboardHeight(event.endCoordinates.height)

    })

    const hideListener = Keyboard.addListener('keyboardDidHide', () => {
      setKeyboardVisible(false)
      setKeyboardHeight(0)
    })

    return () => {
      showListener.remove()
      hideListener.remove()
    }
  }, [])

  const statusBarHeight = Platform.OS === 'android' ? StatusBar.currentHeight : insets.top
  const bottomInset = Platform.OS === 'android' ? insets.bottom : 0

  const content = (
    <>
      <View style={{ height: statusBarHeight, backgroundColor: statusbarColor, }} />
      <StatusBar translucent backgroundColor="transparent" barStyle={!bar ? 'light-content' : 'dark-content'} hidden={false} />
      <SafeAreaView
        edges={Platform.OS === 'android' ? ['bottom', 'left', 'right'] : ['top', 'bottom', 'left', 'right']}
        style={{ flex: 1, backgroundColor, paddingBottom: Platform.OS === 'android' ? keyboardHeight > 0 && !isValid ? keyboardHeight - bottomInset : bottomInset : 0, }} >
        <View style={{ flex: 1 }}>{children}</View>
      </SafeAreaView>
    </>
  )

  if (dismissOnPress) {
    return (
      <KeyboardAvoidingView style={{ flex: 1 }} behavior={Platform.OS === 'ios' ? 'padding' : 'height'} >
        <Pressable style={{ flex: 1 }} onPress={Keyboard.dismiss}>
          {content}
        </Pressable>
      </KeyboardAvoidingView>
    )
  }
  if (!avoidKeyboard) {
    return <View style={{ flex: 1 }}>{content}</View>
  }
  return (
    <KeyboardAvoidingView style={{ flex: 1 }} behavior={Platform.OS === 'ios' ? 'padding' : undefined} keyboardVerticalOffset={Platform.OS === 'ios' ? 0 : statusBarHeight ?? 0} >
      {content}
    </KeyboardAvoidingView>
  )
}

SafeView.propTypes = {
  backgroundColor: PropTypes.string,
  bar: PropTypes.bool,
  statusbarColor: PropTypes.string,
  dismissOnPress: PropTypes.bool,
}

SafeView.defaultProps = {
  backgroundColor: Colors.white,
  bar: false,
  statusbarColor: Colors.main,
  dismissOnPress: false,
}
