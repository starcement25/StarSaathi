/*
 * Copyright (C) 2013 Surviving with Android (http://www.survivingwithandroid.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package org.forcepower.starcement.adapter;

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
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.TrackOrderModelChild;
import org.forcepower.starcement.bean.TrackOrderModelTop;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;

public final class TrackOrderAdapterSub extends BaseExpandableListAdapter
{
    private ArrayList<TrackOrderModelTop>off_data_list = new ArrayList<>();
    private Context ctx;

    public TrackOrderAdapterSub(final Context activity, final ArrayList<TrackOrderModelTop> off_data_list)
    {

        this.off_data_list = off_data_list;
        this.ctx = activity;
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
            convertView = infalInflater.inflate(R.layout.list_item_child, parent, false);

            childViewHolder.tvChallanNo = (TextView) convertView.findViewById(R.id.tvChallanNo);
            childViewHolder.tv_challan_feedback = (TextView) convertView.findViewById(R.id.tv_challan_feedback);
            childViewHolder.tvDate = (TextView) convertView.findViewById(R.id.tvChildDate);
            childViewHolder.tvChildQty = (TextView) convertView.findViewById(R.id.tvChildQty);
            childViewHolder.tvTrackNo = (TextView) convertView.findViewById(R.id.tvTrackNo);
            childViewHolder.tvDriverCont = (TextView) convertView.findViewById(R.id.tvDriverCont);
            childViewHolder.tv_transporter_name = (TextView) convertView.findViewById(R.id.tv_transporter_name);
            childViewHolder.ch_status = (TextView) convertView.findViewById(R.id.tv_ch_status);

            convertView.setTag(childViewHolder);
        }
        else
        {
            childViewHolder = (ChildViewHolder) convertView.getTag();
        }

        try
        {
            final TrackOrderModelChild det = off_data_list.get(groupPosition).getOrder_challan_data().get(childPosition);

            childViewHolder.tvChallanNo.setText(det.getChallanno());

            if(det.getColour_code() != null && det.getColour_code().contains("#"))
                childViewHolder.tv_challan_feedback.setTextColor(Color.parseColor(det.getColour_code()));

            childViewHolder.tv_challan_feedback.setVisibility(View.INVISIBLE);


            childViewHolder.tvDate.setText(det.getChallandt());
            childViewHolder.tvChildQty.setText(det.getChallanqty());

            childViewHolder.tvTrackNo.setText(det.getTruckno());
            childViewHolder.tv_transporter_name.setText(det.getTransporter_name());
            childViewHolder.ch_status.setText(det.getCh_status());

            SpannableString content = new SpannableString(det.getDriverno() + "");
            content.setSpan(new UnderlineSpan(), 0, content.length(), 0);
            childViewHolder.tvDriverCont.setText(content);
            childViewHolder.tvDriverCont.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if(!det.getDriverno().matches(""))
                    {
                        final Intent intent = new Intent(Intent.ACTION_DIAL);
                        intent.setData(Uri.parse("tel:"+det.getDriverno()));
                        ctx.startActivity(intent);
                    }
                }
            });

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
                             final View convertView, final ViewGroup parent)
    {
        View v = convertView;
        try
        {
            if (v == null)
            {
                final LayoutInflater inflater = (LayoutInflater)ctx.getSystemService
                        (Context.LAYOUT_INFLATER_SERVICE);
                v = inflater.inflate(R.layout.list_group_header, parent, false);
            }

            final TextView groupName = (TextView) v.findViewById(R.id.lblListHeader);
            final TextView tvStatus = (TextView) v.findViewById(R.id.tvStatus);
            final TextView tvHeaderQty = (TextView) v.findViewById(R.id.tvHeaderQty);
            final TextView tvProdName = (TextView) v.findViewById(R.id.tvProdName);
            final TextView tv_order_full_date_time = (TextView) v.findViewById(R.id.tv_order_full_date_time);
            final TextView tv_destination_address = (TextView) v.findViewById(R.id.tv_destination_address);
            final TextView tv_freight = (TextView) v.findViewById(R.id.tv_freight);
            final TextView tv_erporderno = (TextView) v.findViewById(R.id.tv_erporderno);
            tv_erporderno.setVisibility(View.GONE);
            final ImageView iv_indicator = (ImageView) v.findViewById(R.id.iv_indicator);

            final TrackOrderModelTop cat = off_data_list.get(groupPosition);

            groupName.setText(cat.getApporderno());
            tvStatus.setText(cat.getStatus());
            tv_order_full_date_time.setText(cat.getOrder_full_date_time()); //cat.getErporderdt()
            tv_destination_address.setText(cat.getDestination_address());
            tv_freight.setText(cat.getFreight());
            if(cat.getStatus().equalsIgnoreCase("order received"))
            {
                tvStatus.setTextColor(ctx.getResources().getColor(R.color.grey));
            }
            else if(cat.getStatus().equalsIgnoreCase("dispatched"))
            {
                tvStatus.setTextColor(Color.parseColor("#3a8a00")); //green
            }
            else if(cat.getStatus().equalsIgnoreCase("do approved"))
            {
                tvStatus.setTextColor(Color.parseColor("#edbe00")); //yellow
            }
            else if(cat.getStatus().equalsIgnoreCase("Order authorized"))
            {
                tvStatus.setTextColor(Color.BLUE); //
            }
            else if(cat.getStatus().equalsIgnoreCase("Order canceled"))
            {
                tvStatus.setTextColor(ctx.getResources().getColor(R.color.red)); //
            }
            else
            {
                tvStatus.setTextColor(ctx.getResources().getColor(R.color.black)); //
            }

            tvHeaderQty.setText(cat.getQty());
            tvProdName.setText(cat.getProd_display_name());

            if(isExpanded)
            {
                iv_indicator.setImageResource(R.drawable.uu);
            }
            else
            {
                iv_indicator.setImageResource(R.drawable.dd);
            }

            if(off_data_list.get(groupPosition).getOrder_challan_data().size() > 0)
            {
                iv_indicator.setVisibility(View.VISIBLE);
            }
            else
            {
                iv_indicator.setVisibility(View.INVISIBLE);
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return v;

    }


    static class ChildViewHolder
    {
        private TextView tvChallanNo, tv_challan_feedback, tvDate, tvChildQty, tvTrackNo, tvDriverCont,
                tv_transporter_name, ch_status;
    }
    @Override
    public boolean hasStableIds() {
        return true;
    }

    @Override
    public boolean isChildSelectable(final int groupPosition, final int childPosition) {
        return true;
    }

    public void setFilter(final ArrayList<TrackOrderModelTop> countryModels)
    {
        off_data_list = new ArrayList<>();
        off_data_list.addAll(countryModels);
        notifyDataSetChanged();
    }
    public static String convertDate2(String date)
    {
        try
        {
            SimpleDateFormat spf=new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            Date newDate=spf.parse(date);
            spf= new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault());
            date = spf.format(newDate);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return date;
    }
}