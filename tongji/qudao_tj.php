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
		echo '<script>location.href="../login/index.php"</script>';
	}	
 	/**
 	权限检查结束
 	*/
 	require '../common/yey.php';
	header("Content-type: text/html; charset=utf-8"); 
 	/**
	获取学校、渠道、地推人列表
 	*/
 	$datalist = '<datalist id="schools">';
	foreach ($yey as $key => $value) {
		$datalist .= '<option value="'. $value .'">' . $value . '</option>';
	}
	$datalist .= '</datalist>';

	$qudaolist = '<select name="qudao">';
	foreach ($qudao as $key => $value) {
		if(in_array($_POST['qudao'],$qudao) && $_POST['qudao'] == $value)
			$qudaolist .= '<option value="'. $value .'" selected=1>' . $value . '</option>';
		else
			$qudaolist .= '<option value="'. $value .'">' . $value . '</option>';
	}
	$qudaolist .= '</select>';
	$dt_list = '<option value="">请选择</option>';
	foreach ($dt_name as $key => $value) {
		if(in_array($_POST['dt_name'],$dt_name) && $_POST['dt_name'] == $value)
			$dt_list .= '<option value="'. $value .'" selected=1>' . $value . '</option>';
		else
			$dt_list .= '<option value="'. $value .'">' . $value . '</option>';
	}

	/**
	计算各项数据
	*/
	//if(!empty($_POST['date_s']) && !empty($_POST['date_e'])){
		$start = $_POST['date_s'];
		$end = $_POST['date_e'];
		if(strtotime($end) < strtotime($start)){
			echo '<script>alert("日期范围选择错误！");location.href="qudao_tj.php"</script>';
			die;
		}
		$qd = $_POST['qudao'];
		$school = trim($_POST['school']);
		$dt_name = trim($_POST['dt_name']);
		if($qd == '活动'){
			$sql = "select dt_name,count(no) as total,tel_status from data_base as db  ";
		}else{
			$sql = "select school,count(no) as total,tel_status from data_base as db  ";
		}
		$where = "where qudao='$qd' ";
		if(!empty($_POST['date_s']) && !empty($_POST['date_e']))
			$where .= " and UNIX_TIMESTAMP(dt_date)>=UNIX_TIMESTAMP('$start') and UNIX_TIMESTAMP(dt_date)<=UNIX_TIMESTAMP('$end') and qudao='$qd' ";
		if($dt_name != '' && $qd != '活动'){
			$where .= " and dt_name='$dt_name' ";
		}
		if($school != '' && $qd != '活动'){
			$where .= " and school like '%$school%'";
		}
		if($qd == '活动'){
			$where .= " group by dt_name,tel_status";
		}else
			$where .= " group by school,tel_status";
		$result = mysql_query($sql.$where);
		$arr = array();
		while($row = mysql_fetch_assoc($result)){
			if($qd == '活动'){
				$y = $row['dt_name'];
			}else{
				$y = $row['school'];
			}
			$t = $row['total'];
			$s = $row['tel_status'];
			$arr[$y][$s]= $t;
		}
		$trs = '';
		$i = 0;
		foreach ($arr as $key => $value) {
			$total_z += array_sum($value);
			$total_wbd += $value['99'];
        	$total_wx += $value['0'];
        	$total_wjt += $value['7'];
        	$total_zlx += $value['8'];
        	$total_cnsm += $value['2'];
        	$total_wcnsm += $value['1'];
        	$total_zcnwsm += $value['9'];
        	$total_dj += $value['4'];
        	$total_qf += $value['5'];
        	if($i%2 == 0)
				$trs .= "<tr style='background:#eee'>";
			else
				$trs .= "<tr>";
			$trs .= "<td>$key</td>";			
			$trs .= "<td>". array_sum($value) ."</td>";
			$trs .= "<td>".(!empty($value['99']) ? $value['99'] : 0 )."</td>";
			$trs .= "<td>".(!empty($value['0']) ? $value['0'] : 0 )."</td>";
			$trs .= "<td>".(!empty($value['7']) ? $value['7'] : 0 )."</td>";
			$trs .= "<td>".(!empty($value['8']) ? $value['8'] : 0 )."</td>";
			$trs .= "<td>".(!empty($value['2']) ? $value['2'] : 0 )."</td>";
			$trs .= "<td>".(!empty($value['1']) ? $value['1'] : 0 )."</td>";
			$trs .= "<td>".(!empty($value['9']) ? $value['9'] : 0 )."</td>";
			$trs .= "<td>".(!empty($value['4']) ? $value['4'] : 0 )."</td>";
			$trs .= "<td>".(!empty($value['5']) ? $value['5'] : 0 )."</td>";
			$trs .= "</tr>";
			$i++;
		}
	//}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>渠道邀约报表</title>	 
	<link href="../css/sta_tab.css" rel="stylesheet" type="text/css" />
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="-1" />
	<script language="javascript" type="text/javascript" src="../My97DatePicker/WdatePicker.js"></script>
	<script type="text/javascript" src="../common/jquery.js"></script>
