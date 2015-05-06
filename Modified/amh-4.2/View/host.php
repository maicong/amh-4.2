<?php include('header.php'); ?>
<script src="View/js/host.js"></script>
<style>
#STable th {
	padding:4px 6px;
}
#STable td {
padding: 4px 5px 3px 5px;
_padding: 2px 3px;
}
</style>

<div id="body">
<?php 
$c_name = 'host';
include('category_list.php'); 
?>

<?php 
if(is_array($host_list) && count($host_list) > 0)
	$list_show = true;
?>


<?php
	if (!empty($top_notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $top_notice . '</p></div>';
?>
<p>虚拟主机列表:</p>
<table border="0" cellspacing="1"  id="STable"  style="width:<?php echo isset($list_show) ? 'auto':'1111px';?>">
	<tr>
	<th>ID</th>
	<th>标识域名</th>
	<th>绑定域名</th>
	<th>网站根目录<br />/home/wwwroot/</th>
	<th>默认主页</th>
	<th>Rewrite<br />规则</th>
	<th>自定义<br />错误页面</th>
	<th>访问<br />日志</th>
	<th>错误<br />日志</th>
	<th>二级域名<br />绑定子目录</th>
	<th>PageSpeed<br />优化</th>
	<th>PHP-FPM<br />配置</th>
	<th>所属组</th>
	<th>添加时间</th>
	<th>运行维护</th>
	<th>操作</th>
	</tr>
	<?php 
	if(!isset($list_show))
	{
	?>
		<tr><td colspan="15" style="padding:10px;">暂无虚拟主机</td></tr>
	<?php	
	}
	else
	{
		foreach ($host_list as $key=>$val)
		{
	?>
			<tr>
			<th class="i"><?php echo $val['host_id'];?></th>
			<td><?php echo $val['host_domain'];?></td>
			<td>
			<?php  
				$server_name_arr = explode(',', $val['host_server_name']);
				foreach ($server_name_arr as $v)
				{
					if(strpos($v, '*') !== false) {
						echo $v . '<br />';
					} else {
					?>
					<a href="http://<?php echo $v;?>" target="_blank"><?php echo $v;?></a><br />
			<?php
					}
				}
			?></td>
			<td><?php echo substr($val['host_root'], 14);?></td>
			<td><?php echo str_replace(',' , '<br />', $val['host_index_name']);?></td>
			<td><?php echo empty($val['host_rewrite']) ? '无' : $val['host_rewrite'];?></td>
			<td><?php echo empty($val['host_error_page']) ? '无' : str_replace(',' , '<br />', $val['host_error_page']);?></td>
			<td><?php echo $val['host_log'] == '1' ? '开启' : '关闭';?></td>
			<td><?php echo $val['host_error_log'] == '1' ? '开启' : '关闭';?></td>
			<td><?php echo $val['host_subdirectory'] == '1' ? '开启' : '关闭';?></td>
			<td><?php echo $val['host_pagespeed'] == '1' ? '开启' : '关闭';?></td>
			<td><?php echo implode('<br />', explode(',', $val['host_php_fpm'], 2));?></td>
			<td><?php echo $val['host_type'];?></td>
			<td><?php echo date('Y-m-d\<\b\r\>H:i:s', strtotime($val['host_time']));?>&nbsp; </td>
			<td>
			<a href="index.php?c=host&a=vhost&run=<?php echo $val['host_domain'];?>&m=host&g=<?php echo $val['host_nginx'] ? 'stop' : 'start';?>" >
			<span <?php echo $val['host_nginx'] ? 'class="run_start" title="主机运行正常"' : 'class="run_stop" title="主机已停止"';?>>Host</span>
			</a>
			<a href="index.php?c=host&a=vhost&run=<?php echo $val['host_domain'];?>&m=php&g=<?php echo $val['host_php'] ? 'stop' : 'start';?>">
				<span <?php echo $val['host_php'] ? 'class="run_start" title="PHP运行正常"' : 'class="run_stop" title="PHP已停止"';?>>PHP</span>
			</a>
			<td>
			<a href="index.php?c=host&a=vhost&edit=<?php echo $val['host_domain'];?>" class="button"><span class="pen icon"></span>编辑</a>
			<a href="index.php?c=host&a=vhost&del=<?php echo $val['host_domain'];?>" class="button" onclick="return confirm('确认删除虚拟主机:<?php echo $val['host_domain'];?>?');"><span class="cross icon"></span>删除</a>
			</td>
			</tr>
	<?php
		}
	}
	?>

</table>
<br />
<br />

<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>

<p>
<?php echo isset($edit_host) ? '编辑' : '新增';?>虚拟主机: <?php echo isset($edit_host) ? $_POST['host_domain'] : '';?>
</p>
<form action="index.php?c=host&a=vhost" method="POST"  id="host_edit" />
<table border="0" cellspacing="1"  id="STable" style="width:950px;">
	<tr>
	<th> &nbsp; </th>
	<th>值</th>
	<th>说明</th>
	</tr>

	<tr><td>主标识域名</td>
	<td><input type="text" id="host_domain" name="host_domain" class="input_text <?php echo isset($edit_host) ? ' disabled' : '';?>" value="<?php echo $_POST['host_domain'];?>" <?php echo isset($edit_host) ? 'disabled=""' : '';?>/></td>
	<td><p> &nbsp; <font class="red">*</font> 用于唯一标识的主域名 </p>
	<p> &nbsp; 不需填写http:// 格式例如: amysql.com</p>
	</td>
	</tr>

	<tr><td>绑定域名</td>
	<td><input type="text" id="host_server_name" name="host_server_name" class="input_text" value="<?php echo $_POST['host_server_name'];?>" </td>
	<td><p> &nbsp; 主机绑定的域名，多项请用英文逗号分隔</p>
	<p> &nbsp; 例如: amysql.com,www.amysql.com,bbs.amysql.com  </p>
	</td>
	</tr>

	<tr><td>网站根目录</td>
	<td>/home/wwwroot/<span id="host_root" class="red">主标识域名</span>/web</td>
	<td><p> &nbsp;  网站的根目录</td>
	</tr>
	<tr><td>主机日志目录</td>
	<td>/home/wwwroot/<span id="host_log" class="red">主标识域名</span>/log</td>
	<td><p> &nbsp;  主机访问与错误日志文件目录</td>
	</tr>

	<tr><td>默认主页	</td>
	<td><input type="text" name="host_index_name" class="input_text" value="<?php echo isset($_POST['host_index_name']) ? $_POST['host_index_name'] : 'index.html,index.htm,index.php';?>" /></td>
	<td><p> &nbsp;  主机默认的主页，多项请用英文逗号分隔 </p></td>
	</tr>

	<tr><td>Rewrite规则</td>
	<td>
	<select name="host_rewrite" id="host_rewrite">
	<option value="">选择虚拟Rewrite规则</option>
	<?php
		foreach ($Rewrite as $key=>$val)
			echo '<option value="' . $val . '">' . $val . '</option>';
	?>
	</select>
	<script>
	G('host_rewrite').value = '<?php echo isset($_POST['host_rewrite']) ? $_POST['host_rewrite'] : '';?>';
	</script>
	</td>
	<td><p> &nbsp; URL重写规则</p><p> &nbsp; Rewrite存放文件夹 /usr/local/nginx/conf/rewrite</p></td>
	</tr>

	<tr><td>自定义错误页面</td>
	<td>
	<?php
		foreach ($error_page_list as $val)
		{ ?>
			<input type="checkbox" name="<?php echo $val[0];?>" id="id_<?php echo $val[0];?>" <?php echo $val[1] ? 'checked=""' : '';?> /> <label for="id_<?php echo $val[0];?>" title="<?php echo $val[2];?>"><?php echo $val[0];?></label>&nbsp;&nbsp; 
	<?php		
		}
	?>
	</td>
	<td>
	<p> &nbsp; 自定义HTTP状态码对应的错误页面</p><p> &nbsp; HTML文件存放在网站根目录ErrorPages文件夹</p>
	</td>
	</tr>

	<tr><td>主机日志开启	</td>
	<td>
	<input type="checkbox" name="host_log" id="id_host_log" <?php echo ($_POST['host_log'] == '1') ? ' checked=""' : '';?> /> 
	<label for="id_host_log">访问日志</label>
	&nbsp;&nbsp; 
	<input type="checkbox" name="host_error_log" id="id_host_error_log" <?php echo ($_POST['host_error_log'] == '1') ? ' checked=""' : '';?> /> 
	<label for="id_host_error_log">错误日志</label>
	</td>
	<td><p> &nbsp; 是否开启访问日志与错误日志</p></td>
	</tr>

	<tr><td>二级域名绑定子目录 </td>
	<td>
	<input type="checkbox" name="host_subdirectory" id="id_host_subdirectory" <?php echo ($_POST['host_subdirectory'] == '1') ? ' checked=""' : '';?> /> 
	<label for="id_host_subdirectory">开启绑定</label>
	</td>
	<td><p> &nbsp; 是否开启二级域名绑定子目录</p><p> &nbsp; 例如绑定域名:bbs.amysql.com 将自动绑定到网站根目录/bbs</p></td>
	</tr>

	<tr><td>PageSpeed 优化</td>
	<td>
	<input type="checkbox" name="host_pagespeed" id="id_host_pagespeed" <?php echo ($_POST['host_pagespeed'] == '1') ? ' checked=""' : '';?> />
	<label for="id_host_pagespeed">开启优化</label>
	</td>
	<td><p> &nbsp; 是否开启 Nginx Page Speed 优化网页代码</p></td>
	</tr>

	<tr><td>PHP-FPM设置	</td>
	<td>
	<select id="php_fpm_pm" name="php_fpm_pm" style="width:110px">
		<option value="static">静态模式</option>
		<option value="dynamic">动态模式</option>
	<select>
	<script>
	G('php_fpm_pm').value = '<?php echo isset($_POST['php_fpm_pm']) ? $_POST['php_fpm_pm'] : 'static';?>';
	</script>
	<input type="text" id="min_spare_servers" name="min_spare_servers" class="input_text" 
	value="<?php echo isset($_POST['min_spare_servers']) ? $_POST['min_spare_servers'] : '1';?>" style="width:30px" title="动态模式最小进程数量"/> <span style="font-size:13px;">≤</span>
	<input type="text" id="start_servers" name="start_servers" class="input_text" 
	value="<?php echo isset($_POST['start_servers']) ? $_POST['start_servers'] : '2';?>" style="width:30px" title="动态模式起始进程数量"/> <span style="font-size:13px;">≤</span>
	<input type="text" id="max_spare_servers" name="max_spare_servers" class="input_text" 
	value="<?php echo isset($_POST['max_spare_servers']) ? $_POST['max_spare_servers'] : '3';?>" style="width:30px" title="动态模式最大进程数量"/> <span style="font-size:13px;">≤</span>
	<input type="text" id="max_children" name="max_children" class="input_text" 
	value="<?php echo isset($_POST['max_children']) ? $_POST['max_children'] : '3';?>" style="width:30px" title="子进程数量"/> 
	</td>
	<td><p> 设置虚拟主机运行的php进程数量 (动态自动调节、静态固定)</p>
	<p> 每一进程耗用>2MB内存 可根据服务器实际负载适当调整</p>
	<p> 需按(<span style="font-size:13px;">≤</span>)条件设置各项大小，否则会影响主机php启动</p>
	</td>
	</tr>
</table>

<?php if (isset($edit_host)) { ?>
	<input type="hidden" name="save_edit" value="<?php echo $_POST['host_domain'];?>" />
<?php } else { ?>
	<input type="hidden" name="save" value="y" />
<?php }?>

<button type="submit" class="primary button" name="submit"><span class="check icon"></span>保存</button> 
</form>


<div id="notice_message">
<h3>» SSH Host</h3>
1) 有步骤提示操作: <br />
ssh执行命令: amh host <br />
然后选择对应的1~7的选项进行操作。<br />

2) 或直接操作: <br />
<ul>
<li>启动虚拟主机: amh host start [主标识域名] 缺省主标识域名即为所有</li>
<li>停止虚拟主机: amh host stop [主标识域名] 缺省主标识域名即为所有</li>
<li>虚拟主机列表: amh host list </li>
<li>新增虚拟主机: amh host add [主标识域名 amysql.com] [绑定域名 amysql.com,www.amysql.com] [默认主页 index.php,index.html] [Rewrite规则 amh] [自定义错误页面 404,502] [访问日志 on/off] [错误日志 on/off] [二级域名绑定子目录 on/off] [设置PHP-FPM static/dynamic,1,2,3,4]</li>
<br />
<li>编辑虚拟主机: amh host edit [主标识域名] [其余参数与add命令相同]</li>
<li>删除虚拟主机: amh host del [主标识域名]</li>

