<?php
include "web_check.php";

include "star_connection.php";

$branch_master = "branch_master";

$customer_master = "customer_master";

$dealer_security_ledger_status = "dealer_security_ledger_status";



$pg_sts_arr = array();

$pg_sts_arr[] = array("key_val"=>"ACTIVE","title_val"=>"ACTIVE");

$pg_sts_arr[] = array("key_val"=>"INACTIVE","title_val"=>"INACTIVE");



$new_qry_string_filtered = "";

$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";

$sl_status = $_GET["sl_status"] ? addslashes(trim($_GET["sl_status"])) : "";

//$sl_status = "INACTIVE";

$astn_branch_code = $_GET["astn_branch_code"] ? addslashes(trim($_GET["astn_branch_code"])) : "";

$whr_str = "";

$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls,"sl_status"=>$sl_status,"astn_branch_code"=>$astn_branch_code );

foreach($search_array as $search_array_key=>$search_array_val){

	if($search_array_key=="srch_dlr_dtls"){

		if($search_array_val!=''){

			//if(trim($whr_str)!=""){

				$aand = " and";

			/*}else{

				$aand = "";

			}*/

$whr_str .= "$aand ($customer_master.`dns_customer_code` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%' or $customer_master.`customer_id` like '%$search_array_val%' ) ";

			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;

		}

	}

	if($search_array_key=="sl_status"){

		if($search_array_val!=''){

			//if(trim($whr_str)!=""){

				$aand = " and";

			/*}else{

				$aand = "";

			}*/

			if($search_array_val=='ACTIVE')

			{

				//$whr_str .= " $aand $dealer_security_ledger_status.`security_ledger_status`='$search_array_val' ";
				$whr_str .= " $aand ($dealer_security_ledger_status.`security_ledger_status`='$search_array_val' OR $customer_master.`dns_customer_code` NOT IN(SELECT customer_code FROM $dealer_security_ledger_status ))";

			}

			if($search_array_val=='INACTIVE')

			{

				//$whr_str .= " $aand ($dealer_security_ledger_status.`security_ledger_status`='$search_array_val'  OR $customer_master.`dns_customer_code` NOT IN(SELECT customer_code FROM $dealer_security_ledger_status ))";
				$whr_str .= " $aand $dealer_security_ledger_status.`security_ledger_status`='$search_array_val'";

			}

			$new_qry_string_filtered .= "&sl_status=".$search_array_val;



		}

	}

	if($search_array_key=="astn_branch_code"){

		if($search_array_val!=''){

			$aand = " and";

			$whr_str .= "$aand $customer_master.`branch_code` = '$search_array_val' ";

			$new_qry_string_filtered .= "&astn_branch_code=".$search_array_val;

		}

	}

}

if($whr_str!=""){

	$new_whr_str = $whr_str;

}else{

	$new_whr_str ="";

}

$add_page_name = "dealer_wise_security_ledger_status.php";

$page_name = "dealer_wise_security_ledger_status.php";

$cnt = 0;

$countrow = 1;

/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;

$targetpage = $page_name;

$limit = "100";

$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$brsql = "select `branch_code`,`branch_name` from $branch_master where `acedns`='Y' order by `branch_name` asc";

$brres = mysql_query($brsql);

$total_brres = mysql_num_rows($brres);

//sk add condition Dealers will start with 10, RSSD will start with 15, shiptopartydealer will start with 14 & shiptopartysubdealer will start with 14

$con="AND
(
    (customer_master.`cust_type` = 'Dealer' AND customer_master.`customer_id` LIKE '10%')
    OR
    (customer_master.`cust_type` = 'RSSD' AND customer_master.`customer_id` LIKE '15%')
    OR
    
    (customer_master.`cust_type` = 'Ship to Party-dealer' AND customer_master.`customer_id` LIKE '14%')
    OR
    (customer_master.`cust_type` = 'ShiptoParty-Subdeale' AND customer_master.`customer_id` LIKE '14%')
)  ";


