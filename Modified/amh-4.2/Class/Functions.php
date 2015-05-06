<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object Functions 面板常用函数类
 * Update:2013-11-01
 * 
 */

class Functions
{
	// 过滤结果数据
	function trim_result($result)
	{
		$result = trim($result);
		$result = str_replace(
			array(
					'[LNMP/Nginx] Amysql Host - AMH 4.2',
					'http://Amysql.com',
					'============================================================='
			), '', $result);

		Return $result;
	}

	// 命令过滤
	function trim_cmd($cmd)
	{
		$cmd = str_replace(array(';', '&', '|', '`'), ' ', trim($cmd));
		$cmd = str_replace(array('#'), array('\\\\#'), trim($cmd));
		$cmd = preg_replace("/[ ]+/", " ", $cmd);
		Return $cmd;
	}
	
	// 分页页码
	function page ($name, $total_num, $total_page, $page, $set_url = null)
	{
		$uri = explode('?', $_SERVER['REQUEST_URI']);
		$url = _Host . $uri[0] . '?';

		if (!empty($set_url))
			$url .= $set_url;
		else
			$url .= preg_replace("/[\&]{0,}page\=[0-9]+/i", '', $uri[1]);
		
		$data = NULL;
		$url_model = '<a id="$id" href="$url&page=$page">$txt</a>';
		$replace_name = array('$url', '$page', '$name', '$txt', '$id');

		if($page-3>0)
		{
			$start=$page-3;
			if($page+3<$total_page)	
				$end=$page+3;	
			else
			{
				if($total_page-6>0)
					$start=$total_page-6;
				else
					$start=1;
				$end=$total_page;
			}
		}
		else
		{
			$start=1;
			if($total_page<7)
				$end=$total_page;
			else
				$end=7;
		}		

		if($page>1)
			$data .= str_replace($replace_name, array($url, $page-1, $name, '<', ''), $url_model);

		if($start!=1)
			$data .= str_replace($replace_name, array($url, '1', $name, '1', ''), $url_model) . ' ...';

		for($i=$start;$i<=$end;$i++)
		{
			if ($i==$page)
				$data .= '&nbsp;' . str_replace($replace_name, array($url, $i, $name, $i, 'page_now'), $url_model) ;
			else
				$data .= '&nbsp;' . str_replace($replace_name, array($url, $i, $name, $i, ''), $url_model);
		}

		if($end!=$total_page)
			$data .= ' ... ' . str_replace($replace_name, array($url, $total_page, $name, $total_page, ''), $url_model) ;
		if($total_page > $page)
			$data .= '&nbsp;' . str_replace($replace_name, array($url, $page+1, $name, '>', ''), $url_model) ;

		Return str_replace('?&', '?', $data);
	}
	
	
	// 面板检查登录
	function CheckLogin()
	{
		if (!isset($_SESSION['amh_user_name']) || empty($_SESSION['amh_user_name']))
		{
			header('location:./index.php?c=index&a=login');
			exit();
		}
		else
		{
			// CSRF防范
			if(($_SESSION['amh_config']['OpenCSRF']['config_value'] == 'on') && (!isset($_REQUEST['amh_token']) || $_REQUEST['amh_token'] != $_SESSION['amh_token']) )
			{
				$_SESSION['CSRF_Url'] = trim(_Http, '/') . $_SERVER['REQUEST_URI'];
				header('location:./index.php?c=index&a=index_csrf');
				exit();
			}
		}
	}

	// 取得模块信息&评分
	function get_module_score()
	{
		if (isset($_SESSION['module_score']))
			Return;
		$timeout = array(
			'http'=>array(
				'method'=>"GET",
				'timeout'=>8,
			)
		);

		$module_score = array();
		$context = stream_context_create($timeout);
		$_module_list = unserialize(file_get_contents('http://amysql.com/AMH.htm?module_list=y&v=' . $_SESSION['amh_version'], false, $context));
		if (is_array($_module_list))
		{
			foreach ($_module_list as $key=>$val)
				$module_score[$val['module_name']] = array('val' => number_format($val['module_stars'] / $val['module_starts_sum'], 2), 'sum' => $val['module_starts_sum']);
			unset($_module_list);
		}
		$_SESSION['module_score'] = $module_score;
	}
	
	// 取得已安装的模块
	function get_module_available()
	{
		// if (isset($_SESSION['module_available'])) Return;
		$cmd = 'amh ls_modules';
		$result = trim(shell_exec($cmd), "\n");
		
		if (empty($result)) Return array();

		$data = array();
		$run_list = explode("\n", $result);
		foreach ($run_list as $key=>$val)
		{
			// Module Status
			$cmd = "amh module $val status";
			$cmd = Functions::trim_cmd($cmd);
			exec($cmd, $tmp, $status);
			if (!$status)
			{
				// Module Info
				$cmd = "amh module $val info";
				$cmd = Functions::trim_cmd($cmd);
				$result = trim(shell_exec($cmd), "\n");
				$result = Functions::trim_result($result);
				preg_match("/AMH-ModuleAdmin:(.*)/", $result, $ModuleAdmin);
				// preg_match("/AMH-ModuleIco:(.*)/", $result, $ModuleIco);
				$ModuleID = explode('-', $val);
				$data[] = array('ModuleID' => $ModuleID[0], 'ModuleName' => $val, 'ModuleAdmin' => $ModuleAdmin[1], /*'ModuleIco' => $ModuleIco[1]*/);
			}
		}
		$_SESSION['module_available'] = $data;
	}
	
}

?>