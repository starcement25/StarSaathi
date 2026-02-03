<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_EMAMI");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);
	$from_date='2017-04-01';
	$to_date='2017-09-10';
	if($from_date!='' && $to_date!='')
	{
		 $date_condition=" AND DATE_FORMAT(LO.date,'%Y-%m-%d') >='".$from_date."' AND
					  	DATE_FORMAT(LO.date,'%Y-%m-%d') <='".$to_date."'";
	}
	$sql_sauda_header_download = "SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.credit_days,CM.state_code,SH.broker_id,SH.sauda_no,DATE_FORMAT(LO.date,'%d-%m-%Y') AS sauda_date,DATE_FORMAT(LO.date,'%H:%i:%s') AS sauda_time,
								 LO.date AS sauda_date_time,DATE_FORMAT(SH.sauda_valid_from,'%d-%m-%Y') AS sauda_valid_from,DATE_FORMAT(SH.sauda_valid_from,'%Y-%m-%d') AS sauda_valid_from_calc,
								 DATE_FORMAT(LO.date,'%Y-%m-%d') AS sauda_date_calc,CM.sauda_validity_period,PM.dns_prod_code,PM.vertical_value,SH.customer_code,LO.emp_code,
								 SD.sku_code,SD.qty,SD.convert_qty_two,PM.UOM1,PM.prod_desc,PM.product_group_code,PM.vertical_value,PM.conversion_factor,PM.conversion_factor_two,BM.branch_code,BM.dns_branch_code,BM.branch_name,BM.plant_name,SD.sale_rate,SD.freight_charge,SD.TD,SD.liquid_TD,SD.premium,SD.amount,CM.TD AS CMTD,EM.emp_name,EM.state,
								 PGM.product_group_name,PGM.is_upload,PGM.formulation,SH.d_instruction FROM sauda_header SH,location LO,sauda_details SD,product_master PM,customer_master CM,branch_master BM,employee_master EM, product_group_master PGM
								 WHERE LO.trans_id=SH.sauda_no AND SH.sauda_no=SD.sauda_no AND LO.emp_code=EM.emp_code AND (LO.trans_id LIKE 'FT%') AND LO.emp_code!='C0007' AND SD.sku_code=PM.prod_code AND
								 SH.customer_code=CM.customer_code AND SH.branch_code=BM.branch_code AND PM.product_group_code=PGM.product_group_code
								 ".$date_condition." ORDER BY DATE_FORMAT(LO.date,'%Y-%m-%d %H:%i:%s') ASC,PGM.product_group_name,EM.state  ASC";
	$rs_sauda_header_download = mysql_query($sql_sauda_header_download) or die(mysql_error()." Error in sauda data download: ".$sql_sauda_header_download);
	$linesaudaheader = '';
	while($rec_sauda_header_download = mysql_fetch_array($rs_sauda_header_download))
	{
		$dns_customer_code=$rec_sauda_header_download['dns_customer_code'];
		$customer_code=$rec_sauda_header_download['customer_code'];
		$customer_name=$rec_sauda_header_download['customer_name'];
		$broker_id=$rec_sauda_header_download['broker_id'];
		$product_group_code = $rec_sauda_header_download['product_group_code'];
		$emp_code = $rec_sauda_header_download['emp_code'];
		$product_group_name = $rec_sauda_header_download['product_group_name'];
		$state = $rec_sauda_header_download['state'];
		$customer_code = $rec_sauda_header_download['customer_code'];
		$branch_code = $rec_sauda_header_download['branch_code'];
		$plant_name = $rec_sauda_header_download['plant_name'];
		$vertical_value = $rec_sauda_header_download['vertical_value'];
		$conversion_one = $rec_sauda_header_download['conversion_factor'];
		$conversion_two = $rec_sauda_header_download['conversion_factor_two'];
		$sauda_date_time = $rec_sauda_header_download['sauda_date_time'];
		$sauda_time = $rec_sauda_header_download['sauda_time'];
		$liquid_TD = $rec_sauda_header_download['liquid_TD'];
		$state_code = $rec_sauda_header_download['state_code'];
		$is_upload = $rec_sauda_header_download['is_upload'];
		$formulation = $rec_sauda_header_download['formulation'];

		$sqlbroker="SELECT dns_broker_id,broker_name FROM broker_master WHERE broker_id='".$broker_id."'";
		$rsbroker=mysql_query($sqlbroker);
		$recbroker=mysql_fetch_array($rsbroker);
		$dns_broker_id=$recbroker['dns_broker_id'];
		$broker_name=$recbroker['broker_name'];
		$sauda_no=$rec_sauda_header_download['sauda_no'];
		$sauda_date=date('Y-m-d',strtotime($rec_sauda_header_download['sauda_date']));
		$credit_days=$rec_sauda_header_download['credit_days'];
		$sauda_date_calc=$rec_sauda_header_download['sauda_date_calc'];
		$payment_due_on=date('Y-m-d',strtotime("+$credit_days days,$sauda_date_calc"));
		$sauda_valid_from=date('Y-m-d',strtotime($rec_sauda_header_download['sauda_valid_from']));
		$sauda_valid_from_calc=$rec_sauda_header_download['sauda_valid_from_calc'];
		$sauda_valid_days = $rec_sauda_header_download['sauda_validity_period'];
		$valid_upto = date('Y-m-d',strtotime("+$sauda_valid_days days,$sauda_valid_from_calc"));
		$dns_prod_code=$rec_sauda_header_download['dns_prod_code'];
		$emp_code=$rec_sauda_header_download['emp_code'];
		$sku_code=$rec_sauda_header_download['sku_code'];
		$qty=$rec_sauda_header_download['qty'];
		$qty=round($qty,2);
		$convert_qty_two=$rec_sauda_header_download['convert_qty_two'];
		$UOM1=$rec_sauda_header_download['UOM1'];
		$prod_desc=$rec_sauda_header_download['prod_desc'];
		$vertical_value = $rec_sauda_header_download['vertical_value'];
		$vertical_value =str_replace(',','&',$vertical_value);
		$dns_branch_code=$rec_sauda_header_download['dns_branch_code'];
		$branch_name=$rec_sauda_header_download['branch_name'];
		$sale_rate=$rec_sauda_header_download['sale_rate'];
		$freight_charge=$rec_sauda_header_download['freight_charge'];
		$TD=$rec_sauda_header_download['TD'];
		$premium=$rec_sauda_header_download['premium'];
		if($premium=='')  $premium=0;
		$employee_name=$rec_sauda_header_download['emp_name'];
		$CMTD=$rec_sauda_header_download['CMTD'];
		if($freight_charge=='')  $freight_charge=0;
		if($freight_charge!=0) $incoterms='FOR';
		else  					$incoterms='Exw';
		$d_instruction = preg_replace('/[\r\n]+/', '',$rec_sauda_header_download['d_instruction']);
		$d_instruction =str_replace(',','',$d_instruction);
		/*$sauda_date=str_replace('-','.',$sauda_date);
		$sauda_date=str_replace('/','.',$sauda_date);
		$sauda_valid_from=str_replace('-','.',$sauda_valid_from);
		$sauda_valid_from=str_replace('/','.',$sauda_valid_from);
		$valid_upto=str_replace('-','.',$valid_upto);
		$valid_upto=str_replace('/','.',$valid_upto);

		$payment_due_on=str_replace('-','.',$payment_due_on);
		$payment_due_on=str_replace('/','.',$payment_due_on);*/


		//$amount=round(($qty*(($sale_rate+$freight_charge+$premium)-$TD)),2);
		$amount=$rec_sauda_header_download['amount'];
		if($CMTD=='')
		{
			$CMTD=0;
		}
		$sqlstate="SELECT `state` FROM state_master WHERE dns_state_code='".$state_code."'";
		$rsstate=mysql_query($sqlstate);
		$rowstate=mysql_fetch_array($rsstate);
		$state_name=$rowstate['state'];
		$sqlcustomercreditlimit="SELECT credit_limit FROM customer_vertical_credit_limit WHERE
								customer_code='".$customer_code."' AND branch_code='".$branch_code."' AND vertical_value='".$vertical_value."'";
		$rscustomercreditlimit=mysql_query($sqlcustomercreditlimit);
		$rowcustomercreditlimit=mysql_fetch_array($rscustomercreditlimit);
		$credit_limit=$rowcustomercreditlimit['credit_limit'];

		/*For Price Generation Parameter*/
		$sqllooserate="SELECT loose_rate_ton FROM pricing_detials WHERE product_group_code='".$product_group_code."' AND
						plant_name='".$plant_name."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rslooserate=mysql_query($sqllooserate);
		$rowlooserate=mysql_fetch_array($rslooserate);
		${loose_rate_ton.$product_group_code}=$rowlooserate['loose_rate_ton'];

		$sqlpackingprodwise="SELECT packing_cost FROM packing_master WHERE dns_prod_code='".$dns_prod_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rspackingprodwise=mysql_query($sqlpackingprodwise);
		$rowpackingprodwise=mysql_fetch_array($rspackingprodwise);
		$packing_cost=round($rowpackingprodwise['packing_cost'],2);
		if($packing_cost=='')   $packing_cost=0;

		if(${loose_rate_ton.$product_group_code}==0)
		 {
		   $packing_cost=0;
	     }
		$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$dns_prod_code."'
							AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdepotcostprodwise=mysql_query($sqldepotcostprodwise);
		$rowdepotcostprodwise=mysql_fetch_array($rsdepotcostprodwise);
		$depot_cost=round($rowdepotcostprodwise['depot_cost'],2);
		if($depot_cost=='')  		$depot_cost=0;

		if(${loose_rate_ton.$product_group_code}==0)
		 {
		   $depot_cost=0;
	   	 }
		
		/*$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$dns_prod_code."'
							AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsfreightcostprodwise=mysql_query($sqlfreightcostprodwise);
		$rowfreightcostprodwise=mysql_fetch_array($rsfreightcostprodwise);
		$freight_cost=round($rowfreightcostprodwise['freight_cost'],2);
		if($freight_cost=='')      $freight_cost=0;
		
		if($loose_rate_ton==0 && $formulation=='no')
		 {
		   $freight_cost=0;
	     }
		if($loose_rate_ton==0 && $formulation=='yes'){
			 $freight_cost=$freight_cost;
		 }*/

		//Temporary start
		$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$dns_prod_code."' AND 
						datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsqtytruckload=mysql_query($sqlqtytruckload);
		$countqtytruckload=mysql_num_rows($rsqtytruckload);
		if($countqtytruckload >0)
		{
			$rowqtytruckload=mysql_fetch_array($rsqtytruckload);
			${qty_truck_load.$dns_prod_code}=round($rowqtytruckload['qty_truck_load'],2);
		}
		if(${qty_truck_load.$dns_prod_code}=='')    ${qty_truck_load.$dns_prod_code}=0;

		$sqlhirecost="SELECT hire_cost FROM basic_freight WHERE branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' 
					ORDER BY datetime DESC LIMIT 0,1";
		$rshirecost=mysql_query($sqlhirecost);
		$counthirecost=mysql_num_rows($rshirecost);
		if($counthirecost >0)
		{
			$rowhirecost=mysql_fetch_array($rshirecost);
			$hire_cost=round($rowhirecost['hire_cost'],2);
		}
		if($hire_cost=='')    $hire_cost=0;

		if(${qty_truck_load.$dns_prod_code}>0)
		{
			$freight_cost=$hire_cost/${qty_truck_load.$dns_prod_code};
		}
		else
		{
			$freight_cost=0;
		}
		//echo $freight_cost;
		$freight_cost=round($freight_cost,2);
		//Temporary end

		$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$dns_prod_code."'
							AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
		$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
		$margin_cost=round($rowmargincostprodwise['margin_cost'],2);
		if($margin_cost=='')       $margin_cost=0;

		if(${loose_rate_ton.$product_group_code}==0)
		 {
		   $margin_cost=0;
	   }
		${material_cost.$dns_prod_code}=round((${loose_rate_ton.$product_group_code}/$conversion_two),2);
		${material_cost.$dns_prod_code}=round((${material_cost.$dns_prod_code}*$conversion_one),2);

		if(strpos($prod_desc,'LUP')==true){
			${material_cost.$dns_prod_code}=0;
		}
		if(${material_cost.$dns_prod_code}==0)
		{
			/*$sqlsalerate="SELECT sale_rate FROM sauda_mrp WHERE branch_code='".$branch_code."' AND product_code='".$sku_code."'";
			$rssalerate=mysql_query($sqlsalerate);
			$rowsalerate=mysql_fetch_array($rssalerate);
			$material_cost=$rowsalerate['sale_rate'];*/
			${material_cost.$dns_prod_code}=$sale_rate;
		}
		if(${material_cost.$dns_prod_code}==0){
			$realization_per_case=0;
			$realization_per_MT=0;
		}
		else
		{
		$realization_per_case=${material_cost.$dns_prod_code}-$TD+$premium;
		$realization_per_case=round($realization_per_case,2);
		$realization_per_MT=round((($realization_per_case*$rec_sauda_header_download['conversion_factor_two'])/$rec_sauda_header_download['conversion_factor']),2);
		}
		//$final_amount=$amount-(($amount*$CMTD)/100);
		$PR00=${material_cost.$dns_prod_code}+$packing_cost+$margin_cost+$premium-$TD-$liquid_TD;
		$FRC1=$freight_cost+$freight_charge+$depot_cost;
		$csv_sale_rate=$PR00+$FRC1;
		$final_amount=$amount;
		/*$final_amount=$amount;
		$valuesaudaheader  = $dns_customer_code.",";
		$valuesaudaheader .= $customer_name.",";
		$valuesaudaheader .= $dns_broker_id.",";
		$valuesaudaheader .= $broker_name.",";
		$valuesaudaheader .= $sauda_no.",";
		$valuesaudaheader .= $sauda_date.",";
		$valuesaudaheader .= $sauda_time.",";
		$valuesaudaheader .= $sauda_valid_from.",";
		$valuesaudaheader .= $valid_upto.",";
		$valuesaudaheader .= $dns_prod_code.",";
		$valuesaudaheader .= $qty.",";
		$valuesaudaheader .= $convert_qty_two.",";
		$valuesaudaheader .= $UOM1.",";
		$valuesaudaheader .= $product_group_name.",";
		$valuesaudaheader .= $prod_desc.",";
		$valuesaudaheader .= $dns_branch_code.",";
		$valuesaudaheader .= $branch_name.",";
		$valuesaudaheader .= $state_name.",";
		$valuesaudaheader .= $material_cost.",";
		$valuesaudaheader .= $freight_cost.",";
		$valuesaudaheader .= $packing_cost.",";
		$valuesaudaheader .= $depot_cost.",";
		$valuesaudaheader .= $margin_cost.",";
		//$valuesaudaheader .= $sale_rate.",";
		$valuesaudaheader .= $freight_charge.",";
		$valuesaudaheader .= $TD.",";
		$valuesaudaheader .= $liquid_TD.",";
		$valuesaudaheader .= $premium.",";
		$valuesaudaheader .= $PR00.",";
		$valuesaudaheader .= $FRC1.",";
		$valuesaudaheader .= $final_amount.",";
		$valuesaudaheader .= $incoterms.",";
		$valuesaudaheader .= $employee_name.",";
		$valuesaudaheader .= $payment_due_on.",";
		$valuesaudaheader .= $credit_limit.",";
		$valuesaudaheader .= $d_instruction.",";
		$valuesaudaheader .= $vertical_value.",";
		$valuesaudaheader .= $realization_per_case.",";
		$valuesaudaheader .= $realization_per_MT.",";
		$valuesaudaheader .= $csv_sale_rate.",";*/
		$sqlinsertsaudadownloadlog="INSERT INTO sauda_download_log SET customer_code='".$dns_customer_code."',
									customer_name='".$customer_name."',
									broker_id='".$dns_broker_id."',
									broker_name='".$broker_name."',sauda_no='".$sauda_no."',
									sauda_date='".$sauda_date."',
									sauda_time='".$sauda_time."',
									contract_valid_from='".$sauda_valid_from."',
									contract_valid_to='".$valid_upto."',
									prod_code='".$dns_prod_code."',
									qty='".$qty."',
									convert_qty_two='".$convert_qty_two."',
									UOM='".$UOM1."',
									product_group_code='".$product_group_code."',
									product_group_name='".$product_group_name."',
									prod_desc='".$prod_desc."',branch_code='".$dns_branch_code."',
									branch_name='".$branch_name."',
										state='".$state_name."',
									material_cost='".${material_cost.$dns_prod_code}."',
									primary_freight='".$freight_cost."',
									packing_cost='".$packing_cost."',
										depot_cost='".$depot_cost."',
									margin_cost='".$margin_cost."',
										freight_charge='".$freight_charge."',
									TD='".$TD."',
									liquid_TD='".$liquid_TD."',
									premium='".$premium."',
									PR00='".$PR00."',
									FRC1='".$FRC1."',
										amount='".$final_amount."',
									incoterms='".$incoterms."',
									emp_name='".$employee_name."',
									payment_due_on='".$payment_due_on."',
									cm_credit_limit='".$credit_limit."',
										remarks='".$d_instruction."',
										vertical='".$vertical_value."',
										realization_per_case='".$realization_per_case."',
									realization_per_MT='".$realization_per_MT."',
										sale_rate='".$csv_sale_rate."',download_time=CURRENT_TIMESTAMP()";
	mysql_query($sqlinsertsaudadownloadlog);									
	}
?>
