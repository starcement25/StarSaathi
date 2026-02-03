package org.forcepower.starcement.bean;

public final class PopProductModel
{
    public String getDns_prod_code() {
        return dns_prod_code;
    }

    public void setDns_prod_code(String dns_prod_code) {
        this.dns_prod_code = dns_prod_code;
    }

    public String getProd_desc() {
        return prod_desc;
    }

    public void setProd_desc(String prod_desc) {
        this.prod_desc = prod_desc;
    }

    public String getProd_image() {
        return prod_image;
    }

    public void setProd_image(String prod_image) {
        this.prod_image = prod_image;
    }

    public int getMin_order_qty() {
        return min_order_qty;
    }

    public void setMin_order_qty(int min_order_qty) {
        this.min_order_qty = min_order_qty;
    }

    public String getPrice_per_piece() {
        return price_per_piece;
    }

    public void setPrice_per_piece(String price_per_piece) {
        this.price_per_piece = price_per_piece;
    }

    public String getGST_rate() {
        return GST_rate;
    }
    public void setGST_rate(String GST_rate) {
        this.GST_rate = GST_rate;
    }

    public boolean get_visible() {
        return visible;
    }
    public void set_visible(boolean val) {
        this.visible = val;
    }



    public String get_qty() {
        return qty;
    }
    public void set_qty(String val) {
        this.qty = val;
    }


    public String get_u_order_id() {
        return u_order_id;
    }
    public void set_u_order_id(String val) {
        this.u_order_id = val;
    }

    public String get_temp_total() {
        return temp_total;
    }
    public void set_temp_total(final String val) {
        this.temp_total = val;
    }
    public String get_temp_gst() {
        return temp__gst;
    }
    public void set_temp_gst(final String val) {
        this.temp__gst = val;
    }

    public String get_temp_sub_total() {
        return sub_total;
    }
    public void set_sub_total(final String val) {
        this.sub_total = val;
    }

    private String dns_prod_code = "",prod_desc = "",prod_image = "",
            price_per_piece = "",GST_rate = "", qty, u_order_id = "",
            temp__gst = "00.00", temp_total = "00.00", sub_total = "00.00";
    private int min_order_qty = 0;
    private boolean visible;

    public boolean getPayment_gateway() {
        return payment_gateway;
    }

    public void setPayment_gateway(final boolean v) {
        this.payment_gateway = v;
    }

    private boolean payment_gateway = true;
}
