package org.forcepower.starcement.adapter;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
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
public final class EpodAdapter_Date extends RecyclerView.Adapter {
    private final int VIEW_ITEM = 1;
    private final int VIEW_PROG = 0;
    private Activity mContext;
    private ArrayList<CategoryItem> orderHistoryClassList = new ArrayList<>();
    
    @SuppressLint("NotifyDataSetChanged")
    public void setFilter(final ArrayList<CategoryItem> images)
    {
        orderHistoryClassList = new ArrayList<>();

        if(images != null)
        {
            orderHistoryClassList.addAll(images);
        }

        notifyDataSetChanged();
    }

    public final class MyViewHolder extends RecyclerView.ViewHolder
    {
        private final TextView tv_val;

        public MyViewHolder(final View convertView)
        {
            super(convertView);

            tv_val = (TextView) convertView.findViewById(R.id.tv_val);
        }
    }

    public EpodAdapter_Date(final Activity mContext, final ArrayList<CategoryItem> orderHistoryClassList)
    {
        this.mContext = mContext;
        this.orderHistoryClassList = orderHistoryClassList;

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
                    R.layout.list_item_val, parent, false);

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
                ((MyViewHolder) holder).tv_val.setText((position+1) + "");
                if(orderHistoryClassList.get(position).getSelected())
                {
                    ((MyViewHolder) holder).tv_val.setBackgroundResource(R.drawable.gray_border_solid_black_bg);
                    ((MyViewHolder) holder).tv_val.setTextColor(Color.WHITE);
                }
                else
                {
                    ((MyViewHolder) holder).tv_val.setBackgroundResource(R.drawable.rounded_solid);
                    ((MyViewHolder) holder).tv_val.setTextColor(Color.BLACK);
                }
                ((MyViewHolder) holder).tv_val.setOnClickListener(new View.OnClickListener() {
                    @SuppressLint("NotifyDataSetChanged")
                    @Override
                    public void onClick(View v) {
                        for(int i = 0; i<orderHistoryClassList.size(); i++)
                        {
                            orderHistoryClassList.get(i).setSelected(false);
                        }

                        orderHistoryClassList.get(position).setSelected(true);

                        //
                        notifyDataSetChanged();

                        ((EpodActivity)mContext).setValue(position+1);
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
