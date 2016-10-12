<?php

	/**
	 *  用于多表联合的分页
	 * 
	 *   @param	string	$tableName	给一个表名，用于通过表获取总数
	 *   @param	string	$url		每次请求的url
	 *   @param	int	$num		每页记录个数
	 *   @return	array			有两个一个是分页字符串"fpage-0",一个每页"limit-1" 	 *
	 *
	 */

	
	function double_fpage($tableName1,$tableName2,$where="", $url="", $num=10,$having) {
		//总记录数
		//$sql="select count(*) as count from  {$where}";
		$sql="SELECT count(t.telno) FROM {$tableName1} as t   join {$tableName2} as b on t.telno=b.no {$where} group by t.telno $having";

		$result = mysql_query($sql);
		$row = mysql_affected_rows();
		$total =  $row["count"];  //总数
		$total = $row;
		//echo $sql,$row;
		//共多少页
		$pagenum = ceil($total/$num);

        $url=!strstr($url, "?") ? $url.'?' : $url; 

		//当前是第几页
		$cpage = !empty($_GET["page"]) ? $_GET["page"] : 1;

		$offset = ($cpage-1) * $num;

		$limit="limit {$offset}, {$num}";


		//如果是第一页
		if($cpage == 1){
			$fp='';

		//如果不在第一页
		}else {
			$fp = '<a href="'.$url.'&page=1">首页</a>&nbsp;&nbsp;';
			$prev=$cpage - 1;
			$fp .= '<a href="'.$url.'&page='.$prev.'">上一页</a>&nbsp;&nbsp;';
		}

		
		//如果是最后一页
		if($cpage == $pagenum){
			$nl='';

		//如果不是最后一页
		}else {
			$next=$cpage + 1;
			$nl = '<a href="'.$url.'&page='.$next.'">下一页</a>&nbsp;&nbsp;';
		
			$nl .= '<a href="'.$url.'&page='.$pagenum.'">尾页</a>&nbsp;&nbsp;';
		}

		//前5个
		for($i=5; $i >= 1; $i--) {
			$page = $cpage - $i;

			if($page > 0)
				$ls.= '<a href="'.$url.'&page='.$page.'">'.$page.'</a>&nbsp;';
		}
		//页数大于1
		if($pagenum > 1)
			$ls .= $cpage.'&nbsp;'; 
		
		//后5个
		for($i=1; $i <= 5; $i++) {
			$page = $cpage + $i;

			if($page <= $pagenum ) 
				$ls.= '<a href="'.$url.'&page='.$page.'">'.$page.'</a>&nbsp;';
		}


		if($total)
			$fpage='<span style="font-size:12px">共 <b>'.$total.'</b> 条记录&nbsp;&nbsp;&nbsp;&nbsp;每页显示 <b>'.$num.'</b> 条&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$cpage.'/'.$pagenum.'</b>  &nbsp;&nbsp;'.$fp.'&nbsp;&nbsp;'.$ls.$nl.'</span>';
		else
			$fpage='<span style="font-size:12px">共 <b>'.$total.'</b> 条记录&nbsp;&nbsp;&nbsp;&nbsp;每页显示 <b>'.$num.'</b> 条&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$cpage.'/'.$pagenum.'</b></span>';

		return array($fpage, $limit, "fpage"=>$fpage, "limit"=>$limit);

	}

	
