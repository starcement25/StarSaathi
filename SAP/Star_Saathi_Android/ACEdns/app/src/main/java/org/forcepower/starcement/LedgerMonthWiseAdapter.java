package org.forcepower.starcement;

//public final class LedgerListViewAdapter {
//}
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.BaseAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;
import org.forcepower.starcement.util.ConnectionDetector;
import org.forcepower.starcement.util.HTTPUtils;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Locale;

import static org.forcepower.starcement.constants.Constants.acedns_star_update_this_month_ledger_status;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.util.Utils.print_log_d;

/**
 * A custom adapter designed to fetch bookmarks from a cursor. Before Honeycomb we used
 * SimpleCursorAdapter, but it assumes the existence of an _id column, and the bookmark schema was
 * rewritten for HC without one. This caused the app to crash, hence this new class, which is
 * forwards and backwards compatible.
 *
 * @author dswitkin@google.com (Daniel Switkin)
 */
public final class LedgerMonthWiseAdapter extends BaseAdapter {
    private Activity activity;
    List<commonDatabaseHelper>all;
    ArrayList<NameValuePair> nameValuePairs = new ArrayList<>();
    public LedgerMonthWiseAdapter(Activity activity_, List<commonDatabaseHelper> all) {
        this.activity = activity_;
        this.all = all;
    }


    public int getCount() {
        return all.size();
    }


    public Object getItem(int index) {
        // Not used, so no point in retrieving it.
        return null;
    }


    public long getItemId(int index) {
        return index;
    }


