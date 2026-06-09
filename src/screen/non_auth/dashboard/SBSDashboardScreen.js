import React, { useCallback, useEffect, useMemo, useRef, useState } from "react";
import { Alert, FlatList, Image, Linking, Modal, Platform, ScrollView, StatusBar, StyleSheet, Text, TouchableOpacity, TouchableWithoutFeedback, View, } from "react-native";
import LinearGradient from "react-native-linear-gradient";
import { useNavigation } from "@react-navigation/native";
import { parseString } from "react-native-xml2js";
import Toast from "react-native-toast-message";
import DeviceInfo from "react-native-device-info";
import DateTimePicker from '@react-native-community/datetimepicker'
import SbsHeaderView from "../../../common/SbsHeaderView";
import SafeView from "../../../helper/SafeView";
import { Colors } from "../../../assets/Colors";
import { moderateScale } from "../../../helper/Window";
import { Icons } from "../../../assets/Icons";
import SBSMenuOpenView from "../../../common/SBSMenuOpenView";
import DataStorage from "../../../storage/DataStorage";
import UrlStorage from "../../../storage/UrlStorage";
import { createTable } from "../../../storage/database/DataBase";
import { insertDataIn_branch_dump, insertDataIn_branch_master, insertDataIn_branch_schemes_PDF, insertDataIn_customer_master, insertDataIn_destination_master, insertDataIn_self_appraisal_product_wise, } from "../../../storage/database/InsertDataInTable";
import toastConfig from "../../../helper/ToastConfig";
import ShipToSelfListPopupView from "../order/popup/ShipToSelfListPopupView";
import Loader from "../../../common/Loader";
import { getAllDataFrom_customer_master } from "../../../storage/database/GetDataFromTable";
import LoadingWithText from "../../../common/LoadingWithText";
import DashboardDataStorage from "../../../storage/DashboardDataStorage";
import ReactNativeModal from "react-native-modal";
import moment from "moment";
import { AuthCheckingApi } from "../../../auth/AuthCheckingApi";
import AuthNotVerifyPopupView from "../../../auth/AuthNotVerifyPopupView";

// ---------------------------------------------------------------------------
// STATIC DATA
// ---------------------------------------------------------------------------

const DASHBOARD_LIST_FOR_SBS = [
  // { id: "order", title: "Order", icon: Icons.SbsOrder, screen: "OrderScreen" }, //new add
  { id: "track_order", title: "Track Order", icon: Icons.SbsTrackOrder, screen: "TrackOrderScreen" },
  { id: "ledger", title: "Last 50 Transaction", icon: Icons.SbsLedger, screen: "LedgerScreen" },
  { id: "performance", title: "Performance", icon: Icons.SbsPerformance, screen: "SBSPerformanceGraphScreen" },
  { id: "performance_product_wise", title: "Performance\n(Product Wise)", icon: Icons.SbsProductPerformance, screen: "SBSProductWiseProductScreen" },
  { id: "scheme", title: "Scheme", icon: Icons.SbsScheme, screen: "SBSSchemeScreen" },
  // { id: "retailer_lifting", title: "Retailer Lifting", icon: Icons.SbsRetailorLifting, screen: "ListingHistoryScreen" }, //new add
  // { id: "pop_order", title: "Pop Order", icon: Icons.SbsPop, screen: "POPOrderScreen" }, //new add
  // { id: "mason_lifting_approval", title: "Mason Lifting Approval", icon: Icons.SbsMasonLifting, screen: "" }, //new add
  // { id: "pending_invoices", title: "Pending Invoices", icon: Icons.SbsPendingInvoice, screen: "PendingInvoicesScreen" }, //new add
  // { id: "order_enquiry", title: "Order Enquiry", icon: Icons.SbsOrderEnquiry, screen: "OrderEnquiryScreen" }, //new add
  { id: "greetings", title: "Greetings", icon: Icons.SbsGreetings, screen: "" },
];

const BROKER_DEALER_DASHBOARD_LIST_FOR_SBS = [
  { id: "order", title: "Order", icon: Icons.SbsOrder, screen: "OrderScreen" },
  { id: "track_order", title: "Track Order", icon: Icons.SbsTrackOrder, screen: "TrackOrderScreen" },
  { id: "ledger", title: "Last 50 Transaction", icon: Icons.SbsLedger, screen: "LedgerScreen" },
  { id: "performance", title: "Performance Total", icon: Icons.SbsPerformance, screen: "SBSPerformanceGraphScreen" },
  { id: "performance_product_wise", title: "Performance\n(Product Wise)", icon: Icons.SbsProductPerformance, screen: "SBSProductWiseProductScreen" },
  { id: "scheme", title: "Scheme", icon: Icons.SbsScheme, screen: "SBSSchemeScreen" },
  { id: "greetings", title: "Greetings", icon: Icons.SbsGreetings, screen: "" },
];

const BROKER_DASHBOARD_LIST = [
  { id: "order", title: "Order", icon: Icons.CementOrder, screen: "OrderScreen" },
  { id: "track_order", title: "Track Order", icon: Icons.CementTrackOrder, screen: "TrackOrderScreen" },
  { id: "ledger", title: "Ledger", icon: Icons.CementLedger, screen: "LedgerScreen" },
  { id: "scheme", title: "Scheme", icon: Icons.CementScheme, screen: "SchemeScreen" },
  { id: "performance", title: "Performance\n(Month Wise)", icon: Icons.CementPerformance, screen: "PerformanceGraphScreen" },
  { id: "performance_product_wise", title: "Performance\n(Product Wise)", icon: Icons.CementProductPerformance, screen: "ProductWiseProductScreen" },
  // { id: "PerformanceGraphSLCT", title: "Performance Graph SLCT", icon: Icons.SlctIcon, screen: "PerformanceGraphSLCT" },
  { id: "tour", title: "Tour", icon: Icons.CementTour, screen: "" },
  { id: "engagements", title: "Engagements", icon: Icons.CementEngagement, screen: "" },
  { id: "pop_order", title: "Pop Order", icon: Icons.CementPop, screen: "POPOrderScreen" },
  { id: "mason_lifting_approval", title: "Mason Lifting Approval", icon: Icons.CementMasonLifting, screen: "" },
  { id: "pending_invoices", title: "Pending Invoices", icon: Icons.CementPendingInvoice, screen: "PendingInvoicesScreen" },
  { id: "ageing", title: "Ageing", icon: Icons.CementAgeing, screen: "AgeingScreen" },
  { id: "order_enquiry", title: "Order Enquiry", icon: Icons.CementOrderEnquiry, screen: "OrderEnquiryScreen" },
  { id: "rewards", title: "Rewards", icon: Icons.CementRewards, screen: "" },
  { id: "greetings", title: "Greetings", icon: Icons.CementGreetings, screen: "" },
  { id: "sales_visit_feedback", title: "Sales Visit Feedback", icon: Icons.CementFeedback, screen: "FeedbackListScreen" },
];
const DEALER_DASHBOARD_LIST = [
  { id: "order", title: "Order", icon: Icons.CementOrder, screen: "OrderScreen" },
  { id: "track_order", title: "Track Order", icon: Icons.CementTrackOrder, screen: "TrackOrderScreen" },
  // { id: "outstanding_summary", title: "Outstanding\nSummary", icon: Icons.OutstandingSummaryIcon, screen: "OutstandingSummaryScreen" },
  { id: "ledger", title: "Ledger", icon: Icons.CementLedger, screen: "LedgerScreen" },
  { id: "scheme", title: "Scheme", icon: Icons.CementScheme, screen: "SchemeScreen" },
  { id: "performance", title: "Performance\n(Month Wise)", icon: Icons.CementPerformance, screen: "PerformanceGraphScreen" },
  { id: "performance_product_wise", title: "Performance\n(Product Wise)", icon: Icons.CementProductPerformance, screen: "ProductWiseProductScreen" },
  // { id: "PerformanceGraphSLCT", title: "Performance Graph SLCT", icon: Icons.SlctIcon, screen: "PerformanceGraphSLCT" },
  { id: "tour", title: "Tour", icon: Icons.CementTour, screen: "" },
  { id: "engagements", title: "Engagements", icon: Icons.CementEngagement, screen: "" },
  { id: "retailer_lifting", title: "Retailer Lifting", icon: Icons.CementRetailorLifting, screen: "ListingHistoryScreen" },
  { id: "pop_order", title: "Pop Order", icon: Icons.CementPop, screen: "POPOrderScreen" },
  { id: "mason_lifting_approval", title: "Mason Lifting Approval", icon: Icons.CementMasonLifting, screen: "" },
  { id: "pending_invoices", title: "Pending Invoices", icon: Icons.CementPendingInvoice, screen: "PendingInvoicesScreen" },
  { id: "ageing", title: "Ageing", icon: Icons.CementAgeing, screen: "AgeingScreen" },
  { id: "order_enquiry", title: "Order Enquiry", icon: Icons.CementOrderEnquiry, screen: "OrderEnquiryScreen" },
  { id: "rewards", title: "Rewards", icon: Icons.CementRewards, screen: "" },
  { id: "greetings", title: "Greetings", icon: Icons.CementGreetings, screen: "" },
  { id: "sales_visit_feedback", title: "Sales Visit Feedback", icon: Icons.CementFeedback, screen: "FeedbackListScreen" },
];

