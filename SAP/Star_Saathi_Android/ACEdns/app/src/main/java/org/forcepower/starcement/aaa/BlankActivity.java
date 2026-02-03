package org.forcepower.starcement.aaa;

import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageView;
import android.widget.TextView;
import org.forcepower.starcement.R;

public final class BlankActivity extends AceDnsParentActivity
{
	private Activity mContext;
	
	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_blank);
		try
		{
			mContext = this;

			final TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			if(getIntent().hasExtra("header_text"))
				tvHeaderText.setText(getIntent().getStringExtra("header_text"));

			final ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
}
