package org.forcepower.starcement.bean;


public final class RouteDetails {
	
	String routeCode 			= "";
	String routeName 			= "";	
	String replacingRouteCode 	= "";
	String distributorCode		= "";
	String employeeCode			= "";
	
	
	
	public String getDistributorCode() {
		return distributorCode;
	}
	public void setDistributorCode(String distributorCode) {
		this.distributorCode = distributorCode;
	}
	
	public String getEmployeeCode() {
		return employeeCode;
	}
	public void setEmployeeCode(String employeeCode) {
		this.employeeCode = employeeCode;
	}
	
	public String getReplacingRouteCode() {
		return replacingRouteCode;
	}
	public void setReplacingRouteCode(String replacingRouteCode) {
		this.replacingRouteCode = replacingRouteCode;
	}
	public String getRouteCode() {
		return routeCode;
	}
	public void setRouteCode(String routeCode) {
		this.routeCode = routeCode;
	}
	public String getRouteName() {
		return routeName;
	}
	public void setRouteName(String routeName) {
		this.routeName = routeName;
	}

}