const OTHER_DASHBOARD_LIST = [
  { id: "track_order", title: "Track Order", icon: Icons.CementTrackOrder, screen: "TrackOrderScreen" },
  { id: "ledger", title: "Ledger", icon: Icons.CementLedger, screen: "LedgerScreen" },
  { id: "performance", title: "Performance\n(Month Wise)", icon: Icons.CementPerformance, screen: "PerformanceGraphScreen" },
  { id: "performance_product_wise", title: "Performance\n(Product Wise)", icon: Icons.CementProductPerformance, screen: "ProductWiseProductScreen" },
  // { id: "PerformanceGraphSLCT", title: "Performance Graph SLCT", icon: Icons.SlctIcon, screen: "PerformanceGraphSLCT" },
  { id: "scheme", title: "Scheme", icon: Icons.CementScheme, screen: "SchemeScreen" },
  { id: "retailer_lifting", title: "Retailer Lifting", icon: Icons.CementRetailorLifting, screen: "ListingHistoryScreen" },
  { id: "pop_order", title: "Pop Order", icon: Icons.CementPop, screen: "POPOrderScreen" },
  { id: "mason_lifting_approval", title: "Mason Lifting Approval", icon: Icons.CementMasonLifting, screen: "" },
  { id: "rsar_lifting_allocation", title: "RSAR Lifting Allocation", icon: Icons.RSSD, screen: "RssdLiftingAllocationScreen" },
  { id: "order_enquiry", title: "New Order Enquiry", icon: Icons.CementOrderEnquiry, screen: "OrderEnquiryScreen" },
  { id: "rewards", title: "Rewards", icon: Icons.CementRewards, screen: "" },
  { id: "greetings", title: "Greetings", icon: Icons.CementGreetings, screen: "" },
  { id: "sales_visit_feedback", title: "Sales Visit Feedback", icon: Icons.CementFeedback, screen: "FeedbackListScreen" },
];

// ---------------------------------------------------------------------------
// HELPERS
// ---------------------------------------------------------------------------

const isBrokerOrDealer = () => {
  //Alert.alert(UrlStorage.ParameterList.BasicData.user_type?.toLowerCase())

  const t = UrlStorage.ParameterList.BasicData.user_type?.toLowerCase();
  return t === "broker" || t === "dealer";
};

