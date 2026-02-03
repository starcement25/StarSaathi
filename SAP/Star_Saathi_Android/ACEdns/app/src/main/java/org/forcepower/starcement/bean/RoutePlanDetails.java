package org.forcepower.starcement.bean;

public final class RoutePlanDetails {
	
	String routePlanId 				="";
	String userId 					="";
	String routePlanAccessPeriod 	="";
	String routePlanDeviation 		="";
	String routePlanApproval 		="";
	String routePlanFlow 			="";
	String routeCustomerPlanning	="";
	String distributorRoutePlanning	="";
	String distributorRoutePlanningMultiple	="";
	String lastUpdateTime 			="";
	
	
	public String getDistributorRoutePlanningMultiple() {
		return distributorRoutePlanningMultiple;
	}
	public void setDistributorRoutePlanningMultiple(
			String distributorRoutePlanningMultiple) {
		this.distributorRoutePlanningMultiple = distributorRoutePlanningMultiple;
	}
	
	public String getLastUpdateTime() {
		return lastUpdateTime;
	}
	public void setLastUpdateTime(String lastUpdateTime) {
		this.lastUpdateTime = lastUpdateTime;
	}
	public String getRoutePlanId() {
		return routePlanId;
	}
	public void setRoutePlanId(String routePlanId) {
		this.routePlanId = routePlanId;
	}
	public String getUserId() {
		return userId;
	}
	public void setUserId(String userId) {
		this.userId = userId;
	}
	public String getRoutePlanFlow() {
		return routePlanFlow;
	}
	public void setRoutePlanFlow(String routePlanFlow) {
		this.routePlanFlow = routePlanFlow;
	}
	
	public String getRouteCustomerPlanning() {
		return routeCustomerPlanning;
	}
	public void setRouteCustomerPlanning(String routeCustomerPlanning) {
		this.routeCustomerPlanning = routeCustomerPlanning;
	}
	
	public String getDistributorRoutePlanning() {
		return distributorRoutePlanning;
	}
	public void setDistributorRoutePlanning(String distributorRoutePlanning) {
		this.distributorRoutePlanning = distributorRoutePlanning;
	}
	
	public String getRoutePlanAccessPeriod() {
		return routePlanAccessPeriod;
	}
	public void setRoutePlanAccessPeriod(String routePlanAccessPeriod) {
		this.routePlanAccessPeriod = routePlanAccessPeriod;
	}
	public String getRoutePlanDeviation() {
		return routePlanDeviation;
	}
	public void setRoutePlanDeviation(String routePlanDeviation) {
		this.routePlanDeviation = routePlanDeviation;
	}
	public String getRoutePlanApproval() {
		return routePlanApproval;
	}
	public void setRoutePlanApproval(String routePlanApproval) {
		this.routePlanApproval = routePlanApproval;
	}

}
