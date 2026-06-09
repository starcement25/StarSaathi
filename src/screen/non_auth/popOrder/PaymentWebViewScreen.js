import React, { useEffect, useRef } from 'react';
import { Alert, BackHandler, View } from 'react-native';
import { WebView } from 'react-native-webview';
import SafeView from '../../../helper/SafeView';
import { Colors } from '../../../assets/Colors';
import SBSCommonHeaderView from '../../../common/SBSCommonHeaderView';

const PaymentWebViewScreen = ({ route, navigation }) => {
  const { link } = route.params;
  const webviewRef = useRef(null);

  useEffect(() => {
    const backHandler = BackHandler.addEventListener('hardwareBackPress', () => {
      Alert.alert(
        'Exit Transaction',
        'Do you want to exit the transaction?',
        [
          { text: 'No', style: 'cancel' },
          { text: 'Yes', onPress: () => navigation.pop() },
        ],
        { cancelable: true }
      );
      return true;
    });

    return () => backHandler.remove();
  }, [navigation]);

  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <View style={{ flex: 1, backgroundColor: Colors.white }}>
        <SBSCommonHeaderView title="Payment" backPath="payment" />
        <WebView
          ref={webviewRef}
          source={{ uri: link }}
          style={{ flex: 1 }}
          startInLoadingState={true}
          javaScriptEnabled={true}
          domStorageEnabled={true}
          scalesPageToFit={true}
          scrollEnabled={true}
          nestedScrollEnabled={true}
        />
      </View>
    </SafeView>
  );
};

export default PaymentWebViewScreen;
