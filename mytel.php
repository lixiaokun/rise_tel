<?php
	require './common/qx.php';
	require './common/yey.php';
	require './common/fpage.php';

	// /**
	// 处理ajax修改报名状态
	// */
	// if(isset($_POST['ajax']) && $_POST['ajax'] === 'ok' && is_numeric($_POST['no'])){
	// 	$zt = $_POST['zt'];
	// 	$no = $_POST['no'];
	// 	$zt_arr = array('dj' => 4,'qf' => 5,'tf' => 6);
	// 	//修改最近一次咨询纪要的状态 以及 data_base的最终状态  data_mx中的状态
	// 	$sql_1 = "update data_base set tel_status='$zt_arr[$zt]' where no=$no";
	// 	$sql_2 = "update tel_bd set status='$zt_arr[$zt]' where telno=$no order by UNIX_TIMESTAMP(bd_date) DESC limit 1";
	// 	$sql_3 = "update data_mx set bm_status='$zt_arr[$zt]' where telno= and demoid=";
	// 	if(mysql_query($sql_1) && mysql_query($sql_2)){
	// 		echo 'success';
	// 	}else{
	// 		echo 'fail';
	// 	}
	// 	die;
	// }
	/**
	处理电话提醒
	*/
	$today = date('Y-n-j',time());
	$tx_sql = "select DISTINCT b.tel,b.s_name,t.telno from tel_bd as t join data_base as b on b.no=t.telno where b.tel_status='8' and t.again_time='$today' and fp_name='$_SESSION[uid]'";
	$tx_res = mysql_query($tx_sql);
	$tx_html = '今天需要联系';
	$flag = 0;
	while($tx_row = mysql_fetch_assoc($tx_res)){
		$flag = 1;
		$tx_html .= $tx_row['s_name'].'-<a href="mytel.php?tel='.$tx_row['tel'].'">'.$tx_row['tel'] . '</a> ';
	}
	/**
	处理电话提醒结束
	*/

	$_arr=isset($_POST["sub"]) ? $_POST : $_GET;
	//电话状态
	// if($_arr['tel_status'] === 'all')
	// 	$status_list .= '<option value="all" selected=1>全部</option>';
	// else
	// 	$status_list .= '<option value="all">全部</option>';
	if(!isset($_arr['tel_status']))
		$_arr['tel_status'] = 99;
	foreach ($all_status as $key => $value) {
		if($_arr['tel_status'] == $key)
			$status_list .= '<option value="'. $key .'" selected=1>'. $value .'</option>';
		else
			$status_list .= '<option value="'. $key .'">'. $value .'</option>';
	}

	$whe=array();       
    $param="";


    $whe[] ="fp_name='{$_SESSION[uid]}'";

    $param.="&fp_name={$_SESSION[uid]}";


    if(!empty($_arr["tel"])) {
        $whe[] ="tel= '{$_arr[tel]}'";   
        $param.="&tel={$_arr[tel]}";
    }else{
    	/**
			2016-10-11 修改 将默认排序由未拨打电话改为显示全部电话，按分配日期倒序
		
    	*/
	    if(isset($_arr["tel_status"])){
	    	$whe[] ="tel_status='{$_arr[tel_status]}'";
	    	$param.="&tel_status={$_arr[tel_status]}";
	    }else{
	    	$whe[] ="tel_status=99";
	    	$param.="&tel_status=99";
	    }

    	// if(isset($_arr["tel_status"]) && $_arr['tel_status'] != 'all'){
	    // 	$whe[] ="tel_status='{$_arr[tel_status]}'";
	    // 	$param.="&tel_status={$_arr[tel_status]}";
	    // }

	    if(!empty($_arr["fp_date_s"])) {
	        $whe[] ="UNIX_TIMESTAMP(fp_date) >= UNIX_TIMESTAMP('{$_arr[fp_date_s]}')";   
	        $param.="&fp_date_s={$_arr[fp_date_s]}";
	    }

	   	if(!empty($_arr["fp_date_e"])) {
	        $whe[] ="UNIX_TIMESTAMP(fp_date) <= UNIX_TIMESTAMP('{$_arr[fp_date_e]}')";   
	        $param.="&fp_date_e={$_arr[fp_date_e]}";
    	}
	}


    if(empty($whe)){
        $where="where fp_status=1";
        $uri="mytel.php";
    }else{
        $where="where fp_status=1 and ".implode(" and ", $whe);
        $uri="mytel.php?".$param;
    }

    $page=fpage("data_base",$where, $uri, "15");

    $sql = 'SELECT `no`, `tel`, `s_name`, `sex`, `age`, `important`, `dt_name`, `dt_date`, `fp_status`, `fp_name`, `qudao`, `p_name`, `school`, `fp_date`,`tel_status` FROM `data_base` '.$where. " order by UNIX_TIMESTAMP(fp_date) DESC, tel_status DESC  {$page[limit]} ";
	$result = mysql_query($sql);
	$tr = '';
	$xh = 1;
	while($row = mysql_fetch_assoc($result)){
		$tr .= $row['important'] == 1 ? '<tr style="color:red">' : '<tr>';
		$tr .= '<td>'. $xh .'</td>';		
		$tr .= '<td>'. $row['fp_date'] .'</td>';
		$tr .= '<td><a href="telbd.php?no='. $row['no'] .'">'. $row['s_name'] .'</a></td>';
		$tr .= '<td>'. ($row['sex'] == 1? '男':'女') .'</td>';
		$tr .= '<td>'. $row['age'] .'</td>';
		$tr .= '<td>'. $row['school'] .'</td>';
		$tr .= '<td><a href="telbd.php?no='. $row['no'] .'">'. $row['tel'] .'</a></td>';
		$tr .= '<td>'. $row['dt_name'] .'</td>';
		$tr .= '<td>'. $row['qudao'] .'</td>';
		$tr .= '<td>'. $all_status[$row['tel_status']] .'</td>';
		$tr .= '<td>'. $cc[$row['fp_name']] .'</td>';
		$tr .= '</tr>';
		$xh++;
	}


