package org.forcepower.starcement.bean;


public final class BrokerMaster {
	
	String mBrokerId=		"";
	String mBrokerName=		"";
	
	public void setBrokerId(String brokerid){
		this.mBrokerId=brokerid;
	}
	public String getBrokerId(){
		return mBrokerId;
	}
	
	public void setBrokerName(String brokername){
		this.mBrokerName=brokername;
	}
	public String getBrokerName(){
		return mBrokerName;
	}

}
