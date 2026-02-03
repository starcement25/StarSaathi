package org.forcepower.starcement.bean;


public final class SauadaBookingCustomerDetails {
	String mCustomerName ="";
	String mCustomerCode ="";
	String mQuantityBooked="";
	String mValue="";

	public void setCustomerName(String customerName)
	{
		this.mCustomerName=customerName;		
	}	
	public String getCustomerName()
	{
		return mCustomerName;
	}
	
	
	public void setCustomerCode(String customerCode)
	{
		this.mCustomerCode=customerCode;		
	}	
	public String getCustomerCode()
	{
		return mCustomerCode;
	}
	
	
	public void setQuantityBooked(String quantityBooked)
	{
		this.mQuantityBooked=quantityBooked;
	}	
	public String getQuantityBooked()
	{
		return mQuantityBooked;
	}

	public void setValue(String mValue)
	{
		this.mValue=mValue;
	}
	public String getValue()
	{
		return mValue;
	}
	

}
