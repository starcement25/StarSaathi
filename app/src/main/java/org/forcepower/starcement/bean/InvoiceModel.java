package org.forcepower.starcement.bean;

public final class InvoiceModel {
    private String invoice_no, invoice_link;
    private String invoice_date;
    private String delivery_no;
    private String sale_order_no;
    private String app_order_no;
    private String product_name;
    private String invoice_qty;
    private String destination;
    private String truck_no;

    public String getInvoice_no() {
        return invoice_no;
    }

    public void setInvoice_no(final String invoice_no) {
        this.invoice_no = invoice_no;
    }

    public String getInvoice_date() {
        return invoice_date;
    }

    public void setInvoice_date(final String invoice_date) {
        this.invoice_date = invoice_date;
    }

    public String getDelivery_no() {
        return delivery_no;
    }

    public void setDelivery_no(final String delivery_no) {
        this.delivery_no = delivery_no;
    }

    public String getSale_order_no() {
        return sale_order_no;
    }

    public void setSale_order_no(final String sale_order_no) {
        this.sale_order_no = sale_order_no;
    }

    public String getApp_order_no() {
        return app_order_no;
    }

    public void setApp_order_no(final String app_order_no) {
        this.app_order_no = app_order_no;
    }

    public String getProduct_name() {
        return product_name;
    }

    public void setProduct_name(final String product_name) {
        this.product_name = product_name;
    }

    public String getInvoice_qty() {
        return invoice_qty;
    }

    public void setInvoice_qty(final String invoice_qty) {
        this.invoice_qty = invoice_qty;
    }

    public String getDestination() {
        return destination;
    }

    public void setDestination(final String destination) {
        this.destination = destination;
    }

    public String getTruck_no() {
        return truck_no;
    }
    public void setTruck_no(final String truck_no) {
        this.truck_no = truck_no;
    }


    public String get_invoice_link() {
        return invoice_link;
    }
    public void set_invoice_link(final String val) {
        this.invoice_link = val;
    }
}
