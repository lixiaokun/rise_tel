<?php
	require '../common/mysql.php';
	header("Content-Type:text/html;charset=utf-8");  
	session_start();
	$username = strtolower($_POST['TxtUserName']);
	$pwd = $_POST['TxtPassword'];
	if(strlen($username) > 0){
		$check = 'select id,user_js from rise_user where uname="'. $username . '" and pwd="' . $pwd . '"';
		$check_res = mysql_query($check);
		$row = mysql_fetch_row($check_res);
		if($row){
			$sessid = session_id();
			$_SESSION['login'] = $username;
			$_SESSION['uid'] = $row['0'];
			$_SESSION['user_js'] = $row['1'];
			$up = "update rise_user set `sessid`='$sessid',`login_date`='". date("Y-m-d",time()) ."' where id=$row[0]";
			mysql_query($up);
			header("Location:../index.php");
			
		}else{
			echo '<script>alert("密码错误！");window.location.href="./index.php"</script>';
			//header('Location:./index.php');
			die;
		}
	}else{
		header('Location:./index.php');
		die;
	}
?>