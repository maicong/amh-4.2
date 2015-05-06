<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object account 管理员控制器
 * Update:2013-11-01
 * 
 */

class account extends AmysqlController
{
	public $indexs = null;
	public $accounts = null;
	public $notice = null;
	public $top_notice = null;

	// 载入数据模型(Model)
	function AmysqlModelBase()
	{
		if($this -> indexs) return;
		$this -> _class('Functions');
		$this -> indexs = $this ->  _model('indexs');
		$this -> accounts = $this ->  _model('accounts');
	}

	// 默认访问
	function IndexAction()
	{
		$this -> account_log();
	}

	// 操作日志
	function account_log()
	{
		$this -> title = '操作日志 - 账号 - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$page_sum = 20;
		$log_list = $this -> accounts -> log_list($page, $page_sum);
		$total_page = ceil($log_list['sum'] / $page_sum);						
		$page_list = Functions::page('AccountLog', $log_list['sum'], $total_page, $page);		// 分页列表

		$this -> page = $page;
		$this -> total_page = $total_page;
		$this -> page_list = $page_list;
		$this -> log_list = $log_list;
		$this -> _view('account_log');
	}

	// 登录日志
	function account_login_log()
	{
		$this -> title = '登录日志 - 账号 - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$page_sum = 20;
		$login_list = $this -> accounts -> login_list($page, $page_sum);
		$total_page = ceil($login_list['sum'] / $page_sum);						
		$page_list = Functions::page('AccountLog', $login_list['sum'], $total_page, $page);		// 分页列表

		$this -> page = $page;
		$this -> total_page = $total_page;
		$this -> page_list = $page_list;
		$this -> login_list = $login_list;
		$this -> _view('account_login_log');
	}

	// 更改密码
	function account_pass()
	{
		$this -> title = '更改密码 - 账号 - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		if (isset($_POST['submit']))
		{
			$user_password = $_POST['user_password'];
			$new_user_password = $_POST['new_user_password'];
			$new_user_password2 = $_POST['new_user_password2'];
			$error = '';
			$this -> status = 'error';

			$status = $this -> indexs -> logins($_SESSION['amh_user_name'], $user_password);
			if ($status)
			{
				if(empty($new_user_password) || empty($new_user_password2))
					$error = '新密码与确认新密码不能为空。';
				elseif($new_user_password != $new_user_password2)
					$error = '新密码与确认新密码不一致。';
			}
			else
				$error = '旧密码错误。';

			if (empty($error))
			{
				$status = $this -> accounts -> change_pass($new_user_password);
				if($status)
				{
					$this -> status = 'success';
					$this -> notice = '更改密码成功。';
				}
				else
					$this -> notice = '更改密码失败。';
			}
			else
				$this -> notice = $error;
		}

		$this -> indexs -> log_insert($this -> notice);
		$this -> _view('account_pass');
	} 

}

?>