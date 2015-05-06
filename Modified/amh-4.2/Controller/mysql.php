<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object mysql MySQL管理控制器
 * Update:2013-11-01
 * 
 */

class mysql extends AmysqlController
{
	public $indexs = null;
	public $mysqls = null;
	public $notice = null;
	public $top_notice = null;

	// 载入数据模型(Model)
	function AmysqlModelBase()
	{
		if($this -> indexs) return;
		$this -> _class('Functions');
		$this -> indexs = $this ->  _model('indexs');
		$this -> mysqls = $this ->  _model('mysqls');
	}

	// 默认访问
	function IndexAction()
	{
		$this -> mysql_list();
	}

	// 数据库列表
	function mysql_list()
	{
		$this -> title = 'MySQL - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		// 删除数据库
		if (isset($_GET['del']))
		{
			$de_name = $_GET['del'];
			if (!in_array($de_name, $this -> mysqls -> databases_systems_list) && $this -> mysqls -> del_database($de_name))
			{
				$this -> status = 'success';
				$this -> notice = "MySQL数据库: {$de_name} 删除成功。";
			}
			else
			{
				$this -> status = 'error';
				$this -> notice = "MySQL数据库: {$de_name} 删除失败。";
			}
		}
		// 清空数据库
		if (isset($_GET['empty']))
		{
			$de_name = $_GET['empty'];
			if (!in_array($de_name, $this -> mysqls -> databases_systems_list) && $this -> mysqls -> empty_database($de_name))
			{
				$this -> status = 'success';
				$this -> notice = "MySQL数据库: {$de_name} 清空成功。";
			}
			else
			{
				$this -> status = 'error';
				$this -> notice = "MySQL数据库: {$de_name} 清空失败。";
			}
		}

		if (isset($_GET['ams']))
		{
			// 打开数据库列表
			if ($_GET['ams'] == 'OpenDatabaseJs')
			{
				header('Content-type: application/x-javascript');
				$open_database = isset($_SESSION['open_database_name']) && !empty($_SESSION['open_database_name']) ? true : false;
				$AmysqlHomeStatus = $open_database ? 'Normal' : 'Activate';
				$_AmysqlTabJson = "var _AmysqlTabJson = [";
				$_AmysqlTabJson .= "{'type':'" . $AmysqlHomeStatus . "','id':'AmysqlHome','name':'AmysqlHome - localhost', 'url': '" . _Http . "ams/index.php?c=ams&a=AmysqlHome'}";
				if($open_database)
				{
					$ODN = $_SESSION['open_database_name'];
					$_AmysqlTabJson .= ", {'type':'Activate','id':'AmysqlDatabase_" . $ODN . "','name':'" . $ODN ."', 'url': '" . _Http . "ams/index.php?c=ams&a=AmysqlDatabase&DatabaseName=" . $ODN ."'}";
				}
				$_AmysqlTabJson .= "];";
				echo $_AmysqlTabJson;
				exit();
			}
			elseif ($_GET['ams'] == 'index')
			{
				$_SESSION['open_database_name'] = null;
				$_SESSION['create_database'] = null;
			}
			elseif ($_GET['ams'] == 'database')
			{
			    if (!empty($_GET['name']))
					$_SESSION['open_database_name'] = $_GET['name'];
				$_SESSION['create_database'] = null;
			}
			header('location:./ams/');
			exit();
		}

		$this -> databases_systems_list = $this -> mysqls -> databases_systems_list;
		$this -> databases = $this -> mysqls -> databases();
		$this -> _view('mysql');
	} 

	// 设置参数
	function mysql_setparam()
	{
		$this -> title = '设置参数 - MySQL - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		$param_list = array(
			array('设置是否开启InnoDB引擎','InnoDB_Engine', 'On / Off'),
			array('是否开启MySQL二进制日志','log_bin', 'On / Off'),
			array('MyISAM索引缓冲区大小','key_buffer_size', '16M'),
			array('客户端/服务器之间通信缓存区最大值','max_allowed_packet', '1M'),
			array('设置打开表数目最大缓存值','table_open_cache', '64'),
			array('设置每一个连接缓存一次性分配的内存','sort_buffer_size', '512K'),
			array('TCP/IP和套接字通信缓冲区大小','net_buffer_length', '8K'),
		);

