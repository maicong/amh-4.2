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

<script src="View/js/backup_remote.js"></script>
<?php
	if (!empty($top_notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $top_notice . '</p></div>';
?>

<p>远程备份设置:</p>
<table border="0" cellspacing="1"  id="STable" style="width:1080px;">
	<tr>
	<th>&nbsp;ID&nbsp;</th>
	<th>类型</th>
	<th>状态</th>
	<th>远程IP域名 / 目地</th>
	<th>保存路径 / 位置</th>
	<th>账号 / ID</th>
	<th>账号验证</th>
	<th>密码 / 密匙</th>
	<th>说明备注</th>
	<th>添加时间</th>
	<th>操作</th>
	</tr>
	<?php 
	if(!is_array($remote_list) || count($remote_list) < 1)
	{
	?>
		<tr><td colspan="11" style="padding:10px;">暂无远程备份设置</td></tr>
	<?php	
	}
	else
	{
		$remote_pass_type_arr = array(
			'1'	=> '密码',
			'2' => '<font color="green">密匙</font>',
			'3' => '<i>无</i>'
		);
		foreach ($remote_list as $key=>$val)
		{
	?>
			<tr>
			<th class="i"><?php echo $val['remote_id'];?></th>
			<td><?php echo $val['remote_type'];?></td>
			<td><?php echo $val['remote_status'] == '1' ? '<font color="green">已开启</font>' : '<font color="red">已关闭</font>';?></td>
			<td><?php echo $val['remote_ip'];?></td>
			<td><?php echo !empty($val['remote_path']) ? $val['remote_path'] : '<i>无</i>';?></td>
			<td><?php echo !empty($val['remote_user']) ? $val['remote_user'] : '<i>无</i>';?></td>
			<td><?php echo $remote_pass_type_arr[$val['remote_pass_type']];?></td>
			<td><?php echo $val['remote_pass_type'] != '3' ? '******' : '<i>无</i>';?></td>
			<td><?php echo !empty($val['remote_comment']) ? $val['remote_comment'] : '<i>无</i>';?></td>
			<td><?php echo $val['remote_time'];?></td>
			<td>
			<?php if (in_array($val['remote_type'], array('FTP', 'SSH'))) { ?>
			<a href="index.php?c=backup&a=backup_remote&check=<?php echo $val['remote_id'];?>" class="button" onclick="return connect_check(this);"><span class="loop icon"></span>连接测试</a>
			<a href="index.php?c=backup&a=backup_remote&edit=<?php echo $val['remote_id'];?>" class="button"><span class="pen icon"></span>编辑</a>
			<a href="index.php?c=backup&a=backup_remote&del=<?php echo $val['remote_id'];?>" class="button" onclick="return confirm('确认删除远程备份设置ID:<?php echo $val['remote_id'];?>?');"><span class="cross icon"></span>删除</a>
			<?php } else { ?>
				<a href="javascript:;" class="button disabled"><span class="loop icon"></span>连接测试</a>
				<a href="javascript:;" class="button disabled"><span class="pen icon"></span>编辑</a>
				<a href="javascript:;" class="button disabled"><span class="cross icon"></span>删除</a>
			<?php } ?>
			</td>
			</tr>
	<?php
		}
	}
	?>
</table>
<br /><br />


<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>

<p>
<?php echo isset($edit_remote) ? '编辑' : '新增';?>远程备份设置:<?php echo isset($edit_remote) ? 'ID' . $_POST['remote_id'] : '';?>
</p>
<form action="index.php?c=backup&a=backup_remote" method="POST"  id="remote_edit" onsubmit="return remote_pass_type_fun(G('remote_pass_type_dom'));"/>
<table border="0" cellspacing="1"  id="STable" style="width:700px;">
	<tr>
	<th> &nbsp; </th>
	<th>值</th>
	<th>说明 </th>
	</tr>

	<tr><td>类型</td>
	<td>
	<select id="remote_type_dom" name="remote_type" onchange="remote_type_fun(this);">
	<option value="FTP">FTP</option>
	<option value="SSH">SSH</option>
	</select>
	<?php if(isset($_POST['remote_type'])) {?>
	<script>G('remote_type_dom').value = '<?php echo $_POST['remote_type'];?>';</script>
	<?php }?>
	</td>
	<td><p> &nbsp; <font class="red">*</font> 远程备份类型</p></td>
	</tr>

	<tr><td>是否启用	</td>
	<td>
	<select id="remote_status_dom" name="remote_status">
	<option value="1">开启</option>
	<option value="2">关闭</option>
	</select>
	<?php if(isset($_POST['remote_status'])) {?>
	<script>G('remote_status_dom').value = '<?php echo $_POST['remote_status'];?>';</script>
	<?php }?>
	</td>
	<td><p> &nbsp; <font class="red">*</font> 是否启用</p></td>
	</tr>

	<tr><td>IP/域</td>
	<td><input type="text" name="remote_ip" class="input_text" value="<?php echo $_POST['remote_ip'];?>" /></td>
	<td><p> &nbsp; <font class="red">*</font> 备份主机的IP或是域名</p></td>
	</tr>

	<tr><td>保存路径	</td>
	<td><input type="text" name="remote_path" class="input_text" value="<?php echo $_POST['remote_path'];?>" /></td>
	<td><p> &nbsp; <font class="red">*</font> 传送至远程保存的路径</p></td>
	</tr>

	<tr><td>账号	</td>
	<td><input type="text" name="remote_user" class="input_text" value="<?php echo $_POST['remote_user'];?>" /></td>
	<td><p> &nbsp; <font class="red">*</font> 账号</p></td>
	</tr>

	<tr><td>账号验证	</td>
	<td>
	<select id="remote_pass_type_dom" name="remote_pass_type" onchange="remote_pass_type_fun(this)">
	</select>
	</td>
	<td><p> &nbsp; <font class="red">*</font> 账号验证方式</p></td>
	</tr>

	<tr><td>密码/密匙</td>
	<td>
		<input id="remote_password1" type="password" class="input_text" name="remote_pass1" value="<?php  echo isset($_POST['remote_pass1']) ? $_POST['remote_pass1'] : '';?>" />
		<textarea id="remote_password2" name="remote_pass2"><?php  echo isset($_POST['remote_pass2']) ? $_POST['remote_pass2'] : '';?></textarea>
		<textarea id="remote_password" name="remote_password" style="display:none;"></textarea>
		<script>remote_type_fun(G('remote_type_dom'));</script>
		<?php if(isset($_POST['remote_pass_type'])) {?>
		<script>G('remote_pass_type_dom').value = '<?php echo $_POST['remote_pass_type'];?>';</script>
		<?php }?>
		<script>remote_pass_type_fun(G('remote_pass_type_dom'));</script>
	</td>
	<td>
		<?php if (!isset($edit_remote)) { ?>
			<p> &nbsp; <font class="red">*</font> 验证账号使用的密码或密匙</p> 
		<?php } else {?> 
			<p> &nbsp; 密码/密匙留空将不做更改</p>
		 <?php }?>
	</td>
	</tr>

	<tr><td>说明备注	</td>
	<td><input type="text" name="remote_comment" class="input_text" value="<?php echo $_POST['remote_comment'];?>" /></td>
	<td><p> &nbsp;  添加说明备注</p></td>
	</tr>
	
</table>

<?php if (isset($edit_remote)) { ?>
	<input type="hidden" name="save_edit" value="<?php echo $_POST['remote_id'];?>" />
<?php } else { ?>
	<input type="hidden" name="save" value="y" />
<?php }?>

<button type="submit" class="primary button" name="submit"><span class="check icon"></span>保存</button> 
</form>


<div id="notice_message" style="width:660px;">
<h3>» 远程备份</h3>
1) 建议使用SSH密匙方式远程备份，数据加密传输、与不需使用明文密码。<br />
2) 设置完成点击连接测试进行检测，可测试远程账号、网络是否能正常连接。<br />
<br />
SSH密匙获取方法：<br />
<ul>
<li>1) SSH登录到用于远程备份的Linux主机。</li>
<li>2) 执行命令 ssh-keygen -t rsa &nbsp; (按三次回车使用默认配置完成生成密钥文件)</li>
<li>3) 执行命令 cd /root/.ssh/ &nbsp; (进入密钥目录，本示例命令为root账号，如用其它账号进对应的密钥目录即可)
<li>3) 执行命令 mv id_rsa.pub authorized_keys &nbsp; (完成公匙重命名)</li>
<li>4) 执行命令 more id_rsa &nbsp; (查看id_rsa密钥，选中文件中的所有内容复制)</li>
<li>5) 回到面板，添加远程设置，粘贴到密码/密匙文本框即完成。 </li>
</ul>


<h3>» SSH 远程备份</h3>
<ul>
<li>FTP连接测试: amh BRftp check [ID] </li>
<li>FTP传输远程数据: amh BRftp post [/home/backup 文件名] </li>
<li>SSH连接测试: amh BRssh check [ID] </li>
<li>SSH传输远程数据: amh BRssh post [/home/backup 文件名] </li>
</ul>

</div>
</div>
<?php include('footer.php'); ?>