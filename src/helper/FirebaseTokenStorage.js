// import { getApp, initializeApp, getApps } from '@react-native-firebase/app';
// import { getMessaging, getToken } from '@react-native-firebase/messaging';

// const firebaseConfig = {
//   apiKey: 'your-api-key',
//   authDomain: 'your-auth-domain',
//   projectId: 'your-project-id',
//   storageBucket: 'your-storage-bucket',
//   messagingSenderId: 'your-messaging-sender-id',
//   appId: 'your-app-id',
// };

// const initializeFirebase = () => {
//   if (getApps().length === 0) {
//     initializeApp(firebaseConfig);
//   }
//   return getApp();
// };

// const fetchAndSaveFirebaseToken = async () => {
//   try {
//     const app = initializeFirebase();
//     const messagingInstance = getMessaging(app);
//     const token = await getToken(messagingInstance);
//     await set_firebase_token(token);
//   } catch (e) {
//     await set_firebase_token('dummy');
//   }
// };