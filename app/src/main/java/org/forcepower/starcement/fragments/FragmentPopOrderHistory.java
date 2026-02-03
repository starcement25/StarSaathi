package org.forcepower.starcement.fragments;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.changeDateFormat_;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.constants.Constants.show_pop_order_list;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;
import static org.forcepower.starcement.util.Utils.show_msg_alert;
import static org.forcepower.starcement.util.Utils.twoDigitRoundOff;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.os.Bundle;
import android.os.Handler;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.DatePicker;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.PopOrderAdapter_Date;
import org.forcepower.starcement.bean.PopOrderModel;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.MonthYearPickerDialog;
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


public final class FragmentPopOrderHistory extends Fragment {
    private Activity mContext;
    private ArrayList<PopOrderModel> images = new ArrayList<>();
    private PopOrderAdapter_Date mAdapter;
    private RecyclerView rv_Pending;
    private int page_no_P = 1;
    protected Handler handler_p;
    // The minimum amount of items to have below your current scroll position
    // before loading more.
    private final int visibleThreshold = 5;
    private int lastVisibleItem, totalItemCount;
    private boolean loading;
    public void setLoaded() {
        loading = false;
    }
    public void setLoading() {
        loading = true;
    }
    private MyCallback callback;

    private TextView tv_start_date, tv_end_date, tvHeaderText;
    private String from_date  = "", to_date = "", year_month = "";
    private LinearLayout ll_start_date, ll_end_date;

    public FragmentPopOrderHistory()
    {
 
    }

    public static FragmentPopOrderHistory newInstance(final String year_month)
    {
        // Required empty public constructor
        final FragmentPopOrderHistory fragmentFirst = new FragmentPopOrderHistory();
        final Bundle args = new Bundle();
        args.putString("year_month", year_month);
        fragmentFirst.setArguments(args);
        return fragmentFirst;
    }
//    public String get_thisCategory() {return getArguments().getString("thisCategory");}

    @Override
    public View onCreateView(final LayoutInflater inflater, final ViewGroup container, final Bundle savedInstanceState)
    {
        return inflater.inflate(R.layout.fragment_pop_history, container, false);
    }

    @Override
    public void onViewCreated(final View view, final Bundle savedInstanceState)
    {
        try
        {
            mContext = getActivity();
            if(get_user_type(mContext).equalsIgnoreCase("broker"))
            {
                selected_customr_code = get_selected_customer_code(mContext);
            }
            else
            {
                selected_customr_code = get_emp_or_customer_code(mContext);
            }

            handler_p = new Handler();
            ll_start_date = (LinearLayout) view.findViewById(R.id.ll_start_date);
            ll_end_date = (LinearLayout) view.findViewById(R.id.ll_end_date);
            rv_Pending = (RecyclerView) view.findViewById(R.id.recycler_view);
            rv_Pending.setHasFixedSize(true);
            rv_Pending.setLayoutManager(new LinearLayoutManager(mContext));

            images = new ArrayList<>();
            mAdapter = new PopOrderAdapter_Date(mContext, images);
            rv_Pending.setAdapter(mAdapter);


            callback = new MyCallback() {

                public void callbackCall() {
                    // callback code goes here
                    onLoadMore_();
                }
            };
            if (rv_Pending.getLayoutManager() instanceof final LinearLayoutManager linearLayoutManager)
            {
                rv_Pending
                        .addOnScrollListener(new RecyclerView.OnScrollListener() {
                            @Override
                            public void onScrolled(@NonNull RecyclerView recyclerView,
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

            tv_start_date = (TextView) view.findViewById(R.id.tv_start_date);
            tv_end_date = (TextView) view.findViewById(R.id.tv_end_date);

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

            ll_start_date.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    show_calendar(view);
                }
            });
            ll_end_date.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    show_calendar(view);
                }
            });

