package org.forcepower.starcement.aaa;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import android.os.Bundle;

import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.SharedPrefData.set_profile_image_url;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.isValidIndianMobile;
import static org.forcepower.starcement.util.Utils.print_log_d;

import android.util.Log;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import org.forcepower.starcement.BuildConfig;
import org.forcepower.starcement.HttpCalling_;
import org.forcepower.starcement.R;
import org.forcepower.starcement.Utils_;
import org.forcepower.starcement.aaa.OtpActivity;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.util.ConnectionDetector;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.MCrypt;
import org.forcepower.starcement.util.Utils;
import org.json.JSONObject;


import static org.forcepower.starcement.constants.Constants.checklogin;
import static org.forcepower.starcement.util.PreferenceData.setLoginStatus;

import java.util.ArrayList;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class LoginActivity extends AceDnsParentActivity
{

    private Context mContext;
    public static String login_mobile_number = "", dealer_id_input = "";
    private EditText et_dealer_mobile, et_dealer_id;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        mContext= this;

        try
        {
            et_dealer_mobile = (EditText) findViewById(R.id.et_dealer_mobile);
            et_dealer_id = (EditText) findViewById(R.id.et_dealer_id);

            Constants.isFirstLoginOfDay = false;
            Constants.isFirstLoginOfApp = false;

            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("LOGIN");
            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setVisibility(View.INVISIBLE);



            //INITIALIZE LOGIN
            setLoginStatus(mContext, false);
            if(BuildConfig.DEBUG)
            {
               myIssues(null);
            }

            set_profile_image_url(mContext, "");
            Log.d("amitabha2715_87_ ", BuildConfig.BUILD_TYPE + " ");
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void myIssues(View view)
    {
        try
        {
            AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext, R.style.MyDialog);
            final CharSequence[] items = {"Test Server" ,"dealer1010", "broker1010", "Sub Dealer"};

            AlertDG.setItems(items, new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int item) {
                    if(items[item].toString().equalsIgnoreCase("Test Server"))
                    {
                        et_dealer_id.setText("1000000021");
                        et_dealer_mobile.setText("8837269940");
                    }
                    else if(items[item].toString().equalsIgnoreCase("DEALER1010"))
                    {
                        // dealer
                        et_dealer_id.setText("1000000341");
                        et_dealer_mobile.setText("9435031862");
                    }
                    else if(items[item].toString().equalsIgnoreCase("BROKER1010"))
                    {
                        //broker
                        et_dealer_id.setText("TEST012");
                        et_dealer_mobile.setText("7044497293");
                    }
                    else if(items[item].toString().equalsIgnoreCase("Sub Dealer"))
                    {
                        //sd
                        et_dealer_id.setText("1500013823");
                        et_dealer_mobile.setText("8921626794");
                    }
                }
            });
            AlertDG.setNegativeButton("CANCEL", new DialogInterface.OnClickListener() {

                public void onClick(DialogInterface dialog, int which) {
                    dialog.dismiss();
                }
            });
            AlertDG.setCancelable(true);
            AlertDG.create().show();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }


    public void ContinueLogin(View view)
    {
        try
        {
            if(!isValidIndianMobile(et_dealer_mobile.getText().toString().trim()))
            {
                Utils_.closeApp(mContext,"Please enter 10 digit mobile number.");
            }
            else if(et_dealer_id.getText().toString().length() <3)
            {
                Utils_.closeApp(mContext,"Please enter Dealer id");
            }
            else
            {
                final ConnectionDetector cd= new ConnectionDetector(mContext);
                final Boolean isInternetPresent=cd.isConnectingToInternet();
                if(isInternetPresent)
                {
                    //authenticate employee
                    login_mobile_number = et_dealer_mobile.getText().toString();
                    dealer_id_input = et_dealer_id.getText().toString();
                    new TRANS_PostLogin_Asynctask(mContext).execute();
                }
                else
                {
                    Utils_.closeApp(mContext,check_internet_connection);
                }

            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    
    public final class TRANS_PostLogin_Asynctask extends AsyncTaskCoroutine<String, String>
    {
        Context mContext;
        boolean loginStatus = false;
        public TRANS_PostLogin_Asynctask(Context mContext) {
            this.mContext = mContext;
        }
        ProgressDialog mStepProgressDialog;
        JSONObject jo2 = new JSONObject();
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
                final String deviceId = Constants.deviceId.length() > 0 ? Constants.deviceId : Utils.getDeviceId(mContext);

                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    try
                    {
                        String url = checklogin;
                        if(BuildConfig.API_URL.startsWith("https://qas.starsaathi.com/"))
                        {
                            url = BuildConfig.API_URL + "checkloginnew_v2.php";

                            final ArrayList<NameValuePair> mHttpParamPairs = new ArrayList<>(2);
                            mHttpParamPairs.add(new BasicNameValuePair("nickname", Constants.nickName));
                            mHttpParamPairs.add(new BasicNameValuePair("phonenumber", login_mobile_number));
                            mHttpParamPairs.add(new BasicNameValuePair("deviceid", deviceId));
                            mHttpParamPairs.add(new BasicNameValuePair("dealer_id", dealer_id_input));

                            POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);

                            print_log_d("_orur4_L_U_i ", url + "");
                            print_log_d("_orur4_L_P_i ", mHttpParamPairs + "");
                            print_log_d("_orur4_L_R_i ",POST_result + "");
                        }
                        else
                        {
                            final MCrypt mcrypt = new MCrypt();
                            final JSONObject jo=new JSONObject();
                            jo.put("nickname",MCrypt.bytesToHex( mcrypt.encrypt(Constants.nickName) ));
                            jo.put("phonenumber",MCrypt.bytesToHex( mcrypt.encrypt(login_mobile_number) ));
                            jo.put("deviceid", MCrypt.bytesToHex( mcrypt.encrypt(Utils.getDeviceId(mContext))));
                            jo.put("dealer_id", MCrypt.bytesToHex( mcrypt.encrypt(dealer_id_input)));

                            POST_result = new HttpCalling_().httpPostCallWithXmlResponseDecrypted(url,jo.toString()).trim();

                            print_log_d("_orur4_L_U_2 ", url + "");
                            print_log_d("_orur4_L_P_2 ", jo + "");
                            print_log_d("_orur4_L_R_2 ",POST_result + "");
                        }

                        jo2 = new JSONObject(POST_result);
                        if(jo2.has("process_status") && jo2.getString("process_status").equalsIgnoreCase("yes"))
                        {
                            loginStatus = true;
                        }

                    }
                    catch (Exception e)
                    {
                        print_log_d("_orur4_L_C_1 ", e.toString());
                        POST_result = "Network Failure";
                    }
                }
            }
            catch (Exception e)
            {
                print_log_d("_orur4_L_C_2 ", e.toString());
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
                    startActivity(new Intent(mContext, OtpActivity.class));
                }
                else
                {
                    if(jo2.has("process_message"))
                    {
                        Toast.makeText(mContext, jo2.getString("process_message")+"", Toast.LENGTH_SHORT).show();
                    }
                    else
                    {
                        Toast.makeText(mContext, "Try again..", Toast.LENGTH_SHORT).show();
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
    @Override
    public void onResume()
    {
        super.onResume();
    }
}
