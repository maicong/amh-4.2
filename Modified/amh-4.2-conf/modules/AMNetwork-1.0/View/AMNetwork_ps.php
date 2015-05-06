<?php include('header.php'); ?>	
<div id="body">
<?php include('AMNetwork_category.php'); ?>

<style>
#ps_list_pre {
	width: 1100px;
	overflow-x: scroll;
	padding-bottom: 10px;
}
</style>

<p>当前系统运行程序进程</p>
<pre id="ps_list_pre">
<?php
print_r($ps_list);
?>
</pre>

</div>
<?php include('footer.php'); ?>	
