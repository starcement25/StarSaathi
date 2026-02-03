package org.forcepower.starcement.bean;

public final class DestinationMaster {
	private String mDestinationCode="", mDestinationName="", mSubDealerCode = "", mExForType = "",
			phone_no= "", address = "", SAP_code = "";
	private boolean selected = false;
	public String getDestinationCode() {
		return mDestinationCode;
	}
	public void setDestinationCode(final String destinationCode) {
		this.mDestinationCode = destinationCode;
	}
	
	public String getDestinationName() {
		return mDestinationName;
	}
	public void setDestinationName(final String destinationName) {
		this.mDestinationName = destinationName;
	}
	public String getSubDealerCode() {
		return mSubDealerCode;
	}
	public void setSubDealerCode(final String mSubDealerCode) {
		this.mSubDealerCode = mSubDealerCode;
	}
	public String getExForType() {
		return mExForType;
	}
	public void setExForType(final String ExForType) {
		this.mExForType = ExForType;
	}

	public String get_phone_no() {
		return phone_no;
	}
	public void set_phone_no(final String val) {
		this.phone_no = val;
	}

	public String get_address() {
		return address;
	}
	public void set_address(final String val) {
		this.address = val;
	}

	public String get_SAP_code() {
		return SAP_code;
	}
	public void set_SAP_code(final String val) {
		this.SAP_code = val;
	}

	public boolean get_selected() {
		return selected;
	}
	public void set_selected(final boolean val) {
		this.selected = val;
	}

	public int getRow_position() {
		return row_position;
	}

	public void setRow_position(final int r) {
		this.row_position = r;
	}

	private int row_position = -1;

}
