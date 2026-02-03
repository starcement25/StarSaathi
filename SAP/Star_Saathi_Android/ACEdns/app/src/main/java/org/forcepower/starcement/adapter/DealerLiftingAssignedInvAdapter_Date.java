package org.forcepower.starcement.adapter;

import static org.forcepower.starcement.util.Utils.print_log_d;

import android.app.Activity;
import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.LifitngAssignedInvModel;
import org.forcepower.starcement.bean.LiftingAssignInvModelChild;
import org.forcepower.starcement.fragments.DealerLiftingAssignedInvFragment;

import java.util.ArrayList;

public final class DealerLiftingAssignedInvAdapter_Date extends BaseExpandableListAdapter
{
    private ArrayList<LifitngAssignedInvModel>off_data_list = new ArrayList<>();
    private Activity ctx;
    private DealerLiftingAssignedInvFragment dealerLiftingAssignedFragment;

    public DealerLiftingAssignedInvAdapter_Date(final Activity activity,
                                                final ArrayList<LifitngAssignedInvModel> off_data_list,
                                                final DealerLiftingAssignedInvFragment dealerLiftingAssignedFragment)
    {
        this.off_data_list = off_data_list;
        this.ctx = activity;
        this.dealerLiftingAssignedFragment = dealerLiftingAssignedFragment;
    }

    @Override
    public Object getChild(final int groupPosition, final int childPosition)
    {
        return off_data_list.get(groupPosition).getOrder_challan_data().get(childPosition);
    }

    @Override
    public long getChildId(final int groupPosition, final int childPosition)
    {
        return off_data_list.get(groupPosition).getOrder_challan_data().get(childPosition).hashCode();
    }

