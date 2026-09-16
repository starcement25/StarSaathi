import { ActivityIndicator, View } from "react-native"

const Loader = () => {
  return (
    <View style={{ position: 'absolute', top: 0, left: 0, right: 0, bottom: 0, backgroundColor: 'rgba(0,0,0,0.4)', alignItems: 'center', justifyContent: 'center', zIndex: 1000 }}>
      <ActivityIndicator size={"large"} color={"red"} />
    </View>
  )
}

export default Loader