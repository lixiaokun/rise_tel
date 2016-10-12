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
		header("Location:../login/index.php");
	}	
 	/**
 	权限检查结束
	*/
 	if($_POST['type'] == 'zhou'){
 		$date = $_POST['date'];
 		$time = strtotime($date);
 		$week = date('N',$time);
 		$mon = $time - ($week-1)*(60*60*24);
 		$monday = date('Y-n-j',$mon);
 		$sun = $time + (7-$week)*(60*60*24);
 		$sunday = date('Y-n-j',$sun);
 		/**
			周一到周三数据
 		*/
		$wendesday = date('Y-n-j',$mon + 2*60*60*24);
		$sql = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)>=UNIX_TIMESTAMP('$monday') and UNIX_TIMESTAMP(demodate)<=UNIX_TIMESTAMP('$wendesday')";
 		//上门人数
 		$sql1 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)>=UNIX_TIMESTAMP('$monday') and UNIX_TIMESTAMP(demodate)<=UNIX_TIMESTAMP('$wendesday') and sm_status=1";
 		//关单人数
 		$sql2 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)>=UNIX_TIMESTAMP('$monday') and UNIX_TIMESTAMP(demodate)<=UNIX_TIMESTAMP('$wendesday') and (bm_status=4 or bm_status=5)";
 		$res = mysql_query($sql);
 		$row = mysql_fetch_row($res);
 		$yy = $row[0];
 		$res1 = mysql_query($sql1);
 		$row1 = mysql_fetch_row($res1);
 		$sm = $row1[0];
 		$res2 = mysql_query($sql2);
 		$row2 = mysql_fetch_row($res2);
 		$bm = $row2[0];
 		/**
 			周四
 		*/
 		$thursday = date('Y-n-j',$mon + 3*60*60*24);
 		$sql = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where  UNIX_TIMESTAMP(demodate)=UNIX_TIMESTAMP('$thursday')";
 		//上门人数
 		$sql1 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where  UNIX_TIMESTAMP(demodate)=UNIX_TIMESTAMP('$thursday') and sm_status=1";
 		//关单人数
 		$sql2 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where  UNIX_TIMESTAMP(demodate)=UNIX_TIMESTAMP('$thursday') and (bm_status=4 or bm_status=5)";
 		$res = mysql_query($sql);
 		$row = mysql_fetch_row($res);
 		$yy1 = $row[0];
 		$res1 = mysql_query($sql1);
 		$row1 = mysql_fetch_row($res1);
 		$sm1 = $row1[0];
 		$res2 = mysql_query($sql2);
 		$row2 = mysql_fetch_row($res2);
 		$bm1 = $row2[0];
 		/**
 			周五
 		*/
 		$friday = date('Y-n-j',$mon + 4*60*60*24);
 		$sql = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)=UNIX_TIMESTAMP('$friday')";
 		//上门人数
 		$sql1 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)=UNIX_TIMESTAMP('$friday') and sm_status=1";
 		//关单人数
 		$sql2 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)=UNIX_TIMESTAMP('$friday') and (bm_status=4 or bm_status=5)";
 		$res = mysql_query($sql);
 		$row = mysql_fetch_row($res);
 		$yy2 = $row[0];
 		$res1 = mysql_query($sql1);
 		$row1 = mysql_fetch_row($res1);
 		$sm2 = $row1[0];
 		$res2 = mysql_query($sql2);
 		$row2 = mysql_fetch_row($res2);
 		$bm2 = $row2[0];
 		/**
 			周六
 		*/
 		$saturday = date('Y-n-j',$mon + 5*60*60*24);
 		$sql = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)=UNIX_TIMESTAMP('$saturday')";
 		//上门人数
 		$sql1 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)=UNIX_TIMESTAMP('$saturday') and sm_status=1";
 		//关单人数
 		$sql2 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)=UNIX_TIMESTAMP('$saturday') and (bm_status=4 or bm_status=5)";
 		$res = mysql_query($sql);
 		$row = mysql_fetch_row($res);
 		$yy3 = $row[0];
 		$res1 = mysql_query($sql1);
 		$row1 = mysql_fetch_row($res1);
 		$sm3 = $row1[0];
 		$res2 = mysql_query($sql2);
 		$row2 = mysql_fetch_row($res2);
 		$bm3 = $row2[0];
 		/**
 			周日
 		*/
 		$sql = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)=UNIX_TIMESTAMP('$sunday')";
 		//上门人数
 		$sql1 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)=UNIX_TIMESTAMP('$sunday') and sm_status=1";
 		//关单人数
 		$sql2 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)=UNIX_TIMESTAMP('$sunday') and (bm_status=4 or bm_status=5)";
 		$res = mysql_query($sql);
 		$row = mysql_fetch_row($res);
 		$yy4 = $row[0];
 		$res1 = mysql_query($sql1);
 		$row1 = mysql_fetch_row($res1);
 		$sm4 = $row1[0];
 		$res2 = mysql_query($sql2);
 		$row2 = mysql_fetch_row($res2);
 		$bm4 = $row2[0];
 		

 		/**
 			全周数据
 		*/

 		//邀约人数
 		$sql = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)>=UNIX_TIMESTAMP('$monday') and UNIX_TIMESTAMP(demodate)<=UNIX_TIMESTAMP('$sunday')";
 		//上门人数
 		$sql1 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)>=UNIX_TIMESTAMP('$monday') and UNIX_TIMESTAMP(demodate)<=UNIX_TIMESTAMP('$sunday') and sm_status=1";
 		//关单人数
 		$sql2 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)>=UNIX_TIMESTAMP('$monday') and UNIX_TIMESTAMP(demodate)<=UNIX_TIMESTAMP('$sunday') and (bm_status=4 or bm_status=5)";

 		$res = mysql_query($sql);
 		$row = mysql_fetch_row($res);
 		$yy5 = $row[0];
 		$res1 = mysql_query($sql1);
 		$row1 = mysql_fetch_row($res1);
 		$sm5 = $row1[0];
 		$res2 = mysql_query($sql2);
 		$row2 = mysql_fetch_row($res2);
 		$bm5 = $row2[0];
 	/**
		周合计
 	*/
		$sml = round(($sm5/$yy5)*100,2).'%';
		$gdl = round(($bm5/$sm5)*100,2).'%';
		$zzl = round(($bm5/$yy5)*100,2).'%';
 		$tr = <<<html
 			 <tr>
	            <td>$yy</td><td>$sm</td><td>$bm</td>
	            <td>$yy1</td><td>$sm1</td><td>$bm1</td>
	            <td>$yy2</td><td>$sm2</td><td>$bm2</td>
	            <td>$yy3</td><td>$sm3</td><td>$bm3</td>
	            <td>$yy4</td><td>$sm4</td><td>$bm4</td>
	            <td>$yy5</td><td>$sm5</td><td>$bm5</td>
	            <td>$sml</td><td>$gdl</td><td>$zzl</td>
	         </tr>
