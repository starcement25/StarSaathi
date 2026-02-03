package org.forcepower.starcement.util;

import static androidx.core.content.FileProvider.getUriForFile;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.DatePickerDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.ApplicationInfo;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.res.Resources;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.graphics.Bitmap;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Matrix;
import android.graphics.Paint;
import android.graphics.PorterDuff;
import android.graphics.PorterDuffXfermode;
import android.graphics.Rect;
import android.graphics.RectF;
import android.net.Uri;
import android.os.Build;
import android.provider.Settings;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.widget.DatePicker;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import org.forcepower.starcement.BuildConfig;
import org.forcepower.starcement.R;
import org.forcepower.starcement.Utils_;
import org.forcepower.starcement.bean.CustomerDetails;
import org.forcepower.starcement.bean.OrderFormDetails;
import org.forcepower.starcement.bean.ProdQtyCustClassWiseTDDetails;
import org.forcepower.starcement.bean.ProductDetails;
import org.forcepower.starcement.bean.RouteDetails;
import org.forcepower.starcement.bean.RoutePlanMasterDetails;
import org.forcepower.starcement.bean.SelfAppraisalDetails;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.database.AceDnsDatabase;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.text.DateFormat;
import java.text.DecimalFormat;
import java.text.ParseException;
import java.text.ParsePosition;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;
import java.util.TimeZone;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import static org.forcepower.starcement.SharedPrefData.get_device_id;
import static org.forcepower.starcement.SharedPrefData.get_login_mobile_number;
import static org.forcepower.starcement.SharedPrefData.set_device_evice_id;
import static org.forcepower.starcement.Utils_.print_Log_d;

public final class Utils {
	static boolean canShowToast = true;
	public static ProgressDialog loaderDialog;
	static AceDnsDatabase dbHelper;

	public static void showToast(Context mContext, String msg) {
		if (canShowToast) {
			Toast.makeText(mContext, ""+msg, Toast.LENGTH_SHORT).show();
		}
	}

	public static void showProgressDialog(Context mContext, String msg) {
		if (!(loaderDialog != null && loaderDialog.isShowing())) {
			loaderDialog = new ProgressDialog(mContext);

			loaderDialog.setMessage(msg);

			loaderDialog.setCancelable(false);
			loaderDialog.setCanceledOnTouchOutside(false);
			loaderDialog.show();
		}
	}

	public static void cancelProgressDialog() {
		if (loaderDialog != null && loaderDialog.isShowing()) {
			loaderDialog.dismiss();
			loaderDialog = null;
		}
	}

	public static void changeProgressDialogMsg(Context mContext, String msg) {
		if (loaderDialog != null) {
			loaderDialog.setMessage(msg);
		} else {
			showProgressDialog(mContext, msg);
		}
	}

	public static String checkLibraryConditions(Context mContext) {

		return "";
	}

	



	public static void directOutsideTheApplication(final Context context,
			final String Message, final boolean sendData) {

		Utils_.closeApp(context, Message);

	}

	public static boolean InitialiseSETUPTableData(Context mContext)
	{
		AceDnsDatabase aceDnsDatabase = new AceDnsDatabase(mContext);
		aceDnsDatabase.getMenuDetailsObj();
		aceDnsDatabase.getUserDetailsObj();
		aceDnsDatabase.getProductDetailsObj();
		aceDnsDatabase.GETSurveyFormDetails();
		aceDnsDatabase.close();
		if(Constants.orderFormDetailsObj == null)
		{
			Constants.orderFormDetailsObj = new OrderFormDetails();
		}
		if(Constants.productDetailsObj == null)
		{
			Constants.productDetailsObj = new ProductDetails();
		}
		if (Constants.menuDetailsObj != null
				&& Constants.userDetailsObj != null
				&& Constants.orderFormDetailsObj != null
				&& Constants.productDetailsObj != null) {
			return true;
		}
		return false;
	}

	public static void insertToEmployeeMaster(Context mContext) {
		dbHelper = new AceDnsDatabase(mContext);
		dbHelper.insertToEmployeeMaster();
		dbHelper.closeDatabase();
	}

	public static void updateEmployeeMasterDate(Context mContext) {
		dbHelper = new AceDnsDatabase(mContext);
		dbHelper.updateEmployeeMaster();
		dbHelper.closeDatabase();
	}

