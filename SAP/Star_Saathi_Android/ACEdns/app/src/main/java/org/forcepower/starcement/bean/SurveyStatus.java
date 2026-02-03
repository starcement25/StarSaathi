package org.forcepower.starcement.bean;

public final class SurveyStatus {
	
	String mSurveyLayout	="";
	String mStatus			="";
	
	public void setSurveyLayout(String surveyLayout)
	{
		this.mSurveyLayout=surveyLayout;
	}
	public String getSurveyLayout()
	{
		return mSurveyLayout;
	}
	
	
	public void setStatus(String status)
	{
		this.mStatus=status;
	}
	public String getStatus()
	{
		return mStatus;
	}

}
