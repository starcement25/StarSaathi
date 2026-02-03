package org.forcepower.starcement.bean;

public final class CustomerProductWiseOrderPlanDetails
{

	String cutomerCode ="";
	String prodCode ="";
	String month ="";
	String purchase ="";
	String plan ="";


	public void setcutomerCode(String cutomerCode)
	{
		this.cutomerCode =cutomerCode;
	}
	public String getcustomerCode()
	{
		return cutomerCode;
	}

	public void setProdCode(String prodCode)
	{
		this.prodCode =prodCode;
	}
	public String geteProdCode()
	{
		return prodCode;
	}

	public void setMonth(String month)
	{
		this.month =month;
	}
	public String getMonth()
	{
		return month;
	}

	public void setPurchase(String purchase)
{
	this.purchase =purchase;
}
	public String getPurchase()
	{
		return purchase;
	}
	public void setPlan(String plan)
	{
		this.plan =plan;
	}
	public String getPlan()
	{
		return plan;
	}
}