html;
 	}
 	if($_POST['type'] == 'yue'){
 		$time = $_POST['date'];
 		$date = date('Y年n月',strtotime($time));
 		$first = date('Y-n-1',strtotime($time));
 		$last =  date('Y-n-t',strtotime($time));
 		//邀约人数
 		$sql = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)>=UNIX_TIMESTAMP('$first') and UNIX_TIMESTAMP(demodate)<=UNIX_TIMESTAMP('$last')";
 		//上门人数
 		$sql1 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)>=UNIX_TIMESTAMP('$first') and UNIX_TIMESTAMP(demodate)<=UNIX_TIMESTAMP('$last') and sm_status=1";
 		//关单人数
 		$sql2 = "select count(m.id) from demo_base as db join data_mx as m on m.demoid=db.id where UNIX_TIMESTAMP(demodate)>=UNIX_TIMESTAMP('$first') and UNIX_TIMESTAMP(demodate)<=UNIX_TIMESTAMP('$last') and (bm_status=4 or bm_status=5)";
 		$res = mysql_query($sql);
 		$row = mysql_fetch_row($res);
 		$yy = $row[0];
 		$res1 = mysql_query($sql1);
 		$row1 = mysql_fetch_row($res1);
 		$sm = $row1[0];
 		$res2 = mysql_query($sql2);
 		$row2 = mysql_fetch_row($res2);
 		$bm = $row2[0];

 		$sml = round(($sm/$yy)*100,2).'%';
		$gdl = round(($bm/$sm)*100,2).'%';
		$zzl = round(($bm/$yy)*100,2).'%';
 		$table = <<<sos
 		<table class="listtab" cellspacing="1" align="center" id="listtab" style="width:80%">
	        <tr class="title"><th  colspan="22">Demo邀约报表</th></tr>
	        <tr>	         
	              <td rowspan="2"  class="tht" width="11%">$date</td>
	              <td colspan="3"  class="tht" width="13%">邀约人数</td>
	              <td colspan="3"  class="tht" width="13%">上门人数</td>
	              <td colspan="3"  class="tht" width="13%">关单人数</td>
	              <td colspan="3"  class="tht" width="13%">承诺上门率<br>实际上门/邀约人数</td>
	              <td colspan="3"  class="tht" width="13%">当面转化率<br>关单人数/上门人数</td>
	              <td colspan="3"  class="tht" width="13%">当月总转<br> 关单人数/邀约人数</td>
	        </tr>
	        <tr>
	              <td colspan="3"  width="13%">$yy</td>
	              <td colspan="3"  width="13%">$sm</td>
	              <td colspan="3"  width="13%">$bm</td>
	              <td colspan="3"  width="13%">$sml</td>
	              <td colspan="3"  width="13%">$gdl</td>
	              <td colspan="3"  width="13%">$zzl</td>
	        </tr>
	    </table> 
