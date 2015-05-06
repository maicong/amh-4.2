<?php include('header.php'); ?>
<script src="View/js/date.js"></script>
<script>
window.onload = function () {
	var start_time = new showDate({ id: "start_time" });
	start_time.init('start_time', '<?php echo isset($_GET['start_time']) ? $_GET['start_time'] : ''?>');
	var end_time = new showDate({ id: "end_time" });
	end_time.init('end_time', '<?php echo isset($_GET['end_time']) ? $_GET['end_time'] : ''?>');
}
</script>
<style>
.fi_main {
	position: relative;
}
.fi {
	position: absolute;
}
</style>
<div id="body">
<?php 
$c_name = 'account';
include('category_list.php'); 
?>

<p>管理员操作记录:</p>
<form action="" method="GET" class="fi_main" style="height:33px;">
<input type="hidden" value="account_log" name="a"/>
<input type="hidden" value="account" name="c"/>
<span class="fi" style="left:0px;top:5px">搜索</span>
<select class="fi" id="field" name="field" style="width:100px;left:30px;top:1px">
<option value="1">日志内容</option>
<option value="0">用户名</option>
<option value="2">IP</option>
</select>
<script>G('field').value = '<?php echo isset($_GET['field']) ? $_GET['field'] : '1';?>';</script>
<input type="text" name="search" class="fi input_text" style="width:180px;left:136px" value="<?php echo isset($_GET['search']) ? $_GET['search'] : '';?>" /> 
<span class="fi" style="left:335px;top:5px">&nbsp; 时间</span>
<span class="fi data_box" id="start_time" style="left:370px"></span>
<span class="fi" style="left:495px;top:5px">至</span>
<span class="fi data_box" id="end_time" style="left:511px"></span>
<button type="submit" class="fi primary button" style="left:635px;top:-3px">搜索</button> 
</form>

<table border="0" cellspacing="1"  id="STable" style="width:auto;">
	<tr>
	<th>&nbsp;ID&nbsp;</th>
	<th width="50">用户</th>
	<th>操作</th>
	<th width="110">操作IP</th>
	<th width="130">操作时间</th>
	</tr>
<?php
	foreach ($log_list['data'] as $key=>$val)
	{
?>
	<tr>
	<th class="i"><?php echo $val['log_id'];?></th>
	<td><?php echo !empty($val['user_name']) ? $val['user_name'] : '<i style="font-size:12px">AMH系统</i>';?></td>
	<td width="500">&nbsp; <?php echo nl2br($val['log_text']);?> &nbsp; </td>
	<td><?php echo $val['log_ip'];?></td>
	<td><?php echo $val['log_time'];?></td>
	</tr>
<?php
	}
?>
</table>
<div id="page_list">总<?php echo $total_page;?>页 - <?php echo $log_list['sum'];?>记录 » 页码 <?php echo htmlspecialchars_decode($page_list);?> </div>
<br />

</div>
<?php include('footer.php'); ?>