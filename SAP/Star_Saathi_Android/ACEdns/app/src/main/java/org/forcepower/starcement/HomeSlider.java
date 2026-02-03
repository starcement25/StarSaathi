package org.forcepower.starcement;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.Utils_.ApiRes;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.DEFAULT_TIMEOUT;
import static org.forcepower.starcement.constants.Constants.acedns_star_slider;
import static org.forcepower.starcement.constants.Constants.redirection;
import static org.forcepower.starcement.constants.Constants.updateChecked;

import android.app.Activity;
import android.graphics.Color;
import android.graphics.drawable.GradientDrawable;
import android.os.Handler;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import androidx.viewpager2.widget.ViewPager2;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.forcepower.starcement.aaa.MenuActivity;
import org.forcepower.starcement.bean.SliderInfo;
import org.forcepower.starcement.util.Utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Timer;
import java.util.TimerTask;

import cz.msebera.android.httpclient.Header;

public final class HomeSlider
{
    private ViewsSliderAdapter mAdapter;
    private TextView[] dots;
    private Activity mContext;
    private ViewPager2 view_pager2;
    private LinearLayout ll_dots;
    private ArrayList<SliderInfo> slider_list_g = new ArrayList<>();
    private String redirect = "";
    private int v_page_position = 0;

    public HomeSlider(final Activity mContext_, final ViewPager2 view_pager2_,
                      final LinearLayout ll_dots_, final String redirect_)
    {
        try
        {
            this.mContext = mContext_;
            this.view_pager2 = view_pager2_;
            this.ll_dots = ll_dots_;
            this.redirect = redirect_;

            init();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    private void init()
    {
        Utils.changeProgressDialogMsg(mContext, "Updating slider...");
        update_slider();
    }
    ViewPager2.OnPageChangeCallback pageChangeCallback = new ViewPager2.OnPageChangeCallback() {
        @Override
        public void onPageSelected(final int position) {
            super.onPageSelected(position);
            v_page_position = position;
            addBottomDots(position, slider_list_g);
        }
    };
    private void addBottomDots(final int currentPage, final ArrayList<SliderInfo> slider_list)
    {
        try
        {
            dots = new TextView[slider_list.size()];

            ll_dots.removeAllViews();
            for (int i = 0; i < dots.length; i++)
            {
                dots[i] = new TextView(mContext);
                dots[i].setGravity(Gravity.CENTER);
                bg_(dots[i], Color.WHITE, Color.WHITE, "non_solid");
                final LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(16, 16);
                params.setMargins(2,0,2,0);
                dots[i].setLayoutParams(params);

                ll_dots.addView(dots[i]);
            }

            if (dots.length > 0)
            {
                bg_(dots[currentPage], mContext.getResources().getColor(R.color.red), Color.WHITE, "solid");
                final LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(20, 20);
                params.setMargins(4,0,4,0);
                dots[currentPage].setLayoutParams(params);
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void bg_(final TextView textView, final int bgColor, final int strockColor, final String type)
    {
        try
        {
            int strokeSize = 0;
            if(type.equalsIgnoreCase("solid"))
            {
                strokeSize = 2;
            }
            final GradientDrawable drawable = new GradientDrawable(GradientDrawable.Orientation.TOP_BOTTOM, new int[]{bgColor, bgColor});
            drawable.setShape(GradientDrawable.OVAL);
            drawable.setStroke(strokeSize, strockColor);
            textView.setBackground(drawable);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public final class ViewsSliderAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder>
    {
        private Activity mContext;
        private ArrayList<SliderInfo> slider_list = new ArrayList<>();
        public ViewsSliderAdapter(Activity mContext_, ArrayList<SliderInfo> slider_list_)
        {
            this.mContext = mContext_;
            this.slider_list = slider_list_;
        }

        @NonNull
        @Override
        public RecyclerView.ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
            final View view = LayoutInflater.from(mContext).inflate(
                    R.layout.list_item_slider, parent, false);
            return new SliderViewHolder(view);
        }

        @Override
        public void onBindViewHolder(@NonNull RecyclerView.ViewHolder holder, int pos)
        {
            try
            {
                final int position = holder.getLayoutPosition();
                Glide.with(mContext)
                        .load(slider_list.get(position).get_image_link())
                        .dontAnimate()
                        .diskCacheStrategy(DiskCacheStrategy.NONE)
                        .error(R.drawable.default_)
                        .placeholder(R.drawable.default_)
                        .into(((SliderViewHolder)holder).iv_home_slider);
            }
            catch (Exception e)
            {
                e.printStackTrace();
            }
        }

        @Override
        public int getItemViewType(int position) {
            return slider_list.get(position).hashCode();
        }

        @Override
        public int getItemCount() {
            return slider_list.size();
        }

        public final class SliderViewHolder extends RecyclerView.ViewHolder {
            public ImageView iv_home_slider;

            public SliderViewHolder(final View view) {
                super(view);
                iv_home_slider = (ImageView) view.findViewById(R.id.iv_home_slider);
            }
        }
    }

    public void update_slider()
    {
        try
        {
            final RequestParams params = new RequestParams();
            params.put("the_id", get_emp_or_customer_code(mContext));
            print_Log_d("acedns_star_slider ", params.toString() + " ");

            final AsyncHttpClient client = new AsyncHttpClient();
            client.setTimeout(DEFAULT_TIMEOUT);

//        HttpsAsyncHttpClient(client);
            client.post(acedns_star_slider, params, new AsyncHttpResponseHandler()
            {
                @Override
                public void onSuccess(int statusCode, Header[] headers, byte[] responseBody)
                {
                    String str = new String(responseBody);
                    try
                    {
                        str = ApiRes(str);
                        final JSONObject reader = new JSONObject(str);
//                        Toast.makeText(mContext, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
                        print_Log_d("acedns_star_slider ", reader.toString() + " ");
                        slider_list_g.clear();
                        if(reader.optString("process_status").equalsIgnoreCase("yes"))
                        {
                            final String start_slider_data = reader.optString("start_slider_data");
                            final JSONArray jsonArray = new JSONArray(start_slider_data);
                            for(int i=0; i<jsonArray.length(); i++)
                            {
                                final SliderInfo sliderInfo = new SliderInfo();
                                sliderInfo.set_image_link(jsonArray.getString(i));
                                slider_list_g.add(sliderInfo);
                            }
                        }
                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                    finally
                    {
                        Utils.cancelProgressDialog();

                        if(redirection.equalsIgnoreCase("OTPActivity"))
                        {
                            ((MenuActivity)mContext).ivSync(null);
                        }
                        else
                        {
                            ((MenuActivity)mContext).ivRefresh(null);
                        }

                        mAdapter = new ViewsSliderAdapter(mContext, slider_list_g);
                        view_pager2.setAdapter(mAdapter);
                        view_pager2.registerOnPageChangeCallback(pageChangeCallback);
                        // adding bottom dots
                        addBottomDots(0, slider_list_g);

                        updateChecked = true;

                        if(!redirect.matches(""))
                            ((MenuActivity)mContext).doOnItemClickJob(redirect);

                        //view_pager_auto_scroll
                        final Handler v_handler = new Handler();
                        final Runnable v_update = new Runnable() {
                            public void run() {
                                if (v_page_position == slider_list_g.size() - 1)
                                {
                                    v_page_position = 0;
                                }
                                else
                                {
                                    v_page_position = v_page_position + 1;
                                }
                                view_pager2.setCurrentItem(v_page_position, true);
                            }
                        };

                        new Timer().schedule(new TimerTask() {

                            @Override
                            public void run() {
                                v_handler.post(v_update);
                            }
                        }, 100, 5000);
                    }
                }

                @Override
                public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                    Utils.cancelProgressDialog();
                }


            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
}
