package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.changeDateFormat_;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.order_query_data_download;
import static org.forcepower.starcement.constants.Constants.order_query_data_status_update;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.os.Bundle;
import android.os.Handler;
import android.view.View;
import android.widget.DatePicker;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.PlaceOrderAdapter_Date;
import org.forcepower.starcement.bean.PlaceOrderModel;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.MyCallback;
import org.forcepower.starcement.util.Utils;
import org.forcepower.starcement.util.VolleyApiCAll;
import org.json.JSONArray;
import org.json.JSONObject;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.Locale;
import java.util.Map;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class PlaceOrderActivity extends AceDnsParentActivity
{
    private TextView tvHeaderText,
            tv_start_date, tv_end_date;
    private String from_date  = "", to_date = "";

    private Activity mContext;
    private ArrayList<PlaceOrderModel> invoice_list = new ArrayList<>();
    private PlaceOrderAdapter_Date mAdapter;
    private RecyclerView rv_Pending;
    private int page_no_P = 1;
    protected Handler handler_p;
    // The minimum amount of items to have below your current scroll position
    // before loading more.
    private int visibleThreshold = 5;
    private int lastVisibleItem, totalItemCount;
    private boolean loading;
    private MyCallback callback;

    public void setLoaded() {
        loading = false;
    }

    public void setLoading() {
        loading = true;
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_place_order);
        mContext= this;
        try
        {
            if(get_user_type(mContext).equalsIgnoreCase("broker"))
            {
                selected_customr_code = get_selected_customer_code(mContext);
            }
            else //if(get_user_type(mContext).equalsIgnoreCase("dealer"))
            {
                selected_customr_code = get_emp_or_customer_code(mContext);
            }

            tv_start_date = (TextView) findViewById(R.id.tv_start_date);
            tv_end_date = (TextView) findViewById(R.id.tv_end_date);
            tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("Order Enquiry");

            if(get_user_type(mContext).equalsIgnoreCase("broker"))
            {
                tvHeaderText.setText(get_selected_customer_name(mContext));
            }

            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });

            //
            //
            final String current_date = new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault()).format(new Date());
            Calendar cldr = Calendar.getInstance();
            cldr.add(Calendar.DATE, -7);  // number of days

            int dayOfMonth = cldr.get(Calendar.DAY_OF_MONTH);
            int monthOfYear = cldr.get(Calendar.MONTH);
            int year = cldr.get(Calendar.YEAR);

            String previous_date = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
            previous_date = changeDateFormat_(previous_date, "dd/MM/yyyy", "MMM dd, yyyy");

            tv_start_date.setText(previous_date + "");
            tv_end_date.setText(current_date + "");
            //
            handler_p = new Handler();

            rv_Pending = (RecyclerView) findViewById(R.id.recycler_view);
            rv_Pending.setHasFixedSize(true);
            rv_Pending.setLayoutManager(new LinearLayoutManager(mContext));

            invoice_list = new ArrayList<>();
            mAdapter = new PlaceOrderAdapter_Date(mContext, invoice_list);
            rv_Pending.setAdapter(mAdapter);

            callback = new MyCallback() {

                public void callbackCall() {
                    // callback code goes here
                    onLoadMore_();
                }
            };
            if (rv_Pending.getLayoutManager() instanceof LinearLayoutManager) {

                final LinearLayoutManager linearLayoutManager = (LinearLayoutManager) rv_Pending
                        .getLayoutManager();
                rv_Pending
                        .addOnScrollListener(new RecyclerView.OnScrollListener() {
                            @Override
                            public void onScrolled(RecyclerView recyclerView,
                                                   int dx, int dy) {
                                super.onScrolled(recyclerView, dx, dy);

                                totalItemCount = linearLayoutManager.getItemCount();
                                lastVisibleItem = linearLayoutManager
                                        .findLastVisibleItemPosition();
                                if(totalItemCount > 9)
                                    if (!loading
                                            && totalItemCount <= (lastVisibleItem + visibleThreshold)) {
                                        // End has been reached
                                        // Do something
                                        callback.callbackCall();

                                        loading = true;
                                    }
                            }
                        });
            }

            //
            reload(null);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void show_calendar(final View view) {
        try
        {
            Calendar cldr = Calendar.getInstance();
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
            final DatePickerDialog picker_to = new DatePickerDialog(mContext,
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
                                Toast.makeText(mContext, "Please select start date.", Toast.LENGTH_SHORT).show();
                            }
                            else if(tv_end_date.getText().toString().trim().matches(""))
                            {
                                Toast.makeText(mContext, "Please select end date.", Toast.LENGTH_SHORT).show();
                            }
                            else if(!HTTPUtils.isConnectionPossible(mContext))
                            {
                                Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                            }
                            else
                            {
                                reload(null);
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

    public void reload(View view) {
        try
        {
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                final Map<String, String> jsonObject = new HashMap<>();

                jsonObject.put("customer_code", selected_customr_code);
                from_date = tv_start_date.getText().toString();
                to_date = tv_end_date.getText().toString();

                jsonObject.put("start_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", from_date));
                jsonObject.put("end_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", to_date));

                page_no_P = 1;
                invoice_list = new ArrayList<>();
                mAdapter = new PlaceOrderAdapter_Date(mContext, invoice_list);
                rv_Pending.setAdapter(mAdapter);
//                jsonObject.put("page_no", page_no_P + "");

                doPOSTcall_order_history(jsonObject, "initial");
            }
            else
            {
                Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    private void doPOSTcall_order_history(final Map<String, String> jsonObject, final String type) {
        try
        {
//            final String page = jsonObject.get("page_no") + "";

            final String url = order_query_data_download;
            print_Log_d("dj4rue_P ", jsonObject+"");
            print_Log_d("dj4rue_U ", url+"");

            //making post call
            final VolleyApiCAll volleyApiCAll = new VolleyApiCAll(mContext);
            volleyApiCAll.makeServiceCallPost(jsonObject, url, new VolleyApiCAll.VolleyCallback()
            {
                @Override
                public void onSuccessResponse(final String result)
                {
                    JSONObject jo = new JSONObject();

                    try
                    {
                        print_Log_d("dj4rue_R ", result+"");
                        invoice_list.clear();

                        if(type.matches("add_p"))
                        {
                            //   remove progress item
                            invoice_list.remove(invoice_list.size() - 1);
                            mAdapter.notifyItemRemoved(invoice_list.size());
                        }

                        if(result.matches("VOLLEY_NETWORK_ERROR"))
                        {
                            Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                        }
                        else
                        {
                            try
                            {

                                jo = new JSONObject(result);
                                if(jo.has("process_status"))
                                {
                                    if(jo.getString("process_status").toLowerCase().matches("yes"))
                                    {
                                        final String order_query_data = jo.getString("order_query_data");
                                        final JSONArray ja = new JSONArray(order_query_data);
                                        for(int i=0; i<ja.length(); i++)
                                        {
                                            final JSONObject e = ja.getJSONObject(i);

                                            final PlaceOrderModel iM = new PlaceOrderModel();
                                            iM.setOrder_query_id(e.optString("order_query_id"));
                                            iM.setOrder_id(e.optString("order_id"));
                                            iM.setProd_name(e.optString("prod_name"));
                                            iM.setQty_bags(e.optString("qty_bags"));
                                            iM.setDate_and_time(e.optString("date_and_time"));
                                            iM.setRssd_name(e.optString("rssd_name"));
                                            iM.setQuery_date(e.optString("query_date"));
                                            iM.setDate_of_lifting(e.optString("date_of_lifting"));
                                            iM.setRemarks(e.optString("remarks"));
                                            iM.setStatus_from_app(e.optString("status_from_app"));
                                            iM.setStatus_remarks(e.optString("status_remarks"));
                                            invoice_list.add(iM);
                                        }
                                    }
                                }
                            }
                            catch (Exception e)
                            {
                                e.printStackTrace();
                            }
                            finally
                            {

                                page_no_P++;
                                if(mAdapter.getItemCount() == 0)
                                {
                                    mAdapter.setFilter(invoice_list);
                                }
                                else if(invoice_list.size() > 0)
                                {
                                    mAdapter.notifyItemInserted(invoice_list.size());
                                }

                                if(mAdapter.getItemCount() > 0)
                                {
                                    rv_Pending.setVisibility(View.VISIBLE);
                                }
                                else
                                {
                                    rv_Pending.setVisibility(View.GONE);
                                }

                                if(invoice_list.size() == 0 && page_no_P == 2)
                                    show_msg_alert(mContext, jo.optString("process_message"), false);
                            }
                        }

                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                    finally
                    {
                        //
                        setLoaded();
                    }
                }
            });

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void onLoadMore_() {
        try
        {
            if(page_no_P > 1)
            {
                invoice_list.add(null);
                mAdapter.notifyItemInserted(invoice_list.size() - 1);

                handler_p.postDelayed(new Runnable() {
                    @Override
                    public void run() {
                        final Map<String, String> jsonObject = new HashMap<>();

                        jsonObject.put("customer_code", selected_customr_code);
                        from_date = tv_start_date.getText().toString();
                        to_date = tv_end_date.getText().toString();

                        jsonObject.put("start_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", from_date));
                        jsonObject.put("end_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", to_date));

//                        jsonObject.put("page_no", page_no_P + "");

                        doPOSTcall_order_history(jsonObject, "add_p");
                    }
                }, 2000);
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public class Downloading extends AsyncTaskCoroutine<String, String> {
        private Activity mContext;
        private JSONObject jo = new JSONObject();
        private String order_query_id, status_from_app, status_remarks;

        public Downloading(final Activity context,
                           final String order_query_id, final String status_from_app, final String status_remarks) {
            this.mContext = context;
            this.order_query_id = order_query_id;
            this.status_from_app = status_from_app;
            this.status_remarks = status_remarks;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            Utils.showProgressDialog(mContext, "Updating...");
        }

        @Override
        public String doInBackground(String... par)
        {
            String POST_result = "";
            try
            {
                final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(1);
                if(get_user_type(mContext).equalsIgnoreCase("broker")) //not needed here
                {
                    nameValuePairs.add(new BasicNameValuePair("customer_id", get_selected_dealer_sap_code(mContext)));
                }
                else
                {
                    nameValuePairs.add(new BasicNameValuePair("customer_id", get_dealer_id(mContext)));
                }

                nameValuePairs.add(new BasicNameValuePair("order_query_id", order_query_id));
                nameValuePairs.add(new BasicNameValuePair("status_from_app", status_from_app));
                nameValuePairs.add(new BasicNameValuePair("status_remarks", status_remarks));

                final String url = order_query_data_status_update;
                POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);
                jo = new JSONObject(POST_result);

                print_Log_d("pe4w5_U ", url);
                print_Log_d("pe4w5_P ", nameValuePairs.toString());
                print_Log_d("pe4w5_R ", POST_result);
            }
            catch (Exception e)
            {
                print_Log_d("pe4w5_Err1 ", e.toString());

                e.printStackTrace();
            }

            return null;
        }

        @Override
        public void onPostExecute(String result)
        {
            super.onPostExecute(result);
            try
            {
                show_msg_Dialog(mContext, jo.optString("process_message"));
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
