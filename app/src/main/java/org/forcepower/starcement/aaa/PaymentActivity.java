package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_branch_wise_pg_rollout;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.adapter.LedgerListViewAdapter.convertDate;
import static org.forcepower.starcement.constants.Constants.acedns_dashboard_webLink;
import static org.forcepower.starcement.constants.Constants.acedns_star_ledger_by_id;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import org.forcepower.starcement.R;
import org.forcepower.starcement.Utils_;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.ConnectionDetector;
import org.forcepower.starcement.util.HTTPUtils;
import org.json.JSONObject;

public final class PaymentActivity extends AppCompatActivity
{
    Activity mContext;
    ListView rvLedger;
    TextView tvLedgerBalanc, tvOS2;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_payment);

        mContext= this;
        if(get_user_type(mContext).equalsIgnoreCase("broker"))
        {
            selected_customr_code = get_selected_customer_code(mContext);
        }
        else
        {
            selected_customr_code = get_emp_or_customer_code(mContext);
        }

        tvLedgerBalanc = (TextView) findViewById(R.id.tvLedgerBalance);
        tvOS2 = (TextView) findViewById(R.id.tvOS2);
        rvLedger = (ListView) findViewById(R.id.rvLedger);
        TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
        tvHeaderText.setText("PAYMENT");

        ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
        ivHeaderBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
        LinearLayout llHeaderDetails = (LinearLayout) findViewById(R.id.llHeaderDetails);
        llHeaderDetails.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                try
                {
                    Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(acedns_dashboard_webLink));
                    startActivity(browserIntent);
                }
                catch (Exception e)
                {
                    e.printStackTrace();
                }
            }
        });
        try
        {

            ConnectionDetector cd= new ConnectionDetector(mContext);
            Boolean isInternetPresent=cd.isConnectingToInternet();
            if(isInternetPresent)
            {
                new TRANS_GetLedgerDetails_Asynctask(mContext).execute("");
            }
            else
            {

                Utils_.closeApp(mContext,check_internet_connection);
            }
            //
            TextView tv_make_payment = (TextView) findViewById(R.id.tv_make_payment);
            if(get_user_type(mContext).equalsIgnoreCase("broker"))
            {
                tvHeaderText.setText(get_selected_customer_name(mContext));
                tv_make_payment.setVisibility(View.GONE);
            }
            else
            {
                tv_make_payment.setVisibility(View.VISIBLE);
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void confirmLedger(View view) {
        try
        {
            ConnectionDetector cd= new ConnectionDetector(mContext);
            Boolean isInternetPresent=cd.isConnectingToInternet();
            if(isInternetPresent)
            {
                Intent intent = new Intent(this, LedgerMonthWiseActivity.class);
                startActivity(intent);
            }
            else
            {
                Utils_.closeApp(mContext,check_internet_connection);
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void make_payment(View view) {
        try
        {
            if(get_branch_wise_pg_rollout(mContext).matches("ACTIVE"))
            {
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    Intent intent = new Intent(mContext, PayAmountActivity.class);
                    intent.putExtra("initialBalance", tvLedgerBalanc.getText().toString().trim()+"");
                    intent.putExtra("coming_from", "payment");
                    startActivity(intent);
                }
                else
                {
                    Utils_.closeApp(mContext,check_internet_connection);
                }
            }
            else
            {
                show_msg_Dialog(mContext, "Coming Soon");
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    
    public final class TRANS_GetLedgerDetails_Asynctask extends AsyncTaskCoroutine<String, String> {
        Activity mContext;
        ProgressDialog mStepProgressDialog;
        String balance = "00.00", date = "";
        public TRANS_GetLedgerDetails_Asynctask(Activity mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(mContext);
            mStepProgressDialog.setMessage("Please wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();
        }
        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            try
            {
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    try
                    {
                        String url = acedns_star_ledger_by_id+ selected_customr_code;
                        print_log_d("PRINT_LEDGER_URL_130", url);
                        POST_result = HTTPUtils.getDataByHTTP_GET(mContext, url);

                        JSONObject jo = new JSONObject(POST_result);
                        if(jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes"))
                        {
                            try
                            {
                                String ledger_balance_data = jo.getString("ledger_balance_data");
                                JSONObject joBal = new JSONObject(ledger_balance_data);
                                //need to add dns_customer_code
                                balance = joBal.getString("balance");
                                date = joBal.getString("date");
                            }
                            catch (Exception e)
                            {
                                e.printStackTrace();
                            }
                        }

                    }
                    catch (Exception e)
                    {
                        POST_result = "Network Failure";
                    }
                }
            }
            catch (Exception e)
            {
                e.printStackTrace();
            }
            return POST_result;
        }
        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);

            try
            {
                if(result.equalsIgnoreCase("Network Failure"))
                {
                    Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                }
                else
                {
                    tvLedgerBalanc.setText("" + balance);
                    tvOS2.setText("OUTSTANDING AS ON " +  convertDate(date));
                }

            }
            catch (Exception e)
            {
                e.printStackTrace();
            }
            finally
            {
                mStepProgressDialog.dismiss();
            }
        }
    }
}
