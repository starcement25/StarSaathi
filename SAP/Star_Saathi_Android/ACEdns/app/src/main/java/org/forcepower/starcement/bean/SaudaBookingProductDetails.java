package org.forcepower.starcement.bean;


public final class SaudaBookingProductDetails {
	
	String mProductName ="";
	String mProductCode="";	
	String mQuantity ="";
	String mValidFrom="";
	String mValue="";

	public void setProductName(String productName)
	{
		this.mProductName=productName;		
	}	
	public String getProductName()
	{
		return mProductName;
	}
	
	public void setProductCode(String productCode)
	{
		this.mProductCode=productCode;		
	}	
	public String getProductCode()
	{
		return mProductCode;
	}	
	
	public void setQuantity(String quantity)
	{
		this.mQuantity=quantity;		
	}	
	public String getQuantity()
	{
		return mQuantity;
	}
	
	public void setValidFrom(String validFrom)
	{
		this.mValidFrom=validFrom;
	}
	public String getValidFrom()
	{
		return mValidFrom;
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