    public View getView(final int index, View view, ViewGroup viewGroup) {
        RelativeLayout layout;
        if (view instanceof RelativeLayout) {
            layout = (RelativeLayout) view;
        } else {
            LayoutInflater factory = LayoutInflater.from(activity);
            layout = (RelativeLayout) factory.inflate(R.layout.list_item_ledger_month_wise, viewGroup, false);
        }
        ((TextView) layout.findViewById(R.id.tvVoucherDate)).setText((all.get(index).getItem6())); //DATE -> ledger_of
        ((TextView) layout.findViewById(R.id.tvVoucherDr)).setText(all.get(index).getItem2()); //AMOUNT -> total_amount_dr
        ((TextView) layout.findViewById(R.id.tvVoucherCr)).setText(all.get(index).getItem3()); //AMOUNT -> total_amount_cr

        ((TextView) layout.findViewById(R.id.tvLedgerConfirm)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                try
                {
                    ConnectionDetector cd= new ConnectionDetector(activity);
                    Boolean isInternetPresent=cd.isConnectingToInternet();
                    if(isInternetPresent)
                    {
                        new TRANS_DelLedgerMonthWise_Asynctask(
                                all.get(index).getItem0(), //customer_code
                                all.get(index).getItem1(), //dns_customer_code
                                all.get(index).getItem2(), //total_amount_dr
                                all.get(index).getItem3(), //total_amount_cr
                                "APPROVED", //STATUS
                                all.get(index).getItem5(), //ledger_year_month_day
                                "" //empty comment
                                ).execute();
                    }
                    else
                    {
                        Utils_.closeApp(activity,check_internet_connection);
                    }
                }
                catch (Exception e)
                {
                    e.printStackTrace();
                }
            }
        });
        ((TextView) layout.findViewById(R.id.tvLedgerReject)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                narratioonDetails(index);
            }
        });


        return layout;
    }
    public void narratioonDetails(final int index)
    {
        try
        {
            final Dialog stkDialog = new Dialog(activity, R.style.PauseDialog);
            stkDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            stkDialog.setContentView(R.layout.ledger_ledger_reject);
            stkDialog.setCancelable(true);

            ImageView ivHeaderBack = (ImageView) stkDialog.findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    stkDialog.dismiss();
                }
            });

            final EditText etRejectLedger = (EditText) stkDialog.findViewById(R.id.etRejectLedger);
            Button btn_ledger_reject = (Button) stkDialog.findViewById(R.id.btn_ledger_reject);
            btn_ledger_reject.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    try
                    {
                        if(etRejectLedger.getText().toString().trim().length() > 0)
                        {
                            ConnectionDetector cd= new ConnectionDetector(activity);
                            Boolean isInternetPresent=cd.isConnectingToInternet();
                            if(isInternetPresent)
                            {
                                stkDialog.dismiss();
                                new TRANS_DelLedgerMonthWise_Asynctask(
                                        all.get(index).getItem0(), //customer_code
                                        all.get(index).getItem1(), //dns_customer_code
                                        all.get(index).getItem2(), //total_amount_dr
                                        all.get(index).getItem3(), //total_amount_cr
                                        "REJECTED", //STATUS
                                        all.get(index).getItem5(), //ledger_year_month_day
                                        etRejectLedger.getText().toString() //comment
                                ).execute();
                            }
                            else
                            {
                                Utils_.closeApp(activity,check_internet_connection);
                            }
                        }
                        else
                        {
                            Utils_.closeApp(activity,"Please write a comment...");
                        }
                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                }
            });


            stkDialog.show();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public static String convertDate(String date)
    {
        try
        {
            SimpleDateFormat spf=new SimpleDateFormat("MM/dd/yyyy hh:mm:ss aaa", Locale.getDefault());
            Date newDate=spf.parse(date);
            spf= new SimpleDateFormat("MM/dd/yyyy", Locale.getDefault());
            date = spf.format(newDate);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return date;
    }
    
    public final class TRANS_DelLedgerMonthWise_Asynctask extends AsyncTaskCoroutine<String, String>
    {
        ProgressDialog mStepProgressDialog;
        JSONObject jo = new JSONObject();
        String customer_code = "",dns_customer_code = "", total_amount_dr = "",
                total_amount_cr = "", status = "", ledger_year_month_day = "", comment = "";

        public TRANS_DelLedgerMonthWise_Asynctask(String customer_code, String dns_customer_code,
                                                  String total_amount_dr, String total_amount_cr,
                                                  String status, String ledger_year_month_day,
                                                  String comment)
        {
            this.customer_code = customer_code;
            this.dns_customer_code = dns_customer_code;
            this.total_amount_dr = total_amount_dr;
            this.total_amount_cr = total_amount_cr;
            this.status = status;
            this.ledger_year_month_day = ledger_year_month_day;
            this.comment = comment;
        }

        @Override
        public void onPreExecute()
        {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(activity);
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
                if (HTTPUtils.isConnectionPossible(activity))
                {
                    try
                    {
                        nameValuePairs = new ArrayList<>(2);
                        jo = new JSONObject();
                        final String url = acedns_star_update_this_month_ledger_status;

                        nameValuePairs.add(new BasicNameValuePair("customer_code", customer_code));
                        nameValuePairs.add(new BasicNameValuePair("dns_customer_code", dns_customer_code));
                        nameValuePairs.add(new BasicNameValuePair("total_amount_dr", total_amount_dr));
                        nameValuePairs.add(new BasicNameValuePair("total_amount_cr", total_amount_cr));
                        nameValuePairs.add(new BasicNameValuePair("status", status));
                        nameValuePairs.add(new BasicNameValuePair("ledger_year_month_day", ledger_year_month_day));
                        if(!comment.trim().matches(""))
                            nameValuePairs.add(new BasicNameValuePair("comment", comment));
                        print_log_d("PRINT_LEDGER_update_URL_250", url);
                        POST_result = HTTPUtils.getDataByHTTP_POST(activity, url, nameValuePairs);

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
                    Toast.makeText(activity, check_internet_connection, Toast.LENGTH_SHORT).show();
                }
                else
                {
                    Toast.makeText(activity, jo.optString("process_message"), Toast.LENGTH_SHORT).show();
                    if(jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes"))
                    {
                        activity.finish();
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

