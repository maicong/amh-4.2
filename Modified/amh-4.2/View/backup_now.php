<?php include('header.php'); ?>

<style>
#STable td.td_block {
	padding:10px 20px;
	text-align:left;
	line-height:23px;
}
td i {
	font-style: normal;
	color: rgb(152, 156, 158);
}
</style>
<div id="body">
<?php 
$c_name = 'backup';
include('category_list.php'); 
?>

<script>
var submit_backup = function (type)
{
	if(type == 'button' )
		G('backup_now_form').submit();
	G('submit_backup_button').innerHTML = '备份提交中…';
	G('submit_backup_button').disabled = true;
}
</script>

<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>
<p>即时备份:</p>
<table border="0" cellspacing="1"  id="STable" style="width:800px;">
	<tr>
	<th>立刻创建数据备份</th>
	</tr>
	<tr>
	<td class="td_block">
	<form action="index.php?c=backup&a=backup_now" method="POST"  id="backup_now_form" onsubmit="return submit_backup('submit')" />
	本地或远程备份选择  <?php echo $_SESSION['amh_config']['DataPrivate']['config_value'] == 'on' ? '(当前已开启面板数据私有保护 / 面板远程备份功能已关闭)' : '' ?><br />
	<select id="backup_retemo" name="backup_retemo">
	<option value="n">只备份到本地</option>
	<option value="Y">只备份到远程</option>
	<option value="y">同时备份到本地与远程</option>
	</select><br />
	<script> 
	G('backup_retemo').value = '<?php echo isset($_POST['backup_retemo']) ? $_POST['backup_retemo'] : 'n';?>';
	</script>
	备份选项<br />
	<select id="backup_options" name="backup_options">
	<option value="y">面板数据全面备份</option>
	<option value="N">不备份数据库数据 (MySQL)</option>
	<option value="n">不备份网站数据文件 (wwwroot)</option>
	</select>
	<script>
	G('backup_options').value = '<?php echo isset($_POST['backup_options']) ? $_POST['backup_options'] : 'y';?>';
	</script>
	<br /><br />
	备份文件加密设置密码<br />
	<input type="password" class="input_text" name="backup_password" value="<?php echo isset($_POST['backup_password']) ? $_POST['backup_password'] : '';?>" /> (留空即不设置密码) <br />
	确认密码<br />
	<input type="password" class="input_text" name="backup_password2" value="<?php echo isset($_POST['backup_password2']) ? $_POST['backup_password2'] : '';?>" /> <br />

	添加备注<br />
	<input type="text" class="input_text" name="backup_comment" value="<?php echo isset($_POST['backup_comment']) ? $_POST['backup_comment'] : '';?>" /> (备注可不填写) <br /> 
	<button type="button" class="primary button" onclick="submit_backup('button');" id="submit_backup_button" ><span class="check icon"></span>备份</button> 
	<input type="hidden" name="backup_now" value="y"/>
	</form>
	</td>
	</tr>
</table>
<br />

<div id="notice_message" style="width:660px;">
<h3>» 即时备份</h3>
1) 如使用远程备份数据同时会传输至远程设置的FTP/SSH服务器(需设置开启状态)。<br />
2) 建议设置密码备份数据，同时密码不可找回，请牢记备份密码。<br />
3) 面板配置如果开启面板数据私有保护，面板远程备份功能将自动关闭。<br />

<h3>» SSH 即时备份</h3>
<ul>
<li>查看备份文件: amh ls_backup </li>
<li>备份命令: amh backup [n/Y/y 远程备份] [y/N/n 备份选项] [密码/n] [备注]</li>
<li>远程备份参数说明：n 只备份到本地、Y 只备份到远程、y 同时备份到本地与远程
<li>备份选项参数说明：y 面板数据全面备份、N 不备份数据库数据 (MySQL)、n 不备份网站数据文件 (wwwroot)
</ul>
使用示例:<br />
本地备份: amh backup<br />
本地与远程备份: amh backup y<br />
只远程备份与不备份网站文件: amh backup Y n<br />
本地与远程全面数据备份并设置密码: amh backup y y amh_password<br />
本地备份与添加备注: amh backup n y n 2012backup<br />
</div>

</div>
<?php include('footer.php'); ?>