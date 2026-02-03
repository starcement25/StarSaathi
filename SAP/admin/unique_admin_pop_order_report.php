<?php
include "unique_web_header.php";
include "web_check.php";
include "star_connection.php";

$pop_order="pop_order";
$pop_product_master="pop_product_master";
$customer_master="customer_master";
$branch_master="branch_master";
$t_order_pop="T_ORDER_POP";
$product_master="product_master";
$broker_master="broker_master";
$lifting="lifting";

function accent2ascii($str)
{
    $charset = 'UTF-8';
	$str = htmlentities($str, ENT_NOQUOTES, $charset);

    // $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '', $str);
    // $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    // $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
    // $str=preg_replace('/[\x80-\xFF]/', '', $str);
    return $str;
}

$new_qry_string_filtered = "";
$export_filtered_str = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$whr_str = "";

$add_page_name = "unique_admin_pop_order_report.php";
$page_name = "unique_admin_pop_order_report.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "10";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "SELECT * FROM $t_order_pop ORDER BY `order_date` DESC";
$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
$start_from = (($page-1)*$limit);
$prev = $page - 1;							//previous page is page - 1
$next = $page + 1;							//next page is page + 1
$lastpage = ceil($total_pgres/$limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;

/*---------PAGINATION RELATED CODE START----------*/
?>

<script type="text/javascript">
jQuery(function () {
	
});
</script>
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                          <h2>POP Order&nbsp;(<?php echo $total_pgres; ?>)

                          </h2><br><br>
                            
<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_branch">
<option value="" selected>Select Branch</option>

<?php

$sql3 = "select `branch_code`,`branch_name` from $branch_master where `acedns`='Y' order by `branch_name`";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);

if($totres3>0){
	while($row3=mysql_fetch_assoc($res3)){
		$the_branch_code = $row3["branch_code"];
		$the_branch_name = $row3["branch_name"];
		?>
 <option value="<?php echo $the_branch_code;?>"><?php echo $the_branch_name;?></option>
        <?php
	}
}
?>
</select>
    </div>
<div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
    <input type="text" class="form-control" id="srch_cust_dtls" value="<?php echo $srch_cust_dtls;?>" placeholder="Search Cust ID">
</div>

<div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
    <input type="date" class="form-control" id="start_date" />
</div>

<div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
    <input type="date" class="form-control" id="end_date" />
</div>

<div class="col-lg-1 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
   <button type="button" class="btn bg-red waves-effect srch_btn" onclick="alpha_func()" >Search</button>
</div>
<div class="col-lg-1 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" onclick="reset_func()" >Reset</button>
</div>

<div class="col-lg-1 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<form action="export_data.php" method="post">
    <input type="submit" value="Export" class="btn bg-red waves-effect srch_export_btn" />
</form>
</div>
<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">

</div>
    </div>

<span style="clear:both;display:block;"></span>
                        </div>
                        <div class="body">
<?php
    echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
<!-- Display your data table as before -->

<!-- Add Pagination Buttons -->

<div class="pagination">
    <?php if ($currentPage > 1) : ?>
        <a href="?page=<?php echo $currentPage - 1; ?>" class="btn btn-primary">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
        <a href="?page=<?php echo $i; ?>" <?php echo ($i == $currentPage) ? 'class="btn btn-primary active"' : 'class="btn btn-primary"'; ?>><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($currentPage < $totalPages) : ?>
        <a href="?page=<?php echo $currentPage + 1; ?>" class="btn btn-primary">Next</a>
    <?php endif; ?>
</div>
<br><br>
 <div class="table-responsive">
