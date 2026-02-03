package org.forcepower.starcement.fragments.dealer_lifting.assigned.adapter;

import android.app.Activity;
import android.content.Context;
import android.graphics.Color;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.LifitngAssignedInvModel;
import org.forcepower.starcement.bean.LiftingAssignInvModelChild;
import org.forcepower.starcement.fragments.dealer_lifting.assigned.AssignedDealerLiftingFragment;

import java.util.ArrayList;

public class AssignedDealerLiftingItemChild extends BaseExpandableListAdapter {
    private ArrayList<LifitngAssignedInvModel> off_data_list = new ArrayList<>();
    private Activity ctx;
    private AssignedDealerLiftingFragment assignedDealerLiftingFragment;

    public AssignedDealerLiftingItemChild(final Activity activity, final ArrayList<LifitngAssignedInvModel> off_data_list, final AssignedDealerLiftingFragment assignedDealerLiftingFragment) {
        this.off_data_list = off_data_list;
        this.assignedDealerLiftingFragment = assignedDealerLiftingFragment;
        this.ctx = activity;
    }

    @Override
    public int getGroupCount() {
        return off_data_list.size();
    }
    @Override
    public int getChildrenCount(int groupPosition) {
        int count = off_data_list.get(groupPosition).getOrder_challan_data().size();
        Log.d("CHILD_COUNT", "Group: " + groupPosition + " -> children: " + count);
        return count;
    }
    @Override
    public Object getGroup(int groupPosition) {
        return off_data_list.get(groupPosition);
    }
    @Override
    public Object getChild(int groupPosition, int childPosition) {
        return off_data_list.get(groupPosition).getOrder_challan_data().get(childPosition);
    }
    @Override
    public long getGroupId(int groupPosition) {
        return groupPosition;
    }
    @Override
    public long getChildId(int groupPosition, int childPosition) {
        return childPosition;
    }
    @Override
    public boolean hasStableIds() {
        return false;
    }
    @Override
    public View getGroupView(int groupPosition, boolean isExpanded, View v, ViewGroup parent) {
        final GroupViewHolder groupViewHolder;
        try {
            if (v == null) {
                groupViewHolder = new GroupViewHolder();
                final LayoutInflater inflater = (LayoutInflater)ctx.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                v = inflater.inflate(R.layout.list_item_header_lifing_assigned, parent, false);
                groupViewHolder.lblListHeader =  v.findViewById(R.id.lblListHeader);
                groupViewHolder.tvStatus =  v.findViewById(R.id.tvStatus);
                groupViewHolder.tvHeaderQty =  v.findViewById(R.id.tvHeaderQty);
                groupViewHolder.tvProdName =  v.findViewById(R.id.tvProdName);
                groupViewHolder.tv_order_full_date_time =  v.findViewById(R.id.tv_order_full_date_time);
                groupViewHolder.tv_destination_address =  v.findViewById(R.id.tv_destination_address);
                groupViewHolder.tv_freight =  v.findViewById(R.id.tv_freight);
                groupViewHolder.tv_allocation =  v.findViewById(R.id.tv_allocation);
                groupViewHolder.tvRemaining =  v.findViewById(R.id.tvRemaining);
                groupViewHolder.iv_indicator = v.findViewById(R.id.iv_indicator);
                v.setTag(groupViewHolder);
            } else {
                groupViewHolder = (GroupViewHolder) v.getTag();
            }

            final LifitngAssignedInvModel cat = off_data_list.get(groupPosition);
            groupViewHolder.lblListHeader.setText(cat.getOrder_id());
            groupViewHolder.tvStatus.setText(cat.getSTATUS());
            groupViewHolder.tv_order_full_date_time.setText(cat.getOrder_date());
            groupViewHolder.tv_destination_address.setText(cat.getDestination_name());
            groupViewHolder.tv_freight.setText(cat.getFreight());
            groupViewHolder.tvRemaining.setText("Remaining Available Qty = " + cat.getRemainingQty());
            groupViewHolder.tvHeaderQty.setText("Qty : " + cat.getQty());
            groupViewHolder.tvProdName.setText(cat.getProd_display_name());

            groupViewHolder.lblListHeader.setTextColor(cat.getRemainingQty() > 0 ? Color.BLACK : ctx.getResources().getColor(R.color.colorGreen));
            groupViewHolder.tv_allocation.setVisibility(cat.get_showAllocateButton()?View.VISIBLE:View.GONE);
            groupViewHolder.iv_indicator.setImageResource(isExpanded?R.drawable.uu:R.drawable.dd);
            groupViewHolder.iv_indicator.setVisibility(!off_data_list.get(groupPosition).getOrder_challan_data().isEmpty()?View.VISIBLE:View.INVISIBLE);
        } catch (Exception e) {
            e.printStackTrace();
        }
        return v;
    }
    @Override
    public View getChildView(int groupPosition, int childPosition, boolean isLastChild, View convertView, ViewGroup parent) {
        final ChildViewHolder childViewHolder;
        if (convertView == null) {
            childViewHolder = new ChildViewHolder();
            final LayoutInflater infalInflater = (LayoutInflater) this.ctx.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = infalInflater.inflate(R.layout.list_item_lifting_inv_child, parent, false);

            childViewHolder.tvChallanNo =  convertView.findViewById(R.id.tvChallanNo);
            childViewHolder.tvDate = convertView.findViewById(R.id.tvChildDate);
            childViewHolder.tvChildQty =  convertView.findViewById(R.id.tvChildQty);
            childViewHolder.tv_dis_allocation = convertView.findViewById(R.id.tv_dis_allocation);

            convertView.setTag(childViewHolder);
        } else {
            childViewHolder = (ChildViewHolder) convertView.getTag();
        }

        try {
            final LiftingAssignInvModelChild det = off_data_list.get(groupPosition).getOrder_challan_data().get(childPosition);

            childViewHolder.tvChallanNo.setText(det.getInvno());
            childViewHolder.tvDate.setText(det.getInvdt());
            childViewHolder.tvChildQty.setText(det.getInvqty());

            if(det.get_showDisAllocateButton())
            {
                childViewHolder.tv_dis_allocation.setVisibility(View.VISIBLE);
                childViewHolder.tv_dis_allocation.setOnClickListener(v -> {
                    final LifitngAssignedInvModel cat = off_data_list.get(groupPosition);
                    assignedDealerLiftingFragment.showDealerSubDealerList(cat.getOrder_id(), cat.getProd_display_name(), det.getInvdt(), det.getInvno());
                });
            } else {
                childViewHolder.tv_dis_allocation.setVisibility(View.INVISIBLE);
                childViewHolder.tv_dis_allocation.setOnClickListener(null);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return convertView;
    }
    @Override
    public boolean isChildSelectable(int groupPosition, int childPosition) {
        return true;
    }

    private class GroupViewHolder {
        private TextView lblListHeader, tvStatus,tvHeaderQty,tvProdName,tv_order_full_date_time,
                tv_destination_address,tv_freight, tv_allocation, tvRemaining;
        private ImageView iv_indicator;
    }
    private class ChildViewHolder {
        private TextView tvChallanNo, tvChildQty, tvDate, tv_dis_allocation;
    }
}