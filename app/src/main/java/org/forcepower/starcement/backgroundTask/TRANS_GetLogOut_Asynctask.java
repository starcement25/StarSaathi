package org.forcepower.starcement.backgroundTask;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.set_dealer_submit_form;
import static org.forcepower.starcement.SharedPrefData.set_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.set_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.set_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.set_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.set_user_type;
import static org.forcepower.starcement.Utils_.deleteRecursive;
import static org.forcepower.starcement.constants.Constants.acedns_star_clear_allocation_by_id;
import static org.forcepower.starcement.constants.Constants.redirection;
import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;
import static org.forcepower.starcement.util.PreferenceData.setLoginStatus;
import static org.forcepower.starcement.util.Utils.print_log_d;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.widget.Toast;

import org.forcepower.starcement.aaa.LoginActivity;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.util.HTTPUtils;
import org.json.JSONObject;

import java.io.File;


public final class TRANS_GetLogOut_Asynctask extends AsyncTaskCoroutine<String, String> {
    Activity mContext;
    boolean clearAllocation = false;
    JSONObject jo = new JSONObject();
    ProgressDialog mStepProgressDialog;
    public TRANS_GetLogOut_Asynctask(Activity mContext) {
        this.mContext = mContext;
        mStepProgressDialog = new ProgressDialog(mContext);
        mStepProgressDialog.setMessage("Logging out..");
        mStepProgressDialog.setCancelable(false);
    }

    @Override
    public void onPreExecute() {
        super.onPreExecute();
        showLoader();
    }

    @Override
    public String doInBackground(final String... params) {
        String POST_result = "";
        try {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    final AceDnsDatabase aceDnsDatabase = new AceDnsDatabase(mContext);
                    aceDnsDatabase.TruncateTableByTableName("customer_master");
                    String url = acedns_star_clear_allocation_by_id+ get_emp_or_customer_code(mContext);
                    print_log_d("PRINT_LOGOUT", url);
                    POST_result = HTTPUtils.getDataByHTTP_GET(mContext, url);

                    jo = new JSONObject(POST_result);
                    if(jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes")) {
                        setLoginStatus(mContext, false);
                        clearAllocation = true;
                        File dir = new File(get_direcory_path(mContext));
                        deleteRecursive(dir);
                    }
                }
                catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
        }
        catch (Exception e) {
            e.printStackTrace();
        }
        return POST_result;
    }

    @Override
    public void onPostExecute(String result) {
        super.onPostExecute(result);
        try {
            setLoginStatus(mContext, false);
            set_user_type(mContext, "");
            set_emp_or_customer_code(mContext, "");
            set_selected_customer_code(mContext, "");
            set_selected_customer_name(mContext, "");
            set_dealer_submit_form(mContext, "NO");
            set_selected_dealer_sap_code(mContext, "");

            redirection = "";
            if(clearAllocation) {

                Toast.makeText(mContext, jo.optString("process_message")+"", Toast.LENGTH_SHORT).show();
                mContext.finishAffinity();

            }
            else {
                Intent intent = new Intent(mContext, LoginActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                mContext.startActivity(intent);
                mContext.finish();
            }
        }
        catch (Exception e) {
            e.printStackTrace();
        }
        finally {
            dismissLoader();
            File dir = new File(get_direcory_path(mContext));
            if(dir.exists()) {
                dir.delete();
            }
        }
    }

    public void showLoader() {
        if(mStepProgressDialog != null && !mStepProgressDialog.isShowing())
        {
            mStepProgressDialog.show();
        }
    }
    public void dismissLoader() {
        if(mStepProgressDialog != null && mStepProgressDialog.isShowing())
        {
            mStepProgressDialog.dismiss();
        }
    }
}
