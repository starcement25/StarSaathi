package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.schemes;
import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;

import android.annotation.TargetApi;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.pdf.PdfRenderer;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.ParcelFileDescriptor;
import android.text.Html;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.github.mikephil.charting.charts.LineChart;
import com.github.mikephil.charting.components.AxisBase;
import com.github.mikephil.charting.components.Legend;
import com.github.mikephil.charting.components.XAxis;
import com.github.mikephil.charting.components.YAxis;
import com.github.mikephil.charting.data.Entry;
import com.github.mikephil.charting.data.LineData;
import com.github.mikephil.charting.data.LineDataSet;
//import com.github.mikephil.charting.formatter.ValueFormatter;

import org.forcepower.starcement.R;
import org.forcepower.starcement.TouchImageView;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.OnSwipeTouchListener;
import org.json.JSONArray;
import org.json.JSONObject;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

public final class SchemePdfActivity extends AceDnsParentActivity implements SwipeRefreshLayout.OnRefreshListener {

    private static final String TAG = "SchemePdfActivity";

    private Context mContext;
    private TouchImageView ImageView_community_resource_document;
    private int minValuePdfPage = 0;
    private int maxValuePdfPage = 0;
    private int currentPdfPage = 0;
    private int totalPage = 1;
    private PdfRenderer renderer;
    private File pdfFile = new File("");
    private int currentApiVersion = Build.VERSION.SDK_INT;
    private TextView pdfPageNumberTextView;
    private String filePath = "";
    private String pdfUrl = "";
    private SwipeRefreshLayout chartListSwipeRefreshLayout;

    // -------- GRAPH FIELDS (from Intent) --------
    private String slabType = "";        // "single" / "multiple"
    private int applicableQty = 0;       // for single
    private String slabDetailsJson = ""; // full slab_details
    private String achievementsJson = "";// achievements[]

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_scheme);

        try {
            mContext = this;

            TextView tvHeaderText = findViewById(R.id.tvHeaderText);
            String schemeHeader = safeString(getIntent().getStringExtra("scheme_name"));
            if (schemeHeader.isEmpty()) {
                schemeHeader = safeString(getIntent().getStringExtra("scheme_id"));
            }
            tvHeaderText.setText(schemeHeader);

            ImageView ivHeaderBack = findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });

            // Header info button → graph
            LinearLayout llHeaderDetails = findViewById(R.id.llHeaderDetails);
            llHeaderDetails.setVisibility(View.VISIBLE);
            TextView tvHeaderInfo = findViewById(R.id.tvHeaderInfo);
            if (tvHeaderInfo != null) {
                tvHeaderInfo.setText("i");
            }

            // read graph extras
            slabType = safeString(getIntent().getStringExtra("slab_type"));
            applicableQty = getIntent().getIntExtra("applicable_qty", 0);
            slabDetailsJson = safeString(getIntent().getStringExtra("slab_details_json"));
            achievementsJson = safeString(getIntent().getStringExtra("achievements_json"));

            llHeaderDetails.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
