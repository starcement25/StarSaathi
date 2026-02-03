package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_dealer_submit_form;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_firebase_token;
import static org.forcepower.starcement.SharedPrefData.get_logged_sub_dealer_name;
import static org.forcepower.starcement.SharedPrefData.get_profile_image_url;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.SharedPrefData.set_profile_image_url;
import static org.forcepower.starcement.SharedPrefData.set_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.set_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.set_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.set_server_current_date;
import static org.forcepower.starcement.Utils_.ApiRes;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.AceDnsWebServiceURL.masson_url;
import static org.forcepower.starcement.constants.Constants.DEFAULT_TIMEOUT;
import static org.forcepower.starcement.constants.Constants.acedns_about_us;
import static org.forcepower.starcement.constants.Constants.acedns_dashboard_webLink;
import static org.forcepower.starcement.constants.Constants.acedns_update_profile_image;
import static org.forcepower.starcement.constants.Constants.api_get_dealer_information;
import static org.forcepower.starcement.constants.Constants.api_get_month_list;
import static org.forcepower.starcement.constants.Constants.arc_offer;
import static org.forcepower.starcement.constants.Constants.branch_code;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.dealer_wise_tour_data_download;
import static org.forcepower.starcement.constants.Constants.dealerwise_credit_limit_s_deposit;
import static org.forcepower.starcement.constants.Constants.downloadTableList;
import static org.forcepower.starcement.constants.Constants.my_payment_history_weblink;
import static org.forcepower.starcement.constants.Constants.redirection;
import static org.forcepower.starcement.constants.Constants.selectedProductMasterList;
import static org.forcepower.starcement.constants.Constants.show_latest_app_version;
import static org.forcepower.starcement.constants.Constants.sub_dealer_destination_new_logic;
import static org.forcepower.starcement.constants.Constants.survey_form;
import static org.forcepower.starcement.constants.Constants.updateChecked;
import static org.forcepower.starcement.util.PreferenceData.getLoginStatus;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.showCommonAlertDialog;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;
import android.os.SystemClock;
import android.provider.Settings;
import android.text.Editable;
import android.text.Html;
import android.text.TextWatcher;
import android.text.method.LinkMovementMethod;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.annotation.RequiresApi;
import androidx.appcompat.app.ActionBarDrawerToggle;
import androidx.core.app.ActivityCompat;
import androidx.core.view.GravityCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.viewpager2.widget.ViewPager2;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;
import com.mikhaellopez.circularimageview.CircularImageView;

import org.forcepower.starcement.BuildConfig;
import org.forcepower.starcement.HomeSlider;
import org.forcepower.starcement.MarshMallowPermission;
import org.forcepower.starcement.R;
import org.forcepower.starcement.activity.WebViewActivity;
import org.forcepower.starcement.activity.declaration_of_dealer.DealerDeclarationActivity;
import org.forcepower.starcement.activity.declaration_of_dealer.dataset.MonthDataSet;
import org.forcepower.starcement.activity.kismet_ki_bori.KismetKiBoriActivity;
import org.forcepower.starcement.activity.test.TextActivity;
import org.forcepower.starcement.adapter.DestinationAdapter;
import org.forcepower.starcement.adapter.MenuAdapter;
import org.forcepower.starcement.adapter.MonthListAdapter;
import org.forcepower.starcement.backgroundTask.AppUses_AsyncTask;
import org.forcepower.starcement.backgroundTask.DATA_LoadDataDictionaryData;
import org.forcepower.starcement.backgroundTask.DATA_LoadDatabaseDetails;
import org.forcepower.starcement.backgroundTask.POST_SendFcmId;
import org.forcepower.starcement.backgroundTask.Rewards_AsyncTask;
import org.forcepower.starcement.backgroundTask.TRANS_GetEngagement_Asynctask;
import org.forcepower.starcement.backgroundTask.TRANS_GetLogOut_Asynctask;
import org.forcepower.starcement.bean.DestinationMaster;
import org.forcepower.starcement.bean.MenuObj;
import org.forcepower.starcement.commonDatabaseHelper;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.database.DatabaseHelperSqlite;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.forcepower.starcement.util.commonAsyncTaskMaster;
import org.json.JSONArray;
import org.json.JSONObject;

import java.io.File;
import java.text.SimpleDateFormat;
import java.time.LocalDate;
import java.time.format.DateTimeFormatter;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Locale;

import cz.msebera.android.httpclient.Header;
import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;
import my_crop.vola.ImagePickerActivity;


public final class MenuActivity extends AceDnsParentActivity{
    private ImageView mButtonLogout, ivFilter;
    private CircularImageView mImageViewUserPic;
    private GridView mGridViewMenu;
    private TextView mTextViewUserName, tvNotiCount, tv_home_caption;
    private AceDnsDatabase mAceDnsDatabase;

    private LinearLayout mLinearLayoutOption, ll_3_balance, ll_dots, ll_dots_game,
            ll_cs_lottery;
    private LinearLayout ll_kismetKiBori;
    private DrawerLayout mDrawerLayout;
    private Activity mContext;

    private ArrayList<MenuObj> mMenuList = new ArrayList<>();
    private ArrayList<DestinationMaster> mDestinationMasterList = new ArrayList<>();
    ArrayList<MonthDataSet> dataMonthDataSet= new ArrayList<>();

    private Handler mPrepareSurveyHandler;
    private int mCount = 0;
    private long lastClickTime = 0;

    private ViewPager2 view_pager2, view_game_pager2;

    private static final int PERMISSION_REQUEST_CODE = 200;
    public static final int REQUEST_IMAGE = 100;
    File outPutFile = new File("");

