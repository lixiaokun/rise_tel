<?php
	/**
	按条件遍历demo列表 上周   本周 及 下周
	@_GET['sj']   sz   上周
				  bz   本周
				  xz   下周

	2016年3月5日
	更新内容：
		邀约demo成功后弹窗提示然后关闭弹出窗口
	*/
	require './common/qx.php';
	require './common/yey.php';
	header('content-type:text/html;charset=utf-8');
	$sj = $_GET['sj'];
	$date=date('Y-m-d');  //当前日期
	$first=1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
	$w=date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
	$now_start=date('Y-n-j',strtotime("$date -".($w ? $w - $first : 6).' days')); 

	if(in_array($sj, array('bz','xz','sz'))){

		switch ($sj) {
			case 'sz':
				$start = date('Y-n-j',strtotime("$now_start - 7 days"));   
				$end =  date('Y-n-j',strtotime("$now_start - 1 days"));
				break;
			
			case 'bz':
				$start = date('Y-n-j',strtotime("$date -".($w ? $w - $first : 6).' days')); 
				$end =  date('Y-n-j',strtotime("$now_start +6 days"));
				break;  
			case 'xz':
				$start = date('Y-n-j',strtotime("$now_start +7 days"));  
				$end =  date('Y-n-j',strtotime("$now_start +13 days"));   
				break;
			default:
				$start = date('Y-n-j',strtotime("$date -".($w ? $w - $first : 6).' days')); 
				$end =  date('Y-n-j',strtotime("$now_start +6 days")); 
				break;
		}
		$sql = "select * from demo_base where UNIX_TIMESTAMP(demodate) >= UNIX_TIMESTAMP('$start') and UNIX_TIMESTAMP(demodate) <= UNIX_TIMESTAMP('$end')";
		$result = mysql_query($sql);
		$trs = '';
		while($row = mysql_fetch_assoc($result)){
			$trs .= '<tr>';
			$trs .= "<td><input type='radio' name='demoid' value='{$row[id]}'></td>";
			$trs .= "<td>{$row['demodate']}</td>";
			$trs .= "<td>". ($row['demotime'] < 18 ? ($row['demotime'] > 12 ? '下午':'上午') : '晚上') ."</td>";
			$trs .= "<td>{$row['demotime']}</td>";
			$rs = "select count(id) as total from data_mx where demoid={$row['id']}";
			$rs_row = mysql_fetch_assoc(mysql_query($rs));
			//$trs .= "<td><a href='demomx.php?demoid=$row[id]'>{$rs_row['total']}</a></td>";
			$trs .= "<td>{$rs_row['total']}</td>";
			$trs .= "<td>{$demo_lb[$row[demotype]]}</td>";
			$trs .= "<td>{$row['bz']}</td>";
			$trs .= '</tr>';
		}
	}

	/*
		把家长约入具体的demo场次
	*/
	if(isset($_POST['sub'])){
		 if(!empty($_POST['demoid'])){
			//print_r($_POST);
			$demoid = $_POST['demoid'];
			$tel = $_POST['tel'];
			//约入demo一定是插入一条数据，因为一个家长可能参加多场demo
			$check = "select id from data_mx where telno='$tel' and demoid='$demoid'";
			$check_result = mysql_query($check);
			$check_row = mysql_fetch_assoc($check_result);
			if($check_row['id']){
				echo '<script>alert("请勿重复邀约！");history.back();</script>';
				die;
			}
			$yy_date = date("Y-n-j",time());
			$yy_name = $_SESSION['login'];
			$insert_sql = "insert into data_mx(`telno`,`demoid`,`yy_date`,`yy_name`)values('$tel','$demoid','$yy_date','$yy_name')";
			$result = mysql_query($insert_sql);
			if($result){
				//echo "<script>location.href='demo_xy.php?demoid=$demoid'</script>";
				echo "<script>alert('邀约成功!');self.close();</script>";
				die;
			}else{
				echo '<script>alert("邀约出错！请联系管理员");history.back();</script>';
			}
		}else{
			echo '<script>history.back();</script>';
		}
		die;
	}







?>
<html>
  <head> 
	<script type="text/javascript" src="./My97DatePicker/WdatePicker.js"></script>
	<link href="./css/sta_tab_main2.css" rel="stylesheet" type="text/css" /> 
	<link href="./css/index.css" rel="stylesheet" type="text/css" />
	<link href="./css/shouye.css" rel="stylesheet" type="text/css" />
	<link href="./css/biaoge.css" rel="stylesheet" type="text/css" />
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
  </head> 
<body>
	<br/>
    <form action="./demoap.php" method='post'>    
	  <table border="0" cellpadding="0" id="demolist" cellspacing="1" width="800"
					class="xuanze02">
					<tr class="bg_tr02">
			<td align="left" colspan="7" >
			<table width="100%" >
			<tr><td class="listbut" align="left">
			  	<input type="button" class="bluebuttoncss02" value="邀约上周"   onclick="location.href='demoap.php?sj=sz&tel=<?php echo $_GET[tel]?>'" id="invitationcurr"/>
			  	<input type="button" class="bluebuttoncss02" value="邀约本周"   onclick="location.href='demoap.php?sj=bz&tel=<?php echo $_GET[tel]?>'" id="invitationcurr"/>
			  	<input type="button" class="bluebuttoncss02" value="邀约下周"  onclick="location.href='demoap.php?sj=xz&tel=<?php echo $_GET[tel]?>'" id="invitationnext"/>
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td></tr></table></td>
		</tr>
        <tr>
          <th width="8%">请选择</th>
          <th width="15%">日期</th>
          <th width="10%">时段</th>
          <th width="10%">时间</th>
          <th width="10%">人数</th>
          <th width="10%">班型</th>       
          <th width="20%">备注 </th>
        </tr>
        <?php
        	echo $trs;
        	echo "<input type='hidden' name='tel' value='$_GET[tel]'>";
        ?>
        <tr>
        	<td colspan="7">
				<input type='submit' name='sub' value='提交'>
				<input type="button" id='关闭' value="关闭" onclick="javascript:window.close();">
			</td>
		</tr>
      </table>
    </form>
</body>
</html>