<html>
<head>
<title> Non-Seamless-kit</title>
</head>
<body onLoad="submitCCForm()">
<center>

<?php include('Crypto.php')?>
<?php 

	error_reporting(0);
	//$orderId = "test_".time();
	$merchant_data='';
	$working_key='3D1F16BEE3CF170414FEB722F34A00E2';//Shared by CCAVENUES
	$access_code='AVCC90HB71CA21CCAC';//Shared by CCAVENUES
	
	foreach ($_POST as $key => $value){
		$merchant_data.=$key.'='.$value.'&';
	}
//$merchant_data .= "order_id=".$orderId;
	$encrypted_data=encrypt($merchant_data,$working_key); // Method for encrypting the data.
//https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction
//https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction
?>
<form method="post" name="redirect" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction"> 
<?php
echo '<input type=hidden name="encRequest" value="'.$encrypted_data.'">';
echo '<input type=hidden name="access_code" value="'.$access_code.'">';
?>
</form>
</center>
<script language='javascript'>
function submitCCForm() {
var CCForm = document.forms.redirect;
CCForm.submit();
}
</script>
</body>
</html>

