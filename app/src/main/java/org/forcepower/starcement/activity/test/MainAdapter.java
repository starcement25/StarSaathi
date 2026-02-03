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

public class MainAdapter extends RecyclerView.Adapter<MainAdapter.ViewHolder>{
    private Context context;
    private ArrayList<Main> list;

    public MainAdapter(Context context, ArrayList<Main> list) {
        this.context = context;
        this.list = list;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView title;
        RecyclerView childRecyclerView;
        public ViewHolder(View itemView) {
            super(itemView);
            title = itemView.findViewById(R.id.title);
            childRecyclerView = itemView.findViewById(R.id.childRecyclerView);
        }
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.test_main_layout, parent, false);
        return new ViewHolder(view);
    }



    @Override
    public void onBindViewHolder( ViewHolder holder, int position) {
        Main item = list.get(position);
        holder.title.setText(item.getDataSet());

        holder.title.setOnClickListener(v -> {
            item.setExpanded(!item.isExpanded());
            notifyItemChanged(position);
        });

        if (item.isExpanded()) {
            holder.childRecyclerView.setVisibility(View.VISIBLE);
            holder.childRecyclerView.setLayoutManager(new LinearLayoutManager(context));
            holder.childRecyclerView.setAdapter(new ChildAdapter(context, item.getObj()));
        } else {
            holder.childRecyclerView.setVisibility(View.GONE);
        }
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}