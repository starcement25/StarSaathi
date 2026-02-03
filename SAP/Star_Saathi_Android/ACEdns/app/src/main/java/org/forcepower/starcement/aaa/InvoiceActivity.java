package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.changeDateFormat_;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.acedns_show_invoice_list;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
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
import org.forcepower.starcement.adapter.InvoiceAdapter_Date;
import org.forcepower.starcement.bean.InvoiceModel;
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

public final class InvoiceActivity extends AceDnsParentActivity
{

    private TextView tvHeaderText,
            tv_start_date, tv_end_date;
    private String from_date  = "", to_date = "";

    private Activity mContext;
    private ArrayList<InvoiceModel> invoice_list = new ArrayList<>();
    private InvoiceAdapter_Date mAdapter;
    private RecyclerView rv_Pending;
    private int page_no_P = 1;
    protected Handler handler_p;
    // The minimum amount of items to have below your current scroll position
    // before loading more.
    private int visibleThreshold = 5;
    private int lastVisibleItem, totalItemCount;
    private boolean loading;
    public void setLoaded() {
        loading = false;
    }
    public void setLoading() {
        loading = true;
    }
    private MyCallback callback;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_invoice);
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
            tvHeaderText.setText("Pending Invoices");

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
            mAdapter = new InvoiceAdapter_Date(mContext, invoice_list);
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

    public void show_calendar(final View view)
    {
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

    public void reload(View view)
    {
        try
        {
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                final Map<String, String> jsonObject = new HashMap<>();

                jsonObject.put("customer_code", selected_customr_code);
                from_date = tv_start_date.getText().toString();
                to_date = tv_end_date.getText().toString();

                jsonObject.put("from_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", from_date));
                jsonObject.put("to_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", to_date));

                page_no_P = 1;
                invoice_list = new ArrayList<>();
                mAdapter = new InvoiceAdapter_Date(mContext, invoice_list);
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

    private void doPOSTcall_order_history(final Map<String, String> jsonObject, final String type)
    {
        try
        {
//            final String page = jsonObject.get("page_no") + "";
            print_Log_d("amitabha2715_200_jsonObject ", jsonObject+"");

            final String url = acedns_show_invoice_list;
            //making post call
            final VolleyApiCAll volleyApiCAll = new VolleyApiCAll(mContext);
            volleyApiCAll.makeServiceCallPost(jsonObject, url, new VolleyApiCAll.VolleyCallback()
            {
                @Override
                public void onSuccessResponse(final String result)
                {
                    try
                    {
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
                                print_Log_d("acedns_show_invoice_list ", result+"");

                                final JSONObject jo = new JSONObject(result);
                                if(jo.has("process_status"))
                                {
                                    if(jo.getString("process_status").toLowerCase().matches("yes"))
                                    {
                                        final String array_data = jo.getString("data");
                                        final JSONArray ja = new JSONArray(array_data);
                                        for(int i=0; i<ja.length(); i++)
                                        {
                                            final JSONObject e = ja.getJSONObject(i);

                                            final InvoiceModel iM = new InvoiceModel();
                                            iM.setInvoice_no(e.optString("invoice_no"));
                                            iM.setInvoice_date(e.optString("invoice_date"));
                                            iM.setDelivery_no(e.optString("delivery_no"));
                                            iM.setSale_order_no(e.optString("sale_order_no"));
                                            iM.setApp_order_no(e.optString("app_order_no"));
                                            iM.setProduct_name(e.optString("product_name"));
                                            iM.setInvoice_qty(e.optString("invoice_qty"));
                                            iM.setDestination(e.optString("destination"));
                                            iM.setTruck_no(e.optString("truck_no"));
                                            iM.set_invoice_link(e.optString("dwd_url"));

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

    public void onLoadMore_()
    {
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

                        jsonObject.put("from_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", from_date));
                        jsonObject.put("to_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", to_date));

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
}