const generateCode = (count) => {
  const chars = "ABCDEhijklmnoFGHIJKLMSTUVWXYZabcdefgpxyz01234qrstuvw56NOPQR789";
  let result = "";
  for (let i = 0; i < count; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return result;
};

const TEXT_PRIMARY = "#111111";
const TEXT_SECONDARY = "#666666";
// ---------------------------------------------------------------------------
// COMPONENT
// ---------------------------------------------------------------------------

const SBSDashboardScreen = (props) => {
  const navigation = useNavigation();

  const [openSBSMenu, setOpenBSMenu] = useState(false);
  const [loading, setLoading] = useState(false);
  const [loadingMessage, setLoadingMessage] = useState("");

  const [isDealerWiseReward, setIsDealerWiseReward] = useState(false);
  const [dealerWiseRewardLink, setDealerWiseRewardLink] = useState("");
  const [dealerList, setDealerList] = useState([]);
  const [customerDetails, setCustomerDetails] = useState(null);
  const [dealerListPopUpOpen, setDealerListPopUpOpen] = useState(false);

  const [isTour, setIsTour] = useState(false);
  const [tourLink, setTourLink] = useState("");
  const [isEngagements, setIsEngagements] = useState(false);
  const [engagementsLink, setEngagementsLink] = useState("");

  const [bannerList, setBannerList] = useState([]);
  const [ledgerData, setLedgerdata] = useState(null);

  // Birth Day Popup
  const [isShowBirthDayCollectPopup, setIsShowBirthDayCollectPopup] = useState(false);
  const [dateOfBirth, setDateOfBirth] = useState("Enter Your Date of Birth");
  const [showStartDatePicker, setShowStartDatePicker] = useState(false);
  const [isShowBirthDayPopup, setIsShowBirthDayPopup] = useState(false);
  const [birthDayWishObject, setBirthDayWishObject] = useState(false);
  const [authChecker, setAuthChecker] = useState(false);
  const [creditLimit, setCreditLimit] = useState(false);

  const dashboardList = useMemo(() => {
    if (DataStorage.typeOfUse === 1) return isBrokerOrDealer() ? BROKER_DEALER_DASHBOARD_LIST_FOR_SBS : DASHBOARD_LIST_FOR_SBS;
    return UrlStorage.ParameterList.BasicData.user_type.toLowerCase() === 'broker' ? BROKER_DASHBOARD_LIST : UrlStorage.ParameterList.BasicData.user_type.toLowerCase() === 'dealer' ? DEALER_DASHBOARD_LIST : OTHER_DASHBOARD_LIST;
  }, []);
  useEffect(() => {
    const init = async () => {
      const requestOptions = {
        method: "GET",
        redirect: "follow"
      };

      const a = await AuthCheckingApi();
      if (!a) {
        setAuthChecker(true);
        setLoading(false);
        return;
      }

      fetch("https://starsaathi.com/SAP/acedns_star_slider.php", requestOptions)
        .then((response) => response.json())
        .then((result) => {
          setBannerList(result.start_slider_data);
        })
        .catch(() => { });
      fetchCustomerCredit()
    };

    init();
  }, []);

  // useEffect(() => {
  //   const requestOptions = {
  //     method: "GET",
  //     redirect: "follow"
  //   };

  //   fetch("https://starsaathi.com/SAP/acedns_star_slider.php", requestOptions)
  //     .then((response) => response.json())
  //     .then((result) => {
  //       setBannerList(result.start_slider_data)
  //     })
  //     .catch((error) => { });
  //   // fetchAuth()
  // }, []);

  const fetchAuth = async () => {
    var a = await AuthCheckingApi();
    if (!a) {
      setAuthChecker(false)
      setLoading(false)
    }
  }


  // -------------------------------------------------------------------------
  // Stable callbacks
  // -------------------------------------------------------------------------

  const openCloseDropDownPopup = useCallback(() => {
    setDealerListPopUpOpen((prev) => !prev);
  }, []);

  const handleOpenSBSMenu = useCallback(() => {
    setOpenBSMenu((prev) => !prev);
  }, []);

  const navigateToNotification = useCallback(() => {
    props.navigation.navigate("NotificationScreen");
  }, [props.navigation]);

  // -------------------------------------------------------------------------
  // API helpers
  // -------------------------------------------------------------------------

  const requestForCheckRewardForDealerFromSP = async (item) => {
    const rewardsForm = new FormData();
    rewardsForm.append("user_type", item.cust_type);
    rewardsForm.append("emp_code", item.SAP_code);
    // {"_parts": [["user_type", "dealer"], ["emp_code", "1000000470"]]}

    const tourForm = new FormData();
    tourForm.append("user_type", item.cust_type);
    tourForm.append("emp_code", item.customer_code);
    // {"_parts": [["user_type", "dealer"], ["emp_code", "C/0000408"]]}

    const engagementsForm = new FormData();
    engagementsForm.append("mobileNumber", item.phone_no);
    engagementsForm.append("sessionToken", generateCode(16));
    // {"_parts": [["mobileNumber", "9954230208"], ["sessionToken", "tlrQ9nuGpkp39B7F"]]}

    const opts = (body) => ({ method: "POST", body, redirect: "follow" });
    const { Saathi } = UrlStorage.BaseUrlList;
    const { DashboardURL } = UrlStorage.NonAuthURL.Saathi;

    const [rewardsRes, tourRes, engagementsRes] = await Promise.allSettled([
      fetch(Saathi.base_url_saathi + DashboardURL.rewards_list_url, opts(rewardsForm)).then((r) => r.json()),
      fetch(Saathi.base_url_saathi + DashboardURL.dealer_wise_tour_data_download_url, opts(tourForm)).then((r) => r.json()),
      fetch(Saathi.base_url_saathi + DashboardURL.engagement_list_url, opts(engagementsForm)).then((r) => r.json()),
    ]);

    if (rewardsRes.status === "fulfilled") {
      const r = rewardsRes.value;
      DashboardDataStorage.requestForDealerWiseRewards = r;
      if (r?.process_status !== "NO") {
        setIsDealerWiseReward(true);
        setDealerWiseRewardLink(r?.reward_data?.[0]?.reward_link ?? "");
      }
    }

    if (tourRes.status === "fulfilled") {
      const r = tourRes.value;
      DashboardDataStorage.requestForTour = r;
      if (r?.process_status !== "NO") {
        setIsTour(true);
        setTourLink(r?.tour_data?.[0]?.tour_link ?? "");
      }
    }

    if (engagementsRes.status === "fulfilled") {
      const r = engagementsRes.value;
      DashboardDataStorage.requestForEngagements = r;
      if (r?.process_status !== "NO") {
        setIsEngagements(true);
        setEngagementsLink(r?.data?.launchURL ?? "");
      }
    }
  }

  const requestForCheckBoriMenuStatus = useCallback(() => {
    const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.OtherURL.KismatKiBoriPermissionUrl + "?customer_id=" + UrlStorage.ParameterList.BasicData.emp_id;

    fetch(url)
      .then((r) => r.json())
      .then((result) => {
        DataStorage.isBoriMenuShow = result.process_status !== "NO";
      })
      .catch(() => {
        DataStorage.isBoriMenuShow = false;
      });
  }, []);

  // const requestLedgerListForCement = useCallback(async (isFromSelected = false) => {
  //   setLoadingMessage("Download Data...40%\nPlease wait for a while");
  //   const customerCode =
  //     UrlStorage.ParameterList.BasicData.user_type === "broker"
  //       ? UrlStorage.ParameterList.BasicData.selectedCustomerCode
  //       : UrlStorage.ParameterList.BasicData.emp_code;
  //   const url =
  //     UrlStorage.BaseUrlList.Saathi.base_url_saathi +
  //     UrlStorage.NonAuthURL.Saathi.LedgerURL.other_ledger_list_url +
  //     "?the_id=" +
  //     customerCode;

  //   try {
  //     const response = await fetch(url);
  //     const result = await response.json();
  //     DashboardDataStorage.requestLedgerListForCement = result;
  //     if (result.process_status === "YES") {
  //       UrlStorage.ParameterList.BasicData.ledger_balance_data = result.ledger_balance_data;
  //       setLedgerdata(result.ledger_balance_data);
  //     } else {
  //       Toast.show({ type: "error", text1: "Sorry", text2: result.process_message });
  //     }
  //   } catch (e) {
  //   } finally {
  //     if (!isFromSelected) requestForDatabaseStructure();
  //   }
  // }, []);

  const requestLedgerListForCement = useCallback(async (isFromSelected = false ) => {
    setLoadingMessage("Download Data...40%\nPlease wait for a while");

    const customerCode = UrlStorage.ParameterList.BasicData.user_type === "broker" ? UrlStorage.ParameterList.BasicData.selectedCustomerCode : UrlStorage.ParameterList.BasicData.emp_id;

    const today = new Date().toISOString().split("T")[0];

    const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DashboardURL.dealer_wise_credit_limit_url;

    const params = new URLSearchParams({
      customer_code: customerCode,
      from_date: "2022-07-31",
      to_date: today,
    });

    try {

      const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: params.toString(),
      });

      const rawText = await response.text();
      const result = JSON.parse(rawText);
      DashboardDataStorage.requestLedgerListForCement = result;

      if (result.process_status?.toLowerCase() === "yes") {
        const creditDetails = {
          credit_limit: result.credit_limit !== "null" ? result.credit_limit : null,
          credit_expose: result.credit_expose !== "null" ? result.credit_expose : null,
          SCL: result.Lcamt_1010 !== "null" ? result.Lcamt_1010 : null,
          SCNEL: result.Lcamt_1017 !== "null" ? result.Lcamt_1017 : null,
          SSBSL: result.Lcamt_1016 !== "null" ? result.Lcamt_1016 : null,
        };

        UrlStorage.ParameterList.BasicData.ledger_balance_data = result.ledger_balance_data;
        UrlStorage.ParameterList.BasicData.credit_details = creditDetails;

        setLedgerdata(creditDetails);
        // setLoading(false)
      } else {
        Toast.show({ type: "error", text1: "Sorry", text2: result.process_message });
      }
    } catch (e) {
    } finally {
      if (!isFromSelected) requestForDatabaseStructure();
    }
  }, []);

  const requestLedgerListForSBS = useCallback(async (isFromSelected = false) => {
    setLoadingMessage("Download Data...40%\nPlease wait for a while");
    const customerCode = UrlStorage.ParameterList.BasicData.user_type === "broker" ? UrlStorage.ParameterList.BasicData.selectedCustomerCode : UrlStorage.ParameterList.BasicData.emp_id;
    const url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.Ledger.ledger_data + "?KUNNR=" + customerCode;

    try {
      const response = await fetch(url);
      const result = await response.json();
      DashboardDataStorage.requestLedgerForSBS = result;
      UrlStorage.ParameterList.BasicData.ledger_balance_data = result;
      setLedgerdata(result);
    } catch (e) {
    } finally {
      if (!isFromSelected) requestForDatabaseStructure();
    }
  }, []);

  const requestForDatabaseStructure = useCallback(async () => {
    setLoadingMessage("Download Data...50%\nPlease wait for a while");
    const formdata = new FormData();
    formdata.append("nick_name", "START");
    formdata.append("device_id", "1524JGFI14");
    formdata.append("emp_code", UrlStorage.ParameterList.BasicData.emp_code);
    formdata.append("mode", "INSTALL");

    try {
      const response = await fetch(
        UrlStorage.BaseUrlList.Saathi.base_url_saathi +
        UrlStorage.NonAuthURL.Saathi.DashboardURL.database_details_url,
        { method: "POST", body: formdata }
      );
      const text = await response.text();
      parseString(text, { explicitArray: false }, async (err, result) => {
        if (err) {
          requestForBranchWiseSchemeList();
          return;
        }
        const tablePromises = result.recordset.data.flatMap((item) =>
          item.table_structure.split(";").map((q) => createTable(q))
        );
        await Promise.all(tablePromises);
        requestForBranchWiseSchemeList();
      });
    } catch (e) {
      requestForBranchWiseSchemeList();
    }
  }, []);

  const requestInitialDashboardData = useCallback(async () => {
    setLoadingMessage("Download Data...10%\nPlease wait for a while");

    const { user_type, emp_id, emp_code, emp_mobile_number } = UrlStorage.ParameterList.BasicData;

    const rewardsForm = new FormData();
    rewardsForm.append("user_type", user_type);
    rewardsForm.append("emp_code", emp_id);


    const tourForm = new FormData();
    tourForm.append("user_type", user_type);
    tourForm.append("emp_code", emp_code);

    const engagementsForm = new FormData();
    engagementsForm.append("mobileNumber", emp_mobile_number);
    engagementsForm.append("sessionToken", generateCode(16));

    const opts = (body) => ({ method: "POST", body, redirect: "follow" });
    const { Saathi } = UrlStorage.BaseUrlList;
    const { DashboardURL } = UrlStorage.NonAuthURL.Saathi;


    const [rewardsRes, tourRes, engagementsRes] = await Promise.allSettled([
      fetch(Saathi.base_url_saathi + DashboardURL.rewards_list_url, opts(rewardsForm)).then((r) => r.json()),
      fetch(Saathi.base_url_saathi + DashboardURL.dealer_wise_tour_data_download_url, opts(tourForm)).then((r) => r.json()),
      fetch(Saathi.base_url_saathi + DashboardURL.engagement_list_url, opts(engagementsForm)).then((r) => r.json()),
    ]);

    if (rewardsRes.status === "fulfilled") {
      const r = rewardsRes.value;
      DashboardDataStorage.requestForDealerWiseRewards = r;
      if (r?.process_status !== "NO") {
        setIsDealerWiseReward(true);
        setDealerWiseRewardLink(r?.reward_data?.[0]?.reward_link ?? "");
      }
    }

    if (tourRes.status === "fulfilled") {
      const r = tourRes.value;
      DashboardDataStorage.requestForTour = r;
      if (r?.process_status !== "NO") {
        setIsTour(true);
        setTourLink(r?.tour_data?.[0]?.tour_link ?? "");
      }
    }

    if (engagementsRes.status === "fulfilled") {
      const r = engagementsRes.value;
      DashboardDataStorage.requestForEngagements = r;
      if (r?.process_status !== "NO") {
        setIsEngagements(true);
        setEngagementsLink(r?.data?.launchURL ?? "");
      }
    }

    setLoadingMessage("Download Data...30%\nPlease wait for a while");

    DataStorage.typeOfUse === 1 ? await requestLedgerListForSBS(false) : await requestLedgerListForCement(false);
  }, [requestLedgerListForCement, requestLedgerListForSBS]);

  const checkAndSetData = useCallback(() => {
    const rewards = DashboardDataStorage.requestForDealerWiseRewards;
    const tour = DashboardDataStorage.requestForTour;
    const engagements = DashboardDataStorage.requestForEngagements;
    const ledger = DataStorage.typeOfUse === 1 ? DashboardDataStorage.requestLedgerForSBS : DashboardDataStorage.requestLedgerListForCement;

    if (rewards?.process_status !== "NO") {
      setIsDealerWiseReward(true);
      setDealerWiseRewardLink(rewards?.reward_data?.reward_link ?? "");
    }
    if (tour?.process_status !== "NO") {
      setIsTour(true);
      setTourLink(tour?.tour_data?.[0]?.tour_link ?? "");
    }
    if (engagements?.process_status !== "NO") {
      setIsEngagements(true);
      setEngagementsLink(engagements?.data?.launchURL ?? "");
    }

    const ledgerValue = DataStorage.typeOfUse === 1 ? ledger : ledger?.ledger_balance_data;
    setLedgerdata(ledgerValue);
  }, []);

  const requestForBranchWiseSchemeList = useCallback(async () => {
    setLoadingMessage("Download Data...60%\nPlease wait for a while");
    const { emp_code, user_type } = UrlStorage.ParameterList.BasicData;
    const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DownloadDatabaseAPI.branchwise_schemes_TXT_download_API + `?nick_name=star&emp_code=${emp_code}&user_type=${user_type}&last_update_time=1971-01-01?10:10:10`;

    try {
      const text = await (await fetch(url)).text();
      const arr = text.split("\n");
      const arraySet = arr.slice(2).map((line) => {
        const a = line.split("^");
        return { branch_code: a[0], PDF_file_name: a[1], acedns: a[2] };
      });
      insertDataIn_branch_schemes_PDF(arraySet);
    } catch (e) {
    } finally {
      requestForBranchMasterList();
    }
  }, []);

  const requestForBranchMasterList = useCallback(async () => {
    setLoadingMessage("Download Data...70%\nPlease wait for a while");
    const { emp_code, user_type } = UrlStorage.ParameterList.BasicData;
    const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DownloadDatabaseAPI.branch_master_TXT_download_API + `?nick_name=star&emp_code=${emp_code}&user_type=${user_type}`;

    try {
      const text = await (await fetch(url)).text();
      const arraySet = text.split("\n").slice(2).map((line) => {
        const a = line.split("^");
        return { company_code: a[0], branch_code: a[1], branch_name: a[2], Hq: a[3], plant_name: a[4] };
      });
      insertDataIn_branch_master(arraySet);
    } catch (e) {
    } finally {
      requestForProductWiseTargetAchievementList();
    }
  }, []);

  const requestForProductWiseTargetAchievementList = useCallback(async () => {
    setLoadingMessage("Download Data...80%\nPlease wait for a while");
    const { emp_code, user_type } = UrlStorage.ParameterList.BasicData;
    const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DownloadDatabaseAPI.product_wise_target_achievement_TXT_download_API + `?nick_name=star&user_type=${user_type}&emp_code=${emp_code}`;

    try {
      const text = await (await fetch(url)).text();
      const arraySet = text.split("\n").slice(2).map((line) => {
        const a = line.split("^");
        return {
          prod_code: a[0], prod_desc: a[1], emp_code: a[2], month: a[3],
          target: a[4], achievement: a[5], prev_y_target: a[6], prev_y_achievement: a[7],
        };
      });
      insertDataIn_self_appraisal_product_wise(arraySet);
    } catch (e) {
    } finally {
      requestForCustomerList();
    }
  }, []);

  const requestForCustomerList = useCallback(async () => {
    setLoadingMessage("Download Data...85%\nPlease wait for a while");
    const { emp_code, user_type, incremental_download } = UrlStorage.ParameterList.BasicData;
    // const url =
    //   UrlStorage.BaseUrlList.Saathi.base_url_saathi +
    //   UrlStorage.NonAuthURL.Saathi.DownloadDatabaseAPI.customer_master_audit_TXT_download_API +
    //   `?nick_name=star&emp_code=${emp_code}&user_type=${user_type}&incremental_download=${incremental_download}&last_update_time=1971-01-01?10:10:10&data_download_time=1971-01-01?10:10:10`;

    const url = `https://starsaathi.com/SAP/customer-master-audit-txt-incremental_ios_v2-7.0.11.php` +
      `?nick_name=star&emp_code=${emp_code}&user_type=${user_type}&incremental_download=${incremental_download}&last_update_time=1971-01-01?10:10:10&data_download_time=1971-01-01?10:10:10`

    try {
      const text = await (
        await fetch(url,)
      ).json();

      const arraySet = text.data.map((obj) => {
        return {
          customer_code: obj.customer_code,
          customer_name: obj.customer_name,
          route_code: obj.route_code, emp_code: obj.emp_code,
          black_list: obj.black_list, acedns: obj.acedns, credit_limit: obj.credit_limit, current_balance: obj.current_balance,
          TD: obj.TD, cust_type: obj.cust_type, route_name: '', pin: obj.pin, phone_no: obj.phone_no,
          rds_tag: obj.rds_tag, flag: "", sauda_validity_period: obj.sauda_validity_period, check_flag: "",
          address: obj.address, landline_no: obj.landline_no, owner_name: obj.owner_name, owner_phone: obj.owner_phone,
          cust_class: obj.cust_class, weekly_closing_day: obj.weekly_closing_day, coverage_type: obj.coverage_type,
          TIN: obj.TIN, PAN: obj.PAN, minimum_stock: obj.minimum_stock, branch_code: obj.branch_code,
          visit_day: obj.visit_day, email: obj.email, sauda_limit: obj.sauda_limit, pending_qty: obj.pending_qty, SAP_code: obj.customer_id,
        };
      })


      insertDataIn_customer_master(arraySet);
    } catch (e) {
    } finally {
      requestForDestinationMasterList();
    }
  }, []);

  const requestForDestinationMasterList = useCallback(async () => {
    setLoadingMessage("Download Data...90%\nPlease wait for a while");
    const { emp_code, customerDetails: cd } = UrlStorage.ParameterList.BasicData;
    const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DownloadDatabaseAPI.destination_master_TXT_download_API + `?nick_name=star&emp_code=${emp_code}&incremental_download=no&last_update_time=1971-01-01?10:10:10&data_download_time=1971-01-01?10:10:10&broker_id=${cd?.broker_id}`;

    try {
      const text = await (await fetch(url)).text();
      const lines = text.split("\n");
      let noColumn = 0;
      const arraySet = [];

      for (let line of lines) {
        line = line.trim();
        if (!line) continue;
        if (line.includes("¥")) { noColumn = parseInt(line.split("¥")[1]); continue; }
        if (line.includes("€")) continue;
        const cols = line.split("^");
        if (cols.length === noColumn) {
          arraySet.push({
            destination_code: cols[0],
            destination_name: cols[1],
            ex_for_type: noColumn === 3 ? cols[2] : "",
          });
        }
      }
      await insertDataIn_destination_master(arraySet);
    } catch (e) {
    } finally {
      requestForBranchDumpList();
    }
  }, []);

  const requestForBranchDumpList = useCallback(async () => {
    setLoadingMessage("Download Data...95%\nPlease wait for a while");
    const { emp_code, user_type } = UrlStorage.ParameterList.BasicData;
    const url = UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DownloadDatabaseAPI.branch_dump_TXT_download_API + `?nick_name=star&emp_code=${emp_code}&incremental_download=${user_type}&last_update_time=1971-01-01?10:10:10&data_download_time=1971-01-01?10:10:10`;

    try {
      const text = await (await fetch(url)).text();
      const arraySet = text.split("\n").slice(2).map((line) => {
        const a = line.split("^");
        return {
          branch_code: a[0], dump_code: a[1], dump_name: a[2],
          acedns: a[3], is_plant: a[4], download_time: a[5],
        };
      });
      insertDataIn_branch_dump(arraySet);
    } catch (e) {
    } finally {
      checkUserType();
    }
  }, []);

  const getUserListForSBS = useCallback(async () => {
    const url = UrlStorage.BaseUrlList.SBS.base_url_sbs + UrlStorage.NonAuthURL.SBS.order.dealer_list;
    try {
      const result = await (
        await fetch(url, {
          headers: { Authorization: "SAP_SP", "Content-Type": "application/json" },
        })
      ).json();
      setDealerList(result);
      openCloseDropDownPopup();
    } catch (e) {
    }
  }, [openCloseDropDownPopup]);

  const checkUserType = useCallback(async () => {
    setLoadingMessage("Download Data...100%\nPlease wait for a while");
    if (UrlStorage.ParameterList.BasicData.user_type === "broker") {
      try {
        if (DataStorage.typeOfUse === 1) {
          await getUserListForSBS();
        } else {
          const arr = await getAllDataFrom_customer_master();
          setDealerList(arr);
          openCloseDropDownPopup();
        }
      } catch (err) {
      }
    }
    setLoading(false);
  }, [getUserListForSBS, openCloseDropDownPopup]);

  useEffect(() => {
    const { user_type, emp_code } = UrlStorage.ParameterList.BasicData;

    if (user_type !== "broker") {
      UrlStorage.ParameterList.BasicData.selectedCustomerCode = emp_code;
    }

    requestForCheckBoriMenuStatus();

    if (DataStorage.isFirstOpen) {
      DataStorage.isFirstOpen = false;
      setLoading(true);
      requestInitialDashboardData();
    } else {
      checkAndSetData();
      if (DataStorage.typeOfUse !== 1) requestLedgerListForCement(true);
    }

    // requestForCheckBirthDay()
  }, []);

  const handleRefresh = useCallback(() => {
    const { user_type, emp_code } = UrlStorage.ParameterList.BasicData;
    if (user_type !== "broker") {
      UrlStorage.ParameterList.BasicData.selectedCustomerCode = emp_code;
    }
    setLoading(true);
    requestInitialDashboardData();
    requestForCheckBoriMenuStatus();
  }, [requestInitialDashboardData, requestForCheckBoriMenuStatus]);

  const selectShipToSelfItem = useCallback(
    (item) => {
      console.log(item);
      requestForCheckRewardForDealerFromSP(item)
      setCustomerDetails(item);
      UrlStorage.ParameterList.BasicData.selectedCustomerCode = item?.SAP_code;
      UrlStorage.ParameterList.BasicData.customerDetails = item;
      DataStorage.typeOfUse !== 1 ? requestLedgerListForCement(true) : requestLedgerListForSBS(true);
      DataStorage.typeOfUse !== 1 ? fetchCustomerCredit(true) : null;
      openCloseDropDownPopup();
    },
    [requestLedgerListForCement, requestLedgerListForSBS, openCloseDropDownPopup]
  );

  const menuOptionClick = useCallback(
    (item) => {
      switch (item.id) {
        case "order":
          props.navigation.navigate(item.screen, {
            isSP: UrlStorage.ParameterList.BasicData.user_type === "broker",
          });
          break;
        case "tour":
          if (isTour) {
            DataStorage.web_page_title = "Tour";
            DataStorage.web_link = tourLink;
            props.navigation.navigate("WebLinkScreen");
          } else {
            Toast.show({ type: "info", text1: "Sorry...", text2: "Tour Coming Soon..." });
          }
          break;
        case "engagements":
          if (isEngagements) {
            DataStorage.web_page_title = "Engagements";
            DataStorage.web_link = engagementsLink;
            props.navigation.navigate("WebLinkScreen");
          } else {
            Toast.show({ type: "info", text1: "Sorry...", text2: "Engagements Coming Soon..." });
          }
          break;
        case "mason_lifting_approval":
          DataStorage.web_page_title = "Mason Lifting Approval";
          DataStorage.web_link = UrlStorage.BaseUrlList.Saathi.mason_link + UrlStorage.ParameterList.BasicData.emp_id;
          props.navigation.navigate("WebLinkScreen");
          break;
        case "rewards":
          if (isDealerWiseReward) {
            DataStorage.web_page_title = "Rewards";
            DataStorage.web_link = dealerWiseRewardLink;
            props.navigation.navigate("WebLinkScreen");
          } else {
            Toast.show({ type: "info", text1: "Sorry...", text2: "Rewards Coming Soon..." });
          }
          break;
        case "greetings":
          Linking.openURL("https://greetings.starcement.co.in/");
          break;
        default:
          if (item.screen) props.navigation.navigate(item.screen);
          break;
      }
    },
    [isTour, tourLink, isEngagements, engagementsLink, isDealerWiseReward, dealerWiseRewardLink, props.navigation]
  );

  const onMenuItemPress = useCallback(
    (item) => {
      const hasCustomer = UrlStorage.ParameterList.BasicData.user_type !== "broker" || UrlStorage.ParameterList.BasicData.selectedCustomerCode !== "";
      if (hasCustomer) {
        menuOptionClick(item);
      } else {
        Toast.show({ type: "error", text1: "Select Dealer...", text2: "Please Select a Dealer first" });
      }
    },
    [menuOptionClick]
  );

  const renderDashboardItem = useCallback(
    ({ item }) => (
      /**
       * iOS shadow pattern:
       *   gridItem       — white bg + borderRadius + shadow (NO overflow:hidden)
       *   gridItemInner  — coloured surface on top, same borderRadius
       *
       * The white bg on gridItem is what iOS needs to draw the shadow.
       * It peeks through at corners but is invisible because it matches
       * the page background.  Never add overflow:hidden to gridItem.
       */
      <TouchableOpacity activeOpacity={0.95} style={styles.gridItem} onPress={() => onMenuItemPress(item)} >
        <View style={styles.gridItemInner}>
          {item.id == 'PerformanceGraphSLCT' ? <View style={{ height: moderateScale(80), width: moderateScale(80), alignItems: 'center', justifyContent: 'center' }}>
            <Image source={item.icon} style={{ width: moderateScale(60), height: moderateScale(60), resizeMode: 'contain', marginBottom: moderateScale(4), }} />
          </View> : item.id=='outstanding_summary'?<View style={{ height: moderateScale(80), width: moderateScale(80), alignItems: 'center', justifyContent: 'center' }}>
            <Image source={item.icon} style={{ width: moderateScale(50), height: moderateScale(50), resizeMode: 'contain', marginBottom: moderateScale(4), }} />
          </View>: <Image source={item.icon} style={styles.gridIcon} />}

          <Text numberOfLines={2} style={styles.gridLabel}>
            {item.title}
          </Text>
        </View>
      </TouchableOpacity>
    ),
    [onMenuItemPress]
  );

  const requestForUpdateDateOfBirth = () => {
    setLoading(true)
    const formdata = new FormData();
    formdata.append("customer_id", UrlStorage.ParameterList.BasicData.emp_id);
    formdata.append("dob", dateOfBirth);

    const requestOptions = {
      method: "POST",
      body: formdata,
      redirect: "follow"
    };

    fetch("https://starsaathi.com/SAP/update_customer_dob.php", requestOptions)
      .then((response) => response.text())
      .then((result) => {
        setLoading(false)
        setIsShowBirthDayCollectPopup(false)
        requestForCheckBirthDay();
      })
      .catch((error) => { });
  }

  const requestForCheckBirthDay = () => {
    const requestOptions = {
      method: "GET",
      redirect: "follow"
    };

    fetch("https://starsaathi.com/SAP/get-customer-dob.php?customer_id=" + UrlStorage.ParameterList.BasicData.emp_id, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        setLoading(false)
        if (result.status) {
          setBirthDayWishObject(result)
          setIsShowBirthDayPopup(true);
          requestForUpdateSeenStatus();
        } else {
          if (result.message != 'Birthday wish already shown') {
            setIsShowBirthDayCollectPopup(true);
          }
        }
      })
      .catch((error) => { });
  }

  const requestForUpdateSeenStatus = () => {
    const formdata = new FormData();
    formdata.append("customer_id", UrlStorage.ParameterList.BasicData.emp_id);

    const requestOptions = {
      method: "POST",
      body: formdata,
      redirect: "follow"
    };

    fetch("https://starsaathi.com/SAP/birthday_wish_seen.php", requestOptions)
      .then((response) => response.text())
      .then((result) => { })
      .catch((error) => { });
  }
  const [showCreditLimit, setShowCreditLimit] = useState(false);
  const fetchCustomerCredit = async (isFromSelected = false) => {
    setShowCreditLimit(false)
    const requestOptions = {
      method: "GET",
      redirect: "follow"
    };

    console.log("code----------", UrlStorage.ParameterList.BasicData);
    

    const customerCode = UrlStorage.ParameterList.BasicData.user_type === "broker" ? UrlStorage.ParameterList.BasicData.customerDetails.customer_code : UrlStorage.ParameterList.BasicData.emp_code;

    await fetch(`${UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DashboardURL.customer_credit_api}` + `?customer_code=${customerCode}`, requestOptions)
      .then((response) => response.json())
      .then((result) => {
        console.log("code----------", result);
        console.log("code----------", `${UrlStorage.BaseUrlList.Saathi.base_url_saathi + UrlStorage.NonAuthURL.Saathi.DashboardURL.customer_credit_api}` + `?customer_code=${customerCode}`);
        setCreditLimit(result[0])
        setShowCreditLimit(true)
      })
      .catch((error) => console.error(error));
  }

  const keyExtractor = useCallback((item) => item.id, []);

  const handleStartDateConfirm = (event, selectedDate) => {
    setShowStartDatePicker(false)
    setDateOfBirth(moment(selectedDate).format('DD-MM-YYYY'))
  }

  const showLedgerCard = UrlStorage.ParameterList.BasicData.user_type === "dealer" || UrlStorage.ParameterList.BasicData.user_type === "broker";
