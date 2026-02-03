<?php
ob_start();
session_start();
include "web_header.php";
include "web_check.php";
include "star_connection.php";
$branch_master="branch_master";
$lifting = "lifting";
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

$pgsql = "select `lid` from $lifting where `status`='APPROVED'";
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
                          <h2>Lifting Edit (<?php echo $total_pgres;?>)&nbsp;&nbsp;

                          </h2>
                            
<span style="clear:both;display:block;"></span>
                        </div>
                        <div class="body">

                        <div class="card">
                <div class="row">
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
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

                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <input type="text" class="form-control" id="srch_linked_dealer" style="width:100%;" value="<?php echo $srch_linked_dealer;?>" placeholder="Search Linked Dealer Name" title="Search Linked Dealer Name">
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <input type="text" class="form-control" id="srch_sub_dealer" value="<?php echo $srch_sub_dealer;?>" placeholder="Search Sub Dealer / RSSD Name" title="Search Sub Dealer / RSSD Name">
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <?php 
                            $months = array(
                                'January', 'February', 'March', 'April', 'May', 'June',
                                'July', 'August', 'September', 'October', 'November', 'December'
                            ); ?>

                            <select name="month[]" id="month" class="form-control">
                            <option value="">Select Month</option>
                            <?php 
                            // Loop through the array to generate options
                            foreach ($months as $month) { ?>
                                <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                            <?php } ?>

                            </select>
                        
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <button type="button" class="btn bg-red waves-effect srch_btn" onclick="alpha_func()">Search</button>
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <button type="button" class="btn bg-red waves-effect srch_reset_btn" onclick="reset_func()">Reset</button>
                    </div>
                </div>
            </div>


                            </div>

<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
 <div class="table-responsive">
