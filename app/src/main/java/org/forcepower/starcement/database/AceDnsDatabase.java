package org.forcepower.starcement.database;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.branch_code;
import static org.forcepower.starcement.constants.Constants.dateString;
import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;
import static org.forcepower.starcement.util.Utils.print_log_d;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;

import org.forcepower.starcement.bean.BackupTableRow;
import org.forcepower.starcement.bean.BranchMasterDetails;
import org.forcepower.starcement.bean.CustomerDetails;
import org.forcepower.starcement.bean.DatabaseStructure;
import org.forcepower.starcement.bean.DestinationMaster;
import org.forcepower.starcement.bean.DumpMaster;
import org.forcepower.starcement.bean.EmployeeDetails;
import org.forcepower.starcement.bean.MenuDetails;
import org.forcepower.starcement.bean.ProductDetails;
import org.forcepower.starcement.bean.ProductMasterDetails;
import org.forcepower.starcement.bean.SchemeDetails;
import org.forcepower.starcement.bean.SchemeMasterDetails;
import org.forcepower.starcement.bean.SelfAppraisalDetailsCustomerWise;
import org.forcepower.starcement.bean.SelfAppraisalDetailsProductGroupWise;
import org.forcepower.starcement.bean.SelfAppraisalDetailsProductWise;
import org.forcepower.starcement.bean.SurveyFormDetails;
import org.forcepower.starcement.bean.UserDetails;
import org.forcepower.starcement.constants.Constants;

import java.util.ArrayList;
import java.util.Arrays;

public final class AceDnsDatabase extends SQLiteOpenHelper {

	private static final String DATABASE_NAME = "Star.db";
	private static final int DATABASE_VERSION = 1;

	String Lock = "dbLock";
	SQLiteDatabase database;
	ArrayList<BackupTableRow> rowList;
	String[] columnArray;

