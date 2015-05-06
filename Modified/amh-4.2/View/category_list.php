<?php !defined('_Amysql') && exit; ?>

<h2>AMH » <?php echo isset($CategoryList[$c_name]) ? $CategoryList[$c_name]['en'] : '';?></h2>
<?php
	if (isset($CategoryList[$c_name]['son']) && count($CategoryList[$c_name]['son']) > 0)
	{
?>
	<div id="category">
	<?php
		foreach ($CategoryList[$c_name]['son'] as $key=>$val)
		{
			if (!isset($val['OnlyMenu']))
			{
	?>
		<a href="<?php echo $val['url'];?>" id="<?php echo $val['id'];?>" <?php echo isset($val['target']) ? "target='{$val['target']}'" : '';?> ><?php echo $val['name'];?></a>
	<?php
			}
		}
	?>
	<script>
	var action = '<?php echo $_GET['a'];?>';
	var action_dom = G(action) ? G(action) : G('<?php echo $CategoryList[$c_name]['son'][0]['id'];?>');
	action_dom.className = 'activ';
	</script>
	</div>
<?php
	}
?>
