package org.forcepower.starcement.activity.kismet_ki_bori.adapter;

import android.content.Context;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.activity.kismet_ki_bori.dataset.CouponDataSet;

import java.util.ArrayList;

public class AddCouponCodeAdapter extends RecyclerView.Adapter<AddCouponCodeAdapter.ViewHolder> {
    private Context context;
    private ArrayList<CouponDataSet> list;
    private OnActionClickListener listener;

    public interface OnActionClickListener {
        void onDeleteClicked(CouponDataSet item, int position);
    }

    public AddCouponCodeAdapter(Context context, ArrayList<CouponDataSet> list, OnActionClickListener listener) {
        this.context = context;
        this.list = list;
        this.listener = listener;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        EditText couponCodeEditText;
        ImageView removeIcon;
        TextView couponText;

        public ViewHolder(View itemView) {
            super(itemView);
            couponCodeEditText = itemView.findViewById(R.id.couponCodeEditText);
            removeIcon = itemView.findViewById(R.id.removeIcon);
            couponText = itemView.findViewById(R.id.couponText);
        }
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_coupon_layout, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {
        CouponDataSet item = list.get(position);
        if(position==0){
            holder.removeIcon.setVisibility(View.GONE);
        }
        holder.couponText.setText("COUPON "+(position+1));
        holder.removeIcon.setOnClickListener(v -> {
            if (listener != null) {
                listener.onDeleteClicked(item, position);
            }
        });
        holder.couponCodeEditText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                item.setCouponCode(s.toString());
            }
            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}
            @Override
            public void afterTextChanged(Editable s) {}
        });
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}
