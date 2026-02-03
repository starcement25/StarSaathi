package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.changeDateFormat_;
import static org.forcepower.starcement.constants.Constants.acedns_star_ledger_subdealer_rssd;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.os.Bundle;
import android.view.View;
import android.widget.DatePicker;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.LedgerSubDealerAdapter;
import org.forcepower.starcement.bean.LedgerModel;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.Locale;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class SubDealerLedgerActivity extends AceDnsParentActivity
{
    private TextView tv_start_date, tv_end_date, empty_text_view, tvLedgerBalance;
    private String from_date  = "", to_date = "";
    private ListView mLisview;

    private Activity mContext_;
    private ArrayList<LedgerModel>ledger_data_list = new ArrayList<>();
    private LedgerSubDealerAdapter oAdapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_subdealer_ledger);

        mContext_ = this;

        try
        {
            if(get_user_type(mContext_).equalsIgnoreCase("broker"))
            {
                selected_customr_code = get_selected_customer_code(mContext_);
            }
            else
            {
                selected_customr_code = get_emp_or_customer_code(mContext_);
            }
            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("LEDGER BALANCE");

            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });

            mLisview = (ListView) findViewById(R.id.mLisview);
            empty_text_view = (TextView) findViewById(R.id.empty_text_view);
            tvLedgerBalance = (TextView) findViewById(R.id.tvLedgerBalance);
            mLisview.setEmptyView(empty_text_view);

            //
            tv_start_date = (TextView) findViewById(R.id.tv_start_date);
            tv_end_date = (TextView) findViewById(R.id.tv_end_date);

            final String current_date = new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault()).format(new Date());
            final Calendar cldr = Calendar.getInstance();
            cldr.add(Calendar.DATE, -30);  // number of days

            final int dayOfMonth = cldr.get(Calendar.DAY_OF_MONTH);
            final int monthOfYear = cldr.get(Calendar.MONTH);
            final int year = cldr.get(Calendar.YEAR);

            String previous_date = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
            previous_date = changeDateFormat_(previous_date, "dd/MM/yyyy", "MMM dd, yyyy");

            tv_start_date.setText(previous_date + "");
            tv_end_date.setText(current_date + "");


            if(get_user_type(mContext_).equalsIgnoreCase("broker"))
            {
                tvHeaderText.setText(get_selected_customer_name(mContext_));
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        finally
        {
            if(HTTPUtils.isConnectionPossible(mContext_))
            {
                new TRANS_GetLedger_Asynctask(mContext_).execute("");
            }
            else
            {
                show_msg_Dialog(mContext_, check_internet_connection);
            }
        }
    }

    public void show_calendar(final View view) {
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
            final DatePickerDialog picker_to = new DatePickerDialog(mContext_,
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
                                Toast.makeText(mContext_, "Please select start date.", Toast.LENGTH_SHORT).show();
                            }
                            else if(tv_end_date.getText().toString().trim().matches(""))
                            {
                                Toast.makeText(mContext_, "Please select end date.", Toast.LENGTH_SHORT).show();
                            }
                            else if(!HTTPUtils.isConnectionPossible(mContext_))
                            {
                                Toast.makeText(mContext_, check_internet_connection, Toast.LENGTH_SHORT).show();
                            }
                            else
                            {
                                new TRANS_GetLedger_Asynctask(mContext_).execute("");
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
    
    public final class TRANS_GetLedger_Asynctask extends AsyncTaskCoroutine<String, String> {
        private Activity mContext;
        private JSONObject jo = new JSONObject();

        public TRANS_GetLedger_Asynctask(final Activity mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            Utils.showProgressDialog(mContext_, "Updating please wait..");
        }

        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            ledger_data_list.clear();
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                try
                {
                    final String url = acedns_star_ledger_subdealer_rssd;
                    print_log_d("acedns_star_ledger_subdealer_rssd ", url);

                    final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);
                    nameValuePairs.add(new BasicNameValuePair("the_id", selected_customr_code));
                    nameValuePairs.add(new BasicNameValuePair("user_type", get_user_type(mContext)));
                    from_date = tv_start_date.getText().toString();
                    to_date = tv_end_date.getText().toString();
//                    nameValuePairs.add(new BasicNameValuePair("start_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", from_date)));
//                    nameValuePairs.add(new BasicNameValuePair("end_date", Utils.changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", to_date)));

                    POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);

                    print_log_d("acedns_star_ledger_subdealer_rssd_nameValuePairs ", nameValuePairs.toString());
                    print_log_d("acedns_star_ledger_subdealer_rssd_result ", POST_result);

                    jo = new JSONObject(POST_result);
                    final String ledger_data = jo.optString("ledger_data");
                    final JSONArray jsonArray = new JSONArray(ledger_data);
                    for(int i=0; i<jsonArray.length(); i++)
                    {
                        final JSONObject e = jsonArray.getJSONObject(i);
                        final LedgerModel lM = new LedgerModel();

                        lM.setCustomer_code(e.optString("customer_code"));
                        lM.setDns_customer_code(e.optString("dns_customer_code"));
                        lM.setVoucher_date(Utils.changeDateFormat( "MM/dd/yyyy", "MMM dd, yyyy", e.optString("voucher_date")));
                        final DateFormat formatter = new SimpleDateFormat("MM/dd/yyyy HH:mm:ss", Locale.getDefault());
                        final Date firstDate = formatter.parse(e.optString("voucher_date") + " 00:00:00");
                        lM.set_sort_date_time(firstDate);
                        lM.setVoucher_no(e.optString("voucher_no"));
                        lM.setAmount(e.optString("amount"));
                        lM.setBalance(e.optString("balance"));
                        lM.setNarration(e.optString("narration"));
                        lM.setEntry_date(Utils.changeDateFormat( "MM/dd/yyyy", "MMM dd, yyyy", e.optString("entry_date")));

                        ledger_data_list.add(lM);
                    }

                    //
                    Collections.sort(ledger_data_list, new Comparator<LedgerModel>() {
                        @Override
                        public int compare(final LedgerModel t0, final LedgerModel t1) {
                            return t1.get_sort_date_time().compareTo(t0.get_sort_date_time());
                        }
                    });
                    //
                    oAdapter = new LedgerSubDealerAdapter(mContext, ledger_data_list);

                }
                catch (Exception e)
                {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try
            {
                tvLedgerBalance.setText(jo.optString("balance"));

                if(ledger_data_list.size() > 0)
                {
                    mLisview.setAdapter(oAdapter);
                }
                else
                {
                    empty_text_view.setText(jo.optString("process_message"));
                }
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
