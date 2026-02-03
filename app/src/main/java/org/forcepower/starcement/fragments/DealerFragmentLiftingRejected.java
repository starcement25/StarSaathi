package org.forcepower.starcement.fragments;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.acedns_star_show_reject_lifting_history_for_dealer;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;

import android.app.Activity;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.DealerLiftingApprovedRejectedAdapter_Date;
import org.forcepower.starcement.bean.DealerLifitngModel;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.MyCallback;
import org.forcepower.starcement.util.VolleyApiCAll;
import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;


public final class DealerFragmentLiftingRejected extends Fragment {
    private Activity mContext;
    private ArrayList<DealerLifitngModel> images = new ArrayList<>();
    private DealerLiftingApprovedRejectedAdapter_Date mAdapter;
    private RecyclerView rv_Pending;
    private int page_no_P = 1;
    protected Handler handler_p;
    // The minimum amount of items to have below your current scroll position
    // before loading more.
    private final int visibleThreshold = 5;
    private int lastVisibleItem, totalItemCount;
    private boolean loading;
    public void setLoaded() {
        loading = false;
    }
    public void setLoading() {
        loading = true;
    }
    private MyCallback callback;
    private String search_sub_dealer_name = "";

    public DealerFragmentLiftingRejected()
    {

    }

    public static DealerFragmentLiftingRejected newInstance(final String year_month)
    {
        // Required empty public constructor
        final DealerFragmentLiftingRejected fragmentFirst = new DealerFragmentLiftingRejected();
        final Bundle args = new Bundle();
        args.putString("year_month", year_month);
        fragmentFirst.setArguments(args);
        return fragmentFirst;
    }
//    public String get_thisCategory() {return getArguments().getString("thisCategory");}

    @Override
    public View onCreateView(final LayoutInflater inflater, final ViewGroup container, final Bundle savedInstanceState)
    {
        return inflater.inflate(R.layout.fragment_pending, container, false);
    }

    @Override
    public void onViewCreated(final View view, final Bundle savedInstanceState)
    {
        try
        {
            mContext = getActivity();
            if(get_user_type(mContext).equalsIgnoreCase("broker"))
            {
                selected_customr_code = get_selected_customer_code(mContext);
            }
            else
            {
                selected_customr_code = get_emp_or_customer_code(mContext);
            }
            handler_p = new Handler();
            rv_Pending = (RecyclerView) view.findViewById(R.id.recycler_view);
            rv_Pending.setHasFixedSize(true);
            rv_Pending.setLayoutManager(new LinearLayoutManager(mContext));

            images = new ArrayList<>();
            mAdapter = new DealerLiftingApprovedRejectedAdapter_Date(mContext, images);
            rv_Pending.setAdapter(mAdapter);

            final EditText et_SearchSD = (EditText) view.findViewById(R.id.et_SearchSD);
            final ImageView ivCrossSD = (ImageView) view.findViewById(R.id.ivCrossSD);
            ivCrossSD.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    et_SearchSD.setText("");
                    search_sub_dealer_name = "";
                    reload();

                    final InputMethodManager in = (InputMethodManager) mContext.getSystemService(Context.INPUT_METHOD_SERVICE);
                    in.hideSoftInputFromWindow(ivCrossSD.getWindowToken(), 0);
                }
            });
            et_SearchSD.setOnEditorActionListener(new TextView.OnEditorActionListener() {
                @Override
                public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
                    if (actionId == EditorInfo.IME_ACTION_SEARCH) {
                        search_sub_dealer_name = et_SearchSD.getText().toString().trim();
                        reload();

                        final InputMethodManager in = (InputMethodManager) mContext.getSystemService(Context.INPUT_METHOD_SERVICE);
                        in.hideSoftInputFromWindow(ivCrossSD.getWindowToken(), 0);
                        return true;
                    }
                    return false;
                }
            });

            callback = new MyCallback() {

                public void callbackCall() {
                    // callback code goes here
                    onLoadMore_();
                }
            };
            if (rv_Pending.getLayoutManager() instanceof final LinearLayoutManager linearLayoutManager)
            {
                rv_Pending
                        .addOnScrollListener(new RecyclerView.OnScrollListener() {
                            @Override
                            public void onScrolled(@NonNull RecyclerView recyclerView,
                                                   int dx, int dy) {
                                super.onScrolled(recyclerView, dx, dy);

                                totalItemCount = linearLayoutManager.getItemCount();
                                lastVisibleItem = linearLayoutManager
                                        .findLastVisibleItemPosition();
                                if(totalItemCount > 9)
                                    if (!loading
                                            && totalItemCount <= (lastVisibleItem + visibleThreshold)) {
                                        // End has been reached
                                        // Do something
                                        callback.callbackCall();

                                        loading = true;
                                    }
                            }
                        });
            }
            //

