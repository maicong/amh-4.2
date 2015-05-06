<?php include('header.php'); ?>

<div id="body" style="height:535px;">
<?php 
$c_name = 'config';
include('category_list.php'); 
?>

<div style="width:500px;"><div>
<p id="ing_status" style="margin:0px"><?php echo "{$UpgradeName}";?> 更新升级进行中：</p>
</div>
</div>

</div>
<input type="button" value="更新中，请稍候…" disabled="" id="upgrade_ing_button" class="cmd_ing_button" />
<script>
UpgradeName = <?php echo json_encode($UpgradeName);?>;
</script>
<?php include('footer.php'); ?>