	public static void updateEmployeeMasterFlag(Context mContext) {
		dbHelper = new AceDnsDatabase(mContext);
		dbHelper.updateEmployeeMasterFlag();
		dbHelper.closeDatabase();
	}

	public static boolean lastLoginSuccessfull(Context mContext) {
		dbHelper = new AceDnsDatabase(mContext);
		boolean bool = dbHelper.checkLastLoginSuccessfull();
		dbHelper.closeDatabase();
		return bool;
	}

	public static String getInstallationTime(Context context) {
		SimpleDateFormat dateFormat = new SimpleDateFormat(
				"dd-MM-yyyy HH:mm:ss");
		String installed = "";
		try {
			PackageManager pm = context.getPackageManager();
			ApplicationInfo appInfo = pm.getApplicationInfo("org.coral.acedns",
					0);
			String appFile = appInfo.sourceDir;
			installed = dateFormat.format(new Date(new File(appFile)
					.lastModified())); // Epoch Time
		} catch (Exception e) {
			print_Log_d("Exception:::::::" + e);
		}
		return installed;
	}

	public static String getAppVersion(Context mContext) {
		String version = "1.0.0";
		try {
			PackageInfo pInfo = mContext.getPackageManager().getPackageInfo(mContext.getPackageName(), 0);
			version = pInfo.versionName;
		} catch (Exception e) {

		}
		return version;
	}

	public static String getDBVersion(Context mContext) {

		return "";
	}



	public static String getDeviceId(Context mContext)
	{
		if(get_device_id(mContext).matches(""))
		{
			String deviceId = new SimpleDateFormat("yyyyMMddHHmmssSSSSSSS", Locale.getDefault()).format(new Date());
			set_device_evice_id(mContext, deviceId);
		}
		Constants.deviceId=get_device_id(mContext);
		return get_device_id(mContext);
	}

