package org.forcepower.starcement.fragments.dealer_lifting.assigned.adapter;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.ExpandableListView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListAdapter;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.LifitngAssignedInvModel;
import org.forcepower.starcement.bean.LifitngAssignedInvModelGroup;
import org.forcepower.starcement.fragments.dealer_lifting.assigned.AssignedDealerLiftingFragment;

import java.util.ArrayList;

public class AssignedDealerLiftingItem extends BaseExpandableListAdapter {
    private ArrayList<LifitngAssignedInvModelGroup> off_data_list = new ArrayList<>();
    private Activity ctx;
    private AssignedDealerLiftingFragment assignedDealerLiftingFragment;

    public AssignedDealerLiftingItem(final Activity activity, final ArrayList<LifitngAssignedInvModelGroup> off_data_list, final AssignedDealerLiftingFragment assignedDealerLiftingFragment) {
        this.off_data_list = off_data_list;
        this.ctx = activity;
        this.assignedDealerLiftingFragment = assignedDealerLiftingFragment;
    }
    @Override
    public int getGroupCount() {
        return off_data_list.size();
    }
    @Override
    public int getChildrenCount(final int groupPosition) {
        return 1;
    }
    @Override
    public Object getGroup(final int groupPosition) {
        return off_data_list.get(groupPosition);
    }
    @Override
    public Object getChild(final int groupPosition, final int childPosition) {
        return off_data_list.get(groupPosition).getOrder_challan_data().get(childPosition);
    }
    @Override
    public long getGroupId(final int groupPosition) {
        return groupPosition;
    }
    @Override
    public long getChildId(final int groupPosition, final int childPosition) {
        return childPosition;
    }
    @Override
    public boolean hasStableIds() {
        return false;
    }
    @Override
    public View getGroupView(final int groupPosition, final boolean isExpanded, View v, final ViewGroup parent) {
        final GroupViewHolder groupViewHolder;
        try {
            if (v == null) {
                groupViewHolder = new GroupViewHolder();
                final LayoutInflater inflater = (LayoutInflater)ctx.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                v = inflater.inflate(R.layout.dealer_name_list_item, parent, false);
                groupViewHolder.lblListHeader =  v.findViewById(R.id.lblListHeader);
                groupViewHolder.iv_indicator =  v.findViewById(R.id.iv_indicator);
                groupViewHolder.exlvTrackOrders =  v.findViewById(R.id.exlvTrackOrders);
                v.setTag(groupViewHolder);
            } else {
                groupViewHolder = (GroupViewHolder) v.getTag();
            }

            final LifitngAssignedInvModelGroup cat = off_data_list.get(groupPosition);
            groupViewHolder.lblListHeader.setText(cat.getName());
            groupViewHolder.iv_indicator.setImageResource(isExpanded ? R.drawable.uu : R.drawable.dd);
        } catch (Exception e){
            e.printStackTrace();
        }
        return v;
    }
    @Override
    public View getChildView(final int groupPosition, final int childPosition, boolean isLastChild, View convertView, final ViewGroup parent) {
        final GroupViewHolder childViewHolder;
        if (convertView == null) {
            childViewHolder = new GroupViewHolder();
            final LayoutInflater infalInflater = (LayoutInflater) this.ctx.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = infalInflater.inflate(R.layout.dealer_name_child_item, parent, false);
            childViewHolder.exlvTrackOrders =  convertView.findViewById(R.id.exlvTrackOrders);
//            childViewHolder.layout =  convertView.findViewById(R.id.layout);

//            childViewHolder.layout.setVisibility(View.GONE);
            convertView.setTag(childViewHolder);
        } else {
            childViewHolder = (GroupViewHolder) convertView.getTag();
        }

//        childViewHolder.exlvTrackOrders.setAdapter((ListAdapter) null);
        ArrayList<LifitngAssignedInvModel> secondLevelList = off_data_list.get(groupPosition).getOrder_challan_data();
        AssignedDealerLiftingItemChild childAdapter1 = new AssignedDealerLiftingItemChild(ctx, secondLevelList,assignedDealerLiftingFragment);
        childViewHolder.exlvTrackOrders.setAdapter(childAdapter1);

//        for (int i = 0; i < childAdapter1.getGroupCount(); i++) {
//            childViewHolder.exlvTrackOrders.expandGroup(i);
//        }

        setListViewHeightBasedOnChildren(childViewHolder.exlvTrackOrders);

        return convertView;
    }
    @Override
    public boolean isChildSelectable(final int groupPosition, final int childPosition) {
        return true;
    }

    private class GroupViewHolder {
        private TextView lblListHeader;
        private ImageView iv_indicator;
        private ExpandableListView exlvTrackOrders;
        private LinearLayout layout;
    }
    public void setFilter(final ArrayList<LifitngAssignedInvModelGroup> countryModels) {
        off_data_list = new ArrayList<>();
        off_data_list.addAll(countryModels);
        notifyDataSetChanged();
    }

    public static void setListViewHeightBasedOnChildren(ExpandableListView listView) {
        ListAdapter listAdapter = listView.getAdapter();
        if (listAdapter == null) return;

        int totalHeight = 0;
        for (int i = 0; i < listAdapter.getCount(); i++) {
            View listItem = listAdapter.getView(i, null, listView);
            listItem.measure(
                    View.MeasureSpec.makeMeasureSpec(listView.getWidth(), View.MeasureSpec.UNSPECIFIED),
                    View.MeasureSpec.makeMeasureSpec(0, View.MeasureSpec.UNSPECIFIED)
            );
            totalHeight += listItem.getMeasuredHeight();
        }

        ViewGroup.LayoutParams params = listView.getLayoutParams();
        params.height = totalHeight + (listView.getDividerHeight() * (listAdapter.getCount() - 1));
        listView.setLayoutParams(params);
        listView.requestLayout();
    }
}