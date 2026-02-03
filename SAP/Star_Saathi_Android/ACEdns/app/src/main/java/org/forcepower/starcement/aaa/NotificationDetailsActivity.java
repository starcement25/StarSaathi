package org.forcepower.starcement.aaa;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import org.forcepower.starcement.R;

public final class NotificationDetailsActivity extends AceDnsParentActivity
{
    Context mContext;
    String title = "", notice_message = "", image_url ="";
    TextView tvNotiTitle, tvNotiMessage;
    ImageView imNotice;
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_notification_details);

        mContext=this;

        TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
        tvHeaderText.setText("NOTIFICATIONS");

        ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
        ivHeaderBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
        LinearLayout llHeaderDetails = (LinearLayout) findViewById(R.id.llHeaderDetails);
        llHeaderDetails.setVisibility(View.INVISIBLE);

       try
       {
           tvNotiTitle = (TextView) findViewById(R.id.tvNotiTitle);
           tvNotiMessage = (TextView) findViewById(R.id.tvNotiMessage);
           imNotice = (ImageView) findViewById(R.id.imNotice);


           Bundle extras = getIntent().getExtras();
           if (extras != null)
           {
               title = extras.getString("title")+"";
               notice_message = extras.getString("message")+"";
               tvNotiTitle.setText(title);
               tvNotiMessage.setText(notice_message);



               image_url = extras.getString("image")+"";
               if(!image_url.matches(""))
               {
                   Glide.with(this)
                           .load(image_url)
                           .diskCacheStrategy(DiskCacheStrategy.NONE)
                           .placeholder(R.drawable.default_)
                           .error(R.drawable.default_)
                           .into(imNotice);
               }
           }
       }

       catch (Exception e)
       {
           e.printStackTrace();
       }
    }
}