console.log('dasdasdasdada  :    ====   :   '+UrlStorage.ParameterList.BasicData.credit_details?.credit_expose ?? 0);
  return (
    <SafeView backgroundColor={Colors.white} bar={false} statusbarColor={Colors.main}>
      <StatusBar backgroundColor={DataStorage.primaryColorCode} />
      <View style={styles.root}>
        <SbsHeaderView
          navigation={navigation}
          props={props}
          handleOpenSBSMenu={handleOpenSBSMenu}
          openDealerList={openCloseDropDownPopup}
          handleRefresh={handleRefresh}
          customerDetails={UrlStorage.ParameterList.BasicData.customerDetails}
          navigateToNotification={navigateToNotification}
        />

        {/*
          outerContent: white rounded panel.
          overflow:"visible" is the critical iOS fix — borderRadius + overflow
          hidden (the iOS default for Views with borderRadius) silently clips
          every child, including the gradient card and grid shadows.
        */}
        <View style={styles.outerContent}>
          <FlatList
            data={dashboardList}
            numColumns={2}
            keyExtractor={keyExtractor}
            showsVerticalScrollIndicator={false}
            decelerationRate="fast"
            columnWrapperStyle={styles.columnWrapper}
            renderItem={renderDashboardItem}
            contentContainerStyle={styles.flatListContent}
            ListHeaderComponent={
              <View style={styles.listHeader}>
                <Image source={DataStorage.typeOfUse == 1 ? Icons.SBSDashBanner : { uri: bannerList[0] }} style={styles.banner} />

                <View style={styles.bannerSpacer} />

                {showLedgerCard && (
                  <View style={DataStorage.typeOfUse == 1 ? styles.ledgerGradient : {}}>
                    {DataStorage.typeOfUse == 1&&<View style={{ ...StyleSheet.absoluteFillObject, backgroundColor: DataStorage.gradientColorCode?.[0], }} />}
                    {DataStorage.typeOfUse == 1&&<View style={{ ...StyleSheet.absoluteFillObject, backgroundColor: DataStorage.gradientColorCode?.[1], opacity: 0.3, }} />}
                    {DataStorage.typeOfUse !== 1 ? (
                      /* ── Cement / Other ── */
                      <View style={{ flexDirection: "column", width: "100%",padding:moderateScale(5), width: "100%",backgroundColor: DataStorage.gradientColorCode?.[0], overflow: 'hidden', borderRadius: moderateScale(10) }}>
                        <View style={{ flexDirection: "row", width: "100%", }}>
                          <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(2), }}>
                            <View style={{ width: "100%", paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(10), borderRadius: moderateScale(10), backgroundColor: "rgba(158, 9,9 ,.2)", alignItems: "flex-start", marginBottom: moderateScale(4), }}>
                              <Text style={{ color: Colors.white, fontSize: moderateScale(11), fontWeight: "600", marginBottom: moderateScale(4), }}>
                                {"₹" + (UrlStorage.ParameterList.BasicData.credit_details?.credit_expose ?? 0)}
                              </Text>
                              <Text style={{ color: Colors.white, fontSize: moderateScale(9), fontWeight: "500", }}>Outstanding Balance</Text>
                            </View>
                          </View>
                          <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(2), }}>
                            <View style={{ width: "100%", paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(10), borderRadius: moderateScale(10), backgroundColor: "rgba(158, 9,9 ,.2)", alignItems: "flex-start", marginBottom: moderateScale(4), }}>
                              <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(5), }}>
                                <Text style={{ color: Colors.white, fontSize:moderateScale(9), fontWeight: "600", }}>
                                  {"SCL "}
                                  <Text style={{ color: Colors.white, fontSize:moderateScale(11), fontWeight: "600", }}>
                                    {"₹" + (UrlStorage.ParameterList.BasicData.credit_details?.SCL ?? 0)}
                                  </Text>
                                </Text>
                              </View>
                              <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(5), }}>
                                <Text style={{ color: Colors.white, fontSize: moderateScale(9), fontWeight: "600", }}>
                                  {"SCNEL "}
                                  <Text style={{ color: Colors.white, fontSize: moderateScale(11), fontWeight: "600", }}>{"₹" + (UrlStorage.ParameterList.BasicData.credit_details?.SCNEL ?? 0)}</Text>
                                </Text>
                              </View>
                            </View>
                          </View>
                        </View>
                        <View style={{ flexDirection: "row", width: "100%", }}>
                          <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(2), }}>
                            <View style={{ width: "100%", paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(10), borderRadius: moderateScale(10), backgroundColor: "rgba(158, 9,9 ,.2)", alignItems: "flex-start", marginBottom: moderateScale(4), justifyContent: 'center' }}>
                              <Text style={{ color: Colors.white, fontSize: moderateScale(11), fontWeight: "600", marginBottom: moderateScale(4), }}>
                                {"₹" + (UrlStorage.ParameterList.BasicData.credit_details?.credit_limit ?? 0)}
                              </Text>
                              <Text style={{ color: Colors.white, fontSize: moderateScale(9), fontWeight: "500", }}>Credit Limit</Text>
                            </View>
                          </View>
                          <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(2), }}>
                            <View style={{ width: "100%", paddingHorizontal: moderateScale(10), paddingVertical: moderateScale(10), borderRadius: moderateScale(10), backgroundColor: "rgba(158, 9,9 ,.2)", alignItems: "flex-start", marginBottom: moderateScale(4), }}>
                              <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(5), }}>
                                <Text style={{ color: Colors.white, fontSize:moderateScale(9), fontWeight: "600", }}>
                                  {"Utilized CL "}
                                  <Text style={{ color: Colors.white, fontSize: moderateScale(11), fontWeight: "600", }}>
                                    {" ₹ "}{Number(parseInt(creditLimit?.CREXP ?? 0)).toLocaleString('en-IN')}
                                  </Text>
                                </Text>
                              </View>
                              <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(5), }}>
                                <Text style={{ color: Colors.white, fontSize: moderateScale(9), fontWeight: "600", }}>
                                  {"Available CL "}
                                  <Text style={{ color: Colors.white, fontSize: moderateScale(11), fontWeight: "600", }}>
                                    {" ₹ "}{(Number(parseInt(creditLimit?.CL ?? 0)) - Number(parseInt(creditLimit?.utilized ?? 0))).toLocaleString('en-IN')}
                                  </Text>
                                </Text>
                              </View>
                            </View>
                          </View>
                        </View>
                        <View style={{ width: '100%' }}>
                          {showCreditLimit&&<View style={{ width: '100%', paddingHorizontal: moderateScale(3), alignItems: 'center', flexDirection: 'row', }} >
                            {(() => {
                              const consumed = Number(creditLimit?.utilized ?? 0);
                              const limit = Number(creditLimit?.CL ?? 0);
                              const rawPercentage =
                                limit > 0 ? (consumed / limit) * 100 : 0;

                              const percentage = Number(rawPercentage.toFixed(2));
                              const consumedPercentage = Math.min(percentage, 100);
                              const overLimit = percentage > 100;
                              const remainingPercentage = overLimit ? Number((percentage - 100).toFixed(2)) : Number((100 - percentage).toFixed(2));
                              return (
                                <View style={{ width: '100%', flexDirection: 'column' }}>
                                  <View style={{ width: '100%', flexDirection: 'row' }}>
                                    <Text style={{ color: Colors.white, fontSize: moderateScale(10), flex: 1, }} > {`${consumedPercentage}% Consumed CL`} </Text>
                                    <Text style={{ color: Colors.white, fontSize: moderateScale(10), }} > {overLimit ? `${remainingPercentage}% Over Credit Limit` : `${remainingPercentage}% Remaining CL`} </Text>
                                  </View>
                                  <View style={{ alignSelf: 'center', height: moderateScale(10), backgroundColor: 'rgba(68, 0, 0, 0.35)', borderRadius: moderateScale(4), overflow: 'hidden', width: "100%" }}>
                                    <LinearGradient
                                      // colors={["#ffe3e3", "#ffffff"]}   
                                      colors={['#00D492', '#009966']}
                                      start={{ x: 0, y: 0 }}
                                      end={{ x: 1, y: 0 }}
                                      style={{ height: '100%', width: `${consumedPercentage}%`, borderRadius: moderateScale(4) }}
                                    />
                                  </View>
                                </View>
                              );
                            })()}
                          </View>}
                        </View>
                        <View style={{height:5}}/>

                        {/* <View style={{ flexDirection: "row", width: "100%", }}>
                          <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(5), }}>
                            <View style={{ width: "100%", padding: moderateScale(14), borderRadius: moderateScale(10), backgroundColor: "rgba(158, 9,9 ,0.20)", alignItems: "flex-start", marginBottom: moderateScale(4), }}>
                              <Text style={{ color: Colors.white, fontSize: moderateScale(15), fontWeight: "600", marginBottom: moderateScale(4), }}>
                                {"₹" + (UrlStorage.ParameterList.BasicData.credit_details?.credit_expose ?? 0)}
                              </Text>
                              <Text style={{ color: Colors.white, fontSize: moderateScale(12), fontWeight: "500", }}>Outstanding Balance</Text>
                            </View>
                          </View>
                          <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(5), }}>
                            <View style={{ width: "100%", padding: moderateScale(14), borderRadius: moderateScale(10), backgroundColor: "rgba(158, 9,9 ,0.20)", alignItems: "flex-start", marginBottom: moderateScale(4), }}>
                             <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(5), }}>
                            <Text style={{ color: Colors.white, fontSize: moderateScale(12), fontWeight: "600", }}>
                              {"SCL "}
                              <Text style={{ color: Colors.white, fontSize: moderateScale(15), fontWeight: "600", }}>
                                {"₹" + (UrlStorage.ParameterList.BasicData.credit_details?.SCL ?? 0)}
                              </Text>
                            </Text>
                          </View>
                          <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(5), }}>
                            <Text style={{ color: Colors.white, fontSize: moderateScale(12), fontWeight: "600", }}>
                              {"SCNEL "}
                              <Text style={{ color: Colors.white, fontSize: moderateScale(15), fontWeight: "600", }}>{"₹" + (UrlStorage.ParameterList.BasicData.credit_details?.SCNEL ?? 0)}</Text>
                            </Text>
                          </View>
                            </View>
                          </View>
                        </View>
                        <View style={{ flexDirection: "row", width: "100%", }}>
                          <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(5), }}>
                            <View style={{ width: "100%", padding: moderateScale(14), borderRadius: moderateScale(10), backgroundColor: "rgba(158, 9,9 ,0.20)", alignItems: "center", marginBottom: moderateScale(4),justifyContent:'center' }}>
                              <Text style={{ color: Colors.white, fontSize: moderateScale(10), fontWeight: "600", marginBottom: moderateScale(4), }}>
                                {"₹" + (UrlStorage.ParameterList.BasicData.credit_details?.credit_limit ?? 0)}
                              </Text>
                              <Text style={{ color: Colors.white, fontSize: moderateScale(8), fontWeight: "500", }}>Credit Limit</Text>
                            </View>
                          </View>
                          <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(5), }}>
                            <View style={{ width: "100%", padding: moderateScale(14), borderRadius: moderateScale(10), backgroundColor: "rgba(158, 9,9 ,0.20)", alignItems: "flex-start", marginBottom: moderateScale(4), }}>
                              <Text style={{ color: Colors.white, fontSize: moderateScale(10), fontWeight: "600", marginBottom: moderateScale(4), }}>
                                {" ₹ "}{Number(creditLimit?.CREXP ?? 0)}
                              </Text>
                              <Text style={{ color: Colors.white, fontSize: moderateScale(8), fontWeight: "500", }}>Credit Limit Utilisation</Text>
                            </View>
                          </View>
                          <View style={{ flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(5), }}>
                            <View style={{ width: "100%", padding: moderateScale(14), borderRadius: moderateScale(10), backgroundColor: "rgba(158, 9,9 ,0.20)", alignItems: "flex-start", marginBottom: moderateScale(4), }}>
                              <Text style={{ color: Colors.white, fontSize: moderateScale(10), fontWeight: "600", marginBottom: moderateScale(4), }}>
                                {" ₹ "}{(Number(creditLimit?.CL ?? 0) - Number(creditLimit?.CREXP ?? 0)).toLocaleString('en-IN')}
                              </Text>
                              <Text style={{ color: Colors.white, fontSize: moderateScale(8), fontWeight: "500", }}>Available Credit Limit</Text>
                            </View>
                          </View>
                        </View>
                        <View style={{ flexDirection: "row", width: "100%", }}>
                          
                        </View>
                        <View style={{ height: 10 }} />
                        <View style={{ width: '100%' }}>
                          {showCreditLimit&&<View style={{ width: '100%', paddingHorizontal: moderateScale(3), alignItems: 'center', flexDirection: 'row', }} >
                            {(() => {
                              const consumed = Number(creditLimit?.CREXP ?? 0);
                              const limit = Number(creditLimit?.CL ?? 0);
                              const rawPercentage =
                                limit > 0 ? (consumed / limit) * 100 : 0;

                              const percentage = Number(rawPercentage.toFixed(2));
                              const consumedPercentage = Math.min(percentage, 100);
                              const overLimit = percentage > 100;
                              const remainingPercentage = overLimit ? Number((percentage - 100).toFixed(2)) : Number((100 - percentage).toFixed(2));
                              return (
                                <View style={{ width: '100%', flexDirection: 'column' }}>
                                  <View style={{ width: '100%', flexDirection: 'row' }}>
                                    <Text style={{ color: Colors.white, fontSize: moderateScale(10), flex: 1, }} > {`${consumedPercentage}% Consumed`} </Text>
                                    <Text style={{ color: Colors.white, fontSize: moderateScale(10), }} > {overLimit ? `${remainingPercentage}% Over Credit Limit` : `${remainingPercentage}% Remaining`} </Text>
                                  </View>
                                  <View style={{ alignSelf: 'center', height: moderateScale(10), backgroundColor: 'rgba(68, 0, 0, 0.35)', borderRadius: moderateScale(4), overflow: 'hidden', width: "100%" }}>
                                    <LinearGradient
                                      // colors={["#ffe3e3", "#ffffff"]}   
                                      colors={['#00D492', '#009966']}
                                      start={{ x: 0, y: 0 }}
                                      end={{ x: 1, y: 0 }}
                                      style={{ height: '100%', width: `${consumedPercentage}%`, borderRadius: moderateScale(4) }}
                                    />
                                  </View>
                                </View>
                              );
                            })()}
                          </View>}
                        </View> */}
                      </View>
                    ) : (
                      /* ── SBS (typeOfUse === 1) ── */
                      <View style={styles.sbsLedgerContainer}>
                        {/* Credit Limit pill */}
                        <View style={styles.sbsCreditLimitRow}>
                          <Text style={styles.sbsLedgerRowLabel}>Credit Limit:</Text>
                          <Text style={styles.sbsLedgerRowValue}>
                            ₹{ledgerData?.credit_limit ?? 0}
                          </Text>
                        </View>

                        {/* Card holding Outstanding Balance + SSBSL */}
                        <View style={styles.sbsLedgerCard}>
                          <View style={styles.sbsLedgerRow}>
                            <Text style={styles.sbsLedgerRowLabel}>Outstanding Balance</Text>
                            <Text style={styles.sbsLedgerRowValue}>
                              ₹{Number(ledgerData?.credit_expose ?? 0).toFixed(2)}
                            </Text>
                          </View>
                          <View style={styles.divider} />
                          <View style={styles.sbsLedgerRow}>
                            <Text style={styles.sbsLedgerRowLabel}>SSBSL</Text>
                            <Text style={styles.sbsLedgerRowValue}>
                              ₹{ledgerData?.ssbsl_value ?? 0}
                            </Text>
                          </View>
                        </View>
                      </View>
                    )}
                  </View>
                )}
              </View>
            }
          />
        </View>

        <SBSMenuOpenView isVisible={openSBSMenu} handleOpenSBSMenu={handleOpenSBSMenu} {...props} />
      </View>

      <ShipToSelfListPopupView
        isDealer
        isVisible={dealerListPopUpOpen}
        dataList={dealerList}
        closePopup={openCloseDropDownPopup}
        selectItem={selectShipToSelfItem}
      />
      <Toast config={toastConfig} />
      <ReactNativeModal
        isVisible={isShowBirthDayCollectPopup}
        style={{ margin: 0 }}
        customBackdrop={
          <TouchableWithoutFeedback onPress={() => { setIsShowBirthDayCollectPopup(false) }}>
            <View style={{ flex: 1, backgroundColor: "rgba(0,0,0,0.4)" }} />
          </TouchableWithoutFeedback>
        }
      >
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Your Date of Birth is not Updated</Text>
          <Text style={styles.cardSubtitle}>Kindly choose your DOB</Text>
          <View style={{ height: moderateScale(20) }} />
          <TouchableOpacity
            style={{ width: '100%', height: moderateScale(44), borderRadius: moderateScale(10), borderColor: '#E5E5E5', borderWidth: 1, paddingHorizontal: moderateScale(10), justifyContent: 'center' }}
            onPress={() => { setShowStartDatePicker(true) }}>
            <Text style={{ color: dateOfBirth != 'Enter Your Date of Birth' ? '#1E1E1E' : "#AFAFAF", fontSize: moderateScale(14) }}>{dateOfBirth}</Text>
          </TouchableOpacity>
          <View style={{ height: moderateScale(20) }} />
          <TouchableOpacity
            style={{ width: '100%', height: moderateScale(44), borderRadius: moderateScale(10), backgroundColor: Colors.red, paddingHorizontal: moderateScale(10), justifyContent: 'center', alignItems: 'center' }}
            onPress={() => {
              requestForUpdateDateOfBirth()
            }}>
            <Text style={{ color: Colors.white, fontSize: moderateScale(16), fontWeight: '600' }}>Submit</Text>
          </TouchableOpacity>
        </View>
        {showStartDatePicker && (
          <View style={{ width: '100%', height: '100%', position: 'absolute', alignItems: 'center', justifyContent: 'center', backgroundColor: '#00000060' }}>
            <View style={{ backgroundColor: '#FFF', padding: 10, borderRadius: 10 }}>
              <DateTimePicker
                value={new Date()}
                mode="date"
                display="default"
                maximumDate={new Date()}
                onChange={handleStartDateConfirm}
              />
            </View>
          </View>
        )}
        {loading && <Loader message={loadingMessage} />}
      </ReactNativeModal>
      <ReactNativeModal
        isVisible={isShowBirthDayPopup}
        customBackdrop={
          <TouchableWithoutFeedback onPress={() => { setIsShowBirthDayPopup(false) }}>
            <View style={{ flex: 1, backgroundColor: "rgba(0,0,0,0.4)" }} />
          </TouchableWithoutFeedback>
        }
      >
        <View style={{ width: '100%', height: moderateScale(350) }}>
          <Image source={{ uri: birthDayWishObject.img }} style={{ width: '100%', height: '100%' }} />
          <View style={{ width: '100%', height: '100%', padding: moderateScale(20), alignItems: 'center', justifyContent: 'center', position: 'absolute', flexDirection: 'column' }}>
            <View style={{ height: moderateScale(15) }} />
            <Text style={{ color: '#FFF', fontSize: moderateScale(24), fontWeight: 'bold' }}>{birthDayWishObject.title}</Text>
            <View style={{ height: moderateScale(15) }} />
            <Text style={{ color: '#FFF', fontSize: moderateScale(20), fontWeight: 'bold' }}>{birthDayWishObject.customer_name}</Text>
            <View style={{ height: moderateScale(30) }} />
            <Text style={{ color: '#FFF', fontSize: moderateScale(14), textAlign: 'center' }}>{birthDayWishObject.message?.trim()}</Text>
            <View style={{ height: moderateScale(35) }} />
            <Text style={{ color: '#FFF', fontSize: moderateScale(14) }}>~ Star Cement Family</Text>
          </View>
        </View>
      </ReactNativeModal>
      {loading && <LoadingWithText message={loadingMessage} />}
      <AuthNotVerifyPopupView isVisible={authChecker} onClose={() => setAuthChecker(false)} />
    </SafeView>
  );
};

