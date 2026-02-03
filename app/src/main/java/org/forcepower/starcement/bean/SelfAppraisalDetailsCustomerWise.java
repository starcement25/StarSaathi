package org.forcepower.starcement.bean;

public final class SelfAppraisalDetailsCustomerWise {
	String cutomerCode ="";
	String customerName ="";
	String empCode ="";
	String month ="";
	String target ="";
	String achievement ="";

	public void setcutomerCode(String cutomerCode)
	{
		this.cutomerCode =cutomerCode;
	}
	public String getcutomerCode()
	{
		return cutomerCode;
	}


	public void setcustomerName(String customerName)
	{
		this.customerName=customerName;
	}
	public String getcustomerName()
	{
		return customerName;
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
}
