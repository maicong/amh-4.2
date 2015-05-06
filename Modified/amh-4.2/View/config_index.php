<?php include('header.php'); ?>

<div id="body">
<?php 
$c_name = 'config';
include('category_list.php'); 
?>

<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>
<p>面板配置更改:</p>
<form action="" method="POST"  id="account">
<table border="0" cellspacing="1"  id="STable" style="width:660px;">
	<tr>
	<th> &nbsp; </th>
	<th>值</th>
	<th>更新时间 / 说明</th>
	</tr>
	<tr><td>面板允许访问域名/IP
	</td>
	<td>
	<select name="AMHDomain" id="AMHDomain" style="width:190px">
	<?php 
		foreach ($amh_domain_list as $key=>$val) {
		if(!empty($val) && strpos($val, '*') === false) {
	?>
		<option value="<?php echo $val;?>"><?php echo $val;?></option>
	<?php } } ?>
		<option value="Off">无限制</option>
	</select>
	<script>G('AMHDomain').value = '<?php echo $amh_config['AMHDomain']['config_value'];?>';</script>
	<input type="hidden" name="AMHDomain_old"  value="<?php echo $amh_config['AMHDomain']['config_value'];?>" />
	</td>
	<td><?php echo $amh_config['AMHDomain']['config_time'];?>
	<div style="color:#848484;margin:5px;">(指定一域名或IP访问面板)</div>
	</td>
	</tr>
	<tr><td>设置面板访问端口
	</td>
	<td>
	<input type="text" name="AMHListen" class="input_text" value="<?php echo $amh_config['AMHListen']['config_value'];?>" />
	<input type="hidden" name="AMHListen_old"  value="<?php echo $amh_config['AMHListen']['config_value'];?>" />
	</td>
	<td><?php echo $amh_config['AMHListen']['config_time'];?>
	<div style="color:#848484;margin:5px;">(避免端口占用 请设置大于5555 小于61000)</div>
	</td>
	</tr>
	<tr><td>登录出错次数限制 </td>
	<td><input type="text" name="LoginErrorLimit" class="input_text" value="<?php echo $amh_config['LoginErrorLimit']['config_value'];?>" />
	<input type="hidden" name="LoginErrorLimit_old"  value="<?php echo $amh_config['LoginErrorLimit']['config_value'];?>" />
	</td>
	<td><?php echo $amh_config['LoginErrorLimit']['config_time'];?> </td>
	</tr>
	<tr><td>是否开启CSRF防范 </td>
	<td>
	<input type="checkbox" name="OpenCSRF" <?php echo ($amh_config['OpenCSRF']['config_value'] == 'on' ) ? 'checked=""' : '';?> style="margin:6px;"/>
	<input type="hidden" name="OpenCSRF_old"  value="<?php echo $amh_config['OpenCSRF']['config_value'];?>" />
	</td>
	<td><?php echo $amh_config['OpenCSRF']['config_time'];?> 
	<div style="color:#848484;margin:5px;">(有效防范外部非法请求面板)</div>
	</td>
	</tr>
	<tr><td>登录是否开启验证码 </td>
	<td>
	<input type="checkbox" name="VerifyCode" <?php echo ($amh_config['VerifyCode']['config_value'] == 'on' ) ? 'checked=""' : '';?> style="margin:6px;"/>
	<input type="hidden" name="VerifyCode_old"  value="<?php echo $amh_config['VerifyCode']['config_value'];?>" />
	</td>
	<td><?php echo $amh_config['VerifyCode']['config_time'];?> </td>
	</tr>
	<tr><td>是否显示版块说明 </td>
	<td>
	<input type="checkbox" name="HelpDoc" <?php echo ($amh_config['HelpDoc']['config_value'] == 'on' ) ? 'checked=""' : '';?> style="margin:6px;"/>
	<input type="hidden" name="HelpDoc_old"  value="<?php echo $amh_config['HelpDoc']['config_value'];?>" />
	</td>
	<td><?php echo $amh_config['HelpDoc']['config_time'];?> </td>
	</tr>
	<tr><td>面板数据私有保护 </td>
	<td>
	<?php echo ($amh_config['DataPrivate']['config_value'] == 'on' ? '已开启' : '未开启');?> 
	</td>
	<td><?php echo $amh_config['DataPrivate']['config_time'];?> 
	<div style="color:#848484;margin:5px;">( 数据库amh » amh_config » DataPrivate <br />有效值 on/Off，更改后重新登录生效。)</div>
	</td>
	</tr>
	<tr><td>是否开启导航菜单</td>
	<td>
	<input type="checkbox" name="OpenMenu" <?php echo ($amh_config['OpenMenu']['config_value'] == 'on' ) ? 'checked=""' : '';?> style="margin:6px;"/>
	<input type="hidden" name="OpenMenu_old"  value="<?php echo $amh_config['OpenMenu']['config_value'];?>" />
	</td>
	<td><?php echo $amh_config['OpenMenu']['config_time'];?> 
	</td>
	</tr>
	</table>
<button type="submit" class="primary button" name="submit"><span class="check icon"></span>保存</button> 
</form>

</div>
<?php include('footer.php'); ?>