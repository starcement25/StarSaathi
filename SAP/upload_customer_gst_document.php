<?php

date_default_timezone_set("Asia/Kolkata");

include_once("star_connection.php");
//include_once("s3_config.php"); // AWS S3 Config

header('Content-Type: application/json');

if(
    empty($_POST['customer_id']) ||
    empty($_POST['gst_no'])
){
    echo json_encode(array(
        "status"=>"NO",
        "message"=>"Required fields missing."
    ));
    exit;
}

$customer_id   = mysql_real_escape_string($_POST['customer_id']);
$gst_no        = mysql_real_escape_string($_POST['gst_no']);
$is_any        = isset($_POST['is_any'])      ? mysql_real_escape_string(trim($_POST['is_any']))      : "";
$decleration   = isset($_POST['decleration']) ? mysql_real_escape_string(trim($_POST['decleration'])) : "";

// get cust_type from customer_master
$cust_type = "";
$sql_cust = "SELECT cust_type FROM customer_master WHERE customer_id='".$customer_id."' LIMIT 1";
$res_cust = mysql_query($sql_cust);

if(!$res_cust || mysql_num_rows($res_cust) == 0){
    echo json_encode(array(
        "status"=>"NO",
        "message"=>"Invalid customer id."
    ));
    exit;
}

$row_cust  = mysql_fetch_assoc($res_cust);
$cust_type = mysql_real_escape_string(trim($row_cust['cust_type']));

$file_url = "";

// upload_doc is OPTIONAL
if(isset($_FILES['upload_doc']) && $_FILES['upload_doc']['error'] == 0 && !empty($_FILES['upload_doc']['tmp_name'])){

    $file = $_FILES['upload_doc'];

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    $file_name = "GST_DOC_".$customer_id."_".time().".".$extension;

    $temp_file = $file['tmp_name'];
    /*
    try{

        $result=$s3->putObject(array(
            'Bucket'=>$bucket,
            'Key'=>"gst_documents/".$file_name,
            'SourceFile'=>$temp_file,
            'ACL'=>'public-read'
        ));

        $file_url=$result['ObjectURL'];

    }
    catch(Exception $e){

        echo json_encode(array(
            "status"=>"NO",
            "message"=>$e->getMessage()
        ));
        exit;

    }*/
}
else if(isset($_FILES['upload_doc']) && $_FILES['upload_doc']['error'] != 0 && $_FILES['upload_doc']['error'] != 4){
    echo json_encode(array(
        "status"=>"NO",
        "message"=>"File upload failed."
    ));
    exit;
}

$file_url = mysql_real_escape_string($file_url);

// check if entry already exists for this customer_id
$sql_check = "SELECT id, upload_doc FROM customer_gst_documents WHERE customer_id='".$customer_id."' LIMIT 1";
$res_check = mysql_query($sql_check);

if($res_check && mysql_num_rows($res_check) > 0)
{
    $row_check = mysql_fetch_assoc($res_check);
    $exist_id  = $row_check['id'];

    // keep old document if no new file was uploaded
    if($file_url == ""){
        $file_url = mysql_real_escape_string($row_check['upload_doc']);
    }

    $query = "
    UPDATE customer_gst_documents
    SET
        cust_type   = '$cust_type',
        gst_no      = '$gst_no',
        upload_doc  = '$file_url',
        is_any      = '$is_any',
        decleration = '$decleration',
        created_date = NOW()
    WHERE id = '".$exist_id."'
    ";

    $action_msg = "Document updated successfully.";
}
else
{
    $query = "
    INSERT INTO customer_gst_documents
    (
    customer_id,
    cust_type,
    gst_no,
    upload_doc,
    is_any,
    decleration,
    created_date
    )
    VALUES
    (
    '$customer_id',
    '$cust_type',
    '$gst_no',
    '$file_url',
    '$is_any',
    '$decleration',
    NOW()
    )
    ";

    $action_msg = "Document uploaded successfully.";
}

if(mysql_query($query))
{

    echo json_encode(array(
        "status"=>"YES",
        "message"=>$action_msg,
        "cust_type"=>$cust_type,
        "document_url"=>$file_url
    ));

}
else
{

    echo json_encode(array(
        "status"=>"NO",
        "message"=>mysql_error()
    ));

}

?>