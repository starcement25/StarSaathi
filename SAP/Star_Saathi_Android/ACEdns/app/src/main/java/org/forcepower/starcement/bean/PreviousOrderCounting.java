package org.forcepower.starcement.bean;

public final class PreviousOrderCounting {
	
	String mCustomerCode	="";
	String mProductCode		="";
	String mVisitDetails	="";
	
	public void setCustomerCode(String customerCode){
		this.mCustomerCode=customerCode;
	}
	public String getCustomerCode(){
		return mCustomerCode;
	}
	
	public void setProductCode(String brokername){
		this.mProductCode=brokername;
	}
	public String getProductCode(){
		return mProductCode;
	}
	
	public void setVisitDetails(String visitDetails){
		this.mVisitDetails=visitDetails;
	}
	public String getVisitDetails(){
		return mVisitDetails;
	}

}
