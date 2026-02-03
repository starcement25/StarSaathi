package org.forcepower.starcement.bean;

public final class LiftingModel {
    public String getProduct_name() {
        return product_name;
    }

    public void setProduct_name(final String product_name) {
        this.product_name = product_name;
    }

    public String getQty_in_bags() {
        return qty_in_bags;
    }

    public void setQty_in_bags(final String qty_in_bags) {
        this.qty_in_bags = qty_in_bags;
    }

    public String getDate_of_lifting() {
        return date_of_lifting;
    }

    public void setDate_of_lifting(final String date_of_lifting) {
        this.date_of_lifting = date_of_lifting;
    }

    public String getDate_of_lifting_show() {
        return date_of_lifting_show;
    }

    public void setDate_of_lifting_show(final String date_of_lifting_show) {
        this.date_of_lifting_show = date_of_lifting_show;
    }

    public String getChallan_number() {
        return challan_number;
    }

    public void setChallan_number(final String challan_number) {
        this.challan_number = challan_number;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(final String status) {
        this.status = status;
    }

    public String getApproved_by() {
        return approved_by;
    }

    public void setApproved_by(final String approved_by) {
        this.approved_by = approved_by;
    }

    public String getApproved_rejection_date() {
        return approved_rejection_date;
    }

    public void setApproved_rejection_date(final String approved_rejection_date) {
        this.approved_rejection_date = approved_rejection_date;
    }

    public String getApproved_rejection_date_show() {
        return approved_rejection_date_show;
    }

    public void setApproved_rejection_date_show(final String approved_rejection_date_show) {
        this.approved_rejection_date_show = approved_rejection_date_show;
    }

    public String getReason_for_rejection() {
        return reason_for_rejection;
    }
    public void setReason_for_rejection(final String reason_for_rejection) {
        this.reason_for_rejection = reason_for_rejection;
    }


    public String get_linked_dealer_name() {
        return linked_dealer_name;
    }
    public void set_linked_dealer_name(final String val) {
        this.linked_dealer_name = val;
    }

    private String product_name,qty_in_bags,date_of_lifting,date_of_lifting_show,
            challan_number,status,approved_by,approved_rejection_date,approved_rejection_date_show,
            reason_for_rejection, linked_dealer_name;
}
