<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object mysqls MySQL管理数据模型
 * Update:2013-11-01
 * 
 */

class mysqls extends AmysqlModel
{
	public $databases_systems_list = array('amh', 'mysql', 'information_schema', 'performance_schema');

	// 数据库列表
	function databases()
	{
		$sql = "SHOW COLLATION";
		$result = $this -> _query($sql);
		while ($rs = mysql_fetch_assoc($result))
		{
			if($rs['Default'] == 'Yes') 
				$CollationDefault[$rs['Charset']] = $rs['Collation'];
		}

		$data_all = $data = array();
		$sql = "SHOW DATABASES";
		$result = mysql_query($sql);
		while ($rs = mysql_fetch_assoc($result))
		{
			$DBname = $rs['Database'];
			$sql = "SHOW TABLES FROM `$DBname` ";
			$rs['sum'] = mysql_num_rows($this -> _query($sql));

			$sql = "SHOW CREATE DATABASE `$DBname` ";
			$collations = mysql_fetch_assoc($this -> _query($sql));
			$collations = explode(' ', $collations['Create Database']);
			$rs['collations'] = $collations[count($collations)-2];
			$rs['collations'] = (isset($CollationDefault[$rs['collations']])) ? $CollationDefault[$rs['collations']] : $rs['collations'];
			if (in_array($rs['Database'], $this -> databases_systems_list))
				$data_all[] = $rs;
			else
				$data[] = $rs;
		}
		usort($data, array('mysqls', 'databases_sort'));
		usort($data_all, array('mysqls', 'databases_sort'));
		foreach ($data as $val)
			$data_all[] = $val;
		Return $data_all;
	}

	// 进行排序
	function databases_sort($a, $b)
	{
		if ($a['Database'] == $b['Database']) 
			Return 0;
		Return ($a['Database'] < $b['Database']) ? -1 : 1;
	}

	// 删除数据库
	function del_database($db_name)
	{
		$sql = "DROP DATABASE `$db_name`";
		Return $this -> _query($sql);
	}
	// 清空数据库
	function empty_database($db_name)
	{
		$status = false;
		$sql = "SHOW TABLES FROM `$db_name`";
		if ($this -> _query($sql))
		{
			$table_list = $this -> _all($sql);
			$status = true;
			foreach ($table_list as $key=>$val)
			{
				$table_name = $val['Tables_in_' . $db_name];
				$sql = "DROP TABLE `$db_name`.`$table_name`";
				if(!$this -> _query($sql))
					$status = false;
			}
		}
		Return $status;
	}


	// 取得php配置参数值
	function get_mysql_param($param_list)
	{
		$cmd = "amh cat_my_cnf";
		$cmd = Functions::trim_cmd($cmd);
		$my_cnf = Functions::trim_result(shell_exec($cmd));
		foreach ($param_list as $key=>$val)
		{
			preg_match("/$val[1] = (.*)/", $my_cnf, $param_val);
			if ($val[1] == 'InnoDB_Engine')
				$param_val[1] = preg_match("/innodb = OFF/", $my_cnf) ? 'Off' : 'On';
			elseif ($val[1] == 'log_bin')
				$param_val[1] = preg_match("/\n\s*log-bin = mysql-bin/", $my_cnf) ? 'On' : 'Off';
			
			$param_list[$key][3] = $param_val[1];
		}
		Return $param_list;
	}

	// 创建数据库
	function create_database($dbname, $character)
	{
		$character_arr = explode('_', $character);
		$sql = "CREATE DATABASE `$dbname` DEFAULT CHARACTER SET {$character_arr[0]} COLLATE {$character}";
		Return $this -> _query($sql);
	}

	// 创建权限
	function create_grant($dbname, $user_name, $user_password, $user_host, $grant)
	{
		// 权限字段
		$field_arr = array(
			'grant_read' => array('SELECT'),
			'grant_write' => array('INSERT', 'UPDATE', 'DELETE'),
			'grant_admin' => array('CREATE','ALTER','INDEX','DROP','CREATE TEMPORARY TABLES','SHOW VIEW','CREATE ROUTINE','ALTER ROUTINE','EXECUTE','CREATE VIEW', 'REFERENCES', 'LOCK TABLES')
		);

		// 权限设置
		$grant_list = array();
		if (in_array('grant_all', $grant))
		{
			$grant_list = array('ALL PRIVILEGES');
			$grant_option = 'GRANT OPTION';
		}
		else
		{
		    if(in_array('grant_read', $grant)) $grant_list = array_merge($field_arr['grant_read'], $grant_list);
		    if(in_array('grant_write', $grant)) $grant_list = array_merge($field_arr['grant_write'], $grant_list);
			if (in_array('grant_admin', $grant))
			{
				 $grant_list = array_merge($field_arr['grant_admin'], $grant_list);
				 $grant_option = 'GRANT OPTION';
			}
		}
		$grant_list = implode(',', $grant_list);
		$sql_u = "CREATE USER '{$user_name}'@'{$user_host}'";
		$sql_p = "SET PASSWORD FOR '{$user_name}'@'{$user_host}' = PASSWORD('{$user_password}')";
		$sql_g = "GRANT {$grant_list} ON `{$dbname}`.* TO '{$user_name}'@'{$user_host}' WITH {$grant_option} MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0";
		Return ($this -> _query($sql_u) && $this -> _query($sql_p) && $this -> _query($sql_g));
	}

	// 取得数据库用户
	function get_mysql_user_list()
	{
		$sql = "SELECT User, Host FROM mysql.user ORDER BY User ASC";
		Return $this -> _all($sql);
	}

	// 修改MySQL用户密码
	function set_mysql_password($user_name, $user_password)
	{
		$sql = "SET PASSWORD FOR '{$user_name -> User}'@'{$user_name -> Host }' = PASSWORD('{$user_password}')";
		Return $this -> _query($sql);
	}

	// 删除MySQL用户
	function del_mysql_user($user_name, $user_password)
	{
		$sql = "DROP USER '{$user_name -> User}'@'{$user_name -> Host }'";
		Return $this -> _query($sql);
	}

}

?>