</ul>

3) 温馨提示:<br />
增加或编辑虚拟主机忽略参某参数请填写0，如参数有多项请使用英文逗号分隔。 <br />
例如: amh host add amysql.com amysql.com,www.amysql.com index.html,index.php 0 404,502 on off on on static,1,2,3,4<br />
以上命令为增加一虚拟主机，主标识域名为amysql.com，绑定域名amysql.com与ww.amysql.com，默认主页为index.html与index.php，开启自定义404与502页面、开启错误日志、与开启子目录绑定。并设置主机php-fpm为静态模式，子进程数为4。<br />

<h3>» SSH PHP</h3>
1) 有步骤提示操作: <br />
ssh执行命令: amh php <br />
2) 或直接操作: (缺省主标识域名即操作所有域名)<br />
<ul>
<li>启动PHP: amh php start [主标识域名]</li>
<li>停止PHP: amh php stop [主标识域名] </li>
<li>重启PHP: amh php restart [主标识域名] </li>
<li>重载PHP: amh php reload [主标识域名] </li>
</ul>
3) 面板自身PHP操作: amh php [start/stop/restart/reload] [amh-web] [y/n] <br />
面板自身PHP主标识参数为 amh-web，并需额外增加确认参数 [y/n]
</div>
</div>

<?php include('footer.php'); ?>
