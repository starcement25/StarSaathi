package org.forcepower.starcement;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.preference.PreferenceManager;

public final class SharedPrefData
{
	static final String SET_dns_emp_code ="SET_dns_emp_code";
	static final String KEY_SET_firebase_token ="KEY_SET_firebase_token";
	static final String KEY_MY__profile_image_url ="KEY_MY__profile_image_url";

	static final String KEY_SET_SAP_CODE ="KEY_SET_SAP_CODE";
	static final String KEY_theaccesscode ="KEY_theaccesscode";
	static final String KEY_SET_branch_wise_PG_rollout ="KEY_SET_branch_wise_PG_rollout";

	static final String KEY_show_dob_anniversary_list_json ="KEY_show_dob_anniversary_list_json";
	static final String KEY_DirectoryCount="KEY_DirectoryCount";
	static final String KEY_SET_DeviceID="KEY_SET_DeviceID";
	static final String SET_KEY__dealer_submit_form="SET_KEY__dealer_submit_form";
	static final String SET_association_Key="SET_association_Key";

	static final String SET_mobile_number="SET_mobile_number";
	static final String SET__dealer_id="SET__dealer_id";
	static final String SET__user_type="SET__user_type";
	static final String SET__emp_or_customer_codee="SET__emp_or_customer_codee";
	static final String SET_belong_dealer_code="SET_belong_dealer_code";
	static final String SET_belong_dealer_dns_code="SET_belong_dealer_dns_code";
	static final String SET_vbelong_dealer_name="SET_vbelong_dealer_name";
	static final String SET_logged_sub_dealer_name="SET_logged_sub_dealer_name";

	public static SharedPreferences getSharedPreferences(final Context ctx) {
		return PreferenceManager.getDefaultSharedPreferences(ctx);
	}


	public static void set_firebase_token(final Context ctx, final String _firebase_token) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(KEY_SET_firebase_token, _firebase_token);
		editor.apply();
	}
	public static String get_firebase_token(final Context ctx) {
		return getSharedPreferences(ctx).getString(KEY_SET_firebase_token, "dummy");
	}

	public static void set_profile_image_url(final Context ctx, final String HomeJsonData) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(KEY_MY__profile_image_url, HomeJsonData);
		editor.apply();
	}
	public static String get_profile_image_url(final Context ctx) {
		return getSharedPreferences(ctx).getString(KEY_MY__profile_image_url, "");
	}

	public static void set_selected_dealer_sap_code(final Context ctx, final String MemberId) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(KEY_SET_SAP_CODE, MemberId);
		editor.apply();
	}
	public static String get_selected_dealer_sap_code(final Context ctx) {
		return getSharedPreferences(ctx).getString(KEY_SET_SAP_CODE, "");
	}

	public static void set_server_current_date(final Context ctx, final String val) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(KEY_theaccesscode, val);
		editor.apply();
	}
	public static String get_server_current_date(final Context ctx) {
		return getSharedPreferences(ctx).getString(KEY_theaccesscode, "");
	}
	public static void set_device_evice_id(final Context ctx, final String memberid) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(KEY_SET_DeviceID, memberid);
		editor.apply();
	}
	public static String get_device_id(final Context ctx) {
		return getSharedPreferences(ctx).getString(KEY_SET_DeviceID, "");
	}
	public static void set_branch_wise_pg_rollout(final Context ctx, final String val) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(KEY_SET_branch_wise_PG_rollout, val);
		editor.apply();
	}
	public static String get_branch_wise_pg_rollout(final Context ctx) {
		return getSharedPreferences(ctx).getString(KEY_SET_branch_wise_PG_rollout, "INACTIVE");
	}
	public static void set_selected_customer_name(final Context ctx, final String DirectoryCount) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(KEY_DirectoryCount, DirectoryCount);
		editor.apply();
	}
	public static String get_selected_customer_name(final Context ctx) {
		return getSharedPreferences(ctx).getString(KEY_DirectoryCount, "");
	}
	public static void set_selected_customer_code(final Context ctx, final String show_dob_anniversary_list_json) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(KEY_show_dob_anniversary_list_json, show_dob_anniversary_list_json);
		editor.apply();
	}
	public static String get_selected_customer_code(final Context ctx) {
		return getSharedPreferences(ctx).getString(KEY_show_dob_anniversary_list_json, "");
	}

	public static void setNotiLastDate(final Context ctx, final String NotiLastDate) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(SET_association_Key, NotiLastDate);
		editor.apply();
	}
	public static void set_dealer_submit_form(final Context ctx, final String _dealer_submit_form) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(SET_KEY__dealer_submit_form, _dealer_submit_form);
		editor.apply();
	}
	public static String get_dealer_submit_form(final Context ctx) {
		return getSharedPreferences(ctx).getString(SET_KEY__dealer_submit_form, "NO");
	}
	public static void set_login_mobile_number(final Context ctx, final String mobile_number) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(SET_mobile_number, mobile_number);
		editor.apply();
	}
	public static String get_login_mobile_number(final Context ctx) {
		return getSharedPreferences(ctx).getString(SET_mobile_number, "");
	}
	public static void set_dealer_id(final Context ctx, final String _dealer_id) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(SET__dealer_id, _dealer_id);
		editor.apply();
	}
	public static String get_dealer_id(final Context ctx) {
		return getSharedPreferences(ctx).getString(SET__dealer_id, "");
	}
	public static void set_user_type(final Context ctx, final String _user_type) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(SET__user_type, _user_type);
		editor.apply();
	}
	public static String get_user_type(final Context ctx) {
		return getSharedPreferences(ctx).getString(SET__user_type, "dealer");
	}
	public static void set_emp_or_customer_code(final Context ctx, final String _emp_or_customer_code) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(SET__emp_or_customer_codee, _emp_or_customer_code);
		editor.apply();
	}
	public static String get_emp_or_customer_code(final Context ctx) {
		return getSharedPreferences(ctx).getString(SET__emp_or_customer_codee, "");
	}

	public static void set_belong_dealer_code (final Context ctx, final String val) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(SET_belong_dealer_code , val);
		editor.apply();
	}
	public static String get_belong_dealer_code (final Context ctx) {
		return getSharedPreferences(ctx).getString(SET_belong_dealer_code, "");
	}
	public static void set_belong_dealer_dns_code(final Context ctx, final String val) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(SET_belong_dealer_dns_code, val);
		editor.apply();
	}
	public static void set_belong_dealer_name(final Context ctx, final String val) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(SET_vbelong_dealer_name, val);
		editor.apply();
	}
	public static String get_belong_dealer_name(final Context ctx) {
		return getSharedPreferences(ctx).getString(SET_vbelong_dealer_name, "");
	}
	public static void set_logged_sub_dealer_name(final Context ctx, final String val) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(SET_logged_sub_dealer_name, val);
		editor.apply();
	}
	public static String get_logged_sub_dealer_name(final Context ctx) {
		return getSharedPreferences(ctx).getString(SET_logged_sub_dealer_name, "");
	}
	public static void set_dns_emp_code(final Context ctx, final String val) {
		final Editor editor = getSharedPreferences(ctx).edit();
		editor.putString(SET_dns_emp_code, val);
		editor.apply();
	}
	public static String get_dns_emp_code(final Context ctx) {
		return getSharedPreferences(ctx).getString(SET_dns_emp_code, "");
	}
}
