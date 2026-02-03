<?php
$file_name = basename($_SERVER['PHP_SELF']);
if($file_name==''){
	$file_name = "index.php";
}
$page_curr_title = "Star Cement Customer Portal";
/*$file_name_title_arr["star_make_order.php"] = "MAKE ORDER";
$file_name_title_arr["track_order.php"] = "TRACK ORDER";
$file_name_title_arr["performance.php"] = "PERFORMANCE";
$file_name_title_arr["show_nitifications.php"] = "NOTIFICATION";
$file_name_title_arr["ledger_balance.php"] = "LEDGER BALANCE";
$file_name_title_arr["show_confirm_ledger_balance_list.php"] = "CONFIRM LEDGER BALANCE";
$file_name_title_arr["show_scheme.php"] = "SCHEME";

if(array_key_exists($file_name,$file_name_title_arr)){
	$page_curr_title = $file_name_title_arr[$file_name];
}*/
$customer_master='customer_master';
$dealer_security_ledger_status='dealer_security_ledger_status';

$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];
$sql3 = "select `customer_id` from $customer_master where `dns_customer_code`='$sswa_selected_dealer_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
$row3 = mysql_fetch_assoc($res3);
$the_customer_id = trim($row3["customer_id"]);

$sql_sho = "select `customer_code`,`security_ledger_status` from $dealer_security_ledger_status where `customer_code`='$sswa_selected_dealer_code'";
$res_sho=mysql_query($sql_sho);
$tot_res_sho = mysql_num_rows($res_sho);
if($tot_res_sho > 0)
{
	$row_sho = mysql_fetch_array($res_sho);
	$security_ledger_status=$row_sho["security_ledger_status"];
}
else{$security_ledger_status='INACTIVE';}
//echo $security_ledger_status;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Star Cement Customer Portal</title>
    <!-- Favicon-->
    <link rel="icon" href="images/star_logo.jpg" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
   
    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />
    
        <!-- Bootstrap Material Datetime Picker Css -->
<link href="plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />


    <!-- Morris Chart Css-->
    <link href="plugins/morrisjs/morris.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="css/themes/all-themes.css" rel="stylesheet" />

    
<link href="css/colorbox.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="plugins/chosen/chosen.css">
<link href="css/jquery-ui.css" rel="stylesheet" />

<!-- Jquery Core Js -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap Core Js -->
<script src="plugins/bootstrap/js/bootstrap.js"></script>
<!-- Select Plugin Js -->
<!--<script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>-->
<!-- Slimscroll Plugin Js -->
<script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- Waves Effect Plugin Js -->
<script src="plugins/node-waves/waves.js"></script>
<!-- Autosize Plugin Js -->
<script src="plugins/autosize/autosize.js"></script>
<!-- Moment Plugin Js -->
<script src="plugins/momentjs/moment.js"></script>
<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
<!-- Custom Js -->
<script src="js/admin.js"></script>
<script src="js/pages/forms/basic-form-elements.js"></script>
<!-- Demo Js -->
<script src="js/demo.js"></script>

<!-- Morris Plugin Js -->
<script src="plugins/raphael/raphael.min.js"></script>
<script src="plugins/morrisjs/morris.js"></script>


<?php /*?><script src="js/highchart/highcharts.js"></script>
<script src="js/highchart/exporting.js"></script>
<script src="js/highchart/export-data.js"></script>
<script src="js/highchart/accessibility.js"></script>

<!-- Chart Plugins Js -->
<script src="plugins/chartjs/Chart.bundle.js"></script><?php */?>

<!--<script src="js/pages/charts/chartjs.js"></script>-->



<script type="text/javascript" src="plugins/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/jquery.colorbox.js"></script>

<script src="js/highchart/highcharts.js"></script>
<script src="js/highchart/data.js"></script>
<script src="js/highchart/exporting.js"></script>
<script src="js/highchart/accessibility.js"></script>

</head>

