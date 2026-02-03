<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
if(isset($_SESSION["start_report_admin"])){
header("location:main.php");
}
include "star_connection.php";
$table_name = "startreport_admin";
$msg = "";
if(@$_POST["login"]=="SIGN IN"){
	$uname = $_POST["username"] ? addslashes(trim($_POST["username"])) : "";
	$password = $_POST["password"] ? addslashes(trim($_POST["password"])) : "";
	if($uname!="" && $password!=""){
		$sql1 = "select * from $table_name where `user_name`='".$uname."' and `password`='".$password."' and `status`='ACTIVE'";
		$res1 = mysql_query($sql1);
		$totres1 = mysql_num_rows($res1);
		if($totres1>0){
			$row1 = mysql_fetch_assoc($res1);
			$admin_id = $row1["id"];
			$admin_name = $row1["user_name"];
			$user_type = $row1["user_type"];
			$order_show_branch = $row1["order_show_branch"];
			$_SESSION["start_report_admin"]=$admin_id;
			$_SESSION["start_report_admin_name"]=$admin_name;
			$_SESSION["start_user_type"]=$user_type;
			$_SESSION["order_show_branch"]=$order_show_branch;
			header("location:main.php");		
		}else{
			$msg = "Wrong credential.";
		}		
	}else{
		$msg = "Please enter username and password.";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Sign In | Star Cement</title>
    <!-- Favicon-->
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">
<!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>

<?php
if($login_status=="YES"){
?>
 <script>
    jQuery(function(){
		window.location = "main.php";
	});
    </script>
<?php	
}
?>

    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="plugins/jquery-validation/jquery.validate.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>
    <script src="js/pages/examples/sign-in.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body class="login-page">
    <div class="login-box">
        <div class="card">
            <div class="body">
                <form id="sign_in" method="POST" action="">
                    <div class="msg"><img style="text-align:center;" src="images/logo.png"></div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="username" placeholder="Username" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-8 p-t-5">
                            <?php
                            if($msg!=""){
							echo $msg;	
							}
							?>
                        </div>
                        <div class="col-xs-4">
                            <button class="btn btn-block bg-black waves-effect" type="submit" value="SIGN IN" name="login">SIGN IN</button>
                        </div>
                    </div>
                    
                    
                </form>
            </div>
        </div>
    </div>

    
</body>

</html>
<?php
mysql_close();
?>