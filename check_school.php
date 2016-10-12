<?php
	require './common/qx.php';
	require "./common/yey.php";
	error_reporting(0);
	$school = trim($_POST['school']);
	if(in_array($school,$yey)){
		echo 'ok';
	}else{
		echo 'no';
	}
	die;
?>