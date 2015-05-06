<?php include('header.php'); ?>	
<div id="body">
<?php include('AMNetwork_category.php'); ?>

<style>
#netstat_list_pre b {
	display:block;
	font-size:14px;
	color:#82879B;
}
</style>

<pre id="netstat_list_pre">
<?php
$netstat_list = str_replace("Active Internet connections (servers and established)", "<b>当前网络连接数据 Active Internet connections (servers and established)</b>", $netstat_list);
$netstat_list = str_replace("Active UNIX domain sockets (servers and established)", "<br /> <b>当前网络通讯数据 Active UNIX domain sockets (servers and established)</b>", $netstat_list);
print_r($netstat_list);
?>
</pre>

</div>
<?php include('footer.php'); ?>	
