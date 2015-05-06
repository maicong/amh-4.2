<?php include('header.php'); ?>
<link type="text/css" rel="stylesheet" href="View/css/info.css" />
<script src="View/js/home.js"></script>

<div id="body">
	<div id="amh_home">
		<h2>欢迎使用LNMP虚拟主机面板 - AMH</h2>

		<?php
			if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
		?>

		<h3>» Host <span>虚拟主机全局运行</span></h3>
		<a href="index.php?m=host&g=start">启动</a>
		<a href="index.php?m=host&g=stop">停止</a>

		<h3>» PHP <span>虚拟主机PHP全局运行</span></h3>
		<a href="index.php?m=php&g=start">启动</a>
		<a href="index.php?m=php&g=stop">停止</a>
		<a href="index.php?m=php&g=reload">重载</a>

		<h3>» Nginx <span>系统Nginx运行</span></h3>
		<a href="index.php?m=nginx&g=start">启动</a>
		<a href="index.php?m=nginx&g=stop" onclick="return confirm('强行停止Nginx吗? 停止后需使用SSH启动。');">停止</a>
		<a href="index.php?m=nginx&g=reload">重载</a>

		<h3>» MySQL <span>系统MySQL运行</span></h3>
		<a href="index.php?m=mysql&g=start">启动</a>
		<a href="index.php?m=mysql&g=stop" onclick="return confirm('强行停止MySQL吗? 停止后需使用SSH启动。');">停止</a>
		<a href="index.php?m=mysql&g=restart">重启</a>

		<br /><br />

		<h3>» SSH 管理命令</h3>
		<ul>
		<li>虚拟主机 : amh host</li>
		<li>PHP管理 : amh php</li>
		<li>Nginx管理 : amh nginx</li>
		<li>MySQL管理 : amh mysql</li>
		<li>FTP管理 : amh ftp</li>
		<li>数据备份 : amh backup</li>
		<li>一键还原 : amh revert</li>
		<li>参数设置 : amh SetParam</li>
		<li>模块扩展 : amh module</li>
		<li>任务计划 : amh crontab</li>
		<li>在线升级 : amh upgrade</li>
		<li>面板信息 : amh info</li>
		</ul>

		<h3>» 相关目录</h3>
		<ul>
		<li>网站目录 : /home/wwwroot</li>
		<li>Nginx目录 : /usr/local/nginx</li>
		<li>PHP目录 : /usr/local/php</li>
		<li>MySQL目录 : /usr/local/mysql</li>
		<li>MySQL数据目录 : /usr/local/mysql/data</li>
		</ul>
	</div>


	<div id="amh_info">
		<div class="item">
			<div id="info">
				<div id="ajax_info">
					<img src="View/images/loading.gif" onload="amh_info();"/> Loading...<br /><br /><br />
				</div>
				<div id="info_go" onclick="info_go(this);" title="实时查看"></div>
				<p id="phpinfo" class="ico php" style="display:none;"> <a href="/index.php?c=index&a=phpinfo">PHPINFO</a></p>
			</div>
		
			<div class="b">» AMH 官方消息</div>
			<div id="amh_news"><br /><img src="View/images/loading.gif" onload="amh_news();amh_module();"/> Loading...</div>
		
			<div class="b">» AMH 面板软件信息</div>
			<div id="amh_version">
				AMH 4.2	<br />
				AMP 1.5 <br />
				AMS 1.5.0107 <br />
				Nginx 1.4.4 <br />
				MySQL 5.5.34 <br />
				PHP 5.3.27 <br />
				PureFTPd 1.0.36 <br />
				<i>V 2013-11-01</i>
			</div>
		</div>
	</div>
	<div style="clear:both"></div>
</div>
<?php include('footer.php'); ?>
<br /><br /><br />
