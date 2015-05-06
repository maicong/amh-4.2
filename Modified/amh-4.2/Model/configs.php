<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object configs 面板配置数据模型
 * Update:2013-11-01
 * 
 */

class configs extends AmysqlModel
{
	// 运行的主机
	function get_amh_domain_list()
	{
		$host_list = $_SERVER['SERVER_ADDR'] . ',';
		$cmd = 'amh ls_vhost';
		$result = trim(shell_exec($cmd), "\n");
		$run_list = explode("\n", $result);
		foreach ($run_list as $key=>$val)
		{
			if(!empty($val))
			{
				$cmd = 'amh cat_vhost ' . substr($val, 0, -5);
				$cmd = Functions::trim_cmd($cmd);
				$conf = trim(shell_exec($cmd), "\n");
				preg_match('/server_name(.*); #server_name end/', $conf, $host_server_name);
				$host_list .= str_replace(' ', ',', trim($host_server_name[1])) . ',';
			}
		}
		Return array_flip(array_flip(explode(',', trim($host_list, ','))));	// 排除重复返回
	}

	// 取得系统配置
	function get_amh_config()
	{
		$sql = "SELECT * FROM amh_config";
		$result = $this -> _query($sql);
		while ($rs = mysql_fetch_assoc($result))
			$data[$rs['config_name']] = $rs;

		$cmd = "amh cat_nginx";
		$result = trim(shell_exec($cmd), "\n");
		$result = Functions::trim_result($result);
		preg_match('/listen[\s]*([0-9]+)/', $result, $listen);
		$data['AMHListen']['config_value'] = $listen[1];

		preg_match('/\$host != \'(.*)\'/', $result, $domain);
		$data['AMHDomain']['config_value'] = isset($domain[1]) ? $domain[1] : 'Off';

		Return $data;
	}

	// 更新系统配置
	function up_amh_config()
	{
		$data_name = array('HelpDoc', 'LoginErrorLimit', 'VerifyCode', 'AMHListen', 'AMHDomain', 'OpenCSRF', 'OpenMenu');
		$Affected = 0;
		foreach ($data_name as $val)
		{
			if (isset($_POST[$val]) && $_POST[$val] != $_POST[$val.'_old'])
			{
				$this -> _update('amh_config', array('config_value' => $_POST[$val]), " WHERE config_name = '$val' ");
				$Affected += $this -> Affected;

				if ($val == 'AMHListen')
				{
					$cmd = "amh SetParam amh amh_Listen $_POST[$val]";
					$cmd = Functions::trim_cmd($cmd);
					$result = trim(shell_exec($cmd), "\n");
				}

				if ($val == 'AMHDomain')
				{
					$cmd = "amh SetParam amh amh_domain $_POST[$val]";
					$cmd = Functions::trim_cmd($cmd);
					$result = trim(shell_exec($cmd), "\n");
				}
			}
		}
		Return $Affected;
	}


	// 取得升级列表
	function get_upgrade_list($get_type = 'all')
	{
		$cmd = 'amh upgrade list';
		$result = shell_exec($cmd);
		$result = Functions::trim_result($result);
		$upgrade_list = explode("\n", trim($result));

		$param_arr = array(
			'AMH-UpgradeName',
			'AMH-UpgradeDescription',
			'AMH-UpgradeGrade',
			'AMH-UpgradeDate',
			'AMH-UpgradeUrl',
			'AMH-UpgradeScriptBy',
		);
		$grade_cn = array('Low' => '较低', 'Medium' => '一般', 'High' => '重要');
		$grade_color = array('Low' => '#333300', 'Medium' => '#669900', 'High' => '#FF6600');

		$data = array();
		foreach ($upgrade_list as $key=>$val)
		{
			if(!empty($val))
			{
				// Upgrade Info
				$cmd = "amh upgrade $val info";
				$cmd = Functions::trim_cmd($cmd);
				$result = trim(shell_exec($cmd), "\n");
				$result = Functions::trim_result($result);
				foreach ($param_arr as $k=>$v)
				{
					preg_match("/{$v}:(.*)/", $result, $param_value);
					$arr[$v] = trim($param_value[1]);
				}
				$arr['AMH-UpgradeGradeCN'] = $grade_cn[$arr['AMH-UpgradeGrade']];
				$arr['AMH-UpgradeGradeColor'] = $grade_color[$arr['AMH-UpgradeGrade']];

				// Upgrade install_status
				$cmd = "amh upgrade $val install_status";
				$cmd = Functions::trim_cmd($cmd);
				exec($cmd, $tmp, $status);
				$arr['AMH-UpgradeInstallStatus'] = ($status) ? 'false' : 'true';

				// Upgrade available_status
				$cmd = "amh upgrade $val available_status";
				$cmd = Functions::trim_cmd($cmd);
				exec($cmd, $tmp, $status);
				$arr['AMH-UpgradeAvailableStatus'] = ($status) ? 'false' : 'true';

				if ($get_type == 'all' || ($get_type == 'need_install' && $arr['AMH-UpgradeInstallStatus'] == 'false'))
					$data[$arr['AMH-UpgradeDate']] = $arr;
			}
		}

		ksort($data);
		Return $data;
	}


	// 取得更新提示
	function get_upgrade_notice()
	{
		$cmd = 'amh upgrade list';
		$result = shell_exec($cmd);
		$result = Functions::trim_result($result);
		$upgrade_list = explode("\n", trim($result));

		$upgrade_sum = 0;
		foreach ($upgrade_list as $key=>$val)
		{
			if(!empty($val))
			{
				// Upgrade install_status
				$cmd = "amh upgrade $val install_status";
				$cmd = Functions::trim_cmd($cmd);
				exec($cmd, $tmp, $status);
				if($status) ++$upgrade_sum;
			}
		}

		$sql = "UPDATE amh_config SET config_value = '$upgrade_sum' WHERE config_name = 'UpgradeSum' ";
		$this -> _query($sql);

		$_SESSION['amh_config'] = $this -> get_amh_config();
		Return $upgrade_sum;
	}

}

?>