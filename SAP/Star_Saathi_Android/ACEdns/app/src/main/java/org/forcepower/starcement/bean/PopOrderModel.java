package org.forcepower.starcement.bean;

public final class PopOrderModel
{
    private String order_id;
    private String order_date;

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

    public String getAddress() {
        return address;
    }

    public void setAddress(final String address) {
        this.address = address;
    }

    public String getPin() {
        return pin;
    }

    public void setPin(final String pin) {
        this.pin = pin;
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

    public String get_prod_image() {
        return prod_image;
    }
    public void set_prod_image(final String val) {
        this.prod_image = val;
    }

    public String get_total_amount() {
        return total_amount;
    }
    public void set_total_amount(final String val) {
        this.total_amount = val;
    }

    public String get_main_order_id() {
        return main_order_id;
    }
    public void set_main_order_id(final String val) {
        this.main_order_id = val;
    }

    public String get_order_status() {
        return order_status;
    }
    public void set_order_status(final String val) {
        this.order_status = val;
    }

    private String customer_name, prod_image, total_amount, main_order_id;
    private String address;
    private String pin;
    private String dns_prod_code;
    private String prod_display_name;
    private String qty, order_status;

}
