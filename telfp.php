<?php
	/**
		2016年3月5日
		更新内容：
			将地推人改为兼职和非兼职两类
	*/
	require './common/qx.php';
	require "./common/yey.php";
	require './common/fpage.php';
	$_arr=isset($_POST["sub"]) ? $_POST : $_GET;

	error_reporting(0);

	$datalist = '<datalist id="schools">';
	foreach ($yey as $key => $value) {
		$datalist .= '<option value="'. $value .'">' . $value . '</option>';
	}
	$datalist .= '</datalist>';
	$qudaolist = '<select name="qudao"><option value="">全部</option>';
	foreach ($qudao as $key => $value) {
		if(in_array($_arr['qudao'],$qudao) && $_arr['qudao'] == $value)
			$qudaolist .= '<option value="'. $value .'" selected=1>' . $value . '</option>';
		else
			$qudaolist .= '<option value="'. $value .'">' . $value . '</option>';
	}
	$qudaolist .= '</select>';
	// $dt_list = '<option value="">请选择</option>';
	// foreach ($dt_name as $key => $value) {
	// 	if(in_array($_arr['dt_name'],$dt_name) && $_arr['dt_name'] == $value)
	// 		$dt_list .= '<option value="'. $value .'" selected=1>' . $value . '</option>';
	// 	else
	// 		$dt_list .= '<option value="'. $value .'">' . $value . '</option>';
	// }
	if($_arr['dt_name'] == '兼职'){
		$dt_list ='<option value="">全部</option><option value="兼职" selected=1>兼职</option><option value="非兼职">非兼职</option>';
	}
	elseif($_arr['dt_name'] == '非兼职'){
		$dt_list ='<option value="">全部</option><option value="兼职">兼职</option><option value="非兼职" selected=1>非兼职</option>';
	}else{
		$dt_list ='<option value="">全部</option><option value="兼职">兼职</option><option value="非兼职">非兼职</option>';
	}
	//cc名单
	$cc_list = '<option value="">请选择</option>';
	foreach ($cc as $key => $value) {
		$cc_list .= '<option value="'. $key .'">'. $value .'</option>';
	}

	//执行分配操作
	if(isset($_POST['fp']) && array_key_exists($_POST['fp_name'], $cc) && !empty($_POST['fp_arr'])){

		$sql = 'update data_base set `fp_status`=1,`fp_date`="'. date('Y-n-j',time()) .'",`fp_name`="'. $_POST['fp_name'] .'" where (';
		$i = 0;
		foreach ($_POST['fp_arr'] as $key => $value) {
			$sql .= 'no='. $value .' or ';
			$i++;
		}
		$sql = substr($sql, 0,-3) . ') and fp_status=0';
		
		mysql_query($sql);
		if(!mysql_affected_rows()){
			$filename = './log/fp_log' . date('YmdHis',time()) . '.log';
			file_put_contents($filename,$sql);
			echo '<script>alert("分配失败，联系管理员！");location.href="telfp.php?page='.$_POST[current_page].'"</script>';
			die;
		}
		echo '<script>alert("'. $i .'条数据分配成功！");location.href="telfp.php?page='.$_POST[current_page].'"</script>';
		die;
	}

	$whe=array();       
    $param="";

    if(!empty($_arr["tel"])) {
        $whe[] ="tel= '{$_arr[tel]}'";   
        $param.="&tel={$_arr[tel]}";
    }else{
	    if(!empty($_arr["dt_name"])) {
	    	if($_arr["dt_name"] == '兼职'){
		        $whe[] ="dt_name='{$_arr[dt_name]}'";

		        $param.="&dt_name={$_arr[dt_name]}";
	    	}else{
	    		$whe[] ="dt_name!='兼职'";

		        $param.="&dt_name!='兼职'";
	    	}
	         
	    }

	    if(!empty($_arr["dt_date_s"])) {
	        $whe[] ="UNIX_TIMESTAMP(dt_date) >= UNIX_TIMESTAMP('{$_arr[dt_date_s]}')";   
	        $param.="&dt_date_s={$_arr[dt_date_s]}";
	    }

	   	if(!empty($_arr["dt_date_e"])) {
	        $whe[] ="UNIX_TIMESTAMP(dt_date) <= UNIX_TIMESTAMP('{$_arr[dt_date_e]}')";   
	        $param.="&dt_date_e={$_arr[dt_date_e]}";
	    }

	    if(!empty($_arr["school"])) {
	        $whe[] ="school='{$_arr[school]}'";

	        $param.="&school={$_arr[school]}";
	         
	    }

	    if(!empty($_arr["qudao"])) {
	        $whe[] ="qudao='{$_arr[qudao]}'";

	        $param.="&qudao={$_arr[qudao]}";
	         
	    }
	}

    if(empty($whe)){
        $where="where fp_status=0";
        $uri="telfp.php";
    }else{
        $where="where fp_status=0 and ".implode(" and ", $whe);
        $uri="telfp.php?".$param;
    }


    $page=fpage("data_base",$where, $uri, "15");

	//遍历未分配电话
	$wfp = 'select no,s_name,sex,age,important,dt_name,dt_date,qudao,p_name,school,tel from data_base '.$where. " order by UNIX_TIMESTAMP(dt_date) DESC {$page[limit]} ";
	//按条件遍历未分配电话
	$res = mysql_query($wfp);
	$tr = '';
	$xh = 1;
	while($row = mysql_fetch_assoc($res)){
		$tr .= $row['important'] == 1 ? '<tr style="color:red">' : '<tr>';
		$tr .= '<td style="text-align:center"><input type="checkbox" name="fp_arr[]" value="'. $row['no'] .'"></td>';
		$tr .= '<td>'. $xh .'</td>';
		$tr .= '<td>'. $row['dt_date'] .'</td>';
		$tr .= '<td>'. $row['s_name'] .'</td>';
		$tr .= '<td>'. ($row['sex'] == 1? '男':'女') .'</td>';
		$tr .= '<td>'. $row['age'] .'</td>';
		$tr .= '<td>'. $row['tel'] .'</td>';
		$tr .= '<td>'. $row['school'] .'</td>';
		$tr .= '<td>'. $row['p_name'] .'</td>';
		$tr .= '<td>'. $row['dt_name'] .'</td>';
		$tr .= '<td>'. $row['qudao'] .'</td>';
		$tr .= '</tr>';
		$xh++;
	}
