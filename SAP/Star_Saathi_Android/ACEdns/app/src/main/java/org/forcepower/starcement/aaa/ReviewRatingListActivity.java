package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.dealer_site_visit_list;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.DealerVisitAdapter;
import org.forcepower.starcement.bean.DealerVisitModel;
import org.forcepower.starcement.bean.DealerVisitModel;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class ReviewRatingListActivity extends AceDnsParentActivity
{
    private ListView mLisview;
    private Activity mContext_;
    private ArrayList<DealerVisitModel>ledger_data_list = new ArrayList<>();
    private DealerVisitAdapter oAdapter;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_dealer_visit);

        mContext_ = this;

        try
        {
            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("Sales Visit feedback");

            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });

            mLisview = (ListView) findViewById(R.id.mLisview);

            if(get_user_type(mContext_).equalsIgnoreCase("broker"))
            {
                tvHeaderText.setText(get_selected_customer_name(mContext_));
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        finally
        {
            if(HTTPUtils.isConnectionPossible(mContext_))
            {
                new TRANS_GetDealerList_Asynctask(mContext_).execute("");
            }
            else
            {
                show_msg_Dialog(mContext_, check_internet_connection);
            }
        }
    }


    public final class TRANS_GetDealerList_Asynctask extends AsyncTaskCoroutine<String, String>
    {
        private Activity mContext;
        private JSONObject jo = new JSONObject();
        public TRANS_GetDealerList_Asynctask(final Activity mContext) {
            this.mContext = mContext;
            ledger_data_list.clear();
        }

        @Override
        public void onPreExecute()
        {
            super.onPreExecute();
            Utils.showProgressDialog(mContext_, "Updating please wait..");
        }
        @Override
        public String doInBackground(final String... params)
        {
            String POST_result = "";

            if (HTTPUtils.isConnectionPossible(mContext))
            {
                try
                {
                    final String url = dealer_site_visit_list;
                    print_log_d("kaw33_U ", url);

                    final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);

                    if(get_user_type(mContext).equalsIgnoreCase("broker"))
                    {
                        print_Log_d("kaw33_b1 ", get_selected_dealer_sap_code(mContext) + " ");
                        nameValuePairs.add(new BasicNameValuePair("customer_code", get_selected_dealer_sap_code(mContext)));

                    }
                    else
                    {
                        nameValuePairs.add(new BasicNameValuePair("customer_code", get_dealer_id(mContext)));
                        print_Log_d("kaw33_b2 ", get_dealer_id(mContext) + " ");

                    }

//                    nameValuePairs.add(new BasicNameValuePair("start_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", toDayDate)));

                    POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);

                    print_log_d("kaw33_P ", nameValuePairs.toString());
                    print_log_d("kaw33_R ", POST_result);

                    jo = new JSONObject(POST_result);
                    final String sales_team_visit_data = jo.optString("sales_team_visit_data");
                    final JSONArray jsonArray = new JSONArray(sales_team_visit_data);
                    for(int i=0; i<jsonArray.length(); i++)
                    {
                        final JSONObject e = jsonArray.getJSONObject(i);
                        final DealerVisitModel lM = new DealerVisitModel();

                        lM.setEmp_code(e.optString("emp_code"));
                        lM.setEmp_name(e.optString("emp_name"));
                        lM.setVisit_datetime(e.optString("visit_datetime"));

                        ledger_data_list.add(lM);
                    }
                    //
                    oAdapter = new DealerVisitAdapter(mContext, ledger_data_list);

                }
                catch (Exception e)
                {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }
        @Override
        public void onPostExecute(String result)
        {
            super.onPostExecute(result);
            try
            {
                if( result.equalsIgnoreCase("Network Failure"))
                {
                    show_msg_alert(mContext, check_internet_connection, true);
                }
                else if(jo.optString("process_status").equalsIgnoreCase("YES"))
                {
                    mLisview.setAdapter(oAdapter);
                    mLisview.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                        @Override
                        public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                            Intent intent = new Intent(mContext, ReviewRatingActivity.class);
                            intent.putExtra("emp_code", ledger_data_list.get(i).getEmp_code());
                            intent.putExtra("visit_datetime", ledger_data_list.get(i).getVisit_datetime());
                            intent.putExtra("emp_name", ledger_data_list.get(i).getEmp_name());
                            startActivity(intent);
                        }
                    });
                }
                else
                {
                    show_msg_alert(mContext, jo.optString("process_message"), true);
                }
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
    }
}