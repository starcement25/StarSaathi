package org.forcepower.starcement.bean;

public final class SurveyFormDetails {
	String mSurveyFormId 			="";
	String mSurveyUserId 			="";
	String mSurveyMenu 				="";
	String mSurveyType			 	="";
	String mSurveyTypeDetails	 	="";
	String mMallsurveyRelation		="";
	String mSurveySubTypeDetails	="";
	String mSurveyOTP				="";
	String mSurveyLayer				="";
	String mSurveySubMenu			="";
	String mSurveySubMenuDetails	="";
	String mSurveyOutletMenu 		="";
	String mSurveyRoutePlan 		="";
	String mSurveyOtherText 		="";
	String mSurveyReportRowId 		="";
	String customer_email_update 		="";

	public String getSurveyReportRowId() {
		return mSurveyReportRowId;
	}
	public void setSurveyReportRowId(String mSurveyReportRowId) {
		this.mSurveyReportRowId = mSurveyReportRowId;
	}
	
	public void setSurveyFormId(String surveyformid){
		this.mSurveyFormId=surveyformid;		
	}	
	public String getSurveyFormId()	{
		return mSurveyFormId;
	}	
	
	public void setSurveyUserId(String userid){
		this.mSurveyUserId=userid;		
	}	
	public String getSurveyUserId()	{
		return mSurveyUserId;
	}	
	
	public void setSurveyMenu(String surveymenu){
		this.mSurveyMenu=surveymenu;		
	}	
	public String getSurveyMenu(){
		return mSurveyMenu;
	}
	
	public void setSurveyType(String surveyType){
		this.mSurveyType=surveyType;		
	}	
	public String getSurveyType(){
		return mSurveyType;
	}
	
	public void setSurveyTypeDetails(String surveyTypeDetails){
		this.mSurveyTypeDetails=surveyTypeDetails;		
	}	
	public String getSurveyTypeDetails(){
		return mSurveyTypeDetails;
	}
	
	public void setSurveyMallSurveyRelation(String mallsurveyRelation){
		this.mMallsurveyRelation=mallsurveyRelation;		
	}	
	public String getSurveyMallSurveyRelation(){
		return mMallsurveyRelation;
	}
	
	public void setSurveySubTypeDetails(String surveySubTypeDetails){
		this.mSurveySubTypeDetails=surveySubTypeDetails;		
	}	
	public String getSurveySubTypeDetails(){
		return mSurveySubTypeDetails;
	}
	
	public void setSurveyOTP(String surveyOTP){
		this.mSurveyOTP=surveyOTP;		
	}	
	public String getSurveyOTP(){
		return mSurveyOTP;
	}
	
	public void setSurveyLayer(String layer){
		this.mSurveyLayer=layer;		
	}	
	public String getSurveyLayer(){
		return mSurveyLayer;
	}
	
	public String getSurveySubMenu() {
		return mSurveySubMenu;
	}
	public void setSurveySubMenu(String surveySubMenu) {
		this.mSurveySubMenu = surveySubMenu;
	}
	
	public String getSurveySubMenuDetails() {
		return mSurveySubMenuDetails;
	}
	public void setSurveySubMenuDetails(String surveySubMenuDetails) {
		this.mSurveySubMenuDetails = surveySubMenuDetails;
	}
	
	public String getSurveyOutletMenu() {
		return mSurveyOutletMenu;
	}
	public void setSurveyOutletMenu(String surveyOutletMenu) {
		this.mSurveyOutletMenu = surveyOutletMenu;
	}
	
	public String getSurveyRoutePlan() {
		return mSurveyRoutePlan;
	}
	public void setSurveyRoutePlan(String surveyRoutePlan) {
		this.mSurveyRoutePlan = surveyRoutePlan;
	}
	
	public String getSurveyOtherText() {
		return mSurveyOtherText;
	}
	public void setSurveyOtherText(String surveyOtherText) {
		this.mSurveyOtherText = surveyOtherText;
	}

	public String getCustomerEmailUpdate() {
		return customer_email_update;
	}
	public void setCustomerEmailUpdate(String customer_email_update) {
		this.customer_email_update = customer_email_update;
	}
}