package org.forcepower.starcement.adapter;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.AllocationListModel;

import java.util.ArrayList;
import java.util.List;


/**
 * Created by @Amitabha
 */
public final class DealerLiftingAllocatedInvAdapter_Date extends RecyclerView.Adapter
{
    private final int VIEW_ITEM = 1;
    private final int VIEW_PROG = 0;
    private Activity mContext;
    private List<AllocationListModel> finalValues = new ArrayList<>();

    @SuppressLint("NotifyDataSetChanged")
    public void setFilter(final ArrayList<AllocationListModel> images) {
        if(images != null)
        {
            finalValues = new ArrayList<>();
            finalValues.addAll(images);
            notifyDataSetChanged();
        }
    }

    public final class MyViewHolder extends RecyclerView.ViewHolder {
        private final TextView tv_allo_prod_desc, tv_allocation_qty, tv_allo_date_and_time,
                tv_allo_counter_nm, tv_allo_challan_no, tv_allo_challan_date,tv_isDelete;

        public MyViewHolder(final View convertView)
        {
            super(convertView);
            tv_allo_prod_desc = (TextView) convertView.findViewById(R.id.tv_allo_prod_desc);
            tv_allocation_qty = (TextView) convertView.findViewById(R.id.tv_allocation_qty);
            tv_allo_date_and_time = (TextView) convertView.findViewById(R.id.tv_allo_date_and_time);
            tv_allo_counter_nm = (TextView) convertView.findViewById(R.id.tv_allo_counter_nm);
            tv_allo_challan_no = (TextView) convertView.findViewById(R.id.tv_allo_challan_no);
            tv_allo_challan_date = (TextView) convertView.findViewById(R.id.tv_allo_challan_date);
            tv_isDelete = (TextView) convertView.findViewById(R.id.tv_isDelete);
        }
    }

    public DealerLiftingAllocatedInvAdapter_Date(final Activity mContext, final List<AllocationListModel> finalValues) {
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
        if (viewType == VIEW_ITEM)
        {
            final View v = LayoutInflater.from(parent.getContext()).inflate(
                    R.layout.list_item_allocated, parent, false);

            vh = new MyViewHolder(v);
        }
        else
        {
            final View v = LayoutInflater.from(parent.getContext()).inflate(
                    R.layout.item_loading, parent, false);

            vh = new ProgressViewHolder(v);
        }
        return vh;
    }

    @Override
    public void onBindViewHolder(@NonNull final RecyclerView.ViewHolder holder, int pos) {
        try
        {
            final int index = holder.getLayoutPosition();
            if (holder instanceof MyViewHolder)
            {
                ((MyViewHolder) holder).tv_allo_prod_desc.setText("Product : " + finalValues.get(index).getProd_desc());
                ((MyViewHolder) holder).tv_allocation_qty.setText("Allocated Qty : " + finalValues.get(index).getAllocation_qty());
                ((MyViewHolder) holder).tv_allo_date_and_time.setText("Trans Date : " + finalValues.get(index).getDate_and_time());

                ((MyViewHolder) holder).tv_allo_counter_nm.setText("Counter Name : " + finalValues.get(index).getCounter_name());
                ((MyViewHolder) holder).tv_allo_challan_no.setText("Invoice No : " + finalValues.get(index).getChallan_no());
                ((MyViewHolder) holder).tv_allo_challan_date.setText("Invoice Date : " + finalValues.get(index).getChallan_date());
                if(finalValues.get(index).getIs_deleted()==1){
                    ((MyViewHolder) holder).tv_isDelete.setVisibility(View.VISIBLE);
                }else{
                    ((MyViewHolder) holder).tv_isDelete.setVisibility(View.GONE);
                }
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