sos;
 	}

 
 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Demo邀约报表</title>
	 
<!-- 	<link href="../css/sta_tab_main2.css" rel="stylesheet" type="text/css" />
 -->	 
 	<link href="../css/sta_tab.css" rel="stylesheet" type="text/css" />

	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="-1" />
	<script language="javascript" type="text/javascript" src="../My97DatePicker/WdatePicker.js"></script>
	<script type="text/javascript" src="../common/jquery.js"></script>
</head>
 
<body style="padding-top: 10px;">
	<center>	
	    <form id="searchform" action="demo_tj.php" method="post">
	    <table id="querytable" align="center" class="detailtab" cellspacing="1" id="querytable" style="width:80%">
	       	<tr><th colspan="6">查询</th></tr>	       
			<tr>
	            <td  class="tdt" width="10%">日期</td>
	            <td> 	   			
	            <input class="Wdate" style="width:150px" type="text" name="date" onClick="WdatePicker({dateFmt:'yyyy-M-d'})" value=<?php echo $_POST['date'];?>>
	            </td>
	            
	       </tr>
	       <tr>  
	           <td class="bbtn" style="height:40px;" colspan="6" >	           
	               <input class="btn1"  type="button" value="查询周" onclick="sub('z')" />
	               <input class="btn1"  type="button" value="查询月" onclick="sub('y')" />
	               <input type="hidden" value="" id="type" name='type'>
	           </td>
	       </tr>
	    </table>
	   </form>	  
	  
	       <br/>
<?php
	if($_POST['type'] == 'zhou'){
?>	      
	    <table class="listtab" cellspacing="1" align="center" id="listtab" style="width:80%">
	        <tr class="title"><th  colspan="22">Demo邀约报表  	        
	           </th></tr>
	        <tr>	         
	              <td colspan="3"  class="tht" width="13%">周一至周三</td>
	              <td colspan="3"  class="tht" width="13%">周四</td>
	              <td colspan="3"  class="tht" width="13%">周五</td>
	              <td colspan="3"  class="tht" width="13%">周六</td>
	              <td colspan="3"  class="tht" width="13%">周日</td>
	              <td colspan="3"  class="tht" width="13%">周合计</td>
	              <td colspan="4"  class="tht" width="24%">一周转化率</td>
	        </tr>
	        <tr>
	            <th class="tht" >邀约人数</th><th class="tht" >上门人数</th><th class="tht" >关单人数</th> 
	            <th class="tht" >邀约人数</th><th class="tht" >上门人数</th><th class="tht" >关单人数</th>
	            <th class="tht" >邀约人数</th><th class="tht" >上门人数</th><th class="tht" >关单人数</th>
	            <th class="tht" >邀约人数</th><th class="tht" >上门人数</th><th class="tht" >关单人数</th>
	            <th class="tht" >邀约人数</th><th class="tht" >上门人数</th><th class="tht" >关单人数</th>
	            <th class="tht" >邀约人数</th><th class="tht" >上门人数</th><th class="tht" >关单人数</th>	            
	            <th class="tht" >承诺上门率<br>实际上门/邀约人数</th>
	            <th class="tht" >当面转化率<br>关单人数/上门人数</th>
	            <th class="tht" >当周总转<br> 关单人数/邀约人数  </th>
	        </tr>
			<?php
				echo $tr;
			?>
	    </table>   
<?php
	}
	if($_POST['type'] == 'yue'){
		echo $table;
	}
?>

	</center>
	<script>
	function sub(t){
		var d='';
		d = $.trim($('.Wdate').val());
		if(d == ''){
			alert('请选择日期')
			return;
		}
		if(t == 'z'){
			$('#type').val('zhou');
		}
		if(t == 'y'){
			$('#type').val('yue');
		}
		$('#searchform').submit();
	}
	</script>
</body> 
</html>
