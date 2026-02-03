<?php
set_time_limit(1000);
error_reporting(E_ALL ^ E_NOTICE);
require("include/config.php");
require("include/dbcon.php");


//Product group code checking start
		$sqlgroupcodesub_group="SELECT product_sub_group_code,product_brand_code,size FROM sku_master ";
		$rsgroupcodesub_group=mysql_query($sqlgroupcodesub_group) or die(mysql_error());
		$cntgroupcodesub_group=mysql_num_rows($rsgroupcodesub_group);
		if($cntgroupcodesub_group>0)
		{
			$color_code='C0001';
			while($rowgroupcodesub_group=mysql_fetch_array($rsgroupcodesub_group))
			{
				$product_sub_group_code=$rowgroupcodesub_group['product_sub_group_code'];
				$product_brand_code_database=$rowgroupcodesub_group['product_brand_code']; //color
				$size=$rowgroupcodesub_group['size'];
				
				$sqlempnamechk="SELECT color_name FROM color_master WHERE color_code='".trim($product_brand_code_database)."'";
				$rsempnamechk=mysql_query($sqlempnamechk);
				$rowempnamechk=mysql_fetch_array($rsempnamechk);
				$color_name=$rowempnamechk['color_name'];
				
				$product_brand_name=$color_name.'-'.$size;
				$product_brand_code=$color_code;
				
			
				$sqlselect="SELECT product_brand_name FROM product_brand_master 
							WHERE product_brand_name='".$product_brand_name."' AND  product_sub_group_code='".$product_sub_group_code."'";
				$rsselect=mysql_query($sqlselect);
				$cntselect=mysql_num_rows($rsselect);			
				if($cntselect<1){
					$sqlinsert="INSERT INTO product_brand_master 
								set product_brand_code='".$product_brand_code."',
								product_brand_name='".$product_brand_name."',
								product_sub_group_code='".$product_sub_group_code."'";
					if(mysql_query($sqlinsert) or die(mysql_error())){
						$sqlupdate="UPDATE sku_master SET product_brand_code='".$product_brand_code."' WHERE 
							product_brand_code='".$product_brand_code_database."' AND size='".$size."'";
							
							if(mysql_query($sqlupdate) or die(mysql_error())){
								
								echo 'successful';
							}
						
					}
					$color_code++;
				}
			}
		}
?>