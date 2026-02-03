<?php
$file_name = basename($_SERVER['PHP_SELF']);
if($file_name==''){
	$file_name = "index.php";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Welcome To Star Cement Dashboard</title>
    <!-- Favicon-->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
   
    
    <!-- Bootstrap Material Datetime Picker Css -->
<link href="plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />
    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />
    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />
    <!-- Morris Chart Css-->
    <link href="plugins/morrisjs/morris.css" rel="stylesheet" />
    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">
    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="css/themes/all-themes.css" rel="stylesheet" />
<!-- Jquery Core Js -->
    
     <link href="css/colorbox.css" rel="stylesheet" />
<link href="css/jquery-ui.css" rel="stylesheet" />
     
     <link rel="stylesheet" type="text/css" href="plugins/chosen/chosen.css">
<script src="plugins/jquery/jquery.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<script type="text/javascript" src="plugins/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
<script src="js/jquery-ui.js"></script>
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
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand" href="main.php">Star Cement - Admin Panel</a>
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
                    <img src="images/user.png" width="48" height="48" alt="User" />
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Welcome 
					<?php if(trim($_SESSION["start_report_admin_name"])!=""){
						$name_login = trim($_SESSION["start_report_admin_name"]);
						$name_login = ucwords(strtolower($name_login));
						echo $name_login;
					}?>
                    </div>
                    <div class="email">Star Cement</div>
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
                    
                   <?php if($_SESSION["start_report_admin_name"]=='user1' || $_SESSION["start_report_admin_name"]=='user2'){ ?>
                        <li <?php if($file_name=="lifting_report.php"){ echo 'class="active"';}?>>
                        <a href="lifting_report.php">
                        <i class="material-icons">layers</i>
                        <span>Lifting Report</span>
                        </a>
                        </li> 
                <?php } else if($_SESSION["start_report_admin_name"]=='user3'){ ?>
                    <li <?php if($file_name=="unique_admin_pop_order_report.php"){ echo 'class="active"';}?>>
                    <a href="unique_admin_pop_order_report.php">
                    <i class="material-icons">layers</i>
                    <span>POP Order</span>
                    </a>
                    </li>
					<?php }else{ ?>
                    <li <?php if($file_name=="home_page_slider_list.php"){ echo 'class="active"';}?>>
                        <a href="home_page_slider_list.php">
                            <i class="material-icons">layers</i>
                            <span>App Slider</span>
                        </a>
                    </li>
                    
                    
                    <?php
                    if($_SESSION["start_user_type"]=="MANAGER"){ ?>
                        <li <?php if($file_name=="order_list.php"){ echo 'class="active"';}?>>
                        <a href="order_list.php">
                        <i class="material-icons">layers</i>
                        <span>Order List</span>
                        </a>
                        </li>
                        
                        <!--li <?php //if($file_name=="canceled_order_list.php"){ echo 'class="active"';}?>>
                        <a href="canceled_order_list.php">
                        <i class="material-icons">layers</i>
                        <span>Cancelled Order List</span>
                        </a>
                        </li-->
					<?php }else{
					?>
                    <li <?php if($file_name=="dealer_list.php"){ echo 'class="active"';}?>>
                        <a href="dealer_list.php">
                            <i class="material-icons">view_list</i>
                            <span>Dealer List</span>
                        </a>
                        
                    </li>
                    
                    
                    <li <?php if($file_name=="submitted_survey_list_18_20.php"){ echo 'class="active"';}?>>
                        <a href="submitted_survey_list_18_20.php">
                            <i class="material-icons">view_list</i>
                            <span>Survey List FY 18-20</span>
                        </a>
                        
                    </li>
                    
                    
                    <li <?php if($file_name=="submitted_survey_list.php"){ echo 'class="active"';}?>>
                        <a href="submitted_survey_list.php">
                            <i class="material-icons">view_list</i>
                            <span>Survey List</span>
                        </a>
                        
                    </li>
                    
                    <li <?php if($file_name=="submitted_notice_list.php"){ echo 'class="active"';}?>>
                        <a href="submitted_notice_list.php">
                            <i class="material-icons">view_list</i>
                            <span>Notice 2023 List</span>
                        </a>
                        
                    </li>
                    
                    <li <?php if($file_name=="submitted_nye_list.php"){ echo 'class="active"';}?>>
                        <a href="submitted_nye_list.php">
                            <i class="material-icons">view_list</i>
                            <span>NYE List</span>
                        </a>
                        
                    </li>
                    
                    <li <?php if($file_name=="edit_dealer_list.php"){ echo 'class="active"';}?> style="display:none;">
                        <a href="edit_dealer_list.php">
                            <i class="material-icons">view_list</i>
                            <span>Edit Dealer</span>
                        </a>
                        
                    </li>
                     <li <?php if($file_name=="sub_dealer_list.php"){ echo 'class="active"';}?>>
                        <a href="sub_dealer_list.php">
                            <i class="material-icons">view_list</i>
                            <span>Sub Dealer List</span>
                        </a>
                        
                    </li>
                    <li <?php if($file_name=="edit_sub_dealer_list.php"){ echo 'class="active"';}?> style="display:none;">
                        <a href="edit_sub_dealer_list.php">
                            <i class="material-icons">view_list</i>
                            <span>Edit Sub Dealer</span>
                        </a>
                        
                    </li>
                    
                    <li <?php if($file_name=="customer_list.php"){ echo 'class="active"';}?>>
                        <a href="customer_list.php">
                            <i class="material-icons">layers</i>
                            <span>Customer List</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="order_list.php"){ echo 'class="active"';}?>>
                        <a href="order_list.php">
                            <i class="material-icons">layers</i>
                            <span>Order List</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="offline_order_list.php"){ echo 'class="active"';}?>>
                        <a href="offline_order_list.php">
                            <i class="material-icons">layers</i>
                            <span>Offline Order List</span>
                        </a>
                    </li>
                    
                    <!--li <?php /*if($file_name=="canceled_order_list.php"){ echo 'class="active"';}*/?>>
                        <a href="canceled_order_list.php">
                        <i class="material-icons">layers</i>
                        <span>Cancelled Order List</span>
                        </a>
                        </li-->
                        
                        <li <?php if($file_name=="auto_notification_log.php"){ echo 'class="active"';}?>>
                        <a href="auto_notification_log.php">
                        <i class="material-icons">layers</i>
                        <span>Auto Notification Log</span>
                        </a>
                        </li>
                    
                   <?php ?> <li <?php if($file_name=="upload_fpx_dealer_data.php"){ echo 'class="active"';}?>>
                        <a href="upload_fpx_dealer_data.php">
                            <i class="material-icons">layers</i>
                            <span>Upload Fpx Dealer Data</span>
                        </a>
                    </li><?php ?>
                    
                    <li <?php if($file_name=="send_notification.php"){ echo 'class="active"';}?>>
                        <a href="send_notification.php">
                            <i class="material-icons">layers</i>
                            <span>Notification</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="employee_kyc_details.php"){ echo 'class="active"';}?>>
                        <a href="employee_kyc_details.php">
                            <i class="material-icons">layers</i>
                            <span>Customer KYC Details</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="schemes.php"){ echo 'class="active"';}?>>
                        <a href="schemes.php">
                            <i class="material-icons">layers</i>
                            <span>Schemes</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="add_new_schemes.php"){ echo 'class="active"';}?> style="display:none;">
                        <a href="add_new_schemes.php">
                            <i class="material-icons">layers</i>
                            <span>Add New Schemes</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="subdealer_schemes.php"){ echo 'class="active"';}?>>
                        <a href="subdealer_schemes.php">
                            <i class="material-icons">layers</i>
                            <span>Schemes for Subdealer</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="add_new_subdealer_schemes.php"){ echo 'class="active"';}?> style="display:none;">
                        <a href="add_new_subdealer_schemes.php">
                            <i class="material-icons">layers</i>
                            <span>Add New Subdealer Schemes</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="ledger_verify_status.php"){ echo 'class="active"';}?>>
                        <a href="ledger_verify_status.php">
                            <i class="material-icons">layers</i>
                            <span>Check Ledger Verify Status</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="customer_credit_limit_list.php"){ echo 'class="active"';}?>>
                        <a href="customer_credit_limit_list.php">
                            <i class="material-icons">layers</i>
                            <span>Customer Credit Limit</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="destination_price_list.php"){ echo 'class="active"';}?>>
                        <a href="destination_price_list.php">
                            <i class="material-icons">layers</i>
                            <span>Destination Price</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="customer_destination_list.php"){ echo 'class="active"';}?>>
                        <a href="customer_destination_list.php">
                            <i class="material-icons">layers</i>
                            <span>Customer Destination</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="sp_destination_list.php"){ echo 'class="active"';}?>>
                        <a href="sp_destination_list.php">
                            <i class="material-icons">layers</i>
                            <span>SP Destination</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="branch_wise_pg_status.php"){ echo 'class="active"';}?>>
                        <a href="branch_wise_pg_status.php">
                            <i class="material-icons">layers</i>
                            <span>Branch Wise PG Status</span>
                        </a>
                    </li>
                     <li <?php if($file_name=="branch_wise_game_status.php"){ echo 'class="active"';}?>>
                        <a href="branch_wise_game_status.php">
                            <i class="material-icons">layers</i>
                            <span>Branch Game Status</span>
                        </a>
                    </li>
                     <li <?php if($file_name=="dealer_wise_tour_status.php"){ echo 'class="active"';}?> >
                        <a href="dealer_wise_tour_status.php">
                            <i class="material-icons">layers</i>
                            <span>Dealer Tour Status</span>
                        </a>
                    </li>
					  <li <?php if($file_name=="dealer_wise_security_ledger_status.php"){ echo 'class="active"';}?> >
                        <a href="dealer_wise_security_ledger_status.php">
                            <i class="material-icons">layers</i>
                            <span>Security Ledger Status</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="branch_credit_limit_status.php"){ echo 'class="active"';}?>>
                        <a href="branch_credit_limit_status.php">
                            <i class="material-icons">layers</i>
                            <span>Branch wise setup</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="arc_consumer_reg.php"){ echo 'class="active"';}?>>
                        <a href="arc_consumer_reg.php">
                            <i class="material-icons">layers</i>
                            <span>ARC Consumer Registration List</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="arc_gift_redeem.php"){ echo 'class="active"';}?>>
                        <a href="arc_gift_redeem.php">
                            <i class="material-icons">layers</i>
                            <span>ARC Gift Redemption List</span>
                        </a>
                    </li>
                    
                    
                    
                    <li <?php if($file_name=="ageing_list.php"){ echo 'class="active"';}?>>
                        <a href="ageing_list.php">
                            <i class="material-icons">layers</i>
                            <span>Ageing Details</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="db_backup_details.php"){ echo 'class="active"';}?>>
                        <a href="db_backup_details.php">
                            <i class="material-icons">layers</i>
                            <span>DB Backup Details</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="show_yellow_card_list.php"){ echo 'class="active"';}?>>
                        <a href="show_yellow_card_list.php">
                            <i class="material-icons">layers</i>
                            <span>Yellow Card List</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="sp_broker_list.php"){ echo 'class="active"';}?>>
                        <a href="sp_broker_list.php">
                            <i class="material-icons">layers</i>
                            <span>Sales Promoter List</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="edit_sp_broker_list.php"){ echo 'class="active"';}?> style="display:none;">
                        <a href="edit_sp_broker_list.php">
                            <i class="material-icons">layers</i>
                            <span>Edit Sales Promoter List</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="star_app_settings.php"){ echo 'class="active"';}?>>
                        <a href="star_app_settings.php">
                            <i class="material-icons">layers</i>
                            <span>App Setting</span>
                        </a>
                    </li>
                    
                    
                    <li <?php if($file_name=="lifting_report.php"){ echo 'class="active"';}?>>
                        <a href="lifting_report.php">
                            <i class="material-icons">layers</i>
                            <span>Lifting Report</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="lifting_panel.php"){ echo 'class="active"';}?>>
                        <a href="lifting_panel.php">
                            <i class="material-icons">layers</i>
                            <span>Lifting Panel</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="lifting_edit.php"){ echo 'class="active"';}?>>
                        <a href="lifting_edit.php">
                            <i class="material-icons">layers</i>
                            <span>Lifting Edit</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="server_disk_space.php"){ echo 'class="active"';}?>>
                        <a href="server_disk_space.php">
                            <i class="material-icons">layers</i>
                            <span>Server Disk Space</span>
                        </a>
                    </li>
					 <li <?php if($file_name=="upload_pop_image.php"){ echo 'class="active"';}?>>
                        <a href="upload_pop_image.php">
                            <i class="material-icons">layers</i>
                            <span>Upload POP Product Image</span>
                        </a>
                    </li>
                    <li <?php if($file_name=="pop_order_report.php"){ echo 'class="active"';}?>>
                        <a href="pop_order_report.php">
                            <i class="material-icons">layers</i>
                            <span>POP Order Report</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="main_pop_order_list.php"){ echo 'class="active"';}?>>
                        <a href="main_pop_order_list.php">
                            <i class="material-icons">layers</i>
                            <span>Main POP Order List</span>
                        </a>
                    </li>
                    
                    
                    <li <?php if($file_name=="star-saathi-rewards.php"){ echo 'class="active"';}?>>
                        <a href="star-saathi-rewards.php">
                            <i class="material-icons">layers</i>
                            <span>Star Saathi Rewards</span>
                        </a>
                    </li>
					<li <?php if($file_name=="customer_usage_log.php"){ echo 'class="active"';}?>>
                        <a href="customer_usage_log.php">
                            <i class="material-icons">layers</i>
                            <span>Customer Portal Usage Report</span>
                        </a>
                    </li>
                    
                    <li <?php if($file_name=="customer_app_usage_log.php"){ echo 'class="active"';}?>>
                        <a href="customer_app_usage_log.php">
                            <i class="material-icons">layers</i>
                            <span>Customer App Usage Report</span>
                        </a>
                    </li>
                  <?php
					}
                }
					?>
                    
                </ul>
            </div>
            <!-- #Menu -->
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                    &copy; 2017 - 2018 <a href="javascript:void(0);">Forcepower infotech Pvt Ltd</a>.
                </div>
                <div class="version">
                    <b>Version: </b> 1.0.0
                </div>
            </div>
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
      
    </section>
