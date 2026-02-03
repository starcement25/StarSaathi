<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('memory_limit', '2G');
//echo"<pre>";print_r('ss');die;
include "star_connection.php";
//echo"<pre>";print_r('ss2');die;

include "function-sfa.php";
//echo"<pre>";print_r('ss1');die;
//$SAP_customer_code='1000000404';
$SAP_customer_code='1000002064';
//$SAP_customer_code='1000001726';
$sqldealeremp="SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND customer_code='".$SAP_customer_code."'";
//echo"<pre>";print_r($sqldealeremp);die;

		$rsdealeremp=mysql_query($sqldealeremp);
		$email_hierarchy='';
while($rowdealeremp=mysql_fetch_array($rsdealeremp))
        {
            $emp_code_db=$rowdealeremp['emp_code'];
            $employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code_db);
//echo"<pre>";print_r($employee_upper_hierarchy);die;

        //$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';
         echo   $sqlemailhierarchy="SELECT email,designation FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.") AND UPPER(sale_access)='PRIMARY' AND acedns='Y'";
            $rsemailhierarchy=mysql_query($sqlemailhierarchy);
            while($rowemailhierarchy=mysql_fetch_array($rsemailhierarchy))
            {
                if($rowemailhierarchy['email']!='')
                {
                $email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';
                echo $email_hierarchy.'<BR>';
                }
            }
        }

        $sqlbroker="SELECT broker_code FROM customer_broker_relation WHERE acedns='Y' AND customer_code='".$SAP_customer_code."'";
        $rsbroker=mysql_query($sqlbroker);
        while($rowbroker=mysql_fetch_array($rsbroker))
        {
            $sqlemailbroker="SELECT mail_id FROM broker_master WHERE broker_id='".$rowbroker['broker_code']."' AND acedns='Y'";
            $rsemailbroker=mysql_query($sqlemailbroker);
            while($rowemailbroker=mysql_fetch_array($rsemailbroker))
            {
                if($rowemailbroker['mail_id']!=''){
                $email_broker=$email_broker.$rowemailbroker['mail_id'].',';
                }
            }
        }

      echo  $final_email=substr($email_hierarchy,0,-1).','.substr($email_broker,0,-1).','.'samirdas@starcement.co.in,antarabanerjee@starcement.co.in,pratipbhunia@starcement.co.in';

