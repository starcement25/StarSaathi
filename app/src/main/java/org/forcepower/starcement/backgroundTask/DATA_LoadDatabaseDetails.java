package org.forcepower.starcement.backgroundTask;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.AceDnsWebServiceURL.table_structure_details;
import static org.forcepower.starcement.constants.Constants.nickName;
import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;

import android.app.Activity;
import android.graphics.BitmapFactory;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.DatabaseStructure;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.parser.DatabaseDetailsXMLParsing;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;

import java.io.File;
import java.util.ArrayList;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

@SuppressWarnings("unused")

public final class DATA_LoadDatabaseDetails  extends AsyncTaskCoroutine<String, String> {
	Activity mContext;
	String mHttpResponse   	= "";
	String mBaseUrlChanged 	= "";
	String mEmployeeCode	= "";
	ArrayList<NameValuePair> nameValuePairs;
	ArrayList<DatabaseStructure> mDatabaseStructureList;
	
	AceDnsDatabase mAceDnsDatabase;	
	boolean isDataDownload = true;
	long dbResult = 0;

	public DATA_LoadDatabaseDetails(Activity context) {
		this.mContext = context;
		mAceDnsDatabase = new AceDnsDatabase(mContext);
	}
	
	@Override
	public void onPreExecute() {
		super.onPreExecute();
		Utils.changeProgressDialogMsg(mContext, "Updating...");
	}

	@Override
	public String doInBackground(final String... params) {
		dbResult = 0;
		try {
			mAceDnsDatabase.TruncateTableByTableName("customer_master");
			String dirName = get_direcory_path(mContext) ;
			File dir = new File(dirName);
			if(!dir.exists())
			{
				dir.mkdirs();
			}
			String fileName = get_direcory_path(mContext)+nickName+"_main.db";
			File f = new File(fileName);
			if(!f.exists()){
				f.createNewFile();
			}

		}
		catch (Exception e) {
			e.printStackTrace();
		}

		try {
			mEmployeeCode = get_emp_or_customer_code(mContext);
			loadDatabaseDetails();

			if(mHttpResponse.length() > 0 && !mHttpResponse.equalsIgnoreCase("Network Failure"))
			{
				DatabaseDetailsXMLParsing parser = new DatabaseDetailsXMLParsing(mHttpResponse);
				mDatabaseStructureList = parser.getParsedData();
				if(mDatabaseStructureList != null && mDatabaseStructureList.size() > 0)
				{
					dbResult  = mAceDnsDatabase.createAppTables(mDatabaseStructureList);
					mBaseUrlChanged=mDatabaseStructureList.get(mDatabaseStructureList.size()-1).getBaseUrlChanged();
					InsertToAppInfo(mDatabaseStructureList.get(mDatabaseStructureList.size()-1).getDbVersion());
				}
				else
				{
					dbResult = 1;   //DB has already been created. No response for Table Structure.
				}
			}

		}
		catch (Exception e) {
			e.printStackTrace();
		}
		finally {
			mAceDnsDatabase.closeDatabase();
		}
		return null;
	}
	
	@Override
	public void onPostExecute(String result) {
		super.onPostExecute(result);

		try {
			if(dbResult > 0)
			{
				Utils.changeProgressDialogMsg(mContext, "Updating...");
				new DATA_LoadDataDictionaryData(mContext).execute(); //amitabha2715 disabled Download App update
			}
			else
			{
				Utils.cancelProgressDialog();
				Utils.directOutsideTheApplication(mContext, "Please contact admin",true);
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
//		finally
//		{
//			Utils.cancelProgressDialog();
//		}

	}
	
	public void loadDatabaseDetails() {
		final String deviceId = Constants.deviceId.length() > 0 ? Constants.deviceId : Utils.getDeviceId(mContext);

		nameValuePairs = new ArrayList<>(4);
		nameValuePairs.add(new BasicNameValuePair("nick_name", Constants.nickName));
		nameValuePairs.add(new BasicNameValuePair("device_id", deviceId));
		nameValuePairs.add(new BasicNameValuePair("emp_code", get_emp_or_customer_code(mContext)));
		nameValuePairs.add(new BasicNameValuePair("mode", "INSTALL"));

		print_Log_d("dfdr_1 " + nameValuePairs);
		print_Log_d("dfdr_2 " + table_structure_details);
		print_Log_d("dfdr_2 " + Constants.nickName);

		mHttpResponse = HTTPUtils.getDataByHTTP_POST(mContext,table_structure_details, nameValuePairs);

		print_Log_d("dfdr_2 " + mHttpResponse);
	}
	
	public void InsertToAppInfo(String dbVersion){
		Constants.logoBmp = BitmapFactory.decodeResource(mContext.getResources(), R.mipmap.ic_launcher);
		String appVersion = Utils.getAppVersion(mContext);
		mAceDnsDatabase.insertOrUpdateAppInfo(Constants.nickName,appVersion,dbVersion,mBaseUrlChanged);
	}	
}
