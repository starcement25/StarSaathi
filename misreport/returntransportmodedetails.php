<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	$type=$_REQUEST['type'];
	if($type=='transportmode'){
	$plant_name=$_REQUEST['plant_name'];
	
	$content='<select name="transport_mode" id="transport_mode" onChange="javascript:load_capacity();">';
	$content.='<option value="">SELECT</option>';
    $sqltransportmode="SELECT DISTINCT transport_mode FROM plantwise_load_capacity WHERE plant_name='".$plant_name."' ORDER BY transport_mode ASC";
	$rstransportmode=mysql_query($sqltransportmode);
	while($rowtransportmode=mysql_fetch_array($rstransportmode))
	{		
		$content.="<option value='".$rowtransportmode['transport_mode']."'>".$rowtransportmode['transport_mode']."</option>";
	}
	$content.='</select>';
	}
	if($type=="loadcapacity"){
		//$plant_name=$_REQUEST['plant_name'];
		$transport_mode=$_REQUEST['transport_mode'];
		
		$content='<select name="truck_load_distribution" id="truck_load_distribution" onChange="javascript:previous_cost();">';
		$content.='<option value="">SELECT</option>';
		/*$sqlloadcapacity="SELECT DISTINCT load_capacity FROM plantwise_load_capacity WHERE plant_name='".$plant_name."' 
							AND transport_mode='".$transport_mode."' ORDER BY load_capacity ASC";*/
		$sqlloadcapacity="SELECT DISTINCT load_capacity FROM plantwise_load_capacity WHERE transport_mode='".$transport_mode."' ORDER BY load_capacity ASC";					
		$rsloadcapacity=mysql_query($sqlloadcapacity);
		while($rowloadcapacity=mysql_fetch_array($rsloadcapacity))
		{		
			$content.="<option value='".$rowloadcapacity['load_capacity']."'>".$rowloadcapacity['load_capacity']."</option>";
		}
		$content.='</select>';

	}
	if($type=="loadcapacityfreight"){
		$plant_name=$_REQUEST['plant_name'];
		$transport_mode=$_REQUEST['transport_mode'];
		
		$content='<select name="truck_load" id="truck_load" onChange="javascript:previous_truckload_hirecost();">';
		$content.='<option value="">SELECT</option>';
		$sqlloadcapacity="SELECT DISTINCT load_capacity FROM plantwise_load_capacity WHERE plant_name='".$plant_name."' 
							AND transport_mode='".$transport_mode."' ORDER BY load_capacity ASC";
		$rsloadcapacity=mysql_query($sqlloadcapacity);
		while($rowloadcapacity=mysql_fetch_array($rsloadcapacity))
		{		
			$content.="<option value='".$rowloadcapacity['load_capacity']."'>".$rowloadcapacity['load_capacity']."</option>";
		}
		$content.='</select>';
	}
	if($type=='honeycombcost'){
		$plant_name=$_REQUEST['plant_name'];
		
		$content='<select name="transport_mode" id="transport_mode" onChange="javascript:previous_honey_comb_cost();">';
		$content.='<option value="">SELECT</option>';
		$sqltransportmode="SELECT DISTINCT transport_mode FROM plantwise_load_capacity WHERE plant_name='".$plant_name."' ORDER BY transport_mode ASC";
		$rstransportmode=mysql_query($sqltransportmode);
		while($rowtransportmode=mysql_fetch_array($rstransportmode))
		{		
			$content.="<option value='".$rowtransportmode['transport_mode']."'>".$rowtransportmode['transport_mode']."</option>";
		}
		$content.='</select>';
	}
	echo $content;
	mysql_close($link);
?>