<table id="data_table" class="table table-bordered table-striped table-hover">
<thead>
<tr>
<th>DATE&nbsp;&&nbsp;TIME</th>
<th>MAIN&nbsp;ORDER&nbsp;DETAILS</th>
<th>ORDER&nbsp;ID</th>
<th>CUSTOMER&nbsp;CODE</th>
<th>CUSTOMER&nbsp;NAME</th>
<th>LINKED&nbsp;DEALER&nbsp;CODE</th>
<th>LINKED&nbsp;DEALER&nbsp;NAME</th>
<th>BRANCH</th>
<th>PRODUCT&nbsp;NAME</th>
<th>QTY</th>
<th>PRICE&nbsp;(PER&nbsp;PCS)</th>
<th>GST&nbsp;(%)</th>
<th>NET AMOUNT</th>
<th>GST&nbsp;AMOUNT</th>
<th>TOTAL&nbsp;AMOUNT</th>
<th>ADDRESS</th>
<th>PIN&nbsp;CODE</th>
<th>REMARKS</th>
<th>ADDRESS&nbsp;AND&nbsp;PIN&nbsp;CODE&nbsp;TO&nbsp;BE&nbsp;PRINTED</th>
<th>CONTACT&nbsp;NO.&nbsp;TO&nbsp;BE&nbsp;PRINTED</th>
</tr>
</thead>
<tfoot>
<tr>
<th>DATE&nbsp;&&nbsp;TIME</th>
<th>MAIN&nbsp;ORDER&nbsp;DETAILS</th>
<th>ORDER&nbsp;ID</th>
<th>CUSTOMER&nbsp;CODE</th>
<th>CUSTOMER&nbsp;NAME</th>
<th>LINKED&nbsp;DEALER&nbsp;CODE</th>
<th>LINKED&nbsp;DEALER&nbsp;NAME</th>
<th>BRANCH</th>
<th>PRODUCT&nbsp;NAME</th>
<th>QTY</th>
<th>PRICE&nbsp;(PER&nbsp;PCS)</th>
<th>GST&nbsp;(%)</th>
<th>NET AMOUNT</th>
<th>GST&nbsp;AMOUNT</th>
<th>TOTAL&nbsp;AMOUNT</th>
<th>ADDRESS</th>
<th>PIN&nbsp;CODE</th>
<th>REMARKS</th>
<th>ADDRESS&nbsp;AND&nbsp;PIN&nbsp;CODE&nbsp;TO&nbsp;BE&nbsp;PRINTED</th>
<th>CONTACT&nbsp;NO.&nbsp;TO&nbsp;BE&nbsp;PRINTED</th>
</tr>
</tfoot>
<tbody id="table_body">
<?php

$new_price_format_datetime = "2023-09-09 00:00:00";
$new_price_format_datetime_ts = strtotime($new_price_format_datetime);

$sql1 = "select * from $t_order_pop order by `order_date` desc";

