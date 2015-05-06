<?php
/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object backups 面板备份数据模型
 * Update:2013-11-01
 * 
 */

class backups extends AmysqlModel
{

	// 取得列表
	function get_backup_list($page = 1, $page_sum = 20)
	{
		$sql = "SELECT * FROM amh_backup_list";
		$sum = $this -> _sum($sql);

		$limit = ' LIMIT ' . ($page-1)*$page_sum . ' , ' . $page_sum;
		$sql = "SELECT * FROM amh_backup_list ORDER BY backup_id ASC $limit";
		Return array('data' => $this -> _all($sql), 'sum' => $sum);
	}
	// 取得指定备份
	function get_backup($id = null, $backup_file = null)
	{
		$where = '';
		$where .= (!empty($id)) ? " AND backup_id = '$id' " : '';
		$where .= (!empty($backup_file)) ? " AND backup_file = '$backup_file' " : '';
		$sql = "SELECT * FROM amh_backup_list WHERE 1 $where ";
		Return $this -> _row($sql);
	}
	// 更新列表
	function backup_list_update()
	{
		$cmd = 'amh ls_backup';
		$result = trim(shell_exec($cmd), "\n");
		$backup_list = explode("\n", $result);

		foreach ($backup_list as $key=>$val)
		{
			$val_arr = explode(' ', preg_replace("/[ ]+/", " ",$val));
			if(substr($val_arr[count($val_arr)-1], -3, 3) == 'amh')
			{
				$backup_file = $val_arr[8];
				$backup_size = number_format($val_arr[4]/1024/1024, 2);
				$backup_password = (strpos($backup_file, 'tar.gz') !== false) ? '0' : '1';
				$backup_file_arr = explode('.', $backup_file);
				$backup_file_arr = explode('-', $backup_file_arr[0]);
				$backup_time = date('Y-m-d H:i:s', strtotime("{$backup_file_arr[1]}{$backup_file_arr[2]}"));
				$all_backup_file[] = $backup_file;
				
				$backup_info = $this -> get_backup(null, $backup_file);
				if(isset($backup_info['backup_id']))
				{
					$this -> _update('amh_backup_list', array('backup_size' => $backup_size, 'backup_password' => $backup_password, 'backup_time' => $backup_time), " WHERE backup_file = '$backup_file' ");
				}
				else
				{
					$this -> _insert('amh_backup_list', array('backup_file' => $backup_file, 'backup_size' => $backup_size, 'backup_password' => $backup_password, 'backup_time' => $backup_time));
				}
			}
		}

		if(count($all_backup_file) > 0)
		{
			$sql = "DELETE FROM amh_backup_list WHERE backup_file NOT IN ('" . implode("','", $all_backup_file) . "')";
			$this -> _query($sql);
		}
		else
		{
		    $sql = "TRUNCATE TABLE `amh_backup_list`";
			$this -> _query($sql);
		}

	}
	// 远程配置列表
	function backup_remote_list()
	{
		$sql = "SELECT * FROM amh_backup_remote ORDER BY remote_id ASC ";
		Return $this -> _all($sql);	
	}

	// 保存远程配置
	function backup_remote_insert()
	{
		$data_name = array('remote_type', 'remote_status', 'remote_ip', 'remote_path', 'remote_user', 'remote_pass_type', 'remote_password', 'remote_comment');
		foreach ($data_name as $val)
			$insert_data[$val] = $_POST[$val];
		Return $this -> _insert('amh_backup_remote', $insert_data);
	}

	// 编辑保存远程配置
	function backup_remote_update()
	{
		$data_name = array('remote_type', 'remote_status', 'remote_ip', 'remote_path', 'remote_user', 'remote_pass_type', 'remote_password', 'remote_comment');
		foreach ($data_name as $val)
		{
			if($val != 'remote_password' || !empty($_POST['remote_password']))
				$insert_data[$val] = $_POST[$val];
		}
		Return $this -> _update('amh_backup_remote', $insert_data,  " WHERE remote_id = '$_POST[remote_id]' ");
	}
	
	// 取得远程配置
	function get_backup_remote($remote_id)
	{
		$sql = "SELECT * FROM amh_backup_remote WHERE remote_id = '$remote_id'";
		Return $this -> _row($sql);
	}

	// 删除远程配置
	function backup_remote_del($remote_id)
	{
		$sql = "DELETE FROM amh_backup_remote WHERE remote_id = '$remote_id'";
		$this -> _query($sql);
		Return $this -> Affected;
	}

}

?>