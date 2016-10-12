<?php
	require './common/qx.php';
	require './common/yey.php';

	/**
		demo排课重复时或者拍错时删除demo场次
	*/
	if($_GET['action'] == 'del' && !empty($_GET['demoid'])){
		$query = "delete from demo_base where id=$_GET[demoid]";
		mysql_query($query);
		echo '<script>location.href="demo_list.php"</script>';
		die;
	}


	//默认显示本周的demo列表
	$first=1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
	$date=date('Y-n-j'); 
	$w=date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
	$now_start=date('Y-n-j',strtotime("$date -".($w ? $w - $first : 6).' days'));
	if(!empty($_POST['start']) && !empty($_POST['end']) && strtotime($_POST['end']) >= strtotime($_POST['start'])){
		$start = $_POST['start'];
		$end = $_POST['end'];
	}
	else{
		$start = date('Y-n-j',strtotime("$date -".($w ? $w - $first : 6).' days')); 
		$end =  date('Y-n-j',strtotime("$now_start +6 days"));
	}
	$sql = "select * from demo_base where UNIX_TIMESTAMP(demodate)>=UNIX_TIMESTAMP('$start') and UNIX_TIMESTAMP(demodate)<=UNIX_TIMESTAMP('$end') order by UNIX_TIMESTAMP(demodate) ASC";
	$res = mysql_query($sql);
	$tds = '';
	while($row = mysql_fetch_assoc($res)){
		$tds .= '<tr>';
		$weeks = date('D',strtotime($row['demodate']));
		$tds .= "<td>{$row['demodate']}({$weeks})</td>";
		$demotime = substr($row['demotime'],0,2);
		if($demotime > 6 && $demotime < 12)
			$d_time = '上午';
		elseif($demotime >= 12 && $demotime <=19)
			$d_time = '下午';
		else
			$d_time = '晚上';

		$tds .= "<td>".$d_time."</td>";
		$tds .= "<td>{$row['demotime']}</td>";

		$rs = "select count(id) from data_mx where demoid={$row['id']}";
		$rs_row = mysql_fetch_row(mysql_query($rs));
		$tds .= "<td>$rs_row[0]</td>";
		$tds .= "<td>{$demo_lb[$row['demotype']]}</td>";
		$tds .= "<td>{$row['bz']}</td>";
		if($rs_row[0]){
			$tds .= "<td><a href='demo_xy.php?demoid={$row[id]}'>学员管理 </a></td>";
		}
		else{
			if($_SESSION['user_js'] == 1){
				$tds .= "<td>学员管理 | <a href='demo_list.php?action=del&demoid={$row[id]}'>删除</a></td>";
			}else{
				$tds .= "<td>学员管理</td>";
			}
		}
		$tds .= '</tr>';
	}
?>
<html>
	<head>
		
		<meta http-equiv="Cache-Control" content="no-cache" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />

	    <link rel="stylesheet" href="./css/validationEngine.jquery.css" type="text/css"/>
	    <link rel="stylesheet" href="./css/template.css" type="text/css"/> 
	    <!-- 蓝色版本样式 -->
		<link href="./css/sta_tab.css" rel="stylesheet" type="text/css" />
		<!--紫色版本样式
		<link href="./css/sta_tab_main2.css" rel="stylesheet" type="text/css" /> 
 		-->    
 		<link href="./css/index.css" rel="stylesheet" type="text/css" />
		<link href="./css/shouye.css" rel="stylesheet" type="text/css" />
		<link href="./css/biaoge.css" rel="stylesheet" type="text/css" /> 
		<script language="javascript" type="text/javascript" src="./My97DatePicker/WdatePicker.js"></script>
		<script type="text/javascript" src="./common/jquery.js"></script>
		<style>
			body{text-align:center;}
			table{margin:0 auto;}
			#demo_list{display:none;margin:0 auto;}
			#demo_list td{text-align: center}
			#demo_list a{cursor: pointer;}
		</style>
	</head>
 
	<body>	
    	<form action='./demo_list.php' method='post'>
		<table align="center" class="detailtab" cellspacing="1" id="querytable" style="width:80%">
			<tr>
				<th colspan="6">查询条件</th>
			</tr>
            <tr>
              <td class="tdt" width="15%">时间</td>
              <td width="38%" class="tdt" style="text-align:left">

				<input class="" type="text" name="start" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" value='<?php echo $_POST['start'];?>'>
				&nbsp;至&nbsp;
				<input class="" type="text" name="end" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" value='<?php echo $_POST['end'];?>'>            
              </td>

            </tr>
			<tr>
				<td class="bbtn" colspan="6" >
				    <input type="submit"  name='sub' class="btn1" value="查&nbsp;&nbsp;询" />
					<input type="reset"  class="btn1" value="重&nbsp;&nbsp;置" />
				</td>
			</tr>	            
        </table> 
        </from>
            <!-- demo 列表 -->
   	<table align="center" class="detailtab" cellspacing="1"  style="width:80%;text-align:center">
        <tr>
          <th class="tht" width="10%" style="text-align:center">日期</th>
          <th class="tht" width="8%" style="text-align:center">时段</th>
          <th class="tht" width="7%" style="text-align:center">时间</th>
          <th class="tht" width="6%" style="text-align:center">人数</th>
          <th class="tht" width="12%" style="text-align:center">班型</th>      
          <th class="tht" width="16%" style="text-align:center">备注</th>
          <th class="tht" width="27%" style="text-align:center">操作</th>
        </tr>
	         <?php
	         echo $tds;
	         ?>                                     
        
       <!-- demo 安排列表-->
       
      <!--demo列表 分页
       <tr>
            <td colspan="8" >共有[1]页[4]条信息 
				<input type="button" class="btn1" value="☜首页" onclick="javascript:;"/>			
				<span id="four">&nbsp;<font color="red">1</font>&nbsp;</span>	
				<input type="button" class="btn1" value="末页☞"/>
				<input type="text"  value="1"  id="strpnum" size="2" ></input>
	 			<input type="button" class="btn1" value="跳转" onclick=""/>
			</td>
        </tr>     
        -->
    </body>
</html>