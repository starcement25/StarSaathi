package org.forcepower.starcement.aaa;

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

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;

import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.messaging.FirebaseMessaging;

import org.forcepower.starcement.BuildConfig;
import org.forcepower.starcement.R;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONObject;

import java.io.File;
import java.text.SimpleDateFormat;
import java.util.Calendar;

@SuppressLint("CustomSplashScreen")
public final class SplashActivity extends AceDnsParentActivity {
    private Boolean isSDPresent = false;
    private AceDnsDatabase mAceDnsDatabase;

    private Activity myActivity;
    private File file = new File("");

    //new popup
    LinearLayout maintenancePopupLayout, btnDesignLayout, openWebsiteButton;
    TextView popupMessage;
    String link = "";
    //new popup

    private static final int REQUEST_NOTIFICATION_PERMISSION = 1001;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);
        init();
        myActivity = this;
        requestNotificationPermissionIfNeeded();
    }

    @Override
    public void onRequestPermissionsResult(int requestCode,
                                           @NonNull String[] permissions,
                                           @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        if (requestCode == REQUEST_NOTIFICATION_PERMISSION) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                // Permission granted
                callNextFunction();
            } else {
                // Permission denied
                Toast.makeText(this, "Notification permission denied", Toast.LENGTH_SHORT).show();
                callNextFunction();
            }
        }
    }

    private void requestNotificationPermissionIfNeeded() {
        // Only for Android 13+ (API 33+)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(this,
                    Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED) {
                // Permission not granted → request it
                ActivityCompat.requestPermissions(this,
                        new String[]{Manifest.permission.POST_NOTIFICATIONS},
                        REQUEST_NOTIFICATION_PERMISSION);
            } else {
                // Permission already granted
                callNextFunction();
            }
        } else {
            // Permission not needed for < Android 13
            callNextFunction();
        }
    }

    private void callNextFunction(){
        try {
            file = new File(getExternalCacheDir(), nickName);
            Log.d("Star_Pakage_Name ", getPackageName() + "");
            updateChecked = false;
            nextScreen();
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            redirection = "";
        }
    }

    private void nextScreen() {
        try {
            set_direcory_path(myActivity, file.getAbsolutePath() + "/");
            print_Log_d("direcory_path_75 ", get_direcory_path(myActivity));
            new Handler().postDelayed(() -> {
                File dbFile = new File(get_direcory_path(myActivity) + "Star.db");
                if (dbFile.exists()) {
                    isSDPresent = true;
                    mAceDnsDatabase = new AceDnsDatabase(myActivity);
                    Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                    if (Constants.employeeDetailObject != null) {
                        Constants.deviceId = Constants.employeeDetailObject.getDeviceID().trim();
                    }
                } else {
                    try {
                        String dirName = get_direcory_path(myActivity);
                        File dir = new File(dirName);
                        if (!dir.exists()) {
                            dir.mkdirs();
                        }
                        String fileName = get_direcory_path(myActivity) + "Star.db";
                        File f = new File(fileName);
                        if (!f.exists()) {
                            f.createNewFile();
                        }
                        Constants.deviceId = Utils.getDeviceId(myActivity);
                        isSDPresent = true;
                    } catch (Exception e) {
                        isSDPresent = false;
                        Utils.directOutsideTheApplication(myActivity,
                                "Please contact your admin", false);
                    }
                }
                if (isSDPresent) {
//                    new TRANS_CheckingData_AsyncTask(SplashActivity.this).execute();
                    gotoLoginPage();
                } else {
                    Utils.directOutsideTheApplication(myActivity, "Please Contact Admin.", false);
                }

            }, 3000);

        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            Thread.setDefaultUncaughtExceptionHandler(new CustomExceptionHandler(myActivity));
            FirebaseMessaging.getInstance().getToken().addOnCompleteListener(task -> {
                if (!task.isSuccessful()) {
                    print_Log_d("Fetching FCM registration token failed", task.getException().toString());
                    set_firebase_token(myActivity, "dummy");
                    return;
                }
                set_firebase_token(myActivity, task.getResult());
            });

            print_Log_d("Refreshed_token_s ", get_firebase_token(myActivity));
        }
    }

    // new code

    public class TRANS_CheckingData_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_CheckingData_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = Constants.baseURL+"saathi_app_api_dowmtime.php";
                    POST_result = HTTPUtils.getDataByHTTP_GET(SplashActivity.this, url).trim();
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                JSONObject obj = new JSONObject(result);
                if (obj.getString("app_status").trim().equalsIgnoreCase("start")) {
//                    gotoLoginPage();
                    new TRANS_CheckingAppVersion_AsyncTask(mContext).execute();
                } else {
                    maintenancePopupLayout.setVisibility(View.VISIBLE);
                    if (obj.getString("is_link_available").trim().equalsIgnoreCase("n")) {
                        btnDesignLayout.setVisibility(View.GONE);
                    } else {
                        link = obj.getString("body_link").trim();
                        btnDesignLayout.setVisibility(View.VISIBLE);
                    }
                    popupMessage.setText(obj.getString("body_message").trim());
                }
            } catch (Exception e) {
                Utils.showToast(mContext, "Something wrong please contact to admin.");
            }
        }
    }

    public class TRANS_CheckingAppVersion_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_CheckingAppVersion_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = Constants.show_latest_app_version;
                    Log.d("TAG", "_DOWNLOAD_ doInBackground: "+url);
                    POST_result = HTTPUtils.getDataByHTTP_GET(SplashActivity.this, url).trim();
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                Log.d("TAG", "_DOWNLOAD_ onPostExecute: "+result);
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").trim().equalsIgnoreCase("YES")) {
                    Log.d("TAG", "_DOWNLOAD_ onPostExecute: "+mContext.getPackageManager().getPackageInfo(mContext.getPackageName(), 0).versionName);
                   if(obj.getString("android_app_version").equalsIgnoreCase(mContext.getPackageManager().getPackageInfo(mContext.getPackageName(), 0).versionName)){
                       gotoLoginPage();
                   }else{
                       try {
                           mContext.startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=org.forcepower.starcement")));
                       } catch (Exception anfe) {
                           mContext.startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/apps/details?id=" + "org.forcepower.starcement")));
                       }
                   }
                } else {
                    gotoLoginPage();
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ onPostExecute: "+e.getMessage());
                Utils.showToast(mContext, "Something wrong please contact to admin.");
            }
        }
    }

    private void gotoLoginPage() {
        boolean crash = false;
        try {
            if (getLoginStatus(myActivity) && !get_emp_or_customer_code(myActivity).matches("")) {
                dateString = new SimpleDateFormat("yyyyMMdd").format(Calendar.getInstance().getTime());
                Utils.InitialiseSETUPTableData(myActivity);
                startActivity(new Intent(myActivity, MenuActivity.class));
            } else {
                startActivity(new Intent(myActivity, LoginActivity.class));
            }
            crash = false;
        } catch (Exception e) {
            crash = true;
            e.printStackTrace();
        } finally {
            if (crash) {
                setLoginStatus(myActivity, false);
                startActivity(new Intent(myActivity, LoginActivity.class));
            }
        }
        finish();
    }

    private void init() {
        maintenancePopupLayout = findViewById(R.id.maintenancePopupLayout);
        btnDesignLayout = findViewById(R.id.btnDesignLayout);
        openWebsiteButton = findViewById(R.id.openWebsiteButton);
        popupMessage = findViewById(R.id.popupMessage);

        maintenancePopupLayout.setVisibility(View.GONE);
        btnDesignLayout.setVisibility(View.GONE);
        openWebsiteButton.setOnClickListener(view -> {
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(link));
            try {
                startActivity(intent);
            } catch (Exception e) {
                Toast.makeText(SplashActivity.this, "No browser app found", Toast.LENGTH_SHORT).show();
            }
        });
    }
}
