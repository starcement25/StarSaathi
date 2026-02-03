package org.forcepower.starcement.bean;

public final class SelfAppraisalDetailsProductGroupWise
{

	String productGroupCode ="";
	String productGroupName ="";
	String empCode ="";
	String month ="";
	String target ="";
	String achievement ="";
	String item_type ="";


	public void setproductGroupCode(String cutomerCode)
	{
		this.productGroupCode =cutomerCode;
	}
	public String getproductGroupCode()
	{
		return productGroupCode;
	}


	public void setproductGroupName(String customerName)
	{
		this.productGroupName =customerName;
	}
	public String getproductGroupName()
	{
		return productGroupName;
	}

	public void setempCode(String empCode)
	{
		this.empCode =empCode;
	}
	public String getempCode()
	{
		return empCode;
	}


	public void setmonth(String month)
	{
		this.month =month;
	}
	public String getmonth()
	{
		return month;
	}


	public void settarget(String target)
	{
		this.target =target;
	}
	public String gettarget()
	{
		return target;
	}


	public void setachievement(String v)
	{
		this.achievement =v;
	}
	public String getachievement()
	{
		return achievement;
	}


	public void set_item_type(final String v)
	{
		this.item_type =v;
	}
	public String get_item_type()
	{
		return item_type;
	}
}
