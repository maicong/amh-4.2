<?php include('header.php'); ?>
<div id="body">
<h2>AMH » AMChroot</h2>

<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>
<p>虚拟主机列表:</p>
<div id="amchroot_list">
	<table border="0" cellspacing="1"  id="STable" style="width:600px;">
	<tr>
	<th>ID</th>
	<th>主标识域名</th>
	<th>设置环境运行模式</th>
	</tr>
	<?php 
	if(!is_array($amchroot_list) || count($amchroot_list) < 1)
	{
	?>
		<tr><td colspan="3" style="padding:10px;">暂无虚拟主机。</td></tr>
	<?php	
	}
	else
	{
		foreach ($amchroot_list as $key=>$val)
		{
	?>
			<tr>
			<th class="i"><?php echo $key+1;?></th>
			<td><?php echo $val[0];?></td>
			<td>
			<a href="./index.php?c=amchroot&domain=<?php echo $val[0];?>&mode=chroot"><span class="<?php echo $val[2] ? 'run_start' : '';?>" >安全模式</span></a>
			<a href="./index.php?c=amchroot&domain=<?php echo $val[0];?>&mode=normal"><span class="<?php echo $val[2] ? '' : 'run_start';?>" >兼容模式</span></a>
			</td>
			</tr>
	<?php
		}
	}
	?>
</table>
<?php if (isset($_GET['domain'])) {?>
	<button type="button" class="primary button" onclick="WindowLocation('./index.php?c=amchroot')"><span class="check icon"></span> 返回</button>
<?php } ?>

<div id="notice_message" style="width:880px;">
<h3>» AMChroot 使用说明</h3>
1) 安全模式：默认防跨站安全模式，各虚拟主机隔离环境下运行，互不影响，安全性较高。<br />
2) 兼容模式：常规兼容模式，虚拟主机不做隔离限制，有较自由的运行空间。<br />
3) 面板配置如果开启面板数据私有保护，面板将不可切换主机为兼容模式。<br />

<h3>» SSH AMChroot</h3>
1) 有步骤提示操作: <br />
ssh执行命令: amh module AMChroot-1.1<br />
然后选择对应的操作选项进行管理。<br />
<br />
2) 或直接操作: <br />
<ul>
<li>AMChroot管理: amh module AMChroot-1.1 [info / install / admin / uninstall / status]</li>
<li>虚拟主机运行列表: amh module AMChroot-1.1 admin list</li>
<li>编辑虚拟主机运行环境: amh module AMChroot-1.1 admin edit,domain.com,normal</li>
</ul>

3) AMChroot admin管理选项说明：
<br />执行 amh module AMChroot-1.1 admin 提示输入管理参数(list,edit)，可以不输入直接回车显示提示选项进行操作。
<br />
4) 更多使用帮助与新版本支持或问题反馈，请联系AMH官方网站。
</div>
</div>
</div>
<?php include('footer.php'); ?>