</head>
 
<body style="padding-top: 10px;">
	<center>	
	    <form id="searchform" action="qudao_tj.php" method="post">
	    <table id="querytable" align="center" class="detailtab" cellspacing="1" id="querytable" style="width:80%">
	       <tr><th 	colspan="6">查询</th></tr>	
	       
			<tr>
	            <td colspan="2" style="text-align:center"> 	   			
	            日期：<input style="width:100px" class="Wdate" type="text" name="date_s" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" value="<?php echo $_POST['date_s'];?>">至
	            <input style="width:100px" class="Wdate" type="text" name="date_e" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" value="<?php echo $_POST['date_e'];?>">　　
				学校：<input type='text' name='school' list='schools' value="<?php echo $_POST['school'];?>"><?php echo $datalist;?>
				　渠道：<?php echo $qudaolist;?>　
				地推人：<select name='dt_name'><?php echo $dt_list;?></select>
	            </td>
	            
	       </tr>
	       <tr>  
	           <td class="bbtn" style="height:40px;" colspan="6" >	           
	               <input class="btn1"  type="submit" value="查　询" onclick="sub()"/>
	           </td>
	       </tr>
	    </table>
	   </form>	  
	  
	       <br/>    
	    <table class="listtab" cellspacing="1" align="center" id="listtab" style="width:80%">
	        <tr class="title"><th  colspan="22">渠道转化报表  	        
	           </th></tr>
	        <tr style="">	         
	              <td   class="tht" width="13%">渠道</td>
	              <td   class="tht" width="5%">总数</td>
	              <td   class="tht" width="5%">未拨打</td>
	              <td   class="tht" width="5%">无效</td>
	              <td   class="tht" width="5%">未接听</td>
	              <td   class="tht" width="5%">再联系</td>
	              <td   class="tht" width="5%">承诺上门</td>
	              <td   class="tht" width="5%">未承诺上门</td>
	              <td   class="tht" width="5%">承诺未上门</td>
	              <td   class="tht" width="5%">定金</td>
	              <td   class="tht" width="5%">全费</td>
	        </tr>
	        <?php
	        	echo $trs;
	        ?>
	        <tr style="background:#C3D4FC">
	        	<td>合计</td>
	        	<?php
	        	echo "<td>$total_z</td>";
	        	echo "<td>$total_wbd</td>";
	        	echo "<td>$total_wx</td>";
	        	echo "<td>$total_wjt</td>";
	        	echo "<td>$total_zlx</td>";
	        	echo "<td>$total_cnsm</td>";
	        	echo "<td>$total_wcnsm</td>";
	        	echo "<td>$total_zcnwsm</td>";
	        	echo "<td>$total_dj</td>";
	        	echo "<td>$total_qf</td>";
	        	?>
	        </tr>
	    </table>   

	</center>

	<div style="width:100%;height:100px;"></div>
</body> 
</html>