<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object indexs 面板前台数据模型
 * Update:2013-11-01
 * 
 */

class indexs extends AmysqlModel
{

	// 登录验证
	function logins($user, $password)
	{
		$password = md5(md5($password.'_amysql-amh'));
		$sql = "SELECT user_id FROM amh_user WHERE user_name = '$user' AND user_password = '$password'";
		$data = mysql_fetch_assoc(mysql_query($sql));
		if(isset($data['user_id']))
			Return $data['user_id'];
		Return false;
	}

	// 是否允许登录
	function login_allow($amh_config)
	{
		$LoginErrorLimit = (int)$amh_config['LoginErrorLimit']['config_value'];
		$LoginErrorLimit = $LoginErrorLimit > 0 ? $LoginErrorLimit : 5;

		$login_ip = $_SERVER["REMOTE_ADDR"];
		$sql = "SELECT * FROM amh_login WHERE login_ip = '$login_ip' AND login_error_tag = '1' ORDER BY login_id DESC";
		$result = mysql_query($sql);
		$num = mysql_num_rows($result);
		$n = ceil($num/$LoginErrorLimit);
		if ($num >= $LoginErrorLimit && $num%$LoginErrorLimit == 0)
		{		
			$data = mysql_fetch_assoc($result);
			$allow_time = strtotime($data['login_time']) + pow($n,3)*60;
			if (time() < $allow_time)
				Return array('status' => false, 'allow_time' => $allow_time, 'login_error_sum' => $num);
		}
		Return array('status' => true, 'login_error_sum' => $num);
	}

	// 登录写记录
	function login_insert($login_success, $user_name)
	{
		$login_ip = $_SERVER["REMOTE_ADDR"];
		$login_error_tag = $login_success ? '0' : '1';
		$login_time = date('Y-m-d H:i:s', time());
		$sql = "INSERT INTO amh_login(login_user_name, login_ip, login_success, login_error_tag, login_time) VALUES('$user_name', '$login_ip', '$login_success', '$login_error_tag', '$login_time')";
		$this -> _query($sql);

		if($login_success)
		{
			$sql = "UPDATE amh_login SET login_error_tag = '0' WHERE login_ip = '$login_ip'";
			$this -> _query($sql);
		}
	}

	// 日志写记录
	function log_insert($txt)
	{
		if(empty($txt)) return;
		$data['log_user_id'] = $_SESSION['amh_user_id'];
		$data['log_text'] = $txt;
		$data['log_ip'] = $_SERVER["REMOTE_ADDR"];
		$this -> _insert('amh_log', $data);
	}
}

?>