$pgsql = "SELECT $customer_master.`customer_code` FROM $customer_master left join $dealer_security_ledger_status on $customer_master.`dns_customer_code`=$dealer_security_ledger_status.`customer_code` WHERE $customer_master.cust_type='Dealer' $con $new_whr_str";

$pgres = mysql_query($pgsql);

$total_pgres = mysql_num_rows($pgres);

$start_from = (($page-1)*$limit);

$prev = $page - 1;							//previous page is page - 1

$next = $page + 1;							//next page is page + 1

$lastpage = ceil($total_pgres/$limit);   //lastpage is = total pages / items per page, rounded up.

$lpm1 = $lastpage - 1;

/*---------PAGINATION RELATED CODE START----------*/

include "web_header.php";

?>

<style>

.dlr_prfl_img{

	width:150px;

}

.bwps_sel{

	width:150px;

}

.os_ldr{

	position:absolute;

	right:5px;

	top:5px;

}

</style>



<section class="content">

        <div class="container-fluid">

            <div class="block-header">

                

            </div>

            <!-- Basic Examples -->

            <div class="row clearfix">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    <div class="card">

                        <div class="header">

                          <h2>Dealer Wise Security Ledger Status (<?php echo $total_pgres;?>)&nbsp;&nbsp;<!--<a href="export_sp_destination.php?get_type=all" class="btn bg-red waves-effe">Export&nbsp;all</a>-->

                         

                          </h2>

                            <div class="row clearfix">

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 add_top_bottom_padding">

<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Dealer Details">

    </div>

     <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">

     <select class="form-control" id="sl_status">

    <option value="">Status</option>

    <option value="ACTIVE" <?php if($sl_status=="ACTIVE"){?> selected="selected" <?php } ?>>ACTIVE</option>

    <option value="INACTIVE" <?php if($sl_status=="INACTIVE"){?> selected="selected" <?php } ?>>INACTIVE</option>

    </select>

    </div>

    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding2">

<select class="form-control" id="astn_branch_code" name="astn_branch_code" style="padding-left:2px;" data-placeholder="Select Branch Name">

<option value="">Select Branch Name</option>

<?php

if($total_brres>0){

	while($brrow=mysql_fetch_assoc($brres)){

		$the_br_code = $brrow["branch_code"];

		$the_br_name = $brrow["branch_name"];?>

        <option value="<?php echo $the_br_code;?>" <?php if($the_br_code==$astn_branch_code){ ?> selected="selected" <?php } ?>><?php echo $the_br_name." (".$the_br_code.")";?></option>

		<?php

	}

	

}

?>



</select>

    </div>							

   

    

    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">

    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>

    </div>

    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">

    <button type="button" class="btn bg-red waves-effect srch_reset_btn" >Reset</button>

    </div>

        

    </div>

<span style="clear:both;display:block;"></span>

                        </div>

                        <div class="body">

<?php

echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);

?>

<span style="display:block; clear:both;"></span>

 <div class="table-responsive">

                                <table class="table table-bordered table-striped table-hover">

                                    <thead>

                                        <tr>

                                            <th>Dealer&nbsp;Code</th>

                                            <th>Dealer&nbsp;Name</th>

                                            <th>Status</th>

                                        </tr>

                                    </thead>

                                    <tfoot>

                                        <tr>

                                            <th>Dealer&nbsp;Code</th>

                                            <th>Dealer&nbsp;Name</th>

                                            <th>Status</th>

                                        </tr>

                                    </tfoot>

                                    <tbody>

<?php



$sql1 = "SELECT $customer_master.`customer_code`,$customer_master.`dns_customer_code`,$customer_master.`customer_name`,$dealer_security_ledger_status.`security_ledger_status` FROM $customer_master left join $dealer_security_ledger_status on $customer_master.`dns_customer_code`=$dealer_security_ledger_status.`customer_code` WHERE $customer_master.cust_type='Dealer' $con $new_whr_str order by $customer_master.`customer_name` asc limit $start_from,$limit";

$res1 = mysql_query($sql1);

$totres1 = mysql_num_rows($res1);

