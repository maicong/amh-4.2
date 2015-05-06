<?php !defined('_Amysql') && exit; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo isset($title) ? $title : 'AMH';?></title>
<base href="<?php echo _Http;?>" /> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="View/css/index.css" />
<link type="text/css" rel="stylesheet" href="View/css/buttons.css" />
</head>
<body>
	<div id="login">
		<div id="header" style="width:auto;text-align:center">
			<a href="index.php" class="logo" style="margin-left:0px;"></a>
			<div style="clear:both"></div>
				<div id="navigation" style="padding:15px">
				<font style="font-size: 12px;margin-left:0px;float:none">专注LNMP / Nginx 虚拟主机面板 - AMH</font>
				</div>		
		</div>
		
		<form id="LoginForm" action="index.php?c=index&a=login" method="POST" autocomplete="off" style="width:230px;margin:0px auto;">
		<?php
			if (isset($LoginError)) echo '<div style="margin:18px auto;"><p id="error">' . $LoginError . '</p></div>';
		?>			
			<p>
				<dl><dt id="UserDom">管理员账号</dt><dd><input type="text" name="user" class="input_text" value="<?php echo isset($_POST['user']) ? $_POST['user'] : '';?>" / ></dd></dl>
				<dl><dt id="PassDom">管理员密码</dt><dd><input type="password" name="password" class="input_text" value="<?php echo isset($_POST['password']) ? $_POST['password'] : '';?>"/ ></dd></dl>
				
				<?php if ($amh_config['VerifyCode']['config_value'] == 'on') { ?>
				<dl><dt><a name="location_code"></a>验证码</dt>
				<dd><input type="text" name="VerifyCode" id="code" class="input_text" style="float:left;width:60px;margin-right:4px;"/>
				<img id="code_img" src="./index.php?c=VerifyCode" onclick="this.src='/index.php?c=VerifyCode&?'+Math.random();" /> <div style="clear:both;"></div></dd></dl>
				<?php } ?>

				<dl><dd id="login_submit"><button name="login" class="primary button" type="submit"  id="SubmitDom"> 登录 </button>
				</dd></dl>
			</p>

			<p style="margin:20px 0px;text-align:left">Powered by <a href="http://amysql.com/" target="_blank">amysql.com</a></p>
		</form>
		
	</div>
</body>
</html>
