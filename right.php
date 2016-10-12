<?php
	require './common/qx.php';
	require './common/yey.php';
	/*
		针对不同的用户显示不同的首页
		$user_js = array(
			'1' => '咨询主管', 显示电话查询页面
			'2' => '咨询师', 显示电话查询页面
			'3' => '前台', 显示当天DEMO列表，如果没有则显示电话查询页面
			'4' => '市场' 显示当天DEMO列表，如果没有则显示电话查询页面
		);
	*/
	$user_js = $_SESSION['user_js'];
	if($user_js == '3' || $user_js == '4'){
		$today = date("Y-n-j",time());
		$time = date("H",time());
		$week = date("N",time());
		//周末上午就一场demo 下午16：30前是一场  后又是一场
		//周中 demo都在晚上18:00左右
		if($week > 5){
			if($time < '12'){
				$and = " and demotime<'12:00'";
			}
			if($time >= '12' && $time < '16'){
				$and = " and demotime>'12:00' and demotime<'16:00'";
			}
			if($time >= '16'){
				$and = " and demotime>'16:00'";
			}
		}else{
			$and = '';
		}
		$sql = "select * from demo_base where demodate='$today'".$and;

		$result = mysql_query($sql);
		$row = mysql_fetch_row($result);
		if(!empty($row)){
			echo "<script>location.href='demo_xy.php?demoid=$row[0]'</script>";
			die;
		}	
	}
	$flag = 0;//HTML显示标记
	if((!empty($_POST['tel']) && strlen($_POST['tel'])) || (is_numeric($_GET['tel']) && strlen($_GET['tel']) == 11)){
		/**
			2016年3月5日
			更新内容：可以用电话或者学生姓名检索具体信息

		*/

		//判断是电话号码还是学生姓名
		$nr = !empty($_POST['tel']) ? trim($_POST['tel']) : trim($_GET['tel']);
		if(is_numeric($nr)){
			//按照电话号码检索
			$sql = "select p_name,fp_status,tel,qudao,gzdw,s_name,sex,age,relation,english,tel_status,tel from data_base where tel=$nr";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			$p_name = $row['p_name'];
			$tel = $row['tel'];
			$qudao = $row['qudao'];
			$gzdw = $row['gzdw'];
			$s_name = $row['s_name'];
			$sex = $row['sex'];
			$age = $row['age'];
			$relation = $row['relation'];
			$english = $row['english'];
			$fp_status = $row['fp_status'] ? '已分配' : '未分配';
			$final_status = $all_status[$row['tel_status']];
			$sex = $sex ? '男' : '女';
			/*
			获取已有的咨询纪要
			*/
			$zx_sql = "select tel_bd.zxjy,tel_bd.status,tel_bd.bd_date,tel_bd.demoid,dmzx,rise_user.uname from tel_bd join rise_user on tel_bd.uid=rise_user.id join data_base on data_base.no=tel_bd.telno where data_base.tel='$tel'";
			$zx_res = mysql_query($zx_sql);
			$zx_history = '';
			while($zx_row = mysql_fetch_assoc($zx_res)){
				$zx_history .= '<tr style="text-align:center">';
				$zx_history .= '<td>'. $tel_status[$zx_row['status']] .'</td>';
				$zx_history .= '<td>' . $zx_row['bd_date'] . '</td>';
				$zx_history .= '<td>' . $zx_row['uname'] . '</td>';
				$zx_history .= '<td>' . $zx_row['zxjy'] .'</td>';
				if($zx_row['demoid']){
					$demo = 'select demodate,demotime from demo_base where id='.$zx_row['demoid'];
					$res = mysql_query($demo);
					$row = mysql_fetch_assoc($res);
					$zx_history .= '<td>' . $row['demodate'] . '　'. $row['demotime'] .'</td>';
				}else{
					$zx_history .= '<td> </td>';
				}
				$zx_history .= '<td>'. $zx_row['dmzx'] .'</td>';
				//$zx_history .= '<td><a href="">'. date('Ymd',strtotime($zx_row['bd_date'])) . $tel .'.wav</a></td>';
				$zx_history .= '</tr>';
			}
			$flag = 1;
		}else{
			$sql = "select p_name,tel,qudao,gzdw,s_name,sex,age,relation,english,tel_status,tel,fp_status from data_base where s_name like '%$nr%'";
			$result = mysql_query($sql);
			$table =<<<ml
			<table align="center" class="listtab" border="1" cellpadding="0" cellspacing="0" width=600>
			<tr class="title" style="text-align:center">
				<td colspan="7" >
				学员列表	
				</td>
			</tr>
			<tr>
				<th class="tht">中文名</th>
				<th class="tht">电话号码</th>
				<th class="tht">客户状态</th>
				<th class="tht">分配状态</th>
				<th class="tht">性别</th>
				<th class="tht">年龄</th>
			</tr>
ml;
			while($row = mysql_fetch_assoc($result)){
				$p_name = $row['p_name'];
				$tel = $row['tel'];
				$qudao = $row['qudao'];
				$gzdw = $row['gzdw'];
				$s_name = $row['s_name'];
				$sex = $row['sex'];
				$age = $row['age'];
				$relation = $row['relation'];
				$english = $row['english'];
				$final_status = $all_status[$row['tel_status']];
				$sex = $sex ? '男' : '女';
				$fp_status = $row['fp_status'] ? '已分配' : '未分配';
				$table .= '<tr>';
				$table .= "<td class='tht'>{$s_name}</td>";
				$table .= "<td class='tht'><a href='right.php?tel=$tel'>{$tel}</a></td>";
				$table .= "<td class='tht'>{$final_status}</td>";
				$table .= "<td class='tht'>{$fp_status}</td>";
				$table .= "<td class='tht'>{$sex}</td>";
				$table .= "<td class='tht'>{$age}</td>";
				$table .= "</tr>";		
			}
			$table .= "</table>";
			$flag = 2;
		}
	}
