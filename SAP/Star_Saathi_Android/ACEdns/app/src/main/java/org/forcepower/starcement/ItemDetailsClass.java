/*
 * Copyright (C) 2013 Surviving with Android (http://www.survivingwithandroid.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package org.forcepower.starcement;

import java.io.Serializable;

public final class ItemDetailsClass implements Serializable
{
	private String challanNO, rate, challanQty, DriverContact="", name, TrackNumber="", Position,
			challan_material_received="", ch_uid = "", apporderno = "", erporderno = "",
			transporter_name = "", ch_quantity_no_of_bags = "", ch_status= "",
			colour_code= "#ffffff", Qty = "0";

	//---------scat_id, rate, quantity, total_order_price_with_gst
	public ItemDetailsClass(String challanNO, String date, String quantity, String TrackNumber,
							String DriverContact, final String challan_material_received,
							final String ch_uid, final String apporderno_, final String _erporderno,
							final String transporter_name, final String ch_quantity_no_of_bags,
							final String ch_status, final String colour_code, final String Qty)
	{
		this.challanNO = challanNO;
		this.rate = date;
		this.challanQty = quantity;
		this.TrackNumber = TrackNumber;
		this.DriverContact = DriverContact;
		this.challan_material_received = challan_material_received;
		this.ch_uid = ch_uid;
		this.apporderno = apporderno_;
		this.erporderno = _erporderno;
		this.transporter_name = transporter_name;
		this.ch_quantity_no_of_bags = ch_quantity_no_of_bags;
		this.ch_status = ch_status;
		this.colour_code = colour_code;
		this.Qty = Qty;
	}
	
	
	public String get_challan_no() {
		return challanNO;
	}
	public void set_challan_no(final String val) {
		this.challanNO = val;
	}

	public String getRateValue() {
		return rate;
	}
	public void setRateValue(String rate) {
		this.rate = rate;
	}

	public String getChallanQty() {
		return challanQty;
	}
	public void setChallanQty(String quantity) {
		this.challanQty = quantity;
	}

	public String getDriverContact() {
		return DriverContact;
	}
	public void setDriverContact(String DriverContact) {
		this.DriverContact = DriverContact;
	}

	public String getName() {
		return name;
	}
	public void setName(String name) {
		this.name = name;
	}

	public String getTrackNumber() {
		return TrackNumber;
	}
	public void setTrackNumber(String TrackNumber) {
		this.TrackNumber = TrackNumber;
	}

    public String getPosition() {
        return Position;
    }
    public void setPosition(String Position) {
        this.Position = Position;
    }

    public String get_challan_material_received() {
        return challan_material_received;
    }
    public void set_challan_material_received(String val) {
        this.challan_material_received = val;
    }

    public String get_ch_uid() {
        return ch_uid;
    }
    public void set_ch_uid(String val) {
        this.ch_uid = val;
    }

    public String get_apporderno() { return apporderno; }
    public void set_apporderno(String val) { this.apporderno = val; }

    public String get_erporderno() { return erporderno; }
    public void set_erporderno(String val) { this.erporderno = val; }


    public void set_transporter_name(String val) { this.transporter_name = val; }

	public String get_transporter_name() { return transporter_name; }
	public String get_ch_status() { return ch_status; }
	public String get_ch_quantity_no_of_bags() { return ch_quantity_no_of_bags; }

	public String get_colour_code() { return colour_code; }
	public String get_qty() { return Qty; }
}
