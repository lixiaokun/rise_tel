<?php
	session_start();
	error_reporting(0);
	require "common/mysql.php";
	$username = "select uname from rise_user";
	$res = mysql_query($username);
	$u_arr = array();
	while($row = mysql_fetch_assoc($res)){
		$u_arr[] = $row['uname'];
	}
	if(!in_array($_SESSION['login'],$u_arr)){
		header("Location:login/index.php");
	}	
?>