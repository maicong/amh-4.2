<?php include('header.php'); ?>

<div id="body" style="height:535px;">
<?php 
$c_name = 'backup';
include('category_list.php'); 
?>
<div style="width:500px;"><div>
<p id="ing_status" style="margin:0px">请稍候，面板数据备份中：</p>
</div>
</div>

</div>
<input type="button" value="备份中，请稍候…" disabled="" id="backup_ing_button" class="cmd_ing_button" />
<?php include('footer.php'); ?>