package org.forcepower.starcement.bean;

public final class MenuObj {
	
	String featureName 	= "";
	String count		= "";
	boolean isSelected;
	int resourceId;
	
	public String getFeatureName() {
		return featureName;
	}
	public void setFeatureName(String featureName) {
		this.featureName = featureName;
	}
	
	public String getName() {
		return count;
	}
	public void setName(final String count) {
		this.count = count;
	}
	
	public boolean isSelected() {
		return isSelected;
	}
	public void setSelected(boolean isSelected) {
		this.isSelected = isSelected;
	}
	
	public int getResourceId() {
		return resourceId;
	}
	public void setResourceId(int resourceId) {
		this.resourceId = resourceId;
	}

}
