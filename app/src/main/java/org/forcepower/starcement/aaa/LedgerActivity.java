package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_branch_wise_pg_rollout;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.changeDateFormat_;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.adapter.LedgerListViewAdapter.convertDate;
import static org.forcepower.starcement.constants.Constants.acedns_dashboard_webLink;
import static org.forcepower.starcement.constants.Constants.acedns_star_ledger_by_id;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.detailed_statement_pdf_download;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.View;
import android.widget.DatePicker;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import org.forcepower.starcement.R;
import org.forcepower.starcement.StarCementDB;
import org.forcepower.starcement.Utils_;
import org.forcepower.starcement.adapter.LedgerListViewAdapter;
import org.forcepower.starcement.commonDatabaseHelper;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.List;
import java.util.Locale;

public final class LedgerActivity extends AceDnsParentActivity
{
    private Activity mContext;
    private StarCementDB starCementDB;
    private ListView rvLedger;
    private TextView tvLedgerBalanc, tvOS2, tv_download_detailed_statement,
    tv_start_date, tv_end_date;
    private String from_date  = "", to_date = "";
    private LinearLayout ll_date_selector;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_ledger);
        mContext= this;
        try
        {
            if(get_user_type(mContext).equalsIgnoreCase("broker"))
            {
                selected_customr_code = get_selected_customer_code(mContext);
            }
            else
            {
                selected_customr_code = get_emp_or_customer_code(mContext);
            }
            starCementDB = new StarCementDB(mContext);

            tvLedgerBalanc = (TextView) findViewById(R.id.tvLedgerBalance);
            tvOS2 = (TextView) findViewById(R.id.tvOS2);
            rvLedger = (ListView) findViewById(R.id.rvLedger);
            TextView tv_proceed = (TextView) findViewById(R.id.tv_proceed);
            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("LEDGER BALANCE");

            final ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });
            final LinearLayout llHeaderDetails = (LinearLayout) findViewById(R.id.llHeaderDetails);
            llHeaderDetails.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    try
                    {
                        Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(acedns_dashboard_webLink));
                        startActivity(browserIntent);
                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                }
            });


            if (HTTPUtils.isConnectionPossible(mContext))
            {
                new TRANS_GetLedgerDetails_Asynctask(mContext).execute("");
            }
            else
            {

                Utils_.closeApp(mContext,check_internet_connection);
            }

            //
            final LinearLayout ll_bal_payment = (LinearLayout) findViewById(R.id.ll_bal_payment);
            TextView tv_make_payment = (TextView) findViewById(R.id.tv_make_payment);
            if(get_user_type(mContext).equalsIgnoreCase("broker"))
            {
                tvHeaderText.setText(get_selected_customer_name(mContext));
                tv_make_payment.setVisibility(View.GONE);
            }
            else if(get_user_type(mContext).equalsIgnoreCase("sub dealer"))
            {
                ll_bal_payment.setVisibility(View.VISIBLE);
                tv_make_payment.setVisibility(View.GONE);
            }
            else
            {
                ll_bal_payment.setVisibility(View.VISIBLE);
                tv_make_payment.setVisibility(View.VISIBLE);
            }

            //
            tv_start_date = (TextView) findViewById(R.id.tv_start_date);
            tv_end_date = (TextView) findViewById(R.id.tv_end_date);


            ll_date_selector = (LinearLayout) findViewById(R.id.ll_date_selector);
            ll_date_selector.setVisibility(View.GONE);
            tv_download_detailed_statement = (TextView) findViewById(R.id.tv_download_detailed_statement);
            tv_download_detailed_statement.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    if(ll_date_selector.getVisibility() == View.GONE)
                    {
                        ll_date_selector.setVisibility(View.VISIBLE);
                    }
                    else
                    {
                        ll_date_selector.setVisibility(View.GONE);
                    }
                }
            });

            //
            tv_proceed.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
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
                        show_msg_alert(mContext, check_internet_connection, false);
                    }
                    else
                    {
                        final String the_start_date = tv_start_date.getTag().toString(),
                                the_end_date = tv_end_date.getTag().toString(),
                                the_customer_code =selected_customr_code,
                                user_type = get_user_type(mContext),

                                UrL = detailed_statement_pdf_download+"?the_start_date="+the_start_date+"&the_end_date="+
                                        the_end_date+"&the_customer_code="+the_customer_code+"&user_type="+user_type;
                        print_Log_d("UrL_452 ", UrL);
                        Intent intent = new Intent(mContext, PdfViewerActivity.class);
                        intent.putExtra("pdf_file_name", "ledger_stmnt_"+System.currentTimeMillis()+".pdf");
                        intent.putExtra("pdf_download_url",  UrL );
                        intent.putExtra("scheme_header", "Detailed Statement PDF");
//                        intent.putExtra("download", "download");
                        startActivity(intent);
                    }
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void confirmLedger(View view) {
        try
        {
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                Intent intent = new Intent(mContext, LedgerMonthWiseActivity.class);
                startActivity(intent);
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

    public void make_payment(View view) {
        try
        {
            if(get_branch_wise_pg_rollout(mContext).matches("ACTIVE"))
            {
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    Intent intent = new Intent(mContext, PayAmountActivity.class);
                    intent.putExtra("initialBalance", tvLedgerBalanc.getText().toString().trim()+"");
                    intent.putExtra("coming_from", "ledger");
                    startActivity(intent);
                }
                else
                {
                    show_msg_alert(mContext, check_internet_connection, false);
                }
            }
            else
            {
                show_msg_Dialog(mContext, "Coming Soon");
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
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
            final DatePickerDialog picker_to = new DatePickerDialog(mContext,
                    new DatePickerDialog.OnDateSetListener() {
                        @Override
                        public void onDateSet(DatePicker datePicker, int year, int monthOfYear, int dayOfMonth) {
                            String date = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
                            date = changeDateFormat_(date, "dd/MM/yyyy", "MMM dd, yyyy");
                            String date_tag = changeDateFormat_(date, "MMM dd, yyyy", "yyyy-MM-dd");
                            if(view instanceof LinearLayout)
                            {
                                if(view.getTag().toString().matches("start_date"))
                                {
                                    tv_start_date.setText(date);
                                    tv_start_date.setTag(date_tag);
                                }
                                else
                                {
                                    tv_end_date.setText(date);
                                    tv_end_date.setTag(date_tag);
                                }

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

    public final class TRANS_GetLedgerDetails_Asynctask extends AsyncTaskCoroutine<String, String> {
        private Activity mContext;
        public TRANS_GetLedgerDetails_Asynctask(final Activity mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            Utils.showProgressDialog(mContext, "Updating ledger...");
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
                        final String url = acedns_star_ledger_by_id+ selected_customr_code;
                        print_log_d("PRINT_LEDGER_URL_130", url);
                        POST_result = HTTPUtils.getDataByHTTP_GET(mContext, url);

                        final JSONObject jo = new JSONObject(POST_result);
                        if(jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes"))
                        {
                            try
                            {
                                starCementDB.deleteTable("ledger");
                                starCementDB.deleteTable("ledger_balance");

                                final String ledger_balance_data = jo.getString("ledger_balance_data");
                                final JSONObject joBal = new JSONObject(ledger_balance_data);
                                //need to add dns_customer_code
                                starCementDB.insertLederBal(joBal.getString("customer_code"),
                                        joBal.getString("balance"),
                                        joBal.getString("date"),
                                        joBal.getString("link")
                                );
                            }
                            catch (Exception e)
                            {
                                e.printStackTrace();
                            }
                            try
                            {
                                final String ledger_data = jo.getString("ledger_data");
                                final JSONArray ja = new JSONArray(ledger_data);

                                for(int i=0; i<ja.length(); i++)
                                {
                                    final JSONObject e = ja.getJSONObject(i);
                                    print_log_d("ACEdns_PRINT_80", e.getString("quantity"));
                                    //need to add dns_customer_code
                                    starCementDB.insertLeder(
                                            e.getString("customer_code"),
                                            e.getString("voucher_date"),
                                            e.getString("voucher_no"),
                                            e.getString("quantity"),
                                            e.getString("amount_dr"),
                                            e.getString("amount_cr"),
                                            e.getString("narration"),
                                            e.getString("entry_date")
                                    );
//                                    entry_date
                                }
                            }
                            catch (Exception e)
                            {
                                e.printStackTrace();
                            }
                        }

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
        public void onPostExecute(String result) {
            super.onPostExecute(result);

            try
            {
                if(result.equalsIgnoreCase("Network Failure"))
                {
                    Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                }
                else
                {
                    final List<commonDatabaseHelper>all= starCementDB.getAllLedgerDetails(); //last 50 result
                    final LedgerListViewAdapter ledgerAdapter = new LedgerListViewAdapter(mContext, all);
                    rvLedger.setAdapter(ledgerAdapter);
                    ledgerAdapter.notifyDataSetChanged();

                    if(!starCementDB.getLedgerBalance("balance").matches(""))
                        tvLedgerBalanc.setText("" + starCementDB.getLedgerBalance("balance"));

                    else
                        tvLedgerBalanc.setText("00.00");

                    tvOS2.setText("* LAST 50 TRANSACTIONS AS ON " +  convertDate(starCementDB.getLedgerBalance("date")));
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
