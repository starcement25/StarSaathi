package org.forcepower.starcement.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.activity.declaration_of_dealer.dataset.MonthDataSet;
import org.forcepower.starcement.bean.ProductMasterDetails;

import java.util.ArrayList;

public final class MonthListAdapter extends ArrayAdapter<MonthDataSet> implements Filterable {
    private final Context context;
    private ArrayList<MonthDataSet> nameValues;
    private final int resourceId;

    public MonthListAdapter(final Context context, final int resourceId, final ArrayList<MonthDataSet> nameValue) {
        super(context, resourceId, nameValue);

        this.context = context;
        this.nameValues = nameValue;
        this.resourceId = resourceId;
    }

    public int getCount() {
        return nameValues.size();
    }

    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(final int position, View convertView, final ViewGroup parent)
    {
        ViewHolder viewHolder;

        if(convertView == null)
        {
            final LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            convertView.setTag(viewHolder);
        }
        else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.txtView.setText(nameValues.get(position).getMonthTitle());

        return convertView;
    }

    public final class ViewHolder {
        private TextView txtView;
    }
}
