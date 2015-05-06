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

<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>
<p>数据备份列表:</p>
<table border="0" cellspacing="1"  id="STable" style="width:1000px;">
	<tr>
	<th>&nbsp;ID&nbsp;</th>
	<th>备份文件</th>
	<th>文件大小</th>
	<th>是否加密</th>
	<th>远程备份</th>
	<th>备份选项</th>
	<th width="170">说明备注</th>
	<th>备份时间</th>
	<th>操作</th>
	</tr>
	<?php 
	if(!is_array($backup_list['data']) || count($backup_list['data']) < 1)
	{
	?>
		<tr><td colspan="9" style="padding:10px;">暂无备份数据记录</td></tr>
	<?php	
	}
	else
	{
		foreach ($backup_list['data'] as $key=>$val)
		{
	?>
			<tr>
			<th class="i"><?php echo $val['backup_id'];?></th>
			<td><?php echo $val['backup_file'];?></td>
			<td><?php echo $val['backup_size'];?>MB</td>
			<td><?php echo empty($val['backup_password']) ? '无' : '有加密';?></th>
			<td><?php echo ($val['backup_remote'] == '0') ? '无' : '有远程备份';?></th>
			<td>全面备份 <?php echo ($val['backup_options'] == 'N') ? '<br /><i>(无MySQL数据)</i>' : ($val['backup_options'] == 'n' ? '<br /><i>(无wwwroot数据)</i>' : '');?></th>
			<td><?php echo !empty($val['backup_comment']) ? $val['backup_comment'] : '<i>无</i>';?>
			</td>
			<td><?php echo $val['backup_time'];?></td>
			<td>
			<a href="index.php?c=backup&a=backup_revert&revert_id=<?php echo $val['backup_id'];?>" class="button" ><span class="reload icon"></span>一键还原</a>
			<a href="index.php?c=backup&a=backup_list&del=<?php echo $val['backup_id'];?>" class="button" onclick="return confirm('确认删除备份数据:<?php echo $val['backup_file'];?>?');"><span class="cross icon"></span>删除</a>
			</td>
			</tr>
	<?php
		}
	}
	?>
</table>
<div id="page_list">总<?php echo $total_page;?>页 - <?php echo $backup_list['sum'];?>份文件 » 页码 <?php echo htmlspecialchars_decode($page_list);?> </div>
<br />
</div>
<?php include('footer.php'); ?>