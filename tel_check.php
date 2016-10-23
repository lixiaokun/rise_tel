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
	require './common/double_fpage.php';
	
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
    if($_SESSION['user_js'] != '1' && $_SESSION['user_js'] != '4'){
	    $whe[] ="fp_name='{$_SESSION[uid]}'";
	    $param.="&fp_name={$_SESSION[uid]}";
	}elseif(isset($_arr['fp_name']) && strlen($_arr['fp_name'])){
		$whe[] ="fp_name='{$_arr[fp_name]}'";
	    $param.="&fp_name={$_arr[fp_name]}";
	}
	/**
	将原来的按照data_base中tel_status筛选改为按照tel_bd中的status筛选
	*/
	// if($_arr['daofang'] == 'qxz'){
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
	// }elseif($_arr['daofang'] == 2){
	// 	//查询所有已到访
	// 	$whe[] ="status in (3,4,5,6)";
	//     $param.="&tel_status={$_arr[tel_status]}";

	// }else{
	// 	//查询所有未到访
	// 	$whe[] ="status not in (3,4,5,6)";
	//     $param.="&tel_status={$_arr[tel_status]}";
	// }
    


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
    //拨打次数
    if(!empty($_arr['bd_cs'])){
    	if(strstr($_arr['bd_cs'], '>')){
    		$number = str_replace('>', '',$_arr['bd_cs']);
    		$having ="having count(no)>'{$number}'";
	    	$param.="&bd_cs={$_arr[bd_cs]}";
    	}elseif(strstr($_arr['bd_cs'], '-')){
    		$number_arr = explode('-', $_arr['bd_cs']);
    		$number_min = $number_arr[0];
    		$number_max = $number_arr[1];
    		$having ="having count(no)>='{$number_min}' and count(no)<='{$number_max}'";
	    	$param.="&bd_cs={$_arr[bd_cs]}";
		}else{
			$having ="having count(no)='{$_arr[bd_cs]}'";
	    	$param.="&bd_cs={$_arr[bd_cs]}";
		}
    }

    if(empty($whe)){
        $where="where fp_status=1";
        $uri="alreadybd.php";
    }else{
        $where="where fp_status=1 and ".implode(" and ", $whe);
        $uri="alreadybd.php?".$param;
    }

    $page=double_fpage("tel_bd",'data_base',$where, $uri, "15",$having);

    // $sql = 'SELECT b.`no`,count(distinct no), b.`tel`, b.`s_name`, b.`sex`, b.`age`, b.`important`, b.`dt_name`, b.`dt_date`, b.`fp_status`, b.`fp_name`, b.`qudao`, b.`p_name`, b.`school`, b.`tel_status`,t.`bd_date` FROM `data_base` as b LEFT join tel_bd as t on b.no=t.telno '.$where. " group by no  order by UNIX_TIMESTAMP(bd_date) DESC {$page[limit]} ";
    //$sql = 'SELECT b.`no`,count(distinct no), b.`tel`, b.`s_name`, b.`sex`, b.`age`, b.`important`, b.`dt_name`, b.`dt_date`, b.`fp_status`, b.`fp_name`, b.`qudao`, b.`p_name`, b.`school`, b.`tel_status`,t.`bd_date` FROM `data_base` as b  right join tel_bd as t on b.no=t.telno '.$where. "   order by UNIX_TIMESTAMP(bd_date) DESC,b.no DESC {$page[limit]} ";
    // $sql = 'SELECT b.`no`, b.`tel`, b.`s_name`, b.`sex`, b.`age`, b.`important`, b.`dt_name`, b.`dt_date`, b.`fp_status`, b.`fp_name`, b.`qudao`, b.`p_name`, b.`school`, t.`status`,t.`bd_date`,count(b.tel) as bd_cs 
    // 		FROM `data_base` as b  
    // 		join tel_bd as t on b.no=t.telno '.$where. " 
    // 		group by t.telno ". $having ."
    // 		order by UNIX_TIMESTAMP(bd_date) DESC,bd_cs DESC {$page[limit]} ";
    $sql = 'SELECT count(t.telno) as bd_cs,t.bd_date,t.status,b.s_name,b.tel,b.school,b.dt_name,b.qudao,b.fp_name,b.no FROM 
    (select * from tel_bd order by UNIX_TIMESTAMP(bd_date) DESC) as t 
    join data_base as b on b.no=t.telno 
     '.$where." group by telno ". $having ." order by id DESC {$page[limit]}";
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
		$tr .= '<td ><a target="_blank" href="telbd.php?no='. $row['no'] .'">'. $row['bd_cs'] .'</a></td>';
		$tr .= '<td >'. $all_status[$row['status']] .'</td>';
		$tr .= '<td ><a href="telbd.php?no='. $row['no'] .'">'. $row['s_name'] .'</a></td>';
		$tr .= '<td >'. ($row['sex'] == 1? '男':'女') .'</td>';
		$tr .= '<td >'. $row['age'] .'</td>';
		$tr .= '<td >'. $row['school'] .'</td>';
		$tr .= '<td >'. $row['tel'] .'</td>';
		$tr .= '<td >'. $row['p_name'] .'</td>';
		$tr .= '<td >'. $row['dt_name'] .'</td>';
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
		</style>
	</head>
	<body>

          			<div id='tj' style='margin:0 auto;width:90%'>
						<fieldset>
							<legend>已拨打电话</legend>
						<form action='./tel_check.php' method='get'>
							拨打日期：<input class="Wdate" type="text" name="bd_date_s" onClick="WdatePicker({dateFmt:'yyyy-M-d'})"<?php echo 'value='.$_arr['bd_date_s'];?>>至<input class="Wdate" type="text" name="bd_date_e" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" <?php echo 'value='.$_arr['bd_date_e'];?>>
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
							拨打次数：<input type="text" name="bd_cs" placeholder='格式：2、1-3、>3' value="<?php echo $_arr['bd_cs'];?>">
							<!-- 到访状态：<select name='daofang'>
										<option value='qxz'>请选择</option>
										<option value='1' <?php if($_arr['daofang'] == 1) echo 'selected';?>>未到访</option>
										<option value='2' <?php if($_arr['daofang'] == 2) echo 'selected';?>>已到访</option>
							</select> -->

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
			                  <TH  scope=col>拨打日期</TH>
			                  <TH  scope=col>拨打次数</TH>
			                  <TH  scope=col>状态</TH>
			                  <TH  scope=col>学生姓名</TH>
			                  <TH  scope=col>性别</TH>
			                  <TH  scope=col>年龄</TH>
			                  <TH  scope=col>学校</TH>
			                  <TH  scope=col>电话号码</TH>
			                  <TH  scope=col>家长姓名</TH>
			                  <TH  scope=col>地推人</TH>
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