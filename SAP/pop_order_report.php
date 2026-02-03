<?php
ob_start();
session_start();
include "web_header.php";
include "web_check.php";
include "star_connection.php";

$pop_product_master="pop_product_master";
$customer_master="customer_master";
$branch_master="branch_master";
$T_ORDER_POP="T_ORDER_POP";
$product_master="product_master";
$broker_master="broker_master";
$lifting="lifting";

echo $_SESSION['the_customer_code'];

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
                          <h2>POP Order (<?php echo $total_pgres;?>)&nbsp;&nbsp;

                          </h2>
                            
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
<th>GST&nbsp;AMOUNT</th>
<th>TOTAL&nbsp;AMOUNT</th>
<th>ADDRESS</th>
<th>PIN&nbsp;CODE</th>
<th>REMARKS</th>
<th>EXPECTED&nbsp;DELIVERY&nbsp;DATE</th>
<th>DELIVERY&nbsp;DATE</th>
<th>STATUS&nbsp;(PENDING/DELIVERY)</th>
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
<th>GST&nbsp;AMOUNT</th>
<th>TOTAL&nbsp;AMOUNT</th>
<th>ADDRESS</th>
<th>PIN&nbsp;CODE</th>
<th>REMARKS</th>
<th>EXPECTED&nbsp;DELIVERY&nbsp;DATE</th>
<th>DELIVERY&nbsp;DATE</th>
<th>STATUS&nbsp;(PENDING/DELIVERY)</th>
</tr>
</tfoot>
<tbody>
<?php
$sql1 = "select * from $lifting order by `lid` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
$linked_dealer_code = $row1["linked_dealer_code"] ? trim($row1["linked_dealer_code"]) : "";
$linked_dealer_sap_code = $row1["linked_dealer_sap_code"] ? trim($row1["linked_dealer_sap_code"]) : "";
$linked_dealer_name = $row1["linked_dealer_name"] ? trim($row1["linked_dealer_name"]) : "";
$sub_dealer_rssd_code = $row1["sub_dealer_rssd_code"] ? trim($row1["sub_dealer_rssd_code"]) : "";
$sub_dealer_rssd_sap_code = $row1["sub_dealer_rssd_sap_code"] ? trim($row1["sub_dealer_rssd_sap_code"]) : "";
$sub_dealer_rssd_name = $row1["sub_dealer_rssd_name"] ? trim($row1["sub_dealer_rssd_name"]) : "";
$branch = $row1["branch"] ? trim($row1["branch"]) : "";

$prod_display_name = $row1["prod_display_name"] ? trim($row1["prod_display_name"]) : "";
$total_bags = $row1["total_bags"] ? trim($row1["total_bags"]) : "";
$date_of_lifting = $row1["date_of_lifting"] ? trim($row1["date_of_lifting"]) : "";
$challan_no = $row1["challan_no"] ? trim($row1["challan_no"]) : "";
$submit_date_time = $row1["submit_date_time"] ? trim($row1["submit_date_time"]) : "";
$status = $row1["status"] ? trim($row1["status"]) : "";
$status_date_and_time = $row1["status_date_and_time"] ? trim($row1["status_date_and_time"]) : "";
$reason_for_rejection = $row1["reason_for_rejection"] ? trim($row1["reason_for_rejection"]) : "";
$total_subdealer_rssd_sale = $row1["total_subdealer_rssd_sale"] ? trim($row1["total_subdealer_rssd_sale"]) : "";
$total_dealer_sale = $row1["total_dealer_sale"] ? trim($row1["total_dealer_sale"]) : "";
$subdealer_rssd_sale_percent = $row1["subdealer_rssd_sale_percent"] ? trim($row1["subdealer_rssd_sale_percent"]) : "";

//$month = $row1["month"] ? trim($row1["month"]) : "";
$month = "";
if($date_of_lifting!=""){
$month = date("M",strtotime($date_of_lifting));	
}

if($status=='APPROVED'){ ?>

<tr>
<td><?php echo $linked_dealer_code;?></td>
<td><?php echo $linked_dealer_sap_code;?></td>
<td><?php echo $linked_dealer_name;?></td>
<td><?php echo $sub_dealer_rssd_code;?></td>
<td><?php echo $sub_dealer_rssd_sap_code;?></td>
<td><?php echo $sub_dealer_rssd_name;?></td>
<td><?php echo $branch;?></td>
<td><?php echo $month;?></td>
<td><?php echo $prod_display_name;?></td>

<td><span id="<?php echo $row1['lid']; ?>"><?php echo $total_bags; ?></span><input type="number" id="newInput_<?php echo $row1['lid']; ?>" style="display:none;" value="<?php echo $total_bags; ?>"/><br/><br/>
<input type="submit" class="btn bg-red waves-effe" value="Edit" id="editButton_<?php echo $row1['lid'] ?>" onclick="edit_bags(<?php echo $row1['lid'] ?>)" />
<input type="submit" class="btn bg-red waves-effe" value="Edit" id="buttonInput_<?php echo $row1['lid'] ?>" style='display:none;' onclick="update_bags(<?php echo $row1['lid']; ?>)" />
<?php $_session['textValues'][]=$row1['lid']; ?>
</td>

<td><?php echo $date_of_lifting;?></td>
<td><?php echo $challan_no;?></td>
<td><?php echo $submit_date_time;?></td>
<td><?php echo $status;?></td>
<td><?php echo $status_date_and_time;?></td>
<td><?php echo $reason_for_rejection;?></td>
<td><?php echo $total_subdealer_rssd_sale;?></td>
<td><?php echo $total_dealer_sale;?></td>
<td><?php echo $subdealer_rssd_sale_percent;?></td>
</tr>

<?php }

}
}else{
?>
<tr>
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
