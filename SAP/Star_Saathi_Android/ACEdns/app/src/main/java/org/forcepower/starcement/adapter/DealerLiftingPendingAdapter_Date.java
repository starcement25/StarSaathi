package org.forcepower.starcement.adapter;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.Dialog;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.OnSingleClickListener;
import org.forcepower.starcement.R;
import org.forcepower.starcement.fragments.DealerFragmentLiftingPending;
import org.forcepower.starcement.bean.DealerLifitngModel;

import java.util.ArrayList;
import java.util.List;


/**
 * Created by @Amitabha
 */
public final class DealerLiftingPendingAdapter_Date extends RecyclerView.Adapter
{
    private final int VIEW_ITEM = 1;
    private final int VIEW_PROG = 0;
    private Activity mContext;
    private ArrayList<DealerLifitngModel> finalValues = new ArrayList<>();
    private final DealerFragmentLiftingPending fragmentLiftingPending;

    @SuppressLint("NotifyDataSetChanged")
    public void setFilter(final ArrayList<DealerLifitngModel> images)
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
        private final TextView tv_l_product_name, tv_qty_bags, tv_lifting_date, tv_challan_no,
                tv_l_status, tv_status_approved, tv_status_rejected, tv_l_sd_rssd_name,
                tv_approved_rejection_date_show;
        private final LinearLayout ll_reject_approve;
        
        public MyViewHolder(final View convertView)
        {
            super(convertView);

            tv_l_product_name = (TextView) convertView.findViewById(R.id.tv_l_product_name);
            tv_qty_bags = (TextView) convertView.findViewById(R.id.tv_qty_bags);
            tv_lifting_date = (TextView) convertView.findViewById(R.id.tv_lifting_date);
            tv_challan_no = (TextView) convertView.findViewById(R.id.tv_challan_no);
            tv_l_status = (TextView) convertView.findViewById(R.id.tv_l_status);
            tv_status_approved = (TextView) convertView.findViewById(R.id.tv_status_approved);
            tv_status_rejected = (TextView) convertView.findViewById(R.id.tv_status_rejected);
            tv_l_sd_rssd_name = (TextView) convertView.findViewById(R.id.tv_l_sd_rssd_name);
            tv_approved_rejection_date_show = (TextView) convertView.findViewById(R.id.tv_approved_rejection_date_show);
            ll_reject_approve = (LinearLayout) convertView.findViewById(R.id.ll_reject_approve);
        }
    }


    public DealerLiftingPendingAdapter_Date(final Activity mContext, final ArrayList<DealerLifitngModel> finalValues,
                                            final DealerFragmentLiftingPending fragmentLiftingPending)
    {
        this.mContext = mContext;
        this.finalValues = finalValues;
        this.fragmentLiftingPending = fragmentLiftingPending;

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
    public void onBindViewHolder(@NonNull final RecyclerView.ViewHolder holder, int pos)
    {
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
                ((MyViewHolder) holder).tv_l_sd_rssd_name.setText("Sub Dealer/RSSD Name : "+finalValues.get(position).getSub_dealer_rssd_name());

                ((MyViewHolder) holder).tv_approved_rejection_date_show.setVisibility(View.GONE);

                ((MyViewHolder) holder).ll_reject_approve.setVisibility(View.VISIBLE);
                
                ((MyViewHolder) holder).tv_status_rejected.setOnClickListener(new OnSingleClickListener() {
                    @Override
                    public void onSingleClick(View v) {
                        update_status_dialog(finalValues.get(position).getLid());
                    }
                });
                ((MyViewHolder) holder).tv_status_approved.setOnClickListener(new OnSingleClickListener() {
                    @Override
                    public void onSingleClick(View v) {
                        fragmentLiftingPending.new TRANS_UpdateLigitngStatus_Asynctask(mContext, finalValues.get(position).getLid(), "APPROVED", "").execute();
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
    public void update_status_dialog(final String the_lifting_id)
    {
        try
        {
            final Dialog dialog = new Dialog( mContext, R.style.MyDialog);
            dialog.setContentView(R.layout.dialog_lifting_status_update);


            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(false);
            dialog.show();

            final Window window = dialog.getWindow();
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.WRAP_CONTENT);

            final EditText et_reject_note =  (EditText) dialog.findViewById(R.id.et_reject_note);
            final TextView tv_status_r_submit =  (TextView) dialog.findViewById(R.id.tv_status_r_submit);
            tv_status_r_submit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {

                    final String reason_for_rejection = et_reject_note.getText().toString().trim()+"";
                    if(reason_for_rejection.matches(""))
                    {
                        Toast.makeText(mContext, "Please enter reason for rejection", Toast.LENGTH_SHORT).show();
                    }
                    else
                    {
                        dialog.dismiss();
                        fragmentLiftingPending.new TRANS_UpdateLigitngStatus_Asynctask(mContext, the_lifting_id, "REJECTED", reason_for_rejection).execute();
                    }
                }
            });
        
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
}
