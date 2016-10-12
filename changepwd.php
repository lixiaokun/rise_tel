<?php
	require './common/qx.php';
	if(!empty($_POST['pwd']) && !empty($_POST['n_pwd1'])){

		$uname = $_SESSION['login'];
		$check = "select pwd from rise_user where uname='$uname'";
		$res = mysql_query($check);
		$row = mysql_fetch_row($res);
		$pwd = $row[0];
		if($pwd != $_POST['pwd']){
			echo '<script>alert("原密码输入错误");location.href="changepwd.php";</script>';
			die; 
		}
		$sql = "update rise_user set pwd='$_POST[n_pwd1]' where uname='$uname' and pwd='$_POST[pwd]'";
		mysql_query($sql);
		if(mysql_affected_rows()){
			echo '<script>alert("修改成功");location.href="changepwd.php";</script>';
			die;
		}
		header("Location:changepwd.php");
	}
?>
<html>
	<head>
		<meta http-equiv="content-type" content="html;charset=utf-8">
		<script type="text/javascript" src="./common/jquery.js"></script>
		<style>
			body{
				text-align: center;				
			}
			div{width:500px;margin-left: 25%}
			div input{
				margin :5px auto;
			}
			div div{margin:0 auto;}
		</style>
	</head>
	<body>
		<div>
			<form action="changepwd.php" method="post">
				<fieldset>
					<legend>修改密码</legend>
					<div>用　户　名：<?php echo $_SESSION[login];?>　　　　　　　</div>
					<div>旧　密　码：<input type="password" name="pwd"></div>
					<div>新　密　码：<input type="password" name="n_pwd" id='n_pwd'></div>
					<div>确认新密码：<input type="password" name="n_pwd1" id='n_pwd1'></div>
					<div><input type='submit' value='确认' id='sub'><input type='reset' value="重置"></div>
				</fieldset>
			</form>
		</div>
		<script type="text/javascript">
		$('#n_pwd1').blur(function(){
			var n_pwd=n_pwd1='';
			n_pwd = $('#n_pwd').val();
			n_pwd1 = $('#n_pwd1').val();
			if(n_pwd1 != n_pwd){
				alert('两次输入新密码不一样');
				$('#sub').attr('disabled','1');
				return
			}else{
				$('#sub').removeAttr('disabled');
				return
			}
		});
		</script>
	</body>
</html>