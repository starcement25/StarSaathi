<?php
include "star_connection.php";
$validation_date = $_POST['validn_date'];
$approval_date = $_POST['apprvl_date'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$brancharray = array();
$res_msg = array();
$brancharray = $_POST['branch'];
//rancharray=array('B0050','B0060');
foreach ($brancharray as $branchval) {
	$sql = "SELECT branch FROM  lifting_date_validation  WHERE branch='" . $branchval . "'";

	$rsquery = mysql_query($sql);
	$countquery = mysql_num_rows($rsquery);
	if ($countquery > 0) {
		$sqlup = "UPDATE lifting_date_validation SET validation_from='" . $start_date . "',validation_to='" . $end_date . "',
				validation_last_date='" . $validation_date . "',approval_last_date='" . $approval_date . "',validation_create_date=CURRENT_TIMESTAMP() WHERE branch='" . $branchval . "'";
		mysql_query($sqlup);
	} else {
		echo $sqlinsert = "INSERT INTO lifting_date_validation SET validation_from='" . $start_date . "',validation_to='" . $end_date . "',validation_last_date='" . $validation_date . "',approval_last_date='" . $approval_date . "',
			validation_create_date=CURRENT_TIMESTAMP(),branch='" . $branchval . "',
					ip_address='" . $_SERVER['REMOTE_ADDR'] . "'";
		mysql_query($sqlinsert);
	}
}
//echo "<b>Lifting date validation updated successfully.</b>";

$res_msg = array("process_sts" => "YES", "process_msg" => "Lifting date validation updated successfully.");
//sql_close();
echo json_encode($res_msg);
exit();
?>