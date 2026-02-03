<?php
include "web_check.php";
include "star_connection.php";
include "web_header.php";

// Pagination settings
$limit = 10;

$param = 'paged';

// Preserve other filters
$query_params = [];

$srch_dtls = isset($_GET['srch_dtls']) ? trim($_GET['srch_dtls']) : '';
$sl_day_wise = isset($_GET['sl_day_wise']) ? $_GET['sl_day_wise'] : '';
$from_dt = isset($_GET['from_dt']) ? $_GET['from_dt'] : '';
$to_dt = isset($_GET['to_dt']) ? $_GET['to_dt'] : '';
$trn_branch = isset($_GET['trn_branch']) ? $_GET['trn_branch'] : '';
$page = isset($_GET['paged']) ? (int)$_GET['paged'] : 1;

if (!empty($srch_dtls)) $query_params['srch_dtls'] = $srch_dtls;
if (!empty($sl_day_wise)) $query_params['sl_day_wise'] = $sl_day_wise;
if (!empty($from_dt)) $query_params['from_dt'] = $from_dt;
if (!empty($to_dt)) $query_params['to_dt'] = $to_dt;
if (!empty($trn_branch)) $query_params['trn_branch'] = $trn_branch;

$base_url = 'kismat_ki_bori_consumer_scheme_report.php';
// Build the URL query string without 'paged'
$filter_query = http_build_query($query_params);

// If we have filters, append them to the base URL
if (!empty($filter_query)) {
    $base_url .= '?' . $filter_query;
}


if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;


$where = "WHERE 1=1";

// Search text (customer_id, customer_name, house_owner_name)
if (!empty($srch_dtls)) {
    $escaped = mysql_real_escape_string($srch_dtls);
    $where .= " AND (
        cm.customer_id LIKE '%$escaped%' OR 
        cm.customer_name LIKE '%$escaped%' OR 
        cm.dns_customer_code LIKE '%$escaped%' OR 
        scs.house_owner_phone LIKE '%$escaped%' OR 
        scs.house_owner_name LIKE '%$escaped%'
    )";
}


// Branch code filter
if (!empty($trn_branch)) {
    $where .= " AND (
        cm.branch_code = '$trn_branch'
    )";
}else{
    $where .= " AND (
        cm.branch_code = 'B0009'
    )";
}

// Day-wise filter
if ($sl_day_wise == 'Today') {
    $where .= " AND DATE(scs.date_of_purchase) = CURDATE()";
} elseif ($sl_day_wise == 'Yesterday') {
    $where .= " AND DATE(scs.date_of_purchase) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
} elseif ($sl_day_wise == 'Date_Range' && !empty($from_dt) && !empty($to_dt)) {
    $from_date = mysql_real_escape_string($from_dt);
    $to_date = mysql_real_escape_string($to_dt);
    $where .= " AND DATE(scs.date_of_purchase) BETWEEN '$from_date' AND '$to_date'";
}



$count_query = "SELECT COUNT(*) AS total 
FROM sikkim_consumer_scheme scs 
LEFT JOIN customer_master cm ON scs.customer_id = cm.customer_id 
LEFT JOIN branch_master bm ON cm.branch_code = bm.branch_code 
$where";

echo $count_query;

$count_result = mysql_query($count_query);
$rowcount = mysql_result($count_result, 0, 'total');
$total_pages = ceil($rowcount / $limit);

function render_pagination($current_page, $total_pages, $base_url = 'kismat_ki_bori_consumer_scheme_report.php', $param = 'paged') {
    $pagination_html = '<div class="pagination">';

    // PREV
    if ($current_page <= 1) {
        $pagination_html .= '<span class="disabled_pg">PREV</span>';
    } else {
        $prev = $current_page - 1;
        $pagination_html .= '<a href="' . $base_url  . '?' . $param . '=' . $prev . '">PREV</a>';
    }

    // Page numbers
    $range = 10;
    $start = max(1, $current_page - floor($range / 2));
    $end = min($total_pages, $start + $range - 1);

    if ($start > 1) {
        $pagination_html .= '<a href="' . $base_url . '?' . $param . '=1">1</a>';
        if ($start > 2) $pagination_html .= '...';
    }

    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current_page) {
            $pagination_html .= '<span class="current_pg">' . $i . '</span>';
        } else {
			$separator = (strpos($base_url, '?') !== false) ? '&' : '?';
			$pagination_html .= '<a href="' . $base_url . $separator . $param . '=' . $i . '">' . $i . '</a>';

            // $pagination_html .= '<a href="' . $base_url . '?' . $param . '=' . $i . '">' . $i . '</a>';
        }
    }

    if ($end < $total_pages - 1) {
        $pagination_html .= '...';
        $pagination_html .= '<a href="' . $base_url . '?' . $param . '=' . $total_pages . '">' . $total_pages . '</a>';
    }

    // NEXT
    if ($current_page < $total_pages) {
        $next = $current_page + 1;
        $pagination_html .= '<a href="' . $base_url . '?' . $param . '=' . $next . '">NEXT</a>';
    } else {
        $pagination_html .= '<span class="disabled_pg">NEXT</span>';
    }

    $pagination_html .= '</div>';

    return $pagination_html;
}




