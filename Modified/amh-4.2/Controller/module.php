<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object module 模块扩展控制器
 * Update:2013-11-01
 * 
 */

class module extends AmysqlController
{
	public $indexs = null;
	public $modules = null;
	public $notice = null;
	public $top_notice = null;

	// 载入数据模型(Model)
	function AmysqlModelBase()
	{
		if($this -> indexs) return;
		$this -> _class('Functions');
		$this -> indexs = $this ->  _model('indexs');
		$this -> modules = $this ->  _model('modules');
	}

	// 默认访问
	function IndexAction()
	{
		$this -> module_list();
	}

	// 模块管理
	function module_list()
	{
		$this -> title = '模块扩展 - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		// 分数提交
		if (isset($_GET['fraction']))
		{
			$timeout = array(
				'http'=>array(
					'method'=>"GET",
					'timeout'=>8,
				)
			);
			$context = stream_context_create($timeout);
			echo file_get_contents("http://amysql.com/AMH.htm?module_fraction={$_GET['fraction']}&val={$_GET['val']}", false, $context);
			exit();
		}

		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$page_sum = 6;

		if (isset($_GET['action']) && isset($_GET['name']))
		{
			$name = $_GET['name'];
			$action = $_GET['action'];
			$action_list = array('install' => '安装' , 'uninstall' => '卸载', 'delete' => '删除');

			// 安装与卸载实时进程 ************************************
			$un_install = in_array($action, array('install', 'uninstall')) ? true : false;
			if ($un_install)
			{
				set_time_limit(0);
				$actionName = isset($_GET['actionName']) ? $_GET['actionName'] : $action_list[$action];
				$this -> module_ing_name = $name;
				$this -> module_ing_actionName = $actionName;
				$this -> page = $page;
				$this -> _view('module_ing');
				$cmd = "amh module $name $action y";
				$cmd = Functions::trim_cmd($cmd);
			    $popen_handle = popen($cmd, 'r');
				$i = 0;
				echo '<div id="show_result">';
				while(!feof($popen_handle))
				{
					$line = fgets($popen_handle);
					echo $line . '<br />';
					if($i%5 == 0) echo "<script>amh_cmd_ing();</script>\n";
					++$i;
				}
				$module_ing_status = json_encode((pclose($popen_handle)));
				$result_status = (!$module_ing_status) ? true : false;
				echo "<script>amh_cmd_ing();module_ing_status = {$module_ing_status};module_end();</script>$line</div>";
			}
			// ***************************************************

			// 删除模块
			if ($action == 'delete')
			{
				$actionName = $action_list[$action];
				$result_status = $this -> modules -> module_delete($name);
			}

			if ($result_status)
			{
				$this -> status = 'success';
				$this -> notice = "$name {$actionName}成功。";
			}
			else
			{
				$this -> status = 'error';
				$this -> notice = "$name {$actionName}失败。";
			}

			$this -> indexs -> log_insert($this -> notice);
			if($un_install) exit();
		}

		
		$get_module_list_data = $this -> modules -> get_module_list_data($page, $page_sum);
		$total_page = ceil($get_module_list_data['sum'] / $page_sum);						
		$page_list = Functions::page('ModuleList', $get_module_list_data['sum'], $total_page, $page, 'c=module&a=module_list');		// 分页列表
		
		$this -> page = $page;
		$this -> total_page = $total_page;
		$this -> page_list = $page_list;
		$this -> module_list_data = $get_module_list_data;
		$this -> _view('module_list');
	}

	// 下载模块
	function module_down()
	{
		$this -> title = '下载模块 - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();


		if (isset($_GET['module_name']))
		{
			$module_name = $_GET['module_name'];
			if (!empty($module_name))
			{
				$status = $this -> modules -> module_download($module_name);
				if($status[0])
				{
					$this -> status = 'success';
					$this -> notice = "模块下载成功：$module_name";
				}
				else
				{
				    $this -> status = 'error';
					if (strpos(serialize($status[1]), 'already exist.') !== false)
						$this -> notice = "模块已存在下载失败：$module_name";
					else 
						$this -> notice = "模块下载失败：$module_name";
				}
			}
			else
			{
			    $this -> status = 'error';
				$this -> notice = "请输入模块名字。";
			}
		}

		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$page_sum = 5;


		$new_module_list = $this -> modules -> get_new_module_list($page, $page_sum);
		$total_page = ceil($new_module_list['sum'] / $page_sum);						
		$page_list = Functions::page('NewModuleList', $new_module_list['sum'], $total_page, $page, "c=module&a=module_down&search_type={$_GET['search_type']}&m_txt={$_GET['m_txt']}");		// 分页列表

		$this -> page = $page;
		$this -> total_page = $total_page;
		$this -> page_list = $page_list;
		$this -> new_module_list = $new_module_list;
		
		$this -> indexs -> log_insert($this -> notice);
		$this -> _view('module_down');
	}


}

?>