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
import java.util.ArrayList;
import java.util.List;


public final class CategoryClass implements Serializable
{
	private String category_id = "", category_name = "", Qty = "", prod_desc = "",
			order_full_date_time = "", freight = "", destination_address = "",
			is_confirmed_material_received = "", erporderno = "";

	private List<ItemDetailsClass> itemList = new ArrayList<ItemDetailsClass>();

	public CategoryClass(String category_id, String category_name, String Qty, String prod_desc,
						 String order_full_date_time,
						 String freight, String destination_address,
						 String is_confirmed_material_received, final String erporderno) {
		this.category_id = category_id;
		this.category_name = category_name;
		this.Qty = Qty;
		this.prod_desc = prod_desc;
		this.order_full_date_time = order_full_date_time;
		this.freight = freight;
		this.destination_address = destination_address;
		this.is_confirmed_material_received = is_confirmed_material_received;
		this.erporderno = erporderno;
	}

	public String getCategoryId() {
		return category_id;
	}

	public String get_order_status() {
		return category_name;
	}

	public List<ItemDetailsClass> getItemList() {
		return itemList;
	}

	public void setItemList(List<ItemDetailsClass> itemList) {
		this.itemList = itemList;
	}

	public String getQty() {
		return Qty;
	}

	public void setQty(String Qty) {
		this.Qty = Qty;
	}

	public String get_order_full_date_time() {
		return order_full_date_time;
	}

	public String getprod_desc() {
		return prod_desc;
	}

	public String get_freight() {
		return freight;
	}

	public String get_destination_address() {
		return destination_address;
	}

	public String get_erporderno() {
		return erporderno;
	}
}
