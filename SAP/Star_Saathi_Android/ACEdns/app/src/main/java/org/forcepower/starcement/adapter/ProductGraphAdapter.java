package org.forcepower.starcement.adapter;

import android.content.Context;
import android.graphics.Color;
import android.graphics.Typeface;
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

public final class ProductGraphAdapter extends BaseAdapter
{
    private ArrayList<SelfAppraisalDetailsProductGroupWise> values;
    private Context mContext;

    public ProductGraphAdapter(final Context context,
                               final ArrayList<SelfAppraisalDetailsProductGroupWise> values)
    {
        this.mContext = context;
        this.values = values;
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
        return 0;
    }

    @Override
    public View getView(final int pos, View convertView, final ViewGroup parent)
    {
        try
        {
            ViewHolder viewHolder;
            if (convertView == null)
            {
                final LayoutInflater inflater = (LayoutInflater) mContext
                        .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                convertView = inflater.inflate(R.layout.self_list_item_prod_graph, parent, false);
                viewHolder = new ViewHolder();
                viewHolder.tvCustomerName = (TextView) convertView.findViewById(R.id.customerBranchName);
                viewHolder.tvTarget = (TextView) convertView.findViewById(R.id.tvtarget);
                viewHolder.tvAchievement = (TextView) convertView.findViewById(R.id.tvachievement);
                viewHolder.tv_item_type = (TextView) convertView.findViewById(R.id.tv_item_type);
                convertView.setTag(viewHolder);
            }
            else
            {
                viewHolder = (ViewHolder) convertView.getTag();
            }

            final SelfAppraisalDetailsProductGroupWise sap = values.get(pos);
            viewHolder.tvCustomerName.setText(sap.getmonth());
            viewHolder.tvTarget.setText(sap.gettarget());
            viewHolder.tvAchievement.setText(sap.getachievement());
            viewHolder.tv_item_type.setText(sap.get_item_type());

            //for Product wise
            if(sap.getmonth().toLowerCase().contains("total qty"))
            {
                viewHolder.tvCustomerName.setTypeface(viewHolder.tvCustomerName.getTypeface(), Typeface.BOLD);
                viewHolder.tvCustomerName.setAllCaps(true);
                viewHolder.tvTarget.setTypeface( viewHolder.tvTarget.getTypeface(), Typeface.BOLD);
                viewHolder.tvAchievement.setTypeface(viewHolder.tvAchievement.getTypeface(), Typeface.BOLD);
                viewHolder.tv_item_type.setTypeface(viewHolder.tv_item_type.getTypeface(), Typeface.BOLD);

                convertView.setBackgroundResource(R.color.colorRed_StatusBar);
                viewHolder.tvCustomerName.setTextColor(Color.WHITE);
                viewHolder.tvCustomerName.setTextColor(Color.WHITE);
                viewHolder.tvTarget.setTextColor(Color.WHITE);
                viewHolder.tvAchievement.setTextColor(Color.WHITE);
                viewHolder.tv_item_type.setTextColor(Color.WHITE);
            }
            else
            {
                viewHolder.tvCustomerName.setTypeface(viewHolder.tvCustomerName.getTypeface(), Typeface.NORMAL);
                viewHolder.tvTarget.setTypeface( viewHolder.tvTarget.getTypeface(), Typeface.NORMAL);
                viewHolder.tvAchievement.setTypeface(viewHolder.tvAchievement.getTypeface(), Typeface.NORMAL);
                viewHolder.tv_item_type.setTypeface(viewHolder.tv_item_type.getTypeface(), Typeface.NORMAL);

                convertView.setBackgroundResource(R.color.white);
                viewHolder.tvCustomerName.setTextColor(Color.BLACK);
                viewHolder.tvCustomerName.setTextColor(Color.BLACK);
                viewHolder.tvTarget.setTextColor(Color.BLACK);
                viewHolder.tvAchievement.setTextColor(Color.BLACK);
                viewHolder.tv_item_type.setTextColor(Color.BLACK);
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return convertView;
    }
    public final class ViewHolder
    {
        private TextView tvCustomerName,tvTarget,tvAchievement, tv_item_type;
    }
}
