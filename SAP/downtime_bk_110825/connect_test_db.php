<?php
	set_time_limit(0);
	ini_set('memory_limit', '-1');

	/*define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaathi_STARS");
		
	$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die(mysql_error()."Database Connection Error.");
	mysql_select_db(DBREMOTE,$link) or die(mysql_error()."could not connect the database");*/

    //mysql_query($sqlcustomerSAP);

 
$servername = "103.87.174.95";
$username = "starsaat_dnsprod";
$password = "dnsprod1234#";
$dbname = "starsaathi_STARS";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to show tables
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h3>Tables in '$dbname':</h3><ul>";
    while($row = $result->fetch_row()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "No tables found.";
}

$conn->close();
?>
