<?php
	/**
		demo排课页面
		ccm在此页面安排demo课
		@dateHms DEMO时间
		@phasezjs DEMO班型
		@memos DEMO备注
	*/
	require './common/qx.php';
	header('content-type:text/html;charset=utf-8');
	if(!empty($_POST['dateHms'])){
		$dateHms = $_POST['dateHms'];
		$phasezjs = $_POST['phasezjs'];
		$memos = $_POST['memos'];
		$total_date = count($dateHms);

		$day = array();

		//拿到分界
		$fj = $fj1 = $fj2 = array();
		for($i = 0;$i < $total_date;$i++){
			if(preg_match("/^(\d.*?)-(\d.*?)$/",trim($dateHms[$i]))){
				$fj[] = $dateHms[$i];
				$fj1[] = $i;
			}
		}
		foreach ($fj1 as $key => $value) {
			if(empty($fj1[$key-1]))
				$fj1[$key-1] = 0;
			$fj2[] = $fj1[$key-1].'-'.$value;
		}
		$fj3 = array_combine($fj, $fj2);
		$year = date('Y',time());
		foreach ($fj3 as $k => $v) { 
			$se = explode('-', $v);
			$k = $year . '-' .$k;
			if($se[0] == 0){
				foreach ($dateHms as $key => $value) {				
					if($key >= $se[0] && $key < $se[1]){
						//$day[$k]['dateHms'][] = $value;
						$day[$k]['sql'][] = "'" . $value . "'," . (!in_array($phasezjs[$key], array(1,2,3,4))? 0:$phasezjs[$key]) .  ",'" . $memos[$key] . "'";
					}
				}
			}else{
				foreach ($dateHms as $key => $value) {				
					if($key > $se[0] && $key < $se[1]){
						//$day[$k]['dateHms'][] = $value;
						$day[$k]['sql'][] = "'" . $value . "'," . (!in_array($phasezjs[$key], array(1,2,3,4))? 0:$phasezjs[$key]) .  ",'" . $memos[$key] . "'";
					}
				}
			}
		}
		/*将拿到的数据写入数据库
	id 	demotime demotype 	bz 	demodate 
	*/
		 $sql = array();
		 $final_sql = array();
		foreach ($day as $key => $value){
			foreach ($value as $k => $v) {
				foreach ($v as $i) {
					if(preg_match("/:/", $i)){
						$sql[$key][] = "insert into demo_base(`demodate`,`demotime`,`demotype`,`bz`) values('$key',$i);";
						$final_sql[] = "insert into demo_base(`demodate`,`demotime`,`demotype`,`bz`) values('$key',$i);";
					}
				}				 
			}		
		}
		for($i=0;$i<count($final_sql);$i++){
			mysql_query($final_sql[$i])or die(mysql_error());
		}
		//$result = mysql_query($final_sql)or die(mysql_error());
		//var_dump($result);
		echo "<script>location.href='demo_list.php'</script>";
		die;
	}


?> 
 
 
 
 
 



