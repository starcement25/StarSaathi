package org.forcepower.starcement.bean;

public final class SaudaProductConversion {
	
	String mProductGroupCode	=		"";
	String mConverSionFactor	=		"";
	
	public void setProductGroupCode(String productGroupCode){
		this.mProductGroupCode=productGroupCode;
	}
	
	public String getProductGroupCode(){
		return mProductGroupCode;
	}	
	
	
	public void setConverSionFactor(String converSionFactor){
		this.mConverSionFactor=converSionFactor;
	}
	public String getConverSionFactor(){
		return mConverSionFactor;
	}
}