<table id="data_table" class="table table-bordered table-striped table-hover">
<thead>
<tr>
<th>Linked&nbsp;Dealer&nbsp;Code</th>
<th>Linked&nbsp;Dealer&nbsp;SAP&nbsp;Code</th>
<th>Linked&nbsp;Dealer&nbsp;Name</th>
<th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;Code</th>
<th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;SAP&nbsp;Code</th>
<th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;Name</th>
<th>Branch</th>
<th>Month</th>
<th>Product&nbsp;Name</th>
<th>Total(Bags)</th>
<th>Date&nbsp;of&nbsp;Lifting</th>
<th>Challan&nbsp;No.</th>
<th>Submit&nbsp;Date&nbsp;Time</th>
<th>Status&nbsp;(Approved&nbsp;/&nbsp;Pending&nbsp;/&nbsp;Rejected)</th>
<th>Approve/Rejection&nbsp;Date&nbsp;Time</th>
<th>Reason&nbsp;for&nbsp;Rejection</th>
<th>Total&nbsp;Subdealer&nbsp;/&nbsp;RSSD&nbsp;Sale</th>
<th>Total&nbsp;Dealer&nbsp;Sale</th>
<th>Subdealer&nbsp;/&nbsp;RSSD&nbsp;Sale&nbsp;(%)</th>
</tr>
</thead>
<tfoot>
<tr>
<th>Linked&nbsp;Dealer&nbsp;Code</th>
<th>Linked&nbsp;Dealer&nbsp;SAP&nbsp;Code</th>
<th>Linked&nbsp;Dealer&nbsp;Name</th>
<th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;Code</th>
<th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;SAP&nbsp;Code</th>
<th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;Name</th>
<th>Branch</th>
<th>Month</th>
<th>Product&nbsp;Name</th>
<th>Total(Bags)</th>
<th>Date&nbsp;of&nbsp;Lifting</th>
<th>Challan&nbsp;No.</th>
<th>Submit&nbsp;Date&nbsp;Time</th>
<th>Status&nbsp;(Approved&nbsp;/&nbsp;Pending&nbsp;/&nbsp;Rejected)</th>
<th>Approve/Rejection&nbsp;Date&nbsp;Time</th>
<th>Reason&nbsp;for&nbsp;Rejection</th>
<th>Total&nbsp;Subdealer&nbsp;/&nbsp;RSSD&nbsp;Sale</th>
<th>Total&nbsp;Dealer&nbsp;Sale</th>
<th>Subdealer&nbsp;/&nbsp;RSSD&nbsp;Sale&nbsp;(%)</th>
</tr>
</tfoot>
<tbody>
<?php
$sql1 = "select * from $lifting order by `lid` desc limit $start_from,$limit";
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
$month = date("M-y",strtotime($date_of_lifting));	
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
    function alpha_func(){

        var branchCode=document.getElementById('sl_branch').value;
        
        var linkedDealerName=document.getElementById('srch_linked_dealer').value;
        var subDealerName=document.getElementById('srch_sub_dealer').value;
        var monthData=document.getElementById('month').value;
        // var date_of_lifting=document.getElementById('date_of_lifting').value;
        // var endDate=document.getElementById('end_date').value;

        $.ajax({
        url: 'branch_data_api.php',
        type: 'POST',
        data: { branchCode: branchCode,
            linkedDealerName: linkedDealerName,
            subDealerName: subDealerName,
            monthData: monthData },
        dataType: 'json',
        success: function(data) {
            var table = document.getElementById('data_table');
            var tableBody = table.getElementsByTagName('tbody')[0];
            tableBody.innerHTML = ''; // Clear the table body

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

                cell1.textContent = data[i].linked_dealer_code;
                cell2.textContent = data[i].linked_dealer_sap_code;
                cell3.textContent = data[i].linked_dealer_name;
                cell4.textContent = data[i].sub_dealer_rssd_code;
                cell5.textContent = data[i].sub_dealer_rssd_sap_code;
                cell6.textContent = data[i].sub_dealer_rssd_name;
                cell7.textContent = data[i].branch;

                var inputDate = new Date(data[i].date_of_lifting);
                var formattedDate = inputDate.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });

                cell8.textContent = formattedDate;

                cell9.textContent = data[i].prod_display_name;
                console.log("test1");
                var spanElement = document.createElement('span');
                spanElement.id = data[i].lid; 
                spanElement.textContent=data[i].total_bags;
                var inputElement = document.createElement('input');
                console.log("test2");
                inputElement.type = 'number';
                inputElement.style.display = 'none';
                inputElement.value = data[i].total_bags;
                inputElement.id = 'newInput_' + data[i].lid;
                var lineBreak = document.createElement('br');
                console.log("test3");
                var lineBreak2 = document.createElement('br');

                // Create an "Edit" button
                var editButton = document.createElement('input');
                editButton.type = 'submit';
                editButton.className = 'btn bg-red waves-effe';
                editButton.value = 'Edit';
                console.log("test4");
                editButton.id = 'editButton_' + data[i].lid; // Set your unique ID here
                var data_lid=data[i].lid;
                console.log("test5");
                editButton.onclick = ()=>{
                    // console.log("Edit button clicked");
                    console.log("test6");
                    edit_bags(data_lid);
                };
                
                // Create another "Edit" button (hidden)
                var hiddenEditButton = document.createElement('input');
                hiddenEditButton.type = 'submit';
                hiddenEditButton.className = 'btn bg-red waves-effe';
                hiddenEditButton.value = 'Edit';
                console.log("test7");
                hiddenEditButton.style.display = 'none';
                hiddenEditButton.id = 'buttonInput_' + data_lid; // Set your unique ID here
                hiddenEditButton.onclick=()=>{
                // console.log("Hidden Edit button clicked");
                console.log("test8");
                    update_bags(data_lid) // Set your unique ID here
                };

                // Append all the elements to your table cell or container element
                cell10.appendChild(spanElement);
                cell10.appendChild(inputElement);
                cell10.appendChild(lineBreak);
                cell10.appendChild(lineBreak2);
                console.log("test9");
                cell10.appendChild(editButton);
                cell10.appendChild(hiddenEditButton);
                cell11.textContent = data[i].date_of_lifting;
                cell12.textContent = data[i].challan_no;
                cell13.textContent = data[i].submit_date_time;
                cell14.textContent = data[i].status;
                cell15.textContent = data[i].status_date_and_time;
                cell16.textContent = data[i].reason_for_rejection;
                cell17.textContent = data[i].total_subdealer_rssd_sale;
                cell18.textContent = data[i].total_dealer_sale;
                cell19.textContent = data[i].subdealer_rssd_sale_percent;
                
            }
            }
        });

        // $.ajax({
        // url: 'linked_dealer_data_api.php',
        // type: 'POST',
        // data: { linkedDealerName: linkedDealerName },
        // dataType: 'json',
        // success: function(data1) {
        //     var table = document.getElementById('data_table');
        //     var tableBody = table.getElementsByTagName('tbody')[0];
        //     tableBody.innerHTML = ''; // Clear the table body

        //     for (var i = 0; i < data1.length; i++) {
        //         var row = tableBody.insertRow(i);
                
        //         var cell1 = row.insertCell(0);
        //         var cell2 = row.insertCell(1);
        //         var cell3 = row.insertCell(2);
        //         var cell4 = row.insertCell(3);
        //         var cell5 = row.insertCell(4);
        //         var cell6 = row.insertCell(5);
        //         var cell7 = row.insertCell(6);
        //         var cell8 = row.insertCell(7);
        //         var cell9 = row.insertCell(8);
        //         var cell10 = row.insertCell(9);
        //         var cell11 = row.insertCell(10);
        //         var cell12 = row.insertCell(11);
        //         var cell13 = row.insertCell(12);
        //         var cell14 = row.insertCell(13);
        //         var cell15 = row.insertCell(14);
        //         var cell16 = row.insertCell(15);
        //         var cell17 = row.insertCell(16);
        //         var cell18 = row.insertCell(17);
        //         var cell19 = row.insertCell(18);
                
        //         cell1.textContent = data1[i].linked_dealer_code;
        //         cell2.textContent = data1[i].linked_dealer_sap_code;
        //         cell3.textContent = data1[i].linked_dealer_name;
        //         cell4.textContent = data1[i].sub_dealer_rssd_code;
        //         cell5.textContent = data1[i].sub_dealer_rssd_sap_code;
        //         cell6.textContent = data1[i].sub_dealer_rssd_name;
        //         cell7.textContent = data1[i].branch;
        //         cell8.textContent = data1[i].month;
        //         cell9.textContent = data1[i].prod_display_name;
        //         cell10.textContent = data1[i].total_bags;
        //         cell11.textContent = data1[i].date_of_lifting;
        //         cell12.textContent = data1[i].challan_no;
        //         cell13.textContent = data1[i].submit_date_time;
        //         cell14.textContent = data1[i].status;
        //         cell15.textContent = data1[i].status_date_and_time;
        //         cell16.textContent = data1[i].reason_for_rejection;
        //         cell17.textContent = data1[i].total_subdealer_rssd_sale;
        //         cell18.textContent = data1[i].total_dealer_sale;
        //         cell19.textContent = data1[i].subdealer_rssd_sale_percent;
                
        //     }
        //     }
        // });

        // $.ajax({
        // url: 'sub_dealer_rssd_data_api.php',
        // type: 'POST',
        // data: { subDealerName: subDealerName },
        // dataType: 'json',
        // success: function(data2) {
        //     var table = document.getElementById('data_table');
        //     var tableBody = table.getElementsByTagName('tbody')[0];
        //     tableBody.innerHTML = ''; // Clear the table body

        //     for (var i = 0; i < data2.length; i++) {
        //         var row = tableBody.insertRow(i);
                
        //         var cell1 = row.insertCell(0);
        //         var cell2 = row.insertCell(1);
        //         var cell3 = row.insertCell(2);
        //         var cell4 = row.insertCell(3);
        //         var cell5 = row.insertCell(4);
        //         var cell6 = row.insertCell(5);
        //         var cell7 = row.insertCell(6);
        //         var cell8 = row.insertCell(7);
        //         var cell9 = row.insertCell(8);
        //         var cell10 = row.insertCell(9);
        //         var cell11 = row.insertCell(10);
        //         var cell12 = row.insertCell(11);
        //         var cell13 = row.insertCell(12);
        //         var cell14 = row.insertCell(13);
        //         var cell15 = row.insertCell(14);
        //         var cell16 = row.insertCell(15);
        //         var cell17 = row.insertCell(16);
        //         var cell18 = row.insertCell(17);
        //         var cell19 = row.insertCell(18);
                
        //         cell1.textContent = data2[i].linked_dealer_code;
        //         cell2.textContent = data2[i].linked_dealer_sap_code;
        //         cell3.textContent = data2[i].linked_dealer_name;
        //         cell4.textContent = data2[i].sub_dealer_rssd_code;
        //         cell5.textContent = data2[i].sub_dealer_rssd_sap_code;
        //         cell6.textContent = data2[i].sub_dealer_rssd_name;
        //         cell7.textContent = data2[i].branch;
        //         cell8.textContent = data2[i].month;
        //         cell9.textContent = data2[i].prod_display_name;
        //         cell10.textContent = data2[i].total_bags;
        //         cell11.textContent = data2[i].date_of_lifting;
        //         cell12.textContent = data2[i].challan_no;
        //         cell13.textContent = data2[i].submit_date_time;
        //         cell14.textContent = data2[i].status;
        //         cell15.textContent = data2[i].status_date_and_time;
        //         cell16.textContent = data2[i].reason_for_rejection;
        //         cell17.textContent = data2[i].total_subdealer_rssd_sale;
        //         cell18.textContent = data2[i].total_dealer_sale;
        //         cell19.textContent = data2[i].subdealer_rssd_sale_percent;
                
        //     }
        //     }
        // });

        // $.ajax({
        // url: 'month_data_api.php',
        // type: 'POST',
        // data: { monthData: monthData },
        // dataType: 'json',
        // success: function(data3) {
        //     var table = document.getElementById('data_table');
        //     var tableBody = table.getElementsByTagName('tbody')[0];
        //     tableBody.innerHTML = ''; // Clear the table body

        //     for (var i = 0; i < data3.length; i++) {
        //         var row = tableBody.insertRow(i);
                
        //         var cell1 = row.insertCell(0);
        //         var cell2 = row.insertCell(1);
        //         var cell3 = row.insertCell(2);
        //         var cell4 = row.insertCell(3);
        //         var cell5 = row.insertCell(4);
        //         var cell6 = row.insertCell(5);
        //         var cell7 = row.insertCell(6);
        //         var cell8 = row.insertCell(7);
        //         var cell9 = row.insertCell(8);
        //         var cell10 = row.insertCell(9);
        //         var cell11 = row.insertCell(10);
        //         var cell12 = row.insertCell(11);
        //         var cell13 = row.insertCell(12);
        //         var cell14 = row.insertCell(13);
        //         var cell15 = row.insertCell(14);
        //         var cell16 = row.insertCell(15);
        //         var cell17 = row.insertCell(16);
        //         var cell18 = row.insertCell(17);
        //         var cell19 = row.insertCell(18);
                
        //         cell1.textContent = data3[i].linked_dealer_code;
        //         cell2.textContent = data3[i].linked_dealer_sap_code;
        //         cell3.textContent = data3[i].linked_dealer_name;
        //         cell4.textContent = data3[i].sub_dealer_rssd_code;
        //         cell5.textContent = data3[i].sub_dealer_rssd_sap_code;
        //         cell6.textContent = data3[i].sub_dealer_rssd_name;
        //         cell7.textContent = data3[i].branch;
        //         cell8.textContent = data3[i].month;
        //         cell9.textContent = data3[i].prod_display_name;
        //         cell10.textContent = data3[i].total_bags;
        //         cell11.textContent = data3[i].date_of_lifting;
        //         cell12.textContent = data3[i].challan_no;
        //         cell13.textContent = data3[i].submit_date_time;
        //         cell14.textContent = data3[i].status;
        //         cell15.textContent = data3[i].status_date_and_time;
        //         cell16.textContent = data3[i].reason_for_rejection;
        //         cell17.textContent = data3[i].total_subdealer_rssd_sale;
        //         cell18.textContent = data3[i].total_dealer_sale;
        //         cell19.textContent = data3[i].subdealer_rssd_sale_percent;
                
        //     }
        //     }
        // });
    }
</script>

<script>
    jQuery('#branch').chosen({no_results_text:'Oops, no branch found!',search_contains: true,placeholder_text_single: 'Select Branches'});
</script>

<script>
    function edit_bags(x){
        console.log("test11");
        var elementId = "editButton_" + x;
        var buttonId="buttonInput_"+x;
        var newInputId="newInput_"+x;
        console.log("test12");
        var element = document.getElementById(elementId);
        var buttonInput=document.getElementById(buttonId);
        console.log("test13");
        var bags=document.getElementById(newInputId);
        // console.log(element);
        if(element){
            var newInputText=x;
            console.log("test14");
            bags.style.display='inline-block';
            document.getElementById(x).style.display='none';
            element.style.display='none';
            console.log("test15");
            buttonInput.style.display='inline-block';
    }
}
</script>

<script>
    function update_bags(x){
    
        var newInputText = "newInput_"+x;
        var elem=document.getElementById(newInputText);
        var inputValue = elem.value;
        // console.log(x);
        // console.log(inputValue);
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
    function reset_func(){
        window.location.href = BASE_URL . "admin/lifting_edit.php";
    }
</script>
