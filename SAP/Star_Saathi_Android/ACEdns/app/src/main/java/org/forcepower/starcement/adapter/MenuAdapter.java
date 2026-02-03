package org.forcepower.starcement.adapter;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.MenuObj;

import java.util.ArrayList;

public final class MenuAdapter extends ArrayAdapter<MenuObj>
{

	private final Context context;
	private final ArrayList<MenuObj> values;

	private final int resourceId;


	public MenuAdapter(Context context, int resourceId, ArrayList<MenuObj> values) {
		super(context, resourceId, values);
		this.context = context;
		this.values = values;
		this.resourceId = resourceId;
	}


	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		try
		{
			ViewHolder viewHolder;
			if (convertView == null)
			{
				final LayoutInflater inflater = (LayoutInflater) context
						.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
				convertView = inflater.inflate(resourceId, parent, false);

				viewHolder = new ViewHolder();
				viewHolder.gridImage = (ImageView) convertView.findViewById(R.id.menu_child_img);
				viewHolder.menu_child_tv = (TextView) convertView.findViewById(R.id.menu_child_tv);

				convertView.setTag(viewHolder);
			}
			else
			{
				viewHolder = (ViewHolder) convertView.getTag();
			}

			viewHolder.gridImage.setImageResource(values.get(position).getResourceId());
			viewHolder.menu_child_tv.setText(values.get(position).getName());
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		return convertView;
	}

	public final class ViewHolder {
		private ImageView gridImage;
		private TextView menu_child_tv;
	}
}