<html>
	<head>
		
		<meta http-equiv="Cache-Control" content="no-cache" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />

	    <link rel="stylesheet" href="./css/validationEngine.jquery.css" type="text/css"/>
	    <link rel="stylesheet" href="./css/template.css" type="text/css"/> 
	    <!-- 蓝色版本样式 -->
		<link href="./css/sta_tab.css" rel="stylesheet" type="text/css" />
		<!--紫色版本样式
		<link href="http://localhost/rise_tel/css/sta_tab_main2.css" rel="stylesheet" type="text/css" /> 
 		-->    
 		<link href="./css/index.css" rel="stylesheet" type="text/css" />
		<link href="./css/shouye.css" rel="stylesheet" type="text/css" />
		<link href="./css/biaoge.css" rel="stylesheet" type="text/css" /> 
		<script language="javascript" type="text/javascript" src="./My97DatePicker/WdatePicker.js"></script>
		<script type="text/javascript" src="./common/jquery.js"></script>
		<style>
			body{text-align:center;}
			table{margin:0 auto;}
			#demo_list{display:none;margin:0 auto;}
			#demo_list td{text-align: center}
			#demo_list a{cursor: pointer;}
		</style>
		<title>DEMO课管理</title>
	</head>
 
	<body>	 
  	<table class="listtab" cellspacing="1" align="center" id="listtab" style="width:80%">
		<tr class="title">
			<th colspan="8">
				<div style="float: left;">
					Demo课管理 
					<span>
						<!-- <img src="http://localhost/rise_tel/images/n_green.gif"/>&nbsp;空闲&nbsp;&nbsp;
						<img src="http://localhost/rise_tel/images/n_red.gif"/>&nbsp;约满 -->
					</span>					
				</div>
				 <div style="float: right; padding-right: 17px">
					<input type="button" class="btn1" value="管理上周" onclick="lastWeek()" />
					<input type="button" class="btn1" value="管理本周" onclick="currWeek()" />
					<input type="button" class="btn1" value="管理下周" onclick="nextWeek()" />
				</div>	
			</th>
		</tr>

    </table>
    <div id='demo_list'>
        <form action="demopk.php" method='post' id='bgtj'>
    	<table align="center" class="detailtab" cellspacing="1" id="querytable" style="width:80%">
         <tr>
          <th class="tht" width="10%">日期</th>
          <th class="tht" width="8%">时段</th>
          <th class="tht" width="10%">时间</th>
          <th class="tht" width="15%">班型</th>
          <th class="tht" width="20%">备注 </th>
          <th class="tht" width="15%">操作</th>
        </tr>
		<span id="id1">
	      	<tr id="id1sw" sj="sw"  dflag="1"  class="bg_tr02">          
	      		<td rowspan="3"></td>
	          	<td>上午 </td>
	          	<td><input name="dateHms[]" type="text" size="10" class="blueinput" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'09:00:00'})" readonly="readonly"/></td>
	          	<td>		         
		         <select id="phasezjs" name="phasezjs[]">
		         	<option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
		         </select> 
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a  onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	        <tr  id="id1xw" sj="xw" dflag="1" class="bg_tr02"> 	 
	          	<td style="display:none"></td>
	          	<td>下午</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'14:30:00'})" readonly="readonly"/>
	           	</td>
	          	<td>
	          	<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          	</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a></td>
	        </tr>
	        <tr id="id1ws" sj="ws" dflag="1" class="bg_tr02">	         
	          	<td style="display:none"></td>
	          	<td>晚上</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'18:00:00'})" readonly="readonly"/></td>
	          	<td>
	          		<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          		</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	        	<input type='hidden' class='hiddenInput' value='' name='dateHms[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='phasezjs[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='memos[]'>
		</span>
		
		<span id="id2">
	      	<tr id="id2sw" sj="sw"  dflag="1"  class="bg_tr02">          
	      		<td rowspan="3"></td>
	          	<td>上午 </td>
	          	<td><input name="dateHms[]" type="text" size="10" class="blueinput" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'09:00:00'})" readonly="readonly"/></td>
	          	<td>		         
		         <select id="phasezjs" name="phasezjs[]">
		         	<option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
		         </select> 
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a  onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	        <tr  id="id2xw" sj="xw" dflag="1" class="bg_tr02"> 	 
	          	<td style="display:none"></td>
	          	<td>下午</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'14:30:00'})" readonly="readonly"/>
	           	</td>
	          	<td>
	          	<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          	</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a></td>
	        </tr>
	        <tr id="id2ws" sj="ws" dflag="1" class="bg_tr02">	         
	          	<td style="display:none"></td>
	          	<td>晚上</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'18:00:00'})" readonly="readonly"/></td>
	          	<td>
	          		<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          		</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	       		<input type='hidden' class='hiddenInput' value='' name='dateHms[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='phasezjs[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='memos[]'>
		</span>

		<span id="id3">
	      	<tr id="id3sw" sj="sw"  dflag="1"  class="bg_tr02">          
	      		<td rowspan="3"></td>
	          	<td>上午 </td>
	          	<td><input name="dateHms[]" type="text" size="10" class="blueinput" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'09:00:00'})" readonly="readonly"/></td>
	          	<td>		         
		         <select id="phasezjs" name="phasezjs[]">
		         	<option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
		         </select> 
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a  onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	        <tr  id="id3xw" sj="xw" dflag="1" class="bg_tr02"> 	 
	          	<td style="display:none"></td>
	          	<td>下午</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'14:30:00'})" readonly="readonly"/>
	           	</td>
	          	<td>
	          	<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          	</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a></td>
	        </tr>
	        <tr id="id3ws" sj="ws" dflag="1" class="bg_tr02">	         
	          	<td style="display:none"></td>
	          	<td>晚上</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'18:00:00'})" readonly="readonly"/></td>
	          	<td>
	          		<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          		</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	        	<input type='hidden' class='hiddenInput' value='' name='dateHms[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='phasezjs[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='memos[]'>
		</span>

		<span id="id4">
	      	<tr id="id4sw" sj="sw"  dflag="1"  class="bg_tr02">          
	      		<td rowspan="3"></td>
	          	<td>上午 </td>
	          	<td><input name="dateHms[]" type="text" size="10" class="blueinput" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'09:00:00'})" readonly="readonly"/></td>
	          	<td>		         
		         <select id="phasezjs" name="phasezjs[]">
		         	<option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
		         </select> 
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a  onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	        <tr  id="id4xw" sj="xw" dflag="1" class="bg_tr02"> 	 
	          	<td style="display:none"></td>
	          	<td>下午</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'14:30:00'})" readonly="readonly"/>
	           	</td>
	          	<td>
	          	<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          	</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a></td>
	        </tr>
	        <tr id="id4ws" sj="ws" dflag="1" class="bg_tr02">	         
	          	<td style="display:none"></td>
	          	<td>晚上</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'18:00:00'})" readonly="readonly"/></td>
	          	<td>
	          		<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          		</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	        	<input type='hidden' class='hiddenInput' value='' name='dateHms[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='phasezjs[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='memos[]'>
		</span>


		<span id="id5">
	      	<tr id="id5sw" sj="sw"  dflag="1"  class="bg_tr02">          
	      		<td rowspan="3"></td>
	          	<td>上午 </td>
	          	<td><input name="dateHms[]" type="text" size="10" class="blueinput" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'09:00:00'})" readonly="readonly"/></td>
	          	<td>		         
		         <select id="phasezjs" name="phasezjs[]">
		         	<option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
		         </select> 
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a  onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	        <tr  id="id5xw" sj="xw" dflag="1" class="bg_tr02"> 	 
	          	<td style="display:none"></td>
	          	<td>下午</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'14:30:00'})" readonly="readonly"/>
	           	</td>
	          	<td>
	          	<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          	</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a></td>
	        </tr>
	        <tr id="id5ws" sj="ws" dflag="1" class="bg_tr02">	         
	          	<td style="display:none"></td>
	          	<td>晚上</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'18:00:00'})" readonly="readonly"/></td>
	          	<td>
	          		<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          		</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	        	<input type='hidden' class='hiddenInput' value='' name='dateHms[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='phasezjs[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='memos[]'>
		</span>


		<span id="id6">
	      	<tr id="id6sw" sj="sw"  dflag="1"  class="bg_tr02">          
	      		<td rowspan="3"></td>
	          	<td>上午 </td>
	          	<td><input name="dateHms[]" type="text" size="10" class="blueinput" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'09:00:00'})" readonly="readonly"/></td>
	          	<td>		         
		         <select id="phasezjs" name="phasezjs[]">
		         	<option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
		         </select> 
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a  onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	        <tr  id="id6xw" sj="xw" dflag="1" class="bg_tr02"> 	 
	          	<td style="display:none"></td>
	          	<td>下午</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'14:30:00'})" readonly="readonly"/>
	           	</td>
	          	<td>
	          	<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          	</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a></td>
	        </tr>
	        <tr id="id6ws" sj="ws" dflag="1" class="bg_tr02">	         
	          	<td style="display:none"></td>
	          	<td>晚上</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'18:00:00'})" readonly="readonly"/></td>
	          	<td>
	          		<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          		</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	        	<input type='hidden' class='hiddenInput' value='' name='dateHms[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='phasezjs[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='memos[]'>
		</span>


		<span id="id7">
	      	<tr id="id7sw" sj="sw"  dflag="1"  class="bg_tr02">          
	      		<td rowspan="3"></td>
	          	<td>上午 </td>
	          	<td><input name="dateHms[]" type="text" size="10" class="blueinput" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'09:00:00'})" readonly="readonly"/></td>
	          	<td>		         
		         <select id="phasezjs" name="phasezjs[]">
		         	<option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
		         </select> 
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a  onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>

	        <tr  id="id7xw" sj="xw" dflag="1" class="bg_tr02"> 	 
	          	<td style="display:none"></td>
	          	<td>下午</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'14:30:00'})" readonly="readonly"/>
	           	</td>
	          	<td>
	          	<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          	</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a></td>
	        </tr>
	        <tr id="id7ws" sj="ws" dflag="1" class="bg_tr02">	         
	          	<td style="display:none"></td>
	          	<td>晚上</td>
	           	<td><input name="dateHms[]" type="text" class="blueinput" size="10" onfocus="WdatePicker({dateFmt:'HH:mm',startDate:'18:00:00'})" readonly="readonly"/></td>
	          	<td>
	          		<select id="phasezjs" name="phasezjs[]"><option value="">请选择</option><option value="1" >瑞思DEMO</option><option value="2" >玛特DEMO</option><option value="3" >瑞思FACE</option><option value="4" >玛特FACE</option>
	          		</select>
	          	</td>
	          	<td><input name="memos[]" type="text" class="blueinput" size="50" /></td>
	          	<td><a onclick="addRow(this)">加场</a>  <a onclick="deleteRow(this)">删除</a> </td>
	        </tr>
	        	<input type='hidden' class='hiddenInput' value='' name='dateHms[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='phasezjs[]'>
	        	<input type='hidden' class='hiddenInput' value='' name='memos[]'>

		</span>
		
		
      
		 <tr>
        	<td colspan="6" style="text-align:center">
        		<input type="submit" class="btn1" value="保存">&nbsp;
        		<!-- <input type="button" class="btn1" value="返回" onclick="javascript:history.back()"/>  -->     		
        		        		
        	</td>
        </tr>
        </table>
        </form>
    </div>
    <script type="text/javascript">
	    function getCurrentDate(){   
	        return new Date();   
	    };  
	    //获取当前时间    
        var currentDate=this.getCurrentDate();   
        //返回date是一周中的某一天    
        var week=currentDate.getDay();   
        //返回date是一个月中的某一天    
        var month=currentDate.getDate();   
       
        //一天的毫秒数    
        var millisecond=1000*60*60*24;   
        //减去的天数    
        var minusDay=week!=0?week-1:6;   
    	//加上的天数
    	var plusDay=week!=0?8-week:1;

    	function lastWeek(){
    		$('#demo_list').show();
    		
	        var currentWeekDayOne=new Date(currentDate.getTime()-(millisecond*minusDay));   
	        //上周最后一天即本周开始的前一天    
	        var priorWeekLastDay=new Date(currentWeekDayOne.getTime()-millisecond);   
	        //上周的第一天    
	        var priorWeekFirstDay=new Date(priorWeekLastDay.getTime()-(millisecond*6));
	        var priorWeek2Day = new Date(priorWeekLastDay.getTime()-(millisecond*5));
	        var priorWeek3Day = new Date(priorWeekLastDay.getTime()-(millisecond*4));
	        var priorWeek4Day = new Date(priorWeekLastDay.getTime()-(millisecond*3));
	        var priorWeek5Day = new Date(priorWeekLastDay.getTime()-(millisecond*2));
	        var priorWeek6Day = new Date(priorWeekLastDay.getTime()-(millisecond*1));

	        priorWeekFirstDay = (priorWeekFirstDay.getMonth()+1)+"-"+priorWeekFirstDay.getDate();
	        priorWeek2Day = (priorWeek2Day.getMonth()+1)+"-"+priorWeek2Day.getDate();
	        priorWeek3Day = (priorWeek3Day.getMonth()+1)+"-"+priorWeek3Day.getDate();
	        priorWeek4Day = (priorWeek4Day.getMonth()+1)+"-"+priorWeek4Day.getDate(); 
	        priorWeek5Day = (priorWeek5Day.getMonth()+1)+"-"+priorWeek5Day.getDate(); 
	        priorWeek6Day = (priorWeek6Day.getMonth()+1)+"-"+priorWeek6Day.getDate();   
	        priorWeekLastDay = (priorWeekLastDay.getMonth()+1)+"-"+priorWeekLastDay.getDate();  

   
        	$(id1sw).children('td').eq(0).html(priorWeekFirstDay+'(周一)');
        	$(id2sw).children('td').eq(0).html(priorWeek2Day+'(周二)');
        	$(id3sw).children('td').eq(0).html(priorWeek3Day+'(周三)');
        	$(id4sw).children('td').eq(0).html(priorWeek4Day+'(周四)');
        	$(id5sw).children('td').eq(0).html(priorWeek5Day+'(周五)');
        	$(id6sw).children('td').eq(0).html(priorWeek6Day+'(周六)');
        	$(id7sw).children('td').eq(0).html(priorWeekLastDay+'(周日)');

        	$(id1ws).nextAll('.hiddenInput').eq(0).attr('value',priorWeekFirstDay);
        	$(id2ws).nextAll('.hiddenInput').eq(0).attr('value',priorWeek2Day);
        	$(id3ws).nextAll('.hiddenInput').eq(0).attr('value',priorWeek3Day);
        	$(id4ws).nextAll('.hiddenInput').eq(0).attr('value',priorWeek4Day);
        	$(id5ws).nextAll('.hiddenInput').eq(0).attr('value',priorWeek5Day);
        	$(id6ws).nextAll('.hiddenInput').eq(0).attr('value',priorWeek6Day);
        	$(id7ws).nextAll('.hiddenInput').eq(0).attr('value',priorWeekLastDay);
 
    	}
		function currWeek(){
			$('#demo_list').show();
	        
	        //本周 周一    
	        var monday=new Date(currentDate.getTime()-(minusDay*millisecond)); 
	        var Tuesday = new Date(monday.getTime()+(1*millisecond));
	        var Wednesday = new Date(monday.getTime()+(2*millisecond));
	        var Thursday = new Date(monday.getTime()+(3*millisecond));
	        var Friday = new Date(monday.getTime()+(4*millisecond));
	        var Saturday = new Date(monday.getTime()+(5*millisecond)); 
	        //本周 周日    
	        var sunday=new Date(monday.getTime()+(6*millisecond));   
	        monday = (monday.getMonth()+1)+"-"+monday.getDate();
	        Tuesday = (Tuesday.getMonth()+1)+"-"+Tuesday.getDate();  
	        Wednesday = (Wednesday.getMonth()+1)+"-"+Wednesday.getDate();  
	        Thursday = (Thursday.getMonth()+1)+"-"+Thursday.getDate();  
	        Friday = (Friday.getMonth()+1)+"-"+Friday.getDate(); 
	        Saturday  = (Saturday.getMonth()+1)+"-"+Saturday.getDate(); 
	        //sunday = sunday.getYear()+"-"+(sunday.getMonth()+1)+"-"+sunday.getDate(); 
	        sunday = (sunday.getMonth()+1)+"-"+sunday.getDate();   
   
        	$(id1sw).children('td').eq(0).html(monday+'(周一)');
        	$(id2sw).children('td').eq(0).html(Tuesday+'(周二)');
        	$(id3sw).children('td').eq(0).html(Wednesday+'(周三)');
        	$(id4sw).children('td').eq(0).html(Thursday+'(周四)');
        	$(id5sw).children('td').eq(0).html(Friday+'(周五)');
        	$(id6sw).children('td').eq(0).html(Saturday+'(周六)');
        	$(id7sw).children('td').eq(0).html(sunday+'(周日)');

        	$(id1ws).nextAll('.hiddenInput').eq(0).attr('value',monday);
        	$(id2ws).nextAll('.hiddenInput').eq(0).attr('value',Tuesday);
        	$(id3ws).nextAll('.hiddenInput').eq(0).attr('value',Wednesday);
        	$(id4ws).nextAll('.hiddenInput').eq(0).attr('value',Thursday);
        	$(id5ws).nextAll('.hiddenInput').eq(0).attr('value',Friday);
        	$(id6ws).nextAll('.hiddenInput').eq(0).attr('value',Saturday);
        	$(id7ws).nextAll('.hiddenInput').eq(0).attr('value',sunday);
		}
		function nextWeek(){
			$('#demo_list').show();
	        
	        var monday=new Date(currentDate.getTime()+(plusDay*millisecond)); 
	        var Tuesday = new Date(monday.getTime()+(1*millisecond));
	        var Wednesday = new Date(monday.getTime()+(2*millisecond));
	        var Thursday = new Date(monday.getTime()+(3*millisecond));
	        var Friday = new Date(monday.getTime()+(4*millisecond));
	        var Saturday = new Date(monday.getTime()+(5*millisecond)); 
	        //本周 周日    
	        var sunday=new Date(monday.getTime()+(6*millisecond));   
	        monday = (monday.getMonth()+1)+"-"+monday.getDate();
	        Tuesday = (Tuesday.getMonth()+1)+"-"+Tuesday.getDate();  
	        Wednesday = (Wednesday.getMonth()+1)+"-"+Wednesday.getDate();  
	        Thursday = (Thursday.getMonth()+1)+"-"+Thursday.getDate();  
	        Friday = (Friday.getMonth()+1)+"-"+Friday.getDate(); 
	        Saturday  = (Saturday.getMonth()+1)+"-"+Saturday.getDate(); 
	        //sunday = sunday.getYear()+"-"+(sunday.getMonth()+1)+"-"+sunday.getDate(); 
	        sunday = (sunday.getMonth()+1)+"-"+sunday.getDate();   
   
        	$(id1sw).children('td').eq(0).html(monday+'(周一)');
        	$(id2sw).children('td').eq(0).html(Tuesday+'(周二)');
        	$(id3sw).children('td').eq(0).html(Wednesday+'(周三)');
        	$(id4sw).children('td').eq(0).html(Thursday+'(周四)');
        	$(id5sw).children('td').eq(0).html(Friday+'(周五)');
        	$(id6sw).children('td').eq(0).html(Saturday+'(周六)');
        	$(id7sw).children('td').eq(0).html(sunday+'(周日)');

        	$(id1ws).nextAll('.hiddenInput').eq(0).attr('value',monday);
        	$(id2ws).nextAll('.hiddenInput').eq(0).attr('value',Tuesday);
        	$(id3ws).nextAll('.hiddenInput').eq(0).attr('value',Wednesday);
        	$(id4ws).nextAll('.hiddenInput').eq(0).attr('value',Thursday);
        	$(id5ws).nextAll('.hiddenInput').eq(0).attr('value',Friday);
        	$(id6ws).nextAll('.hiddenInput').eq(0).attr('value',Saturday);
        	$(id7ws).nextAll('.hiddenInput').eq(0).attr('value',sunday);
		}



	function addRow(data){
		var html = '',row = '',id = '';
		html = $(data).parent('td').parent('tr').html();
		id = $(data).parents().parents().attr('id');
		
		id1 = '#'+id.substr(0,3)+'sw';
		//$(data).parent('td').parent('tr').parent('span').children('tr').eq(0).children('td').eq(0).attr('rowspan',row+1);
		row = $(id1).find('td[rowspan]').attr('rowspan')
		
		//alert('<tr>'+html+'</tr>')
		is = html.search('rowspan')
		if(is == -1){
			$(id1).find('td[rowspan]').attr('rowspan',parseInt(row)+1)
			$(data).parent('td').parent('tr').after('<tr id='+id+'>'+html+'</tr>');
		}else{
			//html = $(data).parent('td').parent('tr').find('td[rowspan]')
			html =  $(data).parent('td').parent('tr').html()
			html = html.replace(/<td rowspan=(.*?)>(.*?)<\/td>/," ");
			//<td rowspan="3"></td>
			$(id1).find('td[rowspan]').attr('rowspan',parseInt(row)+1)
			$(data).parent('td').parent('tr').after('<tr id='+id+'>'+html+'</tr>');
		}
    }
 
 
    function deleteRow(data){
    	var html = '',row = '',id = '';
		id = $(data).parents().parents().attr('id');		
		id1 = '#'+id.substr(0,3)+'sw';
		row = $(id1).find('td[rowspan]').attr('rowspan')
		dflag = $(data).parent('td').parent('tr').attr('dflag');
		if(dflag == 1){
			alert('不允许删除')
			return;
		}
		$(id1).find('td[rowspan]').attr('rowspan',parseInt(row)-1)
		$(data).parent('td').parent('tr').remove();
    }
    </script>
</body>
</html>
