<?php
class amrewrite extends AmysqlController
{
	public $indexs = null;
	public $hosts = null;
	public $notice = null;

	// Model
	function AmysqlModelBase()
	{
		if($this -> indexs) return;
		$this -> _class('Functions');
		$this -> indexs = $this ->  _model('indexs');
		$this -> hosts = $this ->  _model('hosts');
	}


	function IndexAction()
	{
		$this -> title = 'AMH - AMRewrite';	
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		if (isset($_GET['check_config']))
		{
			$amh_cmd = 'amh module AMRewrite-1.0 admin check_config';
			$result = shell_exec($amh_cmd);
			$result = Functions::trim_result($result);
			if (strpos($result , 'is successful') !== false)
			{
				$this -> status = 'success';
				$status = '[正确] Nginx配置Rewrite规则校验成功。';
			}
			else
			{
				$this -> status = 'error';
				$status = '[警告] Nginx配置Rewrite规则错误，请查检改正。';
			}
			$this -> notice = $status . "\n" . $result;
		}


		// 删除
		if (isset($_GET['del']))
		{
			$del = $_GET['del'];
			if(strpos($del, '..') !== false || strpos($del, '/') !== false || strpos($del, 'amh.conf') !== false )
			{
				$this -> status = 'error';
				$this -> notice = "{$del}: 非法请求，删除Rewrite规则失败。";
			}
			else
			{
				$del_file = "/usr/local/nginx/conf/rewrite/{$del}";
				if (is_file($del_file) && unlink($del_file))
				{
					$this -> status = 'success';
					$this -> notice = "{$del}: 删除成功，Rewrite规则删除成功。";
				}
				else
				{
				    $this -> status = 'error';
					$this -> notice = "{$del}: 删除出错，Rewrite规则删除失败。";
				}
			}
		}

		// 新增规则 ***********
		if (isset($_POST['add']))
		{
			if (!empty($_POST['rewrite_name']))
			{
				$rewrite_name = $_POST['rewrite_name'];
				$rewrite_content = stripslashes($_POST['rewrite_content']);

				if(strpos($rewrite_name, '..') !== false || strpos($rewrite_name, '/') !== false )
				{
					$this -> status = 'error';
					$this -> notice = "{$rewrite_name}: 存在非法字符，添加新Rewrite规则失败。";
				}
				else
				{
					$file = "/usr/local/nginx/conf/rewrite/{$rewrite_name}.conf";
					if (is_file($file))
					{
						$this -> status = 'error';
						$this -> notice = "{$rewrite_name}: 已存在，添加新Rewrite规则失败。";
					}
					else
					{
					    file_put_contents($file, $rewrite_content);
						if (is_file($file))
						{
							$_POST = null;
							$this -> status = 'success';
							$this -> notice = "{$rewrite_name}: 添加新Rewrite规则成功。";
						}
						else
						{
							$this -> status = 'error';
							$this -> notice = "{$rewrite_name}: 添加新Rewrite规则失败。";
						}
					}
				}
			}
			else
			{
				$this -> status = 'error';
			    $this -> notice = '添加新Rewrite规则失败，请填写规则名称。';
			}
		}

		// 查看
		if (isset($_GET['name']))
		{
			$name = $_GET['name'];
			$file = "/usr/local/nginx/conf/rewrite/{$name}.conf";
			if (is_file($file))
			{
				$_POST['rewrite_content'] = file_get_contents($file);
			}
		}

		// 保存
		if (isset($_POST['save']))
		{
			if (!empty($_POST['rewrite_name']))
			{
				$rewrite_name = $_POST['rewrite_name'];
				$rewrite_content = stripslashes($_POST['rewrite_content']);

				if(strpos($rewrite_name, '..') !== false || strpos($rewrite_name, '/') !== false )
				{
					$this -> status = 'error';
					$this -> notice = "{$rewrite_name}: 存在非法字符，保存Rewrite规则失败。";
				}
				else
				{
					$file = "/usr/local/nginx/conf/rewrite/{$rewrite_name}.conf";
					if (is_file($file))
					{
						file_put_contents($file, $rewrite_content);
						if (file_get_contents($file) == $rewrite_content)
						{
							$_POST = null;
							$_POST['reload_nginx'] = true;
							$this -> status = 'success';
							$this -> notice = "{$rewrite_name}: 保存Rewrite规则成功。";
						}
						else
						{
							$this -> status = 'error';
							$this -> notice = "{$rewrite_name}: 保存Rewrite规则失败。";
						}

						
					}
					else
					{
					    $this -> status = 'error';
						$this -> notice = "{$rewrite_name}: 不存在，保存Rewrite规则失败。";
					}
				}
			}
			else
			{
				$this -> status = 'error';
			    $this -> notice = '保存Rewrite规则失败，错误的规则名称。';
			}
		}


		// 列表 ***********
		$rewrite_file = scandir("/usr/local/nginx/conf/rewrite");
		$host_list = $this -> hosts -> host_list();

		foreach ($rewrite_file as $key=>$val)
		{
			if (!in_array($val, array('.', '..')))
				$rewrite_list[$val] = array();
		}

		foreach ($host_list as $key=>$val)
		{
			if (isset($rewrite_list[$val['host_rewrite']]))
				$rewrite_list[$val['host_rewrite']][] = $val['host_domain'];
		}

		$this -> indexs -> log_insert($this -> notice);
		$this -> rewrite_list = $rewrite_list;
		$this -> _view('amrewrite');	
	}
}
?>
