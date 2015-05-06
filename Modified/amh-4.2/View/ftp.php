<?php include('header.php'); ?>
<script src="View/js/ftp.js"></script>


<div id="body">
<?php 
$c_name = 'ftp';
include('category_list.php'); 
?>

<?php
	if (!empty($top_notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $top_notice . '</p></div>';
?>
<p>FTP账号列表:</p>
<table border="0" cellspacing="1"  id="STable" style="width:auto;min-width: 700px;">
	<tr>
	<th>&nbsp;ID&nbsp;</th>
	<th>账号</th>
	<th width="60">密码</th>
	<th>根目录</th>
	<th width="60">目录所属<br />权限用户</th>
	<th width="60">FTP账号<br />权限用户</th>
	<th>所属组</th>
	<th width="125">添加时间</th>
	<th>操作</th>
	</tr>
	<?php 
	if(!is_array($ftp_list) || count($ftp_list) < 1)
	{
	?>
		<tr><td colspan="9" style="padding:10px;">暂无FTP账号</td></tr>
	<?php	
	}
	else
	{
		foreach ($ftp_list as $key=>$val)
		{
	?>
			<tr>
			<th class="i"><?php echo $val['ftp_id'];?></th>
			<td><?php echo $val['ftp_name'];?></td>
			<td>******</th>
			<td><?php echo $val['ftp_root'];?></td>
			<td><?php echo $val['ftp_directory_uname'];?></td>
			<td><?php echo $val['ftp_uid_name'];?></td>
			<td><?php echo $val['ftp_type'];?></td>
			<td><?php echo $val['ftp_time'];?></td>
			<td>
			<?php if($val['ftp_type'] == 'ssh') { ?>
			<a href="javascript:" class="button disabled"><span class="pen icon disabled"></span> 编辑</a>
			<a href="javascript:" class="button disabled"><span class="key icon disabled"></span> 重写目录权限</a>
			<a href="javascript:" class="button disabled"><span class="cross icon disabled"></span> 删除</a>
			<?php } else {?>
			<a href="index.php?c=ftp&a=ftp_list&edit=<?php echo urlencode($val['ftp_name']);?>" class="button"><span class="pen icon"></span> 编辑</a>
			<a href="index.php?c=ftp&a=ftp_list&chown=<?php echo urlencode($val['ftp_name']);?>&uidname=<?php echo $val['ftp_uid_name'];?>" class="button"  onclick="return confirm('确认递归重写目录:<?php echo $val['ftp_root'];?>\n\n为FTP账号的<?php echo $val['ftp_uid_name'];?>用户权限吗?');"><span class="key icon"></span> 重写目录权限</a>
			<a href="index.php?c=ftp&a=ftp_list&del=<?php echo urlencode($val['ftp_name']);?>" class="button" onclick="return confirm('确认删除FTP账号:<?php echo $val['ftp_name'];?>?');"><span class="cross icon"></span> 删除</a>

			<?php } ?>
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
<?php echo isset($edit_ftp) ? '编辑' : '新增';?>FTP账号:<?php echo isset($edit_ftp) ? $_POST['ftp_name'] : '';?>
</p>
<form action="index.php?c=ftp&a=ftp_list" method="POST"  id="ftp_edit" />
<table border="0" cellspacing="1"  id="STable" style="width:750px;">
	<tr>
	<th> &nbsp; </th>
	<th>参数值</th>
	<th>说明 [<a href="javascript:;" onclick="ShowFtpTop()">打开 / 关闭 高级选项</a>] </th>
	</tr>
	<tr><td>账号</td>
	<td><input type="text" name="ftp_name" class="input_text <?php echo isset($edit_ftp) ? ' disabled' : '';?>" value="<?php echo $_POST['ftp_name'];?>" <?php echo isset($edit_ftp) ? 'disabled=""' : '';?> style="width:298px"/></td>
	<td><p> &nbsp; <font class="red">*</font> 登录FTP账号</p></td>
	</tr>
	<tr><td>密码</td>
	<td><input type="password" name="ftp_password" class="input_text" value="<?php echo $_POST['ftp_password'];?>"  style="width:298px"/></td>
	<td><p> &nbsp; <font class="red">*</font> 登录FTP密码 <?php echo isset($edit_ftp) ? ' [不更改密码请留空]' : '';?></p></td>
	</tr>
	<tr><td>主机根目录</td>
	<td>
	<select name="ftp_root" id="ftp_root">
	<option value="">请选择虚拟主机根目录</option>
	<?php
		foreach ($dirs as $key=>$val)
		{
			if($val != 'index')
				echo '<option value="' . $val . '">/home/wwwroot/' . $val . '/web</option>';
		}
	?>
	</select>
	<script>
	G('ftp_root').value = '<?php echo isset($_POST['ftp_root']) ? $_POST['ftp_root'] : '';?>';
	</script>
	</td>
	<td><p> &nbsp; <font class="red">*</font> FTP根目录</p></td>
	</tr>
	<tr><td>权限用户</td>
	<td>
	<select name="ftp_uid_name" id="ftp_uid_name">
	<option value="www">www</option>
	<option value="ftpuser">ftpuser</option>
	</select>
	<script>
	G('ftp_uid_name').value = '<?php echo isset($_POST['ftp_uid_name']) ? $_POST['ftp_uid_name'] : 'www';?>';
	</script>
	</td>
	<td><p> &nbsp; <font class="red">*</font> FTP账号所属的权限用户</p></td>
	</tr>
	<tr class="ftptop none"><td>上传速度限制</td>
	<td><input type="text" name="ftp_upload_bandwidth"  class="input_text" value="<?php echo isset($_POST['ftp_upload_bandwidth']) ? $_POST['ftp_upload_bandwidth'] : '';?>" />
	<input type="checkbox" id="checkbox_ftp_upload_bandwidth"  name="_ftp_upload_bandwidth"/>
	<label for="checkbox_ftp_upload_bandwidth">不限制</label>
	</td>
	<td><p> &nbsp; 限制FTP上传速度 [KB]</p></td>
	</tr>
	<tr class="ftptop none"><td>下载速度限制</td>
	<td>
	<input type="text" name="ftp_download_bandwidth"  class="input_text" value="<?php echo isset($_POST['ftp_download_bandwidth']) ? $_POST['ftp_download_bandwidth'] : '';?>" />
	<input type="checkbox" id="checkbox_ftp_download_bandwidth"  name="_ftp_download_bandwidth"/>
	<label for="checkbox_ftp_download_bandwidth" >不限制</label>
	</td>
	<td><p> &nbsp; 限制FTP下载速度  [KB]</p></td>
	</tr>
	<tr class="ftptop none"><td>上传比率值</td>
	<td><input type="text" name="ftp_upload_ratio" class="input_text" value="<?php echo isset($_POST['ftp_upload_ratio']) ? $_POST['ftp_upload_ratio'] : '';?>" />
	<input type="checkbox" id="checkbox_ftp_upload_ratio"  name="_ftp_upload_ratio"/>
	<label for="checkbox_ftp_upload_ratio" >不限制</label>
	</td>
	<td><p> &nbsp; 设置上传比率值 </p></td>
	</tr>
	<tr class="ftptop none"><td>下载比率值</td>
	<td>
	<input type="text" name="ftp_download_ratio" id="" class="input_text" value="<?php echo isset($_POST['ftp_download_ratio']) ? $_POST['ftp_download_ratio'] : '';?>" />
	<input type="checkbox" id="checkbox_ftp_download_ratio"  name="_ftp_download_ratio"/>
	<label for="checkbox_ftp_download_ratio" >不限制</label>
	</td>
	<td><p> &nbsp; 设置下载比率值 </p></td>
	</tr>
	<tr class="ftptop none"><td>文件数量</td>
	<td><input type="text" name="ftp_max_files"  class="input_text" value="<?php echo isset($_POST['ftp_max_files']) ? $_POST['ftp_max_files'] : '';?>" />
	<input type="checkbox" id="checkbox_ftp_max_files"  name="_ftp_max_files"/>
	<label for="checkbox_ftp_max_files" >不限制</label>
	</td>
	<td><p> &nbsp; 限制FTP文件个数</p></td>
	</tr>
	<tr class="ftptop none"><td>容量</td>
	<td><input type="text" name="ftp_max_mbytes"  class="input_text" value="<?php echo isset($_POST['ftp_max_mbytes']) ? $_POST['ftp_max_mbytes'] : '';?>" />
	<input type="checkbox" id="checkbox_ftp_max_mbytes"  name="_ftp_max_mbytes"/>
	<label for="checkbox_ftp_max_mbytes" >不限制</label>
	</td>
	<td><p> &nbsp; 限制FTP空间容量 [MB]</p></td>
	</tr>
	<tr class="ftptop none"><td>连接数限制</td>
	<td><input type="text" name="ftp_max_concurrent" class="input_text" value="<?php echo isset($_POST['ftp_max_concurrent']) ? $_POST['ftp_max_concurrent'] : '';?>" />
	<input type="checkbox" id="checkbox_ftp_max_concurrent"  name="_ftp_max_concurrent"/>
	<label for="checkbox_ftp_max_concurrent" >不限制</label>
	</td>
	<td><p> &nbsp; 限制同时连接FTP数</p></td>
	</tr>
	<tr class="ftptop none"><td>使用时间限制</td>
	<td><input type="text" name="ftp_allow_time"  class="input_text" value="<?php echo isset($_POST['ftp_allow_time']) ? $_POST['ftp_allow_time'] : '';?>" />
	<input type="checkbox" id="checkbox_ftp_allow_time"  name="_ftp_allow_time"/>
	<label for="checkbox_ftp_allow_time">不限制</label>
	</td>
	<td><p> &nbsp; 限制只能在允许时间段内连接FTP</p>
	<p> &nbsp; 格式：小时分钟-小时分钟</p></td>
	</tr>
</table>

<?php if (isset($edit_ftp)) { ?>
	<input type="hidden" name="save_edit" value="<?php echo $_POST['ftp_name'];?>" />
	<script>ShowFtpTop();</script>
<?php } else { ?>
	<input type="hidden" name="save" value="y" />
<?php }?>

<button type="submit" class="primary button" name="submit"><span class="check icon"></span>保存</button> 
</form>


<div id="notice_message" style="width:890px">
<h3>» WEB FTP</h3>
1) web添加的FTP账号根目录只允许为虚拟主机的根目录。 <br />
2) ssh添加的FTP账号web端不可删除与编辑。 <br />
3) 关于FTP权限用户: 虚拟主机首次添加FTP账号时系统会自动重写FTP目录为相应的用户权限。<br />
面板PHP为www权限用户。如FTP账号使用www权限，PHP将拥有FTP账号根目录下所有文件的读写操作。<br />
如FTP账号使用ftpuser权限用户，PHP将不能写文件操作，安装程序时程序要求读写的目录再手动改为www:www用户或使用FTP改为777权限即可。

<h3>» SSH FTP</h3>
1) 有步骤提示操作: <br />
ssh执行命令: amh ftp <br />
然后选择对应的1~7的选项进行操作。<br />

2) 或直接操作: <br />
<ul>
<li>查看FTP列表: amh ftp list </li>
<li>增加FTP用户: amh ftp add [账号] [密码] [根目录] [上传速度限制] [下载速度限制] [上传比率值] [下载比率值] [文件数量] [容量] [连接并发数] [使用时间限制] [权限用户]</li>
<li>编辑FTP用户: amh ftp edit [账号] [-] [根目录] [上传速度限制] [下载速度限制] [下载比率值] [下载比率值] [文件数量] [容量] [连接并发数] [使用时间限制] [权限用户]
<li>更改FTP密码: amh ftp pass [账号] [密码]
<li>重写FTP目录权限: amh ftp chown [账号] [y/n]
<li>删除ftp用户: amh ftp del [账号]</li>
</ul>

温馨提示:<br />
增加或编辑账号忽略参更改某一参数请填写0，不做限制请填写-符号。 <br />
例如: amh ftp add testftp testpass /home/wwwroot 0 100  <br />
以上命令为增加ftp用户，账号为testftp密码为testpass，ftp根目录为/home/wwwroot，忽略更改上传速度参数、限制下载速度为100kb。 <br />
</div>

</div>
<?php include('footer.php'); ?>