<body class="theme-red">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <div class="overlay"></div>
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <?php /*?><a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a><?php */?>
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand" href="javascript:void(0);"><?php echo $page_curr_title;?></a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                
            </div>
        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <div class="user-info">
                <div class="image">
                    <!--<img src="images/user.png" width="48" height="48" alt="User" />-->
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> 
					<?php if(trim($_SESSION["sswa_user_name"])!=""){
						$name_login = trim($_SESSION["sswa_user_name"]);
						$name_login = ucwords(strtolower($name_login));
						echo $name_login;
					}?>
                    </div>
                    <div class="email"><?php echo " (".$_SESSION["sswa_user_type"].")";?></div>
                    <?php if($_SESSION["sswa_user_type"]=="SP"){ ?>
                    <div class="email">Dealer: <?php echo $_SESSION["sswa_selected_dealer_name"];?></div>
                    <?php } ?>
                    
                    
                    
                    <div class="btn-group user-helper-dropdown">
                        <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="web_logout.php"><i class="material-icons">input</i>Sign Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- #User Info -->
            <!-- Menu -->
            <div class="menu">
                <ul class="list">
                    <li class="header">MAIN NAVIGATION</li>
                    
                    <li <?php if($file_name=="main.php"){ echo 'class="active"';}?>>
                        <a href="main.php">
                            <i class="material-icons">home</i>
                            <span>Home</span>
                        </a>
                    </li>
                    <?php
                    if($_SESSION["sswa_user_type"]=="DEALER"){ ?>
                        
                        <!--li <?php //if($file_name=="star_make_order.php"){ echo 'class="active"';}?>>
                        <a href="star_make_order.php">
                        <i class="material-icons">layers</i>
                        <span>Make Order</span>
                        </a>
                        </li-->
                        
                        <li <?php if($file_name=="track_order.php"){ echo 'class="active"';}?>>
                        <a href="track_order.php">
                        <i class="material-icons">layers</i>
                        <span>Track Order</span>
                        </a>
                        </li>
                        
                        <li <?php if($file_name=="ledger_balance.php" || $file_name=="show_confirm_ledger_balance_list.php"){ echo 'class="active"';}?>>
                        <a href="ledger_balance.php">
                        <i class="material-icons">layers</i>
                        <span>Mini Statement</span>
                        </a>
                        </li>
                        
                        <li <?php if($file_name=="show_confirm_ledger_balance_list.php"){ echo 'class="active"';}?> style="display:none;">
                        <a href="show_confirm_ledger_balance_list.php">
                        <i class="material-icons">layers</i>
                        <span>Confirm Ledger Balance</span>
                        </a>
                        </li>
                        
                        
                        <li <?php if($file_name=="performance.php"){ echo 'class="active"';}?>>
                        <a href="performance.php">
                        <i class="material-icons">layers</i>
                        <span>Performance</span>
                        </a>
                        </li>
                        
                        <li <?php if($file_name=="show_nitifications.php"){ echo 'class="active"';}?> >
                        <a href="show_nitifications.php">
                        <i class="material-icons">layers</i>
                        <span>Notification</span>
                        </a>
                        </li>
                        
                        <li <?php if($file_name=="show_scheme.php"){ echo 'class="active"';}?>>
                        <a href="show_scheme.php">
                        <i class="material-icons">layers</i>
                        <span>Scheme</span>
                        </a>
                        </li>
                        
                         <li <?php if($file_name=="customer_ledger.php"){ echo 'class="active"';}?>>
                        <a href="customer_ledger.php">
                        <i class="material-icons">layers</i>
                        <span>Detailed Statement</span>
                        </a>
                        </li>
                        <li <?php if($file_name=="customer_credit_note.php"){ echo 'class="active"';}?>>
                        <a href="customer_credit_note.php">
                        <i class="material-icons">layers</i>
                        <span>Credit Note</span>
                        </a>
                        </li>
                         <li <?php if($file_name=="customer_invoice.php"){ echo 'class="active"';}?>>
                        <a href="customer_invoice.php">
                        <i class="material-icons">layers</i>
                        <span>Invoice</span>
                        </a>
                        </li>
                        <li <?php if($file_name=="customer_debit_note.php"){ echo 'class="active"';}?>  >
                        <a href="customer_debit_note.php">
                        <i class="material-icons">layers</i>
                        <span>Debit Note</span>
                        </a>
                        </li>
                         <li <?php if($file_name=="customer_sub_dealer_ledger.php"){ echo 'class="active"';}?>  >
                        <a href="customer_sub_dealer_ledger.php">
                        <i class="material-icons">layers</i>
                        <span>Sub Dealer Ledger</span>
                        </a>
                        </li>
					<?php if($security_ledger_status=='ACTIVE'){?>
						 <li <?php if($file_name=="customer_security_ledger.php"){ echo 'class="active"';}?>  >
                        <a href="customer_security_ledger.php">
                        <i class="material-icons">layers</i>
                        <span>Security Deposit Ledger</span>
                        </a>
                        </li>
					<?Php }?>

					<?php } else if($_SESSION["sswa_user_type"]=="SP"){ ?>
                        <li <?php if($file_name=="star_make_order.php"){ echo 'class="active"';}?> >
                        <a href="star_make_order.php">
                        <i class="material-icons">layers</i>
                        <span>Make Order</span>
                        </a>
                        </li>
                    
						<li <?php if($file_name=="track_order.php"){ echo 'class="active"';}?>>
                        <a href="track_order.php">
                        <i class="material-icons">layers</i>
                        <span>Track Order</span>
                        </a>
                        </li>
                        
                        <li <?php if($file_name=="ledger_balance.php"){ echo 'class="active"';}?>>
                        <a href="ledger_balance.php">
                        <i class="material-icons">layers</i>
                        <span>Mini Statement</span>
                        </a>
                        </li>
                        
                        <li <?php if($file_name=="performance.php"){ echo 'class="active"';}?>>
                        <a href="performance.php">
                        <i class="material-icons">layers</i>
                        <span>Performance</span>
                        </a>
                        </li>
                        
                        <li <?php if($file_name=="show_nitifications.php"){ echo 'class="active"';}?>>
                        <a href="show_nitifications.php">
                        <i class="material-icons">layers</i>
                        <span>Notification</span>
                        </a>
                        </li>
                        
                        <li <?php if($file_name=="show_scheme.php"){ echo 'class="active"';}?>>
                        <a href="show_scheme.php">
                        <i class="material-icons">layers</i>
                        <span>Scheme</span>
                        </a>
                        </li>
					<li <?php if($file_name=="customer_ledger.php"){ echo 'class="active"';}?>>
                        <a href="customer_ledger.php">
                        <i class="material-icons">layers</i>
                        <span>Detailed Statement</span>
                        </a>
                        </li>
                        <li <?php if($file_name=="customer_credit_note.php"){ echo 'class="active"';}?>>
                        <a href="customer_credit_note.php">
                        <i class="material-icons">layers</i>
                        <span>Credit Note</span>
                        </a>
                        </li>
						</li>
                         <li <?php if($file_name=="customer_invoice.php"){ echo 'class="active"';}?>>
                        <a href="customer_invoice.php">
                        <i class="material-icons">layers</i>
                        <span>Invoice</span>
                        </a>
                        </li>
                        <li <?php if($file_name=="customer_debit_note.php"){ echo 'class="active"';}?>  >
                        <a href="customer_debit_note.php">
                        <i class="material-icons">layers</i>
                        <span>Debit Note</span>
                        </a>
                        </li>
                         <li <?php if($file_name=="customer_sub_dealer_ledger.php"){ echo 'class="active"';}?>  >
                        <a href="customer_sub_dealer_ledger.php">
                        <i class="material-icons">layers</i>
                        <span>Sub Dealer Ledger</span>
                        </a>
                        </li>
						<?php //if($security_ledger_status=='ACTIVE'){?>
						 <li <?php if($file_name=="customer_security_ledger.php"){ echo 'class="active"';}?>  >
                        <a href="customer_security_ledger.php">
                        <i class="material-icons">layers</i>
                        <span>Security Deposit Ledger</span>
                        </a>
                        </li>
					<?Php //s}?>
                        
                       
					<?php }else{
						
						}
					?>
                    <ul class="list-inline">

                      <li><a href="https://m.facebook.com/starcements/" target="_blank"><img src="images/facebook.png" class="img-responsive" width="50"/> </a></li>

                      <li><a href="http://starcement.co.in/" target="_blank"><img src="images/web.png" class="img-responsive" width="50"/> </a></li>

                      <li><a href="https://www.youtube.com/channel/UCuKSCQask__yLwCWzLd5uSw" target="_blank"><img src="images/you.png" class="img-responsive" width="50"/></a></li>

                      </ul>
                </ul>
            </div>
            <!-- #Menu -->
            <!-- Footer -->
            <!--div class="legal">
                <div class="copyright">
                    &copy; 2019 - 2020 <a href="javascript:void(0);">Forcepower infotech Pvt Ltd</a>.
                </div>
                <div class="version">
                    <b>Version: </b> 1.0.0
                </div>
            </div-->
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
      
    </section>
