package org.forcepower.starcement.bean;

public final class SelfAppraisalDetailsProductWise
{

	String productCode ="";
	String branchName ="";
	String empCode ="";
	String month ="";
	String target ="";
	String achievement ="", prev_y_target = "", prev_y_achievement = "";


	public void setProductCode(String productCode)
	{
		this.productCode =productCode;
	}
	public String getProductCode()
	{
		return productCode;
	}

	public void setProductName(String branchName)
	{
		this.branchName=branchName;
	}
	public String getProductName()
	{
		return branchName;
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


	public void setachievement(String achievement)
	{
		this.achievement =achievement;
	}
	public String getachievement()
	{
		return achievement;
	}


	public void set_prev_y_target(String prev_y_target)
	{
		this.prev_y_target =prev_y_target;
	}
	public String get_prev_y_target()
	{
		return prev_y_target;
	}
	public void set_prev_y_achievement(String prev_y_achievement)
	{
		this.prev_y_achievement =prev_y_achievement;
	}
	public String get_prev_y_achievement()
	{
		return prev_y_achievement;
	}

}
