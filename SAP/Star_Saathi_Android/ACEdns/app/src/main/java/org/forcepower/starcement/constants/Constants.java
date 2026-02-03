package org.forcepower.starcement.constants;

import android.graphics.Bitmap;
import android.widget.EditText;

import org.forcepower.starcement.BuildConfig;
import org.forcepower.starcement.bean.BranchMasterDetails;
import org.forcepower.starcement.bean.CashTransferReceive;
import org.forcepower.starcement.bean.CustomerDetails;
import org.forcepower.starcement.bean.DestinationMaster;
import org.forcepower.starcement.bean.EmployeeDetails;
import org.forcepower.starcement.bean.MarketFeedbackDetails;
import org.forcepower.starcement.bean.MenuDetails;
import org.forcepower.starcement.bean.OrderFormDetails;
import org.forcepower.starcement.bean.OutstandingDetails;
import org.forcepower.starcement.bean.PopProductModel;
import org.forcepower.starcement.bean.ProductDetails;
import org.forcepower.starcement.bean.ProductMasterDetails;
import org.forcepower.starcement.bean.SaudaFormDetails;
import org.forcepower.starcement.bean.SelfAppraisalDetails;
import org.forcepower.starcement.bean.SurveyFormDetails;
import org.forcepower.starcement.bean.SurveyMenuDetails;
import org.forcepower.starcement.bean.UserDetails;

import java.text.DecimalFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;

public final class Constants
{
	public static final String nickName = BuildConfig.App_Nick_Name;
	public static String mVerticalValue = "";
	public static boolean isFirstLoginOfDay = false;
	public static boolean isFirstLoginOfApp = false;
	public static final String baseURL = BuildConfig.API_URL;

	public static String mOrderPriceValidationType			="";//empty=normal flow, DOPS="smart input screen"  SPA=normal flow with aditional api call
	public static String mOrderType			="";
	public static String mFreightComponent	="";
	public static String mDestinationCode	="";
	public static String mCurrentOrderNoList="";

	public static String selected_customr_code = "", selected_SAP_code = "";
	public static DecimalFormat defaultFormat = new DecimalFormat("0.00");

	public static String dateString = "", deviceId = "", redirection ="";
	public static EmployeeDetails employeeDetailObject = new EmployeeDetails();
	public static Bitmap logoBmp;
	/*
	 * DATABASE STRUCTURE DATA
	 */
	public static ArrayList<String> downloadTableList = new ArrayList<>();
	/*
	 * SETUP TABLE DATA
	 */
	public static MenuDetails menuDetailsObj = new MenuDetails();
	public static MarketFeedbackDetails marketFeedbackDetailsObj = new MarketFeedbackDetails();
	public static UserDetails userDetailsObj = new UserDetails();
	public static OrderFormDetails orderFormDetailsObj = new OrderFormDetails();

	public static SaudaFormDetails saudaFormDetailsObj = new SaudaFormDetails();
	public static SurveyFormDetails surveyFormDetailsObj = new SurveyFormDetails();
	public static ProductDetails productDetailsObj = new ProductDetails();

	/*
	 * Order Activity
	 */
	public static boolean shouldUpdateCustomerMasterWithEmail 	= false;
	public static HashMap<String, ProductMasterDetails> selectedProductMasterList = new HashMap<>();
	public static HashMap<String, PopProductModel> selectedList = new HashMap<>();
	public static ArrayList<CashTransferReceive> UnVerifiedCashReceiveList;

	public static CustomerDetails selectedCustomer;
	public static BranchMasterDetails selectedBranch;
	public static DestinationMaster selectedDestination;
	public static String currentPrinterIp = "";//storing ip of the printer found from network

	public static String[] mEmployeeList;
	public static String[] mSurveyLayoutList;
	public static String[] mProductGrpouList;
	public static String[] mStreetList;
	public static String[] mPincodeList;
	public static String[] mVerticalValueList;

	public static ArrayList<SurveyMenuDetails> mSurveyMenuDetailsList;
	public static ArrayList<String> sendColumnDataForSurveyCheckBox;


