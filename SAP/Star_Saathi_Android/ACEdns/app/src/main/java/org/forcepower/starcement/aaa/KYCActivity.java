package org.forcepower.starcement.aaa;

import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.DialogFragment;
import android.app.ProgressDialog;
import android.content.Context;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import android.os.Bundle;
import android.os.Handler;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import android.view.View;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;
import org.forcepower.starcement.R;
import org.forcepower.starcement.aaa.AceDnsParentActivity;
import org.forcepower.starcement.util.HTTPUtils;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Calendar;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.validEmailFormat;
import static org.forcepower.starcement.constants.Constants.acedns_save_employee_kyc;
import static org.forcepower.starcement.constants.Constants.acedns_show_employee_kyc;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.employeeDetailObject;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.isValidIndianMobile;
import static org.forcepower.starcement.util.Utils.print_log_d;

public final class KYCActivity extends AceDnsParentActivity implements SwipeRefreshLayout.OnRefreshListener
{
    Context mContext;
    ArrayList<NameValuePair> mHttpParamPairs = new ArrayList<>();;
    public static TextView tvDOB, tvDOM;
    EditText etWhatsAppNumber, etEmail;
    SwipeRefreshLayout chartListSwipeRefreshLayout;
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_kyc);

        mContext=this;

        TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
        tvHeaderText.setText("KYC");

        ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
        ivHeaderBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
        LinearLayout llHeaderDetails = (LinearLayout) findViewById(R.id.llHeaderDetails);
        llHeaderDetails.setVisibility(View.INVISIBLE);

        etWhatsAppNumber = (EditText) findViewById(R.id.etWhatsAppNumber);
        etEmail = (EditText) findViewById(R.id.etEmail);
        tvDOB = (TextView) findViewById(R.id.tvDOB);
        tvDOM = (TextView) findViewById(R.id.tvDOM);
        if (HTTPUtils.isConnectionPossible(mContext))
        {
            new getKYC_Asynctask(mContext).execute();
        }
        else
        {
            Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
        }
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
                        new getKYC_Asynctask(mContext).execute();
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
    public void DOB(View view)
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
    public void DOM(View view)
    {
        try
        {
            DialogFragment newFragment = new DatePickerFragment();
            newFragment.show(getFragmentManager(), "DOM");
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
            else if(myTag.matches("DOM"))
            {
                if(!tvDOM.getText().toString().trim().matches(""))
                {
                    String[] ddMMyyyy = tvDOM.getText().toString().split("-");
                    day = Integer.parseInt(ddMMyyyy[0]);
                    month = Integer.parseInt(ddMMyyyy[1]);
                    year = Integer.parseInt(ddMMyyyy[2]);
                }
            }

            c.add(Calendar.YEAR, -18);
            DatePickerDialog dialog = new DatePickerDialog(getActivity(), this, year, month, day);
            dialog.getDatePicker().setMaxDate(c.getTimeInMillis());
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

            String date = sDay +"-"+ sMondth + "-" + year;

            if(myTag.matches("DOB"))
            {
                tvDOB.setText(date);
            }
            else if(myTag.matches("DOM"))
            {
                tvDOM.setText(date);
            }
        }
    }
    public void updateKYC(View view)
    {
        try
        {
            if(!isValidIndianMobile(etWhatsAppNumber.getText().toString().trim()))
            {
                Toast.makeText(mContext, "Please enter a valid WhatsApp number", Toast.LENGTH_SHORT).show();
            }
            else if(tvDOB.getText().toString().trim().matches(""))
            {
                Toast.makeText(mContext, "Please enter Date of Birth", Toast.LENGTH_SHORT).show();
            }
            else if(tvDOM.getText().toString().trim().matches(""))
            {
                Toast.makeText(mContext, "Please enter Date of Marriage", Toast.LENGTH_SHORT).show();
            }
            else if(!validEmailFormat(etEmail.getText().toString()))
            {
                Toast.makeText(mContext, "Please enter proper email id", Toast.LENGTH_SHORT).show();
            }
            else
            {
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    new UpdateKYC_Asynctask(mContext).execute();
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
    
    public final class UpdateKYC_Asynctask extends AsyncTaskCoroutine<String, String>
    {
        Context mContext;
        ProgressDialog mStepProgressDialog;
        JSONObject jo = new JSONObject();
        public UpdateKYC_Asynctask(Context mContext) {
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
                        final String url = acedns_save_employee_kyc;
                        print_log_d("PRINT_KYC_URL_180_a ", url);
                        mHttpParamPairs = new ArrayList<>(2);
                        mHttpParamPairs.add(new BasicNameValuePair("emp_code", get_emp_or_customer_code(mContext)));
                        mHttpParamPairs.add(new BasicNameValuePair("whatsapp_no", etWhatsAppNumber.getText().toString()));
                        mHttpParamPairs.add(new BasicNameValuePair("dob", tvDOB.getText().toString()));
                        mHttpParamPairs.add(new BasicNameValuePair("dom", tvDOM.getText().toString()));
                        mHttpParamPairs.add(new BasicNameValuePair("email_id", etEmail.getText().toString()));
                        print_log_d("PRINT_KYC_URL_180_b ", mHttpParamPairs.toString());
                        POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);
                        print_log_d("PRINT_KYC_URL_180_c ", POST_result);
                        jo = new JSONObject(POST_result);
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
    
    public final class getKYC_Asynctask extends AsyncTaskCoroutine<String, String>
    {
        Context mContext;
        ProgressDialog mStepProgressDialog;
        JSONObject jo = new JSONObject();
        public getKYC_Asynctask(Context mContext) {
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
                        final String url = acedns_show_employee_kyc;
                        print_log_d("PRINT_KYC_URL_280_a ", url);
                        mHttpParamPairs = new ArrayList<>(2);
                        mHttpParamPairs.add(new BasicNameValuePair("emp_code", get_emp_or_customer_code(mContext)));
                        print_log_d("PRINT_KYC_URL_280_b ", mHttpParamPairs.toString());

                        POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);
                        print_log_d("PRINT_KYC_URL_280_c ", POST_result);

                        jo = new JSONObject(POST_result);
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
                    if(jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes"))
                    {
                        jo = new JSONObject(jo.getString("employee_kyc_data"));
                        etWhatsAppNumber.setText(jo.optString("whatsapp_no"));
                        tvDOB.setText(jo.optString("dob"));
                        tvDOM.setText(jo.optString("dom"));
                        etEmail.setText(jo.optString("email_id"));
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
