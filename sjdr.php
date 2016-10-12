<?php
	/**
		excel导入数据库
	*/
	require_once './common/qx.php';
	require_once './common/yey.php';
	//渠道列表
	$qudao_list = '<option value="">请选择</option>';
	foreach ($qudao as  $value) {
		if(in_array($value,array('活动','渠道','后台'))){
			if($_arr['qudao'] == $value)
				$qudao_list .= '<option value="'. $value .'" selected=1>'. $value .'</option>';
			else
				$qudao_list .= '<option value="'. $value .'">'. $value .'</option>';
		}
	}

	//地推人列表
	$dt_name_list= '<option value="">请选择</option>';
	foreach ($dt_name as $key => $value) {
		if($_arr['dt_name'] == $value)
			$dt_name_list .= '<option value="'. $value .'" selected=1>'. $value .'</option>';
		else
			$dt_name_list .= '<option value="'. $value .'">'. $value .'</option>';
	}


?>
<html>
	<head>
		<title>Excel数据导入</title>
		<meta http-equiv="content-type" content ="text/html;charset=utf-8">
		<style type="text/css">
			body{margin:0;padding: 0;text-align: center}
			div{margin:0 auto;width:60%;}
			.d1{width:100%;height:25px;text-align: left;padding-left: 30%;margin-top:10px;}
			p{text-align: left}
			.red{color:red;}
			.span1{margin:5px auto;width: 100%;display: block}
			select{width:100px;text-align: center}
		</style>
	</head>
	<body>
		<div>
			<fieldset>
				<legend>Excel数据导入</legend>
				<form action="./sjdr_insert.php" method="post" enctype="multipart/form-data">
					<div class='d1'>
						<label for="file">文件名：</label>
						<input type="file" name="file" id="file" />
					</div>
					<div class='d1'>
					渠　道：<select name='qudao'>
						<?php
							echo $qudao_list;
						?>
					</select>
					</div>
					<div class='d1'>
					具体来源(地推人)：<input type="text" name="dt_name">
					</div>
					<div class='d1'>
					<input type="submit" name="submit" value="导入数据" />
					</div>
				</form>
			</fieldset>
		</div>
		<div>
			<fieldset>
				<legend>Excel数据格式要求</legend>
				<p>
					<span class='span1'>1.&nbsp;&nbsp;A列为学生姓名</span>
					<span class='span1'>2.&nbsp;&nbsp;B列为性别,支持的数据格式为：<span class='red'>男、女、1、0</span></span>
					<span class='span1'>3.&nbsp;&nbsp;C列为年龄，支持的数据格式为：<span class='red'>数字、包含小数的数字、生日日期（2016-9-1、2016.9.1、2016-09-01、2016.09.01）</span></span>
					<span class='span1'>4.&nbsp;&nbsp;D列为学校，<span class='red'>系统中已有的学校或空白可导入</span></span>
					<span class='span1'>5.&nbsp;&nbsp;E列为电话号码，支持的数据格式为：<span class='red'>11位手机号码</span></span>
					<span class='span1'>6.&nbsp;&nbsp;F列为家长姓名</span>
					<span class='span1'>7.&nbsp;&nbsp;文件名中不能包含汉字,程序从每个Sheet的第二行开始读取数据</span>
				</p>
			</fieldset>
		</div>
	</body>
</html>
