<?php
	/**
		拨打电话详单
		2016年3月5日
		更新内容：
			选择承诺上门自动弹出邀约demo窗口
	*/
	require './common/qx.php';
	require './common/yey.php';
	header('content-type:text/html;charset=utf-8');

	/*
		获取电话详细内容
	*/
	$no = $_GET['no'] ? $_GET['no'] : $_POST['no'];
	if(!$no){
		echo '<script>alert("请勿直接访问本页面！");history.back()</script>';
		die;
	}
	$sql = "select p_name,tel,qudao,gzdw,s_name,sex,age,relation,english,address,school,tel_status from data_base where no=$no";
	$result = mysql_query($sql);
	list($p_name,$tel,$qudao,$gzdw,$s_name,$sex,$age,$relation,$english,$address,$school,$final_status) = mysql_fetch_row($result);
	$gzdw = !empty($gzdw) ? $gzdw : "<input type='text' name='gzdw'>";
	$sex_option = '<select name="sex" class="a_bc">';
	$sex_option .= $sex ? '<option selected value=1>男</option><option value=0>女</option>' : '<option value=1>男</option><option selected value=0>女</option>';
	$sex_option .= "</select>";
	$relation = !empty($relation) ? $relation : "<input type='text' name='relation'  class='a_bc'>";
	$english = !empty($english) ? $english : "<input type='text' name='english'  class='a_bc'>";
	$school_list = '';
	//遍历学校列表
	$school_list = '<select name="school" class="a_bc">';
	foreach ($yey as $value) {
		if(trim($school) == trim($value))
			$school_list .= "<option value='$value' selected=1 >$value</option>";
		else
			$school_list .= "<option value='$value'>$value</option>";
	}
	$school_list .= '</select>';
	/**
		2016年3月18日 修改
	**/
		$s_name1 = "<input type=text name='s_name' value='$s_name' style='width:90px;' class='a_bc'>";
		$s_name1 .= "<input type='hidden' name='no' value='{$no}' id='a_no'><input type='hidden'  id='a_tel' name='tel' value='{$tel}'>";
		$age1 = "<input type=text name='age' value='$age' style='width:50px;'  class='a_bc'>";
		$address1 = "<input type=text name='address' value='$address' style='width:90px;'  class='a_bc'>";

	/*
		获取状态列表
	*/
	$status_list = '<option value="99">请选择</option>';
	foreach ($tel_status as $key => $value) {

		$status_list .= "<option value=$key>$value</option>";
	}
	/*
		获取已有的咨询纪要
	*/
	$zx_sql = "select tel_bd.id as tid,tel_bd.zxjy,tel_bd.status,tel_bd.bd_date,tel_bd.demoid,dmzx,rise_user.uname from tel_bd join rise_user on tel_bd.uid=rise_user.id  where telno=$no";
	$zx_res = mysql_query($zx_sql);
	$zx_history = '';
	while($zx_row = mysql_fetch_assoc($zx_res)){
		foreach ($tel_status as $key => $value) {
			if($key == $zx_row['status'])
				$status_list1 .= "<option value=$key selected>$value</option>";
			else
				$status_list1 .= "<option value=$key >$value</option>";
		}
		$zx_history .= '<tr style="text-align:center">';
		$zx_history .= '<td>'. $all_status[$zx_row['status']] .'</td>';
		$zx_history .= '<td>' . $zx_row['bd_date'] . '</td>';
		$zx_history .= '<td>' . $zx_row['uname'] . '</td>';
		$zx_history .= '<td>' . $zx_row['zxjy'] .'</td>';
		if($zx_row['demoid']){
			$demo = 'select demodate,demotime from demo_base where id='.$zx_row['demoid'];
			$res = mysql_query($demo);
			$row = mysql_fetch_assoc($res);
			$zx_history .= '<td>' . $row['demodate'] . '　'. $row['demotime'] .'</td>';
		}else{
			$zx_history .= '<td> </td>';
		}
		//$zx_history .= '<td><a href="">'. date('Ymd',strtotime($zx_row['bd_date'])) . $tel .'.wav</a></td>';
		$zx_history .= '<td>' . $zx_row['dmzx'] .'</td>';
		$zx_history .= '<td>' . $ly .'</td>';
		/**
		咨询师可修改自己3天内的咨询纪要
		*/
		//当前时间
		$now = time();
		$zx_date = strtotime($zx_row['bd_date']);
		$cha = $now - $zx_date;
		if($cha <= (3*24*3600)){
			$zx_history .= "<td><a href='javascript:changezxjy({$zx_row[tid]})'>修改咨询纪要</a>";
			$zx_history .= "<div style='display:none' id='chgzx'>";
			$zx_history .= '状态：<select name="status" id="status">'.$status_list1.'</select><input class="Wdate" type="hidden" name="again_time" onClick="WdatePicker({dateFmt:\'yyyy-M-d\'})">';
			$zx_history .= "<textarea style='width: 470px; height: 198px;'>" . $zx_row['zxjy'] . "</textarea>";
			$zx_history .= "<input type='hidden' name='tid' value='{$zx_row[tid]}'><input type='button' id='bczx' value='保存'>";
			$zx_history .= "</div>";
			$zx_history .= " | <a href='javascript:telrecord(\"$zx_row[bd_date]\",\"$tel\")'>电话录音</a></td>";
		}else{
			$zx_history .= "<td><a href='javascript:telrecord(\"$zx_row[bd_date]\",\"$tel\")'>电话录音</a></td>";
		}
		$zx_history .= '</tr>';
	}

	$uid = $_SESSION['uid'];

	//保存电话拨打内容
	if(in_array($_POST['status'], array_keys($tel_status)) && isset($_POST['bczx'])){
		$gzdw_tj = $_POST['gzdw'];
		$relation_tj = $_POST['relation'];
		$english_tj = $_POST['english'];
		$status_tj = $_POST['status'];
		$zxjy_tj = $_POST['zxjy'];
		$yy_date = date('Y-n-j',time());

		/**
			根据咨询状态处理当前电话
			'0' => '无效',
			'1' => '未承诺上门',
			'2' => '承诺上门',
			//'3' => '上门未缴费',
			//'4' => '定金',
			//'5' => '全费',
			//'6' => '退费',
			'7' => '未接听',
			'8' => '再联系',
			'9' => '承诺未上门'
		*/
		if($status_tj == '2'){
			//承诺上门
			//获取当前咨询邀约的demoid
			$demoid_sql = "select demoid from data_mx where telno='$tel' and yy_date='$yy_date' order by yy_date DESC limit 1";
			$demoid_res = mysql_query($demoid_sql);
			$demoid_row = mysql_fetch_assoc($demoid_res);
			$demoid = $demoid_row['demoid'];
			if(empty($demoid)){
				echo '<script>alert("请先邀约DEMO！");history.back();</script>';
				die;
			}
			$tj_sql = "insert into tel_bd(`telno`,`zxjy`,`status`,`bd_date`,`uid`,`demoId`)values('$no','$zxjy_tj','$status_tj','$yy_date','$uid','$demoid')";
		}else{
			if($status_tj == '8'){
				$again_time = $_POST['again_time'];
				if(empty($again_time)){
					echo '<script>alert("请选择再联系的时间！");history.back();</script>';
					die;
				}
				$tj_sql = "insert into tel_bd(`telno`,`zxjy`,`status`,`bd_date`,`uid`,`again_time`)values('$no','$zxjy_tj','$status_tj','$yy_date','$uid','$again_time')";
			}else{		
				$tj_sql = "insert into tel_bd(`telno`,`zxjy`,`status`,`bd_date`,`uid`)values('$no','$zxjy_tj','$status_tj','$yy_date','$uid')";
			}
		}
		$up_sql = "update data_base set tel_status=$status_tj where tel='$tel'";
		if(mysql_query($tj_sql) && mysql_query($up_sql)){
			echo '<script>alert("保存成功！");location.href="mytel.php"</script>';
		}else{
			echo '<script>alert("拨打信息保存出错，请联系管理员！");location.href="mytel.php"</script>';
		}

		die;
	}
