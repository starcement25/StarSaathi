package org.forcepower.starcement.activity.kismet_ki_bori;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.api_get_dealer_information;
import static org.forcepower.starcement.constants.Constants.api_get_exclusive_dealername;
import static org.forcepower.starcement.constants.Constants.api_post_dealer_bori_information;
import static org.forcepower.starcement.constants.Constants.dealer_declaration_url;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.text.Editable;
import android.text.Html;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.CompoundButton;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Switch;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.aaa.MenuActivity;
import org.forcepower.starcement.activity.kismet_ki_bori.adapter.AddCouponCodeAdapter;
import org.forcepower.starcement.activity.kismet_ki_bori.dataset.CouponDataSet;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Calendar;

public class KismetKiBoriActivity extends AppCompatActivity implements View.OnClickListener {

    ImageView backButton;
    TextView ownerNameTextView, ownerMobileNoTextView, dateOfPurchaseTextView, dateOfPurchaseSelectedTextView, quantityTextView, addNewCouponButton;
    EditText ownerNameEditText, ownerMobileNoEditText, quantityEditText;
    LinearLayout dateOfPurchaseButton, isShowCouponCodeField;
    Switch couponGivenSwitch;
    RecyclerView couponRecyclerView;
    Button submitButton;

    Context mContext;
    int minQuantity=100,isCouponEnable=0;
    String dateOfPurchase="";
    ArrayList<CouponDataSet> dataSet = new ArrayList<>();
    AddCouponCodeAdapter adapter = new AddCouponCodeAdapter(this, dataSet, new AddCouponCodeAdapter.OnActionClickListener() {
        @Override
        public void onDeleteClicked(CouponDataSet item, int position) {
            if (position >= 0 && position < dataSet.size()) {
                dataSet.remove(position);
                adapter.notifyItemRemoved(position);
                adapter.notifyItemRangeChanged(position, dataSet.size() - position);// Optional for position sync
            }
        }
    });

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_kismet_ki_bori);
        mContext = this;
        init();
    }

    @Override
    public void onClick(View v) {
        if (v == backButton) {
            Intent intent = new Intent(this, MenuActivity.class);
            startActivity(intent);
        }
        if (v == dateOfPurchaseButton) {
            nextVisitDatePicker();
        }
        if (v == addNewCouponButton) {
            Log.d("TAG", "_DOWNLOAD_ : quantity - "+Integer.parseInt(quantityEditText.getText().toString().trim())+"\nminQuantity - "+minQuantity+"\nsize - "+dataSet.size());
            if(Integer.parseInt(quantityEditText.getText().toString().trim())/minQuantity>dataSet.size()){
                ((KismetKiBoriActivity)mContext).runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        int newPosition = dataSet.size();
                        CouponDataSet a = new CouponDataSet();
                        a.setId(dataSet.size()+1);
                        a.setCouponCode("");
                        dataSet.add(a);
                        adapter.notifyItemInserted(newPosition);
                    }
                });
            }else{
                Toast.makeText(mContext,"You can not able to add more coupon",Toast.LENGTH_LONG).show();
            }
        }
        if (v == submitButton) {
            checkData();
        }
    }

    private void switchFunction() {
        couponGivenSwitch.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                if (isChecked) {
                    if(quantityEditText.getText().toString().trim().equalsIgnoreCase("")){
                        Toast.makeText(mContext,"Please first add your quantity of bag purchase",Toast.LENGTH_LONG).show();
                        couponGivenSwitch.setChecked(false);
                    }else if(Integer.parseInt(quantityEditText.getText().toString().trim())<minQuantity){
                        Toast.makeText(mContext,"Your purchase quantity must be more or equal to "+minQuantity,Toast.LENGTH_LONG).show();
                        couponGivenSwitch.setChecked(false);
                    }else{
                        isCouponEnable=1;
                        ((KismetKiBoriActivity) mContext).runOnUiThread(new Runnable() {
                            @Override
                            public void run() {
                                int newPosition = dataSet.size();
                                CouponDataSet a = new CouponDataSet();
                                a.setId(1);
                                a.setCouponCode("");
                                dataSet.add(a);
                                isShowCouponCodeField.setVisibility(View.VISIBLE);
                                adapter.notifyItemInserted(newPosition);

                                quantityEditText.setEnabled(false);
                            }
                        });
                    }
                } else {
                    isCouponEnable=0;
                    ((KismetKiBoriActivity) mContext).runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            dataSet.clear();
                            isShowCouponCodeField.setVisibility(View.GONE);
                            quantityEditText.setEnabled(true);
                        }
                    });
                }
            }
        });
    }

    private void init() {
        backButton = findViewById(R.id.backButton);
        ownerNameTextView = findViewById(R.id.ownerNameTextView);
        ownerMobileNoTextView = findViewById(R.id.ownerMobileNoTextView);
        dateOfPurchaseTextView = findViewById(R.id.dateOfPurchaseTextView);
        dateOfPurchaseSelectedTextView = findViewById(R.id.dateOfPurchaseSelectedTextView);
        quantityTextView = findViewById(R.id.quantityTextView);
        addNewCouponButton = findViewById(R.id.addNewCouponButton);
        ownerNameEditText = findViewById(R.id.ownerNameEditText);
        ownerMobileNoEditText = findViewById(R.id.ownerMobileNoEditText);
        quantityEditText = findViewById(R.id.quantityEditText);
        dateOfPurchaseButton = findViewById(R.id.dateOfPurchaseButton);
        couponGivenSwitch = findViewById(R.id.couponGivenSwitch);
        couponRecyclerView = findViewById(R.id.couponRecyclerView);
        submitButton = findViewById(R.id.submitButton);
        isShowCouponCodeField = findViewById(R.id.isShowCouponCodeField);

        ownerNameTextView.setText(Html.fromHtml("House Owner Name <font color='#FF0000'>*</font>"));
        ownerMobileNoTextView.setText(Html.fromHtml("House Owner Mobile Number <font color='#FF0000'>*</font>"));
        dateOfPurchaseTextView.setText(Html.fromHtml("Date of Purchase <font color='#FF0000'>*</font>"));
        quantityTextView.setText(Html.fromHtml("Quantity (in Bags) <font color='#FF0000'>*</font>"));

        backButton.setOnClickListener(this);
        dateOfPurchaseButton.setOnClickListener(this);
        addNewCouponButton.setOnClickListener(this);
        submitButton.setOnClickListener(this);

        isShowCouponCodeField.setVisibility(View.GONE);

        switchFunction();

        couponRecyclerView.setLayoutManager(new LinearLayoutManager(this));
        couponRecyclerView.setAdapter(adapter);

        new TRANS_GetProfileInformation_Asynctask(KismetKiBoriActivity.this).execute();
    }

    private void nextVisitDatePicker() {
        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);

        DatePickerDialog datePickerDialog = new DatePickerDialog(
                KismetKiBoriActivity.this,
                (view, selectedYear, selectedMonth, selectedDay) -> {
                    // Month is 0-based, so add 1
                    String date = selectedDay + "/" + (selectedMonth + 1) + "/" + selectedYear;
                    dateOfPurchaseSelectedTextView.setText(date);
                    dateOfPurchase = selectedYear+"-";
                    if(selectedMonth + 1<10){
                        dateOfPurchase=dateOfPurchase+"0"+(selectedMonth + 1)+"-";
                    }else{
                        dateOfPurchase=dateOfPurchase+(selectedMonth + 1)+"-";
                    }
                    if(selectedDay<10){
                        dateOfPurchase=dateOfPurchase+"0"+selectedDay;
                    }else{
                        dateOfPurchase=dateOfPurchase+selectedDay;
                    }
                },
                year, month, day
        );
        datePickerDialog.getDatePicker().setMaxDate(calendar.getTimeInMillis());
        datePickerDialog.show();
    }

    private void checkData(){
        if(ownerNameEditText.getText().toString().trim().equalsIgnoreCase("")){
            Toast.makeText(mContext,"Please enter House Owner Name",Toast.LENGTH_LONG).show();
        }else if(ownerMobileNoEditText.getText().toString().trim().equalsIgnoreCase("")){
            Toast.makeText(mContext,"Please enter House Owner Contact Number",Toast.LENGTH_LONG).show();
        }else if(ownerMobileNoEditText.getText().toString().trim().length()!=10){
            Toast.makeText(mContext,"Please enter valid House Owner Contact Number",Toast.LENGTH_LONG).show();
        }else if(dateOfPurchase.trim().equalsIgnoreCase("")){
            Toast.makeText(mContext,"Please select Date of Purchase",Toast.LENGTH_LONG).show();
        }else if(quantityEditText.getText().toString().trim().equalsIgnoreCase("")){
            Toast.makeText(mContext,"Please enter Purchase quantity",Toast.LENGTH_LONG).show();
        }else if(Integer.parseInt(quantityEditText.getText().toString().trim())<minQuantity){
            Toast.makeText(mContext,"Purchase quantity must be more or equal than "+minQuantity,Toast.LENGTH_LONG).show();
        }else if(isCouponEnable==0){
            Toast.makeText(mContext,"You have to add a coupon to submit",Toast.LENGTH_LONG).show();
        }else{
            int check=0;
            int checkDuplicate=0;
            int checkValid=0;
            for(var i=0;i<dataSet.size();i++){
                if(dataSet.get(i).getCouponCode().trim().equalsIgnoreCase("")){
                    check=1;
                    break;
                }
            }

            for(var i=0;i<dataSet.size();i++){
                for(var j=i+1;j<dataSet.size();j++){
                    if(dataSet.get(i).getCouponCode().trim().equals(dataSet.get(j).getCouponCode().trim())&&!dataSet.get(i).getCouponCode().trim().equalsIgnoreCase("")){
                        checkDuplicate=1;
                        break;
                    }
                }
            }

            for(var i=0;i<dataSet.size();i++){
                if(!dataSet.get(i).getCouponCode().trim().equalsIgnoreCase("")){
                    if(dataSet.get(i).getCouponCode().trim().length()!=6){
                        checkValid=1;
                        break;
                    }
                }
            }

            if(check==1){
                Toast.makeText(mContext,"Enter 6-digit coupon number to submit.",Toast.LENGTH_LONG).show();
            }else if(checkDuplicate==1){
                Toast.makeText(mContext,"Duplicate entry, please re-enter coupon number.",Toast.LENGTH_LONG).show();
            }else if(checkValid==1){
                Toast.makeText(mContext,"Enter 6-digit coupon number.",Toast.LENGTH_LONG).show();
            }else{
                new TRANS_PostBoriInformation_Asynctask(KismetKiBoriActivity.this).execute();
            }
        }
    }

    private void timerStar(){
        new Handler(Looper.getMainLooper()).postDelayed(new Runnable() {
            @Override
            public void run() {
                Intent intent = new Intent(mContext, MenuActivity.class);
                startActivity(intent);
                finish();
            }
        }, 2000);
    }

    public final class TRANS_GetProfileInformation_Asynctask extends AsyncTaskCoroutine<String, String> {
        Activity mContext;
        JSONObject jo = new JSONObject();

        public TRANS_GetProfileInformation_Asynctask(Activity mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
        }
        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            try {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    try {
                        final String url = api_get_dealer_information+get_dealer_id(mContext);
                        Log.d("TAG", "_DOWNLOAD_ get_exclusive_dealer_name: "+url);
                        POST_result = HTTPUtils.getDataByHTTP_GET(mContext, url);
                    } catch (Exception e) {
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
                JSONObject jo=new JSONObject(result);
                if(jo.getString("status").equalsIgnoreCase("yes")){
                    minQuantity=jo.getInt("bag_quantity");
                }
            } catch (Exception e){
                e.printStackTrace();
            } finally{
                Utils.cancelProgressDialog();
            }
        }
    }

    public final class TRANS_PostBoriInformation_Asynctask extends AsyncTaskCoroutine<String, String> {
        Activity mContext;
        JSONObject jo = new JSONObject();

        public TRANS_PostBoriInformation_Asynctask(Activity mContext) {
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
            try {

                if (HTTPUtils.isConnectionPossible(mContext)) {
                    try {
                        final String url = api_post_dealer_bori_information;

                        final JSONArray ja=new JSONArray();

                        for(int i=0;i<dataSet.size();i++){
                            final JSONObject jo=new JSONObject();
                            if(!dataSet.get(i).getCouponCode().trim().equalsIgnoreCase("")){
                                jo.put("coupon_code",dataSet.get(i).getCouponCode().trim());
                                ja.put(jo);
                            }
                        }


                        final JSONObject jo1=new JSONObject();
                        jo1.put("customer_id",get_dealer_id(mContext));
                        jo1.put("house_owner_name",ownerNameEditText.getText().toString().trim() );
                        jo1.put("house_owner_phone", ownerMobileNoEditText.getText().toString().trim());
                        jo1.put("date_of_purchase", dateOfPurchase );
                        jo1.put("bags_quantity", quantityEditText.getText().toString().trim());
                        jo1.put("has_coupon", "Yes");
                        jo1.put("coupons_details", ja);

                        Log.d("TAG", "_DOWNLOAD_ doInBackground: "+url);
                        Log.d("TAG", "_DOWNLOAD_ doInBackground: "+jo1);
                        POST_result = "Network Failure";
                        POST_result = HTTPUtils.getDataByHTTP_POST1(mContext, url, jo1);
                    } catch (Exception e) {
                        POST_result = "Network Failure";
                    }
                } else{
                    ((Activity) mContext).runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            show_msg_alert((Activity)mContext, "Please check your internet connection.", false);
                        }
                    });
                }
            } catch (Exception e){
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
                JSONObject jo =new JSONObject(result);
                Log.d("TAG", "_DOWNLOAD_ onPostExecute: "+jo);
                if(jo.getString("status").equalsIgnoreCase("success")){
                    ((Activity) mContext).runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            show_msg_alert((Activity)mContext, "Scheme added successfully", false);
                            timerStar();
                        }
                    });
                }else{
                    ((Activity) mContext).runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            show_msg_alert((Activity)mContext, "Coupon number already submitted.", false);
                        }
                    });
                }
            } catch (Exception e){
                e.printStackTrace();
            } finally{
                Utils.cancelProgressDialog();
            }
        }
    }
}