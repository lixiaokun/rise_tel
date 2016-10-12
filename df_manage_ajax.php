<?php

	require './common/qx.php';
	header("Content-Type:text/html;charset=utf8");
	/**
	处理修改报名状态
	*/
	$action = $_POST['action'];
	$tel = $_POST['tel'];
	$noo = $_POST['noo'];
	$demoid = $_POST['demoid'];
	$zt = $_POST['zt'];
	if(!in_array($action,array('daofang','baoming'))){
		echo 'action error!';
		die;
	}
	if(strlen($tel) != 11 || !is_numeric($noo) || !is_numeric($demoid)){
		echo 'data error!';
		die;
	}
	if($action == 'baoming' && in_array($zt, array('dj','qf','tf','wbm'))){
		$zt_arr = array('dj' => 4,'qf' => 5,'tf' => 6,'wbm' => 3);
		$today = date('Y-n-j',time());
		//先判断是否到访 没有到访的话不能报名
		$query = "select sm_status from data_mx where telno='$tel' and demoid=$demoid";
		$res = mysql_query($query);
		$row = mysql_fetch_row($res);
		if(!$row[0]){
			echo 'wdf';
			die;
		}
		//修改最近一次咨询纪要的状态 以及 data_base的最终状态  data_mx中的状态
		$sql_1 = "update data_base set tel_status='$zt_arr[$zt]' where tel=$tel";
		$sql_2 = "update tel_bd set status='$zt_arr[$zt]' where telno=$noo order by UNIX_TIMESTAMP(bd_date) DESC limit 1";
		if($zt == 'wbm'){
			$sql_3 = "update data_mx set bm_status='',bm_date='' where telno=$tel and demoid=$demoid";
		}else{
			$sql_3 = "update data_mx set bm_status='$zt_arr[$zt]',bm_date='$today' where telno=$tel and demoid=$demoid";
		}
		if(mysql_query($sql_1) && mysql_query($sql_2) && mysql_query($sql_3)){
			echo 'success';
		}else{
			echo 'fail';
		}
		die;
	}
	if($action == "daofang"){
		//处理到访
		$today = date('Y-n-j',time());
		$query = "update data_mx set sm_status=not sm_status,sm_date=if(sm_status=1,'$today','') where telno='$tel' and demoid='$demoid'";
		mysql_query($query);
		//2 和 3 切换 到访3 删除到访2
		$query1 = "update data_base set tel_status=if(tel_status=2,3,2) where tel='$tel'";
		mysql_query($query1);
		//删除报名状态
		$query2 = "update data_mx set bm_status=0,bm_date='' where telno='$tel' and demoid='$demoid' and sm_status=0";
		if(mysql_query($query2) && !empty($noo)){
		//删除到访后将咨询历史中的状态改回承诺上门  
			$query3 = "update tel_bd set status=2 where telno='$noo' and demoid='$demoid'";
			mysql_query($query3);
		}
		echo "success";
		die;
	}