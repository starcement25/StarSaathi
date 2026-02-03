package org.forcepower.starcement.database;

import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.cusomer_code_sub_dealer_new_logic;
import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;
import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.MatrixCursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.text.TextUtils;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.util.Utils.print_log_d;

import org.forcepower.starcement.bean.*;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.util.Utils;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.Date;
import java.util.List;

import static org.forcepower.starcement.constants.Constants.UnVerifiedCashReceiveList;
import static org.forcepower.starcement.constants.Constants.allocatedRouteCodeTodayCrm;
import static org.forcepower.starcement.constants.Constants.allocatedRouteNameTodayCrm;
import static org.forcepower.starcement.constants.Constants.dateString;
import static org.forcepower.starcement.constants.Constants.isShowValueSendValueDifferentForChcekBOx;
import static org.forcepower.starcement.constants.Constants.mOrderPriceValidationType;
import static org.forcepower.starcement.constants.Constants.sendColumnDataForSurveyCheckBox;
import static org.forcepower.starcement.constants.Constants.branch_code;
import static org.forcepower.starcement.util.Utils.convertCommaSeparatedListToProperFormat;
import static org.forcepower.starcement.util.Utils.decimal3round;

public final class AceDnsDatabase extends SQLiteOpenHelper {

	private static final String DATABASE_NAME = "Star.db";
	private static final int DATABASE_VERSION = 1;

	String Lock = "dbLock";
	SQLiteDatabase database;
	ArrayList<BackupTableRow> rowList;
	String[] columnArray;
	public ArrayList<Cursor> getData(String Query){
		//get writable database
		SQLiteDatabase sqlDB = this.getWritableDatabase();
		String[] columns = new String[] { "message" };
		//an array list of cursor to save two cursors one has results from the query
		//other cursor stores error message if any errors are triggered
		ArrayList<Cursor> alc = new ArrayList<Cursor>(2);
		MatrixCursor Cursor2= new MatrixCursor(columns);
		alc.add(null);
		alc.add(null);

		try{
			String maxQuery = Query ;
			//execute the query results will be save in Cursor c
			Cursor c = sqlDB.rawQuery(maxQuery, null);

			//add value to cursor2
			Cursor2.addRow(new Object[] { "Success" });

			alc.set(1,Cursor2);
			if (null != c && c.getCount() > 0) {

				alc.set(0,c);
				c.moveToFirst();

				return alc ;
			}
			return alc;
		} catch(SQLException sqlEx){
			print_log_d("printing exception", sqlEx.getMessage());
			//if any exceptions are triggered save the error message to cursor an return the arraylist
			Cursor2.addRow(new Object[] { ""+sqlEx.getMessage() });
			alc.set(1,Cursor2);
			return alc;
		} catch(Exception ex){
			print_log_d("printing exception", ex.getMessage());

			//if any exceptions are triggered save the error message to cursor an return the arraylist
			Cursor2.addRow(new Object[] { ""+ex.getMessage() });
			alc.set(1,Cursor2);
			return alc;
		}
	}


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
	public void onCreate(SQLiteDatabase db) {

	}

