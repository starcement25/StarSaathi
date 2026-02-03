package org.forcepower.starcement.adapter;


import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.RelativeLayout;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.AgeingModel;

import java.util.ArrayList;


public final class AgeingAdapter extends BaseAdapter {
    private Activity activity;
    private ArrayList<AgeingModel> all;

    public AgeingAdapter(final Activity activity_, final ArrayList<AgeingModel> all) {
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

    public View getView(final int index, View view, final ViewGroup viewGroup) {
        RelativeLayout layout;
        if (view instanceof RelativeLayout) {
            layout = (RelativeLayout) view;
        } else {
            final LayoutInflater factory = LayoutInflater.from(activity);
            layout = (RelativeLayout) factory.inflate(R.layout.list_item_ageing, viewGroup, false);
        }

        ((TextView) layout.findViewById(R.id.tvAgeingDocNo)).setText(all.get(index).getDocument_no());
        ((TextView) layout.findViewById(R.id.tvAgeingDocDate)).setText(all.get(index).getDocument_date());
        ((TextView) layout.findViewById(R.id.tvAgeingInvAmount)).setText(all.get(index).getInv_amount());

        return layout;
    }
}

