package org.forcepower.starcement.util;

import android.telephony.PhoneStateListener;
import android.telephony.TelephonyManager;

public final class PhoneStateChangeListener  extends PhoneStateListener {
    public static boolean ringing = false;
    
    @Override
    public void onCallStateChanged(int state, String incomingNumber) {
        switch(state){
            case TelephonyManager.CALL_STATE_RINGING:
            	 ringing = true;
                 break;
            case TelephonyManager.CALL_STATE_OFFHOOK:
                 break;
            case TelephonyManager.CALL_STATE_IDLE:
                 break;
        }
    }
}