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

import static org.forcepower.starcement.adapter.LedgerListViewAdapter.convertDate;

import android.annotation.SuppressLint;
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
import android.widget.Toast;

import org.forcepower.starcement.CategoryClass;
import org.forcepower.starcement.ItemDetailsClass;
import org.forcepower.starcement.R;
import org.forcepower.starcement.aaa.EpodActivity;
import org.forcepower.starcement.aaa.MaterialRcvActivity;

import java.util.ArrayList;
import java.util.List;


public final class ExpandableListInvAdapter extends BaseExpandableListAdapter
{
    private ArrayList<CategoryClass> catList;
    private Activity ctx;

    public ExpandableListInvAdapter(final Activity activity, final ArrayList<CategoryClass> catList)
    {

        this.catList = catList;
        this.ctx = activity;
    }

    @Override
    public Object getChild(final int groupPosition, final int childPosition)
    {
        return catList.get(groupPosition).getItemList().get(childPosition);
    }

    @Override
    public long getChildId(final int groupPosition, final int childPosition)
    {
        return catList.get(groupPosition).getItemList().get(childPosition).hashCode();
    }

    @SuppressLint("SuspiciousIndentation")
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
            childViewHolder.tv_challan_feedback.setVisibility(View.INVISIBLE);
            childViewHolder.tvDate = (TextView) convertView.findViewById(R.id.tvChildDate);
            childViewHolder.tvChildQty = (TextView) convertView.findViewById(R.id.tvChildQty);
            childViewHolder.tvTrackNo = (TextView) convertView.findViewById(R.id.tvTrackNo);
            childViewHolder.tvDriverCont = (TextView) convertView.findViewById(R.id.tvDriverCont);
            childViewHolder.tv_transporter_name = (TextView) convertView.findViewById(R.id.tv_transporter_name);
            childViewHolder.ch_status = (TextView) convertView.findViewById(R.id.tv_ch_status);
            childViewHolder.llDriverCont = (LinearLayout) convertView.findViewById(R.id.llDriverCont);
            childViewHolder.llDriverCont.setVisibility(View.GONE);
            childViewHolder.ll_ch_status = (LinearLayout) convertView.findViewById(R.id.ll_ch_status);
            childViewHolder.ll_ch_status.setVisibility(View.GONE);
            childViewHolder.tv_challan_epod = (TextView) convertView.findViewById(R.id.tv_challan_epod);

            childViewHolder.txt_challan_no = (TextView) convertView.findViewById(R.id.txt_challan_no);
            childViewHolder.txt_challan_date = (TextView) convertView.findViewById(R.id.txt_challan_date);
            childViewHolder.txt_challan_qty = (TextView) convertView.findViewById(R.id.txt_challan_qty);
            childViewHolder.txt_destination = (TextView) convertView.findViewById(R.id.txt_destination);

