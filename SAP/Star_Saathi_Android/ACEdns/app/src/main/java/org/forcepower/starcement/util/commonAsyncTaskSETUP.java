package org.forcepower.starcement.util;

import android.content.Context;
import android.graphics.Bitmap;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;
import org.forcepower.starcement.bean.RoutePlanDetails;
import org.forcepower.starcement.constants.AceDnsWebServiceURL;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.parser.MarketFeedbackDetailsXMLParser;
import org.forcepower.starcement.parser.MenuDetailsXMLParsing;
import org.forcepower.starcement.parser.OrderFormDetailsXMLParsing;
import org.forcepower.starcement.parser.ProductDetailsXMLParsing;
import org.forcepower.starcement.parser.RoutePlanDetailsXMLParsing;
import org.forcepower.starcement.parser.SaudaFormDetailsXMLParser;
import org.forcepower.starcement.parser.SelfAppraisalDetailsXMLParser;
import org.forcepower.starcement.parser.SurveyFormDetailsXMLParser;
import org.forcepower.starcement.parser.UserDetailsXMLParsing;

import java.io.ByteArrayOutputStream;
import java.util.ArrayList;

import static org.forcepower.starcement.SharedPrefData.get_user_type;

/**
 * Created by Intellij Amiyo  on 20-07-2017.
 * Please follow standard Java coding conventions.
 * http://source.android.com/source/code-style.html
 */
public final class commonAsyncTaskSETUP
{

    Context mContext;
    String httpResponse = "";
    ArrayList<NameValuePair> nameValuePairs;
    AceDnsDatabase mAceDnsDatabase;
    String lastUpdate = "2014-06-09 18:19:20";
    String dwnldDictTime = "2014-06-09 18:19:20"; // Just to know the format
    String mIsInCremental="",status="";


    public commonAsyncTaskSETUP(Context context, String getNAME)
    {
        this.mContext = context;
        this.status = getNAME;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        lastUpdate = mAceDnsDatabase.getlastDownloadTime("menu_details");
        dwnldDictTime = mAceDnsDatabase.getlastDownloadTime("download_dictionary");

    }
}
