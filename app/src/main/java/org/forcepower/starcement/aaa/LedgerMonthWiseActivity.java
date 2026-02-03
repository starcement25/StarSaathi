package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.acedns_star_show_month_wize_ledger_by_id;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import org.forcepower.starcement.LedgerMonthWiseAdapter;
import org.forcepower.starcement.R;
import org.forcepower.starcement.Utils_;
import org.forcepower.starcement.commonDatabaseHelper;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.ConnectionDetector;
import org.forcepower.starcement.util.HTTPUtils;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

public final class LedgerMonthWiseActivity extends AceDnsParentActivity
{
    Context mContext;
    ListView rvLedger;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_ledger_month_wise);
        try
        {
            mContext=this;
            if(get_user_type(mContext).equalsIgnoreCase("broker"))
            {
                selected_customr_code = get_selected_customer_code(mContext);
            }
            else
            {
                selected_customr_code = get_emp_or_customer_code(mContext);
            }
            rvLedger = (ListView) findViewById(R.id.rvLedger);
            rvLedger.setEmptyView(findViewById(R.id.empty_text_view));
            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("CONFIRM LEDGER BALANCE");

            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });
            LinearLayout llHeaderDetails = (LinearLayout) findViewById(R.id.llHeaderDetails);
            llHeaderDetails.setVisibility(View.INVISIBLE);

            ConnectionDetector cd= new ConnectionDetector(mContext);
            Boolean isInternetPresent=cd.isConnectingToInternet();
            if(isInternetPresent)
            {
                new TRANS_GetLedgerMonthWise_Asynctask(mContext).execute("");
            }
            else
            {
//                List<commonDatabaseHelper>all= starCementDB.getAllLedgerDetails(); //last 50 result
//                if(all != null && all.size() > 0 &&
//                        !starCementDB.getLedgerBalance("balance").matches(""))
//                {
//                    LedgerMonthWiseAdapter ledgerAdapter = new LedgerMonthWiseAdapter(LedgerMonthWiseActivity.this, all);
//                    rvLedger.setAdapter(ledgerAdapter);
//                    ledgerAdapter.notifyDataSetChanged();
//
//                    if(starCementDB.getLedgerBalance("balance").matches(""))
//                        tvLedgerBalanc.setText("00.00");
//                    else
//                        tvLedgerBalanc.setText("" + starCementDB.getLedgerBalance("balance"));
//
//                    tvOS2.setText("* LAST 50 TRANSACTIONS AS ON " + convertDate(starCementDB.getLedgerBalance("date")));
//                }
                Utils_.closeApp(mContext,check_internet_connection);
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    
    public final class TRANS_GetLedgerMonthWise_Asynctask extends AsyncTaskCoroutine<String, String> {
        Context mContext;
        ProgressDialog mStepProgressDialog;
        LedgerMonthWiseAdapter mAdapter;
        List<commonDatabaseHelper> all = new ArrayList<>();
        public TRANS_GetLedgerMonthWise_Asynctask(Context mContext) {
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
                        String url = acedns_star_show_month_wize_ledger_by_id+ selected_customr_code;
                        all = new ArrayList<>();
                        print_log_d("PRINT_LEDGER_URL_130", url);
                        POST_result = HTTPUtils.getDataByHTTP_GET(mContext, url);

                        JSONObject jo = new JSONObject(POST_result);
                        if(jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes"))
                        {

                            try
                            {
                                JSONObject jsonObject = new JSONObject(jo.getString("check_ledger_data"));

                                Iterator<?> keys_Bottom = jsonObject.keys();
                                while( keys_Bottom.hasNext() ) {
                                    String key = (String)keys_Bottom.next();
                                    if ( jsonObject.get(key) instanceof JSONObject )
                                    {
//                                        JSONArray ja = new JSONArray(jsonObject.getString(key));


                                        JSONObject e = new JSONObject(jsonObject.getString(key));

                                        commonDatabaseHelper data = new commonDatabaseHelper();

                                        data.setItem0(e.optString("customer_code")); // customer_code
                                        data.setItem1(e.optString("dns_customer_code")); // dns_customer_code
                                        data.setItem2(e.optString("total_amount_dr")); // total_amount_dr
                                        data.setItem3(e.optString("total_amount_cr")); //total_amount_cr
                                        data.setItem4("CONFIRM"); //status
                                        data.setItem5(e.optString("ledger_year_month_day")); //ledger_year_month_day
                                        data.setItem6(e.optString("ledger_of")); //ledger_of

                                        // Adding contact to list
                                        all.add(data);


                                    }
                                }


                                mAdapter = new LedgerMonthWiseAdapter(LedgerMonthWiseActivity.this, all);
//
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
                    if(all.size() > 0)
                    {
                        rvLedger.setAdapter(mAdapter);
                    }
                    else
                    {
                        Toast.makeText(mContext, "Details not found.", Toast.LENGTH_SHORT).show();
                    }
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
