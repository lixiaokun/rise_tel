<?php
	require './common/qx.php';
	require './common/yey.php';
	header('content-type:text/html;charset=utf-8');
	if($_POST['ajax'] == 'update' && is_numeric($_POST['id'])){
		$sql = "select * from rise_user where id=$_POST[id]";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
		$user_js_list = '';
		foreach ($user_js as $key => $value) {
			if($key == $row[user_js]){
				$user_js_list .= "<option value=$key selected=1>$value</option>";
			}else{
				$user_js_list .=  "<option value=$key>$value</option>";
			}
		}
		$html = 
<<<html
		  <div id="update_form" style="">
	  <form method="post" action="./user_manage.php">
			<div style='margin-top:50px'>用户名：<input type="text" name='uname' id='up_uname' value='$row[uname]'></div>
			<div>密　码：<input type="password" name="pwd" id='up_pwd' value='$row[pwd]'></div>
			<div>角　色：<select name="user_js" id='up_js'>
				<option value=''>请选择</option>
				$user_js_list
					
		
			</select>
				<input type='hidden' name='uid' value='$row[id]'>	<input type='hidden' name='action' value='update'>			
			</div>
			<div><input type='submit' value='更新'></div>
	  </form>
  </div>
html;
	echo $html;
	die;
	}
	$users = "select * from rise_user";
	$res = mysql_query($users);
	$tds = '';
	while($row = mysql_fetch_assoc($res)){
		if($row['id'] != '5'){
			$tds .= '<TR>';
	        $tds .= '<TD class=gridViewItem style="WIDTH: 50px"><IMG src="EmployeeMgr.files/bg_users.gif"> </TD>';
	        $tds .= "<TD class=gridViewItem id='uname'>{$row['uname']}</TD>";
	        $tds .= "<TD class=gridViewItem>{$row['uname']}</TD>";
	        $tds .= "<TD class=gridViewItem id='pwd'>{$row['pwd']}</TD>";
	        $tds .= "<TD class=gridViewItem id='user_js' js='$row[user_js]'>".$user_js[$row['user_js']]."</TD>";
	        $tds .= '<TD class=gridViewItem><A class=cmdField href="javascript:mod_user('.$row['id'].')">编辑</A></TD>';
	        //$tds .= '<TD class=gridViewItem><A class=cmdField id=ctl00_ContentPlaceHolder2_GridView1_ctl02_LinkButton1          onclick="return confirm('. "'确定要删除吗？'".');" href="./user_manage.php?action=del&uid='.$row['id'].'">删除</A> </TD>';
	        $tds .= '</TR>';
	    }
	}
	//添加用户
	if(isset($_POST['sub']) && !empty($_POST['uname']) && !empty($_POST['pwd']) && !empty($_POST['user_js'])){
		$sql = "insert into rise_user(`uname`,`pwd`,`user_js`)values('$_POST[uname]','$_POST[pwd]','$_POST[user_js]');";
		$result = mysql_query($sql);
		if($result){
			echo '<script>location.href="user_manage.php"</script>';
		}
		die;
	}
	//更新用户
	if($_POST['action'] == 'update'){
		if(empty($_POST['uname'])){
			echo '<script>alert("用户名不能为空");history.back()</script>';
			die;
		}
		if(empty($_POST['user_js'])){
			echo '<script>alert("用户角色不能为空");history.back()</script>';
			die;
		}
		$sql = "update rise_user set uname='$_POST[uname]',pwd='$_POST[pwd]',user_js='$_POST[user_js]' where id=$_POST[uid]";
		$res = mysql_query($sql);
		if(mysql_affected_rows()){
			echo '<script>alert("更新成功！");location.href="./user_manage.php"</script>';
		}
		echo '<script>location.href="user_manage.php"</script>';
		die;
	}
	//删除用户
	if($_SESSION['user_js'] == '1' && $_GET['action'] == 'del' && !empty($_GET['uid'])){
		if($_GET['uid'] != '5'){//禁止删除admin管理员
			$del_sql = "DELETE FROM `rise_user` WHERE id={$_GET['uid']}";
			if(mysql_query($del_sql)){
				echo '<script>location.href="user_manage.php"</script>';
			}
			die;
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>用户管理</TITLE>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<script type="text/javascript" src='./common/jquery.js'></script>
<script type="text/javascript" src="./common/layer/layer.js"></script>
<STYLE type=text/css> 
{
	FONT-SIZE: 12px
}
.gridView {
	BORDER-RIGHT: #bad6ec 1px; BORDER-TOP: #bad6ec 1px; BORDER-LEFT: #bad6ec 1px; COLOR: #566984; BORDER-BOTTOM: #bad6ec 1px; FONT-FAMILY: Courier New
}
.gridViewHeader {
	BORDER-RIGHT: #bad6ec 1px solid; BORDER-TOP: #bad6ec 1px solid; BACKGROUND-IMAGE: url(./images/bg_th.gif); BORDER-LEFT: #bad6ec 1px solid; LINE-HEIGHT: 27px; BORDER-BOTTOM: #bad6ec 1px solid
}
.gridViewItem {
	BORDER-RIGHT: #bad6ec 1px solid; BORDER-TOP: #bad6ec 1px solid; BORDER-LEFT: #bad6ec 1px solid; LINE-HEIGHT: 32px; BORDER-BOTTOM: #bad6ec 1px solid; TEXT-ALIGN: center
}
.cmdField {
	BORDER-RIGHT: 0px; BORDER-TOP: 0px; BACKGROUND-IMAGE: url(./images/bg_rectbtn.png); OVERFLOW: hidden; BORDER-LEFT: 0px; WIDTH: 67px; COLOR: #364c6d; LINE-HEIGHT: 27px; BORDER-BOTTOM: 0px; BACKGROUND-REPEAT: repeat-x; HEIGHT: 27px; BACKGROUND-COLOR: transparent; TEXT-DECORATION: none
}
body{text-align: center}
table{margin:0 auto;}
#tj_form div,#update_form div {margin-top: 10px;text-align: left;margin-left: 150px}
</STYLE>
<META content="MSHTML 6.00.2900.5848" name=GENERATOR>
</HEAD>
<BODY>

  
            <TABLE class=gridView  style="WIDTH: 80%; BORDER-COLLAPSE: collapse" cellSpacing=0 rules=all 
      border=1>
              <TBODY>
              	<tr>
              		<th colspan="7" class=gridViewItem style="text-align:left;height:40px;font-size:18px;padding-left:20px;vertical-align:bottom;border-style:none none solid none;background-color:rgb(89, 147, 202);color:white">用户管理</th>
              	</tr>
              	<tr>
              		<td colspan="7" class=gridViewItem style="text-align:right;padding-right:40px">
              			<input type="button" class=gridViewHeader  value='添加用户' id='tj'>
              		</td>
              	</tr>
                <TR>
                  <TH class=gridViewHeader style="WIDTH: 50px" scope=col>&nbsp;</TH>
                  <TH class=gridViewHeader scope=col>用户Id</TH>
                  <TH class=gridViewHeader scope=col>姓名</TH>
                  <TH class=gridViewHeader scope=col>密码</TH>
                  <TH class=gridViewHeader scope=col>角色</TH>
                  <TH class=gridviewHeader scope=col>更新</TH>
<!--                   <TH class=gridviewHeader scope=col>删除</TH> -->
                </TR>
                <?php
                	echo $tds;
                ?>
              </TBODY>
            </TABLE>
   
  <div id="tj_form" style="display:none;">
	  <form method="post" action="./user_manage.php">
			<div style='margin-top:50px'>用户名：<input type="text" name='uname'></div>
			<div>密　码：<input type="password" name="pwd"></div>
			<div>角　色：<select name="user_js">
				<option value=''>请选择</option>
				<?php
					foreach ($user_js as $key => $value) {
						echo "<option value=$key>$value</option>";
					}
				?>
				</select>
			</div>
			<div><input type="submit" value="提交" name="sub"></div>
	  </form>
  </div>

<SCRIPT type=text/javascript>
	$('#tj').click(function(){
	    layer.open({
	    type: 1,
	    title:'添加用户',
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
	function mod_user(id){
		// var uname = pwd = js = '';
		// uname = $(obj).parents().find('#uname').html()
		// pwd = $(obj).siblings('#pwd').text();
		// js = $(obj).siblings('#user_js').attr('js');
		// $('#up_id').val(id);
		// $('#up_uname').val(uname);
		// $('#up_pwd').val(pwd);
		$.post('./user_manage.php',{'ajax':'update','id':id},function(str){
			layer.open({
			    type: 1,
			    title:'修改用户信息',
			    area: ['500px', '300px'],
			    offset: '100px',
			    skin: 'layui-layer-demo', //样式类名
			    closeBtn: 1, //显示关闭按钮
			    shift: 2,
			    scrollbar: 'false',
			    shadeClose: true, //开启遮罩关闭
			    content: str,
			});
		});
	}

</SCRIPT>
</BODY>
</HTML>
