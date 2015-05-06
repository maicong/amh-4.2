<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object index 面板前台&主页控制器
 * Update:2013-11-01
 * 
 */

class index extends AmysqlController
{
	public $indexs = null;
	public $configs = null;
	public $action_name = array('start' => '启动' , 'stop' => '停止' , 'reload' => '重载', 'restart' => '重启');
	public $notice = null;
	public $top_notice = null;

	// 载入数据模型(Model)
	function AmysqlModelBase()
	{
		if($this -> indexs) return;
		$this -> _class('Functions');
		$this -> indexs = $this ->  _model('indexs');
		$this -> configs = $this ->  _model('configs');
	}


	// 面板登录
	function login()
	{
		$this -> title = '登录 - AMH';
		$this -> AmysqlModelBase();
		$amh_config = $this -> configs -> get_amh_config();

		if (isset($_POST['login']))
		{
			$login_allow = $this -> indexs -> login_allow($amh_config);

			// 允许登录
			if($login_allow['status'])
			{
				$user = $_POST['user'];
				$password = $_POST['password'];
				$VerifyCode = $_POST['VerifyCode'];
				if ($amh_config['VerifyCode']['config_value'] == 'on' && strtolower($VerifyCode) != $_SESSION['VerifyCode'])
				{
					$this -> LoginError = '验证码错误，请重新输入。';
				}
				else
				{
					if(empty($user) || empty($password))
						$this -> LoginError = '请输入用户名与密码。';
					else
					{
						$user_id = $this -> indexs -> logins($user, $password);
						if($user_id)
						{

							$this -> indexs -> login_insert(1, $user);
							$_SESSION['amh_user_name'] = $user;
							$_SESSION['amh_user_id'] = $user_id;
							$_SESSION['amh_config'] = $amh_config;
							$_SESSION['amh_token'] = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0,8);
							$token = ($amh_config['OpenCSRF']['config_value'] == 'on') ? '?amh_token=' . $_SESSION['amh_token'] : '';
							header('location:./index.php' . $token);
							exit();
						}
						$_POST['password'] = '';
						$this -> LoginError = '账号或密码错误，登录失败。(' . ($login_allow['login_error_sum']+1) . '次)';
						$this -> login_error_sum = $login_allow['login_error_sum'];
						$this -> indexs -> login_insert(0, $user);
					}
				}
			}
			else
			{
			    $this -> LoginError = '登录出错已有' . $login_allow['login_error_sum'] . '次。当前禁止登录，下次允许登录时间:' . date('Y-m-d H:i:s', $login_allow['allow_time']);
			}
		}

		$this -> amh_config = $amh_config;
		$this -> _view('login');
		exit();
	}

	// 面板主页
	function IndexAction()
	{
		$this -> title = '主页 - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();
		$_SESSION['amh_version'] = '4.2';

		$m = isset($_GET['m']) ? $_GET['m'] : '';
		$g = isset($_GET['g']) ? $_GET['g'] : '';

		if (!empty($m) && !empty($g) && in_array($m, array('host', 'php', 'nginx', 'mysql')) && in_array($g, array('start', 'stop', 'reload', 'restart')) ) 
		{
			$cmd = "amh $m $g";
			$cmd = Functions::trim_cmd($cmd);
			exec($cmd, $tmp, $status);
			if (!$status)
			{
				$this -> status = 'success';
				$this -> notice = "$m " . $this -> action_name[$g] . '成功。';
			}
			else
			{
			    $this -> status = 'error';
				$this -> notice = "$m " . $this -> action_name[$g] . '失败。';
			}
		}
		
		$this -> indexs -> log_insert($this -> notice);
		$this -> _view('index');
	}


	// 面板系统信息
	function infos()
	{
		$this -> AmysqlModelBase();
		Functions::CheckLogin();
		$cmd = "amh info";
		$result = shell_exec($cmd);
		$result = trim(Functions::trim_result($result), "\n ");
		$this -> infos = $result;
		$this -> _view('infos');
	}

	// PHPINFO
	function phpinfo()
	{
		$this -> title = 'PHPINFO - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();
		$this -> _view('phpinfos');
	}

	// CSRF提示
	function index_csrf()
	{
		$this -> title = 'CSRF提示 - AMH';
		$this -> _view('index_csrf');
	}
			
			

	// 退出
	function logout()
	{
		$this -> title = '退出 - AMH';
		$_SESSION['amh_user_name'] = null;
		$_SESSION['amh_user_id'] = null;
		unset($_SESSION['module_score']);
		$_COOKIE['LoginKey'] = '';
		$this -> _view('logout');
	}


	// 面板最新消息
	function ajax()
	{
		$this -> AmysqlModelBase();
		Functions::CheckLogin();
		$timeout = array(
			'http'=>array(
				'method'=>"GET",
				'timeout'=>8,
			)
		);
		$context = stream_context_create($timeout);
		$html = file_get_contents('http://amysql.com/index.php?c=index&a=AMH&tag=ajax&V=' . $_SESSION['amh_version'], false, $context);
		$html = htmlspecialchars($html);
		$html = str_replace('[br]', '<br />', $html);
		$html = preg_replace('/\[url\]([a-z\_]+)\[\/url\]/i', '<a href="http://amysql.com/AMH.htm?tag=$1" target="_blank"> http://amysql.com/AMH.htm?tag=$1</a>', $html);
		echo $html;
		exit();
	}

	// 面板模块信息
	function module_ajax()
	{
		$this -> AmysqlModelBase();
		Functions::CheckLogin();
		Functions::get_module_score();
		Functions::get_module_available();
		echo json_encode($_SESSION['module_available']);
	}

}