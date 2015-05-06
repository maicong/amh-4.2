<?php

class amchroots extends AmysqlModel
{
	// 主机列表
	function amchroot_list()
	{
		$data = array();
		$cmd = 'amh module AMChroot-1.1 admin list';
		$cmd = Functions::trim_cmd($cmd);
		$result = trim(shell_exec($cmd), "\n");
		preg_match_all("/(.*\[(?:Chroot|Normal)\])/", $result, $amchroot_list);
		if (is_array($amchroot_list[1]))
		{
			foreach ($amchroot_list[1] as $key=>$val)
			{
				$rs = explode(' ', $val);
				$data[] = array($rs[0], (strpos($rs[1], 'Running') !== false), (strpos($rs[1], 'Chroot') !== false));
			}
		}
		Return $data;
	}

	// 编辑主机运行环境
	function amchroot_edit($domain, $mode)
	{
		$cmd = "amh module AMChroot-1.1 admin edit,$domain,$mode";
		$cmd = Functions::trim_cmd($cmd);
		exec($cmd, $tmp, $status);
		Return !$status;
	}
}

?>