$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
    while($row1=mysql_fetch_array($res1)){
    $main_order_id = $row1["the_order_id"] ? trim($row1["the_order_id"]) : "";
    $order_id = $row1["APPORDERNO"] ? trim($row1["APPORDERNO"]) : "";
    $order_date = $row1["order_date"] ? trim($row1["order_date"]) : "";
    $formatted_date=date("Y-m-d", strtotime($order_date));
	
	$order_date_time = $order_date;
	$order_date_time_ts = strtotime($order_date_time);
	
    // echo $formatted_date;
    $customer_code = $row1["customer_code"] ? trim($row1["customer_code"]) : "";
    $printed_address_pin = $row1["printed_address_pin"] ? trim($row1["printed_address_pin"]) : "";
    $printed_address_pin =accent2ascii($row1["printed_address_pin"]);
    $contact_num_printed = $row1["contact_num_printed"] ? trim($row1["contact_num_printed"]) : "";
    $sql_customer="select * from $customer_master where `customer_code`='$customer_code'";
    $query_customer=mysql_query($sql_customer);
    $result_customer=mysql_fetch_array($query_customer);
    
    $real_customer_code=$result_customer["customer_id"] ? trim($result_customer["customer_id"]) : "";
    // echo $real_customer_code."<br/>";
    $real_customer_name=$result_customer["customer_name"] ? trim($result_customer["customer_name"]) : "";
    // echo $real_customer_name."<br/>";
    $branch_code = $row1["branch_code"] ? trim($row1["branch_code"]) : "";
    $address = $row1["address"] ? trim($row1["address"]) : "";
    $address =accent2ascii($row1["address"]);
    $pincode = $row1["pin"] ? trim($row1["pin"]) : "";
    $status = $row1["status"] ? trim($row1["status"]) : "";
    $the_tracking_id = $row1["the_tracking_id"] ? trim($row1["the_tracking_id"]) : "";
    $qty = $row1["QTY"] ? trim($row1["QTY"]) : "";
    $remarks = $row1["remarks"] ? trim($row1["remarks"]) : "";
    $prod_name= $row1["prod_display_name"] ? trim($row1["prod_display_name"]) : "";
    $dns_prod_code= $row1["dns_prod_code"] ? trim($row1["dns_prod_code"]) : "";

    $sql2="select * from $customer_master where `customer_code`='$customer_code'";
    // echo $sql2;
    $res2=mysql_query($sql2);
    $customer=mysql_fetch_array($res2);
    $rds_tag=$customer['rds_tag'];
    $branch_code=$customer['branch_code'];

    $sql3="select `branch_name` from $branch_master where `branch_code`='$branch_code'";
    // echo $sql3;
    $res3=mysql_query($sql3);
    $branch=mysql_fetch_array($res3);
    $branch_name=$branch['branch_name'];

    

    $sql5="select * from $customer_master where `customer_code`='$rds_tag'";
    // echo $sql3;
    $res5=mysql_query($sql5);
    $all_result2=mysql_fetch_array($res5);
    $dealer_code=$all_result2['customer_id'];
    $dealer_name=$all_result2['customer_name'];
	
if($order_date_time_ts>$new_price_format_datetime_ts)	{
$price_per_piece = $row1["prod_rate"] ? trim($row1["prod_rate"]) : 0;
$gst_rate = $row1["gst_rate"] ? trim($row1["gst_rate"]) : 0;
$gst_amount = $row1["gst_amount"] ? trim($row1["gst_amount"]) : 0;
$net_amount = $row1["prod_amount"] ? trim($row1["prod_amount"]) : 0;
$total_amount = $row1["prod_total_amount"] ? trim($row1["prod_total_amount"]) : 0;

}else{
$sql4="select * from $pop_product_master where `dns_prod_code`='$dns_prod_code'";
$res4=mysql_query($sql4);
$all_result=mysql_fetch_array($res4);
$price_per_piece=$all_result['price_per_piece'];
$gst_rate=$all_result['GST_rate'];
$gst_amount=$qty*$all_result['price_per_piece']*$all_result['GST_rate']/100;
// echo $gst_amount."<br/>";
$total_amount=($qty*$all_result['price_per_piece'])+$gst_amount;
// echo $total_amount."<br/>";
$net_amount=$qty*$all_result['price_per_piece'];	
	
}
	
	
	
?>
<tr>
<td><?php echo $order_date; ?></td>
<td><?php
if($main_order_id!=""){
echo "Main&nbsp;Order&nbsp;ID:<br>".$main_order_id;
}
echo "<br>Payment&nbsp;Status:<br>".$status;
if($the_tracking_id!=""){
echo "<br>TransactionID:<br>".$the_tracking_id;
}
?></td>
<td><?php echo $order_id; ?></td>
<td><?php echo $real_customer_code; ?></td>
<td><?php echo $real_customer_name; ?></td>
<td><?php echo $dealer_code; ?></td>
<td><?php echo $dealer_name; ?></td>
<td><?php echo $branch['branch_name']; ?></td>
<td><?php echo $prod_name; ?></td>
<td><?php echo $qty; ?></td>
<td><?php echo $price_per_piece; ?></td>
<td><?php echo $gst_rate; ?></td>
<td><?php echo $net_amount; ?></td>
<td><?php echo $gst_amount; ?></td>
<td><?php echo $total_amount; ?></td>
<td><?php echo $address; ?></td>
<td><?php echo $pincode; ?></td>
<td><?php echo $remarks; ?></td>
<td><?php echo $printed_address_pin; ?></td>
<td><?php echo $contact_num_printed; ?></td>

<?php
    // echo $real_customer_code."<br/>";
    // echo $real_customer_name."<br/>";
    $insert_query="insert into $pop_order(`date_and_time`,`formatted_date`,`order_id`,`customer_code`,`customer_name`,`linked_dealer_code`,`linked_dealer_name`,`branch`,`product_name`,`qty`,`price_per_pcs`,`gst_percent`,`net_amount`,`gst_amount`,`total_amount`,`address`,`pin_code`,`remarks`,`printed_address_pin`,`contact_num_printed`) values('$order_date','$formatted_date','$order_id','$real_customer_code','$real_customer_name','$dealer_code','$dealer_name','$branch_name','$prod_name','$qty','$price_per_piece','$gst_rate','$net_amount','$gst_amount','$total_amount','$address','$pincode','$remarks','$printed_address_pin','$contact_num_printed');";
    // echo $insert_query;
    
    mysql_query($insert_query);

    // $upd_query="update $pop_order set `formatted_date`='$formatted_date'";
    // echo $upd_query;
    // mysql_query($upd_query);

?>

</tr>
<tr>
<?php }}else{ ?>
<td style="text-align:center" colspan="19">No data found.</td>
</tr>
<?php
}
?>
</tbody>
</table>
</div>
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

});
</script>

