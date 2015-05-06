<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object backup 面板备份控制器
 * Update:2013-09-05
 * 
 */

class backup extends AmysqlController
{
	public $indexs = null;
	public $backups = null;
	public $notice = null;
	public $top_notice = null;

	// 载入数据模型(Model)
	function AmysqlModelBase()
	{
		if($this -> indexs) return;
		$this -> _class('Functions');
		$this -> indexs = $this ->  _model('indexs');
		$this -> backups = $this ->  _model('backups');
	}

	// 默认访问
	function IndexAction()
	{
		$this -> backup_list();
	}
	
	// 数据备份列表
	function backup_list()
	{
		$this -> title = '备份列表 - 备份 - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		if(isset($_GET['category']) && $_GET['category'] == 'backup_remote')
		{
			$_GET['a'] = 'backup_remote';
			$this -> backup_remote();
			exit();
		}

		$this -> status = 'error';
		if (isset($_GET['del']))
		{
			$del_id = (int)$_GET['del'];
			$del_info = $this -> backups -> get_backup($del_id);
			if (isset($del_info['backup_file']))
			{
				$file = str_replace('.amh', '', $del_info['backup_file']);
				$cmd = "amh rm_backup $file";
				$cmd = Functions::trim_cmd($cmd);
				$result = shell_exec($cmd);
				$this -> status = 'success';
				$this -> notice = "删除备份文件({$file}.amh)执行完成。";
			}
			
		}

		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$page_sum = 20;

		$this -> backups -> backup_list_update();
		$backup_list = $this -> backups -> get_backup_list($page, $page_sum);

		$total_page = ceil($backup_list['sum'] / $page_sum);						
		$page_list = Functions::page('BackupList', $backup_list['sum'], $total_page, $page, 'c=backup&a=backup_list&category=backup_list');		// 分页列表

		$this -> page = $page;
		$this -> total_page = $total_page;
		$this -> backup_list = $backup_list;
		$this -> page_list = $page_list;
		
		$this -> indexs -> log_insert($this -> notice);
		$this -> _view('backup_list');
	}

	
	// 远程设置
	function backup_remote()
	{
		$this -> title = '远程设置 - 备份 - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();
		$this -> status = 'error';
		$input_item = array('remote_type', 'remote_status', 'remote_ip', 'remote_path', 'remote_user', 'remote_password');

		// 连接测试
		if (isset($_GET['check']))
		{
			$id = (int)$_GET['check'];
			$data = $this -> backups -> get_backup_remote($id);
			if($data['remote_type'] == 'FTP')
				$cmd = "amh BRftp check $id";
			if($data['remote_type'] == 'SSH')
				$cmd = "amh BRssh check $id";
			if ($cmd)
			{
				$cmd = Functions::trim_cmd($cmd);
				$result = shell_exec($cmd);
				$result = trim(Functions::trim_result($result), "\n ");
				echo $result;
			}
			exit();
		}
		// 保存远程配置
		if (isset($_POST['save']))
		{
			$save = true;
			foreach ($input_item as $val)
			{
				if(empty($_POST[$val]))
				{
					$this -> notice = '新增远程备份配置失败，请填写完整数据，*号为必填项。';
					$save = false;
				}
			}
			if($save)
			{
				$id = $this -> backups -> backup_remote_insert();
				if ($id)
				{
					$this -> status = 'success';
					$this -> notice = 'ID:' . $id . ' 新增远程备份配置成功。';
					$_POST = array();
				}
				else
					$this -> notice = ' 新增远程备份配置失败。';
			}
		}

		// 删除远程配置
		if (isset($_GET['del']))
		{
			$id = (int)$_GET['del'];
			if(!empty($id))
			{
				$result = $this -> backups -> backup_remote_del($id);
				if ($result)
				{
					$this -> status = 'success';
					$this -> top_notice = 'ID:' . $id . ' 删除远程备份配置成功。';
				}
				else
					$this -> top_notice = 'ID:' . $id . ' 删除远程备份配置失败。';
			}
		}

		// 编辑远程配置
		if (isset($_GET['edit']))
		{
			$id = (int)$_GET['edit'];
			$_POST = $this -> backups -> get_backup_remote($id);
			if($_POST['remote_id'])
			{
				$this -> edit_remote = true;
			}
		}

		// 保存编辑远程配置
		if (isset($_POST['save_edit']))
		{
			$id = $_POST['remote_id'] = (int)$_POST['save_edit'];
			$save = true;
			foreach ($input_item as $val)
			{
				if(empty($_POST[$val]) && $val != 'remote_password')
				{
					$this -> notice = 'ID:' . $id . ' 编辑远程备份配置失败。*号为必填项。';
					$save = false;
					$this -> edit_remote = true;
				}
			}
			if ($save)
			{
				$result = $this -> backups -> backup_remote_update();
				if ($result)
				{
					$this -> status = 'success';
					$this -> notice = 'ID:' . $id . ' 编辑远程备份配置成功。';
					$_POST = array();
				}
				else
				{
					$this -> notice = 'ID:' . $id . ' 编辑远程备份配置失败。';
					$this -> edit_remote = true;
				}
			}
			
		}

		$this -> remote_list = $this -> backups -> backup_remote_list();
		$this -> indexs -> log_insert($this -> notice);
		$this -> _view('backup_remote');
	}

