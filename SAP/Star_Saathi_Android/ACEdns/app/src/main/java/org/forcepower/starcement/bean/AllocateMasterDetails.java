package org.forcepower.starcement.bean;


public final class AllocateMasterDetails {

	String prodCode = "";

	String grpCode = "";
	String brndCode = "";

	String prod_desc_name = "";

	float qty = 0;
	String dnsProdCode= "";

	public String getChallan_no() {
		return challan_no;
	}

	public void setChallan_no(final String val) {
		this.challan_no = val;
	}

	String challan_no = "";

	public String getLifting_order_id() {
		return lifting_order_id;
	}

	public void setLifting_order_id(final String val) {
		this.lifting_order_id = val;
	}

	public boolean isLimitExceed() {
		return limitExceed;
	}

	String lifting_order_id = "";



	public String getDnsProdCode() {
		return dnsProdCode;
	}
	public void setDnsProdCode(final String dnsProdCode) {
		this.dnsProdCode = dnsProdCode;
	}

	public float getQty() {
		return qty;
	}
	public void setQty(final float qty) {
		this.qty = qty;
	}

	public String getGrpCode() {
		return grpCode;
	}
	public void setGrpCode(final String grpCode) {
		this.grpCode = grpCode;
	}

	public String getBrndCode() {
		return brndCode;
	}
	public void setBrndCode(final String brndCode) {
		this.brndCode = brndCode;
	}
	public String getProdCode() {
		return prodCode;
	}
	public void setProdCode(final String prodCode) {
		this.prodCode = prodCode;
	}
	public String getDesc() {
		return prod_desc_name;
	}
	public void setDesc(final String val) {
		this.prod_desc_name = val;
	}

	String total_quantity = "0";

	public String getTotal_quantity() {
		return total_quantity;
	}

	public void setTotal_quantity(final String total_quantity) {
		this.total_quantity = total_quantity;
	}

	public String getDispatch_quantity() {
		return dispatch_quantity;
	}

	public void setDispatch_quantity(final String v) {
		this.dispatch_quantity = v;
	}

	public float getLast_4_days_quantity() {
		return last_4_days_quantity;
	}

	public void setLast_4_days_quantity(final float va) {
		this.last_4_days_quantity = va;
	}

	private String dispatch_quantity = "0";

	public String get_APPORDERNO() {
		return APPORDERNO;
	}

	public void  set_APPORDERNO(final String v) {
		this.APPORDERNO = v;
	}

	private String APPORDERNO;

	public String get_root_order_id() {
		return root_order_id;
	}

	public void set_root_order_id(final String v) {
		this.root_order_id = v;
	}

	private String root_order_id;

	public String getDispatch_date() {
		return dispatch_date;
	}

	public void setDispatch_date(String dispatch_date) {
		this.dispatch_date = dispatch_date;
	}

	private String dispatch_date;
	float last_4_days_quantity = 0;

	public boolean getLimitExceed() {
		return limitExceed;
	}

	public void setLimitExceed(final boolean val) {
		this.limitExceed = val;
	}

	boolean limitExceed = false;

}
