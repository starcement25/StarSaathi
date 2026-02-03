package org.forcepower.starcement.aaa;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.os.Bundle;
import android.os.Handler;
import android.view.View;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import org.forcepower.starcement.R;
import org.forcepower.starcement.util.HTTPUtils;

import static org.forcepower.starcement.SharedPrefData.get_dealer_submit_form;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.set_dealer_submit_form;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.survey_form;
import static org.forcepower.starcement.constants.Constants.survey_success_page;

public final class DealerFormWebViewActivity extends AppCompatActivity
{
    //Check git version
    boolean loadingFinished = true, redirect = false;
    Activity myActivity;
    WebView webView;
    String webview_url = "";
    TextView tv_no_data;
    ProgressDialog pDialog;
    @SuppressLint("SetJavaScriptEnabled")
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.webview_activity);
        try
        {
            myActivity = this;
            pDialog = new ProgressDialog(myActivity);

            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            tv_no_data = (TextView) findViewById(R.id.tv_no_data);
            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText(getIntent().getStringExtra("webview_caption"));

            webView = (WebView) findViewById(R.id.wv_FAQ);
            webView.getSettings().setJavaScriptEnabled(true);
            webView.getSettings().setDomStorageEnabled(true);
            webView.getSettings().setLoadsImagesAutomatically(true);
            webView.setScrollBarStyle(View.SCROLLBARS_INSIDE_OVERLAY);
            webview_url = getIntent().getStringExtra("webview_url");

            reload(null);

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    private void reload_msg()
    {
        new AlertDialog.Builder(myActivity, R.style.MyDialog)
                .setMessage(check_internet_connection)
                .setCancelable(false)
                .setPositiveButton("OK", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        dialog.dismiss();
                        reload(null);
                    }
                }).create().show();
    }

    public void reload(View view)
    {
        if (HTTPUtils.isConnectionPossible(myActivity))
        {
            webView.loadUrl(webview_url + "");
            webView.setWebViewClient(new WebViewClient() {

                @Override
                public boolean shouldOverrideUrlLoading(WebView view, String urlNewString) {
                    if (!loadingFinished) {
                        redirect = true;
                    }

                    loadingFinished = false;
                    view.loadUrl(urlNewString);
                    return true;
                }

                @Override
                public void onPageStarted(WebView view, String url, Bitmap facIcon) {
                    loadingFinished = false;
                    //SHOW LOADING
                    pDialog.show();
                }

                @Override
                public void onPageFinished(WebView view, String url) {
                    if (!redirect) {
                        loadingFinished = true;
                    }

                    if (loadingFinished && !redirect) {
                        //HIDE LOADING IT HAS FINISHED
                        if(pDialog != null) pDialog.dismiss();
                    } else {
                        redirect = false;
                    }

                    print_Log_d("star_payment ", "" + url);
                    if(url.contains(survey_success_page))
                    {
                        set_dealer_submit_form(myActivity, "YES");
                        new Handler().postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                // This method will be executed once the timer is over
                                // Start your app main activity
                                String msg = url.replace(survey_success_page, "");
                                msg = msg.replace("?the_message=", "");
                                msg = msg.replace("%20", " ");
                                msg_Dialog(msg);
                            }
                        }, 10);

                    }
//                    else if(!url.equalsIgnoreCase(survey_success_page))
//                    {
//                        new Handler().postDelayed(new Runnable() {
//                            @Override
//                            public void run() {
//                                // This method will be executed once the timer is over
//                                // Start your app main activity
//                                msg_back_Dialog("Please Try Again.");
//
//                            }
//                        }, 1000);
//                    }
                }

                @Override public void onReceivedError(WebView view, WebResourceRequest request,
                                                      WebResourceError error) {
                    super.onReceivedError(view, request, error);
                }
            });
        }
        else
        {
            reload_msg();
        }
    }

    private void msg_Dialog(String msg)
    {
        try
        {
            final AlertDialog.Builder AlertDG = new AlertDialog.Builder(myActivity, R.style.MyDialog);
            AlertDG.setMessage(msg + "");
            AlertDG.setPositiveButton("Ok", new DialogInterface.OnClickListener() {

                public void onClick(DialogInterface dialog, int which) {
                    Intent intent = new Intent(myActivity, MenuActivity.class);
                    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                    startActivity(intent);

                    finishAffinity();
                }
            });

            AlertDG.setCancelable(false);
            AlertDG.create().show();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    private void msg_back_Dialog(String msg)
    {
        try
        {
            final AlertDialog.Builder AlertDG = new AlertDialog.Builder(myActivity, R.style.MyDialog);
            AlertDG.setMessage(msg + "");

            AlertDG.setNegativeButton("Ok", new DialogInterface.OnClickListener() {

                public void onClick(DialogInterface dialog, int which) {
                    dialog.dismiss();
                }
            });

            AlertDG.setCancelable(false);
            AlertDG.create().show();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    @Override
    public void onBackPressed()
    {
        msg_back_Dialog("Please submit this form.");
    }
}
