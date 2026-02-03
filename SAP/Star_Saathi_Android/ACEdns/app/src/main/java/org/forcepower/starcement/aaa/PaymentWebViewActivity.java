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

import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.the_cancel_url;
import static org.forcepower.starcement.constants.Constants.the_cancel_url_ne;
import static org.forcepower.starcement.constants.Constants.the_success_url;
import static org.forcepower.starcement.constants.Constants.the_success_url_ne;

public final class PaymentWebViewActivity extends AppCompatActivity
{
    //Check git version
    private boolean loadingFinished = true, redirect = false;
    private Activity myActivity;
    private WebView webView;
    private String webview_url = "";
    private ProgressDialog pDialog;

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

            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText(getIntent().getStringExtra("webview_caption"));

            webView = (WebView) findViewById(R.id.wv_FAQ);
            webView.getSettings().setJavaScriptEnabled(true);
            webView.getSettings().setDomStorageEnabled(true);
            webView.getSettings().setLoadsImagesAutomatically(true);
            webView.setScrollBarStyle(View.SCROLLBARS_INSIDE_OVERLAY);
            webview_url = getIntent().getStringExtra("webview_url");
            //
            reload(null);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
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
                    if(url.equalsIgnoreCase(the_success_url) ||
                            url.equalsIgnoreCase(the_success_url_ne))
                    {
                        new Handler().postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                // This method will be executed once the timer is over
                                // Start your app main activity
                                msg_Dialog("Your Transaction is successful. Payment will reflect in the ledger within 24 hours");
                            }
                        }, 1000);
//                        runOnUiThread(new Runnable() {
//                            @Override
//                            public void run() {
//                                // Do something on UiThread
//                            }
//                        });
                    }
                    else if(url.equalsIgnoreCase(the_cancel_url) ||
                            url.equalsIgnoreCase(the_cancel_url_ne))
                    {
                        new Handler().postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                // This method will be executed once the timer is over
                                // Start your app main activity
                                msg_Dialog("Transaction Cancelled. Please Try Again.");

                            }
                        }, 1000);
                    }
                }

                @Override public void onReceivedError(WebView view, WebResourceRequest request,
                                                      WebResourceError error) {
                    super.onReceivedError(view, request, error);
                }
            });
        }
        else
        {
            Toast.makeText(myActivity, check_internet_connection, Toast.LENGTH_SHORT).show();
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
                    finish();
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
            AlertDG.setPositiveButton("Yes", new DialogInterface.OnClickListener() {

                public void onClick(DialogInterface dialog, int which) {
                    Intent intent = new Intent(myActivity, MenuActivity.class);
                    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                    startActivity(intent);
                    finish();
                }
            });
            AlertDG.setNegativeButton("Continue", new DialogInterface.OnClickListener() {

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
//        msg_back_Dialog("Do You Want To Cancel The Transaction?");
        Intent intent = new Intent(myActivity, MenuActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        startActivity(intent);
        finish();
    }
}
