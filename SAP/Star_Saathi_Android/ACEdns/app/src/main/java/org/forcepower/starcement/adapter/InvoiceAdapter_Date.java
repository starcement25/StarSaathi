package org.forcepower.starcement.adapter;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.aaa.PdfViewerActivity;
import org.forcepower.starcement.bean.InvoiceModel;

import java.util.ArrayList;

/**
 * Created by @Amitabha
 */
public final class InvoiceAdapter_Date extends RecyclerView.Adapter
{
    private final int VIEW_ITEM = 1;
    private final int VIEW_PROG = 0;
    private Activity mContext;
    private ArrayList<InvoiceModel> orderHistoryClassList = new ArrayList<>();

    @SuppressLint("NotifyDataSetChanged")
    public void setFilter(final ArrayList<InvoiceModel> images)
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
        private final TextView tv_invoice_no, tv_date, tv_invoice_link, tv_delivery_no,
                tv_sale_order_no, tv_app_order_no,tv_product_name, tv_invoice_qty,
                tv_destination, tv_truck_no;

        public MyViewHolder(final View convertView)
        {
            super(convertView);

            tv_invoice_no = (TextView) convertView.findViewById(R.id.tv_invoice_no);
            tv_date = (TextView) convertView.findViewById(R.id.tv_date);
            tv_invoice_link = (TextView) convertView.findViewById(R.id.tv_invoice_link);
            tv_delivery_no = (TextView) convertView.findViewById(R.id.tv_delivery_no);
            tv_sale_order_no = (TextView) convertView.findViewById(R.id.tv_sale_order_no);
            tv_app_order_no = (TextView) convertView.findViewById(R.id.tv_app_order_no);
            tv_product_name = (TextView) convertView.findViewById(R.id.tv_product_name);
            tv_invoice_qty = (TextView) convertView.findViewById(R.id.tv_invoice_qty);
            tv_destination = (TextView) convertView.findViewById(R.id.tv_destination);
            tv_truck_no = (TextView) convertView.findViewById(R.id.tv_truck_no);
        }
    }


    public InvoiceAdapter_Date(final Activity mContext, final ArrayList<InvoiceModel> orderHistoryClassList)
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
                    R.layout.list_item_invoice, parent, false);

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
                ((MyViewHolder) holder).tv_invoice_no.setText(orderHistoryClassList.get(position).getInvoice_no());
                ((MyViewHolder) holder).tv_date.setText(orderHistoryClassList.get(position).getInvoice_date());
                ((MyViewHolder) holder).tv_delivery_no.setText(orderHistoryClassList.get(position).getDelivery_no());
                ((MyViewHolder) holder).tv_sale_order_no.setText(orderHistoryClassList.get(position).getSale_order_no());
                ((MyViewHolder) holder).tv_app_order_no.setText(orderHistoryClassList.get(position).getApp_order_no());
                ((MyViewHolder) holder).tv_product_name.setText(orderHistoryClassList.get(position).getProduct_name());
                ((MyViewHolder) holder).tv_invoice_qty.setText(orderHistoryClassList.get(position).getInvoice_qty());
                ((MyViewHolder) holder).tv_destination.setText(orderHistoryClassList.get(position).getDestination());
                ((MyViewHolder) holder).tv_truck_no.setText(orderHistoryClassList.get(position).getTruck_no());

                //
                if(orderHistoryClassList.get(position).get_invoice_link().matches("") ||
                        orderHistoryClassList.get(position).get_invoice_link().equalsIgnoreCase("null"))
                {
                    ((MyViewHolder) holder).tv_invoice_link.setVisibility(View.INVISIBLE);
                }
                else
                {
                    ((MyViewHolder) holder).tv_invoice_link.setVisibility(View.VISIBLE);
                }

                ((MyViewHolder) holder).tv_invoice_link.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        Intent intent = new Intent(mContext, PdfViewerActivity.class);
//                        intent.putExtra("pdf_file_name", orderHistoryClassList.get(position).getItem15());
//                        intent.putExtra("scheme_header", "PDF");
//                        intent.putExtra("invoice_number", "invoice_"+orderHistoryClassList.get(position).getItem6()+".pdf");

                        intent.putExtra("pdf_file_name", "invoice_"+orderHistoryClassList.get(position).getInvoice_no()+"_"+System.currentTimeMillis()+".pdf");
                        intent.putExtra("pdf_download_url",  orderHistoryClassList.get(position).get_invoice_link() );
                        intent.putExtra("scheme_header", "PDF");
                        intent.putExtra("download", "download");

                        mContext.startActivity(intent);
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
        if(orderHistoryClassList != null && orderHistoryClassList.size() > 0)
        {
            return orderHistoryClassList.size();
        }
        else
        {
            return 0;
        }
    }
}
