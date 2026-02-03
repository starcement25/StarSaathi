package org.forcepower.starcement.fragments;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_dns_emp_code;
import static org.forcepower.starcement.SharedPrefData.get_login_mobile_number;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;

import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.pop_product_data_download;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import android.widget.Button;
import android.widget.GridView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;


import org.forcepower.starcement.R;
import org.forcepower.starcement.Utils_;

import org.forcepower.starcement.aaa.OrderConfirmationActivity;
import org.forcepower.starcement.adapter.PopProductListAdapter;
import org.forcepower.starcement.bean.PopProductModel;
import org.forcepower.starcement.bean.PopProductModel;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Map;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;


public final class FragmentPopOrder extends Fragment
{
    private Activity mContext;
    private GridView rvLedger;
    private PopProductListAdapter pAdapter;
    private ArrayList<PopProductModel> nameValuesProductListLocal = new ArrayList<>();
    private TextView tvNotiCount;
    public FragmentPopOrder()
    {

    }

    public static FragmentPopOrder newInstance(final String year_month)
    {
        // Required empty public constructor
        final FragmentPopOrder fragmentFirst = new FragmentPopOrder();
        final Bundle args = new Bundle();
        args.putString("year_month", year_month);
        fragmentFirst.setArguments(args);
        return fragmentFirst;
    }
//    public String get_thisCategory() {return getArguments().getString("thisCategory");}

    @Override
    public View onCreateView(final LayoutInflater inflater, final ViewGroup container, final Bundle savedInstanceState)
    {
        return inflater.inflate(R.layout.fragment_pop_order, container, false);
    }