	public static Bitmap getCircleBitmap(Bitmap bitmap) {
		final Bitmap output = Bitmap.createBitmap(bitmap.getWidth(),
				bitmap.getHeight(), Bitmap.Config.ARGB_8888);
		final Canvas canvas = new Canvas(output);

		final int color = Color.RED;
		final Paint paint = new Paint();
		final Rect rect = new Rect(0, 0, bitmap.getWidth(), bitmap.getHeight());
		final RectF rectF = new RectF(rect);

		paint.setAntiAlias(true);
		canvas.drawARGB(0, 0, 0, 0);
		paint.setColor(color);
		canvas.drawOval(rectF, paint);

		paint.setXfermode(new PorterDuffXfermode(PorterDuff.Mode.SRC_IN));
		canvas.drawBitmap(bitmap, rect, rect, paint);

		bitmap.recycle();

		return output;
	}
	public static void show_msg_Dialog(Activity mContext, String msg)
	{
		try
		{
			AlertDialog.Builder issueBuilder = new AlertDialog.Builder(mContext, R.style.MyDialog);

			TextView tvCPopup = new TextView(mContext);
			tvCPopup.setText(mContext.getResources().getString(R.string.app_name));
			tvCPopup.setGravity(Gravity.CENTER);
			tvCPopup.setTextColor(mContext.getResources().getColor(R.color.white));
			tvCPopup.setTextSize(14);
			tvCPopup.setBackgroundColor(mContext.getResources().getColor(R.color.red));
			int margin = 15;
			tvCPopup.setPadding(0, margin*2, 0, margin*2);
			issueBuilder.setCustomTitle(tvCPopup);
			issueBuilder.setMessage("\n" + msg);

			issueBuilder.setPositiveButton("Ok", new DialogInterface.OnClickListener() {
				@Override
				public void onClick(DialogInterface dialog, int which) {
					dialog.dismiss();
				}
			});

			issueBuilder.setCancelable(false);
			issueBuilder.show();
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	//
	public static String changeDateFormat(String inputDateFormat,String outputDateFormat,String inputDateString)
	{
		String outputDateString = "";
		try
		{
			final SimpleDateFormat inputFormat = new SimpleDateFormat(inputDateFormat,  Locale.getDefault());
			final SimpleDateFormat outputFormat = new SimpleDateFormat(outputDateFormat,  Locale.getDefault());

			Date date = inputFormat.parse(inputDateString);
			outputDateString = outputFormat.format(date);
		}
		catch (ParseException e)
		{
			e.printStackTrace();
		}
		return outputDateString;
	}


	private static boolean doesQuantityMatchesQtySlab(String currentqty, String currentQtySlab)
	{

		return false;
	}

	public static boolean isNumeric(String str)
	{
		try
		{
			Double.parseDouble(str);
		}
		catch(Exception nfe)
		{
			return false;
		}
		return true;
	}

	@SuppressLint("SuspiciousIndentation")
    public static String addAllItemsOfAnArray(String amount)
	{
		double finalAmount =0;
		if(amount!=null && !amount.matches("null") && amount.contains(","))
		{
			String[] amountArray=amount.split(",");
			for( String tempAmount : amountArray)
			{
				if(isNumeric(tempAmount))
				{
					finalAmount = finalAmount+Double.parseDouble(tempAmount);
				}
			}
		}
		else
		{
			if(isNumeric(amount))
			finalAmount = finalAmount+Double.parseDouble(amount);
		}


		return String.valueOf(finalAmount);
	}

	public static String addAllItemsOfAnArrayForOrder(String amount, SQLiteDatabase database)
	{
		double finalAmount =0;
		if(amount!=null && !amount.matches("null") && amount.contains(","))
		{
			String[] amountArray=amount.split(",");
			String[] orderNoArray=Constants.mCurrentOrderNoList.split(",");
			int i=0;
			for( String tempAmount : amountArray)
			{
				if(isNumeric(tempAmount))
				{
					double currentAmount = Double.parseDouble(tempAmount);
					currentAmount=calculateTDForOrderValueAndPercentageWiseTD(currentAmount,orderNoArray[i],database);
					finalAmount = finalAmount+ currentAmount;
				}
				i++;
			}
		}
		else
		{
			if(isNumeric(amount))
			{
				double currentAmount = Double.parseDouble(amount);
				currentAmount=calculateTDForOrderValueAndPercentageWiseTD(currentAmount,Constants.mCurrentOrderNoList,database);
				finalAmount = finalAmount+ currentAmount;
			}
		}


		return String.valueOf(finalAmount);
	}



	private static double calculateTDForOrderValueAndPercentageWiseTD(double currentAmount,String order_no,SQLiteDatabase database)
	{
		if(Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise")
				&& Constants.orderFormDetailsObj.getTdCalc().equalsIgnoreCase("percentage"))
		{
			Cursor cursor = null;
			String query="SELECT TD from order_header where order_no ='"+order_no+"'";
			cursor = database.rawQuery(query, null);
			if (cursor.getCount() > 0)
			{
				cursor.moveToFirst();
				String TDPercent=cursor.getString(0);
				if(isNumeric(TDPercent))
				{
					currentAmount=currentAmount-(currentAmount*Double.parseDouble(TDPercent))/100;
				}
			}
			cursor.close();
		}
		return currentAmount;
	}

	public static void SaveImageToExternalStorage(Bitmap finalBitmap,String filePath,String fileName)
	{
		File myDir = new File(filePath);
		myDir.mkdirs();
		File file = new File (myDir, fileName);
		if (file.exists ())
			file.delete ();
		try
		{
			FileOutputStream out = new FileOutputStream(file);
			finalBitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);
			out.flush();
			out.close();

		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public static void showCommonAlertDialog(final Activity context,String title, String message)
	{
		AlertDialog.Builder AlertDG = new AlertDialog.Builder(context, R.style.MyDialog);
		TextView tvCPopup = new TextView(context);
		tvCPopup.setText(title);
		tvCPopup.setGravity(Gravity.CENTER);
		tvCPopup.setTextColor(context.getResources().getColor(R.color.white));
		tvCPopup.setTextSize(14);
		tvCPopup.setBackgroundColor(context.getResources().getColor(R.color.red));
		int margin = 15;
		tvCPopup.setPadding(0, margin*2, 0, margin*2);
		AlertDG.setCustomTitle(tvCPopup);
		AlertDG.setMessage(message);
		AlertDG.setPositiveButton("Ok", new DialogInterface.OnClickListener() {

			public void onClick(DialogInterface dialog, int which)
			{
				//
			}
		});

		AlertDG.setCancelable(true);
		AlertDG.create().show();
	}

	public static Boolean checkIfRateIsProper(String rate)
	{
		if(!Utils.isNumeric(rate))
		{
			return false;
		}
		else
		{
			double rateInDouble = Double.parseDouble(rate);
			{
				if(rateInDouble<=0)
				{
					return false;
				}
				else
				{
					return true;
				}
			}

		}
	}

	public static String dayOfWeek()
	{
		try
		{
			Calendar calendar = Calendar.getInstance();
			Date date = calendar.getTime();
			// 3 letter name form of the day
			// print_Log_d(new SimpleDateFormat("EE", Locale.ENGLISH).format(date.getTime()));//Sat
			// full name form of the day
			return new SimpleDateFormat("EEEE", Locale.ENGLISH).format(date.getTime());//Saturday
		}
		catch (Exception e)
		{
			return "";
		}

	}

	public static ArrayList<String> daysOfWeekExceptToday()
	{
		ArrayList<String> days=new ArrayList<>();
		try
		{
			for(int i =1;i<7;i++)
			{
				Calendar calendar = Calendar.getInstance();
				calendar.add(Calendar.DAY_OF_YEAR, i);
				Date date = calendar.getTime();
				days.add(new SimpleDateFormat("EEEE", Locale.ENGLISH).format(date.getTime()));
			}

			return days;
		}
		catch (Exception e)
		{
			return days;
		}

	}

	public static boolean isValidMail(String email)
	{
		try
		{
			return android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches();
		}
		catch(Exception e)
		{
			return false;
		}

	}
	public static boolean isValidIndianMobile(String phone) {
		if(!Pattern.matches("[a-zA-Z]+", phone)) {
			return phone.matches("[6-9][0-9]{9}");
		}
		return false;
	}
	public static boolean isNotValidInputText(final String text) {
		final Pattern p = Pattern.compile("[^a-z0-9 ]", Pattern.CASE_INSENSITIVE);
		final Matcher m = p.matcher(text);
		return m.find();
	}
	public static boolean isValidIndianPin(String phone) {
		if(!Pattern.matches("[a-zA-Z]+", phone)) {
			return phone.matches("[1-9][0-9]{5}");
		}
		return false;
	}
	public static final Pattern EMAIL_ADDRESS_PATTERN = Pattern.compile(
			"^(([\\w-]+\\.)+[\\w-]+|([a-zA-Z]{1}|[\\w-]{2,}))@"
					+"((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\\.([0-1]?"
					+"[0-9]{1,2}|25[0-5]|2[0-4][0-9])\\."
					+"([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\\.([0-1]?"
					+"[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|"
					+"([a-zA-Z]+[\\w-]+\\.)+[a-zA-Z]{2,4})$"
	);
	public static boolean validEmailFormat(String UserEmailNo)
	{
		return EMAIL_ADDRESS_PATTERN.matcher(UserEmailNo).matches();
	}


	//make sub dealer, retailer to 'sub dealer','retailer'
	public static String convertCommaSeparatedListToProperFormat(String dataTobeFormatted)
	{
//		String dataTobeFormatted=Constants.userDetailsObj.getstk_audit_cust_type();
		String finalData ="";
		if(dataTobeFormatted.contains(","))
		{
			String[] custTypeSplitted=dataTobeFormatted.split(",");
			for(int i=0;i<custTypeSplitted.length;i++)
			{
				if(finalData.matches(""))
				{
					finalData ="'"+custTypeSplitted[i]+"'";
				}
				else
				{
					finalData = finalData +", "+"'"+custTypeSplitted[i]+"'";
				}
			}
		}
		else
		{
			finalData ="'"+dataTobeFormatted+"'";
		}
		return finalData;
	}
	//make sub dealer; retailer to 'sub dealer','retailer'
	public static String convertCommaSeparatedListToProperFormat2(String dataTobeFormatted)
	{
		String finalData ="";
		if(dataTobeFormatted.contains(";"))
		{
			String[] custTypeSplitted=dataTobeFormatted.split(";");
			for(int i=0;i<custTypeSplitted.length;i++)
			{
				if(finalData.matches(""))
				{
					finalData ="'"+custTypeSplitted[i]+"'";
				}
				else
				{
					finalData = finalData +","+"'"+custTypeSplitted[i]+"'";
				}
			}
		}
		else
		{
			finalData ="'"+dataTobeFormatted+"'";
		}
		return finalData;
	}

	public static String getFormattedCustomerCode(ArrayList <String>finalCustomerList)
	{
		String customerCodeFormatted ="";
		for(int i=0;i<finalCustomerList.size();i++)
		{
			if(customerCodeFormatted.matches(""))
			{
				customerCodeFormatted="'"+finalCustomerList.get(i)+"'";
			}
			else
			{
				customerCodeFormatted=customerCodeFormatted+", "+"'"+finalCustomerList.get(i)+"'";
			}
		}
		return customerCodeFormatted;
	}

	//delete a folder and all the files inside it
	public static void deleteTempFolderRecursive(File fileOrDirectory)
	{
		if (fileOrDirectory.isDirectory())
			for (File child : fileOrDirectory.listFiles())
				deleteTempFolderRecursive(child);

		fileOrDirectory.delete();
	}
	public static void getAppVersionDbVersion(TextView txtVersion, Context mContext)
	{
		txtVersion.setText(getAppVersion(mContext) + "~"
				+ getDBVersion(mContext));
	}
	public static void getAppLogo(ImageView headerLogo)
	{
		if(Constants.logoBmp != null){
			headerLogo.setVisibility(View.VISIBLE);
			headerLogo.setImageBitmap(Constants.logoBmp);
		}else{
			headerLogo.setVisibility(View.GONE);
		}
	}
	public static double decimal3round(double value, int places)
	{
		long factor = (long) Math.pow(10, places);
		value = value * factor;
		long tmp = Math.round(value);
		return (double) tmp / factor;
	}

	public static void print_log_d(String key, String value)
	{
		try
		{
			if(BuildConfig.DEBUG)
				Log.d(""+ key,  "" + value);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
	public static void print_log_d(String key_value)
	{
		try
		{
			if(BuildConfig.DEBUG)
				Log.d("PRINT_LOG_DATA_",   "" + key_value);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public static void show_msg_alert(final Activity context, final String msg, final boolean isFinishing)
	{
		try
		{
			AlertDialog.Builder alertDialog = new AlertDialog.Builder(context, R.style.MyDialog);

			TextView tvCPopup = new TextView(context);
			tvCPopup.setText(context.getResources().getString(R.string.app_name));
			tvCPopup.setGravity(Gravity.CENTER);
			tvCPopup.setTextColor(context.getResources().getColor(R.color.white));
			tvCPopup.setTextSize(14);
			tvCPopup.setBackgroundColor(context.getResources().getColor(R.color.red));
			int margin = 15;
			tvCPopup.setPadding(0, margin*2, 0, margin*2);
			alertDialog.setCustomTitle(tvCPopup);
			alertDialog.setMessage("\n" + msg);

			// On pressing Settings button
			alertDialog.setPositiveButton("Ok", new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int which) {
					dialog.dismiss();

					if(isFinishing)
						context.finish();
				}
			});

			alertDialog.setCancelable(!isFinishing);
			// Showing Alert Message
			alertDialog.show();
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public static String twoDigitRoundOff(String value)
	{
		try
		{
			print_Log_d("twoDigitRoundOff", String.format("%.2f", Double.parseDouble(value)));
			return String.format("%.2f", Double.parseDouble(value));
		}
		catch (Exception e)
		{
			e.printStackTrace();
			return value;
		}
	}
	public static String twoDigitRoundOff_(final double value)
	{
		try
		{
			print_Log_d("twoDigitRoundOff", String.format("%.2f", Double.parseDouble(value+"")));
			return String.format("%.2f", Double.parseDouble(value+""));
		}
		catch (Exception e)
		{
			e.printStackTrace();
			return value+"";
		}
	}
	public static Uri getCacheImagePath(String fileName, Activity myActivity) {
		File image = new File(fileName);
		return getUriForFile(myActivity, myActivity.getPackageName() + ".provider", image);
	}
	public static String getAlphaNumericString(final int n)
	{

		// chose a Character random from this String
		final String AlphaNumericString = System.currentTimeMillis() +  "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
				+ "0123456789"
				+ "abcdefghijklmnopqrstuvxyz";

		// create StringBuffer size of AlphaNumericString
		final StringBuilder sb = new StringBuilder(n);

		for (int i = 0; i < n; i++) {

			// generate a random number between
			// 0 to AlphaNumericString variable length
			final int index
					= (int)(AlphaNumericString.length()
					* Math.random());

			// add Character one by one in end of sb
			sb.append(AlphaNumericString
					.charAt(index));
		}

		return sb.toString();
	}

	public static String getFileContent(final Resources resources, final int rawId)
			throws IOException
	{
		final InputStream is = resources.openRawResource(rawId);
		final int size = is.available();
		final byte[] buffer = new byte[size]; // Read the entire asset into a
		// local byte buffer.
		is.read(buffer);
		is.close();
		return new String(buffer, "UTF-8"); // Convert the buffer into a
		// string.
	}
}