// ---------------------------------------------------------------------------
// STYLES
// ---------------------------------------------------------------------------

const styles = StyleSheet.create({
  root: { width: "100%", height: "100%", backgroundColor: Colors.main, },

  // ── White rounded panel ───────────────────────────────────────────────────
  // overflow:"visible" prevents iOS from clipping children against the
  // borderRadius.  This is the root cause of the card being cut off.
  outerContent: { flex: 1, backgroundColor: Colors.white, borderTopRightRadius: moderateScale(20), borderTopLeftRadius: moderateScale(20), overflow: "visible", },

  // Padding lives in contentContainerStyle, not on the parent View,
  // so the FlatList scroll area is not incorrectly constrained.
  flatListContent: { paddingTop: moderateScale(20), paddingHorizontal: moderateScale(10), paddingBottom: moderateScale(30), },

  listHeader: { width: "100%", },

  // ── Banner ───────────────────────────────────────────────────────────────
  banner: { width: "100%", height: moderateScale(160), borderRadius: moderateScale(12), resizeMode: "stretch", },
  bannerSpacer: { height: moderateScale(20), },

  ledgerGradient: { marginBottom: moderateScale(20), width: "100%", padding: moderateScale(16), overflow: 'hidden', borderRadius: moderateScale(20), },

  row: { flexDirection: "row", width: "100%", },
  column: { flexDirection: "column", width: "100%", },
  flex1: { flex: 1, alignItems: "flex-start", marginHorizontal: moderateScale(5), },
  ledgerCard: { width: "100%", padding: moderateScale(14), borderRadius: moderateScale(10), backgroundColor: "rgba(158, 9,9 ,0.20)", alignItems: "flex-start", marginBottom: moderateScale(4), },
  ledgerValue: { color: Colors.white, fontSize: moderateScale(15), fontWeight: "600", marginBottom: moderateScale(4), },
  ledgerLabel: { color: Colors.white, fontSize: moderateScale(12), fontWeight: "500", },
  ledgerSubLabel: { color: Colors.white, fontSize: moderateScale(12), fontWeight: "600", },
  ledgerSubValue: { color: Colors.white, fontSize: moderateScale(15), fontWeight: "600", },

  // ── SBS ledger (typeOfUse === 1) ─────────────────────────────────────────
  sbsLedgerContainer: { width: "100%", },
  // 65%-wide pill for Credit Limit
  sbsCreditLimitRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", paddingVertical: moderateScale(10), paddingHorizontal: moderateScale(14), borderRadius: moderateScale(12), backgroundColor: "rgba(255, 255, 255, 0.20)", width: "65%", marginBottom: moderateScale(10), },
  // White-tinted card for the two data rows — full width, no clipping
  sbsLedgerCard: { width: "100%", borderRadius: moderateScale(12), backgroundColor: "rgba(255, 255, 255, 0.20)", paddingVertical: moderateScale(4), },
  sbsLedgerRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", paddingVertical: moderateScale(10), paddingHorizontal: moderateScale(14), },
  sbsLedgerRowLabel: { fontSize: moderateScale(12), fontWeight: "500", color: Colors.white, opacity: 0.9, flexShrink: 1, marginRight: moderateScale(8), },
  sbsLedgerRowValue: { fontSize: moderateScale(14), fontWeight: "700", color: Colors.white, },
  divider: { height: 1, backgroundColor: "rgba(255, 255, 255, 0.30)", marginHorizontal: moderateScale(14), },

  // ── Dashboard grid ────────────────────────────────────────────────────────
  columnWrapper: { justifyContent: "space-between", marginVertical: moderateScale(5), paddingHorizontal: moderateScale(5), },

  // Shadow carrier — must have a non-transparent backgroundColor on iOS.
  // Never add overflow:"hidden" here; that kills iOS shadows.
  gridItem: {
    flex: 0.48,
    borderRadius: moderateScale(10),
    backgroundColor: Colors.white,
    ...Platform.select({
      ios: {
        shadowColor: "#000",
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.14,
        shadowRadius: 10,
      },
      android: {
        elevation: 4,
      },
    }),
  },
  // Coloured surface — sits on top of the white shadow carrier.
  // borderRadius matches gridItem so corners look clean.
  gridItemInner: { width: "100%", backgroundColor: "#fff", borderRadius: moderateScale(10), paddingTop: moderateScale(14), paddingBottom: moderateScale(10), paddingHorizontal: moderateScale(12), alignItems: "center", justifyContent: "center", },
  gridIcon: { width: moderateScale(80), height: moderateScale(80), resizeMode: 'contain', marginBottom: moderateScale(4), },
  gridLabel: { lineHeight: moderateScale(20), minHeight: moderateScale(40), textAlign: "center", color: Colors.text, fontSize: moderateScale(15), fontWeight: "600", },
  card: { backgroundColor: "#FFFFFF", marginHorizontal: moderateScale(24), borderRadius: moderateScale(16), padding: moderateScale(26), shadowColor: "#000", shadowOffset: { width: 0, height: 8 }, shadowOpacity: 0.1, shadowRadius: 20, elevation: 8, },
  cardTitle: { fontSize: moderateScale(18), fontWeight: "700", color: TEXT_PRIMARY },
  cardSubtitle: { marginTop: moderateScale(4), fontSize: moderateScale(13), color: TEXT_SECONDARY, lineHeight: moderateScale(18), },
});

export default SBSDashboardScreen;