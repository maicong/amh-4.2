<?php include('header.php'); ?>

<div id="body">
<?php 
$c_name = 'account';
include('category_list.php'); 
?>

<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>
<p>更改账号密码:</p>
<form action="index.php?c=account&a=account_pass" method="POST"  id="account"  autocomplete="off">
<table border="0" cellspacing="1"  id="STable" style="width:300px;">
	<tr>
	<th> &nbsp; </th>
	<th>值</th>
	</tr>
	<tr><td>旧密码</td>
	<td><input type="password" name="user_password" class="input_text" value="<?php echo isset($_POST['user_password']) ? $_POST['user_password'] : '';?>" /></td>
	</tr>
	<tr><td>新密码</td>
	<td><input type="password" name="new_user_password" class="input_text" value="<?php echo isset($_POST['user_password']) ? $_POST['new_user_password'] : '';?>" /></td>
	</tr>
	<tr><td>确认新密码</td>
	<td><input type="password" name="new_user_password2" class="input_text"  value="<?php echo isset($_POST['user_password']) ? $_POST['new_user_password2'] : '';?>" /></td>
	</tr>
</table>
<button type="submit" class="primary button" name="submit"><span class="check icon"></span>更改</button> 
</form>


</div>
<?php include('footer.php'); ?>