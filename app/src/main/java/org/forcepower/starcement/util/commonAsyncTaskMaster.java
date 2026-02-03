package org.forcepower.starcement.util;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.cusomer_code_sub_dealer_new_logic;
import static org.forcepower.starcement.constants.Constants.sub_dealer_destination_new_logic;
import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;
import static org.forcepower.starcement.util.Utils.print_log_d;

import android.content.Context;
import android.util.Log;

import org.forcepower.starcement.bean.BranchMasterDetails;
import org.forcepower.starcement.bean.CustomerDetails;
import org.forcepower.starcement.bean.DestinationMaster;
import org.forcepower.starcement.bean.DumpMaster;
import org.forcepower.starcement.bean.ProductMasterDetails;
import org.forcepower.starcement.bean.SchemeMasterDetails;
import org.forcepower.starcement.bean.SelfAppraisalDetailsProductWise;
import org.forcepower.starcement.constants.AceDnsWebServiceURL;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

/**
 * Created by Force Power Intellij Amiyo  on 07-06-2017.
 * Please follow standard Java coding conventions.
 * http://source.android.com/source/code-style.html
 */
public final class commonAsyncTaskMaster {
    private Context mContext;
    private AceDnsDatabase dbHelper;
    private int noRows = -1, noColumn = -1;
    private final String lastUpdate = "1971-01-01?10:10:10", dwnldDictTime = "1971-01-01?10:10:10",mIsInCremental="no";
    private String timeStamp = "", selected_emp_code = "", status ="" ;

    public commonAsyncTaskMaster(final Context context, final String statusOBJ) {
        Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_ DownloadData 101010101010");
     this.mContext = context;
     this.status=statusOBJ;
     dbHelper = new AceDnsDatabase(mContext);

         if(get_user_type(mContext).equalsIgnoreCase("broker")) {
             Log.d("TAG", "_CHECK_ 1");
             selected_emp_code = get_selected_customer_code(mContext);
         }
         else {
             Log.d("TAG", "_CHECK_ 2");
             selected_emp_code = get_emp_or_customer_code(mContext);
         }

         if(status.equalsIgnoreCase("branch_schemes_PDF")) {
             Log.d("TAG", "_CHECK_ 3");
             _DOWNLOAD_scheme();
         }
         else if(status.equalsIgnoreCase("product_data")) {
             Log.d("TAG", "_CHECK_ 4");
             _DOWNLOAD_product_data();
         }
         else if(status.equalsIgnoreCase("branch_master")) {
             Log.d("TAG", "_CHECK_ 5");
             _DOWNLOAD_branch_master();
         }
         else if(status.equalsIgnoreCase("self_appraisal_product_wise")) {
             Log.d("TAG", "_CHECK_ 6");
            _DOWNLOAD_self_appraisal_product_wise();
         }
         else if(status.equalsIgnoreCase("customer_master")) {
             Log.d("TAG", "_CHECK_ 7");
             if(!dbHelper.DataTableByTableName("customer_master")) {
                 Log.d("TAG", "_CHECK_ 8");
                 _DOWNLOAD_customer_master();
             }
         }
         else if(status.equalsIgnoreCase("destination_master")) {
             Log.d("TAG", "_CHECK_ 9");
             _DOWNLOAD_destination_master();
         }
         else if(status.equalsIgnoreCase("branch_dump")) {
             Log.d("TAG", "_CHECK_ 10");
             _DOWNLOAD_branch_dump();
         }

         else if(status.equalsIgnoreCase("product_master")) {
//             _DOWNLOAD_product_master();
         }
         else if(status.equalsIgnoreCase("emp_master"))
         {
//             _DOWNLOAD_emp_master();
         }
    }
    public void _DOWNLOAD_product_data() {
        ArrayList<ProductMasterDetails> prodList = new ArrayList<>();
        try {
            final String url = AceDnsWebServiceURL.branchwise_product_data_download;
            print_log_d("branchwise_product_data_download_239_a ", url);

            final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);
            nameValuePairs.add(new BasicNameValuePair("emp_code", selected_emp_code));
            nameValuePairs.add(new BasicNameValuePair("user_type", get_user_type(mContext)));
            print_log_d("branchwise_product_data_download_239_b ", nameValuePairs.toString());

            final String POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);
            print_log_d("branchwise_product_data_download_239_c ", POST_result);

