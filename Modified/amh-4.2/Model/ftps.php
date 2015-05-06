<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object ftps FTP管理数据模型
 * Update:2013-11-01
 * 
 */

class ftps extends AmysqlModel
{
	
	// FTP列表
	function ftp_list()
	{
		$sql = "SELECT * FROM amh_ftp ORDER BY ftp_id ASC";
		Return $this -> _all($sql);
	}

	// 取得FTP
	function get_ftp($ftp_name)
	{
		$sql = "SELECT * FROM amh_ftp WHERE ftp_name = '$ftp_name'";
		Return $this -> _row($sql);
	}

	// FTP新增
	function ftp_insert($data)
	{
		$data['ftp_password'] = md5(md5($data['ftp_password']));
		$data_name = array('ftp_name', 'ftp_password', 'ftp_root', 'ftp_upload_bandwidth', 'ftp_download_bandwidth', 'ftp_upload_ratio', 'ftp_download_ratio', 'ftp_max_files', 'ftp_max_mbytes', 'ftp_max_concurrent', 'ftp_allow_time', 'ftp_directory_uname', 'ftp_uid_name');
		foreach ($data_name as $val)
			$insert_data[$val] = $data[$val];
		$insert_data['ftp_type'] = isset($data['ftp_type']) ? $data['ftp_type'] : 'web';
		Return $this -> _insert('amh_ftp', $insert_data);
	}

	// FTP新增(ssh)
	function ftp_insert_ssh()
	{
		if($_POST['ftp_root'] == 'index' || strpos($_POST['ftp_root'], '..') !== false || strpos($_POST['ftp_root'], '/') !== false ) 
			Return array(false, array('禁止使用的根目录。')); 

		$data_name = array('ftp_name', 'ftp_password', 'ftp_root', 'ftp_upload_bandwidth', 'ftp_download_bandwidth', 'ftp_upload_ratio', 'ftp_download_ratio', 'ftp_max_files', 'ftp_max_mbytes', 'ftp_max_concurrent', 'ftp_allow_time', 'ftp_uid_name');
		$_POST['ftp_root'] = '/home/wwwroot/' . $_POST['ftp_root'] . '/web';

		if (!is_dir($_POST['ftp_root']))
			Return array(false, array('根目录不存在。')); 

		$get_ftp = $this -> get_ftp($_POST['ftp_name']);
		if (isset($get_ftp['ftp_name']))
			Return array(false, array('已存在账号。')); 
			
		$cmd = 'amh ftp add';
		foreach ($data_name as $key=>$val)
			$cmd .= (isset($_POST[$val]) && !empty($_POST[$val])) ? ' ' . $_POST[$val] : ' 0 ';

		$cmd = Functions::trim_cmd($cmd);
		exec($cmd, $tmp, $status);
		Return array(!$status, $tmp);
	}

