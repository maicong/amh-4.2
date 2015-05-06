<?php include('header.php'); ?>

<div id="body" style="height:535px;">
<?php 
$c_name = 'backup';
include('category_list.php'); 
?>
<div style="width:500px;"><div>
<p id="ing_status" style="margin:0px">请稍候，面板数据一键还原中：</p>
</div>
</div>

</div>
<input type="button" value="还原中，请稍候…" disabled="" id="revert_ing_button" class="cmd_ing_button" />
<?php include('footer.php'); ?>