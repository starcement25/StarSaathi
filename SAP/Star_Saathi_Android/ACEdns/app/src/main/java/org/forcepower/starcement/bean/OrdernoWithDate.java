package org.forcepower.starcement.bean;

public final class OrdernoWithDate {
	String mOrderNo		="";
	String mOrderDate	="";
	
	public void setOrderNo(String orderNo){
		this.mOrderNo=orderNo;
	}
	public String getOrderNo(){
		return mOrderNo;
	}
	
	public void setOrderDate(String orderDate){
		this.mOrderDate=orderDate;
	}
	public String getOrderDate(){
		return mOrderDate;
	}
}