	// FTP更新列表
	function ftp_update($ftp_list_ssh)
	{
		// 取得对应权限uid用户名称与目录所属用户
		$ftp_uidname_list = shell_exec("amh ftp list");
		preg_match_all('/(.*) : (.*) : (.*) : (.*)/', $ftp_uidname_list, $ftp_uidname_arr); 
		if (is_array($ftp_uidname_arr[1]))
		{
			foreach ($ftp_uidname_arr[1] as $key=>$val)
			{
				$ftp_uidname_data[$val] = $ftp_uidname_arr[3][$key];
				$ftp_directory_uidname_data[$val] = $ftp_uidname_arr[4][$key];
			}
		}
		// *****************************
		
		$data_name = array('ftp_name', 'ftp_password', 'ftp_root', 'ftp_upload_bandwidth', 'ftp_download_bandwidth', 'ftp_upload_ratio', 'ftp_download_ratio', 'ftp_max_files', 'ftp_max_mbytes', 'ftp_max_concurrent', 'ftp_allow_time', 'ftp_directory_uname', 'ftp_uid_name');
		$all_ftp_name = array();

		foreach ($ftp_list_ssh as $key=>$val)
		{
			list($ftp_name,$ftp_password,$ftp_uid_name,$gid,$gecos,$ftp_root,$ftp_upload_bandwidth,$ftp_download_bandwidth,$ftp_upload_ratio,$ftp_download_ratio,$ftp_max_concurrent,$ftp_max_files,$ftp_max_mbytes,$authorized_local_IPs,$refused_local_IPs,$authorized_client_IPs,$refused_client_IPs,$ftp_allow_time) = explode(':', $val);

			if (!empty($ftp_name))
			{
				$all_ftp_name[] = $ftp_name;
				$ftp_root = rtrim($ftp_root , './');
				$data = array()
;
				foreach ($data_name as $key=>$val)
				{
					$data[$val] = $$val;
					if(empty($data[$val])) $data[$val] = '';
				}
				$data['ftp_uid_name'] = $ftp_uidname_data[$ftp_name]; // . ' [' . $ftp_uid_name . ']'
				$data['ftp_directory_uname'] = $ftp_directory_uidname_data[$ftp_name];
				
				$get_ftp = $this -> get_ftp($ftp_name);
				if (isset($get_ftp['ftp_name']))
				{
					unset($data['ftp_password']);
					$this -> _update('amh_ftp', $data, " WHERE ftp_name = '$ftp_name' ");
				}
				else
				{
					$data['ftp_type'] = 'ssh';
					$this -> ftp_insert($data);
				}
			}
		}

		if(count($all_ftp_name) > 0)
		{
			$sql = "DELETE FROM amh_ftp WHERE ftp_name NOT IN ('" . implode("','", $all_ftp_name) . "')";
			$this -> _query($sql);
		}
		else
		{
		    $sql = "TRUNCATE TABLE `amh_ftp`";
			$this -> _query($sql);
		}
	}

	// 编辑FTP
	function edit_ftp()
	{

		if($_POST['ftp_root'] == 'index' || strpos($_POST['ftp_root'], '..') !== false || strpos($_POST['ftp_root'], '/') !== false ) 
			Return ' 禁止使用的根目录。';

		$data_name = array('ftp_name', 'ftp_password', 'ftp_root', 'ftp_upload_bandwidth', 'ftp_download_bandwidth', 'ftp_upload_ratio', 'ftp_download_ratio', 'ftp_max_files', 'ftp_max_mbytes', 'ftp_max_concurrent', 'ftp_allow_time', 'ftp_uid_name');
		$_POST['ftp_root'] = '/home/wwwroot/' . $_POST['ftp_root'] . '/web';

		if (!is_dir($_POST['ftp_root']))
			Return ' 根目录不存在。';


		$cmd = 'amh ftp edit';
		foreach ($data_name as $key=>$val)
			$cmd .= (isset($_POST[$val]) && !empty($_POST[$val])) ? ' ' . $_POST[$val] : (isset($_POST['_'.$val]) ? ' - ' : ' 0 ');

		$cmd = Functions::trim_cmd($cmd);
		exec($cmd, $tmp, $result_change);

		if (!empty($_POST['ftp_password']))
		{
			$cmd = 'amh ftp pass ' . $_POST['ftp_name'] . ' ' . $_POST['ftp_password'];
			$cmd = Functions::trim_cmd($cmd);
			exec($cmd, $tmp, $result_pass);
			if (!$result_pass)
			{
				$data['ftp_password'] = md5(md5($_POST['ftp_password']));
				$this -> _update('amh_ftp', $data, " WHERE ftp_name = '$_POST[ftp_name]' ");
			}
		}
		Return array(!$result_change, !$result_pass);
	}


	// 删除FTP(ssh)
	function ftp_del_ssh($del_name)
	{
		$cmd = "amh ftp del $del_name";
		$cmd = Functions::trim_cmd($cmd);
		exec($cmd, $tmp, $status);
		Return array(!$status, $tmp);
	}
	
	// 重写目录权限
	function ftp_chown_ssh($chown_name)
	{
		$cmd = "amh ftp chown $chown_name y";
		$cmd = Functions::trim_cmd($cmd);
		exec($cmd, $tmp, $status);
		Return !$status;
	}

}

?>