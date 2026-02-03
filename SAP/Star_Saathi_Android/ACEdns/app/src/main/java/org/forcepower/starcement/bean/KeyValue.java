package org.forcepower.starcement.bean;

public final class KeyValue {
	
	String mKey			="";
	String mValue		="0";
	String mEnteredValue="0";
	String mImageName	="";
	String mType		="";

	public KeyValue()
	{


	}
	public KeyValue(String  mValue) {
		this.mValue = mValue;

	}

	public KeyValue(String  mValue,String  mKey) {
		this.mValue = mValue;
		this.mKey = mKey;

	}
		
	public void setKey(String key){
		this.mKey=key;
	}
	public String getKey(){
		return mKey;
	}
	
	public void setValue(String value){
		this.mValue=value;
	}
	public String getValue(){
		return mValue;
	}
	
	public void setEnteredValue(String enteredValue){
		this.mEnteredValue=enteredValue;
	}
	public String getEnteredValue(){
		return mEnteredValue;
	}
	
	public void setImageName(String imageName){
		this.mImageName=imageName;
	}
	public String getImageName(){
		return mImageName;
	}
	
	public void setType(String type){
		this.mType=type;
	}
	public String getType(){
		return mType;
	}

}
