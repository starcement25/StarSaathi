package org.forcepower.starcement.bean;



public final class SaudaBookingProductGroupDetails {
	
	String mProductGroupName ="";
	String mProductGroupCode="";	
	String mQuantity ="";
	String mValue ="";

	public void setProductGroupName(String productGroupName)
	{
		this.mProductGroupName=productGroupName;		
	}	
	public String getProductGroupName()
	{
		return mProductGroupName;
	}
	
	public void setProductGroupCode(String productGroupCode)
	{
		this.mProductGroupCode=productGroupCode;		
	}	
	public String getProductGroupCode()
	{
		return mProductGroupCode;
	}
	
	
	public void setQuantity(String quantity)
	{
		this.mQuantity=quantity;		
	}	
	public String getQuantity()
	{
		return mQuantity;
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
