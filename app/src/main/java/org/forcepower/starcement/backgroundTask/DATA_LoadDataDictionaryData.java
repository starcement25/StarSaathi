package org.forcepower.starcement.backgroundTask;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;
import static org.forcepower.starcement.util.Utils.print_log_d;

import android.app.Activity;

import org.forcepower.starcement.aaa.ActivityDownloadStatus;
import org.forcepower.starcement.constants.AceDnsWebServiceURL;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.util.Utils;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;


public final class DATA_LoadDataDictionaryData extends AsyncTaskCoroutine<String, String>{
	Activity mContext;
	AceDnsDatabase dbHelper;
	String timeStamp = "";
	String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
	
	public DATA_LoadDataDictionaryData(Activity context) {
		this.mContext = context;
		dbHelper = new AceDnsDatabase(mContext);
		lastUpdate = dbHelper.getlastDownloadTime("download_dictionary");
		Constants.dictDownldStartTime = new Date();
	}
	
	@Override
	public void onPreExecute() {
		super.onPreExecute();
		Utils.showProgressDialog(mContext, "Updating...");
	}

	@Override
	public String doInBackground(final String... params) {
		final String deviceId = Constants.deviceId.length() > 0 ? Constants.deviceId : Utils.getDeviceId(mContext);

		String url = Constants.baseURL+AceDnsWebServiceURL.downloadDictionaryURL
				+"?nick_name="+Constants.nickName
				+"&emp_code="+get_emp_or_customer_code(mContext)
				+"&incremental_download=no"
				+"&user_type="+get_user_type(mContext)
				+"&last_update_time=1971-01-01?10:10:10"
				+"&device_id="+deviceId;
		print_Log_d("CheckMyURL:  "+ url);
		url = url.replace(" ", "%20");

		downloader(url);
		
		File csvFile = new File(get_direcory_path(mContext)+"data_download_dict.txt");
		Constants.downloadTableList= new ArrayList<String>();
			FileReader file = null;
			try {
				file = new FileReader(csvFile);
			} catch (FileNotFoundException e1) {
				e1.printStackTrace();
			}
			@SuppressWarnings("resource")
			BufferedReader buffer = new BufferedReader(file);
		    try {
		    	String line = "";

				while ((line = buffer.readLine()) != null) {
					if (line.indexOf("€") > 0) {
						timeStamp = line.replace("€", "");
						Constants.dateString = timeStamp.substring(0, 10).replace("-","");
						try{
						      Constants.dictDownldSrverTime = Calendar.getInstance();
						      Constants.dictDownldSrverTime.setTime(new SimpleDateFormat("yyyy-MM-ddHH:mm:ss").parse(timeStamp));
						}catch(Exception e){
							Constants.dictDownldSrverTime = Calendar.getInstance();
						}
					} else {
						Constants.downloadTableList.add(line);
					}
		        }
		    }
		    catch (IOException ex) {
		    	ex.printStackTrace();
		    }
		return null;
	}
	
	@Override
	public void onPostExecute(String result)
	{
		super.onPostExecute(result);
		try
		{
			new ActivityDownloadStatus(mContext);
		}
		catch (Exception e)
		{

			e.printStackTrace();
		}
		finally
		{
			Utils.cancelProgressDialog();
		}
	}
	
	private void downloader(String urlstr) {

		HttpURLConnection c = null;
		FileOutputStream fbo = null;
		File outputFile = null;
		InputStream is = null;
		URL url = null;

		try {
			outputFile = new File(get_direcory_path(mContext)+"data_download_dict.txt");
			if(outputFile.exists())
				print_log_d("File delete",outputFile.delete()+"");
			fbo = new FileOutputStream(outputFile, false);

			// connect with server where remote file is stored to download it
			url = new URL(urlstr);
			c = (HttpURLConnection) url.openConnection();
			c.setRequestMethod("GET");
			c.setDoOutput(true);
			c.setConnectTimeout(55000);
			c.setReadTimeout(55000);

			c.connect();

			is = c.getInputStream();
			
			byte[] buffer = new byte[1024];
			int len1 = 0;
			while ((len1 = is.read(buffer)) != -1)
			{
				fbo.write(buffer, 0, len1);
				print_log_d("length", len1+"----");
			}

			fbo.flush();

		} catch (Exception e) {
			
		} finally {

			if (c != null)
				c.disconnect();
			if (fbo != null)
				try {
					fbo.close();
				} catch (IOException e) {
					e.printStackTrace();
				}
			if (is != null)
				try {
					is.close();
				} catch (IOException e) {
					e.printStackTrace();
				}
			outputFile = null;

		}

	}
}
