package org.forcepower.starcement.fragments.dealer_lifting.allocated;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_server_current_date;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.ajax_allocation_lifting_invoicewise;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.os.Bundle;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;

import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.ExpandableListView;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.AllocationListModel;
import org.forcepower.starcement.fragments.dealer_lifting.allocated.adapter.AllocatedDealerLiftingAdapter;
import org.forcepower.starcement.fragments.dealer_lifting.allocated.dataset.LifitngAllocatedInvModelGroup;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.MonthYearPickerDialog;
import org.forcepower.starcement.util.Utils;
import org.forcepower.starcement.util.VolleyApiCAll;
import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Objects;

public class AllocatedDealerLiftingFragment extends Fragment {
    private Activity mContext;
    private ArrayList<LifitngAllocatedInvModelGroup> allocatedDataList = new ArrayList<>();
    private AllocatedDealerLiftingAdapter mAdapter;
    private ExpandableListView expListView;
    private TextView tvHeaderText;
    private String selected_customer_code = "", year_month = "";
    private EditText et_SearchSD;
    int previousGroup = -1;

    public AllocatedDealerLiftingFragment() {
    }

    public static AllocatedDealerLiftingFragment newInstance(String year_month) {
        final AllocatedDealerLiftingFragment fragment = new AllocatedDealerLiftingFragment();
        final Bundle args = new Bundle();
        args.putString("year_month", year_month);
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_allocated_dealer_lifting, container, false);
        et_SearchSD = view.findViewById(R.id.et_SearchSD);
        et_SearchSD.setHint("Search...");
        final ImageView ivCrossSD = view.findViewById(R.id.ivCrossSD);
        ivCrossSD.setOnClickListener(v -> et_SearchSD.setText(""));
        et_SearchSD.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                try {
                    final ArrayList<LifitngAllocatedInvModelGroup> temp = new ArrayList<>();
                    for (int i = 0; i < allocatedDataList.size(); i++) {
                        if (allocatedDataList.get(i).getName().toLowerCase().contains(s.toString().toLowerCase())) {
                            temp.add(allocatedDataList.get(i));
                        }
                    }
                    mAdapter.setFilter(temp);
                } catch (Exception e) {
                    Log.d("TAG", "getChildView: " + e.getMessage());
                }
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        return view;
    }

    @Override
    public void onViewCreated(@NonNull final View view, final Bundle savedInstanceState) {
        try {
            mContext = getActivity();
            tvHeaderText = requireActivity().findViewById(R.id.tvHeaderText);
            if (get_user_type(mContext).equalsIgnoreCase("broker")) {
                selected_customer_code = get_selected_dealer_sap_code(mContext);
            } else {
                selected_customer_code = get_dealer_id(mContext);
            }

            expListView = view.findViewById(R.id.exlvTrackOrders);
            mAdapter = new AllocatedDealerLiftingAdapter(mContext, allocatedDataList);
            expListView.setAdapter(mAdapter);
            expListView.setEmptyView(view.findViewById(R.id.empty_text_view));
            expListView.setOnGroupClickListener((parent, v, groupPosition, id) -> {
                final int childCount = parent.getExpandableListAdapter().getChildrenCount(groupPosition);
                if (childCount < 1) {
                    return true;
                }
                if (parent.isGroupExpanded(groupPosition)) {
                    parent.collapseGroup(groupPosition);
                } else {
                    if (previousGroup != -1 && previousGroup != groupPosition) {
                        parent.collapseGroup(previousGroup);
                    }
                    parent.expandGroup(groupPosition);
                    previousGroup = groupPosition;
                }
                return true;
            });

        } catch (Exception e) {
            Log.d("TAG", "getChildView: " + e.getMessage());
        }
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onResume() {
        super.onResume();
        if (HTTPUtils.isConnectionPossible(mContext)) {
            final String header_txt = Utils.changeDateFormat("yyyy-MM-dd", "MMM, yyyy", get_server_current_date(mContext));
            tvHeaderText.setText("ALLOCATED (" + header_txt + ")");
            year_month = Utils.changeDateFormat("yyyy-MM-dd", "yyyy-MM", get_server_current_date(mContext));
            et_SearchSD.setText("");
            reload();
        } else {
            allocatedDataList = new ArrayList<>();
            mAdapter.setFilter(allocatedDataList);
            show_msg_alert(mContext, check_internet_connection, false);
        }
    }

    @SuppressLint("SetTextI18n")
    public void allocatedFilter() {
        try {
            final MonthYearPickerDialog pd = new MonthYearPickerDialog();
            pd.setListener((view, selectedYear, selectedMonth, selectedDay) -> {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    final String header_txt = Utils.changeDateFormat("yyyy-MM", "MMM, yyyy", selectedYear + "-" + selectedMonth);
                    tvHeaderText.setText("ALLOCATED (" + header_txt + ")");
                    year_month = Utils.changeDateFormat("yyyy-MM", "yyyy-MM", selectedYear + "-" + selectedMonth);
                    et_SearchSD.setText("");
                    reload();
                } else {
                    show_msg_alert(mContext, check_internet_connection, false);
                }
            });
            //
            pd.show(getChildFragmentManager(), "MonthYearPickerDialog");
        } catch (Exception e) {
            Log.d("TAG", "getChildView: " + e.getMessage());
        }
    }

    public void reload() {
        try {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                final Map<String, String> jsonObject = new HashMap<>();
                jsonObject.put("customer_id", selected_customer_code);
                jsonObject.put("year_month", year_month);
                if (get_user_type(mContext).equalsIgnoreCase("dealer") || get_user_type(mContext).equalsIgnoreCase("broker")) {
                    jsonObject.put("user_type", "DEALER");
                } else {
                    jsonObject.put("user_type", "RSSD");
                }
                allocatedDataList = new ArrayList<>();
                mAdapter = new AllocatedDealerLiftingAdapter(mContext, allocatedDataList);
                expListView.setAdapter(mAdapter);
                _doPOSTcall_(jsonObject);
            } else {
                Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
            }
        } catch (Exception e) {
            Log.d("TAG", "getChildView: " + e.getMessage());
        }
    }

    private void _doPOSTcall_(final Map<String, String> jsonObject) {
        try {
            Utils.showProgressDialog(mContext, "");

            final String url = ajax_allocation_lifting_invoicewise;
            Log.d("TAG", "_DOWNLOAD_ _doPOSTcall_: " + url + " \n" + jsonObject);
            final VolleyApiCAll volleyApiCAll = new VolleyApiCAll(mContext);
            volleyApiCAll.makeServiceCallPost_Time(jsonObject, url, result -> {
                try {
                    JSONObject jo;
                    allocatedDataList = new ArrayList<>();
                    if ("initial".matches("add_p")) {
                        allocatedDataList.remove(allocatedDataList.size() - 1);
                    }

                    if (result.matches("VOLLEY_NETWORK_ERROR")) {
                        Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                    } else {
                        try {
                            jo = new JSONObject(result);
                            if (jo.has("process_status")) {
                                if (jo.getString("process_status").toLowerCase().matches("yes")) {
                                    final String allocation_data = jo.getString("allocation_data");
                                    final JSONArray jsonArray = new JSONArray(allocation_data);
                                    List<String> nameList = new ArrayList<>();
                                    for (int i = 0; i < jsonArray.length(); i++) {
                                        final JSONObject e = jsonArray.getJSONObject(i);
                                        int check = 1;
                                        for (int j = 0; j < nameList.size(); j++) {
                                            if (nameList.get(j).equalsIgnoreCase(e.optString("counter_name"))) {
                                                check = 0;
                                                break;
                                            }
                                        }
                                        if (check == 1) {
                                            nameList.add(e.optString("counter_name"));
                                        }
                                    }
                                    Log.d("TAG", "_DOWNLOAD_ doInBackground: " + nameList);

                                    for (var a = 0; a < nameList.size(); a++) {
                                        final String currentCustomerName = nameList.get(a) != null ? nameList.get(a).trim() : "";
                                        final LifitngAllocatedInvModelGroup temp = new LifitngAllocatedInvModelGroup();
                                        ArrayList<AllocationListModel> list = new ArrayList<>();
                                        for (int i = 0; i < jsonArray.length(); i++) {
                                            final JSONObject e = jsonArray.getJSONObject(i);
                                            final String jsonCustomerName = e.optString("counter_name", "").trim();
                                            if (!currentCustomerName.isEmpty() && currentCustomerName.equalsIgnoreCase(jsonCustomerName)) {
                                                final AllocationListModel lM = new AllocationListModel();
                                                lM.setDns_prod_code("");
                                                lM.setProd_desc(e.optString("prod_desc"));
                                                lM.setAllocation_qty(e.optString("allocation_qty"));
                                                lM.setDate_and_time(e.optString("date_and_time"));
                                                lM.setOrder_id(e.optString("order_id"));
                                                lM.setChallan_no(e.optString("inv_no"));
                                                lM.setChallan_date(e.optString("inv_date"));
                                                lM.setCounter_name(e.optString("counter_name"));

                                                try {
                                                    lM.setIs_deleted(e.optInt("is_deleted"));
                                                } catch (Exception e1) {
                                                    lM.setIs_deleted(0);
                                                }
                                                list.add(lM);
                                            }
                                        }
                                        temp.setName(currentCustomerName);
                                        temp.setOrder_challan_data(list);
                                        allocatedDataList.add(temp);
                                    }
                                    try {
                                        Gson gson = new GsonBuilder().setPrettyPrinting().create();
                                        String jsonOutput = gson.toJson(allocatedDataList);
                                        Log.d("_DOWNLOAD_ DATA_LIST_JSON", jsonOutput);
                                    } catch (Exception e) {
                                        Log.d("_DOWNLOAD_ DATA_LIST_JSON ERROR", Objects.requireNonNull(e.getMessage()));
                                    }
                                    mAdapter.setFilter(allocatedDataList);
                                } else {
                                    show_msg_alert(mContext, jo.optString("process_message"), false);
                                }
                            }
                        } catch (Exception e) {
                            Log.d("_DOWNLOAD_ DATA_LIST_JSON ERROR", Objects.requireNonNull(e.getMessage()));
                        }
                    }

                } catch (Exception e) {
                    Log.d("_DOWNLOAD_ DATA_LIST_JSON ERROR", Objects.requireNonNull(e.getMessage()));
                } finally {
                    Utils.cancelProgressDialog();
                }
            });
        } catch (Exception e) {
            Log.d("_DOWNLOAD_ DATA_LIST_JSON ERROR", Objects.requireNonNull(e.getMessage()));
        }
    }
}