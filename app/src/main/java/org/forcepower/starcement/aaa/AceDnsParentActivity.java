package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_dns_emp_code;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.util.PreferenceData.getLoginStatus;

import android.os.Bundle;

import androidx.appcompat.app.AppCompatActivity;

import org.forcepower.starcement.backgroundTask.TRANS_GetLogOut_Asynctask;

public class AceDnsParentActivity extends AppCompatActivity
{
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		try
		{
			if(getLoginStatus(this))
			{
				if(!(get_emp_or_customer_code(this).trim().length()>0 ||
						get_dns_emp_code(this).trim().length()>0))
				{
					new TRANS_GetLogOut_Asynctask(this).execute();
				}
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	@Override
	public void onBackPressed()
	{
		finish();
	}
}
