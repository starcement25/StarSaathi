package org.forcepower.starcement.aaa;

import android.content.Context;
import android.graphics.Color;
import android.graphics.drawable.GradientDrawable;
import android.os.Bundle;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.fragment.app.Fragment;
import androidx.viewpager.widget.ViewPager;

import org.forcepower.starcement.ImageSlideFragmentOne;
import org.forcepower.starcement.PageAdapterFirstSlide;
import org.forcepower.starcement.R;

import java.util.ArrayList;
import java.util.List;

public final class HelpActivity  extends AceDnsParentActivity
{
	Context mContext;
	public static int[] intDrawable = {
		R.drawable.help_login,
		R.drawable.help_otp,
		R.drawable.help_mainmenu,
		R.drawable.help_ledger,
		R.drawable.help_order,
		R.drawable.help_trackorder,
		R.drawable.help_performance
	};
	private TextView[] dots;
	LinearLayout ll_dots;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
//
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_help_new);

		mContext = this;
		try
		{
			ViewPager viewPagerOne = (ViewPager) findViewById(R.id.sliderOne_Viewpager);
			ll_dots = (LinearLayout) findViewById(R.id.ll_dots);

			List<Fragment> fragmentList = new ArrayList<>();

			for(int i=0; i<intDrawable.length; i++)
			{
				ImageSlideFragmentOne imageSlideFragmentObj = new ImageSlideFragmentOne();
				fragmentList.add(imageSlideFragmentObj.newInstance(i, intDrawable[i])); // Parameter
			}
			viewPagerOne.setAdapter(new PageAdapterFirstSlide(getSupportFragmentManager(), fragmentList));
			addBottomDots(0);
			ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					finish();
				}
			});
			viewPagerOne.addOnPageChangeListener(new ViewPager.OnPageChangeListener() {
				@Override
				public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {

				}

				@Override
				public void onPageSelected(int position) {
					addBottomDots(position);
				}

				@Override
				public void onPageScrollStateChanged(int state) {

				}
			});
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	private void addBottomDots(int currentPage) {
		try
		{
			dots = new TextView[intDrawable.length];

			ll_dots.removeAllViews();
			for (int i = 0; i < dots.length; i++)
			{
				dots[i] = new TextView(this);
				dots[i].setGravity(Gravity.CENTER);
				bg_(dots[i], getResources().getColor(R.color.red), Color.WHITE, "solid");
				LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(16, 16);
				params.setMargins(2,0,2,0);
				dots[i].setLayoutParams(params);

				ll_dots.addView(dots[i]);
			}

			if (dots.length > 0)
			{
				bg_(dots[currentPage], Color.WHITE, Color.WHITE, "non_solid");
				LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(20, 20);
				params.setMargins(4,0,4,0);
				dots[currentPage].setLayoutParams(params);
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void bg_(TextView textView, int bgColor, int strockColor, String type) {
		try
		{
			int backgroundColor = bgColor;
			int strokeColor = strockColor;
			int strokeSize = 0;
			if(type.equalsIgnoreCase("solid"))
			{
				strokeSize = 2;
			}
			GradientDrawable drawable = new GradientDrawable(GradientDrawable.Orientation.TOP_BOTTOM, new int[]{backgroundColor, backgroundColor});
			drawable.setShape(GradientDrawable.OVAL);
			drawable.setStroke(strokeSize, strokeColor);
			textView.setBackground(drawable);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
}
