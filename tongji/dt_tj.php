<?php
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
 	require '../common/yey.php';
	header("Content-type: text/html; charset=utf-8"); 

	/**
	统计各个学校的各次地推日期、数量、总次数、总数量
	*/
	$sql = "select school,dt_date,count(no) as cou from data_base group by school,dt_date";
	$result = mysql_query($sql);
	$arr = array();
	while ($row = mysql_fetch_assoc($result)) {
		$school = $row['school'];
		$dt_date = $row['dt_date'];
		$cou = $row['cou'];
		$arr[$school][$dt_date] = $cou;
		$arr[$school]['total'] += $cou;
		$arr[$school]['cs'] += 1;
	}

	$trs = '';
	foreach ($arr as $key => $value) {
		$trs .= '<tr >';
		$trs .= "<td class='school'>$key</td>";
		$trs .= "<td >{$yey_sj[$key]['d']}</td>";
		$trs .= "<td >{$yey_sj[$key]['x']}</td>";
		$trs .= "<td>{$value['total']}</td>";
		$trs .= "<td class='detail'>{$value['cs']}</td>";
		$trs .= '</tr>';
	}
	if($_POST['ajax'] == 1){
		$ajax_school = $_POST['school'];
		$table = '<table border=1>';
		$table .= '<tr>';
		$table .= '<td>日期</td>';
		$table .= '<td>数量</td>';
		$table .= '</tr>';
		foreach ($arr[$ajax_school] as $key => $value) {
			if($key == 'total' || $key == 'cs')
				continue;
			$table .= '<tr>';
			$table .= "<td>{$key}</td><td>{$value}</td>";
			$table .= '</tr>';
		}
		$table .= '</table>';
		echo $table;
		die;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head> 
	<link href="../css/sta_tab.css" rel="stylesheet" type="text/css" />	 
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="-1" />	 
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script type="text/javascript" src='../My97DatePicker/WdatePicker.js'></script>
	<script type="text/javascript" src='../common/jquery.js'></script>
	<script type="text/javascript" src="../common/layer/layer.js"></script>
	<style type="text/css">
		body{text-align: center;}
		table{margin: 0 auto;}
	</style>
	<title>地推信息统计</title>
</head>
<body>	
	<form name="myform" action="cc_tj.php" method="POST">
		<table align="center" class="detailtab" cellspacing="1" id="querytable" style="width:80%;">
			<tr>
				<th colspan="6">查询</th>
			</tr>			
			<tr>
				<td>学校：<input type="text" name="school"></td>
			</tr>		
			<tr>
				<td class="bbtn" colspan="6">
					<input class="btn1" onclick="mysumt()" type="button" value="查&nbsp;&nbsp;询" />
						<input type="reset" id="querybtn" class="btn1" value="重&nbsp;&nbsp;置" />
				</td>
			</tr>
		</table>
	</form>
	<table class="listtab" cellspacing="1" align="center" style="width:80%">
		<tr class="title">
			<th rowspan="2">学校</th><th rowspan="2">放学时间(夏)</th><th rowspan="2">放学时间(冬)</th><th colspan="2">地推详情</th>
		</tr>
		<tr class="title">
			<th>数量</th><th>地推次数</th>
		</tr>
		<?php
			echo $trs;
		?>

		
	</table>
	<script type="text/javascript">
		$('.detail').click(function(){
			var school = '';
			school = $(this).parent('tr').find('.school').html();
			$.post('./dt_tj.php',{'ajax':'1','school':school},function(str){
				layer.closeAll();
				layer.open({
				    type: 1,
				    title:'地推详情',
				    area: ['500px', '300px'],
				    offset: '100px',
				    skin: 'layui-layer-demo', //样式类名
				    closeBtn: 1, //显示关闭按钮
				    shift: 2,
				    scrollbar: true,
				    shadeClose: true, //开启遮罩关闭
				    content: str,
				});
			});
			
		});

	</script>
</body>
</html>

