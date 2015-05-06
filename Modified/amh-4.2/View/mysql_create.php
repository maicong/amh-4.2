<?php include('header.php'); ?>
<script src="View/js/mysql_create.js"></script>
<div id="body">
<?php 
$c_name = 'mysql';
include('category_list.php'); 
?>

<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>
<p>快速创建数据库:</p>
<form action="" method="POST"  id="mysql_create">
<table border="0" cellspacing="1"  id="STable" style="width:auto;">
	<tr>
	<th width="120"></th>
	<th width="380">值</th>
	<th>说明</td>
	</tr>
	
	<tr>
	<td>数据库名称</td>
	<td><input type="text" name="database_name" id="database_name" class="input_text" value="<?php echo isset($_POST['database_name']) ? $_POST['database_name'] : '';?>"></td>
	<td class="description">填写数据库名字</td>
	</tr>
	<tr>
	<td>数据库编码</td>
	<td style="padding:10px">
	<input type="radio" name="database_character" value="utf8_general_ci" checked=""  id="utf8_general_ci"> <label for="utf8_general_ci" title="utf8_general_ci">utf8</label> &nbsp;
	<input type="radio" name="database_character" value="gbk_chinese_ci"  id="gbk_chinese_ci"> <label for="gbk_chinese_ci" title="gbk_chinese_ci">gbk</label> &nbsp;
	<input type="radio" name="database_character" value="gb2312_chinese_ci"  id="gb2312_chinese_ci"> <label for="gb2312_chinese_ci" title="gb2312_chinese_ci">gb2312</label> &nbsp;
	<input type="radio" name="database_character" value="big5_chinese_ci"  id="big5_chinese_ci"> <label for="big5_chinese_ci" title="big5_chinese_ci">big5</label> &nbsp;
	<input type="radio" name="database_character" value="latin1_general_ci"  id="latin1_general_ci"> <label for="latin1_general_ci" title="latin1_general_ci">latin1</label> &nbsp;
	<script>
	<?php if(isset($_POST['database_character'])) {?>
	G('<?php echo $_POST['database_character'];?>').checked = true;
	<?php }?>
	</script>
	</td>
	<td class="description">数据库使用的编码</td>
	</tr>
	<tr>
	<td>同时创建用户</td>
	<td><input type="checkbox" name="create_user"  id="create_user" checked="" onclick="this.blur();"> <label for="create_user">是 / 否</label> &nbsp;
	<?php if(isset($_POST['submit'])) { ?>
	<script>G('create_user').checked = <?php echo isset($_POST['create_user']) ? 'true' : 'false';?></script>
	<?php }?>
	</td>
	<td class="description">创建数据库同时创建相应用户</td>
	</tr>

	<tr class="user_tr none">
	<td>用户名</td>
	<td><input type="text" name="user_name" id="user_name" class="input_text" value="<?php echo isset($_POST['user_name']) ? $_POST['user_name'] : '';?>"></td>
	<td class="description">填写用户名字</td>
	</tr>

	<tr class="user_tr none">
	<td>用名密码</td>
	<td><input type="text" name="user_password" id="user_password" class="input_text" value="<?php echo isset($_POST['user_password']) ? $_POST['user_password'] : '';?>"> <br /><input type="button" name="create_password" id="create_password" value="生成密码" > </td>
	<td class="description">填写用名密码</td>
	</tr>
	<tr class="user_tr none">
	<td>允许链接来源地址</td>
	<td><input type="text" name="user_host" class="input_text" value="<?php echo isset($_POST['user_host']) ? $_POST['user_host'] : 'localhost';?>"></td>
	<td class="description"> localhost 或 127.0.0.1 只允许本地链接<br />
	% 即支持本地与远程链接
	</td>
	</tr>
	<tr class="user_tr none">
	<td>用户权限</td>
	<td style="padding:10px">
	<input type="checkbox" name="grant[]" value="grant_read" checked=""  id="grant_read" onclick="this.blur();"> <label for="grant_read">读数据</label> &nbsp;
	<input type="checkbox" name="grant[]" value="grant_write" checked=""  id="grant_write" onclick="this.blur();"> <label for="grant_write">写数据</label> &nbsp;
	<input type="checkbox" name="grant[]" value="grant_admin" checked=""  id="grant_admin" onclick="this.blur();"> <label for="grant_admin">管理</label> &nbsp;
	<input type="checkbox" name="grant[]" value="grant_all" checked=""  id="grant_all" onclick="this.blur();"> <label for="grant_all">全部权限</label> &nbsp;
	<script>
	<?php if(isset($_POST['submit'])) { 
			foreach (array('grant_read', 'grant_write', 'grant_admin', 'grant_all') as $val ) {
	?>
		G('<?php echo $val;?>').checked = <?php echo in_array($val, $_POST['grant']) ? 'true' : 'false';?>;
	<?php } }?>
	</script>
	</td>
	<td class="description">用户管理数据库的权限</td>
	</tr>
	</table>
<button type="submit" class="primary button" name="submit"><span class="check icon"></span>创建</button> 
</form>

</div>
<?php include('footer.php'); ?>
