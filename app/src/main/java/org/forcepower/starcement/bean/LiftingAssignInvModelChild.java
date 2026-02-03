package org.forcepower.starcement.bean;

public final class LiftingAssignInvModelChild {
    private boolean showAllocateButton = false;
    private String ch_uid, apporderno, erporderno, challanno,
            invno, invdt, rod_display_name, invqty, customer_code, truckno,
            destination, available_allocation_qty;


    public String getCh_uid() {
        return ch_uid;
    }

    public void setCh_uid(final String ch_uid) {
        this.ch_uid = ch_uid;
    }

    public String getApporderno() {
        return apporderno;
    }

    public void setApporderno(final String apporderno) {
        this.apporderno = apporderno;
    }

    public String getErporderno() {
        return erporderno;
    }

    public void setErporderno(final String erporderno) {
        this.erporderno = erporderno;
    }

    public String getChallanno() {
        return challanno;
    }

    public void setChallanno(final String challanno) {
        this.challanno = challanno;
    }

    public String getInvno() {
        return invno;
    }

    public void setInvno(final String invno) {
        this.invno = invno;
    }

    public String getInvdt() {
        return invdt;
    }

    public void setInvdt(final String invdt) {
        this.invdt = invdt;
    }

    public String getProd_display_name() {
        return rod_display_name;
    }

    public void setProd_display_name(final String rod_display_name) {
        this.rod_display_name = rod_display_name;
    }

    public String getInvqty() {
        return invqty;
    }

    public void setInvqty(final String invqty) {
        this.invqty = invqty;
    }

    public String getCustomer_code() {
        return customer_code;
    }

    public void setCustomer_code(final String customer_code) {
        this.customer_code = customer_code;
    }

    public String getTruckno() {
        return truckno;
    }

    public void setTruckno(final String truckno) {
        this.truckno = truckno;
    }

    public String getDestination() {
        return destination;
    }

    public void setDestination(final String destination) {
        this.destination = destination;
    }

    public String getAvailable_allocation_qty() {
        return available_allocation_qty;
    }

    public void setAvailable_allocation_qty(final String available_allocation_qty) {
        this.available_allocation_qty = available_allocation_qty;
    }

    public boolean get_showDisAllocateButton() {
        return showAllocateButton;
    }

    public void set_showDisAllocateButton(final boolean v) {
        this.showAllocateButton = v;
    }
}
