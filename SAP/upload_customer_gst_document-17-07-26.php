<?php

date_default_timezone_set("Asia/Kolkata");

include_once("star_connection.php");
//include_once("s3_config.php"); // AWS S3 Config

header('Content-Type: application/json');

if(
    empty($_POST['customer_id']) ||
    empty($_POST['gst_no']) ||
    !isset($_FILES['upload_doc'])
){
    echo json_encode(array(
        "status"=>"NO",
        "message"=>"Required fields missing."
    ));
    exit;
}

$customer_id=mysql_real_escape_string($_POST['customer_id']);
$gst_no=mysql_real_escape_string($_POST['gst_no']);

if($_FILES['upload_doc']['error']!=0){

    echo json_encode(array(
        "status"=>"NO",
        "message"=>"File upload failed."
    ));
    exit;

}

$file=$_FILES['upload_doc'];

$extension=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));

$file_name="GST_DOC_".$customer_id."_".time().".".$extension;

$temp_file=$file['tmp_name'];
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

$insert="
INSERT INTO customer_gst_documents
(
customer_id,
gst_no,
upload_doc,
created_date
)
VALUES
(
'$customer_id',
'$gst_no',
'$file_url',
NOW()
)
";

if(mysql_query($insert))
{

    echo json_encode(array(
        "status"=>"YES",
        "message"=>"Document uploaded successfully.",
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