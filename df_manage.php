<?php
	/**
	到访管理
		按条件查询到访家长 提供标记到访和标记报名状态的功能
	*/
	require './common/qx.php';
	require './common/yey.php';
	header('content-type:text/html;charset=utf-8');
	//缺省查询本周
	$date=date('Y-m-d');  //当前日期
	$first=1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
	$w=date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
	$now_start=date('Y-n-j',strtotime("$date -".($w ? $w - $first : 6).' days'));
	// $last_monday = date('Y-n-j',strtotime("$now_start - 7 days"));   
	// $last_sunday =  date('Y-n-j',strtotime("$now_start - 1 days"));

	$monday = $now_start;
	$sunday = date('Y-n-j',strtotime("$now_start + 6 days"));

	$df_arr = array('1' => '请选择','2' => '已到访','3' => '未到访');
	//是否到访 如果没有选择到访状态 缺省选择已到访
	$df_status = !empty($_POST['df_status']) ? $_POST['df_status'] : 3;
	$df_status_list = '';
	foreach ($df_arr as $key => $value) {
		if($df_status == $key)
			$df_status_list .= "<option value='$key' selected=1>$value</option>";
		else
			$df_status_list .= "<option value='$key'>$value</option>";
	}
	
	//查询数据库中所有数据
	$total_sql = "select count(db.no)
			from data_base as db 
			join data_mx as dm on dm.telno=db.tel
			join demo_base as dbs on dbs.id=dm.demoid
			join rise_user as ru on ru.id=db.fp_name
			where db.tel_status in(2,3,4,5,6) group by sm_status";
	$total_res = mysql_query($total_sql);
	$total_row1 = mysql_fetch_row($total_res);
	$total_row2 = mysql_fetch_row($total_res);
	$total =  "累计邀约人数为：{$total_row1[0]}，实际上门人数为：{$total_row2[0]}";

	//学生姓名
	$s_name = trim($_POST['s_name']);
	//电话号码
	$tel = trim($_POST['tel']);
	//到访时间
	// $demo_date_s = !empty($_POST['demo_date_s']) ? $_POST['demo_date_s'] : $last_monday;
	// $demo_date_e = !empty($_POST['demo_date_e']) ? $_POST['demo_date_e'] : $last_sunday;
	$demo_date_s = !empty($_POST['demo_date_s']) ? $_POST['demo_date_s'] : $monday;
	$demo_date_e = !empty($_POST['demo_date_e']) ? $_POST['demo_date_e'] : $sunday;
	if(strtotime($df_date_s) > strtotime($df_date_e)){
		echo '<script>alert("日期区间选择错误!");location.href="df_manage.php"<script>';
		die;
	}

	//按照条件查询数据
	/*

	到访列表  
	序号 中心 家长姓名 联系电话 学生姓名 到访时间 类型 家长状态 状态 电转顾问 demo课时间  
	1 长治解放路中心 郭 13934050260 郭瑞畅  2015-08-15 10:53:14 demo  已上门未交费 到访  许娅婷 2015-08-15 09:30 
 
	*/
	if($df_status == 2){
		$query = "select db.no,db.s_name,db.p_name,db.tel,sm_status,dm.sm_date,bm_status,bm_date,db.tel_status,ru.uname,dm.demoid,dbs.demodate,dbs.demotime 
				from data_base as db 
				join data_mx as dm on dm.telno=db.tel
				join demo_base as dbs on dbs.id=dm.demoid
				join rise_user as ru on ru.id=db.fp_name
				where dm.sm_status=1 and db.tel_status in(3,4,5,6) and UNIX_TIMESTAMP(dbs.demodate)>=UNIX_TIMESTAMP('$demo_date_s') and UNIX_TIMESTAMP(dbs.demodate)<=UNIX_TIMESTAMP('$demo_date_e')";
	}elseif($df_status == 3){
		$query = "select db.no,db.s_name,db.p_name,db.tel,sm_status,dm.sm_date,bm_status,bm_date,db.tel_status,ru.uname,dm.demoid,dbs.demodate,dbs.demotime 
				from data_base as db 
				join data_mx as dm on dm.telno=db.tel
				join demo_base as dbs on dbs.id=dm.demoid
				join rise_user as ru on ru.id=db.fp_name
				where dm.sm_status=0 and db.tel_status=2 and UNIX_TIMESTAMP(dbs.demodate)>=UNIX_TIMESTAMP('$demo_date_s') and UNIX_TIMESTAMP(dbs.demodate)<=UNIX_TIMESTAMP('$demo_date_e')";
	}else{
		$query = "select db.no,db.s_name,db.p_name,db.tel,sm_status,dm.sm_date,bm_status,bm_date,db.tel_status,ru.uname,dm.demoid,dbs.demodate,dbs.demotime 
				from data_base as db 
				join data_mx as dm on dm.telno=db.tel
				join demo_base as dbs on dbs.id=dm.demoid
				join rise_user as ru on ru.id=db.fp_name
				where db.tel_status in(2,3,4,5,6) and UNIX_TIMESTAMP(dbs.demodate)>=UNIX_TIMESTAMP('$demo_date_s') and UNIX_TIMESTAMP(dbs.demodate)<=UNIX_TIMESTAMP('$demo_date_e')";
	}
	if(strlen($tel) == 11 && is_numeric($tel)){
		$query .= " and db.tel='$tel' ";
	}
	if(!empty($s_name)){
		$query .= " and db.s_name like '%{$s_name}%' ";
	}
	$query .= ' order by UNIX_TIMESTAMP(dbs.demodate) DESC,db.no DESC';
	$result = mysql_query($query);
	$trs = '';
	$i = 1;
	//报名选项列表
	$bm_arr = array('wbm' => '未报名','dj' => '定金','qf' => '全费','tf' => '退费');
	$zt_arr = array( '4' => 'dj','5' => 'qf','6' => 'tf','3' => 'wbm');
	while($row = mysql_fetch_assoc($result)){
		$trs .= '<tr>';
		$trs .= '<td>'. $i .'</td>';
		$trs .= '<td>'. $row['s_name'] .'</td>';
		$trs .= '<td><a href="telbd.php?no='. $row['no'] .'">'. $row['tel'] .'</a></td>';
		$trs .= '<td>'. $row['p_name'] .'</td>';
		$trs .= '<td>'. $row['sm_date'] .'</td>';
		$trs .= '<td>'. $all_status[$row[tel_status]] .'</td>';
		$trs .= '<td>'. $row['uname'] .'</td>';
		$trs .= '<td>'. $row['demodate'] . ' ' . $row['demotime'] .'</td>';
		$trs .= '<td>
					<input type="hidden" name="demoid" value="'. $row['demoid'] .'">
					<input type="hidden" name="noo" value="'. $row['no'] .'"> 
					<input type="hidden" name="tel" value="'. $row['tel'] .'">
 					';
 		if(!$row['sm_status']){
 			$trs .= '<a class="daofang">到访</a>';
 		}else{
 			$trs .= '<a class="daofang">删除到访</a>';
 		}
		
		$bm_list = "<select class='baoming'>";
		foreach ($bm_arr as $key => $value) {
			if($zt_arr[$row[bm_status]] == $key)
				$bm_list .= "<option value='$key' selected=1>$value</option>";
			else
				$bm_list .= "<option value='$key'>$value</option>";
		}
		$bm_list .= "</select>";
		$trs .= " | $bm_list</td>";
		$trs .= '</tr>';
		$i++;
	}
