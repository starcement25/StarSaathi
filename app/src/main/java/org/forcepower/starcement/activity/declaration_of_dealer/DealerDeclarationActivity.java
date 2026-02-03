package org.forcepower.starcement.activity.declaration_of_dealer;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.api_get_exclusive_dealername;
import static org.forcepower.starcement.constants.Constants.dealer_declaration_url;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;

import androidx.appcompat.app.AppCompatActivity;
import org.forcepower.starcement.BuildConfig;
import org.forcepower.starcement.R;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONObject;

import java.time.LocalDate;
import java.time.format.DateTimeFormatter;
import java.util.ArrayList;
import java.util.Locale;

public class DealerDeclarationActivity extends AppCompatActivity {
    private ImageView ivHeaderBack;
    private EditText et_dealerName,et_branch,et_liftingQty;
    private Button btn_submit;
    private Activity mContext;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_dealer_declaration);
        mContext=this;
        init();
    }

    private void init(){
        ivHeaderBack=findViewById(R.id.ivHeaderBack);
        et_dealerName=findViewById(R.id.et_dealerName);
        et_branch=findViewById(R.id.et_branch);
        et_liftingQty=findViewById(R.id.et_liftingQty);
        btn_submit=findViewById(R.id.btn_submit);

        onClickFunction();
        new TRANS_GetDealerDetails_Asynctask(mContext).execute();
    }

    private ArrayList<String> generateMonthList() {
        ArrayList<String> monthList = new ArrayList<>();
        LocalDate currentDate = LocalDate.now();
        DateTimeFormatter formatter = DateTimeFormatter.ofPattern("MMM-yyyy", Locale.ENGLISH);

        for (int i = -1; i <= 1; i++) {
            LocalDate futureDate = currentDate.plusMonths(i);
            String formatted = futureDate.format(formatter);
            monthList.add(formatted);
        }

        return monthList;
    }

    private void onClickFunction(){
        ivHeaderBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
            }
        });
        btn_submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                try {
                        if(et_dealerName.getText().toString().trim().isEmpty()){
                        show_msg_alert(mContext, "Please enter dealer name", false);
                    }else if(et_branch.getText().toString().trim().isEmpty()){
                        show_msg_alert(mContext, "Please enter branch name", false);
                    }else if(et_liftingQty.getText().toString().trim().isEmpty()){
                        show_msg_alert(mContext, "Please enter lifting Qty.", false);
                    }else{
                        int qty = 0;
                        int checker=0;
                        try {
                            qty = Integer.parseInt(et_liftingQty.getText().toString().trim());
                            checker=1;
                        }catch(Exception e){
                            show_msg_alert(mContext, "Please enter valid lifting Qty", false);
                        }
                        if(checker==1){
                            new TRANS_ExclusiveDealerDeclaration_Asynctask(mContext).execute();
                        }
                    }
                }catch (Exception e){
                    show_msg_alert(mContext, "Something wrong. Contact to admin.", false);
                }
            }
        });
    }

    public final class TRANS_ExclusiveDealerDeclaration_Asynctask extends AsyncTaskCoroutine<String, String> {
        Activity mContext;
        JSONObject jo = new JSONObject();

        public TRANS_ExclusiveDealerDeclaration_Asynctask(Activity mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            Utils.showProgressDialog(mContext, "Requesting...");

        }
        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            try
            {
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    try
                    {
                        final String url = dealer_declaration_url;
                        final JSONObject jo1=new JSONObject();
                        jo1.put("customer_id",get_dealer_id(mContext));
                        jo1.put("user_type",get_user_type(mContext) );
                        jo1.put("month", getIntent().getStringExtra("date"));
                        jo1.put("dealer_name",  et_dealerName.getText().toString().trim());
                        jo1.put("branch", et_branch.getText().toString().trim());
                        jo1.put("lifting_qty", et_liftingQty.getText().toString().trim());


                        Log.d("TAG", "_DOWNLOAD_ dealer_declaration_url : "+jo1);
                        Log.d("TAG", "_DOWNLOAD_ dealer_declaration_url : "+url);

                        POST_result = HTTPUtils.getDataByHTTP_POST1(mContext, url, jo1);

                        jo = new JSONObject(POST_result);
                        Log.d("TAG", "_DOWNLOAD_ dealer_declaration_url result: "+jo);

                        if(jo.optString("process_status").equalsIgnoreCase("Yes")){
                            Log.d("TAG", "_DOWNLOAD_ dealer_declaration_url result11 hit ");
                            ((Activity) mContext).runOnUiThread(new Runnable() {
                                @Override
                                public void run() {
                                    show_msg_alert((Activity) mContext, jo.optString("message"), true);
                                }
                            });
                        }else{
                            Log.d("TAG", "_DOWNLOAD_ dealer_declaration_url result22 hit ");
                            ((Activity) mContext).runOnUiThread(new Runnable() {
                                @Override
                                public void run() {
                                    show_msg_alert((Activity) mContext, jo.optString("error"), false);
                                }
                            });
                        }
                    }
                    catch (Exception e)
                    {
                        POST_result = "Network Failure";
                    }
                }
                else{
                    ((Activity) mContext).runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            show_msg_alert((Activity)mContext, "Please check your internet connection.", false);
                        }
                    });

                }
            }
            catch (Exception e){
                e.printStackTrace();
                ((Activity) mContext).runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        show_msg_alert((Activity)mContext, "Something wrong. Contact to admin.", false);
                    }
                });

            }
            return POST_result;
        }
        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try{

            }
            catch (Exception e){
                e.printStackTrace();
            }
            finally{
                Utils.cancelProgressDialog();
            }
        }
    }

    public final class TRANS_GetDealerDetails_Asynctask extends AsyncTaskCoroutine<String, String> {
        Activity mContext;
        JSONObject jo = new JSONObject();

        public TRANS_GetDealerDetails_Asynctask(Activity mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            Utils.showProgressDialog(mContext, "Getting Information...");

        }
        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            try
            {
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    try
                    {
                        final String url = api_get_exclusive_dealername+get_dealer_id(mContext);
                        Log.d("TAG", "_DOWNLOAD_ get_exclusive_dealer_name: "+url);

                        POST_result = HTTPUtils.getDataByHTTP_GET(mContext, url);

                        jo = new JSONObject(POST_result);
                        if(jo.optString("process_status").equalsIgnoreCase("YES")){
                            et_dealerName.setText(jo.getString("customer_name"));
                            et_branch.setText(jo.optString("branch_name"));
                        }

                        Log.d("TAG", "_DOWNLOAD_ get_exclusive_dealer_name result: "+jo);
                    }
                    catch (Exception e)
                    {
                        POST_result = "Network Failure";
                    }
                }
            }
            catch (Exception e){
                e.printStackTrace();
            }
            return POST_result;
        }
        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try{

            }
            catch (Exception e){
                e.printStackTrace();
            }
            finally{
                Utils.cancelProgressDialog();
            }
        }
    }
}