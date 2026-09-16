import { BaseToast, ErrorToast, InfoToast } from "react-native-toast-message"

const toastConfig = {
  success: (props) => (
    <BaseToast
      {...props}
      style={{ borderLeftColor: 'green' }}
      contentContainerStyle={{ paddingHorizontal: 15 }}
      text1Style={{ fontSize: 12, fontWeight: '400' }}
      text2NumberOfLines={3} />
  ),
  error: (props) => (
    <ErrorToast
      {...props}
      style={{ borderLeftColor: 'red' }}
      text1Style={{ fontSize: 12 }}
      text2Style={{ fontSize: 12 }}
      text2NumberOfLines={3} />
  ),
  info: (props) => (
    <InfoToast
      {...props}
      style={{ borderLeftColor: 'yellow' }}
      text1Style={{ fontSize: 12 }}
      text2Style={{ fontSize: 12 }}
      text2NumberOfLines={3} />
  )
}

export default toastConfig