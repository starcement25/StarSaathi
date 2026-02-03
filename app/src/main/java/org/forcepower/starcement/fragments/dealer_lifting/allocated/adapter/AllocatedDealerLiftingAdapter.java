package org.forcepower.starcement.fragments.dealer_lifting.allocated.adapter;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.AllocationListModel;
import org.forcepower.starcement.fragments.dealer_lifting.allocated.dataset.LifitngAllocatedInvModelGroup;

import java.util.ArrayList;

public class AllocatedDealerLiftingAdapter extends BaseExpandableListAdapter {
    private ArrayList<LifitngAllocatedInvModelGroup> off_data_list;
    private final Activity activity;

    public AllocatedDealerLiftingAdapter(final Activity activity, final ArrayList<LifitngAllocatedInvModelGroup> off_data_list) {
        this.off_data_list = off_data_list;
        this.activity = activity;
    }
    @Override
    public Object getChild(final int groupPosition, final int childPosition) {
        return off_data_list.get(groupPosition).getOrder_challan_data().get(childPosition);
    }
    @Override
    public long getChildId(final int groupPosition, final int childPosition) {
        return off_data_list.get(groupPosition).getOrder_challan_data().get(childPosition).hashCode();
    }
    @Override
    public int getChildrenCount(final int groupPosition) {
        return off_data_list.get(groupPosition).getOrder_challan_data().size();
    }
    @Override
    public Object getGroup(final int groupPosition) {
        return off_data_list.get(groupPosition);
    }
    @Override
    public int getGroupCount() {
        return off_data_list.size();
    }
    @Override
    public long getGroupId(final int groupPosition) {
        return off_data_list.get(groupPosition).hashCode();
    }
    @Override
    public boolean isChildSelectable(final int groupPosition, final int childPosition) {
        return true;
    }
    @Override
    public boolean hasStableIds() {
        return true;
    }
    @SuppressLint("SetTextI18n")
    @Override
    public View getChildView(final int groupPosition, final int childPosition, boolean isLastChild, View convertView, final ViewGroup parent) {
        final ChildViewHolder childViewHolder;
        if (convertView == null) {
            childViewHolder = new ChildViewHolder();
            final LayoutInflater layoutInflater = (LayoutInflater) this.activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = layoutInflater.inflate(R.layout.list_item_allocated, parent, false);
            childViewHolder.tv_allo_prod_desc =  convertView.findViewById(R.id.tv_allo_prod_desc);
            childViewHolder.tv_allocation_qty =  convertView.findViewById(R.id.tv_allocation_qty);
            childViewHolder.tv_allo_date_and_time =  convertView.findViewById(R.id.tv_allo_date_and_time);
            childViewHolder.tv_allo_counter_nm =  convertView.findViewById(R.id.tv_allo_counter_nm);
            childViewHolder.tv_allo_challan_no =  convertView.findViewById(R.id.tv_allo_challan_no);
            childViewHolder.tv_allo_challan_date =  convertView.findViewById(R.id.tv_allo_challan_date);
            childViewHolder.tv_isDelete =  convertView.findViewById(R.id.tv_isDelete);
            convertView.setTag(childViewHolder);
        } else {
            childViewHolder = (ChildViewHolder) convertView.getTag();
        }

        try {
            final AllocationListModel det = off_data_list.get(groupPosition).getOrder_challan_data().get(childPosition);
            childViewHolder.tv_allo_prod_desc.setText("Product : " + det.getProd_desc());
            childViewHolder.tv_allocation_qty.setText("Allocated Qty : " + det.getAllocation_qty());
            childViewHolder.tv_allo_date_and_time.setText("Trans Date : " + det.getDate_and_time());
            childViewHolder.tv_allo_counter_nm.setText("Counter Name : " + det.getCounter_name());
            childViewHolder.tv_allo_challan_no.setText("Invoice No : " + det.getChallan_no());
            childViewHolder.tv_allo_challan_date.setText("Invoice Date : " + det.getChallan_date());
            childViewHolder.tv_isDelete.setVisibility(det.getIs_deleted()==1?View.VISIBLE:View.GONE);
        } catch (Exception e) {
            Log.d("TAG", "getChildView: "+e.getMessage());
        }

        return convertView;
    }

    @Override
    public View getGroupView(final int groupPosition, final boolean isExpanded, View v, final ViewGroup parent) {
        final GroupViewHolder groupViewHolder;
        try {
            if (v == null) {
                groupViewHolder = new GroupViewHolder();
                final LayoutInflater inflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                v = inflater.inflate(R.layout.allocated_dealer_name_list_item, parent, false);
                groupViewHolder.lblListHeader =  v.findViewById(R.id.lblListHeader);
                groupViewHolder.iv_indicator =  v.findViewById(R.id.iv_indicator);
                v.setTag(groupViewHolder);
            } else {
                groupViewHolder = (GroupViewHolder) v.getTag();
            }

            final LifitngAllocatedInvModelGroup cat = off_data_list.get(groupPosition);
            groupViewHolder.lblListHeader.setText(cat.getName());
            groupViewHolder.iv_indicator.setImageResource(isExpanded?R.drawable.uu:R.drawable.dd);
        } catch (Exception e) {
            Log.d("TAG", "getChildView: "+e.getMessage());
        }
        return v;
    }


    private static class GroupViewHolder {
        private TextView lblListHeader;
        private ImageView iv_indicator;

    }

    private static class ChildViewHolder {
        private TextView tv_allo_prod_desc, tv_allocation_qty, tv_allo_date_and_time,
                tv_allo_counter_nm, tv_allo_challan_no, tv_allo_challan_date,tv_isDelete;

    }
    public void setFilter(final ArrayList<LifitngAllocatedInvModelGroup> countryModels) {
        off_data_list = new ArrayList<>();
        off_data_list.addAll(countryModels);
        notifyDataSetChanged();
    }
}