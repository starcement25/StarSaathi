package org.forcepower.starcement.adapter;

import static org.forcepower.starcement.util.Utils.show_msg_Dialog;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.net.Uri;
import android.text.SpannableString;
import android.text.style.UnderlineSpan;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.LiftingAssignModelChild;
import org.forcepower.starcement.bean.LifitngAssignedModel;
import org.forcepower.starcement.fragments.DealerLiftingAssignedFragment;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

public final class DealerLiftingAssignedAdapter_Date extends BaseExpandableListAdapter
{
    private ArrayList<LifitngAssignedModel>off_data_list = new ArrayList<>();
    private Activity ctx;
    private DealerLiftingAssignedFragment dealerLiftingAssignedFragment;

    public DealerLiftingAssignedAdapter_Date(final Activity activity,
                                             final ArrayList<LifitngAssignedModel> off_data_list,
                                             final DealerLiftingAssignedFragment dealerLiftingAssignedFragment)
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
            convertView = infalInflater.inflate(R.layout.list_item_lifting_child, parent, false);

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
            final LiftingAssignModelChild det = off_data_list.get(groupPosition).getOrder_challan_data().get(childPosition);

            childViewHolder.tvChallanNo.setText(det.getChallanno());
            childViewHolder.tvDate.setText(det.getDispatch_date());
            childViewHolder.tvChildQty.setText(det.getDispatch_qty());

            if(det.get_showDisAllocateButton())
            {
                childViewHolder.tv_dis_allocation.setVisibility(View.VISIBLE);
                childViewHolder.tv_dis_allocation.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        final LifitngAssignedModel cat = off_data_list.get(groupPosition);
                        dealerLiftingAssignedFragment.showDealerSubDealerList(cat.getOrder_id(), cat.getProd_display_name(),
                        det.getDispatch_date());
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
                groupViewHolder.iv_indicator = (ImageView) v.findViewById(R.id.iv_indicator);

                v.setTag(groupViewHolder);
            }
            else
            {
                groupViewHolder = (GroupViewHolder) v.getTag();
            }

            final LifitngAssignedModel cat = off_data_list.get(groupPosition);

            groupViewHolder.lblListHeader.setText(cat.getOrder_id());
            groupViewHolder.tvStatus.setText(cat.getSTATUS());
            groupViewHolder.tv_order_full_date_time.setText(cat.getOrder_date()); //cat.getErporderdt()
            groupViewHolder.tv_destination_address.setText(cat.getDestination_name());
            groupViewHolder.tv_freight.setText(cat.getFreight());

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
                tv_destination_address,tv_freight, tv_allocation;
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

    public void setFilter(final ArrayList<LifitngAssignedModel> countryModels)
    {
        off_data_list = new ArrayList<>();
        off_data_list.addAll(countryModels);
        notifyDataSetChanged();
    }
}