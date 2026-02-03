<?php
include "star_connection.php";
$destination_master = "destination_master";
$destination_wise_price = "destination_wise_price";
$product_master = "product_master";


/*$sqlall = "select `destination_name` from $destination_wise_price where `destination_name`!='' and `dns_destination_code` is null group by `destination_name` order by `destination_name` asc";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
if($totall>0){
	while($row11=mysql_fetch_assoc($resall)){
		$destination_name = $row11["destination_name"] ? addslashes(trim($row11["destination_name"])) : "";
		$sql1 = "select `dns_destination_code` from $destination_master where `destination_name`='$destination_name' and `dns_destination_code`!='' order by `dns_destination_code` limit 0,1";
		$res1 = mysql_query($sql1);
		$tot1 = mysql_num_rows($res1);
		if($tot1>0){
			$row1=mysql_fetch_assoc($res1);
			$dns_destination_code = $row1["dns_destination_code"] ? addslashes(trim($row1["dns_destination_code"])) : "";
			$sql2_upd = "update $destination_wise_price set `dns_destination_code`='$dns_destination_code' where `destination_name`='$destination_name'";
			$res2_upd = mysql_query($sql2_upd);
		}
		
	}	
}*/


/*$sqlall = "select `product_name` from $destination_wise_price where `product_name`!='' group by `product_name` order by `product_name` asc";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
if($totall>0){
	while($row11=mysql_fetch_assoc($resall)){
		$product_name = $row11["product_name"] ? addslashes(trim($row11["product_name"])) : "";
		$sql1 = "select `dns_prod_code` from $product_master where `prod_desc`='$product_name' and `dns_prod_code`!='' order by `dns_prod_code` limit 0,1";
		$res1 = mysql_query($sql1);
		$tot1 = mysql_num_rows($res1);
		if($tot1>0){
			$row1=mysql_fetch_assoc($res1);
			$dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
			$sql2_upd = "update $destination_wise_price set `dns_prod_code`='$dns_prod_code' where `product_name`='$product_name'";
			$res2_upd = mysql_query($sql2_upd);
		}
		
	}	
}*/

mysql_close();
?>