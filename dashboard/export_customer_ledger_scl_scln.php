<?php
set_time_limit(0);
	ini_set('memory_limit', '-1');

include "web_check.php";
include "star_connection.php";


$the_start_date = $_GET["the_start_date"] ? addslashes(trim($_GET["the_start_date"])) :'';
$the_end_date = $_GET["the_end_date"] ? addslashes(trim($_GET["the_end_date"])) : "";
$the_company_code = $_GET["the_company_code"] ? addslashes(trim($_GET["the_company_code"])) : "";
$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

//echo $sswa_selected_customer_code=$_GET["customer_code"];

$curr_date = date("m/d/Y");
function sort_by_date($a, $b) {
    $a = strtotime($a['Bldat']);
    $b = strtotime($b['Bldat']);
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
	//return strtotime($a) - strtotime($b);
}

if($the_start_date=='' && $the_end_date=='')
		{
		$the_start_date_time = date('Y-m-d', strtotime("-7 days,$curr_date"))."T00:00:00";
		$the_end_date_time = $curr_date."T00:00:00";
		}
		else
		{
			$the_start_date_time=date('Y-m-d', strtotime($the_start_date))."T00:00:00";
			$the_end_date_time=date('Y-m-d', strtotime($the_end_date))."T00:00:00";
		}

$customer_master='customer_master';

$ledger_total_balance = 0;
$ledger_link = "";

$sql3 = "select `dns_customer_code`,`customer_id`,customer_name from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = trim($row3["dns_customer_code"]);
$customer_name = trim($row3["customer_name"]);
$the_customer_id = trim($row3["customer_id"]);
}else{
$the_customer_id = "";
}

$output = "<html><body><div  class=\"container\" style=\"width:100%;\"><img src=\"images/star-pdf-logo.jpeg\" alt=\"Star\" width=\"100px\" style=\"position:absolute; top:0px;\"/><h3 style=\"text-align:center;\">STAR CEMENT LIMITED</h3><h4 style=\"text-align:center;\">COMPANY WISE LEDGER</h4><p style=\"text-align:center;\">Period from ".$the_start_date." To ".$the_end_date."</p> <p><strong>Party Code</strong> : ".$the_customer_id." </p><p><strong>Party Name</strong> : ".$customer_name."</p><table style=\"width:100%; text-align:left;border:1px solid #333; \"><thead>

        <tr style=\"height: 50px;\">
          <th style=\"border-left: 1px solid #dee2e6; padding:5px;  width:15%\"><b>COMPANY</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:15%\"><b>VOUCHERDT</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:20%\"><b>PARTICULARS</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:10%\"><b>QTY</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:15%\"><b>AMTDR(Rs.)</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:10%\"><b>AMTCR(Rs.)</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:15%\"><b>BALANCE(Rs.)</b></th>
        </tr>
      </thead>
      <tbody>";

if($the_company_code=='') $the_company_code='1010';
	$the_filter = '&$filter=(Bukrs eq \''.$the_company_code.'\' and Kunnr eq \''.$the_customer_id.'\' and ( Bldat ge datetime\''.$the_start_date_time.'\' and Bldat le datetime\''.$the_end_date_time.'\') )';

$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZFI_CUST_LEDGER_ODATA_SRV/ZFI_CUST_LEDGER_STSet?$format=json'.str_replace(" ","%20",$the_filter).'&sap-client=900';