$export_query = array();
$export_query[] = 'get_type=all';

if (!empty($srch_dtls))   $export_query[] = 'srch_dtls=' . urlencode($srch_dtls);
if (!empty($trn_branch))  $export_query[] = 'trn_branch=' . urlencode($trn_branch);
if (!empty($sl_day_wise)) $export_query[] = 'sl_day_wise=' . urlencode($sl_day_wise);
if (!empty($from_dt))     $export_query[] = 'from_dt=' . urlencode($from_dt);
if (!empty($to_dt))       $export_query[] = 'to_dt=' . urlencode($to_dt);

$export_url = 'export_kismat_ki_bori_consumer_scheme.php?' . implode('&', $export_query);
?>

<style>


.wrapper_scrl{
border: none;
overflow-x: scroll;
overflow-y:hidden;
height: 20px;
}
.wrapper_scrl_div{
height: 20px;	
}
.each_mk_cncl_span{
	display:block;
	width:150px;
	margin-bottom: 7px;
margin-top: 7px;
}

.table-container {
  height: 400px; /* Set the height of the container to limit the height of the table */
  overflow-y: auto; /* Enable vertical scrolling */
}

table {
  border-collapse: collapse;
  width: 100%;
}

th {
  background-color: #ddd;
  position: sticky; /* Make the table header fixed */
  top: 0;
}

th,
td {
  padding: 8px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}



</style>



