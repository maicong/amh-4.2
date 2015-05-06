<?php include('header.php'); ?>	
<style>
.host_list {
	margin:1px;
}
#rewrite_name , #rewrite_content {
	width: 500px;
	display:inline-block;
}
#rewrite_name { 
	width: 455px;
}
#rewrite_content { 
	height:200px;
}
</style>

<div id="body">
	<h2>AMH » AMRewrite</h2>
<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p style="display:inline-block" id="' . $status . '">' . $notice . '</p></div>';
?>
<p>虚拟主机Rewrite规则管理:</p>
<div id="AMProxy_list">
	<table border="0" cellspacing="1"  id="STable" style="width:800px;">
	<tr>
	<th>ID</th>
	<th>名称</th>
	<th>文件位置</th>
	<th>正在应用规则的站点</th>
	<th>Rewrite规则管理</th>
	</tr>
	<?php 
	if(!is_array($rewrite_list) || count($rewrite_list) < 1)
	{
	?>
		<tr><td colspan="3" style="padding:10px;">暂无Rewrite规则文件。</td></tr>
	<?php	
	}
	else
	{
		$i = 0;
		foreach ($rewrite_list as $key=>$val)
		{
	?>
			<tr>
			<th class="i"><?php echo ++$i;?></th>
			<td><?php echo ($key == 'amh.conf') ? $key .' &nbsp;(Default)' : $key ;?></td>
			<td>/usr/local/nginx/conf/rewrite/<?php echo $key;?></td>
			<td>
				<?php
					if (is_array($val))
					{
						foreach ($val as $k=>$v)
							echo "<p class='host_list'><a href='http://{$v}' class='button' target='_blank'><span class='home icon'></span>主页</a> <a href='/index.php?c=host&a=vhost&edit={$v}' target='_blank'>{$v}</a></p>";	
					}
					else
					{    
						echo '<i>无</i>';
					}
				?>
			</td>
			<td>
			<a href="./index.php?c=amrewrite&name=<?php echo substr($key, 0, -5);?>" class="button"><span class="pen icon"></span> 查看编辑</a>
			<?php if($key != 'amh.conf') {?>
				<a href="./index.php?c=amrewrite&del=<?php echo $key;?>" class="button" onclick="return confirm('确认删除Rewrite规则:<?php echo $key;?> ?');"><span class="cross icon"></span> 删除</a>
			<?php }else{?>
				<a href="javascript:;" class="button disabled"><span class="cross icon"></span> 删除</a>
			<?php } ?>
			</td>
			</tr>
	<?php
		}
	}
	?>
</table>
<button type="submit" class="primary button" onclick="WindowLocation('/index.php?c=amrewrite')" ><span class="home icon"></span>返回列表</button> 
<button type="submit" class="primary button" onclick="WindowLocation('/index.php?c=amrewrite&check_config=y')" ><span class="check icon"></span>校验Rewrite规则</button> 

<br /><br /><br />
<form action="./index.php?c=amrewrite" method="POST" >
<p><?php echo isset($_GET['name']) ? '查看编辑' : '新增'; ?>Rewrite规则:</p>
<table border="0" cellspacing="1"  id="STable" style="width:860px;">
<tr>
<th>名称</th>
<th>值</th>
<th>说明</th>
</tr>

<tr>
<td>名称</td>
<td>
<?php if (isset($_GET['name'])) { ?>
	<!-- 编辑保存 -->
	<input type="text" id="rewrite_name" value="<?php echo $_GET['name'];?>" class="input_text disabled" disabled />
	<input type="hidden" name="rewrite_name" value="<?php echo $_GET['name'];?>" />
<?php } else {?>
	<input type="text" class="input_text" id="rewrite_name" name="rewrite_name" value="<?php echo isset($_POST['rewrite_name']) ?  $_POST['rewrite_name'] : '';?>" />
<?php } ?>
.conf &nbsp; <font class="red">*</font></td>
<td>规则文件名称
<div style="font-size:11px;color:#848484;margin:5px;">(e.g: discuz,phpwind)</div>
</td>
</tr>
<tr>
<td>规则内容</td>
<td><textarea name="rewrite_content" class="input_text"  id="rewrite_content"><?php echo isset($_POST['rewrite_content']) ? $_POST['rewrite_content'] : '';?></textarea> <font class="red"></font></td>
<td>请确认规则内容正确无误，<br />错误的规则会影响Nginx启动。
</td>
</tr>
<tr><th colspan="3" style="padding:10px;text-align:left;">
<button type="submit" class="primary button" name="<?php echo isset($_GET['name']) ? 'save' : 'add';?>"><span class="check icon"></span><?php echo isset($_GET['name']) ? '保存' : '新增';?></button> 
</th></tr>
</table>
</form>
</div>



<div id="notice_message" style="width:660px;">
<h3>» AMRewrite 模块使用说明</h3>
1) amh.conf 为AMH面板默认Rewrite规则文件，不可删除。 <br />
2) Rewrite规则编辑保存后会直接生效(面板会平滑重载Nginx)，不需要您再额外重载Nginx。 <br />
3) 检查您的规则是否完整与是否正确，虚拟主机如果使用错误的规则会影响Nginx的启动。<br />
4) 保存使用规则后，您可以点击"校验Rewrite规则", 避免存在错误规则，导致重启机器时Nginx启动不成功。<br />
5) 本模块的卸载&删除不会影响已保存的Rewrite规则文件与对应的虚拟主机。 <br />
6) 获得新版本支持或问题反馈，请联系AMH官方网站。
<br />
</div>


</div>

</div>

<?php if (isset($_POST['reload_nginx'])) {?>
<script>
window.onload = function (){
	Ajax.get('/index.php?m=nginx&g=reload'); // 重载Nginx
}
</script>
<?php } ?>

<?php include('footer.php'); ?>
