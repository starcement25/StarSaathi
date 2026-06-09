// import messaging from '@react-native-firebase/messaging';
// import notifee, {
//   AndroidImportance,
//   AndroidStyle,
//   EventType,
// } from '@notifee/react-native';

// /* =====================================================
//    ANDROID NOTIFICATION CHANNEL
//    (maps to NotificationChannel in Java)
// ===================================================== */
// export async function createNotificationChannel() {
//   return await notifee.createChannel({
//     id: '1000001010', // same as default_notification_channel_id
//     name: 'Star Cement Notifications',
//     vibration: true,
//     importance: AndroidImportance.DEFAULT,
//   });
// }

// /* =====================================================
//    FIREBASE PERMISSION (Android 13+ & iOS)
// ===================================================== */
// export async function requestNotificationPermission() {
//   const status = await messaging().requestPermission();
//   const enabled =
//     status === messaging.AuthorizationStatus.AUTHORIZED ||
//     status === messaging.AuthorizationStatus.PROVISIONAL;


//   return enabled;
// }

// /* =====================================================
//    FIREBASE TOKEN (maps to onNewToken)
// ===================================================== */
// export async function getFirebaseToken() {
//   const token = await messaging().getToken();


//   // TODO: send token to backend OR store locally
//   // same as set_firebase_token(...)
//   return token;
// }

// /* =====================================================
//    TOKEN REFRESH LISTENER
// ===================================================== */
// export function listenTokenRefresh() {
//   return messaging().onTokenRefresh(token => {

//     // TODO: update backend
//   });
// }

// /* =====================================================
//    CORE NOTIFICATION HANDLER
//    (maps to handleDataMessage + showBig/Small)
// ===================================================== */
// async function showNotification(remoteMessage) {
//   const channelId = await createNotificationChannel();

//   const title = remoteMessage?.notification?.title || '';
//   const message = remoteMessage?.notification?.body || '';
//   const imageUrl =
//     remoteMessage?.notification?.android?.imageUrl ||
//     remoteMessage?.notification?.image;

//   let style = null;

//   // 🔁 SAME LOGIC AS YOUR JAVA CODE
//   if (message.length > 35) {
//     style = {
//       type: AndroidStyle.BIGTEXT,
//       text: message,
//     };
//   } else if (imageUrl && imageUrl.length > 4) {
//     style = {
//       type: AndroidStyle.BIGPICTURE,
//       picture: imageUrl,
//     };
//   }

//   await notifee.displayNotification({
//     title,
//     body: message,
//     android: {
//       channelId,
//       smallIcon: 'ic_launcher', // same as R.mipmap.ic_launcher
//       pressAction: {
//         id: 'default',
//         launchActivity: 'default', // SplashActivity equivalent
//       },
//       style,
//     },
//   });
// }

// /* =====================================================
//    FOREGROUND MESSAGE
//    (maps to onMessageReceived when app is open)
// ===================================================== */
// export function registerForegroundHandler() {
//   return messaging().onMessage(async remoteMessage => {

//     await showNotification(remoteMessage);
//   });
// }

// /* =====================================================
//    BACKGROUND / KILLED MESSAGE
// ===================================================== */
// export function registerBackgroundHandler() {
//   messaging().setBackgroundMessageHandler(async remoteMessage => {

//     await showNotification(remoteMessage);
//   });
// }

// /* =====================================================
//    NOTIFICATION CLICK HANDLER
// ===================================================== */
// export function registerNotificationClickHandler(onPress) {
//   return notifee.onForegroundEvent(({ type, detail }) => {
//     if (type === EventType.PRESS) {

//       onPress?.(detail.notification);
//     }
//   });
// }
