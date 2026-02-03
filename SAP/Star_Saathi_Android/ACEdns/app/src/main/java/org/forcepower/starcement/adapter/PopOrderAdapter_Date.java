package org.forcepower.starcement.adapter;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.PopOrderModel;
import org.forcepower.starcement.fragments.FragmentPopOrderHistory;

import java.util.ArrayList;
import java.util.List;


/**
 * Created by @Amitabha
 */
public final class PopOrderAdapter_Date extends RecyclerView.Adapter
{
    private final int VIEW_ITEM = 1;
    private final int VIEW_PROG = 0;
    private Activity mContext;
    private List<PopOrderModel> finalValues = new ArrayList<>();

    @SuppressLint("NotifyDataSetChanged")
    public void setFilter(final ArrayList<PopOrderModel> images)
    {
        if(images != null)
        {
            finalValues = new ArrayList<>();
            finalValues.addAll(images);
            notifyDataSetChanged();
        }
    }


    public final class MyViewHolder extends RecyclerView.ViewHolder
    {
        private TextView tv_main_order_id, tv_order_id, tv_pop_date, tv_prod_desc, tv_min_total_amount,
                tv_min_order_qty, tv_pop_address, tv_pop_order_status;
        private ImageView iv_pop;

        public MyViewHolder(final View convertView)
        {
            super(convertView);

            tv_order_id = (TextView) convertView.findViewById(R.id.tv_order_id);
            tv_main_order_id = (TextView) convertView.findViewById(R.id.tv_main_order_id);
            tv_pop_date = (TextView) convertView.findViewById(R.id.tv_pop_date);
            tv_prod_desc = (TextView) convertView.findViewById(R.id.tv_prod_desc);
            tv_min_order_qty = (TextView) convertView.findViewById(R.id.tv_min_order_qty);
            tv_min_total_amount = (TextView) convertView.findViewById(R.id.tv_min_total_amount);
            tv_pop_address = (TextView) convertView.findViewById(R.id.tv_pop_address);
            tv_pop_order_status = (TextView) convertView.findViewById(R.id.tv_pop_order_status);
            iv_pop = (ImageView) convertView.findViewById(R.id.iv_pop);
        }
    }


    public PopOrderAdapter_Date(final Activity mContext, final List<PopOrderModel> finalValues)
    {
        this.mContext = mContext;
        this.finalValues = finalValues;
    }
    @Override
    public int getItemViewType(final int position) {
        return finalValues.get(position) != null ? VIEW_ITEM : VIEW_PROG;
    }
    @NonNull
    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(@NonNull final ViewGroup parent,
                                                      final int viewType) {
        RecyclerView.ViewHolder vh;
        if (viewType == VIEW_ITEM) {
            final View v = LayoutInflater.from(parent.getContext()).inflate(
                    R.layout.list_item_pop_product_hist, parent, false);

            vh = new MyViewHolder(v);
        } else {
            final View v = LayoutInflater.from(parent.getContext()).inflate(
                    R.layout.item_loading, parent, false);

            vh = new ProgressViewHolder(v);
        }
        return vh;
    }

    @Override
    public void onBindViewHolder(@NonNull final RecyclerView.ViewHolder holder, int pos)
    {
        try
        {
            final int index = holder.getLayoutPosition();
            if (holder instanceof MyViewHolder)
            {
                ((MyViewHolder) holder).tv_order_id.setText(finalValues.get(index).getOrder_id());
                ((MyViewHolder) holder).tv_main_order_id.setText(finalValues.get(index).get_main_order_id());
                ((MyViewHolder) holder).tv_pop_date.setText(finalValues.get(index).getOrder_date());
                ((MyViewHolder) holder).tv_prod_desc.setText(finalValues.get(index).getProd_display_name());
                ((MyViewHolder) holder).tv_min_order_qty.setText(finalValues.get(index).getQty());
                ((MyViewHolder) holder).tv_min_total_amount.setText(finalValues.get(index).get_total_amount());
                ((MyViewHolder) holder).tv_pop_address.setText(finalValues.get(index).getAddress() + ", "+finalValues.get(index).getPin());
                ((MyViewHolder) holder).tv_pop_order_status.setText(finalValues.get(index).get_order_status());

                Glide.with(mContext)
                        .load(finalValues.get(index).get_prod_image())
                        .diskCacheStrategy(DiskCacheStrategy.NONE)
                        .error(R.drawable.default_)
                        .placeholder(R.drawable.default_)
                        .into (((MyViewHolder) holder).iv_pop);
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
    public int getItemCount()
    {
        if(finalValues != null && finalValues.size() > 0)
        {
            return finalValues.size();
        }
        else
        {
            return 0;
        }
    }
}
