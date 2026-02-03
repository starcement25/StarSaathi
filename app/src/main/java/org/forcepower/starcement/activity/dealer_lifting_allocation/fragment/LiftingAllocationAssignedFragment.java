package org.forcepower.starcement.activity.dealer_lifting_allocation.fragment;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_server_current_date;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.dateToMilliSecond;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.dispatched_order_list_invoicewise;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.os.Bundle;

import androidx.fragment.app.Fragment;
import androidx.viewpager2.widget.ViewPager2;

import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ExpandableListView;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.material.tabs.TabLayout;
import com.google.android.material.tabs.TabLayoutMediator;

import org.forcepower.starcement.R;
import org.forcepower.starcement.activity.dealer_lifting_allocation.adapter.LiftingAssignedAdapter;
import org.forcepower.starcement.activity.dealer_lifting_allocation.dataset.LifitngAssignedDataSet;
import org.forcepower.starcement.activity.dealer_lifting_allocation.dataset.LiftingAssignedChildDataSet;
import org.forcepower.starcement.adapter.DestinationAdapter_M;
import org.forcepower.starcement.adapter.ViewPagerFragmentAdapter;
import org.forcepower.starcement.bean.DestinationMaster;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.MonthYearPickerDialog;
import org.forcepower.starcement.util.Utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class LiftingAllocationAssignedFragment extends Fragment
{
    private Activity mContext;
    private ArrayList<LifitngAssignedDataSet> images = new ArrayList<>();
    public LiftingAssignedAdapter oAdapter;
    private ExpandableListView expListView;
    private EditText et_SearchSD;

    private AceDnsDatabase mAceDnsDatabase;
    private TextView tvHeaderText;
    private String month_year = "";

    private ArrayList<DestinationMaster> mDealerSubDealerList = new ArrayList<>();
    private final ArrayList<Fragment> fragments = new ArrayList<>();
    private final ArrayList<String> arrData = new ArrayList<>();
    private final ArrayList<String> arrSap = new ArrayList<>();
    private final ArrayList<String> arrCus = new ArrayList<>();

    public LiftingAllocationAssignedFragment() {}

    public static LiftingAllocationAssignedFragment newInstance(final String month_year) {
        // Required empty public constructor
        final LiftingAllocationAssignedFragment fragmentFirst = new LiftingAllocationAssignedFragment();
        final Bundle args = new Bundle();
        args.putString("month_year", month_year);
        fragmentFirst.setArguments(args);

        return fragmentFirst;
    }

    @Override
    public View onCreateView(final LayoutInflater inflater, final ViewGroup container, final Bundle savedInstanceState) {
        View view= inflater.inflate(R.layout.fragment_lifting_allocation_assigned, container, false);
        et_SearchSD = (EditText) view.findViewById(R.id.et_SearchSD);
        et_SearchSD.setHint("Search...");
        final ImageView ivCrossSD = (ImageView) view.findViewById(R.id.ivCrossSD);
        ivCrossSD.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                et_SearchSD.setText("");
            }
        });

        et_SearchSD.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2,
                                      int arg3) {
                try
                {
                    final ArrayList<LifitngAssignedDataSet> temp = new ArrayList<>();

                    for(int i = 0; i<images.size(); i++)
                    {
                        if(images.get(i).getOrder_id().toLowerCase().contains(s.toString().toLowerCase()))
                        {
                            temp.add(images.get(i));
                        }
                    }

                    oAdapter.setFilter(temp);
                }
                catch (Exception e)
                {
                    e.printStackTrace();
                }
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        return  view;
    }

    @Override
    public void onViewCreated(final View view, final Bundle savedInstanceState) {
        try
        {
            mContext = getActivity();
            tvHeaderText = getActivity().findViewById(R.id.tvHeaderText);
            mAceDnsDatabase = new AceDnsDatabase(mContext);

            expListView = (ExpandableListView) view.findViewById(R.id.exlvTrackOrders);
            oAdapter = new LiftingAssignedAdapter(mContext, images, this);
            expListView.setAdapter(oAdapter);
            expListView.setEmptyView(view.findViewById(R.id.empty_text_view));
            expListView.setOnGroupClickListener(new ExpandableListView.OnGroupClickListener() {
                @Override
                public boolean onGroupClick(ExpandableListView parent, View v, int groupPosition,
                                            long id) {
                    final int childCountWay2 = parent.getExpandableListAdapter().getChildrenCount(groupPosition);
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

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        finally
        {

        }
    }
    
    public void assignFilter() {
        try
        {
            final MonthYearPickerDialog pd = new MonthYearPickerDialog();
            pd.setListener(new DatePickerDialog.OnDateSetListener() {
                @Override
                public void onDateSet(DatePicker view, int selectedYear, int selectedMonth, int selectedDay) {
                    if (HTTPUtils.isConnectionPossible(mContext))
                    {
                        final String header_txt = Utils.changeDateFormat( "yyyy-MM", "MMM, yyyy", selectedYear + "-" + selectedMonth);
                        tvHeaderText.setText("ASSIGNED ("+header_txt+")");
                        month_year = Utils.changeDateFormat( "yyyy-MM", "yyyy-MM", selectedYear + "-" + selectedMonth);

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

    public void reload() {
        if(HTTPUtils.isConnectionPossible(mContext))
        {
            new TRANS_SetLifingData_Asynctask(mContext).execute();
        }
        else
        {
            images.clear();
            oAdapter.setFilter(images);
            show_msg_alert_this(mContext, check_internet_connection+" Try again.");
        }
    }

    public void show_msg_alert_this(final Activity context, final String msg) {
        try
        {
            AlertDialog.Builder internetAlertDialog = new AlertDialog.Builder(context, R.style.MyDialog);

            TextView tvCPopup = new TextView(context);
            tvCPopup.setText(context.getResources().getString(R.string.app_name));
            tvCPopup.setGravity(Gravity.CENTER);
            tvCPopup.setTextColor(context.getResources().getColor(R.color.white));
            tvCPopup.setTextSize(14);
            tvCPopup.setBackgroundColor(context.getResources().getColor(R.color.red));
            int margin = 15;
            tvCPopup.setPadding(0, margin*2, 0, margin*2);
            internetAlertDialog.setCustomTitle(tvCPopup);
            internetAlertDialog.setMessage("\n" + msg);

            // On pressing Settings button
            internetAlertDialog.setPositiveButton("Ok", new DialogInterface.OnClickListener() {
                public void onClick(DialogInterface dialog, int which) {
                    dialog.dismiss();

                    reload();
                }
            });

            internetAlertDialog.setCancelable(false);
            // Showing Alert Message
            internetAlertDialog.show();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public final class TRANS_SetLifingData_Asynctask extends AsyncTaskCoroutine<String, String> {
        private Activity mContext;
        private JSONObject jo = new JSONObject();
        private ProgressDialog mStepProgressDialog;

        public TRANS_SetLifingData_Asynctask(final Activity mContext) {
            this.mContext = mContext;

            images.clear();
            oAdapter.setFilter(images);
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

            if (HTTPUtils.isConnectionPossible(mContext))
            {
                try
                {
                    final String url = dispatched_order_list_invoicewise;
                    print_log_d("dispatched_order_list_invoicewise ", url);

                    final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);
                    nameValuePairs.add(new BasicNameValuePair("month_year", month_year)); //vola2715 not confirmed yet

                    if(get_user_type(mContext).equalsIgnoreCase("broker"))
                    {
                        nameValuePairs.add(new BasicNameValuePair("customer_code", get_selected_dealer_sap_code(mContext)));
                    }
                    else
                    {
                        nameValuePairs.add(new BasicNameValuePair("customer_code", get_dealer_id(mContext)));
                    }

                    POST_result = HTTPUtils.getDataByHTTP_POST_Time(mContext, url, nameValuePairs);

                    print_log_d("_kri45o_U ", url);
                    print_log_d("_kri45o_P ", nameValuePairs.toString());
                    print_log_d("_kri45o_PLiif ", nameValuePairs.toString());
                    print_log_d("_kri45o_R ", POST_result);

                    jo = new JSONObject(POST_result);
                    long rssd_allocation_days = jo.optLong("rssd_allocation_days");

                    final String dispatched_order_data = jo.optString("dispatched_order_data");
                    final JSONArray jsonArray = new JSONArray(dispatched_order_data);
                    final long currDate = dateToMilliSecond(get_server_current_date(mContext));

                    final long days_count_four = rssd_allocation_days * 86400000;
                    final long calculate = currDate - days_count_four;

                    for(int i=0; i<jsonArray.length(); i++)
                    {
                        final JSONObject e = jsonArray.getJSONObject(i);
                        final LifitngAssignedDataSet cDH = new LifitngAssignedDataSet();

                        cDH.setOrder_id(e.optString("order_id"));
                        cDH.setOrder_date(e.optString("order_date"));
                        cDH.setCustomer_name(e.optString("customer_name"));
                        cDH.setDestination_name(e.optString("destination_name"));
                        cDH.setDns_prod_code(e.optString("dns_prod_code"));
                        cDH.setProd_display_name(e.optString("prod_display_name"));
                        cDH.setQty(e.optString("qty"));
                        cDH.set_allocation_qty("");
                        cDH.setFreight(e.optString("freight"));
                        cDH.setSTATUS(e.optString("STATUS"));
                        cDH.set_showAllocateButton(false);

                        cDH.setOrder_challan_data(new ArrayList<>());

                        final String dispatched_invoice_data = e.optString("dispatched_invoice_data");
                        if(!dispatched_invoice_data.isEmpty())
                        {
                            final JSONArray jaChallan = new JSONArray(dispatched_invoice_data);
                            final ArrayList<LiftingAssignedChildDataSet>child_list = new ArrayList<>();

                            double remaining_qty = 0;
                            for(int j=0; j<jaChallan.length(); j++)
                            {
                                final JSONObject ec = jaChallan.getJSONObject(j);
                                final LiftingAssignedChildDataSet tC = new LiftingAssignedChildDataSet();

                                tC.setCh_uid(ec.optString("ch_uid"));
                                tC.setApporderno(ec.optString("apporderno"));
                                tC.setErporderno(ec.optString("erporderno"));
                                tC.setChallanno(ec.optString("challanno"));
                                tC.setInvno(ec.optString("invno"));
                                tC.setInvdt(ec.optString("invdt"));
                                tC.setProd_display_name(ec.optString("prod_display_name"));
                                tC.setInvqty(ec.optString("invqty"));
                                tC.setCustomer_code(ec.optString("customer_code"));
                                tC.setTruckno(ec.optString("truckno"));
                                tC.setDestination(ec.optString("destination"));
                                tC.setAvailable_allocation_qty(ec.optString("available_allocation_qty"));

                                remaining_qty  = remaining_qty + ec.optDouble("available_allocation_qty");
                                final long thisDate = splConvertMilli(ec.optString("invdt")); //dispatch_date
                                if(thisDate >= calculate)
                                {
                                    cDH.set_showAllocateButton(true);
                                    tC.set_showDisAllocateButton(true);
                                }
                                else
                                {
                                    tC.set_showDisAllocateButton(false);
                                }

                                child_list.add(tC);
                            }

                            cDH.setRemainingQty(remaining_qty);
                            cDH.setOrder_challan_data(child_list);
                        }
                        //
                        images.add(cDH);
                    }
                }
                catch (Exception e)
                {
                    POST_result = "Network Failure";
                }
                finally
                {

                }
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
                    show_msg_alert_this(mContext, check_internet_connection);
                }
                else if(jo.optString("process_status").equalsIgnoreCase("NO"))
                {
                    show_msg_alert(mContext, jo.optString("process_message"), false);
                }
                else if (images.size() == 0)
                {
                    final String header_txt = Utils.changeDateFormat( "yyyy-MM", "MMM, yyyy", month_year);
                    show_msg_alert(mContext, "Data not available for this month("+header_txt+"). Please contact admin", false);
                }

            }
            catch (Exception e)
            {
                e.printStackTrace();
            }
            finally
            {
                mStepProgressDialog.dismiss();

                oAdapter.setFilter(images);
            }
        }
    }

    @Override
    public void onResume() {
        super.onResume();

        if (HTTPUtils.isConnectionPossible(mContext))
        {
            final String header_txt = Utils.changeDateFormat( "yyyy-MM-dd", "MMM, yyyy", get_server_current_date(mContext));
            tvHeaderText.setText("ASSIGNED ("+header_txt+")");
            month_year = Utils.changeDateFormat( "yyyy-MM-dd", "yyyy-MM", get_server_current_date(mContext));

            reload();
        }
        else
        {
            images.clear();
            oAdapter.setFilter(images);
            show_msg_alert(mContext,check_internet_connection, false);
        }


    }

    public void details_dialog(String builer, ViewPagerFragmentAdapter adapter, ArrayList<String> arrData,
                               final String order_id, final String dispatch_date) {
        try
        {
            final Dialog mDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar);
            mDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            final Window window = mDialog.getWindow();
            window.setGravity(Gravity.CENTER);
            window.setStatusBarColor(mContext.getResources().getColor(R.color.colorRed_StatusBar));

            mDialog.setContentView(R.layout.dialog_allocate_input);

            final TextView tvHeaderTxt = (TextView) mDialog.findViewById(R.id.tvHeaderText);
            tvHeaderTxt.setText("Allocation");

            final TextView autoCompleteTextView1 = (TextView) mDialog.findViewById(R.id.autoCompleteTextView1);
            final TabLayout tabLayout = (TabLayout) mDialog.findViewById(R.id.sliding_tabs);
            final ViewPager2 view_pager2 = (ViewPager2) mDialog.findViewById(R.id.view_pager2);


            final ImageView ivHeaderBack = (ImageView) mDialog.findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    mDialog.dismiss();
                }
            });

            autoCompleteTextView1.setText(builer);


            view_pager2.setAdapter(adapter);

            new TabLayoutMediator(tabLayout, view_pager2,
                    (tab, position) -> {
                        // Set tab titles here
                        tab.setText(arrData.get(position));
                    }).attach();

            view_pager2.registerOnPageChangeCallback(new ViewPager2.OnPageChangeCallback() {

                @Override
                public void onPageSelected(int position) {
                    super.onPageSelected(position);

                }

            });


            //
            mDialog.setCancelable(true);
            mDialog.setCanceledOnTouchOutside(true);

            mDialog.show();

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void showDealerSubDealerList(final String order_id, final String prod_name,
                                        final String invoice_date, final String invoice_no) {
        try
        {
            mDealerSubDealerList = mAceDnsDatabase.getMySubDealerList("rssd");
            if(!mDealerSubDealerList.isEmpty())
            {
                Log.d("TAG", "showDealerSubDealerList: "+mDealerSubDealerList);
                final Dialog mDestinationDialog = new Dialog(mContext,
                        android.R.style.Theme_DeviceDefault_Light_NoActionBar);
                mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                Window window = mDestinationDialog.getWindow();
                window.setGravity(Gravity.CENTER);
                window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
                mDestinationDialog.setContentView(R.layout.select_with_search);
                mDestinationDialog.setCancelable(true);
                window.setStatusBarColor(getResources().getColor(R.color.colorRed_StatusBar));
                final ImageView btnback = (ImageView) mDestinationDialog.findViewById(R.id.back);
                btnback.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        mDestinationDialog.dismiss();
                    }
                });
                final TextView tv_d_submit = (TextView) mDestinationDialog.findViewById(R.id.tv_d_submit);
                tv_d_submit.setVisibility(View.VISIBLE);
                final TextView title = (TextView) mDestinationDialog.findViewById(R.id.tvDestHeading);
                title.setText("Please choose sub dealers");
                final ListView dialogList = (ListView) mDestinationDialog.findViewById(R.id.list);

                final DestinationAdapter_M dAdapter = new DestinationAdapter_M(mContext,R.layout.customer_broker_list_child, mDealerSubDealerList);

                dialogList.setAdapter(dAdapter);

                final TextView searchText = (TextView) mDestinationDialog
                        .findViewById(R.id.autoCompleteTextView1);
                searchText.addTextChangedListener(new TextWatcher() {
                    @Override
                    public void onTextChanged(CharSequence s, int arg1, int arg2,
                                              int arg3) {
                        dAdapter.getFilter().filter(s.toString());

                        ArrayList<DestinationMaster> temp_List = new ArrayList<>();
                    }

                    @Override
                    public void beforeTextChanged(CharSequence arg0, int arg1,
                                                  int arg2, int arg3) {
                    }

                    @Override
                    public void afterTextChanged(Editable s) {
                    }
                });

                dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                    @Override
                    public void onItemClick(AdapterView<?> arg0, View arg1,
                                            int index, long arg3) {

                        try
                        {
                            final TextView list_details = (TextView) arg1.findViewById(R.id.list_details);
                            final int pos = Integer.parseInt(list_details.getTag().toString().trim());
                            if(mDealerSubDealerList.get(pos).get_selected())
                            {
                                mDealerSubDealerList.get(pos).set_selected(false);
                            }
                            else
                            {
                                mDealerSubDealerList.get(pos).set_selected(true);
                            }
                        }
                        catch (Exception er)
                        {
                            er.printStackTrace();
                        }
                        dAdapter.setFilter(mDealerSubDealerList);
                    }
                });

                tv_d_submit.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        try
                        {
                            boolean subDealerSelected = false;

                            arrData.clear();
                            arrSap.clear();
                            arrCus.clear();
                            for(int i=0; i<mDealerSubDealerList.size(); i++)
                            {
                                if(mDealerSubDealerList.get(i).get_selected())
                                {
                                    subDealerSelected = true;
                                    arrData.add(mDealerSubDealerList.get(i).getDestinationName());
                                    arrSap.add(mDealerSubDealerList.get(i).get_SAP_code());
                                    arrCus.add(mDealerSubDealerList.get(i).getSubDealerCode());
                                }
                            }
                            if(subDealerSelected)
                            {
                                final StringBuilder builder = new StringBuilder();
                                fragments.clear();
                                for(int i=0; i<arrData.size(); i++)
                                {
                                    fragments.add(DealerLiftingAssignedSaveFragment.newInstance(arrSap.get(i), arrCus.get(i), order_id, prod_name, invoice_date, invoice_no, LiftingAllocationAssignedFragment.this));

                                    builder.append((i + 1)).append("- ").append(arrData.get(i)).append("\n");
                                }

                                final ViewPagerFragmentAdapter adapter = new ViewPagerFragmentAdapter(getActivity(), fragments);
                                details_dialog(builder.toString(), adapter, arrData, order_id, prod_name);
                            }
                        }
                        catch (Exception e)
                        {
                            e.printStackTrace();
                        }
                        finally
                        {
                            mDestinationDialog.dismiss();
                        }
                    }
                });
                mDestinationDialog.show();
            }
            else
            {
                Toast.makeText(mContext, "Please Refresh sub dealer data.", Toast.LENGTH_SHORT).show();
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public long splConvertMilli(String date) {
        long d = 0;
        try
        {
            SimpleDateFormat spf=new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
            Date newDate=spf.parse(date);
            d = newDate.getTime();
            print_log_d("_kri45o_dval", d + "");
        }
        catch (Exception e)
        {
            print_log_d("_kri45o_dcatch", e.toString());
            e.printStackTrace();
        }
        return d;
    }
}