    //--------------------terms and condition popup--------------------
    private LinearLayout reward_t_and_c_popup_layout;
    private TextView reward_t_and_c_popup_text,popup_check_box_text,schemeText;
    private ImageView popup_check_box;
    private Button popup_t_c_submit_button;
    private int tc_popup_value=0;
    //--------------------terms and condition popup--------------------

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_menu);

        tc_popup_value=0;

        init();
        try {
            mContext = this;
            view_pager2 = (ViewPager2) findViewById(R.id.view_pager2);
            view_game_pager2 = (ViewPager2) findViewById(R.id.view_game_pager2);
            ll_cs_lottery = (LinearLayout) findViewById(R.id.ll_cs_lottery);
            ll_dots_game = (LinearLayout) findViewById(R.id.ll_dots_game);
            ll_dots = (LinearLayout) findViewById(R.id.ll_dots);
            schemeText = findViewById(R.id.marqueeText);
            schemeText.setSelected(true);


            try {
                mAceDnsDatabase = new AceDnsDatabase(mContext);
                Log.d("TAG", "onCreate 1 DONE");
            } catch (Exception e) {
                Log.d("TAG", "onCreate Exception 1: " + e);
            }

            ivFilter = (ImageView) findViewById(R.id.ivFilter);
            mButtonLogout = (ImageView) findViewById(R.id.btn_logout);
            mImageViewUserPic = (CircularImageView) findViewById(R.id.img_user);
            mLinearLayoutOption = (LinearLayout) findViewById(R.id.option_layout);
            ll_3_balance = (LinearLayout) findViewById(R.id.ll_3_balance);
            mTextViewUserName = (TextView) findViewById(R.id.txt_username);
            mGridViewMenu = (GridView) findViewById(R.id.grid_menu);
            tv_home_caption = (TextView) findViewById(R.id.tv_home_caption);
            tvNotiCount = (TextView) findViewById(R.id.tvNotiCount);
            mDrawerLayout = (DrawerLayout) findViewById(R.id.drawer_layout);

            try {
                Constants.isFirstLoginOfApp = false;
                Log.d("TAG", "onCreate 2 DONE");
            } catch (Exception e) {
                Log.d("TAG", "onCreate Exception 2: " + e);
            }

            try {
                prepareFeatureList();
                Log.d("TAG", "onCreate 3 DONE");
            } catch (Exception e) {
                Log.d("TAG", "onCreate Exception 3: " + e);
            }

            try {
                initView();
                Log.d("TAG", "onCreate 4 DONE");
            } catch (Exception e) {
                Log.d("TAG", "onCreate Exception 4: " + e);
            }



            mGridViewMenu.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                        long arg3) {
                    if (SystemClock.elapsedRealtime() - lastClickTime < 4000) {
                        return;
                    }
                    lastClickTime = SystemClock.elapsedRealtime();
                    final String redirect = mMenuList.get(arg2).getFeatureName() + "";
                    if (HTTPUtils.isConnectionPossible(mContext)) {
                        if (updateChecked) {
                            doOnItemClickJob(redirect);
                        } else {
                            Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_ mGridViewMenu loader On");
                            showLoader();
                            update_checking(redirect);
                        }

                        if (!BuildConfig.DEBUG)
                            new AppUses_AsyncTask(mContext, mMenuList.get(arg2).getName() + "").execute();
                    } else {
                        Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                        lastClickTime = 0;
                    }
                }
            });

            Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_  onCreate");
            load_on_create("");
            Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_  onCreate HI");

            mPrepareSurveyHandler = new Handler(Looper.myLooper()) {
                public void handleMessage(@NonNull Message threadmsg) {
                    final int listcount = threadmsg.getData().getInt("JOBALLOCATE");
                    Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_ mPrepareSurveyHandler " + downloadTableList.size());
                    runOnUiThread(new Runnable() {
                        public void run() {

                            if (listcount == downloadTableList.size() - 1) {
                                Utils.changeProgressDialogMsg(mContext, "Successfully updated");
                                dismissLoader();
                            } else {
                                mCount++;
                                DownloadData(mCount, downloadTableList.get(mCount));
                            }
                        }
                    });
                }
            };

            Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_  onCreate BYE");
            //
            print_log_d("the_profile_image_url_229 ", get_profile_image_url(mContext));
            Glide.with(mContext)
                    .load(get_profile_image_url(mContext))
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .placeholder(R.drawable.cust_bg)
                    .error(R.drawable.cust_bg)
                    .into(mImageViewUserPic);
            //
            if (get_user_type(mContext).equalsIgnoreCase("Dealer") ||
                    get_user_type(mContext).equalsIgnoreCase("broker")) {
                ll_3_balance.setVisibility(View.VISIBLE);
            } else {
                ll_3_balance.setVisibility(View.GONE); //rssd sd
            }
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
                if (ActivityCompat.checkSelfPermission(this, Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED) {
                    requestPermissions(new String[]{Manifest.permission.POST_NOTIFICATIONS}, 1100);
                }
            }
        } catch (Exception e) {
            Log.d("TAG", "onCreate Exception 5: " + e);
            e.printStackTrace();
        } finally {
            update_checking("");
        }
    }

    @Override
    public void onPause() {
        super.onPause();
        dismissLoader();
    }

    @SuppressLint("MissingSuperCall")
    @Override
    public void onBackPressed() {
        if (mDrawerLayout.isDrawerOpen(GravityCompat.START)) {
            mDrawerLayout.closeDrawers();
        } else {
            finishAffinity();
        }
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
    }

    @Override
    public void onResume() {
        super.onResume();

        sub_dealer_destination_new_logic = false;
        if (getLoginStatus(mContext)) {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                FCMProcess();
            }
            DatabaseHelperSqlite notiDB = new DatabaseHelperSqlite(mContext);
            List<commonDatabaseHelper> all = notiDB.getAllNotiList("unread");
            if (all != null && all.size() > 0) {
                tvNotiCount.setText(all.size() + "");
            } else {
                tvNotiCount.setText("0");
            }
        } else {
            startActivity(new Intent(mContext, SplashActivity.class));
            finish();
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == REQUEST_IMAGE) {
            if (resultCode == Activity.RESULT_OK) {
                try {
                    final Uri uri = data.getParcelableExtra("path");
//					mImageViewUserPic.setImageURI(uri);
                    outPutFile = new File(uri.getPath());
                    // You can update this bitmap to your server
//					Bitmap bitmap = MediaStore.Images.Media.getBitmap(this.getContentResolver(), uri);
//					mImageViewUserPic.setImageBitmap(bitmap);
//					String imgFilePath = saveImage(bitmap);
//					outPutFile = new File(imgFilePath);

                    if (outPutFile.exists())
                        updateProfile();
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }
        }
    }

    @RequiresApi(api = Build.VERSION_CODES.R)
    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == PERMISSION_REQUEST_CODE) {
            MarshMallowPermission mMP = new MarshMallowPermission(mContext);
            if (mMP.checkPermissionForCamera()) {
                openCamera();
            } else {
                Intent intent = new Intent(Settings.ACTION_MANAGE_APP_ALL_FILES_ACCESS_PERMISSION);
                intent.addCategory("android.intent.category.DEFAULT");
                intent.setData(Uri.parse(String.format("package:%s", getApplicationContext().getPackageName())));
                mContext.startActivityForResult(intent, PERMISSION_REQUEST_CODE);
            }
        }
    }

    private void init(){
        reward_t_and_c_popup_layout=findViewById(R.id.reward_t_and_c_popup_layout);
        reward_t_and_c_popup_text=findViewById(R.id.reward_t_and_c_popup_text);
        popup_check_box_text=findViewById(R.id.popup_check_box_text);
        popup_check_box=findViewById(R.id.popup_check_box);
        popup_t_c_submit_button=findViewById(R.id.popup_t_c_submit_button);

        reward_t_and_c_popup_layout.setVisibility(View.GONE);
        reward_t_and_c_popup_text.setText(Html.fromHtml(getString(R.string.colored_text)));
        reward_t_and_c_popup_text.setMovementMethod(LinkMovementMethod.getInstance());

        reward_t_and_c_popup_layout.setOnClickListener(v -> {
            reward_t_and_c_popup_layout.setVisibility(View.GONE);
        });
        popup_check_box_text.setOnClickListener(v -> {
            if(tc_popup_value==0){
                tc_popup_value=1;
                popup_check_box.setImageResource(R.drawable.baseline_check_box_24);
            }else{
                tc_popup_value=0;
                popup_check_box.setImageResource(R.drawable.baseline_check_box_outline_blank_24);
            }
        });
        popup_check_box.setOnClickListener(v -> {
            if(tc_popup_value==0){
                tc_popup_value=1;
                popup_check_box.setImageResource(R.drawable.baseline_check_box_24);
            }else{
                tc_popup_value=0;
                popup_check_box.setImageResource(R.drawable.baseline_check_box_outline_blank_24);
            }
        });
        popup_t_c_submit_button.setOnClickListener(v -> {
            if(tc_popup_value==1) {
                reward_t_and_c_popup_layout.setVisibility(View.GONE);
                start_Rewards(null);
            }else{
                Toast.makeText(mContext,"Accept terms & condition first",Toast.LENGTH_LONG).show();
            }
        });
    }

    public void update_checking(String redirect) {
        try {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_ update_checking loader On");
                showLoader();
                new TRANS_GetAppUpdate_Asynctask(mContext, redirect).execute();
            } else {
                Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void load_on_create(final String ssyynncc) {
        try {
            final LinearLayout ll_credit_limit = findViewById(R.id.ll_credit_limit);
            final LinearLayout ll_appPaymentHistory = findViewById(R.id.ll_appPaymentHistory);
            final LinearLayout ll_exclusiveDealerDeclaration = findViewById(R.id.ll_exclusiveDealerDeclaration);
            ll_kismetKiBori=findViewById(R.id.ll_kismetKiBori);
            if (get_user_type(mContext).equalsIgnoreCase("broker")) {
                branch_code = mAceDnsDatabase.getBranchCode(get_selected_customer_code(mContext));
            } else {
                branch_code = mAceDnsDatabase.getBranchCode(get_emp_or_customer_code(mContext));
            }
            if (get_user_type(mContext).equalsIgnoreCase("broker")) {
                ll_credit_limit.setVisibility(View.GONE);
                ll_appPaymentHistory.setVisibility(View.GONE);
                ll_exclusiveDealerDeclaration.setVisibility(View.GONE);
                ivFilter.setVisibility(View.VISIBLE);
                if (!get_selected_customer_name(mContext).matches("")) {
                    tv_home_caption.setText(get_selected_customer_name(mContext) + "");
                }
                if (get_selected_customer_code(mContext).matches("")) {
                    mDestinationMasterList = mAceDnsDatabase.getMyDealerList();
                    if (mDestinationMasterList.size() > 0) {
                        showMyDealerList();
                    }
                }
            } else if (get_user_type(mContext).equalsIgnoreCase("dealer")) {
                if (get_dealer_submit_form(mContext).equalsIgnoreCase("NO")) {
                    if (!ssyynncc.equalsIgnoreCase("ssyynncc")) {
                        Intent intent = new Intent(mContext, DealerFormWebViewActivity.class);
                        intent.putExtra("webview_caption", "TDS/TCS Confirmation");
                        intent.putExtra("webview_url", survey_form + get_emp_or_customer_code(mContext));
                        startActivity(intent);
                    }
                }
                ll_credit_limit.setVisibility(View.VISIBLE);
                ll_appPaymentHistory.setVisibility(View.VISIBLE);
                ll_exclusiveDealerDeclaration.setVisibility(View.VISIBLE);
//                ll_exclusiveDealerDeclaration.setVisibility(View.GONE);
            } else {
                ll_credit_limit.setVisibility(View.GONE);
                ll_appPaymentHistory.setVisibility(View.GONE);
                ll_exclusiveDealerDeclaration.setVisibility(View.GONE);
            }
            Utils.InitialiseSETUPTableData(mContext);

            new TRANS_GetProfileInformation_Asynctask(mContext).execute();
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            get_credit_details();
        }
    }

    public void prepareFeatureList() {
        try {
            mMenuList = new ArrayList<>();

            if (get_user_type(mContext).equalsIgnoreCase("broker")) {
                MenuObj menuObj0 = new MenuObj();
                menuObj0.setFeatureName("Order");
                menuObj0.setName("Order");
                menuObj0.setResourceId(R.drawable.order);
                mMenuList.add(menuObj0);

                MenuObj menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.trackorder);
                menuObj1.setFeatureName("Track_Orders");
                menuObj1.setName("Track Order");
                mMenuList.add(menuObj1);

                MenuObj menuObj = new MenuObj();
                menuObj.setResourceId(R.drawable.ledger);
                menuObj.setFeatureName("Ledger");
                menuObj.setName("Ledger");
                mMenuList.add(menuObj);

                menuObj = new MenuObj();
                menuObj.setFeatureName("Performance_Month");
                menuObj.setName("Performance");
                menuObj.setResourceId(R.drawable.performance);
                mMenuList.add(menuObj);

                menuObj = new MenuObj();
                menuObj.setFeatureName("product_performance");
                menuObj.setName("Performance\n(Product Wise)");
                menuObj.setResourceId(R.drawable.product_performance);
                mMenuList.add(menuObj);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.scheme_new);
                menuObj1.setName("Scheme");
                menuObj1.setFeatureName("Scheme");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.tour);
                menuObj1.setFeatureName("Tour");
                menuObj1.setName("Tour");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.game);
                menuObj1.setFeatureName("ENGAGEMENTS");
                menuObj1.setName("Engagements");
                mMenuList.add(menuObj1);

//				menuObj1 = new MenuObj();
//				menuObj1.setResourceId(R.drawable.retailor_lifting);
//				menuObj1.setFeatureName("Retailer_Lifting");
//				menuObj1.setName("Retailer Lifting");
//				mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.pop);
                menuObj1.setFeatureName("Pop_Order");
                menuObj1.setName("Pop Order");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.mason_lifting);
                menuObj1.setFeatureName("mason_lifting");
                menuObj1.setName("Mason Lifting\nApproval");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.invoice);
                menuObj1.setFeatureName("Pending_Invoices");
                menuObj1.setName("Pending Invoices");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.ageing);
                menuObj1.setFeatureName("Ageing");
                menuObj1.setName("Ageing");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.plceorder_rssd);
                menuObj1.setFeatureName("Place_Order");
                menuObj1.setName("Order Enquiry");
                mMenuList.add(menuObj1);

            } else if (get_user_type(mContext).equalsIgnoreCase("Dealer")) {
                MenuObj menuObj0 = new MenuObj();
                menuObj0.setFeatureName("Order");
                menuObj0.setName("Order");
                menuObj0.setResourceId(R.drawable.order);
                mMenuList.add(menuObj0);

                MenuObj menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.trackorder);
                menuObj1.setFeatureName("Track_Orders");
                menuObj1.setName("Track Order");
                mMenuList.add(menuObj1);

                MenuObj menuObj = new MenuObj();
                menuObj.setResourceId(R.drawable.ledger);
                menuObj.setFeatureName("Ledger");
                menuObj.setName("Ledger");
                mMenuList.add(menuObj);

                menuObj = new MenuObj();
                menuObj.setFeatureName("Performance_Month");
                menuObj.setName("Performance");
                menuObj.setResourceId(R.drawable.performance);
                mMenuList.add(menuObj);

                menuObj = new MenuObj();
                menuObj.setFeatureName("product_performance");
                menuObj.setName("Performance\n(Product Wise)");
                menuObj.setResourceId(R.drawable.product_performance);
                mMenuList.add(menuObj);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.scheme_new);
                menuObj1.setFeatureName("Scheme");
                menuObj1.setName("Scheme");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.tour);
                menuObj1.setFeatureName("Tour");
                menuObj1.setName("Tour");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.game);
                menuObj1.setFeatureName("ENGAGEMENTS");
                menuObj1.setName("Engagements");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.retailor_lifting);
                menuObj1.setFeatureName("Retailer_Lifting");
                menuObj1.setName("Retailer Lifting");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.pop);
                menuObj1.setFeatureName("Pop_Order");
                menuObj1.setName("Pop Order");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.mason_lifting);
                menuObj1.setFeatureName("mason_lifting");
                menuObj1.setName("Mason Lifting\nApproval");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.invoice);
                menuObj1.setFeatureName("Pending_Invoices");
                menuObj1.setName("Pending Invoices");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.ageing);
                menuObj1.setFeatureName("Ageing");
                menuObj1.setName("Ageing");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.plceorder_rssd);
                menuObj1.setFeatureName("Place_Order");
                menuObj1.setName("Order Enquiry");
                mMenuList.add(menuObj1);
            } else if ((get_user_type(mContext).equalsIgnoreCase("sub dealer") || get_user_type(mContext).equalsIgnoreCase("rssd"))) {
                MenuObj menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.trackorder);
                menuObj1.setFeatureName("Track_Orders");
                menuObj1.setName("Track Order");
                mMenuList.add(menuObj1);

                MenuObj menuObj = new MenuObj();
                menuObj.setResourceId(R.drawable.ledger);
                menuObj.setFeatureName("Ledger");
                menuObj.setName("Ledger");
                mMenuList.add(menuObj);

                menuObj = new MenuObj();
                menuObj.setFeatureName("Performance_Month");
                menuObj.setName("Performance");
                menuObj.setResourceId(R.drawable.performance);
                mMenuList.add(menuObj);

                if(get_user_type(mContext).equalsIgnoreCase("rssd")){
                    menuObj = new MenuObj();
                    menuObj.setFeatureName("product_performance");
                    menuObj.setName("Performance\n(Product Wise)");
                    menuObj.setResourceId(R.drawable.product_performance);
                    mMenuList.add(menuObj);
                }

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.scheme_new);
                menuObj1.setFeatureName("Scheme");
                menuObj1.setName("Scheme");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.retailor_lifting);
                menuObj1.setFeatureName("Retailer_Lifting");
                menuObj1.setName("Retailer Lifting");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.pop);
                menuObj1.setFeatureName("Pop_Order");
                menuObj1.setName("Pop Order");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.mason_lifting);
                menuObj1.setFeatureName("mason_lifting");
                menuObj1.setName("Mason Lifting\nApproval");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.rssd);
                menuObj1.setFeatureName("Allocation_History");
                menuObj1.setName("RSAR Lifting\nAllocation");
                mMenuList.add(menuObj1);

                menuObj1 = new MenuObj();
                menuObj1.setResourceId(R.drawable.plceorder_rssd);
                menuObj1.setFeatureName("Place_Order");
                menuObj1.setName("New Order Enquiry");
                mMenuList.add(menuObj1);
            }

            MenuObj menuObj1 = new MenuObj();
            menuObj1.setResourceId(R.drawable.rewards);
            menuObj1.setFeatureName("Rewards");
            menuObj1.setName("Rewards");
            mMenuList.add(menuObj1);

            menuObj1 = new MenuObj();
            menuObj1.setResourceId(R.drawable.greetings);
            menuObj1.setFeatureName("Greetings");
            menuObj1.setName("Greetings");
            mMenuList.add(menuObj1);

            menuObj1 = new MenuObj();
            menuObj1.setResourceId(R.drawable.sale_visit_feedback);
            menuObj1.setFeatureName("Sales Visit feedback");
            menuObj1.setName("Sales Visit feedback");
            mMenuList.add(menuObj1);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void initView() {
        try {

            mButtonLogout.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (mDrawerLayout.isDrawerOpen(GravityCompat.START)) {
                        mDrawerLayout.closeDrawer(mLinearLayoutOption);
                    } else {
                        mDrawerLayout.openDrawer(mLinearLayoutOption);
                    }
                }
            });

            //			mDrawerToggle
            new ActionBarDrawerToggle(this, mDrawerLayout, 0, 0) {
                //			mDrawerToggle
                public void onDrawerClosed(View view) {
                    super.onDrawerClosed(view);
                }

                public void onDrawerOpened(View drawerView) {
                    super.onDrawerOpened(drawerView);
                }
            };


            MenuAdapter mMenuAdapter = new MenuAdapter(mContext, R.layout.grid_child, mMenuList);
            mGridViewMenu.setAdapter(mMenuAdapter);

            TextView tv_app_version = (TextView) findViewById(R.id.tv_app_version);
            tv_app_version.setText("App Version : " + BuildConfig.VERSION_NAME);

            if (get_logged_sub_dealer_name(mContext).isEmpty()) {
                mTextViewUserName.setText(Constants.employeeDetailObject.getEmpName());
            } else {
                mTextViewUserName.setText(get_logged_sub_dealer_name(mContext));
            }

        } catch (Exception e) {
            Log.d("TAG", "initView 1: " + e);
            e.printStackTrace();
        }
    }

    public void showMyDealerList() {
        try {
            Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_  showMyDealerList");
            final Dialog mDestinationDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar);
            Window window = mDestinationDialog.getWindow();
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            mDestinationDialog.setContentView(R.layout.select_with_search);
            mDestinationDialog.setCancelable(true);
            window.setStatusBarColor(getResources().getColor(R.color.colorRed_StatusBar));
            ImageView btnback = (ImageView) mDestinationDialog.findViewById(R.id.back);
            btnback.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    mDestinationDialog.dismiss();
                }
            });
            TextView tvDestHeading = (TextView) mDestinationDialog.findViewById(R.id.tvDestHeading);
            tvDestHeading.setText("Please select a Dealer");
            ListView dialogList = (ListView) mDestinationDialog.findViewById(R.id.list);

            final DestinationAdapter destinationAdapter = new DestinationAdapter(this, R.layout.customer_broker_list_child, mDestinationMasterList);

            dialogList.setAdapter(destinationAdapter);

            EditText searchText = (EditText) mDestinationDialog
                    .findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2,
                                          int arg3) {
                    destinationAdapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int position, long arg3) {
                    try {
                        if (HTTPUtils.isConnectionPossible(mContext)) {
                            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                            Constants.selectedDestination = destinationAdapter.getItem(position);
                            Constants.mDestinationCode = Constants.selectedDestination.getDestinationCode();

                            tv_home_caption.setText(Constants.selectedDestination.getDestinationName() + "");
                            set_selected_customer_code(mContext, Constants.selectedDestination.getSubDealerCode());
                            set_selected_customer_name(mContext, Constants.selectedDestination.getDestinationName());
                            set_selected_dealer_sap_code(mContext, Constants.selectedDestination.get_SAP_code());
                            print_Log_d("selected_SAP_code ", get_selected_dealer_sap_code(mContext));


                            downloadTableList.clear();
                            downloadTableList.add("destination_master");
                            downloadTableList.add("branch_dump");
                            downloadTableList.add("self_appraisal_product_wise");

                            mCount = 0;
                            Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_  showMyDealerList  111");
                            DownloadData(mCount, downloadTableList.get(mCount));
                            if (get_user_type(mContext).equalsIgnoreCase("broker")) {
                                load_on_create("");
                                branch_code = mAceDnsDatabase.getBranchCode(get_selected_customer_code(mContext));
                            } else {
                                branch_code = mAceDnsDatabase.getBranchCode(get_emp_or_customer_code(mContext));
                            }
                        } else {
                            Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                        }
                    } catch (Exception e) {
                        e.printStackTrace();
                    } finally {
                        mDestinationDialog.dismiss();
                    }
                }
            });

            mDestinationDialog.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void showLoader() {
        Utils.showProgressDialog(mContext, "Updating please wait..");
    }

    public void dismissLoader() {
        Utils.cancelProgressDialog();
    }

    public void DownloadData(final int task, final String params) {
        Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_ DownloadData loader On");
        showLoader();
        Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_ DownloadData");
        new Thread() {
            public void run() {
                Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_ DownloadData");
                new commonAsyncTaskMaster(mContext, params);

                Message msg = mPrepareSurveyHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareSurveyHandler.sendMessage(msg);
            }
        }.start();
    }

    public void doOnItemClickJob(String featureName) {
        try {
            if (get_user_type(mContext).equalsIgnoreCase("broker")) {
                if (get_selected_customer_name(mContext).matches("")) {
                    Toast.makeText(mContext, "Please select a Dealer", Toast.LENGTH_SHORT).show();
                    return;
                }
            }
            if (featureName.equalsIgnoreCase("Order")) {
                selectedProductMasterList.clear();
                Intent intent = new Intent(mContext, OrderFormActivityAlternateDesign.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("Pending_Invoices")) {
                Intent intent = new Intent(mContext, InvoiceActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("Ledger")) {
                if (get_user_type(mContext).equalsIgnoreCase("dealer") ||
                        get_user_type(mContext).equalsIgnoreCase("broker")) {
                    Intent intent = new Intent(this, LedgerActivity.class);
                    startActivity(intent);
                } else {
                    Intent intent = new Intent(this, SubDealerLedgerActivity.class);
                    startActivity(intent);
                }

            } else if (featureName.equalsIgnoreCase("Track_Orders")) {
                if (get_user_type(mContext).equalsIgnoreCase("dealer") ||
                        get_user_type(mContext).equalsIgnoreCase("broker")) {
                    Intent intent = new Intent(this, TrackOrderActivityNewInv.class);
                    startActivity(intent);
                } else {
                    Intent intent = new Intent(this, SubDealerTrackOrderNewActivityNew.class);
                    startActivity(intent);
                }

            } else if (featureName.equalsIgnoreCase("Payment")) {
                Intent intent = new Intent(this, PaymentActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("Scheme")) {
                Intent intent = new Intent(this, SchemeListPdfActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("Ageing")) {
                Intent intent = new Intent(this, AgeingActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("Performance_Month")) {
                Intent intent = new Intent(mContext, SelfAppraisalLandingActivity.class);
//                Intent intent = new Intent(mContext, TextActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("product_performance")) {
                Intent intent = new Intent(mContext, ProductWiseGraphActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("Tour")) {
                get_tour_details(null);
            } else if (featureName.equalsIgnoreCase("ENGAGEMENTS")) {
                start_ENGAGEMENTS(null);
            } else if (featureName.equalsIgnoreCase("Rewards")) {
                start_Rewards(null);
            } else if (featureName.equalsIgnoreCase("Pop_Order")) {
                pop_product(null);
            } else if (featureName.equalsIgnoreCase("Greetings")) {
                Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse("https://greetings.starcement.co.in/"));
                startActivity(browserIntent);
            } else if (featureName.equalsIgnoreCase("Allocation_History")) {

                Intent intent = new Intent(this, SubDealerShowAllocateActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("Sales Visit feedback")) {

                Intent intent = new Intent(this, ReviewRatingListActivity.class);
                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("Place_Order")) {

                if (get_user_type(mContext).equalsIgnoreCase("dealer") ||
                        get_user_type(mContext).equalsIgnoreCase("broker")) {
                    Intent intent = new Intent(this, PlaceOrderActivity.class);
                    startActivity(intent);
                } else {
                    Intent intent = new Intent(this, PlaceOrderRssdActivity.class);
                    startActivity(intent);
                }
            } else if (featureName.equalsIgnoreCase("mason_lifting")) {
                Intent intent = new Intent(mContext, CommonWebViewActivity.class);
                intent.putExtra("webview_caption", "Mason Lifting");


                //todo vola2715
                if (get_user_type(mContext).equalsIgnoreCase("broker")) {
                    print_Log_d("KEY_SET_1173_SAP_CODE ", get_selected_dealer_sap_code(mContext) + " ");
                    intent.putExtra("webview_url", masson_url + get_selected_dealer_sap_code(mContext));
                } else {
                    intent.putExtra("webview_url", masson_url + get_dealer_id(mContext));
                    print_Log_d("KEY_SET_1174_SAP_CODE ", get_dealer_id(mContext) + " ");
                }


                startActivity(intent);
            } else if (featureName.equalsIgnoreCase("Retailer_Lifting")) {
                if (get_user_type(mContext).equalsIgnoreCase("dealer")) {
                    Intent intent = new Intent(this, DealerLiftingActivity.class);
                    startActivity(intent);
                } else {
                    Intent intent = new Intent(this, NewSubDealerLiftingActivity.class);
                    startActivity(intent);
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void FCMProcess() {
        try {
            ArrayList<NameValuePair> nameValuePairsFCM = new ArrayList<>(3);
            nameValuePairsFCM.add(new BasicNameValuePair("deviceId", Utils.getDeviceId(mContext)));
            nameValuePairsFCM.add(new BasicNameValuePair("emp_code", get_emp_or_customer_code(mContext)));
            nameValuePairsFCM.add(new BasicNameValuePair("registrationid", get_firebase_token(mContext)));
            nameValuePairsFCM.add(new BasicNameValuePair("nick_name", Constants.nickName));
            nameValuePairsFCM.add(new BasicNameValuePair("device_type", "ANDROID"));
            nameValuePairsFCM.add(new BasicNameValuePair("app_version", BuildConfig.VERSION_NAME));
            nameValuePairsFCM.add(new BasicNameValuePair("user_type", get_user_type(mContext)));
            print_Log_d("nameValuePairsFCM ", nameValuePairsFCM.toString());
            //call api and change status to
            new POST_SendFcmId(mContext, nameValuePairsFCM, true, get_emp_or_customer_code(mContext)).execute();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void ivMnuFB(View v) {
        try {
            Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse("https://m.facebook.com/starcements/"));
            startActivity(browserIntent);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void ivMnuYouTube(View v) {
        try {
            Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse("https://www.youtube.com/channel/UCuKSCQask__yLwCWzLd5uSw"));
            startActivity(browserIntent);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void ivMnuFBWeb(View v) {
        try {
            Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse("http://starcement.co.in/"));
            startActivity(browserIntent);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void ivMnuDashboard(View v) {
        try {
            Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(acedns_dashboard_webLink));
            startActivity(browserIntent);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void ivHelpCall(View v) {
        try {
            Intent intent = new Intent(Intent.ACTION_DIAL);
            intent.setData(Uri.parse("tel:180034534500"));
            startActivity(intent);

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void ivNotification(View v) {
        startActivity(new Intent(mContext, NotificationListActivity.class));
    }

    public void ivRefresh(View v) {
        if (HTTPUtils.isConnectionPossible(mContext)) {
            redirection = "MenuActivity";
            Constants.isFirstLoginOfApp = true;

            new DATA_LoadDataDictionaryData(mContext).execute("");

        } else {
            Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
        }
    }

    public void ivSync(View v) {
        if (HTTPUtils.isConnectionPossible(mContext)) {
            redirection = "MenuActivity";
            Constants.isFirstLoginOfApp = true;

            new DATA_LoadDatabaseDetails(mContext).execute("");

        } else {
            Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
        }
    }

    public void ivMenuMail(View v) {
        try {
            /* Create the Intent */
            final Intent emailIntent = new Intent(Intent.ACTION_SEND);

            /* Fill it with Data */
            emailIntent.setType("plain/text");
            emailIntent.putExtra(Intent.EXTRA_EMAIL, new String[]{"customercare@starcement.co.in"});
            emailIntent.putExtra(Intent.EXTRA_SUBJECT, "Subject ");
            emailIntent.putExtra(Intent.EXTRA_TEXT, "Details...");

            /* Send it off to the Activity-Chooser */
            startActivity(Intent.createChooser(emailIntent, "Send mail..."));

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void ivFilter(View view) {
        Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_ ivFilter");
        try {
            if (get_user_type(mContext).equalsIgnoreCase("broker")) {
                mDestinationMasterList = mAceDnsDatabase.getMyDealerList();
                Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_ ivFilter " + mDestinationMasterList.size());
                if (mDestinationMasterList.size() > 0) {
                    showMyDealerList();
                } else {
                    Toast.makeText(mContext, "No Dealer found", Toast.LENGTH_SHORT).show();
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void start_ENGAGEMENTS(View view) {
        try {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                new TRANS_GetEngagement_Asynctask(mContext).execute();
            } else {
                Utils.showToast(mContext, check_internet_connection);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void start_Rewards(View view) {
        if (HTTPUtils.isConnectionPossible(mContext)) {
            new Rewards_AsyncTask(mContext).execute();
        } else {
            show_msg_Dialog(mContext, check_internet_connection);
        }
    }

    public void pop_product(View view) {
        try {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                Intent intent = new Intent(mContext, PopProductActivity.class);
                startActivity(intent);
                mDrawerLayout.closeDrawer(mLinearLayoutOption);
            } else {
                Utils.showToast(mContext, check_internet_connection);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void versionCheck_Dialog(String msg) {
        try {
            AlertDialog.Builder issueBuilder = new AlertDialog.Builder(mContext, R.style.MyDialog);

            TextView tvCPopup = new TextView(mContext);
            tvCPopup.setText(getResources().getString(R.string.app_name));
            tvCPopup.setGravity(Gravity.CENTER);
            tvCPopup.setTextColor(mContext.getResources().getColor(R.color.white));
            tvCPopup.setTextSize(14);
            tvCPopup.setBackgroundColor(mContext.getResources().getColor(R.color.red));
            int margin = 15;
            tvCPopup.setPadding(0, margin * 2, 0, margin * 2);
            issueBuilder.setCustomTitle(tvCPopup);
            issueBuilder.setMessage(msg);

            issueBuilder.setPositiveButton("UPDATE", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/apps/details?id=" + getPackageName())));
                    dialog.dismiss();
                    finishAffinity();
                }
            });

            issueBuilder.setCancelable(false);
            issueBuilder.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void openCamera() {
        try {
            Intent intent = new Intent(this, ImagePickerActivity.class);
            intent.putExtra(ImagePickerActivity.INTENT_IMAGE_PICKER_OPTION, ImagePickerActivity.REQUEST_IMAGE_CAPTURE);

            // setting aspect ratio
            intent.putExtra(ImagePickerActivity.INTENT_LOCK_ASPECT_RATIO, true);
            intent.putExtra(ImagePickerActivity.INTENT_ASPECT_RATIO_X, 1); // 16x9, 1x1, 3:4, 3:2
            intent.putExtra(ImagePickerActivity.INTENT_ASPECT_RATIO_Y, 1);

            // setting maximum bitmap width and height
            intent.putExtra(ImagePickerActivity.INTENT_SET_BITMAP_MAX_WIDTH_HEIGHT, true);
            intent.putExtra(ImagePickerActivity.INTENT_BITMAP_MAX_WIDTH, 1000);
            intent.putExtra(ImagePickerActivity.INTENT_BITMAP_MAX_HEIGHT, 1000);

            mContext.startActivityForResult(intent, REQUEST_IMAGE);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void requestPermission() {
        ActivityCompat.requestPermissions(this, new String[]{
                Manifest.permission.CAMERA
        }, PERMISSION_REQUEST_CODE);
    }

    public void updateProfile() {
        try {
            Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_ updateProfile loader On");
            showLoader();

            RequestParams params = new RequestParams();

            params.put("profile_image", outPutFile, "image/jpeg");
            params.put("the_id", get_emp_or_customer_code(mContext));
            params.put("user_type", get_user_type(mContext));

            print_Log_d("dkeur_P ", params.toString());
            print_Log_d("dkeur_U ", acedns_update_profile_image);

            AsyncHttpClient client = new AsyncHttpClient();
            client.setTimeout(DEFAULT_TIMEOUT);

//        HttpsAsyncHttpClient(client);
            client.post(acedns_update_profile_image, params, new AsyncHttpResponseHandler() {
                @Override
                public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                    String str = new String(responseBody);
                    try {
                        print_Log_d("dkeur_R ", str);
                        str = ApiRes(str);
                        JSONObject reader = new JSONObject(str);
                        Toast.makeText(mContext, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
                        print_Log_d("the_profile_image_url_1325 ", reader.toString() + " ");

                        if (reader.optString("process_status").equalsIgnoreCase("yes")) {

                            set_profile_image_url(mContext, reader.optString("the_profile_image_url"));

                            Glide.with(mContext)
                                    .load(get_profile_image_url(mContext))
                                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                                    .placeholder(R.drawable.cust_bg)
                                    .error(R.drawable.cust_bg)
                                    .into(mImageViewUserPic);

                            if (outPutFile.exists())
                                outPutFile.delete();
                        }
                    } catch (Exception e) {
                        e.printStackTrace();
                    } finally {
                        dismissLoader();
                    }
                }

                @Override
                public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                    dismissLoader();
                    print_Log_d("dkeur_E ", error.toString());
                }


            });
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void get_tour_details(final View view) {

        if (HTTPUtils.isConnectionPossible(mContext)) {
            mDrawerLayout.closeDrawer(mLinearLayoutOption);

            try {
                Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_ get_tour_details loader On");
                showLoader();

                RequestParams params = new RequestParams();

                params.put("emp_code", get_emp_or_customer_code(mContext));
                params.put("user_type", get_user_type(mContext));

                print_Log_d("dealer_wise_tour_data_PARAMS ", params + " ");

                AsyncHttpClient client = new AsyncHttpClient();
                client.setTimeout(DEFAULT_TIMEOUT);

//        HttpsAsyncHttpClient(client);
                client.post(dealer_wise_tour_data_download, params, new AsyncHttpResponseHandler() {
                    @Override
                    public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                        String str = new String(responseBody);
                        try {
                            str = ApiRes(str);
                            JSONObject reader = new JSONObject(str);
                            print_Log_d("tour_url ", dealer_wise_tour_data_download + " ");
                            print_Log_d("tour_RES ", reader + " ");
                            print_Log_d("tour_Par ", params + " ");

                            if (reader.optString("process_status").equalsIgnoreCase("yes")) {
                                final String tour_data = reader.optString("tour_data");
                                final JSONArray jsonArray = new JSONArray(tour_data);
                                String tour_link = jsonArray.getJSONObject(0).optString("tour_link");
                                print_Log_d("tour_link_1541 ", tour_link);

                                Intent intent = new Intent(mContext, CommonWebViewActivity.class);
                                intent.putExtra("webview_caption", "Tour");
                                intent.putExtra("webview_url", tour_link);
                                startActivity(intent);
                            } else {
                                showCommonAlertDialog(mContext, "Tour", "Coming soon!");
                            }
                        } catch (Exception e) {
                            e.printStackTrace();
                        } finally {
                            dismissLoader();
                        }
                    }

                    @Override
                    public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                        dismissLoader();
                    }


                });
            } catch (Exception e) {
                e.printStackTrace();
            }
        } else {
            Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
        }
    }

    public void get_credit_details() {

        if (HTTPUtils.isConnectionPossible(mContext)) {
            mDrawerLayout.closeDrawer(mLinearLayoutOption);

            try {
                Log.d("TAG", "_CALLING_FORM_MAIN_PAGE_ get_credit_details loader On");
                showLoader();

                RequestParams params = new RequestParams();

                if (get_user_type(mContext).equalsIgnoreCase("broker")) {
                    print_Log_d("KEY_SET_1171_SAP_CODE ", get_selected_dealer_sap_code(mContext) + " ");
                    params.put("customer_code", get_selected_dealer_sap_code(mContext));
                } else {
                    params.put("customer_code", get_dealer_id(mContext));
                    print_Log_d("KEY_SET_1172_SAP_CODE ", get_dealer_id(mContext) + " ");

                }

                params.put("from_date", "2022-07-31"); //31-07-2022
                String yyyyMMdd = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(new Date());
                params.put("to_date", yyyyMMdd);
                print_Log_d("dealerwise_credit_limit_s_deposit_PARAMS ", params + " ");

                AsyncHttpClient client = new AsyncHttpClient();
                client.setTimeout(DEFAULT_TIMEOUT);

//        HttpsAsyncHttpClient(client);
                client.post(dealerwise_credit_limit_s_deposit, params, new AsyncHttpResponseHandler() {
                    @Override
                    public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                        String str = new String(responseBody);
                        try {
                            str = ApiRes(str);
                            JSONObject reader = new JSONObject(str);

                            print_Log_d("1765_PARA ", params + " ");
                            print_Log_d("dealerwise_credit_limit_s_deposit_URL ", dealerwise_credit_limit_s_deposit);
                            print_Log_d("dealerwise_credit_limit_s_deposit_RESPONSE ", reader + " ");

                            if (reader.optString("process_status").equalsIgnoreCase("yes")) {
                                final String credit_limit = reader.optString("credit_limit");
                                final String credit_expose = reader.optString("credit_expose");
                                final String Lcamt_1010_SCL = reader.optString("Lcamt_1010");
                                final String Lcamt_1017_SCNEL = reader.optString("Lcamt_1017");

                                final TextView tv_outstanding_bal = (TextView) findViewById(R.id.tv_outstanding_bal);
                                if (!credit_expose.equalsIgnoreCase("null"))
                                    tv_outstanding_bal.setText(credit_expose);

                                final TextView tv_SCL = (TextView) findViewById(R.id.tv_SCL);
                                final TextView tv_SCNEL = (TextView) findViewById(R.id.tv_SCNEL);
                                final TextView tv_credit_limit = (TextView) findViewById(R.id.tv_credit_limit);
                                if (!credit_limit.equalsIgnoreCase("null"))
                                    tv_credit_limit.setText(credit_limit);

                                if (!Lcamt_1010_SCL.equalsIgnoreCase("null"))
                                    tv_SCL.setText(Lcamt_1010_SCL);

                                if (!Lcamt_1017_SCNEL.equalsIgnoreCase("null"))
                                    tv_SCNEL.setText(Lcamt_1017_SCNEL);
                            }

                        } catch (Exception e) {
                            e.printStackTrace();
                        } finally {
                            dismissLoader();
                        }
                    }

                    @Override
                    public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                        dismissLoader();
                    }


                });
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    public void showMonthListDialog(ArrayList<MonthDataSet> data) {
        try {
            final Dialog mDestinationDialog = new Dialog(this, android.R.style.Theme_DeviceDefault_Light_NoActionBar);
            mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            Window window = mDestinationDialog.getWindow();
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            mDestinationDialog.setContentView(R.layout.select_without_search);
            mDestinationDialog.setCancelable(true);
            window.setStatusBarColor(getResources().getColor(R.color.colorRed_StatusBar));
            ImageView btnback = (ImageView) mDestinationDialog.findViewById(R.id.back);
            btnback.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    mDestinationDialog.dismiss();
                }
            });
            TextView title = (TextView) mDestinationDialog.findViewById(R.id.tvDestHeading);
            title.setText("Please select the month");
            ListView dialogList = (ListView) mDestinationDialog.findViewById(R.id.list);

            final MonthListAdapter pAdapter = new MonthListAdapter(this, R.layout.month_list_child, data);
            dialogList.setAdapter(pAdapter);

            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int position, long arg3) {
                    if (pAdapter.getItem(position).getIsMonthAvailable().equalsIgnoreCase("0")) {
                        notValidMonthDialog(pAdapter.getItem(position).getMonthMessage());
                    } else {
                        Intent intent = new Intent(mContext, DealerDeclarationActivity.class);
                        intent.putExtra("date", pAdapter.getItem(position).getMonthId());
                        startActivity(intent);
                    }
                    mDestinationDialog.dismiss();
                }
            });

            mDestinationDialog.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void notValidMonthDialog(String msg) {
        try {
            AlertDialog.Builder issueBuilder = new AlertDialog.Builder(mContext, R.style.MyDialog);

            TextView tvCPopup = new TextView(mContext);
            tvCPopup.setText("SORRY");
            tvCPopup.setGravity(Gravity.CENTER);
            tvCPopup.setTextColor(mContext.getResources().getColor(R.color.white));
            tvCPopup.setTextSize(14);
            tvCPopup.setBackgroundColor(mContext.getResources().getColor(R.color.red));
            int margin = 15;
            tvCPopup.setPadding(0, margin * 2, 0, margin * 2);
            issueBuilder.setCustomTitle(tvCPopup);
            issueBuilder.setMessage(msg);

            issueBuilder.setPositiveButton("Ok", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    dialog.dismiss();
                }
            });

            issueBuilder.setCancelable(false);
            issueBuilder.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }


//    API Calling

    //Check APP Version API
    public final class TRANS_GetAppUpdate_Asynctask extends AsyncTaskCoroutine<String, String> {
        private Activity mContext;
        boolean isUpdateAvaiable = false;
        private String updateMsg = "", redirect = "";
        private JSONObject jo = new JSONObject();

        public TRANS_GetAppUpdate_Asynctask(final Activity mContext, final String redirect) {
            this.mContext = mContext;
            this.redirect = redirect;
            ll_cs_lottery.setVisibility(View.GONE);
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            showLoader();
            Utils.showProgressDialog(mContext, "Please wait..");
        }

        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            try {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    try {
                        final String url = show_latest_app_version;
                        ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);
                        nameValuePairs.add(new BasicNameValuePair("customer_code", get_dealer_id(mContext)));
                        POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);
                        jo = new JSONObject(POST_result);
                        set_server_current_date(mContext, jo.optString("current_date"));
                        if (jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes")) {
                            if (jo.getDouble("android_app_version") > Double.parseDouble(BuildConfig.VERSION_NAME)) {
                                updateMsg = jo.getString("process_message");
                                isUpdateAvaiable = true;
                            }
                        }
                    } catch (Exception e) {
                        POST_result = "Network Failure";
                    }
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
            return POST_result;
        }

        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                if (jo.optString("consumer_scheme_status").equalsIgnoreCase("ACTIVE")) {
                    ll_cs_lottery.setVisibility(View.VISIBLE);
                } else {
                    ll_cs_lottery.setVisibility(View.GONE);
                }

                if (isUpdateAvaiable) {
                    versionCheck_Dialog(updateMsg);
                } else {
                    new HomeSlider(mContext, view_pager2, ll_dots, redirect);
                }
            } catch (Exception e) {
                e.printStackTrace();
                dismissLoader();
            }
        }
    }

    //Check Month List API
    public final class TRANS_MonthList_Asynctask extends AsyncTaskCoroutine<String, String> {
        private Activity mContext;
        private JSONObject jo = new JSONObject();

        public TRANS_MonthList_Asynctask(final Activity mContext, final String redirect) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            showLoader();
            Utils.showProgressDialog(mContext, "Please wait..");
        }

        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            try {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    try {
                        final String url = api_get_month_list+get_dealer_id(mContext);

                        Log.d("DOWNLOAD", "TRANS_MonthList_Asynctask 0: "+url);

                        POST_result = HTTPUtils.getDataByHTTP_GET(mContext, url);

                        jo = new JSONObject(POST_result);
                        dataMonthDataSet.clear();

                        Log.d("DOWNLOAD", "TRANS_MonthList_Asynctask 1: "+POST_result);



                    } catch (Exception e) {
                        POST_result = "Network Failure";
                        notValidMonthDialog("Network Failure. Check your internet connection.");
                        Log.d("DOWNLOAD", "TRANS_MonthList_Asynctask 3: "+e);
                    }
                }
            } catch (Exception e) {
                e.printStackTrace();
                dismissLoader();
                Log.d("DOWNLOAD", "TRANS_MonthList_Asynctask 4: "+e);
            }
            return POST_result;
        }

        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            dismissLoader();
            Log.d("DOWNLOAD", "TRANS_MonthList_Asynctask 5: "+result);
            try{

                for(int i=0;i<jo.getJSONArray("months").length();i++){
                    MonthDataSet mds=new MonthDataSet();
                    String value="0";
                    if(!jo.getJSONArray("months").getJSONObject(i).getBoolean("is_applied")&&jo.getJSONArray("months").getJSONObject(i).getBoolean("cutoff")){
                        value="1";
                    }


                    mds.setMonthId(jo.getJSONArray("months").getJSONObject(i).getString("key"));
                    mds.setMonthTitle(jo.getJSONArray("months").getJSONObject(i).getString("month_name"));
                    mds.setIsMonthAvailable(value);
                    mds.setMonthMessage(jo.getJSONArray("months").getJSONObject(i).getString("message"));

                    dataMonthDataSet.add(mds);
                }
                showMonthListDialog(dataMonthDataSet);
            }catch (Exception e){
                dismissLoader();
                notValidMonthDialog("Something wrong. Contact to Admin.");
                Log.d("DOWNLOAD", "TRANS_MonthList_Asynctask 2: "+e);
            }
        }
    }

    public final class TRANS_GetProfileInformation_Asynctask extends AsyncTaskCoroutine<String, String> {
        Activity mContext;
        JSONObject jo = new JSONObject();

        public TRANS_GetProfileInformation_Asynctask(Activity mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
        }
        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            try {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    try {
                        final String url = api_get_dealer_information+get_dealer_id(mContext);
                        Log.d("TAG", "_DOWNLOAD_ get_exclusive_dealer_name: "+url);
                        POST_result = HTTPUtils.getDataByHTTP_GET(mContext, url);
                    } catch (Exception e) {
                        POST_result = "Network Failure";
                    }
                }
            }
            catch (Exception e){
                e.printStackTrace();
            }
            return POST_result;
        }
        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try{
                JSONObject jo=new JSONObject(result);
                Log.d("TAG", "_DOWNLOAD_ onPostExecute: "+jo);
                if(jo.getString("status").equalsIgnoreCase("yes")){
                    ((MenuActivity)mContext).runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            ll_kismetKiBori.setVisibility(View.VISIBLE);
                        }
                    });
                }else{
                    ((MenuActivity)mContext).runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            ll_kismetKiBori.setVisibility(View.GONE);
                        }
                    });
                }
            } catch (Exception e){
                e.printStackTrace();
                ((MenuActivity)mContext).runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        ll_kismetKiBori.setVisibility(View.GONE);
                    }
                });
            }
        }
    }

//    API Calling


//    Menu Item Click

    //Profile image change function
    public void chooseYourImage(View view) {
        //for Runtime permission
        final CharSequence[] items = {"Camera", "Gallery"};

        AlertDialog.Builder builder = new AlertDialog.Builder(mContext, R.style.MyDialog);
        TextView tvCPopup = new TextView(mContext);
        tvCPopup.setText("Update Your Profile Photo");
        tvCPopup.setGravity(Gravity.CENTER);
        tvCPopup.setTextColor(mContext.getResources().getColor(R.color.white));
        tvCPopup.setTextSize(14);
        tvCPopup.setBackgroundColor(mContext.getResources().getColor(R.color.red));
        int margin = 15;
        tvCPopup.setPadding(0, margin * 2, 0, margin * 2);
        builder.setCustomTitle(tvCPopup);
        builder.setItems(items, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int item) {
                try {
                    if (items[item].equals("Camera")) {
                        try {
                            MarshMallowPermission mMP = new MarshMallowPermission(mContext);
                            if (!mMP.checkPermissionForCamera()) {
                                requestPermission();
                            } else {
                                openCamera();
                            }
                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                    } else if (items[item].equals("Gallery")) {
                        try {
                            Intent intent = new Intent(getApplicationContext(), ImagePickerActivity.class);
                            intent.putExtra(ImagePickerActivity.INTENT_IMAGE_PICKER_OPTION, ImagePickerActivity.REQUEST_GALLERY_IMAGE);

                            // setting aspect ratio
                            intent.putExtra(ImagePickerActivity.INTENT_LOCK_ASPECT_RATIO, true);
                            intent.putExtra(ImagePickerActivity.INTENT_ASPECT_RATIO_X, 1); // 16x9, 1x1, 3:4, 3:2
                            intent.putExtra(ImagePickerActivity.INTENT_ASPECT_RATIO_Y, 1);
                            mContext.startActivityForResult(intent, REQUEST_IMAGE);
                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                } finally {
                    dialog.dismiss();
                }
            }
        });
        builder.setNegativeButton("CANCEL", new DialogInterface.OnClickListener() {

            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();
            }
        });
        builder.show();
    }

    //About Us
    public void myAboutUs(View view) {
        if (HTTPUtils.isConnectionPossible(mContext)) {
            Intent intent = new Intent(mContext, CommonWebViewActivity.class);
            intent.putExtra("webview_caption", "About Us");
            intent.putExtra("webview_url", acedns_about_us);
            startActivity(intent);
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
        } else {
            Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
        }
    }

    //Help
    public void myHelp(View view) {
        try {
            Intent intent = new Intent(mContext, HelpActivity.class);
            startActivity(intent);
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //KYC
    public void myKYC(View view) {
        try {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                Intent intent = new Intent(mContext, KYCActivity.class);
                startActivity(intent);
                mDrawerLayout.closeDrawer(mLinearLayoutOption);
            } else {
                Utils.showToast(mContext, check_internet_connection);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //Payment History
    public void appPaymentHistory(View view) {
        try {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                Intent intent = new Intent(mContext, CommonWebViewActivity.class);
                intent.putExtra("webview_caption", "PAYMENT HISTORY");
                intent.putExtra("webview_url", my_payment_history_weblink + get_emp_or_customer_code(mContext));
                startActivity(intent);
                mDrawerLayout.closeDrawer(mLinearLayoutOption);
            } else {
                Utils.showToast(mContext, check_internet_connection);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //Exclusive Dealer Declaration
    public void exclusiveDealerDeclaration(View view) {
        try {
            new TRANS_MonthList_Asynctask(mContext, "").execute();
//            showMonthListDialog();
//            startActivity(new Intent(mContext, DealerDeclarationActivity.class));
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //Kismet Ki Bori
    public void kismetKiBori(View view){
        try {
//            new TRANS_MonthList_Asynctask(mContext, "").execute();
//            showMonthListDialog();
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
            startActivity(new Intent(mContext, KismetKiBoriActivity.class));
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //Issues
    public void myIssues(View view) {
        try {
            AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext, R.style.MyDialog);
            final CharSequence[] items = {"PHONE", "EMAIL"};

            AlertDG.setItems(items, new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int item) {
                    if (items[item].equals("PHONE")) {
                        dialog.dismiss();
                        ivHelpCall(null);
                    } else if (items[item].equals("EMAIL")) {
                        dialog.dismiss();
                        ivMenuMail(null);
                    } else {
                        dialog.dismiss();
                    }
                }
            });
            AlertDG.setNegativeButton("CANCEL", new DialogInterface.OnClickListener() {

                public void onClick(DialogInterface dialog, int which) {
                    dialog.dismiss();
                }
            });
            AlertDG.setCancelable(true);
            AlertDG.create().show();
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
        }
    }

    //Terms and conditions
    public void Terms_and_Conditions(View view) {
        try {
            Intent intent = new Intent(mContext, DetailsActivity.class);
            intent.putExtra("details_text", getResources().getString(R.string.terms_conditions) + "");
            intent.putExtra("header_text", "Terms and Conditions");
            startActivity(intent);
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //Privacy Policy
    public void myPrivacyPolicy(View view) {
        try {
            Intent intent = new Intent(mContext, WebViewActivity.class);
            intent.putExtra("url","https://starsaathi.com/SAP/privacy.html");
            intent.putExtra("header_text", "Privacy Policy");
            startActivity(intent);
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //Refund Policy
    public void myRefundPolicy(View view) {
        try {
            Intent intent = new Intent(mContext, DetailsActivity.class);
            intent.putExtra("details_text", getResources().getString(R.string.refund_policy) + "");
            intent.putExtra("header_text", "Refund Policy");
            startActivity(intent);
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //Credit Limit
    public void myCreditLimit(View view) {
        try {
            Intent intent = new Intent(mContext, CreditLimitActivity.class);
            startActivity(intent);
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //Consumer Scheme
    public void _con_scheme(View view) {
        try {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                Intent intent = new Intent(mContext, CommonWebViewActivity.class);
                intent.putExtra("webview_caption", "CONSUMER SCHEME");
                intent.putExtra("webview_url", arc_offer + get_emp_or_customer_code(mContext) + "&user_type=" + get_user_type(mContext));
                startActivity(intent);
                mDrawerLayout.closeDrawer(mLinearLayoutOption);
            } else {
                Utils.showToast(mContext, check_internet_connection);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //Consumer Lottery Scheme
    public void myConsumerScheme(View view) {
        try {
            if (get_user_type(mContext).equalsIgnoreCase("broker")) {
                if (get_selected_customer_name(mContext).matches("")) {
                    Toast.makeText(mContext, "Please select a Dealer", Toast.LENGTH_SHORT).show();
                    return;
                }
            }
            Intent intent = new Intent(mContext, ConsumerSchemeActivity.class);
            startActivity(intent);
            mDrawerLayout.closeDrawer(mLinearLayoutOption);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //Log Out
    public void myLogOut(View view) {
        try {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                new TRANS_GetLogOut_Asynctask(mContext).execute("");
            } else {
                Utils.showToast(mContext, check_internet_connection);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

//    Menu Item Click
}
