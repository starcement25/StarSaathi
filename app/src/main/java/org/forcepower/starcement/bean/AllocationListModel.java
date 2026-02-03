package org.forcepower.starcement.bean;

public final class AllocationListModel {
    public String getAllocation_qty() {
        return allocation_qty;
    }

    public void setAllocation_qty(String allocation_qty) {
        this.allocation_qty = allocation_qty;
    }

    public String getChallan_date() {
        return challan_date;
    }

    public void setChallan_date(String challan_date) {
        this.challan_date = challan_date;
    }

    public String getChallan_no() {
        return challan_no;
    }

    public void setChallan_no(String challan_no) {
        this.challan_no = challan_no;
    }

    public String getCounter_name() {
        return counter_name;
    }

    public void setCounter_name(String counter_name) {
        this.counter_name = counter_name;
    }

    public String getDate_and_time() {
        return date_and_time;
    }

    public void setDate_and_time(String date_and_time) {
        this.date_and_time = date_and_time;
    }

    public String getDns_prod_code() {
        return dns_prod_code;
    }

    public void setDns_prod_code(String dns_prod_code) {
        this.dns_prod_code = dns_prod_code;
    }

    public String getOrder_id() {
        return order_id;
    }

    public void setOrder_id(String order_id) {
        this.order_id = order_id;
    }

    public String getProd_desc() {
        return prod_desc;
    }

    public void setProd_desc(String prod_desc) {
        this.prod_desc = prod_desc;
    }

    public int getIs_deleted(){return  is_deleted;}

    public void setIs_deleted(int is_deleted){this.is_deleted=is_deleted;}

        private String dns_prod_code,prod_desc,allocation_qty,date_and_time,
            order_id,challan_no,challan_date,counter_name;

    private int is_deleted;
}
