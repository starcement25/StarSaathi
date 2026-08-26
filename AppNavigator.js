import React from 'react';
import { StatusBar } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';

// Splash Screen
import SplashScreen from './src/screen/splash/SplashScreen';

// Login Screen
import LoginScreen from './src/screen/auth/LoginScreen';

// OTP Screen
import OTPScreen from './src/screen/auth/OTPScreen';

// Home Screen
import HomeScreen from './src/screen/non_auth/HomeScreen';

// SBS Dashboard Screen
import SBSDashboardScreen from './src/screen/non_auth/dashboard/SBSDashboardScreen';

// SBS Order Screen
import OrderScreen from './src/screen/non_auth/order/OrderScreen';

//SBS Order Details Screen
import OrderDetailsScreen from './src/screen/non_auth/order/OrderDetailsScreen';

//SBS Order Confirm Screen
import OrderConfirmScreen from './src/screen/non_auth/order/OrderConfirmScreen';

//SBS Track Order Screen
import TrackOrderScreen from './src/screen/non_auth/trackOrder/TrackOrderScreen';

//SBS Ledger Screen
import LedgerScreen from './src/screen/non_auth/ledger/LedgerScreen';

//SBS Performance Graph Screen
import PerformanceGraphScreen from './src/screen/non_auth/performanceGraph/NewPerformanceGraphScreen';

//SBS Performance Wise Performance Screen
import ProductWiseProductScreen from './src/screen/non_auth/productWisePerformance/NewProductWiseProductScreen';

//SBS Lifting History Screen
import ListingHistoryScreen from './src/screen/non_auth/liftingHistory/ListingHistoryScreen';

//SBS Lifting Assigned Screen
import AssignedScreen from './src/screen/non_auth/liftingHistory/AssignedScreen';

//SBS Allocation Screen
import AllocationScreen from './src/screen/non_auth/liftingHistory/AllocationScreen';

//SBS POP Order Screen
import POPOrderScreen from './src/screen/non_auth/popOrder/POPOrderScreen';

//SBS POP cart Screen
import CartScreen from './src/screen/non_auth/popOrder/CartScreen';

//SBS POP Payment Screen
import PaymentScreen from './src/screen/non_auth/popOrder/PaymentScreen';

//SBS POP Address Screen
import AddressScreen from './src/screen/non_auth/popOrder/AddressScreen';

import MasonLiftingScreen from './src/screen/non_auth/masonLifting/MasonLiftingScreen';
import PendingInvoicesScreen from './src/screen/non_auth/pendingInvoices/PendingInvoicesScreen';
import OrderEnquiryScreen from './src/screen/non_auth/orderEnquiry/OrderEnquiryScreen';
import AgeingScreen from './src/screen/non_auth/ageing/AgeingScreen';
import FeedbackScreen from './src/screen/non_auth/feedback/FeedbackScreen';
import WebLinkScreen from './src/screen/non_auth/webLink/WebLinkScreen';
import FeedbackListScreen from './src/screen/non_auth/feedback/FeedbackListScreen';
import OtherScreen from './src/screen/non_auth/other/OtherScreen';
import PdfViewScreen from './src/screen/non_auth/ledger/PdfViewScreen';
import PerformanceGraphDetails from './src/screen/non_auth/performanceGraph/PerformanceGraphDetails';
import KycScreen from './src/screen/non_auth/kyc/KycScreen';
import SchemeScreen from './src/screen/non_auth/scheme/SchemeScreen';
import ProductWisePerformanceDetails from './src/screen/non_auth/productWisePerformance/ProductWisePerformanceDetails';
import HomeSliderScreen from './src/screen/non_auth/HomeSliderScreen';

import DealerDeclarationScreen from './src/screen/non_auth/declaration/DealerDeclarationScreen';
import DealerDeclarationHistoryScreen from './src/screen/non_auth/declaration/DealerDeclarationHistoryScreen';
import KismatKiBoriScreen from './src/screen/non_auth/bori/KismatKiBoriScreen';
import NotificationScreen from './src/screen/non_auth/notification/NotificationScreen';
import AddNewLiftingScreen from './src/screen/non_auth/liftingHistory/AddNewLiftingScreen';
import RssdLiftingAllocationScreen from './src/screen/non_auth/liftingHistory/RssdLiftingAllocationScreen';
import PaymentWebViewScreen from './src/screen/non_auth/popOrder/PaymentWebViewScreen';
import SBSPerformanceGraphScreen from './src/screen/non_auth/performanceGraph/SBSPerformanceGraphScreen';
import SBSPOPOrderScreen from './src/screen/non_auth/popOrder/SBSPOPOrderScreen';
import SBSSchemeScreen from './src/screen/non_auth/scheme/SBSSchemeScreen';
import SBSProductWiseProductScreen from './src/screen/non_auth/productWisePerformance/SBSProductWiseProductScreen';
import SchemeDetailsScreen from './src/screen/non_auth/scheme/SchemeDetailsScreen';

import PerformanceGraphSLCT from './src/screen/non_auth/slct/PerformanceGraphSLCT';

// Outstanding
import OutstandingSummaryScreen from './src/screen/non_auth/outstanding/OutStandingSummary';
import OverdueInvoicesScreen from './src/screen/non_auth/outstanding/OverdueInvoicesScreen';
import DeliveryTrackOrderScreen from './src/screen/non_auth/deliveryTrackOrder/DeliveryTrackOrderScreen';
import DeliveryTrackOrderDetails from './src/screen/non_auth/deliveryTrackOrder/DeliveryTrackOrderDetails';
import DeliveryComplainScreen from './src/screen/non_auth/deliveryTrackOrder/DeliveryComplainScreen';
import OverdueInvoicesListScreen from './src/screen/non_auth/outstanding/OverdueInvoicesListScreen';

