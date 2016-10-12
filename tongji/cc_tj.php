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
 	cc名单
 	*/
	$cc_list = '<option value="">请选择</option>';
	foreach ($cc as $key => $value) {
		if($_POST['cc'] == $key)
			$cc_list .= '<option value="'. $key .'" selected=1>'. $value .'</option>';
		else
			$cc_list .= '<option value="'. $key .'">'. $value .'</option>';
	}
	/**
 	qudao名单
 	*/
	$qd_list = '<option value="">请选择</option>';
	foreach ($qudao as $key => $value) {
		if($_POST['qudao'] == $value)
			$qd_list .= '<option value="'. $value .'" selected=1>'. $value .'</option>';
		else
			$qd_list .= '<option value="'. $value .'">'. $value .'</option>';
	}

	if(!empty($_POST['startdate']) && !empty($_POST['enddate']) && strtotime($_POST['startdate']) <= strtotime($_POST['enddate'])){
		$startdate = $_POST['startdate'];
		$enddate = $_POST['enddate'];
		$qudao = $_POST['qudao'];
		//渠道咨询量
		$sql = "select r.uname,b.qudao,count(DISTINCT b.no) as total from data_base as b 
		join tel_bd as t on b.no=t.telno  
		join rise_user as r on r.id=b.fp_name 
		where UNIX_TIMESTAMP(t.bd_date)>=UNIX_TIMESTAMP('$startdate') and UNIX_TIMESTAMP(t.bd_date)<=UNIX_TIMESTAMP('$enddate')";
		//承诺上门
		$sql1 = "select r.uname,b.qudao,count(DISTINCT b.no) as total from data_base as b 
		join tel_bd as t on b.no=t.telno  
		join rise_user as r on r.id=b.fp_name 
		where UNIX_TIMESTAMP(t.bd_date)>=UNIX_TIMESTAMP('$startdate') and UNIX_TIMESTAMP(t.bd_date)<=UNIX_TIMESTAMP('$enddate') and (b.tel_status=2 or b.tel_status=9 or b.tel_status=3 or b.tel_status=4 or b.tel_status=5 or b.tel_status=6)";
		//实际上门
		$sql2 = "select r.uname,b.qudao,count(DISTINCT b.no) as total from data_base as b 
		join tel_bd as t on b.no=t.telno  
		join rise_user as r on r.id=b.fp_name 
		where UNIX_TIMESTAMP(t.bd_date)>=UNIX_TIMESTAMP('$startdate') and UNIX_TIMESTAMP(t.bd_date)<=UNIX_TIMESTAMP('$enddate') and (b.tel_status=3 or b.tel_status=4 or b.tel_status=5 or b.tel_status=6)";
		//实际关单
		$sql3 = "select r.uname,b.qudao,count(DISTINCT b.no) as total from data_base as b 
		join tel_bd as t on b.no=t.telno  
		join rise_user as r on r.id=b.fp_name 
		where UNIX_TIMESTAMP(t.bd_date)>=UNIX_TIMESTAMP('$startdate') and UNIX_TIMESTAMP(t.bd_date)<=UNIX_TIMESTAMP('$enddate') and (b.tel_status=4 or b.tel_status=5 )";
		if(!empty($qudao)){
			$sql .= " and b.qudao='$qudao' ";
			$sql1 .= " and b.qudao='$qudao' ";
			$sql2 .= " and b.qudao='$qudao' ";
			$sql3 .= " and b.qudao='$qudao' ";
		}
		if(!empty($_POST['cc'])){
			$sql .= " and t.uid='$_POST[cc]' ";
			$sql1 .= " and t.uid='$_POST[cc]' ";
			$sql2 .= " and t.uid='$_POST[cc]' ";
			$sql3 .= " and t.uid='$_POST[cc]' ";
		}
		$sql .= " group by b.qudao,b.fp_name";
		$sql1 .= " group by b.qudao,b.fp_name";
		$sql2 .= " group by b.qudao,b.fp_name";
		$sql3 .= " group by b.qudao,b.fp_name";

		$result = mysql_query($sql);
		$result1 = mysql_query($sql1);
		$result2 = mysql_query($sql2);
		$result3 = mysql_query($sql3);

		$arr = array();
		while($row = mysql_fetch_assoc($result)){
			$uname = $row['uname'];
			$qudao = $row['qudao'];
			$arr[$qudao][$uname]['total'] = $row['total'];
		}

		while($row1 = mysql_fetch_assoc($result1)){
			$uname = $row1['uname'];
			$qudao = $row1['qudao'];
			$arr[$qudao][$uname]['cn'] = $row1['total'];
		}
		while($row2 = mysql_fetch_assoc($result2)){
			$uname = $row2['uname'];
			$qudao = $row2['qudao'];
			$arr[$qudao][$uname]['sj'] = $row2['total'];
		}
		while($row3 = mysql_fetch_assoc($result3)){
			$uname = $row3['uname'];
			$qudao = $row3['qudao'];
			$arr[$qudao][$uname]['gd'] = $row3['total'];
		}
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
	<style type="text/css">
		body{text-align: center;}
		table{margin: 0 auto;}
	</style>
	<title>咨询人员转化统计</title>
</head>
<body>	
	<form name="myform" action="cc_tj.php" method="POST">
		<table align="center" class="detailtab" cellspacing="1" id="querytable" style="width:80%">
			<tr>
				<th colspan="6">查询</th></tr>			
				<tr>
						<td class="tdt" >时间区间</td>
		             	<td class="tdf" >
							<input style="width:150px" type="text" class="Wdate" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" name="startdate" value="<?php echo $_POST[startdate]?>"/>
						&nbsp;至&nbsp;
							<input style="width:150px" type="text" class="Wdate" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" name="enddate" value="<?php echo $_POST[enddate]?>"/>
		              	</td>
		              <td class="tdt">人员</td>
		              <td class="tdf" style="width:15%"><select name='cc'><?php echo $cc_list;?></select></td>
		              <td class="tdt">渠道</td>
		              <td class="tdf" style="width:15%"><select name='qudao'><?php echo $qd_list;?></select></td>
				</tr>		
			<tr>
				<td class="bbtn" colspan="6">
					<input class="btn1" onclick="mysumt()" type="button" value="查&nbsp;&nbsp;询" />
						<input type="reset" id="querybtn" class="btn1" value="重&nbsp;&nbsp;置" />
				</td>
			</tr>
		</table>
	</form>
	<!-- 列表展示 -->	
<!-- 	<table class="listtab" cellspacing="1" align="center" id="listtab" style="width:80%">
		<thead>
			<tr class="title">
				<th colspan="10">
				<div style="float: left;">咨询顾问人员转化率统计表</div>
				</th>
			</tr>
			<tr>
				<th class="tht" >序号</th>
				<th class="tht" >人员</th>	
				<th class="tht" >咨询量合计</th>
				<th class="tht" >电话承诺上门</th>
				<th class="tht" >电话实际上门</th>
				<th class="tht" >实际关单</th>
				<th class="tht" >电转率<br/>(电话实际上门<br/>/汇总电话咨询量)</th>
				<th class="tht" >当面关单率<br/>(实际关单<br/>/电话+walk in<br/>共计上门人数)</th>
				<th class="tht" >总转<br/>(实际关单<br/>/汇总所有咨询量)</th>
			</tr>
		</thead>
	</table> -->
	<table class="listtab" cellspacing="1" align="center" id="listtab" style="width:80%">
		<thead>
			<tr class="title">
				<th colspan="10">
				<div style="float: left;">咨询顾问人员转化率统计表</div>
				</th>
			</tr>
			<tr>
				<th class="tht" colspan="9" style="font-size:18px">地推</th>
			</tr>
			<tr>
				<th class="tht" >序号</th>
				<th class="tht" >人员</th>	
				<th class="tht" >咨询量合计</th>
				<th class="tht" >电话承诺上门</th>
				<th class="tht" >电话实际上门</th>
				<th class="tht" >实际关单</th>
				<th class="tht" >电转率</th>
				<th class="tht" >当面关单率</th>
				<th class="tht" >总转</th>
			</tr>
			<?php
				$i = 1;
				foreach ($arr['地推'] as $key => $value) {
					$total = empty($value['total'])? 0 : $value['total'];
					$cn = empty($value['cn'])? 0 : $value['cn'];
					$sj = empty($value['sj'])? 0 : $value['sj'];
					$gd = empty($value['gd'])? 0 : $value['gd'];
					$dz = round($sj / $total,4) * 100 . '%';
					$mz = round($gd / $sj,4) * 100 . '%';
					$zz = round($gd / $total,4) * 100 . '%';
					echo '<tr>';
					echo '<td>',$i,'</td>';
					echo '<td>',$key,'</td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=dt&class=zxl&fp_name='.$key.'">',$total,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=dt&class=cn&fp_name='.$key.'">',$cn,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=dt&class=sj&fp_name='.$key.'">',$sj,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=dt&class=gd&fp_name='.$key.'">',$gd,'</a></td>';
					echo '<td>',$dz,'</td>';
					echo '<td>',$mz,'</td>';
					echo '<td>',$zz,'</td>';
					echo '</tr>';
					$i++;
				}
			?>
			<tr>
				<th class="tht" colspan="9" style="font-size:18px">Call In</th>
			</tr>
			<tr>
				<th class="tht" >序号</th>
				<th class="tht" >人员</th>	
				<th class="tht" >咨询量合计</th>
				<th class="tht" >电话承诺上门</th>
				<th class="tht" >电话实际上门</th>
				<th class="tht" >实际关单</th>
				<th class="tht" >电转率</th>
				<th class="tht" >当面关单率</th>
				<th class="tht" >总转</th>
			</tr>
			<?php
				$i = 1;
				foreach ($arr['call in'] as $key => $value) {
					$total = empty($value['total'])? 0 : $value['total'];
					$cn = empty($value['cn'])? 0 : $value['cn'];
					$sj = empty($value['sj'])? 0 : $value['sj'];
					$gd = empty($value['gd'])? 0 : $value['gd'];
					$dz = round($sj / $total,4) * 100 . '%';
					$mz = round($gd / $sj,4) * 100 . '%';
					$zz = round($gd / $total,4) * 100 . '%';
					echo '<tr>';
					echo '<td>',$i,'</td>';
					echo '<td>',$key,'</td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=callin&class=zxl&fp_name='.$key.'">',$total,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=callin&class=cn&fp_name='.$key.'">',$cn,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=callin&class=sj&fp_name='.$key.'">',$sj,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=callin&class=gd&fp_name='.$key.'">',$gd,'</a></td>';
					echo '<td>',$dz,'</td>';
					echo '<td>',$mz,'</td>';
					echo '<td>',$zz,'</td>';
					echo '</tr>';
					$i++;
				}
			?>
			<tr>
				<th class="tht" colspan="9" style="font-size:18px">Walk In</th>
			</tr>
			<tr>
				<th class="tht" >序号</th>
				<th class="tht" >人员</th>	
				<th class="tht" >咨询量合计</th>
				<th class="tht" >电话承诺上门</th>
				<th class="tht" >电话实际上门</th>
				<th class="tht" >实际关单</th>
				<th class="tht" >电转率</th>
				<th class="tht" >当面关单率</th>
				<th class="tht" >总转</th>
			</tr>
			<?php
				$i = 1;
				foreach ($arr['walk in'] as $key => $value) {
					$total = empty($value['total'])? 0 : $value['total'];
					$cn = empty($value['cn'])? 0 : $value['cn'];
					$sj = empty($value['sj'])? 0 : $value['sj'];
					$gd = empty($value['gd'])? 0 : $value['gd'];
					$dz = round($sj / $total,4) * 100 . '%';
					$mz = round($gd / $sj,4) * 100 . '%';
					$zz = round($gd / $total,4) * 100 . '%';
					echo '<tr>';
					echo '<td>',$i,'</td>';
					echo '<td>',$key,'</td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=walkin&class=zxl&fp_name='.$key.'">',$total,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=walkin&class=cn&fp_name='.$key.'">',$cn,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=walkin&class=sj&fp_name='.$key.'">',$sj,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=walkin&class=gd&fp_name='.$key.'">',$gd,'</a></td>';
					echo '<td>',$dz,'</td>';
					echo '<td>',$mz,'</td>';
					echo '<td>',$zz,'</td>';
					echo '</tr>';
					$i++;
				}
			?>
			<tr>
				<th class="tht" colspan="9" style="font-size:18px">转介绍</th>
			</tr>
			<tr>
				<th class="tht" >序号</th>
				<th class="tht" >人员</th>	
				<th class="tht" >咨询量合计</th>
				<th class="tht" >电话承诺上门</th>
				<th class="tht" >电话实际上门</th>
				<th class="tht" >实际关单</th>
				<th class="tht" >电转率</th>
				<th class="tht" >当面关单率</th>
				<th class="tht" >总转</th>
			</tr>
			<?php
				$i = 1;
				foreach ($arr['转介绍'] as $key => $value) {
					$total = empty($value['total'])? 0 : $value['total'];
					$cn = empty($value['cn'])? 0 : $value['cn'];
					$sj = empty($value['sj'])? 0 : $value['sj'];
					$gd = empty($value['gd'])? 0 : $value['gd'];
					$dz = round($sj / $total,4) * 100 . '%';
					$mz = round($gd / $sj,4) * 100 . '%';
					$zz = round($gd / $total,4) * 100 . '%';
					echo '<tr>';
					echo '<td>',$i,'</td>';
					echo '<td>',$key,'</td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=zjs&class=zxl&fp_name='.$key.'">',$total,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=zjs&class=cn&fp_name='.$key.'">',$cn,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=zjs&class=sj&fp_name='.$key.'">',$sj,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=zjs&class=gd&fp_name='.$key.'">',$gd,'</a></td>';
					echo '<td>',$dz,'</td>';
					echo '<td>',$mz,'</td>';
					echo '<td>',$zz,'</td>';
					echo '</tr>';
					$i++;
				}
			?>
			<tr>
				<th class="tht" colspan="9" style="font-size:18px">活动</th>
			</tr>
			<tr>
				<th class="tht" >序号</th>
				<th class="tht" >人员</th>	
				<th class="tht" >咨询量合计</th>
				<th class="tht" >电话承诺上门</th>
				<th class="tht" >电话实际上门</th>
				<th class="tht" >实际关单</th>
				<th class="tht" >电转率</th>
				<th class="tht" >当面关单率</th>
				<th class="tht" >总转</th>
			</tr>
			<?php
				$i = 1;
				foreach ($arr['活动'] as $key => $value) {
					$total = empty($value['total'])? 0 : $value['total'];
					$cn = empty($value['cn'])? 0 : $value['cn'];
					$sj = empty($value['sj'])? 0 : $value['sj'];
					$gd = empty($value['gd'])? 0 : $value['gd'];
					$dz = round($sj / $total,4) * 100 . '%';
					$mz = round($gd / $sj,4) * 100 . '%';
					$zz = round($gd / $total,4) * 100 . '%';
					echo '<tr>';
					echo '<td>',$i,'</td>';
					echo '<td>',$key,'</td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=hd&class=zxl&fp_name='.$key.'">',$total,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=hd&class=cn&fp_name='.$key.'">',$cn,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=hd&class=sj&fp_name='.$key.'">',$sj,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=hd&class=gd&fp_name='.$key.'">',$gd,'</a></td>';
					echo '<td>',$dz,'</td>';
					echo '<td>',$mz,'</td>';
					echo '<td>',$zz,'</td>';
					echo '</tr>';
					$i++;
				}
			?>
						<tr>
				<th class="tht" colspan="9" style="font-size:18px">渠道</th>
			</tr>
			<tr>
				<th class="tht" >序号</th>
				<th class="tht" >人员</th>	
				<th class="tht" >咨询量合计</th>
				<th class="tht" >电话承诺上门</th>
				<th class="tht" >电话实际上门</th>
				<th class="tht" >实际关单</th>
				<th class="tht" >电转率</th>
				<th class="tht" >当面关单率</th>
				<th class="tht" >总转</th>
			</tr>
			<?php
				$i = 1;
				foreach ($arr['渠道'] as $key => $value) {
					$total = empty($value['total'])? 0 : $value['total'];
					$cn = empty($value['cn'])? 0 : $value['cn'];
					$sj = empty($value['sj'])? 0 : $value['sj'];
					$gd = empty($value['gd'])? 0 : $value['gd'];
					$dz = round($sj / $total,4) * 100 . '%';
					$mz = round($gd / $sj,4) * 100 . '%';
					$zz = round($gd / $total,4) * 100 . '%';
					echo '<tr>';
					echo '<td>',$i,'</td>';
					echo '<td>',$key,'</td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=qd&class=zxl&fp_name='.$key.'">',$total,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=qd&class=cn&fp_name='.$key.'">',$cn,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=qd&class=sj&fp_name='.$key.'">',$sj,'</a></td>';
					echo '<td><a target="_blank" href="tj_detail.php?s_date='.$_POST['startdate'].'&e_date='. $_POST['enddate'] .'&qd=qd&class=gd&fp_name='.$key.'">',$gd,'</a></td>';
					echo '<td>',$dz,'</td>';
					echo '<td>',$mz,'</td>';
					echo '<td>',$zz,'</td>';
					echo '</tr>';
					$i++;
				}
			?>
		</thead>
	</table>
	<script type="text/javascript">
		function mysumt(){

			$('form').submit();
		}
	</script>
</body>
</html>

