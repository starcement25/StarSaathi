package org.forcepower.starcement.aaa;

import android.app.Activity;
import android.os.Bundle;
import android.text.Html;
import android.text.method.ScrollingMovementMethod;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageView;
import android.widget.TextView;

import org.forcepower.starcement.R;

public final class DetailsActivity extends AceDnsParentActivity
{
	private Activity mContext;
	private TextView tv_details;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_details);
		try
		{
			mContext = this;

			tv_details = (TextView) findViewById(R.id.tv_details);
			final TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);

			final ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					finish();
				}
			});

			final String details_data = getIntent().getStringExtra("details_text")+"";
			tv_details.setText(details_data);
			tv_details.setMovementMethod(new ScrollingMovementMethod());
			final String tv_header = getIntent().getStringExtra("header_text")+"";
			tvHeaderText.setText(Html.fromHtml(tv_header));
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
}
