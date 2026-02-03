package org.forcepower.starcement.aaa;

import android.app.Activity;

import org.forcepower.starcement.BuildConfig;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import org.forcepower.starcement.HttpCalling_;
import org.forcepower.starcement.R;
import org.forcepower.starcement.Utils_;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.util.ConnectionDetector;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.MCrypt;
import org.forcepower.starcement.util.Utils;
import org.json.JSONObject;

import static org.forcepower.starcement.SharedPrefData.get_dealer_submit_form;
import static org.forcepower.starcement.SharedPrefData.get_profile_image_url;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.SharedPrefData.set_belong_dealer_code;
import static org.forcepower.starcement.SharedPrefData.set_belong_dealer_dns_code;
import static org.forcepower.starcement.SharedPrefData.set_belong_dealer_name;
import static org.forcepower.starcement.SharedPrefData.set_dealer_id;
import static org.forcepower.starcement.SharedPrefData.set_dealer_submit_form;
import static org.forcepower.starcement.SharedPrefData.set_dns_emp_code;
import static org.forcepower.starcement.SharedPrefData.set_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.set_logged_sub_dealer_name;
import static org.forcepower.starcement.SharedPrefData.set_login_mobile_number;
import static org.forcepower.starcement.SharedPrefData.set_profile_image_url;
import static org.forcepower.starcement.SharedPrefData.set_user_type;
import static org.forcepower.starcement.aaa.LoginActivity.dealer_id_input;
import static org.forcepower.starcement.aaa.LoginActivity.login_mobile_number;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.redirection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.constants.Constants.verifyotp;
import static org.forcepower.starcement.util.PreferenceData.setLoginStatus;
import static org.forcepower.starcement.util.Utils.print_log_d;

