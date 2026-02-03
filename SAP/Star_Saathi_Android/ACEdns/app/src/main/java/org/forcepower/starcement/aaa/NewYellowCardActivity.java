package org.forcepower.starcement.aaa;

import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.DialogFragment;
import android.app.ProgressDialog;
import android.content.Context;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;
import org.forcepower.starcement.R;
import org.forcepower.starcement.aaa.AceDnsParentActivity;
import org.forcepower.starcement.adapter.DestinationAdapter;
import org.forcepower.starcement.adapter.ProductListAdapter_New;
import org.forcepower.starcement.bean.DestinationMaster;
import org.forcepower.starcement.bean.ProductMasterDetails;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.util.HTTPUtils;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Calendar;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.acedns_star_submit_yellow_card_details;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.employeeDetailObject;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;

public final class NewYellowCardActivity extends AceDnsParentActivity implements SwipeRefreshLayout.OnRefreshListener
{
    Context mContext;
    ArrayList<NameValuePair> mHttpParamPairs;
    public static TextView tvDOB;
    TextView tv_sub_delear, tv_select_products, selected_sub_dealer_code, selected_prod_code;
    EditText et_challan_number, et_quantity;
    SwipeRefreshLayout chartListSwipeRefreshLayout;
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_yellow_card);

        mContext=this;
        if(get_user_type(mContext).equalsIgnoreCase("broker"))
        {
            selected_customr_code = get_selected_customer_code(mContext);
        }
        else
        {
            selected_customr_code = get_emp_or_customer_code(mContext);
        }
        TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
        tvHeaderText.setText("YELLOW CARD");

        ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
        ivHeaderBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
        LinearLayout llHeaderDetails = (LinearLayout) findViewById(R.id.llHeaderDetails);
        llHeaderDetails.setVisibility(View.INVISIBLE);

        tv_sub_delear = (TextView) findViewById(R.id.tv_sub_delear);
        selected_sub_dealer_code = (TextView) findViewById(R.id.tv_selected_sub_dealer_code);
        tv_select_products = (TextView) findViewById(R.id.tv_select_products);
        selected_prod_code = (TextView) findViewById(R.id.tv_selected_prod_code);
        et_challan_number = (EditText) findViewById(R.id.et_challan_number);
        et_quantity = (EditText) findViewById(R.id.et_quantity);
        tvDOB = (TextView) findViewById(R.id.tvDOB);

        chartListSwipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.chartListSwipeRefreshLayout);
        chartListSwipeRefreshLayout.setOnRefreshListener(this);
        chartListSwipeRefreshLayout.setColorSchemeResources(R.color.red, R.color.white, R.color.red, R.color.white);

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

                    }
                    else
                    {
                        Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                    }
                }
            }, 10);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    ArrayList<DestinationMaster> mDestinationMasterList = new ArrayList<>();
    ArrayList<ProductMasterDetails> productMasterList = new ArrayList<>();

    AceDnsDatabase mAceDnsDatabase;
    public void select_sub_dealer(View view)
    {
        try
        {
            mAceDnsDatabase = new AceDnsDatabase(mContext);
            mDestinationMasterList=mAceDnsDatabase.getMySubDealerList("");
            if(mDestinationMasterList.size()>0)
            {
                showMySubDealerList();
            }
            else
            {
                Toast.makeText(mContext, "No sub dealer found", Toast.LENGTH_SHORT).show();
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        finally
        {
            if(mAceDnsDatabase != null)
                mAceDnsDatabase.close();
        }
    }
    public void showMySubDealerList()
    {
        try
        {
            final Dialog mDestinationDialog = new Dialog(mContext,
                    android.R.style.Theme_DeviceDefault_Light_NoActionBar);
            mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            Window window = mDestinationDialog.getWindow();
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            mDestinationDialog.setContentView(R.layout.select_with_search);
            mDestinationDialog.setCancelable(true);
            window.setStatusBarColor(getResources().getColor(R.color.colorRed_StatusBar));
            ImageView btnback = (ImageView) mDestinationDialog.findViewById(R.id.back);
            btnback.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    mDestinationDialog.dismiss();
                }
            });
            TextView title = (TextView) mDestinationDialog.findViewById(R.id.tvDestHeading);
            title.setText("Please select a sub-dealers");
            ListView dialogList = (ListView) mDestinationDialog.findViewById(R.id.list);

            //final ArrayAdapter<String> RoutePlanAdapter = new ArrayAdapter<String>(this,R.layout.activity_listview, mDestinationMasterList);

            final DestinationAdapter destinationAdapter = new DestinationAdapter(this,R.layout.customer_broker_list_child,mDestinationMasterList);

            dialogList.setAdapter(destinationAdapter);

            EditText searchText = (EditText) mDestinationDialog
                    .findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2,
                                          int arg3) {
                    destinationAdapter.getFilter().filter(s.toString());
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
                                        int position, long arg3) {
                    getWindow()
                            .setSoftInputMode(
                                    WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    Constants.selectedDestination = destinationAdapter.getItem(position);
//                    Constants.mDestinationCode=Constants.selectedDestination.getDestinationCode();
                    tv_sub_delear.setText(Constants.selectedDestination.getDestinationName());
                    selected_sub_dealer_code.setText(Constants.selectedDestination.getSubDealerCode());

//                    etCustName.setText(Constants.selectedDestination.getDestinationName());
//                    etDestination.setText(Constants.selectedDestination.getDestinationCode());

                    mDestinationDialog.dismiss();
                }
            });

            mDestinationDialog.show();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void select_products(View view)
    {
        try
        {
            mAceDnsDatabase = new AceDnsDatabase(mContext);
            productMasterList = mAceDnsDatabase.getProductMasterList("1", 1,false);
            if(productMasterList.size()>0)
            {
                ShowProductListDialog();
            }
            else
            {
                Toast.makeText(mContext, "No product list found", Toast.LENGTH_SHORT).show();
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        finally
        {
            if(mAceDnsDatabase != null)
                mAceDnsDatabase.close();
        }
    }
    public void ShowProductListDialog()
    {
        try
        {
            final Dialog mDestinationDialog = new Dialog(mContext,
                    android.R.style.Theme_DeviceDefault_Light_NoActionBar);
            mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            Window window = mDestinationDialog.getWindow();
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            mDestinationDialog.setContentView(R.layout.select_with_search);
            mDestinationDialog.setCancelable(true);
            window.setStatusBarColor(getResources().getColor(R.color.colorRed_StatusBar));
            ImageView btnback = (ImageView) mDestinationDialog.findViewById(R.id.back);
            btnback.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    mDestinationDialog.dismiss();
                }
            });
            TextView title = (TextView) mDestinationDialog.findViewById(R.id.tvDestHeading);
            title.setText("Please select a product");
            ListView dialogList = (ListView) mDestinationDialog.findViewById(R.id.list);


            final ProductListAdapter_New destinationAdapter = new ProductListAdapter_New(this,R.layout.customer_broker_list_child, productMasterList);

            dialogList.setAdapter(destinationAdapter);

            EditText searchText = (EditText) mDestinationDialog
                    .findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2,
                                          int arg3) {
                    destinationAdapter.getFilter().filter(s.toString());
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
                                        int position, long arg3) {
                    getWindow()
                            .setSoftInputMode(
                                    WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
//                    Constants.productDetailsObj = destinationAdapter.getItem(position);
//                    Constants.mDestinationCode=Constants.selectedDestination.getDestinationCode();
                    tv_select_products.setText(destinationAdapter.getItem(position).getDesc()+"");
                    selected_prod_code.setText(destinationAdapter.getItem(position).getProdCode()+"");

//                    etCustName.setText(Constants.selectedDestination.getDestinationName());
//                    etDestination.setText(Constants.selectedDestination.getDestinationCode());

                    mDestinationDialog.dismiss();
                }
            });

            mDestinationDialog.show();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void select_date(View view)
    {
        try
        {
            DialogFragment newFragment = new DatePickerFragment();
            newFragment.show(getFragmentManager(), "DOB");
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public static class DatePickerFragment extends DialogFragment implements DatePickerDialog.OnDateSetListener
    {
        String myTag = "";
        @Override
        public Dialog onCreateDialog(Bundle savedInstanceState)
        {
            myTag = getTag();
            int year = 0, month = 0, day = 0;
            final Calendar c = Calendar.getInstance();
            year = c.get(Calendar.YEAR);
            month = c.get(Calendar.MONTH);
            day = c.get(Calendar.DAY_OF_MONTH);

            if(myTag.matches("DOB"))
            {
                if(!tvDOB.getText().toString().trim().matches(""))
                {
                    String[] ddMMyyyy = tvDOB.getText().toString().split("-");
                    day = Integer.parseInt(ddMMyyyy[0]);
                    month = Integer.parseInt(ddMMyyyy[1]);
                    year = Integer.parseInt(ddMMyyyy[2]);
                }
            }


            DatePickerDialog dialog = new DatePickerDialog(getActivity(), this, year, month, day);
//            dialog.getDatePicker().setMinDate(c.getTimeInMillis());
            return  dialog;
        }

        public void onDateSet(DatePicker view, int year, int month, int day)
        {
            String sDay = ""+day, sMondth = ""+(month + 1);
            if(sDay.length() == 1)
            {
                sDay = "0"+sDay;
            }
            if(sMondth.length() == 1)
            {
                sMondth = "0"+sMondth;
            }

            String date = year +"-"+ sMondth + "-" + sDay;

            if(myTag.matches("DOB"))
            {
                tvDOB.setText(date);
            }

        }
    }
    public void submit_yellow_card(View view)
    {
        try
        {
            if(tv_sub_delear.getText().toString().trim().matches(""))
            {
                Toast.makeText(mContext, "Please select a sub dealer", Toast.LENGTH_SHORT).show();
            }
            else if(tvDOB.getText().toString().trim().matches(""))
            {
                Toast.makeText(mContext, "Please select a date", Toast.LENGTH_SHORT).show();
            }
            else if(et_challan_number.getText().toString().trim().matches(""))
            {
                Toast.makeText(mContext, "Please enter challan number", Toast.LENGTH_SHORT).show();
            }
            else if(et_quantity.getText().toString().trim().matches(""))
            {
                Toast.makeText(mContext, "Please enter quantity", Toast.LENGTH_SHORT).show();
            }
            else if(tv_select_products.getText().toString().trim().matches(""))
            {
                Toast.makeText(mContext, "Please select a product", Toast.LENGTH_SHORT).show();
            }
            else
            {
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    new submit_yellow_card_Asynctask(mContext).execute();
                }
                else
                {
                    Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                }
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    
    public final class submit_yellow_card_Asynctask extends AsyncTaskCoroutine<String, String>
    {
        Context mContext;
        ProgressDialog mStepProgressDialog;
        JSONObject jo = new JSONObject();
        public submit_yellow_card_Asynctask(Context mContext) {
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

            mHttpParamPairs = new ArrayList<>();
            mHttpParamPairs.add(new BasicNameValuePair("logged_in_customer_code", employeeDetailObject.getEmpCode()));
            mHttpParamPairs.add(new BasicNameValuePair("selected_sub_dealer_code", selected_sub_dealer_code.getText().toString()));
            mHttpParamPairs.add(new BasicNameValuePair("selected_date", tvDOB.getText().toString()));
            mHttpParamPairs.add(new BasicNameValuePair("challan_no", et_challan_number.getText().toString()));
            mHttpParamPairs.add(new BasicNameValuePair("qty_in_bags", et_quantity.getText().toString()));
            mHttpParamPairs.add(new BasicNameValuePair("selected_prod_code", selected_prod_code.getText().toString()));

            /*
            Parameters : logged_in_customer_code,selected_sub_dealer_code,selected_date,challan_no,qty_in_bags,selected_prod_code

Note: logged_in_customer_code and selected_sub_dealer_code are like C/0015322.

selected_date  = YYYY-MM-DD
             */
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
                        String url = acedns_star_submit_yellow_card_details;
                        print_log_d("acedns_star_submit_yellow_card_details", url);

                        POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);

                        jo = new JSONObject(POST_result);
                        print_log_d("acedns_star_submit_yellow_card_details", jo.toString());
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
                    Toast.makeText(mContext, jo.optString("process_message"), Toast.LENGTH_SHORT).show();
                    if(jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes"))
                    {
                        finish();
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
