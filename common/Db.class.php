<?php
	/**
		数据库操作类
	*/
	Class DB{
		public $conn = null;
		public function __Construct($config){
			$this->conn = mysql_connect($config['host'],$config['username'],$config['password']) or die(mysql_error());
			mysql_select_db($config['database'],$this->conn) or die(mysql_error());
			mysql_query('set names '. $config['charset'],$this->conn) or die(mysql_error());
		}
		//根据sql获取结果集数组
		public function getResult($sql){
			$resource = mysql_query($sql,$this->conn);
			$result = array();
			while($row = mysql_fetch_assoc($resource)){
				$result[] = $row;
			}
			return $result;
		}
		//按照学校获取结果集数组
		public function getResultBySchool($school){
			$sql = "select no,tel,s_name,dt_date,tel_status,school from data_base where school='$school'";
			return $this->getResult($sql);
		}
		//按照状态获取结果集数组
		public function getResultByStatus($status){
			$sql = "select no,tel,s_name,dt_date,tel_status,school from data_base where tel_status='$status'";
			return $this->getResult($sql);
		}
		//获取所有学校
		public function getAllSchool(){
			$sql = 'select DISTINCT school from data_base';
			return $this->getResult($sql);
		}
		//按照学校获取所有当前学校的所有状态
		public function getAllStatusBySchool($school){
			$sql = 'select DISTINCT tel_status from data_base where school="'.$school.'"';
			return $this->getResult($sql);
		}
		//按照学校和状态获取学生姓名和电话
		public function getDataBySchoolStatus($school,$status){
			$sql = "select no,s_name,tel from data_base where school='$school' and tel_status='$status'";
			return $this->getResult($sql);
		}
		//按照地推人获取名单列表
		public function getDataByDtr($name){
			$sql = "select no,s_name,tel from data_base where dt_name='$name'";
			return $this->getResult($sql);
		}
	}
?>