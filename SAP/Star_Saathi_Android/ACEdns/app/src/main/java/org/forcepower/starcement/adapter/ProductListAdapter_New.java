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
import org.forcepower.starcement.bean.ProductMasterDetails;

import java.util.ArrayList;

public final class ProductListAdapter_New extends ArrayAdapter<ProductMasterDetails> implements Filterable
{

	private final Context context;
	private ArrayList<ProductMasterDetails> nameValues;
	private ArrayList<ProductMasterDetails> finalValues;
	private ItemFilter mFilter = new ItemFilter();
	private ViewHolder viewHolder;
	private final int resourceId;

	public ProductListAdapter_New(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValue) {
		super(context,resourceId,nameValue);
		this.context = context;
		this.nameValues = nameValue;
		this.finalValues= nameValue;
		this.resourceId = resourceId;
	}
	
	public int getCount() {
		return finalValues.size();
	}

	public ProductMasterDetails getItem(int position) {
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
		viewHolder.txtView.setText(finalValues.get(position).getDesc());
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
			
			final ArrayList<ProductMasterDetails> destinationlist = nameValues;

			int count = destinationlist.size();
			final ArrayList<ProductMasterDetails> newDestinationlist = new ArrayList<ProductMasterDetails>(count);

			String filterableString ;
			
			for (int i = 0; i < count; i++) {
				filterableString = destinationlist.get(i).getDesc();
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
			finalValues = (ArrayList<ProductMasterDetails>) results.values;
			notifyDataSetChanged();
		}

	}

	
}