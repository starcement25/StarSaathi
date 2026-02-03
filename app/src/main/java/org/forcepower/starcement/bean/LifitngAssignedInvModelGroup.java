package org.forcepower.starcement.bean;

import java.util.ArrayList;

public class LifitngAssignedInvModelGroup {
    String name;
    private ArrayList<LifitngAssignedInvModel> order_challan_data = new ArrayList<>();

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public ArrayList<LifitngAssignedInvModel> getOrder_challan_data() {
        return order_challan_data;
    }

    public void setOrder_challan_data(ArrayList<LifitngAssignedInvModel> order_challan_data) {
        this.order_challan_data = order_challan_data;
    }
}