	/*
	 * Collection Activity
	 */
	public static ArrayList<OutstandingDetails> selectedOutstandingList;
	public static ArrayList<String> allocatedRouteCodeTodayCrm;
	public static ArrayList<String> allocatedRouteNameTodayCrm;


	public static Date dictDownldStartTime;
	public static Calendar dictDownldSrverTime;


	public static boolean isMenuDetailsUpdated 					= false;
	public static boolean isMarketFeedbackDetailsUpdated 		= false;
	public static boolean isOrderFormDetailsUpdated 			= false;
	public static boolean isProductDetailsUpdated 				= false;
	public static boolean isUserDetailsUpdated 					= false;
	public static boolean isRoutePlanDetailsUpdated 			= false;
	public static boolean isSaudaFormDetailsUpdated 			= false;

	public static boolean isSurveyFormDetailsUpdated 			= false;

	public static boolean isBankTableUpdated 					= false;
	public static boolean isClosingStockUpdated 				= false;
	public static boolean isCustomerTableUpdated 				= false;
	public static boolean isMrpTableUpdated 					= false;
	public static boolean isSurveyPublishTableUpdated 			= false;
	public static boolean isOfferPublishTableUpdated 			= false;
	public static boolean isFsSurveyPublishTableUpdated 		= false;
	public static boolean isSaudaMrpTableUpdated 				= false;
	public static boolean isSubmittedFeedback 				= false;
	public static boolean isPrevStockCountingUpdated 			= false;
	public static boolean isProductBrandTableUpdated 			= false;
	public static boolean isProductGroupTableUpdated 			= false;
	public static boolean isProductTableUpdated 				= false;
	public static boolean isSchemeTableUpdated 				= false;
	public static boolean isSurveyTableUpdated 					= false;
	public static boolean isSelfAppraisalDetailsTableUpdated 					= false;
	public static boolean isSelfAppraisalCustomerWiseTableUpdated 					= false;
	public static boolean isSelfAppraisalBranchWiseTableUpdated 					= false;
	public static boolean isProductSubGroupTableUpdated 		= false;
	public static boolean isRDSTableUpdated 					= false;
	public static boolean isRouteTableUpdated 					= false;
	public static boolean isDistributorRouteTableUpdated 		= false;
	public static boolean isLoyaltyTableUpdated 				= false;
	public static boolean isMISTransactionTableUpdated 			= false;
	public static boolean isGITTableUpdated 					= false;

	public static boolean isLoyaltyPurchaseUpdated 				= false;

	public static boolean isCustBranchUpdated 					= false;
	public static boolean isShowValueSendValueDifferentForChcekBOx					= false;

	public static boolean isBrokerUpdated 				= false;
	public static boolean isNonTradeCustomer			= false;
	public static boolean isBranchUpdated 				= false;
	public static boolean isDestinationUpdated 			= false;
	public static boolean isDumpMasterUpdated 			= false;
	public static boolean isEmployeeUpdated 			= false;
	public static boolean isSaudaAllocationUpdated 		= false;
	public static boolean isSaudaTransactionUpdated		= false;
	public static boolean isStreetUpdated 				= false;
	public static boolean isPreviousOrderCounting		= false;
	public static boolean isOrderStatus					= false;
	public static boolean isRouteCustomer				= false;
	public static boolean isTableView					= false;

	public static boolean isDownLoadComplete = true;
	public static boolean isCommpleteDownLoadComplete = true;


	/*
	 *   For App Data back up from Menu Screen
	 */
	public static boolean isDataRefreshed 	=false, updateChecked = false;

	public static final String check_internet_connection = "You need to have an active internet connection to use this feature.";

	public static String customerCode_ = "", branch_code = "", d_instruction_ = ""; //amitabha2715

	public static final int DEFAULT_TIMEOUT = 20 * 1000;

