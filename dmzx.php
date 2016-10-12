<?php
/**
	保存当面咨询内容 按照电话 demoid存入data_mx表
*/
	require "./common/qx.php";
	header("Content-Type:text/html;charset=utf8");
	$dmzx = trim($_POST['dmzx']);
	$demoid = $_POST['demoid'];
	$tel = $_POST['tel'];
	$no = $_POST['no'];
	//print_r($_POST);
	if($_POST['action'] == 'ajax' && is_numeric($no)){
		$sql = "select dmzx from tel_bd where telno=$no and demoid=$demoid";
		$res = mysql_query($sql);
		$row = mysql_fetch_row($res);
		echo $row[0];
		die;
	}
	if(!empty($dmzx) && $demoid && strlen($tel)){
		$query = "update tel_bd set dmzx='$dmzx' where  demoid=$demoid and telno='$no'";
		if(mysql_query($query)){
			echo '<script>alert("保存成功！");location.href="demo_xy.php?demoid='.$demoid.'"</script>';
		}
	}
	//echo '<script>location.href="demo_xy.php?demoid='.$demoid.'"</script>';
