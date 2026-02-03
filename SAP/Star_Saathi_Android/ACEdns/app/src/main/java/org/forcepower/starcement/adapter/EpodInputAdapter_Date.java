package org.forcepower.starcement.adapter;

import static org.forcepower.starcement.SharedPrefData.get_server_current_date;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.CategoryItem;
import org.forcepower.starcement.R;
import org.forcepower.starcement.aaa.EpodActivity;

import java.util.ArrayList;

/**
 * Created by @Amitabha
 */
public final class EpodInputAdapter_Date extends RecyclerView.Adapter
{
    private final int VIEW_ITEM = 1;
    private final int VIEW_PROG = 0;
    private Activity mContext;
    private ArrayList<CategoryItem> orderHistoryClassList = new ArrayList<>();
    private String is_delivered = "";

    @SuppressLint("NotifyDataSetChanged")
    public void setFilter(final ArrayList<CategoryItem> images, final String is_delivered)
    {
        this.is_delivered = is_delivered;
        orderHistoryClassList = new ArrayList<>();

        if(images != null)
        {
            orderHistoryClassList.addAll(images);
        }

        notifyDataSetChanged();
    }


    public final class MyViewHolder extends RecyclerView.ViewHolder
    {
        private TextView tv_total_qty, tv_ch_dispatch_order, tv_ch_date_time;
        private EditText tv_ch_rcvd_order, et_ch_pending_order, tv_ch_delivery_qty,
                et_ch_delivery_remarks;
        private ImageView img_1_top, img_2_track;

        private final LinearLayout ll_Delivery, ll_Reject;

        public MyViewHolder(final View convertView)
        {
            super(convertView);

            tv_total_qty = (TextView) convertView.findViewById(R.id.tv_total_qty);

            tv_ch_rcvd_order =  (EditText) convertView.findViewById(R.id.tv_ch_rcvd_order);
            tv_ch_delivery_qty =  (EditText) convertView.findViewById(R.id.tv_ch_delivery_qty);
            et_ch_pending_order =  (EditText) convertView.findViewById(R.id.et_ch_pending_order);
            et_ch_delivery_remarks =  (EditText) convertView.findViewById(R.id.et_ch_delivery_remarks);
            tv_ch_dispatch_order =  (TextView) convertView.findViewById(R.id.tv_ch_dispatch_order);
            tv_ch_date_time =  (TextView) convertView.findViewById(R.id.tv_ch_date_time);

            img_1_top = (ImageView) convertView.findViewById(R.id.img_1_top);
            img_2_track = (ImageView) convertView.findViewById(R.id.img_2_track);

            ll_Delivery =  (LinearLayout) convertView.findViewById(R.id.ll_Delivery);
            ll_Reject =  (LinearLayout) convertView.findViewById(R.id.ll_Reject);
        }
    }


    public EpodInputAdapter_Date(final Activity mContext,
                                 final ArrayList<CategoryItem> orderHistoryClassList,
                                 final String is_delivered)
    {
        this.mContext = mContext;
        this.orderHistoryClassList = orderHistoryClassList;
        this.is_delivered = is_delivered;
    }
    @Override
    public int getItemViewType(int position) {
        return orderHistoryClassList.get(position) != null ? VIEW_ITEM : VIEW_PROG;
    }
    @NonNull
    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(@NonNull ViewGroup parent,
                                                      int viewType) {
        RecyclerView.ViewHolder vh;
        if (viewType == VIEW_ITEM) {
            final View v = LayoutInflater.from(parent.getContext()).inflate(
                    R.layout.list_item_epod, parent, false);

            vh = new MyViewHolder(v);
        } else {
            final View v = LayoutInflater.from(parent.getContext()).inflate(
                    R.layout.item_loading, parent, false);

            vh = new ProgressViewHolder(v);
        }
        return vh;
    }

    @Override
    public void onBindViewHolder(@NonNull final RecyclerView.ViewHolder holder, int posit)
    {
        try
        {
            final int position = holder.getLayoutPosition();
            if (holder instanceof MyViewHolder)
            {
                if(is_delivered.equalsIgnoreCase("YES"))
                {
                    ((MyViewHolder) holder).ll_Delivery.setVisibility(View.VISIBLE);
                    ((MyViewHolder) holder).ll_Reject.setVisibility(View.GONE);
                }
                else
                {
                    ((MyViewHolder) holder).ll_Delivery.setVisibility(View.GONE);
                    ((MyViewHolder) holder).ll_Reject.setVisibility(View.VISIBLE);
                }
                ((MyViewHolder) holder).tv_total_qty.setText((position+1) + "");
                ((MyViewHolder) holder).tv_ch_date_time.setText(get_server_current_date(mContext));
                ((MyViewHolder) holder).img_1_top.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        ((EpodActivity)mContext).chooseYourImage(100);
                    }
                });
            }
            else
            {
                ((ProgressViewHolder) holder).progressBar.setIndeterminate(true);
            }

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public final class ProgressViewHolder extends RecyclerView.ViewHolder {
        private ProgressBar progressBar;

        public ProgressViewHolder(final View v) {
            super(v);
            progressBar = (ProgressBar) v.findViewById(R.id.progressBar);
        }
    }

    @Override
    public int getItemCount() {
        if(orderHistoryClassList != null && orderHistoryClassList.size() > 0){
            return orderHistoryClassList.size();
        }else {
            return 0;
        }
    }
}
