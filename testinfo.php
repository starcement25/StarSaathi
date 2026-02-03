<?php echo phpinfo();

//echo dirname(__FILE__);

define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_UCLINDIA");

$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
		mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");
		
$date=date('Y-m-d');
$time=date('H:i:s');
echo $contentsdatetime = $date.'€'.$time."\n";
		
?>