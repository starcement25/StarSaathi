package org.forcepower.starcement.activity.test;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;

import java.util.ArrayList;

public class SubChildAdapter extends RecyclerView.Adapter<SubChildAdapter.ViewHolder> {
    private Context context;
    private ArrayList<SubChild> list;

    public SubChildAdapter(Context context, ArrayList<SubChild> list) {
        this.context = context;
        this.list = list;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView subTitle;
        public ViewHolder(View itemView) {
            super(itemView);
            subTitle = itemView.findViewById(R.id.childTitle);
        }
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.test_main_layout, parent, false);
        return new SubChildAdapter.ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {
        holder.subTitle.setText(list.get(position).getTitle());
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}