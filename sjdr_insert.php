<?php
	header('content-type:text/html;charset=utf-8');
	require_once './common/qx.php';
	require_once './common/yey.php';
	require_once './common/PHPExcel/Classes/PHPExcel/IOFactory.php';
	error_reporting(1);
	ini_set('memory_limit','1G');

	if(empty($_FILES)){
		die("不允许直接访问本页面！");
	}
	$quDao = $_POST['qudao'];
	$dtName = trim($_POST['dt_name']);
	if(!in_array($quDao,array('活动','渠道','后台'))){
		echo '<script>alert("请选择渠道！");history.back()</script>';
		die;
	}
	if(empty($dtName)){
		echo '<script>alert("请输入数据的具体来源！");history.back()</script>';
		die;
	}
	if ($_FILES["file"]["error"] > 0)
  	{
  		echo '<script>alert("请选择要导入的excel文件！");history.back()</script>';
  		die;
  	}
	else
  	{
	  	//上传目录
	  	$upload = "./upload/";	 
		$uploadFile = $upload . $_FILES["file"]["name"];
		$type = explode('.', $uploadFile);
		$type_tmp = array_pop($type);
		if(!in_array(strtolower($type_tmp), array('xls','xlsx'))){
			echo '你导入的文件不是Excel文件，请重新导入！';
			die;
		}
		if(move_uploaded_file($_FILES["file"]["tmp_name"],$uploadFile)){
	  		$fileType=PHPExcel_IOFactory::identify($uploadFile);//自动获取文件的类型提供给phpexcel用
			$objReader=PHPExcel_IOFactory::createReader($fileType);//获取文件读取操作对象
			$objPHPExcel=$objReader->load($uploadFile);
			$error_arr = array();//存放错误数据信息
			$data_arr = array();//存放合法的数据信息
			$sql_arr = array();//存放sql语句
			$sheet_index = 1;
			$dt_date = date('Y-n-j',time());
			$countS = 0;//计算总共执行成功的sql条数
			$countF = 0;//计算总共执行失败的sql条数
			foreach($objPHPExcel->getWorksheetIterator() as $sheet){//循环取sheet
				foreach($sheet->getRowIterator() as $row){//逐行处理
						if($row->getRowIndex()<2){
							continue;
						}

						$rowIndex = $row->getRowIndex();//获取当前是第几行

						$s_name = $sheet->getCell('A'. $rowIndex)->getValue();
						
						$sex    = $sheet->getCell('B'. $rowIndex)->getValue();
						if(!in_array($sex,array('男','女','1','0',''))){
							$error_arr[$sheet_index][] = '第' . $rowIndex . "行 {$s_name} <span style='color:red'>性别</span> 不符合规则。";
							continue;
						}
						$age    = $sheet->getCell('C'. $rowIndex)->getValue();
						if(!is_numeric($age) && !preg_match("/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9]))?$/", $age) && !empty($age)){
							$error_arr[$sheet_index][] = '第' . $rowIndex . "行 {$s_name} <span style='color:red'>年龄</span> 不符合规则。";
							continue;
						}else{
							if(!is_numeric($age)){
								$age_tmp = date('Y',strtotime($age));
								$now = date('Y',time());
								$age = $now-$age_tmp;
							}
						}
						$school = trim($sheet->getCell('D'. $rowIndex)->getValue());
						if(empty($school)){
							$school = '*';
						}
						if(!in_array($school, $yey)){
							$error_arr[$sheet_index][] = '第' . $rowIndex . "行 {$s_name} <span style='color:red'>学校</span>不符合规则。";
							continue;
						}
						$tel    = (string)$sheet->getCell('E'. $rowIndex)->getValue();
						if(!is_numeric($tel) && strlen($tel) != 11){
							$error_arr[$sheet_index][] = '第' . $rowIndex ."行 {$s_name} <span style='color:red'>电话号码</span> 不符合规则。";
							continue;
						}
						$p_name = $sheet->getCell('F'. $rowIndex)->getValue();
						
						$data_arr[$sheet_index][$rowIndex]['s_name'] 	= $s_name;
						$data_arr[$sheet_index][$rowIndex]['sex']    	= $sex;
						$data_arr[$sheet_index][$rowIndex]['age']		= $age;
						$data_arr[$sheet_index][$rowIndex]['school']	= $school;
						$data_arr[$sheet_index][$rowIndex]['tel']		= $tel;
						$data_arr[$sheet_index][$rowIndex]['p_name'] 	= $p_name;

						//$sql_arr[$sheet_index][$rowIndex] = "insert into data_base(`s_name`,`sex`,`age`,`school`,`tel`,`p_name`,`dt_date`,`qudao`) values('$s_name','$sex','$age','$school','$tel','$p_name','$dt_date','$quDao');";
						$sql_tmp = "insert into data_base(`s_name`,`sex`,`age`,`school`,`tel`,`p_name`,`dt_date`,`qudao`,`dt_name`) values('$s_name','$sex','$age','$school','$tel','$p_name','$dt_date','$quDao','$dtName');";
						if(!mysql_query($sql_tmp)){
							$errorMsg = mysql_error();
							if(preg_match('/Duplicate entry/', $errorMsg)){
								echo '第' . $rowIndex ."行 {$s_name} <span style='color:red'>电话号码已存在</span> 导入失败。",'<br/>';
							}
							else{
								echo '第' . $rowIndex ."行 {$s_name} <span style='color:red'>电话号码</span> 导入失败。",mysql_error(),'<br/>';
							}
							$countF++;
						}else{
							$countS++;
						}
						unset($sql_tmp);
				}
				$sheet_index++;
			}
		}
		echo '成功导入'. $countS . '条数据！失败' . $countF . '条数据！';
		echo '<script>alert("成功导入'. $countS . '，失败' . $countF .'条！")</script>';
		//echo $sql1;
		// echo '<pre>';
		// print_r($error_arr);
		// print_r($sql_arr);
  	}
?>