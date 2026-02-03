package org.forcepower.starcement.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filter;
import android.widget.Filterable;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.DumpMaster;

import java.util.ArrayList;

public final class DumpAdapter extends ArrayAdapter<DumpMaster> implements Filterable
{

	private final Context context;
	private ArrayList<DumpMaster> nameValues;
	private ArrayList<DumpMaster> finalValues;
	private ItemFilter mFilter = new ItemFilter();
	private ViewHolder viewHolder;
	private final int resourceId;

	public DumpAdapter(Context context, int resourceId, ArrayList<DumpMaster> nameValue) {
		super(context,resourceId,nameValue);
		this.context = context;
		this.nameValues = nameValue;
		this.finalValues= nameValue;
		this.resourceId = resourceId;
	}
	
	public int getCount() {
		return finalValues.size();
	}

	public DumpMaster getItem(int position) {
		return finalValues.get(position);
	}

	public long getItemId(int position) {
		return position;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {		
		if(convertView == null) {
			final LayoutInflater inflater = (LayoutInflater) context
					.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
			convertView = inflater.inflate(resourceId, parent, false);						
			viewHolder = new ViewHolder();
			viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
			convertView.setTag(viewHolder);			
		}
		else{
			viewHolder = (ViewHolder) convertView.getTag();
		}		
		viewHolder.txtView.setText(finalValues.get(position).get_dump_name());
		return convertView;
	}

	public final class ViewHolder {
		TextView txtView;		
	}

	public Filter getFilter() {
		return mFilter;
	}

	private class ItemFilter extends Filter {
		@Override
		protected FilterResults performFiltering(CharSequence constraint) {
			
			String filterString = constraint.toString().toLowerCase();			
			FilterResults results = new FilterResults();
			
			final ArrayList<DumpMaster> destinationlist = nameValues;

			int count = destinationlist.size();
			final ArrayList<DumpMaster> newDestinationlist = new ArrayList<>(count);

			String filterableString ;
			
			for (int i = 0; i < count; i++) {
				filterableString = destinationlist.get(i).get_dump_name();
				if (filterableString.toLowerCase().contains(filterString)) {
					newDestinationlist.add(destinationlist.get(i));
				}
			}			
			results.values = newDestinationlist;
			results.count = newDestinationlist.size();

			return results;
		}

		@SuppressWarnings("unchecked")
		@Override
		protected void publishResults(CharSequence constraint, FilterResults results) {
			finalValues = (ArrayList<DumpMaster>) results.values;
			notifyDataSetChanged();
		}

	}
}