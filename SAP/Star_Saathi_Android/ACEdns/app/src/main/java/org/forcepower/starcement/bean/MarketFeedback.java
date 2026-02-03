package org.forcepower.starcement.bean;

public final class MarketFeedback {
	
	String mFeedbackID		=		"";
	String mProductGroup	=		"";
	String mRouteCode		=		"";
	String mCopmpetitorName	=		"";
	String mPtd				=		"";
	String mPtr				=		"";
	String mPtc				=		"";
	String mPv				=		"";
	String mCustomerCode		=		"";
	
	
	public void setFeedbackID(String feedbackID){
		this.mFeedbackID=feedbackID;
	}
	public String getFeedbackID(){
		return mFeedbackID;
	}
	
	public void setProductGroup(String productGroup){
		this.mProductGroup=productGroup;
	}
	public String getProductGroup(){
		return mProductGroup;
	}
	
	public void setCopmpetitorName(String copmpetitorName){
		this.mCopmpetitorName=copmpetitorName;
	}
	public String getCopmpetitorName(){
		return mCopmpetitorName;
	}
	
	public void setRouteCode(String routeCode){
		this.mRouteCode=routeCode;
	}
	public String getRouteCode(){
		return mRouteCode;
	}
	
	public void setPtd(String ptd){
		this.mPtd=ptd;
	}
	public String getPtd(){
		return mPtd;
	}
	
	public void setPtr(String ptr){
		this.mPtr=ptr;
	}
	public String getPtr(){
		return mPtr;
	}
	
	public void setPtc(String ptc){
		this.mPtc=ptc;
	}
	public String getPtc(){
		return mPtc;
	}
	
	public void setPv(String pv){
		this.mPv=pv;
	}
	public String getPv(){
		return mPv;
	}
	
	public String getCustomerCode() {
		return mCustomerCode;
	}
	public void setCustomerCode(String customerCode) {
		this.mCustomerCode = customerCode;
	}


}
