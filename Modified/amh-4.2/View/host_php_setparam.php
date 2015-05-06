<?php include('header.php'); ?>
<div id="body">
<?php 
$c_name = 'host';
include('category_list.php'); 
?>

<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>
<p>PHP全局配置更改:</p>
<form action="" method="POST"  id="account">
<table border="0" cellspacing="1"  id="STable" style="width:600px;">
	<tr>
	<th>参数名</th>
	<th>值</th>
	<th>示例值</th>
	</tr>
	<?php
	foreach ($param_list as $key=>$val)
	{
	?>
		<tr><td><?php echo $val[0];?> (<?php echo $val[1];?>) </td>
		<td><input type="text" name="<?php echo $val[1];?>" class="input_text" value="<?php echo $val[3];?>" />
		</td>
		<td><i style="font-size:12px;"><?php echo $val[2];?></i></td>
		</tr>
	<?php
	}
	?>
	</table>
<button type="submit" class="primary button" name="submit"><span class="check icon"></span>保存</button> 
</form>


<div id="notice_message" style="width:470px;">
<h3>» SSH SetParam php</h3>
1) 有步骤提示操作: <br />
ssh执行命令: amh SetParam php <br />
然后选择对应的选项进行操作。<br />
<br />
2) 或直接执行完整命令: <br />
<ul>
<li>更改display_errors参数: amh SetParam php display_errors On</li>
<li>更改memory_limit参数: amh SetParam php memory_limit 68M </li>
<li>更改post_max_size参数: amh SetParam php post_max_size 4M</li>
<li>其它参数更改的命令格式相同，更换对应参数名称即可。</li>
</ul>
<br />
3) PHP配置文件位置: /etc/php.ini
</div>
</div>

<?php if (isset($_POST['submit'])) {?>
<script>
// 面板php与所有虚拟主机php重载
Ajax.get('./index.php?c=host&a=host&run=amh-web&m=php&g=reload&confirm=y');
Ajax.get('./index.php?m=php&g=reload');
</script>
<?php } ?>

<?php include('footer.php'); ?>
