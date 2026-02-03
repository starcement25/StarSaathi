package org.forcepower.starcement.bean;

public final class LiftingAssignModelChild
{
    public String getChallanno() {
        return challanno;
    }

    public void setChallanno(final String val) {
        this.challanno = val;
    }

    public String getDispatch_date() {
        return dispatch_date;
    }

    public void setDispatch_date(final String val) {
        this.dispatch_date = val;
    }

    public String getDispatch_qty() {
        return dispatch_qty;
    }

    public void setDispatch_qty(final String val) {
        this.dispatch_qty = val;
    }

    private String challanno;
    private String dispatch_date;
    private String dispatch_qty;

    public String getAvailable_allocation_qty() {
        return available_allocation_qty;
    }

    public void setAvailable_allocation_qty(String available_allocation_qty) {
        this.available_allocation_qty = available_allocation_qty;
    }

    private String available_allocation_qty;

    public boolean get_showDisAllocateButton() {
        return showDisAllocateButton;
    }

    public void set_showDisAllocateButton(final boolean v) {
        this.showDisAllocateButton = v;
    }

    private boolean showDisAllocateButton = false;
}
