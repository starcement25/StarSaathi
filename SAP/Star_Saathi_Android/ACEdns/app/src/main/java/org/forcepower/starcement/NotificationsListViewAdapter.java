package org.forcepower.starcement;

import android.app.Activity;
import android.app.Dialog;
import android.content.Intent;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import org.forcepower.starcement.aaa.NotificationDetailsActivity;
import org.forcepower.starcement.aaa.NotificationPdfActivity;
import org.forcepower.starcement.database.DatabaseHelperSqlite;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Locale;

/**
 * A custom adapter
 * @author @Amitabha2715
 */
public final class NotificationsListViewAdapter extends BaseAdapter {
    private Activity activity;
    List<commonDatabaseHelper>all;
    DatabaseHelperSqlite notiDB;
    public NotificationsListViewAdapter(Activity activity_, List<commonDatabaseHelper> all) {
        this.activity = activity_;
        this.all = all;
        notiDB = new DatabaseHelperSqlite(activity_);
    }


    public int getCount() {
        return all.size();
    }


    public Object getItem(int index) {
        // Not used, so no point in retrieving it.
        return null;
    }


    public long getItemId(int index) {
        return index;
    }


    public View getView(final int index, View view, ViewGroup viewGroup) {
        LinearLayout layout;
        if (view instanceof RelativeLayout) {
            layout = (LinearLayout) view;
        } else {
            LayoutInflater factory = LayoutInflater.from(activity);
            layout = (LinearLayout) factory.inflate(R.layout.list_item_noti_list, viewGroup, false);
        }
        ((TextView) layout.findViewById(R.id.tvNotiTitle)).setText(all.get(index).getItem1());
        ((TextView) layout.findViewById(R.id.tvNotiMsg)).setText(all.get(index).getItem2());
        if(all.get(index).getItem5().equalsIgnoreCase("read"))
        {
            ((LinearLayout) layout.findViewById(R.id.llNotiBackground)).setBackgroundColor(Color.parseColor("#dbdbdb"));
        }
        else
        {
            ((LinearLayout) layout.findViewById(R.id.llNotiBackground)).setBackgroundColor(Color.parseColor("#FFFFFF"));
        }

        Glide.with(activity)
                .load(all.get(index).getItem3())
                .diskCacheStrategy(DiskCacheStrategy.NONE)
                .error(R.drawable.default_)
                .placeholder(R.drawable.default_)
                .into(((ImageView) layout.findViewById(R.id.ivNotiImage)));



        return layout;
    }

    public static String convertDate(String date)
    {
        try
        {
            SimpleDateFormat spf=new SimpleDateFormat("MM/dd/yyyy hh:mm:ss aaa", Locale.getDefault());
            Date newDate=spf.parse(date);
            spf= new SimpleDateFormat("MM/dd/yyyy", Locale.getDefault());
            date = spf.format(newDate);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return date;
    }
    public void setFilter(List<commonDatabaseHelper>all_)
    {
        this.all = new ArrayList<>();
        this.all = all_;
        notifyDataSetChanged();
    }
}

