package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.constants.Constants.dealer_schemes;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.GridView;
// or ListView if you are using that instead
// import android.widget.ListView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.SchemeListAdapter;
import org.forcepower.starcement.bean.SchemeModel;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;

public class SchemeListPdfActivity extends Activity {

    private GridView gvSchemes; // or ListView lvSchemes;
    private SchemeListAdapter adapter;
    private ArrayList<SchemeModel> schemeList = new ArrayList<>();

    // TODO: use dynamic dealerId from login/sharedPref if needed
    private static final String DEALER_ID = "1000001497";
    private static final String TAG = "SchemeListActivity";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_scheme_list);

        gvSchemes = findViewById(R.id.grid_menu_Scheme); // or findViewById for your ListView id
        adapter = new SchemeListAdapter(this, schemeList);
        gvSchemes.setAdapter(adapter);

        //  OLD FLOW
        // loadStaticSchemes();
        //use this dealer id get_dealer_id(mContext)
        new GetSchemesTask().execute(
                dealer_schemes + "?dealer_id="
                        + DEALER_ID + "&status=active"
        );

        gvSchemes.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {

                SchemeModel selected = (SchemeModel) parent.getItemAtPosition(position);
                Intent intent = new Intent(SchemeListPdfActivity.this, SchemePdfActivity.class);

                intent.putExtra("scheme_id", selected.getSchemeId());
                intent.putExtra("scheme_name", selected.getSchemeName());
                intent.putExtra("scheme_category", selected.getCategory());
                intent.putExtra("scheme_status", selected.getStatus());
                intent.putExtra("scheme_pdf_url", selected.getPdfUrl());
                intent.putExtra("start_date", selected.getStartDate());
                intent.putExtra("end_date", selected.getEndDate());
                intent.putExtra("lifting_start", selected.getLiftingStart());
                intent.putExtra("lifting_end", selected.getLiftingEnd());
                intent.putExtra("days_remaining", selected.getDaysRemaining());
                // ✅ Add graph-related extras from selected
                intent.putExtra("slab_type", selected.getSlabType());
                intent.putExtra("applicable_qty", selected.getApplicableQty());
                intent.putExtra("slab_details_json", selected.getSlabDetailsJson());
                intent.putExtra("achievements_json", selected.getAchievementsJson());

                startActivity(intent);
            }
        });

    }

    private class GetSchemesTask extends AsyncTask<String, Void, ArrayList<SchemeModel>> {

        ProgressDialog dialog;

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            dialog = new ProgressDialog(SchemeListPdfActivity.this);
            dialog.setMessage("Loading schemes...");
            dialog.setCancelable(false);
            dialog.show();
        }

        @Override
        protected ArrayList<SchemeModel> doInBackground(String... urls) {
            String urlString = urls[0];
            ArrayList<SchemeModel> tempList = new ArrayList<>();
            HttpURLConnection urlConnection = null;
            BufferedReader reader = null;

            try {
                URL url = new URL(urlString);
                urlConnection = (HttpURLConnection) url.openConnection();
                urlConnection.setRequestMethod("GET");
                urlConnection.setConnectTimeout(15000);
                urlConnection.setReadTimeout(15000);
                urlConnection.connect();

                InputStream inputStream = urlConnection.getInputStream();
                if (inputStream == null) {
                    return tempList;
                }

                reader = new BufferedReader(new InputStreamReader(inputStream));
                StringBuilder buffer = new StringBuilder();
                String line;

                while ((line = reader.readLine()) != null) {
                    buffer.append(line);
                }

                if (buffer.length() == 0) {
                    return tempList;
                }

                String responseJson = buffer.toString();
                Log.d(TAG, "Response: " + responseJson);

                JSONObject root = new JSONObject(responseJson);
                String status = root.optString("status");
                if (!"success".equalsIgnoreCase(status)) {
                    return tempList;
                }

                JSONObject dataObj = root.optJSONObject("data");
                if (dataObj == null) {
                    return tempList;
                }

                JSONArray schemesArray = dataObj.optJSONArray("schemes");
                if (schemesArray == null) {
                    return tempList;
                }

                for (int i = 0; i < schemesArray.length(); i++) {
                    JSONObject s = schemesArray.getJSONObject(i);

                    String schemeId = s.optString("scheme_id");
                    String schemeName = s.optString("scheme_name");
                    String pdfUrl = s.optString("pdf_url");
                    String startDate = s.optString("start_date");
                    String endDate = s.optString("end_date");
                    String liftingStart = s.optString("lifting_start");
                    String liftingEnd = s.optString("lifting_end");
                    String category = s.optString("category");
                    String statusScheme = s.optString("status");
                    int daysRemaining = s.optInt("days_remaining", 0);

                    // 🔹 Graph-related fields
                    String slabType = "";
                    int applicableQty = 0;
                    String slabDetailsJson = "";
                    String achievementsJson = "";

                    JSONObject slabDetailsObj = s.optJSONObject("slab_details");
                    if (slabDetailsObj != null) {
                        slabType = slabDetailsObj.optString("type", "");
                        // only for single
                        if ("single".equalsIgnoreCase(slabType)) {
                            applicableQty = slabDetailsObj.optInt("applicable_qty", 0);
                        }
                        slabDetailsJson = slabDetailsObj.toString();
                    }

                    JSONArray achArray = s.optJSONArray("achievements");
                    if (achArray != null) {
                        achievementsJson = achArray.toString();
                    }

                    SchemeModel model = new SchemeModel(
                            schemeId,
                            schemeName,
                            category,
                            statusScheme,
                            pdfUrl,
                            startDate,
                            endDate,
                            liftingStart,
                            liftingEnd,
                            daysRemaining,
                            slabType,
                            applicableQty,
                            slabDetailsJson,
                            achievementsJson
                    );

                    tempList.add(model);
                }


            } catch (Exception e) {
                Log.e(TAG, "Error loading schemes", e);
            } finally {
                try {
                    if (reader != null) reader.close();
                } catch (Exception ignore) {
                }
                if (urlConnection != null) {
                    urlConnection.disconnect();
                }
            }

            return tempList;
        }

        @Override
        protected void onPostExecute(ArrayList<SchemeModel> result) {
            super.onPostExecute(result);
            if (dialog != null && dialog.isShowing()) {
                dialog.dismiss();
            }

            // Clear old data & set new
            schemeList.clear();
            if (result != null) {
                schemeList.addAll(result);
            }
            adapter.notifyDataSetChanged();
        }
    }
}
