package org.forcepower.starcement.bean;

public final class SurveyReport {

	String mMenuID = "";
	String mMenuName = "";
	String mTotal = "";

	public void setMenuId(String menuid) {
		this.mMenuID = menuid;
	}
	public String getMenuId() {
		return mMenuID;
	}

	public void setMenuName(String menuname) {
		this.mMenuName = menuname;
	}
	public String getMenuName() {
		return mMenuName;
	}

	public void setTotal(String total) {
		this.mTotal = total;
	}
	public String getTotal() {
		return mTotal;
	}

}
