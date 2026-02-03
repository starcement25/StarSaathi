package org.forcepower.starcement.aaa;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.DEFAULT_TIMEOUT;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.survey_form;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.twoDigitRoundOff;
import static org.forcepower.starcement.util.Utils.twoDigitRoundOff_;

import android.view.View;
import android.view.View.OnClickListener;

import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.PopAdapter;
import org.forcepower.starcement.bean.PopProductModel;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import java.util.ArrayList;

import java.util.Map;


public final class OrderConfirmationActivity extends AceDnsParentActivity
{
    ListView productListView;
    PopAdapter adapter;
    Context mContext;
    TextView txtTotal;
    double tempTotal = 0;
    ProgressBar progressBar;
    ArrayList <PopProductModel> masterDetails = new ArrayList<>();
    private String payment_option = "";

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_confirm);
        try
        {
            mContext = this;
            if(getIntent().hasExtra("payment_option"))
            {
                payment_option = getIntent().getStringExtra("payment_option");
            }
            progressBar = (ProgressBar) findViewById(R.id.progressBar);
            txtTotal = (TextView) findViewById(R.id.txt_total);

            productListView = (ListView) findViewById(R.id.list_prod);

            tempTotal = 0;

            for (final Map.Entry<String, PopProductModel> entry : Constants.selectedList.entrySet()) {
                final String data  = entry.getKey();
                // Do things with the list

                final double total = (Double.parseDouble(Constants.selectedList.get(data).get_qty())*Double.parseDouble(Constants.selectedList.get(data).getPrice_per_piece()));

                final double gst = (
                        Double.parseDouble(Constants.selectedList.get(data).get_qty())
                                *Double.parseDouble(Constants.selectedList.get(data).getPrice_per_piece())
                                *(Double.parseDouble(Constants.selectedList.get(data).getGST_rate())/100));

                Constants.selectedList.get(data).set_temp_gst(twoDigitRoundOff_(gst));
                Constants.selectedList.get(data).set_temp_total(twoDigitRoundOff_(total));
                Constants.selectedList.get(data).set_sub_total(twoDigitRoundOff_(total+gst));

                masterDetails.add(Constants.selectedList.get(data));

                tempTotal = tempTotal + Double.parseDouble(Constants.selectedList.get(data).get_temp_sub_total());
            }

            adapter = new PopAdapter(mContext, masterDetails);

            productListView.setAdapter(adapter);


            final String data = String.format("%.2f", tempTotal);
            txtTotal.setText("Rs. " + data);

            ImageView ivHeaderBack = (ImageView) findViewById(R.id.back);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
            txtTotal.setText("Rs. " + tempTotal);
        }
    }

    @Override
    public void onBackPressed()
    {
            finish();
    }


    public void submit_order(View view)
    {
        view.setEnabled(false);
        progressBar.setVisibility(View.VISIBLE);
        print_log_d("amitabha2715_104 ", "CONFIRM");
        try
        {
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                if(payment_option.equalsIgnoreCase("Paid"))
                {
                    Intent intent = new Intent(mContext, PayAmountPopActivity.class);
                    intent.putExtra("initialBalance", twoDigitRoundOff_(tempTotal)+"");
                    intent.putExtra("coming_from", "pop_order");
                    intent.putExtra("payment_option", payment_option);
                    startActivity(intent);
                }
                else
                {
                    Intent intent = new Intent(mContext, PopOrderFeedBackActivity.class);
                    intent.putExtra("payment_environment", "payment pending");
                    intent.putExtra("coming_from", "pop_order");
                    intent.putExtra("payment_option", payment_option);
                    startActivity(intent);
                }
            }
            else
            {
                Utils.showToast(mContext, check_internet_connection);
                view.setEnabled(true);

            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        finally
        {
            progressBar.setVisibility(View.GONE);
        }
    }
}