    @Override
    public void onViewCreated(@NonNull final View view, final Bundle savedInstanceState)
    {
        try
        {
            mContext = getActivity();
            if(get_user_type(mContext).equalsIgnoreCase("broker"))
            {
                selected_customr_code = get_selected_dealer_sap_code(mContext);
            }
            else
            {
                selected_customr_code = get_dealer_id(mContext);
            }
            Constants.selectedList.clear();
            tvNotiCount = (TextView) getActivity().getWindow().getDecorView().getRootView().findViewById(R.id.tvNotiCount);

            rvLedger = (GridView) view.findViewById(R.id.rvLedger);
            pAdapter = new PopProductListAdapter(mContext, nameValuesProductListLocal, tvNotiCount);
            rvLedger.setAdapter(null);
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                new TRANS_GetPopProduct_Asynctask(mContext).execute("");
            }
            else
            {
                Utils_.closeApp(mContext,check_internet_connection);
            }

            final Button btn_checkout = (Button) view.findViewById(R.id.btn_checkout);
            btn_checkout.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                   goto_order();
                }
            });

            final RelativeLayout rlNoti = (RelativeLayout) getActivity().getWindow().getDecorView().getRootView().findViewById(R.id.rlNoti);
            rlNoti.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    goto_order();
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    private void goto_order()
    {
        try
        {
            boolean issueFound = false;
            for (final Map.Entry<String, PopProductModel> entry : Constants.selectedList.entrySet())
            {
                final String data  = entry.getKey();
                // Do things with the list
                final String text = Constants.selectedList.get(data).get_qty()+"";
                if(!text.matches(""))
                {
                    final double val = Double.parseDouble(text);
                    if(val>0 && !(val >= Constants.selectedList.get(data).getMin_order_qty()))
                    {
                        issueFound = true;
                        break;
                    }
                }
            }


            if(issueFound)
            {
                show_msg_Dialog(mContext, "Please follow minimum qty (Pcs)");
            }
            else if (Constants.selectedList.size() > 0)
            {
                int total_free_product = 0;

                for (final Map.Entry<String, PopProductModel> entry : Constants.selectedList.entrySet())
                {
                    final String data  = entry.getKey();
                    // Do things with the list
                    if(!Constants.selectedList.get(data).getPayment_gateway())
                    {
                        total_free_product++;
                    }
                }

                final int total_payment_product = (Constants.selectedList.size() - total_free_product);

//                show_msg_Dialog(mContext, "Total Selected Product " + Constants.selectedList.size() + "\n" +
//                                                "Total Free Product " + total_free_product + "\n" +
//                                                "Total Paid Product " + total_payment_product);

                boolean continue_checkout = false;

                if(Constants.selectedList.size() >1)
                {
                    if(total_payment_product > 0 && total_free_product > 0)
                    {
                        show_msg_Dialog(mContext, "Please select only one type of product");
                    }
                    else
                    {
                        continue_checkout = true;
                    }
                }
                else
                {
                    continue_checkout = true;
                }

                if(continue_checkout)
                {
                    if(Constants.selectedList.size() == total_free_product)
                    {
                        Intent intent = new Intent(mContext, OrderConfirmationActivity.class);
                        intent.putExtra("payment_option", "Free");
                        startActivity(intent);
                    }
                    else if(Constants.selectedList.size() == total_payment_product)
                    {
                        Intent intent = new Intent(mContext, OrderConfirmationActivity.class);
                        intent.putExtra("payment_option", "Paid");
                        startActivity(intent);
                    }
                }
            }
            else
            {
                show_msg_Dialog(mContext, "Please add at least one product");
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public final class TRANS_GetPopProduct_Asynctask extends AsyncTaskCoroutine<String, String>
    {
        private Activity mContext;
        public TRANS_GetPopProduct_Asynctask(final Activity mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute()
        {
            super.onPreExecute();
            Utils.showProgressDialog(mContext, "Updating...");
        }
        @Override
        public String doInBackground(final String... params)
        {
            String POST_result = "";
            nameValuesProductListLocal.clear();
            try
            {
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    try
                    {
                        final String url = pop_product_data_download;
                        print_log_d("PRINT_LEDGER_URL_130", url);

                        ArrayList<NameValuePair> mHttpParamPairs = new ArrayList<>(2);
                        mHttpParamPairs.add(new BasicNameValuePair("customer_code", selected_customr_code));
                        mHttpParamPairs.add(new BasicNameValuePair("user_type", get_user_type(mContext)));

                        POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);

                        print_log_d("PRINT_LEDGER_URL_130", url);
                        print_log_d("PRINT_LEDGER_URL_130", mHttpParamPairs.toString());
                        print_log_d("_130", POST_result);

                        final JSONObject jo = new JSONObject(POST_result);
                        if(jo.has("process_status"))
                        {
                            if(jo.getString("process_status").toLowerCase().matches("yes"))
                            {
                                final String lifting_history_data  = jo.getString("pop_product_date");
                                final JSONArray jsonArray = new JSONArray(lifting_history_data );

                                for (int i = 0; i < jsonArray.length(); i++)
                                {
                                    final JSONObject e = jsonArray.getJSONObject(i);
                                    final PopProductModel lM = new PopProductModel();

                                    lM.setDns_prod_code(e.optString("dns_prod_code"));
                                    lM.setProd_desc(e.optString("prod_desc"));
                                    lM.setProd_image(e.optString("prod_image"));
                                    try
                                    {
                                        final int val = e.optInt("min_order_qty");
                                        if(val>0)
                                            lM.setMin_order_qty(val);
                                    }
                                    catch (Exception ex)
                                    {
                                        ex.printStackTrace();
                                        lM.setMin_order_qty(100);
                                    }

                                    lM.setPrice_per_piece(e.optString("price_per_piece"));
                                    lM.setGST_rate(e.optString("GST_rate"));

                                    if(e.optString("payment_gateway").equalsIgnoreCase("N"))
                                    {
                                        lM.setPayment_gateway(false);
                                    }
                                    else
                                    {
                                        lM.setPayment_gateway(true);
                                    }

                                    nameValuesProductListLocal.add(lM);
                                }

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
        public void onPostExecute(String result)
        {
            super.onPostExecute(result);

            try
            {
                pAdapter = new PopProductListAdapter(mContext, nameValuesProductListLocal, tvNotiCount);
                if(nameValuesProductListLocal.size() > 0)
                    rvLedger.setAdapter(pAdapter);
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

    @Override
    public void onResume() {
        super.onResume();

        final TextView tvHeaderText = (TextView) getActivity().findViewById(R.id.tvHeaderText);
        tvHeaderText.setText("POP PRODUCT");

        pAdapter = new PopProductListAdapter(mContext, nameValuesProductListLocal, tvNotiCount);
        if(nameValuesProductListLocal.size() > 0)
            rvLedger.setAdapter(pAdapter);
    }
}
