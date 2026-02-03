package org.forcepower.starcement.bean;

public final class MarketFeedbackStockAudit {
	
	String stockAuditId		="";
	String competitorName	="";
	String quantity			="";
	String discount			="";
	
	public String getStockAuditId() {
		return stockAuditId;
	}
	public void setStockAuditId(String stockAuditId) {
		this.stockAuditId = stockAuditId;
	}
	
	public String getCompetitorName() {
		return competitorName;
	}
	public void setCompetitorName(String competitorName) {
		this.competitorName = competitorName;
	}
	
	public String getQuantity() {
		return quantity;
	}
	public void setQuantity(String quantity) {
		this.quantity = quantity;
	}
	
	public String getDiscount() {
		return discount;
	}
	public void setDiscount(String discount) {
		this.discount = discount;
	}

}
