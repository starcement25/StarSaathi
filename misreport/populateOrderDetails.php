<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$trans_id=$_REQUEST['trans_id'];
$order_no=$trans_id;

$sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_code='".$_REQUEST['customer_code']."'";
$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in select customer name : ".$sqlcustomer);	
$rowcustomer=mysql_fetch_array($rscustomer);
$customer=$rowcustomer['customer_name'];

?>
<style type="text/css">
.TDHEAD{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
	FONT-WEIGHT: bold;
	COLOR: #FFFFFF;
	BACKGROUND-COLOR: #A92A61;/*#92C006;*/
}

.TDHEAD_SUB{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
	FONT-WEIGHT: bold;
	BACKGROUND-COLOR:#c0c8b0;
}

.border{
	BORDER: #A92A61/*#80A537*/ 1px solid;
}
TD{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
}

</style>
<?php 
if(sauda_allocation=='yes' && sauda_rate_variable=='yes')
{ 
	$freight_charge_TR="<td width=\"10%\" align=\"left\" style=\"padding-left:20px;\">Freight Charge</td>";
	$freight_charge_condition=",OD.freight_charge ";
	
	$order_suada_text="Sauda";
	$order_suada_colspan='8';
}
else
{
	$order_suada_text="Order";
	$order_suada_colspan='7';
}
$tableval='
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;">
    <tr class="TDHEAD" > 
        <td colspan="'.$order_suada_colspan.'" align="center"><strong>'.$order_suada_text.' Details of  '.$customer.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
         <td width="5%" align="center">Sl</td>
		<td width="14%" align="left" style="padding-left:20px;">Brand</td>
		<td width="14%" align="left" style="padding-left:20px;">Brand Form</td>
		<td width="14%" align="left" style="padding-left:20px;">Brand Sub Form</td>
		<td width="18%" align="left" style="padding-left:20px;">Sku</td>
		<td width="13%" align="left" style="padding-left:20px;">Quantity</td>'.$freight_charge_TR.'
		<td width="" align="left" style="padding-left:20px;">Amount</td>
    </tr> ';
			
		
		if(no_of_filter==3){
		$sqlorderdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PM.prod_desc,OD.qty,OD.mrp_code".$freight_charge_condition."
						 FROM order_details OD,product_master PM,product_group_master PGM,product_sub_group_master PSGM
				  		 WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code 
						  AND PM.product_group_code=PGM.product_group_code AND PGM.product_group_code=PSGM.product_group_code AND PM.prod_code=OD.sku_code 
						  AND OD.order_no='".$order_no."'";
		}
		elseif(no_of_filter==2){
		$sqlorderdetails="SELECT PGM.product_group_name,PM.prod_desc,OD.qty,OD.mrp_code".$freight_charge_condition."
						 FROM order_details OD,product_master PM,product_group_master PGM
				  		 WHERE PM.product_group_code=PGM.product_group_code AND PM.prod_code=OD.sku_code AND OD.order_no='".$order_no."'";
		}
		elseif(no_of_filter==1){
		$sqlorderdetails="SELECT PM.prod_desc,OD.qty,OD.mrp_code".$freight_charge_condition."
						 FROM order_details OD,product_master PM
				  		 WHERE PM.prod_code=OD.sku_code AND OD.order_no='".$order_no."'";
		}
		elseif(no_of_filter==4){
		$sqlorderdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PBM.product_brand_name,PM.prod_desc,OD.qty,OD.mrp_code,OD.amount".$freight_charge_condition."
						 FROM order_details OD,product_master PM,product_group_master PGM,product_sub_group_master PSGM,product_brand_master PBM
				  		 WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code 
						 AND PM.product_group_code=PGM.product_group_code AND PGM.product_group_code=PSGM.product_group_code AND 
						 PM.prod_code=OD.sku_code AND PSGM.product_sub_group_code=PBM.product_sub_group_code AND 
						 PM.product_brand_code=PBM.product_brand_code  AND OD.order_no='".$order_no."'";
		}
		
		$rsorderdetails=mysql_query($sqlorderdetails) or die(mysql_error()." Error in select order details: ".$sqlorderdetails);
 
		 $cnt=$GLOBALS[start]+1;
        while($roworderdetails=mysql_fetch_array($rsorderdetails))
        {
		 		if(mrp=='yes'){
					$queryval='mrp';
				}
				if(sale_rate=='yes'){
					$queryval='sale_rate';
				}
				$mrp_code=$roworderdetails['mrp_code'];
				$sqlmrpval="SELECT ".$queryval." FROM mrp WHERE mrp_code='".$mrp_code."' ";
				$rsmrpval=mysql_query($sqlmrpval);
				$rowmrpval=@mysql_fetch_array($rsmrpval);
				$mrp=$rowmrpval["$queryval"];
				
				$brand_code=$roworderdetails['product_group_name'];
				$brand_form_code=$roworderdetails['product_sub_group_name'];
				$brand_sub_form_code=$roworderdetails['product_brand_name'];
				$sku=$roworderdetails['prod_desc'];
				$qty=number_format($roworderdetails['qty'],2);
				$AMOUNT=$roworderdetails['amount'];
				if($AMOUNT ==0)
				{
					$amountval=($roworderdetails['qty']*$mrp);
					$amount=number_format($amountval,2);
				}
				else
				{
					$amountval=$AMOUNT;
					$amount=number_format($AMOUNT,2);
				}
				if(sauda_allocation=='yes' && sauda_rate_variable=='yes')
				{ 
					$freight_charge=$roworderdetails['freight_charge'];
					$freight_charge_TD="<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($freight_charge,2)."</td>";
					$amount=$amountval+$freight_charge;
					$amount=number_format($amount,2);
				}
				
				$rowval.="<tr> 
							<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$brand_code."</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$brand_form_code."</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$brand_sub_form_code."</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$sku."</td>
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$qty."</td>".$freight_charge_TD."
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$amount."</td>
				  		</tr>";
		        
      }
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;

echo $finalval;

mysql_close($link);
?>