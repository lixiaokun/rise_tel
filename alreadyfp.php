<?php
	require './common/qx.php';
	require './common/yey.php';
	require './common/fpage.php';
	$_arr=isset($_POST["sub"]) ? $_POST : $_GET;
	//cc名单
	$cc_list = '<option value="">请选择</option>';
	foreach ($cc as $key => $value) {
		if(array_key_exists($_arr['fp_name'],$cc) && $_arr['fp_name'] == $key)
			$cc_list .= '<option value="'. $key .'" selected=1>'. $value .'</option>';
		else
			$cc_list .= '<option value="'. $key .'">'. $value .'</option>';
	}
	//电话状态
	$status_list = '<option value="">全部</option>';
	foreach ($all_status as $key => $value) {
		if(strlen($_arr['tel_status']) > 0 && $_arr['tel_status'] == $key){
			$status_list .= '<option value="'. $key .'" selected=1>'. $value .'</option>';
		}
		else
			$status_list .= '<option value="'. $key .'">'. $value .'</option>';
	}
	//执行重新分配操作
	if(isset($_POST['fp']) && array_key_exists($_POST['fp_name'], $cc) && !empty($_POST['fp_arr'])){

		$sql = 'update data_base set `fp_status`=1,`fp_date`="'. date('Y-n-j',time()) .'",`fp_name`="'. $_POST['fp_name'] .'" where (';
		$i = 0;
		foreach ($_POST['fp_arr'] as $key => $value) {
			$sql .= 'no='. $value .' or ';
			$i++;
		}
		$sql = substr($sql, 0,-3) . ') and fp_status=1';
		mysql_query($sql);
		if(!mysql_affected_rows()){
			$filename = './log/cxfp_log' . date('YmdHis',time()) . '.log';
			file_put_contents($filename,$sql);
			echo '<script>alert("重新分配失败，联系管理员！");location.href="alreadyfp.php"</script>';
			die;
		}
		echo '<script>alert("'. $i .'条数据重新分配成功！");location.href="alreadyfp.php"</script>';
		die;
	}
	//按条件查询  分页
	//$page=fpage("user",$where, $uri, "5");

	$whe=array();       
    $param="";

    if(!empty($_arr["fp_name"])) {
        $whe[] ="fp_name='{$_arr[fp_name]}'";

        $param.="&fp_name={$_arr[fp_name]}";
         
    }

    if(!empty($_arr["fp_date_s"])) {
        $whe[] ="fp_date >= '{$_arr[fp_date_s]}'";   
        $param.="&fp_date_s={$_arr[fp_date_s]}";
    }

   	if(!empty($_arr["fp_date_e"])) {
        $whe[] ="fp_date <= '{$_arr[fp_date_e]}'";   
        $param.="&fp_date_e={$_arr[fp_date_e]}";
    }
    if(strlen($_arr["tel_status"])) {
        $whe[] ="tel_status = '{$_arr[tel_status]}'";   
        $param.="&tel_status={$_arr[tel_status]}";
    }
    if(!empty($_arr["telno"])) {
        $whe[] ="tel = '{$_arr[telno]}'";   
        $param.="&tel={$_arr[telno]}";
    }

    if(empty($whe)){
        $where="where fp_status=1";
        $uri="alreadyfp.php";
    }else{
        $where="where fp_status=1 and ".implode(" and ", $whe);
        $uri="alreadyfp.php?".$param;
    }

    $page=fpage("data_base",$where, $uri, "15");

	$sql = 'SELECT `no`, `tel`, `s_name`, `sex`, `age`, `important`, `dt_name`, `dt_date`, `fp_status`, `fp_name`, `qudao`, `p_name`, `school`, `fp_date`,`tel_status` FROM `data_base` '.$where. " order by no DESC {$page[limit]} ";
	// echo $sql;
	$result = mysql_query($sql);
	$tr = '';
	$xh = 1;
	while($row = mysql_fetch_assoc($result)){
		$tr .= $row['important'] == 1 ? '<tr style="color:red">' : '<tr>';
		$tr .= '<td><input type="checkbox" name="fp_arr[]" value="'. $row['no'] .'"></td>';
		$tr .= '<td>'. $xh .'</td>';
		$tr .= '<td>'. $all_status[$row['tel_status']] .'</td>';
		$tr .= '<td>'. $cc[$row['fp_name']] .'</td>';
		$tr .= '<td>'. $row['fp_date'] .'</td>';
		$tr .= '<td>'. $row['s_name'] .'</td>';
		$tr .= '<td>'. ($row['sex'] == 1? '男':'女') .'</td>';
		$tr .= '<td>'. $row['age'] .'</td>';
		$tr .= '<td>'. $row['school'] .'</td>';
		$tr .= '<td>'. $row['tel'] .'</td>';
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
		<style>
			th,td{text-align: center;}
			body {TEXT-ALIGN: center;}
			#tj{width:80%;margin: 0 auto;}
			th{font-size: 12px}
			.className{
	margin:1px;
 	line-height:27px;
	height:27px;
	width:44px;
	color:#777777;
	background-color:#ededed;
	font-size:12px;
	font-weight:bold;
	font-family:Arial;
	background:-webkit-gradient(linear, left top, left bottom, color-start(0.05, #ededed), color-stop(1, #f5f5f5));
	background:-moz-linear-gradient(top, #ededed 5%, #f5f5f5 100%);
	background:-o-linear-gradient(top, #ededed 5%, #f5f5f5 100%);
	background:-ms-linear-gradient(top, #ededed 5%, #f5f5f5 100%);
	background:linear-gradient(to bottom, #ededed 5%, #f5f5f5 100%);
	background:-webkit-linear-gradient(top, #ededed 5%, #f5f5f5 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ededed', endColorstr='#f5f5f5',GradientType=0);
	border:1px solid #dcdcdc;
	-webkit-border-top-left-radius:10px;
	-moz-border-radius-topleft:10px;
	border-top-left-radius:10px;
	-webkit-border-top-right-radius:10px;
	-moz-border-radius-topright:10px;
	border-top-right-radius:10px;
	-webkit-border-bottom-left-radius:10px;
	-moz-border-radius-bottomleft:10px;
	border-bottom-left-radius:10px;
	-webkit-border-bottom-right-radius:10px;
	-moz-border-radius-bottomright:10px;
	border-bottom-right-radius:10px;
	-moz-box-shadow: inset -1px 0px 0px -3px #ffffff;
	-webkit-box-shadow: inset -1px 0px 0px -3px #ffffff;
	box-shadow: inset -1px 0px 0px -3px #ffffff;
	text-align:center;
	display:inline-block;
	text-decoration:none;
}
.className:hover {
	background-color:#f5f5f5;
	background:-webkit-gradient(linear, left top, left bottom, color-start(0.05, #f5f5f5), color-stop(1, #ededed));
	background:-moz-linear-gradient(top, #f5f5f5 5%, #ededed 100%);
	background:-o-linear-gradient(top, #f5f5f5 5%, #ededed 100%);
	background:-ms-linear-gradient(top, #f5f5f5 5%, #ededed 100%);
	background:linear-gradient(to bottom, #f5f5f5 5%, #ededed 100%);
	background:-webkit-linear-gradient(top, #f5f5f5 5%, #ededed 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#f5f5f5', endColorstr='#ededed',GradientType=0);
}
		</style>
	</head>
	<body>
		<div id='tj'>
			<fieldset>
				<legend>已分配电话</legend>
			<form action='./alreadyfp.php' method='get'>
				分配日期：<input class="Wdate" type="text" name="fp_date_s" onClick="WdatePicker({dateFmt:'yyyy-M-d'})"<?php echo 'value='.$_arr['fp_date_s'];?>>至<input class="Wdate" type="text" name="fp_date_e" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" <?php echo 'value='.$_arr['fp_date_e'];?>>
				状态：<select name='tel_status'>
					<?php
						echo $status_list;
					?>
							</select>
				咨询师：<select name='fp_name'><?php echo $cc_list;?></select>
				电话：<input type='text' name='telno'>
				<input type='submit' value='查询' name='sub'>
			</form>
			</fieldset>
		</div>
		<form action='./alreadyfp.php' method="post">
			<table border="1" cellspacing="0" cellpadding="0" width="800" align="center">
				<tr>
					<th style="width:13%"><input type="button" value="全选" class='classname' onclick="allSelectType();"/><input type="button" value="反选" class='classname' onclick="invertSelectType();"/>
					</th>
					<th>序号</th><th>状态</th><th>咨询师</th><th>分配日期</th><th>学生姓名</th><th>性别</th><th>年龄</th><th>学校</th><th>电话号码</th><th>地推人</th><th>渠道</th>
				</tr>
				<?php echo $tr; echo '<tr><td colspan="12" style="text-align:center">'.$page["fpage"].'</td></tr>';
						$user_js = $_SESSION['user_js'];
						if($user_js == '1'){
					?>
				<tr>
					<td colspan="12"  style="text-align:center"><select name='fp_name'><?php echo $cc_list;?></select>
					<input type="submit" name="fp" value="重新分配">
					</td>
				</tr>
					<?php
						}
					?>
			</table>
		</form>
	</body>
	<script>
//反选

function invertSelectType()

{ 

//这里重写反选和全选方法，因为再次使用原先的会导致页面上的选项也会被选  

　　var ids=$("input[name='fp_arr[]']");

　　 for(var i=0;i<ids.length;i++)

　　{  

 　　　　if(ids[i].checked==true)

　　　　{    

　　　　　　ids[i].checked="";   

　　　　}else{   

 　　　　　　ids[i].checked="checked";  

 　　　}  

　　}

}

//全选

function allSelectType()

{  

　　var ids=$("input[name='fp_arr[]']");  

　　for(var i=0;i<ids.length;i++)

　　{   

　　　　ids[i].checked="checked";  

　　}

}
</script>