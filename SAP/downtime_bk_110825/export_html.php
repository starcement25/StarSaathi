<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");

session_start();
$_SESSION['attnum'] = 0;
   


?>
<html>
    
    <body>

<form method='post' action="export_csv_21.php">

<input type="text" name="page" value="<?php echo $_SESSION['attnum']; ?>">
<input name='add' type="submit" value='+'>
<!--<h3><em>:<?php echo $_SESSION['attnum']; ?>: </em></h3>-->
</form>
</body>
</html>







