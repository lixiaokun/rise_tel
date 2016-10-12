<?php
	$con = mysql_connect("localhost","root","123456");
	mysql_select_db('rise_tel');
	mysql_query("set names utf8");
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}