package org.forcepower.starcement.bean;


public final class CustomerDetails {
	String customerCode 	= "";
	String customerName 	= "";
	String routeCode 		= "";
	String empCode 			= "";
	String isBlackList 		= "";
	String isACEDNS 		= "";
	
	String creditLimit 		= "";
	String currentBalance 	= "";
	
	String tradeDiscount 	= "0";
	String customerType 	= "";
	String rdsTag 			= "NA";
	String flag 			= "";
	
	String address 			= "";
	String number 			= "";
	String pin 				= "";
	String SAP_code 	= "";
	String remarks 			= "";
	
	String replacingCustCode 		= "";
	String mSaudaValidityPeriod		= "";
	String mRouteName				= "";
	
	String landlineNo 				= "";	
	String ownerName 				= "";
	String ownerPhone 				= "";
	String custClass 				= "";
	String weeklyClosingDay 		= "";
	String coverageType 			= "";
	String TIN 						= "";
	String PAN 						= "";
	String minimumStock 						= "";
	String image 						= "";
	String branchCode 						= "";
	String visitDay 						= "";
	String email 						= "";
	String sauda_limit 						= "", pending_qty = "";

	public String getLandlineNo() {
		return landlineNo;
	}
	public void setLandlineNo(String landlineNo) {
		this.landlineNo = landlineNo;
	}
	public String getOwnerName() {
		return ownerName;
	}
	public void setOwnerName(String ownerName) {
		this.ownerName = ownerName;
	}
	public String getOwnerPhone() {
		return ownerPhone;
	}
	public void setOwnerPhone(String ownerPhone) {
		this.ownerPhone = ownerPhone;
	}
	public String getCustClass() {
		return custClass;
	}
	public void setCustClass(String custClass) {
		this.custClass = custClass;
	}
	public String getWeeklyClosingDay() {
		return weeklyClosingDay;
	}
	public void setWeeklyClosingDay(String weeklyClosingDay) {
		this.weeklyClosingDay = weeklyClosingDay;
	}
	public String getCoverageType() {
		return coverageType;
	}
	public void setCoverageType(String coverageType) {
		this.coverageType = coverageType;
	}
	public String getTIN() {
		return TIN;
	}
	public void setTIN(String tIN) {
		TIN = tIN;
	}
	public String getPAN() {
		return PAN;
	}
	public void setPAN(String pAN) {
		PAN = pAN;
	}
	public void setReplacingCustCode(String replacingCustCode) {
		this.replacingCustCode = replacingCustCode;
	}
	public String getFlag() {
		return flag;
	}
	public void setFlag(String flag) {
		this.flag = flag;
	}
	public String getRdsTag() {
		return rdsTag;
	}
	public void setRdsTag(String rdsTag) {
		this.rdsTag = rdsTag;
	}
	public String getCustomerType() {
		return customerType;
	}
	public void setCustomerType(String customerType) {
		this.customerType = customerType;
	}
	public String getTradeDiscount() {
		return tradeDiscount;
	}
	public void setTradeDiscount(String tradeDiscount) {
		this.tradeDiscount = tradeDiscount;
	}
	public  String getAddress() {
		return address;
	}
	public  void setAddress(String address) {
		this.address = address;
	}
	public  String getNumber() {
		return number;
	}
	public  void setNumber(String number) {
		this.number = number;
	}
	public  String getPin() {
		return pin;
	}
	public  void setPin(String pin) {
		this.pin = pin;
	}

	public String get_SAP_code() {
		return SAP_code;
	}
	public void set_SAP_code(String val) {
		this.SAP_code = val;
	}
	public  String getRemarks() {
		return remarks;
	}
	public  void setRemarks(String remarks) {
		this.remarks = remarks;
	}
	public String getCustomerCode() {
		return customerCode;
	}
	public void setCustomerCode(String customerCode) {
		this.customerCode = customerCode;
	}
	public String getCustomerName() {
		return customerName;
	}
	public void setCustomerName(String customerName) {
		this.customerName = customerName;
	}
	public String getRouteCode() {
		return routeCode;
	}
	public void setRouteCode(String routeCode) {
		this.routeCode = routeCode;
	}
	public void setEmpCode(String empCode) {
		this.empCode = empCode;
	}
	public String getCurrentBalance() {
		return currentBalance;
	}
	public void setCurrentBalance(String currentBalance) {
		this.currentBalance = currentBalance;
	}
	public String getCreditLimit() {
		return creditLimit;
	}
	public void setCreditLimit(String creditLimit) {
		this.creditLimit = creditLimit;
	}
	public String getIsACEDNS() {
		return isACEDNS;
	}
	public void setIsACEDNS(String isACEDNS) {
		this.isACEDNS = isACEDNS;
	}
	public String getIsBlackList() {
		return isBlackList;
	}
	public void setIsBlackList(String isBlackList) {
		this.isBlackList = isBlackList;
	}
	
	public String getSaudaValidityPeriod() {
		return mSaudaValidityPeriod;
	}
	public void setSaudaValidityPeriod(String saudavalidityperiod) {
		this.mSaudaValidityPeriod = saudavalidityperiod;
	}
	public String getRouteName() {
		return mRouteName;
	}
	public String getMinimumStock() {
		return minimumStock;
	}
	public void setMinimumStock(String minimumStock) {
		this.minimumStock = minimumStock;
	}
	public String getImage() {
		return image;
	}
	public void setImage(String image) {
		this.image = image;
	}

	public String getBranchCode() {
		return branchCode;
	}
	public void setBranchCode(String branchCode) {
		this.branchCode = branchCode;
	}
	public String getVisitDay()
	{
		return visitDay;
	}
	public void setVisitDay(String visitDay)
	{
		this.visitDay = visitDay;
	}
	public String getEmail()
	{
		return email;
	}
	public void setEmail(String email)
	{
		this.email = email;
	}

	public String getSaudaLimit()
	{
		return sauda_limit;
	}
	public void setSaudaLimit(String sauda_limit)
	{
		this.sauda_limit = sauda_limit;
	}
	public String getPendingQty()
	{
		return pending_qty;
	}
	public void setPendingQty(String pending_qty)
	{
		this.pending_qty = pending_qty;
	}
}