<!-- Display your data table as before -->

<!-- Add Pagination Buttons -->
<div class="pagination">
    <?php if ($currentPage > 1) : ?>
        <a href="?page=<?php echo $currentPage - 1; ?>" class="btn btn-primary">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
        <a href="?page=<?php echo $i; ?>" <?php echo ($i == $currentPage) ? 'class="btn btn-primary active"' : 'class="btn btn-primary"'; ?>><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($currentPage < $totalPages) : ?>
        <a href="?page=<?php echo $currentPage + 1; ?>" class="btn btn-primary">Next</a>
    <?php endif; ?>
</div>

<?php
include "web_footer.php";
mysql_close();
?>

<script>
    function edit_bags(x){
        var elementId = "editButton_" + x;
        var buttonId="buttonInput_"+x;
        var newInputId="newInput_"+x;

        var element = document.getElementById(elementId);
        var buttonInput=document.getElementById(buttonId);
        var bags=document.getElementById(newInputId);
        // console.log(element);
        if(element){
            var newInputText=x;
            bags.style.display='inline-block';
            document.getElementById(x).style.display='none';
            element.style.display='none';
            buttonInput.style.display='inline-block';
    }
}
</script>

<script>
    function update_bags(x){
    
        var newInputText = "newInput_"+x;
        var elem=document.getElementById(newInputText);
        var inputValue = elem.value;
        console.log(x);
        console.log(inputValue);
        var elementId = "editButton_" + x;
        var buttonId="buttonInput_"+x;

        $.ajax({
        type: "GET",
        url: "lifting_edit_data.php",
        data: {
            inputValueText: inputValue,
            id: x
        },
        success: function(response) {
            
            document.getElementById(x).innerHTML=response;
            document.getElementById(elementId).style.display='inline-block';
            document.getElementById(buttonId).style.display='none';
            document.getElementById(x).style.display='inline-block';
            document.getElementById(newInputText).style.display='none';
        },
        error: function(xhr, textStatus, errorThrown) {
            console.error(textStatus);
        }
    });

    }
</script>