import GSTScreen from './src/screen/non_auth/gst/GSTScreen';

const Stack = createNativeStackNavigator();
const AppNavigator = props => {
    return (
        <NavigationContainer>
            <StatusBar animated={true} backgroundColor="transparent" translucent={true} />
            <Stack.Navigator initialRouteName="MainStack" screenOptions={{ headerShown: false, gestureEnabled: false, }}>
                <Stack.Screen name="MainStack" component={MainStack} />
            </Stack.Navigator>
        </NavigationContainer>
    );
};
const MainStack = () => (
    <Stack.Navigator screenOptions={{ headerShown: false }} initialRouteName="SplashScreen">
        <Stack.Screen name="SplashScreen" component={SplashScreen} />
        <Stack.Screen name="LoginScreen" component={LoginScreen} />
        <Stack.Screen name="OTPScreen" component={OTPScreen} />
        <Stack.Screen name="HomeScreen" component={HomeScreen} />

        {/* SBS Designs */}
        <Stack.Screen name="SBSDashboardScreen" component={SBSDashboardScreen} />
        <Stack.Screen name="OrderScreen" component={OrderScreen} />
        <Stack.Screen name="OrderDetailsScreen" component={OrderDetailsScreen} />
        <Stack.Screen name="OrderConfirmScreen" component={OrderConfirmScreen} />
        <Stack.Screen name="TrackOrderScreen" component={TrackOrderScreen} />
        <Stack.Screen name="LedgerScreen" component={LedgerScreen} />
        <Stack.Screen name="PerformanceGraphScreen" component={PerformanceGraphScreen} />
        <Stack.Screen name="SBSPerformanceGraphScreen" component={SBSPerformanceGraphScreen} />
        <Stack.Screen name="ProductWiseProductScreen" component={ProductWiseProductScreen} />
        <Stack.Screen name="ListingHistoryScreen" component={ListingHistoryScreen} />
        <Stack.Screen name="AssignedScreen" component={AssignedScreen} />
        <Stack.Screen name="AllocationScreen" component={AllocationScreen} />
        <Stack.Screen name="POPOrderScreen" component={POPOrderScreen} />
        <Stack.Screen name="SBSPOPOrderScreen" component={SBSPOPOrderScreen} />
        <Stack.Screen name="SBSSchemeScreen" component={SBSSchemeScreen} />

        <Stack.Screen name="SBSProductWiseProductScreen" component={SBSProductWiseProductScreen} />
        <Stack.Screen name="CartScreen" component={CartScreen} />
        <Stack.Screen name="PaymentScreen" component={PaymentScreen} />
        <Stack.Screen name="AddressScreen" component={AddressScreen} />
        <Stack.Screen name="MasonLiftingScreen" component={MasonLiftingScreen} />
        <Stack.Screen name="PendingInvoicesScreen" component={PendingInvoicesScreen} />
        <Stack.Screen name="OrderEnquiryScreen" component={OrderEnquiryScreen} />
        <Stack.Screen name="AgeingScreen" component={AgeingScreen} />
        <Stack.Screen name="FeedbackScreen" component={FeedbackScreen} />
        <Stack.Screen name="FeedbackListScreen" component={FeedbackListScreen} />
        <Stack.Screen name="WebLinkScreen" component={WebLinkScreen} />
        <Stack.Screen name="OtherScreen" component={OtherScreen} />
        <Stack.Screen name="PdfViewScreen" component={PdfViewScreen} />
        <Stack.Screen name="PerformanceGraphDetails" component={PerformanceGraphDetails} />
        <Stack.Screen name="KycScreen" component={KycScreen} />
        <Stack.Screen name="SchemeScreen" component={SchemeScreen} />
        <Stack.Screen name="SchemeDetailsScreen" component={SchemeDetailsScreen} />
        <Stack.Screen name="ProductWisePerformanceDetails" component={ProductWisePerformanceDetails} />
        <Stack.Screen name="HomeSliderScreen" component={HomeSliderScreen} />
        <Stack.Screen name="DealerDeclarationScreen" component={DealerDeclarationScreen} />
        <Stack.Screen name="DealerDeclarationHistoryScreen" component={DealerDeclarationHistoryScreen} />
        <Stack.Screen name="KismatKiBoriScreen" component={KismatKiBoriScreen} />
        <Stack.Screen name="NotificationScreen" component={NotificationScreen} />
        <Stack.Screen name="AddNewLiftingScreen" component={AddNewLiftingScreen} />
        <Stack.Screen name="RssdLiftingAllocationScreen" component={RssdLiftingAllocationScreen} />
        <Stack.Screen name="PaymentWebViewScreen" component={PaymentWebViewScreen} />
        <Stack.Screen name="PerformanceGraphSLCT" component={PerformanceGraphSLCT} />
        <Stack.Screen name="OutstandingSummaryScreen" component={OutstandingSummaryScreen} />
        <Stack.Screen name="OverdueInvoicesScreen" component={OverdueInvoicesScreen} />
        <Stack.Screen name="DeliveryTrackOrderScreen" component={DeliveryTrackOrderScreen} />
        <Stack.Screen name="DeliveryTrackOrderDetails" component={DeliveryTrackOrderDetails} />
        <Stack.Screen name="DeliveryComplainScreen" component={DeliveryComplainScreen} />
        <Stack.Screen name="OverdueInvoicesListScreen" component={OverdueInvoicesListScreen} />
        <Stack.Screen name="GSTScreen" component={GSTScreen} />
    </Stack.Navigator>
);

export default AppNavigator;
