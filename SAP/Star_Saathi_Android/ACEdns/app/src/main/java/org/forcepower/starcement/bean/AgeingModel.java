package org.forcepower.starcement.bean;

import java.util.Date;

public final class AgeingModel
{
    public String getCustomercode() {
        return customercode;
    }

    public void setCustomercode(String customercode) {
        this.customercode = customercode;
    }

    public String getDocument_no() {
        return document_no;
    }

    public void setDocument_no(String document_no) {
        this.document_no = document_no;
    }

    public String getDocument_date() {
        return document_date;
    }

    public void setDocument_date(String document_date) {
        this.document_date = document_date;
    }

    public String getInv_amount() {
        return inv_amount;
    }

    public void setInv_amount(String inv_amount) {
        this.inv_amount = inv_amount;
    }

    private String customercode, document_no, document_date, inv_amount;
}
