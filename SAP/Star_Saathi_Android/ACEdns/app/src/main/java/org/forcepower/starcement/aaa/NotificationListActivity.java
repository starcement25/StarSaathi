package org.forcepower.starcement.aaa;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import android.os.Bundle;
import android.os.Handler;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;
import org.forcepower.starcement.NotificationsListViewAdapter;
import org.forcepower.starcement.R;
import org.forcepower.starcement.Utils_;
import org.forcepower.starcement.commonDatabaseHelper;
import org.forcepower.starcement.database.DatabaseHelperSqlite;
import org.forcepower.starcement.util.HTTPUtils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.io.File;
import java.util.ArrayList;
import java.util.List;

import static org.forcepower.starcement.SharedPrefData.getNotiLastDate;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.SharedPrefData.setNotiLastDate;
import static org.forcepower.starcement.constants.Constants.acedns_show_notifications;
import static org.forcepower.starcement.constants.Constants.branch_code;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.make_notification_read_v3;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;
import static org.forcepower.starcement.util.Utils.print_log_d;

public final class NotificationListActivity extends AceDnsParentActivity implements SwipeRefreshLayout.OnRefreshListener
{
    Context mContext;
    DatabaseHelperSqlite NotiDB;
    ListView rvLedger;
    TextView tvLedgerBalanc, tvOS2;
    SwipeRefreshLayout chartListSwipeRefreshLayout;
    ArrayList<NameValuePair> mHttpParamPairs;
    NotificationsListViewAdapter notiAdapter;
    List<commonDatabaseHelper>all = new ArrayList<>();
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_notification_list);

        try
        {
            mContext = this;
            if(get_user_type(mContext).equalsIgnoreCase("broker"))
            {
                selected_customr_code = get_selected_customer_code(mContext);
            }
            else
            {
                selected_customr_code = get_emp_or_customer_code(mContext);
            }
            NotiDB = new DatabaseHelperSqlite(mContext);

            chartListSwipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.chartListSwipeRefreshLayout);
            chartListSwipeRefreshLayout.setOnRefreshListener(this);
            chartListSwipeRefreshLayout.setColorSchemeResources(R.color.red, R.color.white, R.color.red, R.color.white);

            tvLedgerBalanc = (TextView) findViewById(R.id.tvLedgerBalance);
            tvOS2 = (TextView) findViewById(R.id.tvOS2);
            rvLedger = (ListView) findViewById(R.id.rvLedger);
            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("NOTIFICATIONS");

            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });
            LinearLayout llHeaderDetails = (LinearLayout) findViewById(R.id.llHeaderDetails);
            llHeaderDetails.setVisibility(View.INVISIBLE);

            notiAdapter = new NotificationsListViewAdapter(this, all);
            rvLedger.setEmptyView(findViewById(R.id.empty_text_view));
            rvLedger.setAdapter(notiAdapter);

            all= NotiDB.getAllNotiList(""); //ALL list
            if(all != null && all.size() > 0 )
            {
                notiAdapter = new NotificationsListViewAdapter(this, all);
                rvLedger.setAdapter(notiAdapter);
            }
            else
            {
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    new GetNotiList_Asynctask(mContext).execute("");
                }
                else
                {
                    Utils_.closeApp(mContext,check_internet_connection);
                }
            }
            //
            rvLedger.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> parent, View view, int index, long id) {
                    if(all.get(index).getItem5().equalsIgnoreCase("READ"))
                    {
                        if(all.get(index).getItem6().equalsIgnoreCase("PDF")) //m_file_type
                        {
                            Intent intent = new Intent(mContext, NotificationPdfActivity.class);
                            intent.putExtra("id", all.get(index).getItem0()); //id
                            intent.putExtra("title", all.get(index).getItem1()); //title
                            intent.putExtra("message", all.get(index).getItem2()); //message
                            intent.putExtra("imageUrl", all.get(index).getItem3()); //image
                            startActivity(intent);
                        }
                        else
                        {
                            Intent intent = new Intent(mContext, NotificationDetailsActivity.class);
                            intent.putExtra("title", all.get(index).getItem1()); //title
                            intent.putExtra("message", all.get(index).getItem2()); //message
                            intent.putExtra("image", all.get(index).getItem3()); //image
                            startActivity(intent);
                        }
                    }
                    else if (HTTPUtils.isConnectionPossible(mContext))
                    {
                        new UpdateReadUnread_Asynctask(mContext, all.get(index).getItem0(), index).execute("");
                    }
                    else
                    {
                        Utils_.closeApp(mContext,check_internet_connection);
                    }
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    @Override
    public void onRefresh()
    {
        try
        {
            chartListSwipeRefreshLayout.setRefreshing(false);
            Handler mHandler = new Handler();
            mHandler.postDelayed(new Runnable()
            {
                public void run()
                {
                    if (HTTPUtils.isConnectionPossible(mContext))
                    {
                        all= NotiDB.getAllNotiList("");
                        if(all != null && all.size() > 0 )
                        {
                            setNotiLastDate(mContext, all.get(0).getItem4());
                        }
                        new GetNotiList_Asynctask(mContext).execute("");
                    }
                    else
                    {
                        Utils_.closeApp(mContext,check_internet_connection);
                    }
                }
            }, 10);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void reload(View view)
    {
        try
        {
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                new GetNotiList_Asynctask(mContext).execute("");
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
    
    public final class GetNotiList_Asynctask extends AsyncTaskCoroutine<String, String>
    {
        Context mContext;
        ProgressDialog mStepProgressDialog;
        JSONObject jo = new JSONObject();
        public GetNotiList_Asynctask(Context mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute()
        {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(mContext);
            mStepProgressDialog.setMessage("Please wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();
        }
        @Override
        public String doInBackground(final String... params)
        {
            String POST_result = "";
            try
            {
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    try
                    {
                        String url = acedns_show_notifications;
                        print_log_d("acedns_show_notifications ", url);
                        mHttpParamPairs = new ArrayList<>(2);
                        mHttpParamPairs.add(new BasicNameValuePair("the_branch_code", branch_code));
                        mHttpParamPairs.add(new BasicNameValuePair("the_id", get_emp_or_customer_code(mContext)));
//                        File file = new File(get_direcory_path(mContext)+"Notifications.db");
//                        if(file.exists())
//                        {
//                            if(!getNotiLastDate(mContext).matches(""))
//                            {
//                                all= NotiDB.getAllNotiList(""); //ALL list
//                                if(all != null && all.size() > 0)
//                                mHttpParamPairs.add(new BasicNameValuePair("last_update_datetime", getNotiLastDate(mContext)));
//                            }
//                        }

                        POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);
                        print_log_d("acedns_show_notifications_201 ", mHttpParamPairs.toString());
                        print_log_d("POST  ", POST_result);

                        jo = new JSONObject(POST_result);
                        if(jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes"))
                        {
                            try
                            {
                                String ledger_balance_data = jo.getString("notification_data");
                                JSONArray jsonArray = new JSONArray(ledger_balance_data);
                                NotiDB.deleteTable("firebase_notifications");
                                for(int i=0; i<jsonArray.length(); i++)
                                {
                                    JSONObject joBal = jsonArray.getJSONObject(i);
                                    NotiDB.addNotifications(joBal.getString("nid"),
                                            joBal.getString("m_title"),
                                            joBal.getString("m_message"),
                                            joBal.getString("m_image_link"),
                                            joBal.getString("n_date_time"),
                                            joBal.getString("m_file_type"),
                                            joBal.getString("the_noti_sts")
                                    );
                                }
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
        public void onPostExecute(String result)
        {
            super.onPostExecute(result);
            try
            {
                if(result.equalsIgnoreCase("Network Failure"))
                {
                    Toast.makeText(mContext, "Try again...", Toast.LENGTH_SHORT).show();
                }
                else
                {
                    all= NotiDB.getAllNotiList(""); //ALL list

                    if(all.size() > 0)
                    {
                        notiAdapter = new NotificationsListViewAdapter(NotificationListActivity.this, all);
                        rvLedger.setAdapter(notiAdapter);
                    }
                    else
                    {
                        Toast.makeText(mContext, jo.optString("process_message"), Toast.LENGTH_SHORT).show();
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
    
    public final class UpdateReadUnread_Asynctask extends AsyncTaskCoroutine<String, String>
    {
        ProgressDialog mStepProgressDialog;
        Context mContext;
        JSONObject jo = new JSONObject();
        String noti_id = "";
        int index = -1;
        public UpdateReadUnread_Asynctask(Context mContext, String noti_id, int index) {
            this.mContext = mContext;
            this.noti_id = noti_id;
            this.index = index;
        }

        @Override
        public void onPreExecute()
        {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(mContext);
            mStepProgressDialog.setMessage("Please wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();
        }
        @Override
        public String doInBackground(final String... params)
        {
            String POST_result = "";
            try
            {
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    try
                    {
                        String url = make_notification_read_v3;
                        print_log_d("make_notification_read_v3 ", url);
                        mHttpParamPairs = new ArrayList<>(2);
                        mHttpParamPairs.add(new BasicNameValuePair("the_id", selected_customr_code));
                        mHttpParamPairs.add(new BasicNameValuePair("noti_id", noti_id));


                        POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);

                        jo = new JSONObject(POST_result);
                        if(jo.optString("process_status").equalsIgnoreCase("YES"))
                        {
                            NotiDB.updateNotiStatus(noti_id, "read");
                            all.get(index).setItem5("read");
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
        public void onPostExecute(String result)
        {
            super.onPostExecute(result);
            try
            {
                if(jo.optString("process_status").equalsIgnoreCase("YES"))
                {

                    if(all.get(index).getItem6().equalsIgnoreCase("PDF")) //m_file_type
                    {
                        Intent intent = new Intent(mContext, NotificationPdfActivity.class);
                        intent.putExtra("id", all.get(index).getItem0()); //id
                        intent.putExtra("title", all.get(index).getItem1()); //title
                        intent.putExtra("message", all.get(index).getItem2()); //message
                        intent.putExtra("imageUrl", all.get(index).getItem3()); //image
                        startActivity(intent);
                    }
                    else
                    {
                        Intent intent = new Intent(mContext, NotificationDetailsActivity.class);
                        intent.putExtra("title", all.get(index).getItem1()); //title
                        intent.putExtra("message", all.get(index).getItem2()); //message
                        intent.putExtra("image", all.get(index).getItem3()); //image
                        startActivity(intent);
                    }
                    //
                    notiAdapter.setFilter(all);
                }
                else
                {
                    Toast.makeText(mContext, jo.optString("process_message"), Toast.LENGTH_SHORT).show();
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
    @Override
    public void onResume()
    {
        super.onResume();

        if(NotiDB == null)
            NotiDB = new DatabaseHelperSqlite(this);
    }
    @Override
    public void onDestroy()
    {
        super.onDestroy();

        if(NotiDB != null)
            NotiDB.close();
    }
}
