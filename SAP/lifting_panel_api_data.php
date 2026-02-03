<?php
// ob_start();
// session_start();
error_reporting(E_ALL); 
ini_set('display_errors', '1');

header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST"); // Allow GET and POST methods
header("Access-Control-Allow-Headers: Content-Type"); // Allow the Content-Type header

include "star_connection.php";
// include "acedns_star_add_lifting.php";
// Check if the request method is POST

$lifting_table = "lifting";

$lifting_date_validation="lifting_date_validation";

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve the data using $_REQUEST
    $zone = $_REQUEST['zone'];
    $state = $_REQUEST['state'];
    $branch = $_REQUEST['branch'];
    $start_date = $_REQUEST['start_date'];
    // echo '$start_date= '.$start_date;
    // $start_date=strtotime($start_date);
    $end_date = $_REQUEST['end_date'];
    // echo '$end_date= '.$end_date;
    // $end_date=strtotime($end_date);
    $validn_date = $_REQUEST['validn_date'];
    // echo '$validn_date= '.$validn_date;
    // $validn_date=strtotime($validn_date);
    $submit_date_time = date("Y-m-d H:i:s");
    $lifting_date='';
    $msg='';
    $continue='yes';
    // $msg='';
    // $date='';

    // $data = array(
    //     'zone' => $zone,
    //     'state' => $state,
    //     'branch' => $branch,
    //     'start_date' => $start_date,
    //     'end_date' => $end_date,
    //     'validn_date' => $validn_date,
    //     'continue' => $continue
    // );

    $sql="INSERT INTO $lifting_date_validation (`zone`,`state`,`branch`,`validation_from`,`validation_to`,`validation_last_date`,`validation_create_date`) VALUES($zone,$state,$branch,'$start_date','$end_date','$validn_date','$submit_date_time')";

    echo $sql;

    $query=mysql_query($sql);

    if($query){

    }else{
        echo "Error in Insertion";
    }

    $sql1="SELECT `date_of_lifting` FROM $lifting_table ORDER BY `submit_date_time` DESC LIMIT 1";

    $query1=mysql_query($sql1);

    $result1=mysql_fetch_array($query1);

    $lifting_date=$result1['date_of_lifting'];

    // $date = DateTime::createFromFormat('d-m-Y', $lifting_date);

    // // Convert the date to the desired format "D-M-Y"
    // $lifting_date = $date->format('d-m-Y');
    
    // echo '$lifting_date= '.$lifting_date;

    $lifting_date=strtotime($lifting_date);
    
    if($lifting_date==NULL || $lifting_date==''){

        $msg='You can proceed.';
        $continue='yes';

    }else if($lifting_date > $start_date && $lifting_date < $end_date){
        
        $msg='You can proceed.';
        $continue='yes';

    }else if($lifting_date < $start_date || $lifting_date > $end_date){
        
        $msg='Sorry! Invalid challan date. Please contact admin.';
        $continue='no';

        $sql2="UPDATE $lifting_table SET `date_of_lifting`= NULL ORDER BY `submit_date_time` DESC LIMIT 1";

        $result2=mysql_query($sql2);

    }
    
    $data = array(
        'zone' => $zone,
        'state' => $state,
        'branch' => $branch,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'validn_date' => $validn_date,
        'msg' => $msg,
        'continue' => $continue
    );
    
    // Set JSON response headers
    header('Content-Type: application/json');
    
    // Convert the data array to JSON format
    $jsonData = json_encode($data);
    
    // Print the JSON data
    echo $jsonData;
} else {
    // Handle invalid request method
    http_response_code(405); // Method Not Allowed
    echo json_encode(array('error' => 'Invalid request method.'));
}
?>
