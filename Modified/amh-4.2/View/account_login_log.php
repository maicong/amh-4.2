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

<p>最近登录记录:</p>

<form action="" method="GET" class="fi_main" style="height:33px;">
<input type="hidden" value="account_login_log" name="a"/>
<input type="hidden" value="account" name="c"/>
<span class="fi" style="left:0px;top:5px">搜索</span>
<select id="field" name="field" class="fi" style="width:100px;left:30px;top:1px">
<option value="0">用户名</option>
<option value="1">IP</option>
</select>
<script>G('field').value = '<?php echo isset($_GET['field']) ? $_GET['field'] : '0';?>';</script>
<input type="text" name="search" class="fi input_text" style="width:120px;left:136px" value="<?php echo isset($_GET['search']) ? $_GET['search'] : '';?>" /> 
<span class="fi" style="left:275px;top:5px;">&nbsp; 登录状态 </span>
<select id="login_success" name="login_success" class="fi" style="width:100px;left:335px">
<option value="">所有</option>
<option value="1">成功</option>
<option value="2">失败</option>
</select>
<script>G('login_success').value = '<?php echo isset($_GET['login_success']) ? $_GET['login_success'] : '';?>';</script>

<span class="fi" style="left:442px;top:5px;">&nbsp; 时间 </span>
<span class="fi data_box" id="start_time" style="left:480px"></span>
<span class="fi" style="left:605px;top:5px">至</span>
<span class="fi data_box" id="end_time" style="left:622px"></span>
<button type="submit" class="fi primary button" style="left:746px;top:-3px;">搜索</button> 
</form>

<table border="0" cellspacing="1"  id="STable" style="width:auto;">
	<tr>
	<th>&nbsp;ID&nbsp;</th>
	<th>&nbsp; 用户名 &nbsp;</th>
	<th width="160">登录IP</th>
	<th width="80">登录状态</th>
	<th width="160">登录时间</th>
	</tr>
<?php
	foreach ($login_list['data'] as $key=>$val)
	{
?>
	<tr>
	<th class="i"><?php echo $val['login_id'];?></th>
	<td><?php echo $val['login_user_name'];?></td>
	<td><?php echo $val['login_ip'];?></td>
	<td><?php echo $val['login_success'] ? '成功' : '失败';?></td>
	<td><?php echo $val['login_time'];?></td>
	</tr>
<?php
	}
?>
</table>
<div id="page_list">总<?php echo $total_page;?>页 - <?php echo $login_list['sum'];?>记录 » 页码 <?php echo htmlspecialchars_decode($page_list);?> </div>
<br />

</div>
<?php include('footer.php'); ?>