?>
<html>
	<head>
		<meta http-equiv="content-type" content="html/text;charset=utf-8">
		<style type="text/css">
			body{margin:0;padding: 0;text-align: center}
			fieldset{margin:0 auto;width:90%;border-radius:5px;}
			p{margin-top:20px;}
			th{background-color: #EEEEEE;color:black;height: 30px;line-height: 30px;}
			tr{text-align: center}

			th{background-color: #EEEEEE;color:black;height: 40px;line-height: 40px;}
			td{height:30px;}
			tr{text-align: center}
			td input{text-align:center}
			th { text-shadow: 1px 1px 1px #fff; background:#e8eaeb;}
			table {
			overflow:hidden;
			border:1px solid #d3d3d3;
			background:#fefefe;
			/*width:70%;
			margin:5% auto 0;*/
			-moz-border-radius:5px; /* FF1+ */
			-webkit-border-radius:5px; /* Saf3-4 */
			border-radius:5px;
			-moz-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
			-webkit-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
			font:12px/15px "Helvetica Neue",Arial, Helvetica, sans-serif;
			}

		</style>
	</head>
	<body>
		<fieldset>
			<legend>学员检索</legend>
        	<form action='./right.php' method="post">
				<input type='text' maxlength="11" placeholder="电话号码或学生姓名" autocomplete='on' required="required" name='tel'>
				<input type='submit' value='查询'>
			</form>
<?php
	if($flag == 1){
?>
<div id='none'>
		<!--家长及学员信息 -->
		<table align="center"  cellspacing="0">
			<tr>
				<th colspan="4">家长基本信息</th>
			</tr>
<?php
echo <<<ht
			<tr>
				<td>家长姓名</td>
				<td  style="width: 45%">{$p_name}</td>
				<td>手机</td>
				<td  style="width: 35%">{$tel}</td>
			</tr>
			<tr>
				<td>工作单位</td>
				<td>{$gzdw}</td>
				<td>渠道</td>
				<td>{$qudao}</td>
			</tr>

		</table>
		<br>
		<table align="center"  cellspacing="0" width=600>
			<tr class="title" style="text-align:center">
				<td colspan="7" >
				学员列表	
				</td>
			</tr>
			<tr>
				<th class="tht">中文名</th>
				<th class="tht">与家长关系</th>
				<th class="tht">客户状态</th>
				<th class="tht">分配状态</th>
				<th class="tht">性别</th>
				<th class="tht">年龄</th>
				<th class="tht">英语基础</th>
			</tr>
			<tr>
				<td class="tht">{$s_name}</td>
				<td class="tht">{$relation}</td>
				<td class="tht">{$final_status}</td>
				<td class="tht">{$fp_status}</td>
				<td class="tht">{$sex}</td>
				<td class="tht">{$age}</td>
				<td class="tht">{$english}</td>
			</tr>			
		</table>
ht;
?>	
	<br>
		<!--咨询历史-->
		<table align="center"  cellspacing="0" id='zx_history'>
			<tr class="title" style="text-align:center">
				<td colspan="7">
				咨询历史
				</td>
			</tr>
			<tr>
				<th class="tht" style="text-align:center;width:80px">状态</th>
				<th class="tht" style="text-align: center" width="140px">咨询时间</th>
				<th class="tht" style="text-align: center" width="100px">顾问</th>
				<th class="tht" style="text-align: center" width="380px">咨询纪要</th>
				<th class="tht" style="text-align: center" width="200px">邀约信息</th>
				<th class="tht" style="text-align: center" width="150px">当面咨询内容</th>
			</tr>
			<?php
			echo $zx_history;
			?>			
		</table>
</fieldset>
	</div>
	<?php
}
	if($flag == '2'){
		echo $table;
	}
	?>

	</body>
</html>


