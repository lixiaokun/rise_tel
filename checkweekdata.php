<?php
	require './common/qx.php';
	header("Content-type: text/html; charset=utf-8");
	$current_user = $_SESSION['uid'];
	//周一
	$time = time();
	$week = date('N',$time);

	$arr = array();
	$hj_total = 0;
	$hj_wx = 0;
	$hj_wjt = 0;
	$hj_zlx = 0;
	$hj_wcn = 0;
	$hj_cn = 0;
	for($i = 1;$i <= 7;$i++){
		$day = date('Y-n-j',$time - ($week-$i)*(60*60*24));
		$sql = "select count(no) as total,tel_status from data_base as db join tel_bd as t on t.telno=db.no where UNIX_TIMESTAMP(t.bd_date)=UNIX_TIMESTAMP('$day') and db.fp_name='$current_user' and t.id in (select max(id) from tel_bd group by telno) group by tel_status";
		$res = mysql_query($sql);
		while($row = mysql_fetch_assoc($res)){
			$r_key = $row['tel_status'];
			$r_value = $row['total'];
			$arr[$day][$r_key] = $r_value;
		}
		$arr[$day]['total'] = array_sum($arr[$day]);
		$hj_total += $arr[$day]['total'];
		$hj_wx += $arr[$day]['0'];
		$hj_wjt += $arr[$day]['7'];
		$hj_zlx += $arr[$day]['8'];
		$hj_wcn += $arr[$day]['1'];
		$hj_cn += $arr[$day]['2'];
		$hj_smwjf += $arr[$day]['3'];
	}
	$arr['合计']['total'] = $hj_total; 
	$arr['合计']['0'] = $hj_wx; 
	$arr['合计']['7'] = $hj_wjt; 
	$arr['合计']['8'] = $hj_zlx; 
	$arr['合计']['1'] = $hj_wcn; 
	$arr['合计']['2'] = $hj_cn; 
	$arr['合计']['3'] = $hj_smwjf; 

	//将数组遍历为表格
	// '0' => '无效',
	// '1' => '未承诺上门',
	// '2' => '承诺上门',	
	// '7' => '未接听',
	// '8' => '再联系',
	// '3' => '上门未缴费',
	// '99' => '未拨打'
	$table = '<table style="margin-top:15px">';
	$table .= '<tr>';
	$table .= '<th>日期</th>';
	$table .= '<th>拨打总数</th>';
	$table .= '<th>无效</th>';
	$table .= '<th>未接听</th>';
	$table .= '<th>再联系</th>';
	$table .= '<th>未承诺上门</th>';
	$table .= '<th>承诺上门</th>';
	$table .= '<th>上门未缴费</th>';
	$table .= '</tr>';
	foreach ($arr as $key => $value) {
		$total = $value['total'] ? $value['total'] : 0;
		$wx = $value['0'] ? $value['0'] : 0;
		$wjt = $value['7'] ? $value['7'] : 0;
		$zlx = $value['8'] ? $value['8'] : 0;
		$wcn = $value['1'] ? $value['1'] : 0;
		$cn = $value['2'] ? $value['2'] : 0;
		$smwjf = $value['3'] ? $value['3'] :0 ;
		$table .= <<<html
		<tr>
			<td>$key</td>
			<td>{$total}</td>
			<td>{$wx}</td>
			<td>{$wjt}</td>
			<td>{$zlx}</td>
			<td>{$wcn}</td>
			<td>{$cn}</td>
			<td>{$smwjf}</td>
		</tr>
html;
	}
	$table .= '</table>';
	echo $table;
	die;
?>