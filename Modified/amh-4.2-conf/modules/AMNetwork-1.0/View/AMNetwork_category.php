<?php !defined('_Amysql') && exit; ?>
<style>
#AMNetwork_list input.input_text {
	width: 292px;
}
#AMNetwork_list textarea {
	display:inline;
}
</style>
<script>
if(!WindowLocation)
{
	var WindowLocation = function (url)
	{
		window.location = url;
	}
	var WindowOpen = function (url)
	{
		window.open(url);
	}
}
</script>
<h2>AMH » AMNetwork</h2>
<div id="category">
<a href="index.php?c=AMNetwork&a=AMNetwork_netstat" id="AMNetwork_netstat">网络连接</a>
<a href="index.php?c=AMNetwork&a=AMNetwork_ps" id="AMNetwork_ps">系统进程</a>
<a href="index.php?c=AMNetwork&a=AMNetwork_iptables" id="AMNetwork_iptables" >防火墙</a>
<script>
var action = '<?php echo $_GET['a'];?>';
var action_dom = G(action) ? G(action) : G('AMNetwork_netstat');
action_dom.className = 'activ';
</script>
</div>