import java.util.ArrayList;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class OtpActivity extends AceDnsParentActivity
{
    private Activity mContext;
    private EditText et_OTP;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_otp);


        mContext= this;
        et_OTP = (EditText) findViewById(R.id.et_OTP);
        TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
        tvHeaderText.setText("VERIFY MOBILE");

        ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
        ivHeaderBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
        if(BuildConfig.DEBUG)
        {
            et_OTP.setText("1010");
        }
    }

    public void continueLogin(View view)
    {
        try
        {
            ConnectionDetector cd= new ConnectionDetector(mContext);
            Boolean isInternetPresent=cd.isConnectingToInternet();
            if(isInternetPresent)
            {
                //authenticate employee
//                new AUTH_LoadEmployeeMasterData_M(this).execute(login_mobile_number);
                if(et_OTP.getText().toString().trim().length() == 4)
                {
                    new TRANS_PostOTP_Asynctask(mContext).execute();
                }
                else
                {
                    Utils_.closeApp(mContext,"Please enter 4 digit OTP");
                }
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
    
    public final class TRANS_PostOTP_Asynctask extends AsyncTaskCoroutine<String, String>
    {
        private Activity mContext;

        public TRANS_PostOTP_Asynctask(Activity mContext) {
            this.mContext = mContext;
        }
        private JSONObject jo2 = new JSONObject();
        private final String my_otp = et_OTP.getText().toString();
        private boolean loginStatus = false;
        @Override
        public void onPreExecute()
        {
            super.onPreExecute();
            Utils.showProgressDialog(mContext, "Verify OTP..");
        }
        @Override
        public String doInBackground(final String... params)
        {
            String POST_result = "";
            try
            {
                final String deviceId = Constants.deviceId.length() > 0 ? Constants.deviceId : Utils.getDeviceId(mContext);

                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    try
                    {

                        String url = verifyotp;
                        if(BuildConfig.API_URL.startsWith("https://qas.starsaathi.com/"))
                        {
                            url =  BuildConfig.API_URL + "verifyotpnew_v2.php";

                            final ArrayList<NameValuePair> mHttpParamPairs = new ArrayList<>(2);
                            mHttpParamPairs.add(new BasicNameValuePair("nickname", Constants.nickName));
                            mHttpParamPairs.add(new BasicNameValuePair("phonenumber", login_mobile_number));
                            mHttpParamPairs.add(new BasicNameValuePair("deviceid",  deviceId));
                            mHttpParamPairs.add(new BasicNameValuePair("dealer_id", dealer_id_input));
                            mHttpParamPairs.add(new BasicNameValuePair("the_otp", my_otp));

                            POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);

                            print_log_d("_orur4_O_U_i ", url + "");
                            print_log_d("_orur4_O_P_i ", mHttpParamPairs + "");
                            print_log_d("_orur4_O_R_i ",POST_result + "");
                        }
                        else
                        {
                            final MCrypt mcrypt = new MCrypt();
                            final JSONObject jo=new JSONObject();
                            jo.put("nickname",MCrypt.bytesToHex( mcrypt.encrypt(Constants.nickName) ));
                            jo.put("phonenumber",MCrypt.bytesToHex( mcrypt.encrypt(login_mobile_number) ));
                            jo.put("deviceid", MCrypt.bytesToHex( mcrypt.encrypt(deviceId)));
                            jo.put("dealer_id", MCrypt.bytesToHex( mcrypt.encrypt(dealer_id_input)));
                            jo.put("the_otp", MCrypt.bytesToHex( mcrypt.encrypt(my_otp)));

                            POST_result = new HttpCalling_().httpPostCallWithXmlResponseDecrypted(url,jo.toString()).trim();

                            print_log_d("_orur4_O_U_2 ", url + "");
                            print_log_d("_orur4_O_P_2 ", jo + "");
                            print_log_d("_orur4_O_R_2 ",POST_result + "");
                        }



                        jo2 = new JSONObject(POST_result);
                        if(jo2.has("process_status") && jo2.getString("process_status").equalsIgnoreCase("yes"))
                        {
                            loginStatus = true;
                            set_user_type(mContext, jo2.optString("user_type"));
                            set_belong_dealer_code(mContext, jo2.optString("belong_dealer_code"));
                            set_logged_sub_dealer_name(mContext, jo2.optString("emp_name"));
                            set_belong_dealer_dns_code(mContext, jo2.optString("belong_dealer_dns_code"));
                            set_belong_dealer_name(mContext, jo2.optString("belong_dealer_name"));
                            set_dns_emp_code(mContext, jo2.optString("dns_emp_code"));

                            set_emp_or_customer_code(mContext, jo2.optString("emp_code"));
                            set_profile_image_url(mContext, jo2.optString("the_profile_image_url"));
                            set_dealer_submit_form(mContext, jo2.optString("is_survey_form_submitted"));
                            print_log_d("get_profile_image_url_ ", get_profile_image_url(mContext) + "");
                            print_log_d("get_dealer_submit_form_ ", get_dealer_submit_form(mContext) + "");
                        }
                        else
                        {
                            loginStatus = false;
                        }

                    }
                    catch (Exception e)
                    {
                        print_log_d("_orur4_O_C_1 ",e + "");

                        POST_result = "Network Failure";
                    }
                }
            }
            catch (Exception e)
            {
                print_log_d("_orur4_O_C_2 ",e + "");
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
                if(loginStatus)
                {
                    setLoginStatus(mContext, true);
                    set_login_mobile_number(mContext, login_mobile_number);
                    set_dealer_id(mContext, dealer_id_input);
                    redirection = "OTPActivity";
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    mContext.startActivity(intent);
                    mContext.finishAffinity();
                }
                else
                {
                    Utils.cancelProgressDialog();
                    Toast.makeText(mContext, jo2.getString("process_message") + "", Toast.LENGTH_SHORT).show();
                }
            }
            catch (Exception e)
            {
                e.printStackTrace();
                Utils.cancelProgressDialog();
            }
        }
    }
}
