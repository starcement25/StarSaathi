package org.forcepower.starcement.aaa;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;

import androidx.annotation.NonNull;
import android.util.Log;

import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.messaging.FirebaseMessaging;

import org.forcepower.starcement.BuildConfig;
import org.forcepower.starcement.R;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.util.Utils;
import java.io.File;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_firebase_token;
import static org.forcepower.starcement.SharedPrefData.set_firebase_token;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.dateString;
import static org.forcepower.starcement.constants.Constants.nickName;
import static org.forcepower.starcement.constants.Constants.redirection;
import static org.forcepower.starcement.constants.Constants.updateChecked;
import static org.forcepower.starcement.util.PreferenceData.getLoginStatus;
import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;
import static org.forcepower.starcement.util.PreferenceData.setLoginStatus;
import static org.forcepower.starcement.util.PreferenceData.set_direcory_path;


@SuppressLint("CustomSplashScreen")
public final class SplashActivity extends AceDnsParentActivity
{
	private Boolean isSDPresent = false;
	private AceDnsDatabase mAceDnsDatabase;

	private Activity myActivity;
	private File file = new File("");

	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_splash);
		myActivity = this;
		try
		{
			file = new File(getExternalCacheDir(), nickName);
			Log.d("Star_Pakage_Name ", getPackageName()+"");
			updateChecked = false;
			nextScreen();
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		finally
		{
			redirection = "";
		}
	}

	private void nextScreen()
	{
		try
		{
			set_direcory_path(myActivity, file.getAbsolutePath()+"/");

//			if(BuildConfig.DEBUG)
//				set_direcory_path(myActivity, "/storage/emulated/0/Android/media/org.forcepower.starcement/cache/START/");

			print_Log_d("direcory_path_75 ", get_direcory_path(myActivity));
			new Handler().postDelayed(new Runnable() {

				/*
				 * Showing splash screen with a timer. This will be useful when you
				 * want to show case your app logo / company
				 */

				@Override
				public void run() {
					// Read the sqlite file
					File dbFile = new File(get_direcory_path(myActivity) + "Star.db");
					if (dbFile.exists())
					{
						isSDPresent = true;
						mAceDnsDatabase = new AceDnsDatabase(myActivity);
						Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
						if (Constants.employeeDetailObject != null)
						{
							Constants.deviceId = Constants.employeeDetailObject.getDeviceID().trim();
						}
					}
					else
					{
						// If sqlite file not exist create the file
						try
						{
							String dirName = get_direcory_path(myActivity) ;
							File dir = new File(dirName);
							if(!dir.exists())
							{
								dir.mkdirs();
							}
							String fileName = get_direcory_path(myActivity)+"Star.db";
							File f = new File(fileName);
							if(!f.exists()){
								f.createNewFile();
							}
							Constants.deviceId = Utils.getDeviceId(myActivity);
							isSDPresent = true;
						}
						catch (Exception e)
						{
							isSDPresent = false;
							Utils.directOutsideTheApplication(myActivity,
									"Please contact your admin", false);
						}
					}
					
					//
					if (isSDPresent)
					{
						boolean crash = false;
						try
						{
							if(getLoginStatus(myActivity) && !get_emp_or_customer_code(myActivity).matches(""))
							{
								dateString = new SimpleDateFormat("yyyyMMdd").format(Calendar.getInstance().getTime());
								Utils.InitialiseSETUPTableData(myActivity);

								startActivity(new Intent(myActivity, MenuActivity.class));

							}
							else
							{
								startActivity(new Intent(myActivity, LoginActivity.class));

							}
							crash = false;
						}
						catch (Exception e)
						{
							crash = true;
							e.printStackTrace();
						}
						finally
						{
							if(crash)
							{
								setLoginStatus(myActivity, false);
								startActivity(new Intent(myActivity,LoginActivity.class));
							}
						}
						//
						finish();
					}
					else
					{
						Utils.directOutsideTheApplication(myActivity, "Please Contact Admin.", false);
					}
					finishAffinity();
				}
			}, 3000);

		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		finally
		{
			// CustomExceptionHandler class is called at the time of unchecked exception occured
			Thread.setDefaultUncaughtExceptionHandler(new CustomExceptionHandler(myActivity));

			FirebaseMessaging.getInstance().getToken()
					.addOnCompleteListener(new OnCompleteListener<String>() {
						@Override
						public void onComplete(@NonNull Task<String> task) {
							if (!task.isSuccessful()) {
								print_Log_d("Fetching FCM registration token failed", task.getException().toString());
								set_firebase_token(myActivity, "dummy");
								return;
							}
							// Get new FCM registration token
							set_firebase_token(myActivity, task.getResult());
						}
					});
		}
	}
}
