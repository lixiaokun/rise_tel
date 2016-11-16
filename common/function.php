<?php
	function Read_Excel_File2($file_name,&$result){
	    require_once 'PHPExcel/Classes/PHPExcel/IOFactory.php';
	    $result=null;
	    $objReader = PHPExcel_IOFactory::createReader('Excel5');
	//    $objReader->setReadDataOnly(true);
	    try{
	        $objPHPExcel = $objReader->load($file_name);
	    }catch(Exception $e){}
	    if(!isset($objPHPExcel)) return "无法解析文件";
	    $allobjWorksheets = $objPHPExcel->getAllSheets();
	    foreach($allobjWorksheets as $objWorksheet){
	        $sheetname=$objWorksheet->getTitle();
	        $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
			$highestColumn = $objWorksheet->getHighestColumn();
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	        for ($row = 1; $row <= $highestRow; ++$row) {
	            for ($col = 0; $col <= $highestColumnIndex; ++$col) {
	                $cell =$objWorksheet->getCellByColumnAndRow($col, $row);
	                $value=$cell->getValue();
	                if($cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC){
	                    $cellstyleformat=$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat();
	                    $formatcode=$cellstyleformat->getFormatCode();
	                    if (preg_match('/^(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy]/i', $formatcode)) {
	                       $value=gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($value));
	                    }else{
	                        $value=PHPExcel_Style_NumberFormat::toFormattedString($value,$formatcode);
	                    }
	//                    echo $value,$formatcode,'<br>';
	                    
	                }
	                $result[$sheetname][$row-1][$col]=$value;
	            }
	        }
	    }
    return 0;
}
?>