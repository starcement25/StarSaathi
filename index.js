import messaging from '@react-native-firebase/messaging';
import { AppRegistry } from 'react-native';
import App from './App';
import { name as appName } from './app.json';

// Must be outside your React component, registered as early as possible
messaging().setBackgroundMessageHandler(async remoteMessage => {
    console.log('[FCM] Message handled in the background:', remoteMessage);
});

AppRegistry.registerComponent(appName, () => App);