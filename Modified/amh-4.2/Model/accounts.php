<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object accounts 管理员数据模型
 * Update:2013-11-01
 * 
 */

class accounts extends AmysqlModel
{

	// 更改密码
	function change_pass($user_password)
	{
		$user_name = $_SESSION['amh_user_name'];
		$user_password = md5(md5($user_password.'_amysql-amh'));
		$sql = "UPDATE amh_user SET user_password = '$user_password' WHERE user_name = '$user_name'";
		$this -> _query($sql);
		Return $this -> Affected;
	}
	

	// 日志列表
	function log_list($page = 1, $page_sum = 20)
	{
		$where = '';
		if (isset($_GET['search']))
		{
			$field_arr = array('user_name', 'log_text', 'log_ip');
			$field = (int)$_GET['field'];
			$field = $field_arr[$field];

			$search = isset($_GET['search']) ? $_GET['search'] : '';
			$start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
			$end_time = isset($_GET['end_time']) ? $_GET['end_time'] : '';

			if (!empty($search))
				$where .= " AND {$field} LIKE '%{$search}%' ";
			if (!empty($start_time))
				$where .= " AND log_time >= '{$start_time}' ";
			if (!empty($end_time))
				$where .= " AND log_time <= '{$end_time}' ";
		}

		$sql = "SELECT * FROM amh_log AS al LEFT JOIN amh_user AS au ON al.log_user_id = au.user_id WHERE 1 $where";
		$sum = $this -> _sum($sql);

		$limit = ' LIMIT ' . ($page-1)*$page_sum . ' , ' . $page_sum;
		$sql = "SELECT al.*, au.user_name FROM amh_log AS al LEFT JOIN amh_user AS au ON al.log_user_id = au.user_id WHERE 1 $where ORDER BY al.log_id DESC $limit";
		Return array('data' => $this -> _all($sql), 'sum' => $sum);
	}

	// 登录记录列表
	function login_list($page = 1, $page_sum = 20)
	{
		$where = '';
		if (isset($_GET['search']))
		{
			$field_arr = array('login_user_name', 'login_ip');
			$field = (int)$_GET['field'];
			$field = $field_arr[$field];

			$search = isset($_GET['search']) ? $_GET['search'] : '';
			$login_success = isset($_GET['login_success']) ? $_GET['login_success'] : '';
			$start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
			$end_time = isset($_GET['end_time']) ? $_GET['end_time'] : '';

			if (!empty($search))
				$where .= " AND {$field} LIKE '%{$search}%' ";
			if (!empty($login_success))
			{
				$login_success = ($login_success == '2') ? '0' : '1';
				$where .= " AND login_success LIKE '{$login_success}' ";
			}
			if (!empty($start_time))
				$where .= " AND login_time >= '{$start_time}' ";
			if (!empty($end_time))
				$where .= " AND login_time <= '{$end_time}' ";
		}


		$sql = "SELECT * FROM amh_login WHERE 1 $where ";
		$sum = $this -> _sum($sql);

		$limit = ' LIMIT ' . ($page-1)*$page_sum . ' , ' . $page_sum;
		$sql = "SELECT * FROM amh_login WHERE 1 $where ORDER BY login_id DESC $limit";

		Return array('data' => $this -> _all($sql), 'sum' => $sum);
	}

}

?>