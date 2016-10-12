<?php
	require './common/qx.php';
	require "./common/yey.php";
	error_reporting(0);
	if($_POST['ajax'] == 'check' ){
		$sql = "select no from data_base where tel='$_POST[tel]'";
		$res = mysql_query($sql);
		$row = mysql_fetch_row($res);
		if($row[0]){
			echo 'exist';
		}else{
			echo 'not';
		}
		die;
	}
	$datalist = '<datalist id="schools">';
	foreach ($yey as $key => $value) {
		$datalist .= '<option value="'. $value .'">' . $value . '</option>';
	}
	$datalist .= '</datalist>';
	$qudaolist = '<select name="qudao">';
	foreach ($qudao as $key => $value) {
		$qudaolist .= '<option value="'. $value .'">' . $value . '</option>';
	}
	$qudaolist .= '</select>';
	$dt_list = '<option value="">请选择</option>';
	foreach ($dt_name as $key => $value) {
		$dt_list .= '<option value="'. $value .'">' . $value . '</option>';
	}

	//新增渠道、后台分类 判断是否是咨询师或咨询主管 如果是 则她们录入的渠道、转介绍、后台自动分配给录入人
	$is_cc = in_array($_SESSION['user_js'],array(1,2)) ? 1 : 0;
	$fp_name = $_SESSION['uid'];
?>
<html>
	<head>
		<meta http-equiv="content-type" content="html;charset=utf-8">
		<script language="javascript" type="text/javascript" src="./My97DatePicker/WdatePicker.js"></script>
		<script type="text/javascript" src="./common/jquery.js"></script>
		<style>
			body{
				text-align: center;				
			}
			div{width:500px;margin-left: 25%}
			div input{
				margin :5px auto;
			}
			div select{
				width:150px;
			}

		</style>
	</head>
	<body>
		<div>
			<form action="./data_insert.php" method="post">
				<fieldset>
					<legend>试听卡信息录入</legend>
					　　渠道：<?php echo $qudaolist;?><br>
					学生姓名：<input type='text' name='s_name'><br>
					　　性别：<input type='radio' name='sex' value='1' checked="1">男<input type='radio' name='sex' value='0'>女<br>
					　　年龄：<input type='text' name='age' id='age'><br>
					家长姓名：<input type='text' name='p_name'><br>
					　　学校：<input type='text' name='school' list='schools' id='school'><?php echo $datalist;?><br>
					电话号码：<input type='text' name='tel' id='tel'><br>
					　地推人：<select name='dt_name' id='dt_name'><?php echo $dt_list;?></select><br>
					地推日期：<input class="Wdate" type="text" name="dt_date" onClick="WdatePicker({position:{left:60},dateFmt:'yyyy-M-d'})"> <br>
					　　重点：<input type="checkbox" name="important" value='1' >　　　　　　　　<br>
					<input type='button' name='data_sub' value="提交" id='data_sub'><br>
<!-- 					<div style="margin:0 auto;font-size:12px;color:red">
					注意：如果地推地点不是幼儿园或者小学，比如潞安剧院地推的，则渠道选择‘地推’，学校填写‘*’，地推人选择‘潞安剧院’。如果是在潞安剧院做活动的数据，则渠道选择‘活动’，学校填写‘*’，地推人选择‘潞安剧院’
					</div> -->
					<?php
						if($is_cc){
							echo '<div style="margin:0 auto;font-size:12px;color:red">';
							echo '注意：你所录入的转介绍、渠道及后台数据将直接分配给你自己，其他数据仍需CCM统一分配！';
							echo '</div>';
						}
					?>
				</fieldset>
			</form>
		</div>
		<script>

			$('#data_sub').click(function(){
				var dt_name = school = dt_date = age = tel = '';
				school = $.trim($('#school').val());
				dt_name = $('#dt_name').val();
				dt_date = $('.Wdate').val();
				age = $.trim($('#age').val());
				tel = $('#tel').val();

				if(dt_name == ''){
					alert('请选择地推人！');
					return;
				}
				if(school == ''){
					alert('请选择学校！')
					return;
				}

				if(dt_date == ''){
					alert('请选择日期！');
					return;
				}
				if(age == ''){
					alert('请输入年龄！')
					return;
				}
				if(tel == ''){
					alert('请输入电话号码！');
					return;
				}

				$('form').submit();
				return;
			});
			$('#tel').blur(function(){
				var tel = '';
				tel = $('#tel').val();
				tel = $.trim(tel);
				if(tel == ''){
					$('#tel').css('border-color','');
					return;
				}
				if(tel.length != 11){
					alert('请输入正确的电话号码')
					$('#data_sub').attr('disabled',1);
					return
				}
				$.post('data_insert.php',{'tel':tel,'ajax':'check'},function(tag){
					if(tag == 'exist'){
						$('#tel').css('border-color','red');
						//$('#data_sub').attr('disabled',1);
					}
					if(tag == 'not'){
						$('#tel').css('border-color','');
						//$('#data_sub').removeAttr('disabled');
					}
				});
			});

			$('#school').blur(function(){
				var school = '';
				school = $('#school').val();
				if(school == ''){
					return
				}
				//判断输入的学校是否是列表中的
				$.post('check_school.php',{'school':school},function(data){
					if(data != 'ok'){
						$('#school').css('border-color','red');
						$('#data_sub').attr('disabled',1);
					}else{
						$('#school').css('border-color','');
						$('#data_sub').removeAttr('disabled');
					}

				});
		});
		</script>
	</body>
