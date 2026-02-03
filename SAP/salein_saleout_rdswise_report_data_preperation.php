	<?php
	$nick_name='RKBK';
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	require("include/config-setup.php");
	define("DB","acedns_$nick_name");
	//require("include/dbcon.php");
	$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	require("include/config-email-setup.php");
	
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		
		$sqlbranch="SELECT branch_code FROM branch_master ORDER BY branch_name ASC";
		$resbranch=mysql_query($sqlbranch) or die(mysql_error()." Error in select branch: ".$sqlbranch);
		
        while($rowbranch=mysql_fetch_array($resbranch))
        {
			$branch_code=$rowbranch['branch_code'];
			$sqlbranchemployee="SELECT emp_code FROM employee_master WHERE branch_code='".$branch_code."' 
								AND SUBSTRING(emp_code,1,1)!='C'";
			$resbranchemployee=mysql_query($sqlbranchemployee) or die(mysql_error()." Error in select branch employee: ".$sqlbranchemployee);
			$branch_employee='';
			while($rowbranchemployee=mysql_fetch_array($resbranchemployee))
        	{
				$emp_code=$rowbranchemployee['emp_code'];
				$branch_employee=$branch_employee."'".$emp_code."'".',';
			}
			$branch_employee=substr($branch_employee,0,-1);
			//exit();
			if($branch_employee!=''){
				$sqlrdsemployee="SELECT rds_code,emp_code FROM rds_master WHERE emp_code IN($branch_employee)";
				//$sqlrdsemployee="SELECT rds_code,emp_code FROM rds_master WHERE emp_code IN('E0033')";
				$resrdsemployee=mysql_query($sqlrdsemployee) or die(mysql_error()." Error in select rds employee: ".$sqlrdsemployee);
				while($rowrdsemployee=mysql_fetch_array($resrdsemployee))
        		{
					$rds_code=$rowrdsemployee['rds_code'];
					$rds_employee="'".$rowrdsemployee['emp_code']."'";
					
					$sqlproduct="SELECT PM.prod_code FROM product_master PM WHERE 1 ORDER BY PM.prod_desc ASC";				
					$resproduct=mysql_query($sqlproduct) or die(mysql_error()." Error in select product group product: ".$sqlproduct);
					while($rowproduct=mysql_fetch_array($resproduct))
					{
						$prod_code=$rowproduct['prod_code'];					
						$opening_stk_initial=0;
						
						$sql_purchase_till_day="SELECT SUM(OD.qty) AS total_purchase_qty_till_day,SUM(OD.qty*OD.sale_rate) AS total_purchase_amount_till_day 
												FROM order_header OH,order_details OD
												WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
												AND SUBSTRING(OH.order_no,2,5) IN($rds_employee) AND OH.transaction_type='PB' AND
												DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d')";
						$res_purchase_till_day=mysql_query($sql_purchase_till_day) or die(mysql_error()." 
												Error in select purchase till day: ".$sql_purchase_till_day);
						$row_purchase_till_day=mysql_fetch_array($res_purchase_till_day);
						$purchase_till_day=$row_purchase_till_day['total_purchase_qty_till_day'];
						$purchase_amount_till_day=$row_purchase_till_day['total_purchase_amount_till_day'];
							
						$sql_sale_till_day="SELECT SUM(OD.qty) AS total_sale_qty_till_day,SUM(OD.qty*OD.sale_rate) AS total_sale_amount_till_day 
											FROM order_header OH,order_details OD
											WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
											AND SUBSTRING(OH.order_no,2,5) IN($rds_employee) AND OH.transaction_type='SB' AND
											DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d')<=DATE_FORMAT(NOW(),'%Y%-%m-%d')";
						$res_sale_till_day=mysql_query($sql_sale_till_day) or die(mysql_error()." Error in select sale till day: ".$sql_sale_till_day);
						$row_sale_till_day=mysql_fetch_array($res_sale_till_day);
						$sale_till_day=$row_sale_till_day['total_sale_qty_till_day'];	
						$sale_amount_till_day=$row_sale_till_day['total_sale_amount_till_day'];
						
						$sql_shortage_till_day="SELECT SUM(OD.qty) AS total_shortage_qty_till_day,SUM(OD.qty*OD.sale_rate) AS total_shortage_amount_till_day 
												FROM order_header OH,order_details OD
												WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
												AND SUBSTRING(OH.order_no,2,5) IN($rds_employee) AND OH.transaction_type IN('SH') AND
												DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d')<=DATE_FORMAT(NOW(),'%Y%-%m-%d')";
						$res_shortage_till_day=mysql_query($sql_shortage_till_day) or die(mysql_error()." 
												Error in select shortage till day: ".$sql_shortage_till_day);
						$row_shortage_till_day=mysql_fetch_array($res_shortage_till_day);
						$shortage_till_day=$row_shortage_till_day['total_shortage_qty_till_day'];	
						$shortage_amount_till_day=$row_shortage_till_day['total_shortage_amount_till_day'];
						
						$sql_stock_transfer_till_day="SELECT SUM(OD.qty) AS total_stocktransfer_qty_till_day 
													FROM order_header OH,order_details OD
													WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
													AND SUBSTRING(OH.order_no,2,5) IN($rds_employee) AND OH.transaction_type='ST' AND
													DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d')<=DATE_FORMAT(NOW(),'%Y%-%m-%d')";
						$res_stock_transfer_till_day=mysql_query($sql_stock_transfer_till_day) or die(mysql_error()." 
												Error in select stock transfer till day: ".$sql_stock_transfer_till_day);
						$row_stock_transfer_till_day=mysql_fetch_array($res_stock_transfer_till_day);
						$stock_transfer_till_day=$row_stock_transfer_till_day['total_stocktransfer_qty_till_day'];
						
						$sql_stock_receive_till_day="SELECT SUM(OD.qty) AS total_stockreceive_qty_till_day 
													FROM order_header OH,order_details OD
													WHERE OH.order_no=OD.order_no AND OD.sku_code='".$prod_code."' AND SUBSTRING(OH.order_no,1,1)='O'
													AND SUBSTRING(OH.order_no,2,5) IN($rds_employee) AND OH.transaction_type='BT' AND
													DATE_FORMAT(SUBSTRING(OH.order_no,7,8),'%Y%-%m-%d')<=DATE_FORMAT(NOW(),'%Y%-%m-%d')";
						$res_stock_receive_till_day=mysql_query($sql_stock_receive_till_day) or die(mysql_error()." 
												Error in select stock receive till day: ".$sql_stock_receive_till_day);
						$row_stock_receive_till_day=mysql_fetch_array($res_stock_receive_till_day);
						$stock_receive_till_day=$row_stock_receive_till_day['total_stockreceive_qty_till_day'];		
							
						$purchase_qty_year=$purchase_till_day;
						$sale_qty_year=$sale_till_day;
						$shortage_qty_year=$shortage_till_day;
						$stock_transfer_qty_year=$stock_transfer_till_day;
						$stock_receive_qty_year=$stock_receive_till_day;
						
						$closing_stock=($opening_stk_initial+$purchase_qty_year+$stock_receive_qty_year)-($sale_qty_year+$shortage_qty_year+$stock_transfer_qty_year);
						
						$sql_branch_rds_product_stk="SELECT branch_code,product_code FROM branch_rds_product_wise_stock WHERE branch_code='".$branch_code."' 
												AND rds_code='".$rds_code."' AND product_code='".$prod_code."'";
						$res_branch_rds_product_stk=mysql_query($sql_branch_rds_product_stk) or die(mysql_error()." 
												Error in select branch rds product stk: ".$sql_branch_rds_product_stk);	
						$cnt_branch_rds_product_stk=mysql_num_rows($res_branch_rds_product_stk);
						if($cnt_branch_rds_product_stk<1)
						{										
							$sql_insert_branch_rds_product_stk="INSERT INTO branch_rds_product_wise_stock SET 
															branch_code='".$branch_code."',
															rds_code='".$rds_code."',
															product_code='".$prod_code."',
															closing_stk='".$closing_stock."'";
							$res_insert_branch_rds_product_stk=mysql_query($sql_insert_branch_rds_product_stk) or die(mysql_error()." error
												Insert in branch rds product stk: ".$sql_insert_branch_rds_product_stk);	
						}
						else
						{
							$sql_update_branch_rds_product_stk="UPDATE branch_rds_product_wise_stock SET 
															closing_stk='".$closing_stock."' WHERE 
															branch_code='".$branch_code."' AND rds_code='".$rds_code."' AND product_code='".$prod_code."'";
							$res_update_branch_rds_product_stk=mysql_query($sql_update_branch_rds_product_stk) or die(mysql_error()." error
												update in branch rds product stk: ".$sql_update_branch_rds_product_stk);	
						}
					}
				}
			}
		}
				
	mysql_close($link);
	echo 'SUCCESS';	
?>		