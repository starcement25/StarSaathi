<?php
date_default_timezone_set("Asia/Kolkata");
header("Content-Type: application/json");

$data = array(
    array("start_day" => 0,  "end_day" => 3),
    array("start_day" => 4,  "end_day" => 10),
    array("start_day" => 11, "end_day" => 15),
    array("start_day" => 16, "end_day" => 25),
    array("start_day" => 26, "end_day" => 30),
    array("start_day" => 30, "end_day" => 0)
);

echo json_encode(array("data" => $data));
?>