?>
<html>
	<head>
		<title>到访管理</title>
		<script type="text/javascript" src="./My97DatePicker/WdatePicker.js"></script>
		<script type="text/javascript" src="./common/jquery.js"></script>
		<link rel="stylesheet" href="css/table.css">
		<style type="text/css">
			body{margin:0;padding: 0;text-align: center;}
			fieldset{width:95%;margin:0 auto;height: 50px}
			.Wdate{width:100px;}
			th,td{text-align: center;padding: 10px 20px 10px 20px}
			td,td select{font-size: 12px}
			div{width:95%;margin: 0 auto;}
			table{width:100%;}
			.daofang{
				text-decoration: underline;
				color:blue;
				cursor:pointer;
			}
			#tongji{font-size: 12px;color:red;text-align: left;margin-left:60px;}
		</style>
		<script>
			$(function(){
				$('.daofang').click(function(){
					var tel = noo = demoid = obj = '';
					tel = $(this).prevAll('[name=tel]').val();
					noo = $(this).prevAll('[name=noo]').val();
					demoid = $(this).prevAll('[name=demoid]').val();
					obj = $(this);
					$.post('df_manage_ajax.php',{'tel':tel,'noo':noo,'demoid':demoid,'action':'daofang'},function(data){
						if(data == 'success' && obj.html() == '到访'){
							obj.html('删除到访');
						}else{
							obj.html('到访');
						}
					});
				});
				$('.baoming').change(function(){
					var tel = noo = demoid = zt = '';
					zt = $(this).val();
					tel = $(this).prevAll('[name=tel]').val();
					noo = $(this).prevAll('[name=noo]').val();
					demoid = $(this).prevAll('[name=demoid]').val();

					$.post('df_manage_ajax.php',{'tel':tel,'noo':noo,'demoid':demoid,'action':'baoming','zt':zt},function(data){
						if(data == 'success'){
							alert('修改成功!');
						}else if(data == 'wdf'){
							alert('未到访不能报名!')
						}else{
							alert('修改失败！')
						}
					});
				});					
			});
			
		</script>
	</head>
	<body>
		<fieldset>
			<legend>到访管理</legend>
			<form action="./df_manage.php" method="post">
				状态：<select name="df_status">
						<?php
							echo $df_status_list;
						?>
				</select>
				学生姓名：<input type='text' name="s_name" value="<?php echo $s_name;?>">
				电话号码：<input type='text' name='tel' value="<?php echo $tel;?>">
				DEMO时间：<input class="Wdate" type="text" name="demo_date_s" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" value="<?php echo $demo_date_s?>"> - <input class="Wdate" type="text" name="demo_date_e" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" value="<?php echo $demo_date_e?>">
				<input type="reset" value="重置">
				<input type="submit" value="查询">
			</form>
		</fieldset>
		<div id="tongji">
			<?php
				echo $total;
			?>
		</div>
		<div>
			<table border="1" cellspacing="0" cellpadding="0"  align="center">
				<tr>
					<th>序号</th>
					<th>学生姓名</th>
					<th>电话</th>
					<th>家长姓名</th>
					<th>到访时间</th>
					<th>状态</th>
					<th>顾问</th>
					<th>DEMO课时间</th>
					<th>操作</th>
				</tr>
				<?php
					echo $trs;

				?>
			</table>
		</div>
	</body>
</html>