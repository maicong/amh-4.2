<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object modules 模块扩展数据模型
 * Update:2013-11-01
 * 
 */

class modules extends AmysqlModel
{
	
	// 取得模块列表数据
	function get_module_list_data($page = 1, $page_sum = 5)
	{
		$cmd = 'amh ls_modules';
		$result = trim(shell_exec($cmd), "\n");
		
		if (empty($result))
			Return array('data' => array(), 'sum' => 0);

		$run_list = explode("\n", $result);
		$sum = count($run_list);
		$run_list = array_slice($run_list, ($page-1)*$page_sum,  $page_sum);

		$param_arr = array(
			'AMH-ModuleName',
			'AMH-ModuleIco',
			'AMH-ModuleDescription',
			'AMH-ModuleButton',
			'AMH-ModuleDate',
			'AMH-ModuleAdmin',
			'AMH-ModuleWebSite',
			'AMH-ModuleScriptBy',
		);

		$module_data = array();
		if (is_array($run_list))
		{
			Functions::get_module_score();
			foreach ($run_list as $key=>$val)
			{
				// Module Info
				$cmd = "amh module $val info";
				$cmd = Functions::trim_cmd($cmd);
				$result = trim(shell_exec($cmd), "\n");
				$result = Functions::trim_result($result);
				foreach ($param_arr as $k=>$v)
				{
					preg_match("/{$v}:(.*)/", $result, $param_value);
					$arr[$v] = trim($param_value[1]);
				}
				
				// Module Status
				$cmd = "amh module $val status";
				$cmd = Functions::trim_cmd($cmd);
				exec($cmd, $tmp, $status);
				$arr['AMH-ModuleStatus'] = ($status) ? 'false' : 'true';
				
				$arr['AMH-ModuleName'] = addslashes($arr['AMH-ModuleName']);
				$arr['AMH-ModuleButton'] = explode('/', $arr['AMH-ModuleButton']);
				if ($arr['AMH-ModuleStatus'] == 'true')
				{
					$arr['AMH-ModuleButton'] = $arr['AMH-ModuleButton'][1];
					$arr['AMH-ModuleAction'] = 'uninstall';
				}
				else
				{
					$arr['AMH-ModuleButton'] = $arr['AMH-ModuleButton'][0];
					$arr['AMH-ModuleAction'] = 'install';
				}
				$arr['AMH-ModuleScore'] = isset($_SESSION['module_score'][$arr['AMH-ModuleName']]) ? $_SESSION['module_score'][$arr['AMH-ModuleName']] : '';
				$arr['AMH-ModuleIco'] = (isset($_SESSION['module_score']) && count($_SESSION['module_score']) > 0) ? $arr['AMH-ModuleIco'] : '';
				$i = strtotime($arr['AMH-ModuleDate']) + $key;
				$data[$i] = $arr;
			}

			if (is_array($data))
			{
				krsort($data);
				$i = 0;
				foreach ($data as $key=>$val)
				{
					$module_data[($i++)%3][] = $val;
				}
				unset($data);
			}
		}
		Return array('data' => $module_data, 'sum' => $sum);
	}

	// 下载模块
	function module_download($name)
	{
		$cmd = "amh module download $name";
		$cmd = Functions::trim_cmd($cmd);
		exec($cmd, $tmp, $status);
		Return array(!$status, $tmp);
	}

	// 删除模块
	function module_delete($name)
	{
		$cmd = "amh module $name delete y";
		$cmd = Functions::trim_cmd($cmd);
		exec($cmd, $tmp, $status);
		Return !$status;
	}

	// 取得最新模块
	function get_new_module_list($page = 1, $page_sum = 5)
	{
		$cmd = 'amh ls_modules';
		$local_module_list = explode("\n", trim(shell_exec($cmd), "\n"));

		$timeout = array(
			'http'=>array(
				'method'=>"GET",
				'timeout'=>8,
			)
		);
		$context = stream_context_create($timeout);
		$data = unserialize(file_get_contents('http://amysql.com/AMH.htm?module_list=y&v=' . $_SESSION['amh_version'], false, $context));
		if (empty($data) || !is_array($data) || count($data) == 0)
			Return array('data' => array(), 'sum' => 0);

		// 搜索模块
		if (isset($_GET['m_txt']) && !empty($_GET['m_txt']))
		{
			foreach ($data as $key=>$val)
			{
				$search_txt = ($_GET['search_type'] == 'search_module_name') ? $val['module_name'] . $val['module_description'] : $val['module_by'];
				if (stripos($search_txt, $_GET['m_txt']) === false)
					unset($data[$key]);
			}
		}
		
		$sum = count($data);
		$data = array_slice($data, ($page-1)*$page_sum,  $page_sum);
		foreach ($data as $key=>$val)
			$data[$key]['module_download'] = (in_array($val['module_name'], $local_module_list)) ? 'y' : 'n';
		Return array('data' => $data, 'sum' => $sum);
	}

}

?>