	Context mContext;
	public AceDnsDatabase(Context context) {
		super(context, get_direcory_path(context) + DATABASE_NAME, null,DATABASE_VERSION);
		try
		{
			mContext = context;
			closeDatabase();
			String databasepath=get_direcory_path(mContext)+DATABASE_NAME;
			database = SQLiteDatabase.openDatabase(databasepath, null, SQLiteDatabase.NO_LOCALIZED_COLLATORS);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	@Override
	public void onCreate(SQLiteDatabase db) {}

	@Override
	public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {}

	public long createAppTables(ArrayList<DatabaseStructure> queryList) {
		long result = -1;
		try {
			for (int ii = 0; ii < queryList.size(); ii++) {
				DatabaseStructure currentObj = queryList.get(ii);
				if (currentObj.getTableName().equalsIgnoreCase("menu_details")) {
					Constants.isMenuDetailsUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("order_form_details")) {
					Constants.isOrderFormDetailsUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("survey_form_details")) {
					Constants.isSurveyFormDetailsUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("market_feedback_details")) {
					Constants.isMarketFeedbackDetailsUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("sauda_form_details")) {
					Constants.isSaudaFormDetailsUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("product_details")) {
					Constants.isProductDetailsUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("user_details")) {
					Constants.isUserDetailsUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("route_plan_details")) {
					Constants.isRoutePlanDetailsUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("route_master")) {
					Constants.isRouteTableUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("distributor_route_relation")) {
					Constants.isDistributorRouteTableUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("customer_master")) {
					Constants.isCustomerTableUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("bank_master")) {
					Constants.isBankTableUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("product_group_master")) {
					Constants.isProductGroupTableUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("product_sub_group_master")) {
					Constants.isProductSubGroupTableUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("product_brand_master")) {
					Constants.isProductBrandTableUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("product_master")) {
					Constants.isProductTableUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("mrp")) {
					Constants.isMrpTableUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("sauda_mrp")) {
					Constants.isSaudaMrpTableUpdated = true;
				}

				if (currentObj.getTableName().equalsIgnoreCase("prev_stock_counting_master")) {
					Constants.isPrevStockCountingUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("rds_master")) {
					Constants.isRDSTableUpdated = true;
				}

				if (currentObj.getTableName().equalsIgnoreCase("closing_stock")) {
					Constants.isClosingStockUpdated = true;
				}

				if (currentObj.getTableName().equalsIgnoreCase("loyalty_card_holder_master")) {
					Constants.isLoyaltyTableUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("mis_transaction_log")) {
					Constants.isMISTransactionTableUpdated = true;
				}

				if (currentObj.getTableName().equalsIgnoreCase("goods_in_transit")) {
					Constants.isGITTableUpdated = true;
				}

				if (currentObj.getTableName().equalsIgnoreCase("loyalty_purchase_details")) {
					Constants.isLoyaltyPurchaseUpdated = true;
				}

				if (currentObj.getTableName().equalsIgnoreCase("customer_branch_relation")) {
					Constants.isCustBranchUpdated = true;
				}

				if (currentObj.getTableName().equalsIgnoreCase("broker_master")) {
					Constants.isBrokerUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("order_status")) {
					Constants.isOrderStatus = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("prev_order_counting_master")) {
					Constants.isPreviousOrderCounting = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("sauda_allocation_log")) {
					Constants.isSaudaAllocationUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("sauda_transaction_log")) {
					Constants.isSaudaTransactionUpdated = true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("street_master")) {
					Constants.isStreetUpdated=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("route_customer_plan_transaction")){
					Constants.isRouteCustomer=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("table_view")){
					Constants.isTableView=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("survey_publish")){
					Constants.isSurveyPublishTableUpdated=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("offer_publish")){
					Constants.isOfferPublishTableUpdated=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("fs_survey_publish")){
					Constants.isFsSurveyPublishTableUpdated=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("branch_master")){
					Constants.isBranchUpdated=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("destination_master")){
					Constants.isDestinationUpdated=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("emp_master")){
					Constants.isEmployeeUpdated=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("non_trade_customer_master")){
					Constants.isNonTradeCustomer=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("survey_input")){
					Constants.isSurveyTableUpdated=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("self_appraisal_details")){
					Constants.isSelfAppraisalDetailsTableUpdated=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("self_appraisal_customer_wise")){
					Constants.isSelfAppraisalCustomerWiseTableUpdated=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("self_appraisal_branch_wise")){
					Constants.isSelfAppraisalBranchWiseTableUpdated=true;
				}
				if (currentObj.getTableName().equalsIgnoreCase("self_appraisal_product_wise")){
					print_log_d("","");
				}

				if (currentObj.getTransaction().equalsIgnoreCase("Y")) {
					BackupData(currentObj);
					String query = currentObj.getQuery();
					String[] queryArray = query.split(";\n");
					database.beginTransaction();
					for (int aa = 0; aa < queryArray.length; aa++) {
						database.execSQL(queryArray[aa]);
					}
					database.setTransactionSuccessful();
					database.endTransaction();
					if (rowList != null && rowList.size() > 0) {
						ReStoreBackupData(currentObj);
					}
				} else {
					String query = currentObj.getQuery();
					String[] queryArray = query.split(";\n");
					database.beginTransaction();
					for (int aa = 0; aa < queryArray.length; aa++) {
						database.execSQL(queryArray[aa]);
					}
					database.setTransactionSuccessful();
					database.endTransaction();
				}
			}
			result = 1;
		} catch (Exception e) {
			print_log_d("Database Creation","Exception " + e);
			result = -1;
		} finally {}
		return result;
	}

	public void insertOrUpdateAppInfo(String nickName, String appVersion,String dbVersion,String condition) {
		Cursor cursors=null;
		cursors = database.rawQuery("SELECT app_version FROM app_info",null);
		if (cursors.getCount() > 0) {
			database.beginTransaction();
			try {
				ContentValues cv = new ContentValues();
				cv.put("db_version", dbVersion);
				if(condition.equalsIgnoreCase("Y")){
					cv.put("base_url", Constants.baseURL);
				}
				synchronized (Lock) {
					database.update("app_info", cv, null, null);
					print_log_d("app_info:", "Updated");
				}
				database.setTransactionSuccessful();
			} catch (SQLException e) {
				print_log_d("app_info", "Exception:" + e);
			} finally {
				database.endTransaction();
				if(cursors!=null){
					cursors.close();
				}
			}
		} else {
			database.beginTransaction();
			try {
				ContentValues cv = new ContentValues();
				cv.put("nick_name", nickName);
				cv.put("app_version", appVersion);
				cv.put("db_version", dbVersion);
				cv.put("base_url", Constants.baseURL);
				synchronized (Lock) {
					long result = database.insertWithOnConflict("app_info",
							null, cv, SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("App_info:", "Data Insert status " + result);
				}
				database.setTransactionSuccessful();
			} catch (SQLException e) {
				print_log_d("App_info", "Exception:" + e);
			} finally {
				database.endTransaction();
			}
		}
	}

	public void insertToEmployeeMaster() {}

	public boolean checkLastLoginSuccessfull() {
		boolean status = false;
		String currentDate = dateString.substring(0, 4) + "-"
				+ dateString.substring(4, 6) + "-"
				+ dateString.substring(6, 8);
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("Select flag FROM employee_master_login where emp_code=? AND date=?",new String[] {get_emp_or_customer_code(mContext),currentDate });
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				int flagStatus = cursor.getInt(0);
				if (flagStatus == 1) {
					status = true;
				}
			}
			cursor.close();
		} catch (Exception e) {
			print_log_d("Employee Master Login", "Exception:" + e);
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return status;
	}

	public long updateEmployeeMaster() {
		long status = 0;
		database.beginTransaction();
		String currentDate = dateString.substring(0, 4) + "-"
				+ dateString.substring(4, 6) + "-"
				+ dateString.substring(6, 8);
		try {
			ContentValues cv = new ContentValues();
			cv.put("date", currentDate);
			cv.put("flag", 0);
			synchronized (Lock) {
				status = database.update("employee_master_login", cv,"emp_code=?",new String[] { get_emp_or_customer_code(mContext) });
				print_log_d("EmployeeMaster:", "Updated");
			}
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("EmployeeMaster", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long updateEmployeeMasterFlag() {
		long status = 0;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("flag", 1);
			synchronized (Lock) {
				status = database.update("employee_master_login", cv,"emp_code=?",new String[] { get_emp_or_customer_code(mContext) });
			}
			print_log_d("EmployeeMaster:", "Updated");
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Employee Master", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public EmployeeDetails getEmployeeObj() {
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT * FROM employee_master_login", new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				EmployeeDetails detailsObj = new EmployeeDetails();
				detailsObj.setEmpCode(cursor.getString(0));
				detailsObj.setDate(cursor.getString(1));
				detailsObj.setEmpName(cursor.getString(2));
				detailsObj.setDeviceID(cursor.getString(3));
				detailsObj.setNewPassword(cursor.getString(4));
				detailsObj.setSaleAccess(cursor.getString(5));
				detailsObj.setAppVersion(cursor.getString(7));
				detailsObj.setUpdationFlag(cursor.getString(8));
				cursor.close();
				return detailsObj;
			}
		} catch (Exception e) {
			print_log_d("Employee", "Exception:" + e);
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return null;
	}

	public void deleteNonIncrementalData() {
		deleteOutstandingMaster();
		deleteVendorMaster();
		deleteSchemeDetails();
		deleteRedeemeDetails();
		deleteUserAccessDetails();
	}

	public void deleteUserAccessDetails() {
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM user_access");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_log_d("User Access Delete", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
	}

	public void deleteSchemeDetails() {
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM scheme_details");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_log_d("Scheme Details Delete", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
	}

	public void deleteRedeemeDetails() {
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM redeeme_details");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_log_d("Redeeme Details Delete", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
	}

	public void deleteVendorMaster() {
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM vendor_master");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_log_d("Vendor Details Delete", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
	}

	public void deleteOutstandingMaster() {
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM outstanding_master");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_log_d("Outstanding Details Delete", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
	}

	public void getMenuDetailsObj() {
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("select * FROM menu_details",new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				Constants.menuDetailsObj = new MenuDetails();
				Constants.menuDetailsObj.setMenuId(cursor.getString(0));
				Constants.menuDetailsObj.setUserid(cursor.getString(1));
				Constants.menuDetailsObj.setAttendance(cursor.getString(2));
				Constants.menuDetailsObj.setRoutePlan(cursor.getString(3));
				Constants.menuDetailsObj.setOrder(cursor.getString(4));
				Constants.menuDetailsObj.setCollection(cursor.getString(5));
				Constants.menuDetailsObj.setStkAudit(cursor.getString(6));
				Constants.menuDetailsObj.setBusinessProspect(cursor.getString(7));
				Constants.menuDetailsObj.setTourExp(cursor.getString(8));
				Constants.menuDetailsObj.setCaptureImage(cursor.getString(9));
				Constants.menuDetailsObj.setNotesInfo(cursor.getString(10));
				Constants.menuDetailsObj.setActivityReport(cursor.getString(11));
				Constants.menuDetailsObj.setLoyalty(cursor.getString(12));
				Constants.menuDetailsObj.setMisReport(cursor.getString(13));
				Constants.menuDetailsObj.setDeleteTransaction(cursor.getString(14));
				Constants.menuDetailsObj.setLoadingFreight(cursor.getString(15));
				Constants.menuDetailsObj.setSaudaAllocation(cursor.getString(16));
				Constants.menuDetailsObj.setSurvey(cursor.getString(17));
				Constants.menuDetailsObj.setSampling(cursor.getString(18));
				Constants.menuDetailsObj.setReplacement(cursor.getString(19));
				Constants.menuDetailsObj.setMarketFeedback(cursor.getString(20));
				Constants.menuDetailsObj.setSaudaAllocationfromApp(cursor.getString(21));
				Constants.menuDetailsObj.setPendingContract(cursor.getString(22));
				Constants.menuDetailsObj.setSaudaMis(cursor.getString(23));
				Constants.menuDetailsObj.setOrderStatus(cursor.getString(24));
				Constants.menuDetailsObj.setCheckOut(cursor.getString(25));
				Constants.menuDetailsObj.setSaudaOutstanding(cursor.getString(26));
				Constants.menuDetailsObj.setSalePerFormance(cursor.getString(27));
				Constants.menuDetailsObj.setCheckInOut(cursor.getString(28));
				Constants.menuDetailsObj.setOutstanding(cursor.getString(29));
				Constants.menuDetailsObj.setOutstandingAgeing(cursor.getString(30));
				Constants.menuDetailsObj.setTargetAcheivement(cursor.getString(31));
				Constants.menuDetailsObj.setWholeSaleInfo(cursor.getString(32));
				Constants.menuDetailsObj.setSelfAppraisalDetails(cursor.getString(33));
				Constants.menuDetailsObj.setYellowCard(cursor.getString(34));
				Constants.menuDetailsObj.setCatalogue(cursor.getString(35));
				Constants.menuDetailsObj.setCatalogueUrl(cursor.getString(36));
				Constants.menuDetailsObj.setTelephonicTransaction(cursor.getString(37));
				Constants.menuDetailsObj.setTDAllocation(cursor.getString(38));
				Constants.menuDetailsObj.setcatalogue_dependency(cursor.getString(39));
				Constants.menuDetailsObj.setTD_allocation_vertical(cursor.getString(40));
				Constants.menuDetailsObj.setrun_time_TD_approval_vertical(cursor.getString(41));
				Constants.menuDetailsObj.setquotation(cursor.getString(42));
				Constants.menuDetailsObj.setCRM_app(cursor.getString(43));
				Constants.menuDetailsObj.setISP(cursor.getString(44));
				Constants.menuDetailsObj.setmonthly_report_mail(cursor.getString(45));
				Constants.menuDetailsObj.setretailer_app(cursor.getString(46));
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
	}

	public void getUserDetailsObj() {
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT * FROM user_details",new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				Constants.userDetailsObj = new UserDetails();
				Constants.userDetailsObj.setUserId(cursor.getString(0));
				Constants.userDetailsObj.setName(cursor.getString(1));
				Constants.userDetailsObj.setAddress(cursor.getString(2));
				Constants.userDetailsObj.setPhoneNo(cursor.getString(3));
				Constants.userDetailsObj.setEmail(cursor.getString(4));
				Constants.userDetailsObj.setLicenseKey(cursor.getString(5));
				Constants.userDetailsObj.setNoUser(cursor.getString(6));
				Constants.userDetailsObj.setNickName(cursor.getString(7));
				Constants.userDetailsObj.setNoBranches(cursor.getString(8));

				Constants.userDetailsObj.setEmailHierarchy(cursor.getString(10));
				Constants.userDetailsObj.setVerticalFields(cursor.getString(11));
				Constants.userDetailsObj.setVerticalFieldsValue(cursor.getString(12));
				Constants.userDetailsObj.setPreviousStock(cursor.getString(13));
				Constants.userDetailsObj.setMultipleProspect(cursor	.getString(14));
				Constants.userDetailsObj.setMultipleProspectValue(cursor.getString(15));
				Constants.userDetailsObj.setStkAuditScan(cursor.getString(16));
				Constants.userDetailsObj.setStockAuditRate(cursor.getString(17));
				Constants.userDetailsObj.setLocation_drag_drop(cursor.getString(18));
				Constants.userDetailsObj.setTourPlanDayWise(cursor.getString(19));
				Constants.userDetailsObj.setCheckInOutTypeVal(cursor.getString(20));
				Constants.userDetailsObj.setFcm(cursor.getString(21));
				Constants.userDetailsObj.setMinimumStock(cursor.getString(22));
				Constants.userDetailsObj.setStockAuditUnit(cursor.getString(23));
				Constants.userDetailsObj.setstk_audit_irrespective_routeplan(cursor.getString(24));
				Constants.userDetailsObj.setstk_audit_cust_type(cursor.getString(25));
				Constants.userDetailsObj.setnotes_info_hint_remarks(cursor.getString(26));
				Constants.userDetailsObj.setnotes_info_upload_photo(cursor.getString(27));
				Constants.userDetailsObj.setcountry(cursor.getString(28));
				Constants.userDetailsObj.settimeZone(cursor.getString(29));
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
	}

	public void getProductDetailsObj() {
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT * FROM product_details",new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				Constants.productDetailsObj = new ProductDetails();
				Constants.productDetailsObj.setProductId(cursor.getString(0));
				Constants.productDetailsObj.setUserId(cursor.getString(1));
				Constants.productDetailsObj.setNoFilter(cursor.getString(2));
				Constants.productDetailsObj.setCol1(cursor.getString(3));
				Constants.productDetailsObj.setCol2(cursor.getString(4));
				Constants.productDetailsObj.setCol3(cursor.getString(5));
				Constants.productDetailsObj.setCol4(cursor.getString(6));
				Constants.productDetailsObj.setUomWiseMRP(cursor.getString(7));
				Constants.productDetailsObj.setSaudaFilter(cursor.getString(8));
				Constants.productDetailsObj.setProductBusinessProspect(cursor.getString(9));
				Constants.productDetailsObj.setBranchWiseProduct(cursor.getString(10));
				Constants.productDetailsObj.setSecondaryUnit(cursor.getString(11));
				Constants.productDetailsObj.setDestinationPriceList(cursor.getString(12));
				Constants.productDetailsObj.setDestinationOrderTypePriceList(cursor.getString(13));
				Constants.productDetailsObj.setStateWiseMrp(cursor.getString(14));
				Constants.productDetailsObj.setMultipleRate(cursor.getString(15));
				Constants.productDetailsObj.setProductQtyWiseTD(cursor.getString(16));
				Constants.productDetailsObj.setFocusProduct(cursor.getString(17));

				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
	}

	public long insertToCustomerMaster(ArrayList<CustomerDetails> custList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		TruncateTableByTableName("customer_master");
		try {
			for (ii = 0; ii < custList.size(); ii++) {
				CustomerDetails obj = custList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("customer_code", obj.getCustomerCode());
				cv.put("customer_name", obj.getCustomerName());
				cv.put("route_code", obj.getRouteCode());
				cv.put("emp_code", get_emp_or_customer_code(mContext));
				cv.put("black_list", obj.getIsBlackList());
				cv.put("acedns", obj.getIsACEDNS());
				cv.put("credit_limit", obj.getCreditLimit());
				cv.put("current_balance", obj.getCurrentBalance());
				cv.put("TD", obj.getTradeDiscount());
				cv.put("cust_type", obj.getCustomerType());
				cv.put("route_name", obj.getRouteName());
				cv.put("pin", obj.getPin());
				cv.put("phone_no", obj.getNumber());
				cv.put("rds_tag", obj.getRdsTag());
				cv.put("flag", obj.getFlag());
				cv.put("sauda_validity_period",obj.getSaudaValidityPeriod());
				cv.put("address",obj.getAddress());
				cv.put("landline_no",obj.getLandlineNo());
				cv.put("owner_name",obj.getOwnerName());
				cv.put("owner_phone",obj.getOwnerPhone());
				cv.put("cust_class",obj.getCustClass());
				cv.put("weekly_closing_day",obj.getWeeklyClosingDay());
				cv.put("coverage_type",obj.getCoverageType());
				cv.put("TIN",obj.getTIN());
				cv.put("PAN",obj.getPAN());
				cv.put("minimum_stock",obj.getMinimumStock());
				cv.put("branch_code",obj.getBranchCode());
				cv.put("Visit_day",obj.getVisitDay());
				cv.put("email",obj.getEmail());
				cv.put("sauda_limit",obj.getSaudaLimit());
				cv.put("pending_qty",obj.getPendingQty());
				cv.put("SAP_code",obj.get_SAP_code());

				synchronized (Lock) {
					database.insert("customer_master", null,cv);
					print_log_d("Customer_Master_er44f "+ii, " Data Inserted");
				}

				obj=null;
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("cstmr_er44f_err_3411 ", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToProductMaster(ArrayList<ProductMasterDetails> prodList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		TruncateTableByTableName("product_master");
		try {
			for (ii = 0; ii < prodList.size(); ii++) {
				ProductMasterDetails detailObj = prodList.get(ii);
				ContentValues cv = new ContentValues();

				cv.put("product_group_name", "N");
				cv.put("product_sub_group_code", "N");
				cv.put("product_sub_group_name", "N");
				cv.put("product_brand_code", "N");
				cv.put("product_brand_name", "N");
				cv.put("black_list", "N");
				cv.put("acedns", "N");
				cv.put("cl_stk", "n");
				cv.put("uom1","N");
				cv.put("uom2", "N");
				cv.put("conversion_factor", "N");
				cv.put("pack_size", "N");
				cv.put("uom3", "N");
				cv.put("conversion_factor_two", "N");
				cv.put("TD", "N");
				cv.put("vertical_value", "N");
				cv.put("secondary_unit", "N");
				cv.put("secondary_unit", "N");
				cv.put("focus", "N");
				cv.put("weightage", "N");
				cv.put("vat", "N");
				cv.put("addl_vat", "N");
				cv.put("freight_cost", "N");

				cv.put("prod_code", detailObj.getProdCode());
				cv.put("dns_prod_code", detailObj.getDnsProdCode());
				cv.put("prod_desc", detailObj.getDesc());
				cv.put("branch_code", detailObj.getBranchCode());
				cv.put("product_group_code", detailObj.getGrpCode());
				cv.put("black_list", "No");

				synchronized (Lock) {
					long val = database.insertWithOnConflict("product_master", null,
							cv, SQLiteDatabase.CONFLICT_REPLACE);
					print_log_d("Product_master3914 ", "branchwise_product_data_download_239 Data Inserted "+ val);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			Log.d("TAG", "branchwise_product_data_download_239 insertToProductMaster: "+e.getMessage());
			print_log_d("Product_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		print_log_d("Product_master3914 ", "branchwise_product_data_download_239 status "+ status);
		getAllTableNames(database);
		return status;
	}

	public void getAllTableNames(SQLiteDatabase db) {
		Cursor cursor = null;
		try {
			// Query sqlite_master for all tables
			cursor = db.rawQuery(
					"SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'android_%' AND name != 'sqlite_sequence'",
					null
			);

			if (cursor.moveToFirst()) {
				do {
					Log.d("TAG", "branchwise_product_data_download_239 getAllTableNames: "+cursor.getString(0));
				} while (cursor.moveToNext());
			}
		} finally {
			if (cursor != null) cursor.close();
		}
	}

	public long insertToScheme(ArrayList<SchemeMasterDetails> prodList) {
		long status = 0;
		int ii = 0;
		try
		{
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		database.beginTransaction();
		TruncateTableByTableName("branch_schemes_PDF");
		try {
            for (ii = 0; ii < prodList.size(); ii++) {
				SchemeMasterDetails detailObj = prodList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("branch_code", detailObj.getBranchCode());
				cv.put("PDF_file_name", detailObj.getBrndName());
				cv.put("acedns", detailObj.getIsAcedns());

				synchronized (Lock) {
						database.insertWithOnConflict("branch_schemes_PDF", "PDF_file_name",
								cv, SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("branch_schemes_PDF:", "Data Inserted");
					}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("branch_schemes_PDF", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToDestinationMaster(ArrayList<DestinationMaster> destinationList, int noColumn) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		TruncateTableByTableName("destination_master");

		try {
			for (ii = 0; ii < destinationList.size(); ii++) {
				DestinationMaster detailObj = destinationList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("destination_code", detailObj.getDestinationCode());
				cv.put("destination_name", detailObj.getDestinationName());
				if(noColumn == 3)
					cv.put("ex_for_type", detailObj.getExForType());

				database.insertWithOnConflict("destination_master", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				print_log_d("destination_master:", "Data Inserted");
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e)
		{
			print_log_d("Destination Master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToDumpMasterMaster(ArrayList<DumpMaster> destinationList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		TruncateTableByTableName("branch_dump");

		try {
			for (ii = 0; ii < destinationList.size(); ii++) {
				DumpMaster detailObj = destinationList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("branch_code", detailObj.get_branch_code());
				cv.put("dump_code", detailObj.get_dump_code());
				cv.put("dump_name", detailObj.get_dump_name());
				cv.put("acedns", detailObj.get_acedns());
				cv.put("is_plant", detailObj.get_is_plant());
				cv.put("download_time", detailObj.get_download_time());

				database.insertWithOnConflict("branch_dump", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				print_log_d("branch_dump:", "Data Inserted");
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e)
		{
			print_log_d("branch_dump Master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToBranchMaster(ArrayList<BranchMasterDetails> branchList) {
		long status = 0;
		int ii = 0;
		TruncateTableByTableName("branch_master");
		database.beginTransaction();
		try {
			for (ii = 0; ii < branchList.size(); ii++) {
				BranchMasterDetails detailObj = branchList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("company_code", detailObj.getCompanyCode());
				cv.put("branch_code", detailObj.getBranchCode());
				cv.put("branch_name", detailObj.getBranchName());
				cv.put("hq", detailObj.getHq());
				cv.put("plant_name", detailObj.getPlantName());

				if (Constants.isFirstLoginOfApp || Constants.isBranchUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict("branch_master", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("branch_master:", "Data Inserted");
					}
				} else {
					database.execSQL("DELETE FROM branch_master WHERE branch_code='"+ detailObj.getBranchCode().replace("'", "") + "'");
					database.insertWithOnConflict("branch_master", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("branch_master:", "Data Updated");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Branch_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public void GETSurveyFormDetails(){
		Cursor cursor = null;
		Constants.surveyFormDetailsObj= new SurveyFormDetails();
		try {
			cursor = database.rawQuery("SELECT * FROM survey_form_details", null);
			cursor.moveToFirst();
			Constants.surveyFormDetailsObj.setSurveyFormId(cursor.getString(0));
			Constants.surveyFormDetailsObj.setSurveyUserId(cursor.getString(1));
			Constants.surveyFormDetailsObj.setSurveyMenu(cursor.getString(2));
			Constants.surveyFormDetailsObj.setSurveyType(cursor.getString(3));
			Constants.surveyFormDetailsObj.setSurveyTypeDetails(cursor.getString(4));
			Constants.surveyFormDetailsObj.setSurveyMallSurveyRelation(cursor.getString(5));
			Constants.surveyFormDetailsObj.setSurveySubTypeDetails(cursor.getString(6));
			Constants.surveyFormDetailsObj.setSurveyOTP(cursor.getString(7));
			Constants.surveyFormDetailsObj.setSurveyLayer(cursor.getString(8));
			Constants.surveyFormDetailsObj.setSurveySubMenu(cursor.getString(9));
			Constants.surveyFormDetailsObj.setSurveySubMenuDetails(cursor.getString(10));
			Constants.surveyFormDetailsObj.setSurveyOutletMenu(cursor.getString(11));
			Constants.surveyFormDetailsObj.setSurveyRoutePlan(cursor.getString(12));
			Constants.surveyFormDetailsObj.setSurveyOtherText(cursor.getString(13));
			Constants.surveyFormDetailsObj.setSurveyReportRowId(cursor.getString(14));
			Constants.surveyFormDetailsObj.setCustomerEmailUpdate(cursor.getString(15));
			cursor.close();
		} catch (Exception e) {
			print_Log_d("Exception:::::" + e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
	}

	public long InsertToProductWiseTargetAchievement(ArrayList<SelfAppraisalDetailsProductWise> dataList) {
		long status = 0;
		int ii = 0;
		TruncateTableByTableName("self_appraisal_product_wise");
		database.beginTransaction();
		try
		{
			for (ii = 0; ii < dataList.size(); ii++)
			{
				SelfAppraisalDetailsProductWise obj = dataList.get(ii);
				ContentValues cv = new ContentValues();

				cv.put("prod_code", obj.getProductCode());
				cv.put("prod_desc", obj.getProductName());
				cv.put("emp_code", obj.getempCode());
				cv.put("month", obj.getmonth());
				cv.put("target", obj.gettarget());
				cv.put("achievement", obj.getachievement());
				cv.put("prev_y_target", obj.get_prev_y_target()); 
				cv.put("prev_y_achievement", obj.get_prev_y_achievement()); 
				synchronized (Lock)
				{
					database.insertWithOnConflict("self_appraisal_product_wise", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		}
		catch (SQLException e)
		{
			e.printStackTrace();
		}
		finally
		{
			database.endTransaction();
		}
		return status;
	}

	public boolean DataTableByTableName(String tableName) {
		boolean dataAvailaable = false;

		try {
			final Cursor cursor = database.rawQuery("SELECT * FROM "+tableName,new String[] {});
			if (cursor.getCount() > 0) {
				dataAvailaable = true;
			}
		} catch (Exception e) {
			print_Log_d("Exception:::::::::::" + e);
		}
		print_Log_d("dataAvailaable:::::::::::" + dataAvailaable);
		return dataAvailaable;
	}

	public void TruncateTableByTableName(String tableName) {
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM "+tableName);
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_Log_d("Exception:::::::::::" + e);
		} finally {
			database.endTransaction();
		}

	}
	public static void printCursorAsTable(Cursor cursor) {
		if (cursor == null || cursor.getCount() == 0) {
			Log.d("CURSOR_TABLE", "Cursor is empty or null.");
			return;
		}

		StringBuilder table = new StringBuilder();
		String[] columnNames = cursor.getColumnNames();

		// Header
		table.append("| ");
		for (String columnName : columnNames) {
			table.append(String.format("%-20s", columnName)).append(" | ");
		}
		table.append("\n");

		// Divider
		table.append("|");
		for (int i = 0; i < columnNames.length; i++) {
			table.append("--------------------------------------------------------------------------|");
		}
		table.append("\n");

		// Rows
		cursor.moveToFirst();
		do {
			table.append("| ");
			for (String columnName : columnNames) {
				int colIndex = cursor.getColumnIndex(columnName);
				String value = cursor.isNull(colIndex) ? "NULL" : cursor.getString(colIndex);
				table.append(String.format("%-20s", value)).append(" | ");
			}
			table.append("\n");
		} while (cursor.moveToNext());

		Log.d("CURSOR_TABLE", "\n" + table.toString());
	}
	public static void logAllTableNames(SQLiteDatabase db) {
		Cursor cursor = null;
		try {
			cursor = db.rawQuery("SELECT name FROM Star WHERE type='table'", null);
			if (cursor.moveToFirst()) {
				do {
					String tableName = cursor.getString(0);
					Log.d("TABLE_LIST", "TABLE_LIST: " + tableName);
				} while (cursor.moveToNext());
			} else {
				Log.d("TABLE_LIST", "No tables found in the database.");
			}
		} catch (Exception e) {
			Log.e("TABLE_LIST", "Error while fetching table list: " + e.getMessage());
		} finally {
			if (cursor != null) cursor.close();
		}
	}
	public ArrayList<DestinationMaster> getMySubDealerList(final String params) {
		ArrayList<DestinationMaster> destinationList = new ArrayList<DestinationMaster>();
		Cursor cursor=null;
		try
		{
			if(params.equalsIgnoreCase("rssd"))
			{
				if(get_user_type(mContext).equalsIgnoreCase("broker"))
				{
					String query_dealer = "SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type == 'RSSD' AND customer_code !=  '"+ get_selected_customer_code(mContext)+"' ORDER BY customer_name ASC";
					print_log_d("QUERY_BROKER broker", query_dealer);
					cursor = database.rawQuery(query_dealer, new String[] {});
				}
				else
				{
					String query_dealer = "SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type == 'RSSD' AND customer_code !=  '"+ get_emp_or_customer_code(mContext)+"' ORDER BY customer_name ASC";
					print_log_d("QUERY_BROKER !broker", query_dealer);
					cursor = database.rawQuery(query_dealer, new String[] {});
				}
			}
			else if(get_user_type(mContext).equalsIgnoreCase("broker"))
			{
				String query_broker = "SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type IN( 'Sub Dealer','RSSD') AND rds_tag =  '"+ get_selected_customer_code(mContext)+"' ORDER BY customer_name ASC";
				print_log_d("QUERY_BROKER ", query_broker);
				cursor = database.rawQuery(query_broker, new String[] {});
			}
			else
			{
				String query_dealer = "SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type != 'Dealer' ORDER BY customer_name ASC";
						print_log_d("QUERY_BROKER ", query_dealer);
				cursor = database.rawQuery(query_dealer, new String[] {});
			}
			print_log_d("QUERY_BROKER ", cursor.getCount()+"");
			printCursorAsTable(cursor);
			logAllTableNames(database);
			if (cursor.getCount() > 0) {

				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					final DestinationMaster detailsObj = new DestinationMaster();
					detailsObj.setRow_position(ii);
					detailsObj.set_address(cursor.getString(0)); 
					detailsObj.setDestinationCode(cursor.getString(0));
					detailsObj.setDestinationName(cursor.getString(1));
					detailsObj.setSubDealerCode(cursor.getString(2));
					detailsObj.set_phone_no(cursor.getString(3));
					detailsObj.set_SAP_code(cursor.getString(4));
					destinationList.add(detailsObj);
					Log.d("TAG", "getMySubDealerList: "+cursor);
					cursor.moveToNext();
				}
				cursor.close();
				return destinationList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return destinationList;
	}

	public ArrayList<DestinationMaster> getMyDealerList() {
		ArrayList<DestinationMaster> destinationList = new ArrayList<DestinationMaster>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type = 'Dealer' ORDER BY customer_name ASC", new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					final DestinationMaster detailsObj = new DestinationMaster();
					detailsObj.setRow_position(ii);
					detailsObj.setDestinationCode(cursor.getString(0));
					detailsObj.setDestinationName(cursor.getString(1));
					detailsObj.setSubDealerCode(cursor.getString(2));
					detailsObj.set_phone_no(cursor.getString(3));
					detailsObj.set_SAP_code(cursor.getString(4));
					destinationList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return destinationList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return destinationList;
	}

	public ArrayList<DestinationMaster> getDesti_List() {
		ArrayList<DestinationMaster> destinationList = new ArrayList<DestinationMaster>();
		Cursor cursor=null;
		try
		{
		    String selectQuery = "SELECT destination_code, destination_name, ex_for_type FROM destination_master";
			cursor = database.rawQuery(selectQuery, new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					DestinationMaster detailsObj = new DestinationMaster();
					detailsObj.setDestinationCode(cursor.getString(0));
					detailsObj.setDestinationName(cursor.getString(1));
					detailsObj.setExForType(cursor.getString(2));
					destinationList.add(detailsObj);
					cursor.moveToNext();
					detailsObj=null;
				}
				cursor.close();
				return destinationList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return destinationList;
	}

	public ArrayList<DumpMaster> getDesti_Dump_List() {
		ArrayList<DumpMaster> destinationList = new ArrayList<>();
		Cursor cursor=null;
		try
		{
		    String selectQuery = "SELECT dump_code, dump_name FROM branch_dump ORDER BY dump_name ASC";

			cursor = database.rawQuery(selectQuery, new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					DumpMaster detailsObj = new DumpMaster();
					detailsObj.set_dump_code(cursor.getString(0));
					detailsObj.set_dump_name(cursor.getString(1));

					destinationList.add(detailsObj);
					cursor.moveToNext();
					detailsObj=null;
				}
				cursor.close();
				return destinationList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return destinationList;
	}

	public ArrayList<SelfAppraisalDetailsCustomerWise> getTargetForAllMonths(String tableName, String customer_code) {

		ArrayList<SelfAppraisalDetailsCustomerWise> targetList = new ArrayList<>();
		try
		{
			String mData = "04, 05, 06, 07, 08, 09, 10, 11, 12, 01, 02, 03";
			String[] mArray = mData.split(",");
			for(int index=0;index<12;index++)
			{
				Cursor cursor=null;
				try
				{
					String selectQuery = "";
					if(tableName.equalsIgnoreCase("c"))
					{
						selectQuery = "SELECT SUM(target), SUM(achievement) FROM self_appraisal_product_wise WHERE month="+ "'"+mArray[index].trim()+"' AND emp_code = '"+customer_code+"'";
					}
					else
					{
						selectQuery = "SELECT SUM(prev_y_target), SUM(prev_y_achievement) FROM self_appraisal_product_wise WHERE month="+ "'"+mArray[index].trim()+"' AND emp_code = '"+customer_code+"'";
					}
					cursor = database.rawQuery(selectQuery, null);
					cursor.moveToFirst();
					SelfAppraisalDetailsCustomerWise targetListEachMonth=new SelfAppraisalDetailsCustomerWise();
					targetListEachMonth.setmonth(index+"");
					String target = cursor.getString(0);
					if(target==null || target.matches("") || target.matches("null"))
					{
						target="0";
					}
					targetListEachMonth.settarget(target);
					String achievement = cursor.getString(1);
					if(achievement==null || achievement.matches("") || achievement.matches("null"))
					{
						achievement="0";
					}
					targetListEachMonth.setachievement(achievement);
					targetList.add(targetListEachMonth);
				}
				catch (Exception e)
				{
					e.printStackTrace();
				}
				finally
				{
					if(cursor!=null)
					{
						cursor.close();
					}
				}

			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		return targetList;
	}

	public ArrayList<SelfAppraisalDetailsProductGroupWise> getProductGroupWiseTargetForSingleMonth(String curryerPrevious, String customer_code) {
		ArrayList<SelfAppraisalDetailsProductGroupWise> targetList = new ArrayList<>();
		try {

			String mValue = "04, 05, 06, 07, 08, 09, 10, 11, 12, 01, 02, 03";
			String[] mArrayValue = mValue.split(",");

			String mData = "April, May, June, July, August, September, October, November, December, January, February, March";
			String[] mArray = mData.split(",");

			for(int index=0;index<12;index++)
			{
				String selectQuery = "";
				if(curryerPrevious.equalsIgnoreCase("c"))
				{
					selectQuery = "SELECT month, SUM(target), SUM(achievement) FROM self_appraisal_product_wise WHERE month="+ "'"+mArrayValue[index].trim()+"' AND emp_code = '"+customer_code+"'";
				}
				else
				{
					selectQuery = "SELECT month, SUM(prev_y_target), SUM(prev_y_achievement) FROM self_appraisal_product_wise WHERE month="+ "'"+mArrayValue[index].trim()+"' AND emp_code = '"+customer_code+"'";
				}

				Cursor cursor = database.rawQuery(selectQuery, null);
				cursor.moveToFirst();
				SelfAppraisalDetailsProductGroupWise targetListItem = new SelfAppraisalDetailsProductGroupWise();
				targetListItem.setmonth(mArray[index]);
				String target = cursor.getString(1);
				target=target.trim();
				if (target == null || target.matches("") || target.matches("null"))
				{
					target = "0";
				}
				targetListItem.settarget(target);
				String achievement = cursor.getString(2);
				achievement=achievement.trim();
				if (achievement == null || achievement.matches("") || achievement.matches("null"))
				{
					achievement = "0";
				}
				targetListItem.setachievement(achievement);
				targetList.add(targetListItem);
				cursor.close();
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		finally
		{
		}


		return targetList;
	}
	
	public String getTotalByKey(String currentPrev, String targetAchiv, String customer_code){
		String returnValue = "";

		try
		{
			String selectQuery = "SELECT SUM("+targetAchiv+") FROM self_appraisal_product_wise WHERE emp_code = '"+customer_code+"'";
			Cursor cursor = database.rawQuery(selectQuery, null);
			cursor.moveToFirst();
			returnValue = cursor.getString(0);
			cursor.close();

		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		finally
		{
		}


		return returnValue;
	}

	public ArrayList<ProductMasterDetails> getProductMasterList(String parent,
																int filterNo, boolean carryInSales) {

		ArrayList<ProductMasterDetails> productMasterList = new ArrayList<ProductMasterDetails>();
		String selectQuery = "SELECT prod_code, dns_prod_code, prod_desc, product_group_code, branch_code FROM product_master";

		Cursor cursor=null;
		try {
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int i = 0; i < cursor.getCount(); i++)
				{
					ProductMasterDetails prodObj = new ProductMasterDetails();
					prodObj.setProdCode(cursor.getString(0));
					prodObj.setDnsProdCode(cursor.getString(1));
					prodObj.setDesc(cursor.getString(2));
					prodObj.setGrpCode(cursor.getString(3));
					prodObj.setBranchCode(cursor.getString(4));

					productMasterList.add(prodObj);


					cursor.moveToNext();
				}
			}
			cursor.close();
		} catch (Exception e) {
			print_Log_d("Exception :::::::::" + e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return productMasterList;
	}

	public void BackupData(DatabaseStructure tableDetails) {
		Cursor pragmacursor=null;
		Cursor cursor=null;
		try {

			pragmacursor= database.rawQuery("PRAGMA table_info(" + tableDetails.getTableName() + ")",	null);
			if(pragmacursor !=null && pragmacursor.getCount()>0){
				pragmacursor.moveToFirst();
				columnArray = new String[pragmacursor.getCount()];
				for (int xx = 0; xx < pragmacursor.getCount(); xx++) {
					columnArray[xx] = pragmacursor.getString(1);
					pragmacursor.moveToNext();
				}
			}
			if (pragmacursor != null){
				pragmacursor.close();
			}
			cursor = database.rawQuery("SELECT * FROM " + tableDetails.getTableName(), null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				rowList = new ArrayList<BackupTableRow>();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					BackupTableRow currentObj = new BackupTableRow();
					String[] entries = new String[cursor.getColumnCount()];
					for (int aa = 0; aa < cursor.getColumnCount(); aa++) {
						entries[aa] = cursor.getString(aa);
					}
					currentObj.setColumnValues(entries);
					rowList.add(currentObj);
					cursor.moveToNext();
				}
			}
			cursor.close();
		} catch (Exception e) {
			print_log_d("Backup Exception","Table Name " +tableDetails.getTableName() + e);
		} finally {
			if (pragmacursor != null){
				pragmacursor.close();
			}
			if(cursor!=null){
				cursor.close();
			}
		}
	}

	public void ReStoreBackupData(DatabaseStructure tableDetails) {
		try {
			StringBuilder sbinsert = new StringBuilder(" (");
			for (int kk = 0; kk < columnArray.length; kk++) {
				sbinsert.append(columnArray[kk] + ",");
			}
			sbinsert.append(getAdditionalColumns(tableDetails));
			sbinsert.setLength(sbinsert.length() - 1);
			sbinsert.append(")");
			String additionalvalue=getAdditionalValues(tableDetails);
			String prefix = "INSERT into " + tableDetails.getTableName()+ sbinsert.toString() + " values(";
			String postFix = ")";
			for (int ii = 0; ii < rowList.size(); ii++) {
				BackupTableRow currentObj = rowList.get(ii);
				String[] valueArray = currentObj.getColumnValues();
				StringBuilder sb = new StringBuilder(prefix);
				for (int aa = 0; aa < valueArray.length; aa++) {
					if(valueArray[aa]!=null){
						sb.append("'" + valueArray[aa].replace("\"", "") + "',");
					}else{
						sb.append("'',");
					}
				}
				sb.append(additionalvalue);
				sb.setLength(sb.length() - 1);
				sb.append(postFix);
				database.execSQL(sb.toString());
				print_log_d("Restore " +tableDetails.getTableName(), sb.toString());
			}
		} catch (Exception e) {
			print_log_d("Restore","Exception " + e);
		}
	}

	public String getAdditionalColumns(DatabaseStructure tableDetails) {
		StringBuilder sb = new StringBuilder();
		Cursor cursor = database.rawQuery("PRAGMA table_info(" + tableDetails.getTableName() + ")", null);
		cursor.moveToFirst();
		for (int count = 0; count < cursor.getCount(); count++) {
			if (!Arrays.asList(columnArray).contains(cursor.getString(1))) {
				sb.append(cursor.getString(1) + ",");
			}
			cursor.moveToNext();
		}
		if(cursor!=null){
			cursor.close();
		}
		return sb.toString();
	}

	public String getAdditionalValues(DatabaseStructure tableDetails) {
		StringBuilder sb = new StringBuilder();
		Cursor cursor = database.rawQuery("PRAGMA table_info(" + tableDetails.getTableName() + ")", null);
		int oldCount = columnArray.length;
		int newCount = cursor.getCount();
		for (int count = 0; count < newCount - oldCount; count++) {
			sb.append("'" + "" + "',");
		}
		if(cursor!=null){
			cursor.close();
		}
		return sb.toString();
	}

	public String getlastDownloadTime(String tableName) {
		String time = "";
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT last_download_time FROM data_download_log WHERE table_name = ?",new String[] { tableName });
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				time = cursor.getString(0);
			}
			cursor.close();
		} catch (Exception e) {
			print_log_d("Last Update Time","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return time;
	}

	public void insertToLogTable(String timeStamp, String tableName) {
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM data_download_log WHERE table_name = '"+ tableName + "'");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_log_d("Delete Download Log", "Exception " +e);
		} finally {
			database.endTransaction();
		}

		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("table_name", tableName);
			cv.put("last_download_time", timeStamp);
			cv.put("is_download ", (Constants.isDataRefreshed == true ? "no" : "yes"));
			cv.put("is_refresh ", (Constants.isDataRefreshed == true ? "yes" : "no"));
			synchronized (Lock) {
				database.insertWithOnConflict("data_download_log", null, cv,
						SQLiteDatabase.CONFLICT_IGNORE);
				database.setTransactionSuccessful();
				print_log_d("data_download_log:", "Data Inserted");
			}
		} catch (Exception e) {
			print_log_d("Insertion Download Log", "Exception " +e);
		} finally {
			database.endTransaction();
		}
	}

	public void closeDatabase() {
		if(database!=null){
			if (database.isOpen()) {
				try {
					database.close();
					database=null;
				} catch (Exception e) {
					print_log_d("Close Database","Exception:" + e);
				}
			}
		}
	}

	public String getValueFromKey(String key, String selected_customr_code) {
		String value ="";
		Cursor cursor = null;
		try {
			String query = "SELECT "+key+" FROM customer_master WHERE customer_code ='"+selected_customr_code+"'";
			print_log_d("15418_amitabha2715", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				value = cursor.getString(0);
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Employee Hierarchy","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return value;
	}

	public String getBranchCode(String emp_) {
		String branch_code ="";
		Cursor cursor=null;
		try
		{
			String sqlQuery = "SELECT branch_code FROM customer_master WHERE customer_code='" + emp_ + "'";
			print_log_d("amitabha2715_branch_code", sqlQuery + "");
			cursor = database.rawQuery(sqlQuery, null);
			if (cursor!=null && cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				branch_code=cursor.getString(0).trim();
			}
			cursor.close();
		}
		catch (Exception e)
		{
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return branch_code;
	}

	public ArrayList<SchemeDetails> getSchemeFile() {
		ArrayList<SchemeDetails> prodList = new ArrayList<>();
		Cursor cursor=null;
		try
		{
			String sqlQuery = "SELECT PDF_file_name FROM branch_schemes_PDF WHERE branch_code='" + branch_code + "'";
			print_Log_d("branch_schemes_PDF_Q ", sqlQuery);
			cursor = database.rawQuery(sqlQuery, null);

			if (cursor.moveToFirst()) {
				do {
					SchemeDetails detailObj = new SchemeDetails();
					detailObj.setSchemeValue(cursor.getString(0).trim());
					prodList.add(detailObj);

				} while (cursor.moveToNext());
			}

		}
		catch (Exception e)
		{
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return prodList;
	}
}
