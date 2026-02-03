package org.forcepower.starcement.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;
import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.PopProductModel;
import java.util.ArrayList;

public final class PopAdapter extends ArrayAdapter<PopProductModel>
{
    private final Context context;
    private final ArrayList<PopProductModel> nameValues;

    public PopAdapter(final Context context, final ArrayList<PopProductModel> nameValues)
    {
        super(context,0,nameValues);
        this.context = context;
        this.nameValues = nameValues;

    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent)
    {
        try
        {
            ViewHolder viewHolder;
            if (convertView == null)
            {
                final LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                convertView = inflater.inflate(R.layout.list_item_order_confirm, parent, false);
                viewHolder = new ViewHolder();

                viewHolder.tv_prod_name = (TextView) convertView.findViewById(R.id.tv_prod_name);
                viewHolder.tv_prod_qty = (TextView) convertView.findViewById(R.id.tv_prod_qty);
                viewHolder.tv_prod_mrp = (TextView) convertView.findViewById(R.id.tv_prod_mrp);
                viewHolder.tv_prod_gst = (TextView) convertView.findViewById(R.id.tv_prod_gst);
                viewHolder.tv_prod_total = (TextView) convertView.findViewById(R.id.tv_prod_total);
                viewHolder.tv_prod_t_GST = (TextView) convertView.findViewById(R.id.tv_prod_t_GST);
                viewHolder.tv_prod_t_total = (TextView) convertView.findViewById(R.id.tv_prod_t_total);

                convertView.setTag(viewHolder);
            }
            else
            {
                viewHolder = (ViewHolder) convertView.getTag();
            }


            viewHolder.tv_prod_name.setText(nameValues.get(position).getProd_desc());
            viewHolder.tv_prod_qty.setText(nameValues.get(position).get_qty() + " (Pcs)");
            viewHolder.tv_prod_mrp.setText(nameValues.get(position).getPrice_per_piece());
            viewHolder.tv_prod_gst.setText(nameValues.get(position).getGST_rate() + "%");
            viewHolder.tv_prod_t_GST.setText(nameValues.get(position).get_temp_gst() + "");
            viewHolder.tv_prod_t_total.setText(nameValues.get(position).get_temp_total()+"");
            viewHolder.tv_prod_total.setText(nameValues.get(position).get_temp_sub_total()+"");
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

        return convertView;
    }


    public final class ViewHolder {
        private TextView tv_prod_name,tv_prod_qty, tv_prod_mrp, tv_prod_gst, tv_prod_total,
                tv_prod_t_GST, tv_prod_t_total;
    }

}