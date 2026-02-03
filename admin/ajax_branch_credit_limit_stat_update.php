<?php
include "star_connection.php";
$branchcode = $_REQUEST["branchcode"] ? addslashes(trim($_REQUEST["branchcode"])) : "";
if($branchcode!=""){
	$selcreditlimitstat="SELECT credit_limit_status FROM branch_credit_limit_status WHERE branch_code='".$branchcode."'";
	$rscreditlimitstat=mysql_query($selcreditlimitstat);
	$rowcreditlimitstat=mysql_fetch_array($rscreditlimitstat);
	$credit_limit_status=$rowcreditlimitstat['credit_limit_status'];
	if($credit_limit_status=='Y')
	{
		$credit_limit_status_changed='N';
		$sql="update branch_credit_limit_status set credit_limit_status='".$credit_limit_status_changed."',download_time=CURRENT_TIMESTAMP() where branch_code='".$branchcode."'";
	}
	if($credit_limit_status=='N')
	{
		$credit_limit_status_changed='Y';
		$sql="update branch_credit_limit_status set credit_limit_status='".$credit_limit_status_changed."',download_time=CURRENT_TIMESTAMP() where branch_code='".$branchcode."'";
	}
	
	$res=mysql_query($sql);
$res_data = array("credit_limit_status"=>$credit_limit_status_changed,"process_status"=>"YES","process_message"=>"Branch credit limit status updated successfully.");
}else{	
	$res_data = array("credit_limit_status"=>'',"process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}	
echo json_encode($res_data);
mysql_close();
?>