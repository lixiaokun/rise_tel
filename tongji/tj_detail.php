<?php
	/**
	本页面用于显示cc各渠道统计数字的具体内容	
	*/
	/**
	权限检查开始
	*/
	session_start();
	error_reporting(0);
	require "../common/mysql.php";
	$username = "select uname from rise_user";
	$res = mysql_query($username);
	$u_arr = array();
	while($row = mysql_fetch_assoc($res)){
		$u_arr[] = $row['uname'];
	}
	if(!in_array($_SESSION['login'],$u_arr)){
		header("Location:../login/index.php");
	}	
 	/**
 	权限检查结束
 	*/
 	require "../common/yey.php";
	$qd = $_GET['qd'];
	$class = $_GET['class'];
	$fp_name = $_GET['fp_name'];
	if(!in_array($qd, array('dt','callin','walkin','zjs','hd','qd')) || !in_array($class,array('zxl','cn','sj','gd')) || !in_array($fp_name, $cc)){
		echo "参数错误!";
		die;
	}
	$qd_arr = array('dt' => '地推',
		'callin' => 'call in',
		'walkin' => 'walk in',
		'zjs' => '转介绍',
		'hd' => '活动',
		'qd' => '渠道'
		);
	$qudao = $qd_arr[$qd];
	$uid_arr = array_flip($cc);
	$uid = $uid_arr[$fp_name];
	$startdate = $_GET['s_date'];
	$enddate = $_GET['e_date'];
	switch ($class) {
		case 'zxl':
			//渠道咨询量
			$sql = "select DISTINCT b.tel,b.s_name,b.school,t.bd_date from data_base as b 
			join tel_bd as t on b.no=t.telno  
			join rise_user as r on r.id=b.fp_name 
			where UNIX_TIMESTAMP(t.bd_date)>=UNIX_TIMESTAMP('$startdate') and UNIX_TIMESTAMP(t.bd_date)<=UNIX_TIMESTAMP('$enddate')";
			break;
		case 'cn':
			//承诺上门
			$sql = "select DISTINCT b.tel,b.s_name,b.school,t.bd_date from data_base as b 
			join tel_bd as t on b.no=t.telno  
			join rise_user as r on r.id=b.fp_name 
			where UNIX_TIMESTAMP(t.bd_date)>=UNIX_TIMESTAMP('$startdate') and UNIX_TIMESTAMP(t.bd_date)<=UNIX_TIMESTAMP('$enddate') and (b.tel_status=2 or b.tel_status=9 or b.tel_status=3 or b.tel_status=4 or b.tel_status=5 or b.tel_status=6)";
			break;
		case 'sj':
			//实际上门
			$sql = "select DISTINCT b.tel,b.s_name,b.school,t.bd_date from data_base as b 
			join tel_bd as t on b.no=t.telno  
			join rise_user as r on r.id=b.fp_name 
			where UNIX_TIMESTAMP(t.bd_date)>=UNIX_TIMESTAMP('$startdate') and UNIX_TIMESTAMP(t.bd_date)<=UNIX_TIMESTAMP('$enddate') and (b.tel_status=3 or b.tel_status=4 or b.tel_status=5 or b.tel_status=6)";
			break;
		case 'gd':
			//实际关单
			$sql = "select b.tel,b.s_name,b.school,t.bd_date from data_base as b 
			join tel_bd as t on b.no=t.telno  
			join rise_user as r on r.id=b.fp_name 
			where UNIX_TIMESTAMP(t.bd_date)>=UNIX_TIMESTAMP('$startdate') and UNIX_TIMESTAMP(t.bd_date)<=UNIX_TIMESTAMP('$enddate') and (b.tel_status=4 or b.tel_status=5 )";
			break;
	}		
	$sql .= " and b.qudao='$qudao' ";
	$sql .= " and t.uid='$uid' ";
	$sql .= " order by UNIX_TIMESTAMP(bd_date) DESC,b.s_name DESC";

	$result = mysql_query($sql);
	$tds = '';
	$xh = 1;
	while($row = mysql_fetch_assoc($result)){
		$tds .= '<tr>';
		$tds .= '<td>'. $xh .'</td>';
		$tds .= '<td>'. $row['bd_date'] .'</td>';
		$tds .= '<td>'. $row['tel'] .'</td>';
		$tds .= '<td>'. $row['s_name'] .'</td>';
		$tds .= '<td>'. $row['school'] .'</td>';
		$tds .= '</tr>';
		$xh++;
	}
?>
<html>
	<head>
		<title>数据详情</title>
		<link href="../css/sta_tab.css" rel="stylesheet" type="text/css" />	 
		<style type="text/css">
			body{text-align: center;}
			table{margin: 0 auto;}
		</style>
	</head>
	<body>
		<table class="listtab" cellspacing="1" align="center" id="listtab" style="width:80%">
			<tr style="background:red">
				<th>序号</th>
				<th>咨询时间</th>
				<th>电话</th>
				<th>学生姓名</th>
				<th>学校</th>
			</tr>
			<?php
				echo $tds;
			?>
		</table>
	</body>
</html>