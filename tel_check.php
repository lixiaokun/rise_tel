<?php
	/**
		2016年3月5日
		更新内容：
			1 修改单表分页为多表
			2 已拨打电话重新拨打（老单拨打的处理）
				将拨打状态重新置为未拨打状态99 并且将电话再次分配
				只有ccm有权限进行老单再分配

	*/

	require './common/qx.php';
	require './common/yey.php';
	require './common/fpage.php';
	
	// $sql = "select no from data_base where tel_status!='99'";
	// $res = mysql_query($sql);
	// while ($row = mysql_fetch_assoc($res)) {
	// 	$sql1 = "select bd_date from tel_bd where telno={$row[no]} order by UNIX_TIMESTAMP(bd_date) desc limit 1";
	// 	$res1 = mysql_query($sql1);
	// 	$row1 = mysql_fetch_row($res1);
	// 	$insert = "update data_base set bd_date='$row1[0]' where no={$row[no]}";
	// 	if(!mysql_query($insert))
	// 		echo $insert,'<br>';
	// }

	error_reporting(1);

	$_arr=isset($_POST["sub"]) ? $_POST : $_GET;
	//cc名单
	$cc_list = '<option value="">请选择</option>';
	foreach ($cc as $key => $value) {
		if(array_key_exists($_arr['fp_name'],$cc) && $_arr['fp_name'] == $key)
			$cc_list .= '<option value="'. $key .'" selected=1>'. $value .'</option>';
		else
			$cc_list .= '<option value="'. $key .'">'. $value .'</option>';
	}
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
	//学校列表
	$school_list = '<select name="school"><option value="all">全部</option>';
	foreach ($yey as $value) {
		if(trim($_arr['school']) == trim($value))
			$school_list .= "<option value='$value' selected=1 >$value</option>";
		else
			$school_list .= "<option value='$value'>$value</option>";
	}
	$school_list .= '</select>';
	/**
		处理老单分配  2016年3月5日
	*/
	if(!empty($_POST['fp_name']) && !empty($_POST['fp_arr'])){
		$sql = 'update data_base set `tel_status`=99,`fp_date`="'. date('Y-n-j',time()) .'",`fp_name`="'. $_POST['fp_name'] .'" where (';
		foreach ($_POST['fp_arr'] as $key => $value) {
			$sql .= 'no='. $value .' or ';
		}
		$sql = substr($sql, 0,-3) . ') and fp_status=1';
		mysql_query($sql);
		if(!mysql_affected_rows()){
			$filename = './log/fp_log' . date('YmdHis',time()) . 'log';
			file_put_contents($filename,$sql);
		}else{
			echo '<script>location.href="alreadybd.php"</script>';
		}
		die;
	}
	$whe=array();       
    $param="";
	if(isset($_arr['fp_name']) && strlen($_arr['fp_name'])){
		$whe[] ="fp_name='{$_arr[fp_name]}'";
	    $param.="&fp_name={$_arr[fp_name]}";
	}

	if(isset($_arr["tel_status"])){
    	if($_arr['tel_status'] != 'all'){
    		$whe[] ="tel_status='{$_arr[tel_status]}'";
    		$param.="&tel_status={$_arr[tel_status]}";
    	}else{
    		$whe[] ="tel_status!='99'";
    		$param.="&tel_status!='99'";
    	}
    }else{
    	$whe[] ="tel_status!='99'";
    	$param.="&tel_status!='99'";
    }
    //年龄
    if(!empty($_arr['age'])){
    	if(strstr($_arr['age'], '>')){
    		$number = str_replace('>', '',$_arr['age']);
    		$whe[] =" age>'{$number}'";
	    	$param.="&age={$_arr[age]}";
    	}elseif(strstr($_arr['age'], '-')){
    		$number_arr = explode('-', $_arr['age']);
    		$number_min = $number_arr[0];
    		$number_max = $number_arr[1];
    		$whe[] =" age>='{$number_min}' and age<='{$number_max}'";
	    	$param.="&age={$_arr[age]}";
		}else{
			$whe[] =" age='{$_arr[age]}'";
	    	$param.="&age={$_arr[age]}";
		}
    }


    if(!empty($_arr["bd_date_s"])) {
        $whe[] ="UNIX_TIMESTAMP(bd_date) >= UNIX_TIMESTAMP('{$_arr[bd_date_s]}')";   
        $param.="&bd_date_s={$_arr[bd_date_s]}";
    }

   	if(!empty($_arr["bd_date_e"])) {
        $whe[] ="UNIX_TIMESTAMP(bd_date) <= UNIX_TIMESTAMP('{$_arr[bd_date_e]}')";   
        $param.="&bd_date_e={$_arr[bd_date_e]}";
    }
    if(!empty($_arr["qudao"])) {
        $whe[] ="qudao = '{$_arr[qudao]}'";   
        $param.="&qudao={$_arr[qudao]}";
    }
    if(!empty($_arr["school"]) && $_arr['school'] != 'all') {
        $whe[] ="school = '{$_arr[school]}'";   
        $param.="&school={$_arr[school]}";
    }
    if(empty($whe)){
        $where="where fp_status=1";
        $uri="tel_check.php";
    }else{
        $where="where fp_status=1 and ".implode(" and ", $whe);
        $uri="tel_check.php?".$param;
    }

    $page=fpage('data_base',$where, $uri, "15");

    $sql = "select no,tel_status,important,s_name,sex,age,school,tel,qudao,fp_name,tags_final,bd_date from data_base {$where} order by UNIX_TIMESTAMP(bd_date) DESC {$page[limit]}";
    // echo $sql;
	$result = mysql_query($sql);
	$tr = '';
	$xh = 1;
	$xls = array();

	while($row = mysql_fetch_assoc($result)){
		$tr .= $row['important'] == 1 ? '<tr style="color:red">' : '<tr>';
		$tr .= '<td><input type="checkbox" name="fp_arr[]" value="'. $row['no'] .'"></td>';
		$tr .= '<td >'. $xh .'</td>';		
		$tr .= '<td >'. $row['bd_date'] .'</td>';
		$t_sql = "select tag_name from tags_base where id in ({$row['tags_final']})";
		$t_res = mysql_query($t_sql);
		$tag_names = '';
		while($t_row = mysql_fetch_assoc($t_res)){
			$tag_names .= '<span class="checkoutTags">' .$t_row['tag_name'] . '</span>';
		}
		$tr .= '<td >'. $tag_names .'</td>';
		$tr .= '<td >'. $all_status[$row['tel_status']] .'</td>';
		$tr .= '<td ><a href="telbd.php?no='. $row['no'] .'">'. $row['s_name'] .'</a></td>';
		$tr .= '<td >'. ($row['sex'] == 1? '男':'女') .'</td>';
		$tr .= '<td >'. $row['age'] .'</td>';
		$tr .= '<td >'. $row['school'] .'</td>';
		$tr .= '<td >'. $row['tel'] .'</td>';
		$tr .= '<td >'. $row['qudao'] .'</td>';
		$tr .= '<td >'. $cc[$row['fp_name']] .'</td>';
		$tr .= '</tr>';
		$xh++;
		$xls[$xh] = $row;
	}


