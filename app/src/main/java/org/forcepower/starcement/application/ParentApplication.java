package org.forcepower.starcement.application;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.util.PreferenceData.getLoginStatus;

import android.app.Application;
import android.content.Context;

import androidx.multidex.MultiDex;

import com.google.firebase.FirebaseApp;
//import com.google.firebase.crashlytics.FirebaseCrashlytics;

/**
 * Created by amit paul on 05/12/2016.
 * it is created to add support of multidex
 */
public final class ParentApplication extends Application {
    static ParentApplication  mInstance;

    @Override
    protected void attachBaseContext(Context base)
    {
        super.attachBaseContext(base);
        MultiDex.install(this);
    }
    @Override
    public void onCreate()
    {
        super.onCreate();
        mInstance = this;

        FirebaseApp.initializeApp(this);
//        FirebaseCrashlytics.getInstance().setCrashlyticsCollectionEnabled(true);

        if(getLoginStatus(this))
        {
            if(get_user_type(this).equalsIgnoreCase("broker"))
            {
//                FirebaseCrashlytics.getInstance().setUserId("broker_"+get_selected_dealer_sap_code(this));
            }
            else
            {
//                FirebaseCrashlytics.getInstance().setUserId(get_user_type(this)+"_"+get_dealer_id(this));
            }
        }
        else
        {
//            FirebaseCrashlytics.getInstance().setUserId("Not_Logged_User");
        }
    }


    public static synchronized ParentApplication getInstance()
    {
        return mInstance;
    }
}
