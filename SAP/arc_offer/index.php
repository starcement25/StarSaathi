<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
$branch_master = "branch_master";

$is_show = "NO";
$show_message = "";

$user_type = $_GET["user_type"] ? strtolower($_GET["user_type"]) : "";
$login_user_id = $_GET["login_user_id"] ? urldecode($_GET["login_user_id"]) : "";

if ($user_type != "" && $login_user_id != "") {
	if ($user_type == "dealer") {
		$customer_data_arr = get_customer_data_check_by_id($login_user_id);
		$the_sts = $customer_data_arr["sts"];
		$is_branch_arc = $customer_data_arr["is_branch_arc"];
		if ($the_sts == "YES") {
			if ($is_branch_arc == "YES") {
				$is_show = "YES";
			} else {
				$show_message = "Feature not available.";
			}
		} else {
			$show_message = "Your details are missing.";
		}
	} else {
		$show_message = "Feature not available.";
	}
} else {
	$show_message = "Something went wrong.";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<title>Gift Catelogue</title>
</head>

<body>
	<div class="container-fluid">
		<?php
		if ($is_show == "NO" && $show_message != "") { ?>
			<h4 style="text-align:center;margin-top:30px;"><?php echo $show_message; ?></h4>
		<?php
			exit;
		}
		?>


		<!-- <h4 style="text-align:center">ARC CONSUMER SCHEME</h4>
		<p style="text-align:center">GIFT CATALOGUE</p>
		<div class="row">
			<div class="col-6">
				<div class="card">
					<img class="card-img-top" src="img/flask.jpg" alt="Card image">
				</div>
			</div>
			<div class="col-6">
				<div class="card">
					<img class="card-img-top" src="img/kettle.jpg" alt="Card image">
				</div>
			</div>
		</div>

		<div class="row" style="margin-top:20px;">
			<div class="col-6">
				<div class="card">
					<img class="card-img-top" src="img/dinner_set.jpg" alt="Card image">
				</div>
			</div>
			<div class="col-6">
				<div class="card">
					<img class="card-img-top" src="img/suitcase.jpg" alt="Card image">
				</div>
			</div>
		</div>

		<div class="row" style="margin-top:20px;">
			<div class="col-6">
				<div class="card">
					<img class="card-img-top" src="img/bike.jpg" alt="Card image">
				</div>
			</div>
			<div class="col-6">

			</div>
		</div> -->
		<!-- insert a image with full width -->
		<img src="img/ARC_consumer_display_updated.jpg" class="img-fluid" alt="Responsive image" style="margin-top:20px;">


		<div class="row" style="margin-top:20px;">
			<a href="arc_consumer_reg.php?login_user_id=<?php echo urlencode($login_user_id); ?>&user_type=<?php echo $user_type; ?>" class="btn btn-large btn-block btn-success">SALE REGISTRATION</a>

			<!-- <a href="arc_gift_redemption.php?login_user_id=<?php echo urlencode($login_user_id); ?>&user_type=<?php echo $user_type; ?>" class="btn btn-large btn-block btn-primary">GIFT REDEMPTION</a> -->

			<a href="arc_sales_registration_history.php?login_user_id=<?php echo urlencode($login_user_id); ?>&user_type=<?php echo $user_type; ?>" class="btn btn-large btn-block btn-primary">SALE REGISTRATION HISTORY</a> 

			<a href="arc_gift_redemption_history.php?login_user_id=<?php echo urlencode($login_user_id); ?>&user_type=<?php echo $user_type; ?>" class="btn btn-large btn-block btn-primary">GIFT REDEMPTION HISTORY</a>
			<p style="text-align:center; font-size:12px; margin-top:10px;width:100%;">END CUSTOMER NEEDS TO LIFT MINIMUM 100 BAGS TO BE ELLIGIBLE FOR THE ABOVE SCHEME DURING THE SCHEME PERIOD - Terms & Conditions Apply
				<br />
				Prize images shown in the App are indicative only.
			</p>
		</div>

	</div>
</body>

</html>
<?php
mysql_close();
?>