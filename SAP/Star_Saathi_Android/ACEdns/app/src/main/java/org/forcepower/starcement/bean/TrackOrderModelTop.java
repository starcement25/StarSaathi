package org.forcepower.starcement.bean;

import java.util.ArrayList;

public final class TrackOrderModelTop
{
    public String getApporderno() {
        return apporderno;
    }

    public void setApporderno(String apporderno) {
        this.apporderno = apporderno;
    }

    public String getOrder_for() {
        return order_for;
    }

    public void setOrder_for(String order_for) {
        this.order_for = order_for;
    }

    public String getOrder_full_date_time() {
        return order_full_date_time;
    }

    public void setOrder_full_date_time(String order_full_date_time) {
        this.order_full_date_time = order_full_date_time;
    }

    private String apporderno, order_for, order_full_date_time;
    public String getErporderno() {
        return erporderno;
    }

    public void setErporderno(final String erporderno) {
        this.erporderno = erporderno;
    }

    public String getErporderdt() {
        return erporderdt;
    }

    public void setErporderdt(final String erporderdt) {
        this.erporderdt = erporderdt;
    }

    public String getCustomer_code() {
        return customer_code;
    }

    public void setCustomer_code(final String customer_code) {
        this.customer_code = customer_code;
    }

    public String getDns_customer_code() {
        return dns_customer_code;
    }

    public void setDns_customer_code(final String dns_customer_code) {
        this.dns_customer_code = dns_customer_code;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(final String status) {
        this.status = status;
    }

    public String getProd_code() {
        return prod_code;
    }

    public void setProd_code(final String prod_code) {
        this.prod_code = prod_code;
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

    public String getDestination_address() {
        return destination_address;
    }

    public void setDestination_address(final String destination_address) {
        this.destination_address = destination_address;
    }

    public String getIs_confirmed_material_received() {
        return is_confirmed_material_received;
    }

    public void setIs_confirmed_material_received(final String is_confirmed_material_received) {
        this.is_confirmed_material_received = is_confirmed_material_received;
    }

    public String getQuantity_checking() {
        return quantity_checking;
    }

    public void setQuantity_checking(final String quantity_checking) {
        this.quantity_checking = quantity_checking;
    }

    public String getQuality_checking() {
        return quality_checking;
    }

    public void setQuality_checking(final String quality_checking) {
        this.quality_checking = quality_checking;
    }

    public String getRemarks() {
        return remarks;
    }

    public void setRemarks(final String remarks) {
        this.remarks = remarks;
    }

    public ArrayList<TrackOrderModelChild> getOrder_challan_data() {
        return order_challan_data;
    }

    public void setOrder_challan_data(final ArrayList<TrackOrderModelChild> order_challan_data) {
        this.order_challan_data = order_challan_data;
    }
    private String erporderno;
    private String erporderdt;
    private String customer_code;
    private String dns_customer_code;
    private String status;
    private String prod_code;
    private String dns_prod_code;
    private String prod_display_name;
    private String qty;
    private String freight = "";
    private String destination_address;
    private String is_confirmed_material_received;
    private String quantity_checking;
    private String quality_checking;
    private String remarks;
    private ArrayList<TrackOrderModelChild>order_challan_data = new ArrayList<>();
}
