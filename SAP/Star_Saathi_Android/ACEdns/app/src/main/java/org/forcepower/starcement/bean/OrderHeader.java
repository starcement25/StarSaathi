package org.forcepower.starcement.bean;

public final class OrderHeader {
	
	String orderNo = "";
	String customerCode = "";
	int transferred;
	int flag;
	String instruction = "";
	String salesType = "";
	String trdDiscnt = "";
	String order_value = "";
	String tag_distributor_code = "";
	String transaction_type = "";
	String grnNo = "";
	String VAT = "0";
	String branchCode = "";
	String mVerticalValue	= "";
	String mDestinationCode	="";
	String mOrderType		="";
	String mFreightComponent	="";
	String mGstType	="";
	String mPriceValidationType	="";

	
	public synchronized String getBranchCode() {
		return branchCode;
	}
	public synchronized void setBranchCode(String branchCode) {
		this.branchCode = branchCode;
	}
	public String getVAT() {
		return VAT;
	}
	public void setVAT(String vAT) {
		VAT = vAT;
	}
	public String getGrnNo() {
		return grnNo;
	}
	public void setGrnNo(String grnNo) {
		this.grnNo = grnNo;
	}
	public String getTransaction_type() {
		return transaction_type;
	}
	public void setTransaction_type(String transaction_type) {
		this.transaction_type = transaction_type;
	}
	public String getTag_distributor_code() {
		return tag_distributor_code;
	}
	public void setTag_distributor_code(String tag_distributor_code) {
		this.tag_distributor_code = tag_distributor_code;
	}
	public String getOrder_value() {
		return order_value;
	}
	public void setOrder_value(String order_value) {
		this.order_value = order_value;
	}
	public String getTrdDiscnt() {
		return trdDiscnt;
	}
	public void setTrdDiscnt(String trdDiscnt) {
		this.trdDiscnt = trdDiscnt;
	}
	public String getOrderNo() {
		return orderNo;
	}
	public void setOrderNo(String orderNo) {
		this.orderNo = orderNo;
	}
	public String getCustomerCode() {
		return customerCode;
	}
	public void setCustomerCode(String customerCode) {
		this.customerCode = customerCode;
	}
	public int getTransferred() {
		return transferred;
	}
	public void setTransferred(int transferred) {
		this.transferred = transferred;
	}
	public int getFlag() {
		return flag;
	}
	public void setFlag(int flag) {
		this.flag = flag;
	}
	public String getInstruction() {
		return instruction;
	}
	public void setInstruction(String instruction) {
		this.instruction = instruction;
	}
	public String getSalesType() {
		return salesType;
	}
	public void setSalesType(String salesType) {
		this.salesType = salesType;
	}
	
	public String getVerticalValue() {
		return mVerticalValue;
	}
	public void setVerticalValue(String verticalValue) {
		this.mVerticalValue = verticalValue;
	}
	
	public String getDestinationCode() {
		return mDestinationCode;
	}
	public void setDestinationCode(String destinationCode) {
		this.mDestinationCode = destinationCode;
	}
	
	public String getOrderType() {
		return mOrderType;
	}
	public void setOrderType(String orderType) {
		this.mOrderType = orderType;
	}
	
	public String getFreightComponent() {
		return mFreightComponent;
	}
	public void setFreightComponent(String freightComponent) {
		this.mFreightComponent = freightComponent;
	}

	public String getGstType() {
		return mGstType;
	}
	public void setGstType(String mGstType) {
		this.mGstType = mGstType;
	}
	public String getPriceValidationType() {
		return mPriceValidationType;
	}
	public void setPriceValidationType(String mPriceValidationType) {
		this.mPriceValidationType = mPriceValidationType;
	}

}