	public static String dealer_truck = "",
			sub_dealer_code = "", dump_code = "";
	public static boolean  sub_dealer_destination_new_logic = false;
	public static String cusomer_code_sub_dealer_new_logic = "";
	//url For retailer App
	public static String acedns_about_us = baseURL  + "weblink/about.html";
	public static String survey_form = baseURL  + "weblink/survey_form.php?the_id=";
	public static String survey_success_page = baseURL  + "weblink/survey_success_page.php";
	public static String my_payment_history_weblink = baseURL  + "weblink/my_payment_history_weblink.php?the_id=";
	public static String arc_offer = baseURL  + "arc_offer/index.php?login_user_id=";
	public static String acedns_star_clear_allocation_by_id = baseURL + "acedns_star_clear_allocation_by_id.php?the_id=";
	public static String show_latest_app_version = baseURL + "show_latest_app_version_v2.php";
	public static String game_authorization = baseURL + "game_authorization.php";
	public static String dealer_wise_rewards = baseURL + "dealer-wise-rewards_test.php";
	public static String save_epod_details = baseURL + "save_epod_details.php";
	public static String save_app_usage_details = baseURL + "save_app_usage_details.php";
	public static String acedns_star_add_lifting = baseURL + "acedns_star_add_lifting.php";
	public static String lifting_date_validation_data = baseURL + "lifting_date_validation_data.php";
	public static String place_order_lifting_days_download = baseURL + "place_order_lifting_days_download.php";
	public static String save_order_query_data = baseURL + "save_order_query_data.php";
	public static String schemes = baseURL + "schemes/";
	public static String detailed_statement_pdf_download = baseURL + "dashboard/detailed_statement_pdf_download.php";
	public static String acedns_star_ledger_by_id = baseURL + "acedns_star_ledger_by_id.php?the_id=";
	public static String acedns_show_order_list_for_subdealer = baseURL + "acedns_show_order_list_for_subdealer_V1.php";
	public static String acedns_pay_ledger_amount_v2 = baseURL + "acedns_pay_ledger_amount_v2.php";
	public static String acedns_star_ledger_subdealer_rssd = baseURL + "acedns_star_ledger_subdealer_rssd.php";
	public static String pop_product_data_download = baseURL + "pop-product-data-download-v2.php";
	public static String show_pop_order_list = baseURL + "show-pop-order-list.php";
	public static String ajax_allocation_lifting_V2 = baseURL + "ajax_allocation_lifting_V3.php";
	public static String acedns_star_show_month_wize_ledger_by_id = baseURL + "acedns_star_show_month_wize_ledger_by_id.php?the_id=";
	public static String acedns_star_update_this_month_ledger_status = baseURL + "acedns_star_update_this_month_ledger_status.php";
	public static String acedns_show_notifications = baseURL + "acedns_show_notifications.php";
	public static String acedns_save_employee_kyc = baseURL + "acedns_save_employee_kyc.php";
	public static String ship_to_party_master_txt_V1 = baseURL + "ship-to-party-master-txt-V2.php";
	public static String acedns_show_employee_kyc = baseURL + "acedns_show_employee_kyc.php";
	public static String acedns_star_ledger_balance_details_by_id = baseURL + "acedns_star_ledger_balance_details_by_id.php";
	public static String acedns_star_order_details_by_id_inv = baseURL + "acedns_star_order_details_by_id_v1.php";
	public static String acedns_star_order_details_by_id = baseURL + "acedns_star_order_details_by_id.php";
	public static String order_query_data_download = baseURL + "order_query_data_download.php";
	public static String acedns_show_offline_order_list = baseURL + "acedns_show_offline_order_list.php";
	public static String make_notification_read_v3 = baseURL + "make_notification_read_v3.php";
	public static String acedns_update_profile_image = baseURL + "acedns_update_profile_image.php";
	public static String dealer_wise_tour_data_download = baseURL + "dealer-wise-tour-data-download.php";
	public static String acedns_star_update_lifting_status_by_dealer = baseURL + "acedns_star_update_lifting_status_by_dealer.php";
	public static String verifyotp = baseURL + "reportmvc/api/v2/verifyotpnew_v2";
	public static String checklogin = baseURL + "reportmvc/api/v2/checkloginnew_v2";
	public static String dealerwise_ageing_data = baseURL + "dealerwise-ageing-data.php";
	public static String dealer_site_visit_list = baseURL + "dealer-site-visit-list.php";
	public static String save_dealer_sales_team_visit_details = baseURL + "save_dealer_sales_team_visit_details.php";
	public static String save_consumer_scheme = baseURL + "save_consumer_scheme.php";
	public static String acedns_star_submit_yellow_card_details = baseURL + "acedns_star_submit_yellow_card_details.php";
	public static String save_pop_order_date_v1 = baseURL + "save_pop_order_date_v1.php";
	public static String acedns_star_slider = baseURL + "acedns_star_slider.php";
	public static String branchwise_game_banner_download = baseURL + "branchwise-game-banner-download.php";
	public static String acedns_star_save_online_offline_app_order_new_v2 = baseURL + "acedns_star_save_online_offline_app_order_new_v4.php";
	public static String save_allocation_details = baseURL + "save_allocation_details.php";
	public static String save_allocation_details_inv = baseURL + "save_allocation_details_invoicewise.php";
	public static String acedns_star_update_material_receive_confirmation_v2 = baseURL + "acedns_star_update_material_receive_confirmation_v2.php";

