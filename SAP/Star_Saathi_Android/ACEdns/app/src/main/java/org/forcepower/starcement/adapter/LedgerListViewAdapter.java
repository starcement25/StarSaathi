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
import org.forcepower.starcement.commonDatabaseHelper;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;

/**
 * A custom adapter designed to fetch bookmarks from a cursor. Before Honeycomb we used
 * SimpleCursorAdapter, but it assumes the existence of an _id column, and the bookmark schema was
 * rewritten for HC without one. This caused the app to crash, hence this new class, which is
 * forwards and backwards compatible.
 *
 * @author dswitkin@google.com (Daniel Switkin)
 */
public final class LedgerListViewAdapter extends BaseAdapter
{
    private Activity activity;
    List<commonDatabaseHelper>all;

    public LedgerListViewAdapter(Activity activity_, List<commonDatabaseHelper> all) {
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
            layout = (RelativeLayout) factory.inflate(R.layout.list_item_ledger, viewGroup, false);
        }
        ((TextView) layout.findViewById(R.id.tvVoucherD)).setText(convertDate(all.get(index).getItem1()));
        ((TextView) layout.findViewById(R.id.tvVoucherN)).setText(all.get(index).getItem2());
        ((TextView) layout.findViewById(R.id.tvLedgerQty)).setText(all.get(index).getItem3()); // QTY
        ((TextView) layout.findViewById(R.id.tvAmountDr)).setText(" " + all.get(index).getItem4());
        ((TextView) layout.findViewById(R.id.tvAmountCr)).setText(" " + all.get(index).getItem5());
        ((TextView) layout.findViewById(R.id.tv_narration)).setText(all.get(index).getItem6());
        ((ImageView) layout.findViewById(R.id.iv_narration)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                narratioonDetails(all.get(index).getItem6());
            }
        });


        return layout;
    }
    public void narratioonDetails(String narrationDetails)
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
    public static String convertDate(String date)
    {
        try
        {
            SimpleDateFormat spf=new SimpleDateFormat("MM/dd/yyyy", Locale.getDefault());

           if(date.contains(" "))
           {
               spf=new SimpleDateFormat("MM/dd/yyyy hh:mm:ss aaa");
           }
            Date newDate=spf.parse(date);
            spf= new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault());
            date = spf.format(newDate);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return date;
    }
}