?>
<html>
	<head>
		<META http-equiv="content-type" content="html/text;charset=utf-8">
		<title>我的电话</title>			
		<link rel="stylesheet" href="css/table.css">
		<script type="text/javascript" src="common/jquery.js"></script>
		<script type="text/javascript" src="./common/layer/layer.js"></script>
		<script language="javascript" type="text/javascript" src="./My97DatePicker/WdatePicker.js"></script>
		<META content="MSHTML 6.00.2900.5848" name=GENERATOR>
		<style>
			body{text-align: center}
			td,th{text-align: center}
			#tj{width:80%;margin:0 auto;height:50px;}
			fieldset{border-radius: 5px}
			marquee{color:red;font-size: 20px;width:80%;padding-top: 10px}
			table{border-radius: 5px}
			.Wdate{width:10%;}
			th{font-size: 12px}
		</style>
	</head>
	<body>
		<?php 
			if($flag){
				echo '<marquee  onmouseover="this.stop()"   onMouseOut="this.start()">'.$tx_html.'</marquee>';
			}
		?>
		<form action='./mytel.php' method="post">		
        <div id='tj'>		
        	<fieldset>
        		<legend>我的电话</legend>
			分配日期：<input class="Wdate" type="text" name="fp_date_s" onClick="WdatePicker({dateFmt:'yyyy-M-d'})"<?php echo 'value='.$_arr['fp_date_s'];?>>至<input class="Wdate" type="text" name="fp_date_e" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" <?php echo 'value='.$_arr['fp_date_e'];?>>
				状态：<select name='tel_status'>
					<?php
						echo $status_list;
					?>
				</select>
				电话号码：<input type='text' name='tel' value=''>
				<input type='submit' value='查询' name='sub'>
				<input type='button' value='本周拨打情况' id="checkweekdata">
			</fieldset>
		</div>
		<TABLE>
  			<TR>
              <TH>序号</TH>
              <TH>分配日期</TH>
              <TH>学生姓名</TH>
              <TH>性别</TH>
              <TH>年龄</TH>
              <TH>学校</TH>
              <TH>电话号码</TH>
              <TH>地推人</TH>
              <TH>渠道</TH>
              <TH>状态</TH>
              <TH>咨询师</TH>
<!-- 			  <TH>操作</TH>
 -->            </TR>
			<?php echo $tr; echo '<tr><td colspan="11" >'.$page["fpage"].'</td></tr>';?>
		</TABLE>
		</form>
		<script>
			$('#checkweekdata').click(function(){
				$.get('checkweekdata.php',function(str){
					layer.open({
					    type: 1,
					    title:'本周拨打情况',
					    area: ['700px', '400px'],
					    closeBtn: 1, //显示关闭按钮
					    shift: 4,
					    offset: '100px',
					    scrollbar: 'false',
					    shadeClose: true, //开启遮罩关闭
					    content: str,
					});
				});
			});
		</script>
	</body>
</html>