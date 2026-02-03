package org.forcepower.starcement.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;
import androidx.annotation.NonNull;
import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.KeyValue;

import java.util.ArrayList;

public final class MonthAdapter extends ArrayAdapter<KeyValue>
{
	private final Context context;
	private ArrayList<KeyValue> nameValues;


	public MonthAdapter(final Context context, final ArrayList<KeyValue> nameValue) {
		super(context, 0, nameValue);

		this.context = context;
		this.nameValues = nameValue;

	}
	
	public int getCount() {
		return nameValues.size();
	}

	public KeyValue getItem(int position) {
		return nameValues.get(position);
	}

	public long getItemId(int position) {
		return position;
	}

	@NonNull
	@Override
	public View getView(final int position, View convertView, final ViewGroup parent)
	{
		ViewHolder viewHolder;

		if(convertView == null)
		{
			final LayoutInflater inflater = (LayoutInflater) context
					.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
			convertView = inflater.inflate(R.layout.customer_broker_list_child, parent, false);
			viewHolder = new ViewHolder();
			viewHolder.list_details = (TextView) convertView.findViewById(R.id.list_details);
			viewHolder.imageView1 = (ImageView) convertView.findViewById(R.id.imageView1);
			convertView.setTag(viewHolder);
		}
		else
		{
			viewHolder = (ViewHolder) convertView.getTag();
		}

		viewHolder.list_details.setText(nameValues.get(position).getKey());
		viewHolder.imageView1.setImageResource(R.drawable.cal);

		return convertView;
	}
	
	
	public final class ViewHolder {
		private TextView list_details;
		private ImageView imageView1;
	}
}