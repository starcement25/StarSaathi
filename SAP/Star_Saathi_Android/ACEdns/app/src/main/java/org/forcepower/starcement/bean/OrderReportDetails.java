package org.forcepower.starcement.bean;

public final class OrderReportDetails {
	
	String name		="";
	String code		="";
	String quantity	="";
	String amount	="";
	String remarks	="";
	String Type ="";
	String TransactionId	="";

	public String getName() {
		return name;
	}
	public void setName(String name) {
		this.name = name;
	}
	
	public String getCode() {
		return code;
	}
	public void setCode(String code) {
		this.code = code;
	}
	
	public String getQuantity() {
		return quantity;
	}
	public void setQuantity(String quantity) {
		this.quantity = quantity;
	}
	
	public String getAmount() {
		return amount;
	}
	public void setAmount(String amount) {
		this.amount = amount;
	}
	public String getremarks() {
		return remarks;
	}
	public void setremarks(String remarks) {
		this.remarks = remarks;
	}


	public String getType() {
		return Type;
	}
	public void setType(String SaleType) {
		this.Type = SaleType;
	}

	public String getTransactionId() {
		return TransactionId;
	}
	public void setTransactionId(String TransactionId) {
		this.TransactionId = TransactionId;
	}

}
