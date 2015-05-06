<?php 
	!defined('_Amysql') && exit; 
	include('category_list_data.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo isset($title) ? $title : 'AMH';?></title>
<base href="<?php echo _Http;?>" /> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn">
<link type="text/css" rel="stylesheet" href="View/css/index.css" />
<link type="text/css" rel="stylesheet" href="View/css/buttons.css" />
<script src="View/js/index.js"></script>
<style>
<?php if($_SESSION['amh_config']['HelpDoc']['config_value'] == 'no') { ?>
#notice_message {display:none;}
<?php }?>
</style>

<script>
var HTTP_HOST = '<?php echo $_SERVER['HTTP_HOST'];?>';
var amh_token = '<?php echo $_SESSION['amh_token'];?>';
var OpenCSRF = '<?php echo $_SESSION['amh_config']['OpenCSRF']['config_value'];?>';
var OpenMenu = '<?php echo $_SESSION['amh_config']['OpenMenu']['config_value'];?>';
</script>
</head>
<body>
<div id="header">
<a href="index.php" class="logo"></a>

<?php if(!empty($_SESSION['amh_config']['UpgradeSum']['config_value'])) { ?>
<a href="/index.php?c=config&a=config_upgrade" id="upgrade_notice">您现在有<?php echo $_SESSION['amh_config']['UpgradeSum']['config_value'];?>个更新</a>
<?php }?>

<div id="navigation">
	<font>Hi, <?php echo $_SESSION['amh_user_name'];?></font>
	<ul id="navigation_ul">
	<?php
		foreach ($CategoryList as $key=>$val)
		{
	?>
		<li>
			<div class="navigation_top">
				<a href="<?php echo $val['url'];?>" id="<?php echo $val['id'];?>"><?php echo $val['name'];?></a>
			</div>
			<?php if ($_SESSION['amh_config']['OpenMenu']['config_value'] == 'on' && is_array($val['son']) && count($val['son']) > 0) {?>
			<div id="<?php echo $val['id'];?>_ul" class="navigation_bottom">
				<?php foreach ($val['son'] as $k=>$v) { ?>
					<a href="<?php echo $v['url'];?>" class="<?php echo isset($v['class']) ? $v['class'] : '';?>" id="<?php echo isset($v['id']) ? "{$v['id']}_li" : '';?>" <?php echo isset($v['target']) ? "target='{$v['target']}'" : '';?>><em id="<?php echo $v['id'];?>_em"></em><span id="<?php echo $v['id'];?>_span"><?php echo $v['name'];?></span></a>
				<?php } ?>
			</div>
			<?php } ?>
		</li>
	<?php
		}
	?>
	</ul>
</div>
<?php $action_name = (!isset($_GET['c']) || in_array($_GET['c'], array('index', 'host', 'mysql', 'ftp', 'backup', 'task', 'account', 'config'))) ? $_GET['c'] : 'module';?>
<script>
var action = '<?php echo $action_name;?>';
var action_dom = G(action) ? G(action) : G('home');
action_dom.className = 'activ';
</script>
</div>