</html>
<?php
	if(is_numeric($_POST['tel']) && strlen($_POST['tel']) == 11){
		
		
		$sql = "select no from data_base where tel='$_POST[tel]'";
		$res = mysql_query($sql);
		$row = mysql_fetch_row($res);
		if($row[0]){
			//处理重复单录入
			$sql = "update data_base set dt_date='$_POST[dt_date]',fp_status='0',dt_name='$_POST[dt_name]',qudao='$_POST[qudao]' where no='$row[0]'";
			$result = mysql_query($sql);
			if($result){
				echo "<script language='javascript' type='text/javascript'>";
				echo "alert('重复单重置为新单！');";
				echo "window.location.href='data_insert.php'";
				echo "</script>";
			}else{
				$err_name = './log/' . date('YmdHis',time()) . '.log';
				file_put_contents($err_name,$sql);
				echo "<script language='javascript' type='text/javascript'>";
				echo "alert('重置失败，请联系管理员');";
				echo "window.location.href='data_insert.php'";
				echo "</script>";
			}
		}else{
			//处理新单录入
			if($is_cc && in_array($_POST['qudao'], array('转介绍','渠道','后台'))){		
				$now = date('Y-n-j',time());		
				$sql = 'insert into data_base(`s_name`,`sex`,`age`,`p_name`,`school`,`tel`,`dt_name`,`fp_status`,`fp_name`,`fp_date`,`important`,`dt_date`,`qudao`)values("'. $_POST['s_name'].'",'. $_POST['sex'] .',"'.$_POST['age'].'","'. $_POST['p_name'] . '","' . $_POST['school'] . '",' . $_POST['tel'] . ',"' . $_POST['dt_name'] . '"' . ',"1"'.',"'. $fp_name .'"'.',"'. $now .'"' ;
			}else{
				$sql = 'insert into data_base(`s_name`,`sex`,`age`,`p_name`,`school`,`tel`,`dt_name`,`important`,`dt_date`,`qudao`)values("'. $_POST['s_name'].'",'. $_POST['sex'] .',"'.$_POST['age'].'","'. $_POST['p_name'] . '","' . $_POST['school'] . '",' . $_POST['tel'] . ',"' . $_POST['dt_name'] . '"' ;
			}
			if($_POST['important'] == 1){
				$sql .= ',1'; 
			}else{
				$sql .= ',0';
			}
			$sql .= ',"' . $_POST['dt_date'] . '","'. $_POST['qudao'] .'")';
			$result = mysql_query($sql);
			if($result){
				echo "<script language='javascript' type='text/javascript'>";
				echo "alert('录入成功');";
				echo "window.location.href='data_insert.php'";
				echo "</script>";
			}else{
				$err_name = './log/' . date('YmdHis',time()) . '.log';
				file_put_contents($err_name,$sql);
				echo "<script language='javascript' type='text/javascript'>";
				echo "alert('录入失败，请联系管理员');";
				echo "window.location.href='data_insert.php'";
				echo "</script>";
			}
		}
	}
?>