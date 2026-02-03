package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.changeDateFormat_;
import static org.forcepower.starcement.constants.Constants.acedns_show_offline_order_list;
import static org.forcepower.starcement.constants.Constants.acedns_show_order_list_for_subdealer;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.view.MotionEvent;
import android.view.View;
import android.widget.DatePicker;
import android.widget.ExpandableListView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.TrackOrderAdapterSub;
import org.forcepower.starcement.bean.TrackOrderModelChild;
import org.forcepower.starcement.bean.TrackOrderModelTop;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class SubDealerTrackOrderNewActivityNew extends AceDnsParentActivity
{
    private TextView tvAppOrder, tvOfflineOrder, tvApporderLine, tvOfflineorderLine,
            tv_start_date, tv_end_date;
    private String from_date  = "", to_date = "";
    private ExpandableListView expListView;

    private Activity mContext_;
    
    private ArrayList<TrackOrderModelTop>off_data_list = new ArrayList<>();
    private TrackOrderAdapterSub oAdapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_track_order);

        mContext_ = this;

        try
        {
            if(get_user_type(mContext_).equalsIgnoreCase("broker"))
            {
                selected_customr_code = get_selected_customer_code(mContext_);
            }
            else
            {
                selected_customr_code = get_emp_or_customer_code(mContext_);
            }
            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("TRACK ORDERS");

            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });

            tvAppOrder = (TextView) findViewById(R.id.tvAppOrder);
            tvOfflineOrder = (TextView) findViewById(R.id.tvOfflineOrder);
            tvApporderLine = (TextView) findViewById(R.id.tvApporderLine);
            tvOfflineorderLine = (TextView) findViewById(R.id.tvOfflineorderLine);
            expListView = (ExpandableListView) findViewById(R.id.exlvTrackOrders);
            expListView.setEmptyView(findViewById(R.id.empty_text_view));

            //
            tv_start_date = (TextView) findViewById(R.id.tv_start_date);
            tv_end_date = (TextView) findViewById(R.id.tv_end_date);

            String current_date = new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault()).format(new Date());
            Calendar cldr = Calendar.getInstance();
            cldr.add(Calendar.DATE, -30);  // number of days

            int dayOfMonth = cldr.get(Calendar.DAY_OF_MONTH);
            int monthOfYear = cldr.get(Calendar.MONTH);
            int year = cldr.get(Calendar.YEAR);

            String previous_date = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
            previous_date = changeDateFormat_(previous_date, "dd/MM/yyyy", "MMM dd, yyyy");

            tv_start_date.setText(previous_date + "");
            tv_end_date.setText(current_date + "");

            tvAppOrder.setOnTouchListener(new View.OnTouchListener() {
                @Override
                public boolean onTouch(View v, MotionEvent event) {
                    if(HTTPUtils.isConnectionPossible(mContext_))
                    {
                        new TRANS_GetAppOrder_Asynctask(mContext_).execute("");
                    }
                    else
                    {
                        show_msg_Dialog(mContext_, check_internet_connection);
                    }
                    return false;
                }
            });
            tvOfflineOrder.setOnTouchListener(new View.OnTouchListener() {
                @Override
                public boolean onTouch(View v, MotionEvent event) {
                    if(HTTPUtils.isConnectionPossible(mContext_))
                    {
                        new TRANS_GetOffline_Asynctask(mContext_).execute();
                    }
                    else
                    {
                        show_msg_Dialog(mContext_, check_internet_connection);
                    }
                    return false;
                }
            });

            // Listview Group click listener
            expListView.setOnGroupClickListener(new ExpandableListView.OnGroupClickListener() {
                @Override
                public boolean onGroupClick(ExpandableListView parent, View v, int groupPosition,
                                            long id) {
                    int childCountWay2 = parent.getExpandableListAdapter().getChildrenCount(groupPosition);
                    if (childCountWay2<1)
                    {
                        // do whatever you want
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
            });

            if(get_user_type(mContext_).equalsIgnoreCase("broker"))
            {
                tvHeaderText.setText(get_selected_customer_name(mContext_));
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void show_calendar(final View view) {
        try
        {
            final Calendar cldr = Calendar.getInstance();
            int day = cldr.get(Calendar.DAY_OF_MONTH);
            int month = cldr.get(Calendar.MONTH);
            int year = cldr.get(Calendar.YEAR);
            if(view instanceof LinearLayout)
            {
                try
                {
                    if(view.getTag().toString().matches("start_date"))
                    {
                        String date = tv_start_date.getText().toString();

                        DateFormat formatter = new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault());
                        cldr.setTime(formatter.parse(date));

                        day = cldr.get(Calendar.DAY_OF_MONTH);
                        month = cldr.get(Calendar.MONTH);
                        year = cldr.get(Calendar.YEAR);
                    }
                    else
                    {
                        String date = tv_end_date.getText().toString();

                        DateFormat formatter = new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault());
                        cldr.setTime(formatter.parse(date));

                        day = cldr.get(Calendar.DAY_OF_MONTH);
                        month = cldr.get(Calendar.MONTH);
                        year = cldr.get(Calendar.YEAR);
                    }
                }
                catch (Exception e)
                {
                    e.printStackTrace();
                }
            }
            // date picker dialog
            final DatePickerDialog picker_to = new DatePickerDialog(mContext_,
                    new DatePickerDialog.OnDateSetListener() {
                        @Override
                        public void onDateSet(DatePicker datePicker, int year, int monthOfYear, int dayOfMonth) {
                            String date = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
                            date = changeDateFormat_(date, "dd/MM/yyyy", "MMM dd, yyyy");
                            if(view instanceof LinearLayout)
                            {
                                if(view.getTag().toString().matches("start_date"))
                                {
                                    tv_start_date.setText(date);
                                }
                                else
                                {
                                    tv_end_date.setText(date);
                                }

                            }

                            if(tv_start_date.getText().toString().trim().matches(""))
                            {
                                Toast.makeText(mContext_, "Please select start date.", Toast.LENGTH_SHORT).show();
                            }
                            else if(tv_end_date.getText().toString().trim().matches(""))
                            {
                                Toast.makeText(mContext_, "Please select end date.", Toast.LENGTH_SHORT).show();
                            }
                            else if(!HTTPUtils.isConnectionPossible(mContext_))
                            {
                                Toast.makeText(mContext_, check_internet_connection, Toast.LENGTH_SHORT).show();
                            }
                            else
                            {
                                new TRANS_GetAppOrder_Asynctask(mContext_).execute("");
                            }
                        }
                    }, year, month, day);
            picker_to.show();
//            cldr.add(Calendar.YEAR, -18);
//            picker_to.getDatePicker().setMaxDate(cldr.getTimeInMillis());
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    @Override
    public void onDestroy()
    {
        super.onDestroy();
    }

    @Override
    public void onResume() {
        super.onResume();
        // setting list adapter
        if(HTTPUtils.isConnectionPossible(mContext_))
        {
            new TRANS_GetAppOrder_Asynctask(mContext_).execute("");
        }
    }
    
    public final class TRANS_GetOffline_Asynctask extends AsyncTaskCoroutine<String, String> {
        Context mContext;
        JSONObject jo = new JSONObject();

        public TRANS_GetOffline_Asynctask(Context mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            Utils.showProgressDialog(mContext_, "Updating please wait..");

        }

        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            off_data_list.clear();
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                try
                {
                    String url = acedns_show_offline_order_list;
                    print_log_d("acedns_show_offline_order_list ", url);

                    ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);
                    nameValuePairs.add(new BasicNameValuePair("the_id", selected_customr_code));
                    nameValuePairs.add(new BasicNameValuePair("user_type", get_user_type(mContext)));
                    from_date = tv_start_date.getText().toString();
                    to_date = tv_end_date.getText().toString();
                    nameValuePairs.add(new BasicNameValuePair("start_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", from_date)));
                    nameValuePairs.add(new BasicNameValuePair("end_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", to_date)));

                    POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);

                    print_log_d("acedns_show_offline_order_list_nameValuePairs ", nameValuePairs.toString());
                    print_log_d("acedns_show_offline_order_listPOST_result ", POST_result);

                    jo = new JSONObject(POST_result);
                    final String order_data = jo.optString("order_data");
                    final JSONArray jsonArray = new JSONArray(order_data);
                    for(int i=0; i<jsonArray.length(); i++)
                    {
                        final JSONObject e = jsonArray.getJSONObject(i);
                        final TrackOrderModelTop tM = new TrackOrderModelTop();

                        tM.setApporderno(e.optString("erporderno")); //**
                        tM.setErporderno(e.optString("erporderno"));
                        tM.setCustomer_code(e.optString("customer_code"));
                        tM.setDns_customer_code(e.optString("dns_customer_code"));
                        tM.setErporderdt(e.optString("erporderdt"));
                        tM.setOrder_for(e.optString("order_for"));
                        tM.setStatus(e.optString("status"));
                        tM.setProd_code(e.optString("prod_code"));
                        tM.setDns_prod_code(e.optString("dns_prod_code"));
                        tM.setProd_display_name(e.optString("prod_display_name"));

                        String qty = e.optString("qty")+"";
                        if(qty.matches("") || qty.equalsIgnoreCase("null"))
                        {
                            qty = "0";
                        }
                        tM.setQty("X " + qty);
                        tM.setOrder_full_date_time(e.optString("erporderdt")); //**
                        tM.setFreight(e.optString("freight"));
                        tM.setDestination_address(e.optString("destination_name")); //**
                        tM.setIs_confirmed_material_received(e.optString("is_confirmed_material_received"));
                        tM.setOrder_challan_data(new ArrayList<>());
                        final ArrayList<TrackOrderModelChild>child_list = new ArrayList<>();

                        final String order_challan_data = e.optString("order_challan_data");
                        if(!order_challan_data.isEmpty())
                        {
                            final JSONArray jaChallan = new JSONArray(order_challan_data);

                            if(jaChallan.length() > 0)
                            {

                                for(int j=0; j<jaChallan.length(); j++)
                                {
                                    final JSONObject ec = jaChallan.getJSONObject(j);
                                    final TrackOrderModelChild tC = new TrackOrderModelChild();
                                    tC.setApporderno(ec.optString("apporderno"));
                                    tC.setChallandt(ec.optString("challandt"));
                                    tC.setChallanno(ec.optString("challanno"));
                                    tC.setChallanqty(ec.optString("challanqty"));
                                    tC.setDriverno(ec.optString("driverno"));
                                    tC.setErporderdt(ec.optString("erporderdt"));
                                    tC.setErporderno(ec.optString("erporderno"));
                                    tC.setProd_code(ec.optString("prod_code"));
                                    tC.setQty(ec.optString("qty"));
                                    tC.setTruckno(ec.optString("truckno"));
                                    tC.setProd_display_name(ec.optString("prod_display_name"));
                                    tC.setIs_confirmed_challan_material_received(ec.optString("is_confirmed_challan_material_received"));
                                    tC.setCh_uid(ec.optString("ch_uid"));
                                    tC.setTransporter_name(ec.optString("transporter_name"));
                                    tC.setCh_quantity_no_of_bags(ec.optString("ch_quantity_no_of_bags"));
                                    tC.setCh_status(ec.optString("ch_status"));
                                    tC.setColour_code(ec.optString("colour_code"));

                                    child_list.add(tC);
                                }
                            }
                        }

                        tM.setOrder_challan_data(child_list);
                        off_data_list.add(tM);
                    }
                }
                catch (Exception e)
                {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try
            {

                oAdapter = new TrackOrderAdapterSub(mContext, off_data_list);
                expListView.setAdapter(oAdapter);
            }
            catch (Exception e)
            {
                e.printStackTrace();
            }
            finally
            {
                Utils.cancelProgressDialog();

                tvOfflineOrder.setTextColor(Color.RED);
                tvOfflineorderLine.setBackgroundColor(Color.RED);

                tvAppOrder.setTextColor(Color.GRAY);
                tvApporderLine.setBackgroundColor(Color.GRAY);
            }
        }
    }

    public final class TRANS_GetAppOrder_Asynctask extends AsyncTaskCoroutine<String, String> {
        Context mContext;
        JSONObject jo = new JSONObject();

        public TRANS_GetAppOrder_Asynctask(Context mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            Utils.showProgressDialog(mContext_, "Updating please wait..");
        }
        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            off_data_list.clear();
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                try
                {
                    final String url = acedns_show_order_list_for_subdealer;
                    print_log_d("acedns_show_order_list_for_subdealer ", url);

                    final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);
                    nameValuePairs.add(new BasicNameValuePair("the_id", selected_customr_code));
                    nameValuePairs.add(new BasicNameValuePair("user_type", get_user_type(mContext)));
                    from_date = tv_start_date.getText().toString();
                    to_date = tv_end_date.getText().toString();
                    nameValuePairs.add(new BasicNameValuePair("start_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", from_date)));
                    nameValuePairs.add(new BasicNameValuePair("end_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", to_date)));

                    POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);

                    print_log_d("acedns_show_order_list_for_subdealer_nameValuePairs ", nameValuePairs.toString());
                    print_log_d("acedns_show_order_list_for_subdealer_result ", POST_result);

                    jo = new JSONObject(POST_result);
                    final String subdealer_order_data = jo.optString("subdealer_order_data");
                    final JSONArray jsonArray = new JSONArray(subdealer_order_data);
                    for(int i=0; i<jsonArray.length(); i++)
                    {
                        final JSONObject e = jsonArray.getJSONObject(i);
                        final TrackOrderModelTop tM = new TrackOrderModelTop();

                        tM.setApporderno(e.optString("order_id"));
                        String qty = e.optString("qty")+"";
                        if(qty.matches("") || qty.equalsIgnoreCase("null"))
                        {
                            qty = "0";
                        }
                        tM.setQty("X " + qty);

                        tM.setStatus(e.optString("status"));
                        tM.setProd_display_name(e.optString("prod_display_name"));
                        tM.setDestination_address(e.optString("destination_address"));
//                        tM.setOrder_full_date_time( Utils.changeDateFormat("yyyy-MM-dd HH:mm:ss", "MMM dd, yyyy", e.optString("order_date")));
                        tM.setOrder_full_date_time(e.optString("order_date"));
                        tM.setFreight(e.optString("freight"));

                        tM.setOrder_challan_data(new ArrayList<>());

                        final String subdealer_challan_data = e.optString("subdealer_challan_data");
                        if(!subdealer_challan_data.isEmpty())
                        {
                            final JSONArray jaChallan = new JSONArray(subdealer_challan_data);
                            final ArrayList<TrackOrderModelChild>child_list = new ArrayList<>();

                            for(int j=0; j<jaChallan.length(); j++)
                            {
                                final JSONObject ec = jaChallan.getJSONObject(j);
                                final TrackOrderModelChild tC = new TrackOrderModelChild();
                                tC.setApporderno(ec.optString("apporderno"));
                                tC.setChallandt(ec.optString("challandt"));
                                tC.setChallanno(ec.optString("challanno"));
                                tC.setChallanqty(ec.optString("challanqty"));
                                tC.setDriverno(ec.optString("driverno"));
                                tC.setErporderdt(ec.optString("erporderdt"));
                                tC.setErporderno(ec.optString("erporderno"));
                                tC.setProd_code(ec.optString("prod_code"));
                                tC.setQty(ec.optString("qty"));
                                tC.setTruckno(ec.optString("truckno"));
                                tC.setProd_display_name(ec.optString("prod_display_name"));
                                tC.setIs_confirmed_challan_material_received(ec.optString("is_confirmed_challan_material_received"));
                                tC.setCh_uid(ec.optString("ch_uid"));
                                tC.setTransporter_name(ec.optString("transporter_name"));
                                tC.setCh_quantity_no_of_bags(ec.optString("ch_quantity_no_of_bags"));
                                tC.setCh_status(ec.optString("ch_status"));
                                tC.setColour_code(ec.optString("colour_code"));

                                child_list.add(tC);
                            }

                            tM.setOrder_challan_data(child_list);
                        }
                        off_data_list.add(tM);
                    }
                }
                catch (Exception e)
                {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }
        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try
            {

                oAdapter = new TrackOrderAdapterSub(mContext, off_data_list);
                expListView.setAdapter(oAdapter);
            }
            catch (Exception e)
            {
                e.printStackTrace();
            }
            finally
            {
                Utils.cancelProgressDialog();

                tvOfflineOrder.setTextColor(Color.GRAY);
                tvOfflineorderLine.setBackgroundColor(Color.GRAY);

                tvAppOrder.setTextColor(Color.RED);
                tvApporderLine.setBackgroundColor(Color.RED);
            }
        }
    }
}
