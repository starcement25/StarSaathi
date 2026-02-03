package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.DatePicker;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.fragment.app.Fragment;
import androidx.viewpager2.widget.ViewPager2;

import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.google.android.material.tabs.TabLayout;
import com.google.android.material.tabs.TabLayoutMediator;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.ViewPagerFragmentAdapter;
import org.forcepower.starcement.fragments.SubDealerLiftingApproved;
import org.forcepower.starcement.fragments.SubDealerLiftingPending;
import org.forcepower.starcement.fragments.SubDealerLiftingRejected;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.MonthYearPickerDialog;
import org.forcepower.starcement.util.Utils;

import java.util.ArrayList;

public final class NewSubDealerLiftingActivity extends AceDnsParentActivity
{
	private Activity mContext;
	private ViewPager2 view_pager2;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_sub_dealer_lifting);
		try
		{
			mContext = this;

			final FloatingActionButton floating_add_lifting = (FloatingActionButton) findViewById(R.id.floating_add_lifting);
			floating_add_lifting.setVisibility(View.VISIBLE);
			floating_add_lifting.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					Intent intent = new Intent(mContext, AddLiftingActivity.class);
					startActivity(intent);
				}
			});

			final TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("LIFTING HISTORY");
			final LinearLayout llHeaderDetails = (LinearLayout) findViewById(R.id.llHeaderDetails);
			llHeaderDetails.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					try
					{
						final MonthYearPickerDialog pd = new MonthYearPickerDialog();
						pd.setListener(new DatePickerDialog.OnDateSetListener() {
							@Override
							public void onDateSet(DatePicker view, int selectedYear, int selectedMonth, int selectedDay) {
								if (HTTPUtils.isConnectionPossible(mContext))
								{
									final ArrayList<Fragment> fragments = new ArrayList<>();
									final String header_txt = Utils.changeDateFormat( "yyyy-MM", "MMM, yyyy", selectedYear + "-" + selectedMonth);
									tvHeaderText.setText("LIFTING HISTORY ("+header_txt+")");
									final String year_month = Utils.changeDateFormat( "yyyy-MM", "yyyy-MM", selectedYear + "-" + selectedMonth);

									fragments.add(SubDealerLiftingPending.newInstance(year_month));
									fragments.add(SubDealerLiftingApproved.newInstance(year_month));
									fragments.add(SubDealerLiftingRejected.newInstance(year_month));

									final ViewPagerFragmentAdapter adapter = new ViewPagerFragmentAdapter(NewSubDealerLiftingActivity.this, fragments);
									view_pager2.setAdapter(adapter);
								}
								else
								{
									show_msg_alert(mContext,check_internet_connection, false);
								}
							}
						});
						//
						pd.show(getSupportFragmentManager(), "MonthYearPickerDialog");
					}
					catch (Exception e)
					{
						e.printStackTrace();
					}
				}
			});

			final TabLayout tabLayout = (TabLayout) findViewById(R.id.sliding_tabs);

			view_pager2 = (ViewPager2) findViewById(R.id.view_pager2);
			final String[] arrData = {"Pending", "Approved", "Rejected"};

			final ArrayList<Fragment> fragments = new ArrayList<>();
			fragments.add(SubDealerLiftingPending.newInstance(""));
			fragments.add(SubDealerLiftingApproved.newInstance(""));
			fragments.add(SubDealerLiftingRejected.newInstance(""));

			final ViewPagerFragmentAdapter adapter = new ViewPagerFragmentAdapter(this, fragments);
			view_pager2.setAdapter(adapter);

			new TabLayoutMediator(tabLayout, view_pager2,
					(tab, position) -> {
						// Set tab titles here
						tab.setText(arrData[position]);
					}).attach();

			final ImageView ivShowAllocate = (ImageView) findViewById(R.id.ivShowAllocate);
			ivShowAllocate.setVisibility(View.GONE);

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

	@Override
	public void onBackPressed() {
		if(view_pager2.getCurrentItem() != 0)
		{
			view_pager2.setCurrentItem(0);
		}
		else
		{
			finish();
		}
	}
}