//                    showSchemePopup();
                }
            });

            // PDF handling (url may be invalid for now)
            pdfUrl = getIntent().getStringExtra("scheme_pdf_url");
            if (pdfUrl != null && !pdfUrl.isEmpty()) {
                int lastSlash = pdfUrl.lastIndexOf('/');
                if (lastSlash != -1 && lastSlash < pdfUrl.length() - 1) {
                    filePath = pdfUrl.substring(lastSlash + 1);
                } else {
                    filePath = pdfUrl;
                }
            } else {
                filePath = getIntent().getStringExtra("pdf_file_name");
                if (filePath == null) filePath = "";
            }

            pdfFile = new File(get_direcory_path(mContext) + filePath);
            Log.d(TAG, "Local PDF path: " + pdfFile);

            if (pdfFile.exists() && !filePath.matches("")) {
                loadFromLocal();
            } else {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    new getScheme_Asynctask(mContext).execute();
                } else {
                    Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                }
            }

            chartListSwipeRefreshLayout = findViewById(R.id.chartListSwipeRefreshLayout);
            chartListSwipeRefreshLayout.setOnRefreshListener(this);
            chartListSwipeRefreshLayout.setColorSchemeResources(R.color.red, R.color.white, R.color.red, R.color.white);
            chartListSwipeRefreshLayout.setEnabled(false);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private String safeString(String s) {
        return s == null ? "" : s;
    }

    @Override
    public void onRefresh() {
        try {
            chartListSwipeRefreshLayout.setRefreshing(false);
            Handler mHandler = new Handler();
            mHandler.postDelayed(new Runnable() {
                public void run() {
                    if (HTTPUtils.isConnectionPossible(mContext)) {
                        new getScheme_Asynctask(mContext).execute();
                    }
                }
            }, 10);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @TargetApi(Build.VERSION_CODES.LOLLIPOP)
    private void ShowPdfPagesInImageView(int i) {
        try {
            renderer = new PdfRenderer(ParcelFileDescriptor.open(pdfFile, ParcelFileDescriptor.MODE_READ_ONLY));
            PdfRenderer.Page page = renderer.openPage(i);
            Bitmap mBitmap = Bitmap.createBitmap(page.getWidth(), page.getHeight(), Bitmap.Config.ARGB_4444);
            page.render(mBitmap, null, null, PdfRenderer.Page.RENDER_MODE_FOR_DISPLAY);
            ImageView_community_resource_document.setImageBitmap(mBitmap);
            page.close();
            renderer.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    // ---------------------- GRAPH POPUP ---------------------- //
//    private void showSchemePopup() {
//        try {
//            final Dialog dialog = new Dialog(mContext);
//            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
//            dialog.setContentView(R.layout.dialog_scheme_popup);
//            dialog.setCancelable(true);
//
//            TextView tvTitle = dialog.findViewById(R.id.tvDialogTitle);
//            TextView tvDesc = dialog.findViewById(R.id.tvDialogDesc);
//            tvTitle.setText("Scheme Overview");
//
//            LineChart lineChart = dialog.findViewById(R.id.lineChart);
//            Button btnOk = dialog.findViewById(R.id.btnDialogOk);
//
//            List<Entry> entries = new ArrayList<>();
//            final ArrayList<String> xLabels = new ArrayList<>();
//            double maxLiftingQty = 0.0;
//
//            // ----- Build data from achievementsJson -----
//            try {
//                if (!achievementsJson.isEmpty()) {
//                    JSONArray achArray = new JSONArray(achievementsJson);
//                    int size = achArray.length();
//
//                    if (size == 1) {
//                        // Single point case: fake a starting point at 0
//                        JSONObject a = achArray.getJSONObject(0);
//                        String date = a.optString("date");
//                        double liftingQty = a.optDouble("lifting_qty", 0.0);
//
//                        entries.add(new Entry(0, 0f));
//                        xLabels.add("");
//
//                        entries.add(new Entry(1, (float) liftingQty));
//                        xLabels.add(formatDateLabel(date));
//
//                        maxLiftingQty = liftingQty;
//                    } else {
//                        for (int i = 0; i < size; i++) {
//                            JSONObject a = achArray.getJSONObject(i);
//                            String date = a.optString("date");
//                            double liftingQty = a.optDouble("lifting_qty", 0.0);
//
//                            xLabels.add(formatDateLabel(date));      // "dd-MM"
//                            entries.add(new Entry(i, (float) liftingQty));
//
//                            if (liftingQty > maxLiftingQty) {
//                                maxLiftingQty = liftingQty;
//                            }
//                        }
//                    }
//                }
//            } catch (Exception e) {
//                e.printStackTrace();
//            }
//
//            if (entries.isEmpty()) {
//                tvDesc.setText("No achievement data available. Showing sample trend.");
//                entries.add(new Entry(0, 0));
//                entries.add(new Entry(1, 60));
//                xLabels.add("");
//                xLabels.add("Sample");
//                maxLiftingQty = 60;
//            } else {
//                tvDesc.setText("Your scheme achievement trend.");
//            }
//
//            // ---- Y-axis logic ----
//            final ArrayList<Float> yLevelsSingle = new ArrayList<>();
//            final ArrayList<Integer> yLevelsMultiple = new ArrayList<>();
//
//            double axisMax = maxLiftingQty;
//            boolean isSingle = "single".equalsIgnoreCase(slabType);
//            boolean isMultiple = "multiple".equalsIgnoreCase(slabType);
//
//            if (isSingle && applicableQty > 0) {
//                if (axisMax <= 0) axisMax = applicableQty;
//                int stepCount = (int) Math.ceil(axisMax / applicableQty);
//                axisMax = stepCount * applicableQty;
//
//                yLevelsSingle.add(0f);
//                for (int s = 1; s <= stepCount; s++) {
//                    yLevelsSingle.add((float) (s * applicableQty));
//                }
//            } else if (isMultiple) {
//                // Collect exact lifting_max values from slabs: 0, 99, 199, 299, 399...
//                double maxFromSlabs = 0.0;
//                yLevelsMultiple.clear();
//                yLevelsMultiple.add(0); // start at 0
//                try {
//                    if (!slabDetailsJson.isEmpty()) {
//                        JSONObject slabObj = new JSONObject(slabDetailsJson);
//                        JSONArray slabsArr = slabObj.optJSONArray("slabs");
//                        if (slabsArr != null) {
//                            for (int i = 0; i < slabsArr.length(); i++) {
//                                JSONObject slab = slabsArr.getJSONObject(i);
//                                int max = (int) Math.round(slab.optDouble("lifting_max", 0.0));
//                                if (max > 0) {
//                                    yLevelsMultiple.add(max);
//                                    if (max > maxFromSlabs) {
//                                        maxFromSlabs = max;
//                                    }
//                                }
//                            }
//                        }
//                    }
//                } catch (Exception e) {
//                    e.printStackTrace();
//                }
//
//                // sort just in case
//                Collections.sort(yLevelsMultiple);
//
//                if (maxFromSlabs > axisMax) {
//                    axisMax = maxFromSlabs;
//                }
//                if (axisMax <= 0) axisMax = 10;
//            } else {
//                if (axisMax <= 0) axisMax = 10;
//            }
//
//            final float axisMaxF = (float) axisMax;
//
//            // ---- DataSet styling ----
//            LineDataSet dataSet = new LineDataSet(entries, "Lifting Qty");
//            dataSet.setLineWidth(2.5f);
//            dataSet.setCircleRadius(4f);
//            dataSet.setCircleHoleRadius(2f);
//            dataSet.setDrawCircleHole(true);
//            dataSet.setDrawValues(false);
//            dataSet.setMode(entries.size() > 2
//                    ? LineDataSet.Mode.CUBIC_BEZIER
//                    : LineDataSet.Mode.LINEAR);
//            dataSet.setColor(getResources().getColor(R.color.red, null));
//            dataSet.setCircleColor(getResources().getColor(R.color.red, null));
//
//            LineData lineData = new LineData(dataSet);
//            lineChart.setData(lineData);
//
//            // ---- Chart base config: no scroll / zoom ----
//            lineChart.getDescription().setEnabled(false);
//            lineChart.setTouchEnabled(false);
//            lineChart.setDragEnabled(false);
//            lineChart.setScaleEnabled(false);
//            lineChart.setPinchZoom(false);
//            lineChart.setDoubleTapToZoomEnabled(false);
//            lineChart.setExtraTopOffset(16f);
//            lineChart.setExtraBottomOffset(16f);
//
//            Legend legend = lineChart.getLegend();
//            legend.setEnabled(true);
//
//            // ---- X-axis: labels under each point, with space after last ----
//            XAxis x = lineChart.getXAxis();
//            x.setPosition(XAxis.XAxisPosition.BOTTOM);
//            x.setDrawAxisLine(true);
//            x.setDrawGridLines(false);
//            x.setGranularity(1f);
//            x.setGranularityEnabled(true);        // <--- IMPORTANT
//            x.setAxisMinimum(0f);
//            float lastIndex = entries.size() - 1;
//            x.setAxisMaximum(lastIndex + 0.6f);   // extra space to the right
//            x.setLabelCount(xLabels.size(), false); // don't force exact positions
//            x.setValueFormatter(new ValueFormatter() {
//                @Override
//                public String getAxisLabel(float value, AxisBase axis) {
//                    int index = Math.round(value);
//                    if (index >= 0 && index < xLabels.size()) {
//                        return xLabels.get(index);
//                    }
//                    return "";
//                }
//            });
//
//            // ---- Y-axis ----
//            YAxis leftAxis = lineChart.getAxisLeft();
//            leftAxis.setAxisMinimum(0f);
//            leftAxis.setAxisMaximum(axisMaxF);
//            leftAxis.setDrawAxisLine(true);
//            leftAxis.setDrawGridLines(true);
//
//            if (isSingle && applicableQty > 0 && !yLevelsSingle.isEmpty()) {
//                // Single: 0, 60, 120...
//                leftAxis.setLabelCount(yLevelsSingle.size(), true);
//                leftAxis.setGranularity(applicableQty);
//                leftAxis.setValueFormatter(new ValueFormatter() {
//                    @Override
//                    public String getFormattedValue(float value) {
//                        for (Float lvl : yLevelsSingle) {
//                            if (Math.abs(value - lvl) < applicableQty / 4f) {
//                                return String.valueOf(Math.round(lvl));
//                            }
//                        }
//                        return "";
//                    }
//                });
//            } else if (isMultiple && !yLevelsMultiple.isEmpty()) {
//                // Multiple: 0, 99, 199, 299, 399...
//                leftAxis.setLabelCount(yLevelsMultiple.size(), true);
//                leftAxis.setGranularity(1f);
//                leftAxis.setValueFormatter(new ValueFormatter() {
//                    @Override
//                    public String getFormattedValue(float value) {
//                        if (value <= 0.5f) {
//                            return String.valueOf(yLevelsMultiple.get(0));
//                        }
//                        float ratio = value / axisMaxF; // 0..1
//                        int idx = Math.round(ratio * (yLevelsMultiple.size() - 1));
//                        if (idx < 0) idx = 0;
//                        if (idx >= yLevelsMultiple.size()) idx = yLevelsMultiple.size() - 1;
//                        return String.valueOf(yLevelsMultiple.get(idx));
//                    }
//                });
//            } else {
//                leftAxis.setLabelCount(4, true);
//                leftAxis.setGranularity(1f);
//            }
//
//            lineChart.getAxisRight().setEnabled(false);
//            lineChart.invalidate();
//
//            btnOk.setOnClickListener(new View.OnClickListener() {
//                @Override
//                public void onClick(View v) {
//                    dialog.dismiss();
//                }
//            });
//
//            dialog.show();
//            Window window = dialog.getWindow();
//            if (window != null) {
//                window.setLayout(LinearLayout.LayoutParams.MATCH_PARENT,
//                        LinearLayout.LayoutParams.WRAP_CONTENT);
//            }
//        } catch (Exception e) {
//            e.printStackTrace();
//        }
//    }

    // "2025-12-03" -> "03-12"
    private String formatDateLabel(String apiDate) {
        try {
            if (apiDate != null && apiDate.length() >= 10) {
                String day = apiDate.substring(8, 10);
                String month = apiDate.substring(5, 7);
                return day + "-" + month;
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return apiDate != null ? apiDate : "";
    }

    @TargetApi(Build.VERSION_CODES.LOLLIPOP)
    private void readPdfFileAndShowOnImageView() {
        try {
            if (currentApiVersion >= Build.VERSION_CODES.LOLLIPOP) {
                renderer = new PdfRenderer(ParcelFileDescriptor.open(pdfFile, ParcelFileDescriptor.MODE_READ_ONLY));
                final int pageCount = renderer.getPageCount();
                if (pageCount > 0) {
                    maxValuePdfPage = pageCount - 1;
                    totalPage = pageCount;
                    pdfPageNumberTextView = findViewById(R.id.pdfPageNumberTextView);
                    pdfPageNumberTextView.setVisibility(View.VISIBLE);
                    pdfPageNumberTextView.setText(
                            Html.fromHtml("Pages: " + 1 + "/<font color='#000000'>" + totalPage + "</font>")
                    );
                }

                PdfRenderer.Page page = renderer.openPage(0);
                Bitmap mBitmap = Bitmap.createBitmap(page.getWidth(), page.getHeight(), Bitmap.Config.ARGB_4444);
                page.render(mBitmap, null, null, PdfRenderer.Page.RENDER_MODE_FOR_DISPLAY);
                ImageView_community_resource_document.setImageBitmap(mBitmap);
                page.close();
                renderer.close();
            } else {
                Intent target = new Intent(Intent.ACTION_VIEW);
                target.setDataAndType(Uri.fromFile(pdfFile), "application/pdf");
                target.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);

                Intent intent = Intent.createChooser(target, "Open File");
                try {
                    startActivity(intent);
                } catch (ActivityNotFoundException e) {
                    e.printStackTrace();
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

    // ----------- PDF download task ----------- //
    public final class getScheme_Asynctask extends AsyncTaskCoroutine<String, String> {
        Context mContext;
        ProgressDialog mStepProgressDialog;
        JSONObject jo = new JSONObject();

        public getScheme_Asynctask(Context mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(mContext);
            mStepProgressDialog.setMessage("Please wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();
        }

        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            try {
                if (filePath == null || filePath.isEmpty()) {
                    return POST_result;
                }
                File fileLocal = new File(get_direcory_path(mContext) + filePath);
                DownloadFile(schemes + filePath, fileLocal);
            } catch (Exception e) {
                e.printStackTrace();
            }
            return POST_result;
        }

        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                pdfFile = new File(get_direcory_path(mContext) + filePath);
                if (pdfFile.exists()) {
                    loadFromLocal();
                }
            } catch (Exception e) {
                e.printStackTrace();
            } finally {
                if (mStepProgressDialog != null)
                    mStepProgressDialog.dismiss();
            }
        }
    }

    public void loadFromLocal() {
        try {
            ImageView_community_resource_document = findViewById(R.id.imgPdfRendered);
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
                                pdfPageNumberTextView.setText(
                                        Html.fromHtml("Pages: " + currentPage + "/<font color='#000000'>" + totalPage + "</font>")
                                );
                            } else if (currentPdfPage < maxValuePdfPage) {
                                ShowPdfPagesInImageView(currentPdfPage + 1);
                                currentPdfPage = currentPdfPage + 1;
                                currentPage = currentPdfPage + 1;
                                pdfPageNumberTextView.setText(
                                        Html.fromHtml("Pages: " + currentPage + "/<font color='#000000'>" + totalPage + "</font>")
                                );
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
                                pdfPageNumberTextView.setText(
                                        Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>")
                                );
                            } else if (currentPdfPage > minValuePdfPage) {
                                currentPage = currentPdfPage;
                                ShowPdfPagesInImageView(currentPdfPage - 1);
                                currentPdfPage = currentPdfPage - 1;
                                pdfPageNumberTextView.setText(
                                        Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>")
                                );
                            }
                        }
                    }
                });
            }
            readPdfFileAndShowOnImageView();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public Boolean DownloadFile(String fileURL, File directory) {
        Boolean isDownloadSuccess;
        try {
            FileOutputStream f = new FileOutputStream(directory);
            URL u = new URL(fileURL);
            HttpURLConnection c = (HttpURLConnection) u.openConnection();
            c.setRequestMethod("GET");
            c.setDoOutput(true);
            c.connect();

            InputStream in = c.getInputStream();
            byte[] buffer = new byte[1024];
            int len1;
            while ((len1 = in.read(buffer)) > 0) {
                f.write(buffer, 0, len1);
            }
            f.close();
            isDownloadSuccess = true;
        } catch (Exception e) {
            e.printStackTrace();
            isDownloadSuccess = false;
        }
        return isDownloadSuccess;
    }
}
