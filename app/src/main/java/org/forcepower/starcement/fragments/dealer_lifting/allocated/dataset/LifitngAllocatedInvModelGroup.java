package org.forcepower.starcement.fragments.dealer_lifting.allocated.dataset;

import org.forcepower.starcement.bean.AllocationListModel;

import java.util.ArrayList;

public class LifitngAllocatedInvModelGroup {
    String name;
    private ArrayList<AllocationListModel> order_challan_data = new ArrayList<>();

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public ArrayList<AllocationListModel> getOrder_challan_data() {
        return order_challan_data;
    }

    public void setOrder_challan_data(ArrayList<AllocationListModel> order_challan_data) {
        this.order_challan_data = order_challan_data;
    }
}
