<?php

class amchroot extends AmysqlController
{
	public $indexs = null;
	public $amchroots = null;
	public $notice = null;

	// Model
	function AmysqlModelBase()
	{
		if($this -> indexs) return;
		$this -> _class('Functions');
		$this -> indexs = $this ->  _model('indexs');
		$this -> amchroots = $this ->  _model('amchroots');
	}


	function IndexAction()
	{
		$this -> title = 'AMH - AMChroot';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		if (isset($_GET['domain']) && !empty($_GET['domain']) && isset($_GET['mode']) && in_array($_GET['mode'], array('chroot', 'normal')))
		
		{

			$domain = $_GET['domain'];
			
			$mode = $_GET['mode'];
	
			if ($_SESSION['amh_config']['DataPrivate']['config_value'] == 'on' && $_GET['mode'] == 'normal')
			{
				$this -> status = 'error';
				$this -> notice .= "AMChroot设置：{$domain}域名设置{$mode}模式失败： 您已开启面板数据私有保护，面板不可切换主机为兼容模式。";
			}
			elseif ($this -> amchroots -> amchroot_edit($domain, $mode))
			
			{
				
				$this -> status = 'success';
				
				$this -> notice = " AMChroot设置：{$domain}域名设置{$mode}模式成功。";

			}
		
			else

			{
				
				$this -> status = 'error';
				
				$this -> notice = " AMChroot设置：{$domain}域名设置{$mode}模式失败。";
	
			}
		
		}
		$this -> indexs -> log_insert($this -> notice);
		
		$this -> amchroot_list = $this -> amchroots -> amchroot_list();
		$this -> _view('amchroot');
	} 
}

?>