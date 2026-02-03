package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.schemes;
import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;
import static org.forcepower.starcement.util.Utils.getCacheImagePath;

import android.Manifest;
import android.annotation.SuppressLint;
import android.annotation.TargetApi;
import android.app.DownloadManager;
import android.app.ProgressDialog;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.graphics.pdf.PdfRenderer;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.os.Handler;
import android.os.ParcelFileDescriptor;
import android.provider.Settings;
import android.text.Html;
import android.util.Log;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.result.ActivityResult;
import androidx.activity.result.ActivityResultCallback;
import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.annotation.NonNull;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.core.content.FileProvider;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import org.forcepower.starcement.BuildConfig;
import org.forcepower.starcement.R;
import org.forcepower.starcement.TouchImageView;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.OnSwipeTouchListener;
import org.json.JSONObject;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public final class PdfViewerActivity extends AceDnsParentActivity implements SwipeRefreshLayout.OnRefreshListener
{
    private Context mContext;
    private TouchImageView ImageView_community_resource_document;
    private final int minValuePdfPage = 0;
    private int maxValuePdfPage = 0;
    private int currentPdfPage = 0;
    private int totalPage = 1;
    private PdfRenderer renderer;
    private File pdfFile = new File("");
    private final int currentApiVersion = Build.VERSION.SDK_INT;
    private TextView pdfPageNumberTextView;
    private String filePath = "";
    private SwipeRefreshLayout chartListSwipeRefreshLayout;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_scheme);
        try
        {
            mContext=this;

            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText(getIntent().getStringExtra("scheme_header"));

            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });


            filePath = getIntent().getStringExtra("pdf_file_name");
            pdfFile = new File(get_direcory_path(mContext)+filePath);

            if (HTTPUtils.isConnectionPossible(mContext))
            {
                new getScheme_Asynctask(mContext).execute();
            }
            else
            {
                Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
            }
            chartListSwipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.chartListSwipeRefreshLayout);
            chartListSwipeRefreshLayout.setOnRefreshListener(this);
            chartListSwipeRefreshLayout.setColorSchemeResources(R.color.red, R.color.white, R.color.red, R.color.white);
            chartListSwipeRefreshLayout.setEnabled(false);

            //
            final LinearLayout llHeaderDetails = (LinearLayout) findViewById(R.id.llHeaderDetails);
            llHeaderDetails.setVisibility(View.VISIBLE);
            llHeaderDetails.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    download_share(null);
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    @Override
    public void onRefresh()
    {
        try
        {
            chartListSwipeRefreshLayout.setRefreshing(false);
            Handler mHandler = new Handler();
            mHandler.postDelayed(new Runnable()
            {
                public void run()
                {
                    if (HTTPUtils.isConnectionPossible(mContext))
                    {
                        new getScheme_Asynctask(mContext).execute();
                    }
                    else
                    {
//                        Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                    }
                }
            }, 10);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    private void ShowPdfPagesInImageView(int i) {
        try {
            renderer = new PdfRenderer(ParcelFileDescriptor.open(pdfFile, ParcelFileDescriptor.MODE_READ_ONLY));
            PdfRenderer.Page page = renderer.openPage(i);
            Bitmap mBitmap = Bitmap.createBitmap(page.getWidth(), page.getHeight(), Bitmap.Config.ARGB_4444);
            // say we render for showing on the screen
            page.render(mBitmap, null, null, PdfRenderer.Page.RENDER_MODE_FOR_DISPLAY);

            // do stuff with the bitmap
            ImageView_community_resource_document.setImageBitmap(mBitmap);
            // close the page
            page.close();

            // close the renderer
            renderer.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void readPdfFileAndShowOnImageView() {
        try {

            if (currentApiVersion >= Build.VERSION_CODES.LOLLIPOP) {
                renderer = new PdfRenderer(ParcelFileDescriptor.open(pdfFile, ParcelFileDescriptor.MODE_READ_ONLY));
                // let us just render all pages
                final int pageCount = renderer.getPageCount();
                if (pageCount > 0) {
                    maxValuePdfPage = pageCount - 1;
                    totalPage = pageCount;
                    pdfPageNumberTextView = (TextView) findViewById(R.id.pdfPageNumberTextView);
                    pdfPageNumberTextView.setVisibility(View.VISIBLE);
//                    pdfPageNumberTextView.setText(1+"/"+pageCount);
                    pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + 1 + "/<font color='#000000'>" + totalPage + "</font>"));
                }

                PdfRenderer.Page page = renderer.openPage(0);
                Bitmap mBitmap = Bitmap.createBitmap(page.getWidth(), page.getHeight(), Bitmap.Config.ARGB_4444);
                // say we render for showing on the screen
                page.render(mBitmap, null, null, PdfRenderer.Page.RENDER_MODE_FOR_DISPLAY);

                // do stuff with the bitmap
                ImageView_community_resource_document.setImageBitmap(mBitmap);
                // close the page
                page.close();

                // close the renderer
                renderer.close();
            } else {

                Intent target = new Intent(Intent.ACTION_VIEW);
//                target.setDataAndType(Uri.fromFile(pdfFile),"application/pdf");


                target.setDataAndType(Uri.fromFile(pdfFile), "application/pdf");

                target.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);

                Intent intent = Intent.createChooser(target, "Open File");
                try {
                    startActivity(intent);
                } catch (ActivityNotFoundException e) {
                    // Instruct the user to install a PDF reader here, or something
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onResume() {
        super.onResume();
    }
    
    public final class getScheme_Asynctask extends AsyncTaskCoroutine<String, String>
    {
        private Context mContext;
        private ProgressDialog mStepProgressDialog;
        public getScheme_Asynctask(final Context mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute()
        {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(mContext);
            mStepProgressDialog.setMessage("Please wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();
        }
        @Override
        public String doInBackground(final String... params)
        {
            String POST_result = "";
            try
            {
                filePath = getIntent().getStringExtra("pdf_file_name");
                File fileLocal = new File(get_direcory_path(mContext)+filePath);
                DownloadFile(getIntent().getStringExtra("pdf_download_url"), fileLocal);
            }
            catch (Exception e)
            {
                e.printStackTrace();
            }
            return POST_result;
        }
        @Override
        public void onPostExecute(String result)
        {
            super.onPostExecute(result);
            try
            {

                if(pdfFile.exists())
                {
                    loadFromLocal();
                }
            }
            catch (Exception e)
            {
                e.printStackTrace();
            }
            finally
            {
                if(mStepProgressDialog != null)
                    mStepProgressDialog.dismiss();
            }
        }
    }
    public void loadFromLocal()
    {
        try
        {

            ImageView_community_resource_document = (TouchImageView) findViewById(R.id.imgPdfRendered);
            if (currentApiVersion >= Build.VERSION_CODES.LOLLIPOP) {
                ImageView_community_resource_document.setOnTouchListener(new OnSwipeTouchListener(mContext) {
                    @Override
                    public void onSwipeLeft() {
                        if (maxValuePdfPage > 0) {
                            int currentPage = 1;
                            if (currentPdfPage == 0) {

                                ShowPdfPagesInImageView(currentPdfPage + 1);
                                currentPdfPage = currentPdfPage + 1;
                                currentPage = currentPdfPage + 1;
//                        pdfPageNumberTextView.setText(currentPage + "/" + totalPage);
                                pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#000000'>" + totalPage + "</font>"));
                            } else if (currentPdfPage < maxValuePdfPage) {
                                ShowPdfPagesInImageView(currentPdfPage + 1);
                                currentPdfPage = currentPdfPage + 1;
                                currentPage = currentPdfPage + 1;
//                        pdfPageNumberTextView.setText(currentPage + "/" + totalPage);
                                pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#000000'>" + totalPage + "</font>"));
                            }
                        }
                    }

                    @Override
                    public void onSwipeRight() {
                        if (maxValuePdfPage > 0) {
                            int currentPage = totalPage;
                            if (currentPdfPage == maxValuePdfPage) {
                                currentPage = currentPdfPage;
                                ShowPdfPagesInImageView(currentPdfPage - 1);
                                currentPdfPage = currentPdfPage - 1;
                                pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>"));
                            } else if (currentPdfPage > minValuePdfPage) {
                                currentPage = currentPdfPage;
                                ShowPdfPagesInImageView(currentPdfPage - 1);
                                currentPdfPage = currentPdfPage - 1;
//                        pdfPageNumberTextView.setText(currentPage + "/" + totalPage);
                                pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>"));
                            }
                        }
                        //Toast.makeText(CommunityResourceFileOpen.this, "swipped right", Toast.LENGTH_SHORT).show();

                    }
                });
            }
            readPdfFileAndShowOnImageView();

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public Boolean DownloadFile(final String fileURL, final File directory)
    {
        boolean isDownloadSuccess;
        print_Log_d("pdf_download_url ", fileURL);
        try
        {
            final FileOutputStream f = new FileOutputStream(directory);
            final URL u = new URL(fileURL);

            final HttpURLConnection c = (HttpURLConnection) u.openConnection();
            c.setRequestMethod("GET");
            c.setDoOutput(true);
            c.connect();

            final InputStream in = c.getInputStream();

            final byte[] buffer = new byte[1024];
            int len1 = 0;
            while ((len1 = in.read(buffer)) > 0) {
                f.write(buffer, 0, len1);
            }
            f.close();
            isDownloadSuccess = true;
        }
        catch (Exception e)
        {
            e.printStackTrace();
            isDownloadSuccess = false;
        }
        return isDownloadSuccess;
    }
    public void download_share(View view)
    {
        try
        {
            if(getIntent().hasExtra("download"))
            {
                startDownload();
            }
            else
            {

                Intent shareIntent=new Intent(Intent.ACTION_SEND);
                shareIntent.setData(Uri.parse("mailto:"));
                shareIntent.setType("text/plain");
                shareIntent.putExtra(Intent.EXTRA_SUBJECT, getResources().getString(R.string.app_name));
                shareIntent.putExtra(Intent.EXTRA_TEXT, "PDF...");
                shareIntent.addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION);
                shareIntent.putExtra(Intent.EXTRA_STREAM, getCacheImagePath(pdfFile.getAbsolutePath(), this));
                startActivity(Intent.createChooser(shareIntent,"Sharing"));

            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void startDownload()
    {
        try
        {
            final Uri uri= Uri.parse(getIntent().getStringExtra("pdf_download_url"));
            // Create request for android download manager
            final DownloadManager downloadManager = (DownloadManager) getSystemService(Context.DOWNLOAD_SERVICE);
            final DownloadManager.Request request = new DownloadManager.Request(uri);
            request.setAllowedNetworkTypes(DownloadManager.Request.NETWORK_WIFI |
                    DownloadManager.Request.NETWORK_MOBILE);

// set title and description
            request.setTitle(mContext.getResources().getString(R.string.app_name));
            request.setDescription("Pdf Download");

            request.allowScanningByMediaScanner();
            request.setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED);

//set the local destination for download file to a path within the application's external files directory
            request.setDestinationInExternalPublicDir(Environment.DIRECTORY_DOWNLOADS,pdfFile.getName());
            request.setMimeType("*/*");
            downloadManager.enqueue(request);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
}
