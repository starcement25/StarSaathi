package org.forcepower.starcement.bean;

public final class KeyValue {
	
	String mKey			="";
	String mValue		="0";
	String mType		="";

	public KeyValue()
	{}
		
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

	public void setType(String type){
		this.mType=type;
	}
	public String getType(){
		return mType;
	}

}
