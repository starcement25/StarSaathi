package org.forcepower.starcement.bean;

public final class MallSurveyRelation {

	String mMenuId			="";	
	String mRowId			="";
	String mMallInfo	 	="";
	String mType		 	="";
	
	public String getMenuId() {
		return mMenuId;
	}
	public void setMenuId(String menuId) {
		this.mMenuId = menuId;
	}
	
	public String getRowId() {
		return mRowId;
	}
	public void setRowId(String rowId) {
		this.mRowId = rowId;
	}
	
	public String getMallInfo() {
		return mMallInfo;
	}
	public void setMallInfo(String mallInfo) {
		this.mMallInfo = mallInfo;
	}
	
	public void setType(String type){
		this.mType=type;
	}
	public String getType(){
		return mType;
	}
	
}
