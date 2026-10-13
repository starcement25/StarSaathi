<?php
/* -----------------------------------------------------------------------
   Security Deposit Statement of Account  ->  PDF
   Data source : SAP OData V4 service ZSD_SDEPOSIT_CUSTOMER
                 (SecurityDepositHeader?$expand=_Items)
   Called from : customer_security_ledger.php  (SCL = 1010 / SCNEL = 1017)
   ----------------------------------------------------------------------- */
set_time_limit(0);
ini_set('memory_limit', '-1');
ob_start();

include "web_check.php";
include "star_connection.php";

require_once('test/dompdf/autoload.inc.php');

use Dompdf\Dompdf;
use Dompdf\Options;

/* Port of the OData V4 gateway serving the SD deposit service. */
if(!defined('SRARFIORI_V4_PORT_NO')){
	define('SRARFIORI_V4_PORT_NO','44300');
	//define('SRARFIORI_V4_PORT_NO','44301');
}

$customer_master = "customer_master";

$sswa_user_type              = $_SESSION["sswa_user_type"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

/* ---------------------------------------------------------------- inputs */
$company_title_arr = array("1010"=>"SCL","1016"=>"SBS","1017"=>"SCNEL");

$the_company_code = isset($_REQUEST["bukrs"]) ? trim($_REQUEST["bukrs"]) : "";
if(!isset($company_title_arr[$the_company_code])){
	$the_company_code = '1010';
}
$the_company_title = $company_title_arr[$the_company_code];

/* Static registered-office block. The OData service returns CompName /
   CompAddr but not the sub-title / CIN / office lines that appear on the
   printed statement, so they are kept here per company code. Companies
   that are not listed fall back to the address returned by the service. */
$company_static_arr = array(
	"1010" => array(
		"lines" => array(
			"(Formerly Cement Manufacturing Company Limited)",
			"CIN NO : L26942ML2001PLC006663",
			"REG OFF : Vill &amp; P.O.:Lumshnong,Dist:East Jaintia Hills, Meghalaya - 793210",
			"Guwahati Off : Mayur Garden , 2nd Flr , GS Road , Bhangagarh , Guwahati , Assam - 781005",
			"Kolkata Off : Century House , Star Cement Limited , P15/1 , Taratala Road Kokata- 700088"
		)
	),
	"1017" => array(
		"lines" => array(
			"<b>CIN NO</b> : U26999AS2021PLC021391",
			"<b>REGD Office</b>: Mayur Garden , 2nd Floor , GS Road , Bhangagarh , Guwahati , Assam - 781005",
			"<b>Kolkata Office</b> : Century House , Star Cement Limited , P15/1 , Taratala Road Kokata- 700088"
		)
	)
);

$curr_date = date("Y-m-d");
$from_dt = isset($_REQUEST['from_dt']) ? trim($_REQUEST['from_dt']) : '';
$to_dt   = isset($_REQUEST['to_dt'])   ? trim($_REQUEST['to_dt'])   : '';

/* Same default window customer_security_ledger.php pre-fills its search
   with, so a direct call without a range produces the same statement. */
if(!defined('SD_PDF_DEFAULT_FROM')){
	define('SD_PDF_DEFAULT_FROM','2025-04-01');
}

if($from_dt=='' || $to_dt==''){
	$the_date_from = date("Ymd", strtotime(SD_PDF_DEFAULT_FROM));
	$the_date_to   = date("Ymd", strtotime($curr_date));
}else{
	$the_date_from = date("Ymd", strtotime($from_dt));
	$the_date_to   = date("Ymd", strtotime($to_dt));
}

/* ------------------------------------------------------- customer lookup */
$the_customer_id = "";
$the_customer_nm = "";
$sql3 = "select `dns_customer_code`,`customer_id`,customer_name from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
	$row3 = mysql_fetch_assoc($res3);
	$the_dealer_id   = trim($row3["dns_customer_code"]);
	$the_customer_id = trim($row3["customer_id"]);
	$the_customer_nm = trim($row3["customer_name"]);
}

if($the_customer_id==""){
	ob_end_clean();
	echo "Customer code could not be resolved. Please contact support.";
	exit;
}

