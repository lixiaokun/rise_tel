<?php
	require './common/qx.php';
	require './common/PHPExcel/Classes/PHPExcel.php';
	ini_set('memory_limit','1G');
	/**

	*/
	$all_status = array(
	'0' => '无效',
	'1' => '未承诺上门',
	'2' => '承诺上门', //承诺上门即是承诺未上门   承诺已上门 即是 上门未交费	
	'7' => '未接听',
	'8' => '再联系',
	'3' => '上门未缴费',
	'4' => '定金',
	'5' => '全费',
	'6' => '退费',
	'9' => '承诺未上门',
	'99' => '未拨打'
	);
	$sql = "select uname,id from rise_user where user_js='1' or user_js='2'";
	$res = mysql_query($sql);
	$cc = array();
	while ($row = mysql_fetch_assoc($res)) {
		$k = $row['id'];
		if($k != '5')
		$cc[$k] = $row['uname'];
	}
	/**

	*/
	$_arr=isset($_POST["sub"]) ? $_POST : $_GET;
	$whe=array();       
    $param="";

    if(!empty($_arr["dt_name"])) {
    	if($_arr['dt_name'] == 'all'){

    	}else{
	        $whe[] ="dt_name='{$_arr[dt_name]}'";
	        $param.="&dt_name={$_arr[dt_name]}";
	    }
         
    }
     if(!empty($_arr["school"])) {
        $whe[] ="school='{$_arr[school]}'";

        $param.="&school={$_arr[school]}";
         
    }
    if(!empty($_arr["dt_date_s"])) {
        $whe[] ="UNIX_TIMESTAMP(dt_date) >= UNIX_TIMESTAMP('{$_arr[dt_date_s]}')";   
        $param.="&dt_date_s={$_arr[dt_date_s]}";
    }

   	if(!empty($_arr["dt_date_e"])) {
        $whe[] ="UNIX_TIMESTAMP(dt_date) <= UNIX_TIMESTAMP('{$_arr[dt_date_e]}')";   
        $param.="&dt_date_e={$_arr[dt_date_e]}";
    }
    if(strlen($_arr["tel_status"]) && $_arr['tel_status'] != 'all') {
        $whe[] ="tel_status = '{$_arr[tel_status]}'";   
        $param.="&tel_status={$_arr[tel_status]}";
    }

    if(empty($whe)){
        $where=" ";
    }else{
        $where="where ".implode(" and ", $whe);
    }

    $objPHPExcel = new PHPExcel();
    $objSheet = $objPHPExcel->getActiveSheet();
    $objSheet->setTitle('地推数据');
    //设置excel文件默认水平垂直方向居中
	$objSheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	//设置默认字体大小和格式
	$objSheet->getDefaultStyle()->getFont()->setSize(12)->setName("宋体");

	 //写入首行标题
    $head_arr = array(
    		'序号','状态','学生姓名','性别','年龄','学校','电话号码','地推日期','地推人','渠道','咨询师',
    	);
    for($i = 0; $i < count($head_arr); $i++){
    	$index = getCellIndex($i) . '1';
   		$objSheet->setCellValue($index,$head_arr[$i]);
		$objSheet->getStyle($index)->getFont()->setBold(true);
    }
    //设置首行行高
	$objSheet->getRowDimension('1')->setRowHeight(37);
	//设置列宽
	$objSheet->getColumnDimension('F')->setWidth(20);
	$objSheet->getColumnDimension('G')->setWidth(20);
	$objSheet->getColumnDimension('H')->setWidth(20);
	$sql = 'SELECT `no`, `tel`, `s_name`, `sex`, `age`, `important`, `dt_name`, `dt_date`, `fp_status`, `fp_name`, `qudao`, `p_name`, `school`, `fp_date`,`tel_status` FROM `data_base` '.$where. " order by UNIX_TIMESTAMP(dt_date) DESC {$page[limit]} ";
	// echo $sql;
	$result = mysql_query($sql);
	$tr = '';
	$xh = 2;
	while($row = mysql_fetch_assoc($result)){
		$objSheet->setCellValue('A'.$xh,$xh-1);
		$objSheet->setCellValue('B'.$xh,$all_status[$row['tel_status']]);
		$objSheet->setCellValue('C'.$xh,$row['s_name']);
		$sex_tmp = ($row['sex'] == 1)? '男':'女';
		$objSheet->setCellValue('D'.$xh,$sex_tmp);
		$objSheet->setCellValue('E'.$xh,$row['age']);
		$objSheet->setCellValue('F'.$xh,$row['school']);
		$objSheet->setCellValue('G'.$xh,$row['tel']);
		$objSheet->setCellValue('H'.$xh,$row['dt_date']);
		$objSheet->setCellValue('I'.$xh,$row['dt_name']);
		$objSheet->setCellValue('J'.$xh,$row['qudao']);
		$objSheet->setCellValue('K'.$xh,$cc[$row['fp_name']]);
		$xh++;
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'excel5');
	$filename = date('YmdHis') . '.xls';
	browser_export('Excel5',$filename);
	$objWriter->save('php://output');
	

	function browser_export($type,$filename){
		if($type=="Excel5"){
				header('Content-Type: application/vnd.ms-excel');//告诉浏览器将要输出excel03文件
		}else{
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器数据excel07文件
		}
		header('Content-Disposition: attachment;filename="'.$filename.'"');//告诉浏览器将输出文件的名称
		header('Cache-Control: max-age=0');//禁止缓存
	}
	function getCellIndex($index){
		$arr = range('A','Z');
		$array = array();
		for($i = 0;$i <= 25;$i++){
			for($j = 0; $j <= 25;$j++){
				$array[] = $arr[$i].$arr[$j];
			}		
		}
		$final = array_merge($arr,$array);
		return $final[$index];
	}
?>
