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

public final class SchemeListAdapter extends ArrayAdapter<MenuObj>
{

	private final Context context;
	private final ArrayList<MenuObj> values;
	private ViewHolder viewHolder;

	public SchemeListAdapter(Context context, ArrayList<MenuObj> values) {
		super(context, 0, values);
		this.context = context;
		this.values = values;
	}


	@Override
	public View getView(int position, View convertView, ViewGroup parent)
	{

		if (convertView == null)
		{
			final LayoutInflater inflater = (LayoutInflater) context
					.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
			convertView = inflater.inflate(R.layout.list_item_scheme_list, parent, false);

			viewHolder = new ViewHolder();
			viewHolder.gridImage = (ImageView) convertView.findViewById(R.id.menu_child_img);
			viewHolder.tvSchemeName = (TextView) convertView.findViewById(R.id.tvSchemeName);

			convertView.setTag(viewHolder);
		}
		else
		{
			viewHolder = (ViewHolder) convertView.getTag();
		}
		
		viewHolder.gridImage.setImageResource(values.get(position).getResourceId());
		viewHolder.tvSchemeName.setText("SCHEME " + (position+1));
		return convertView;
	}

	public final class ViewHolder
	{
		ImageView gridImage;
		TextView featureName, tvSchemeName;
	}
}
