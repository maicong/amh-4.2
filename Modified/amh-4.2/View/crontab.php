<?php include('header.php'); ?>
<script src="View/js/task.js"></script>
<style>
#STable td{
	padding:5px;
}
#STable td.td_block {
	padding:10px 20px;
	text-align:left;
	line-height:23px;
}
.crontab_item {
	display:inline-block;
	margin-right:1%;
	/*float:left;*/
	width:97%;
	margin:8px 0px;
}
.crontab_item .name {
	width:55px;
	display:inline-block;
}
.crontab_item span, .crontab_item select, .crontab_item input {
	float:left;
	margin:0px 1px;
}
.crontab_item select {
	width:100px;
}
.crontab_item span {
	padding:0px 1px;
}
.average_input {
	width:30px;
}
.respectively {
	width:100px;
}
</style>

<div id="body">
<?php 
$c_name = 'task';
include('category_list.php'); 
?>

<?php
	if (!empty($top_notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $top_notice . '</p></div>';
?>
<p>任务计划列表:</p>
<table border="0" cellspacing="1"  id="STable" style="width:auto;">
	<tr>
	<th>&nbsp;ID&nbsp;</th>
	<th>&nbsp; 分钟 &nbsp;</th>
	<th>&nbsp; 小时 &nbsp;</th>
	<th>&nbsp; 天 &nbsp;</th>
	<th>&nbsp; 月 &nbsp;</th>
	<th>&nbsp; 星期段 &nbsp;</th>
	<th>&nbsp; 运行脚本 / 命令 &nbsp;</th>
	<th width="50">所属组</th>
	<th width="150">添加时间</th>
	<th width="160">操作</th>
	</tr>
	<?php 
	if(!is_array($crontab_list) || count($crontab_list) < 1)
	{
	?>
		<tr><td colspan="10" style="padding:10px;">暂无任务计划</td></tr>
	<?php	
	}
	else
	{
		foreach ($crontab_list as $key=>$val)
		{
	?>
			<tr>
			<th class="i"><?php echo $val['crontab_id'];?></th>
			<td><?php echo $val['crontab_minute'];?></td>
			<td><?php echo $val['crontab_hour'];?></td>
			<td><?php echo $val['crontab_day'];?></td>
			<td><?php echo $val['crontab_month'];?></td>
			<td><?php echo $val['crontab_week'];?></td>
			<td><?php echo $val['crontab_ssh'];?></td>
			<td><?php echo $val['crontab_type'];?></td>
			<td><?php echo $val['crontab_time'];?></td>
			<td>
			<?php if($val['crontab_type'] == 'ssh') { ?>
			<a href="javascript:" class="button disabled"><span class="pen icon disabled"></span> 编辑</a>
			<a href="javascript:" class="button disabled"><span class="cross icon disabled"></span> 删除</a>
			<?php } else {?>
			<a href="index.php?c=task&a=task_set&edit=<?php echo $val['crontab_id'];?>" class="button"><span class="pen icon"></span> 编辑</a>
			<a href="index.php?c=task&a=task_set&del=<?php echo $val['crontab_id'];?>" class="button" onclick="return confirm('确认删除任务计划ID:<?php echo $val['crontab_id'];?> ?');"><span class="cross icon"></span> 删除</a>
			<?php }?>
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
<table border="0" cellspacing="1"  id="STable" style="width:670px;">
	<tr>
	<th><?php echo !isset($edit_task) ? '创建' : '编辑' ;?>任务计划</th>
	</tr>
	<tr>
	<td class="td_block">
	<form action="" method="POST"  id="task" />
	<div id="crontab">
	</div>

	运行命令: &nbsp; 
	<input type="text" class="input_text" style="width:190px" name="crontab_ssh" value="<?php echo isset($_POST['crontab_ssh']) ? $_POST['crontab_ssh'] : '';?>" /> <font class="red">*</font> AMH相关命令
	<br /> 
	<br />


	<?php if (isset($edit_task)) { ?>
	<input type="hidden" name="crontab_id" value="<?php echo $_POST['crontab_id'];?>"/>
	<button type="submit" class="primary button" name="save_submit"><span class="check icon"></span>保存</button> 
	<?php } else { ?>
	<button type="submit" class="primary button" name="task_submit"><span class="check icon"></span>创建</button> 
	<?php }?>

	</form>
	</td>
	</tr>
</table>


<script>
var post = {};
<?php
	foreach ($_POST as $key=>$val)
	{
		$val = json_encode($val);
		echo "post['$key'] = $val;\n";
	}
?>
var crontab_run = function ()
{
	for (var k in  crontab_object)
	{
		for (var i in crontab_object[k] )
		{
			var name = null;
			if(crontab_object[k][i].name) name = crontab_object[k][i].name;
			if(crontab_object[k][i].amh_name) name = crontab_object[k][i].amh_name;
			if (name && post[name])
			{
				if (typeof(post[name]) == 'object')
				{
					for (var s =0; s < crontab_object[k][i].length; ++s)
					{
						if(crontab_object[k][i][s] && post[name].join('').indexOf(crontab_object[k][i][s].value) != -1)
							crontab_object[k][i][s].selected = true;
					}
				}
				else
				{
					crontab_object[k][i].value = post[name];
				}
			}
		}
		crontab_object[k].select.onchange();
	}
}
if (window.attachEvent)
	window.attachEvent('onload', function(){ crontab_run(); });
else
	window.addEventListener('load', function(){ crontab_run(); }, false);
</script>


<div id="notice_message" style="width:630px;">
<h3>» WEB 任务计划</h3>
时间属性说明：<br />
1) 定时：固定在指定的时间点运行。* 为不限制，即每个时间点都执行一次。<br />
2) 期间：在指定时间范围内的每个时间点执行。<br />
3) 平均：在指定时间范围内平均多少个时间点执行一次。如分钟设置 0 到 59 / 2，即为平均2分钟执行一次。<br />
4) 选择：使用Ctrl铵键，选择您需的时间点执行。<br />
<br />
温馨提示: <br />
WEB端只允许添加AMH命令，例如添加AMH即时备份命令：amh backup y y >/tmp/amh.log
<br />SSH添加的任务计划WEB端不可更改。

<h3>» SSH 任务计划</h3>
1) 有步骤提示操作: <br />
ssh执行命令: amh crontab <br />
2) 或直接操作: (直接操作*号需加\\)<br />
<ul>
<li>任务列表: amh crontab list </li>
<li>增加任务: amh crontab add [crontab数据 如： 1 2 \\* \\* \\* amh php restart] </li>
<li>删除任务: amh crontab del [crontab数据] </li>
</ul>
3) ssh非root用户，amh crontab 只允许增加/删除amh命令。
</div>


</div>
<?php include('footer.php'); ?>