?>
<title>待分配电话</title>
<link rel="stylesheet" href="css/table.css">
<meta http-equiv="content-type" content="html/text;charset=utf-8">
<style>
	th,td{text-align: center;}
	body {TEXT-ALIGN: center;}
	#tj{width:80%;margin: 0 auto;}
	.inp{width:10%;}
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
<script type="text/javascript" src="common/jquery.js"></script>
<script language="javascript" type="text/javascript" src="./My97DatePicker/WdatePicker.js"></script>
<form action='./telfp.php' method="post">
		<div id='tj'>
		<fieldset>
				<legend>待分配电话</legend>
			<form action='./alreadyfp.php' method='get'>
				地推日期：<input class="Wdate inp" type="text" name="dt_date_s" onClick="WdatePicker({dateFmt:'yyyy-M-d'})"<?php echo 'value='.$_arr['dt_date_s'];?>>至<input class="Wdate inp" type="text" name="dt_date_e" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" <?php echo 'value='.$_arr['dt_date_e'];?>>
				地推人：<select name='dt_name'><?php echo $dt_list;?></select>
				学校：<input type='text' class='inp'name='school' list='schools' value="<?php echo $_arr['school'];?>"><?php echo $datalist;?>
				渠道：<?php echo $qudaolist;?>
				电话：<input type='' name='tel'  class='inp'>
				<input type='submit' value='查询' name='sub'>
			</form>
			</fieldset>
		</div>
<form action='./telfp.php' method="post">
	<table border="1" cellspacing="0" cellpadding="0" width="800" align="center">
		<tr>
			<th><input type="button" class='className' value="全选" onclick="allSelectType();"/><input type="button" class='className' value="反选" onclick="invertSelectType();"/></th>
			<th>序号</th><th>地推日期</th><th>学生姓名</th><th>性别</th><th>年龄</th><th>电话号码</th><th>学校</th><th>家长姓名</th><th>地推人</th><th>渠道</th>
		</tr>
		<?php echo $tr;echo '<tr><td colspan="12" style="text-align:center">'.$page["fpage"].'</td></tr>';
		$user_js = $_SESSION['user_js'];
						if($user_js == '1'){
		?>
		<tr>
			<td colspan="11" style="text-align:center"><select name='fp_name'><?php echo $cc_list;?></select><input type="hidden" name="current_page" value='<?php echo $_GET[page];?>'><input type="submit" name="fp" value="分配"></td>
		</tr>
		<?php 
			}
		?>
	</table>
</form>

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