		if (isset($_POST['submit']))
		{
			foreach ($param_list as $key=>$val)
			{
				$post_keyname = str_replace('.', '_', $val[1]);
				$cmd = "amh SetParam mysql $val[1] {$_POST[$post_keyname]}";
				$cmd = Functions::trim_cmd($cmd . ' noreload');		// 只更改参数不重启
				exec($cmd, $tmp, $status);
			}

			if (!$status)
			{
				$this -> status = 'success';
				$this -> notice = 'MySQL配置更改成功。';
			}
			else
			{
				$this -> status = 'error';
				$this -> notice = 'MySQL配置更改失败。';
			}
		}
		
		$param_list = $this -> mysqls -> get_mysql_param($param_list);
		$this -> param_list = $param_list;
		$this -> indexs -> log_insert($this -> top_notice . $this -> notice);
		$this -> _view('mysql_setparam');

	}

	// 快速建库
	function mysql_create()
	{
		$this -> title = '快速建库 - MySQL - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		if (isset($_POST['submit']))
		{
			$database_name = $_POST['database_name'];
			$database_character = $_POST['database_character'];
			$user_name = $_POST['user_name'];
			$user_password = $_POST['user_password'];
			$user_host = $_POST['user_host'];
			$grant = $_POST['grant'];

			if ($this -> mysqls -> create_database($database_name, $database_character))
			{
				$this -> status = 'success';
				$this -> notice = '数据库创建成功：' . $database_name;
				if (isset($_POST['create_user']))
				{
					if ($this -> mysqls -> create_grant($database_name, $user_name, $user_password, $user_host, $grant))
					{
						$this -> status = 'success';
						$this -> notice .= ' 用户权限创建成功：' . $user_name;
					}
					else
					{
						$this -> status = 'error';
						$this -> notice .= ' 用户权限创建失败：' . mysql_error();
					}
				}
				$_POST = array();
			}
			else
			{
				$this -> status = 'error';
				$this -> notice = '数据库创建失败：' . mysql_error();
			}
		}

		$this -> indexs -> log_insert($this -> top_notice . $this -> notice);
		$this -> _view('mysql_create');
	}


	// 修改密码
	function mysql_password()
	{
		$this -> title = '修改密码 - MySQL - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		if (isset($_POST['submit']))
		{
			$user_action = $_POST['user_action'];
			$user_password1 = $_POST['user_password1'];
			$user_password2 = $_POST['user_password2'];
			$mysql_user_list = json_decode(base64_decode($_POST['mysql_user_list']));
			$user_name = $mysql_user_list[$_POST['user_name']];

			// 删除或修改密码
			if ($user_action == 'del')
			{
				if ($user_name -> User == 'root')
				{
					$this -> status = 'error';
					$this -> notice .= ' MySQL用户删除失败： 面板不允许删除root账号。' ;
				}
				elseif ($this -> mysqls -> del_mysql_user($user_name))
				{
					$this -> status = 'success';
					$this -> notice .= ' MySQL用户删除成功：' . $user_name -> User . ' - ' . $user_name -> Host;
					$_POST = array();
				}
				else
				{
					$this -> status = 'error';
					$this -> notice .= ' MySQL用户删除失败：' . mysql_error();
				}
			}
			else
			{
				if ($user_password1 == $user_password2)
				{
					if ($_SESSION['amh_config']['DataPrivate']['config_value'] == 'on' && $user_name -> User == 'root')
					{
						$this -> status = 'error';
						$this -> notice .= ' MySQL用户密码修改失败： 您已开启面板数据私有保护，面板不可更改root账号密码。' ;
					}
					elseif ($this -> mysqls -> set_mysql_password($user_name, $user_password1))
					{
						$this -> status = 'success';
						$this -> notice .= ' MySQL用户密码修改成功：' . $user_name -> User . ' - ' . $user_name -> Host;

						if ($user_name -> User == 'root' && $user_name -> Host == 'localhost')
						{
							$file = '/home/wwwroot/index/web/Amysql/Config.php';
							$contents = file_get_contents($file);
							$root_pass = str_replace('$', '\$', $user_password1);
							$contents = preg_replace("/\\\$Config\['Password'\] = '.*';/", "\$Config['Password'] = '{$root_pass}';", $contents);
							file_put_contents($file, $contents);
						}
						$_POST = array();
					}
					else
					{
						$this -> status = 'error';
						$this -> notice .= ' MySQL用户密码修改失败：' . mysql_error();
					}
				}
				else
				{
					$this -> status = 'error';
					$this -> notice = 'MySQL用户密码修改失败：两次密码不一致。';
				}
			}
		}

		$mysql_user_list = $this -> mysqls -> get_mysql_user_list();
		$this -> mysql_user_list = $mysql_user_list;
		$this -> indexs -> log_insert($this -> top_notice . $this -> notice);
		$this -> _view('mysql_password');
	}

}

?>