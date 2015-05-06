<?php include('header.php'); ?>

<div id="body">
<?php 
$c_name = 'mysql';
include('category_list.php'); 
?>

<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>
<p>MySQL全局配置:</p>
<form action="" method="POST"  id="account">
<table border="0" cellspacing="1"  id="STable" style="width:650px;">
	<tr>
	<th>参数名</th>
	<th>值</th>
	<th width="60">示例值</th>
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


<div id="notice_message" style="width:520px;">
<h3>» SSH SetParam MySQL</h3>
1) 有步骤提示操作: <br />
ssh执行命令: amh SetParam MySQL <br />
然后选择对应的选项进行操作。<br />
<br />
2) 或直接执行完整命令: <br />
<ul>
<li>更改InnoDB_Engine参数: amh SetParam mysql InnoDB_Engine On</li>
<li>更改max_allowed_packet参数: amh SetParam mysql max_allowed_packet 1M </li>
<li>更改table_open_cache参数: amh SetParam mysql table_open_cache 64</li>
<li>其它参数更改的命令格式相同，更换对应参数名称即可。</li>
</ul>
<br />
3) MySQL配置文件位置: /etc/my.cnf
</div>
</div>

<?php if (isset($_POST['submit'])) {?>
<script>
window.onload = function ()
{
	Ajax.get('./index.php?m=mysql&g=restart', null, false, true); // 重启MySQL
}
</script>
<?php } ?>

<?php include('footer.php'); ?>