$body_for_mcode10 = get_data_from_cserver($url_ck1);
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
	//print_r($app_results_arr);
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$kunnr = $app_results_aval["kunnr"];
		$Butxt = $app_results_aval["Butxt"];
		$name1 = $app_results_aval["name1"];
		$Belnr = $app_results_aval["Belnr"];
		$Belnr2 = $app_results_aval["Belnr2"];
		$Ltext = $app_results_aval["Ltext"];
		$Bldat = $app_results_aval["Bldat"];
		$quayntity = $app_results_aval["Menge"];
		$CrAmount = $app_results_aval["CrAmount"];
		$DrAmount = $app_results_aval["DrAmount"];
		$Balance = $app_results_aval["Balance"];
		$OpBalance = $app_results_aval["OpBalance"];
		
		$Bldat_format = "";
		if($Bldat!=""){
		$Bldat_str = str_replace("/","",$Bldat);
		$Bldat_str = str_replace("Date","",$Bldat_str);	
		$Bldat_str = str_replace("(","",$Bldat_str);
		$Bldat_str = str_replace(")","",$Bldat_str);
		$Bldat_str = ($Bldat_str / 1000);
		//$DocDate_format = date("m/d/Y h:i:s A",$DocDate_str);
		$Bldat_format = date("Y-m-d",$Bldat_str);
		}
		
		//if($OpBalance > 0)
		if($OpBalance !='0.000')
		{
			$particulars='opening';
			$Balance=$OpBalance;
		}
		else
		{
			$particulars=$Belnr.' / '.$Belnr2.'<br />'.$Ltext;
			$Balance=$CrAmount-$DrAmount;
		}
		$total_balance=$total_balance+$Balance;
		
		$customer_ledger_data[]= array("Butxt"=>$Butxt,"Bldat"=>$Bldat_format,"particulars"=>$particulars,"quayntity"=>$quayntity,"CrAmount"=>$CrAmount,"DrAmount"=>$DrAmount,"total_balance"=>$total_balance);
		
	}
	//print_r($credit_note_data);
	//usort($customer_ledger_data, 'sort_by_date');
	foreach($customer_ledger_data as $customer_ledger_data_val)
	{
		$Butxt = $customer_ledger_data_val["Butxt"];
		$Bldat = $customer_ledger_data_val["Bldat"];
		$particulars = $customer_ledger_data_val["particulars"];
		$quayntity = $customer_ledger_data_val["quayntity"];
		$CrAmount = $customer_ledger_data_val["CrAmount"];
		$DrAmount = $customer_ledger_data_val["DrAmount"];
		$total_balance = $customer_ledger_data_val["total_balance"];
		
		$Bldat_format = date("d/m/Y",strtotime($Bldat));
		if($Butxt=='SCNE') $Butxt='SCNEL';
		
		$output .= "<tr>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:15%\">".$Butxt."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:15%\">".$Bldat_format."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:20%\">".$particulars."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:10%\">".number_format($quayntity,2)."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:15%\">".number_format($CrAmount,2)."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:10%\">".number_format($DrAmount,2)."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:15%\">".number_format($total_balance,2)."</td>
        </tr>";
		$total_qty=$total_qty+$quayntity;
		$total_CrAmount=$total_CrAmount+$CrAmount;
		$total_DrAmount=$total_DrAmount+$DrAmount;
		//$final_balance=$final_balance+$total_balance;
		
	  }
   }
  }
 }
 $output .="</tbody>";
 $output .= "<tfoot><tr>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:15%\"><b>Total</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:15%\"></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:20%\"></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:10%\"><b>".number_format($total_qty,2)."</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:15%\"><b>".number_format($total_CrAmount,2)."</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:10%\"><b>".number_format($total_DrAmount,2)."</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:15%\"><b>".number_format($total_balance,2)."</b></td>
        </tr></tfoot>";
 $output .= "</table></div></body></html>";
}
else
{
	$output .= "<tr><td colspan=\"13\">No Records<td></td></tr></table></body></html>";
}

creditnotePDFfile($output);
function creditnotePDFfile($output){
	$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "customer_ledger_".$curr_date.".pdf";
//$output .='"'.$booking_id.'","'.$booking_date.'","'.$booking_slot_timing.'","'.$facility_tee_type.'","'.$booking_status.'","'.$primary_member_id.'","'.$primary_member_name.'","'.$primary_member_mobile.'","'.$pair_member2_id.'","'.$pair_member2_name.'","'.$pair_member2_mobile.'","'.$pair_member3_id.'","'.$pair_member3_name.'","'.$pair_member3_mobile.'","'.$pair_member4_id.'","'.$pair_member4_name.'","'.$pair_member4_mobile.'","'.$carts_needed.'","'.$caddies_needed.'","'.$release_choice.'","'.$created_date.'"';
//$output .="\n";
require_once('tcpdf/tcpdf.php');
		//define ('PDF_MARGIN_RIGHT', 4);
$pdf=new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetPrintHeader(false);
		$pdf->SetPrintfooter(false);
		$pdf->SetTopMargin(0);
		//$pdf->SetLeftMargin(0);
$pdf->SetFont('helvetica', '', 9);
$pdf->AddPage('L',"A4");
$pdf->writeHTML($output);
//Use 'D' for download
$tcpdf=$pdf->Output($the_file_name, 'D');
	
if($conn!=""){
mysqli_close($conn);
}
exit;
}
?>