package org.forcepower.starcement.bean;


public final class EmployeeDetails {
	String empCode = "";
	String empName = "";
	String newPassword = "";
	String deviceID = "";
	String saleAccess = "";
	String date = "";
	String appVersion = "";
	String updationFlag = "";
	
	public String getAppVersion() {
		return appVersion;
	}
	public void setAppVersion(String appVersion) {
		this.appVersion = appVersion;
	}
	public String getUpdationFlag() {
		return updationFlag;
	}
	public void setUpdationFlag(String updationFlag) {
		this.updationFlag = updationFlag;
	}
	
	
	public String getDate() {
		return date;
	}
	public void setDate(String date) {
		this.date = date;
	}
	public String getSaleAccess() {
		return saleAccess;
	}
	public void setSaleAccess(String saleAccess) {
		this.saleAccess = saleAccess;
	}
	public String getEmpCode() {
		return empCode;
	}
	public void setEmpCode(String empCode) {
		this.empCode = empCode;
	}
	public String getEmpName() {
		return empName;
	}
	public void setEmpName(String empName) {
		this.empName = empName;
	}
	public String getNewPassword() {
		return newPassword;
	}
	public void setNewPassword(String newPassword) {
		this.newPassword = newPassword;
	}
	public String getDeviceID() {
		return deviceID;
	}
	public void setDeviceID(String deviceID) {
		this.deviceID = deviceID;
	}
}
