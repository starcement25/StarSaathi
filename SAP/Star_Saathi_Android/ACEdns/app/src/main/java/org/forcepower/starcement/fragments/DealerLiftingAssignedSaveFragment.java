package org.forcepower.starcement.fragments;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_server_current_date;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.ApiRes;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.DEFAULT_TIMEOUT;
import static org.forcepower.starcement.constants.Constants.dispatched_order_list_download_v2;
import static org.forcepower.starcement.constants.Constants.save_allocation_details;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.os.Bundle;
import android.text.Editable;
import android.text.InputFilter;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.CustomRangeInputFilter;
import org.forcepower.starcement.bean.AllocateMasterDetails;
import org.forcepower.starcement.bean.LifitngAssignedModel;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Locale;
import java.util.Map;

import cz.msebera.android.httpclient.Header;
import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;


public final class DealerLiftingAssignedSaveFragment extends Fragment
{
    private Activity mContext;
    private AllocateMasterDetails amd;

    private TextView txtViewProductDesc, tv_allo_total, tv_allo_dispatch, tv_allo_4days_dispatch, tv_submit_allocation;
    private EditText etProdQty_new;
    private final Map<String, LifitngAssignedModel> hashMap_qty = new HashMap<>();

    public DealerLiftingAssignedSaveFragment()
    {

    }

    public static DealerLiftingAssignedSaveFragment newInstance(final String sapCode, final String cusCode,
                                                                final String order_id, final String prod_name,
                                                                final String dispatch_date)
    {
        // Required empty public constructor
        final DealerLiftingAssignedSaveFragment fragmentFirst = new DealerLiftingAssignedSaveFragment();
        final Bundle args = new Bundle();
        args.putString("selected_SAP_code", sapCode);
        args.putString("selected_CUS_code", cusCode);
        args.putString("order_id", order_id);
        args.putString("prod_name", prod_name);
        args.putString("dispatch_date", dispatch_date);
        fragmentFirst.setArguments(args);
        return fragmentFirst;
    }
    public String get_Sap_code() {return getArguments().getString("selected_SAP_code");}
    public String get_cus_code() {return getArguments().getString("selected_CUS_code");}
    public String get_order_id() {return getArguments().getString("order_id");}
    public String get_ARG_prod_name() {return getArguments().getString("prod_name");}
    public String get_Dispatch_date() {return getArguments().getString("dispatch_date");}

    @Override
    public View onCreateView(final LayoutInflater inflater, final ViewGroup container, final Bundle savedInstanceState)
    {
        return inflater.inflate(R.layout.fragment_dealer_lifting_product, container, false);
    }

