<?php
include "star_connection.php";

// Assuming these are your include files and variables
$app_service_track_log = "app_service_track_log";
$customer_master = "customer_master";
$dealer_reward_status = "dealer_reward_status";

//print_r($_POST);die;
// Retrieving parameters from request
$emp_code = $_REQUEST['emp_code'];

$user_type = isset($_REQUEST['user_type']) ? strtolower(trim($_REQUEST['user_type'])) : ""; /* sub dealer/dealer/broker */

// Initializations
$condition_branch = "";
$customer_master = "customer_master";
$dealer_reward_status = "dealer_reward_status";
$res_data = array();

// Query to fetch customer details
$sqldealertour = "SELECT `customer_id`, `dns_customer_code` FROM `$customer_master` WHERE `customer_id`='" . $emp_code . "'";
$rsdealertour = mysql_query($sqldealertour);

if ($rsdealertour === false) {
    $res_data = array("process_status" => "NO", "process_message" => "Error fetching customer details: " . mysql_error());
    
} else {
    $rowdealertour = mysql_fetch_array($rsdealertour);
    if ($rowdealertour) {
        $dns_customer_code = $rowdealertour['dns_customer_code'];
        $customer_id = $rowdealertour['customer_id'];

        // Query to check reward status based on emp_code (dns_customer_code)
        //$sqlquery = "SELECT `dealer_status` FROM `$dealer_reward_status` WHERE `emp_code`='$customer_id'";
       /* $sqlquery = "SELECT `dealer_status` FROM `$dealer_reward_status` WHERE `emp_code`='1000004115'";
        $result = mysql_query($sqlquery);*/
        //sk add line start -17-04-26
        $url = "https://admin.starsaathirewards.com/check_reward_points?dealer_id=" . urlencode($customer_id);

        // Initialize CURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response = curl_exec($ch);
        $data = json_decode($response);
        //echo"<pre>";print_r($data->rwd_points);die;

        //echo $response['rwd_points']; die;
        if ($data->rwd_points == 'NO') {
            $res_data = array("process_status" => "NO", "process_message" => "Error checking dealer reward status: " . mysql_error()); 
        } else {
            $rowtour = mysql_fetch_array($result);
        //echo"<pre>";print_r($data->rwd_points);die;

            if ($data->rwd_points =='YES') {
                $rewardstatus = 'ACTIVE';
        //echo"<pre>";print_r($data->rwd_points);die;

                // Determine the reward link based on user_type and rewardstatus
                if ($rewardstatus == 'ACTIVE') {
                    if ($user_type == 'dealer') {
                        $reward_link = 'https://dealer.starsaathirewards.com/sap_code='.$customer_id.'/';
                    } elseif ($user_type == 'sub dealer') {
                        $reward_link = 'https://dealer.starsaathirewards.com/sap_code='.$customer_id.'/'; 
                    } elseif ($user_type == 'rssd'){
                        $reward_link = 'https://dealer.starsaathirewards.com/sap_code='.$customer_id.'/';
                    } elseif ($user_type == 'broker'){
                        $reward_link = 'https://dealer.starsaathirewards.com/sap_code='.$customer_id.'/';
                    } 
                    $reward_status_data = array("customer_code" => $customer_id, "reward_link" => $reward_link);
                    $res_data = array("process_status" => "YES", "process_message" => "Success.", "reward_data" => array($reward_status_data));
                } else {
                    //$res_data = array("process_status" => "NO", "process_message" => "Reward status not active. Coming Soon.");
                    $reward_status_data = array("customer_code" => $customer_id, "reward_link" => 'https://dealer.starsaathirewards.com/sap_code='.$customer_id.'/');
                    $res_data = array("process_status" => "NO", "process_message" => "Reward status not active. Coming Soon.", "reward_data" => array($reward_status_data));
                }
            } else {
                //$res_data = array("process_status" => "NO", "process_message" => "No reward status found for the dealer.");
                $reward_status_data = array("customer_code" => $customer_id, "reward_link" =>'https://dealer.starsaathirewards.com/sap_code='.$customer_id.'/');
                $res_data = array("process_status" => "NO", "process_message" => "No reward status found for the dealer.", "reward_data" => array($reward_status_data));
            }
        }
    } else {
        $res_data = array("process_status" => "NO", "process_message" => "Dealer not found.");
        
    }
    
}
 
// Output JSON response
echo json_encode($res_data);

// Close MySQL connection
mysql_close($link);
?>