            convertView.setTag(childViewHolder);
        }
        else
        {
            childViewHolder = (ChildViewHolder) convertView.getTag();
        }

        try
        {
            final ItemDetailsClass det = catList.get(groupPosition).getItemList().get(childPosition);

            childViewHolder.tvChallanNo.setText(det.get_challan_no());

            if(det.get_colour_code() != null && det.get_colour_code().contains("#"))
            childViewHolder.tv_challan_feedback.setTextColor(Color.parseColor(det.get_colour_code()));

            childViewHolder.tv_challan_feedback.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if(det.get_challan_material_received().equalsIgnoreCase("NO"))
                    {
                        final Intent intent = new Intent(ctx, MaterialRcvActivity.class);
                        intent.putExtra("apporderno",  det.get_apporderno());
                        intent.putExtra("erporderno",  det.get_erporderno());
                        intent.putExtra("ch_uid", det.get_ch_uid());
                        intent.putExtra("challanno", det.get_challan_no());

                        intent.putExtra("ch_quantity_no_of_bags", det.get_ch_quantity_no_of_bags());
                        intent.putExtra("ch_status", det.get_ch_status());
                        ctx.startActivity(intent);
                    }
                    else
                    {
                        Toast.makeText(ctx, "Feedback already submitted", Toast.LENGTH_SHORT).show();
                    }
                }
            });

            childViewHolder.tvDate.setText(det.getRateValue());
            childViewHolder.tvChildQty.setText(det.getChallanQty());

            childViewHolder.tvTrackNo.setText(det.getTrackNumber());
            childViewHolder.tv_transporter_name.setText(det.get_transporter_name());
            childViewHolder.ch_status.setText(det.get_ch_status());

            SpannableString content = new SpannableString(det.getDriverContact() + "");
            content.setSpan(new UnderlineSpan(), 0, content.length(), 0);
            childViewHolder.tvDriverCont.setText(content);
            childViewHolder.tvDriverCont.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if(!det.getDriverContact().matches(""))
                    {
                        final Intent intent = new Intent(Intent.ACTION_DIAL);
                        intent.setData(Uri.parse("tel:"+det.getDriverContact()));
                        ctx.startActivity(intent);
                    }
                }
            });

            childViewHolder.tv_challan_epod.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    final Intent intent = new Intent(ctx, EpodActivity.class);
                    intent.putExtra("challan_no",  det.get_challan_no());
                    intent.putExtra("challan_date",  convertDate(det.getRateValue()));
                    intent.putExtra("challanqty", det.getChallanQty());
                    intent.putExtra("qty", det.get_qty());

                    ctx.startActivity(intent);
                }
            });

            childViewHolder.txt_challan_no.setText("INVOICE NO. ");
            childViewHolder.txt_challan_date.setText("INVOICE DATE ");
            childViewHolder.txt_challan_qty.setText("INVOICE QTY(MT)");
            childViewHolder.txt_destination.setText("DESTINATION");
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
        return catList.get(groupPosition).getItemList().size();
    }

    @Override
    public Object getGroup(final int groupPosition) {
        return catList.get(groupPosition);
    }

    @Override
    public int getGroupCount() {
        return catList.size();
    }

    @Override
    public long getGroupId(final int groupPosition) {
        return catList.get(groupPosition).hashCode();
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
            tv_erporderno.setVisibility(View.VISIBLE);
            final ImageView iv_indicator = (ImageView) v.findViewById(R.id.iv_indicator);

            final CategoryClass cat = catList.get(groupPosition);

            groupName.setText(cat.getCategoryId());
            tvStatus.setText(cat.get_order_status());
            tv_order_full_date_time.setText(cat.get_order_full_date_time());
            tv_destination_address.setText(cat.get_destination_address());
            tv_freight.setText(cat.get_freight());
            tv_erporderno.setText(cat.get_erporderno());
            if(cat.get_order_status().equalsIgnoreCase("order received"))
            {
                tvStatus.setTextColor(ctx.getResources().getColor(R.color.grey));
            }
            else if(cat.get_order_status().equalsIgnoreCase("dispatched"))
            {
                tvStatus.setTextColor(Color.parseColor("#3a8a00")); //green
            }
            else if(cat.get_order_status().equalsIgnoreCase("do approved"))
            {
                tvStatus.setTextColor(Color.parseColor("#edbe00")); //yellow
            }
            else if(cat.get_order_status().equalsIgnoreCase("Order authorized"))
            {
                tvStatus.setTextColor(Color.BLUE); //
            }
            else if(cat.get_order_status().equalsIgnoreCase("Order canceled"))
            {
                tvStatus.setTextColor(ctx.getResources().getColor(R.color.red)); //
            }
            else
            {
                tvStatus.setTextColor(ctx.getResources().getColor(R.color.black)); //
            }
            String qty = cat.getQty()+"";
            if(qty.matches("") || qty.equalsIgnoreCase("null"))
            {
                qty = "0";
            }

            tvHeaderQty.setText("X " + qty);
            tvProdName.setText(cat.getprod_desc());

            if(isExpanded)
            {
                iv_indicator.setImageResource(R.drawable.uu);
            }
            else
            {
                iv_indicator.setImageResource(R.drawable.dd);
            }

            if(catList.get(groupPosition).getItemList().size() > 0)
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
        private TextView tvChallanNo, tv_challan_feedback, tvDate, tvChildQty, tvTrackNo,
                tvDriverCont, tv_transporter_name, ch_status, tv_challan_epod;

        private TextView txt_challan_no, txt_challan_date, txt_challan_qty, txt_destination;
        private LinearLayout ll_ch_status, llDriverCont;
    }
    @Override
    public boolean hasStableIds() {
        return true;
    }

    @Override
    public boolean isChildSelectable(final int groupPosition, final int childPosition) {
        return true;
    }

    public void setFilter(final List<CategoryClass> countryModels)
    {
        catList = new ArrayList<>();
        catList.addAll(countryModels);
        notifyDataSetChanged();
    }
}