<?php
include "web_header.php";
include "web_check.php";
include "star_connection.php";

$pop_product_master="pop_product_master";
$customer_master="customer_master";
$branch_master="branch_master";
$t_order_pop="T_ORDER_POP";
$product_master="product_master";
$broker_master="broker_master";
$lifting="lifting";

$_session['textValues']=array();
$new_qry_string_filtered = "";
$export_filtered_str = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$whr_str = "";

$add_page_name = "lifting_report.php";
$page_name = "lifting_report.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select `id` from $pop_order";
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
                          <h2>POP Order&nbsp;&nbsp;

                          </h2><br><br>
                            
<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_branch">
<option value="">Select Branch</option>

<?php

$sql3 = "select `branch_code`,`branch_name` from $branch_master where `acedns`='Y' order by `branch_name`";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);

if($totres3>0){
	while($row3=mysql_fetch_assoc($res3)){
		$the_branch_code = $row3["branch_code"];
		$the_branch_name = $row3["branch_name"];
		?>
 <option value="<?php echo $the_branch_code;?>" selected><?php echo $the_branch_name;?></option>
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
    <input type="date" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" />
</div>

<div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
    <input type="date" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" />
</div>

    <div class="col-lg-1 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
</div>
<div class="col-lg-1 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" >Reset</button>
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
 <div class="table-responsive">
<table class="table table-bordered table-striped table-hover">
<thead>
<tr>
<th>DATE&nbsp;&&nbsp;TIME</th>
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
</tr>
</thead>
<tfoot>
<tr>
<th>DATE&nbsp;&&nbsp;TIME</th>
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
</tr>
</tfoot>
<tbody>
<?php

$sql1 = "select * from $t_order_pop order by `order_date` desc";

$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
    while($row1=mysql_fetch_array($res1)){
    $order_id = $row1["APPORDERNO"] ? trim($row1["APPORDERNO"]) : "";
    $order_date = $row1["order_date"] ? trim($row1["order_date"]) : "";
    $customer_code = $row1["customer_code"] ? trim($row1["customer_code"]) : "";
    $branch_code = $row1["branch_code"] ? trim($row1["branch_code"]) : "";
    $address = $row1["address"] ? trim($row1["address"]) : "";
    $pincode = $row1["pin"] ? trim($row1["pin"]) : "";
    $status = $row1["status"] ? trim($row1["status"]) : "";
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

    $sql4="select * from $pop_product_master where `dns_prod_code`='$dns_prod_code'";
    // echo $sql3;
    $res4=mysql_query($sql4);
    $all_result=mysql_fetch_array($res4);

    $sql5="select * from $customer_master where `customer_code`='$rds_tag'";
    // echo $sql3;
    $res5=mysql_query($sql5);
    $all_result2=mysql_fetch_array($res5);

?>
<tr>
<td><?php echo $order_date; ?></td>
<td><?php echo $order_id; ?></td>
<td><?php echo $customer_code; ?></td>
<td><?php echo $customer['customer_name']; ?></td>
<td><?php echo $customer['rds_tag']; ?></td>
<td><?php echo $all_result2['customer_name']; ?></td>
<td><?php echo $branch['branch_name']; ?></td>
<td><?php echo $prod_name; ?></td>
<td><?php echo $qty; ?></td>
<td><?php echo $all_result['price_per_piece']; ?></td>
<td><?php echo $all_result['GST_rate']; ?></td>
<?php 
    $gst_amount=$qty*$all_result['price_per_piece']*$all_result['GST_rate']/100;
    // echo $gst_amount."<br/>";
    $total_amount=($qty*$all_result['price_per_piece'])+$gst_amount;
    // echo $total_amount."<br/>";
    $net_amount=$qty*$all_result['price_per_piece'];
?>
<td><?php echo $net_amount; ?></td>
<td><?php echo $gst_amount; ?></td>
<td><?php echo $total_amount; ?></td>
<td><?php echo $address; ?></td>
<td><?php echo $pincode; ?></td>
<td><?php echo $remarks; ?></td>

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

});
</script>
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