if(strtoupper($sswa_user_type)=='DEALER'){
	/*----Tracklog Code Start-----*/
	$curr_date_time  = date("Y-m-d H:i:s");
	$webservice_name = "SECURITY DEPOSIT STATEMENT PDF";
	$track_details   = $the_company_code." | ".$the_date_from." - ".$the_date_to;
	$sqlin_tl = "insert into `webservice_track_log` (`customer_code`,`webservice_name`,`details`,`datetime`) values ('$the_customer_id','$webservice_name','$track_details','$curr_date_time')";
	$resin_tl = mysql_query($sqlin_tl);
	/*----Tracklog Code End-----*/
}

/* ------------------------------------------------------------- helpers */
function sdp_e($str){
	return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/* plain amount : -3,434,311 */
function sdp_num($v, $dec=0){
	if($v===null || $v===''){ $v = 0; }
	return number_format((float)$v, $dec);
}

/* SAP style amount : 10,742.00-  (sign printed after the figure) */
function sdp_sap_num($v, $dec=2){
	$v = (float)$v;
	if($v < 0){
		return number_format(abs($v), $dec)."-";
	}
	return number_format($v, $dec);
}

function sdp_words_below_thousand($n){
	$ones = array("","One","Two","Three","Four","Five","Six","Seven","Eight","Nine","Ten",
		"Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen","Seventeen","Eighteen","Nineteen");
	$tens = array("","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety");

	$n = (int)$n;
	$out = "";
	if($n >= 100){
		$out .= $ones[(int)floor($n/100)]." Hundred";
		$n = $n % 100;
		if($n > 0){ $out .= " "; }
	}
	if($n >= 20){
		$out .= $tens[(int)floor($n/10)];
		$n = $n % 10;
		if($n > 0){ $out .= " ".$ones[$n]; }
	}elseif($n > 0){
		$out .= $ones[$n];
	}
	return $out;
}

/* Indian numbering : Crore / Lakh / Thousand */
function sdp_words_int($n){
	$n = (int)$n;
	if($n <= 0){ return "Zero"; }

	$parts = array();
	$crore = (int)floor($n/10000000); $n = $n % 10000000;
	$lakh  = (int)floor($n/100000);   $n = $n % 100000;
	$thou  = (int)floor($n/1000);     $n = $n % 1000;

	if($crore > 0){ $parts[] = sdp_words_int($crore)." Crore"; }
	if($lakh  > 0){ $parts[] = sdp_words_below_thousand($lakh)." Lakh"; }
	if($thou  > 0){ $parts[] = sdp_words_below_thousand($thou)." Thousand"; }
	if($n     > 0){ $parts[] = sdp_words_below_thousand($n); }

	return implode(" ", $parts);
}

/* Rupees Thirty Four Lakh ... Only. */
function sdp_amount_in_words($amount){
	$amount = abs((float)$amount);
	$rupees = (int)floor($amount);
	$paise  = (int)round(($amount - $rupees) * 100);

	$words = "Rupees ".sdp_words_int($rupees);
	if($paise > 0){
		$words .= " and ".sdp_words_int($paise)." Paise";
	}
	return $words." Only.";
}

/* 20260331 -> 31.03.2026 */
function sdp_fmt_yyyymmdd($d, $format="d.m.Y"){
	$d = trim($d);
	if(strlen($d)!=8){ return $d; }
	$ts = strtotime(substr($d,0,4)."-".substr($d,4,2)."-".substr($d,6,2));
	if($ts===false){ return $d; }
	return date($format, $ts);
}

/* -------------------------------------------------------------- transport */
/* Same SSL handling as get_data_from_cserver(), but this OData V4 service is
   accessed with its own service user, and the curl info / error are handed
   back so &debug=1 can show what went wrong. */
function sdp_get_data($url_ck, &$info, &$err){
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$username  = "STARSAATHI";
	$password  = "Srikrishna@93933";

	/*$username  = "STARSAATHI2";
	$password  = "Krishna@9021";*/

	$ch_sheader = curl_init();
	curl_setopt($ch_sheader, CURLOPT_URL, $url_ck);
	curl_setopt($ch_sheader, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch_sheader, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch_sheader, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch_sheader, CURLOPT_USERPWD, "$username:$password");
	curl_setopt($ch_sheader, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch_sheader, CURLOPT_USERAGENT, $useragent);
	curl_setopt($ch_sheader, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch_sheader, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch_sheader, CURLOPT_HTTPHEADER, array("Accept: application/json"));

	$body_for_mcode = curl_exec($ch_sheader);
	$err  = curl_error($ch_sheader);
	$info = curl_getinfo($ch_sheader);
	curl_close($ch_sheader);

	return $body_for_mcode;
}

/* ------------------------------------------------------- call the service */
$the_filter = "Bukrs eq '".$the_company_code."' and Kunnr eq '".$the_customer_id."'"
			." and DateFrom eq '".$the_date_from."' and DateTo eq '".$the_date_to."'";

/* Other SAP calls in this application always pass the client explicitly.
   Use &sap_client=none in the URL to drop it while testing. */
$sap_client = isset($_REQUEST["sap_client"]) ? trim($_REQUEST["sap_client"]) : "900";

$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_V4_PORT_NO
		 .'/sap/opu/odata4/sap/zsd_sdeposit_customer/srvd_a2x/sap/zsd_sdeposit_cust/0001/SecurityDepositHeader'
		 .'?$filter='.rawurlencode($the_filter).'&$expand=_Items';

if($sap_client!="" && $sap_client!="none"){
	$url_ck1 .= '&sap-client='.rawurlencode($sap_client);
}

$curl_info = array();
$curl_err  = "";
$body_for_mcode10 = sdp_get_data($url_ck1, $curl_info, $curl_err);

$header_data = array();
$item_data   = array();

if(isJsonCk($body_for_mcode10)){
	$json_decoded21 = json_decode($body_for_mcode10, true);
	if(is_array($json_decoded21) && array_key_exists("value", $json_decoded21)){
		$app_results_arr = $json_decoded21["value"];
		if(is_array($app_results_arr) && count($app_results_arr)>0){
			$header_data = $app_results_arr[0];
			if(array_key_exists("_Items", $header_data) && is_array($header_data["_Items"])){
				$item_data = $header_data["_Items"];
			}
		}
	}
}

/* ----------------------------------------------------------- debug output */
/* security_deposit_pdf.php?bukrs=1010&debug=1  ->  show the raw exchange
   instead of the PDF, so gateway errors are visible. */
if(isset($_REQUEST["debug"]) && $_REQUEST["debug"]=='1'){
	ob_end_clean();
	header("Content-Type: text/plain; charset=utf-8");
	echo "REQUEST URL   : ".$url_ck1."\n";
	echo "FILTER (raw)  : ".$the_filter."\n";
	echo "HTTP STATUS   : ".(isset($curl_info["http_code"]) ? $curl_info["http_code"] : "n/a")."\n";
	echo "CONTENT TYPE  : ".(isset($curl_info["content_type"]) ? $curl_info["content_type"] : "n/a")."\n";
	echo "TOTAL TIME    : ".(isset($curl_info["total_time"]) ? $curl_info["total_time"] : "n/a")."\n";
	echo "CURL ERROR    : ".($curl_err!="" ? $curl_err : "(none)")."\n";
	echo "VALID JSON    : ".(isJsonCk($body_for_mcode10) ? "yes" : "no")."\n";
	echo "HEADER FOUND  : ".(count($header_data)>0 ? "yes" : "no")."\n";
	echo "ITEM COUNT    : ".count($item_data)."\n";
	echo "\n----- RAW RESPONSE BODY -----\n";
	echo $body_for_mcode10;
	exit;
}

/* ------------------------------------------------------------ page data */
$comp_name  = isset($header_data["CompName"]) ? $header_data["CompName"] : "";
$comp_addr  = isset($header_data["CompAddr"]) ? $header_data["CompAddr"] : "";
$cust_name  = isset($header_data["CustName"]) ? $header_data["CustName"] : $the_customer_nm;
$cust_addr  = isset($header_data["CustAddr"]) ? $header_data["CustAddr"] : "";
$gst_no     = isset($header_data["GstNo"])     ? $header_data["GstNo"]    : "";
$pan_no     = isset($header_data["PanNo"])     ? $header_data["PanNo"]    : "";
$email_id   = isset($header_data["EmailId"])   ? $header_data["EmailId"]  : "";
$statement  = isset($header_data["Statement"]) ? $header_data["Statement"]: "";
$close_bal  = isset($header_data["CloseBal"])  ? (float)$header_data["CloseBal"] : 0;

if($comp_name==""){
	$comp_name = ($the_company_code=='1017') ? "STAR CEMENT NORTH EAST LIMITED" : "STAR CEMENT LIMITED";
}
if($statement==""){
	$statement = "Security Deposit Statement of Account for the period "
			   .sdp_fmt_yyyymmdd($the_date_from)." to ".sdp_fmt_yyyymmdd($the_date_to);
}

$print_date = sdp_fmt_yyyymmdd($the_date_to, "M d, Y");

$logo_src  = "";
$logo_path = dirname(__FILE__)."/images/star-pdf-logo.jpeg";
if(file_exists($logo_path)){
	$logo_src = "data:image/jpeg;base64,".base64_encode(file_get_contents($logo_path));
}

/* ------------------------------------------------------------- build html */
$comp_block = "";
if(isset($company_static_arr[$the_company_code])){
	foreach($company_static_arr[$the_company_code]["lines"] as $cmp_line_val){
		$comp_block .= "<div class=\"cmp-line\">".$cmp_line_val."</div>";
	}
}elseif($comp_addr!=""){
	$comp_block .= "<div class=\"cmp-line\">".sdp_e($comp_addr)."</div>";
}

$letter_head = "<table class=\"lay\"><tr>"
	.($logo_src!="" ? "<td class=\"logo-cell\"><img src=\"".$logo_src."\" class=\"logo\" /></td>" : "")
	."<td class=\"cmp-cell\"><div class=\"cmp-name\">".sdp_e($comp_name)."</div>".$comp_block."</td>"
	.($logo_src!="" ? "<td class=\"logo-cell\">&nbsp;</td>" : "")
	."</tr></table>";

$party_block = "<table class=\"box party\">
	<tr>
		<td class=\"party-left\">
			<div><b>Customer Name :</b> ".sdp_e($cust_name)."</div>
			<div class=\"gap\"><b>Address</b> &nbsp;&nbsp;&nbsp;: ".sdp_e($cust_addr)."</div>
			<div class=\"gap\"><b>Customer Code :</b> ".sdp_e($the_customer_id)."</div>
			<div><b>Co Code</b> &nbsp;&nbsp;&nbsp;: ".sdp_e($the_company_code)."</div>
		</td>
		<td class=\"party-right\">
			<table class=\"lay\">
				<tr><td class=\"lbl\"><b>GST No</b></td><td class=\"val\"><b>: ".sdp_e($gst_no)."</b></td></tr>
				<tr><td class=\"lbl\"><b>PAN No</b></td><td class=\"val\"><b>: ".sdp_e($pan_no)."</b></td></tr>
				<tr><td class=\"lbl\"><b>Email Id</b></td><td class=\"val\"><b>: ".sdp_e($email_id)."</b></td></tr>
				<tr><td class=\"lbl\"><b>Date</b></td><td class=\"val\"><b>: ".sdp_e($print_date)."</b></td></tr>
			</table>
		</td>
	</tr>
</table>";

$statement_block = "<table class=\"box stmt\"><tr><td><b>".sdp_e($statement)."</b></td></tr></table>";

/* ------------------------------------------------------------ item table */
$tot_dr = 0; $tot_cr = 0; $tot_base = 0; $tot_tds = 0; $last_run_bal = 0;

$rows_html = "";
if(count($item_data)>0){
	foreach($item_data as $item_val){
		$budat      = isset($item_val["Budat"])      ? $item_val["Budat"]      : "";
		$doc_type   = isset($item_val["DocType"])    ? $item_val["DocType"]    : "";
		$doc_no     = isset($item_val["DocNo"])      ? $item_val["DocNo"]      : "";
		$dr_amt     = isset($item_val["DrAmt"])      ? (float)$item_val["DrAmt"]   : 0;
		$cr_amt     = isset($item_val["CrAmt"])      ? (float)$item_val["CrAmt"]   : 0;
		$run_bal    = isset($item_val["RunBal"])     ? (float)$item_val["RunBal"]  : 0;
		$base_val   = isset($item_val["BaseVal"])    ? (float)$item_val["BaseVal"] : 0;
		$tds        = isset($item_val["Tds"])        ? (float)$item_val["Tds"]     : 0;
		$particular = isset($item_val["Particular"]) ? $item_val["Particular"] : "";
		$gsber      = isset($item_val["Gsber"])      ? $item_val["Gsber"]      : "";

		$tot_dr   = $tot_dr + $dr_amt;
		$tot_cr   = $tot_cr + $cr_amt;
		$tot_base = $tot_base + $base_val;
		$tot_tds  = $tot_tds + $tds;
		$last_run_bal = $run_bal;

		$rows_html .= "<tr>
			<td class=\"c\">".sdp_e($budat)."</td>
			<td class=\"c\">".sdp_e($doc_type)."</td>
			<td class=\"c\">".sdp_e($doc_no)."</td>
			<td class=\"r\">".sdp_num($dr_amt)."</td>
			<td class=\"r\">".sdp_num($cr_amt)."</td>
			<td class=\"r\">".sdp_num($run_bal)."</td>
			<td class=\"r\">".sdp_num($base_val)."</td>
			<td class=\"r\">".sdp_sap_num($tds)."</td>
			<td class=\"c\">".sdp_e($particular)."</td>
			<td class=\"c\">".sdp_e($gsber)."</td>
		</tr>";
	}

	$rows_html .= "<tr class=\"tot\">
		<td class=\"c\" colspan=\"3\"><b>Total</b></td>
		<td class=\"r\"><b>".sdp_num($tot_dr)."</b></td>
		<td class=\"r\"><b>".sdp_num($tot_cr)."</b></td>
		<td class=\"r\"><b>".sdp_num($last_run_bal)."</b></td>
		<td class=\"r\"><b>".sdp_num($tot_base)."</b></td>
		<td class=\"r\"><b>".sdp_num($tot_tds)."</b></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>";
}else{
	$rows_html = "<tr><td class=\"c\" colspan=\"10\">No record found</td></tr>";
}

$item_table = "<table class=\"grid\">
	<thead>
		<tr>
			<th style=\"width:8%\">Date</th>
			<th style=\"width:5%\">Doc Type</th>
			<th style=\"width:9%\">Doc No.</th>
			<th style=\"width:9%\">DR. Amt(Rs)</th>
			<th style=\"width:9%\">CR. Amt(Rs)</th>
			<th style=\"width:10%\">Running Balance(Rs.)</th>
			<th style=\"width:9%\">Base Value</th>
			<th style=\"width:9%\">TDS</th>
			<th style=\"width:26%\">Particular</th>
			<th style=\"width:6%\">Business Area</th>
		</tr>
	</thead>
	<tbody>".$rows_html."</tbody>
</table>";

if(count($item_data)>0 && $close_bal==0){
	$close_bal = $last_run_bal;
}

$closing_block = "<table class=\"box closing\"><tr><td>
	<div><b>Closing Balance as on ".sdp_fmt_yyyymmdd($the_date_to)." Rs. ".sdp_sap_num($close_bal)."</b></div>
	<div class=\"gap\"><b>In words: ".sdp_e(sdp_amount_in_words($close_bal))."</b></div>
</td></tr></table>";

/* --------------------------------------------------------- confirmation */
$confirm_block = "
<div class=\"cnf-title\">(To be filled up by the Customer)</div>
<p class=\"cnf\">I / We hereby confirm that all transactions of our dealings with your company as shown above have been verified and reconciled.
The process of reconciliation has been completed and it is found that an amount of Rs.
<u>&nbsp;".sdp_num($close_bal)."&nbsp;</u> only as on <u>&nbsp;".sdp_e($print_date)."&nbsp;</u> is due and legally payable / receivable by me / us to / from your company
and I / we hereby undertake to pay / receive the same subject to claims mentioned herein below.</p>

<p class=\"cnf\">The Details of pending claims , if any , to be provided below , only incase of difference with Company balance</p>
<div class=\"dash\"></div>
<table class=\"lay cnf\">
	<tr><td style=\"width:40%\">No. Particulars</td><td style=\"width:20%\">Amounts</td><td style=\"width:20%\">Annex No.</td><td style=\"width:20%\">Remarks</td></tr>
</table>
<div class=\"dash\"></div>
<p class=\"cnf\">Pending Claims , on a/c of</p>
<div class=\"dash\"></div>
<p class=\"cnf\">
a) Schemes<br />
b) Pending Invoices (transit invoices , if any)<br />
c) Sales Return<br />
d) Unaccounted Payment<br />
e) Others If any (Specify Details)
</p>
<div class=\"dash\"></div>
<p class=\"cnf\">Total Pending Claims</p>
<p class=\"cnf\">I/We further acknowledge that we have received all the schemes/Incentives/claims except as mentioned above from ".sdp_e($comp_name)." till
<u>&nbsp;".sdp_e($print_date)."&nbsp;</u> and nothing is due as on date.</p>
<p class=\"cnf\">Yours Truely,</p>
<p class=\"cnf\">For &nbsp; <b>".sdp_e($cust_name)."</b></p>
<div class=\"sign-space\"></div>
<p class=\"cnf\">Name of Authorized Person<br />signature and Seal</p>";

