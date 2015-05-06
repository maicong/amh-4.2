<?php include('header.php'); ?>

<div id="body">
<?php 
$c_name = 'mysql';
include('category_list.php'); 
?>

<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>
<p>MySQL数据库列表:</p>
<table border="0" cellspacing="1"  id="STable" style="width:auto;margin-bottom:5px;">
	<tr>
	<th>&nbsp;ID&nbsp;</th>
	<th>数据库</th>
	<th width="150">字符集</th>
	<th  width="80">表数量</th>
	<th  width="160">操作</th>
	</tr>
	<?php
		$i = 0;
		foreach ($databases as $key=>$val)
		{
	?>
	<tr>
	<th class="i"><?php echo ++$i;?></th>
	<td style="padding:5px 30px">
		<a href="index.php?c=mysql&a=mysql_list&ams=database&name=<?php echo urlencode($val['Database']);?>" target="_blank"><?php echo $val['Database'];?></a>
	</td>
	<td><?php echo $val['collations'];?></td>
	<td><?php echo $val['sum'];?></td>
	<td>
		<?php if(!in_array($val['Database'], $databases_systems_list)) { ?>
			<a href="index.php?c=mysql&a=mysql_list&del=<?php echo urlencode($val['Database']);?>" class="button" onclick="return confirm('确认删除数据库:<?php echo $val['Database'];?>?');"><span class="cross icon"></span> 删除</a>
			<a href="index.php?c=mysql&a=mysql_list&empty=<?php echo urlencode($val['Database']);?>" class="button" onclick="return confirm('确认清空数据库:<?php echo $val['Database'];?>?');"><span class="trash icon"></span> 清空</a>
		<?php } else {?>
			<a href="javascript:" class="button disabled"><span class="cross icon disabled"></span> 删除</a>
			<a href="javascript:" class="button disabled"><span class="trash icon disabled"></span> 清空</a>
		<?php } ?>
	</td>
	<?php
		}
	?>
</table>
<img src="View/images/logo_ams.gif" align="top"/> 
<input type="button" value="MySQL管理" onclick="WindowOpen('index.php?c=mysql&a=mysql_list&ams=index');"/>

<div id="notice_message" style="width:470px;">
<h3>» SSH MySQL</h3>
1) 有步骤提示操作: <br />
ssh执行命令: amh mysql <br />
然后选择对应的1~6的选项进行操作。<br />

2) 或直接操作: <br />
<ul>
<li>启动MySQL: amh mysql start</li>
<li>停止MySQL: amh mysql stop </li>
<li>重载MySQL: amh mysql reload </li>
<li>重启MySQL: amh mysql restart</li>
<li>强制重载MySQL: amh mysql force-reload </li>
</ul>
3) MySQL本地连接地址使用 127.0.0.1
</div>
</div>
<?php include('footer.php'); ?>
