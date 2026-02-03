<?php
if($remote_db_access=='yes' && $mode!='SETUP')
	{
		$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error.");
		mysql_select_db(DBREMOTE,$link) or die("could not connect the database for invalid nick names");
	}
	else
	{
		$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
		mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	}


