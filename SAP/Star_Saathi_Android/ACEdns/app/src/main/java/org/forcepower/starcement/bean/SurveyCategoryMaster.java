package org.forcepower.starcement.bean;



public final class SurveyCategoryMaster {
	String mRowId			=		"";
	String mCategoryId		=		"";
	String mSubCategoryId	=		"";
	String mCategoryName	=		"";
	String mSubCategoryName	=		"";
	
	public void setRowId(String rowId)
	{
		this.mRowId=rowId;
	}
	public String getRowId()
	{
		return mRowId;
	}
	
	public void setCategoryId(String categoryId)
	{
		this.mCategoryId=categoryId;
	}
	public String getCategoryId()
	{
		return mCategoryId;
	}
	
	
	public void setSubCategoryId(String subCategoryId)
	{
		this.mSubCategoryId=subCategoryId;
	}
	public String getSubCategoryId()
	{
		return mSubCategoryId;
	}	
	
	
	public void setCategoryName(String categoryName)
	{
		this.mCategoryName=categoryName;
	}
	public String getCategoryName()
	{
		return mCategoryName;
	}	
	
	
	public void setSubCategoryName(String subCategoryName)
	{
		this.mSubCategoryName=subCategoryName;
	}
	public String getSubCategoryName()
	{
		return mSubCategoryName;
	}
	

}

