package org.forcepower.starcement.bean;

import java.util.ArrayList;

public final class LifitngAssignedInvModel {
    private boolean showAllocateButton = false;
    private double RemainingQty = 0;
    private String order_id, order_date, customer_name, destination_name, dns_prod_code,
            prod_display_name, qty, freight, STATUS, allocation_qty;
    private ArrayList<LiftingAssignInvModelChild> order_challan_data = new ArrayList<>();


    public boolean get_showAllocateButton() {
        return showAllocateButton;
    }

    public void set_showAllocateButton(final boolean v) {
        this.showAllocateButton = v;
    }

    public double getRemainingQty() {
        return RemainingQty;
    }

    public void setRemainingQty(final double val) {
        this.RemainingQty = val;
    }

    public String getOrder_id() {
        return order_id;
    }

    public void setOrder_id(final String order_id) {
        this.order_id = order_id;
    }

    public String getOrder_date() {
        return order_date;
    }

    public void setOrder_date(final String order_date) {
        this.order_date = order_date;
    }

    public String getCustomer_name() {
        return customer_name;
    }

    public void setCustomer_name(final String customer_name) {
        this.customer_name = customer_name;
    }

    public String getDestination_name() {
        return destination_name;
    }

    public void setDestination_name(final String destination_name) {
        this.destination_name = destination_name;
    }

    public String getDns_prod_code() {
        return dns_prod_code;
    }

    public void setDns_prod_code(final String dns_prod_code) {
        this.dns_prod_code = dns_prod_code;
    }

    public String getProd_display_name() {
        return prod_display_name;
    }

    public void setProd_display_name(final String prod_display_name) {
        this.prod_display_name = prod_display_name;
    }

    public String getQty() {
        return qty;
    }

    public void setQty(final String qty) {
        this.qty = qty;
    }

    public String getFreight() {
        return freight;
    }

    public void setFreight(final String freight) {
        this.freight = freight;
    }

    public String getSTATUS() {
        return STATUS;
    }

    public void setSTATUS(final String STATUS) {
        this.STATUS = STATUS;
    }

    public String get_allocation_qty() {
        return allocation_qty;
    }

    public void set_allocation_qty(final String v) {
        this.allocation_qty = v;
    }

    public ArrayList<LiftingAssignInvModelChild> getOrder_challan_data() {
        return order_challan_data;
    }

    public void setOrder_challan_data(final ArrayList<LiftingAssignInvModelChild> order_challan_data) {
        this.order_challan_data = order_challan_data;
    }
}