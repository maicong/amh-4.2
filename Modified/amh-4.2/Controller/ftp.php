<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object ftp FTP管理控制器
 * Update:2013-11-01
 * 
 */

class ftp extends AmysqlController
{
	public $indexs = null;
	public $ftps = null;
	public $notice = null;
	public $top_notice = null;

	// 载入数据模型(Model)
	function AmysqlModelBase()
	{
		if($this -> indexs) return;
		$this -> _class('Functions');
		$this -> indexs = $this ->  _model('indexs');
		$this -> ftps = $this ->  _model('ftps');
	}

	// 默认访问
	function IndexAction()
	{
		$this -> ftp_list();
	}

	// FTP账号列表
	function ftp_list()
	{
		$this -> title = 'FTP - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		$this -> status = 'error';

		// 删除ftp
		if (isset($_GET['del']))
		{
			$del_name = $_GET['del'];
			if(!empty($del_name))
			{
				$get_ftp = $this -> ftps -> get_ftp($del_name);
				if($get_ftp['ftp_type'] == 'web')
				{
					$result = $this -> ftps -> ftp_del_ssh($del_name);
					if ($result[0])
					{
						$this -> status = 'success';
						$this -> top_notice = $del_name . ' : 删除FTP账号成功。';
					}
					else
						$this -> top_notice = $del_name . ' : 删除FTP账号失败。' . implode(',', $result[1]);
				}
				else
				    $this -> top_notice = $del_name . ' : ssh FTP账号web端不可删除。';
			}
		}
		
		// 重写目录权限
		if (isset($_GET['chown']))
		{
			$chown_name = $_GET['chown'];
			$uidname = $_GET['uidname'];
			if(!empty($chown_name))
			{
				$get_ftp = $this -> ftps -> get_ftp($chown_name);
				if($get_ftp['ftp_type'] == 'web')
				{
					if ($this -> ftps -> ftp_chown_ssh($chown_name))
					{
						$this -> status = 'success';
						$this -> top_notice = $chown_name . ' : 重写目录权限' . $uidname . '成功。';
					}
					else
						$this -> top_notice = $chown_name . ' : 重写目录权限' . $uidname . '失败。';
				}
				else
				    $this -> top_notice = $chown_name . ' : ssh FTP账号web端不可重写目录权限。';
			}
		}

		// 保存ftp
		if (isset($_POST['save']))
		{
			if (empty($_POST['ftp_name']) || empty($_POST['ftp_password']) || empty($_POST['ftp_root']) || empty($_POST['ftp_uid_name']))
				$this -> notice = '账号密码与根目录与权限用户不能为空。';
			else
			{
				$_POST['ftp_name'] = substr($_POST['ftp_name'], 0, 20);
				$result = $this -> ftps -> ftp_insert_ssh($_POST);
				if ($result[0])
				{
					$this -> ftps -> ftp_insert($_POST);
					$this -> status = 'success';
					$this -> notice = $_POST['ftp_name'] . ' : 新增FTP账号成功。';
					$_POST = array();
				}
				else
					$this -> notice = $_POST['ftp_name'] . ' : 新增FTP账号失败。' . implode(',', $result[1]);
			}
		}

		// 编辑ftp
		if (isset($_GET['edit']))
		{
			$edit_name = $_GET['edit'];
			$_POST = $this -> ftps -> get_ftp($edit_name);
			if($_POST['ftp_type'] == 'web')
			{
				$_POST['ftp_password'] = '';
				if(!empty($_POST['ftp_upload_bandwidth'])) $_POST['ftp_upload_bandwidth'] /= 1024;
				if(!empty($_POST['ftp_download_bandwidth'])) $_POST['ftp_download_bandwidth'] /= 1024;
				if(!empty($_POST['ftp_max_mbytes'])) $_POST['ftp_max_mbytes'] /= 1024*1024;
				$this -> edit_ftp = true;
			}
			else
			{
			     $this -> top_notice = $edit_name . ' : ssh FTP账号web端不可编辑。';
				 $_POST = array();
			}
		}
	
		// 保存编辑ftp
		if (isset($_POST['save_edit']))
		{
			$_POST['ftp_name'] = $ftp_name = $_POST['save_edit'];
			$edit_ftp = $this -> ftps -> get_ftp($ftp_name);
			if($edit_ftp['ftp_type'] == 'web')
			{
				$this -> status = 'success';
				$result = $this -> ftps -> edit_ftp();
				if ($result[0])
				{
					$status = true;
					$top_notice = $ftp_name . ' : 编辑FTP账号成功。';
				}
				else
				{
					$this -> status = 'error';
					$top_notice = $ftp_name . ' : 编辑FTP账号失败。' . $result[0];
				}
				
				if (!empty($_POST['ftp_password']))
				{
					if ($result[1])
					{
						$status = true;
						$top_notice .= $ftp_name . ' : 更改FTP密码成功。';
					}
					else
					{
						$this -> status = 'error';
						$top_notice .= $ftp_name . ' 更改FTP密码失败。' . $result[1];
					}
				}
				if(isset($status)) 
					$_POST = array();
				else 
					$this -> edit_ftp = true;
				$this -> top_notice = $top_notice;
			}
		}

		$ftp_list_ssh = explode("\n", trim(shell_exec("amh ls_ftp"), "\n"));
		$this -> ftps -> ftp_update($ftp_list_ssh);
		$this -> ftp_list = $this -> ftps -> ftp_list();

		$dir_str = trim(shell_exec("amh ls_wwwroot"), "\n");
		$this -> dirs = explode("\n", $dir_str);

		$_POST['ftp_root'] = explode('/', $_POST['ftp_root']);
		$_POST['ftp_root'] = $_POST['ftp_root'][3];

		
		$this -> indexs -> log_insert($this -> top_notice . $this -> notice);
		$this -> _view('ftp');
	} 

}

?>