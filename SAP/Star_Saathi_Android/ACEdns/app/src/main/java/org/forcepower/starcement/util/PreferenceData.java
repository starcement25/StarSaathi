package org.forcepower.starcement.util;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.preference.PreferenceManager;

import static org.forcepower.starcement.Utils_.print_Log_d;

/**
 * This class will store the state of the user whether he has voted or not.
 */

public final class PreferenceData
{

	static final String CHECKIN_OUT = "check";
	static final String CHECKIN_TIME ="check_in_time";
	static final String EMPLOYEE_ID ="emp_id";
	static final String ROUTE_CODE ="route_code";
	static final String EMPLOYEE_NAME ="emp_name";
	static final String KEY_SET__direcory_path ="KEY_SET__direcory_path";
	static final String EMPLOYEE_login_status ="EMPLOYEE_login_status";

	public static SharedPreferences getSharedPreferences(Context ctx) {
		return PreferenceManager.getDefaultSharedPreferences(ctx);
	}


	public static void setCheckInOutId(Context ctx, String id) {
		Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(CHECKIN_OUT, id);
		editor.apply();
	}
	public static void setCheckInTime(Context ctx, String checkintime) {
		Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(CHECKIN_TIME, checkintime);
		editor.apply();
	}

	public static String getCheckInOutId(Context ctx) {
		return getSharedPreferences(ctx).getString(CHECKIN_OUT, "0");
	}	
	public static String getCheckInTime(Context ctx) {
		return getSharedPreferences(ctx).getString(CHECKIN_TIME, "0");
	}

	public static void setCheckInOutEmpCode(Context ctx, String id)
	{
		Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(EMPLOYEE_ID, id);
		editor.apply();
	}
	public static String getCheckInOutEmpCode(Context ctx)
	{
		return getSharedPreferences(ctx).getString(EMPLOYEE_ID, "0");
	}

	public static void setCheckInOutRouteCode(Context ctx, String id)
	{
		Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(ROUTE_CODE, id);
		editor.apply();
	}
	public static String getCheckInOutRouteCode(Context ctx)
	{
		return getSharedPreferences(ctx).getString(ROUTE_CODE, "0");
	}

	public static void setCheckInOutEmpName(Context ctx, String id)
	{
		Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(EMPLOYEE_NAME, id);
		editor.apply();
	}
	public static String getCheckInOutEmpName(Context ctx)
	{
		return getSharedPreferences(ctx).getString(EMPLOYEE_NAME, "0");
	}

	public static void set_direcory_path(Context ctx, String _direcory_path)
	{
		print_Log_d("_direcory_path", _direcory_path);
		Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(KEY_SET__direcory_path, _direcory_path);
		editor.apply();
	}
	public static String get_direcory_path(Context ctx)
	{
		return getSharedPreferences(ctx).getString(KEY_SET__direcory_path, "");
	}

	public static void setLoginStatus(Context ctx, boolean status)
	{
		Editor editor = getSharedPreferences(ctx).edit();
		editor.putBoolean(EMPLOYEE_login_status, status);
		editor.apply();
	}
	public static boolean getLoginStatus(Context ctx)
	{
		return getSharedPreferences(ctx).getBoolean(EMPLOYEE_login_status, false);
	}
}