	public static String acedns_star_show_pending_lifting_history_for_sub_dealer = baseURL + "acedns_star_show_pending_lifting_history_for_sub_dealer.php";
	public static String acedns_star_show_approve_lifting_history_for_sub_dealer = baseURL + "acedns_star_show_approve_lifting_history_for_sub_dealer.php";
	public static String acedns_star_show_reject_lifting_history_for_sub_dealer = baseURL + "acedns_star_show_reject_lifting_history_for_sub_dealer.php";
	public static String acedns_star_show_pending_lifting_history_for_dealer = baseURL + "acedns_star_show_pending_lifting_history_for_dealer.php";
	public static String dispatched_order_list_download_v3 = baseURL + "dispatched-order-list-download-v3.php";
	public static String dispatched_order_list_invoicewise = baseURL + "dispatched-order-list-invoicewise-v10.php";
	public static String dispatched_order_list_download_v2 = baseURL + "dispatched-order-list-download-v2.php";
	public static String dispatched_order_list_download_invoicewise = baseURL + "dispatched-order-list-download-invoicewise-v2.php";
	public static String ajax_allocation_lifting_invoicewise = baseURL + "ajax_allocation_lifting_invoicewise_v10.php";

	public static String acedns_star_show_approve_lifting_history_for_dealer = baseURL + "acedns_star_show_approve_lifting_history_for_dealer.php";
	public static String acedns_star_show_reject_lifting_history_for_dealer = baseURL + "acedns_star_show_reject_lifting_history_for_dealer.php";

	public static String acedns_dashboard_webLink = Constants.baseURL+ "dashboard/";

	public static String acedns_show_invoice_list = baseURL + "acedns_show_invoice_list.php";
	public static String dealerwise_credit_limit_s_deposit = baseURL + "dealerwise-credit-limit-s-deposit-v2.php";
	public static String the_success_url = baseURL + "ccavenue_pg/the_success_url.php";
	public static String the_success_url_ne = baseURL + "ccavenue_pg/the_success_url_ne.php";
	public static String the_cancel_url = baseURL + "ccavenue_pg/the_cancel_url.php";
	public static String the_cancel_url_ne = baseURL + "ccavenue_pg/the_cancel_url_ne.php";
	public static String item_wise_target_achievement_SAP_data = baseURL + "item_wise_target_achievement_SAP_data.php";
	public static String fpx_delaer_truck_list = baseURL + "fpx-delaer-truck-list.php";
	public static String order_query_data_status_update = baseURL + "order_query_data_status_update.php";

	public static String the_pop_success_url = "the_pop_success_url.php";
	public static String the_pop_success_url_ne = "the_pop_success_url_ne.php";
	public static String the_pop_cancel_url = "the_pop_cancel_url.php";
	public static String the_pop_cancel_url_ne = "the_pop_cancel_url_ne.php";
}