<script>
    function alpha_func(){
        
        var branchCode=document.getElementById('sl_branch').value;
        var custId=document.getElementById('srch_cust_dtls').value;
        var startDate=document.getElementById('start_date').value;
        var endDate=document.getElementById('end_date').value;

        $.ajax({
            url: 'pop_order_report_filter.php',
            type: 'POST',
            data: { branchCode: branchCode },
            dataType: 'json',
            success: function(data) {
                
                var table = document.getElementById('data_table');
                var tableBody = table.getElementsByTagName('tbody')[0];
                
                tableBody.innerHTML='';

                for (var i = 0; i < data.length; i++) {
                var row = tableBody.insertRow(i);
                
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                var cell7 = row.insertCell(6);
                var cell8 = row.insertCell(7);
                var cell9 = row.insertCell(8);
                var cell10 = row.insertCell(9);
                var cell11 = row.insertCell(10);
                var cell12 = row.insertCell(11);
                var cell13 = row.insertCell(12);
                var cell14 = row.insertCell(13);
                var cell15 = row.insertCell(14);
                var cell16 = row.insertCell(15);
                var cell17 = row.insertCell(16);
                var cell18 = row.insertCell(17);
                var cell19 = row.insertCell(18);
                var cell20 = row.insertCell(19);
                var cell21 = row.insertCell(20);
                
                cell1.textContent = data[i].date_and_time;
                cell2.textContent = data[i].main_order_details;
                cell3.textContent = data[i].order_id;
                cell4.textContent = data[i].customer_code;
                cell5.textContent = data[i].customer_name;
                cell6.textContent = data[i].linked_dealer_code;
                cell7.textContent = data[i].linked_dealer_name;
                cell8.textContent = data[i].branch;
                cell9.textContent = data[i].product_name;
                cell10.textContent = data[i].qty;
                cell11.textContent = data[i].price_per_pcs;
                cell12.textContent = data[i].gst_percent;
                cell13.textContent = data[i].net_amount;
                cell14.textContent = data[i].gst_amount;
                cell15.textContent = data[i].total_amount;
                cell16.textContent = data[i].address;
                cell17.textContent = data[i].pin_code;
                cell18.textContent = data[i].remarks;
                cell19.textContent = data[i].printed_address_pin;
                cell20.textContent = data[i].contact_num_printed;
                
            }
            }
        });

        $.ajax({
            url: 'pop_order_customer_filter.php',
            type: 'POST',
            data: { custId: custId },
            dataType: 'json',
            success: function(data1) {
                
                var table = document.getElementById('data_table');
                var tableBody = table.getElementsByTagName('tbody')[0];
                
                tableBody.innerHTML='';

                for (var i = 0; i < data1.length; i++) {
                var row = tableBody.insertRow(i);
                
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                var cell7 = row.insertCell(6);
                var cell8 = row.insertCell(7);
                var cell9 = row.insertCell(8);
                var cell10 = row.insertCell(9);
                var cell11 = row.insertCell(10);
                var cell12 = row.insertCell(11);
                var cell13 = row.insertCell(12);
                var cell14 = row.insertCell(13);
                var cell15 = row.insertCell(14);
                var cell16 = row.insertCell(15);
                var cell17 = row.insertCell(16);
                
                cell1.textContent = data1[i].date_and_time;
                cell2.textContent = data1[i].order_id;
                cell3.textContent = data1[i].customer_code;
                cell4.textContent = data1[i].customer_name;
                cell5.textContent = data1[i].linked_dealer_code;
                cell6.textContent = data1[i].linked_dealer_name;
                cell7.textContent = data1[i].branch;
                cell8.textContent = data1[i].product_name;
                cell9.textContent = data1[i].qty;
                cell10.textContent = data1[i].price_per_pcs;
                cell11.textContent = data1[i].gst_percent;
                cell12.textContent = data1[i].net_amount;
                cell13.textContent = data1[i].gst_amount;
                cell14.textContent = data1[i].total_amount;
                cell15.textContent = data1[i].address;
                cell16.textContent = data1[i].pin_code;
                cell17.textContent = data1[i].remarks;
                
            }
            }
        });


        $.ajax({
            url: 'pop_order_date_filter.php',
            type: 'POST',
            data: { 
                    startDate:startDate, 
                    endDate:endDate
                },
            dataType: 'json',
            success: function(data2) {
                
                var table = document.getElementById('data_table');
                var tableBody = table.getElementsByTagName('tbody')[0];
                
                tableBody.innerHTML='';

                for (var i = 0; i < data2.length; i++) {
                var row = tableBody.insertRow(i);
                
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                var cell7 = row.insertCell(6);
                var cell8 = row.insertCell(7);
                var cell9 = row.insertCell(8);
                var cell10 = row.insertCell(9);
                var cell11 = row.insertCell(10);
                var cell12 = row.insertCell(11);
                var cell13 = row.insertCell(12);
                var cell14 = row.insertCell(13);
                var cell15 = row.insertCell(14);
                var cell16 = row.insertCell(15);
                var cell17 = row.insertCell(16);
                var cell18 = row.insertCell(17);
                
                cell1.textContent = data2[i].date_and_time;
                cell2.textContent = data2[i].formatted_date;
                cell3.textContent = data2[i].order_id;
                cell4.textContent = data2[i].customer_code;
                cell5.textContent = data2[i].customer_name;
                cell6.textContent = data2[i].linked_dealer_code;
                cell7.textContent = data2[i].linked_dealer_name;
                cell8.textContent = data2[i].branch;
                cell9.textContent = data2[i].product_name;
                cell10.textContent = data2[i].qty;
                cell11.textContent = data2[i].price_per_pcs;
                cell12.textContent = data2[i].gst_percent;
                cell13.textContent = data2[i].net_amount;
                cell14.textContent = data2[i].gst_amount;
                cell15.textContent = data2[i].total_amount;
                cell16.textContent = data2[i].address;
                cell17.textContent = data2[i].pin_code;
                cell18.textContent = data2[i].remarks;
                
            }
            }
        });
    }
</script>

<script>
    function reset_func(){
        window.location.href =BASE_URL . "admin/pop_order_report.php";
    }
</script>
