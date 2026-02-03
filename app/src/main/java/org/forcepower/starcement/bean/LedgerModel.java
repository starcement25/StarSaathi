package org.forcepower.starcement.bean;

import java.util.Date;

public final class LedgerModel {
    private String customer_code;
    private String dns_customer_code;

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

    public String getVoucher_date() {
        return voucher_date;
    }

    public void setVoucher_date(final String voucher_date) {
        this.voucher_date = voucher_date;
    }

    public String getVoucher_no() {
        return voucher_no;
    }

    public void setVoucher_no(final String voucher_no) {
        this.voucher_no = voucher_no;
    }

    public String getAmount() {
        return amount;
    }

    public void setAmount(final String amount) {
        this.amount = amount;
    }

    public String getBalance() {
        return balance;
    }

    public void setBalance(final String balance) {
        this.balance = balance;
    }

    public String getNarration() {
        return narration;
    }

    public void setNarration(final String narration) {
        this.narration = narration;
    }

    public String getEntry_date() {
        return entry_date;
    }

    public void setEntry_date(final String entry_date) {
        this.entry_date = entry_date;
    }
    public Date get_sort_date_time() {
        return _sort_date_time;
    }

    public void set_sort_date_time(final Date val) {
        this._sort_date_time = val;
    }

    private String voucher_date;
    private String voucher_no;
    private String amount;
    private String balance;
    private String narration;
    private String entry_date;
    private Date _sort_date_time;
}
