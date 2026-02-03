package org.forcepower.starcement.adapter;


import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.DealerVisitModel;

import java.util.ArrayList;


public final class DealerVisitAdapter extends BaseAdapter {
    private Activity activity;
    private ArrayList<DealerVisitModel> all;

    public DealerVisitAdapter(final Activity activity_, final ArrayList<DealerVisitModel> all) {
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

    public View getView(final int index, View convertView, final ViewGroup viewGroup)
    {
        ViewHolder viewHolder;
        if (convertView == null)
        {
            final LayoutInflater inflater = (LayoutInflater) activity
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(R.layout.list_item_dealer_visit, viewGroup, false);
            viewHolder = new ViewHolder();
            viewHolder.tv_dealer_name = (TextView) convertView.findViewById(R.id.tv_dealer_name);
            viewHolder.tv_disit_date_time = (TextView) convertView.findViewById(R.id.tv_disit_date_time);

            convertView.setTag(viewHolder);
        }
        else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.tv_dealer_name.setText(all.get(index).getEmp_name());
        viewHolder.tv_disit_date_time.setText(all.get(index).getVisit_datetime());

        return convertView;
    }

    public final class ViewHolder
    {
        private TextView tv_dealer_name, tv_disit_date_time;
    }
}