	@Override
	public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion)
	{
/*		// Drop older table if existed
		db.execSQL("DROP TABLE IF EXISTS branch_schemes_PDF");
		// Create tables again
		onCreate(db);*/
	}

	public String getDatabaseVersion()
	{
		String dbVersion = "";
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT db_version FROM app_info", new String[] {});
			cursor.moveToFirst();
			dbVersion = cursor.getString(0);
			if(cursor!=null){
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("DB Version","Exception:" + e);
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return dbVersion;
	}

	public long createAppTables(ArrayList<DatabaseStructure> queryList)
	{
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
					String[] queryArray = query.split(";\n");// response -> DROP
					// QUERY ;\n
					// CREATE QUERY
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
					String[] queryArray = query.split(";\n");// response -> DROP
					// QUERY ;\n
					// CREATE QUERY
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
		} finally {
			//database.endTransaction();
		}
		return result;
	}

	public boolean getAttendanceForToday() {
		Cursor cursor=null;
		try {
			String date = dateString.substring(0, 4) + "-"
					+ dateString.substring(4, 6) + "-"
					+ dateString.substring(6, 8);
			String selectQuery = "SELECT * FROM attendence where date = '"
					+ date + "'";
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				return true;
			}
			cursor.close();
		} catch (Exception e) {
			print_Log_d("Exception :::::::" + e.getMessage());
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return false;
	}

	public ArrayList<AlocatedSauda> GETSaudaAllocation()
	{
		ArrayList<AlocatedSauda> mAlocatedSaudaList=new ArrayList<AlocatedSauda>();
		Cursor cursor =null;
		try {
			cursor= database.rawQuery("SELECT * FROM sauda_allocation", null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					AlocatedSauda detailsObj = new AlocatedSauda();
					detailsObj.setEmployeCode(cursor.getString(0));
					detailsObj.setProductFilterCode(cursor.getString(1));
					detailsObj.setQty(cursor.getString(2));
					detailsObj.setBalance(cursor.getString(4));

					mAlocatedSaudaList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Sauda Allocation","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return mAlocatedSaudaList;
	}

	public ArrayList<SaudaBookingProductDetails> GetSauadaBookingProductDetails(String customercode, String productgroupcode,String condition)
	{
		ArrayList<SaudaBookingProductDetails> SBPList = new ArrayList<SaudaBookingProductDetails>();
		Cursor cursor=null;
		try {
			String query ="SELECT PM.prod_desc,SD.qty,SH.sauda_valid_from, SD.amount  FROM sauda_header SH,sauda_details SD,product_master PM, location LO WHERE SH.sauda_no=SD.sauda_no AND SD.sku_code=PM.prod_code AND SH.customer_code='"+ customercode +"' AND PM.product_group_code='"+productgroupcode+"' AND LO.trans_id=SH.sauda_no AND "+ condition +" ORDER BY PM.prod_desc ASC";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					SaudaBookingProductDetails SBPDetails = new SaudaBookingProductDetails();
					SBPDetails.setProductName(cursor.getString(0));
					SBPDetails.setQuantity(cursor.getString(1));
					SBPDetails.setValidFrom(cursor.getString(2));
					SBPDetails.setValue(cursor.getString(3));
					SBPList.add(SBPDetails);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Sauda Booking Product Details","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return SBPList;
	}

	private String GetRowID(String menuname)
	{
		String rowID="";
		if(Constants.surveyFormDetailsObj.getSurveyReportRowId().trim().length()>0){
			if(Constants.surveyFormDetailsObj.getSurveyReportRowId().trim().contains("#")){
				String[] splitsurveyreportrowid=Constants.surveyFormDetailsObj.getSurveyReportRowId().trim().split("#");
				for(int count=0;count<splitsurveyreportrowid.length;count++){
					String[] menurow=splitsurveyreportrowid[count].trim().split(";");
					if(menurow[0].trim().equalsIgnoreCase(menuname)){
						rowID=menurow[1].trim();
						break;
					}
				}
			}else{
				String[] menurow=Constants.surveyFormDetailsObj.getSurveyReportRowId().trim().split(";");
				if(menurow[0].trim().equalsIgnoreCase(menuname)){
					rowID=menurow[1].trim();
				}
			}
		}
		return rowID;
	}


	public ArrayList<OutletDetails> GetOutletDetails(String menuname,String condition)
	{
		ArrayList<OutletDetails> outletList =new ArrayList<OutletDetails>();
		Cursor cursor=null;
		String query="";
		try {
			if(condition.length()==8){
				if(menuname.equalsIgnoreCase("DCE")){
					query = "SELECT survey_id,flag,value FROM survey_output WHERE  substr(survey_id,-14,8) LIKE '"+ condition + "'  AND (row_id ='RA002' OR row_id='RA136')";
				}
				if(menuname.equalsIgnoreCase("FS")){
					query = "SELECT FS.foot_soldier_id,LO.flag,FS.business_name FROM foot_soldier FS, location LO WHERE FS.foot_soldier_id=LO.trans_id AND SUBSTR(FS.foot_soldier_id,-14,8) LIKE '"+ condition + "'";
				}
				if(menuname.equalsIgnoreCase("DCA")){
					query = "SELECT survey_id,flag,value FROM DCA_transaction WHERE SUBSTR(DCA_trans_id,-14,8) LIKE '"+ condition + "' AND (row_id ='RA002' OR row_id='RA136')";
				}
				if(menuname.equalsIgnoreCase("KYC")){
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) LIKE '"+ condition + "' AND row_id='RA004' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("Site Visit")){
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) LIKE '"+ condition + "' AND row_id='RA021' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("Technical Meets")){
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) LIKE '"+ condition + "' AND row_id='RA054' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("Branding")){
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) LIKE '"+ condition + "' AND row_id='RA045' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("New IHB")){
					String rowid=GetRowID(menuname);
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) LIKE '"+ condition + "' AND row_id='"+rowid+"' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("Existing IHB")){
					String rowid=GetRowID(menuname);
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) LIKE '"+ condition + "' AND row_id='"+rowid+"' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("IHB Site & Complaint Visit")){
					String rowid=GetRowID(menuname);
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) LIKE '"+ condition + "' AND row_id='"+rowid+"' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("New Dealer")){
					String rowid=GetRowID(menuname);
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) LIKE '"+ condition + "' AND row_id='"+rowid+"' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("New Sub Dealer")){
					String rowid=GetRowID(menuname);
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) LIKE '"+ condition + "' AND row_id='"+rowid+"' GROUP BY survey_id";
				}
			}else{
				if(menuname.equalsIgnoreCase("DCE")){
					query = "SELECT survey_id,flag,value FROM survey_output WHERE  substr(survey_id,-14,6) LIKE '"+ condition + "'  AND (row_id ='RA002' OR row_id='RA136')";
				}
				if(menuname.equalsIgnoreCase("FS")){
					query = "SELECT FS.foot_soldier_id,LO.flag,FS.business_name FROM foot_soldier FS, location LO WHERE FS.foot_soldier_id=LO.trans_id AND SUBSTR(FS.foot_soldier_id,-14,6) LIKE '"+ condition + "'";
				}
				if(menuname.equalsIgnoreCase("DCA")){
					query = "SELECT survey_id,flag,value FROM DCA_transaction WHERE SUBSTR(DCA_trans_id,-14,6) LIKE '"+ condition + "' AND (row_id ='RA002' OR row_id='RA136')";
				}
				if(menuname.equalsIgnoreCase("KYC")){
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,6) LIKE '"+ condition + "' AND row_id='RA004' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("Site Visit")){
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,6) LIKE '"+ condition + "' AND row_id='RA021' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("Technical Meets")){
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,6) LIKE '"+ condition + "' AND row_id='RA054' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("Branding")){
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,6) LIKE '"+ condition + "' AND row_id='RA045' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("New IHB")){
					String rowid=GetRowID(menuname);
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,6) LIKE '"+ condition + "' AND row_id='"+rowid+"' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("Existing IHB")){
					String rowid=GetRowID(menuname);
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,6) LIKE '"+ condition + "' AND row_id='"+rowid+"' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("IHB Site & Complaint Visit")){
					String rowid=GetRowID(menuname);
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,6) LIKE '"+ condition + "' AND row_id='"+rowid+"' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("New Dealer")){
					String rowid=GetRowID(menuname);
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,6) LIKE '"+ condition + "' AND row_id='"+rowid+"' GROUP BY survey_id";
				}
				if(menuname.equalsIgnoreCase("New Sub Dealer")){
					String rowid=GetRowID(menuname);
					query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,6) LIKE '"+ condition + "' AND row_id='"+rowid+"' GROUP BY survey_id";
				}
			}
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OutletDetails obj = new OutletDetails();
					obj.setSurveyID(cursor.getString(0));
					obj.setOutletCode(cursor.getString(1));
					obj.setOutletName(cursor.getString(2));
					outletList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Outlet Details","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return outletList;
	}

	public ArrayList<OutletDetails> GetOutletDetails(String menuname,String firstdate,String enddate)
	{
		ArrayList<OutletDetails> outletList =new ArrayList<OutletDetails>();
		Cursor cursor=null;
		String query="";
		try {
			if(menuname.equalsIgnoreCase("DCE")){
				query = "SELECT survey_id,flag,value FROM survey_output WHERE  substr(survey_id,-14,8) BETWEEN'"+ firstdate + "' AND '" + enddate + "'  AND (row_id ='RA002' OR row_id='RA136')";
			}
			if(menuname.equalsIgnoreCase("FS")){
				query = "SELECT FS.foot_soldier_id,LO.flag,FS.business_name FROM foot_soldier FS, location LO WHERE FS.foot_soldier_id=LO.trans_id AND SUBSTR(FS.foot_soldier_id,-14,8) BETWEEN'"+ firstdate + "' AND '" + enddate + "'";
			}
			if(menuname.equalsIgnoreCase("DCA")){
				query = "SELECT survey_id,flag,value FROM DCA_transaction WHERE SUBSTR(DCA_trans_id,-14,8) BETWEEN'"+ firstdate + "' AND '" + enddate + "' AND (row_id ='RA002' OR row_id='RA136')";
			}
			if(menuname.equalsIgnoreCase("KYC")){
				query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) BETWEEN'"+ firstdate + "' AND '" + enddate + "' AND row_id='RA004' GROUP BY survey_id";
			}
			if(menuname.equalsIgnoreCase("Site Visit")){
				query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) BETWEEN'"+ firstdate + "' AND '" + enddate + "' AND row_id='RA021' GROUP BY survey_id";
			}
			if(menuname.equalsIgnoreCase("Technical Meets")){
				query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) BETWEEN'"+ firstdate + "' AND '" + enddate + "' AND row_id='RA054' GROUP BY survey_id";
			}
			if(menuname.equalsIgnoreCase("Branding")){
				query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) BETWEEN'"+ firstdate + "' AND '" + enddate + "' AND row_id='RA045' GROUP BY survey_id";
			}
			if(menuname.equalsIgnoreCase("New IHB")){
				String rowid=GetRowID(menuname);
				query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) BETWEEN'"+ firstdate + "' AND '" + enddate + "' AND row_id='"+rowid+"' GROUP BY survey_id";
			}
			if(menuname.equalsIgnoreCase("Existing IHB")){
				String rowid=GetRowID(menuname);
				query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) BETWEEN'"+ firstdate + "' AND '" + enddate + "' AND row_id='"+rowid+"' GROUP BY survey_id";
			}
			if(menuname.equalsIgnoreCase("IHB Site & Complaint Visit")){
				String rowid=GetRowID(menuname);
				query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) BETWEEN'"+ firstdate + "' AND '" + enddate + "' AND row_id='"+rowid+"' GROUP BY survey_id";
			}
			if(menuname.equalsIgnoreCase("New Dealer")){
				String rowid=GetRowID(menuname);
				query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) BETWEEN'"+ firstdate + "' AND '" + enddate + "' AND row_id='"+rowid+"' GROUP BY survey_id";
			}
			if(menuname.equalsIgnoreCase("New Sub Dealer")){
				String rowid=GetRowID(menuname);
				query = "SELECT survey_id,flag,value FROM survey_output WHERE SUBSTR(survey_id,-14,8) BETWEEN'"+ firstdate + "' AND '" + enddate + "' AND row_id='"+rowid+"' GROUP BY survey_id";
			}

			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OutletDetails obj = new OutletDetails();
					obj.setSurveyID(cursor.getString(0));
					obj.setOutletCode(cursor.getString(1));
					obj.setOutletName(cursor.getString(2));
					outletList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Outlet Details","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return outletList;
	}

	public ArrayList<KeyValue> GetSurveyDetails(String surveyid)
	{
		ArrayList<KeyValue> KeyValueList = new ArrayList<KeyValue>();
		String query ="";
		Cursor cursor = null;
		try {
			query = "SELECT SI.display_name,SO.value FROM survey_input SI, survey_output SO WHERE SI.row_id=SO.row_id AND SO.survey_id='"+surveyid+"'";
			print_log_d("Survey Details", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					KeyValue obj = new KeyValue();
					obj.setKey(cursor.getString(0));
					obj.setValue(cursor.getString(1));
					KeyValueList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Survey Details","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return KeyValueList;
	}


	public ArrayList<SaudaBookingProductGroupDetails> GetSauadaBookingProductGroupDetails(String customercode,String condition)
	{
		ArrayList<SaudaBookingProductGroupDetails> SBPGList = new ArrayList<SaudaBookingProductGroupDetails>();

		Cursor cursor=null;
		try {
			String query = "SELECT PGM.product_group_name,PGM.product_group_code,SUM(SD.qty), GROUP_CONCAT(SD.amount) FROM sauda_header SH,sauda_details SD,product_group_master PGM,product_master PM ,location LO WHERE SH.sauda_no=SD.sauda_no AND SD.sku_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND SH.customer_code='"+customercode+"' AND LO.trans_id=SH.sauda_no AND "+ condition +" GROUP BY PGM.product_group_code ORDER BY PGM.product_group_name ASC";
			print_log_d("Product group", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SaudaBookingProductGroupDetails SBPGDetails = new SaudaBookingProductGroupDetails();
					SBPGDetails.setProductGroupName(cursor.getString(0));
					SBPGDetails.setProductGroupCode(cursor.getString(1));
					SBPGDetails.setQuantity(cursor.getString(2));
					String concatenatedAmount = cursor.getString(3);
					concatenatedAmount=Utils.addAllItemsOfAnArray(concatenatedAmount);
					SBPGDetails.setValue(concatenatedAmount);
					SBPGList.add(SBPGDetails);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return SBPGList;
	}

	public String GetMenuWiseTotalSurvey(String condition,String menuid)
	{
		Cursor cursor = null;
		String total="0";
		try {
			String query = "SELECT COUNT(DISTINCT survey_id) FROM survey_output WHERE "+condition+ " AND row_id IN( SELECT DISTINCT row_id FROM survey_input WHERE menu_id='"+ menuid +"')";
			print_log_d("Survey", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				total=cursor.getString(0);
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Menu Wise Total Survey","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return total;
	}

	public ArrayList<SurveyReport> GetDCAReportMenuDetails(String condition)
	{
		ArrayList<SurveyReport> SBCList = new ArrayList<SurveyReport>();
		Cursor cursor = null;
		String query ="";
		try {
			query= "SELECT  type,COUNT(DISTINCT DCA_trans_id) FROM DCA_transaction WHERE "+condition +"  GROUP BY type ORDER BY type";
			print_log_d("DCA Report", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyReport SRDetails = new SurveyReport();
					SRDetails.setMenuName(cursor.getString(0));
					SRDetails.setTotal(cursor.getString(1));
					SBCList.add(SRDetails);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("DCA Report Menu Details","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return SBCList;
	}


	public ArrayList<SurveyReport> GetSurveyReportMenuDetails(String condition,String type)
	{
		ArrayList<SurveyReport> SBCList = new ArrayList<SurveyReport>();
		Cursor cursor = null;
		String query ="";
		try {
			if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
				query= "SELECT menu_id,layout_name FROM survey_input WHERE type='menu' AND menu_id  IN(SELECT distinct  SI.menu_id FROM survey_input SI,survey_output SO,location LO where  SI.row_id=SO.row_id AND LO.trans_id=SO.survey_id AND SI.survey_type='"+ type +"' AND " +condition+" ) ORDER BY display_order ASC";
			}else{
				query= "SELECT menu_id,layout_name FROM survey_input WHERE type='menu' AND menu_id  IN(SELECT distinct  SI.menu_id FROM survey_input SI,survey_output SO,location LO where  SI.row_id=SO.row_id AND LO.trans_id=SO.survey_id AND " +condition+" ) ORDER BY display_order ASC";
			}
			print_log_d("Survey Report", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyReport SRDetails = new SurveyReport();
					SRDetails.setMenuId(cursor.getString(0));
					SRDetails.setMenuName(cursor.getString(1));
					SBCList.add(SRDetails);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Survey Report Menu Details","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return SBCList;
	}

	public ArrayList<OrderReportDetails> GetOrderSKUData(String customercode, String daterange)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT DISTINCT PM.prod_desc,PM.prod_code,SUM(OD.qty),SUM(OD.amount), GROUP_CONCAT(OD.order_no) FROM order_header OH,product_master PM,order_details OD WHERE OH.customer_code='"+customercode+"' AND OH.order_no=OD.order_no AND OD.sku_code=PM.prod_code AND "+daterange+" GROUP BY PM.prod_code";
			print_log_d("Order SKU", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					obj.setQuantity(cursor.getString(2));
					String concatenatedAmount = cursor.getString(3);
					Constants.mCurrentOrderNoList=cursor.getString(4);
					concatenatedAmount=Utils.addAllItemsOfAnArrayForOrder(concatenatedAmount,database);
					obj.setAmount(concatenatedAmount);
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order SKU","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}


	public String GetFreightRateByBranchCodeFromBranchRouteFreightMaster(String branchCode, String selectedRouteCode)
	{
		String query="",freightRate="";
		Cursor cursor = null;
		try
		{
			query="select freight FROM branch_route_freight WHERE route_code='" +selectedRouteCode+"' AND branch_code='"+branchCode+"'AND acedns='Y'";

			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				freightRate=cursor.getString(0);
				cursor.close();
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
			freightRate="";
		}
		finally
		{
			if(cursor !=null){
				cursor.close();
			}
		}
		return freightRate;
	}

	public String GetTruckLoadQuantityByDnsProductCode(String dnsProductCode)
	{
		String query="",TruckLoadQuantity="";
		Cursor cursor = null;
		try
		{
			query="select qty_truck_load FROM load_distribution WHERE prod_code='" +dnsProductCode+"' ORDER by download_time desc LIMIT 1";

			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				TruckLoadQuantity=cursor.getString(0);
				cursor.close();
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
			TruckLoadQuantity="";
		}
		finally
		{
			if(cursor !=null){
				cursor.close();
			}
		}
		return TruckLoadQuantity;
	}

	public ArrayList<OrderReportDetails> GetOrderRouteData(String daterange)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT RM.route_name, RM.route_code,GROUP_CONCAT(OD.amount),SUM(OD.qty), GROUP_CONCAT(OD.order_no) FROM order_header OH,customer_master CM,order_details OD,route_master RM WHERE OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no AND CM.route_code=RM.route_code  AND "+daterange+" GROUP BY RM.route_code";
			print_log_d("Order Route", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					String concatenatedAmount = cursor.getString(2);
					Constants.mCurrentOrderNoList=cursor.getString(4);
					concatenatedAmount=Utils.addAllItemsOfAnArrayForOrder(concatenatedAmount, database);
					obj.setAmount(concatenatedAmount);
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Route","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}

	public ArrayList<OrderReportDetails> GetOrderProductGroupData(String condition)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT PGM.product_group_name,PGM.product_group_code,GROUP_CONCAT(OD.amount),SUM(OD.qty), GROUP_CONCAT(OD.order_no) FROM order_details OD,order_header OH,product_master PM,product_group_master PGM  WHERE OD.sku_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND OD.order_no=OH.order_no AND "+condition+" GROUP BY PGM.product_group_code";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					String concatenatedAmount = cursor.getString(2);
					Constants.mCurrentOrderNoList=cursor.getString(4);
					concatenatedAmount=Utils.addAllItemsOfAnArrayForOrder(concatenatedAmount, database);
					obj.setAmount(concatenatedAmount);
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Product Group","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}


	public ArrayList<OrderReportDetails> GetStockProductGroupData(String condition)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT PGM.product_group_name,PGM.product_group_code,SUM(SA.quantity),SUM(SA.quantity)  FROM mf_stock_audit_header OH, mf_stk_audit_details SA,product_master PM,product_group_master PGM  WHERE OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no SA.product_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND  "+condition+" GROUP BY PGM.product_group_code";
			query="SELECT PGM.product_group_name,PGM.product_group_code,SUM(SA.quantity),SUM(SA.quantity)  FROM stock_audit SA,product_master PM,product_group_master PGM  WHERE SA.product_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND  "+condition+" GROUP BY PGM.product_group_code";

			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					obj.setAmount(cursor.getString(2));
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Product Group","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}


	public ArrayList<OrderReportDetails> GetOrderProductSubGroupData(String condition,String productgrpcode)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT PGM.product_sub_group_name,PGM.product_sub_group_code,GROUP_CONCAT(OD.amount),SUM(OD.qty), GROUP_CONCAT(OD.order_no) FROM order_details OD,product_master PM,product_sub_group_master PGM  WHERE OD.sku_code=PM.prod_code AND PM.product_sub_group_code=PGM.product_sub_group_code AND PM.product_group_code='"+productgrpcode+"' AND "+condition+" GROUP BY PGM.product_sub_group_code";
			print_log_d("Product Sub Group", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					String concatenatedAmount = cursor.getString(2);
					Constants.mCurrentOrderNoList=cursor.getString(4);
					concatenatedAmount=Utils.addAllItemsOfAnArrayForOrder(concatenatedAmount, database);
					obj.setAmount(concatenatedAmount);
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Product Sub Group","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}

	public ArrayList<OrderReportDetails> GetStockProductSubGroupData(String condition,String productgrpcode)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT PGM.product_sub_group_name,PGM.product_sub_group_code,SUM(SA.quantity),SUM(SA.quantity)  FROM stock_audit SA,product_master PM,product_sub_group_master PGM  WHERE SA.product_code=PM.prod_code AND PM.product_sub_group_code=PGM.product_sub_group_code AND PM.product_group_code='"+productgrpcode+"' AND "+condition+" GROUP BY PGM.product_sub_group_code";
			print_log_d("Product Sub Group", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					obj.setAmount(cursor.getString(2));
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Product Sub Group","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}

	public ArrayList<OrderReportDetails> GetYellowCardProductSubGroupData(String condition,String productgrpcode)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT PGM.product_sub_group_name,PGM.product_sub_group_code,SUM(YCD.qty),SUM(YCD.qty) FROM yellow_card_details YCD,product_master PM,product_sub_group_master PGM  WHERE YCD.product_code=PM.prod_code AND PM.product_sub_group_code=PGM.product_sub_group_code AND PM.product_group_code='"+productgrpcode+"' AND "+condition+" GROUP BY PGM.product_sub_group_code";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					obj.setAmount(cursor.getString(2));
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Product Sub Group","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}

	public ArrayList<OrderReportDetails> GetOrderProductBrandData(String condition,String productgrpcode,String productsubgroupcode)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT PGM.product_brand_name,PGM.product_brand_code,GROUP_CONCAT(OD.amount),SUM(OD.qty), GROUP_CONCAT(OD.order_no) FROM order_details OD,product_master PM,product_brand_master PGM  WHERE OD.sku_code=PM.prod_code AND PM.product_brand_code=PGM.product_brand_code AND PM.product_sub_group_code='"+productsubgroupcode+"' AND PM.product_group_code='"+productgrpcode+"' AND "+condition+" GROUP BY PGM.product_brand_code";
			print_log_d("Product Sub Group", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					String concatenatedAmount = cursor.getString(2);
					Constants.mCurrentOrderNoList=cursor.getString(4);
					concatenatedAmount=Utils.addAllItemsOfAnArrayForOrder(concatenatedAmount, database);
					obj.setAmount(concatenatedAmount);
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		}
		catch (Exception e)
		{

		}
		finally
		{
			if(cursor !=null)
			{
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}

	public ArrayList<OrderReportDetails> GetStockProductBrandData(String condition,String productgrpcode,String productsubgroupcode)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT PGM.product_brand_name,PGM.product_brand_code,SUM(SA.quantity),SUM(SA.quantity) FROM stock_audit SA,product_master PM,product_brand_master PGM  WHERE SA.product_code=PM.prod_code AND PM.product_sub_group_code=PGM.product_sub_group_code AND PM.product_sub_group_code='"+productsubgroupcode+"' AND PM.product_group_code='"+productgrpcode+"' AND "+condition+" GROUP BY PGM.product_sub_group_code";
			print_log_d("Product Brand ", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					obj.setAmount(cursor.getString(2));
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Product Sub Group","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}

	public ArrayList<OrderReportDetails> GetYellowCardProductBrandData(String condition,String productgrpcode,String productsubgroupcode)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT PGM.product_brand_name,PGM.product_brand_code,SUM(YCD.qty),SUM(YCD.qty) FROM yellow_card_details YCD,product_master PM,product_brand_master PGM  WHERE YCD.product_code=PM.prod_code AND PM.product_sub_group_code=PGM.product_sub_group_code AND PM.product_sub_group_code='"+productsubgroupcode+"' AND PM.product_group_code='"+productgrpcode+"' AND "+condition+" GROUP BY PGM.product_sub_group_code";
			print_log_d("Product Brand ", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					obj.setAmount(cursor.getString(2));
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Product Sub Group","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}

	public ArrayList<OrderReportDetails> GetStockProductData(String condition,String productgrpcode,String productsubgroupcode,String productbrandcode,int filterno, String customerCode)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			if(filterno==4){
				query="SELECT PM.prod_desc,PM.prod_code,SUM(SA.quantity),SUM(SA.quantity) FROM stock_audit SA,product_master PM  WHERE SA.product_code=PM.prod_code AND customer_code='"+customerCode+"' AND PM.product_sub_group_code='"+productsubgroupcode+"' AND PM.product_group_code='"+productgrpcode+"' AND PM.product_brand_code='"+productbrandcode+"' AND "+condition+" GROUP BY PM.prod_code";
			}else if(filterno==3){
				query="SELECT PM.prod_desc,PM.prod_code,SUM(SA.quantity),SUM(SA.quantity) FROM stock_audit SA,product_master PM  WHERE SA.product_code=PM.prod_code AND AND customer_code='"+customerCode+"' PM.product_sub_group_code='"+productsubgroupcode+"' AND PM.product_group_code='"+productgrpcode+"' AND "+condition+" GROUP BY PM.prod_code";
			}else if(filterno==2){
				query="SELECT PM.prod_desc,PM.prod_code,SUM(SA.quantity),SUM(SA.quantity) FROM stock_audit SA,product_master PM  WHERE SA.product_code=PM.prod_code AND customer_code='"+customerCode+"' AND PM.product_group_code='"+productgrpcode+"' AND "+condition+" GROUP BY PM.prod_code";
			}else if(filterno==1){
				query="SELECT PM.prod_desc,PM.prod_code,SUM(SA.quantity),SUM(SA.quantity) FROM stock_audit SA,product_master PM  WHERE SA.product_code=PM.prod_code AND customer_code='"+customerCode+"' AND "+condition+" GROUP BY PM.prod_code";
			}
			print_log_d("Order Product", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					obj.setAmount(cursor.getString(2));
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Product ","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}
	public ArrayList<YellowCard> GetYellowCardProductData(String condition, String customerCode)
	{
		ArrayList<YellowCard> YellowCardReportDetailsList = new ArrayList<>();
		String query="";
		Cursor cursor = null;
		try {

			query="SELECT YCD.challan_no,YCD.challan_date,YCD.qty,YCD.qty_UOM FROM yellow_card_details YCD where customer_code='"+customerCode+"' AND "+condition;

			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					YellowCard obj = new YellowCard();
					obj.setchallan_no(cursor.getString(0));
					obj.setchallan_date(cursor.getString(1));
					obj.setqty(cursor.getString(2));
					obj.setqty_UOM(cursor.getString(3));
					YellowCardReportDetailsList.add(obj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return YellowCardReportDetailsList;
	}
	public ArrayList<OrderReportDetails> GetMFSSKUData(String customercode, String daterange)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT DISTINCT OD.competitor_name,SUM(OD.qty_mt),SUM(OD.qty_mt) FROM mf_stk_audit_header OH,mf_stk_audit_details OD WHERE OH.customer_code='"+customercode+"' AND OH.mf_stk_audit_id=OD.mf_stk_audit_id AND "+daterange+" GROUP BY OD.competitor_name";
			print_log_d("Order SKU", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(0));
					obj.setQuantity(cursor.getString(1));
					obj.setAmount(cursor.getString(2));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order SKU","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}


	public ArrayList<OrderReportDetails> GetOrderProductData(String condition,String productgrpcode,String productsubgroupcode,String productbrandcode,int filterno)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			if(filterno==4){
				query="SELECT PM.prod_desc,PM.prod_code,GROUP_CONCAT(OD.amount),SUM(OD.qty),GROUP_CONCAT(OD.order_no) FROM order_details OD,product_master PM  WHERE OD.sku_code=PM.prod_code AND PM.product_sub_group_code='"+productsubgroupcode+"' AND PM.product_group_code='"+productgrpcode+"' AND PM.product_brand_code='"+productbrandcode+"' AND "+condition+" GROUP BY PM.prod_code";
			}else if(filterno==3){
				query="SELECT PM.prod_desc,PM.prod_code,GROUP_CONCAT(OD.amount),SUM(OD.qty),GROUP_CONCAT(OD.order_no) FROM order_details OD,product_master PM  WHERE OD.sku_code=PM.prod_code AND PM.product_sub_group_code='"+productsubgroupcode+"' AND PM.product_group_code='"+productgrpcode+"' AND "+condition+" GROUP BY PM.prod_code";
			}else if(filterno==2){
				query="SELECT PM.prod_desc,PM.prod_code,GROUP_CONCAT(OD.amount),SUM(OD.qty),GROUP_CONCAT(OD.order_no) FROM order_details OD,product_master PM  WHERE OD.sku_code=PM.prod_code AND PM.product_group_code='"+productgrpcode+"' AND "+condition+" GROUP BY PM.prod_code";
			}else if(filterno==1){
				query="SELECT PM.prod_desc,PM.prod_code,GROUP_CONCAT(OD.amount),SUM(OD.qty), GROUP_CONCAT(OD.order_no) FROM order_details OD,product_master PM  WHERE OD.sku_code=PM.prod_code AND "+condition+" GROUP BY PM.prod_code";
			}
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					String concatenatedAmount = cursor.getString(2);
					Constants.mCurrentOrderNoList=cursor.getString(4);
					concatenatedAmount=Utils.addAllItemsOfAnArrayForOrder(concatenatedAmount,database);
					obj.setAmount(concatenatedAmount);
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Product ","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}

	public ArrayList<OrderReportDetails> GetStockCustomerData(String daterange)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT DISTINCT CM.customer_name,CM.customer_code,SUM(SA.quantity),SUM(SA.quantity) FROM stock_audit SA,customer_master CM WHERE SA.customer_code=CM.customer_code AND "+daterange+" GROUP BY CM.customer_code";
			print_log_d("Query", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					obj.setAmount(cursor.getString(2));
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Customer","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}

	public ArrayList<OrderReportDetails> GetYellowCardCustomerData(String daterange)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT DISTINCT CM.customer_name,CM.customer_code,SUM(YCD.qty),SUM(YCD.qty) FROM yellow_card_details YCD,customer_master CM WHERE YCD.customer_code=CM.customer_code AND "+daterange+" GROUP BY CM.customer_code";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					obj.setAmount(cursor.getString(2));
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Customer","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}
	public ArrayList<OrderReportDetails> GetCheckInOutCustomerData(String daterange)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT CM.customer_name,CM.customer_code,CIID.check_in_time, CIID.check_out_time,CIID.remarks FROM check_in_out_details CIID,customer_master CM WHERE CIID.customer_code=CM.customer_code AND "+daterange+" ORDER BY CIID.check_in_time DESC ";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					obj.setAmount(cursor.getString(2));
					obj.setQuantity(cursor.getString(3));
					obj.setremarks(cursor.getString(4));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Customer","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}
	public ArrayList<OrderReportDetails> GetSalesReportData(String daterange)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT  OH.order_no,SUM(OD.qty), OH.transaction_type FROM order_header OH, order_details OD WHERE OH.order_no=OD.order_no AND "+daterange+" GROUP BY OH.order_no order by OH.order_no";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					OrderReportDetails obj = new OrderReportDetails();
					obj.setTransactionId(cursor.getString(0));
					obj.setAmount(cursor.getString(1));
					obj.setType(cursor.getString(2));
					OrderReportDetailsList.add(obj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		finally
		{
			if(cursor !=null)
			{
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}

	public ArrayList<OrderReportDetails> GetMFSCustomerData(String daterange)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			query="SELECT DISTINCT CM.customer_name,CM.customer_code,SUM(OD.qty_mt),SUM(OD.qty_mt) FROM mf_stk_audit_header OH,customer_master CM,mf_stk_audit_details OD WHERE OH.customer_code=CM.customer_code AND OH.mf_stk_audit_id=OD.mf_stk_audit_id  AND "+daterange+" GROUP BY CM.customer_code";
//			query="SELECT DISTINCT CM.customer_name,CM.customer_code,SUM(SA.quantity),SUM(SA.quantity) FROM stock_audit SA,customer_master CM WHERE SA.customer_code=CM.customer_code AND "+daterange+" GROUP BY CM.customer_code";
			print_log_d("Query", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					obj.setAmount(cursor.getString(2));
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Customer","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}


	public ArrayList<OrderReportDetails> GetOrderCustomerData(String routecode,String daterange)
	{
		ArrayList<OrderReportDetails> OrderReportDetailsList = new ArrayList<OrderReportDetails>();
		String query="";
		Cursor cursor = null;
		try {
			if(routecode.length()>0){
				query="SELECT DISTINCT CM.customer_name,CM.customer_code,GROUP_CONCAT(OD.amount),SUM(OD.qty),GROUP_CONCAT(OD.order_no) FROM order_header OH,customer_master CM,order_details OD WHERE OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no  AND "+daterange+"  AND CM.route_code='"+routecode+"' GROUP BY CM.customer_code";
			}else{
				query="SELECT DISTINCT CM.customer_name,CM.customer_code,GROUP_CONCAT(OD.amount),SUM(OD.qty),GROUP_CONCAT(OD.order_no) FROM order_header OH,customer_master CM,order_details OD WHERE OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no  AND "+daterange+" GROUP BY CM.customer_code";
			}
			print_log_d("Query", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderReportDetails obj = new OrderReportDetails();
					obj.setName(cursor.getString(0));
					obj.setCode(cursor.getString(1));
					String concatenatedAmount = cursor.getString(2);
					Constants.mCurrentOrderNoList=cursor.getString(4);
					concatenatedAmount=Utils.addAllItemsOfAnArrayForOrder(concatenatedAmount, database);
					obj.setAmount(concatenatedAmount);
					obj.setQuantity(cursor.getString(3));
					OrderReportDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Order Customer","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return OrderReportDetailsList;
	}

	public ArrayList<MisDetails> GetMisDetailsData(String type,String condition,String productgroupcode,String empcode,String state,String branchcode,String zone,String plant)
	{
		ArrayList<MisDetails> MisDetailsList = new ArrayList<MisDetails>();
		String query="";
		DecimalFormat defaultFormat = new DecimalFormat("0");
		Cursor cursor = null;
		try {
			if(type.equalsIgnoreCase("sku")){
				query="SELECT PM.prod_code,PM.prod_desc,SUM(STL.qty) AS total_qty,SUM(STL.convert_qty_two) AS ton FROM product_group_master PGM,sauda_transaction_log STL,product_master PM WHERE  STL.prod_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND PM.vertical_value='"+Constants.mVerticalValue+"' AND "+condition+" AND PGM.product_group_code='"+productgroupcode+"'  GROUP BY PM.prod_desc";
			}
			if(type.equalsIgnoreCase("plant")){
				query="SELECT PM.prod_code,PM.prod_desc,SUM(STL.qty) AS total_qty,SUM(STL.convert_qty_two) AS ton FROM product_group_master PGM,sauda_transaction_log STL,product_master PM WHERE  STL.prod_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND PM.vertical_value='"+Constants.mVerticalValue+"' AND STL.plant='"+plant+"' AND STL.branch_code='"+branchcode+"' AND "+condition+" AND PGM.product_group_code='"+productgroupcode+"'  GROUP BY PM.prod_desc";
			}
			if(type.equalsIgnoreCase("state")){
				query="SELECT PM.prod_code,PM.prod_desc,SUM(STL.qty) AS total_qty,SUM(STL.convert_qty_two) AS ton FROM product_group_master PGM,sauda_transaction_log STL,product_master PM WHERE  STL.prod_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND PM.vertical_value='"+Constants.mVerticalValue+"' AND STL.state='"+state+"' AND STL.branch_code='"+branchcode+"' AND "+condition+" AND PGM.product_group_code='"+productgroupcode+"'  GROUP BY PM.prod_desc";
			}
			if(type.equalsIgnoreCase("zone")){
				query="SELECT PM.prod_code,PM.prod_desc,SUM(STL.qty) AS total_qty,SUM(STL.convert_qty_two) AS ton FROM product_group_master PGM,sauda_transaction_log STL,product_master PM WHERE  STL.prod_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND PM.vertical_value='"+Constants.mVerticalValue+"' AND STL.state='"+state+"' AND STL.zone='"+zone+"' AND STL.branch_code='"+branchcode+"' AND "+condition+"  AND PGM.product_group_code='"+productgroupcode+"'  GROUP BY PM.prod_desc";
			}
			if(type.equalsIgnoreCase("employee")){
				query="SELECT EM.emp_name,PM.prod_desc,SUM(STL.qty) AS total_qty,SUM(STL.convert_qty_two) AS ton FROM product_group_master PGM,sauda_transaction_log STL,product_master PM,emp_master EM WHERE  EM.emp_code=STL.emp_code AND  STL.prod_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND PM.vertical_value='"+Constants.mVerticalValue+"' AND STL.emp_code='"+empcode+"' AND "+condition+" AND PGM.product_group_code='"+productgroupcode+"'  GROUP BY EM.emp_name ,PM.prod_desc ORDER BY EM.emp_name";
			}
			print_log_d("Mis Query", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					MisDetails obj = new MisDetails();
					obj.setType(cursor.getString(0));
					obj.setSkuName(cursor.getString(1));
					obj.setBookedQtyCase(cursor.getString(2));
					if(cursor.getString(3)!=null && cursor.getString(3).length()>0){
						double value=Double.parseDouble(cursor.getString(3));
						obj.setBookedQtyTon(defaultFormat.format(value));
					}else{
						obj.setBookedQtyTon("0");
					}
					MisDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Mis Details","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return MisDetailsList;
	}

	public ArrayList<BranchMasterDetails> GetSTLBranchDetails(String condition,String state, String plant)
	{
		ArrayList<BranchMasterDetails> BranchMasterDetailsList = new ArrayList<BranchMasterDetails>();
		String query ="";
		Cursor cursor = null;
		try {
			if(state.length()>0){
				query = "SELECT BM.branch_code,BM.branch_name,SUM(STL.convert_qty_two) AS total_qty FROM branch_master BM, sauda_transaction_log STL,product_master PM,product_group_master PGM  WHERE STL.prod_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND PM.vertical_value='"+Constants.mVerticalValue+"' AND BM.branch_code=STL.branch_code AND "+condition+" AND STL.state!=' ' AND STL.state='"+state+"' GROUP BY BM.branch_code";
			}else{
				query = "SELECT BM.branch_code,BM.branch_name,SUM(STL.convert_qty_two) AS total_qty FROM branch_master BM, sauda_transaction_log STL,product_master PM,product_group_master PGM  WHERE STL.prod_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND PM.vertical_value='"+Constants.mVerticalValue+"' AND BM.branch_code=STL.branch_code AND "+condition+" AND STL.plant!=' ' AND STL.plant='"+plant+"' GROUP BY BM.branch_code";
			}
			print_log_d("State or Plant", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					BranchMasterDetails obj = new BranchMasterDetails();
					obj.setBranchCode(cursor.getString(0));
					obj.setBranchName(cursor.getString(1));
					obj.setValue(cursor.getString(2));
					BranchMasterDetailsList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("State or Plant","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return BranchMasterDetailsList;
	}

	public ArrayList<KeyValue> GetSTLPlantDetails(String condition)
	{
		ArrayList<KeyValue> KeyValueList = new ArrayList<KeyValue>();
		String query ="";
		Cursor cursor = null;
		try {
			query = "SELECT STL.plant,SUM(STL.convert_qty_two) AS total_qty FROM sauda_transaction_log STL,product_master PM,product_group_master PGM  WHERE STL.prod_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND PM.vertical_value='"+Constants.mVerticalValue+"' AND "+condition+" AND STL.plant!=' ' GROUP BY STL.plant";
			print_log_d("Plant", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					KeyValue obj = new KeyValue();
					obj.setKey(cursor.getString(0));
					obj.setValue(cursor.getString(1));
					KeyValueList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Plant","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return KeyValueList;
	}

	public ArrayList<KeyValue> GetSTLStateDetails(String condition,String zonename)
	{
		ArrayList<KeyValue> KeyValueList = new ArrayList<KeyValue>();
		String query ="";
		Cursor cursor = null;
		try {
			if(zonename.length()>0){
				query = "SELECT STL.state,SUM(STL.convert_qty_two) AS total_qty FROM sauda_transaction_log STL, product_master PM,product_group_master PGM  WHERE STL.prod_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND PM.vertical_value='"+Constants.mVerticalValue+"' AND "+condition+" AND STL.state!=' ' AND STL.zone='"+zonename+"' GROUP BY STL.state";
			}else{
				query = "SELECT STL.state,SUM(STL.convert_qty_two) AS total_qty FROM sauda_transaction_log STL, product_master PM,product_group_master PGM  WHERE STL.prod_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND PM.vertical_value='"+Constants.mVerticalValue+"' AND  "+condition+" AND STL.state!=' ' GROUP BY STL.state";
			}
			print_log_d("State", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					KeyValue obj = new KeyValue();
					obj.setKey(cursor.getString(0));
					obj.setValue(cursor.getString(1));
					KeyValueList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("State Details","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return KeyValueList;
	}

	public ArrayList<KeyValue> GetSTLZoneDetails(String condition)
	{
		ArrayList<KeyValue> KeyValueList = new ArrayList<KeyValue>();
		String query ="";
		Cursor cursor = null;
		try {
			query = "SELECT STL.zone,SUM(STL.convert_qty_two) AS total_qty FROM sauda_transaction_log STL,product_master PM,product_group_master PGM  WHERE STL.prod_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND PM.vertical_value='"+Constants.mVerticalValue+"' AND "+condition+" AND STL.zone!=' ' GROUP BY STL.zone";

			print_log_d("Zone", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					KeyValue obj = new KeyValue();
					obj.setKey(cursor.getString(0));
					obj.setValue(cursor.getString(1));
					KeyValueList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Zone Details","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return KeyValueList;
	}

	public ArrayList<KeyValue> GetEmployeeTargetCollection(String empcode,String type)
	{
		ArrayList<KeyValue> KeyValueList = new ArrayList<KeyValue>();
		String query ="";
		Cursor cursor = null;
		try {
			if(type.equalsIgnoreCase("SINGLE")){
				query="SELECT SUM(collection_target),SUM(collection_achievement) FROM emp_target_achievement WHERE emp_code='"+empcode+"'";
			}else{
				query="SELECT SUM(collection_target),SUM(collection_achievement) FROM emp_target_achievement WHERE emp_code IN("+empcode+")";
			}

			print_log_d("Emp Target Achievment", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getColumnCount(); ii++) {
					KeyValue obj1 = new KeyValue();
					obj1.setKey("Collection Target");
					obj1.setValue(cursor.getString(0));
					KeyValueList.add(obj1);
					KeyValue obj2 = new KeyValue();
					obj2.setKey("Collection Achieved");
					obj2.setValue(cursor.getString(1));
					KeyValueList.add(obj2);
					obj1=null;
					obj2=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Employee Target Achievment","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return KeyValueList;
	}

	public EmployeeTargetJCP GetEmployeeTargetJCP(String condition,String type)
	{
		EmployeeTargetJCP obj=new EmployeeTargetJCP();
		String query ="";
		int total=0;
		int other=0;
		int ihb=0;
		Cursor cursor = null;
		try {
			if(type.equalsIgnoreCase("TARGET")){
				query="SELECT CM.cust_type,COUNT(RCPT.customer_code)  FROM customer_master CM,route_customer_plan_transaction RCPT WHERE CM.customer_code=RCPT.customer_code AND RCPT.visit_date='"+condition+"'  GROUP BY CM.cust_type";
			}else{
				query="SELECT CM.cust_type,COUNT(OH.customer_code)  FROM customer_master CM,order_header OH WHERE CM.customer_code=OH.customer_code AND SUBSTR(OH.order_no,-14,8)='"+condition+"'  GROUP BY CM.cust_type";
			}

			if(type.equalsIgnoreCase("ACHIEVED")){
				Cursor cursor1 = null;
				String query1="SELECT type,COUNT(DISTINCT survey_id)  FROM survey_output WHERE  SUBSTR(survey_id,-14,8)='"+condition+"' GROUP BY survey_id";
				cursor1 = database.rawQuery(query1, null);
				if (cursor1.getCount() > 0) {
					cursor1.moveToFirst();
					for (int ii = 0; ii < cursor1.getCount(); ii++) {
						if(cursor1.getString(0).trim().equalsIgnoreCase("New IHB")){
							if(cursor1.getString(1)!=null && cursor1.getString(1).trim().length()>0){
								ihb+=Integer.parseInt(cursor1.getString(1));
								total+=Integer.parseInt(cursor1.getString(1));
							}
						}else if(cursor1.getString(0).trim().equalsIgnoreCase("Existing IHB")){
							if(cursor1.getString(1)!=null && cursor1.getString(1).trim().length()>0){
								ihb+=Integer.parseInt(cursor1.getString(1));
								total+=Integer.parseInt(cursor1.getString(1));
							}
						}else{
							if(cursor1.getString(1)!=null && cursor1.getString(1).trim().length()>0){
								other+=Integer.parseInt(cursor1.getString(1));
								total+=Integer.parseInt(cursor1.getString(1));
							}
						}
						cursor1.moveToNext();
					}
					obj.setIHB(String.valueOf(ihb));
				}
			}


			print_log_d("Emp Target Achievment JCP", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					if(cursor.getString(0).trim().equalsIgnoreCase("Dealer")){
						if(cursor.getString(1)!=null && cursor.getString(1).trim().length()>0){
							obj.setDealer(cursor.getString(1));
							total+=Integer.parseInt(cursor.getString(1));
						}
					}else if(cursor.getString(0).trim().equalsIgnoreCase("Sub-Dealer")){
						if(cursor.getString(1)!=null && cursor.getString(1).trim().length()>0){
							obj.setSubDealer(cursor.getString(1));
							total+=Integer.parseInt(cursor.getString(1));
						}
					}/*else if(cursor.getString(0).trim().equalsIgnoreCase("IHB")){
						if(cursor.getString(1)!=null && cursor.getString(1).trim().length()>0){
							obj.setIHB(cursor.getString(1));
							total+=Integer.parseInt(cursor.getString(1));
						}
					}*/
					else{
						if(cursor.getString(1)!=null && cursor.getString(1).trim().length()>0){
							other+=Integer.parseInt(cursor.getString(1));
							total+=Integer.parseInt(cursor.getString(1));
						}
					}
					cursor.moveToNext();
				}
				cursor.close();
				obj.setOthers(String.valueOf(other));
				obj.setAchievement(String.valueOf(total));
			}
		} catch (Exception e) {
			print_log_d("Emp Target Achievment JCP","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return obj;
	}

	public double GetEmployeeTargetVolume(String condition,String empcode,String type)
	{
		double value=0;
		String query ="";
		Cursor cursor = null;
		try {
			if(type.equalsIgnoreCase("SINGLE")){
				query="SELECT SUM(volume_target),SUM(volume_achievement) FROM emp_target_achievement WHERE "+condition+" AND emp_code='"+empcode+"'";
			}else{
				query="SELECT SUM(volume_target),SUM(volume_achievement) FROM emp_target_achievement WHERE "+condition+" AND emp_code IN("+empcode+")";
			}

			print_log_d("Emp Target Achievment", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				double volumeacheived=0;
				double volumetarget=0;
				if(cursor.getString(0).trim().length()>0){
					volumetarget=Double.parseDouble(cursor.getString(0).trim());
				}
				if(cursor.getString(1).trim().length()>0){
					volumeacheived=Double.parseDouble(cursor.getString(1).trim());
				}
				value=(volumeacheived/volumetarget)*100;
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Emp Target Achievment","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return value;
	}



	public ArrayList<KeyValue> GetEmployeeTargetVolume(String empcode,String type)
	{
		ArrayList<KeyValue> KeyValueList = new ArrayList<KeyValue>();
		String query ="";
		Cursor cursor = null;
		try {
			if(type.equalsIgnoreCase("SINGLE")){
				query="SELECT SUM(volume_target),SUM(volume_achievement) FROM emp_target_achievement WHERE emp_code='"+empcode+"'";
			}else{
				query="SELECT SUM(volume_target),SUM(volume_achievement) FROM emp_target_achievement WHERE emp_code IN("+empcode+")";
			}

			print_log_d("Emp Target Achievment", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getColumnCount(); ii++) {
					KeyValue obj1 = new KeyValue();
					obj1.setKey("Sales Target");
					obj1.setValue(cursor.getString(0));
					KeyValueList.add(obj1);
					KeyValue obj2 = new KeyValue();
					obj2.setKey("Sales Achieved");
					obj2.setValue(cursor.getString(1));
					KeyValueList.add(obj2);
					obj1=null;
					obj2=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Emp Target Achievment","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return KeyValueList;
	}

	public String getQuantity(String condition,String leaves)
	{
		String quantity="";
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT SUM(STL.convert_qty_two) AS total_qty  FROM sauda_transaction_log STL WHERE "+condition+" AND STL.emp_code IN("+leaves+")", new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				if(cursor.getString(0)!=null){
					quantity=cursor.getString(0);
				}else{
					quantity="";
				}

				cursor.close();
				return quantity;
			}
		} catch (Exception e) {
			print_log_d("Sauda Transaction Log","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return quantity;
	}

	public String GetLowerleavesOfEmployee(String empcode){
		String lowerLeaves="";
		String query ="";
		Cursor cursor = null;
		try {
			query = "SELECT lower_leaves FROM emp_master WHERE emp_code ='"+empcode+"'";
			print_log_d("Employee", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				lowerLeaves=cursor.getString(0);
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Employee Hierarchy","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return lowerLeaves;
	}

	public String GetMaxAllocatedTDForLowerleavesOfEmployee(String empcode,String currentProductGroupCode)
	{
		String lowerLeaves ="";
		String maxTDOfLowerLeaves ="0";
		String query ="";
		String query2 ="";
		Cursor cursor = null;
		Cursor cursor2 = null;
		try
		{
			query2="SELECT lower_leaves FROM emp_master WHERE emp_code ='"+empcode+"'";
			cursor2 = database.rawQuery(query2, null);
			if (cursor2.getCount() > 0)
			{
				cursor2.moveToFirst();
				lowerLeaves=cursor2.getString(0).trim();
				if(lowerLeaves.contains(","))
				{
					//removing current employee from lowe leaves
					String[] splitted_leaves = lowerLeaves.split(",");
					List<String> list = new ArrayList<String>(Arrays.asList(splitted_leaves));
					while(list.contains("'"+empcode+"'"))
					{
						list.remove("'"+empcode+"'");
					}
					if(list.size()>1)
					{
						lowerLeaves=TextUtils.join(",", list);
					}
					else
					{
						lowerLeaves="";
					}

					query="select max(TD) from TD_allocation where product_filter_code='"+currentProductGroupCode+"' AND  emp_code IN("+lowerLeaves+")";
					cursor = database.rawQuery(query, null);
					if (cursor.getCount() > 0)
					{
						cursor.moveToFirst();
						maxTDOfLowerLeaves =cursor.getString(0);
						if(!Utils.isNumeric(maxTDOfLowerLeaves))
						{
							maxTDOfLowerLeaves ="0";
						}

					}
				}

			}



		}
		catch (Exception e)
		{

		}
		finally
		{
			if(cursor !=null)
			{
				cursor.close();
			}
			if(cursor2!=null)
			{
				cursor2.close();
			}
		}
		return maxTDOfLowerLeaves;
	}

	public String GetTDOfEmployee(String empcode,String currentProductGroupCode)
	{
		String td ="0";
		String query ="";
		Cursor cursor = null;
		try
		{
			query="select TD from TD_allocation where product_filter_code='"+currentProductGroupCode+"' AND  emp_code ='"+empcode+"'";
			cursor = database.rawQuery(query, null);
			if (cursor!=null && cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				td =cursor.getString(0);
				if(!Utils.isNumeric(td))
				{
					td ="0";
				}
			}
		}
		catch (Exception e)
		{

		}
		finally
		{
			if(cursor !=null)
			{
				cursor.close();
			}
		}
		return td;
	}

	public void insertOrUpdateAppInfo(String nickName, String appVersion,String dbVersion,String condition)
	{
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

	public void insertToEmployeeMaster()
	{

	}

	public boolean checkLastLoginSuccessfull()
	{
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

	public long updateEmployeeMaster()
	{
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

	public long updateEmployeeMasterFlag()
	{
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

	public void updateUpdationStatus(String version, String status)
	{
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("app_version", version);
			cv.put("updation_flag", status);
			synchronized (Lock) {
				database.update("employee_master_login", cv, "emp_code=?",new String[] { get_emp_or_customer_code(mContext) });
			}
			print_log_d("EmployeeMaster:", "Updated");
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Employee Master", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
	}


	public EmployeeDetails getEmployeeObj()
	{
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

	public ArrayList<CatalogueInfoDetails> getCatalogueVal()
	{
		ArrayList<CatalogueInfoDetails> catalogueList=new ArrayList<>();
		Cursor cursor=null;
		try
		{
			cursor = database.rawQuery("SELECT DISTINCT vertical,file_name, file_version FROM catalogue_info", new String[] {});
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					CatalogueInfoDetails temp=new CatalogueInfoDetails();
					temp.setVertical(cursor.getString(0));
					temp.setFileName(cursor.getString(1));
					temp.setFileVersion(cursor.getString(2));
					catalogueList.add(temp);
					cursor.moveToNext();
				}


				cursor.close();
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
		return catalogueList;
	}
	/*
	 * ****************************** DELETE MASTER TABLE DATA
	 * *******************************************
	 */
	public void deleteNonIncrementalData()
	{
		deleteOutstandingMaster();
		//deleteRoutePlan();
		deleteVendorMaster();
		deleteSchemeDetails();
//		deleteBranchMaster();
		deleteRedeemeDetails();
		deleteUserAccessDetails();
	}

	public void deleteUserAccessDetails()
	{
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

	public void deleteSchemeDetails()
	{
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

	public void deleteRedeemeDetails()
	{
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

//	public void deleteLoyaltyPurhaseDetails()
//	{
//		database.beginTransaction();
//		try {
//			database.execSQL("DELETE FROM loyalty_purchase_details");
//			database.setTransactionSuccessful();
//		} catch (Exception e) {
//			print_log_d("Loyalty Details Delete", "Exception:" + e);
//		} finally {
//			database.endTransaction();
//		}
//	}

	public void deleteVendorMaster()
	{
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


	public void deleteOutstandingMaster()
	{
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

	public void DeleteMarketFeedbackDetails()
	{
		try {
			database.execSQL("DELETE FROM market_feedback_details");
		} catch (Exception e) {
			print_log_d("Market Feedback Details Delete", "Exception:" + e);
		}
	}

	public void deleteRoutePlanDetailsDetails()
	{
		try {
			database.execSQL("DELETE FROM route_plan_details");
		} catch (Exception e) {
			print_log_d("Route Plan Details Delete", "Exception:" + e);
		}
	}

	public void deleteUserDetails()
	{
		try {
			database.execSQL("DELETE FROM user_details");
		} catch (Exception e) {
			print_log_d("User Details Delete", "Exception:" + e);
		}
	}

	public void deleteProductDetails()
	{
		try {
			database.execSQL("DELETE FROM product_details");
		} catch (Exception e) {
			print_log_d("Product Details Delete", "Exception:" + e);
		}
	}

	public long InsertToMarketFeedbackDetails(MarketFeedbackDetails obj)
	{
		DeleteMarketFeedbackDetails();
		long status = 0;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("market_feedback_id", obj.getMarketFeedbackId());
			cv.put("user_id", obj.getUserId());
			cv.put("mf_group_enable", obj.getMfGroupEnable());
			cv.put("mf_col1", obj.getMfCol1());
			cv.put("mf_col2", obj.getMfCol2());
			cv.put("mf_col3", obj.getMfCol3());
			cv.put("mf_col4", obj.getMfCol4());
			cv.put("mf_sub_menu_details", obj.getMfSubMenuDetails());
			cv.put("mf_sub_menu_image", obj.getMfSubMenuImage());

			synchronized (Lock) {
				status = database.insertWithOnConflict("market_feedback_details", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				print_log_d("Market Feedback Details:", "Data Inserted");
			}
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Market Feedback Details", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
		return status;
	}


	public long InsertToMenuDetails(MenuDetails menuObject)
	{
		TruncateTableByTableName("menu_details");
		long status = 0;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("menu_id", menuObject.getMenuId());
			cv.put("user_id", menuObject.getUserid());
			cv.put("attendance", menuObject.getAttendance());
			cv.put("route_plan", menuObject.getRoutePlan());
			cv.put("take_order", menuObject.getOrder());
			cv.put("collection", menuObject.getCollection());
			cv.put("stk_audit", menuObject.getStkAudit());
			cv.put("business_prospect", menuObject.getBusinessProspect());
			cv.put("tour_exp", menuObject.getTourExp());
			cv.put("capture_image", menuObject.getCaptureImage());
			cv.put("notes", menuObject.getNotesInfo());
			cv.put("activity_report", menuObject.getActivityReport());
			cv.put("loyalty", menuObject.getLoyalty());
			cv.put("mis_report", menuObject.getMisReport());
			cv.put("delete_transaction", menuObject.getDeleteTransaction());
			cv.put("loading_freight", menuObject.getLoadingFreight());
			cv.put("sauda_allocation", menuObject.getSaudaAllocation());
			cv.put("survey", menuObject.getSurvey());
			cv.put("product_promotion", menuObject.getSampling());
			cv.put("replacement", menuObject.getReplacement());
			cv.put("market_feedback", menuObject.getMarketFeedback());
			cv.put("sauda_allocation_app", menuObject.getSaudaAllocationfromApp());
			cv.put("pending_contract", menuObject.getPendingContract());
			cv.put("sauda_mis", menuObject.getSaudaMis());
			cv.put("order_status", menuObject.getOrderStatus());
			cv.put("checkout", menuObject.getCheckOut());
			cv.put("sauda_outstanding", menuObject.getSaudaOutstanding());
			cv.put("sale_performance", menuObject.getSalePerformance());
			cv.put("check_in_out", menuObject.getCheckInOut());
			cv.put("outstanding", menuObject.getOutstanding());
			cv.put("outstanding_ageing", menuObject.getOutstandingAgeing());
			cv.put("target_achievement", menuObject.getTargetAcheivement());
			cv.put("wholesaler_info", menuObject.getWholeSaleInfo());
			cv.put("self_appraisal", menuObject.getSelfAppraisalDetails());
			cv.put("yellow_card", menuObject.getYellowCard());
			cv.put("catalogue", menuObject.getCatalogue());
			cv.put("catalogue_url", menuObject.getCatalogueUrl());
			cv.put("tele_tran", menuObject.getTelephonicTransaction());
			cv.put("TD_allocation_app", menuObject.getTDAllocation());
			cv.put("catalogue_dependency", menuObject.getcatalogue_dependency());
			cv.put("TD_allocation_vertical", menuObject.getTD_allocation_vertical());
			cv.put("run_time_TD_approval_vertical", menuObject.getrun_time_TD_approval_vertical());
			cv.put("quotation", menuObject.getquotation());
			cv.put("CRM_app", menuObject.getCRM_app());
			cv.put("ISP", menuObject.getISP());
			cv.put("monthly_report_mail", menuObject.getmonthly_report_mail());
			cv.put("retailer_app", menuObject.getretailer_app());
			synchronized (Lock) {
				status = database.insertWithOnConflict("menu_details", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
			}
			database.setTransactionSuccessful();
		} catch (SQLException e)
		{

		}
		finally
		{
			database.endTransaction();
		}
		return status;
	}

	public long insertToRoutePlanDetails(RoutePlanDetails routeObject)
	{
		deleteRoutePlanDetailsDetails();
		long status = 0;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("route_plan_id", routeObject.getRoutePlanId());
			cv.put("user_id", routeObject.getUserId());
			cv.put("route_plan_access_period",routeObject.getRoutePlanAccessPeriod());
			cv.put("route_plan_deviation", routeObject.getRoutePlanDeviation());
			cv.put("route_plan_approval", routeObject.getRoutePlanApproval());
			cv.put("route_plan_flow", routeObject.getRoutePlanFlow());
			cv.put("route_customer_planning", routeObject.getRouteCustomerPlanning());
			cv.put("distributor_route_planning", routeObject.getDistributorRoutePlanning());
			cv.put("distributor_route_planning_multiple", routeObject.getDistributorRoutePlanning());
			synchronized (Lock) {
				status = database.insertWithOnConflict("route_plan_details",null, cv, SQLiteDatabase.CONFLICT_IGNORE);
				print_log_d("Route_plan_details:", "Data Inserted");
			}
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Route Plan Details", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToUserDetails(UserDetails userObject)
	{
		deleteUserDetails();
		long status = 0;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("user_id", userObject.getUserId());
			cv.put("name", userObject.getName());
			cv.put("address", userObject.getAddress());
			cv.put("phone_no", userObject.getPhoneNo());
			cv.put("email", userObject.getEmail());
			cv.put("license_key", userObject.getLicenseKey());
			cv.put("no_users", userObject.getNoUser());
			cv.put("nick_name", userObject.getNickName());
			cv.put("no_of_branches", userObject.getNoBranches());
			cv.put("image", 0);
			cv.put("email_hierarchywise", userObject.getEmailHierarchy());
			cv.put("vertical_fields", userObject.getVerticalFields());
			cv.put("vertical_fields_value", userObject.getVerticalFieldsValue());
			cv.put("previous_stock", userObject.getPreviousStock());
			cv.put("multiple_prospect", userObject.getMultipleProspect());
			cv.put("multiple_prospect_value",userObject.getMultipleProspectValue());
			cv.put("stock_audit_scan", userObject.getStkAuditScan());
			cv.put("stock_audit_rate", userObject.getStockAuditRate());
			cv.put("location_drag_drop", userObject.getLocation_drag_drop());
			cv.put("tour_plan_daywise", userObject.getTourPlanDayWise());
			cv.put("check_in_out_typeval", userObject.getCheckInOutTypeVal());
			cv.put("fcm", userObject.getFcm());
			cv.put("minimum_stock", userObject.getMinimumStock());
			cv.put("stk_audit_unit", userObject.getStockAuditUnit());
			cv.put("stk_audit_irrespective_routeplan", userObject.getstk_audit_irrespective_routeplan());
			cv.put("stk_audit_cust_type", userObject.getstk_audit_cust_type());
			cv.put("notes_info_hint_remarks", userObject.getnotes_info_hint_remarks());
			cv.put("notes_info_upload_photo", userObject.getnotes_info_upload_photo());
			cv.put("country", userObject.getcountry());
			cv.put("time_zone", userObject.gettimeZone());
			synchronized (Lock)
			{
				status = database.insertWithOnConflict("user_details", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
			}
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("User Details", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
		return status;
	}


	public void UpdateMRPDetails(String productcode,String mrpvalue)
	{
		database.beginTransaction();
		String sql="UPDATE mrp SET mrp_value='"+mrpvalue+"', sale_rate='"+mrpvalue+"' WHERE sku_code='"+productcode+"'";
		try {
			database.execSQL(sql);
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("MRP Updated", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
	}

	public void UpadateSurveyType()
	{
		database.beginTransaction();
		String sql="update survey_output set type=(select survey_sub_menu from survey_input WHERE row_id= survey_output.row_id)";
		try {
			database.execSQL(sql);
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Location Update", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
	}


	public void UpdateMRPDetails()
	{
		database.beginTransaction();
		String sql="UPDATE mrp SET mrp_value='0', sale_rate='0' ";
		try {
			database.execSQL(sql);
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("MRP Updated", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
	}
	public long UpdateCustomerDetails(String pin,String phone,String address)
	{
		long status = 0;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("pin", pin);
			cv.put("phone_no", phone);
			cv.put("check_flag", "0");
			cv.put("address",address);
			database.update("customer_master",cv,"customer_code=?",	new String[] { Constants.selectedCustomer.getCustomerCode().replace("'","") });
			status = 1;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Customer Master", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
		return status;
	}
	public long InsertToSurveyFormDetails(SurveyFormDetails surveyObject)
	{
		TruncateTableByTableName("survey_form_details");
		long status = 0;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("survey_form_id", surveyObject.getSurveyFormId());
			cv.put("user_id", surveyObject.getSurveyUserId());
			cv.put("survey_menu",surveyObject.getSurveyMenu());
			cv.put("survey_type",surveyObject.getSurveyType());
			cv.put("survey_type_details",surveyObject.getSurveyTypeDetails());
			cv.put("mall_survey_relation",surveyObject.getSurveyMallSurveyRelation());
			cv.put("survey_sub_type_details",surveyObject.getSurveySubTypeDetails());
			cv.put("OTP", surveyObject.getSurveyOTP());
			cv.put("survey_layer", surveyObject.getSurveyLayer());
			cv.put("survey_submenu", surveyObject.getSurveySubMenu());
			cv.put("survey_submenu_details", surveyObject.getSurveySubMenuDetails());
			cv.put("outlet_menu", surveyObject.getSurveyOutletMenu());
			cv.put("survey_route_plan", surveyObject.getSurveyRoutePlan());
			cv.put("other_text", surveyObject.getSurveyOtherText());
			cv.put("survey_report_row_id", surveyObject.getSurveyReportRowId());
			cv.put("customer_email_update", surveyObject.getCustomerEmailUpdate());
			synchronized (Lock) {
				status = database.insertWithOnConflict("survey_form_details",null, cv, SQLiteDatabase.CONFLICT_IGNORE);
				print_log_d("survey_form_details:", "Data Inserted");
			}
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Survey Form Details", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
		return status;
	}


	public long insertToSaudaFormDetails(SaudaFormDetails saudaObject)
	{
		TruncateTableByTableName("sauda_form_details");

		long status = 0;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("sauda_form_id", saudaObject.getSaudaFormId());
			cv.put("user_id", saudaObject.getUserId());
			cv.put("sauda_allocation_carry_forward",saudaObject.getSaudaAllocationCarryForward());
			cv.put("sauda_depot_wise", saudaObject.getSaudaDepotWise());
			cv.put("sauda_rate_variable", saudaObject.getSaudaRateVariable());
			cv.put("sauda_rate_variable_value",saudaObject.getSaudaRateVariableValue());
			cv.put("sauda_booked_through", saudaObject.getSaudaBookedThrough());
			cv.put("sauda_valid_from", saudaObject.getSaudaValidFrom());
			cv.put("sauda_rate_dependent_on_despatch_point", saudaObject.getsauda_rate_dependent_on_despatch_point());
			cv.put("sauda_rate_dependent_on_despatch_point_val", saudaObject.getsauda_rate_dependent_on_despatch_point_val());
			cv.put("sauda_rate_dependent_on_despatch_point_verticlewise", saudaObject.getsauda_rate_dependent_on_despatch_point_verticlewise());
			cv.put("sauda_rate_dependent_on_despatch_point_verticle_val", saudaObject.getsauda_rate_dependent_on_despatch_point_verticle_val());
			cv.put("secondary_freight_vertical", saudaObject.getSecondaryFreightVertical());
			cv.put("special_discount_vertical", saudaObject.getSpecialDiscountVertical());
			cv.put("customer_email_check_vertical", saudaObject.getCustomerEmailCheckVertical());
			synchronized (Lock)
			{
				status = database.insertWithOnConflict("sauda_form_details",null, cv, SQLiteDatabase.CONFLICT_IGNORE);
			}
			database.setTransactionSuccessful();
		}
		catch (SQLException e)
		{

		}
		finally
		{
			database.endTransaction();
		}
		return status;
	}

	public long insertToSelfAppraisalDetails(SelfAppraisalDetails dataObject)
	{
		TruncateTableByTableName("self_appraisal_details");
		long status = 0;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("self_appraisal_id", dataObject.getSelfAppraisalId());
			cv.put("user_id", dataObject.getUserId());
			cv.put("multiple_target_achievement", dataObject.getMultipleTargetAchievement());
			cv.put("multiple_target_achievement_val", dataObject.getMultipleTargetAchievementVal());
			cv.put("volume_wise", dataObject.getVolumeWise());
			cv.put("value_wise", dataObject.getValueWise());
			cv.put("product_group_wise", dataObject.getProductGroupWise());
			cv.put("product_sub_group_wise", dataObject.getProductSubGroupWise());
			cv.put("product_brand_wise", dataObject.getProductBrandWise());
			cv.put("product_wise", dataObject.getProductWise());
			cv.put("employee_wise", dataObject.getEmployeeWise());
			cv.put("customer_wise", dataObject.getCustomerWise());
			cv.put("branch_wise", dataObject.getBranchWise());
			cv.put("HQ_wise", dataObject.getHQWise());
			cv.put("route_wise", dataObject.getRouteWise());
			cv.put("on_total", dataObject.getOnTotal());
			cv.put("on_individual", dataObject.getOnIndividual());
			cv.put("month_wise", dataObject.getMonthWise());
			cv.put("week_wise", dataObject.getWeekWise());
			cv.put("day_wise", dataObject.getDayWise());
			cv.put("UOM_val", dataObject.getUomVal());
			synchronized (Lock)
			{
				status = database.insertWithOnConflict("self_appraisal_details",null, cv, SQLiteDatabase.CONFLICT_IGNORE);
			}
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Sauda Form Details", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToOrderFormDetails(OrderFormDetails orderObject)
	{
		TruncateTableByTableName("order_form_details");
		long status = 0;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("order_form_id", orderObject.getOrderFormId());
			cv.put("user_id", orderObject.getUserId());
			cv.put("credit_limit", orderObject.getCreditLimit());
			cv.put("cl_stk", orderObject.getClosingStk());
			cv.put("mrp_input_dropdown", orderObject.getMrpDrpdwn());
			cv.put("mrp", orderObject.getMrp());
			cv.put("TD", orderObject.getTradeDiscount());
			cv.put("add_customer", orderObject.getAddCustomer());
			cv.put("tagged_customer_for_business_prospect",	orderObject.getCustomerBsnsProspct());
			cv.put("TD_type", orderObject.getTdType());
			cv.put("sale_rate", orderObject.getSaleRate());
			cv.put("sale_rate_input_dropdown", orderObject.getSaleRateDrpdwn());
			cv.put("attached_printer", orderObject.getAttachedPrinter());
			cv.put("printer_mandatory", orderObject.getPrinter_mandetory());
			cv.put("payment_type", orderObject.getPayment_type());
			cv.put("tag_distributor", orderObject.getTagDistributor());
			cv.put("sale", orderObject.getSale());
			cv.put("instruction", orderObject.getInstruction());
			cv.put("VAT", orderObject.getVat());
			cv.put("VAT_details", orderObject.getVatDetails());
			cv.put("branch_rds_transfer", orderObject.getBranchRDSTransfer());
			cv.put("amount", orderObject.getAmount());
			cv.put("VAT_type", orderObject.getVatType());
			cv.put("TD_calc", orderObject.getTdCalc());
			cv.put("TD_trans_type", orderObject.getTdTransType());
			cv.put("VAT_calc_on", orderObject.getVatCalcOn());
			cv.put("TD_validation", orderObject.getTDValidation());
			cv.put("TD_calc_basedon", orderObject.getTDCalcBasedOn());
			cv.put("premium", orderObject.getPremium());
			cv.put("previous_order", orderObject.getPreviousOrder());
			cv.put("add_customer_OTP", orderObject.getNewCustomerOtp());
			cv.put("customer_information_check", orderObject.getCustomerInfoCheck());
			cv.put("add_customer_route_creation", orderObject.getAddCustomerRouteCreation());
			cv.put("order_type", orderObject.getOrderType());
			cv.put("freight_component", orderObject.getFreightComponent());
			cv.put("tax_type", orderObject.getTaxType());
			cv.put("destination", orderObject.getDestination());
			cv.put("input_screen_normal", orderObject.getInputScreenNormal());
			cv.put("input_screen_special", orderObject.getInputScreenSpecial());
			cv.put("add_customer_details", orderObject.getAddCustomerDetails());
			cv.put("add_customer_trade_nontrade", orderObject.getAddCustomerTradeNonTrade());
			cv.put("printer_type", orderObject.getPrintMedium());
			cv.put("printer_menu", orderObject.getPrinterMenu());
			cv.put("hint_remarks", orderObject.getHintsRemarks());
			cv.put("hint_remarks_val", orderObject.getHintsRemarksVal());
			cv.put("add_customer_image_creation", orderObject.getAddCustomImage());
			cv.put("distributor_route_emp_relation", orderObject.getDistributorRouteEmployeeRelation());
			cv.put("multiple_UOM", orderObject.getMultipleUom());
			cv.put("input_screen_planwise", orderObject.getInputScreenPlanwise());
			cv.put("TD_type_input_dropdown", orderObject.getTdInputDropDown());
			cv.put("input_screen_planwise_filter1wise", orderObject.getInputScreenPlanwiseFilter1Wise());
			cv.put("input_screen_price_validation", orderObject.getInputScreenPriceValidation());
			cv.put("sauda_sale_rate_input_dropdown", orderObject.getSaudaSaleRateDrpdwn());

			synchronized (Lock) {
				status = database.insertWithOnConflict("order_form_details",null, cv, SQLiteDatabase.CONFLICT_IGNORE);
			}
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Order Form Details", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToProductDetails(ProductDetails productObj)
	{
		deleteProductDetails();
		long status = 0;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("product_id", productObj.getProductId());
			cv.put("user_id", productObj.getUserId());
			cv.put("no_of_filter", productObj.getNoFilter());
			cv.put("col1", productObj.getCol1());
			cv.put("col2", productObj.getCol2());
			cv.put("col3", productObj.getCol3());
			cv.put("col4", productObj.getCol4());
			cv.put("uom_wise_mrp", productObj.getUomWiseMRP());
			cv.put("sauda_allocation_basedon_filter",productObj.getSaudaFilter());
			cv.put("product_in_business_prospect",productObj.getProductBusinessProspect());
			cv.put("branch_wise_product",productObj.getBranchWiseProduct());
			cv.put("secondary_unit",productObj.getSecondaryUnit());
			cv.put("destination_price_list",productObj.getDestinationPriceList());
			cv.put("destination_ordertype_price_list",productObj.getDestinationOrderTypePriceList());
			cv.put("state_wise_mrp",productObj.getStateWiseMrp());
			cv.put("multiple_rate",productObj.getMultipleRate());
			cv.put("product_qty_wise_TD",productObj.getProductQtyWiseTD());
			cv.put("focus_product",productObj.getFocusProduct());

			synchronized (Lock) {
				status = database.insertWithOnConflict("product_details", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				print_log_d("Product_details:", "Data Inserted");
			}
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Product Details", "Exception:" + e);
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public void GetMarketFeedbackDetails()
	{
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT * FROM market_feedback_details",new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				Constants.marketFeedbackDetailsObj = new MarketFeedbackDetails();
				Constants.marketFeedbackDetailsObj.setMarketFeedbackId(cursor.getString(0));
				Constants.marketFeedbackDetailsObj.setUserId(cursor.getString(1));
				Constants.marketFeedbackDetailsObj.setMfGroupEnable(cursor.getString(2));
				Constants.marketFeedbackDetailsObj.setMfCol1(cursor.getString(3));
				Constants.marketFeedbackDetailsObj.setMfCol2(cursor.getString(4));
				Constants.marketFeedbackDetailsObj.setMfCol3(cursor.getString(5));
				Constants.marketFeedbackDetailsObj.setMfCol4(cursor.getString(6));
				Constants.marketFeedbackDetailsObj.setMfSubMenuDetails(cursor.getString(7));
				Constants.marketFeedbackDetailsObj.setMfSubMenuImage(cursor.getString(8));
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		}  finally{
			if(cursor!=null){
				cursor.close();
			}
		}
	}


	public void getMenuDetailsObj()
	{
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

	public RoutePlanDetails getRoutePlanDetailsObj()
	{
		RoutePlanDetails routePlanDetailsObj = new RoutePlanDetails();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT * FROM route_plan_details", new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				routePlanDetailsObj.setRoutePlanId(cursor.getString(0));
				routePlanDetailsObj.setUserId(cursor.getString(1));
				routePlanDetailsObj.setRoutePlanAccessPeriod(cursor.getString(2));
				routePlanDetailsObj.setRoutePlanDeviation(cursor.getString(3));
				routePlanDetailsObj.setRoutePlanApproval(cursor.getString(4));
				routePlanDetailsObj.setRoutePlanFlow(cursor.getString(5));
				routePlanDetailsObj.setRouteCustomerPlanning(cursor.getString(6));
				routePlanDetailsObj.setDistributorRoutePlanning(cursor.getString(7));
				routePlanDetailsObj.setDistributorRoutePlanningMultiple(cursor.getString(8));
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return routePlanDetailsObj;
	}

	public void getUserDetailsObj()
	{
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


	public String CheckLoginOfDay(String date)
	{
		String checkinstatus="";
		Cursor cursor = database.rawQuery("SELECT * FROM employee_master_login WHERE date=?",new String[] { date });
		if (cursor.getCount() > 0) {
			checkinstatus="NO";   // NOT FIRST LOGIN OF DAY
		}else{
			checkinstatus="YES";  // FIRST LOGIN OF DAY
		}
		if(cursor!=null){
			cursor.close();
		}
		return checkinstatus;
	}


	public void getProductDetailsObj()
	{
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

	public long insertToRouteMaster(ArrayList<RouteDetails> routeList,String noneed)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < routeList.size(); ii++) {
				RouteDetails detailObj = routeList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("route_code", detailObj.getRouteCode());
				cv.put("route_name", detailObj.getRouteName());
				cv.put("emp_code", get_emp_or_customer_code(mContext));
				cv.put("flag", 0);
				synchronized (Lock) {
					database.insertWithOnConflict("route_master", null, cv,	SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("route_master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Route_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}


	public long insertToRouteMaster(ArrayList<RouteDetails> routeList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < routeList.size(); ii++) {
				RouteDetails detailObj = routeList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("route_code", detailObj.getRouteCode());
				cv.put("route_name", detailObj.getRouteName());
				cv.put("emp_code", get_emp_or_customer_code(mContext));
				if (Constants.isFirstLoginOfApp	|| Constants.isRouteTableUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict("route_master", null, cv,	SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("route_master:", "Data Inserted");
					}
				} else {
					database.execSQL("DELETE FROM route_master WHERE route_code='"+ detailObj.getRouteCode().replace("'", "") + "'");
					if (detailObj.getReplacingRouteCode().length() > 0) {
						cv.put("route_code", detailObj.getReplacingRouteCode());
					}
					database.insertWithOnConflict("route_master", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("route_master:", "Updated");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Route_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToRouteMasterCrm(ArrayList<RouteDetails> routeList)
	{
		TruncateTableByTableName("route_master");
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < routeList.size(); ii++)
			{
				RouteDetails detailObj = routeList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("route_code", detailObj.getRouteCode());
				cv.put("route_name", detailObj.getRouteName());
				cv.put("emp_code", get_emp_or_customer_code(mContext));
				synchronized (Lock)
				{
					database.insertWithOnConflict("route_master", null, cv,	SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Route_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public void getRouteListForEmployeeTodayCRM()
	{
		String selectQuery = "";
		Cursor cursor=null;
		allocatedRouteCodeTodayCrm = new ArrayList<>();
		allocatedRouteNameTodayCrm = new ArrayList<>();
		try {
			String today=Utils.changeDateFormat("yyyyMMdd","yyyy-MM-dd",dateString);
			selectQuery = "SELECT route_code FROM emp_datewise_route_allocation WHERE allocation_date='"+today+"'";

			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int i = 0; i < cursor.getCount(); i++)
				{

					allocatedRouteCodeTodayCrm.add(cursor.getString(0));
					allocatedRouteNameTodayCrm.add(getRouteNameFromRouteCode(cursor.getString(0)));
					cursor.moveToNext();
				}
			}
			cursor.close();
		}
		catch (Exception e)
		{

		}
		finally
		{
			if(cursor !=null)
			{
				cursor.close();
			}
		}
	}

	public long InsertToDistributorRouteMaster(ArrayList<RouteDetails> routeList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < routeList.size(); ii++) {
				RouteDetails detailObj = routeList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("distributor_code", detailObj.getDistributorCode());
				cv.put("route_code", detailObj.getRouteCode());
				cv.put("emp_code", detailObj.getEmployeeCode());
				if (Constants.isFirstLoginOfApp	|| Constants.isDistributorRouteTableUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict("distributor_route_relation", null, cv,	SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("distributor_route_relation:", "Data Inserted");
					}
				} else {
					database.execSQL("DELETE FROM distributor_route_relation WHERE distributor_code='"+detailObj.getDistributorCode().replace("'", "")+"' AND route_code='"+ detailObj.getRouteCode().replace("'", "") + "'");
					database.insertWithOnConflict("distributor_route_relation", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("route_master:", "Updated");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Route_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToNonTradeCustomerMaster(ArrayList<CustomerDetails> custList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < custList.size(); ii++) {
				CustomerDetails obj = custList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("customer_code", obj.getCustomerCode());
				cv.put("customer_name", obj.getCustomerName());
				cv.put("address",obj.getAddress());
				cv.put("phone_no", obj.getNumber());
				cv.put("route_code", obj.getRouteCode());
				cv.put("emp_code", obj.getEmpCode());
				cv.put("rds_tag", obj.getRdsTag());

				if (Constants.isFirstLoginOfApp	|| Constants.isNonTradeCustomer) {
					synchronized (Lock) {
						database.insertWithOnConflict("non_trade_customer_master", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("Non Trade Customer Master", "Data Inserted");
					}
				} else {
					database.execSQL("DELETE FROM non_trade_customer_master WHERE customer_code='"+ obj.getCustomerCode().replace("'", "")+ "'");
					database.insertWithOnConflict("non_trade_customer_master", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Non Trade Customer Master", "Data Updated");
				}
				obj=null;
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Non Trade Customer Master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

//	public long insertToCustomerMaster(ArrayList<CustomerDetails> custList)
//	{
//		database.beginTransaction();
//		DataTableByTableName("customer_master");
//
//	}
	public long insertToCustomerMaster(ArrayList<CustomerDetails> custList)
	{
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
	public long insertToCustomerMasterForAllocation(ArrayList<CustomerDetails> custList)
	{
		TruncateTableByTableName("customer_master");
		long status = 0;
		int ii = 0;
		database.beginTransaction();
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

				synchronized (Lock)
				{
					database.insertWithOnConflict("customer_master", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				}

				obj=null;
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Customer_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long updateCreditLimit(ArrayList<CustomerDetails> creditList)
	{
		long status = 0;
		int ii = 0;
		database.setLockingEnabled(false);
		database.beginTransaction();
		try {
			for (ii = 0; ii < creditList.size(); ii++) {
				CustomerDetails detailObj = creditList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("customer_code", detailObj.getCustomerCode());
				cv.put("credit_limit", detailObj.getCreditLimit());
				database.update(
						"customer_master",
						cv,
						"customer_code=?",
						new String[] { detailObj.getCreditLimit().replace("'",
								"") });
				print_log_d("Customer_master:", "Credit Limit Updated");
				print_Log_d("Count : " + ii);
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Credit Limit", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToOutstandingMaster(ArrayList<OutstandingDetails> outList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < outList.size(); ii++) {
				OutstandingDetails detailObj = outList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("customer_code", detailObj.getCustomerCode());
				cv.put("recid", detailObj.getRecId());
				cv.put("invoice_id", detailObj.getInvoice_id());
				cv.put("customer_name", detailObj.getCustomerName());
				cv.put("date", detailObj.getDate());
				cv.put("invoice_amount", detailObj.getInvoice_amount());
				cv.put("due_amount", detailObj.getDue_amount());

				synchronized (Lock) {
					database.insertWithOnConflict("outstanding_master", null,
							cv, SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Outstanding_master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Outstanding_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public void DeleteMenuAccess()
	{
		try {
			database.execSQL("DELETE FROM menu_access");
		} catch (Exception e) {

		}
	}

	public void DeleteEmployeeMenuAccess()
	{
		try {
			database.execSQL("DELETE FROM emp_menu_access");
		} catch (Exception e) {

		}
	}

	public long InserttoEmployeeMenuAccessTable(ArrayList<EmployeeMenuAccess> employeeMenuAccessList)
	{
		DeleteEmployeeMenuAccess();
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < employeeMenuAccessList.size(); ii++) {
				EmployeeMenuAccess obj = employeeMenuAccessList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("menu", obj.getMenu());
				synchronized (Lock) {
					database.insertWithOnConflict("emp_menu_access", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Employee Menu Access:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Employee Menu Access", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}



	public long InserttoMenuAccessTable(ArrayList<MenuAccess> menuAccessList)
	{
		DeleteMenuAccess();
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		TruncateTableByTableName("menu_access");
		try {
			for (ii = 0; ii < menuAccessList.size(); ii++) {
				MenuAccess obj = menuAccessList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("not_accessibility_menu", obj.getNotAccessibilityMenu());
				synchronized (Lock) {
					database.insertWithOnConflict("menu_access", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Menu Access:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Menu Access", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}


	public long insertToUserAccessTable(ArrayList<UserAccessDetails> outList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		TruncateTableByTableName("user_access");
		try {
			for (ii = 0; ii < outList.size(); ii++) {
				UserAccessDetails detailObj = outList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("emp_code", detailObj.getEmpCode());
				cv.put("accessibility_menu", detailObj.getAccessibilityMenu());

				synchronized (Lock) {
					database.insertWithOnConflict("user_access", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("user_access:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("user_access", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToProductGroupMaster(
			ArrayList<ProductGroupDetails> grpList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < grpList.size(); ii++) {
				ProductGroupDetails detailObj = grpList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("product_group_code", detailObj.getGroupCode());
				cv.put("product_group_name", detailObj.getGroupName());
				cv.put("vertical_value", detailObj.getVerticalValue());
				if (Constants.isFirstLoginOfApp
						|| Constants.isProductGroupTableUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict("product_group_master",
								null, cv, SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("Product_group_master:", "Data Inserted");
					}
				} else {
					database.execSQL("DELETE FROM product_group_master WHERE product_group_code='"
							+ detailObj.getGroupCode().replace("'", "") + "'");
					database.insertWithOnConflict("product_group_master", null,
							cv, SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Product_group_master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Product_group_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToProductSubGroupMaster(ArrayList<ProductSubGrpDetails> subGrpList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < subGrpList.size(); ii++) {
				ProductSubGrpDetails detailObj = subGrpList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("product_group_code", detailObj.getGrpCode());
				cv.put("product_sub_group_code", detailObj.getSubGrpCode());
				cv.put("product_sub_group_name", detailObj.getSubGrpName());
				if (Constants.isFirstLoginOfApp
						|| Constants.isProductSubGroupTableUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict(
								"product_sub_group_master", null, cv,
								SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("Product_sub_group_master:", "Data Inserted");
					}
				} else {
					database.execSQL("DELETE FROM product_sub_group_master WHERE product_sub_group_code='"
							+ detailObj.getSubGrpCode().replace("'", "") + "'");
					database.insertWithOnConflict("product_sub_group_master",
							null, cv, SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Product_sub_group_master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Product_sub_group_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToProductBrandMaster(
			ArrayList<ProductBrandDetails> brndList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < brndList.size(); ii++) {
				ProductBrandDetails detailObj = brndList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("product_sub_group_code", detailObj.getSubGrpCode());
				cv.put("product_brand_code", detailObj.getBrandCode());
				cv.put("product_brand_name", detailObj.getBrandName());
				if (Constants.isFirstLoginOfApp
						|| Constants.isProductBrandTableUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict("product_brand_master",
								null, cv, SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("Product_brand_master:", "Data Inserted");
					}
				} else {
					database.execSQL("DELETE FROM product_brand_master WHERE product_brand_code='"
							+ detailObj.getBrandCode().replace("'", "") + "'");
					status = database.insertWithOnConflict(
							"product_brand_master", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Product_brand_master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Product_brand_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToPCatSubcatBrandMapping(ArrayList<CatSubCatBrandProdDetails> dataList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try
		{
			for (ii = 0; ii < dataList.size(); ii++)
			{
				CatSubCatBrandProdDetails detailObj = dataList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("id_row", detailObj.getid_row());
				cv.put("cat_id", detailObj.getcat_id());
				cv.put("sub_cat_id", detailObj.getsub_cat_id());
				cv.put("cat_name", detailObj.getcat_name());
				cv.put("sub_cat_name", detailObj.getsub_cat_name());
				cv.put("brand_id", detailObj.getbrand_prod_id());
				cv.put("brand_name", detailObj.getbrand_prod_name());
				cv.put("status", detailObj.getstatus());

				if (Constants.isFirstLoginOfApp)
				{
					synchronized (Lock)
					{
						database.insertWithOnConflict("cat_subcat_brand_mapping", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
					}
				}
				else
				{
					String sqlDeleteQuery = "DELETE FROM cat_subcat_brand_mapping WHERE cat_id='" + detailObj.getcat_id() + "' AND sub_cat_id='" + detailObj.getsub_cat_id() + "' AND brand_id='" + detailObj.getbrand_prod_id() + "'";
					database.execSQL(sqlDeleteQuery);
					database.insertWithOnConflict("cat_subcat_brand_mapping", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		}
		catch (SQLException e)
		{

		}
		finally
		{
			database.endTransaction();
		}
		return status;
	}

	public long insertToPCatSubcatProdMapping(ArrayList<CatSubCatBrandProdDetails> dataList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try
		{
			for (ii = 0; ii < dataList.size(); ii++)
			{
				CatSubCatBrandProdDetails detailObj = dataList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("id_row", detailObj.getid_row());
				cv.put("cat_id", detailObj.getcat_id());
				cv.put("sub_cat_id", detailObj.getsub_cat_id());
				cv.put("cat_name", detailObj.getcat_name());
				cv.put("sub_cat_name", detailObj.getsub_cat_name());
				cv.put("prod_id", detailObj.getbrand_prod_id());
				cv.put("prod_name", detailObj.getbrand_prod_name());
				cv.put("status", detailObj.getstatus());

				if (Constants.isFirstLoginOfApp)
				{
					synchronized (Lock)
					{
						database.insertWithOnConflict("cat_subcat_prod_mapping", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
					}
				}
				else
				{
					String sqlDeleteQuery = "DELETE FROM cat_subcat_prod_mapping WHERE cat_id='" + detailObj.getcat_id() + "' AND sub_cat_id='" + detailObj.getsub_cat_id() + "' AND prod_id='" + detailObj.getbrand_prod_id() + "'";
					database.execSQL(sqlDeleteQuery);
					database.insertWithOnConflict("cat_subcat_prod_mapping", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		}
		catch (SQLException e)
		{

		}
		finally
		{
			database.endTransaction();
		}
		return status;
	}

	public long insertToProductMaster(ArrayList<ProductMasterDetails> prodList)
	{
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
					print_log_d("Product_master3914 ", "Data Inserted "+ val);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Product_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToScheme(ArrayList<SchemeMasterDetails> prodList)
	{
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


	public long insertToBankMaster(final ArrayList<BankDetails> bankList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < bankList.size(); ii++) {
				BankDetails detailObj = bankList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("bank_id", detailObj.getBankId());
				cv.put("bank_name", detailObj.getBankName());
				if (Constants.isFirstLoginOfApp || Constants.isBankTableUpdated) {
					synchronized (Lock) {
						status = database.insertWithOnConflict("bank_master",
								null, cv, SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("bank_master:", "Data Inserted");
					}
				} else {
					database.execSQL("DELETE FROM bank_master WHERE bank_id='"
							+ detailObj.getBankId().replace("'", "") + "'");
					database.insertWithOnConflict("bank_master", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("bank_master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Bank_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

//	public MRPDetails GetDataFromMrpMaster(String skucode, String branchcode) {
//		MRPDetails mrpdetails = new MRPDetails();
//		database.beginTransaction();
//		try {
//			Cursor cursor = database.rawQuery(
//					"Select * FROM mrp where sku_code='" + skucode
//							+ "'  and branch_code='" + branchcode + "'", null);
//			cursor.moveToFirst();
//			mrpdetails.setProdCode(cursor.getString(0));
//			mrpdetails.setMrpCode(cursor.getString(1));
//			mrpdetails.setMrpValue(cursor.getString(2));
//			mrpdetails.setSaleRate(cursor.getString(3));
//			mrpdetails.setUom(cursor.getString(4));
//			mrpdetails.setBranchCode(cursor.getString(5));
//
//			cursor.close();
//		} catch (Exception e) {
//			print_Log_d("Exception:::::" + e.getMessage());
//		}
//		return mrpdetails;
//
//	}

	public long InsertToOutStandingAgeingMaster(ArrayList<OutstandingAgeing> outstandingAgeingList) {
		long status = 0;
		int ii = 0;
		DeleteOutstandingAgeing();
		database.beginTransaction();
		try {
			for (ii = 0; ii < outstandingAgeingList.size(); ii++) {
				OutstandingAgeing detailObj = outstandingAgeingList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("customer_code", detailObj.getCustomerCode());
				cv.put("customer_name", detailObj.getCustomerName());
				cv.put("outstanding_amount ", detailObj.getOutstandingAmount());
				cv.put("amount_0_15_days", detailObj.getOutstanding0to15());
				cv.put("amount_16_30_days", detailObj.getOutstanding16to30());
				cv.put("amount_31_45_days", detailObj.getOutstanding31to45());
				cv.put("amount_46_90_days", detailObj.getOutstanding46to90());
				cv.put("amount_greater_90_days", detailObj.getOutstandingGreater90());

				database.insertWithOnConflict("outstanding_ageing", null, cv,
						SQLiteDatabase.CONFLICT_IGNORE);
				detailObj=null;
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Pending Contract Ageing", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public void DeleteOutstandingAgeing(){
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM outstanding_ageing");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_Log_d("Exception:::::::::::" + e);
		} finally {
			database.endTransaction();
		}

	}

	public void DeleteSalesPerformance(){
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM sale_performance_details");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_Log_d("Exception:::::::::::" + e);
		} finally {
			database.endTransaction();
		}

	}

	public long InsertToSalesPerformance(ArrayList<SalesPerformance> salesPerformanceList) {
		long status = 0;
		int ii = 0;
		DeleteSalesPerformance();
		database.beginTransaction();
		try {
			for (ii = 0; ii < salesPerformanceList.size(); ii++) {
				SalesPerformance obj = salesPerformanceList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("customer_code", obj.getCustomerCode());
				cv.put("customer_name", obj.getCustomerName());
				cv.put("emp_code", obj.getEmployeeCode());
				cv.put("emp_name", obj.getEmployeeName());
				cv.put("product_group_code", obj.getProductGroupCode());
				cv.put("prod_code", obj.getProductCode());
				cv.put("prod_desc", obj.getProductDescription());
				cv.put("YTD_sale", obj.getYTDSale());
				cv.put("MTD_sale", obj.getMTDSale());
				database.insertWithOnConflict("sale_performance_details", null, cv,
						SQLiteDatabase.CONFLICT_IGNORE);
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Sale Performance Details", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}



	public long InsertToDestinationMaster(ArrayList<DestinationMaster> destinationList, int noColumn)
	{
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
	public long InsertToDumpMasterMaster(ArrayList<DumpMaster> destinationList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		TruncateTableByTableName("branch_dump");

		try {
			for (ii = 0; ii < destinationList.size(); ii++) {
				DumpMaster detailObj = destinationList.get(ii);
				ContentValues cv = new ContentValues();
				//branch_code,dump_code,dump_name,acedns,is_plant,download_time
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
	public Boolean VerticalExistsInCatalogueTable(String vertical)
	{
		Boolean VerticalExistsInCatalogueTable = false;
		Cursor cursor=null;
		try
		{
			cursor = database.rawQuery("SELECT vertical FROM catalogue_info where vertical='"+vertical+"'", null);
			if (cursor.getCount() > 0)
			{
				VerticalExistsInCatalogueTable=true;
			}
			cursor.close();
		}
		catch (Exception e)
		{
			VerticalExistsInCatalogueTable=false;
		}
		finally
		{
			if(cursor!=null){
				cursor.close();
			}
		}
		return VerticalExistsInCatalogueTable;
	}
	public long insertToCustBranchMaster(
			ArrayList<CustBranchRelationalDetails> objList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < objList.size(); ii++) {
				CustBranchRelationalDetails detailObj = objList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("customer_code", detailObj.getCustCode());
				cv.put("branch_code", detailObj.getBranchCode());
				cv.put("acedns",detailObj.getAcedns());
				if (Constants.isFirstLoginOfApp
						|| Constants.isCustBranchUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict(
								"customer_branch_relation", null, cv,
								SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("Customer_branch_relation:", "Data Inserted");
					}
				} else {
					database.execSQL("DELETE FROM customer_branch_relation WHERE customer_code ='"
							+ detailObj.getCustCode() + "' and branch_code='"+detailObj.getBranchCode()+"'");
					database.insertWithOnConflict("customer_branch_relation",
							null, cv, SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("customer_branch_relation:", "Data Updated");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("customer_branch_relation", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToRoutePlanMaster(ArrayList<RoutePlanMasterDetails> routeList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < routeList.size(); ii++) {
				RoutePlanMasterDetails detailObj = routeList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("route_plan_trans_id", detailObj.getTranId());
				cv.put("emp_code", detailObj.getEmpCode());
				cv.put("route_code", detailObj.getRoutecode());
				cv.put("visit_date", detailObj.getVisitDate());
				cv.put("create_date", detailObj.getCreateDate());
				cv.put("route_name", detailObj.getRouteName());
				cv.put("flag", 1);
				cv.put("previous_route_code",detailObj.getPrevious_route_code());
				cv.put("previous_route_name",detailObj.getPrevious_route_name());
				cv.put("remarks", detailObj.getRemarks());
				cv.put("distributor_code", detailObj.getDistributorCode());
				cv.put("status", detailObj.getStatus());
				synchronized (Lock)
				{

					//deleting duplicate data while inserting in
					database.execSQL("DELETE FROM route_plan_transaction WHERE route_plan_trans_id='"
							+ detailObj.getTranId() + "' AND route_code='"+detailObj.getRoutecode()+"' AND status='"+detailObj.getStatus()+
							"' AND visit_date='"+detailObj.getVisitDate()+"' AND create_date='"+detailObj.getCreateDate()+"'" );
					database.insertWithOnConflict("route_plan_transaction",	null, cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Route_plan_transaction", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToRoutePlanAccessTable(String startDate, String endDate,String period) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("access_start_date", startDate);
			cv.put("access_end_date", endDate);
			cv.put("period", period);
			synchronized (Lock) {
				database.insertWithOnConflict("route_plan_access_period", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				print_log_d("Route_plan_access_period:", "Data Inserted");
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Route_plan_access_period", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToTravelExpCatMaster(ArrayList<TravelExpCategory> catList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < catList.size(); ii++) {
				TravelExpCategory detailObj = catList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("transport_mode_cat_id", detailObj.getCategoryId());
				cv.put("transport_mode_cat_name", detailObj.getCategoryName());
				synchronized (Lock) {
					database.insertWithOnConflict("transport_mode_category",
							null, cv, SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Transport_mode_category:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Transport_mode_category", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToTravelExpSubCatMaster(
			ArrayList<TravelExpSubCategory> catList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < catList.size(); ii++) {
				TravelExpSubCategory detailObj = catList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("transport_mode_sub_cat_id", detailObj.getSubCatId());
				cv.put("transport_mode_sub_cat_name", detailObj.getSubCatName());
				cv.put("transport_mode_cat_id", detailObj.getCategoryId());
				synchronized (Lock) {
					database.insertWithOnConflict(
							"transport_mode_sub_category", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Transport_mode_sub_category:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Transport_mode_sub_category", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToCardHolderMaster(
			ArrayList<LoyaltyCustomerDetails> freightCostList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < freightCostList.size(); ii++) {
				LoyaltyCustomerDetails detailObj = freightCostList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("loyalty_card_holder_code",
						detailObj.getCardHolderCode());
				cv.put("loyalty_card_holder_name",
						detailObj.getCardHolderName());
				cv.put("loyalty_card_no", detailObj.getCardNumber());
				cv.put("card_type", detailObj.getCardType());
				cv.put("total_purchase_value", detailObj.getPurchaseValue());
				cv.put("total_reward_point", detailObj.getRewardPoint());
				cv.put("last_update_on", detailObj.getLastUpdate());
				cv.put("redeemed", detailObj.getRedeemed());
				cv.put("phone_no", detailObj.getPhone());
				cv.put("address", detailObj.getAddress());
				cv.put("vehicle_no", detailObj.getVehicleNo());
				cv.put("expiry_date", detailObj.getCardExpDate());
				if (Constants.isFirstLoginOfApp	|| Constants.isLoyaltyTableUpdated)
				{
					synchronized (Lock)
					{
						database.insertWithOnConflict("loyalty_card_holder_master", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
					}
				}
				else
				{
					database.execSQL("DELETE FROM loyalty_card_holder_master WHERE loyalty_card_holder_code='"
							+ detailObj.getCardHolderCode() + "'");
					database.insertWithOnConflict("loyalty_card_holder_master",
							null, cv, SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d(" Loyalty_card_holder_master:", "Data Inserted");
				}

			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Loyalty_card_holder_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToSchemeDetails(ArrayList<SchemeDetails> schemeList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < schemeList.size(); ii++) {
				SchemeDetails detailObj = schemeList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("vertical_name", detailObj.getVerticalName());
				cv.put("scheme_value", detailObj.getSchemeValue());
				synchronized (Lock) {
					database.insertWithOnConflict("scheme_details", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d(" Scheme_details:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Scheme_details", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToRedeemeDetails(ArrayList<RedeemeDetails> redeemeList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < redeemeList.size(); ii++) {
				RedeemeDetails detailObj = redeemeList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("scheme_expiry_date", detailObj.getExpDate());
				cv.put("points", detailObj.getPoints());
				cv.put("award", detailObj.getAward());
				synchronized (Lock) {
					database.insertWithOnConflict("redeeme_details", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d(" redeem_details:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("redeem_details", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToLoyaltyPurchaseDetails(ArrayList<LoyaltyPurchaseDetails> purchaseList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try
		{
			for (ii = 0; ii < purchaseList.size(); ii++)
			{
				LoyaltyPurchaseDetails detailObj = purchaseList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("loyalty_card_no", detailObj.getLoyaltyCardNo());
				cv.put("vertical_name", detailObj.getVerticalName());
				cv.put("purchase_value", detailObj.getPurchaseValue());
				cv.put("accumulated_points", detailObj.getRwrdPoint());
				cv.put("redeemed_points", detailObj.getRdmdPoint());

				if (Constants.isFirstLoginOfApp || Constants.isLoyaltyPurchaseUpdated) synchronized (Lock)
				{
					database.insertWithOnConflict("loyalty_purchase_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
				else
				{
					database.execSQL("DELETE FROM loyalty_purchase_details WHERE loyalty_card_no='" + detailObj.getLoyaltyCardNo() + "'");
					database.insertWithOnConflict("loyalty_purchase_details", null, cv, SQLiteDatabase.CONFLICT_IGNORE); print_log_d("LoyaltyPurchaseDetails:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		}
		catch (SQLException e)
		{
			print_log_d("LoyaltyPurchaseDetails", e.getMessage());
		}
		finally
		{
			database.endTransaction();
		}
		return status;
	}

	public long insertToPrevStockCountingMaster(
			ArrayList<PrevStockCountingDetails> stockList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < stockList.size(); ii++) {
				PrevStockCountingDetails detailObj = stockList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("customer_code", detailObj.getCustomerCode());
				cv.put("product_code", detailObj.getProductCode());
				cv.put("visit_details", detailObj.getVisitDetails());
				if (Constants.isFirstLoginOfApp
						|| Constants.isPrevStockCountingUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict(
								"prev_stock_counting_master", null, cv,
								SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("Prev_stock_counting_master:", "Data Inserted");
					}
				} else {
					String query = "DELETE FROM prev_stock_counting_master WHERE product_code='"
							+ detailObj.getProductCode().replace("'", "")
							+ "' and customer_code='"
							+ detailObj.getCustomerCode().replace("'", "")
							+ "'";
					database.execSQL(query);
					database.insertWithOnConflict("prev_stock_counting_master",
							null, cv, SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Prev_stock_counting_master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Product_sub_group_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToProductQtyCustClassWiseTDMaster(ArrayList<ProdQtyCustClassWiseTDDetails> dataList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < dataList.size(); ii++)
			{
				ProdQtyCustClassWiseTDDetails detailObj = dataList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("branch_code", detailObj.getBranchCode());
				cv.put("prod_code", detailObj.getProductCode());
				cv.put("qty_slab", detailObj.getQtySlab());
				cv.put("TD_percent", detailObj.getTDPercent());
				cv.put("cust_class", detailObj. getCustClass());
				cv.put("acedns ", detailObj. getAcedns());
				if (Constants.isFirstLoginOfApp)
				{
					synchronized (Lock)
					{
						database.insertWithOnConflict("prodqty_custclass_wise_TD", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
					}
				}
				else
				{
					String query = "DELETE FROM prodqty_custclass_wise_TD WHERE branch_code='"
							+ detailObj.getBranchCode()+ "' AND prod_code='"
							+ detailObj.getProductCode()+ "' AND qty_slab='"+detailObj.getQtySlab()
							+"' AND cust_class='"+detailObj. getCustClass()+"'";

					database.execSQL(query);
					database.insertWithOnConflict("prodqty_custclass_wise_TD",
							null, cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		}
		catch (SQLException e)
		{

		}
		finally
		{
			database.endTransaction();
		}
		return status;
	}

	public ArrayList<ProdQtyCustClassWiseTDDetails> getProductQtyCustClassWiseTDForGivenProductCodeBranchCode(String productCode,String BranchCode,String customerClass)
	{
		ArrayList<ProdQtyCustClassWiseTDDetails> ProdQtyCustClassWiseTDDetailsList=new ArrayList<>();
		Cursor cursor=null;
		try
		{
			String query="Select qty_slab, TD_percent from prodqty_custclass_wise_TD Where branch_code='"+BranchCode+"' AND prod_code='"+productCode+"' AND cust_class='"+customerClass+"' AND acedns= 'Y'";
			cursor = database.rawQuery(query, null);
			if (cursor!=null && cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					ProdQtyCustClassWiseTDDetails temp=new ProdQtyCustClassWiseTDDetails();
					temp.setQtySlab(cursor.getString(0));
					temp.setTDPercent(cursor.getString(1));
					ProdQtyCustClassWiseTDDetailsList.add(temp);
					cursor.moveToNext();
				}

			}

			cursor.close();
		}
		catch (Exception e)
		{

		}
		finally
		{
			cursor.close();
		}
		return ProdQtyCustClassWiseTDDetailsList;
	}

	public void deleteSaudaAllocation() {
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM sauda_allocation");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_Log_d("Exception:::::::::::" + e);
		} finally {
			database.endTransaction();
		}
	}

	//this is a common process for both sauda and td allocation access table as both the tables have exact same columns
	public long InsertToSaudaOrTDAllocationAccess(ArrayList<SaudaOrTDAllocationAccess> saudaAllocationAccessList,String tableName)
	{
		TruncateTableByTableName(tableName);
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < saudaAllocationAccessList.size(); ii++) {
				SaudaOrTDAllocationAccess obj = saudaAllocationAccessList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("emp_code", obj.getEmployeeCode());
				cv.put("designation", obj.getDesignation());
				cv.put("do_allocation", obj.getDoAllocation());
				cv.put("get_allocation", obj.getGetAllocation());
				synchronized (Lock)
				{
					database.insertWithOnConflict(tableName, null, cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {

		} finally {
			database.endTransaction();
		}
		return status;
	}


	public long insertToSaudaAllocation(ArrayList<SaudaAllocationDetails> saudaList) {
		TruncateTableByTableName("sauda_allocation");
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < saudaList.size(); ii++) {
				SaudaAllocationDetails detailObj = saudaList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("emp_code", detailObj.getEmpCode());
				cv.put("product_filter_code", detailObj.getProdFilterCode());
				cv.put("qty", detailObj.getQty());
				cv.put("allot_qty", detailObj.getAllotedQty());
				cv.put("BAL", detailObj.getBal());
				synchronized (Lock) {
					database.insertWithOnConflict("sauda_allocation", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Sauda_allocation:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Sauda_allocation", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToTDAllocation(ArrayList<SaudaAllocationDetails> saudaList) {
		TruncateTableByTableName("TD_allocation");
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < saudaList.size(); ii++) {
				SaudaAllocationDetails detailObj = saudaList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("emp_code", detailObj.getEmpCode());
				cv.put("product_filter_code", detailObj.getProdFilterCode());
				cv.put("TD", detailObj.getAllotedTD());
				synchronized (Lock) {
					database.insertWithOnConflict("TD_allocation", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Sauda_allocation", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToRDSMaster(ArrayList<RDSDetails> rdsList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < rdsList.size(); ii++) {
				RDSDetails detailObj = rdsList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("rds_code", detailObj.getRdsCode());
				cv.put("rds_name", detailObj.getRdsName());
				cv.put("emp_code", detailObj.getemp_code());
				cv.put("rds_type", detailObj.getRdsType());
//				cv.put("rds_nick_name", detailObj.getrds_nick_name());
//				cv.put("rds_address", detailObj.getrds_address());
//				cv.put("rds_pin_code", detailObj.getrds_pin_code());
//				cv.put("rds_tin_no", detailObj.getrds_tin_no());
//				cv.put("rds_cst_no", detailObj.getrds_cst_no());

				if (Constants.isFirstLoginOfApp || Constants.isRDSTableUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict("rds_master", null, cv,
								SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("Rds_master:", "Data Inserted");
					}
				} else {
					String query = "DELETE FROM rds_master WHERE rds_code='"
							+ detailObj.getRdsCode().replace("'", "") + "'";
					database.execSQL(query);
					database.insertWithOnConflict("rds_master", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Rds_master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Rds_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToCrdTransaction(
			ArrayList<CardTransactionDetails> transList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < transList.size(); ii++) {
				CardTransactionDetails detailObj = transList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("transaction_id", detailObj.getTransactionId());
				cv.put("loyalty_card_no", detailObj.getCardNumber());
				cv.put("rds_code", detailObj.getRdsCode());
				cv.put("purchase_value", detailObj.getPurchaseValue());
				cv.put("trans_type", detailObj.getVerticalName());
				cv.put("vehicle_no", detailObj.getVehicleNo());
				cv.put("vehicle_type", detailObj.getVehicleType());
				cv.put("points_earned", detailObj.getPointsEarned());
				cv.put("points_redeemed", detailObj.getPointsRedeemed());
				cv.put("flag", detailObj.getFlag());
				synchronized (Lock) {
					database.insertWithOnConflict("card_transaction", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("card_transaction:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("card_transaction", e.getMessage());
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
//			cursor.close();
			print_Log_d("Exception:::::" + e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
	}


	public long InsertToSurveyCategoryMaster(ArrayList<SurveyCategoryMaster> mSurveyCategoryMasterList) {

		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < mSurveyCategoryMasterList.size(); ii++) {
				SurveyCategoryMaster detailObj = mSurveyCategoryMasterList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("id_row", detailObj.getRowId());
				cv.put("cat_id", detailObj.getCategoryId());
				cv.put("sub_cat_id", detailObj.getSubCategoryId());
				cv.put("cat_name", detailObj.getCategoryName());
				cv.put("sub_cat_name", detailObj.getSubCategoryName());

				synchronized (Lock) {
					database.insertWithOnConflict("survey_category_master", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Survey_Category_Master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Survey_Category_Master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public int GetSurveyMasterTableCategoryDetailsCase6(String tablename,String columnname,String mCustomerSelectionBasisFilter){
		int Max=0;
		Cursor cursor=null;
		if(database.isOpen()){
			print_log_d("", "OPEN");
		}else{
			print_log_d("", "Close");
		}
		try {
			String query = "SELECT DISTINCT "+columnname+" FROM "+tablename +mCustomerSelectionBasisFilter+" ORDER BY "+columnname+" ASC" ;
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				Max=cursor.getCount();
				Constants.mSurveyLayoutList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					Constants.mSurveyLayoutList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}

		return Max;
	}

	public int GetSurveyMasterTableCategoryDetailsClause(String tablename,String columnname,String clause){
		int Max=0;
		Cursor cursor=null;
		try {
			String query = "SELECT DISTINCT "+columnname+" FROM "+tablename +" WHERE "+clause+" ORDER BY "+columnname+" ASC" ;
			print_log_d("Query", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				Max=cursor.getCount();
				Constants.mSurveyLayoutList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					Constants.mSurveyLayoutList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}

		return Max;
	}

	public ArrayList<KeyValue> GetSurveyMasterTableCategoryDetailsCase7(String tablename,String sendcolumnname,String showcolumnname,String mCustomerSelectionBasis,String mCustomerSelectionBasisFilter)
	{
		ArrayList<KeyValue> KeyValueList=new ArrayList<KeyValue>();
		Cursor cursor=null;
		try
		{
			String query ="";
			if(mCustomerSelectionBasis.matches("routeplan"))
			{
				String today = dateString.substring(6, 8) + "-"
						+ dateString.substring(4, 6) + "-"
						+ dateString.substring(0, 4);
				if(tablename.matches("customer_master") && Constants.surveyFormDetailsObj.getCustomerEmailUpdate().equalsIgnoreCase("yes"))
				{
					Constants.shouldUpdateCustomerMasterWithEmail=true;
					query = "SELECT DISTINCT "+sendcolumnname+","+showcolumnname+" FROM "+tablename + " WHERE route_code IN(SELECT route_code FROM route_plan_transaction WHERE visit_date LIKE '%"+today+"%') AND (email IS NULL OR email=' ') "+mCustomerSelectionBasisFilter+"ORDER BY "+sendcolumnname+" ASC" ;
				}
				else
				{
					Constants.shouldUpdateCustomerMasterWithEmail=false;
					query = "SELECT DISTINCT "+sendcolumnname+","+showcolumnname+" FROM "+tablename + " WHERE route_code IN(SELECT route_code FROM route_plan_transaction WHERE visit_date LIKE '%"+today+"%')"+mCustomerSelectionBasisFilter+" ORDER BY "+sendcolumnname+" ASC" ;
				}
			}
			else
			{
				Constants.shouldUpdateCustomerMasterWithEmail=false;
				query = "SELECT DISTINCT "+sendcolumnname+","+showcolumnname+" FROM "+tablename +mCustomerSelectionBasisFilter+" ORDER BY "+sendcolumnname+" ASC" ;

			}

			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					KeyValue obj=new KeyValue();
					obj.setKey(cursor.getString(0));
					obj.setValue(cursor.getString(1));
					KeyValueList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return KeyValueList;
	}

	public ArrayList<KeyValue> GetMasterTableDetailsRelationalView(String query)
	{
		ArrayList<KeyValue> KeyValueList=new ArrayList<KeyValue>();
		Cursor cursor=null;
		try
		{
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					KeyValue obj=new KeyValue();
					obj.setKey(cursor.getString(0));
					obj.setValue(cursor.getString(1));
					KeyValueList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return KeyValueList;
	}

	public ArrayList<KeyValue> GetSurveyMasterTableCategoryDetailsCondition(String tablename,String sendcolumnname,String showcolumnname,String clause){
		ArrayList<KeyValue> KeyValueList=new ArrayList<KeyValue>();
		Cursor cursor=null;
		try {
			String query = "SELECT DISTINCT "+sendcolumnname+","+showcolumnname+" FROM "+tablename+" WHERE "+clause+" ORDER BY "+sendcolumnname+" ASC" ;
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					KeyValue obj=new KeyValue();
					obj.setKey(cursor.getString(0));
					obj.setValue(cursor.getString(1));
					KeyValueList.add(obj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return KeyValueList;
	}


	public int Get_Survey_Master_Table_Category_Details(String tablename,String columnname){
		int Max=0;
		Cursor cursor=null;
		try {
			String query = "SELECT DISTINCT "+columnname+" FROM "+tablename +" ORDER BY id_row" ;
			print_log_d("Query", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				Max=cursor.getCount();
				Constants.mSurveyLayoutList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					Constants.mSurveyLayoutList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}

		return Max;
	}

	public int Get_Survey_Master_Table_Category_Details(String tablename,String columnname,String mShowColumn)
	{
		int Max=0;
		Cursor cursor=null;
		try
		{
			if(isShowValueSendValueDifferentForChcekBOx)
			{
				sendColumnDataForSurveyCheckBox=new ArrayList<>();
				String query = "SELECT DISTINCT "+mShowColumn+", "+columnname+" FROM "+tablename +" ORDER BY id_row" ;
				cursor = database.rawQuery(query, null);
				if (cursor.getCount() > 0)
				{
					Max=cursor.getCount();
					Constants.mSurveyLayoutList= new String[Max];
					cursor.moveToFirst();
					for (int ii = 0; ii < cursor.getCount(); ii++)
					{
						Constants.mSurveyLayoutList[ii]=cursor.getString(0);
						sendColumnDataForSurveyCheckBox.add(cursor.getString(1));
						cursor.moveToNext();
					}
					cursor.close();
				}
			}
			else
			{
				String query = "SELECT DISTINCT "+columnname+" FROM "+tablename +" ORDER BY id_row" ;
				cursor = database.rawQuery(query, null);
				if (cursor.getCount() > 0)
				{
					Max=cursor.getCount();
					Constants.mSurveyLayoutList= new String[Max];
					cursor.moveToFirst();
					for (int ii = 0; ii < cursor.getCount(); ii++)
					{
						Constants.mSurveyLayoutList[ii]=cursor.getString(0);
						cursor.moveToNext();
					}
					cursor.close();
				}
			}


		}
		catch (Exception e)
		{
			cursor.close();
			print_Log_d(e.getMessage());
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}

		return Max;
	}


	public String getRouteNameFromRouteCode(String routeCode){
		String routeName="";
		Cursor cursor=null;
		try
		{
			String query = "SELECT route_name from route_master where route_code='"+routeCode+"'" ;
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				routeName=cursor.getString(0);
				cursor.close();
			}
		}
		catch (Exception e)
		{
			cursor.close();
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return routeName;
	}

	public int Get_Survey_Master_Table_SubCategory_Details(String tablename,String columnname,String mShowColumn,String dependent,String condition)
	{
		int Max=0;
		Cursor cursor=null;
		try
		{
			if(isShowValueSendValueDifferentForChcekBOx)
			{
				sendColumnDataForSurveyCheckBox=new ArrayList<>();
				//String query = "SELECT DISTINCT sub_cat_name  FROM survey_category_master where cat_name='"+condition +"'";
				String query="Select "+mShowColumn+", "+columnname+" FROM "+tablename+" WHERE "+dependent+" IN("+condition.replace(" ","")+") and "+columnname+" <>' '";
				cursor = database.rawQuery(query, null);
				if (cursor.getCount() > 0)
				{
					Max=cursor.getCount();
					Constants.mSurveyLayoutList= new String[Max];
					cursor.moveToFirst();
					for (int ii = 0; ii < cursor.getCount(); ii++)
					{
						if(cursor.getString(0).trim().length()>0 && cursor.getString(0)!=null)
						{
							Constants.mSurveyLayoutList[ii]=cursor.getString(0);
							sendColumnDataForSurveyCheckBox.add(cursor.getString(1));
						}
						cursor.moveToNext();
					}
					cursor.close();
				}
				else
				{
					Max=0;
					Constants.mSurveyLayoutList= new String[Max];
				}
			}
			else
			{
				//String query = "SELECT DISTINCT sub_cat_name  FROM survey_category_master where cat_name='"+condition +"'";
				String query="Select "+columnname+" FROM "+tablename+" WHERE "+dependent+" IN("+condition+") and "+columnname+" <>' '";
				cursor = database.rawQuery(query, null);
				if (cursor.getCount() > 0)
				{
					Max=cursor.getCount();
					Constants.mSurveyLayoutList= new String[Max];
					cursor.moveToFirst();
					for (int ii = 0; ii < cursor.getCount(); ii++)
					{
						if(cursor.getString(0).trim().length()>0 &&
								cursor.getString(0)!=null)
						{
							Constants.mSurveyLayoutList[ii]=cursor.getString(0);
						}
						cursor.moveToNext();
					}
					cursor.close();
				}
				else
				{
					Max=0;
					Constants.mSurveyLayoutList= new String[Max];
				}
			}

		}
		catch (Exception e)
		{
			cursor.close();
			print_Log_d(e.getMessage());
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return Max;
	}

	public int Get_Survey_Master_Table_SubCategory_Details(String tablename,String columnname,String dependent,String condition){
		int Max=0;
		Cursor cursor=null;
		try
		{
			//String query = "SELECT DISTINCT sub_cat_name  FROM survey_category_master where cat_name='"+condition +"'";
			String query="Select "+columnname+" FROM "+tablename+" WHERE "+dependent+" IN("+condition+") and "+columnname+" <>' '";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				Max=cursor.getCount();
				Constants.mSurveyLayoutList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					if(cursor.getString(0).trim().length()>0 &&
							cursor.getString(0)!=null)
					{
						Constants.mSurveyLayoutList[ii]=cursor.getString(0);
					}
					cursor.moveToNext();
				}
				cursor.close();
			}
			else
			{
				Max=0;
				Constants.mSurveyLayoutList= new String[Max];
			}
		}
		catch (Exception e)
		{
			cursor.close();
			print_Log_d(e.getMessage());
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return Max;
	}

	public int GetHierarchywiseEmployee(String empcode)
	{
		int Max=0;
		Cursor cursor=null;
		try {
			String query = "SELECT emp_code, emp_name FROM emp_master WHERE reporting_to='"+empcode+"'";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				Max=cursor.getCount();
				Constants.mEmployeeList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					Constants.mEmployeeList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return Max;
	}


	public int GetSurveyTableDetails(String tablename,String dependantConditionSqlQueryForRadioType)
	{
		int Max=0;
		Cursor cursor=null;
		try {
			String query = "SELECT *  FROM "+tablename +"";
			if(dependantConditionSqlQueryForRadioType.length()>0)
			{
				query=dependantConditionSqlQueryForRadioType;
			}
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				Max=cursor.getCount();
				Constants.mSurveyLayoutList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					Constants.mSurveyLayoutList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return Max;
	}

	public void DeleteSurveyTempOutData(String layoutname){
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM survey_output_temp where layout_name='"+layoutname+"'");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_log_d("Delete survey_output_temp","Exception:::::::::::" + e);
		} finally {
			database.endTransaction();
		}
	}

	public void DeleteSurveyTempOutData(){
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM survey_output_temp");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_log_d("Delete survey_output_temp","Exception:::::::::::" + e);
		} finally {
			database.endTransaction();
		}
	}

	public boolean INSERTToSurveyTempOutData(ArrayList<SurveyDetails> surveyDetailsList,String layoutname) {
		boolean isstatus = false;
		int count=0;
		database.beginTransaction();
		try {
			for (count = 0; count < surveyDetailsList.size(); count++) {
				SurveyDetails surveydetailsobj = surveyDetailsList.get(count);
				ContentValues cv = new ContentValues();
				cv.put("row_id", surveydetailsobj.getRowId());
				cv.put("value", surveydetailsobj.getValue());
				cv.put("layout_name", layoutname);
				synchronized (Lock) {
					database.insertWithOnConflict("survey_output_temp", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("survey_output_temp:", "Data Inserted");
				}
			}
			database.setTransactionSuccessful();
			isstatus = true;
		} catch (SQLException e) {
			print_log_d("INSERTION Error:survey_output_temp :", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return isstatus;
	}

	public ArrayList<SurveyDetails> GetSurveyTempOutput(String layoutname){
		ArrayList<SurveyDetails> mSurveyTempOutputList =new ArrayList<SurveyDetails>();
		Cursor cursor=null;
		try {
			String query = "SELECT row_id,value FROM survey_output_temp where layout_name='"+layoutname+"' ";
			print_log_d("Query", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyDetails temp = new SurveyDetails();
					temp.setRowId(cursor.getString(0));
					temp.setValue(cursor.getString(1));
					mSurveyTempOutputList.add(temp);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}

		return mSurveyTempOutputList;
	}

	public ArrayList<SurveyInput> GetSurveyInputLayoutWise(String layoutname,String menuid){
		ArrayList<SurveyInput> mSurveyInputList =new ArrayList<SurveyInput>();
		Cursor cursor=null;
		try {
			String query = "SELECT * FROM survey_input WHERE layout_name='"+layoutname+"' AND menu_id='"+menuid+"' AND acedns='Y' ORDER BY display_order ASC";
			print_log_d("Query", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyInput temp = new SurveyInput();
					temp.setSurveyRowId(cursor.getString(0));
					temp.setSurveyActionId(cursor.getString(1));
					temp.setSurveyMenuId(cursor.getString(2));
					temp.setSurveyLayoutName(cursor.getString(3));
					temp.setSurveyDisplayName(cursor.getString(4));
					temp.setSurveyType(cursor.getString(5));
					temp.setSurveyTableName(cursor.getString(6));
					temp.setSurveyMadatory(cursor.getString(7));
					temp.setSurveyAction(cursor.getString(8));
					temp.setSurveyValidation(cursor.getString(9));
					temp.setSurveyDisplayOrder(cursor.getString(10));

					mSurveyInputList.add(temp);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}

		return mSurveyInputList;
	}

	public ArrayList<SurveyInput> GetOffersSurveyInputLayoutWise(String mType)
	{
		ArrayList<SurveyInput> mSurveyInputList =new ArrayList<SurveyInput>();
		Cursor cursor=null;
		try {
			String query = "SELECT * FROM survey_input WHERE layout_name= 'Offers' AND survey_type='"+mType+"' AND type!='layer'  ORDER BY display_order ASC";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyInput temp = new SurveyInput();
					temp.setSurveyRowId(cursor.getString(0));
					temp.setSurveyActionId(cursor.getString(1));
					temp.setSurveyMenuId(cursor.getString(2));
					temp.setSurveyLayoutName(cursor.getString(3));
					temp.setSurveyDisplayName(cursor.getString(4));
					temp.setSurveyType(cursor.getString(5));
					temp.setSurveyTableName(cursor.getString(6));
					temp.setSurveyMadatory(cursor.getString(7));
					temp.setSurveyAction(cursor.getString(8));
					temp.setSurveyValidation(cursor.getString(9));
					temp.setSurveyDisplayOrder(cursor.getString(10));

					mSurveyInputList.add(temp);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}

		return mSurveyInputList;
	}

	public ArrayList<SurveyTableView> GetSurveyTableView(){
		ArrayList<SurveyTableView> mSurveyTableViewList =new ArrayList<SurveyTableView>();
		Cursor cursor=null;
		try {
			String query = "SELECT * FROM table_view ";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyTableView temp = new SurveyTableView();
					temp.setRowId(cursor.getString(0));
					temp.setType(cursor.getString(1));
					temp.setValue(cursor.getString(2));
					temp.setDependentOn(cursor.getString(3));
					temp.setDependentValue(cursor.getString(4));
					temp.setAction(cursor.getString(5));

					mSurveyTableViewList.add(temp);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}

		return mSurveyTableViewList;
	}

	public ArrayList<SurveyInput> GetSurveyInputLayoutWise(){
		ArrayList<SurveyInput> mSurveyInputList =new ArrayList<SurveyInput>();
		Cursor cursor=null;
		try {
			String query = "SELECT * FROM survey_input ORDER BY display_order ASC";
			print_log_d("Query", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyInput temp = new SurveyInput();
					temp.setSurveyRowId(cursor.getString(0));
					temp.setSurveyActionId(cursor.getString(1));
					temp.setSurveyMenuId(cursor.getString(2));
					temp.setSurveyLayoutName(cursor.getString(3));
					temp.setSurveyDisplayName(cursor.getString(4));
					temp.setSurveyType(cursor.getString(5));
					temp.setSurveyTableName(cursor.getString(6));
					temp.setSurveyMadatory(cursor.getString(7));
					temp.setSurveyAction(cursor.getString(8));
					temp.setSurveyValidation(cursor.getString(9));
					temp.setSurveyDisplayOrder(cursor.getString(10));

					mSurveyInputList.add(temp);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}

		return mSurveyInputList;
	}


	public ArrayList<SurveyInput> GetSurveyInputLayoutWise(String layoutname){
		ArrayList<SurveyInput> mSurveyInputList =new ArrayList<SurveyInput>();
		Cursor cursor=null;
		try {
			String query = "SELECT * FROM survey_input where layout_name='"+layoutname+"' ORDER BY display_order ASC";

			print_log_d("Query", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyInput temp = new SurveyInput();
					temp.setSurveyRowId(cursor.getString(0));
					temp.setSurveyActionId(cursor.getString(1));
					temp.setSurveyMenuId(cursor.getString(2));
					temp.setSurveyLayoutName(cursor.getString(3));
					temp.setSurveyDisplayName(cursor.getString(4));
					temp.setSurveyType(cursor.getString(5));
					temp.setSurveyTableName(cursor.getString(6));
					temp.setSurveyMadatory(cursor.getString(7));
					temp.setSurveyAction(cursor.getString(8));
					temp.setSurveyValidation(cursor.getString(9));
					temp.setSurveyDisplayOrder(cursor.getString(10));

					mSurveyInputList.add(temp);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return mSurveyInputList;
	}

	public ArrayList<SurveyInput> GetSurveyInputSubMenuWise(String submenu){
		ArrayList<SurveyInput> mSurveyInputList =new ArrayList<SurveyInput>();
		Cursor cursor=null;
		try {
			String query = "SELECT * FROM survey_input where survey_sub_menu='"+submenu+"' ORDER BY display_order ASC";
			print_log_d("Query", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyInput temp = new SurveyInput();
					temp.setSurveyRowId(cursor.getString(0));
					temp.setSurveyActionId(cursor.getString(1));
					temp.setSurveyMenuId(cursor.getString(2));
					temp.setSurveyLayoutName(cursor.getString(3));
					temp.setSurveyDisplayName(cursor.getString(4));
					temp.setSurveyType(cursor.getString(5));
					temp.setSurveyTableName(cursor.getString(6));
					temp.setSurveyMadatory(cursor.getString(7));
					temp.setSurveyAction(cursor.getString(8));
					temp.setSurveyValidation(cursor.getString(9));
					temp.setSurveyDisplayOrder(cursor.getString(10));

					mSurveyInputList.add(temp);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return mSurveyInputList;
	}

	public ArrayList<SurveyInput> GetSurveyInputSubMenuWise(String submenu,String menuid){
		ArrayList<SurveyInput> mSurveyInputList =new ArrayList<SurveyInput>();
		Cursor cursor=null;
		try {
			String query = "SELECT * FROM survey_input where menu_id='"+menuid+"' AND survey_sub_menu='"+submenu+"' AND acedns='Y' ORDER BY display_order ASC";
			print_log_d("Query", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyInput temp = new SurveyInput();
					temp.setSurveyRowId(cursor.getString(0));
					temp.setSurveyActionId(cursor.getString(1));
					temp.setSurveyMenuId(cursor.getString(2));
					temp.setSurveyLayoutName(cursor.getString(3));
					temp.setSurveyDisplayName(cursor.getString(4));
					temp.setSurveyType(cursor.getString(5));
					temp.setSurveyTableName(cursor.getString(6));
					temp.setSurveyMadatory(cursor.getString(7));
					temp.setSurveyAction(cursor.getString(8));
					temp.setSurveyValidation(cursor.getString(9));
					temp.setSurveyDisplayOrder(cursor.getString(10));
					mSurveyInputList.add(temp);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Survey Input","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return mSurveyInputList;
	}

	public int GetMenuName(String surveytype){
		int Max=0;
		Constants.mSurveyMenuDetailsList= new ArrayList<SurveyMenuDetails>();
		Cursor cursor=null;
		try {
			String query = "SELECT menu_id,layout_name FROM survey_input WHERE type='menu' AND survey_type='"+surveytype+"' ORDER BY display_order ASC";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				Max=cursor.getCount();
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyMenuDetails temp= new SurveyMenuDetails();
					temp.setMenuID(cursor.getString(0));
					temp.setMenuName(cursor.getString(1));
					Constants.mSurveyMenuDetailsList.add(temp);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Survey Menu Name","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return Max;
	}

	public int GetMenuName(String surveytype,String surveysubmenu){
		int Max=0;
		Constants.mSurveyMenuDetailsList= new ArrayList<SurveyMenuDetails>();
		Cursor cursor=null;
		try {
			String query = "SELECT menu_id,layout_name FROM survey_input WHERE type='menu' AND survey_sub_menu='"+surveysubmenu+"' ORDER BY display_order ASC";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				Max=cursor.getCount();
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyMenuDetails temp= new SurveyMenuDetails();
					temp.setMenuID(cursor.getString(0));
					temp.setMenuName(cursor.getString(1));
					Constants.mSurveyMenuDetailsList.add(temp);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Survey Menu Name","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return Max;
	}

	public int GetMenuName(){
		int Max=0;
		Constants.mSurveyMenuDetailsList= new ArrayList<SurveyMenuDetails>();
		Cursor cursor=null;
		try {
			String query = "SELECT menu_id,layout_name FROM survey_input WHERE type='menu' ORDER BY display_order ASC";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				Max=cursor.getCount();
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyMenuDetails temp= new SurveyMenuDetails();
					temp.setMenuID(cursor.getString(0));
					temp.setMenuName(cursor.getString(1));
					Constants.mSurveyMenuDetailsList.add(temp);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Survey Menu","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return Max;
	}

	public int GetLayoutName(String menuid){
		int Max=0;
		Cursor cursor=null;
		try {
			String query = "SELECT layout_name FROM survey_input WHERE type='layer' and acedns='Y' and menu_id='"+ menuid +"' ORDER BY display_order ASC";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				Max=cursor.getCount();
				Constants.mSurveyLayoutList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					Constants.mSurveyLayoutList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Survey Layout Name","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return Max;
	}

	public long InsertToStreetMaster(String streetname,String pincode) {
		long status = 0;
		int ii = 1;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("street_name", streetname);
			cv.put("pin_code", pincode);
			database.insertWithOnConflict("street_master", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
			print_log_d("Street Master", "Data Inserted");
			database.setTransactionSuccessful();
			status = ii;
		} catch (SQLException e) {
			print_log_d("Street Master Insertion","Exception " + e);
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToGenericOilMaster(String oilname) {
		long status = 0;
		int ii = 1;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("oil_name", oilname);
			cv.put("competitor_name","");
			synchronized (Lock) {
				database.insertWithOnConflict("generic_oil_master", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
				print_log_d("Generic oil master:", "Data Inserted");
			}
			database.setTransactionSuccessful();
			status = ii;
		} catch (SQLException e) {
			print_log_d("Generic oil master","Exception " + e);
		} finally {
			database.endTransaction();
		}
		return status;
	}


	public int GetOilName(String name,String blanck){
		int Max=0;
		Cursor cursor=null;
		try {
			String query = "SELECT oil_name from generic_oil_master WHERE oil_name IN (SELECT product_group_name FROM product_group_master)";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				Max=cursor.getCount();
				Constants.mProductGrpouList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					Constants.mProductGrpouList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Generic Oil Name","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return Max;
	}

	public String GetCompetitorName(){
		String name="";
		Cursor cursor=null;
		try {
			String query = "SELECT DISTINCT competitor_name FROM competitor_group_master ";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					name+=cursor.getString(0);
					if(ii<cursor.getCount()){
						name+=",";
					}
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Competitor Name","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return name;
	}

	public String GetUOM(String competitorname){
		String name="";
		Cursor cursor=null;
		try {
			String query = "SELECT  UOM FROM competitor_group_master WHERE competitor_name='"+competitorname+"'";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				cursor.moveToFirst();
				name=cursor.getString(0);
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("UOM Name","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return name;
	}


	public String GetCompetitorName(String oilname){
		String name="";
		Cursor cursor=null;
		try {
			String query = "SELECT competitor_name FROM generic_oil_master WHERE oil_name='"+ oilname +"'";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				cursor.moveToFirst();
				name=cursor.getString(0);
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Competitor Name","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return name;
	}

//	public void GetOilName(String competitorname){
//		Constants.selectedFeedBackList= new ArrayList<MarketFeedback>();
//		Cursor cursor=null;
//		try {
//			String query = "SELECT oil_name,generic_oil_master FROM generic_oil_master WHERE competitor_name='"+ competitorname +"'";
//			cursor = database.rawQuery(query, null);
//			if (cursor.getCount() > 0){
//				cursor.moveToFirst();
//				for (int ii = 0; ii < cursor.getCount(); ii++) {
//					MarketFeedback temp = new MarketFeedback();
//					temp.setProductGroup(cursor.getString(0));
//					temp.setCopmpetitorName(cursor.getString(1));
//					Constants.selectedFeedBackList.add(temp);
//					cursor.moveToNext();
//				}
//				cursor.close();
//			}
//		} catch (Exception e) {
//			print_log_d("Generic Oil Master","Exception " + e);
//		} finally {
//			if(cursor !=null){
//				cursor.close();
//			}
//		}
//	}

	public int GetStreetName(String pincode){
		int Max=0;
		Cursor cursor=null;
		try {
			String query = "SELECT street_name FROM street_master WHERE pin_code='"+ pincode +"' ORDER BY street_name ASC";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				Max=cursor.getCount();
				Constants.mStreetList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					Constants.mStreetList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		}
		return Max;
	}



	public int GetOilName(){
		int Max=0;
		Cursor cursor=null;
		try {
			String query = "SELECT oil_name FROM generic_oil_master";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				Max=cursor.getCount();
				Constants.mProductGrpouList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					Constants.mProductGrpouList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return Max;
	}

	public int GetPackSize(){
		int Max=0;
		Cursor cursor=null;
		try {
			String query = "SELECT DISTINCT pack_size FROM product_master";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				Max=cursor.getCount();
				Constants.mVerticalValueList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					Constants.mVerticalValueList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return Max;
	}

	public int GetVerticalValue(){
		int Max=0;
		Cursor cursor=null;
		try
		{
			String query = "SELECT DISTINCT vertical_value FROM product_master";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				Max=cursor.getCount();
				Constants.mVerticalValueList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					Constants.mVerticalValueList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
			else
			{
				Constants.mVerticalValueList= new String[Max];
			}
		} catch (Exception e)
		{
			if(cursor!=null && !cursor.isClosed())
				cursor.close();
			print_Log_d(e.getMessage());
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return Max;
	}

	public long InsertToPinCodeMaster(String pincode) {
		long status = 0;
		int ii = 1;
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("pin_code", pincode);
			synchronized (Lock) {
				database.insertWithOnConflict("pin_code_master", null, cv,
						SQLiteDatabase.CONFLICT_IGNORE);
				print_log_d("Pin Code Master:", "Data Inserted");
			}
			database.setTransactionSuccessful();
			status = ii;
		} catch (SQLException e) {
			print_log_d("Pin Code Master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}


	public int GetPincode(){
		int Max=0;
		Cursor cursor=null;
		try {
			String query = "SELECT pin_code  FROM pin_code_master";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				Max=cursor.getCount();
				Constants.mPincodeList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					Constants.mPincodeList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return Max;
	}


	public int GetLayoutName(){
		int Max=0;
		Cursor cursor=null;
		try {
			String query = "SELECT layout_name FROM survey_input WHERE type='layer' and acedns='Y' ORDER BY display_order ASC";
			cursor = database.rawQuery(query, null);

			if (cursor.getCount() > 0)
			{
				Max=cursor.getCount();
				Constants.mSurveyLayoutList= new String[Max];
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					Constants.mSurveyLayoutList[ii]=cursor.getString(0);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return Max;
	}

	public long InsertToSurveyInput(ArrayList<SurveyInput> mSurveyInputList) {

		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < mSurveyInputList.size(); ii++) {
				SurveyInput detailObj = mSurveyInputList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("row_id", detailObj.getSurveyRowId());
				cv.put("action_id", detailObj.getSurveyActionId());
				cv.put("menu_id", detailObj.getSurveyMenuId());
				cv.put("layout_name", detailObj.getSurveyLayoutName());
				cv.put("display_name", detailObj.getSurveyDisplayName());
				cv.put("type", detailObj.getSurveyType());
				cv.put("display_table_name", detailObj.getSurveyTableName());
				cv.put("mandatory", detailObj.getSurveyMadatory());
				cv.put("action", detailObj.getSurveyAction());
				cv.put("validation", detailObj.getSurveyValidation());
				cv.put("display_order", Integer.parseInt(detailObj.getSurveyDisplayOrder()));
				cv.put("survey_type", detailObj.getSurveySurveyType());
				cv.put("survey_sub_menu", detailObj.getSurveySubMenu());
				cv.put("acedns", detailObj.getAceDns());

				synchronized (Lock) {
					database.execSQL("DELETE FROM survey_input WHERE row_id='"+ detailObj.getSurveyRowId()+ "' AND survey_sub_menu='"+detailObj.getSurveySubMenu()+"'");
					database.insertWithOnConflict("survey_input", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("survey_input:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("survey_input", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public String GetMallMasterValue(String columnname,String mallid){
		String value="";
		Cursor cursor=null;
		try {
			String query = "SELECT "+columnname+" FROM mall_master WHERE mall_id='"+mallid+"'";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				value=cursor.getString(0);
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Mall Master","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return value;
	}


	public ArrayList<SurveyPublish> GethighstreetAreaData(String pincode){
		ArrayList<SurveyPublish> surveyPublishList =new ArrayList<SurveyPublish>();
		Cursor cursor=null;
		String query="";
		try {
			query = "SELECT * FROM survey_publish WHERE mall_id IN (SELECT mall_id FROM mall_master WHERE pincode='"+pincode+"') AND row_id='RA143' AND status='NOT_DONE' GROUP BY mall_id";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyPublish obj = new SurveyPublish();
					obj.setSurveyId(cursor.getString(0));
					obj.setMallId(cursor.getString(1));
					obj.setRowId(cursor.getString(2));
					obj.setActionId(cursor.getString(3));
					obj.setValue(cursor.getString(4));
					surveyPublishList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Survey Publish","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return surveyPublishList;
	}

	public ArrayList<FsSurveyPublish> GetFsSurveyPublishData(String type,String mallid,String pincode){
		ArrayList<FsSurveyPublish> fsSurveyPublishList =new ArrayList<FsSurveyPublish>();
		Cursor cursor=null;
		try {
			String query="";
			if(type.equalsIgnoreCase("mall")){
				query= "SELECT * FROM fs_survey_publish WHERE mall_id='"+mallid+"' AND type='"+type+"' AND DCE_status='NOT DONE'";
			}else{
				query= "SELECT * FROM fs_survey_publish WHERE mall_id='"+mallid+"' AND pincode='"+pincode+"' AND type='"+type+"' AND DCE_status='NOT DONE'";
			}
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					FsSurveyPublish obj = new FsSurveyPublish();
					obj.setFsSurveyId(cursor.getString(0));
					obj.setMallId(cursor.getString(1));
					obj.setMallName(cursor.getString(2));
					obj.setPincode(cursor.getString(3));
					obj.setBusinessName(cursor.getString(4));
					obj.setType(cursor.getString(5));
					obj.setDceStatus(cursor.getString(6));
					fsSurveyPublishList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("FS Survey Publish","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return fsSurveyPublishList;
	}

//	public ArrayList<SurveyDetails> GetPredefinedData(String routecode,String ihbname){
//		ArrayList<SurveyDetails> surveyDetailsList =new ArrayList<SurveyDetails>();
//		Cursor cursor=null;
//		String query="";
//		try {
//			query = "SELECT row_id,value FROM survey_output  WHERE survey_id IN(SELECT SH.survey_id from survey_header SH,survey_output SO WHERE SH.survey_id=SO.survey_id AND SH.route_code='"+routecode+"' AND SO.value='"+ihbname+"')";
//			cursor = database.rawQuery(query, null);
//			if (cursor.getCount() > 0) {
//				cursor.moveToFirst();
//				for (int ii = 0; ii < cursor.getCount(); ii++) {
//					SurveyDetails obj = new SurveyDetails();
//					obj.setRowId(cursor.getString(0));
//					obj.setValue(cursor.getString(1));
//					surveyDetailsList.add(obj);
//					obj=null;
//					cursor.moveToNext();
//				}
//				cursor.close();
//			}
//		} catch (Exception e) {
//			print_log_d("Survey Publish","Exception " + e);
//		} finally {
//			if(cursor !=null){
//				cursor.close();
//			}
//		}
//		return surveyDetailsList;
//	}


	public ArrayList<MallMaster> GetMallMasterData(String type,String subtype){
		ArrayList<MallMaster> mallMasterList =new ArrayList<MallMaster>();
		Cursor cursor=null;
		try {
			String query = "SELECT * FROM mall_master WHERE pincode='"+subtype+"' AND type='"+type+"' ";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					MallMaster obj = new MallMaster();
					obj.setMallId(cursor.getString(0));
					obj.setMallName(cursor.getString(1));
					obj.setAddress(cursor.getString(2));
					obj.setLandmark(cursor.getString(3));
					obj.setArea(cursor.getString(4));
					obj.setCity(cursor.getString(5));
					obj.setPincode(cursor.getString(6));
					obj.setState(cursor.getString(7));
					obj.setCountry(cursor.getString(8));
					obj.setClosedOn(cursor.getString(9));
					obj.setSTDCode(cursor.getString(10));
					obj.setUpcomingEvents(cursor.getString(11));
					obj.setType(cursor.getString(12));
					obj.setGeneralFacility(cursor.getString(13));
					obj.setStreetNumber(cursor.getString(14));
					obj.setMarket(cursor.getString(15));
					obj.setOpeningTime(cursor.getString(16));
					obj.setClosingTime(cursor.getString(17));
					obj.setRating(cursor.getString(18));
					obj.setPhoneNo(cursor.getString(19));
					obj.setFloor(cursor.getString(20));
					mallMasterList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Mall Master","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return mallMasterList;
	}


	public ArrayList<MallMaster> GetDCAMallMasterData(String type){
		ArrayList<MallMaster> mallMasterList =new ArrayList<MallMaster>();
		Cursor cursor=null;
		String query="";
		try {
			if(type.equalsIgnoreCase("mall")){
				query = "SELECT DISTINCT MM.* FROM mall_master MM, survey_publish SP WHERE SP.mall_id=MM.mall_id AND MM.type='"+type+"'";
			}else{
				query = "SELECT DISTINCT MM.* FROM mall_master MM, survey_publish SP WHERE SP.mall_id=MM.mall_id AND MM.type='"+type+"' GROUP BY MM.pincode";
			}
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					MallMaster obj = new MallMaster();
					obj.setMallId(cursor.getString(0));
					obj.setMallName(cursor.getString(1));
					obj.setAddress(cursor.getString(2));
					obj.setLandmark(cursor.getString(3));
					obj.setArea(cursor.getString(4));
					obj.setCity(cursor.getString(5));
					obj.setPincode(cursor.getString(6));
					obj.setState(cursor.getString(7));
					obj.setCountry(cursor.getString(8));
					obj.setClosedOn(cursor.getString(9));
					obj.setSTDCode(cursor.getString(10));
					obj.setUpcomingEvents(cursor.getString(11));
					obj.setType(cursor.getString(12));
					obj.setGeneralFacility(cursor.getString(13));
					obj.setStreetNumber(cursor.getString(14));
					obj.setMarket(cursor.getString(15));
					obj.setOpeningTime(cursor.getString(16));
					obj.setClosingTime(cursor.getString(17));
					obj.setRating(cursor.getString(18));
					obj.setPhoneNo(cursor.getString(19));
					obj.setFloor(cursor.getString(20));

					mallMasterList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return mallMasterList;
	}

	public ArrayList<MallMaster> GetOfferMallMasterData(String type){
		ArrayList<MallMaster> mallMasterList =new ArrayList<MallMaster>();
		Cursor cursor=null;
		String query="";
		try {
			if(type.equalsIgnoreCase("mall")){
				query = "SELECT DISTINCT MM.* FROM mall_master MM, offer_publish SP WHERE SP.mall_id=MM.mall_id AND MM.type='"+type+"'";
			}else{
				query = "SELECT DISTINCT MM.* FROM mall_master MM, offer_publish SP WHERE SP.mall_id=MM.mall_id AND MM.type='"+type+"' GROUP BY MM.pincode";
			}
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					MallMaster obj = new MallMaster();
					obj.setMallId(cursor.getString(0));
					obj.setMallName(cursor.getString(1));
					obj.setAddress(cursor.getString(2));
					obj.setLandmark(cursor.getString(3));
					obj.setArea(cursor.getString(4));
					obj.setCity(cursor.getString(5));
					obj.setPincode(cursor.getString(6));
					obj.setState(cursor.getString(7));
					obj.setCountry(cursor.getString(8));
					obj.setClosedOn(cursor.getString(9));
					obj.setSTDCode(cursor.getString(10));
					obj.setUpcomingEvents(cursor.getString(11));
					obj.setType(cursor.getString(12));
					obj.setGeneralFacility(cursor.getString(13));
					obj.setStreetNumber(cursor.getString(14));
					obj.setMarket(cursor.getString(15));
					obj.setOpeningTime(cursor.getString(16));
					obj.setClosingTime(cursor.getString(17));
					obj.setRating(cursor.getString(18));
					obj.setPhoneNo(cursor.getString(19));
					obj.setFloor(cursor.getString(20));

					mallMasterList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return mallMasterList;
	}

	public ArrayList<SurveyPublish> GetDCADrawLayoutData(String surveyId){
		ArrayList<SurveyPublish> surveyPublishList =new ArrayList<SurveyPublish>();
		Cursor cursor=null;
		String query="";
		try {
			query = "SELECT DISTINCT SP.*,SI.display_name,SI.type FROM survey_input SI, survey_publish SP WHERE SI.row_id=SP.row_id AND SP.survey_id='"+surveyId+"'";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyPublish obj = new SurveyPublish();
					obj.setSurveyId(cursor.getString(0));
					obj.setMallId(cursor.getString(1));
					obj.setRowId(cursor.getString(2));
					obj.setActionId(cursor.getString(3));
					obj.setValue(cursor.getString(4));
					obj.setStatus(cursor.getString(5));
					obj.setDisplay(cursor.getString(6));
					obj.setType(cursor.getString(7));
					surveyPublishList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return surveyPublishList;
	}

	public ArrayList<SurveyPublish> GetDCAOutletData(String type,String mallid,String value){
		ArrayList<SurveyPublish> surveyPublishList =new ArrayList<SurveyPublish>();
		Cursor cursor=null;
		String query="";
		try {
			if(type.equalsIgnoreCase("mall")){
				query = "SELECT * FROM survey_publish WHERE mall_id='"+mallid+"' AND row_id='RA002' AND SUBSTR(mall_id,1,1)='M' AND status='NOT_DONE'";
			}else{
				query = "SELECT * FROM survey_publish WHERE survey_id IN(SELECT survey_id FROM survey_publish WHERE row_id='RA143' AND mall_id='"+mallid+"' AND value='"+value+"' ) AND row_id='RA136' AND status='NOT_DONE'";
			}
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyPublish obj = new SurveyPublish();
					obj.setSurveyId(cursor.getString(0));
					obj.setMallId(cursor.getString(1));
					obj.setRowId(cursor.getString(2));
					obj.setActionId(cursor.getString(3));
					obj.setValue(cursor.getString(4));
					surveyPublishList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return surveyPublishList;
	}

	public ArrayList<SurveyPublish> GetDCAOutletDataForOffers(String type,String mallid,String value)
	{
		ArrayList<SurveyPublish> surveyPublishList =new ArrayList<SurveyPublish>();
		Cursor cursor=null;
		String query="";
		try {
			if(type.equalsIgnoreCase("mall")){
				query = "SELECT * FROM offer_publish WHERE mall_id='"+mallid+"' AND row_id='RA002' AND SUBSTR(mall_id,1,1)='M' AND survey_id NOT IN" +
						"(select Distinct survey_id from offer_transaction WHERE substr(offer_trans_id,9,8) ='"+ dateString+"')";
			}else{
				query = "SELECT * FROM offer_publish WHERE survey_id IN(SELECT survey_id FROM offer_publish WHERE row_id='RA143' AND mall_id='"+mallid+"' AND value='"+value+"' AND survey_id NOT IN(select Distinct survey_id from offer_transaction WHERE substr(offer_trans_id,9,8) ='"+ dateString+"')) AND row_id='RA136' ";
			}
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SurveyPublish obj = new SurveyPublish();
					obj.setSurveyId(cursor.getString(0));
					obj.setMallId(cursor.getString(1));
					obj.setRowId(cursor.getString(2));
					obj.setActionId(cursor.getString(3));
					obj.setValue(cursor.getString(4));
					surveyPublishList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return surveyPublishList;
	}

	public ArrayList<MallMaster> GetMallMasterData(String type){
		ArrayList<MallMaster> mallMasterList =new ArrayList<MallMaster>();
		Cursor cursor=null;
		String query="";
		try {
			if(type.equalsIgnoreCase("mall")){
				query = "SELECT * FROM mall_master WHERE type='"+type+"'";
			}else{
				query = "SELECT * FROM mall_master WHERE type='"+type+"' GROUP BY pincode";
			}
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					MallMaster obj = new MallMaster();
					obj.setMallId(cursor.getString(0));
					obj.setMallName(cursor.getString(1));
					obj.setAddress(cursor.getString(2));
					obj.setLandmark(cursor.getString(3));
					obj.setArea(cursor.getString(4));
					obj.setCity(cursor.getString(5));
					obj.setPincode(cursor.getString(6));
					obj.setState(cursor.getString(7));
					obj.setCountry(cursor.getString(8));
					obj.setClosedOn(cursor.getString(9));
					obj.setSTDCode(cursor.getString(10));
					obj.setUpcomingEvents(cursor.getString(11));
					obj.setType(cursor.getString(12));
					obj.setGeneralFacility(cursor.getString(13));
					obj.setStreetNumber(cursor.getString(14));
					obj.setMarket(cursor.getString(15));
					obj.setOpeningTime(cursor.getString(16));
					obj.setClosingTime(cursor.getString(17));
					obj.setRating(cursor.getString(18));
					obj.setPhoneNo(cursor.getString(19));
					obj.setFloor(cursor.getString(20));
					mallMasterList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return mallMasterList;
	}

	public ArrayList<MallSurveyRelation> GetMallSurveyRelation(String menuid,String type){
		ArrayList<MallSurveyRelation> mallSurveyRelationList =new ArrayList<MallSurveyRelation>();
		Cursor cursor=null;
		try {
			String query = "SELECT * FROM mall_survey_relation WHERE menu_id='"+menuid+"' AND type='"+type+"'";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0){
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					MallSurveyRelation obj = new MallSurveyRelation();
					obj.setMenuId(cursor.getString(0));
					obj.setRowId(cursor.getString(1));
					obj.setMallInfo(cursor.getString(2));
					obj.setType(cursor.getString(3));
					mallSurveyRelationList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return mallSurveyRelationList;
	}

	public ArrayList<BrokerMaster> GetBrokerMaster(){
		ArrayList<BrokerMaster> brokerMasterList =new ArrayList<BrokerMaster>();
		Cursor cursor=null;
		try {
			String query = "SELECT * FROM broker_master ";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					BrokerMaster detailsObj = new BrokerMaster();
					detailsObj.setBrokerId(cursor.getString(0));
					detailsObj.setBrokerName(cursor.getString(1));
					brokerMasterList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			cursor.close();
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}

		return brokerMasterList;
	}
	public long InsertToGenericOilMaster(ArrayList<GenericOilMaster> genericOilMasterList) {
		DeleteGenericOil();
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < genericOilMasterList.size(); ii++) {
				GenericOilMaster obj = genericOilMasterList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("oil_name", obj.getOilName());
				cv.put("competitor_name", obj.getCompetitorName());

				synchronized (Lock) {
					database.insertWithOnConflict("generic_oil_master", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Generic oil master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Generic oil master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}


	public long InsertToProspectiveCustomerMaster(ArrayList<CustomerDetails> prospectiveCustomerMasterList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < prospectiveCustomerMasterList.size(); ii++)
			{
				CustomerDetails obj = prospectiveCustomerMasterList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("emp_code", obj.getEmpCode());
				cv.put("customer_code", obj.getCustomerCode());
				cv.put("customer_name", obj.getCustomerName());
				cv.put("address", obj.getAddress());
				cv.put("pin", obj.getPin());
				cv.put("area", obj.getRouteCode());
				cv.put("phone_no", obj.getNumber());
				cv.put("cust_type", obj.getCustomerType());
				cv.put("tagged_customer_code", obj.getTaggedCustomerCode());

				synchronized (Lock)
				{
					database.insertWithOnConflict("prospective_customer_master", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		}
		catch (SQLException e)
		{

		}
		finally
		{
			database.endTransaction();
		}
		return status;
	}

	public void DeleteGenericOil() {
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM generic_oil_master");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_Log_d("Exception:::::::::::" + e);
		} finally {
			database.endTransaction();
		}
	}

	public long InsertToStreetName(ArrayList<StreetName> streetNameList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < streetNameList.size(); ii++) {
				StreetName obj = streetNameList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("street_name", obj.getStreetName());
				cv.put("pin_code", obj.getPinCode());

				if (Constants.isFirstLoginOfApp || Constants.isStreetUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict("street_master", null, cv,
								SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("street_master:", "Data Inserted");
					}
				} else {
					database.execSQL("DELETE FROM street_master WHERE street_name='"
							+ obj.getStreetName()+ "' AND pin_code='"+obj.getPinCode()+"'");
					database.insertWithOnConflict("street_master", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
				}
				print_log_d("street_master:", "Data Updated");
			}
			status = ii;
			database.setTransactionSuccessful();
			Constants.isStreetUpdated=false;
		} catch (SQLException e) {
			print_log_d("Street Master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToSaudaTransactionLog(ArrayList<SauadaTransactionLog> sauadaTransactionLogList) {

		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < sauadaTransactionLogList.size(); ii++) {
				SauadaTransactionLog obj = sauadaTransactionLogList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("branch_code", obj.getBranchCode());
				cv.put("broker_id", obj.getBrokerId());
				cv.put("emp_code", obj.getEmployeeCode());
				cv.put("sauda_date", obj.getSaudaDate());
				cv.put("sauda_no", obj.getSaudaNo());
				cv.put("customer_code", obj.getCustomerCode());
				cv.put("prod_code", obj.getProductCode());
				cv.put("qty", obj.getQuantity());
				cv.put("convert_qty_one", obj.getConvertQtyOne());
				cv.put("convert_qty_two", obj.getConvertQtyTwo());
				cv.put("sale_rate", obj.getSaleRate());
				cv.put("TD", obj.getTD());
				cv.put("premium", obj.getPremium());
				cv.put("freight_charge", obj.getFreightCharge());
				cv.put("amount", obj.getAmount());
				cv.put("plant", obj.getPlant());
				cv.put("state", obj.getState());
				cv.put("zone", obj.getZone());

				database.execSQL("DELETE FROM sauda_transaction_log WHERE sauda_no='"
						+ obj.getSaudaNo()+ "' AND prod_code='"+obj.getProductCode()+"'");
				database.insertWithOnConflict("sauda_transaction_log", null, cv,
						SQLiteDatabase.CONFLICT_IGNORE);
				print_log_d("sauda_allocation_log:", "Data Updated");
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Broker Master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToSaudaAllocationLog(ArrayList<SaudaAllocationLog> saudaAllocationLogList) {

		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < saudaAllocationLogList.size(); ii++) {
				SaudaAllocationLog obj = saudaAllocationLogList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("allocation_id", obj.getAllocationId());
				cv.put("date", obj.getDate());
				cv.put("emp_code", obj.getEmployeCode());
				cv.put("product_filter_code", obj.getProductFilterCode());
				cv.put("qty_ton", obj.getQuantityinTon());
				cv.put("qty", obj.getQuantityinLtr());
				database.execSQL("DELETE FROM sauda_allocation_log WHERE allocation_id='"+ obj.getAllocationId()+ "' AND product_filter_code='"+obj.getProductFilterCode()+"'");
				database.insertWithOnConflict("sauda_allocation_log", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
				print_log_d("sauda_allocation_log:", "Data Updated");
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Broker Master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToMallSurveyRelationMaster(ArrayList<MallSurveyRelation> mallSurveyRelationList) {
		DeleteMallSurveyRelation();
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < mallSurveyRelationList.size(); ii++) {
				MallSurveyRelation obj = mallSurveyRelationList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("menu_id", obj.getMenuId());
				cv.put("row_id", obj.getRowId());
				cv.put("mall_info", obj.getMallInfo());
				cv.put("type", obj.getType());
				obj=null;
				synchronized (Lock) {
					database.insertWithOnConflict("mall_survey_relation", null,	cv, SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("mall_master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Mall Survey Relatio", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public void DeleteMallSurveyRelation() {
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM mall_survey_relation");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_Log_d("Exception:::::::::::" + e);
		} finally {
			database.endTransaction();
		}
	}

	public long InsertToMallMaster(ArrayList<MallMaster> mallMasterList) {
		DeleteMallMaster();
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < mallMasterList.size(); ii++) {
				MallMaster obj = mallMasterList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("mall_id", obj.getMallId());
				cv.put("mall_name", obj.getMallName());
				cv.put("address", obj.getAddress());
				cv.put("landmark", obj.getLandmark());
				cv.put("area", obj.getArea());
				cv.put("city", obj.getCity());
				cv.put("pincode", obj.getPincode());
				cv.put("state", obj.getState());
				cv.put("country", obj.getCountry());
				cv.put("closed_on", obj.getClosedOn());
				cv.put("STD_code", obj.getSTDCode());
				cv.put("upcoming_event", obj.getUpcomingEvents());
				cv.put("type", obj.getType());
				cv.put("general_facility", obj.getGeneralFacility());
				cv.put("street_number", obj.getStreetNumber());
				cv.put("market", obj.getMarket());
				cv.put("opening_time", obj.getOpeningTime());
				cv.put("closing_time", obj.getClosingTime());
				cv.put("rating", obj.getRating());
				cv.put("phone_no", obj.getPhoneNo());
				cv.put("floor", obj.getFloor());
				obj=null;
				synchronized (Lock) {
					database.insertWithOnConflict("mall_master", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("mall_master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Mall Master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public void DeleteMallMaster() {
		database.beginTransaction();
		try {
			database.execSQL("DELETE FROM mall_master");
			database.setTransactionSuccessful();
		} catch (Exception e) {
			print_Log_d("Exception:::::::::::" + e);
		} finally {
			database.endTransaction();
		}
	}


	public long InsertToEmployeeTargetAcheivement(ArrayList<EmployeeTargetAcheivement> employeeTargetAcheivementList)
	{
		TruncateTableByTableName("emp_target_achievement");
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < employeeTargetAcheivementList.size(); ii++) {
				EmployeeTargetAcheivement obj = employeeTargetAcheivementList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("emp_code", obj.getEmpCode());
				cv.put("emp_name", obj.getEmpName());
				cv.put("month", obj.getMonth());
				cv.put("district", obj.getDistrict());
				cv.put("customer_code", obj.getCustomerCode());
				cv.put("customer_name", obj.getCustomerName());
				cv.put("volume_target", obj.getVolumeTarget());
				cv.put("volume_achievement", obj.getVolumeAcheivement());
				cv.put("collection_target", obj.getCollectionTarget());
				cv.put("collection_achievement", obj.getCollectionAcheivement());
				obj=null;
				synchronized (Lock) {
					database.insertWithOnConflict("emp_target_achievement", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("emp_target_achievement:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("emp_target_achievement", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToEmpDateWiseRouteAlocation(ArrayList<EmpDateWiseRouteAlloc> employeeTargetAcheivementList)
	{
		TruncateTableByTableName("emp_datewise_route_allocation");
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < employeeTargetAcheivementList.size(); ii++)
			{
				EmpDateWiseRouteAlloc obj = employeeTargetAcheivementList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("emp_code", obj.getEmpCode());
				cv.put("route_code", obj.getRouteCode());
				cv.put("allocation_date", obj.getDate());

				synchronized (Lock)
				{
					database.insertWithOnConflict("emp_datewise_route_allocation", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("emp_target_achievement", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}
	public long InsertToCustomerWiseTargetAchievement(ArrayList<SelfAppraisalDetailsCustomerWise> dataList)
	{
		long status = 0;
		int ii = 0;
		TruncateTableByTableName("self_appraisal_customer_wise");
		database.beginTransaction();
		try {
			for (ii = 0; ii < dataList.size(); ii++) {
				SelfAppraisalDetailsCustomerWise obj = dataList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("customer_code", obj.getcutomerCode());
				cv.put("customer_name", obj.getcustomerName());
				cv.put("emp_code", obj.getempCode());
				cv.put("month", obj.getmonth());
				cv.put("target", obj.gettarget());
				cv.put("achievement", obj.getachievement());
				synchronized (Lock) {
					database.insertWithOnConflict("self_appraisal_customer_wise", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {

		} finally {
			database.endTransaction();
		}
		return status;
	}
	public long InsertToBranchWiseTargetAchievement(ArrayList<SelfAppraisalDetailsBranchWise> dataList)
	{
		long status = 0;
		int ii = 0;
		TruncateTableByTableName("self_appraisal_branch_wise");
		database.beginTransaction();
		try
		{
			for (ii = 0; ii < dataList.size(); ii++)
			{
				SelfAppraisalDetailsBranchWise obj = dataList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("branch_code", obj.getbranchCode());
				cv.put("branch_name", obj.getbranchName());
				cv.put("emp_code", obj.getempCode());
				cv.put("month", obj.getmonth());
				cv.put("target", obj.gettarget());
				cv.put("achievement", obj.getachievement());
				synchronized (Lock)
				{
					database.insertWithOnConflict("self_appraisal_branch_wise", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		}
		catch (SQLException e)
		{

		}
		finally
		{
			database.endTransaction();
		}
		return status;
	}
	public long InsertToProductWiseTargetAchievement(ArrayList<SelfAppraisalDetailsProductWise> dataList)
	{
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

				cv.put("prod_code", obj.getProductCode()); //changed
				cv.put("prod_desc", obj.getProductName()); //changed
				cv.put("emp_code", obj.getempCode());
				cv.put("month", obj.getmonth());
				cv.put("target", obj.gettarget());
				cv.put("achievement", obj.getachievement());
				cv.put("prev_y_target", obj.get_prev_y_target()); //added
				cv.put("prev_y_achievement", obj.get_prev_y_achievement()); //added
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
	public long InsertToProductGroupWiseTargetAchievement(ArrayList<SelfAppraisalDetailsProductGroupWise> dataList)
	{
		TruncateTableByTableName("self_appraisal_productgroup_wise");
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try
		{
			for (ii = 0; ii < dataList.size(); ii++) {
				SelfAppraisalDetailsProductGroupWise obj = dataList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("product_group_code", obj.getproductGroupCode());
				cv.put("product_group_name", obj.getproductGroupName());
				cv.put("emp_code", obj.getempCode());
				cv.put("month", obj.getmonth());
				cv.put("target", obj.gettarget());
				cv.put("achievement", obj.getachievement());
				synchronized (Lock)
				{
					database.insertWithOnConflict("self_appraisal_productgroup_wise", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		}
		catch (SQLException e)
		{
		}
		finally
		{
			database.endTransaction();
		}
		return status;
	}

	public long InsertToYellowCardValidationMonth(ArrayList<YellowCardDateValidation> dataList)
	{
		TruncateTableByTableName("yellow_card_date_validation");
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try
		{
			for (ii = 0; ii < dataList.size(); ii++) {
				YellowCardDateValidation obj = dataList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("validation_month", obj.getvalidationMonth());
				cv.put("validation_date", obj.getvalidationDate());

				synchronized (Lock)
				{
					database.insertWithOnConflict("yellow_card_date_validation", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		}
		catch (SQLException e)
		{
		}
		finally
		{
			database.endTransaction();
		}
		return status;
	}

	public long InsertToStateDistrictTown(ArrayList<StateDistrictTown> dataList)
	{
		TruncateTableByTableName("state_district_town");
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try
		{
			for (ii = 0; ii < dataList.size(); ii++) {
				StateDistrictTown obj = dataList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("state", obj.getstate());
				cv.put("district", obj.getdistrict());
				cv.put("town", obj.gettown());
				synchronized (Lock)
				{
					database.insertWithOnConflict("state_district_town", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		}
		catch (SQLException e)
		{
		}
		finally
		{
			database.endTransaction();
		}
		return status;
	}
	public long InsertToCustomerProductWiseOrderPlan(ArrayList<CustomerProductWiseOrderPlanDetails> dataList)
	{
		long status = 0;
		int ii = 0;
		TruncateTableByTableName("customer_product_wise_orderplan");
		database.beginTransaction();
		try {
			for (ii = 0; ii < dataList.size(); ii++) {
				CustomerProductWiseOrderPlanDetails obj = dataList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("customer_code", obj.getcustomerCode());
				cv.put("product_code", obj.geteProdCode());
				cv.put("month", obj.getMonth());
				cv.put("purchase", obj.getPurchase());
				cv.put("plan", obj.getPlan());
				synchronized (Lock) {
					database.insertWithOnConflict("customer_product_wise_orderplan", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {

		} finally {
			database.endTransaction();
		}
		return status;
	}

	public boolean DataTableByTableName(String tableName)
	{
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
	public void TruncateTableByTableName(String tableName)
	{
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


	public long InsertToOrderStatusMaster(ArrayList<OrderStatus> orderStatusList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < orderStatusList.size(); ii++) {
				OrderStatus obj = orderStatusList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("order_no", obj.getOrderNo());
				cv.put("customer_code", obj.getCustomerCode());
				cv.put("product_code", obj.getProductCode());
				cv.put("order_qty",  Constants.defaultFormat.format(Double.parseDouble(obj.getOrderQuantity())));
				cv.put("delivery_qty", obj.getAlreadyDeliveredQuantity());
				cv.put("status", obj.getStatus());
				cv.put("remarks", obj.getRemarks());
				cv.put("flag", obj.getFlag());
				if (Constants.isFirstLoginOfApp || Constants.isOrderStatus) {
					synchronized (Lock) {
						database.insertWithOnConflict("order_status", null,	cv, SQLiteDatabase.CONFLICT_IGNORE);
					}
				} else {
					database.execSQL("DELETE FROM order_status WHERE "
							+"order_no='"+obj.getOrderNo()+"' AND "
							+"product_code='"+obj.getProductCode()+"'");
					database.insertWithOnConflict("order_status", null, cv,	SQLiteDatabase.CONFLICT_IGNORE);
				}
				obj=null;
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Order Status", e.getMessage());
		} finally {
			database.endTransaction();
		}
		Constants.isOrderStatus=false;
		return status;
	}


	public long InsertToPreviousOrderCountingMaster(ArrayList<PreviousOrderCounting> previousOrderCountingList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < previousOrderCountingList.size(); ii++) {
				PreviousOrderCounting obj = previousOrderCountingList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("customer_code", obj.getCustomerCode());
				cv.put("product_code", obj.getProductCode());
				cv.put("visit_details", obj.getVisitDetails());
				if (Constants.isFirstLoginOfApp || Constants.isPreviousOrderCounting) {
					synchronized (Lock) {
						database.insertWithOnConflict("prev_order_counting_master", null,
								cv, SQLiteDatabase.CONFLICT_IGNORE);
					}
				} else {
					database.execSQL("DELETE FROM prev_order_counting_master WHERE "
							+"customer_code='"+obj.getCustomerCode()+"' AND "
							+"product_code='"+obj.getProductCode()+"'");
					database.insertWithOnConflict("prev_order_counting_master", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
				}
				obj=null;
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Prev order counting master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		Constants.isPreviousOrderCounting=false;
		return status;
	}

	public long InsertToRoutePlanCustomerMaster(ArrayList<RoutePlanCustomer> routePlanCustomerList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < routePlanCustomerList.size(); ii++) {
				RoutePlanCustomer detailObj = routePlanCustomerList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("route_plan_trans_id", detailObj.getTranSactionId());
				cv.put("route_code", detailObj.getRouteCode());
				cv.put("visit_date", detailObj.getVisitDate());
				cv.put("customer_code", detailObj.getCustomerCode());
				cv.put("status", detailObj.getStatus());
				cv.put("flag", 1);
				if (Constants.isFirstLoginOfApp || Constants.isRouteCustomer) {
					synchronized (Lock) {
						database.insertWithOnConflict("route_customer_plan_transaction", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
					}
				} else {
					database.execSQL("DELETE FROM route_customer_plan_transaction WHERE route_plan_trans_id='"+ detailObj.getTranSactionId() + "' AND route_code='"+detailObj.getRouteCode()+"' AND visit_date='"+detailObj.getVisitDate()+"' AND customer_code='"+detailObj.getCustomerCode()+"'");
					database.insertWithOnConflict("route_customer_plan_transaction", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Broker Master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToBranchRouteFreight(ArrayList<BranchRouteFreight> branchRouteFreightList)
	{
		TruncateTableByTableName("branch_route_freight");
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < branchRouteFreightList.size(); ii++)
			{
				BranchRouteFreight detailObj = branchRouteFreightList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("branch_code", detailObj.getbranch_code());
				cv.put("route_code", detailObj.getroute_code());
				cv.put("freight", detailObj.getfreight());
				cv.put("acedns", detailObj.getacedns());
				cv.put("date ", detailObj.getdate());

//				if (Constants.isFirstLoginOfApp)
//				{
				synchronized (Lock)
				{
					database.insertWithOnConflict("branch_route_freight", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
//				}
//				else
//				{
//					database.execSQL("DELETE FROM branch_route_freight WHERE branch_code='"+ detailObj.getbranch_code() + "' AND route_code='"+detailObj.getroute_code()+"' AND freight='"+detailObj.getfreight()+"' AND date='"+detailObj.getdate()+"'");
//					database.insertWithOnConflict("branch_route_freight", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
//				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
		} finally {
			database.endTransaction();
		}
		return status;
	}
	public long InsertToLoadDistribution(ArrayList<LoadDistribution> branchRouteFreightList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < branchRouteFreightList.size(); ii++)
			{
				LoadDistribution detailObj = branchRouteFreightList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("prod_code", detailObj.getprod_code());
				cv.put("qty_truck_load", detailObj.getqty_truck_load());
				cv.put("download_time ", detailObj.getdownload_time());

//				if (Constants.isFirstLoginOfApp)
//				{
				synchronized (Lock)
				{
					database.insertWithOnConflict("load_distribution", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
//				}
//				else
//				{
////					database.execSQL("DELETE FROM load_distribution WHERE prod_code='"+ detailObj.getprod_code() + "' AND route_code='"+detailObj.getroute_code()+"' AND freight='"+detailObj.getfreight()+"' AND date='"+detailObj.getdate()+"'");
//					database.insertWithOnConflict("branch_route_freight", null, cv, SQLiteDatabase.CONFLICT_IGNORE);
//				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
		} finally {
			database.endTransaction();
		}
		return status;
	}


	public long InsertToTableViewMaster(ArrayList<SurveyTableView> surveyTableViewList)
	{
		TruncateTableByTableName("table_view");
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < surveyTableViewList.size(); ii++)
			{
				SurveyTableView obj = surveyTableViewList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("row_id", obj.getRowId());
				cv.put("type", obj.getType());
				cv.put("value", obj.getValue());
				cv.put("dependent_on", obj.getDependentOn());
				cv.put("dependent_value", obj.getDependentValue());
				cv.put("action", obj.getAction());
//				if (Constants.isFirstLoginOfApp || Constants.isTableView)
//				{
				synchronized (Lock) {
					database.insertWithOnConflict("table_view", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
				}
//				}
//				else
//				{
//					database.execSQL("DELETE FROM table_view WHERE row_id='"+ obj.getRowId().replace("'", "") + "'");
//					database.insertWithOnConflict("table_view", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
//					print_log_d("Table View:", "Data Updated");
//				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Table View", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}


	public long InsertToCompetitorGroupMaster(ArrayList<CompetitorGroupMaster> competitorGroupMasterList) {
		long status = 0;
		int ii = 0;
		TruncateTableByTableName("competitor_group_master");
		database.beginTransaction();
		try {
			for (ii = 0; ii < competitorGroupMasterList.size(); ii++) {
				CompetitorGroupMaster detailObj = competitorGroupMasterList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("group_name", detailObj.getGroupName());
				cv.put("competitor_name", detailObj.getCompetitorName());
				cv.put("UOM", detailObj.getUom());
				database.insertWithOnConflict("competitor_group_master", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Competitor Group Master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}


	public long InsertToBrokerMaster(ArrayList<BrokerMaster> brokerMasterList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < brokerMasterList.size(); ii++) {
				BrokerMaster detailObj = brokerMasterList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("broker_id", detailObj.getBrokerId());
				cv.put("broker_name", detailObj.getBrokerName());
				if (Constants.isFirstLoginOfApp || Constants.isBrokerUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict("broker_master", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("broker_master:", "Data Inserted");
					}
				} else {
					database.execSQL("DELETE FROM broker_master WHERE broker_id='"
							+ detailObj.getBrokerId().replace("'", "") + "'");
					database.insertWithOnConflict("broker_master", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("broker_master:", "Data Updated");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Broker Master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToGITMaster(ArrayList<GITDetails> gitList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < gitList.size(); ii++) {
				GITDetails detailObj = gitList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("grn_no", detailObj.getGrnNo());
				cv.put("despatcher_code", detailObj.getDespatcherCode());
				cv.put("prod_code", detailObj.getProdCode());
				cv.put("despatch_qty", detailObj.getDespatchQty());
				cv.put("bal_rec_qty ", detailObj.getBalancedRecceivedQty());
				cv.put("status", detailObj.getStatus());
				cv.put("order_no", detailObj.getOrderNumber());
				cv.put("trans_type", detailObj.getTransType());
				cv.put("sale_rate", detailObj.getSaleRate());
				if (Constants.isFirstLoginOfApp || Constants.isGITTableUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict("goods_in_transit", null,
								cv, SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("Goods_in_transit:", "Data Inserted");
					}
				} else {
					database.insertWithOnConflict("goods_in_transit", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Goods_in_transit:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Goods_in_transit", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToEmpMaster(ArrayList<EmployeeMasterDetails> empList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		TruncateTableByTableName("emp_master");
		try {
			for (ii = 0; ii < empList.size(); ii++) {
				EmployeeMasterDetails detailObj = empList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("emp_code", detailObj.getEmpCode());
				cv.put("emp_name", detailObj.getEmpName());
				cv.put("sale_access", detailObj.getSaleAccess());
				cv.put("reporting_to", detailObj.getReportingTo());
				cv.put("level", detailObj.getLevel());
				cv.put("designation", detailObj.getDesignation());
				cv.put("vertical_value", detailObj.getVerticalValue());
				cv.put("branch_code", detailObj.getBranchCode());
				cv.put("state", detailObj.getState());
				cv.put("zone", detailObj.getZone());
				cv.put("acedns", detailObj.getAcedns());
				cv.put("lower_leaves", detailObj.getLowerLeaves());

				if (Constants.isFirstLoginOfApp || Constants.isEmployeeUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict("emp_master", null,cv, SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("emp_master:", "Data Inserted");
					}
				} else {
					database.execSQL("DELETE FROM emp_master WHERE emp_code='"+ detailObj.getEmpCode().replace("'", "") + "'");
					database.insertWithOnConflict("emp_master", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("emp_master:", "Data Updated");
				}
				detailObj=null;
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Emp_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long InsertToCashTransferMaster(ArrayList<CashTransferReceive> dataList)
	{
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < dataList.size(); ii++) {
				CashTransferReceive detailObj = dataList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("cash_transaction_id", detailObj.getcash_trans_rcv_trans_id());
				cv.put("despatcher_code", detailObj.getdespatcher_code());
				cv.put("receiver_code", detailObj.getreceiver_code());
				cv.put("despatch_value", detailObj.getdespatch_value());
				cv.put("rec_value", detailObj.getrec_value());
				cv.put("status", detailObj.getstatus());
				cv.put("transaction_type", detailObj.gettransaction_type());
				cv.put("cash_transfer_id", detailObj.getcash_transfer_id());

				database.execSQL("DELETE FROM cash_transaction_details WHERE cash_transaction_id='"+ detailObj.getcash_trans_rcv_trans_id() + "'");
				database.insertWithOnConflict("cash_transaction_details", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Emp_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToVendorMaster(ArrayList<VendorDetails> vendorList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < vendorList.size(); ii++) {
				VendorDetails detailObj = vendorList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("vendor_code", detailObj.getVendorCode());
				cv.put("vendor_name", detailObj.getVendorName());
				synchronized (Lock) {
					database.insertWithOnConflict("vendor_master", null, cv,
							SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("Vendor_master:", "Data Inserted");
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Vendor_master", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	public long insertToMISTransaction(ArrayList<MIS_TransctionDetails> misList) {
		long status = 0;
		int ii = 0;
		database.beginTransaction();
		try {
			for (ii = 0; ii < misList.size(); ii++) {
				MIS_TransctionDetails detailObj = misList.get(ii);
				ContentValues cv = new ContentValues();
				cv.put("branch_code", detailObj.getBranchCode());
				cv.put("rds_code", detailObj.getRdsCode());
				cv.put("emp_code", detailObj.getEmpCode());
				cv.put("trans_date", detailObj.getTransDate());
				cv.put("trans_id", detailObj.getTransId());
				cv.put("customer_code", detailObj.getCustomerCode());
				cv.put("customer_name", detailObj.getCustomerName());
				cv.put("sku_code", detailObj.getSkuCode());
				cv.put("qty", detailObj.getQty());
				cv.put("sale_rate", detailObj.getSaleRate());
				cv.put("amount", detailObj.getAmount());
				cv.put("VAT", detailObj.getVAT());
				cv.put("TD", detailObj.getTD());
				cv.put("trans_type", detailObj.getTransType());
				cv.put("d_instruction", detailObj.getdInstruction());
				cv.put("group_code", detailObj.getGroupCode());

				if (Constants.isFirstLoginOfApp
						|| Constants.isMISTransactionTableUpdated) {
					synchronized (Lock) {
						database.insertWithOnConflict("mis_transaction_log",
								null, cv, SQLiteDatabase.CONFLICT_IGNORE);
						print_log_d("MIS_transaction_log:", "Data Inserted" + ii);
					}
				} else {
					// database.execSQL("DELETE FROM mis_transaction_log WHERE trans_id='"+detailObj.getTransId()+"' AND sku_code = '"+detailObj.getSkuCode()+"'");
					database.insertWithOnConflict("mis_transaction_log", null,
							cv, SQLiteDatabase.CONFLICT_IGNORE);
					print_log_d("MIS_transaction_log:", "Data Updated" + ii);
				}
			}
			status = ii;
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("MIS_transaction_log", e.getMessage());
		} finally {
			database.endTransaction();
		}
		return status;
	}

	/*
	 * ****************************************** RETRIEVING DATA FROM MASTER
	 * TABLE STARTS****************************************
	 */

	public RouteDetails getLastOrderRouteList() {
		RouteDetails obj=null;
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT RM.route_code,RM.route_name  FROM order_header OH,customer_master CM,route_master RM WHERE OH.customer_code=CM.customer_code AND CM.route_code=RM.route_code  AND SUBSTR(OH.order_no,1,1)='O' ORDER BY OH.order_no DESC LIMIT 0,1",
					new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				obj =new RouteDetails();
				obj.setRouteCode(cursor.getString(0));
				obj.setRouteName(cursor.getString(1));
				cursor.close();
				return obj;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return obj;
	}

	public ArrayList<RouteDetails> getDistributorRouteList(String distributorcode,String visitdate) {
		ArrayList<RouteDetails> detailList = new ArrayList<RouteDetails>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT RM.route_code,RM.route_name from route_master RM, distributor_route_relation DRR WHERE RM.route_code=DRR.route_code AND DRR.distributor_code='"+distributorcode+"' AND DRR.route_code NOT IN(SELECT RPT.route_code FROM route_plan_transaction RPT JOIN (SELECT route_code,visit_date,MAX(create_date) AS timestamp FROM route_plan_transaction  WHERE  visit_date LIKE '%"+visitdate+"%' AND distributor_code='"+distributorcode+"'  GROUP BY route_code, visit_date) SAT ON RPT.route_code = SAT.route_code  AND RPT.create_date = SAT.timestamp AND RPT.visit_date=SAT.visit_date  AND RPT.status='active' GROUP BY RPT.route_code,RPT.visit_date ORDER BY RPT.route_name ASC)",new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					RouteDetails detailsObj = new RouteDetails();
					detailsObj.setRouteCode(cursor.getString(0));
					detailsObj.setRouteName(cursor.getString(1));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<String> getTransactionDetailsForOrder(String orderNumber) {
		ArrayList<String> detailList = new ArrayList<>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("select cd.call_duration, COUNT(od.qty),SUM(od.qty),SUM(od.amount) FROM call_duration cd inner JOIN order_details od on cd.transaction_id = od.order_no WHERE cd.transaction_id='"+orderNumber+"'",new String[] {});
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				detailList.add(cursor.getString(0));
				detailList.add(cursor.getString(1));
				detailList.add(cursor.getString(2));
				detailList.add(cursor.getString(3));
				cursor.moveToNext();

				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<RouteDetails> getMultipleDistributorRouteList(String distributorcode,String visitdate) {
		ArrayList<RouteDetails> detailList = new ArrayList<RouteDetails>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT RM.route_code,RM.route_name,DRR.distributor_code from route_master RM, distributor_route_relation DRR WHERE RM.route_code=DRR.route_code AND DRR.distributor_code IN("+distributorcode+") AND DRR.route_code NOT IN(SELECT RPT.route_code FROM route_plan_transaction RPT JOIN (SELECT route_code,visit_date,MAX(create_date) AS timestamp FROM route_plan_transaction  WHERE  visit_date LIKE '%"+visitdate+"%' AND distributor_code IN ("+distributorcode+")  GROUP BY route_code, visit_date) SAT ON RPT.route_code = SAT.route_code  AND RPT.create_date = SAT.timestamp AND RPT.visit_date=SAT.visit_date  AND RPT.status='active' GROUP BY RPT.route_code,RPT.visit_date ORDER BY RPT.route_name ASC)",new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					RouteDetails detailsObj = new RouteDetails();
					detailsObj.setRouteCode(cursor.getString(0));
					detailsObj.setRouteName(cursor.getString(1));
					detailsObj.setDistributorCode(cursor.getString(2));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<RouteDetails> getPlanForToday(String visitDate,String distributorcode) {
		ArrayList<RouteDetails> routePlanList = new ArrayList<RouteDetails>();
		Cursor cursor=null;
		try {
			String selectQuery = "SELECT RPT.route_plan_trans_id,RPT.emp_code,RPT.route_code,RPT.visit_date,RPT.create_date,RPT.route_name,RPT.previous_route_code,RPT.previous_route_name,RPT.status  FROM route_plan_transaction RPT  JOIN (SELECT route_code,visit_date,MAX(create_date) AS timestamp FROM route_plan_transaction  WHERE  visit_date LIKE '%"+visitDate+"%' AND distributor_code='"+distributorcode+"'  GROUP BY route_code, visit_date) SAT ON RPT.route_code = SAT.route_code  AND RPT.create_date = SAT.timestamp AND RPT.visit_date=SAT.visit_date  AND RPT.status='active' GROUP BY RPT.route_code,RPT.visit_date ORDER BY RPT.route_name ASC" ;
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int i = 0; i < cursor.getCount(); i++) {
					RouteDetails routeObj = new RouteDetails();
					routeObj.setRouteCode(cursor.getString(2));
					routeObj.setRouteName(cursor.getString(5));
					routePlanList.add(routeObj);
					cursor.moveToNext();
				}
			}
			cursor.close();
		} catch (Exception e) {
			print_Log_d("Exception:::::" + e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return routePlanList;
	}



	public ArrayList<RouteDetails> getRouteList() {
		ArrayList<RouteDetails> detailList = new ArrayList<RouteDetails>();
		boolean isAttendanceGiven =getAttendanceForToday();
		String StringToRemoveLeaveRequestRoute ="";
		if(isAttendanceGiven)
		{
			StringToRemoveLeaveRequestRoute=" AND route_name NOT LIKE '%leave request%' ";
		}
		Cursor cursor=null;
		try {
			String sqlQuery = "SELECT * FROM route_master WHERE route_name IS NOT null AND route_name != ''" + StringToRemoveLeaveRequestRoute + " ORDER BY route_name ASC";
			cursor = database.rawQuery(sqlQuery,new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					RouteDetails detailsObj = new RouteDetails();
					detailsObj.setRouteCode(cursor.getString(0));
					detailsObj.setRouteName(cursor.getString(1));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<RouteDetails> getRouteListForSauda() {
		ArrayList<RouteDetails> detailList = new ArrayList<RouteDetails>();
		boolean isAttendanceGiven =getAttendanceForToday();

		Cursor cursor=null;
		try {
			String sqlQuery = "SELECT * FROM route_master WHERE route_name IS NOT null AND route_name != '' AND route_code in (SELECT DISTINCT route_code from customer_master where cust_type='D') ORDER BY route_name ASC";
			cursor = database.rawQuery(sqlQuery,new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					RouteDetails detailsObj = new RouteDetails();
					detailsObj.setRouteCode(cursor.getString(0));
					detailsObj.setRouteName(cursor.getString(1));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<RouteDetails> getRouteListForStockAudit() {
		ArrayList<RouteDetails> detailList = new ArrayList<RouteDetails>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT * FROM route_master",new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					RouteDetails detailsObj = new RouteDetails();
					detailsObj.setRouteCode(cursor.getString(0));
					detailsObj.setRouteName(cursor.getString(1));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public boolean MenuAccess(String menu) {
		boolean isAccess=true;
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT * FROM menu_access WHERE not_accessibility_menu = '"+ menu +"'", new String[] {});
			if (cursor.getCount() > 0) {
				isAccess= false;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return isAccess;
	}


	public boolean checkAccess(String menu) {
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT * FROM user_access WHERE accessibility_menu = '"+ menu + "'", new String[] {});
			if (cursor.getCount() > 0) {
				cursor.close();
				return true;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return false;
	}

	public ArrayList<RouteDetails> 	getRouteForCollection() {
		ArrayList<RouteDetails> detailList = new ArrayList<RouteDetails>();
		Cursor cursor=null;
		try {
			String customQuery = "SELECT DISTINCT route_master.* "
					+ "FROM route_master,customer_master,outstanding_master "
					+ "WHERE route_master.route_code=customer_master.route_code "
					+ "AND customer_master.customer_code=outstanding_master.customer_code ";

			cursor = database.rawQuery(customQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					RouteDetails detailsObj = new RouteDetails();
					detailsObj.setRouteCode(cursor.getString(0));
					detailsObj.setRouteName(cursor.getString(1));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<RouteDetails> 	getAllRouteForCollection() {
		ArrayList<RouteDetails> detailList = new ArrayList<RouteDetails>();
		Cursor cursor=null;
		try {
			String customQuery = "SELECT * "
					+ "FROM route_master";

			cursor = database.rawQuery(customQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					RouteDetails detailsObj = new RouteDetails();
					detailsObj.setRouteCode(cursor.getString(0));
					detailsObj.setRouteName(cursor.getString(1));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> getCustomerList() {
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT * FROM customer_master where acedns = 'Y' and black_list = 'N'",new String[] {});
			if (cursor.getCount() > 0) {
				ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailsObj.setAddress(cursor.getString(17));
					detailsObj.setNumber(cursor.getString(18));
					detailsObj.setEmail(cursor.getString(29));
					detailsObj.setSaudaLimit(cursor.getString(30));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return null;
	}

	public void UpdateOrderStatus(String orderno){
		database.beginTransaction();
		try {
			ContentValues cv = new ContentValues();
			cv.put("flag", "0");
			database.update("order_status", cv, "order_no=?",
					new String[] { orderno });
			print_log_d("survey_output:", "Survey Output flagUpdated");
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Survey Output flag", e.getMessage());
		} finally {
			database.endTransaction();
		}
	}

	public ArrayList<OrderStatus> GetCustomerWiseOrderStatus(String orederno) {
		ArrayList<OrderStatus> orderStatusList = new ArrayList<OrderStatus>();
		Cursor cursor=null;
		try {
			cursor = database
					.rawQuery("SELECT OS.product_code,PM.prod_desc,OS.order_qty,OS.delivery_qty,OS.status FROM product_master PM,order_status OS WHERE PM.prod_code=OS.product_code AND OS.status='pending' AND OS.order_no='"+orederno+"' GROUP BY OS.product_code" , null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OrderStatus obj = new OrderStatus();
					obj.setOrderNo(orederno);
					obj.setProductCode(cursor.getString(0));
					obj.setProductName(cursor.getString(1));
					obj.setOrderQuantity(cursor.getString(2));
					obj.setAlreadyDeliveredQuantity(cursor.getString(3));
					obj.setStatus(cursor.getString(4));
					orderStatusList.add(obj);
					obj=null;
					cursor.moveToNext();
				}
			}
			cursor.close();

		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return orderStatusList;
	}


	public ArrayList<OrdernoWithDate> GetCustomerWiseOrderNO(String customercode,String chosenDateOfOrder){
		Cursor cursor=null;
		ArrayList<OrdernoWithDate> ordernoWithDateList = new ArrayList<OrdernoWithDate>();
		try {
			String orderno="";
			cursor = database
					.rawQuery("SELECT order_no FROM order_status WHERE customer_code='"+customercode+"' AND status='pending' AND SUBSTR(order_no,-14,8)  LIKE '"+ chosenDateOfOrder +"' GROUP BY order_no" , null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					OrdernoWithDate detailsObj = new OrdernoWithDate();
					orderno=cursor.getString(0);
					detailsObj.setOrderNo(orderno);
					detailsObj.setOrderDate(FormatDate(orderno));
					ordernoWithDateList.add(detailsObj);
					cursor.moveToNext();
				}
			}
			cursor.close();

		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return ordernoWithDateList;
	}

	public String FormatDate(String orderno){
		String subdate		="";
		String finaldate	="";
		if(orderno.length()>0){
			subdate=orderno.substring(6, 14);
			finaldate=subdate.substring(6,8)+"-"+subdate.substring(4,6)+"-"+subdate.substring(0,4);

		}
		return finaldate;
	}

	public ArrayList<CustomerDetails> GetOrderStatusCustomer(String chosenDateOfOrder)
	{

		ArrayList<CustomerDetails> customerDetailsList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try {
			cursor = database
					.rawQuery("SELECT CM.customer_name,OS.customer_code FROM customer_master CM,order_status OS WHERE CM.customer_code=OS.customer_code AND OS.status='pending' AND SUBSTR(OS.order_no,-14,8)  LIKE '"+ chosenDateOfOrder +"' GROUP BY OS.customer_code" , null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerName(cursor.getString(0));
					detailsObj.setCustomerCode(cursor.getString(1));
					customerDetailsList.add(detailsObj);
					detailsObj=null;
					cursor.moveToNext();
				}
			}
			cursor.close();

		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return customerDetailsList;
	}




	public ArrayList<CustomerDetails> getCustomerByEmployeeAndRouteForProspect(
			String routeCode) {
		Cursor cursor=null;
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		try {
			cursor = database
					.rawQuery(
							"SELECT * FROM customer_master where cust_type = 'D' and acedns = 'Y' and black_list = 'N' and emp_code = '"
									+ Constants.employeeDetailObject
									.getEmpCode().replace("'", "\'")
									+ "'", null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
			}
			cursor.close();
			return detailList;
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return null;
	}

	public ArrayList<OutstandingAgeing> GetOutstandingAgeingList() {
		ArrayList<OutstandingAgeing> outstandingAgeingList = new ArrayList<OutstandingAgeing>();
		Cursor cursor=null;
		try {
			cursor = database
					.rawQuery("SELECT customer_name, outstanding_amount , amount_0_15_days , amount_16_30_days , amount_31_45_days, amount_46_90_days , amount_greater_90_days   FROM  outstanding_ageing ORDER BY customer_name ASC",null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OutstandingAgeing obj = new OutstandingAgeing();
					obj.setCustomerName(cursor.getString(0));
					obj.setOutstandingAmount(cursor.getString(1));
					obj.setOutstanding0to15(cursor.getString(2));
					obj.setOutstanding16to30(cursor.getString(3));
					obj.setOutstanding31to45(cursor.getString(4));
					obj.setOutstanding46to90(cursor.getString(5));
					obj.setOutstandingGreater90(cursor.getString(6));
					outstandingAgeingList.add(obj);
					cursor.moveToNext();
					obj=null;
				}
				cursor.close();
				return outstandingAgeingList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return outstandingAgeingList;
	}

	public ArrayList<PendingContract> GetPendingContractAgeingList(String branchcode,String productcode,String customercode) {
		ArrayList<PendingContract> pendingContractList = new ArrayList<PendingContract>();
		Cursor cursor=null;
		try {
			cursor = database
					.rawQuery("SELECT BM.branch_name,(SELECT PGM.product_group_name FROM product_group_master PGM WHERE PGM.product_group_code=PC.product_group_code),PM.prod_desc,CM.customer_name, (SELECT BRM.broker_name FROM broker_master BRM WHERE BRM.broker_id=PC.broker_id), PC.qty_0_15  , PC.qty_16_30 ,PC.qty_31_45 ,PC.qty_46_60 , PC.qty_greater_60 ,PC.greater_60_days FROM product_master PM, pending_contract_ageing PC,branch_master BM,customer_master CM WHERE PM.prod_code=PC.prod_code AND BM.branch_code=PC.branch_code AND PC.customer_code=CM.customer_code AND PC.customer_code='"+customercode+"'",null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					PendingContract obj = new PendingContract();
					obj.setBranchCode(cursor.getString(0));
					obj.setProductGroupCode(cursor.getString(1));
					obj.setProductName(cursor.getString(2));
					obj.setCustomerName(cursor.getString(3));
					obj.setBrokerID(cursor.getString(4));
					obj.setQuantity0to15(cursor.getString(5));
					obj.setQuantity16to30(cursor.getString(6));
					obj.setQuantity31to45(cursor.getString(7));
					obj.setQuantity46to60(cursor.getString(8));
					obj.setQuantityGreater60(cursor.getString(9));
					obj.setGreater60Days(cursor.getString(10));
					pendingContractList.add(obj);
					cursor.moveToNext();
					obj=null;
				}
				cursor.close();
				return pendingContractList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return pendingContractList;
	}

	public ArrayList<SalesPerformance> GetSalesPerformanceList(String empcode,String customercode) {
		ArrayList<SalesPerformance> saleperformaneList = new ArrayList<SalesPerformance>();
		Cursor cursor=null;
		try {
			cursor = database
					.rawQuery("SELECT product_group_code,customer_code,SUM(YTD_sale) AS total_YTD,SUM(MTD_sale) AS total_MTD FROM sale_performance_details WHERE emp_code='"+empcode+"' AND customer_code='"+ customercode +"' GROUP BY product_group_code",null);

			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SalesPerformance obj = new SalesPerformance();
					obj.setEmployeeName(cursor.getString(0));
					obj.setEmployeeCode(cursor.getString(1));
					obj.setYTDSale(cursor.getString(2));
					obj.setMTDSale(cursor.getString(3));
					saleperformaneList.add(obj);
					cursor.moveToNext();
					obj=null;
				}
				cursor.close();
				return saleperformaneList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return saleperformaneList;
	}


	public ArrayList<SalesPerformance> GetSalesPerformanceProductWiseList(String empcode,String customercode,String productgroupcode) {
		ArrayList<SalesPerformance> saleperformaneList = new ArrayList<SalesPerformance>();
		Cursor cursor=null;
		try {
			if(customercode.trim().length()>0){
				cursor = database
						.rawQuery("SELECT prod_desc,customer_code,SUM(YTD_sale) AS total_YTD,SUM(MTD_sale) AS total_MTD FROM sale_performance_details WHERE emp_code='"+empcode+"' AND customer_code='"+customercode+"' AND product_group_code ='"+productgroupcode+"' GROUP BY prod_desc ORDER BY prod_desc ASC",null);

			}else{
				cursor = database
						.rawQuery("SELECT prod_desc,customer_code,SUM(YTD_sale) AS total_YTD,SUM(MTD_sale) AS total_MTD FROM sale_performance_details WHERE emp_code='"+empcode+"' AND product_group_code ='"+productgroupcode+"' GROUP BY prod_desc ORDER BY prod_desc ASC",null);
			}

			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SalesPerformance obj = new SalesPerformance();
					obj.setEmployeeName(cursor.getString(0));
					obj.setEmployeeCode(cursor.getString(1));
					obj.setYTDSale(cursor.getString(2));
					obj.setMTDSale(cursor.getString(3));
					saleperformaneList.add(obj);
					cursor.moveToNext();
					obj=null;
				}
				cursor.close();
				return saleperformaneList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return saleperformaneList;
	}




	public ArrayList<SalesPerformance> GetSalesPerformanceProductGroupWiseList(String empcode) {
		ArrayList<SalesPerformance> saleperformaneList = new ArrayList<SalesPerformance>();
		Cursor cursor=null;
		try {
			cursor = database
					.rawQuery("SELECT product_group_code,customer_code,SUM(YTD_sale) AS total_YTD,SUM(MTD_sale) AS total_MTD FROM sale_performance_details WHERE emp_code='"+empcode+"' GROUP BY product_group_code",null);

			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SalesPerformance obj = new SalesPerformance();
					obj.setEmployeeName(cursor.getString(0));
					obj.setEmployeeCode(cursor.getString(1));
					obj.setYTDSale(cursor.getString(2));
					obj.setMTDSale(cursor.getString(3));
					saleperformaneList.add(obj);
					cursor.moveToNext();
					obj=null;
				}
				cursor.close();
				return saleperformaneList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return saleperformaneList;
	}

	public ArrayList<SalesPerformance> GetSalesPerformanceList(String empcode) {
		ArrayList<SalesPerformance> saleperformaneList = new ArrayList<SalesPerformance>();
		Cursor cursor=null;
		try {
			cursor = database
					.rawQuery("SELECT customer_name,customer_code,SUM(YTD_sale) AS total_YTD,SUM(MTD_sale) AS total_MTD FROM sale_performance_details WHERE emp_code='"+empcode+"' GROUP BY customer_code",null);

			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SalesPerformance obj = new SalesPerformance();
					obj.setEmployeeName(cursor.getString(0));
					obj.setEmployeeCode(cursor.getString(1));
					obj.setYTDSale(cursor.getString(2));
					obj.setMTDSale(cursor.getString(3));
					saleperformaneList.add(obj);
					cursor.moveToNext();
					obj=null;
				}
				cursor.close();
				return saleperformaneList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return saleperformaneList;
	}

	public ArrayList<SalesPerformance> GetSalesPerformanceList() {
		ArrayList<SalesPerformance> saleperformaneList = new ArrayList<SalesPerformance>();
		Cursor cursor =null;
		try {
			cursor = database
					.rawQuery("SELECT emp_name,emp_code,SUM(YTD_sale) AS total_YTD,SUM(MTD_sale) AS total_MTD FROM sale_performance_details GROUP BY emp_code",null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					SalesPerformance obj = new SalesPerformance();
					obj.setEmployeeName(cursor.getString(0));
					obj.setEmployeeCode(cursor.getString(1));
					obj.setYTDSale(cursor.getString(2));
					obj.setMTDSale(cursor.getString(3));
					saleperformaneList.add(obj);
					cursor.moveToNext();
					obj=null;
				}
				cursor.close();
				return saleperformaneList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return saleperformaneList;
	}


	public ArrayList<PendingContract> GetPendingContractAgeingList() {
		ArrayList<PendingContract> pendingContractList = new ArrayList<PendingContract>();
		Cursor cursor =null;
		try {
			/*Cursor cursor = database
					.rawQuery("SELECT BM.branch_code,BM.branch_name,(SELECT PGM.product_group_name FROM product_group_master PGM WHERE PGM.product_group_code=PC.product_group_code),PM.prod_code,PM.prod_desc,CM.customer_code,CM.customer_name, (SELECT BRM.broker_name FROM broker_master BRM WHERE BRM.broker_id=PC.broker_id), SUM(PC.qty_0_15) , SUM(PC.qty_16_30) ,SUM(PC.qty_31_45) ,SUM(PC.qty_46_60) , SUM(PC.qty_greater_60) , SUM(PC.greater_60_days) FROM product_master PM, pending_contract_ageing PC,branch_master BM,customer_master CM WHERE PM.prod_code=PC.prod_code AND BM.branch_code=PC.branch_code AND PC.customer_code=CM.customer_code GROUP BY PC.branch_code,PC.customer_code,PC.prod_code",null);*/
			cursor = database
					.rawQuery("SELECT BM.branch_code,BM.branch_name,(SELECT PGM.product_group_name FROM product_group_master PGM WHERE PGM.product_group_code=PC.product_group_code),PM.prod_code,PM.prod_desc,CM.customer_code,CM.customer_name, (SELECT BRM.broker_name FROM broker_master BRM WHERE BRM.broker_id=PC.broker_id), SUM(PC.qty_0_15) , SUM(PC.qty_16_30) ,SUM(PC.qty_31_45) ,SUM(PC.qty_46_60) , SUM(PC.qty_greater_60) , SUM(PC.greater_60_days) FROM product_master PM, pending_contract_ageing PC,branch_master BM,customer_master CM WHERE PM.prod_code=PC.prod_code AND BM.branch_code=PC.branch_code AND PC.customer_code=CM.customer_code GROUP BY PC.customer_code ORDER BY CM.customer_name ASC",null);

			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					PendingContract obj = new PendingContract();
					obj.setBranchCode(cursor.getString(0));
					obj.setBranchName(cursor.getString(1));
					obj.setProductGroupCode(cursor.getString(2));
					obj.setProductCode(cursor.getString(3));
					obj.setProductName(cursor.getString(4));
					obj.setCustomerCode(cursor.getString(5));
					obj.setCustomerName(cursor.getString(6));
					obj.setBrokerID(cursor.getString(7));
					obj.setQuantity0to15(cursor.getString(8));
					obj.setQuantity16to30(cursor.getString(9));
					obj.setQuantity31to45(cursor.getString(10));
					obj.setQuantity46to60(cursor.getString(11));
					obj.setQuantityGreater60(cursor.getString(12));
					obj.setGreater60Days(cursor.getString(13));
					pendingContractList.add(obj);
					cursor.moveToNext();
					obj=null;
				}
				cursor.close();
				return pendingContractList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return pendingContractList;
	}


	public ArrayList<CustomerDetails> GetCustomerListforRoutePlan(String visitdate,String routeCode) {
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try {


			if(Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary")){
				cursor = database.rawQuery("SELECT CM.* FROM customer_master CM WHERE customer_code IN(SELECT RPT.customer_code FROM route_customer_plan_transaction  RPT  JOIN(SELECT customer_code,route_code,visit_date,MAX(route_plan_trans_id) AS timestamp FROM route_customer_plan_transaction  WHERE visit_date LIKE '%"+visitdate+"%'  GROUP BY customer_code, visit_date) SAT ON RPT.customer_code= SAT.customer_code AND RPT.route_plan_trans_id=SAT.timestamp AND RPT.visit_date=SAT.visit_date AND RPT.status='active' AND RPT.route_code='"+routeCode+"' GROUP BY RPT.customer_code,RPT.visit_date ORDER BY RPT.customer_code ASC) AND SUBSTR(CM.cust_type,1,1)<>'D' AND CM.acedns='Y' AND CM.black_list='N'",null);
			}else{
				cursor = database.rawQuery("SELECT CM.* FROM customer_master CM WHERE customer_code IN(SELECT RPT.customer_code FROM route_customer_plan_transaction  RPT  JOIN(SELECT customer_code,route_code,visit_date,MAX(route_plan_trans_id) AS timestamp FROM route_customer_plan_transaction  WHERE visit_date LIKE '%"+visitdate+"%'  GROUP BY customer_code, visit_date) SAT ON RPT.customer_code= SAT.customer_code AND RPT.route_plan_trans_id=SAT.timestamp AND RPT.visit_date=SAT.visit_date AND RPT.status='active' AND RPT.route_code='"+routeCode+"' GROUP BY RPT.customer_code,RPT.visit_date ORDER BY RPT.customer_code ASC) AND CM.acedns='Y' AND CM.black_list='N'",null);
			}
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRouteName(cursor.getString(10));
					detailsObj.setPin(cursor.getString(11));
					detailsObj.setNumber(cursor.getString(12));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailsObj.setAddress(cursor.getString(17));
					detailsObj.setCustClass(cursor.getString(21));
					detailsObj.setBranchCode(cursor.getString(27));
					detailsObj.setVisitDay(cursor.getString(28));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> GetCustomerListforRoutePlanWholeSale(String visitdate,String wholsalecollectdate) {
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT DISTINCT  RPT.distributor_code,CM.customer_name  FROM route_plan_transaction RPT,customer_master CM WHERE RPT.distributor_code=CM.customer_code AND RPT. visit_date='"+visitdate+"' AND RPT.distributor_code NOT IN(SELECT customer_code FROM wholesaler_details WHERE SUBSTR(wholesale_trans_id,-14,8)='"+wholsalecollectdate+"')",null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> GetCustomerListforEditedRoutePlan(String routecode,String visitdate) {
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT CM.* FROM customer_master CM WHERE CM.route_code='"+routecode+"' AND CM.customer_code NOT IN(SELECT RPT.customer_code  FROM route_customer_plan_transaction  RPT  JOIN(SELECT customer_code,route_code,visit_date,MAX(route_plan_trans_id) AS timestamp FROM route_customer_plan_transaction  WHERE visit_date LIKE '%"+visitdate+"%'  GROUP BY customer_code, visit_date) SAT ON RPT.customer_code= SAT.customer_code AND RPT.route_plan_trans_id=SAT.timestamp AND RPT.visit_date=SAT.visit_date AND RPT.status='active' AND RPT.route_code='"+routecode+"' GROUP BY RPT.customer_code,RPT.visit_date ORDER BY RPT.customer_code ASC)",null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRouteName(cursor.getString(10));
					detailsObj.setPin(cursor.getString(11));
					detailsObj.setNumber(cursor.getString(12));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailsObj.setAddress(cursor.getString(17));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> GetCustomerListforRoutePlan() {
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT * FROM customer_master WHERE acedns = 'Y' AND black_list = 'N' AND cust_type='D' ",null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRouteName(cursor.getString(10));
					detailsObj.setPin(cursor.getString(11));
					detailsObj.setNumber(cursor.getString(12));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailsObj.setAddress(cursor.getString(17));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> GetDistributorCustomerListforRoutePlan(String visitdate) {
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT DISTINCT CM.* FROM customer_master CM , route_plan_transaction RPT WHERE RPT.distributor_code=CM.customer_code AND RPT.visit_date='"+visitdate+"' AND status='active' ",null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRouteName(cursor.getString(10));
					detailsObj.setPin(cursor.getString(11));
					detailsObj.setNumber(cursor.getString(12));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailsObj.setAddress(cursor.getString(17));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> GetCustomerListforRoutePlan(String routeCode) {
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT * FROM customer_master where acedns = 'Y' and black_list = 'N' and route_code='"+routeCode+"' order by customer_name",null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRouteName(cursor.getString(10));
					detailsObj.setPin(cursor.getString(11));
					detailsObj.setNumber(cursor.getString(12));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailsObj.setAddress(cursor.getString(17));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> getNonTradeCustomerListByRoute(String routeCode) {
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT * FROM non_trade_customer_master WHERE route_code=?",new String[] { routeCode.replace("'", "\'") });
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setAddress(cursor.getString(2));
					detailsObj.setNumber(cursor.getString(3));
					detailsObj.setRouteCode(cursor.getString(4));
					detailsObj.setEmpCode(cursor.getString(5));
					detailsObj.setRdsTag(cursor.getString(6));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> getCustomerListByRoute(String routeCode)
	{
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try {
			if(Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary")){
				cursor = database.rawQuery("SELECT * FROM customer_master where acedns = 'Y' AND black_list = 'N' AND SUBSTR(cust_type,1,1)<>'D' AND route_code=?",	new String[] { routeCode.replace("'", "\'") });
			}else{
				cursor = database.rawQuery("SELECT * FROM customer_master where acedns = 'Y' AND black_list = 'N' AND route_code=?",	new String[] { routeCode.replace("'", "\'") });
			}

			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRouteName(cursor.getString(10));
					detailsObj.setPin(cursor.getString(11));
					detailsObj.setNumber(cursor.getString(12));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));//RKBK comment out
					detailsObj.setAddress(cursor.getString(17));//RKBK comment out
					detailsObj.setCustClass(cursor.getString(21));//RKBK comment out
//					detailsObj.setBranchCode(cursor.getString(27));
//					detailsObj.setVisitDay(cursor.getString(28));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> getCustomerListByRouteSkippingD(String routeCode)
	{
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try {
			if(Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary")){
				cursor = database.rawQuery("SELECT * FROM customer_master where acedns = 'Y' AND black_list = 'N' AND SUBSTR(cust_type,1,1)<>'D' AND route_code=?",	new String[] { routeCode.replace("'", "\'") });
			}else{
				cursor = database.rawQuery("SELECT * FROM customer_master where acedns = 'Y' AND black_list = 'N' AND SUBSTR(cust_type,1,1)<>'D' AND route_code=?",	new String[] { routeCode.replace("'", "\'") });
			}

			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRouteName(cursor.getString(10));
					detailsObj.setPin(cursor.getString(11));
					detailsObj.setNumber(cursor.getString(12));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));//RKBK comment out
					detailsObj.setAddress(cursor.getString(17));//RKBK comment out
					detailsObj.setCustClass(cursor.getString(21));//RKBK comment out
					detailsObj.setBranchCode(cursor.getString(27));
					detailsObj.setVisitDay(cursor.getString(28));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}
	public ArrayList<CustomerDetails> getCustomerListByRouteForSauda(String routeCode)
	{
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try
		{
			cursor = database.rawQuery("SELECT * FROM customer_master where acedns = 'Y' AND black_list = 'N' AND cust_type='D' AND route_code=?", new String[] { routeCode.replace("'", "\'") });

			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRouteName(cursor.getString(10));
					detailsObj.setPin(cursor.getString(11));
					detailsObj.setNumber(cursor.getString(12));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailsObj.setAddress(cursor.getString(17));
					detailsObj.setCustClass(cursor.getString(21));
					detailsObj.setBranchCode(cursor.getString(27));
					detailsObj.setVisitDay(cursor.getString(28));
					detailsObj.setEmail(cursor.getString(29));
					detailsObj.setSaudaLimit(cursor.getString(30));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> getProspectCustomerList()
	{
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try
		{

			String sql = "SELECT * FROM prospective_customer_master";
			cursor = database.rawQuery(sql,	new String[] {});

			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setEmpCode(cursor.getString(0));
					detailsObj.setCustomerCode(cursor.getString(1));
					detailsObj.setCustomerName(cursor.getString(2));
					detailsObj.setAddress(cursor.getString(3));
					detailsObj.setPin(cursor.getString(4));
					detailsObj.setRouteCode(cursor.getString(5));
					detailsObj.setNumber(cursor.getString(6));
					detailsObj.setTaggedCustomerCode(cursor.getString(8));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		}
		catch (Exception e)
		{
			print_Log_d(e.getMessage());
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> getCustomerListDayOfWeekWise(String visitDay) {
		ArrayList<CustomerDetails> detailList = new ArrayList<>();

		Cursor cursor=null;
		try {
			if( Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes") || Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary") )
			{

				String sql = "SELECT * FROM customer_master where acedns = 'Y' AND black_list = 'N' AND SUBSTR(cust_type,1,1)<>'D' AND visit_day LIKE '" + visitDay + "'";
				cursor = database.rawQuery(sql,	new String[] { });
			}
			else
			{
				String sql = "SELECT * FROM customer_master where acedns = 'Y' AND black_list = 'N' AND visit_day LIKE '" + visitDay + "'";
				cursor = database.rawQuery(sql,	new String[] {  });
			}

			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRouteName(cursor.getString(10));
					detailsObj.setPin(cursor.getString(11));
					detailsObj.setNumber(cursor.getString(12));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailsObj.setAddress(cursor.getString(17));
					detailsObj.setCustClass(cursor.getString(21));
					detailsObj.setBranchCode(cursor.getString(27));
					detailsObj.setVisitDay(cursor.getString(28));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}
	public ArrayList<CustomerDetails> getRetailerSubDelearTypeCustomerListByRoute(String routeCode) {
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try
		{
			cursor = database.rawQuery("SELECT * FROM customer_master where acedns = 'Y' AND black_list = 'N' AND (cust_type='Sub Dealer' OR cust_type='Retailer') AND route_code=?",	new String[] { routeCode.replace("'", "\'") });

			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRouteName(cursor.getString(10));
					detailsObj.setPin(cursor.getString(11));
					detailsObj.setNumber(cursor.getString(12));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailsObj.setAddress(cursor.getString(17));
					detailsObj.setEmail(cursor.getString(29));
					detailsObj.setSaudaLimit(cursor.getString(30));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		}
		catch (Exception e)
		{
			print_Log_d(e.getMessage());
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> getCustomerListByRouteForStockAudit(String routeCodeListFormatted)
	{
		ArrayList<CustomerDetails> detailListFinal = new ArrayList<>();
		ArrayList <String>finalCustomerList = new  ArrayList();
		try
		{
			String custTypeFromServer=Constants.userDetailsObj.getstk_audit_cust_type();//example D#customer_master,R#customer_master
			ArrayList <String>custTypeFromServerList = new  ArrayList();
			if(custTypeFromServer.contains(","))
			{
				String[] custTypeSplitted=custTypeFromServer.split(",");
				custTypeFromServerList = new  ArrayList(Arrays.asList(custTypeSplitted));
			}
			else
			{
				custTypeFromServerList.add(custTypeFromServer);
			}

			for(int ii = 0; ii < custTypeFromServerList.size(); ii++)
			{
				String[] custTypeTableNameSplited=custTypeFromServerList.get(ii).split("\\#");
				String sqlQuery2="";
				if(custTypeTableNameSplited.length>1 && custTypeTableNameSplited[1].matches("distributor_route_relation"))
				{
					sqlQuery2 = "SELECT Distinct distributor_code FROM distributor_route_relation where route_code IN(" + routeCodeListFormatted + ")";
				}
				else//if(custTypeTableNameSplited[1].matches("customer_master"))
				{
					sqlQuery2 = "SELECT Distinct customer_code FROM customer_master where acedns = 'Y' AND black_list = 'N' AND cust_type='"+custTypeTableNameSplited[0]+"' AND route_code IN(" + routeCodeListFormatted + ")";
				}
				Cursor cursor = database.rawQuery(sqlQuery2, new String[] {});
				if (cursor.getCount() > 0)
				{
					cursor.moveToFirst();
					for (int i = 0; i < cursor.getCount(); i++)
					{
						finalCustomerList.add(cursor.getString(0));
						cursor.moveToNext();
					}
					cursor.close();
				}

			}
			String sqlQuery3="Select * from customer_master where customer_code IN("+Utils.getFormattedCustomerCode(finalCustomerList)+")";
			Cursor cursor=null;
			cursor = database.rawQuery(sqlQuery3, new String[] {});
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int i = 0; i < cursor.getCount(); i++)
				{
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailsObj.setMinimumStock(cursor.getString(26));
					detailListFinal.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		return detailListFinal;
	}

	public ArrayList<CustomerDetails> getEntireCustomerList()
	{
		ArrayList<CustomerDetails> detailListFinal = new ArrayList<>();
		try
		{

			String sqlQuery3="Select * from customer_master where acedns = 'Y' AND black_list = 'N'";
			Cursor cursor=null;
			cursor = database.rawQuery(sqlQuery3, new String[] {});
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int i = 0; i < cursor.getCount(); i++)
				{
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailsObj.setMinimumStock(cursor.getString(26));
					detailListFinal.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		return detailListFinal;
	}

	public ArrayList<CustomerDetails> getCustomerListByRouteForStockAuditForCheckedInCustomer(String routeCode)
	{
		ArrayList<CustomerDetails> detailList = new ArrayList<>();
		Cursor cursor=null;
		try
		{
			if(Constants.orderFormDetailsObj.getDistributorRouteEmployeeRelation().equalsIgnoreCase("yes"))
			{
				String SqlQuery="Select * from customer_master where customer_code IN(SELECT Distinct distributor_code FROM distributor_route_relation where route_code= '"+routeCode+"')";
				cursor = database.rawQuery(SqlQuery,new String[] {});
				if (cursor.getCount() > 0)
				{
					cursor.moveToFirst();
					for (int ii = 0; ii < cursor.getCount(); ii++)
					{
						CustomerDetails detailsObj = new CustomerDetails();
						detailsObj.setCustomerCode(cursor.getString(0));
						detailsObj.setCustomerName(cursor.getString(1));
						detailsObj.setRouteCode(cursor.getString(2));
						detailsObj.setEmpCode(cursor.getString(3));
						detailsObj.setIsBlackList(cursor.getString(4));
						detailsObj.setIsACEDNS(cursor.getString(5));
						detailsObj.setCreditLimit(cursor.getString(6));
						detailsObj.setCurrentBalance(cursor.getString(7));
						detailsObj.setTradeDiscount(cursor.getString(8));
						detailsObj.setCustomerType(cursor.getString(9));
						detailsObj.setRdsTag(cursor.getString(13));
						detailsObj.setFlag(cursor.getString(14));
						detailsObj.setSaudaValidityPeriod(cursor.getString(15));
						detailsObj.setMinimumStock(cursor.getString(26));
						detailList.add(detailsObj);
						cursor.moveToNext();
					}
					cursor.close();
				}
			}
			else
			{
				String sqlQuery = "SELECT * FROM customer_master where acedns = 'Y' AND black_list = 'N' AND cust_type IN ("+ convertCommaSeparatedListToProperFormat(Constants.userDetailsObj.getstk_audit_cust_type())+") AND route_code IN(" + routeCode + ")";
				cursor = database.rawQuery(sqlQuery, new String[] {});
				if (cursor.getCount() > 0)
				{
					cursor.moveToFirst();
					for (int ii = 0; ii < cursor.getCount(); ii++)
					{
						CustomerDetails detailsObj = new CustomerDetails();
						detailsObj.setCustomerCode(cursor.getString(0));
						detailsObj.setCustomerName(cursor.getString(1));
						detailsObj.setRouteCode(cursor.getString(2));
						detailsObj.setEmpCode(cursor.getString(3));
						detailsObj.setIsBlackList(cursor.getString(4));
						detailsObj.setIsACEDNS(cursor.getString(5));
						detailsObj.setCreditLimit(cursor.getString(6));
						detailsObj.setCurrentBalance(cursor.getString(7));
						detailsObj.setTradeDiscount(cursor.getString(8));
						detailsObj.setCustomerType(cursor.getString(9));
						detailsObj.setRdsTag(cursor.getString(13));
						detailsObj.setFlag(cursor.getString(14));
						detailsObj.setSaudaValidityPeriod(cursor.getString(15));
						detailsObj.setMinimumStock(cursor.getString(26));
						detailList.add(detailsObj);
						cursor.moveToNext();
					}
					cursor.close();
					return detailList;
				}
			}
		}
		catch (Exception e)
		{
			print_Log_d(e.getMessage());
		}
		finally
		{
			if(cursor!=null && !cursor.isClosed())
			{
				cursor.close();
			}
		}
		return detailList;
	}

	public CustomerDetails getCustomerListByRouteForTagDistributor(String routecode) {
		CustomerDetails detailsObj = new CustomerDetails();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT CM.customer_code,CM.customer_name FROM customer_master CM, distributor_route_relation DRR WHERE CM.customer_code=DRR.distributor_code AND DRR.route_code='"+routecode+"'",new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				detailsObj.setCustomerCode(cursor.getString(0));
				detailsObj.setCustomerName(cursor.getString(1));
				cursor.close();
				return detailsObj;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailsObj;
	}

	public ArrayList<CustomerDetails>  getCustomerListByRouteForTagDistributor()
	{
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try
		{
			cursor = database.rawQuery("SELECT * FROM customer_master where acedns = 'Y' and black_list = 'N' and cust_type = 'D'",new String[] {});
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		}
		catch (Exception e)
		{
			print_Log_d(e.getMessage());
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails>  getCustomerListByRDSForTagDistributor(String routeCode)
	{
		ArrayList<CustomerDetails> detailList = new ArrayList<>();
		Cursor cursor=null;
		try
		{
			String SqlQuery="";
			if(Constants.orderFormDetailsObj.getDistributorRouteEmployeeRelation().equalsIgnoreCase("yes"))
			{
				SqlQuery="Select * from customer_master where customer_code IN(SELECT Distinct distributor_code FROM distributor_route_relation where route_code= '"+routeCode+"')";
			}
			else
			{
				SqlQuery="Select * from customer_master where customer_code IN(SELECT Distinct rds_tag FROM customer_master where acedns = 'Y' AND black_list = 'N' AND route_code= '"+routeCode+"')";
			}

			cursor = database.rawQuery(SqlQuery,new String[] {});
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		}
		catch (Exception e)
		{
			print_Log_d(e.getMessage());
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return detailList;
	}

	public void UpdateLocationDataForQuotation()
	{
		database.beginTransaction();
		int updateResult = -1;
		String sql="UPDATE  location SET flag='1' WHERE substr(trans_id,1,1)='Q' AND flag='0'";
		try {
			database.execSQL(sql);
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Location Update", e.getMessage());
		} finally {
			database.endTransaction();
		}
		System.out
				.println("Location Update status ::::::::::::" + updateResult);
	}

	public ArrayList<RouteDetails> getRouteListRDSWise(String rdsCodes) {
		ArrayList<RouteDetails> detailList = new ArrayList<>();
		boolean isAttendanceGiven =getAttendanceForToday();
		String StringToRemoveLeaveRequestRoute ="";
		if(isAttendanceGiven)
		{
			StringToRemoveLeaveRequestRoute=" AND route_name NOT LIKE '%leave request%' ";
		}
		Cursor cursor=null;
		try {
			String qur = "SELECT * FROM route_master WHERE route_name IS NOT null AND route_name != ''"+StringToRemoveLeaveRequestRoute+" AND route_code IN(SELECT route_code FROM customer_master WHERE rds_tag IN("
					+ rdsCodes + ") OR customer_code IN(" + rdsCodes + "))";
			cursor = database.rawQuery(qur, new String[] {});
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					RouteDetails detailsObj = new RouteDetails();
					detailsObj.setRouteCode(cursor.getString(0));
					detailsObj.setRouteName(cursor.getString(1));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<BranchMasterDetails> GETDEPOList(String type) {
		ArrayList<BranchMasterDetails> detailList = new ArrayList<BranchMasterDetails>();
		Cursor cursor=null;
		try {
			if(type.equalsIgnoreCase("sauda")){
				cursor = database.rawQuery("SELECT BM.company_code, MRP.branch_code,BM.branch_name,BM.Hq FROM branch_master BM, sauda_mrp MRP WHERE MRP.branch_code=BM.branch_code GROUP BY  MRP.branch_code", null);
			}else{
				cursor = database.rawQuery("SELECT BM.company_code, MRP.branch_code,BM.branch_name,BM.Hq FROM branch_master BM, mrp MRP WHERE MRP.branch_code=BM.branch_code GROUP BY  MRP.branch_code", null);
			}
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					BranchMasterDetails detailsObj = new BranchMasterDetails();
					detailsObj.setCompanyCode(cursor.getString(0));
					detailsObj.setBranchCode(cursor.getString(1));
					detailsObj.setBranchName(cursor.getString(2));
					detailsObj.setHq(cursor.getString(3));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<DestinationMaster> getMySubDealerList(final String params)
	{
		ArrayList<DestinationMaster> destinationList = new ArrayList<DestinationMaster>();
		Cursor cursor=null;
		try
		{
			if(params.equalsIgnoreCase("rssd"))
			{
				if(get_user_type(mContext).equalsIgnoreCase("broker"))
				{
					String query_dealer = "SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type == 'RSSD' AND customer_code !=  '"+ get_selected_customer_code(mContext)+"' ORDER BY customer_name ASC";
					print_log_d("QUERY_BROKER ", query_dealer);
					cursor = database.rawQuery(query_dealer, new String[] {});
				}
				else
				{
					String query_dealer = "SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type == 'RSSD' AND customer_code !=  '"+ get_emp_or_customer_code(mContext)+"' ORDER BY customer_name ASC";
					print_log_d("QUERY_BROKER ", query_dealer);
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

			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					final DestinationMaster detailsObj = new DestinationMaster();
					detailsObj.setRow_position(ii);
					detailsObj.set_address(cursor.getString(0)); //todo
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
	public ArrayList<DestinationMaster> getMyDealerList()
	{
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
//		    if(!freight.matches(""))
//            {
//                selectQuery = "SELECT destination_code, destination_name, ex_for_type FROM destination_master WHERE ex_for_type = '" + freight + "'";
//            }
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

	public String getDealerName(String rdscode)
	{
		String dealername="";
		Cursor cursor=null;
		try
		{
			cursor = database.rawQuery("SELECT customer_name FROM customer_master WHERE customer_code IN("+convertCommaSeparatedListToProperFormat(rdscode)+")", new String[] {});
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				if(cursor.getString(0)!=null)
				{
					dealername=cursor.getString(0);
				}
				else
				{
					dealername="";
				}
				cursor.close();
				return dealername;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return dealername;
	}

	public ArrayList<String> getDealerNameForOrder(String rdscode)
	{
		ArrayList<String> getDealerNameList=new ArrayList<>();
		Cursor cursor=null;
		try
		{
			cursor = database.rawQuery("SELECT customer_name FROM customer_master WHERE customer_code IN("+convertCommaSeparatedListToProperFormat(rdscode)+")", new String[] {});
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					if(cursor.getString(0)!=null)
					{
						getDealerNameList.add(cursor.getString(0));
					}

					cursor.moveToNext();
				}

				cursor.close();
				return getDealerNameList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return getDealerNameList;
	}

	public ArrayList<BranchMasterDetails> getSaudaRDSList(String custCode) {
		ArrayList<BranchMasterDetails> detailList = new ArrayList<BranchMasterDetails>();
		Cursor cursor=null;
		try {
			String query = "SELECT * FROM branch_master WHERE branch_code IN(SELECT branch_code from customer_branch_relation WHERE customer_code ='"
					+ custCode + "' AND acedns='Y')";
			cursor = database
					.rawQuery(
							query, new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					BranchMasterDetails detailsObj = new BranchMasterDetails();
					detailsObj.setCompanyCode(cursor.getString(0));
					detailsObj.setBranchCode(cursor.getString(1));
					detailsObj.setBranchName(cursor.getString(2));
					detailsObj.setHq(cursor.getString(3));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<BranchMasterDetails> getSaudaRDSListWithRouteCode(String custCode,String selectedRouteCode) {
		ArrayList<BranchMasterDetails> detailList = new ArrayList<BranchMasterDetails>();
		Cursor cursor=null;
		try {
			String query= "SELECT * FROM branch_master WHERE branch_code IN(SELECT branch_code from customer_branch_relation WHERE customer_code ='"+custCode+"' AND acedns='Y' AND branch_code IN(SELECT DISTINCT branch_code FROM branch_route_freight WHERE route_code = '"+selectedRouteCode+"'))";
			cursor = database
					.rawQuery(
							query, new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					BranchMasterDetails detailsObj = new BranchMasterDetails();
					detailsObj.setCompanyCode(cursor.getString(0));
					detailsObj.setBranchCode(cursor.getString(1));
					detailsObj.setBranchName(cursor.getString(2));
					detailsObj.setHq(cursor.getString(3));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> getCustomerListByRouteWithOS(String routeCode) {
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try {

			String customQuery = "SELECT DISTINCT customer_master.* "
					+ "FROM customer_master,outstanding_master "
					+ "WHERE customer_master.acedns = 'Y' and customer_master.black_list = 'N' "
					+ "and customer_master.route_code='"
					+ routeCode.replace("'", "\'")
					+ "' "
					+ "and customer_master.customer_code = outstanding_master.customer_code;";

			cursor = database.rawQuery(customQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<CustomerDetails> getCustomerListWithOS() {
		ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
		Cursor cursor=null;
		try {
			String customQuery = "SELECT DISTINCT customer_master.* "
					+ "FROM customer_master,outstanding_master "
					+ "WHERE customer_master.acedns = 'Y' and customer_master.black_list = 'N' "
					+ "and customer_master.customer_code = outstanding_master.customer_code;";

			cursor = database.rawQuery(customQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					CustomerDetails detailsObj = new CustomerDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setCustomerName(cursor.getString(1));
					detailsObj.setRouteCode(cursor.getString(2));
					detailsObj.setEmpCode(cursor.getString(3));
					detailsObj.setIsBlackList(cursor.getString(4));
					detailsObj.setIsACEDNS(cursor.getString(5));
					detailsObj.setCreditLimit(cursor.getString(6));
					detailsObj.setCurrentBalance(cursor.getString(7));
					detailsObj.setTradeDiscount(cursor.getString(8));
					detailsObj.setCustomerType(cursor.getString(9));
					detailsObj.setRdsTag(cursor.getString(13));
					detailsObj.setFlag(cursor.getString(14));
					detailsObj.setSaudaValidityPeriod(cursor.getString(15));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}



	public ArrayList<ProductMasterDetails> getProdMasterListForProspect(
			String query) {
		Cursor cursor =null;
		ArrayList<ProductMasterDetails> detailList = new ArrayList<ProductMasterDetails>();
		try {
			cursor = database.rawQuery(query, new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					ProductMasterDetails detailsObj = new ProductMasterDetails();
					detailsObj.setProdCode(cursor.getString(0));
					detailsObj.setGrpCode(cursor.getString(1));
					detailsObj.setGrpName(cursor.getString(2));
					detailsObj.setSubGrpCode(cursor.getString(3));
					detailsObj.setSubGrpName(cursor.getString(4));
					detailsObj.setBrndCode(cursor.getString(5));
					detailsObj.setBrndName(cursor.getString(6));
					detailsObj.setDesc(cursor.getString(7));
					detailsObj.setIsBlkLst(cursor.getString(8));
					detailsObj.setIsAcedns(cursor.getString(9));
					detailsObj.setClosingStk(cursor.getString(11));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<OutstandingDetails> getOutstandingListForCustomer(
			String cust_code) {
		Cursor cursor =null;
		ArrayList<OutstandingDetails> detailList = new ArrayList<OutstandingDetails>();
		try {
			cursor = database.rawQuery(
					"select * FROM outstanding_master where customer_code = ?",
					new String[] { cust_code.replace("'", "\'") });
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					OutstandingDetails detailsObj = new OutstandingDetails();
					detailsObj.setCustomerCode(cursor.getString(0));
					detailsObj.setRecId(cursor.getString(1));
					detailsObj.setInvoice_id(cursor.getString(2));
					detailsObj.setCustomerName(cursor.getString(3));
					detailsObj.setDate(cursor.getString(4));
					detailsObj.setInvoice_amount(cursor.getString(5));
					detailsObj.setDue_amount(cursor.getString(6));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return null;
	}

	public ArrayList<RoutePlanMasterDetails> getRoutePlanList() {
		ArrayList<RoutePlanMasterDetails> detailList = new ArrayList<RoutePlanMasterDetails>();
		Cursor cursor =null;
		try {
			cursor = database.rawQuery(
					"select * FROM route_plan_transaction", new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					RoutePlanMasterDetails detailsObj = new RoutePlanMasterDetails();
					detailsObj.setTranId(cursor.getString(0));
					detailsObj.setEmpCode(cursor.getString(1));
					detailsObj.setRoutecode(cursor.getString(2));
					detailsObj.setVisitDate(cursor.getString(3));
					detailsObj.setCreateDate(cursor.getString(4));
					detailsObj.setRouteName(cursor.getString(5));
					detailsObj.setFlag(cursor.getString(6));
					detailsObj.setPrevious_route_code(cursor.getString(7));
					detailsObj.setPrevious_route_name(cursor.getString(8));
					detailsObj.setRemarks(cursor.getString(9));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return null;
	}

	public String[] getRoutePlanAccessPeriod() {
		String[] detailList = null;
		Cursor cursor =null;
		try {
			cursor = database.rawQuery("SELECT * FROM route_plan_access_period", new String[] {});
			if (cursor.getCount() > 0) {
				detailList = new String[3];
				cursor.moveToFirst();
				detailList[0] = (cursor.getString(0));
				detailList[1] = (cursor.getString(1));
				detailList[2] = (cursor.getString(2));
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d("10222 " + e.toString());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return null;
	}

	public ArrayList<TravelExpCategory> getTravelCatList() {
		ArrayList<TravelExpCategory> detailList = new ArrayList<TravelExpCategory>();
		Cursor cursor =null;
		try {
			cursor = database.rawQuery("SELECT * FROM transport_mode_category", new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					TravelExpCategory detailsObj = new TravelExpCategory();
					detailsObj.setCategoryId(cursor.getString(0));
					detailsObj.setCategoryName(cursor.getString(1));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return null;
	}

	public ArrayList<TravelExpSubCategory> getTravelSubCatList(String catId) {
		ArrayList<TravelExpSubCategory> detailList = new ArrayList<TravelExpSubCategory>();
		Cursor cursor =null;
		try {
			String selectQuery = "select * FROM transport_mode_sub_category where transport_mode_cat_id = '"
					+ catId + "'";
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					TravelExpSubCategory detailsObj = new TravelExpSubCategory();
					detailsObj.setSubCatId(cursor.getString(0));
					detailsObj.setSubCatName(cursor.getString(1));
					detailsObj.setCategoryId(cursor.getString(2));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<BankDetails> getBankList()
	{
		Cursor cursor =null;
		try {
			ArrayList<BankDetails> detailList = new ArrayList<BankDetails>();
			cursor = database.rawQuery("select * FROM bank_master",new String[] {});

			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					BankDetails detailsObj = new BankDetails();
					detailsObj.setBankId(cursor.getString(0));
					detailsObj.setBankName(cursor.getString(1));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e)
		{
			print_Log_d(e.getMessage());
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return null;
	}

	public ArrayList<RDSDetails> getRDSList() {
		ArrayList<RDSDetails> detailList = new ArrayList<RDSDetails>();
		Cursor cursor =null;
		try {
			cursor = database.rawQuery("select * FROM rds_master",
					new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					RDSDetails detailsObj = new RDSDetails();
					detailsObj.setRdsCode(cursor.getString(0));
					detailsObj.setRdsName(cursor.getString(1));
					detailsObj.setRdsType(cursor.getString(2));
					detailsObj.setemp_code(cursor.getString(3));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return null;
	}

	public RDSDetails getRDSDetails(String rdsType) {
		RDSDetails detailsObj = new RDSDetails();
		Cursor cursor =null;
		try {
			cursor = database.rawQuery(
					"SELECT * FROM rds_master WHERE rds_type = '" + rdsType
							+ "'", new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				detailsObj.setRdsCode(cursor.getString(0));
				detailsObj.setRdsName(cursor.getString(1));
				detailsObj.setRdsType(cursor.getString(2));
				cursor.moveToNext();
				cursor.close();
				return detailsObj;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailsObj;
	}

	public SchemeDetails getSupportedScheme(String verticalName) {
		SchemeDetails detailsObj = new SchemeDetails();
		Cursor cursor =null;
		try {
			cursor = database.rawQuery(
					"SELECT * FROM scheme_details WHERE vertical_name = ?",
					new String[] { verticalName });
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					detailsObj.setVerticalName(cursor.getString(0));
					detailsObj.setSchemeValue(cursor.getString(1));
				}
				cursor.close();
				return detailsObj;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return null;
	}

	public ArrayList<RedeemeDetails> getRedeemeDetails(String accuPoints) {
		ArrayList<RedeemeDetails> redeemeList = new ArrayList<RedeemeDetails>();
		String currentDate = dateString.substring(0, 4) + "-"
				+ dateString.substring(4, 6) + "-"
				+ dateString.substring(6, 8);
		Cursor cursor =null;
		try {
			String query = "SELECT * FROM redeeme_details WHERE points <= '"
					+ accuPoints + "' AND scheme_expiry_date >= '"
					+ currentDate + "'";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				redeemeList = new ArrayList<RedeemeDetails>();
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					RedeemeDetails detailsObj = new RedeemeDetails();
					detailsObj.setExpDate(cursor.getString(0));
					detailsObj.setPoints(cursor.getInt(1));
					detailsObj.setAward(cursor.getString(2));
					redeemeList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return redeemeList;
	}

	public LoyaltyPurchaseDetails getLoyaltyPurchaseDetails(
			String customerCode, String verticalName) {
		LoyaltyPurchaseDetails detailsObj = null;
		Cursor cursor1 =null;
		try {
			String query1 = "SELECT * FROM loyalty_purchase_details WHERE loyalty_card_no = '"
					+ customerCode + "'";
			String query2 = "SELECT purchase_value FROM loyalty_purchase_details WHERE loyalty_card_no = '"
					+ customerCode
					+ "' AND vertical_name = '"
					+ verticalName
					+ "'";
			cursor1 = database.rawQuery(query1, new String[] {});
			if (cursor1.getCount() > 0) {
				cursor1.moveToFirst();
				for (int ii = 0; ii < cursor1.getCount(); ii++) {
					detailsObj = new LoyaltyPurchaseDetails();
					detailsObj.setLoyaltyCardNo(cursor1.getString(0));
					detailsObj.setVerticalName(cursor1.getString(1));
					detailsObj.setPurchaseValue("0.00");
					detailsObj.setRwrdPoint(cursor1.getString(3));
					detailsObj.setRdmdPoint(cursor1.getString(4));
				}
				Cursor cursor2 = database.rawQuery(query2, new String[] {});
				if (cursor2.getCount() > 0) {
					cursor2.moveToFirst();
					detailsObj.setPurchaseValue(cursor2.getString(0));
					cursor2.close();
				}
				cursor1.close();
				return detailsObj;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor1!=null){
				cursor1.close();
			}
		}
		return detailsObj;
	}

	public ArrayList<LoyaltyCustomerDetails> getLoyaltyCustomerList() {
		ArrayList<LoyaltyCustomerDetails> detailList = new ArrayList<LoyaltyCustomerDetails>();
		Cursor cursor=null;
		try {
			cursor = database
					.rawQuery(
							"select * FROM loyalty_card_holder_master ORDER BY loyalty_card_holder_name",
							new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					LoyaltyCustomerDetails detailsObj = new LoyaltyCustomerDetails();
					detailsObj.setCardHolderCode(cursor.getString(0));
					detailsObj.setCardHolderName(cursor.getString(1));
					detailsObj.setCardNumber(cursor.getString(2));
					detailsObj.setCardType(cursor.getString(3));
					detailsObj.setPurchaseValue(cursor.getString(4));
					detailsObj.setRewardPoint(cursor.getString(5));
					detailsObj.setLastUpdate(cursor.getString(6));
					detailsObj.setRedeemed(cursor.getString(7));
					detailsObj.setPhone(cursor.getString(8));
					detailsObj.setAddress(cursor.getString(9));
					detailsObj.setVehicleNo(cursor.getString(10));
					detailsObj.setCardExpDate(cursor.getString(11));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public ArrayList<BranchMasterDetails> getBranchForStockIn() {
		ArrayList<BranchMasterDetails> detailList = new ArrayList<BranchMasterDetails>();
		Cursor cursor=null;
		try {
			cursor = database
					.rawQuery(
							"SELECT * FROM branch_master WHERE branch_code IN(SELECT despatcher_code FROM goods_in_transit WHERE status = '0' AND (despatch_qty - (SELECT SUM(bal_rec_qty) FROM goods_in_transit GROUP BY prod_code,grn_no) > 0))",
							new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					BranchMasterDetails detailsObj = new BranchMasterDetails();
					detailsObj.setCompanyCode(cursor.getString(0));
					detailsObj.setBranchCode(cursor.getString(1));
					detailsObj.setBranchName(cursor.getString(2));
					detailsObj.setHq(cursor.getString(3));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}


	public ArrayList<EmployeeMasterDetails> GetEmployeeForSaudaAllocation(String empcode) {
		ArrayList<EmployeeMasterDetails> employeeMasterDetailsList = new ArrayList<EmployeeMasterDetails>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("SELECT SAA.emp_code,EM.emp_name  FROM sauda_allocation_access SAA,emp_master EM WHERE EM.emp_code=SAA.emp_code AND SAA.get_allocation='yes' AND EM.acedns!='N' AND EM.reporting_to='"+ empcode +"'",
					new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					EmployeeMasterDetails obj = new EmployeeMasterDetails();
					obj.setEmpCode(cursor.getString(0));
					obj.setEmpName(cursor.getString(1));
					employeeMasterDetailsList.add(obj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return employeeMasterDetailsList;
	}

	public ArrayList<EmployeeMasterDetails> GetEmployeeForTDAllocation(String empcode)
	{
		String verticalCondition="";
		ArrayList<EmployeeMasterDetails> employeeMasterDetailsList = new ArrayList<EmployeeMasterDetails>();
		Cursor cursor=null;
		try
		{
			String verticalFromServerForTDAllocation=Constants.menuDetailsObj.getTD_allocation_vertical().trim();
			if(!verticalFromServerForTDAllocation.matches(""))
			{
				verticalFromServerForTDAllocation =" AND EM.vertical_value IN("+ convertCommaSeparatedListToProperFormat(verticalFromServerForTDAllocation)+")";

			}

			cursor = database.rawQuery("SELECT TDAA.emp_code,EM.emp_name  FROM td_allocation_access TDAA,emp_master EM WHERE EM.emp_code=TDAA.emp_code AND TDAA.get_allocation='yes' AND EM.acedns!='N' AND EM.reporting_to='"+ empcode +"'"+verticalFromServerForTDAllocation,
					new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					EmployeeMasterDetails obj = new EmployeeMasterDetails();
					obj.setEmpCode(cursor.getString(0));
					obj.setEmpName(cursor.getString(1));
					employeeMasterDetailsList.add(obj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return employeeMasterDetailsList;
	}

	public ArrayList<EmployeeMasterDetails> getEmpForStockOut() {
		Cursor cursor=null;
		ArrayList<EmployeeMasterDetails> detailList = new ArrayList<EmployeeMasterDetails>();

		try {
			cursor = database.rawQuery("select * FROM emp_master",
					new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					EmployeeMasterDetails detailsObj = new EmployeeMasterDetails();
					detailsObj.setEmpCode(cursor.getString(0));
					detailsObj.setEmpName(cursor.getString(1));
					detailsObj.setSaleAccess(cursor.getString(2));
					detailsObj.setReportingTo(cursor.getString(3));
					detailsObj.setLevel(cursor.getString(4));
					detailsObj.setDesignation(cursor.getString(5));
					detailsObj.setVerticalValue(cursor.getString(6));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}

	public EmployeeMasterDetails getEmpHierarchyDetails(String empCode) {
		EmployeeMasterDetails detailsObj = new EmployeeMasterDetails();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery(
					"SELECT * FROM emp_master WHERE emp_code = '" + empCode
							+ "'", new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				detailsObj.setEmpCode(cursor.getString(0));
				detailsObj.setEmpName(cursor.getString(1));
				detailsObj.setSaleAccess(cursor.getString(2));
				detailsObj.setReportingTo(cursor.getString(3));
				detailsObj.setLevel(cursor.getString(4));
				detailsObj.setDesignation(cursor.getString(5));
				detailsObj.setVerticalValue(cursor.getString(6));
				cursor.close();
				return detailsObj;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return null;
	}

	public String getVerticalValueOfLoggedInEmployee()
	{
		String vertical_value="";
		Cursor cursor=null;
		try
		{
			cursor = database.rawQuery(
					"SELECT vertical_value FROM emp_master WHERE emp_code = '" + get_emp_or_customer_code(mContext)
							+ "'", new String[] {});
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				vertical_value=cursor.getString(0);
				cursor.close();
			}
		}
		catch (Exception e)
		{
			print_Log_d(e.getMessage());
			vertical_value="";
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return vertical_value;
	}

	public String getPendingQuantityOfCustomer(String customerCode)
	{
		String vertical_value="";
		Cursor cursor=null;
		try
		{
			cursor = database.rawQuery(
					"SELECT pending_qty FROM customer_master WHERE customer_code = '" + customerCode + "'", new String[] {});
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				vertical_value=cursor.getString(0);
				cursor.close();
			}
		}
		catch (Exception e)
		{
			print_Log_d(e.getMessage());
			vertical_value="";
		}
		finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return vertical_value;
	}

	public ArrayList<VendorDetails> getVendorForStockOut() {
		ArrayList<VendorDetails> detailList = new ArrayList<VendorDetails>();
		Cursor cursor=null;
		try {
			cursor = database.rawQuery("select * FROM vendor_master",
					new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					VendorDetails detailsObj = new VendorDetails();
					detailsObj.setVendorCode(cursor.getString(0));
					detailsObj.setVendorName(cursor.getString(1));
					detailList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
				return detailList;
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return detailList;
	}


	public ArrayList<GITDetails> getGITDetailsList(String code) {
		ArrayList<GITDetails> grnList = new ArrayList<GITDetails>();
		String sqlQuery = "";
		if (Constants.orderFormDetailsObj.getBranchRDSTransfer()
				.equalsIgnoreCase("yes"))
		{
			sqlQuery = "SELECT DISTINCT Gt.grn_no,RD.rds_name FROM goods_in_transit GT,rds_master RD WHERE GT.status = '0' AND SUBSTR(GT.grn_no,2,5) = RD.emp_code AND GT.despatcher_code = '"
					+ code
					+ "' AND (GT.despatch_qty - (SELECT SUM(GT.bal_rec_qty) FROM goods_in_transit GT GROUP BY GT.prod_code,GT.grn_no) > 0)";
		}
		else
		{
			sqlQuery = "SELECT DISTINCT Gt.grn_no,BM.branch_name FROM goods_in_transit GT,branch_master BM WHERE GT.status = '0' AND SUBSTR(GT.grn_no,2,5) = BM.branch_code AND GT.despatcher_code = '"
					+ code
					+ "' AND (GT.despatch_qty - (SELECT SUM(GT.bal_rec_qty) FROM goods_in_transit GT GROUP BY GT.prod_code,GT.grn_no) > 0)";
		}
		try {
			Cursor cursor = database.rawQuery(sqlQuery, new String[] {});
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					GITDetails detailsObj = new GITDetails();
					detailsObj.setGrnNo(cursor.getString(0));
					detailsObj.setDespatcherName(cursor.getString(1));
					grnList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		}
		return grnList;
	}

	public ArrayList<GITDetails> getPendingGITDetailsList()
	{
		ArrayList<GITDetails> grnList = new ArrayList<GITDetails>();
		String sqlQuery = "";
		Cursor cursor=null;
		if (Constants.orderFormDetailsObj.getBranchRDSTransfer().equalsIgnoreCase("yes"))
		{
			sqlQuery = "SELECT DISTINCT Gt.grn_no,Gt.despatcher_code,RD.rds_name FROM goods_in_transit GT,rds_master RD WHERE GT.status = '0' AND SUBSTR(GT.grn_no,2,5) = RD.emp_code";
		}
		else
		{
			sqlQuery = "SELECT DISTINCT Gt.grn_no,BM.branch_name FROM goods_in_transit GT,branch_master BM WHERE GT.status = '0' AND SUBSTR(GT.grn_no,2,5) = BM.branch_code";
		}
		try
		{
			cursor = database.rawQuery(sqlQuery, new String[] {});
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					GITDetails detailsObj = new GITDetails();
					detailsObj.setGrnNo(cursor.getString(0));
					detailsObj.setDespatcherCode(cursor.getString(1));
					detailsObj.setDespatcherName(cursor.getString(2));
					grnList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
		} catch (Exception e) {
			print_Log_d(e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return grnList;
	}

	/*
	 * ****************************************** RETRIEVING DATA FROM MASTER
	 * TABLE ENDS****************************************
	 */

	/*
	 * ****************************************** RETRIEVING DATA FROM MASTER
	 * TABLE FOR TRANSACTIONS****************************************
	 */

	/*
	 * ORDER FORM TRANACTION
	 */
	public ArrayList<String> getFilterList() {
		ArrayList<String> filterList = new ArrayList<String>();
		Cursor cursor=null;
		try {
			String selectQuery = "SELECT * FROM product_details";
			cursor = database.rawQuery(selectQuery, null);
			cursor.moveToFirst();
			filterList.add(cursor.getString(2));
			for (int i = 0; i < 4; i++) {
				if (cursor.getString(3 + i).length() > 0) {
					filterList.add(cursor.getString(3 + i));
				} else {
					filterList.add("NA");
				}
			}
			cursor.close();
		} catch (Exception e) {
			print_Log_d("Exception ::::::::" + e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return filterList;
	}
	public ArrayList<SelfAppraisalDetailsCustomerWise> getTargetForAllMonths(String tableName, String customer_code)
	{

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

	public SelfAppraisalDetails getTargetAchievementSetupDetails()
	{
		SelfAppraisalDetails SelfAppraisalDetails=new SelfAppraisalDetails();
		Cursor cursor=null;
		try
		{
			String selectQuery = "SELECT * FROM self_appraisal_details";
			cursor = database.rawQuery(selectQuery, null);
			cursor.moveToFirst();
			SelfAppraisalDetails.setSelfAppraisalId(cursor.getString(0));
			SelfAppraisalDetails.setUserId(cursor.getString(1));
			SelfAppraisalDetails.setMultipleTargetAchievement(cursor.getString(2));
			SelfAppraisalDetails.setMultipleTargetAchievementVal(cursor.getString(3));
			SelfAppraisalDetails.setVolumeWise(cursor.getString(4));
			SelfAppraisalDetails.setValueWise(cursor.getString(5));
			SelfAppraisalDetails.setProductGroupWise(cursor.getString(6));
			SelfAppraisalDetails.setProductSubGroupWise(cursor.getString(7));
			SelfAppraisalDetails.setProductBrandWise(cursor.getString(8));
			SelfAppraisalDetails.setProductWise(cursor.getString(9));
			SelfAppraisalDetails.setEmployeeWise(cursor.getString(10));
			SelfAppraisalDetails.setCustomerWise(cursor.getString(11));
			SelfAppraisalDetails.setBranchWise(cursor.getString(12));
			SelfAppraisalDetails.setHQWise(cursor.getString(13));
			SelfAppraisalDetails.setRouteWise(cursor.getString(14));
			SelfAppraisalDetails.setOnTotal(cursor.getString(15));
			SelfAppraisalDetails.setOnIndividual(cursor.getString(16));
			SelfAppraisalDetails.setMonthWise(cursor.getString(17));
			SelfAppraisalDetails.setWeekWise(cursor.getString(18));
			SelfAppraisalDetails.setDayWise(cursor.getString(19));
			SelfAppraisalDetails.setUomVal(cursor.getString(20));


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


		return SelfAppraisalDetails;
	}
	public ArrayList<SelfAppraisalDetailsCustomerWise> getCustomerWiseTargetForSingleMonth(String month)
	{
		ArrayList<SelfAppraisalDetailsCustomerWise> targetList = new ArrayList<>();

		Cursor cursor=null;
		try {

			String selectQuery = "SELECT * FROM self_appraisal_customer_wise WHERE month='" + month + "' AND (target <> '0' OR achievement <> '0')";
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					SelfAppraisalDetailsCustomerWise targetListItem = new SelfAppraisalDetailsCustomerWise();
					targetListItem.setcutomerCode(cursor.getString(0));
					targetListItem.setcustomerName(cursor.getString(1));
					targetListItem.setempCode(cursor.getString(2));
					targetListItem.setmonth(cursor.getString(3));
					String target = cursor.getString(4);
					target=target.trim();
					if (target == null || target.matches("") || target.matches("null"))
					{
						target = "0";
					}
					targetListItem.settarget(target);
					String achievement = cursor.getString(5);
					achievement=achievement.trim();
					if (achievement == null || achievement.matches("") || achievement.matches("null"))
					{
						achievement = "0";
					}
					targetListItem.setachievement(achievement);
					targetList.add(targetListItem);
					cursor.moveToNext();
				}
				cursor.close();
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


		return targetList;
	}
	public ArrayList<SelfAppraisalDetailsBranchWise> getBranchWiseTargetForSingleMonth(String month)
	{
		ArrayList<SelfAppraisalDetailsBranchWise> targetList = new ArrayList<>();

		Cursor cursor=null;
		try {

			String selectQuery = "SELECT * FROM self_appraisal_branch_wise WHERE month='" + month + "' AND (target <> '0' OR achievement <> '0')";
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					SelfAppraisalDetailsBranchWise targetListItem = new SelfAppraisalDetailsBranchWise();
					targetListItem.setbranchCode(cursor.getString(0));
					targetListItem.setbranchName(cursor.getString(1));
					targetListItem.setempCode(cursor.getString(2));
					targetListItem.setmonth(cursor.getString(3));
					String target = cursor.getString(4);
					target=target.trim();
					if (target == null || target.matches("") || target.matches("null"))
					{
						target = "0";
					}
					targetListItem.settarget(target);
					String achievement = cursor.getString(5);
					achievement=achievement.trim();
					if (achievement == null || achievement.matches("") || achievement.matches("null"))
					{
						achievement = "0";
					}
					targetListItem.setachievement(achievement);
					targetList.add(targetListItem);
					cursor.moveToNext();
				}
				cursor.close();
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


		return targetList;
	}
	public ArrayList<SelfAppraisalDetailsProductGroupWise> getProductGroupWiseTargetForSingleMonth(String curryerPrevious, String customer_code)
	{
		ArrayList<SelfAppraisalDetailsProductGroupWise> targetList = new ArrayList<>();
//		Cursor cursor = null;

		try {

			String mValue = "04, 05, 06, 07, 08, 09, 10, 11, 12, 01, 02, 03";
			String[] mArrayValue = mValue.split(",");

			String mData = "April, May, June, July, August, September, October, November, December, January, February, March";
			String[] mArray = mData.split(",");

			for(int index=0;index<12;index++)
			{
//				String currentMonth=index+"";
//					if(currentMonth.length()<2)
//					{
//						currentMonth="0"+currentMonth;
//					}

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
//				targetListItem.setproductGroupCode(cursor.getString(0));
//				targetListItem.setproductGroupName(cursor.getString(1)); //amitabha2715
//				targetListItem.setempCode(cursor.getString(2));
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
//				cursor.moveToNext();
				cursor.close();
			}



		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		finally
		{
//			if(cursor!=null)
//			{
//				cursor.close();
//			}
		}


		return targetList;
	}
	public String getTotalByKey(String currentPrev, String targetAchiv, String customer_code)
	{
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
//			if(cursor!=null)
//			{
//				cursor.close();
//			}
		}


		return returnValue;
	}

	public ArrayList<ProductMasterDetails> getProductMasterList(String parent,
																int filterNo, boolean carryInSales)
	{

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

	public ArrayList<ProductMasterDetails> getProductMasterListStockAudit(String parent,
																		  int filterNo, boolean carryInSales)
	{
		String focusedProductSuffix="",groupBySuffix="";
		if(Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes"))
		{
			focusedProductSuffix=" ORDER BY PM.focus DESC";
		}
		if(Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes"))
		{
			groupBySuffix=" GROUP BY  PM.dns_prod_code ";
		}

		ArrayList<ProductMasterDetails> productMasterList = new ArrayList<ProductMasterDetails>();
		String selectQuery = "";
		if (!carryInSales) {
			switch (filterNo)
			{
				case 1:
					if (Constants.orderFormDetailsObj.getClosingStk()
							.equalsIgnoreCase("yes"))
					{
						if ((Constants.orderFormDetailsObj.getMrp()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getMrpDrpdwn().equalsIgnoreCase("dropdown"))
								|| ((Constants.orderFormDetailsObj.getSaleRate()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getSaleRateDrpdwn().equalsIgnoreCase(
										"dropdown"))))
						{
							//AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'";
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS ,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS ,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N'"+groupBySuffix+focusedProductSuffix;
//							}

						}
						else
						{
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N'"+groupBySuffix+focusedProductSuffix;
//							}
						}
					}
					else
					{
						if ((Constants.orderFormDetailsObj.getMrp()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getMrpDrpdwn().equalsIgnoreCase("dropdown"))
								|| ((Constants.orderFormDetailsObj.getSaleRate()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getSaleRateDrpdwn().equalsIgnoreCase(
										"dropdown")))) {
							if(Constants.menuDetailsObj.getOrder().equalsIgnoreCase("no")){
//								if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes"))
//								{
//									selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//								}
//								else
//								{
								selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N'"+groupBySuffix+focusedProductSuffix;
//								}
							}
							else
							{
//								if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes"))
//								{
//									selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//
//								}
//								else
//								{
								selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N'"+groupBySuffix+focusedProductSuffix;
//								}
							}
						}
						else
						{
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes"))
//							{
//								selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}
//							else
//							{
							selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N'"+groupBySuffix+focusedProductSuffix;
//							}
						}
					}
					break;
				case 2:
					if (Constants.orderFormDetailsObj.getClosingStk()
							.equalsIgnoreCase("yes"))
					{
						if ((Constants.orderFormDetailsObj.getMrp()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getMrpDrpdwn().equalsIgnoreCase("dropdown"))
								|| ((Constants.orderFormDetailsObj.getSaleRate()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getSaleRateDrpdwn().equalsIgnoreCase(
										"dropdown"))))
						{

//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS ,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
//										+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS ,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
									+ parent.replace("'", "\'") + "'"+groupBySuffix+focusedProductSuffix;
//							}

						}
						else
						{
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
//										+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
									+ parent.replace("'", "\'") + "'"+groupBySuffix+focusedProductSuffix;
//							}

						}
					} else {
						if ((Constants.orderFormDetailsObj.getMrp()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getMrpDrpdwn().equalsIgnoreCase("dropdown"))
								|| ((Constants.orderFormDetailsObj.getSaleRate()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getSaleRateDrpdwn().equalsIgnoreCase(
										"dropdown")))) {

//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
//										+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
									+ parent.replace("'", "\'") + "'"+groupBySuffix+focusedProductSuffix;
//							}
						} else {
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
//										+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
									+ parent.replace("'", "\'") + "'"+groupBySuffix+focusedProductSuffix;
//							}
						}
					}
					break;
				case 3:
					if (Constants.orderFormDetailsObj.getClosingStk()
							.equalsIgnoreCase("yes")) {
						if ((Constants.orderFormDetailsObj.getMrp()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getMrpDrpdwn().equalsIgnoreCase("dropdown"))
								|| ((Constants.orderFormDetailsObj.getSaleRate()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getSaleRateDrpdwn().equalsIgnoreCase(
										"dropdown")))) {
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS ,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
//										+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS ,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
									+ parent.replace("'", "\'") + "'"+groupBySuffix+focusedProductSuffix;
//							}


						} else {
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
//										+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
									+ parent.replace("'", "\'") + "'"+groupBySuffix+focusedProductSuffix;
//							}

						}
					} else {
						if ((Constants.orderFormDetailsObj.getMrp()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getMrpDrpdwn().equalsIgnoreCase("dropdown"))
								|| ((Constants.orderFormDetailsObj.getSaleRate()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getSaleRateDrpdwn().equalsIgnoreCase(
										"dropdown")))) {
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
//										+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
									+ parent.replace("'", "\'") + "'"+groupBySuffix+focusedProductSuffix;
//							}

						} else {
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
//										+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
									+ parent.replace("'", "\'") + "'"+groupBySuffix+focusedProductSuffix;
//							}

						}
					}
					break;
				case 4:
					if (Constants.orderFormDetailsObj.getClosingStk()
							.equalsIgnoreCase("yes")) {
						if ((Constants.orderFormDetailsObj.getMrp()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getMrpDrpdwn().equalsIgnoreCase("dropdown"))
								|| ((Constants.orderFormDetailsObj.getSaleRate()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getSaleRateDrpdwn().equalsIgnoreCase(
										"dropdown")))) {
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS ,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_brand_code='"
//										+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS ,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_brand_code='"
									+ parent.replace("'", "\'") + "'"+groupBySuffix+focusedProductSuffix;
//							}

						} else {
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_brand_code='"
//										+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_brand_code='"
									+ parent.replace("'", "\'") + "'"+groupBySuffix+focusedProductSuffix;
//							}

						}
					} else {
						if ((Constants.orderFormDetailsObj.getMrp()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getMrpDrpdwn().equalsIgnoreCase("dropdown"))
								|| ((Constants.orderFormDetailsObj.getSaleRate()
								.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
								.getSaleRateDrpdwn().equalsIgnoreCase(
										"dropdown")))) {
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_brand_code='"
//										+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_brand_code='"
									+ parent.replace("'", "\'") + "'"+groupBySuffix+focusedProductSuffix;
//							}

						} else {
//							if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//								selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_brand_code='"
//										+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+focusedProductSuffix;
//							}else{
							selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_brand_code='"
									+ parent.replace("'", "\'") + "'"+groupBySuffix+focusedProductSuffix;
//							}

						}
					}
					break;
			}
		} else {
			switch (filterNo) {
				case 1:
					if ((Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase(
							"yes") && Constants.orderFormDetailsObj.getMrpDrpdwn()
							.equalsIgnoreCase("dropdown"))
							|| ((Constants.orderFormDetailsObj.getSaleRate()
							.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
							.getSaleRateDrpdwn().equalsIgnoreCase(
									"dropdown")))) {
						selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS ,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND CS.cl_stk > '0.00'"+groupBySuffix+focusedProductSuffix;
					} else {
						selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND CS.cl_stk > '0.00'"+groupBySuffix+focusedProductSuffix;
					}
					break;
				case 2:
					if ((Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase(
							"yes") && Constants.orderFormDetailsObj.getMrpDrpdwn()
							.equalsIgnoreCase("dropdown"))
							|| ((Constants.orderFormDetailsObj.getSaleRate()
							.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
							.getSaleRateDrpdwn().equalsIgnoreCase(
									"dropdown")))) {
						selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS ,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
								+ parent.replace("'", "\'")
								+ "' AND CS.cl_stk > '0.00'"+groupBySuffix+focusedProductSuffix;
					} else {
						selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
								+ parent.replace("'", "\'")
								+ "' AND CS.cl_stk > '0.00'"+groupBySuffix+focusedProductSuffix;
					}
					break;
				case 3:
					if ((Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase(
							"yes") && Constants.orderFormDetailsObj.getMrpDrpdwn()
							.equalsIgnoreCase("dropdown"))
							|| ((Constants.orderFormDetailsObj.getSaleRate()
							.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
							.getSaleRateDrpdwn().equalsIgnoreCase(
									"dropdown")))) {
						selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS ,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
								+ parent.replace("'", "\'")
								+ "' AND CS.cl_stk > '0.00'"+groupBySuffix+focusedProductSuffix;
					} else {
						selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
								+ parent.replace("'", "\'")
								+ "' AND CS.cl_stk > '0.00'"+groupBySuffix+focusedProductSuffix;
					}
					break;
				case 4:
					if ((Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase(
							"yes") && Constants.orderFormDetailsObj.getMrpDrpdwn()
							.equalsIgnoreCase("dropdown"))
							|| ((Constants.orderFormDetailsObj.getSaleRate()
							.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
							.getSaleRateDrpdwn().equalsIgnoreCase(
									"dropdown")))) {
						selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS ,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' PM.product_brand_code='"
								+ parent.replace("'", "\'")
								+ "' CS.cl_stk != '0.00'"+groupBySuffix+focusedProductSuffix;
					} else {
						selectQuery = "SELECT  DISTINCT PM.*,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N' PM.product_brand_code='"
								+ parent.replace("'", "\'")
								+ "' CS.cl_stk != '0.00'"+groupBySuffix+focusedProductSuffix;
					}
					break;
			}
		}
		Cursor cursor=null;
		try {
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int i = 0; i < cursor.getCount(); i++)
				{
					Boolean isRateOk=true;
					ProductMasterDetails prodObj = new ProductMasterDetails();
					prodObj.setProdCode(cursor.getString(0));
					prodObj.setGrpCode(cursor.getString(1));
					prodObj.setGrpName(cursor.getString(2));
					prodObj.setSubGrpCode(cursor.getString(3));
					prodObj.setSubGrpName(cursor.getString(4));
					prodObj.setBrndCode(cursor.getString(5));
					prodObj.setBrndName(cursor.getString(6));
					prodObj.setDesc(cursor.getString(7));
					prodObj.setIsBlkLst(cursor.getString(8));
					prodObj.setIsAcedns(cursor.getString(9));
					prodObj.setUom1(cursor.getString(11));
					prodObj.setUom2(cursor.getString(12));
					prodObj.setConversionFactor(cursor.getString(13));
					prodObj.setPackSize(cursor.getString(14));
					prodObj.setUOM3(cursor.getString(15));
					prodObj.setConversionFactorTwo(cursor.getString(16));
					prodObj.setTD(cursor.getString(17));
					prodObj.setBranchCode(cursor.getString(18));
					prodObj.setVerticalValue(cursor.getString(19));
					prodObj.setSecondaryUnit(cursor.getString(20));
					prodObj.setDnsProdCode(cursor.getString(21));
					prodObj.setFocus(cursor.getString(22));
					prodObj.setWeightage(cursor.getString(23));
					prodObj.setVatRate(cursor.getString(24));
					prodObj.setAdditionalVatRate(cursor.getString(25));
					prodObj.setFreightCost(cursor.getString(26));
					prodObj.setClosingStk(cursor.getString(27));

					isRateOk = saleRateOrMrpValidationProcess(cursor, isRateOk);
					if(isRateOk)
					{
						productMasterList.add(prodObj);
					}

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

	private Boolean saleRateOrMrpValidationProcess(Cursor cursor, Boolean isRateOk) {
		if(Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes"))
		{
			ArrayList<MRPDetails> mrpSpinnerList = getMRPList(cursor.getString(0), "");
			if(mrpSpinnerList.size()>0)
			{
				isRateOk= Utils.checkIfRateIsProper(mrpSpinnerList.get(0).getMrpValue());
			}
			else
			{
				isRateOk=false;
			}
		}
		else if(Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")&& Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown"))
		{
			ArrayList<MRPDetails> mrpSpinnerList = getMRPList(cursor.getString(0), "");
			if(mrpSpinnerList.size()>0)
			{
//                String customerType=Constants.selectedCustomer.getCustomerType();
				String customerType="D"; //amitabha2715
				if(Constants.productDetailsObj.getMultipleRate().equalsIgnoreCase("yes"))
				{
					if(customerType.matches("R") || customerType.matches(""))
					{
						isRateOk=Utils.checkIfRateIsProper(mrpSpinnerList.get(0).getSaleRate());
					}

					else if(customerType.matches("D"))
					{
						isRateOk=Utils.checkIfRateIsProper(mrpSpinnerList.get(0).getDistributorRate());
					}
					else if(customerType.matches("WS"))
					{
						isRateOk=Utils.checkIfRateIsProper(mrpSpinnerList.get(0).getWSRate());
					}
					else if(customerType.matches("SS"))
					{
						isRateOk=Utils.checkIfRateIsProper(mrpSpinnerList.get(0).getSSRate());
					}
					else if(customerType.matches("DEPOT"))
					{
						isRateOk=Utils.checkIfRateIsProper(mrpSpinnerList.get(0).getDepotRate());
					}
				}
				else
				{
					isRateOk=Utils.checkIfRateIsProper(mrpSpinnerList.get(0).getSaleRate());
				}
			}
			else
			{
				isRateOk=false;
			}
		}
		return isRateOk;
	}

	public ArrayList<ProductMasterDetails> getSpecialProductMasterSizeListIgnoringStock(String productgroupcode,String dnsprodcode) {
		ArrayList<ProductMasterDetails> productMasterList = new ArrayList<ProductMasterDetails>();
		Cursor cursor=null;
		try {
			String selectQuery = "SELECT prod_code ,uom1 ,dns_prod_code,SUBSTR(dns_prod_code,(LENGTH(dns_prod_code)-2),3) FROM product_master  WHERE acedns='Y' AND black_list='N' AND product_group_code='"+productgroupcode+"' AND SUBSTR(dns_prod_code,1,(LENGTH(dns_prod_code)-3))='"+dnsprodcode+"' ";
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int i = 0; i < cursor.getCount(); i++) {
					ProductMasterDetails prodObj = new ProductMasterDetails();
					prodObj.setProdCode(cursor.getString(0));
					prodObj.setUom1(cursor.getString(1));
					prodObj.setDnsProdCode(cursor.getString(2));
					prodObj.setPackSize(cursor.getString(3));
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



	public ArrayList<ProductMasterDetails> getSpecialProductMasterListIgnoringStock(String parent) {
		ArrayList<ProductMasterDetails> productMasterList = new ArrayList<ProductMasterDetails>();
		Cursor cursor=null;
		try {
			String selectQuery = "SELECT prod_code ,product_group_code ,product_group_name ,product_sub_group_code ,product_sub_group_name ,product_brand_code ,product_brand_name , prod_desc ,black_list ,acedns ,cl_stk ,uom1 ,uom2 ,conversion_factor ,pack_size ,uom3 ,conversion_factor_two ,TD ,branch_code ,vertical_value ,secondary_unit ,SUBSTR(dns_prod_code,1,(LENGTH(dns_prod_code)-3)),COUNT(dns_prod_code) FROM product_master  WHERE acedns='Y' AND black_list='N' AND product_group_code='"+parent+"' GROUP BY SUBSTR(dns_prod_code,1,(LENGTH(dns_prod_code)-3))";
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int i = 0; i < cursor.getCount(); i++) {
					ProductMasterDetails prodObj = new ProductMasterDetails();
					prodObj.setProdCode(cursor.getString(0));
					prodObj.setGrpCode(cursor.getString(1));
					prodObj.setGrpName(cursor.getString(2));
					prodObj.setSubGrpCode(cursor.getString(3));
					prodObj.setSubGrpName(cursor.getString(4));
					prodObj.setBrndCode(cursor.getString(5));
					prodObj.setBrndName(cursor.getString(6));
					prodObj.setDesc(cursor.getString(7));
					prodObj.setIsBlkLst(cursor.getString(8));
					prodObj.setIsAcedns(cursor.getString(9));
					prodObj.setUom1(cursor.getString(11));
					prodObj.setUom2(cursor.getString(12));
					prodObj.setConversionFactor(cursor.getString(13));
					prodObj.setUOM3(cursor.getString(15));
					prodObj.setConversionFactorTwo(cursor.getString(16));
					prodObj.setTD(cursor.getString(17));
					prodObj.setSecondaryUnit(cursor.getString(20));
					prodObj.setDnsProdCode(cursor.getString(21));
					prodObj.setPackSize(cursor.getString(22));

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

	public ArrayList<ProductMasterDetails> getProductMasterListAlternateDesign(String parent)
	{
		String focusedProductSuffix="",grpBySuffix="";
		ArrayList<ProductMasterDetails> productMasterList = new ArrayList<ProductMasterDetails>();
		if(Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes"))
		{
			focusedProductSuffix=" ORDER BY PM.focus DESC";
		}
		if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes") && MenuAccess("sauda"))
		{
			grpBySuffix=" GROUP BY PM.dns_prod_code ";
		}
		String selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
				+ parent.replace("'", "\'") + "'"+grpBySuffix+focusedProductSuffix;
		Cursor cursor=null;
		try
		{
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int i = 0; i < cursor.getCount(); i++)
				{
					Boolean isRateOk=true;
					ProductMasterDetails prodObj = new ProductMasterDetails();
					prodObj.setProdCode(cursor.getString(0));
					prodObj.setGrpCode(cursor.getString(1));
					prodObj.setGrpName(cursor.getString(2));
					prodObj.setSubGrpCode(cursor.getString(3));
					prodObj.setSubGrpName(cursor.getString(4));
					prodObj.setBrndCode(cursor.getString(5));
					prodObj.setBrndName(cursor.getString(6));
					prodObj.setDesc(cursor.getString(7));
					prodObj.setIsBlkLst(cursor.getString(8));
					prodObj.setIsAcedns(cursor.getString(9));
					prodObj.setUom1(cursor.getString(11));
					prodObj.setUom2(cursor.getString(12));
					prodObj.setConversionFactor(cursor.getString(13));
					prodObj.setPackSize(cursor.getString(14));
					prodObj.setUOM3(cursor.getString(15));
					prodObj.setConversionFactorTwo(cursor.getString(16));
					prodObj.setTD(cursor.getString(17));
					prodObj.setSecondaryUnit(cursor.getString(20));
					prodObj.setFocus(cursor.getString(22));
					prodObj.setClosingStk(cursor.getString(25));
					isRateOk = saleRateOrMrpValidationProcess(cursor, isRateOk);
					if(isRateOk)
					{
						productMasterList.add(prodObj);
					}
					cursor.moveToNext();
				}
			}
			cursor.close();
		}
		catch (Exception e)
		{
			print_Log_d("Exception :::::::::" + e.getMessage());
		} finally
		{
			if(cursor!=null)
			{
				cursor.close();
			}
		}
		return productMasterList;
	}

	public ArrayList<ProductMasterDetails> getProductMasterListIgnoringStock(String parent, int filterNo)
	{
		String focusedProductSuffix="",grpBySuffix="";
		if(Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes"))
		{
			focusedProductSuffix=" ORDER BY PM.focus DESC";
		}
		if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes") && MenuAccess("sauda"))
		{
			grpBySuffix=" GROUP BY PM.dns_prod_code ";
		}

		ArrayList<ProductMasterDetails> productMasterList = new ArrayList<ProductMasterDetails>();
		String selectQuery = "";
		switch (filterNo) {
			case 1:
				if ((mOrderPriceValidationType.matches("SPA")) || (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
						.getMrpDrpdwn().equalsIgnoreCase("dropdown")) || ((Constants.orderFormDetailsObj.getSaleRate()
						.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown"))))
				{
					if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes"))
					{
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+grpBySuffix+focusedProductSuffix;
					}
					else
					{
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N'"+grpBySuffix+focusedProductSuffix;
					}

				}
				else
				{
					if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes") && !Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes"))
					{
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+grpBySuffix+focusedProductSuffix;
					}
					else
					{
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N'"+grpBySuffix+focusedProductSuffix;
					}
				}
				break;
			case 2:
				if ((mOrderPriceValidationType.matches("SPA")) || (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getMrpDrpdwn().equalsIgnoreCase("dropdown"))
						|| ((Constants.orderFormDetailsObj.getSaleRate()
						.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
						.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")))) {

					if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
								+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+grpBySuffix+focusedProductSuffix;
					}else{

						if(Constants.productDetailsObj.getDestinationOrderTypePriceList().equalsIgnoreCase("yes")){
							selectQuery= "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"+ parent.replace("'", "\'") + "' AND MP.destination_code='"+Constants.mDestinationCode+"' AND MP.order_type='"+Constants.mOrderType+"'"+grpBySuffix+focusedProductSuffix;

						}else{
							selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
									+ parent.replace("'", "\'") + "'"+grpBySuffix+focusedProductSuffix;
						}
					}

				} else
				{

					if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
								+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+grpBySuffix+focusedProductSuffix;
					}else{
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_group_code='"
								+ parent.replace("'", "\'") + "'"+grpBySuffix+focusedProductSuffix;
					}
				}
				break;
			case 3:
				if ((mOrderPriceValidationType.matches("SPA")) || (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
						.getMrpDrpdwn().equalsIgnoreCase("dropdown"))
						|| ((Constants.orderFormDetailsObj.getSaleRate()
						.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
						.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")))) {

					if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
								+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+grpBySuffix+focusedProductSuffix;
					}else{
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
								+ parent.replace("'", "\'") + "'"+grpBySuffix+focusedProductSuffix;
					}

				} else {
					if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
								+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+grpBySuffix+focusedProductSuffix;
					}else{
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_sub_group_code='"
								+ parent.replace("'", "\'") + "'"+grpBySuffix+focusedProductSuffix;
					}
				}
				break;
			case 4:
				if ((mOrderPriceValidationType.matches("SPA")) || (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
						.getMrpDrpdwn().equalsIgnoreCase("dropdown"))
						|| ((Constants.orderFormDetailsObj.getSaleRate()
						.equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
						.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")))) {

					if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_brand_code='"
								+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+grpBySuffix+focusedProductSuffix;
					}else{
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM,mrp MP WHERE PM.prod_code = MP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_brand_code='"
								+ parent.replace("'", "\'") + "'"+grpBySuffix+focusedProductSuffix;
					}

				} else {
					if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_brand_code='"
								+ parent.replace("'", "\'") + "' AND PM.branch_code='"+Constants.selectedBranch.getBranchCode()+"'"+grpBySuffix+focusedProductSuffix;
					}else{
						selectQuery = "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' AND PM.product_brand_code='"
								+ parent.replace("'", "\'") + "'"+grpBySuffix+focusedProductSuffix;
					}

				}
				break;
		}
		Cursor cursor=null;
		try {
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int i = 0; i < cursor.getCount(); i++)
				{
					Boolean isRateOk=true;
					ProductMasterDetails prodObj = new ProductMasterDetails();
					prodObj.setProdCode(cursor.getString(0));
					prodObj.setGrpCode(cursor.getString(1));
					prodObj.setGrpName(cursor.getString(2));
					prodObj.setSubGrpCode(cursor.getString(3));
					prodObj.setSubGrpName(cursor.getString(4));
					prodObj.setBrndCode(cursor.getString(5));
					prodObj.setBrndName(cursor.getString(6));
					prodObj.setDesc(cursor.getString(7));
					prodObj.setIsBlkLst(cursor.getString(8));
					prodObj.setIsAcedns(cursor.getString(9));
					prodObj.setUom1(cursor.getString(11));
					prodObj.setUom2(cursor.getString(12));
					prodObj.setConversionFactor(cursor.getString(13));
					prodObj.setPackSize(cursor.getString(14));
					prodObj.setUOM3(cursor.getString(15));
					prodObj.setConversionFactorTwo(cursor.getString(16));
					prodObj.setTD(cursor.getString(17));
					prodObj.setSecondaryUnit(cursor.getString(20));
					prodObj.setFocus(cursor.getString(22));
					prodObj.setClosingStk(cursor.getString(25));
					isRateOk = saleRateOrMrpValidationProcess(cursor, isRateOk);
					if(isRateOk)
					{
						productMasterList.add(prodObj);
					}
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


	public ArrayList<MRPDetails> getMRPList(String productCode,	String selectedUOM) {
		ArrayList<MRPDetails> mrpList = new ArrayList<MRPDetails>();
		Cursor cursor=null;
		try {
			String selectQuery = "";

			if(Constants.productDetailsObj.getDestinationOrderTypePriceList().equalsIgnoreCase("yes")){
				if (selectedUOM.length() == 0) {
					selectQuery = "SELECT * FROM mrp where sku_code='"+ productCode.replace("'", "\'") + "' AND destination_code='"+Constants.mDestinationCode+"' AND order_type='"+Constants.mOrderType+"' AND acedns='Y'";
				} else {
					selectQuery = "SELECT * FROM mrp where sku_code='"+ productCode.replace("'", "\'") + "' AND UOM = '"+ selectedUOM + "' AND destination_code='"+Constants.mDestinationCode+"' AND order_type='"+Constants.mOrderType+"' AND acedns='Y'";
				}
			}else{
				if (selectedUOM.length() == 0) {
					selectQuery = "SELECT * FROM mrp where sku_code='"+ productCode.replace("'", "\'") + "' AND acedns='Y'";
				} else {
					selectQuery = "SELECT * FROM mrp where sku_code='"+ productCode.replace("'", "\'") + "' AND UOM = '"+ selectedUOM + "' AND acedns='Y'";
				}
			}
			cursor = database.rawQuery(selectQuery, null);

			if (cursor.getCount() > 0)
			{

				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					MRPDetails detailsObj = new MRPDetails();
					detailsObj.setProdCode(cursor.getString(0));
					detailsObj.setMrpCode(cursor.getString(1));
					detailsObj.setMrpValue(cursor.getString(2));
					detailsObj.setSaleRate(cursor.getString(3));
					detailsObj.setUom(cursor.getString(4));
					detailsObj.setBranchCode(cursor.getString(5));
					detailsObj.setWSRate(cursor.getString(9));
					detailsObj.setDistributorRate(cursor.getString(10));
					detailsObj.setSSRate(cursor.getString(11));
					detailsObj.setDepotRate(cursor.getString(12));
					mrpList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
			cursor.close();
		} catch (Exception e) {
			print_Log_d("Exception :::::::" + e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return mrpList;
	}
	public ArrayList<MRPDetails> getMRPListForOrder(String productCode,	String selectedUOM,String conversionFactor)
	{
		ArrayList<MRPDetails> mrpList = new ArrayList<MRPDetails>();
		Cursor cursor=null;
		try {
			String selectQuery = "";

			if(Constants.productDetailsObj.getDestinationOrderTypePriceList().equalsIgnoreCase("yes")){
				if (selectedUOM.length() == 0) {
					selectQuery = "SELECT * FROM mrp where sku_code='"+ productCode.replace("'", "\'") + "' AND destination_code='"+Constants.mDestinationCode+"' AND order_type='"+Constants.mOrderType+"' AND acedns='Y'";
				} else {
					selectQuery = "SELECT * FROM mrp where sku_code='"+ productCode.replace("'", "\'") + "' AND UOM = '"+ selectedUOM + "' AND destination_code='"+Constants.mDestinationCode+"' AND order_type='"+Constants.mOrderType+"' AND acedns='Y'";
				}
			}else{
				if (selectedUOM.length() == 0) {
					selectQuery = "SELECT * FROM mrp where sku_code='"+ productCode.replace("'", "\'") + "' AND acedns='Y'";
				} else {
					selectQuery = "SELECT * FROM mrp where sku_code='"+ productCode.replace("'", "\'") + "' AND UOM = '"+ selectedUOM + "' AND acedns='Y'";
				}
			}
			cursor = database.rawQuery(selectQuery, null);
			cursor.moveToFirst();
			if (cursor.getCount() > 0)
			{

				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					MRPDetails detailsObj = new MRPDetails();
					detailsObj.setProdCode(cursor.getString(0));
					detailsObj.setMrpCode(cursor.getString(1));
					String mrpVal = String.valueOf(Double.valueOf(cursor.getString(2))/Double.valueOf(conversionFactor));
					String SaleRate = String.valueOf(Double.valueOf(cursor.getString(3))/Double.valueOf(conversionFactor));
					detailsObj.setMrpValue(mrpVal);
					detailsObj.setSaleRate(SaleRate);
					detailsObj.setUom(cursor.getString(4));
					detailsObj.setBranchCode(cursor.getString(5));
					detailsObj.setWSRate(cursor.getString(9));
					detailsObj.setDistributorRate(cursor.getString(10));
					detailsObj.setSSRate(cursor.getString(11));
					detailsObj.setDepotRate(cursor.getString(12));
					mrpList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
			cursor.close();
		} catch (Exception e) {
			print_Log_d("Exception :::::::" + e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return mrpList;
	}


	public MRPDetails getMRPByProdCode(String productCode)//in case of special pricing, assuimg that for one prod code there will only be one mrp value
	{
		MRPDetails mrpListObj = new MRPDetails();
		Cursor cursor=null;
		try {
			String selectQuery = "";
			selectQuery = "SELECT * FROM mrp where sku_code='"+ productCode.replace("'", "\'") + "' LIMIT 1" ;
			cursor = database.rawQuery(selectQuery, null);

			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();

				mrpListObj.setProdCode(cursor.getString(0));
				mrpListObj.setMrpCode(cursor.getString(1));
				mrpListObj.setMrpValue(cursor.getString(2));
				mrpListObj.setSaleRate(cursor.getString(3));
				mrpListObj.setUom(cursor.getString(4));
				mrpListObj.setBranchCode(cursor.getString(5));
				mrpListObj.setWSRate(cursor.getString(9));
				mrpListObj.setDistributorRate(cursor.getString(10));
				mrpListObj.setSSRate(cursor.getString(11));
				mrpListObj.setDepotRate(cursor.getString(12));

				cursor.close();
			}
			cursor.close();
		} catch (Exception e) {
			print_Log_d("Exception :::::::" + e.getMessage());
		} finally{
			if(cursor!=null){
				cursor.close();
			}
		}
		return mrpListObj;
	}

	/*
	 * ROUTE PLAN TRANSACTION
	 */


	public String getLastVisitData(ProductMasterDetails currentObj) {
		String visitData = "0,0,0";
		String selectQuery = "SELECT visit_details FROM prev_stock_counting_master where customer_code='"
				+ Constants.selectedCustomer.getCustomerCode().replace("'","\'")
				+ "' AND product_code = '"+ currentObj.getProdCode().replace("'", "\'") + "'";
		Cursor cursor = database.rawQuery(selectQuery, null);
		if (cursor.getCount() > 0) {
			cursor.moveToFirst();
			visitData = cursor.getString(0);
		} else {
			visitData = "0,0,0";
		}
		if(cursor !=null){
			cursor.close();
		}
		return visitData;
	}

	public String getTotalOutstandingForMenu(String custCode) {
		String result = "0.00";
		Cursor cursor=null;
		try {
			String selectQuery = "SELECT SUM(due_amount) FROM outstanding_master WHERE customer_code IN("+ custCode +")";
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				result = cursor.getString(0);
				cursor.close();
			} else {
				result = "0.00";
			}
			cursor.close();
		} catch (Exception e) {
			result = "0.00";
			print_log_d("Outstanding Master","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}

		if (result == null || result.length() == 0) {
			result = "0.00";
		}
		return result;
	}

	public ArrayList<MenuOutstandingParent> getOutstandingSummaryDetails(String custCode) {
		ArrayList<MenuOutstandingParent> mrpList = new ArrayList<MenuOutstandingParent>();
		Cursor cursor=null;
		try {
			String selectQuery = "SELECT CM.customer_name,SUM(OM.due_amount),OM.customer_code,COUNT(OM.invoice_id) FROM outstanding_master OM,customer_master CM WHERE OM.customer_code = CM.customer_code AND CM.customer_code IN("+ custCode + ") GROUP BY OM.customer_code";
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					MenuOutstandingParent detailsObj = new MenuOutstandingParent();
					detailsObj.setCustomerName(cursor.getString(0));
					detailsObj.setTotalInvoice(cursor.getString(1));
					detailsObj.setCustomerCode(cursor.getString(2));
					detailsObj.setNumberOfInvoice(cursor.getString(3));
					mrpList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
			cursor.close();
		} catch (Exception e) {
			print_log_d("Outstanding Summery","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return mrpList;
	}

	public ArrayList<MenuOutstandingParent> getAgeingSummaryDetails() {
		ArrayList<MenuOutstandingParent> mrpList = new ArrayList<MenuOutstandingParent>();
		Cursor cursor=null;
		try {
			String selectQuery = "SELECT CM.customer_name,SUM(OM.due_amount),OM.customer_code,COUNT(OM.invoice_id) FROM outstanding_master OM,customer_master CM WHERE OM.customer_code = CM.customer_code GROUP BY OM.customer_code";
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					MenuOutstandingParent detailsObj = new MenuOutstandingParent();
					detailsObj.setCustomerName(cursor.getString(0));
					detailsObj.setTotalInvoice(cursor.getString(1));
					detailsObj.setCustomerCode(cursor.getString(2));
					detailsObj.setNumberOfInvoice(cursor.getString(3));
					mrpList.add(detailsObj);
					cursor.moveToNext();
				}
				cursor.close();
			}
			cursor.close();
		} catch (Exception e) {
			print_log_d("Ageing Summery","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return mrpList;
	}

	public ArrayList<MenuOutstandingChild> getOutstandingDetailsForMenu(ArrayList<MenuOutstandingParent> grpList) {
		ArrayList<MenuOutstandingChild> parentList = new ArrayList<MenuOutstandingChild>();
		try {
			for (int kk = 0; kk < grpList.size(); kk++) {
				Cursor cursor = database.rawQuery("SELECT * FROM outstanding_master where customer_code = ?",new String[] { grpList.get(kk).getCustomerCode() });
				if (cursor.getCount() > 0) {
					MenuOutstandingChild childObj = new MenuOutstandingChild();
					ArrayList<OutstandingDetails> detailList = new ArrayList<OutstandingDetails>();
					cursor.moveToFirst();
					for (int ii = 0; ii < cursor.getCount(); ii++) {
						OutstandingDetails detailsObj = new OutstandingDetails();
						detailsObj.setCustomerCode(cursor.getString(0));
						detailsObj.setRecId(cursor.getString(1));
						detailsObj.setInvoice_id(cursor.getString(2));
						detailsObj.setCustomerName(cursor.getString(3));
						detailsObj.setDate(cursor.getString(4));
						detailsObj.setInvoice_amount(cursor.getString(5));
						detailsObj.setDue_amount(cursor.getString(6));
						detailList.add(detailsObj);
						cursor.moveToNext();
					}
					childObj.setOutstandingList(detailList);
					parentList.add(childObj);
					cursor.close();
				}else{
					if(cursor!=null){
						cursor.close();
					}
				}
			}
		} catch (Exception e) {
			print_log_d("outstanding_master", e.getMessage());
		}
		return parentList;
	}

	public ArrayList<MenuAegingListChild> getAgeingDetailsForMenu(ArrayList<MenuOutstandingParent> parentList, int period1,int period2, int period3) {
		ArrayList<MenuAegingListChild> childList = new ArrayList<MenuAegingListChild>();
		Cursor cursor=null;
		try {
			for (int kk = 0; kk < parentList.size(); kk++) {
				String query = "SELECT recid,invoice_id,due_amount,(SELECT julianday('now') - julianday(date) AS 'Due Days') FROM outstanding_master WHERE customer_code = '"+ parentList.get(kk).getCustomerCode()+ "' ORDER BY recid";
				cursor=null;
				cursor = database.rawQuery(query, null);
				if (cursor.getCount() > 0) {
					MenuAegingListChild childObj = new MenuAegingListChild();
					ArrayList<AgeingDetails> detailList = new ArrayList<AgeingDetails>();
					cursor.moveToFirst();
					for (int ii = 0; ii < cursor.getCount(); ii++) {
						AgeingDetails detailsObj = new AgeingDetails();
						detailsObj.setParty(cursor.getString(0));
						detailsObj.setInvNo(cursor.getString(1));
						int dueDays = (int) (Double.parseDouble(cursor.getString(3)));
						if (dueDays <= period1) {
							detailsObj.setPeriod1(cursor.getString(2));
						} else if (dueDays > period1 && dueDays <= period2) {
							detailsObj.setPeriod2(cursor.getString(2));
						} else {
							detailsObj.setPeriod3(cursor.getString(2));
						}
						detailList.add(detailsObj);
						cursor.moveToNext();
					}
					childObj.setAgeingChildList(detailList);
					childList.add(childObj);
					cursor.close();
				}else{
					if(cursor!=null){
						cursor.close();
					}
				}
			}
		} catch (Exception e) {
			print_log_d("outstanding_master", e.getMessage());
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return childList;
	}

	public ArrayList<MenuClStkMrpDetails> getMenuMrpList(String branchcode,String type) {
		ArrayList<MenuClStkMrpDetails> mrpList = new ArrayList<MenuClStkMrpDetails>();
		String selectQuery = "";
		Cursor cursor=null;
		if(Constants.saudaFormDetailsObj.getSaudaDepotWise().equalsIgnoreCase("yes")){
			if(type.equalsIgnoreCase("sauda")){
				selectQuery="SELECT PM.prod_desc,MRP.mrp_value  FROM  sauda_mrp MRP,product_master PM WHERE  MRP.sku_code=PM.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N'  AND MRP.branch_code='"+branchcode+"'  ORDER BY PM.prod_desc ASC";
			}else{
				selectQuery="SELECT PM.prod_desc,MRP.sale_rate  FROM  mrp MRP,product_master PM WHERE  MRP.sku_code=PM.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N'  AND MRP.branch_code='"+branchcode+"'  ORDER BY PM.prod_desc ASC";
			}
		}
		else{
			if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
				selectQuery = "SELECT PM.prod_desc,MRP.mrp_value FROM product_master PM,mrp MRP WHERE PM.prod_code = MRP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N'";
			} else {
				selectQuery = "SELECT PM.prod_desc,MRP.sale_rate FROM product_master PM,mrp MRP WHERE PM.prod_code = MRP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N'";
			}
		}

		try {
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					MenuClStkMrpDetails detailsObj = new MenuClStkMrpDetails();
					detailsObj.setProduct(cursor.getString(0));
					detailsObj.setValue(cursor.getString(1));
					mrpList.add(detailsObj);
					cursor.moveToNext();
				}
			}
		} catch (Exception e) {
			print_log_d("Menu MRP","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return mrpList;
	}

	public ArrayList<MenuClStkMrpDetails> getMenuMrpList(String branchcode) {
		ArrayList<MenuClStkMrpDetails> mrpList = new ArrayList<MenuClStkMrpDetails>();
		String selectQuery = "";
		Cursor cursor=null;
		if(Constants.saudaFormDetailsObj.getSaudaDepotWise().equalsIgnoreCase("yes")){
			selectQuery="SELECT PM.prod_desc,MRP.sale_rate  FROM  mrp MRP,product_master PM WHERE  MRP.sku_code=PM.prod_code AND PM.acedns = 'Y' AND PM.black_list = 'N'  AND MRP.branch_code='"+branchcode+"'  ORDER BY PM.prod_desc ASC";
		}
		else{
			if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
				selectQuery = "SELECT PM.prod_desc,MRP.mrp_value FROM product_master PM,mrp MRP WHERE PM.prod_code = MRP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N'";
			} else {
				selectQuery = "SELECT PM.prod_desc,MRP.sale_rate FROM product_master PM,mrp MRP WHERE PM.prod_code = MRP.sku_code AND PM.acedns = 'Y' AND PM.black_list = 'N'";
			}
		}
		try {
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					MenuClStkMrpDetails detailsObj = new MenuClStkMrpDetails();
					detailsObj.setProduct(cursor.getString(0));
					detailsObj.setValue(cursor.getString(1));
					mrpList.add(detailsObj);
					cursor.moveToNext();
				}
			}
		} catch (Exception e) {
			print_log_d("Menu MRP","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return mrpList;
	}

	public ArrayList<MenuClStkMrpDetails> getMenuClStkList() {
		ArrayList<MenuClStkMrpDetails> clStkList = new ArrayList<MenuClStkMrpDetails>();
		String selectQuery = "SELECT PM.prod_desc,CS.cl_stk FROM product_master PM,closing_stock CS WHERE PM.prod_code = CS.prod_code AND CS.cl_stk>0 AND PM.acedns = 'Y' AND PM.black_list = 'N' ";
		Cursor cursor=null;
		try {
			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++) {
					MenuClStkMrpDetails detailsObj = new MenuClStkMrpDetails();
					detailsObj.setProduct(cursor.getString(0));
					detailsObj.setValue(cursor.getString(1));
					clStkList.add(detailsObj);
					cursor.moveToNext();
				}
			}
		} catch (Exception e) {
			print_log_d("Closing Stock","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return clStkList;
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

	/*
	 * Incremental Data Download
	 */
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

	public void updateDictTimeInLogTable() {
		long status = 0;
		try {
			database.beginTransaction();
			String newTime = getNewTime();
			ContentValues cv = new ContentValues();
			cv.put("last_download_time", newTime);
			synchronized (Lock) {
				status = database.update("data_download_log", cv,"table_name=?", new String[] { "download_dictionary" });
				print_log_d("Time in data_download_log :", "Updated" + status);
			}
			database.setTransactionSuccessful();
		} catch (SQLException e) {
			print_log_d("Update Download Log", "Exception " +e);
		} finally {
			database.endTransaction();
		}
	}

	public String getNewTime() {
		String newTime = "";
		try {
			String timeStamp = new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
			Date dictDownldEndTime = new SimpleDateFormat("yyyyMMddHHmmss").parse(dateString + timeStamp);
			long diffInSec = (dictDownldEndTime.getTime() - Constants.dictDownldStartTime.getTime()) / 1000;
			Constants.dictDownldSrverTime.add(Calendar.SECOND, (int) diffInSec);
			newTime = new SimpleDateFormat("yyyy-MM-ddHH:mm:ss").format(Constants.dictDownldSrverTime.getTime());
			newTime = newTime.substring(0, 10) + "€" + newTime.substring(10, newTime.length());
		} catch (Exception e) {
			print_log_d("Time ","Exception:" + e);
		}
		return newTime;
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

	public String getPlanOrPurchaseByCustomerAndMonth(String customerCode, String prodCode, String currentOrPreviousMonth, String columnName)
	{
		String planOrPurchase="";
		Cursor cursor=null;
		try
		{
			String sql = "SELECT " + columnName + " FROM customer_product_wise_orderplan WHERE customer_code='" +
					customerCode + "' AND product_code='" + prodCode + "' AND month='" + currentOrPreviousMonth+"'";
			cursor = database.rawQuery(sql,null);
			if (cursor!=null && cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				planOrPurchase=cursor.getString(0);
				cursor.close();
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
		return planOrPurchase;
	}

	public String getImmediateBossOfCurrentEmployee(String currentlyAllocatedToTheEmployee)
	{
		String AllocatedEmp ="";
		Cursor cursor=null;
		try
		{
			String sqlQuery = "SELECT reporting_to FROM emp_master WHERE emp_code='" + currentlyAllocatedToTheEmployee + "'";
			cursor = database.rawQuery(sqlQuery, null);
			if (cursor!=null && cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				String emp_code=cursor.getString(0).trim();
				AllocatedEmp=emp_code;
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
		return AllocatedEmp;
	}

	public void getUVerifiedCashReceive()
	{
		UnVerifiedCashReceiveList=new ArrayList<>();
		String selectQuery = "";
		Cursor cursor=null;
		try
		{
			selectQuery = "SELECT *,substr(cash_transaction_id,-10,10) FROM cash_transaction_details WHERE status='0' AND transaction_type='CT' AND despatcher_code <> '"+get_emp_or_customer_code(mContext)+"'";

			cursor = database.rawQuery(selectQuery, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				for (int ii = 0; ii < cursor.getCount(); ii++)
				{
					CashTransferReceive item=new CashTransferReceive();
					item.setcash_trans_rcv_trans_id(cursor.getString(0));
					item.setdespatcher_code(cursor.getString(1));
					item.setreceiver_code(cursor.getString(2));
					item.setdespatch_value(cursor.getString(3));
					item.setrec_value(cursor.getString(4));
					item.setstatus(cursor.getString(5));
					item.settransaction_type(cursor.getString(6));
					item.setcash_transfer_id(cursor.getString(7));
					item.setcash_transfer_date_time(cursor.getString(9));
					UnVerifiedCashReceiveList.add(item);
					cursor.moveToNext();
				}

			}
			cursor.close();
		}
		catch (Exception e)
		{

		}
		finally
		{
			if(cursor !=null)
			{
				cursor.close();
			}

		}
	}
	public String VerticalValueOfEmployee(String empcode){
		String lowerLeaves="";
		String query ="";
		Cursor cursor = null;
		try {
			query = "SELECT vertical_value FROM emp_master WHERE emp_code ='"+empcode+"'";
			print_log_d("Employee", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				lowerLeaves=cursor.getString(0);
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Employee Hierarchy","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return lowerLeaves;
	}
	public double calculatedValueM2C(String prodcode, double maxAllocation){
		String query ="";
		Cursor cursor = null;
		try {
			query = "SELECT conversion_factor,conversion_factor_two FROM product_master WHERE prod_code ='"+prodcode+"'";
			print_log_d("Employee", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				maxAllocation = (maxAllocation * cursor.getDouble(1)) / cursor.getDouble(0);
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Employee Hierarchy","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return maxAllocation;
	}
	public double calculatedValueC2M(String prodcode, double maxAllocation){
		String query ="";
		Cursor cursor = null;
		try {
			query = "SELECT conversion_factor, conversion_factor_two FROM product_master WHERE prod_code ='"+prodcode+"'";
			print_log_d("Employee", query);
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0) {
				cursor.moveToFirst();
				maxAllocation = (maxAllocation * cursor.getDouble(0)) / cursor.getDouble(1);
				maxAllocation=decimal3round(maxAllocation,3);
				cursor.close();
			}
		} catch (Exception e) {
			print_log_d("Employee Hierarchy","Exception " + e);
		} finally {
			if(cursor !=null){
				cursor.close();
			}
		}
		return maxAllocation;
	}

	public void updateCustomerMasterWithEmailOrPhone(String emailOrPhone)
	{
		try
		{
			database.beginTransaction();
			ContentValues cv = new ContentValues();
			if(emailOrPhone.matches("Email"))
			{
				cv.put("email", Constants.selectedCustomer.getEmail());
			}
			else
			{
				cv.put("phone_no", Constants.selectedCustomer.getNumber());
			}
			synchronized (Lock)
			{
				database.update("customer_master", cv,"customer_code=?", new String[] { Constants.selectedCustomer.getCustomerCode()});
			}
			database.setTransactionSuccessful();
		} catch (SQLException e)
		{
			print_log_d("Update Download Log", "Exception " +e);
		}
		finally
		{
			database.endTransaction();
		}
	}

	public String getYellowCardValidationMonthDate(String CurrentValidationMonth)
	{
		String ValidationMonthDate="";
		Cursor cursor=null;
		try
		{
			cursor = database.rawQuery("SELECT validation_date FROM yellow_card_date_validation WHERE  validation_month ='"+ CurrentValidationMonth +"'", new String[] {});

			if(cursor!=null && cursor.getCount()>0)
			{
				cursor.moveToFirst();
				ValidationMonthDate = cursor.getString(0);
				cursor.close();
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
		return ValidationMonthDate;
	}

	public boolean checkLastLoginSuccessful()
	{
		boolean status = false;
		String currentDate = Constants.dateString.substring(0, 4) + "-"
				+ Constants.dateString.substring(4, 6) + "-"
				+ Constants.dateString.substring(6, 8);
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

	public void insertOrUpdateToEmployeeMaster()
	{
		TruncateTableByTableName("employee_master_login");

		database.beginTransaction();
		String currentDate = Constants.dateString.substring(0, 4) + "-"
				+ Constants.dateString.substring(4, 6) + "-"
				+ Constants.dateString.substring(6, 8);
		try {
			ContentValues cv = new ContentValues();
			cv.put("emp_code", get_emp_or_customer_code(mContext));
			cv.put("date", currentDate);
			cv.put("emp_name", Constants.employeeDetailObject.getEmpName());
			cv.put("device_id", Constants.employeeDetailObject.getDeviceID());
			cv.put("password", "");
			cv.put("sale_access", Constants.employeeDetailObject.getSaleAccess());
			cv.put("app_version", "");
			cv.put("updation_flag", "");
			cv.put("phone_no", Constants.employeeDetailObject.getPhoneNumber());
//			cv.put("verification_token", Constants.employeeDetailObject.getverificationtoken()); //amitabha2715
			cv.put("verification_token", "");
			synchronized (Lock)
			{
				database.insertWithOnConflict("employee_master_login", null, cv,SQLiteDatabase.CONFLICT_IGNORE);
			}
			database.setTransactionSuccessful();
		}
		catch (SQLException e)
		{
		}
		finally
		{
			database.endTransaction();
		}
	}
	public String getValueFromKey(String key, String selected_customr_code)
	{
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
	public String getCustomerCodeFromEmpCode()
	{
		String value ="";
		Cursor cursor = null;
		try {
			String query = "SELECT customer_code FROM customer_master WHERE cust_type ='Dealer' AND customer_code= '" + get_emp_or_customer_code(mContext) +"'";
			print_log_d("amitabha2715_customerCode", query);
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
	public String getBranchCode(String emp_)
	{
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
	public ArrayList<SchemeDetails> getSchemeFile()
	{
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
					detailObj.setSchemeValue(cursor.getString(0).trim()); //set PDF_file_name
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
	public void deleteTableByTableName(String tableName)
	{
		try
		{
			database.beginTransaction();
			database.execSQL("DELETE FROM " + tableName);
			database.endTransaction();
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
}
