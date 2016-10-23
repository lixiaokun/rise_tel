<?php
	include("./common/PHPMailer/PHPMailer.class.php");
	include("./common/PHPMailer/class.smtp.php");
	//获取一个外部文件的内容
	$mail       = new PHPMailer();
	// $body       = file_get_contents('contents.html');
	// $body       = eregi_replace("[\]",'',$body);
	$url = "http://localhost/rise_tel/tongji/cc_tj.php";
	
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	//在需要用户检测的网页里需要增加下面两行
	//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	//curl_setopt($ch, CURLOPT_USERPWD, US_NAME.":".US_PWD);
	$body = curl_exec($ch);
	curl_close($ch);

	//如果出现中文乱码使用下面代码
	$body = iconv("gb2312", "utf-8",$body);

	

	//设置smtp参数
	$mail->IsSMTP();
	$mail->SMTPAuth  = true;
	$mail->SMTPKeepAlive = true;
	$mail->Host      = "smtp.163.com";
	$mail->Port      = 25;
	//填写你的gmail账号和密码
	$mail->Username  = "lixiaokun00100@163.com";
	$mail->Password  = "Wbslxk460888";
	//设置发送方，最好不要伪造地址
	$mail->From    	 = "lixiaokun00100@163.com";
	$mail->FromName  = "Webmaster";
	$mail->Subject   = "This is the subject";
	$mail->AltBody   = $body;
	$mail->WordWrap  = 50; // set word wrap
	$mail->MsgHTML($body);
	//设置回复地址
	$mail->AddReplyTo("yourname@gmail.com","Webmaster");
	//添加附件，此处附件与脚本位于相同目录下
	//否则填写完整路径
	// $mail->AddAttachment("attachment.jpg");
	// $mail->AddAttachment("attachment.zip");
	//设置邮件接收方的邮箱和姓名
	$mail->AddAddress("michaelli@rise-cz.com","FirstName LastName");
	//使用HTML格式发送邮件
 	$mail->IsHTML(true);
	//通过Send方法发送邮件
	//根据发送结果做相应处理
	if(!$mail->Send()) {
	 echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
	 echo "Message has been sent";
	}