?>
<html>
	<head>
		<title>电话拨打</title>
		<STYLE type=text/css>
			th{background-color: #EEEEEE;color:black;height: 40px;line-height: 40px;}
			td{height:30px;}
			td input{text-align:center}
			th { text-shadow: 1px 1px 1px #fff; background:#e8eaeb;}
			table {
			overflow:hidden;
			border:1px solid #d3d3d3;
			background:#fefefe;
			/*width:70%;
			margin:5% auto 0;*/
			-moz-border-radius:5px; /* FF1+ */
			-webkit-border-radius:5px; /* Saf3-4 */
			border-radius:5px;
			-moz-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
			-webkit-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
			font:12px/15px "Helvetica Neue",Arial, Helvetica, sans-serif;
			}
		</STYLE>
		<script language="javascript" type="text/javascript" src="./My97DatePicker/WdatePicker.js"></script>
		<script type="text/javascript" src="./common/jquery.js"></script>
		<script type="text/javascript" src="./common/layer/layer.js"></script>

		<META content="MSHTML 6.00.2900.5848" name=GENERATOR>
		<meta http-equiv="content-type" content="html/text;charset=utf-8">
		<link href="./common/Tags/css/lanrenzhijia.css" type="text/css" rel="stylesheet" />
	</head>
	<body >
      	<fieldset>
      	<legend>咨询详情</legend>	
        <form action="telbd.php" method="post">
		<!--家长及学员信息 -->
		<table align="center"   cellspacing="0" width=500>
			<tr>
				<th colspan="4">家长基本信息</th>
			</tr>
<?php
echo <<<ht
			<tr>
				<td>家长姓名</td>
				<td>{$p_name}</td>
				<td>手机</td>
				<td>{$tel}</td>
			</tr>
			<tr>
				<td>工作单位</td>
				<td>{$gzdw}</td>
				<td>渠道</td>
				<td>{$qudao}</td>
			</tr>

		</table>
		<br>
		<table align="center"  cellspacing="0" width=1000>
			<tr class="title">
				<td colspan="10">
				学员列表	
				</td>
			</tr>
			<tr>
				<th class="tht">中文名</th>
				<th class="tht">与家长关系</th>
				<th class="tht">客户状态</th>
				<th class="tht">性别</th>
				<th class="tht">年龄</th>
				<th class="tht">英语基础</th>
				<th class="tht">家庭住址</th>
				<th class="tht">学校</th>
			</tr>
			<tr>
				<td class="tht">{$s_name1}</td>
				<td class="tht">{$relation}</td>
				<td class="tht">{$all_status[$final_status]}</td>
				<td class="tht">{$sex_option}</td>
				<td class="tht">{$age1}</td>
				<td class="tht">{$english}</td>
				<td class="tht">{$address1}</td>
				<td class="tht">{$school_list}</td>
			</tr>			
	</table>
ht;
?>	
	<br>
		<!--咨询历史-->
		<table align="center"   cellspacing="0" id='zx_history'>
			<tr class="title">
				<td colspan="7">
				咨询历史
				</td>
			</tr>
			<tr>
				<th class="tht" style="text-align:center;width:80px">状态</th>
				<th class="tht" style="text-align: center" width="140px">咨询时间</th>
				<th class="tht" style="text-align: center" width="100px">顾问</th>
				<th class="tht" style="text-align: center" width="380px">咨询纪要</th>
				<th class="tht" style="text-align: center" width="200px">邀约信息</th>
				<th class="tht" style="text-align: center" width="150px">当面咨询内容</th>
				<th class="tht" style="text-align: center" width="150px">录音时长</th>
				<th class="tht" style="text-align: center" width="150px">操作</th>
			</tr>
			<?php
			echo $zx_history;
			?>			
		</table>
		<br>
		<table align="center"  cellspacing="0">
			<tr>
				<td>状　　态：</td><td><select name="status" id='status'><?php echo $status_list;?></select><input class="Wdate" type="hidden" name="again_time" onClick="WdatePicker({dateFmt:'yyyy-M-d'})">
				</td>
			</tr>
			<tr>
				<td>
					<span class="label">标　　签：</span>
				</td>
				<td>
					<div class="plus-tag-add">
						<input id="" name="" type="text" class="stext" maxlength="20" />
						<span class="fff"></span>
						<a href="javascript:void(0);">展开标签</a>
					</div>
						<div id="mycard-plus" style="display:none;">
							<div class="default-tag tagbtn">
								<div class="clearfix">
									<a value="-1" title="互联网" href="javascript:void(0);"><span>互联网</span><em></em></a>
									<a value="-1" title="移动互联网" href="javascript:void(0);"><span>移动互联网</span><em></em></a>
									<a value="-1" title="it" href="javascript:void(0);"><span>it</span><em></em></a>
									<a value="-1" title="电子商务" href="javascript:void(0);"><span>电子商务</span><em></em></a>
									<a value="-1" title="广告" href="javascript:void(0);"><span>广告</span><em></em></a> 
									<a value="-1" title="互联网" href="javascript:void(0);"><span>互联网</span><em></em></a>
									<a value="-1" title="移动互联网" href="javascript:void(0);"><span>移动互联网</span><em></em></a>
									<a value="-1" title="it" href="javascript:void(0);"><span>it</span><em></em></a>
									<a value="-1" title="电子商务" href="javascript:void(0);"><span>电子商务</span><em></em></a>
									<a value="-1" title="广告" href="javascript:void(0);"><span>广告</span><em></em></a> 

								</div>
							</div>
						</div><!--mycard-plus end-->
				</td>
			</tr>
			<tr>
				<td>咨询纪要：</td><td><textarea rows="5" cols='50' name='zxjy'></textarea></td>
			</tr>
			<!-- <tr>
				<td colspan="2" ><input type='button' id='demoyy' value='邀约DEMO'></td>
			</tr> -->
			<tr>
				<td colspan="2" align="center"><input type="hidden" name="no" value="<?php echo $no;?>"><input type="submit" value="保存" name='bczx' onclick="sub_check()"></td>
			</tr>
		</table>
		</form>
		
	</fieldset>
	<!--新增咨询-->
	</body>
	<script type="text/javascript">
		//处理再联系
		$('#status').change(function(){
			data = $(this).val();
			if(data == 8){
				$(this).next(':hidden').attr('type','text');
			}else{
				$(this).next('input').attr('type','hidden');
			}
			if(data == 2){
				window.open ('demoap.php?sj=bz&tel=<?php echo $tel,"&bdid=$bdid";?>', 'newwindow', 'height=400, width=800, top=220, left=350, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')
			}
		});
		// $('#demoyy').click(function(){
		// 	window.open ('demoap.php?sj=bz&tel=<?php echo $tel,"&bdid=$bdid";?>', 'newwindow', 'height=400, width=800, top=220, left=350, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')
		// });
		/**
		2016-09-28 添加
		------------------------------------------------------
		*/
		function sub_check(){
			var zxjy = status = '';
			zxjy = $('#zxjy').val();
			status = $('#status').val()
		}

		/**
		------------------------------------------------------
		*/
		function changezxjy(tid){
			//tid = tel_bd id
			layer.open({
			    type: 1,
			    title:'修改咨询纪要',
			    area: ['500px', '300px'],
			    offset: '100px',
			    skin: 'layui-layer-demo', //样式类名
			    closeBtn: 1, //显示关闭按钮
			    shift: 2,
			    scrollbar: 'false',
			    shadeClose: true, //开启遮罩关闭
			    content: $('#chgzx'),
			});
		}
		$('#bczx').click(function(){
			var tid = zxjy = status = '';
			tid = $(this).prev().val();
			zxjy = $(this).parent().find('textarea').val();
			status = $(this).parent().find('select').val();
			$.post('zxjy_bc.php',{'tid':tid,'zxjy':zxjy,'status':status},function(data){
				if(data == 'success'){
					alert('修改成功！')
					location.reload();
				}else{
					alert('修改失败！')
				}
				layer.closeAll();
			});
		});
		var origin = '';
		$('.a_bc').focusin(function(){
			var z = $(this).val();
			origin = $.trim(z);
		});
		$('.a_bc').blur(function(){
			var name = z = no = tel = obj = '';
			name = $(this).attr('name')
			obj = $(this)
			z = $.trim($(this).val());
			no = $('#a_no').val();
			tel = $('#a_tel').val();
			if(no && tel && origin != z){
				$.post('tel_mx_bc.php',{'name':name,'z':z,'no':no,'tel':tel},function(data){
					var img = '<img src="./images/dg.png" width=22 height=22>';
					if(data == 'success'){
						// obj.css({background:'green'});
						obj.nextAll('img').remove()
						obj.after(img)
					}else{
						alert('保存失败！请联系管理员')
					}
				});
			}
		});

		function telrecord(date1,tel){
			window.open ('telrecord.php?date=' + date1 + '&tel=' + tel, 'newwindow', 'height=400, width=800, top=220, left=350, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')
		}
	</script>
</html>