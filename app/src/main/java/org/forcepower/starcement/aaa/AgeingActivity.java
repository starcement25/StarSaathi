package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.dealerwise_ageing_data;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;

import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.AgeingAdapter;
import org.forcepower.starcement.bean.AgeingModel;
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

public final class AgeingActivity extends AceDnsParentActivity
{
    private TextView tv_start_date;
    private ListView mLisview;

    private Activity mContext_;
    private ArrayList<AgeingModel>ledger_data_list = new ArrayList<>();
    private AgeingAdapter oAdapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_ageing);

        mContext_ = this;

        try
        {
            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("Ageing");

            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });

            mLisview = (ListView) findViewById(R.id.mLisview);

            //
            tv_start_date = (TextView) findViewById(R.id.tv_start_date);

            final String current_date = new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault()).format(new Date());

            tv_start_date.setText(current_date + "");


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
                new TRANS_GetAgeing_Asynctask(mContext_).execute("");
            }
            else
            {
                show_msg_Dialog(mContext_, check_internet_connection);
            }
        }
    }

    public final class TRANS_GetAgeing_Asynctask extends AsyncTaskCoroutine<String, String> {
        private Activity mContext;
        private JSONObject jo = new JSONObject();
        private String toDayDate = "";

        public TRANS_GetAgeing_Asynctask(final Activity mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            Utils.showProgressDialog(mContext_, "Updating please wait..");
            toDayDate = tv_start_date.getText().toString();
        }

        @Override
        public String doInBackground(final String... params)
        {
            String POST_result = "";
            ledger_data_list.clear();
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                try
                {
                    final String url = dealerwise_ageing_data;
                    print_log_d("dealerwise_ageing_data ", url);

                    final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);

                    if(get_user_type(mContext).equalsIgnoreCase("broker"))
                    {
                        print_Log_d("KEY_SET_1172_SAP_CODE ", get_selected_dealer_sap_code(mContext) + " ");
                        nameValuePairs.add(new BasicNameValuePair("customer_code", get_selected_dealer_sap_code(mContext)));

                    }
                    else
                    {
                        nameValuePairs.add(new BasicNameValuePair("customer_code", get_dealer_id(mContext)));
                        print_Log_d("KEY_SET_1179_SAP_CODE ", get_dealer_id(mContext) + " ");

                    }

//                    nameValuePairs.add(new BasicNameValuePair("start_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", toDayDate)));

                    POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);

                    print_log_d("dealerwise_ageing_data_nameValuePairs ", nameValuePairs.toString());
                    print_log_d("dealerwise_ageing_data_result ", POST_result);

                    jo = new JSONObject(POST_result);
                    final String ageing_data = jo.optString("ageing_data");
                    final JSONArray jsonArray = new JSONArray(ageing_data);
                    for(int i=0; i<jsonArray.length(); i++)
                    {
                        final JSONObject e = jsonArray.getJSONObject(i);
                        final AgeingModel lM = new AgeingModel();

                        lM.setDocument_date(e.optString("document_date"));
                        lM.setDocument_no(e.optString("document_no"));
                        lM.setInv_amount(e.optString("inv_amount"));

                        ledger_data_list.add(lM);
                    }
                    //
                    oAdapter = new AgeingAdapter(mContext, ledger_data_list);

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
                if(ledger_data_list.size() > 0)
                {
                    mLisview.setAdapter(oAdapter);
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