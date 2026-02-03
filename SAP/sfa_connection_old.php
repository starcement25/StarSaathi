<?php
// $host = "3.7.158.23";
// $port = 3306;
// $user = "sfa";
// $pass = "Passw0rd123#$";
// $db = "acedns_STAR";

// $link = mysqli_connect($host, $user, $pass, $db, $port);

// if (!$link) {
//     die("Connection failed: " . mysqli_connect_error());
// }

// mysqli_set_charset($link, 'utf8mb4');
// echo "✅ Connected successfully from server.";
?>

<?php
// $host = "3.7.158.23";
// $port = 3306;
// $user = "sfa";
// $pass = "Passw0rd123#$";
// $db = "acedns_STAR";

// $link = mysqli_init();

// if (!$link) {
//     die("MySQLi initialization failed");
// }

// if (!mysqli_real_connect($link, $host, $user, $pass, $db, $port)) {
//     die("Connection failed: " . mysqli_connect_error());
// }

// // Instead of mysqli_options, run this query AFTER connection
// if (!mysqli_query($link, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'")) {
//     die("Failed to set names: " . mysqli_error($link));
// }

// echo "Connected successfully from server.";
?>


<?php

$host = '3.7.158.23';
$user = 'sfa';
$password = 'Passw0rd123#$';
$db = 'acedns_STAR';

$mysqli = mysqli_init();

// 💡 Force charset BEFORE connecting
mysqli_options($mysqli, MYSQLI_SET_CHARSET_NAME, 'utf8');

// Connect
if (!$mysqli->real_connect($host, $user, $password, $db, 3306)) {
    die('Connection error: ' . mysqli_connect_error());
}

// Test query
$result = $mysqli->query("SELECT * FROM branch_dump");
while ($row = $result->fetch_assoc()) {
    print_r($row);
}

$mysqli->close();
?>


?>



