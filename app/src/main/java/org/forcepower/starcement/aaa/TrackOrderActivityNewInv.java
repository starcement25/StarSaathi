package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.changeDateFormat_;
import static org.forcepower.starcement.constants.Constants.acedns_show_offline_order_list;
import static org.forcepower.starcement.constants.Constants.acedns_star_order_details_by_id_inv;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.isSubmittedFeedback;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.ProgressDialog;
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

import org.forcepower.starcement.CategoryClass;
import org.forcepower.starcement.ItemDetailsClass;
import org.forcepower.starcement.R;
import org.forcepower.starcement.StarCementDB;
import org.forcepower.starcement.adapter.ExpandableListInvAdapter;
import org.forcepower.starcement.adapter.TrackOrderAdapter;
import org.forcepower.starcement.bean.TrackOrderModelChild;
import org.forcepower.starcement.bean.TrackOrderModelTop;
import org.forcepower.starcement.commonDatabaseHelper;
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
import java.util.List;
import java.util.Locale;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class TrackOrderActivityNewInv extends AceDnsParentActivity
{
    private TextView tvAppOrder, tvOfflineOrder, tvApporderLine, tvOfflineorderLine,
            tv_start_date, tv_end_date;
    private String from_date  = "", to_date = "";
    private ExpandableListView expListView;
    private ProgressDialog mStepProgressDialog;
    private Activity mContext_;

    private ArrayList<TrackOrderModelTop>off_data_list = new ArrayList<>();
    private ExpandableListInvAdapter mAdapter;
    private TrackOrderAdapter oAdapter;
    private StarCementDB starCementDB;

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
            starCementDB = new StarCementDB(this);
            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("TRACK ORDERS (Invoice Data)");

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
                        new TRANS_GetOrderDetails_Asynctask(mContext_).execute("");
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
            isSubmittedFeedback = false;
            // setting list adapter
            if(HTTPUtils.isConnectionPossible(mContext_))
            {
                new TRANS_GetOrderDetails_Asynctask(getApplicationContext()).execute("");
            }

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
                                new TRANS_GetOrderDetails_Asynctask(getApplicationContext()).execute("");
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

    private static CategoryClass createCategory(String name, String group_name, String Qty,
                                                String prod_desc, String order_full_date_time,
                                                String freight, String destination_address,
                                                String is_confirmed_material_received, final String erporderno) {
        return new CategoryClass(name, group_name, Qty, prod_desc, order_full_date_time,
                freight, destination_address, is_confirmed_material_received, erporderno);
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
        if (starCementDB != null) {
            starCementDB.close();
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        // setting list adapter
        if(HTTPUtils.isConnectionPossible(mContext_) && isSubmittedFeedback)
        {
            isSubmittedFeedback = false;
            new TRANS_GetOrderDetails_Asynctask(getApplicationContext()).execute("");
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
            mStepProgressDialog = new ProgressDialog(mContext_);
            mStepProgressDialog.setMessage("Please wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();

        }

        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            off_data_list.clear();
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try
                {
                    String url = acedns_show_offline_order_list;
                    print_log_d("acedns_show_offline_order_list ", url);

                    ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);
                    nameValuePairs.add(new BasicNameValuePair("the_id", selected_customr_code));
                    from_date = tv_start_date.getText().toString();
                    to_date = tv_end_date.getText().toString();
                    nameValuePairs.add(new BasicNameValuePair("start_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", from_date)));
                    nameValuePairs.add(new BasicNameValuePair("end_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", to_date)));
                    nameValuePairs.add(new BasicNameValuePair("user_type", get_user_type(mContext)));
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

                        tM.setApporderno(e.optString("apporderno")); //**
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
                        // append plant to freight display if plant present
                        String freightVal = e.optString("freight", "");
                        String plantVal = e.optString("plant", "");
                        if(plantVal != null && !plantVal.trim().isEmpty()) {
                            tM.setFreight(freightVal + " ( " + plantVal+ " )" );
                        } else {
                            tM.setFreight(freightVal);
                        }
                        tM.setDestination_address(e.optString("destination_address")); //**
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

                oAdapter = new TrackOrderAdapter(mContext, off_data_list);
                expListView.setAdapter(oAdapter);
            }
            catch (Exception e)
            {
                e.printStackTrace();
            }
            finally
            {
                mStepProgressDialog.dismiss();

                tvOfflineOrder.setTextColor(Color.RED);
                tvOfflineorderLine.setBackgroundColor(Color.RED);

                tvAppOrder.setTextColor(Color.GRAY);
                tvApporderLine.setBackgroundColor(Color.GRAY);
            }
        }
    }

    public final class TRANS_GetOrderDetails_Asynctask extends AsyncTaskCoroutine<String, String> {
        private Context mContext;
        public TRANS_GetOrderDetails_Asynctask(Context mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(mContext_);
            mStepProgressDialog.setMessage("Please wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();

        }

        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    final String url = acedns_star_order_details_by_id_inv;
                    print_log_d("PRINT_TRACK_ORDERS_URL_", url);

                    ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);
                    nameValuePairs.add(new BasicNameValuePair("the_id", selected_customr_code));
                    from_date = tv_start_date.getText().toString();
                    to_date = tv_end_date.getText().toString();
                    nameValuePairs.add(new BasicNameValuePair("start_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", from_date)));
                    nameValuePairs.add(new BasicNameValuePair("end_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", to_date)));
                    nameValuePairs.add(new BasicNameValuePair("user_type", get_user_type(mContext)));

                    POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);
                    print_log_d("POST_result_nameValuePairs ", nameValuePairs.toString());
                    print_log_d("POST_result ", POST_result);
                    starCementDB.deleteTable("T_APPERPDO");
                    starCementDB.deleteTable("T_DOCHALLAN");
                    final JSONObject jo = new JSONObject(POST_result);
                    if(jo.has("process_status") && jo.optString("process_status").equalsIgnoreCase("yes"))
                    {
                        final String order_data = jo.optString("order_data");
                        final JSONArray ja = new JSONArray(order_data);

                        for(int i=0; i<ja.length(); i++)
                        {
                            final JSONObject e = ja.getJSONObject(i);
                            print_log_d("ACEdns_PRINT_100", e.optString("apporderno"));
                            // derive freight value: append plant when present
                            String freightVal = e.optString("freight", "");
                            String plantVal = e.optString("plant", "");
                            String freightToStore;
                            if(plantVal != null && !plantVal.trim().isEmpty()) {
                                freightToStore = freightVal + " ( " + plantVal + " )";
                            } else {
                                freightToStore = freightVal;
                            }

                            //insert to T_APPERPDO table
                            starCementDB.insertOrderData(
                                    e.optString("apporderno"),
                                    e.optString("erporderno"),
                                    e.optString("customer_code"),
                                    e.optString("dns_customer_code"),
                                    e.optString("erporderdt"),
                                    e.optString("order_for"),
                                    e.optString("status"),
                                    e.optString("prod_code"),
                                    e.optString("dns_prod_code"),
                                    e.optString("prod_display_name"),
                                    e.optString("qty"),
                                    e.optString("order_full_date_time"),
                                    freightToStore, // store freight + plant here (if plant present)
                                    e.optString("destination_address"),
                                    e.optString("is_confirmed_material_received")
                            );

                            final String order_invoice_data = e.optString("order_invoice_data");
                            final JSONArray jaChallan = new JSONArray(order_invoice_data);

                            for(int j=0; j<jaChallan.length(); j++)
                            {
                                final JSONObject eChallan = jaChallan.getJSONObject(j);

                                print_log_d("ACEdns_PRINT_180", e.optString("apporderno"));

                                starCementDB.insertChallan(
                                        eChallan.optString("apporderno"),
                                        eChallan.optString("invdt"), //challandt
                                        eChallan.optString("invno"), //challanno
                                        eChallan.optString("invqty"), //challanqty
                                        eChallan.optString("driverno"),
                                        eChallan.optString("erporderdt"),
                                        eChallan.optString("erporderno"),
                                        eChallan.optString("prod_code"),
                                        eChallan.optString("qty"),
                                        eChallan.optString("truckno"),
                                        eChallan.optString("prod_display_name"),
                                        "YES", //eChallan.optString("is_confirmed_challan_material_received")
                                        eChallan.optString("ch_uid"),
                                        eChallan.optString("destination"), //transporter_name
                                        eChallan.optString("ch_quantity_no_of_bags"),
                                        eChallan.optString("ch_status"),
                                        eChallan.optString("colour_code")
                                );
                            }

                        }
                    }

                }
                catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                // setting list adapter
                final ArrayList<CategoryClass> catList = new ArrayList<>();
                final List<commonDatabaseHelper> category = starCementDB.getAllAppOrderDetails();
                for (commonDatabaseHelper cat : category)
                {
                    final CategoryClass cat1 = createCategory(cat.getItem0(), cat.getItem1(), cat.getItem2(), cat.getItem3(), cat.getItem4(),
                            cat.getItem5(), cat.getItem6(), cat.getItem7(), cat.getItem8());
                    final List<commonDatabaseHelper> sub_category = starCementDB.getSubCategoryAppOrder(cat.getItem0());
                    final List<ItemDetailsClass> cResult = new ArrayList<>();

                    for (commonDatabaseHelper scat : sub_category)
                    {
                        //-------------------------------------------challanno, date, quantity, TrackNumber, DriverContact....
                        // 9->transporter_name, ch_quantity_no_of_bags, ch_status
                        final ItemDetailsClass item = new ItemDetailsClass(scat.getItem0(), scat.getItem1(),
                                scat.getItem2(), scat.getItem3(), scat.getItem4(), scat.getItem5(), scat.getItem6(),
                                scat.getItem7(), scat.getItem8(), scat.getItem9(), scat.getItem10(), scat.getItem11(),
                                scat.getItem12(), scat.getItem13());
                        cResult.add(item);
                    }
                    cat1.setItemList(cResult);
                    catList.add(cat1);
                }

                //
                mAdapter = new ExpandableListInvAdapter(mContext_, catList);
                expListView.setAdapter(mAdapter);
            }
            catch (Exception e) {
                e.printStackTrace();
            }
            finally {
                mStepProgressDialog.dismiss();

                tvAppOrder.setTextColor(Color.RED);
                tvApporderLine.setBackgroundColor(Color.RED);

                tvOfflineOrder.setTextColor(Color.GRAY);
                tvOfflineorderLine.setBackgroundColor(Color.GRAY);
            }
        }
    }
}
