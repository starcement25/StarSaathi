package org.forcepower.starcement.bean;

public final class BranchMasterDetails {
	String companyCode 	= "";
	String branchCode 	= "";
	String branchName 	= "";
	String hq 			= "";
	String mPlantName 	= "";
	String mValue		= "";
	
	
	public String getHq() {
		return hq;
	}
	public void setHq(String hq) {
		this.hq = hq;
	}
	public String getCompanyCode() {
		return companyCode;
	}
	public void setCompanyCode(String companyCode) {
		this.companyCode = companyCode;
	}
	public String getBranchCode() {
		return branchCode;
	}
	public void setBranchCode(String branchCode) {
		this.branchCode = branchCode;
	}
	public String getBranchName() {
		return branchName;
	}
	public void setBranchName(String branchName) {
		this.branchName = branchName;
	}
	
	
	public String getPlantName() {
		return mPlantName;
	}
	public void setPlantName(String plantName) {
		this.mPlantName = plantName;
	}
	
	public String getValue() {
		return mValue;
	}
	public void setValue(String value) {
		this.mValue = value;
	}
}
