package org.forcepower.starcement.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.DealerLifitngModel;

import java.util.ArrayList;
import java.util.List;


/**
 * Created by @Amitabha
 */
public final class DealerLiftingApprovedRejectedAdapter_Date extends RecyclerView.Adapter {
    private final int VIEW_ITEM = 1;
    private final int VIEW_PROG = 0;
    private Context mContext;
    private List<DealerLifitngModel> finalValues = new ArrayList<>();

    @SuppressLint("NotifyDataSetChanged")
    public void setFilter(final ArrayList<DealerLifitngModel> images) {
        if(images != null)
        {
            finalValues = new ArrayList<>();
            finalValues.addAll(images);
            notifyDataSetChanged();
        }
    }

    public final class MyViewHolder extends RecyclerView.ViewHolder {
        private final TextView tv_l_product_name, tv_qty_bags, tv_lifting_date, tv_challan_no,
                tv_l_status, tv_reason_for_rejection, tv_l_sd_rssd_name, tv_approved_rejection_date_show;
        public MyViewHolder(final View convertView)
        {
            super(convertView);

            tv_l_product_name = (TextView) convertView.findViewById(R.id.tv_l_product_name);
            tv_qty_bags = (TextView) convertView.findViewById(R.id.tv_qty_bags);
            tv_lifting_date = (TextView) convertView.findViewById(R.id.tv_lifting_date);
            tv_challan_no = (TextView) convertView.findViewById(R.id.tv_challan_no);
            tv_l_status = (TextView) convertView.findViewById(R.id.tv_l_status);
            tv_reason_for_rejection = (TextView) convertView.findViewById(R.id.tv_reason_for_rejection);
            tv_l_sd_rssd_name = (TextView) convertView.findViewById(R.id.tv_l_sd_rssd_name);
            tv_approved_rejection_date_show = (TextView) convertView.findViewById(R.id.tv_approved_rejection_date_show);
        }
    }

    public DealerLiftingApprovedRejectedAdapter_Date(final Context mContext, final List<DealerLifitngModel> finalValues) {
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
                    R.layout.list_item_lifting_history_dealer, parent, false);

            vh = new MyViewHolder(v);
        } else {
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
            final int position = holder.getLayoutPosition();
            if (holder instanceof MyViewHolder)
            {
                ((MyViewHolder) holder).tv_l_product_name.setText("Product Name : "+finalValues.get(position).getProduct_name());
                ((MyViewHolder) holder).tv_qty_bags.setText("Quantity in Bags : "+finalValues.get(position).getQty_in_bags());
                ((MyViewHolder) holder).tv_lifting_date.setText("Date of Lifting : "+finalValues.get(position).getDate_of_lifting_show());
                ((MyViewHolder) holder).tv_challan_no.setText("Challan Number : "+finalValues.get(position).getChallan_number());
                ((MyViewHolder) holder).tv_l_status.setText("Status : "+finalValues.get(position).getStatus());
                ((MyViewHolder) holder).tv_reason_for_rejection.setText("Reason for Rejection : "+finalValues.get(position).getReason_for_rejection());
                ((MyViewHolder) holder).tv_l_sd_rssd_name.setText("Sub Dealer/RSSD Name : "+finalValues.get(position).getSub_dealer_rssd_name());

                if(finalValues.get(position).getStatus().equalsIgnoreCase("APPROVED"))
                {
                    ((MyViewHolder) holder).tv_approved_rejection_date_show.setText("Approved Date : "+finalValues.get(position).getApproved_rejection_date_show());
                    ((MyViewHolder) holder).tv_approved_rejection_date_show.setVisibility(View.VISIBLE);
                }
                else if(finalValues.get(position).getStatus().equalsIgnoreCase("REJECTED"))
                {
                    ((MyViewHolder) holder).tv_approved_rejection_date_show.setText("Rejection Date : "+finalValues.get(position).getApproved_rejection_date_show());
                    ((MyViewHolder) holder).tv_approved_rejection_date_show.setVisibility(View.VISIBLE);
                }
                else
                {
                    ((MyViewHolder) holder).tv_approved_rejection_date_show.setVisibility(View.GONE);
                }
                if(!finalValues.get(position).getReason_for_rejection().isEmpty())
                {
                    ((MyViewHolder) holder).tv_reason_for_rejection.setVisibility(View.VISIBLE);
                }
                else
                {
                    ((MyViewHolder) holder).tv_reason_for_rejection.setVisibility(View.GONE);
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
        public ProgressBar progressBar;

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
