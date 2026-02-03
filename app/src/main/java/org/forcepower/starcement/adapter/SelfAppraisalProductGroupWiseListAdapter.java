package org.forcepower.starcement.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.SelfAppraisalDetailsProductGroupWise;

import java.util.ArrayList;

/**
 * Created by Suvradip on 16/02/2017.
 */

public final class SelfAppraisalProductGroupWiseListAdapter extends BaseAdapter {
    private ArrayList<SelfAppraisalDetailsProductGroupWise> values;
    private Context mContext;
    private final int resourceId;

    public SelfAppraisalProductGroupWiseListAdapter(final Context context, final int resourceid,
                                                    final ArrayList<SelfAppraisalDetailsProductGroupWise> values)
    {
        this.mContext = context;
        this.values = values;
        this.resourceId=resourceid;

    }
    @Override
    public int getCount()
    {
        return values.size();
    }

    @Override
    public Object getItem(int i)
    {
        return null;
    }

    @Override
    public long getItemId(int i)
    {
        return i;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent)
    {
        try
        {
            ViewHolder viewHolder;
            if (convertView == null)
            {
                final LayoutInflater inflater = (LayoutInflater) mContext
                        .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                convertView = inflater.inflate(resourceId, parent, false);
                viewHolder = new ViewHolder();
                viewHolder.tvCustomerName = (TextView) convertView.findViewById(R.id.customerBranchName);
                viewHolder.tvTarget = (TextView) convertView.findViewById(R.id.tvtarget);
                viewHolder.tvAchievement = (TextView) convertView.findViewById(R.id.tvachievement);
                convertView.setTag(viewHolder);
            }
            else
            {
                viewHolder = (ViewHolder) convertView.getTag();
            }

            viewHolder.tvCustomerName.setText(values.get(position).getmonth());
            viewHolder.tvTarget.setText(values.get(position).gettarget());
            viewHolder.tvAchievement.setText(values.get(position).getachievement());
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return convertView;
    }
    public final class ViewHolder
    {
        private TextView tvCustomerName,tvTarget,tvAchievement;
    }
}
