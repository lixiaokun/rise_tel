<?php
	/**
		2016年3月5日
		更新内容：
			邀约错误后，删除相关该学生相关demo场次，还要把电话得最终状态重新置为
			上门后需要更新 最终状态
			2016年4月21日增加了报名日期和上门日期
	*/
	require './common/qx.php';
	header("Content-Type:text/html;charset=utf8");
	/**
	处理修改报名状态
	*/
	if($_GET['bm'] && is_numeric($_GET['telno']) && is_numeric($_GET['demoid']) && !empty($_GET['noo'])){
		$zt = $_GET['zt'];
		$telno = $_GET['telno'];
		$demoid = $_GET['demoid'];
		$noo = $_GET['noo'];
		$zt_arr = array('dj' => 4,'qf' => 5,'tf' => 6);
		$today = date('Y-n-j',time());
		//修改最近一次咨询纪要的状态 以及 data_base的最终状态  data_mx中的状态
		$sql_1 = "update data_base set tel_status='$zt_arr[$zt]' where tel=$telno";
		$sql_2 = "update tel_bd set status='$zt_arr[$zt]' where telno=$noo order by UNIX_TIMESTAMP(bd_date) DESC limit 1";
		$sql_3 = "update data_mx set bm_status='$zt_arr[$zt]',bm_date='$today' where telno=$telno and demoid=$demoid";
		if(mysql_query($sql_1) && mysql_query($sql_2) && mysql_query($sql_3)){
			echo '<script>alert("修改成功")</script>';
		}else{
			echo '<script>alert("修改失败")</script>';
		}
		echo '<script>location.href="demo_xy.php?demoid='. $demoid .'"</script>';
		die;
	}

	if($_GET['action'] == 'dy' && !empty($_GET['demoid'])){
		$demoid = $_GET['demoid'];
		require "common/PHPExcel/Classes/PHPExcel.php";
		require_once 'common/PHPExcel/Classes/PHPExcel/IOFactory.php';  
		require_once 'common/PHPExcel/Classes/PHPExcel/Writer/Excel5.php';

		$resultPHPExcel = new PHPExcel(); 

		//设值 
		$resultPHPExcel->getActiveSheet()->setCellValue('A1','序号');
		// $resultPHPExcel->getActiveSheet()->mergeCells("A1:A2");		
        $resultPHPExcel->getActiveSheet()->setCellValue('B1','孩子姓名');
        // $resultPHPExcel->getActiveSheet()->mergeCells("B1:B2");	
        $resultPHPExcel->getActiveSheet()->setCellValue('C1','年龄');
        // $resultPHPExcel->getActiveSheet()->mergeCells("C1:C2");	
        $resultPHPExcel->getActiveSheet()->setCellValue('D1','性别');
        // $resultPHPExcel->getActiveSheet()->mergeCells("D1:D2");	
        $resultPHPExcel->getActiveSheet()->setCellValue('E1','所在学校');
        // $resultPHPExcel->getActiveSheet()->mergeCells("E1:E2");	
        $resultPHPExcel->getActiveSheet()->setCellValue('F1','联系方式');
        // $resultPHPExcel->getActiveSheet()->mergeCells("F1:F2");	
        $resultPHPExcel->getActiveSheet()->setCellValue('G1','家庭住址');
        // $resultPHPExcel->getActiveSheet()->mergeCells("G1:G2");	
        $resultPHPExcel->getActiveSheet()->setCellValue('H1','信息来源');
        // $resultPHPExcel->getActiveSheet()->mergeCells("H1:H2");	
        $resultPHPExcel->getActiveSheet()->setCellValue('I1','咨询师');
        // $resultPHPExcel->getActiveSheet()->mergeCells("I1:I2");	
        $resultPHPExcel->getActiveSheet()->setCellValue('J1','咨询类型');
        // $resultPHPExcel->getActiveSheet()->mergeCells("J1:J2");	       
        $resultPHPExcel->getActiveSheet()->mergeCells("K1:O1");
        $resultPHPExcel->getActiveSheet()->setCellValue('K1','备注');
        $resultPHPExcel->getActiveSheet()->setCellValue('K2','孩子程度');
        $resultPHPExcel->getActiveSheet()->setCellValue('L2','家长情况');
        $resultPHPExcel->getActiveSheet()->setCellValue('M2','兴趣班安排');
        $resultPHPExcel->getActiveSheet()->setCellValue('N2','关注点及抗拒点');
        $resultPHPExcel->getActiveSheet()->setCellValue('O2','暖场关键点');
        //设置居中
        $resultPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('L1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('M1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('N1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $resultPHPExcel->getActiveSheet()->getStyle('O1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置宽度
        $resultPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(13);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(13);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(13);
        $resultPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(13);


		//遍历列表
		$sql = "select b.*,r.uname,t.zxjy,db.demotime,db.demodate from tel_bd as t right join data_base as b on b.no=t.telno join rise_user as r on r.id=t.uid join demo_base as db on db.id=t.demoid where t.demoid='$demoid'";
		$result = mysql_query($sql);
		$trs = '';
		$xh = 1;
		$i = 3;
		while($row = mysql_fetch_assoc($result)){		
			$resultPHPExcel->getActiveSheet()->setCellValue('A' . $i, $xh);//序号
			$resultPHPExcel->getActiveSheet()->setCellValue('B' . $i, $row['s_name']);//孩子姓名
			$resultPHPExcel->getActiveSheet()->setCellValue('C' . $i, $row['age']); //年龄
			$sex = $row['sex'] ? '男' : '女';
			$resultPHPExcel->getActiveSheet()->setCellValue('D' . $i, $sex); //性别
			$resultPHPExcel->getActiveSheet()->setCellValue('E' . $i, $row['school']); //所在学校
			$resultPHPExcel->getActiveSheet()->setCellValue('F' . $i, $row['tel']); //联系方式
			$resultPHPExcel->getActiveSheet()->setCellValue('G' . $i, $row['address']); //家庭住址
			$resultPHPExcel->getActiveSheet()->setCellValue('H' . $i, $row['qudao']); //信息来源
			$resultPHPExcel->getActiveSheet()->setCellValue('I' . $i, $row['uname']); //咨询师
			$resultPHPExcel->getActiveSheet()->setCellValue('J' . $i, 'call-out');  //咨询类型
			$resultPHPExcel->getActiveSheet()->setCellValue('L' . $i, $row['zxjy']);//家长情况
			$xh++;
			$i++;
			$filename = $row['demodate'].$row['demotime'].'.xls';
		}
		//设置导出文件名 

		$outputFileName = $filename; 

		$xlsWriter = new PHPExcel_Writer_Excel5($resultPHPExcel); 

		//ob_start(); ob_flush(); 

		header("Content-Type: application/force-download"); 

		header("Content-Type: application/octet-stream"); 

		header("Content-Type: application/download"); 

		header('Content-Disposition:inline;filename="'.$outputFileName.'"'); 

		header("Content-Transfer-Encoding: binary"); 

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 

		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 

		header("Pragma: no-cache"); 

		$xlsWriter->save( "php://output" );
		die;
	}
	if(isset($_GET['demoid']) && !empty($_GET['demoid'])){
		$demoid = $_GET['demoid'];
		$tel = $_GET['tel'];
		$no = $_GET['telno'];
		//处理到访
		if($_GET['action'] == 'df'){
			$today = date('Y-n-j',time());
			$query = "update data_mx set sm_status=not sm_status,sm_date=if(sm_status=1,'$today','') where telno='$tel' and demoid='$demoid'";
			mysql_query($query);
			//2 和 3 切换 到访3 删除到访2
			$query1 = "update data_base set tel_status=if(tel_status=2,3,2) where tel='$tel'";
			mysql_query($query1);
			//删除报名状态
			$query2 = "update data_mx set bm_status=0,bm_date='' where telno='$tel' and demoid='$demoid' and sm_status=0";
			if(mysql_query($query2) && !empty($no)){
			//删除到访后将咨询历史中的状态改回承诺上门  
				$query3 = "update tel_bd set status=2 where telno='$no' and demoid='$demoid'";
				mysql_query($query3);

			}
			echo "<script>location.href='demo_xy.php?demoid=$demoid'</script>";
			die;
		}

		//处理删除
		if($_GET['action'] == 'del' && !empty($_GET['no'])){
			$query = "delete from data_mx where demoid='$demoid' and telno='$tel'";
			mysql_query($query);
			//$query1 = "update tel_bd set demoid='',status='9' where telno='$_GET[no]' and demoid='$demoid'";
			$query1 = "update tel_bd set demoid='' where telno='$_GET[no]' and demoid='$demoid'";
			mysql_query($query1);
			echo "<script>location.href='demo_xy.php?demoid=$demoid'</script>";
			die;
		}

		$bm_arr = array(0 => '未报名',4 => '订金',5 => '全费','6' => '退费',);
		//遍历列表
		$sql = "select b.*,r.uname,r.id as uid,t.zxjy,db.demotime,db.demodate,db.id as demoid from tel_bd as t 
				right join data_base as b on b.no=t.telno 
				join rise_user as r on r.id=t.uid 
				join demo_base as db on db.id=t.demoid 
				where t.demoid='$demoid'";
		$result = mysql_query($sql);
		$trs = '';
		$xh = 1;
		while($row = mysql_fetch_assoc($result)){
			$df = "select sm_status,bm_status from data_mx where telno='$row[tel]' and demoid='$demoid'";
			$df_res = mysql_query($df);
			$df_row = mysql_fetch_row($df_res);
			$trs .= '<tr>';
			$trs .= "<td>$xh</td>";
			if($_SESSION['user_js'] == 1 || $_SESSION['user_js'] == 2){
				$trs .= '<td><a href="telbd.php?no='.$row['no'].'">'. $row['p_name'] .'</a></td>';
				$trs .= '<td><a href="telbd.php?no='.$row['no'].'">'. $row['s_name'] .'</a></td>';
			}else{
				$trs .= "<td>{$row['p_name']}</td>";
				$trs .= "<td>{$row['s_name']}</td>";
			}
			$trs .= "<td>{$row['tel']}</td>";
			$trs .= '<td>承诺上门</td>';
			$trs .= "<td>{$row['qudao']}</td>";
			$trs .= "<td>{$row['school']}</td>";
			$trs .= '<td></td>';
			if(strlen($row['zxjy']))
				$zxjy = mb_substr($row['zxjy'],0,5,'utf-8').'...';
			else
				$zxjy = '';
			$trs .= '<td class="zxjy"><input type="hidden" value="'. $row['zxjy'] .'">'.$zxjy.'</td>';
			if($df_row[0])
				$trs .= '<td>已到访</td>';
			else
				$trs .= '<td>未到访</td>';
			$trs .= "<td>{$row['uname']}</td>";
			if($df_row[0]){
				$trs .= "<td>				 
							<a href='./demo_xy.php?demoid=$demoid&tel=$row[tel]&action=df&telno=$row[no]'>删除到访</a>  |
							<select class='cz' onchange='chn(this)''>
					 			<option value='qxz'>请选择</option>
					 			<option value='dj'>订金</option>
					 			<option value='qf'>全费</option>
					 			<option value='tf'>退费</option>
							</select>
							{$bm_arr[$df_row[1]]}    	
							<input type='hidden' value='$row[tel]' name='telno'>
							<input type='hidden' value='$row[demoid]' name='demoid'>
							<input type='hidden' value='$row[no]' name='noo'>
							| <a href='javascript:#' class='dm'>当面咨询</a>
				    	</td>";
			}
			else{
				if(in_array($_SESSION['user_js'], array('1','2')) && $row['uid'] == $_SESSION['uid']){
					$trs .= "<td>				 
							<a href='javascript:del($demoid,$row[tel],$row[no])'>删除</a> |
							<a href='./demo_xy.php?demoid=$demoid&tel=$row[tel]&action=df'>到访</a>	        	
				    	</td>";
				}else{
				$trs .= "<td>				 
							<a href='./demo_xy.php?demoid=$demoid&tel=$row[tel]&action=df'>到访</a>       	
				    	</td>";
				}
			}
			$trs .= '</tr>';
			$xh++;
			$demoname = $row['demodate'].' '.$row['demotime'];
		}
	}

?>

 
 
 
<html>
  <head>
  		<meta http-equiv="content-type" content="html/text;charset=utf-8">
		<script type="text/javascript" src="./My97DatePicker/WdatePicker.js"></script>
<!-- 		<link href="./css/sta_tab_main2.css" rel="stylesheet" type="text/css" />
 -->		<link href="./css/sta_tab.css" rel="stylesheet" type="text/css" />		 
		<script type="text/javascript" src='./common/jquery.js'></script>
		<script type="text/javascript" src="./common/layer/layer.js"></script>
		<style type="text/css">
			body{text-align: center;}
			table{margin: 0 auto;}
		</style>
</head>
 
<body>
	<table class="listtab" cellspacing="1" align="center" style="width:90%;">
		<tr class="title">
			<th colspan="13">
			<div style="float: left;">
				Demo课邀约客户列表【<?php echo $demoname;?>】
			</div>
			<div style="float: right; padding-right: 17px">
				<input type="button" class="btn1" value="打印" onclick="dy(<?php echo $demoid;?>)"/>
			</div>
			</th>
		</tr>	
    	<tr>
            <th class="tht">序号</th>
            <th class="tht">家长</th>
            <th class="tht">学员</th>
            <th class="tht">手机</th>
            <th class="tht">家长状态</th>
            <th class="tht">渠道</th>
            <th class="tht">在读学校</th>
            <th class="tht">家庭住址</th>
            <th class="tht">咨询纪要</th>
            <th class="tht">是否到访</th>
            <th class="tht">邀约人</th>
            <th class="tht">操作</th>
        </tr>

			<?php echo $trs;?>
        
    </table> 

	<div id="tj_form" style="display:none;">
	  <form method="post" action="./dmzx.php">
			<div>
				<textarea cols=50 rows=10 name="dmzx"></textarea>
			</div>
			<br>
			<div id="hid">
			<input type="hidden"  name="demoid" value="<?php echo $_GET[demoid]?>">
			<input type="submit" value="提交" name="sub">
			</div>
	  </form>
  </div>
	<script type="text/javascript">
		$('.zxjy').mouseover(function(){
			var zxjy = '';
			zxjy = $(this).find('input:hidden').val();
			layer.tips(zxjy, $(this), {tips: [1, '#9C74A6'],time:4000});
		    
		});
		function del(demoid,tel,no){
			if(confirm("确定删除？")){
				location.href="demo_xy.php?action=del&demoid=" + demoid + "&tel=" + tel + "&no=" + no;
			}
		}
		function dy(demoid){
			location.href="demo_xy.php?action=dy&demoid=" + demoid ;
		}
		function chn(d){
			var telno=demoid=zt=noo='';
			telno = $(d).nextAll(':hidden').eq(0).val();
			demoid = $(d).nextAll(':hidden').eq(1).val();
			noo = $(d).nextAll(':hidden').eq(2).val();
			zt = $(d).val();
			if(zt != 'qxz'){
				location.href="demo_xy.php?bm=1&telno="+ telno +"&demoid="+ demoid +"&zt="+zt+"&noo="+noo;
			}
		}

	$('.dm').click(function(){
		var tel = no = nohtml = telhtml = '';
		no = $(this).prevAll(':hidden').eq(0).val();
		tel = $(this).prevAll(':hidden').eq(2).val();
		nohtml = '<input type="hidden"  name="tel" value="'+ tel +'">';
		telhtml = '<input type="hidden"  name="no" value="'+ no +'">';
		$('#hid').append(nohtml);
		$('#hid').append(telhtml);
	    
	    var demoid = '';
	    demoid = $('#hid').find('[name=demoid]').val();
		$.post('dmzx.php',{'action':'ajax','no':no,'demoid':demoid},function(h){
			$('textarea').val(h)
		});

	    layer.open({
	    type: 1,
	    title:'请输入当面咨询纪要',
	    area: ['500px', '300px'],
	    skin: 'layui-layer-demo', //样式类名
	    closeBtn: 1, //显示关闭按钮
	    shift: 2,
	    offset: '100px',
	    scrollbar: 'false',
	    shadeClose: true, //开启遮罩关闭
	    content: $('#tj_form'),
		});
	});
	</script>
</body>
</html>

