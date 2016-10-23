<?php
	/**
		2016年3月4日更新 
		修改除ccm外的权限。都可以看到待分配电话 已分配电话 和 已拨打电话 但是具体页面的操作权限不同
	*/
	require './common/qx.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>无标题页</TITLE>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<STYLE type=text/css> 
{
	FONT-SIZE: 12px
}
#menuTree A {
	COLOR: #566984; TEXT-DECORATION: none
}
</STYLE>
<SCRIPT src="Left.files/TreeNode.js" type=text/javascript></SCRIPT>
<SCRIPT src="Left.files/Tree.js" type=text/javascript></SCRIPT>
<META content="MSHTML 6.00.2900.5848" name=GENERATOR>
</HEAD>
<BODY 
style="BACKGROUND-POSITION-Y: -120px; BACKGROUND-IMAGE: url(./images/bg.gif); BACKGROUND-REPEAT: repeat-x">
<TABLE height="100%" cellSpacing=0 cellPadding=0 width="100%">
  <TBODY>
    <TR>
      <TD width=10 height=29><IMG src="Left.files/bg_left_tl.gif"></TD>
      <TD 
    style="FONT-SIZE: 18px; BACKGROUND-IMAGE: url(./images/bg_left_tc.gif); COLOR: white; FONT-FAMILY: system">Main 
        Menu</TD>
      <TD width=10><IMG src="Left.files/bg_left_tr.gif"></TD>
    </TR>
    <TR>
      <TD style="BACKGROUND-IMAGE: url(./images/bg_left_ls.gif)"></TD>
      <TD id=menuTree 
    style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px; HEIGHT: 100%; BACKGROUND-COLOR: white" 
    vAlign=top></TD>
      <TD style="BACKGROUND-IMAGE: url(./images/bg_left_rs.gif)"></TD>
    </TR>
    <TR>
      <TD width=10><IMG src="Left.files/bg_left_bl.gif"></TD>
      <TD style="BACKGROUND-IMAGE: url(./images/bg_left_bc.gif)"></TD>
      <TD width=10><IMG 
src="Left.files/bg_left_br.gif"></TD>
    </TR>
  </TBODY>
</TABLE>
<?php
	session_start();
	$user_js = $_SESSION['user_js'];
?>
<SCRIPT type=text/javascript>
	var tree = null;
	var root = new TreeNode('系统菜单');
	var fun1 = new TreeNode('电话管理');
	var fun2 = new TreeNode('电话录入', 'data_insert.php', 'tree_node.gif', null, 'tree_node.gif', null);
	fun1.add(fun2);

	var fun3 = new TreeNode('待分配电话', 'telfp.php', 'tree_node.gif', null, 'tree_node.gif', null);
	fun1.add(fun3);
	var fun4 = new TreeNode('已分配电话', 'alreadyfp.php', 'tree_node.gif', null, 'tree_node.gif', null);
	fun1.add(fun4);
	<?php
	if($user_js == '1' || $user_js == '2' || $user_js == '4'){
	?>
	var fun5 = new TreeNode('已拨打电话', 'alreadybd.php', 'tree_node.gif', null, 'tree_node.gif', null);
	fun1.add(fun5);
	var ld = new TreeNode('老单筛选', 'tel_check.php', 'tree_node.gif', null, 'tree_node.gif', null);
	fun1.add(ld);
	<?php
	}
	?>
	var fun6 = new TreeNode('Excel数据导入', 'sjdr.php', 'tree_node.gif', null, 'tree_node.gif', null);
	fun1.add(fun6);

	var fun7 = new TreeNode('地推信息查询', 'alreadylr.php', 'tree_node.gif', null, 'tree_node.gif', null);
	fun1.add(fun7);


	root.add(fun1);
	var fun6 = new TreeNode('DEMO管理');
	root.add(fun6);
	<?php
	if($user_js == '1'){
	?>
	var fun7 = new TreeNode('DEMO排课', 'demopk.php', 'tree_node.gif', null, 'tree_node.gif', null);
		fun6.add(fun7);
	<?php
	}
	?>
	var fun8 = new TreeNode('DEMO列表', 'demo_list.php', 'tree_node.gif', null, 'tree_node.gif', null);
		fun6.add(fun8);
	root.add(fun6);

	var fun14 = new TreeNode('到访管理','df_manage.php', 'tree_node.gif', null, 'tree_node.gif', null);
	root.add(fun14);
	<?php
	if($user_js == '1'){
	?>
	var fun9 = new TreeNode('用户管理', 'user_manage.php', 'tree_node.gif', null, 'tree_node.gif', null);
	root.add(fun9);
	<?php
		}
	?>
	
	var fun10 = new TreeNode('统计报表');
	var fun11 = new TreeNode('DEMO邀约统计', './tongji/demo_tj.php', 'tree_node.gif', null, 'tree_node.gif', null);
	var fun12 = new TreeNode('渠道转化统计', './tongji/qudao_tj.php', 'tree_node.gif', null, 'tree_node.gif', null);
	var fun13 = new TreeNode('咨询人员转化统计', './tongji/cc_tj.php', 'tree_node.gif', null, 'tree_node.gif', null);
	var fun15 = new TreeNode('地推信息统计', './tongji/dt_tj.php', 'tree_node.gif', null, 'tree_node.gif', null);
	fun10.add(fun11);
	fun10.add(fun12);
	fun10.add(fun13);
	fun10.add(fun15);
	root.add(fun10)
	tree = new Tree(root);tree.show('menuTree')
	//var fun6 = new TreeNode('我的日程', 'MySchedule.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun5.add(fun6);

	//root.add(fun5);var fun9 = new TreeNode('文档管理');var fun10 = new TreeNode('文档管理', 'DocumentMgr.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun9.add(fun10);var fun11 = new TreeNode('回收站', 'Recycler.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun9.add(fun11);var fun12 = new TreeNode('文件搜索', 'FileSearch.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun9.add(fun12);root.add(fun9);var fun13 = new TreeNode('消息传递');var fun14 = new TreeNode('消息管理', 'MessageMgr.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun13.add(fun14);var fun15 = new TreeNode('信箱', 'MailBox.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun13.add(fun15);root.add(fun13);var fun16 = new TreeNode('系统管理');var fun17 = new TreeNode('角色管理', 'RoleMgr.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun16.add(fun17);var fun18 = new TreeNode('登录日志', 'LoginLog.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun16.add(fun18);var fun19 = new TreeNode('操作日志', 'OperationLog.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun16.add(fun19);var fun20 = new TreeNode('菜单排序', 'MenuSort.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun16.add(fun20);root.add(fun16);var fun21 = new TreeNode('考勤管理');var fun22 = new TreeNode('签到签退', 'SignInOrOut.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun21.add(fun22);var fun23 = new TreeNode('考勤查询', 'HistoryQuery.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun21.add(fun23);var fun24 = new TreeNode('考勤统计', 'TimeStatistics.aspx', 'tree_node.gif', null, 'tree_node.gif', null);fun21.add(fun24);root.add(fun21);tree = new Tree(root);tree.show('menuTree')
</SCRIPT>
</BODY>
</HTML>
