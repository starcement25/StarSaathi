package org.forcepower.starcement.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;
import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.ProductMasterDetails;
import java.util.ArrayList;

public final class OrderConfirmAdapter extends ArrayAdapter<ProductMasterDetails>
{
	private final Context context;
	private final ArrayList<ProductMasterDetails> nameValues;

	public OrderConfirmAdapter(final Context context, final ArrayList<ProductMasterDetails> nameValues)
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
				convertView = inflater.inflate(R.layout.list_item_order_, parent, false);
				viewHolder = new ViewHolder();

				viewHolder.tv_prod_name = (TextView) convertView.findViewById(R.id.tv_prod_name);
				viewHolder.tv_prod_qty = (TextView) convertView.findViewById(R.id.tv_prod_qty);

				convertView.setTag(viewHolder);
			}
			else
			{
				viewHolder = (ViewHolder) convertView.getTag();
			}


			viewHolder.tv_prod_name.setText(nameValues.get(position).getDesc());
			viewHolder.tv_prod_qty.setText(nameValues.get(position).getQty() + " MT");
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}

		return convertView;
	}
	
	
	public final class ViewHolder {
		private TextView tv_prod_name,tv_prod_qty;
	}

}