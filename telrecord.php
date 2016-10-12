<?php
	header('content-type:text/html;charset=utf-8');
	session_start();
	error_reporting(0);
	$date = $_GET['date'];
	$tel = $_GET['tel'];
	$uname = $_SESSION['login'];
	// if(empty($date) || empty($tel)){
	// 	echo '参数错误！联系管理员';
	// 	die;
	// }
	
	$uname_record = array(
		'coco' => '001',
		'ann' => '002',
		'cici' => '003',
		'joy' => '004',
		'Kristen' => '005',
        '006'
	);

    // 20160413105745065--018803450066-Out-004.wav

    $date1 = date('Ymd',strtotime($date));
	//$file_path = "D:\FileRecord\\" . $date1 . '\\' . $uname_record[$uname];
	$path_arr = array();

	//$audio_path = "http://192.168.1.101:8080/" . $date1 . '/' . $uname_record[$uname];
	$flag = 0;
    function traverse($path = '.') {
    	global $tel,$audio_path,$flag; 
        $current_dir = opendir($path);    //opendir()返回一个目录句柄,失败返回false
        while(($file = readdir($current_dir)) !== false) {    //readdir()返回打开目录句柄中的一个条目
            $sub_dir = $path . DIRECTORY_SEPARATOR . $file;    //构建子目录路径
            if($file == '.' || $file == '..') {
                continue;
            } else if(is_dir($sub_dir)) {    //如果是目录,进行递归
                traverse($sub_dir);
            } else {    //如果是文件,直接输出
            	if(strstr($file, $tel)){
                	echo '<audio src="'. $audio_path . '/' . $file .'" controls="controls">您的浏览器不支持！</audio><br>','<a href="'. $audio_path . '/' . $file .'">'.$file.'</a>';
                	$flag = 1;
            	}
            }
        }
    }

    foreach ($uname_record as  $value) {
    	$file_path = "D:\FileRecord\\" . $date1 . '\\' . $value;
    	$audio_path = "http://192.168.1.100:8080/" . $date1 . '/' . $value;
    	traverse($file_path);
    }    
    if(!$flag)
    	echo '未找到录音文件！';





       
?>

