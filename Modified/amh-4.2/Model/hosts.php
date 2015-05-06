<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object hosts 虚拟主机数据模型
 * Update:2013-11-01
 * 
 */

class hosts extends AmysqlModel
{

	// 主机列表
	function host_list()
	{
		$sql = "SELECT * FROM amh_host ORDER BY host_id ASC";
		Return $this -> _all($sql);
	}

	// 取得主机
	function get_host($host_domain)
	{
		$sql = "SELECT * FROM amh_host WHERE host_domain = '$host_domain'";
		Return $this -> _row($sql);
	}

	// 主机列表更新
	function host_update()
	{
		$host_list = array();
		$cmd = 'amh ls_vhost';
		$result = trim(shell_exec($cmd), "\n");
		$run_list = explode("\n", $result);
		foreach ($run_list as $key=>$val)
		{
			if(!empty($val))
			{
				$cmd = 'amh cat_vhost ' . substr($val, 0, -5);
				$cmd = Functions::trim_cmd($cmd);
				$host_list[$val]['conf'] = trim(shell_exec($cmd), "\n");
				$host_list[$val]['host_nginx'] = 1;

				$cmd = 'amh cat_php_fpm ' . substr($val, 0, -5);
				$cmd = Functions::trim_cmd($cmd);
				$host_list[$val]['php_fpm_conf'] = trim(shell_exec($cmd), "\n");
			}
		}

		$cmd = 'amh ls_vhost_stop';
		$result = trim(shell_exec($cmd), "\n");
		$stop_list = explode("\n", $result);
		foreach ($stop_list as $key=>$val)
		{
			if(!empty($val))
			{
				$cmd = 'amh cat_vhost_stop ' . substr($val, 0, -5);
				$cmd = Functions::trim_cmd($cmd);
				$host_list[$val]['conf'] = trim(shell_exec($cmd), "\n");
				$host_list[$val]['host_nginx'] = 0;

				$cmd = 'amh cat_php_fpm ' . substr($val, 0, -5);
				$cmd = Functions::trim_cmd($cmd);
				$host_list[$val]['php_fpm_conf'] = trim(shell_exec($cmd), "\n");
			}
		}

		foreach ($host_list as $key=>$val)
		{
			$conf = $val['conf'];
			$php_fpm_conf = $val['php_fpm_conf'];

			preg_match_all('/server_name(.*); #server_name end/', $conf, $host_server_name);
			$host_list[$key]['host_server_name'] = str_replace(' ', ',', trim($host_server_name[1][0]));

			preg_match_all('/root(.*)\$subdomain;/', $conf, $host_root);
			$host_list[$key]['host_root'] = trim($host_root[1][0]);

			preg_match_all('/index(.*); #index end/', $conf, $host_index_name);
			$host_list[$key]['host_index_name'] = str_replace(' ', ',', trim($host_index_name[1][0]));

			preg_match_all('/include rewrite\/(.*); #rewrite end/', $conf, $host_rewrite);
			$host_list[$key]['host_rewrite'] = trim($host_rewrite[1][0]);

			preg_match_all('/error_page ([0-9]{3}) \/ErrorPages/', $conf, $host_error_page);
			$host_list[$key]['host_error_page'] = implode(',', $host_error_page[1]);

			preg_match_all('/access_log(.*); #access_log end/', $conf, $host_log);
			$host_list[$key]['host_log'] = strpos($host_log[1][0] , 'access.log') !== false ? 1 : 0;

			preg_match_all('/error_log(.*); #error_log end/', $conf, $host_error_log);
			$host_list[$key]['host_error_log'] = strpos($host_error_log[1][0], 'error.log') !== false ? 1 : 0;

			$host_list[$key]['host_subdirectory'] = (preg_match('/set \$subdomain "\/\$2"/', $conf)) ? 1 : 0;

			$host_list[$key]['host_pagespeed'] = (preg_match('/pagespeed on/', $conf)) ? 1 : 0;

			$php_fpm_arr = array('/pm = (.*)/', '/pm\.min_spare_servers = (.*)/', '/pm\.start_servers = (.*)/', '/pm\.max_spare_servers = (.*)/', '/pm\.max_children = (.*)/');
			$php_fpm_val = array();
			foreach ($php_fpm_arr as $val)
			{
				preg_match($val, $php_fpm_conf, $host_php_fpm);
				$php_fpm_val[] = $host_php_fpm[1];
			}
			$host_list[$key]['host_php_fpm'] = implode(',', $php_fpm_val);

			$host_list[$key]['host_domain'] = str_replace('.conf', '', $key);
			$cmd = 'amh cat_php_pid php-fpm-' . $host_list[$key]['host_domain'];
			$cmd = Functions::trim_cmd($cmd);
			$host_list[$key]['host_php'] = strlen(trim(shell_exec($cmd), "\n")) > 1 ? 1 : 0;
			unset($host_list[$key]['conf']);
			unset($host_list[$key]['php_fpm_conf']);
		}

		$all_host_name = array();
		foreach ($host_list as $key=>$val)
		{
			$get_host = $this -> get_host($val['host_domain']);
			if (isset($get_host['host_domain']))
				$this -> _update('amh_host', $val, " WHERE host_domain = '$val[host_domain]' ");
			else
			{
			    $val['host_type'] = 'ssh';
				$this -> _insert('amh_host', $val);
			}
			$all_host_name[] = $val['host_domain'];
		}

		if(count($all_host_name) > 0)
		{
			$sql = "DELETE FROM amh_host WHERE host_domain NOT IN ('" . implode("','", $all_host_name) . "')";
			$this -> _query($sql);
		}
		else
		{
		    $sql = "TRUNCATE TABLE `amh_host`";
			$this -> _query($sql);
		}
	}