//            reload();
        }
        catch (final Exception e)
        {
            e.printStackTrace();
        }
    }
    public void reload()
    {
        try
        {
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                final Map<String, String> jsonObject = new HashMap<>();

                jsonObject.put("dealer_cust_code", selected_customr_code);
                jsonObject.put("year_month", get_thisCategory());
                jsonObject.put("search_sub_dealer_name", search_sub_dealer_name);

                page_no_P = 1;
                images = new ArrayList<>();
                mAdapter = new DealerLiftingApprovedRejectedAdapter_Date(mContext, images);
                rv_Pending.setAdapter(mAdapter);
                jsonObject.put("page_no", page_no_P + "");

                _doPOSTcall_(jsonObject, "initial");
            }
            else
            {
                Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    
    public String get_thisCategory()
    {
        assert getArguments() != null;
        return getArguments().getString("year_month", "");
    }
    private void _doPOSTcall_(final Map<String, String> jsonObject, final String type)
    {
        try
        {
            final String page = jsonObject.get("page_no") + "";
            final String year_month = jsonObject.get("year_month") + "";
            print_Log_d("amitabha2715_200_page ", page+"");
            print_Log_d("RJT69y7 ", get_thisCategory()+"");
            print_Log_d("RJT69y7 ", jsonObject+"");

            final String url = acedns_star_show_reject_lifting_history_for_dealer;
            //making post call
            final VolleyApiCAll volleyApiCAll = new VolleyApiCAll(mContext);
            volleyApiCAll.makeServiceCallPost(jsonObject, url, new VolleyApiCAll.VolleyCallback()
            {
                @Override
                public void onSuccessResponse(String result)
                {
                    try
                    {
                        print_Log_d("amitabha2715_236_Reject ", url+"");
                        print_Log_d("amitabha2715_237_Reject ", jsonObject+"");
                        print_Log_d("amitabha2715_238_Reject ", result+"");

                        if(type.matches("add_p"))
                        {
                            //   remove progress item
                            images.remove(images.size() - 1);
                            mAdapter.notifyItemRemoved(images.size());
                        }

                        if(result.matches("VOLLEY_NETWORK_ERROR"))
                        {
                            Toast.makeText(mContext, "" + check_internet_connection, Toast.LENGTH_SHORT).show();
                        }
                        else
                        {
                            try
                            {
                                print_Log_d("LIFTING_222_Rejected ", url+"");
                                print_Log_d("LIFTING_222_Rejected ", result+"");

                                final JSONObject jo = new JSONObject(result);
                                if(jo.has("process_status"))
                                {
                                    if(jo.getString("process_status").toLowerCase().matches("yes"))
                                    {
                                        final String lifting_history_data  = jo.getString("lifting_history_data");
                                        final JSONArray jsonArray = new JSONArray(lifting_history_data );

                                        for (int i = 0; i < jsonArray.length(); i++)
                                        {
                                            final JSONObject e = jsonArray.getJSONObject(i);
                                            final DealerLifitngModel cDH = new DealerLifitngModel();
                                            cDH.setLid(e.optString("lid"));
                                            cDH.setProduct_name(e.optString("product_name"));
                                            cDH.setQty_in_bags(e.optString("qty_in_bags"));
                                            cDH.setDate_of_lifting(e.optString("date_of_lifting"));
                                            cDH.setDate_of_lifting_show(e.optString("date_of_lifting_show"));
                                            cDH.setChallan_number(e.optString("challan_number"));
                                            cDH.setStatus(e.optString("status"));
                                            cDH.setApproved_rejection_date(e.optString("approved_rejection_date"));
                                            cDH.setApproved_rejection_date_show(e.optString("approved_rejection_date_show"));
                                            cDH.setReason_for_rejection(e.optString("reason_for_rejection"));
                                            cDH.setSub_dealer_cust_code(e.optString("sub_dealer_cust_code"));
                                            cDH.setSub_dealer_rssd_name(e.optString("sub_dealer_rssd_name"));
                                            cDH.setSub_dealer_rssd_code(e.optString("sub_dealer_rssd_code"));
                                            cDH.setSub_dealer_rssd_sap_code(e.optString("sub_dealer_rssd_sap_code"));
                             
                                            images.add(cDH);
                                        }

                                    }
                                }
                            }
                            catch (Exception e)
                            {
                                e.printStackTrace();
                            }
                            finally
                            {

                                page_no_P++;
                                if(mAdapter.getItemCount() == 0)
                                {
                                    mAdapter.setFilter(images);
                                }
                                else if(images.size() > 0)
                                {
                                    mAdapter.notifyItemInserted(images.size());
                                }

                                if(mAdapter.getItemCount() > 0)
                                {
                                    rv_Pending.setVisibility(View.VISIBLE);
                                }
                                else
                                {
                                    rv_Pending.setVisibility(View.GONE);
                                }
                            }
                        }
                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                    finally
                    {
                        //
                        setLoaded();
                    }
                }
            });

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void onLoadMore_()
    {
        try
        {
            if(page_no_P > 1)
            {
                images.add(null);
                mAdapter.notifyItemInserted(images.size() - 1);

                handler_p.postDelayed(new Runnable() {
                    @Override
                    public void run() {
                        final Map<String, String> jsonObject = new HashMap<>();

                        jsonObject.put("dealer_cust_code", selected_customr_code);
                        jsonObject.put("year_month", get_thisCategory());
                        jsonObject.put("search_sub_dealer_name", search_sub_dealer_name);

                        jsonObject.put("page_no", page_no_P + "");

                        _doPOSTcall_(jsonObject, "add_p");
                        //or you can add all at once but do not forget to call pAdapter.notifyDataSetChanged();
                    }
                }, 2000);
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
}
