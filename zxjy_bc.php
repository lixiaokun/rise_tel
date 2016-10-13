<?php
/**
	ajax保存咨询纪要
*/
	require './common/qx.php';
	require './common/yey.php';
	header('content-type:text/html;charset=utf-8');
	$zxjy = trim($_POST['zxjy']);
	// $status = $_POST['status'];
	$tid = $_POST['tid'];
	$againTime = $_POST['againTime'];
	// if(is_numeric($tid) && !empty($zxjy)){
	// 	if($status == 8){
	// 		if(empty($againTime)){
	// 			echo 'againTime';
	// 			die;
	// 		}
	// 		$query = "update tel_bd set zxjy='$zxjy',status='$status',again_time='$againTime' where id=$tid";
	// 	}else{
	// 		$query = "update tel_bd set zxjy='$zxjy',status='$status' where id=$tid";
	// 	}
	$query = "update tel_bd set zxjy='$zxjy' where id=$tid";
		mysql_query($query);
		if(mysql_affected_rows()){
			echo 'success'; 
		}else{
			echo 'fail';
		}
		die;
	// }