/* ---------------------------------------------------------------- output */
$output = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
	@page { margin: 18px 16px; }
	body { font-family: Helvetica, Arial, sans-serif; font-size: 8px; color:#000; margin:0; padding:0; }
	table { border-collapse: collapse; width:100%; }
	table.lay, table.lay td { border:0; padding:0; }
	.page { border:1px solid #000; padding:10px; }
	.logo-cell { width:14%; vertical-align:top; }
	.logo { width:66px; }
	.cmp-cell { width:72%; text-align:center; vertical-align:top; }
	.cmp-name { font-size:15px; font-weight:bold; margin-bottom:4px; }
	.cmp-line { font-size:7.5px; line-height:1.5; }
	.box { border:1px solid #000; margin-top:8px; }
	.box td { border:0; padding:6px 8px; vertical-align:top; }
	.party .party-left { width:52%; border-right:1px solid #000; line-height:1.9; }
	.party .party-right { width:48%; line-height:1.9; }
	.party .lbl { width:34%; }
	.party .val { width:66%; }
	.gap { margin-top:2px; }
	.stmt td { text-align:center; padding:5px; }
	table.grid { margin-top:8px; }
	table.grid th, table.grid td { border:1px solid #000; padding:3px 2px; font-size:7px; word-wrap:break-word; }
	table.grid th { text-align:center; font-weight:bold; }
	table.grid td.c { text-align:center; }
	table.grid td.r { text-align:right; }
	table.grid tr.tot td { font-size:7.5px; }
	.closing td { line-height:1.9; }
	.cnf-title { text-align:center; font-size:8px; margin:10px 0 6px 0; }
	p.cnf, table.cnf { font-size:8px; line-height:1.7; margin:6px 0; }
	.dash { border-top:1px dashed #000; margin:4px 0; }
	.sign-space { height:60px; }
	.pbreak { page-break-before: always; }
</style></head><body>';

/* ---- page 1 : statement ---- */
$output .= '<div class="page">'
		. $letter_head
		. $party_block
		. $statement_block
		. $item_table
		. $closing_block
		. '</div>';

/* ---- page 2 : customer confirmation ---- */
$output .= '<div class="page pbreak">'
		. $letter_head
		. $party_block
		. $statement_block
		. $confirm_block
		. '</div>';

$output .= '</body></html>';

$the_file_name = "security_deposit_".$the_company_title."_".$the_customer_id."_".$the_date_from."_".$the_date_to.".pdf";

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', false);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($output);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

ob_end_clean();

/* download=1 forces the save dialog, otherwise it opens in the browser */
$force_download = (isset($_REQUEST["download"]) && $_REQUEST["download"]=='1') ? true : false;
$dompdf->stream($the_file_name, array('Attachment' => $force_download));
exit;
?>
