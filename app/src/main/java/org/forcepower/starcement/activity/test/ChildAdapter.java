package org.forcepower.starcement.activity.test;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;

import java.util.ArrayList;

public class ChildAdapter extends RecyclerView.Adapter<ChildAdapter.ViewHolder> {
    private Context context;
    private ArrayList<Child> list;

    public ChildAdapter(Context context, ArrayList<Child> list) {
        this.context = context;
        this.list = list;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView childTitle;
        RecyclerView childRecyclerView;
        public ViewHolder(View itemView) {
            super(itemView);
            childTitle = itemView.findViewById(R.id.childTitle);
            childRecyclerView = itemView.findViewById(R.id.childRecyclerView);
        }
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.test_main_layout, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {
        Child item = list.get(position);
        holder.childTitle.setText(item.getData());

        holder.childTitle.setOnClickListener(v -> {
            item.setExpanded(!item.isExpanded());
            notifyItemChanged(position);
        });

        if (item.isExpanded()) {
            holder.childRecyclerView.setVisibility(View.VISIBLE);
            holder.childRecyclerView.setLayoutManager(new LinearLayoutManager(context));
            holder.childRecyclerView.setAdapter(new SubChildAdapter(context, item.getObj()));
        } else {
            holder.childRecyclerView.setVisibility(View.GONE);
        }
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}