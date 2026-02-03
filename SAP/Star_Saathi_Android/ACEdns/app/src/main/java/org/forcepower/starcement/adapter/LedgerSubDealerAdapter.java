package org.forcepower.starcement.adapter;


import android.app.Activity;
import android.app.Dialog;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.LedgerModel;
import java.util.ArrayList;



public final class LedgerSubDealerAdapter extends BaseAdapter
{
    private Activity activity;
    private ArrayList<LedgerModel> all;

    public LedgerSubDealerAdapter(final Activity activity_, final ArrayList<LedgerModel> all) {
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


    public View getView(final int index, View view, final ViewGroup viewGroup)
    {
        RelativeLayout layout;
        if (view instanceof RelativeLayout) {
            layout = (RelativeLayout) view;
        } else {
            final LayoutInflater factory = LayoutInflater.from(activity);
            layout = (RelativeLayout) factory.inflate(R.layout.list_item_ledger_sd, viewGroup, false);
        }
        ((TextView) layout.findViewById(R.id.tvVoucherD)).setText(all.get(index).getVoucher_date());
        ((TextView) layout.findViewById(R.id.tvVoucherN)).setText(all.get(index).getVoucher_no());
        ((TextView) layout.findViewById(R.id.tvAmount)).setText( all.get(index).getAmount());
        ((TextView) layout.findViewById(R.id.tvLedgerEntryDate)).setText(all.get(index).getEntry_date());
        ((TextView) layout.findViewById(R.id.tv_narration)).setText(all.get(index).getNarration());
        ((ImageView) layout.findViewById(R.id.iv_narration)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                narratioonDetails(all.get(index).getNarration());
            }
        });


        return layout;
    }
    public void narratioonDetails(final String narrationDetails)
    {
        try
        {
            final Dialog stkDialog = new Dialog(activity, R.style.PauseDialog);
            stkDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            stkDialog.setContentView(R.layout.ledger_narration_details);
            stkDialog.setCancelable(true);

            ImageView ivHeaderBack = (ImageView) stkDialog.findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    stkDialog.dismiss();
                }
            });

            TextView tvNarrationDetails = (TextView) stkDialog.findViewById(R.id.tvNarrationDetails);
            tvNarrationDetails.setText(narrationDetails+"");


            stkDialog.show();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
}