            final JSONObject jo = new JSONObject(POST_result);
            final String product_date = jo.optString("product_date");
            final JSONArray jsonArray = new JSONArray(product_date);
            for(int i=0; i<jsonArray.length(); i++) {
                final JSONObject e = jsonArray.getJSONObject(i);
                Log.d("TAG", "_DOWNLOAD_ branchwise_product_data_download_239_c objects: "+e);
                final ProductMasterDetails temp = new ProductMasterDetails();
                temp.setProdCode(e.optString("prod_code"));
                temp.setGrpCode(e.optString("product_group_code"));
                temp.setBrndCode(e.optString("branch_code"));
                temp.setDesc(e.optString("prod_desc"));
                temp.setDnsProdCode(e.optString("dns_prod_code"));

                prodList.add(temp);
            }

            long insertStatus = dbHelper.insertToProductMaster(prodList);

            Log.d("TAG", "_DOWNLOAD_ branchwise_product_data_download_239_c: "+insertStatus);

            if (insertStatus == noRows && noRows > 0) {
                Constants.isProductTableUpdated = false;
                decideNavigation();
            }
            else if (noRows == 0 && noColumn != 0) {
                Constants.isProductTableUpdated = false;
                decideNavigation();
            }
            else {
                if(noRows!=0) {
                    Constants.isDownLoadComplete=false;
                }
            }
        }
        catch (Exception e) {
            Log.d("TAG", "branchwise_product_data_download_239_c error: "+e.getMessage());
            e.printStackTrace();
        }
    }
    public void _DOWNLOAD_scheme() {
        try
        {
            String URL = Constants.baseURL+ AceDnsWebServiceURL.branchwiseSchemeDownloadURL
                    + "?nick_name="+ Constants.nickName
                    + "&emp_code="+ get_emp_or_customer_code(mContext)
                    + "&user_type=" + get_user_type(mContext)
                    + "&last_update_time=" + lastUpdate;

            Download_txt(URL);

            final File csvFile = new File(get_direcory_path(mContext) +status+ ".txt");
//        final File csvFile = new File(get_direcory_path(mContext) +"scheme.txt");
            ArrayList<SchemeMasterDetails> prodList = new ArrayList<SchemeMasterDetails>();
            FileReader file = null;
            try
            {
                file = new FileReader(csvFile);
            }
            catch (FileNotFoundException e1)
            {
                e1.printStackTrace();
            }
            final BufferedReader buffer = new BufferedReader(file);
            try
            {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0)
                    {
                        final String[] dataArray = line.split("¥");
                        noRows = Integer.parseInt(dataArray[0]);
                        noColumn = Integer.parseInt(dataArray[1]);
                    }
                    else if (line.indexOf("€") > 0)
                    {
                        timeStamp = line;
                    }
                    else
                    {
                        final String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn)
                        {
                            final SchemeMasterDetails temp = new SchemeMasterDetails();
                            temp.setBranchCode(RowData[0]);
                            temp.setBrndName(RowData[1]);
                            temp.setIsAcedns(RowData[2]);

                            prodList.add(temp);
                            
                        }
                    }
                }
                buffer.close();
            }
            catch (IOException ex)
            {
                ex.printStackTrace();
            }
            long insertStatus = dbHelper.insertToScheme(prodList);

            if (insertStatus == noRows && noRows > 0)
            {
                Constants.isSchemeTableUpdated = false;
                decideNavigation();
            }
            else if (noRows == 0 && noColumn != 0)
            {
                Constants.isSchemeTableUpdated = false;
                decideNavigation();
            }
            else
            {
                if(noRows!=0)
                {
                    Constants.isDownLoadComplete=false;
                }
            }


        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void _DOWNLOAD_branch_master() {
        try {
            String URL =Constants.baseURL+AceDnsWebServiceURL.branchURL
                    + "?nick_name="+ Constants.nickName
                    + "&emp_code="+ get_emp_or_customer_code(mContext)
                    + "&user_type=" + get_user_type(mContext)
                    + "&incremental_download="+ mIsInCremental
                    + "&last_update_time=" + lastUpdate
                    + "&data_download_time="+dwnldDictTime ;

            Download_txt(URL);

            final File csvFile = new File(get_direcory_path(mContext)  + status+".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            }
            catch (FileNotFoundException e1) {
                e1.printStackTrace();
            }
            ArrayList<BranchMasterDetails> mBranchMasterDetailsList = new ArrayList<BranchMasterDetails>();
            final BufferedReader buffer = new BufferedReader(file);
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        final String[] dataArray = line.split("¥");
                        noRows = Integer.parseInt(dataArray[0]);
                        noColumn = Integer.parseInt(dataArray[1]);
                    }
                    else if (line.indexOf("€") > 0) {
                        timeStamp = line;
                    }
                    else {
                        final String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn) {
                            final BranchMasterDetails temp = new BranchMasterDetails();
                            temp.setCompanyCode(RowData[0]);
                            temp.setBranchCode(RowData[1]);
                            temp.setBranchName(RowData[2]);
                            temp.setHq(RowData[3]);
                            temp.setPlantName(RowData[4]);
                            mBranchMasterDetailsList.add(temp);
                            
                        }
                    }
                }
                buffer.close();
            }
            catch (IOException ex) {
                ex.printStackTrace();
            }
            long insertStatus = dbHelper.InsertToBranchMaster(mBranchMasterDetailsList);

            if (insertStatus == noRows && noRows > 0) {
                Constants.isBranchUpdated=false;
                decideNavigation();
            }
            else if (noRows == 0 && noColumn != 0) {
                Constants.isBranchUpdated=false;
                decideNavigation();
            }
            else {
                if(noRows!=0){
                    Constants.isDownLoadComplete=false;
                }
            }
        }
        catch (Exception e) {
            e.printStackTrace();
        }
    }
    public void _DOWNLOAD_self_appraisal_product_wise() {
        try {

            String URL =Constants.baseURL+AceDnsWebServiceURL.productWiseTargetAchievement
                    + "?nick_name="+ Constants.nickName
                    + "&user_type=" + get_user_type(mContext)
                    + "&emp_code="+ get_emp_or_customer_code(mContext);

            Log.d("TAG", "_DOWNLOAD_self_appraisal_product_wise: "+URL);
            Download_txt(URL);

            final File csvFile = new File(get_direcory_path(mContext) + status+".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            }
            catch (FileNotFoundException e1) {
                e1.printStackTrace();
            }
            ArrayList<SelfAppraisalDetailsProductWise> mSelfAppraisalProductWiseList = new ArrayList<>();
            final BufferedReader buffer = new BufferedReader(file);
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        final String[] dataArray = line.split("¥");
                        noRows = Integer.parseInt(dataArray[0]);
                        noColumn = Integer.parseInt(dataArray[1]);
                    }
                    else if (line.indexOf("€") > 0) {}
                    else {
//                        Log.d("TAG", "_DOWNLOAD_self_appraisal_product_wise: "+line);
                        final String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn) {
                            final SelfAppraisalDetailsProductWise temp = new SelfAppraisalDetailsProductWise();
                            temp.setProductCode(RowData[0]);
                            temp.setProductName(RowData[1]);
                            temp.setempCode(RowData[2]);
                            temp.setmonth(RowData[3]);
                                temp.settarget(RowData[4]);
                            temp.setachievement(RowData[5]);
                            temp.set_prev_y_target(RowData[6]);
                            temp.set_prev_y_achievement(RowData[7]);
                            mSelfAppraisalProductWiseList.add(temp);
                        }
                    }
                }
                buffer.close();
            }
            catch (IOException ex) {
                ex.printStackTrace();
            }
            long insertStatus = dbHelper.InsertToProductWiseTargetAchievement(mSelfAppraisalProductWiseList);

            if (insertStatus == noRows && noRows > 0) {}
            else if (noRows == 0 && noColumn != 0) {}
            else {
                if(noRows!=0){
                    Constants.isDownLoadComplete=false;
                }
            }
        }
        catch (Exception e) {
            e.printStackTrace();
        }
    }
    public void _DOWNLOAD_customer_master() {
        try {
            String URL =Constants.baseURL+AceDnsWebServiceURL.customerDetailsURL
                    + "?nick_name="+ Constants.nickName
                    + "&emp_code="+ get_emp_or_customer_code(mContext)
                    + "&user_type=" + get_user_type(mContext)
                    + "&incremental_download="+ mIsInCremental
                    + "&last_update_time=" + lastUpdate
                    + "&data_download_time="+ dwnldDictTime;

            Download_txt(URL);
            print_log_d("cstmr_er44f_err_429 ", URL);
            final File csvFile = new File(get_direcory_path(mContext) + status+".txt");
            ArrayList<CustomerDetails> customerList = new ArrayList<CustomerDetails>();
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            }
            catch (FileNotFoundException e1) {
                Log.d("TAG", "_DOWNLOAD_customer_master: "+e1);
                e1.printStackTrace();
            }
            final BufferedReader buffer = new BufferedReader(file);
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        final String[] dataArray = line.split("¥");
                        noRows = Integer.parseInt(dataArray[0]);
                        noColumn = Integer.parseInt(dataArray[1]);
                    }
                    else if (line.indexOf("€") > 0) {
                        timeStamp = line;
                    }
                    else {
                        final String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn) {
                            final CustomerDetails temp = new CustomerDetails();
                            temp.setCustomerCode(RowData[0]);
                            temp.setCustomerName(RowData[1]);
                            temp.setRouteCode(RowData[2]);
                            temp.setEmpCode(RowData[3]);
                            temp.setCurrentBalance(RowData[4]);
                            temp.setCreditLimit(RowData[5]);
                            temp.setIsACEDNS(RowData[6]);
                            temp.setIsBlackList(RowData[7]);
                            temp.setTradeDiscount(RowData[8]);
                            temp.setCustomerType(RowData[9]);
                            temp.setRdsTag(RowData[10]);
                            temp.setSaudaValidityPeriod(RowData[11]);
                            temp.setAddress(RowData[12]);
                            temp.setPin(RowData[13]);
                            temp.setNumber(RowData[14]);
                            temp.setReplacingCustCode(RowData[15]);
                            temp.setLandlineNo(RowData[16]);
                            temp.setOwnerName(RowData[17]);
                            temp.setOwnerPhone(RowData[18]);
                            temp.setCustClass(RowData[19]);
                            temp.setWeeklyClosingDay(RowData[20]);
                            temp.setCoverageType(RowData[21]);
                            temp.setTIN(RowData[22]);
                            temp.setPAN(RowData[23]);
                            temp.setMinimumStock(RowData[24]);
                            temp.setBranchCode(RowData[25]);
                            temp.setVisitDay(RowData[26]);
                            temp.setEmail(RowData[27]);
                            temp.setSaudaLimit(RowData[28]);
                            temp.setPendingQty(RowData[29]);
                            temp.setFlag("1");
                            temp.set_SAP_code(RowData[30]);
                            customerList.add(temp);
                            
                        }
                        Log.d(""+RowData[0], "_DOWNLOAD_customer_master: "+line);
                    }

                }
                buffer.close();
            }
            catch (IOException ex) {
                ex.printStackTrace();
                print_log_d("cstmr_er44f_err_495 ", ex.getMessage());
            }
            print_log_d("Customer Details", "Saving " + noRows
                    + " records for \n Customer Details..");
            print_log_d("cstmr_er44f_err_498 ", customerList + " ");

            long insertStatus = dbHelper.insertToCustomerMaster(customerList);
            print_log_d("cstmr_er44f_err_499 ", insertStatus + " ");
            if (insertStatus == noRows && noRows > 0) {
                Constants.isCustomerTableUpdated = false;
                decideNavigation();
            }
            else if (noRows == 0 && noColumn != 0) {
                Constants.isCustomerTableUpdated = false;
                decideNavigation();
            }
            else {
                if(noRows!=0){
                    Constants.isDownLoadComplete=false;
                }
            }
        }
        catch (Exception e) {
            e.printStackTrace();
            print_log_d("cstmr_er44f_err_516 ", e.toString());
        }
    }
    public void _DOWNLOAD_destination_master() {
        try {
            String URL = "";
            if(get_user_type(mContext).equalsIgnoreCase("broker")) {
                URL =Constants.baseURL+AceDnsWebServiceURL.destinationMasterURL
                        + "?nick_name="+ Constants.nickName
                        + "&emp_code="+ cusomer_code_sub_dealer_new_logic
                        + "&broker_id="+ get_dealer_id(mContext)
                        + "&incremental_download="+ mIsInCremental
                        + "&last_update_time=" + lastUpdate
                        + "&data_download_time="+ dwnldDictTime;
            }
            else {
                if(sub_dealer_destination_new_logic) {
                    URL =Constants.baseURL+AceDnsWebServiceURL.destinationMasterURL
                            + "?nick_name="+ Constants.nickName
                            + "&emp_code="+ cusomer_code_sub_dealer_new_logic
                            + "&incremental_download="+ mIsInCremental
                            + "&last_update_time=" + lastUpdate
                            + "&data_download_time="+ dwnldDictTime;
                }
                else {
                    URL =Constants.baseURL+AceDnsWebServiceURL.destinationMasterURL
                            + "?nick_name="+ Constants.nickName
                            + "&emp_code="+ selected_emp_code
                            + "&incremental_download="+ mIsInCremental
                            + "&last_update_time=" + lastUpdate
                            + "&data_download_time="+ dwnldDictTime;
                }
            }
            Log.d("TAG", "_DOWNLOAD_destination_master: "+URL);
            Download_txt(URL);
            try {
                File responseFile = new File(get_direcory_path(mContext) + status + ".txt");
                BufferedReader br = new BufferedReader(new FileReader(responseFile));

                Log.d("DEST_DEBUG", "====== DESTINATION RAW RESPONSE START ======");

                String line;
                while ((line = br.readLine()) != null) {
                    Log.d("DEST_DEBUG", line);
                }

                Log.d("DEST_DEBUG", "====== DESTINATION RAW RESPONSE END ======");

                br.close();
            } catch (Exception e) {
                Log.e("DEST_DEBUG", "Error reading destination response file", e);
            }

            final File csvFile = new File(get_direcory_path(mContext) + status+".txt");
            ArrayList<DestinationMaster> destinationList = new ArrayList<DestinationMaster>();
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            }
            catch (FileNotFoundException e1) {
                e1.printStackTrace();
            }
            final BufferedReader buffer = new BufferedReader(file);
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        final String[] dataArray = line.split("¥");
                        noRows = Integer.parseInt(dataArray[0]);
                        noColumn = Integer.parseInt(dataArray[1]);
                    }
                    else if (line.indexOf("€") > 0) {
                        timeStamp = line;
                    }
                    else {
                        final String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn) {
                            final DestinationMaster temp = new DestinationMaster();
                            temp.setDestinationCode(RowData[0]);
                            temp.setDestinationName(RowData[1]);
                            if(noColumn == 3)
                                temp.setExForType(RowData[2]);
                            destinationList.add(temp);
                        }
                    }
                }
                buffer.close();
            }
            catch (IOException ex) {
                ex.printStackTrace();
            }
            long insertStatus = dbHelper.InsertToDestinationMaster(destinationList, noColumn);

            if (insertStatus == noRows && noRows > 0) {
                Constants.isDestinationUpdated=false;
                decideNavigation();
            }
            else if (noRows == 0 && noColumn != 0) {
                Constants.isDestinationUpdated=false;
                decideNavigation();
            }
            else {
                if(noRows!=0 ){
                    Constants.isDownLoadComplete=false;
                }
            }
        }
        catch (Exception e) {
            e.printStackTrace();
        }
    }
    public void _DOWNLOAD_branch_dump() {
        try {
            String URL = "";
            if(sub_dealer_destination_new_logic) {
                URL =Constants.baseURL+AceDnsWebServiceURL.branch_dump_master_txt_URL
                        + "?nick_name="+ Constants.nickName
                        + "&emp_code="+ cusomer_code_sub_dealer_new_logic
                        + "&incremental_download="+ mIsInCremental
                        + "&last_update_time=" + lastUpdate
                        + "&data_download_time="+ dwnldDictTime;
            }
            else {
                URL =Constants.baseURL+AceDnsWebServiceURL.branch_dump_master_txt_URL
                        + "?nick_name="+ Constants.nickName
                        + "&emp_code="+ selected_emp_code
                        + "&incremental_download="+ mIsInCremental
                        + "&last_update_time=" + lastUpdate
                        + "&data_download_time="+ dwnldDictTime;
            }


            Download_txt(URL);

            final File csvFile = new File(get_direcory_path(mContext) + status+".txt");
            ArrayList<DumpMaster> destinationList = new ArrayList<>();
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            }
            catch (FileNotFoundException e1) {
                e1.printStackTrace();
            }
            final BufferedReader buffer = new BufferedReader(file);
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        final String[] dataArray = line.split("¥");
                        noRows = Integer.parseInt(dataArray[0]);
                        noColumn = Integer.parseInt(dataArray[1]);
                    }
                    else if (line.indexOf("€") > 0) {
                        timeStamp = line;
                    }
                    else {
                        final String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn) {
                            final DumpMaster temp = new DumpMaster();
                            temp.set_branch_code(RowData[0]);
                            temp.set_dump_code(RowData[1]);
                            temp.set_dump_name(RowData[2]);
                            temp.set_acedns(RowData[3]);
                            temp.set_is_plant(RowData[4]);
                            temp.set_download_time(RowData[5]);

                            destinationList.add(temp);
                            
                        }
                    }
                }
                buffer.close();
            }
            catch (IOException ex) {
                ex.printStackTrace();
            }
            long insertStatus = dbHelper.InsertToDumpMasterMaster(destinationList);

            if (insertStatus == noRows && noRows > 0) {
                Constants.isDumpMasterUpdated=false;
                decideNavigation();
            }
            else if (noRows == 0 && noColumn != 0) {
                Constants.isDumpMasterUpdated=false;
                decideNavigation();
            }
            else {
                if(noRows!=0 ){
                    Constants.isDumpMasterUpdated=false;
                }
            }
        }
        catch (Exception e) {
            e.printStackTrace();
        }
    }
    private void Download_txt(String URL) {
        try {
            URL = URL.replace(" ", "%20");
            HttpURLConnection c = null;
            FileOutputStream fbo = null;
            File outputFile = null;
            InputStream is = null;
            URL url = null;

            try {
                outputFile = new File(get_direcory_path(mContext) + status+".txt");
                if (outputFile.exists())
                    outputFile.delete();
                fbo = new FileOutputStream(outputFile, false);
                url = new URL(URL);
                c = (HttpURLConnection) url.openConnection();
                c.setRequestMethod("GET");
                c.setDoOutput(true);
                c.setConnectTimeout(55000);
                c.setReadTimeout(55000);

                c.connect();
                is = c.getInputStream();
                final byte[] buffer = new byte[1024];
                int len1 = 0;
                while ((len1 = is.read(buffer)) != -1) {
                    fbo.write(buffer, 0, len1);
                }
                fbo.flush();

            }
            catch (Exception e) {
                e.printStackTrace();
                Log.d("TAG", "Download_txt: "+URL+"  "+e+"");
                print_log_d("er44f_err_789 URL ", e+"");
            }
            finally {
                if (c != null)
                    c.disconnect();
                if (fbo != null)
                    try {
                        fbo.close();
                    }
                    catch (IOException e) {
                        e.printStackTrace();
                    }
                if (is != null)
                    try {
                        is.close();
                    }
                    catch (IOException e) {
                        e.printStackTrace();
                    }

            }
        }
        catch (Exception e1) {
            e1.printStackTrace();
            Log.d("TAG", "Download_txt: "+URL+"  "+e1+"");
            print_log_d("er44f_err_789 URL ", e1+"");
        }
    }
    public void decideNavigation() {
        if (!(noRows == 0 && noColumn != 0)) {
            dbHelper.insertToLogTable(timeStamp, status);
        }
    }
}
