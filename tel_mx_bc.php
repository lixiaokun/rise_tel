<?php
	require './common/qx.php';
	require './common/yey.php';
	header('content-type:text/html;charset=utf-8');
	/**
	保存电话呢的具体信息 学生姓名 年龄 住址 英语程度等信息
	*/
	//将工作单位等基础信息保存到电话基础表
	// if(!empty($_POST['gzdw']) || !empty($_POST['relation']) || !empty($_POST['english']) || !empty($_POST['s_name']) || !empty($_POST['age']) || !empty($_POST['address']) || !empty($_POST['school'])){
	// 	$sql = "update data_base set age='$_POST[age]',s_name='$_POST[s_name]',gzdw='$_POST[gzdw]',relation='$_POST[relation]',english='$_POST[english]',address='$_POST[address]',school='$_POST[school]' where no=$no and tel=$tel";
	// 	mysql_query($sql);
	// }
	$z = trim($_POST['z']);
	$name = $_POST['name'];
	$no = $_POST['no'];
	$tel = $_POST['tel'];
	if(!empty($name) && !empty($no) && strlen($tel) == 11){
		$sql = "update data_base set {$name}='{$z}' where no='{$no}' and tel='{$tel}'";
		if(mysql_query($sql)){
			echo 'success';
		}else{
			echo 'fail';
		}
		die;
	}
?>