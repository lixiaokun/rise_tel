<?php
	require './common/qx.php';
	require './common/yey.php';
	require './common/fpage.php';
	$_arr=isset($_POST["sub"]) ? $_POST : $_GET;
	//渠道列表
	$qudao_list = '<option value="">请选择</option>';
	foreach ($qudao as  $value) {
		if($_arr['qudao'] == $value)
			$qudao_list .= '<option value="'. $value .'" selected=1>'. $value .'</option>';
		else
			$qudao_list .= '<option value="'. $value .'">'. $value .'</option>';
	}
	//电话状态
	$all_status['all'] = '全部';
	foreach ($all_status as $key => $value) {
		if($key != '99'){
			if((strlen($_arr['tel_status']) >0 && $_arr['tel_status'] == $key) || (!isset($_arr['tel_status']) && $key == 'all')){
				$status_list .= '<option value="'. $key .'" selected=1>'. $value .'</option>';
			}
			else
				$status_list .= '<option value="'. $key .'">'. $value .'</option>';
		}
	}

	//地推人列表
	$dt_name_list= '<option value="">请选择</option>';
	foreach ($dt_name as $key => $value) {
		if($_arr['dt_name'] == $value)
			$dt_name_list .= '<option value="'. $value .'" selected=1>'. $value .'</option>';
		else
			$dt_name_list .= '<option value="'. $value .'">'. $value .'</option>';
	}


	$whe=array();       
    $param="";

    if(!empty($_arr["dt_name"])) {
    	if($_arr['dt_name'] == 'all'){

    	}else{
	        $whe[] ="dt_name='{$_arr[dt_name]}'";
	        $param.="&dt_name={$_arr[dt_name]}";
	    }
         
    }
     if(!empty($_arr["school"])) {
        $whe[] ="school='{$_arr[school]}'";

        $param.="&school={$_arr[school]}";
         
    }
    if(!empty($_arr["dt_date_s"])) {
        $whe[] ="UNIX_TIMESTAMP(dt_date) >= UNIX_TIMESTAMP('{$_arr[dt_date_s]}')";   
        $param.="&dt_date_s={$_arr[dt_date_s]}";
    }

   	if(!empty($_arr["dt_date_e"])) {
        $whe[] ="UNIX_TIMESTAMP(dt_date) <= UNIX_TIMESTAMP('{$_arr[dt_date_e]}')";   
        $param.="&dt_date_e={$_arr[dt_date_e]}";
    }
    if(strlen($_arr["tel_status"]) && $_arr['tel_status'] != 'all') {
        $whe[] ="tel_status = '{$_arr[tel_status]}'";   
        $param.="&tel_status={$_arr[tel_status]}";
    }

    if(empty($whe)){
        $where=" ";
        $uri="alreadylr.php";
    }else{
        $where="where ".implode(" and ", $whe);
        $uri="alreadylr.php?".$param;
    }

    $page=fpage("data_base",$where, $uri, "15");

	$sql = 'SELECT `no`, `tel`, `s_name`, `sex`, `age`, `important`, `dt_name`, `dt_date`, `fp_status`, `fp_name`, `qudao`, `p_name`, `school`, `fp_date`,`tel_status` FROM `data_base` '.$where. " order by UNIX_TIMESTAMP(dt_date) DESC {$page[limit]} ";
	// echo $sql;
	$result = mysql_query($sql);
	$tr = '';
	$xh = 1;
	while($row = mysql_fetch_assoc($result)){
		$tr .= $row['important'] == 1 ? '<tr style="color:red">' : '<tr>';
		// $tr .= '<td><input type="checkbox" name="fp_arr[]" value="'. $row['no'] .'"></td>';
		$tr .= '<td>'. $xh .'</td>';
		$tr .= '<td>'. $all_status[$row['tel_status']] .'</td>';
		$tr .= '<td>'. $cc[$row['fp_name']] .'</td>';
		$tr .= '<td>'. $row['dt_date'] .'</td>';
		$tr .= '<td>'. $row['s_name'] .'</td>';
		$tr .= '<td>'. ($row['sex'] == 1? '男':'女') .'</td>';
		$tr .= '<td>'. $row['age'] .'</td>';
		$tr .= '<td>'. $row['school'] .'</td>';
		$tr .= '<td>'. $row['p_name'] .'</td>';
		$tr .= '<td>'. $row['dt_name'] .'</td>';
		$tr .= '<td>'. $row['qudao'] .'</td>';
		$tr .= '</tr>';
		$xh++;
	}
?>
<html>
	<head>
		<meta http-equiv="content-type" content="html/text;charset=utf-8">
		<script type="text/javascript" src="common/jquery.js"></script>
		<script language="javascript" type="text/javascript" src="./My97DatePicker/WdatePicker.js"></script>
		<link rel="stylesheet" href="css/table.css">
		<title>地推信息查询</title>
		<style>
			th,td{text-align: center;}
			body {TEXT-ALIGN: center;}
			#tj{width:90%;margin: 0 auto;}
			th{font-size: 12px}
			table{width:90%;}

		</style>
		<script type="text/javascript">
				function check1(){
					$('#form1').attr('action','./alreadylr.php');
				}
				function export1(){
					$('#form1').attr('action','./export_excel.php');
				}
		</script>
	</head>
	<body>
		<div id='tj'>
			<fieldset>
				<legend>地推信息查询</legend>
			<form action='./alreadylr.php' method='get' id="form1">
				地推日期：<input class="Wdate" type="text" name="dt_date_s" onClick="WdatePicker({dateFmt:'yyyy-M-d'})"<?php echo 'value='.$_arr['dt_date_s'];?>>至<input class="Wdate" type="text" name="dt_date_e" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" <?php echo 'value='.$_arr['dt_date_e'];?>>
				地推地点：<input type="text" name="school" >
				状态：<select name='tel_status'>
						<?php
							echo $status_list;
						?>
					</select>
				渠道：<select name='qudao'>
					<?php
						echo $qudao_list;
					?>
				</select>
				地推人：<select name='dt_name'><?php echo $dt_name_list;?></select>
				<input type='submit' value='查询' onclick='check1()'>
				<input type='submit' value='导出Excel' onclick='export1()'>
			</form>
			</fieldset>
		</div>
		<table border="1" cellspacing="0" cellpadding="0"  align="center" >
			<tr>

				<th>序号</th><th>状态</th><th>咨询师</th><th>地推日期</th><th>学生姓名</th><th>性别</th><th>年龄</th><th>学校</th><th>家长姓名</th><th>地推人</th><th>渠道</th>
			</tr>
			<?php echo $tr; echo '<tr><td colspan="12" style="text-align:center">'.$page["fpage"].'</td></tr>';
				?>
		</table>
	</body>
</html>