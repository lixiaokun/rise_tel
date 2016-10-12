<?php
	require './common/qx.php';
	$sql = "select count(no) from data_base where fp_status=0";
	$result = mysql_query($sql);
	$total = mysql_fetch_row($result);
	$count = $total[0];
	if($_GET['exit']){
		unset($_SESSION['login']);
		echo "<script language='javascript' type='text/javascript'>";
		echo "top.location='login/index.php'";
		echo "</script>";
		die;
	}
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD id=Head1>
<TITLE>无标题页</TITLE>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<STYLE type=text/css> 
*{
	FONT-SIZE: 12px; COLOR: white
}
#logo {
	COLOR: white
}
#logo A {
	COLOR: white
}
FORM {
	MARGIN: 0px
}
</STYLE>
<SCRIPT src="Top.files/Clock.js" type=text/javascript></SCRIPT>
<META content="MSHTML 6.00.2900.5848" name=GENERATOR>
<style type="text/css">
	#update_form{ margin-left: 150px;margin-top: 100px;}
</style>
</HEAD>
<BODY 
style="BACKGROUND-IMAGE: url(./images/bg.gif); MARGIN: 0px; BACKGROUND-REPEAT: repeat-x">
<form id="form1">
  <DIV id=logo 
style="BACKGROUND-IMAGE: url(./images/logo.png); BACKGROUND-REPEAT: no-repeat">
    <DIV 
style="PADDING-RIGHT: 50px; BACKGROUND-POSITION: right 50%; DISPLAY: block; PADDING-LEFT: 0px; BACKGROUND-IMAGE: url(./images/bg_banner_menu.gif); PADDING-BOTTOM: 0px; PADDING-TOP: 3px; BACKGROUND-REPEAT: no-repeat; HEIGHT: 30px; TEXT-ALIGN: right"><A 
href=""><IMG src="Top.files/mail.gif" 
align=absMiddle border=0></A> 未分配电话<A id=HyperLink1 
href="telfp.php" target='mainFrame'><?php echo $count;?></A>条 <IMG 
src="Top.files/menu_seprator.gif" align=absMiddle> <A id=HyperLink2 
href="Index.php" target=_TOP>返回首页</A> <IMG 
src="Top.files/menu_seprator.gif" align=absMiddle> <A id=HyperLink3 
href="javascript:window.location.href='top.php?exit=1';%20window.close();">退出系统</A> </DIV>

    
    <DIV style="DISPLAY: block; HEIGHT: 54px"></DIV>
    <DIV 
style="BACKGROUND-IMAGE: url(./images/bg_nav.gif); BACKGROUND-REPEAT: repeat-x; HEIGHT: 30px">
      <TABLE cellSpacing=0 cellPadding=0 width="100%">
        <TBODY>
          <TR>
            <TD>
              <DIV><IMG src="Top.files/nav_pre.gif" align=absMiddle> 欢迎 <SPAN 
      id=lblBra>瑞思学科英语</SPAN> <SPAN id=lblDep></SPAN><?php echo $_SESSION['login'];?> 登录！ </DIV>
            </TD>
            <TD align=right width="70%"><SPAN style="PADDING-RIGHT: 50px"><A 
      href="javascript:history.go(-1);"><IMG src="Top.files/nav_back.gif" 
      align=absMiddle border=0>后退</A> <A href="javascript:history.go(1);"><IMG 
      src="Top.files/nav_forward.gif" align=absMiddle border=0>前进</A>
      <?php
      if($_SESSION['user_js'] == '1' || $_SESSION['user_js'] == '2'){
      ?>
      <A href="mytel.php" target='mainFrame'>
      <IMG src="Top.files/nav_changePassword.gif" align=absMiddle border=0>我的电话</A>
      <?php
      }
      ?>
      <A href="changepwd.php" target=mainFrame><IMG src="Top.files/nav_resetPassword.gif" align=absMiddle border=0>修改密码</A> 
<!--       <A href="" target=mainFrame><IMG src="Top.files/nav_help.gif" align=absMiddle border=0>帮助</A> 
 -->      <IMG src="Top.files/menu_seprator.gif" align=absMiddle> 
      <SPAN id=clock></SPAN></SPAN></TD>
          </TR>
        </TBODY>
      </TABLE>
    </DIV>
  </DIV>
  <SCRIPT type=text/javascript>
    var clock = new Clock();
    clock.display(document.getElementById("clock"));
</SCRIPT>
</form>
</BODY>
</HTML>