<section class="content">
	<div class="container-fluid">
		<div class="block-header">
		</div>
		<!-- Basic Examples -->
		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<?php 
				// Query to fetch all records
				$query = "SELECT scs.*, cm.dns_customer_code, cm.customer_name, cm.customer_id, cm.region, bm.branch_name, bm.branch_name, cm.branch_code, cm.cust_type, cm.rds_tag FROM sikkim_consumer_scheme scs LEFT JOIN customer_master cm ON scs.customer_id = cm.customer_id LEFT JOIN branch_master bm ON cm.branch_code = bm.branch_code $where ORDER BY scs.id DESC LIMIT $offset, $limit";
				
				$result = mysql_query($query);
				?>
				<div class="card">
					<div class="header">
						<h2>Kismat Ki Bori Consumer Scheme List (<?php echo $rowcount; ?>) &nbsp;&nbsp;&nbsp;<span class="rpt_loader"></span> &nbsp;&nbsp;<a href="<?php echo $export_url; ?>" class="btn bg-red waves-effe">Export&nbsp;Consumer&nbsp;Scheme</a></h2>
						<div class="row clearfix">
							<form method="get" id="filter_form">
								<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding focused">
									<input type="text" class="form-control" id="srch_dtls" name="srch_dtls" value="<?php echo htmlspecialchars($srch_dtls); ?>" placeholder="Search Details">
								</div>
								<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding focused">
									<?php $sqlbrn = mysql_query("SELECT `branch_code`,`branch_name` FROM `branch_master` ORDER BY `branch_name` ASC"); ?>
									<select name="trn_branch" id="trn_branch" class="form-control">
										<option value="">Select Branch Name</option>
										<?php
										if($sqlbrn){
											while($rowbrn=mysql_fetch_assoc($sqlbrn)){
										?>
										<option value="<?php echo $rowbrn['branch_code'];?>" 
										<?php if(!empty($trn_branch)){
										if($rowbrn["branch_code"] == "$trn_branch"){ ?>
											selected="selected"
										<?php } }elseif($rowbrn["branch_code"] == "B0009"){ ?>
											selected="selected"
										<?php } ?> ><?php echo $rowbrn["branch_name"];?></option>
										<?php }
										} ?>
									</select>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding focused">
									<select class="form-control" id="sl_day_wise" name="sl_day_wise">
										<option value="" <?php if ($sl_day_wise == '') echo 'selected'; ?>>Select Day-Wise</option>
										<option value="Today" <?php if ($sl_day_wise == 'Today') echo 'selected'; ?>>Today</option>
										<option value="Yesterday" <?php if ($sl_day_wise == 'Yesterday') echo 'selected'; ?>>Yesterday</option>
										<option value="Date_Range" <?php if ($sl_day_wise == 'Date_Range') echo 'selected'; ?>>Date Range</option>
									</select>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
									<!-- <input type="text" class="datepicker form-control" id="from_dt" style="display:none;" value="" placeholder="Choose from date" data-dtp="dtp_DYR8U"> -->
									<input type="text" class="datepicker form-control" name="from_dt" id="from_dt" placeholder="Choose from date" value="<?php echo $from_dt; ?>">
								</div>
								<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
									<!-- <input type="text" class="datepicker form-control" id="to_dt" style="display:none;" value="" placeholder="Choose to date" data-dtp="dtp_YLldu"> -->
									<input type="text" class="datepicker form-control" name="to_dt" id="to_dt" placeholder="Choose to date" value="<?php echo $to_dt; ?>">
								</div>
								<div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
									<button type="button" class="btn bg-red waves-effect srch_btn">Search</button>
								</div>
							</form>
							<div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
								<a href="kismat_ki_bori_consumer_scheme_report.php" class="btn bg-red waves-effect srch_reset_btn">Reset</a>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
							</div>
						</div>
						<span style="clear:both;display:block;"></span>
					</div>
					<?php if ($result) { ?>
					<div class="body">
						<!-- ✅ Pagination Bar -->
						<?php
						// Build filter params manually (for PHP5 compatibility)
						$filter_params = array();
						if (!empty($srch_dtls)) $filter_params[] = 'srch_dtls=' . urlencode($srch_dtls);
						if (!empty($sl_day_wise)) $filter_params[] = 'sl_day_wise=' . urlencode($sl_day_wise);
						if (!empty($from_dt)) $filter_params[] = 'from_dt=' . urlencode($from_dt);
						if (!empty($to_dt)) $filter_params[] = 'to_dt=' . urlencode($to_dt);

						// Join with &
						$filter_query = implode('&', $filter_params);

						// Final base URL
						$base_url = 'kismat_ki_bori_consumer_scheme_report.php';
						if (!empty($filter_query)) {
							$base_url .= '?' . $filter_query;
						}

						// Call your existing pagination function
						echo render_pagination($page, $total_pages, $base_url, 'paged');
						?>


						<span style="display:block; clear:both;"></span>
						<div class="wrapper_scrl">
							<div class="wrapper_scrl_div"></div>
						</div>
						<div class="table-wrap">
							<div class="table-responsive table-container">  
								<table class="table-bordered">
									<thead>
										<tr>
											<th>Dealer SAP code</th>
											<th>SFA Code</th>
											<th>Dealer name</th>
											<th>Linked Dealer code</th>
											<th>Linked Dealer name</th>
											<th>Branch name</th>
											<th>Region</th>
											<th>Customer Type</th>
											<th>House Owner Name</th> 
											<th>House Owner Number</th> 
											<th>Date Of Purchase</th> 
											<th>Quantity (in Bags)</th> 
											<th>Has Coupon</th>
											<th>Coupon Numbers</th> 
											<th>Submit Date time</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>Dealer SAP code</th> 
											<th>SFA Code</th>
											<th>Dealer name</th>
											<th>Linked Dealer code</th>
											<th>Linked Dealer name</th>
											<th>Branch name</th>
											<th>Region</th>
											<th>Customer Type</th>
											<th>House Owner Name</th> 
											<th>House Owner Number</th> 
											<th>Date Of Purchase</th> 
											<th>Quantity (in Bags)</th> 
											<th>Has Coupon</th>
											<th>Coupon Numbers</th> 
											<th>Submit Date time</th>
										</tr>
									</tfoot>
									<tbody>
										<?php while ($row = mysql_fetch_assoc($result)) {

											$rds_tag = $row['rds_tag'];
											$cmsql = "SELECT `customer_id`,`customer_name` FROM `customer_master` WHERE `customer_code`='$rds_tag'";
											$cmquery = mysql_query($cmsql);
											$cmrow= mysql_fetch_assoc($cmquery);

											$coupons_details = $row['coupons_details'];

											// Decode JSON to PHP array
											$coupons_details = json_decode($coupons_details, true);

											// Then do your existing logic:
											$coupon_text = '';
											$coupon_values = [];

											if (is_array($coupons_details)) {
												if (array_keys($coupons_details) !== range(0, count($coupons_details) - 1)) {
													// Object format
													$coupon_values = array_values($coupons_details);
												} else {
													// Array of single-key objects
													foreach ($coupons_details as $item) {
														if (is_array($item)) {
															foreach ($item as $code) {
																$coupon_values[] = $code;
															}
														}
													}
												}
											}

											// Remove empty codes if any
											$coupon_values = array_filter($coupon_values, function($v) { return !empty($v); });

											$coupon_text = implode(',', $coupon_values);
											
											$dateofpurchase = date("d-m-Y", strtotime($row['date_of_purchase']));
											$formatted_datetime = date("d-m-Y H:i:s", strtotime($row['date_and_time']));
											echo '<tr id="each_scheme_tr_">';
											echo "<td>" . htmlspecialchars($row['customer_id']) . "</td>";
											echo "<td>" . htmlspecialchars($row['dns_customer_code']) . "</td>";
											echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
											echo "<td>" . htmlspecialchars($cmrow['customer_id']) . "</td>";
											echo "<td>" . htmlspecialchars($cmrow['customer_name']) . "</td>";
											echo "<td>" . htmlspecialchars($row['branch_name']) . "</td>";
											echo "<td>" . htmlspecialchars($row['region']) . "</td>";
											echo "<td>" . htmlspecialchars($row['cust_type']) . "</td>";
											echo "<td>" . htmlspecialchars($row['house_owner_name']) . "</td>";
											echo "<td>" . htmlspecialchars($row['house_owner_phone']) . "</td>";
											echo "<td>" . htmlspecialchars($dateofpurchase) . "</td>";
											echo "<td>" . htmlspecialchars($row['bags_quantity']) . "</td>";
											echo "<td>" . htmlspecialchars($row['has_coupon']) . "</td>";
											echo '<td>';
											if (!empty($coupon_text)) {
												$coupon_array = explode(',', $coupon_text);
												$coupon_array = array_map('trim', $coupon_array);
												$first_two = array_slice($coupon_array, 0, 2);
												$first_display = implode(', ', $first_two);
												echo '<span class="short-coupons">' . htmlspecialchars($first_display) . '</span>';

												if (count($coupon_array) > 2) {
													echo '... <button type="button" class="btn bg-red btn-xs show-coupon-btn" data-coupons="' . htmlspecialchars($coupon_text) . '">Show More</button>';
												}
											} else {
												echo '-';
											}
											echo '</td>';
											echo "<td>" . htmlspecialchars($formatted_datetime) . "</td>";
											echo "</tr>";
										} ?>
									</tbody>
								</table>
							</div>
						</div>
						<!-- ✅ Pagination Bar -->
						<?php
						// Build filter params manually (for PHP5 compatibility)
						$filter_params = array();
						if (!empty($srch_dtls)) $filter_params[] = 'srch_dtls=' . urlencode($srch_dtls);
						if (!empty($sl_day_wise)) $filter_params[] = 'sl_day_wise=' . urlencode($sl_day_wise);
						if (!empty($from_dt)) $filter_params[] = 'from_dt=' . urlencode($from_dt);
						if (!empty($to_dt)) $filter_params[] = 'to_dt=' . urlencode($to_dt);

						// Join with &
						$filter_query = implode('&', $filter_params);

						// Final base URL
						$base_url = 'kismat_ki_bori_consumer_scheme_report.php';
						if (!empty($filter_query)) {
							$base_url .= '?' . $filter_query;
						}

						// Call your existing pagination function
						echo render_pagination($page, $total_pages, $base_url, 'paged');
						?>

						<span style="display:block; clear:both;"></span>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Coupon Modal -->
<div id="couponModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Coupons</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float:right;">×</button>
      </div>
      <div class="modal-body">
        <p id="couponModalContent" style="word-break: break-word;"></p>
      </div>
    </div>
  </div>
</div>



<?php
include "web_footer.php";
?>
<script>
$(document).ready(function(){
	$('#from_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
	$('#to_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });

	function handleDayWiseChange() {
		var sl_day_wise = $("#sl_day_wise").val();

		if (sl_day_wise === "Date_Range") {
			$('#from_dt, #to_dt').show();
		} else {
			$('#from_dt, #to_dt').hide().val("");
		}
	}

	handleDayWiseChange(); // run once on page load
	// Attach the function to the change event
	$("#sl_day_wise").change(handleDayWiseChange);
	


	$('.srch_btn').on('click', function () {
		$('#filter_form').submit();
	});

	$('.show-coupon-btn').on('click', function () {
        var couponText = $(this).data('coupons');
        $('#couponModalContent').text(couponText);
        $('#couponModal').modal('show');
    });
});
</script>