            //
            tvHeaderText = (TextView) getActivity().findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("POP PRODUCT");
            getActivity().findViewById(R.id.ivMonthSelection).setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    try
                    {
                        final MonthYearPickerDialog pd = new MonthYearPickerDialog();
                        pd.setListener(new DatePickerDialog.OnDateSetListener() {
                            @Override
                            public void onDateSet(DatePicker view, int selectedYear, int selectedMonth, int selectedDay) {
                                if (HTTPUtils.isConnectionPossible(mContext))
                                {
                                    final String header_txt = Utils.changeDateFormat( "yyyy-MM", "MMM, yyyy", selectedYear + "-" + selectedMonth);
                                    tvHeaderText.setText("POP PRODUCT ("+header_txt+")");
//                                    final String yyyyMM = Utils.changeDateFormat( "yyyy-MM", "yyyy-MM", selectedYear + "-" + selectedMonth);
                                    year_month = Utils.changeDateFormat( "yyyy-MM", "yyyy-MM", selectedYear + "-" + selectedMonth);
                                    reload();
                                }
                                else
                                {
                                    show_msg_alert(mContext,check_internet_connection, false);
                                }
                            }
                        });
                        //
                        pd.show(getChildFragmentManager(), "MonthYearPickerDialog");
                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                }
            });

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void reload()
    {
        try
        {
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                final Map<String, String> jsonObject = new HashMap<>();

                jsonObject.put("customer_code", selected_customr_code);
                jsonObject.put("year_month", year_month);
                
//                jsonObject.put("start_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", from_date));
//                jsonObject.put("end_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", to_date));


                page_no_P = 1;
                images = new ArrayList<>();
                mAdapter = new PopOrderAdapter_Date(mContext, images);
                rv_Pending.setAdapter(mAdapter);
                jsonObject.put("page_no", page_no_P + "");

                _doPOSTcall_(jsonObject, "initial");
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

    private void _doPOSTcall_(final Map<String, String> jsonObject, final String type)
    {
        try
        {
            final String page = jsonObject.get("page_no") + "";
            print_Log_d("show_pop_order_list_page ", page+"");
            print_Log_d("show_pop_order_list_jsonObject ", jsonObject+"");

            final String url = show_pop_order_list;
            //making post call
            final VolleyApiCAll volleyApiCAll = new VolleyApiCAll(mContext);
            volleyApiCAll.makeServiceCallPost(jsonObject, url, new VolleyApiCAll.VolleyCallback()
            {
                @Override
                public void onSuccessResponse(String result)
                {
                    try
                    {
                        JSONObject jo = new JSONObject(result);
                        print_Log_d("show_pop_order_list_URL ", url+"");
                        print_Log_d("show_pop_order_list_PRARAM ", jsonObject+"");
                        print_Log_d("show_pop_order_list_RES ", result+"");
                        images = new ArrayList<>();
                        if(type.matches("add_p"))
                        {
                            //   remove progress item
                            images.remove(images.size() - 1);
                            mAdapter.notifyItemRemoved(images.size());
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
                                        final String track_pop_order_data  = jo.getString("track_pop_order_data");
                                        final JSONArray jsonArray = new JSONArray(track_pop_order_data );

                                        for (int i = 0; i < jsonArray.length(); i++)
                                        {
                                            final JSONObject e = jsonArray.getJSONObject(i);
                                            final PopOrderModel lM = new PopOrderModel();

                                            lM.setOrder_id(e.optString("order_id"));
                                            lM.setOrder_date(e.optString("order_date"));
                                            lM.setCustomer_name(e.optString("customer_name"));
                                            lM.setAddress(e.optString("address"));
                                            lM.setPin(e.optString("pin"));
                                            lM.setDns_prod_code(e.optString("dns_prod_code"));
                                            lM.setProd_display_name(e.optString("prod_display_name"));
                                            lM.set_prod_image(e.optString("image"));
                                            lM.set_main_order_id(e.optString("main_order_id"));
                                            lM.setQty(e.optString("qty") + " (Pcs)");
                                            lM.set_total_amount(getResources().getString(R.string.Rs) +" "+ twoDigitRoundOff(e.optString("total_amount")));
                                            lM.set_order_status(e.optString("order_status"));
                                            images.add(lM);
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
                                    mAdapter.setFilter(images);
                                }
                                else if(images.size() > 0)
                                {
                                    mAdapter.notifyItemInserted(images.size());
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

                        if(images.isEmpty() && page_no_P == 2)
                            show_msg_Dialog(mContext, jo.optString("process_message"));

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
                images.add(null);
                mAdapter.notifyItemInserted(images.size() - 1);

                handler_p.postDelayed(new Runnable() {
                    @Override
                    public void run() {
                        final Map<String, String> jsonObject = new HashMap<>();

                        jsonObject.put("customer_code", selected_customr_code);
                        jsonObject.put("year_month", year_month);

//                        jsonObject.put("start_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", from_date));
//                        jsonObject.put("end_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", to_date));

                        jsonObject.put("page_no", page_no_P + "");

                        _doPOSTcall_(jsonObject, "add_p");
                        //or you can add all at once but do not forget to call pAdapter.notifyDataSetChanged();
                    }
                }, 2000);
            }
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
                               reload();
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
    public void onResume() {
        super.onResume();

        tvHeaderText.setText("POP PRODUCT");
        year_month = "";
        reload();
    }

}