if($totres1>0){

	while($row1=mysql_fetch_assoc($res1)){

		$customer_code = $row1["customer_code"];

		$dns_customer_code = $row1["dns_customer_code"];

		$customer_name = $row1["customer_name"];

		$security_ledger_status = $row1["security_ledger_status"];

		if($security_ledger_status=='') $security_ledger_status='ACTIVE';

		

?>

<tr>

<td><?php echo $dns_customer_code;?></td>

<td><?php echo $customer_name;?></td>

<td style="position:relative;">



<select class="form-control cwts_sel" id="cwts_sel_<?php echo $dns_customer_code;?>" the_customer_code="<?php echo $dns_customer_code;?>">

<?php

if(count($pg_sts_arr)>0){

	foreach($pg_sts_arr as $pg_sts_arr_val){

		$the_key_val = $pg_sts_arr_val["key_val"];

		$the_title_val = $pg_sts_arr_val["title_val"]; ?>

        <option value="<?php echo $the_key_val;?>" <?php if($the_key_val==$security_ledger_status){?> selected="selected" <?php } ?>><?php echo $the_title_val;?></option>

        <?php

	}

	

}



?>

</select>





<span class="os_ldr" id="ca_ldr_<?php echo $dns_customer_code;?>"></span></td>





</tr>

<?php

	}

}else{

?>

<tr>

<td style="text-align:center" colspan="3">No data found.</td>

</tr>

<?php

}

?>

</tbody>

</table>

                            </div>

<?php

echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);

?>

<span style="display:block; clear:both;"></span>

                        </div>

                   

                    </div>

                </div>

            </div>

            <!-- #END# Basic Examples -->

            <!-- Exportable Table -->

            

            <!-- #END# Exportable Table -->

        </div>

    </section>

<script type="text/javascript">

jQuery(function(){

	var imgs = '<img src="images/ajax-loader.gif"/>';

	var done_img = '<img src="images/success_tick.png"/>';

	

	jQuery(".cwts_sel").change(function(){

		var ancr_elmnt = jQuery(this);

		var the_status = ancr_elmnt.val();

		var the_customer_code = ancr_elmnt.attr("the_customer_code");

		//alert(the_customer_code+the_status);

		if(the_customer_code!=""){

			var for_loader = jQuery("#ca_ldr_"+the_customer_code);

			for_loader.html(imgs);

			jQuery.ajax({

			url: 'ajax_security_ledger_status_update.php',

			type: 'post',

			dataType: "JSON",

			data: "the_customer_code="+the_customer_code+"&the_status="+the_status,

			success: function(response){

			if(response.process_status=="YES"){

			for_loader.html(done_img);

			setTimeout(function (){

			for_loader.html("");

			},3000);

			}else{

			for_loader.html("");

			alert(response.process_message);

			}

			}

			});

		}

	});

	

	jQuery(".srch_btn").click(function(){

		var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();

		var sl_status = jQuery("#sl_status").val();	

		var astn_branch_code = jQuery("#astn_branch_code").val();

		var qstring ="";

		var amp = "";

		if(srch_dlr_dtls!="" || sl_status!="" || astn_branch_code!="" ){

			

		if(astn_branch_code!=""){

			if(qstring!=""){

				qstring = qstring+"&astn_branch_code="+encodeURIComponent(astn_branch_code);

			}else{

				qstring = qstring+"astn_branch_code="+encodeURIComponent(astn_branch_code);

			}

		}

		if(srch_dlr_dtls!=""){

			if(qstring!=""){

				qstring = qstring+"&srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);

			}else{

				qstring = qstring+"srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);

			}

		}

		if(sl_status!=""){

			if(qstring!=""){

				qstring = qstring+"&sl_status="+encodeURIComponent(sl_status);

			}else{

				qstring = qstring+"sl_status="+encodeURIComponent(sl_status);

			}

		}

		

		  if(qstring!=""){

			 qstring = "?"+qstring; 

		  }

		window.location = "<?php echo $page_name;?>"+qstring;

		}else{

			alert("Please select atleast one field to search.");

		}

	});

	

	jQuery(".srch_reset_btn").click(function(){

		window.location = "<?php echo $page_name;?>";

	});

});

</script>

<?php

include "web_footer.php";

mysql_close();

?>