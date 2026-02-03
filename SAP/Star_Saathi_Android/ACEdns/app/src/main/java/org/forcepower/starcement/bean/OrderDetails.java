package org.forcepower.starcement.bean;


public final class OrderDetails {
	
	String orderNo = "";
	String skuCode = "";
	double qty;
	String mrpCode = "";
	String TD = "";
	String mPremium = "";
	String saleRate = "";
	String VAT = "0";
	String amount = "0";
	String freight = "";
	String uom = "";

	
	
	public synchronized String getFreight() {
		return freight;
	}
	public synchronized void setFreight(String freight) {
		this.freight = freight;
	}
	public String getAmount() {
		return amount;
	}
	public void setAmount(String amount) {
		this.amount = amount;
	}
	public String getVAT() {
		return VAT;
	}
	public void setVAT(String vAT) {
		VAT = vAT;
	}
	public String getSaleRate() {
		return saleRate;
	}
	public void setSaleRate(String saleRate) {
		this.saleRate = saleRate;
	}
	public String getTD() {
		return TD;
	}
	public void setTD(String tD) {
		TD = tD;
	}
	
	public String getPremium() {
		return mPremium;
	}
	public void setPremium(String premium) {
		mPremium = premium;
	}
	
	public String getOrderNo() {
		return orderNo;
	}
	public void setOrderNo(String orderNo) {
		this.orderNo = orderNo;
	}
	public String getSkuCode() {
		return skuCode;
	}
	public void setSkuCode(String skuCode) {
		this.skuCode = skuCode;
	}
	public double getQty() {
		return qty;
	}
	public void setQty(double qty) {
		this.qty = qty;
	}
	public String getMrpCode() {
		return mrpCode;
	}
	public void setMrpCode(String mrpCode) {
		this.mrpCode = mrpCode;
	}
	public String getUom() {
		return uom;
	}
	public void setUom(String uom) {
		this.uom = uom;
	}
	
	

}
