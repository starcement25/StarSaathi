package org.forcepower.starcement.adapter;

import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.Dialog;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.aaa.PdfViewerActivity;
import org.forcepower.starcement.aaa.PlaceOrderActivity;
import org.forcepower.starcement.bean.PlaceOrderModel;
import org.forcepower.starcement.util.HTTPUtils;

import java.util.ArrayList;

/**
 * Created by @Amitabha
 */
public final class PlaceOrderAdapter_Date extends RecyclerView.Adapter
{
    private final int VIEW_ITEM = 1;
    private final int VIEW_PROG = 0;
    private Activity mContext;
    private ArrayList<PlaceOrderModel> orderHistoryClassList = new ArrayList<>();

    @SuppressLint("NotifyDataSetChanged")
    public void setFilter(final ArrayList<PlaceOrderModel> images)
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
        private final TextView tv_prod_name, tv_qty_bags,
                tv_date_and_time, tv_rssd_name, tv_lifting_date, tv_remarks,
                tv_oq_submit, tv_status_remarks;
        private final LinearLayout ll_status_remarks;

        public MyViewHolder(final View convertView)
        {
            super(convertView);
            tv_prod_name = (TextView) convertView.findViewById(R.id.tv_prod_name);
            tv_qty_bags = (TextView) convertView.findViewById(R.id.tv_qty_bags);
            tv_date_and_time = (TextView) convertView.findViewById(R.id.tv_date_and_time);
            tv_rssd_name = (TextView) convertView.findViewById(R.id.tv_rssd_name);
            tv_lifting_date = (TextView) convertView.findViewById(R.id.tv_lifting_date);
            tv_remarks = (TextView) convertView.findViewById(R.id.tv_remarks);
            tv_oq_submit = (TextView) convertView.findViewById(R.id.tv_oq_submit);
            tv_status_remarks = (TextView) convertView.findViewById(R.id.tv_status_remarks);
            ll_status_remarks = (LinearLayout) convertView.findViewById(R.id.ll_status_remarks);
        }
    }


    public PlaceOrderAdapter_Date(final Activity mContext, final ArrayList<PlaceOrderModel> orderHistoryClassList)
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
                    R.layout.list_item_place_order, parent, false);

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
                ((MyViewHolder) holder).tv_prod_name.setText(orderHistoryClassList.get(position).getProd_name());
                ((MyViewHolder) holder).tv_qty_bags.setText(orderHistoryClassList.get(position).getQty_bags());
                ((MyViewHolder) holder).tv_date_and_time.setText(orderHistoryClassList.get(position).getDate_and_time());
                ((MyViewHolder) holder).tv_rssd_name.setText(orderHistoryClassList.get(position).getRssd_name());
                ((MyViewHolder) holder).tv_lifting_date.setText(orderHistoryClassList.get(position).getQuery_date());
                ((MyViewHolder) holder).tv_remarks.setText(orderHistoryClassList.get(position).getRemarks());
                ((MyViewHolder) holder).tv_status_remarks.setText(orderHistoryClassList.get(position).getStatus_remarks());

                if(orderHistoryClassList.get(position).getStatus_from_app().isEmpty())
                {
                    ((MyViewHolder) holder).tv_oq_submit.setText("Submit");
                    ((MyViewHolder) holder).ll_status_remarks.setVisibility(View.GONE);
                }
                else
                {
                    if(orderHistoryClassList.get(position).getStatus_from_app().equalsIgnoreCase("Reject"))
                    {
                        ((MyViewHolder) holder).ll_status_remarks.setVisibility(View.VISIBLE);
                    }
                    else
                    {
                        ((MyViewHolder) holder).ll_status_remarks.setVisibility(View.GONE);
                    }
                    ((MyViewHolder) holder).tv_oq_submit.setText(orderHistoryClassList.get(position).getStatus_from_app());
                }

                if(((MyViewHolder) holder).tv_oq_submit.getText().toString().equalsIgnoreCase("Submit"))
                {
                    ((MyViewHolder) holder).tv_oq_submit.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View v) {
                            update_status_dialog(orderHistoryClassList.get(position).getOrder_query_id());
                        }
                    });
                }
                else
                {
                    ((MyViewHolder) holder).tv_oq_submit.setOnClickListener(null);
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
    public int getItemCount()
    {
        if(orderHistoryClassList != null && orderHistoryClassList.size() > 0)
        {
            return orderHistoryClassList.size();
        }
        else 
        {
            return 0;
        }
    }

    private String status_from_app = "", status_remarks = "";
    public void update_status_dialog(final String order_query_id)
    {
        try
        {
            final Dialog dialog = new Dialog( mContext, R.style.MyDialog);
            dialog.setContentView(R.layout.dialog_oq_submit);

            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(false);
            dialog.show();
            status_from_app = "";
            status_remarks = "";
            final RadioGroup rg_status =  (RadioGroup) dialog.findViewById(R.id.rg_status);
            rg_status.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                @Override
                public void onCheckedChanged(RadioGroup group, int checkedId) {
                    final RadioButton radioButton = group.findViewById(checkedId);
                    status_from_app = radioButton.getText().toString();
                }
            });

            final EditText et_reject_note =  (EditText) dialog.findViewById(R.id.et_reject_note);
            final TextView tv_status_r_submit =  (TextView) dialog.findViewById(R.id.tv_status_r_submit);
            tv_status_r_submit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    status_remarks = et_reject_note.getText().toString().trim();

                    if(status_from_app.trim().isEmpty())
                    {
                        show_msg_Dialog(mContext, "Please select a option");
                    }
                    else if(status_from_app.equalsIgnoreCase("Reject") &&
                            status_remarks.isEmpty())
                    {
                        show_msg_Dialog(mContext, "Enter reason for rejection");
                    }
                    else
                    {
                        if (HTTPUtils.isConnectionPossible(mContext))
                        {
                            dialog.dismiss();
                            ((PlaceOrderActivity)mContext).new Downloading(mContext, order_query_id, status_from_app, status_remarks).execute();
                        }
                        else
                        {
                            show_msg_Dialog(mContext, check_internet_connection);
                        }
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