    @Override
    public void onViewCreated(@NonNull final View mDialog, final Bundle savedInstanceState)
    {
        try
        {
            mContext = getActivity();
            final String selected_customr_code = get_Sap_code();
            final String order_id = get_order_id();
            final String cus_code = get_cus_code();
            print_Log_d("fr6y6_o_id ", order_id);
            print_Log_d("fr6y6_SAP_code ", selected_customr_code);
            print_Log_d("fr6y6_CUS_code ", cus_code);

            txtViewProductDesc = (TextView) mDialog.findViewById(R.id.list_details);
            tv_allo_total = (TextView) mDialog.findViewById(R.id.tv_allo_total);
            tv_allo_dispatch = (TextView) mDialog.findViewById(R.id.tv_allo_dispatch);
            tv_allo_4days_dispatch = (TextView) mDialog.findViewById(R.id.tv_allo_4days_dispatch);
            tv_submit_allocation = (TextView) mDialog.findViewById(R.id.tv_submit_allocation);

            etProdQty_new = (EditText) mDialog.findViewById(R.id.etProdQty_new);
            etProdQty_new.setFilters(new InputFilter[]{new CustomRangeInputFilter(0, 9999)});
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void confirm_order()
    {
        try
        {
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                Utils.showProgressDialog(mContext, "");

                final RequestParams jsObject = new RequestParams();
                if(amd.getQty() >0 && !amd.getLifting_order_id().isEmpty())
                {
                    jsObject.add("allocation_data[0][APPORDERNO]", get_order_id());
                    jsObject.add("allocation_data[0][order_id]", amd.getLifting_order_id());
                    jsObject.add("allocation_data[0][challan_no]", amd.getChallan_no());
                    jsObject.add("allocation_data[0][dns_prod_code]", amd.getDnsProdCode());
                    jsObject.add("allocation_data[0][prod_desc]", amd.getDesc());
                    jsObject.add("allocation_data[0][qty]", amd.getQty()+"");
                    jsObject.add("allocation_data[0][despatch_qty]", amd.getDispatch_quantity());
                    jsObject.add("allocation_data[0][dispatch_date]", amd.getDispatch_date());
                    jsObject.add("allocation_data[0][sub_dealer_id]", get_Sap_code());

                    if(get_user_type(mContext).equalsIgnoreCase("broker"))
                    {
                        jsObject.add("allocation_data[0][customer_id]", get_selected_dealer_sap_code(mContext));
                    }
                    else
                    {
                        jsObject.add("allocation_data[0][customer_id]", get_dealer_id(mContext));
                    }

                }
                print_log_d("fr6y6_t ", jsObject + "");



                final AsyncHttpClient client = new AsyncHttpClient();
                client.setTimeout(DEFAULT_TIMEOUT);
//        HttpsAsyncHttpClient(client);

                print_Log_d("fr6y6_Ui ", save_allocation_details);
                print_Log_d("fr6y6_Ri ", "");
                print_Log_d("fr6y6_Pi ", jsObject+"");

                client.post(save_allocation_details, jsObject, new AsyncHttpResponseHandler()
                {
                    @Override
                    public void onSuccess(int statusCode, Header[] headers, byte[] responseBody)
                    {
                        String str = new String(responseBody);
                        try
                        {
                            str = ApiRes(str);
                            print_Log_d("fr6y6_U ", save_allocation_details);
                            print_Log_d("fr6y6_R ", str+"");
                            print_Log_d("fr6y6_P ", jsObject+"");

                            final JSONObject reader = new JSONObject(str);
                            if(reader.has("process_status") &&
                                    reader.getString("process_status").equalsIgnoreCase("yes"))
                            {
                                new TRAN_OldSetLiftingData_Asunctask(mContext, true).execute();
                            }
                            else
                            {
                                show_msg_Dialog(mContext, reader.optString("process_message"));
                            }

                        }
                        catch (Exception e)
                        {
                            e.printStackTrace();
                            print_log_d("fr6y6_190 ",  e.toString());

                            Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                            mContext.finish();
                        }
                        finally
                        {
                            Utils.cancelProgressDialog();
                        }
                    }

                    @Override
                    public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {

                        Utils.cancelProgressDialog();
                        show_msg_alert(mContext, "Please Contact with Admin.", true);
                        print_log_d("fr6y6_192 ",  error.toString());
                    }


                });
            }
            else
            {
                Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void show_msg_this(final Activity context, final String msg, final boolean isFinishing)
    {
        try
        {
            AlertDialog.Builder alertDialog = new AlertDialog.Builder(context, R.style.MyDialog);

            TextView tvCPopup = new TextView(context);
            tvCPopup.setText(context.getResources().getString(R.string.app_name));
            tvCPopup.setGravity(Gravity.CENTER);
            tvCPopup.setTextColor(context.getResources().getColor(R.color.white));
            tvCPopup.setTextSize(14);
            tvCPopup.setBackgroundColor(context.getResources().getColor(R.color.red));
            int margin = 15;
            tvCPopup.setPadding(0, margin*2, 0, margin*2);
            alertDialog.setCustomTitle(tvCPopup);
            alertDialog.setMessage("\n" + msg);

            // On pressing Settings button
            alertDialog.setPositiveButton("Ok", new DialogInterface.OnClickListener() {
                public void onClick(DialogInterface dialog, int which) {
                    dialog.dismiss();
                    // vola2715
                    new TRAN_OldSetLiftingData_Asunctask(mContext, false).execute();
                }
            });

            alertDialog.setCancelable(false);
            // Showing Alert Message
            alertDialog.show();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    @Override
    public void onResume()
    {
        super.onResume();
        try
        {
            new TRAN_OldSetLiftingData_Asunctask(mContext, false).execute();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }


    public final class TRAN_OldSetLiftingData_Asunctask extends AsyncTaskCoroutine<String, String>
    {
        private Activity mContext;
        private JSONObject jo = new JSONObject();
        private ProgressDialog mStepProgressDialog;

        private boolean orderSubmitted = false;
        public TRAN_OldSetLiftingData_Asunctask(final Activity mContext, final boolean orderSubmitted)
        {
            this.mContext = mContext;
            this.orderSubmitted = orderSubmitted;
            hashMap_qty.clear();
            amd = new AllocateMasterDetails();
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
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                try
                {
                    final String url = dispatched_order_list_download_v2;
                    print_log_d("dispatched_order_list_download_v2 ", url);

                    final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);

                    if(get_user_type(mContext).equalsIgnoreCase("broker"))
                    {
                        nameValuePairs.add(new BasicNameValuePair("customer_code", get_selected_dealer_sap_code(mContext)));
                    }
                    else
                    {
                        nameValuePairs.add(new BasicNameValuePair("customer_code", get_dealer_id(mContext)));
                    }
                    POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);

                    print_log_d("nq3ee_v2_U ", dispatched_order_list_download_v2);
                    print_log_d("nq3ee_v2_P ", nameValuePairs.toString());
                    print_log_d("nq3ee_v2_R ", POST_result);

                    jo = new JSONObject(POST_result);
                    final String dispatched_order_data = jo.optString("dispatched_order_data");
                    final JSONArray jsonArray = new JSONArray(dispatched_order_data);
                    for(int i=0; i<jsonArray.length(); i++)
                    {
                        final JSONObject e = jsonArray.getJSONObject(i);
                        final LifitngAssignedModel cDH = new LifitngAssignedModel();

                        cDH.setOrder_id(e.optString("APPORDERNO"));
                        cDH.setQty(e.optString("dispatch_qty"));
                        cDH.set_allocation_qty(e.optString("available_allocation_qty"));
                        cDH.setProd_display_name(e.optString("prod_display_name"));
                        cDH.setOrder_date(e.optString("dispatch_date"));
                        cDH.setDns_prod_code(e.optString("dns_prod_code"));

                        cDH.setFreight(e.optString("challanno")); //vola2715
                        //
                        hashMap_qty.put(e.optString("APPORDERNO") + "_" + e.optString("dispatch_date"), cDH);
                    }
                }
                catch (Exception e)
                {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }
        @Override
        public void onPostExecute(String result)
        {
            super.onPostExecute(result);
            try
            {
                try
                {
                    final String o_id = get_order_id()+"_"+ get_Dispatch_date();
                    LifitngAssignedModel cDH = new LifitngAssignedModel();

                    if(hashMap_qty.containsKey(o_id))
                    {
                        cDH = hashMap_qty.get(o_id);
                    }
                    else
                    {
                        cDH.setQty("0");
                        cDH.set_allocation_qty("0");
                        cDH.setProd_display_name(get_ARG_prod_name());
                    }


                    final float dispatch_qty = Float.parseFloat(cDH.getQty());
                    final float available_qty = Float.parseFloat(cDH.get_allocation_qty());
                    final String allChallan = cDH.getFreight();

                    final String prod_display_name = cDH.getProd_display_name();


                    final String despatchDate = cDH.getOrder_date();

                    txtViewProductDesc.setText(prod_display_name);
                    tv_allo_total.setText("Remaining Allocation Qty " + available_qty);
                    tv_allo_dispatch.setText("Dispatch Qty " + dispatch_qty);
                    tv_allo_4days_dispatch.setText("");
                    etProdQty_new.setText("");

                    amd = new AllocateMasterDetails();
                    amd.setDesc(prod_display_name);
                    amd.setDnsProdCode(cDH.getDns_prod_code());
                    amd.setQty(0);

                    amd.setTotal_quantity("");
                    amd.setDispatch_quantity(dispatch_qty+"");
                    amd.setDispatch_date(despatchDate+"");
                    amd.setLast_4_days_quantity(available_qty);
                    amd.setChallan_no(allChallan);
                    amd.set_root_order_id(get_order_id());
                    amd.set_APPORDERNO(get_order_id());

                    print_Log_d("fr6y6_allChallan ", allChallan);

                    //
                    etProdQty_new.addTextChangedListener(new TextWatcher() {
                        @Override
                        public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                        }

                        @Override
                        public void onTextChanged(CharSequence s, int start, int before, int count) {
                            try
                            {
                                final float temp = Float.parseFloat(etProdQty_new.getText().toString().trim());
                                if(temp > 0)
                                {

                                    if(temp <= available_qty)
                                    {
                                        amd.setQty(temp);
                                        final String global_timeStamp = get_server_current_date(mContext).replace("-", "")+ new SimpleDateFormat("HHmmss", Locale.getDefault()).format(Calendar.getInstance().getTime());
                                        amd.setLifting_order_id("L_"+get_selected_dealer_sap_code(mContext)+ "_" +global_timeStamp+"_"+get_order_id());
                                        amd.set_APPORDERNO(amd.getLifting_order_id());
                                        amd.setLimitExceed(false);
                                        etProdQty_new.setBackgroundResource(R.drawable.gray_border_gray_bg);
                                    }
                                    else
                                    {
                                        amd.setQty(0);
                                        amd.setLimitExceed(true);
                                        amd.setLifting_order_id("");
                                        amd.set_APPORDERNO("");
                                        etProdQty_new.setBackgroundResource(R.drawable.gray_border_red_bg);
                                    }
                                }
                                else
                                {
                                    amd.setQty(0);
                                    amd.setLifting_order_id("");
                                    amd.set_APPORDERNO("");
                                }
                            }
                            catch (Exception e)
                            {
                                e.printStackTrace();
                            }
                        }

                        @Override
                        public void afterTextChanged(Editable s) {
                            try
                            {
                                if(etProdQty_new.getText().toString().trim().isEmpty())
                                {
                                    amd.setQty(0);
                                    amd.setLifting_order_id("");
                                    amd.set_APPORDERNO("");
                                }
                            }
                            catch (Exception e)
                            {
                                e.printStackTrace();
                            }
                        }
                    });

                    tv_submit_allocation.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View view) {

                            if(amd.getQty() == 0)
                            {
                                amd.setLifting_order_id("");
                                amd.set_APPORDERNO("");
                            }

                            boolean maxLimitfound = false;
                            String maxL = "";
                            if(amd.getLimitExceed())
                            {
                                maxLimitfound = true;
                                maxL = amd.getLast_4_days_quantity() + "";
                            }

                            boolean input1Value = false;
                            if(!amd.getLifting_order_id().isEmpty())
                            {
                                input1Value = true;
                            }

                            if(maxLimitfound)
                            {
                                show_msg_alert(mContext, "Max Limit "+ maxL, false);
                            }
                            else if(!input1Value)
                            {
                                show_msg_alert(mContext, "Please allocate the product", false);
                            }
                            else
                            {
                                confirm_order();
                            }

                        }
                    });

                }
                catch (Exception e)
                {
                    e.printStackTrace();
                }

                if(result.equalsIgnoreCase("Network Failure"))
                {
                    show_msg_alert(mContext, check_internet_connection, true);
                }
                else if(jo.optString("process_status").equalsIgnoreCase("NO") && orderSubmitted)
                {
                    show_msg_alert(mContext, jo.optString("process_message"), true);
                }

                else if(jo.optString("process_status").equalsIgnoreCase("YES") && orderSubmitted)
                {
                    show_msg_alert(mContext, jo.optString("process_message"), false);
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
