package org.forcepower.starcement.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.SchemeModel;
// import org.forcepower.starcement.bean.MenuObj; // ⛔ OLD – not needed now

import java.util.ArrayList;

public final class SchemeListAdapter extends ArrayAdapter<SchemeModel> {

	private final Context context;
	private final ArrayList<SchemeModel> values;
	private ViewHolder viewHolder;

	// 🔁 Constructor now takes SchemeModel list instead of MenuObj
	public SchemeListAdapter(Context context, ArrayList<SchemeModel> values) {
		super(context, 0, values);
		this.context = context;
		this.values = values;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {

		if (convertView == null) {
			final LayoutInflater inflater = (LayoutInflater) context
					.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
			convertView = inflater.inflate(R.layout.list_item_scheme_list, parent, false);

			viewHolder = new ViewHolder();
			viewHolder.gridImage = convertView.findViewById(R.id.menu_child_img);
			viewHolder.tvSchemeName = convertView.findViewById(R.id.tvSchemeName);
//			viewHolder.tvSchemeCategory = convertView.findViewById(R.id.tvSchemeCategory);
//			viewHolder.tvSchemeDates = convertView.findViewById(R.id.tvSchemeDates);
//			viewHolder.tvDaysRemaining = convertView.findViewById(R.id.tvDaysRemaining);

			convertView.setTag(viewHolder);
		} else {
			viewHolder = (ViewHolder) convertView.getTag();
		}

		SchemeModel item = values.get(position);

		// ❌ OLD:
		// viewHolder.gridImage.setImageResource(values.get(position).getResourceId());
		// viewHolder.tvSchemeName.setText("SCHEME " + (position+1));

		// ✅ NEW:
		// if you have a static icon, set it here, otherwise keep commented
		 viewHolder.gridImage.setImageResource(R.drawable.scheme_only);

		viewHolder.tvSchemeName.setText(item.getSchemeName());

		String category = item.getCategory() != null ? item.getCategory() : "";
//		viewHolder.tvSchemeCategory.setText(category.isEmpty()
//				? ""
//				: "Category: " + category);

		// e.g. "02-12-2025 to 05-12-2025"
		String dates = "";
		if (item.getStartDate() != null && item.getEndDate() != null) {
			dates = item.getStartDate() + " to " + item.getEndDate();
		}
//		viewHolder.tvSchemeDates.setText(dates);

		// e.g. "2 days left"
//		int daysRemaining = item.getDaysRemaining();
//		if (daysRemaining > 0) {
//			viewHolder.tvDaysRemaining.setText(daysRemaining + " days left");
//		} else if (daysRemaining == 0) {
//			viewHolder.tvDaysRemaining.setText("Last day");
//		} else {
//			viewHolder.tvDaysRemaining.setText("");
//		}

		return convertView;
	}

	public final static class ViewHolder {
		ImageView gridImage;
		TextView tvSchemeName;
		// ⛔ OLD:
		// TextView featureName;
		TextView tvSchemeCategory;
		TextView tvSchemeDates;
		TextView tvDaysRemaining;
	}
}