?>
<html>
	<head>
		<title>电话查询</title>
		<META http-equiv="content-type" content="html/text;charset=utf-8">
		<script type="text/javascript" src="common/jquery.js"></script>
		<script language="javascript" type="text/javascript" src="./My97DatePicker/WdatePicker.js"></script>
		<META content="MSHTML 6.00.2900.5848" name=GENERATOR>
		<link rel="stylesheet" href="css/table.css">
		<style type="text/css">
			body{text-align: center;}
			th,td{text-align: center;}
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
	.checkoutTags{color:#333333;display:block;float:left;height:20px;line-height:20px;overflow:hidden;margin:0 10px 5px 0px;padding:0 10px 0 5px;white-space:nowrap;font-size: 14px}
	.checkoutTags{padding: 0 5px}
		</style>
	</head>
	<body>

          			<div id='tj' style='margin:0 auto;width:90%'>
						<fieldset>
							<legend>老单再分配</legend>
						<form action='./tel_check.php' method='get'>
							最后拨打日期：<input class="Wdate" type="text" name="bd_date_s" onClick="WdatePicker({dateFmt:'yyyy-M-d'})"<?php echo 'value='.$_arr['bd_date_s'];?>>至<input class="Wdate" type="text" name="bd_date_e" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" <?php echo 'value='.$_arr['bd_date_e'];?>>
							状态：<select name='tel_status'>
								<?php
									echo $status_list;
								?>
							</select>
							<?php
							if($_SESSION['user_js'] == 1){
							echo "咨询师：<select name='fp_name'>$cc_list</select>";
							}
							?>
							渠道：<select name='qudao'>
								<?php
									echo $qudao_list;
								?>
							</select>
							年龄：<input type="text" name="age" placeholder='格式：2、1-3、>3' value="<?php echo $_arr['age'];?>">
							学校：<?php echo $school_list;?>
							<input type='submit' value='查询' name='sub'>
						</form>
						</fieldset>
					</div>
					<form action='./alreadybd.php' method="post">

						<TABLE style="width:90%">
			      			<TR>
			      			<th style="width:13%"><input type="button" value="全选" class='classname' onclick="allSelectType();"/><input type="button" value="反选" class='classname' onclick="invertSelectType();"/>
					</th>
			                  <TH  scope=col>序号</TH>
			                  <TH  scope=col>最后拨打日期</TH>
			                  <TH  scope=col>标签</TH>
			                  <TH  scope=col>状态</TH>
			                  <TH  scope=col>学生姓名</TH>
			                  <TH  scope=col>性别</TH>
			                  <TH  scope=col>年龄</TH>
			                  <TH  scope=col>学校</TH>
			                  <TH  scope=col>电话号码</TH>
			                  <TH  scope=col>渠道</TH>
			                  <TH  scope=col>咨询师</TH>
			                </TR>
				<?php 
					echo $tr; echo '<tr ><td colspan="14"  style="text-align:center">'.$page["fpage"].'</td></tr>';
					if($_SESSION['user_js'] == 1){
				?>
				<tr>
					<td colspan="14"  style="text-align:center"><select name='fp_name'><?php echo $cc_list;?></select>
					<input type="submit" name="fp" value="老单分配">
					</td>
				</tr>
				<?php
				}
				?>
						</TABLE>
						</form>
	</body>
	<script>
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
</html>