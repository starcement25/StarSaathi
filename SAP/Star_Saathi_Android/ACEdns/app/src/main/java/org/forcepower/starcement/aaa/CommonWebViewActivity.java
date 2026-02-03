package org.forcepower.starcement.aaa;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Bitmap;
import android.net.MailTo;
import android.net.Uri;
import android.os.Bundle;
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

public final class CommonWebViewActivity extends AppCompatActivity
{
    //Check git version
    boolean loadingFinished = true, redirect = false;
    Activity myActivity;
    WebView webView;
    String webview_url = "";
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
            pDialog.show();

            webView.loadUrl(webview_url + "");
            print_Log_d("webview_url ", webview_url);
            webView.setWebViewClient(new WebViewClient() {

                @Override
                public boolean shouldOverrideUrlLoading(WebView view, String urlNewString) {
                    if (!loadingFinished) {
//                        redirect = true;
                    }

                    loadingFinished = false;

                    if(urlNewString.startsWith("mailto:"))
                    {
                        MailTo mt = MailTo.parse(urlNewString);
                        Intent i = new Intent(Intent.ACTION_SEND);
                        i.setType("text/plain");
                        i.putExtra(Intent.EXTRA_EMAIL, new String[]{mt.getTo()});
                        i.putExtra(Intent.EXTRA_SUBJECT, mt.getSubject());
                        i.putExtra(Intent.EXTRA_CC, mt.getCc());
                        i.putExtra(Intent.EXTRA_TEXT, mt.getBody());
                        startActivity(i);
                        view.reload();
                        return true;
                    }
                    else if(urlNewString.startsWith("tel"))
                    {
                        startActivity(new Intent(Intent.ACTION_DIAL, Uri.parse(urlNewString)));
                        return true;
                    }
                    else if(urlNewString.toLowerCase().endsWith(".pdf"))
                    {
                        urlNewString = "https://docs.google.com/viewer?url=" + urlNewString;
                    }
                    //SHOW LOADING
//                    loadDialog();
                    view.loadUrl(urlNewString.replace(" ", "%20"));
                    print_Log_d("WebViewActivity_urlNewString ", urlNewString + "");

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
    @Override
    public void onBackPressed()
    {
        if(webView!= null && webView.canGoBack())
        {
            webView.goBack();
        }
        else
        {
            if(webView!= null)
            {
                webView.destroy();
                webView = null;
            }
            finish();
        }
    }
}
