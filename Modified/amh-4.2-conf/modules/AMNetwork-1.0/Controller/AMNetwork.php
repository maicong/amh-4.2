<?php
class AMNetwork extends AmysqlController
{
	public $indexs = null;
	public $notice = null;

	// Model
	function AmysqlModelBase()
	{
		if($this -> indexs) return;
		$this -> _class('Functions');
		$this -> indexs = $this ->  _model('indexs');
	}

	// 数据私有保护
	function DataPrivate()
	{
		if ($_SESSION['amh_config']['DataPrivate']['config_value'] == 'on')
		{
			$this -> status = 'error';
			$this -> notice .= "您已开启面板数据私有保护，当前AMNetwork模块不可操作。";
			Return false;
		}
		Return true;
	}

	function IndexAction()
	{
		$this -> AMNetwork_netstat();
	}

	function AMNetwork_iptables()
	{
		$this -> title = '防火墙 - AMNetwork - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		if (isset($_POST['save_iptables']) && $this -> DataPrivate())
		{
			$iptables_val = $_POST['iptables_val'];
			$iptables_val = str_replace("\r\n", "\n", $iptables_val);
			if(file_put_contents('/tmp/amh-iptables', $iptables_val))
			{
				$cmd = "amh module AMNetwork-1.0 admin save_iptables";
				exec($cmd, $tmp, $status);
				if (!$status)
				{
					$this -> status = 'success';
					$this -> notice = 'AMNetwork : 防火墙配置成功。';
				}
				else
				{
					$this -> status = 'error';
					$this -> notice = 'AMNetwork : 防火墙配置失败，请检查更改规则是否正确。';
				}
			}
		}

		$cmd = "amh module AMNetwork-1.0 admin show_iptables";
		$iptables_list = Functions::trim_result(shell_exec($cmd));
		$iptables_list = trim(str_replace(array('[OK] AMNetwork is already installed.', '[AMNetwork-1.0 admin]'), '', $iptables_list));

		$this -> iptables_list = $iptables_list;
		$this -> indexs -> log_insert($this -> notice);
		$this -> _view('AMNetwork_iptables');	
	}

	function AMNetwork_netstat()
	{
		$this -> title = '网络连接 - AMNetwork - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		$cmd = "amh module AMNetwork-1.0 admin netstat";
		$netstat_list = Functions::trim_result(shell_exec($cmd));
		$netstat_list = trim(str_replace(array('[OK] AMNetwork is already installed.', '[AMNetwork-1.0 admin]'), '', $netstat_list));

		$this -> netstat_list = $netstat_list;
		$this -> _view('AMNetwork_netstat');	
	}

	function AMNetwork_ps()
	{
		$this -> title = '系统进程 - AMNetwork - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		$cmd = "amh module AMNetwork-1.0 admin ps";
		$ps_list = Functions::trim_result(shell_exec($cmd));
		$ps_list = trim(str_replace(array('[OK] AMNetwork is already installed.', '[AMNetwork-1.0 admin]'), '', $ps_list));

		$this -> ps_list = $ps_list;
		$this -> _view('AMNetwork_ps');	
	}
}
?>
