package org.forcepower.starcement.bean;

public final class LifitngAssignedInv
{
    public String getAvailable_allocation_qty() {
        return available_allocation_qty;
    }

    public void setAvailable_allocation_qty(String available_allocation_qty) {
        this.available_allocation_qty = available_allocation_qty;
    }

    public String getInv_qty() {
        return inv_qty;
    }

    public void setInv_qty(String inv_qty) {
        this.inv_qty = inv_qty;
    }

    public String getINVNO() {
        return INVNO;
    }

    public void setINVNO(String INVNO) {
        this.INVNO = INVNO;
    }

    private String INVNO;
    private String inv_qty;
    private String available_allocation_qty;

    public String getProd_display_name() {
        return prod_display_name;
    }

    public void setProd_display_name(String prod_display_name) {
        this.prod_display_name = prod_display_name;
    }

    private String prod_display_name;
}
