package org.forcepower.starcement.bean;


public final class AllocateMasterDetails {
	String prodCode = "";
	String prod_desc_name = "";
	float qty = 0;
	String dnsProdCode= "";
	String challan_no = "";
	String lifting_order_id = "";
	String total_quantity = "0";

	public String getChallan_no() {
		return challan_no;
	}

	public void setChallan_no(final String val) {
		this.challan_no = val;
	}

	public String getLifting_order_id() {
		return lifting_order_id;
	}

	public void setLifting_order_id(final String val) {
		this.lifting_order_id = val;
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

	public String getProdCode() {
		return prodCode;
	}

	public String getDesc() {
		return prod_desc_name;
	}

	public void setDesc(final String val) {
		this.prod_desc_name = val;
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

	float last_4_days_quantity = 0;

	public boolean getLimitExceed() {
		return limitExceed;
	}

	public void setLimitExceed(final boolean val) {
		this.limitExceed = val;
	}

	boolean limitExceed = false;

}
