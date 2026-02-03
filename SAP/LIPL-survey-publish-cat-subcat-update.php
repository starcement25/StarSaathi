<?php
	ini_set('MAX_EXECUTION_TIME', -1);
	set_time_limit (0);
  	ini_set('memory_limit', '-1');

	$link = mysql_connect('localhost','acedns_dnsprod','dnsprod1234'); 
	mysql_select_db('acedns_LIPL', $link); 
	
	$sqlselallcatsubcat="SELECT row_id,value,survey_id FROM `survey_publish` WHERE row_id IN ('RA037', 'RA038', 'RA168', 'RA169')";
	$rsselallcatsubcat=mysql_query($sqlselallcatsubcat);
	while($rowselallcatsubcat=mysql_fetch_array($rsselallcatsubcat))
	{
		$row_id=$rowselallcatsubcat['row_id'];
		$value=$rowselallcatsubcat['value'];
		$survey_id=$rowselallcatsubcat['survey_id'];
		$value_replaced=str_replace('; ',';',$value);
		$value_explode=explode(';',$value_replaced);

		${catstring.$survey_id}='';
		${subcatstring.$survey_id}='';
		if($row_id=='RA037' || $row_id=='RA168' )
		{
			foreach($value_explode as $cat_val)
			{
				if($cat_val!='')
				{
					$sqlselcatid="SELECT cat_id FROM survey_category_master WHERE cat_name='".addslashes($cat_val)."'";
					$rsselcatid=mysql_query($sqlselcatid);
					$cntselcatid=mysql_num_rows($rsselcatid);
					if($cntselcatid >0)
					{
						$rowselcatid=mysql_fetch_array($rsselcatid);
						$cat_id=$rowselcatid['cat_id'];
						${catstring.$survey_id}=${catstring.$survey_id}.$cat_id.';';
					}
					else
					{
						${catstring.$survey_id}=${catstring.$survey_id}.$cat_val.';';
					}
			  }
			}
			${catstring.$survey_id}=substr(${catstring.$survey_id},0,-1);
			$sqlupdatesurveyoutputcat="UPDATE survey_publish SET value='".addslashes(${catstring.$survey_id})."' WHERE survey_id='".$survey_id."' 
									AND row_id='".$row_id."'";
			mysql_query($sqlupdatesurveyoutputcat);						
		}
		if($row_id=='RA038' || $row_id=='RA169' )
		{
			foreach($value_explode as $sub_cat_val)
			{
				if($sub_cat_val!='')
				{
					$sqlselsubcatid="SELECT sub_cat_id FROM survey_category_master WHERE sub_cat_name='".addslashes($sub_cat_val)."'";
					$rsselsubcatid=mysql_query($sqlselsubcatid);
					$cntselsubcatid=mysql_num_rows($rsselsubcatid);
					if($cntselsubcatid >0)
					{
						$rowselsubcatid=mysql_fetch_array($rsselsubcatid);
						$sub_cat_id=$rowselsubcatid['sub_cat_id'];
						${subcatstring.$survey_id}=${subcatstring.$survey_id}.$sub_cat_id.';';
					}
					else
					{
						${subcatstring.$survey_id}=${subcatstring.$survey_id}.$sub_cat_val.';';
					}
				}
			}
			${subcatstring.$survey_id}=substr(${subcatstring.$survey_id},0,-1);
			$sqlupdatesurveyoutputsubcat="UPDATE survey_publish SET value='".addslashes(${subcatstring.$survey_id})."' WHERE survey_id='".$survey_id."' 
									AND row_id='".$row_id."'";
			mysql_query($sqlupdatesurveyoutputsubcat);
		}
	}
	echo 'SUCCESS';
?>	