	// 即时备份
	function backup_now()
	{
		$this -> title = '即时备份 - 备份 - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();
		$this -> status = 'error';

		if (isset($_POST['backup_now']))
		{
			$backup_retemo = ($_SESSION['amh_config']['DataPrivate']['config_value'] != 'on' && !empty($_POST['backup_retemo'])) ? $_POST['backup_retemo'] : 'n';
			$backup_options = (!empty($_POST['backup_options'])) ? $_POST['backup_options'] : 'y';
			$backup_password = (!empty($_POST['backup_password'])) ? $_POST['backup_password'] : 'n';
			$backup_comment = (!empty($_POST['backup_comment'])) ? $_POST['backup_comment'] : '';

			if ((!empty($_POST['backup_password2']) || !empty($_POST['backup_password'])) && $_POST['backup_password'] != $_POST['backup_password2'])
			{
				$this -> notice = ' 两次密码不一致，请确认。' ;
			}
			else
			{
				set_time_limit(0);
				$this -> category = $category;
				$this -> _view('backup_now_ing');
				$cmd = "amh backup $backup_retemo $backup_options $backup_password $backup_comment";
				$cmd = Functions::trim_cmd($cmd);
				$popen_handle = popen($cmd, 'r');
				$i = 0;
				$_i = 50;
				echo '<div id="show_result">';
				while(!feof($popen_handle))
				{
					$line = fgets($popen_handle);
					echo $line . '<br />';
					if($i%200 == 0) ++$_i;
					if($i%$_i == 0) echo "<script>amh_cmd_ing();</script>\n";
					++$i;
					if(!empty($line)) $result = $line;
				}
				$backup_ing_status = json_encode((pclose($popen_handle)));
				$result_status = (!$backup_ing_status) ? true : false;
				if ($result_status)
				{
					$this -> status = 'success';
					$this -> notice = $result . ' 已成功创建备份文件。';
					$_POST = array();
				}
				else
				{
					$this -> status = 'error';
					$this -> notice = $result . ' 备份文件创建失败。' ;
				}
				$notice = json_encode($this -> notice);
				echo "<script>amh_cmd_ing();backup_ing_status = {$backup_ing_status}; backup_result = {$notice}; backup_end();</script>$line</div>";
				$this -> indexs -> log_insert($this -> notice);
				exit();
			}
		}

		$this -> indexs -> log_insert($this -> notice);
		$this -> _view('backup_now');
	}

	
	// 一键还原
	function backup_revert()
	{
		$this -> title = '一键还原 - 备份 - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();
		$this -> status = 'error';

		$revert_id = isset($_GET['revert_id']) ? (int)$_GET['revert_id'] : '';
		if (!empty($revert_id))
			$revert = $this -> backups -> get_backup($revert_id);

		$this -> revert = $revert;
		if (isset($_POST['revert_submit']))
		{
			set_time_limit(0);
			$backup_file = $revert['backup_file'];
			$backup_password = empty($_POST['backup_password']) ? 'n' : $_POST['backup_password'];
			$this -> category = $category;
			$this -> _view('backup_revert_ing');
			$cmd = "amh revert $backup_file $backup_password noreload";
			$cmd = Functions::trim_cmd($cmd);
			$popen_handle = popen($cmd, 'r');
			$i = 0;
			$_i = 50;
			echo '<div id="show_result">';
			while(!feof($popen_handle))
			{
				$line = fgets($popen_handle);
				echo $line . '<br />';
				if($i%200 == 0) ++$_i;
				if($i%$_i == 0) echo "<script>amh_cmd_ing();</script>\n";
				++$i;
				if(!empty($line)) $result = $line;
			}
			$revert_ing_status = json_encode((pclose($popen_handle)));
			$result_status = (!$revert_ing_status) ? true : false;
			if ($result_status)
			{
				$this -> status = 'success';
				$this -> notice = $backup_file . ' 数据一键还原成功。';
				$_POST = array();
			}
			else
			{
				$this -> status = 'error';
				$this -> notice = $result . $backup_file . ' 一键还原失败。' ;
			}
			$notice = json_encode($this -> notice);
			echo "<script>amh_cmd_ing();revert_ing_status = {$revert_ing_status}; revert_result = {$notice}; revert_end();</script>$line</div>";
			$this -> indexs -> log_insert($this -> notice);
			exit();
		}

		$this -> indexs -> log_insert($this -> notice);
		$this -> _view('backup_revert');
	}
		
}

?>