	//新增主机(SSH)
	function host_insert_ssh($data)
	{
		$data['host_php_fpm'] = "$data[php_fpm_pm],$data[min_spare_servers],$data[start_servers],$data[max_spare_servers],$data[max_children]";
		$data_name = array('host_domain', 'host_server_name', 'host_index_name', 'host_rewrite', 'host_error_page', 'host_log', 'host_error_log', 'host_subdirectory', 'host_pagespeed', 'host_php_fpm');

		$cmd = 'amh host add';
		foreach ($data_name as $key=>$val)
			$cmd .= (isset($data[$val]) && !empty($data[$val])) ? ' ' . $data[$val] : ' 0 ';
		$cmd = Functions::trim_cmd($cmd);
		exec($cmd, $tmp, $status);
		Return array(!$status, $tmp);
	}

	// 新增主机
	function host_insert($data)
	{
		$data['host_php_fpm'] = "$data[php_fpm_pm],$data[min_spare_servers],$data[start_servers],$data[max_spare_servers],$data[max_children]";
		$data_name = array('host_domain', 'host_root', 'host_server_name', 'host_index_name', 'host_rewrite', 'host_error_page', 'host_log', 'host_error_log', 'host_subdirectory', 'host_pagespeed', 'host_php_fpm');
		foreach ($data_name as $val)
			$insert_data[$val] = $data[$val];
		$insert_data['host_type'] = isset($data['host_type']) ? $data['host_type'] : 'web';
		$insert_data['host_root'] = '/home/wwwroot/' . $data['host_domain'] . '/web';
		$insert_data['host_log'] = ($data['host_log']) ? '1' : '0';
		$insert_data['host_error_log'] = ($data['host_error_log']) ? '1' : '0';
		$insert_data['host_subdirectory'] = ($data['host_subdirectory']) ? '1' : '0';
		$insert_data['host_pagespeed'] = ($data['host_pagespeed']) ? '1' : '0';
		Return $this -> _insert('amh_host', $insert_data);
	}

	// 编辑主机
	function edit_host()
	{
		$data_name = array('host_domain', 'host_server_name',  'host_index_name', 'host_rewrite', 'host_error_page', 'host_log', 'host_error_log', 'host_subdirectory', 'host_pagespeed', 'host_php_fpm');
		$_POST['host_log'] = ($_POST['host_log']) ? 'on' : 'off';
		$_POST['host_error_log'] = ($_POST['host_error_log']) ? 'on' : 'off';
		$_POST['host_subdirectory'] = ($_POST['host_subdirectory']) ? 'on' : 'off';
		$_POST['host_pagespeed'] = ($_POST['host_pagespeed']) ? 'on' : 'off';
		$_POST['host_php_fpm'] = "$_POST[php_fpm_pm],$_POST[min_spare_servers],$_POST[start_servers],$_POST[max_spare_servers],$_POST[max_children]";

		$cmd = 'amh host edit';
		foreach ($data_name as $key=>$val)
			$cmd .= (isset($_POST[$val]) && !empty($_POST[$val])) ? ' ' . $_POST[$val] : ' 0 ';
		$cmd = Functions::trim_cmd($cmd);
		exec($cmd, $tmp, $status);
		Return array(!$status, $tmp);
	}

	// 删除主机(SSH)
	function host_del_ssh($host_domain)
	{
		$cmd = "amh host del $host_domain";
		$cmd = Functions::trim_cmd($cmd);
		exec($cmd, $tmp, $status);
		Return array(!$status, $tmp);
	}


	// 取得php配置参数值
	function get_php_param($param_list)
	{
		$cmd = "amh cat_php_ini";
		$cmd = Functions::trim_cmd($cmd);
		$php_ini = Functions::trim_result(shell_exec($cmd));
		foreach ($param_list as $key=>$val)
		{
			preg_match("/$val[1] = (.*)/", $php_ini, $param_val);
			$param_list[$key][3] = $param_val[1];
		}
		Return $param_list;
	}
}

?>