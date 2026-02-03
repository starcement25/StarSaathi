<?php
//error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set("Asia/Kolkata");
$servername = "localhost";
$username = "starsaat_dnsprod";
$password = "dnsprod1234#";
$db_name = "starsaat_START";

$conn = mysql_connect($servername, $username, $password);
if(!$conn){
   die('Could not connect: ' . mysql_error());
}

$db_selected = mysql_select_db($db_name, $conn);
if (!$db_selected) {
    die ('Can\'t connect to database : ' . mysql_error());
}
function olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,$page_qury_stn_ky_nm,$filtered_query_string)
{
$pagination = "";
if($page_qury_stn_ky_nm!="page"){
$page_query_string_name = $page_qury_stn_ky_nm;
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($page > 1) 
			$pagination.= "<a href=\"$targetpage?$page_query_string_name=$prev$filtered_query_string\">PREV</a>";
		else
			$pagination.= "<span class=\"disabled_pg\">PREV</span>";
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<span class=\"current_pg\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage?$page_query_string_name=$counter$filtered_query_string\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current_pg\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?$page_query_string_name=$counter$filtered_query_string\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=$lpm1$filtered_query_string\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=$lastpage$filtered_query_string\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=1$filtered_query_string\">1</a>";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=2$filtered_query_string\">2</a>";
				$pagination.= "...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current_pg\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?$page_query_string_name=$counter$filtered_query_string\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=$lpm1$filtered_query_string\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=$lastpage$filtered_query_string\">$lastpage</a>";	
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=1$filtered_query_string\">1</a>";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=2$filtered_query_string\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current_pg\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?$page_query_string_name=$counter$filtered_query_string\">$counter</a>";					
				}
			}
		}
		//next button
		if ($page < $counter - 1)
			$pagination.= "<a href=\"$targetpage?$page_query_string_name=$next$filtered_query_string\">NEXT</a>";
		else
			$pagination.= "<span class=\"disabled_pg\">NEXT</span>";
		$pagination.= "</div>\n";
	}
}else{
	$pagination.="Dont use query string key=page";
}
return $pagination;
}

?>