    @Override
    public View getChildView(final int groupPosition, final int childPosition,
                             boolean isLastChild, View convertView, final ViewGroup parent)
    {
        final ChildViewHolder childViewHolder;

        if (convertView == null)
        {
            childViewHolder = new ChildViewHolder();
            final LayoutInflater infalInflater = (LayoutInflater) this.ctx
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = infalInflater.inflate(R.layout.list_item_lifting_inv_child, parent, false);

            childViewHolder.tvChallanNo = (TextView) convertView.findViewById(R.id.tvChallanNo);
            childViewHolder.tvDate = (TextView) convertView.findViewById(R.id.tvChildDate);
            childViewHolder.tvChildQty = (TextView) convertView.findViewById(R.id.tvChildQty);
            childViewHolder.tv_dis_allocation = (TextView) convertView.findViewById(R.id.tv_dis_allocation);

            convertView.setTag(childViewHolder);
        }
        else
        {
            childViewHolder = (ChildViewHolder) convertView.getTag();
        }

        try
        {
            final LiftingAssignInvModelChild det = off_data_list.get(groupPosition).getOrder_challan_data().get(childPosition);

            childViewHolder.tvChallanNo.setText(det.getInvno());
            childViewHolder.tvDate.setText(det.getInvdt());
            childViewHolder.tvChildQty.setText(det.getInvqty());

            if(det.get_showDisAllocateButton())
            {
                childViewHolder.tv_dis_allocation.setVisibility(View.VISIBLE);
                childViewHolder.tv_dis_allocation.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        final LifitngAssignedInvModel cat = off_data_list.get(groupPosition);
                        dealerLiftingAssignedFragment.showDealerSubDealerList(cat.getOrder_id(), cat.getProd_display_name(),
                                det.getInvdt(), det.getInvno());
                    }
                });
            }
            else
            {
                childViewHolder.tv_dis_allocation.setVisibility(View.INVISIBLE);
                childViewHolder.tv_dis_allocation.setOnClickListener(null);
            }
        }
        catch (Exception e)
        {
            print_log_d("_kri45o_101 ", e.toString());

            e.printStackTrace();
        }

        return convertView;

    }

    @Override
    public int getChildrenCount(final int groupPosition)
    {
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
    public View getGroupView(final int groupPosition, final boolean isExpanded,
                             View v, final ViewGroup parent)
    {
        final GroupViewHolder groupViewHolder;

        try
        {
            if (v == null)
            {
                groupViewHolder = new GroupViewHolder();
                final LayoutInflater inflater = (LayoutInflater)ctx.getSystemService
                        (Context.LAYOUT_INFLATER_SERVICE);
                v = inflater.inflate(R.layout.list_item_header_lifing_assigned, parent, false);

                groupViewHolder.lblListHeader = (TextView) v.findViewById(R.id.lblListHeader);
                groupViewHolder.tvStatus = (TextView) v.findViewById(R.id.tvStatus);
                groupViewHolder.tvHeaderQty = (TextView) v.findViewById(R.id.tvHeaderQty);
                groupViewHolder.tvProdName = (TextView) v.findViewById(R.id.tvProdName);
                groupViewHolder.tv_order_full_date_time = (TextView) v.findViewById(R.id.tv_order_full_date_time);
                groupViewHolder.tv_destination_address = (TextView) v.findViewById(R.id.tv_destination_address);
                groupViewHolder.tv_freight = (TextView) v.findViewById(R.id.tv_freight);
                groupViewHolder.tv_allocation = (TextView) v.findViewById(R.id.tv_allocation);
                groupViewHolder.tvRemaining = (TextView) v.findViewById(R.id.tvRemaining);
                groupViewHolder.iv_indicator = (ImageView) v.findViewById(R.id.iv_indicator);

                v.setTag(groupViewHolder);
            }
            else
            {
                groupViewHolder = (GroupViewHolder) v.getTag();
            }

            final LifitngAssignedInvModel cat = off_data_list.get(groupPosition);

            groupViewHolder.lblListHeader.setText(cat.getOrder_id());
            groupViewHolder.tvStatus.setText(cat.getSTATUS());
            groupViewHolder.tv_order_full_date_time.setText(cat.getOrder_date()); //cat.getErporderdt()
            groupViewHolder.tv_destination_address.setText(cat.getDestination_name());
            groupViewHolder.tv_freight.setText(cat.getFreight());
            groupViewHolder.tvRemaining.setText("Remaining Available Qty = " + cat.getRemainingQty());

            if(cat.getRemainingQty() > 0)
            {
                groupViewHolder.lblListHeader.setTextColor(Color.BLACK);
            }
            else
            {
                groupViewHolder.lblListHeader.setTextColor(ctx.getResources().getColor(R.color.colorGreen));
            }

            if(cat.get_showAllocateButton())
            {
                groupViewHolder.tv_allocation.setVisibility(View.VISIBLE);
            }
            else
            {
                groupViewHolder.tv_allocation.setVisibility(View.GONE);
            }


            groupViewHolder.tvHeaderQty.setText("Qty : " + cat.getQty());
            groupViewHolder.tvProdName.setText(cat.getProd_display_name());

            if(isExpanded)
            {
                groupViewHolder.iv_indicator.setImageResource(R.drawable.uu);
            }
            else
            {
                groupViewHolder.iv_indicator.setImageResource(R.drawable.dd);
            }

            if(!off_data_list.get(groupPosition).getOrder_challan_data().isEmpty())
            {
                groupViewHolder.iv_indicator.setVisibility(View.VISIBLE);
            }
            else
            {
                groupViewHolder.iv_indicator.setVisibility(View.INVISIBLE);
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return v;

    }


    private class GroupViewHolder
    {
        private TextView lblListHeader, tvStatus,tvHeaderQty,tvProdName,tv_order_full_date_time,
                tv_destination_address,tv_freight, tv_allocation, tvRemaining;
        private ImageView iv_indicator;
    }
    private class ChildViewHolder
    {
        private TextView tvChallanNo, tvChildQty, tvDate, tv_dis_allocation;
    }
    @Override
    public boolean hasStableIds() {
        return true;
    }

    @Override
    public boolean isChildSelectable(final int groupPosition, final int childPosition) {
        return true;
    }

    public void setFilter(final ArrayList<LifitngAssignedInvModel> countryModels)
    {
        off_data_list = new ArrayList<>();
        off_data_list.addAll(countryModels);
        notifyDataSetChanged();
    }
}