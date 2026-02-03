<?php
include "star_connection.php";
$destination_master = "destination_master";
$branch_destination_freight = "branch_destination_freight";
$res_msg = array();
$destination_options = '<option value="">Select Destination</option>';
$trn_branch_id = $_POST["trn_branch_id"] ? trim($_POST["trn_branch_id"]) : "";
if($trn_branch_id!=''){
	$sql5 = "select $destination_master.`destination_code`,$destination_master.`destination_name` from $branch_destination_freight left join $destination_master on $branch_destination_freight.`destination_code`=$destination_master.`destination_code` where $branch_destination_freight.`branch_code`='$trn_branch_id' order by $destination_master.`destination_name` asc ";
	$res5 = mysql_query($sql5);
	$tot_res5 = mysql_num_rows($res5);
	if($tot_res5>0){
		while($row5 = mysql_fetch_assoc($res5)){
			$destination_code=$row5["destination_code"];
			$destination_name=$row5["destination_name"];
			$destination_options .= '<option value="'.$destination_code.'">'.$destination_name.'</option>';
		}
	}

$res_msg = array("process_sts"=>"YES","process_msg"=>"Success","destination_options"=>$destination_options);
}else{
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong.","destination_options"=>$destination_options);
}
echo json_encode($res_msg);
mysql_close();
?>