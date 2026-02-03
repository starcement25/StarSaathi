package org.forcepower.starcement.bean;


public final class SchemeMasterDetails {
	
	String prodCode = "";
	
	String grpCode = "";
	String subGrpCode = "";
	String brndCode = "";
	
	String desc = "";
	String isAcedns = "";
	String isBlkLst = "";
	String closingStk = "0";
	String grpName = "";
	String subGrpName = "";
	String brndName = "";
	String uom1 = "";
	String uom2 = "";
	String UomSelectedForProduct = "";
	int selectedUOM = -1;
	
	String qty = "0";
	String qtyRemaining = "0";
	String mrpCode = "";
	String mrpValue = "0.00";
	String tradeDiscnt = "0.00";
	String vatVAlue = "0";
	
	String despatchQtyForStockIn = "";
	String statusForStockIn = "";
	
	String amount = "";
	boolean amtEntered = false;
	boolean eligibleForProdQuantityCustClassWiseTd = false;
	boolean QuantityEligibleForTD = false;

	String IMEINo = "";
	
	
	String conversionFactor  = "";
	String packSize = "";
	
	String freight = "";
	
	String filterCode = "";
	
	String mUOM3 =		"";
	String mConversionFactorTwo	=		"";
	String mTD 		=		"";
	String mPremium =		"0";
	String mBranchCode= "";
	String mVerticalValue= "";
	String mSecondaryUnit= "";
	String dnsProdCode= "";
	String focus= "";
	String weightage= "";
	String vatRate= "";
	String additionalVatRate= "";
	String FreightCost= "";
	String plan= "";
	String purchase= "";
	String tdPercent= "";
	String QtySlabForTD= "";

	boolean isTd=true;
	
	
	public String getDnsProdCode() {
		return dnsProdCode;
	}
	public void setDnsProdCode(String dnsProdCode) {
		this.dnsProdCode = dnsProdCode;
	}
	
	public synchronized String getFilterCode() {
		return filterCode;
	}
	public synchronized void setFilterCode(String filterCode) {
		this.filterCode = filterCode;
	}
	public String getFreight() {
		return freight;
	}
	public void setFreight(String freight) {
		this.freight = freight;
	}
	public String getPackSize() {
		return packSize;
	}
	public void setPackSize(String packSize) {
		this.packSize = packSize;
	}
	public int getSelectedUOM() {
		return selectedUOM;
	}
	public void setSelectedUOM(int selectedUOM) {
		this.selectedUOM = selectedUOM;
	}
	public String getConversionFactor() {
		return conversionFactor;
	}
	public void setConversionFactor(String conversionFactor) {
		this.conversionFactor = conversionFactor;
	}
	public String getIMEINo() {
		return IMEINo;
	}
	public void setIMEINo(String iMEINo) {
		IMEINo = iMEINo;
	}
	public boolean isAmtEntered() {
		return amtEntered;
	}
	public void setAmtEntered(boolean amtEntered) {
		this.amtEntered = amtEntered;
	}
	public String getAmount() {
		return amount;
	}
	public void setAmount(String amount) {
		this.amount = amount;
	}
	public String getDespatchQtyForStockIn() {
		return despatchQtyForStockIn;
	}
	public void setDespatchQtyForStockIn(String despatchQtyForStockIn) {
		this.despatchQtyForStockIn = despatchQtyForStockIn;
	}
	public String getStatusForStockIn() {
		return statusForStockIn;
	}
	public void setStatusForStockIn(String statusForStockIn) {
		this.statusForStockIn = statusForStockIn;
	}
	public String getVat() {
		return vatVAlue;
	}
	public void setVat(String vatVAlue) {
		this.vatVAlue = vatVAlue;
	}
	public String getUom1() {
		return uom1;
	}
	public void setUom1(String uom1) {
		this.uom1 = uom1;
	}
	public String getUomSelectedForProduct() {
		return UomSelectedForProduct;
	}
	public void setUomSelectedForProduct(String UomSelectedForProduct) {
		this.UomSelectedForProduct = UomSelectedForProduct;
	}
	public String getUom2() {
		return uom2;
	}
	public void setUom2(String uom2) {
		this.uom2 = uom2;
	}
	public String getGrpName() {
		return grpName;
	}
	public void setGrpName(String grpName) {
		this.grpName = grpName;
	}
	public String getSubGrpName() {
		return subGrpName;
	}
	public void setSubGrpName(String subGrpName) {
		this.subGrpName = subGrpName;
	}
	public String getBrndName() {
		return brndName;
	}
	public void setBrndName(String brndName) {
		this.brndName = brndName;
	}
	public final String getMrpValue() {
		return mrpValue;
	}
	public final void setMrpValue(String mrpValue) {
		this.mrpValue = mrpValue;
	}
	public String getQty() {
		return qty;
	}
	public void setQty(String qty) {
		this.qty = qty;
	}
	public String getQtyRemaining() {
		return qtyRemaining;
	}
	public void setQtyRemaining(String qtyRemaining) {
		this.qtyRemaining = qtyRemaining;
	}
	public String getMrpCode() {
		return mrpCode;
	}
	public void setMrpCode(String mrpCode) {
		this.mrpCode = mrpCode;
	}
	public String getTradeDiscnt() {
		return tradeDiscnt;
	}
	public void setTradeDiscnt(String tradeDiscnt) {
		this.tradeDiscnt = tradeDiscnt;
	}
	public String getClosingStk() {
		return closingStk;
	}
	public void setClosingStk(String closingStk) {
		this.closingStk = closingStk;
	}
	public String getGrpCode() {
		return grpCode;
	}
	public void setGrpCode(String grpCode) {
		this.grpCode = grpCode;
	}
	public String getSubGrpCode() {
		return subGrpCode;
	}
	public void setSubGrpCode(String subGrpCode) {
		this.subGrpCode = subGrpCode;
	}
	public String getBrndCode() {
		return brndCode;
	}
	public void setBrndCode(String brndCode) {
		this.brndCode = brndCode;
	}
	public String getProdCode() {
		return prodCode;
	}
	public void setProdCode(String prodCode) {
		this.prodCode = prodCode;
	}
	public String getDesc() {
		return desc;
	}
	public void setDesc(String desc) {
		this.desc = desc;
	}
	public String getIsAcedns() {
		return isAcedns;
	}
	public void setIsAcedns(String isAcedns) {
		this.isAcedns = isAcedns;
	}
	public String getIsBlkLst() {
		return isBlkLst;
	}
	public void setIsBlkLst(String isBlkLst) {
		this.isBlkLst = isBlkLst;
	}
	
	public String getUOM3() {
		return mUOM3;
	}
	public void setUOM3(String uom3) {
		this.mUOM3 = uom3;
	}
	
	public String getConversionFactorTwo() {
		return mConversionFactorTwo;
	}
	public void setConversionFactorTwo(String conversionfactortwo) {
		this.mConversionFactorTwo = conversionfactortwo;
	}
	
	public String getTD() {
		return mTD;
	}
	public void setTD(String td) {
		this.mTD = td;
	}
	
	public String getPremium() {
		return mPremium;
	}
	public void setPremium(String premium) {
		this.mPremium = premium;
	}
	
	public String getBranchCode() {
		return mBranchCode;
	}
	public void setBranchCode(String branchcode) {
		this.mBranchCode = branchcode;
	}
	
	public String getVerticalValue() {
		return mVerticalValue;
	}
	public void setVerticalValue(String verticalValue) {
		this.mVerticalValue = verticalValue;
	}
	
	public String getSecondaryUnit() {
		return mSecondaryUnit;
	}
	public void setSecondaryUnit(String secondaryUnit) {
		this.mSecondaryUnit = secondaryUnit;
	}

	public boolean getIsTradeDiscount() {
		return isTd;
	}
	public void setIsTradeDiscount(boolean td) {
		this.isTd = td;
	}

	public String getFocus()
	{
		return focus;
	}
	public void setFocus(String focus)
	{
		this.focus = focus;
	}

	public String getWeightage()
	{
		return weightage;
	}
	public void setWeightage(String weightage)
	{
		this.weightage = weightage;
	}

	public String getVatRate()
	{
		return vatRate;
	}
	public void setVatRate(String vatRate)
	{
		this.vatRate = vatRate;
	}

	public String getAdditionalVatRate()
	{
		return additionalVatRate;
	}
	public void setAdditionalVatRate(String additionalVatRate)
	{
		this.additionalVatRate = additionalVatRate;
	}

	public String getFreightCost()
	{
		return FreightCost;
	}
	public void setFreightCost(String FreightCost)
	{
		this.FreightCost = FreightCost;
	}

	public String getPlan()
	{
		return plan;
	}
	public void setPlan(String plan)
	{
		this.plan = plan;
	}

	public String getPurchase()
	{
		return purchase;
	}
	public void setPurchase(String purchase)
	{
		this.purchase = purchase;
	}
	public boolean getEligibleForProdQuantityCustClassWiseTd() {
		return eligibleForProdQuantityCustClassWiseTd;
	}
	public void seteligibleForProdQuantityCustClassWiseTd(boolean eligibleForProdQuantityCustClassWiseTd) {
		this.eligibleForProdQuantityCustClassWiseTd = eligibleForProdQuantityCustClassWiseTd;
	}
	public boolean getQuantityEligibleForTD() {
		return QuantityEligibleForTD;
	}
	public void setQuantityEligibleForTD(boolean AmountEligibleForTD) {
		this.QuantityEligibleForTD = AmountEligibleForTD;
	}

	public String getTDPercent()
	{
		return tdPercent;
	}
	public void setTDPercent(String tdPercent)
	{
		this.tdPercent = tdPercent;
	}
	public String getQtySlabForTD()
	{
		return QtySlabForTD;
	}
	public void setQtySlabForTD(String QtySlabForTD)
	{
		this.QtySlabForTD = QtySlabForTD;
	}
}
