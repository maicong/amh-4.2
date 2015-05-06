<?php include('header.php'); ?>	
<div id="body">
<?php include('AMNetwork_category.php'); ?>

<style>
#iptables_block {
	width:1100px;
}
#iptables_list_pre {
	margin-left:20px;
	width:500px;
	float:left;
}
#iptables_list_pre h3 {
	color: #82879B;
	font-size: 19px;
}
#iptables_list_pre font {
	border-bottom:1px dashed ;
}

#iptables_form {
	float:left;
}
#iptables_val {
	width:500px;
	height:450px;
	display:block;
	font-family: 宋体;
}
</style>
<div id="iptables_block">
<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>

<p>防火墙 IPTABLES 配置:</p>
<form action="/index.php?c=AMNetwork&a=AMNetwork_iptables" method="POST" id="iptables_form">
<textarea name="iptables_val" id="iptables_val">
<?php
print_r($iptables_list);
?>
</textarea>
<br />
<input type="submit" value="保存" name="save_iptables"/>
</form>

<pre id="iptables_list_pre">
<?php
$iptables_list_pre = $iptables_list;
$iptables_list_pre = str_replace("*nat\n", "<h3><b>*nat #地址转换</b></h3>", $iptables_list_pre);
$iptables_list_pre = str_replace("*filter\n", "<h3><b>*filter #数据包过滤</b></h3>", $iptables_list_pre);
$iptables_list_pre = str_replace("*mangle\n", "<h3><b>*mangle #数据包重构</b></h3>", $iptables_list_pre);
$iptables_list_pre = str_replace("*raw\n", "<h3><b>*raw #路径跟踪</b></h3>", $iptables_list_pre);

$iptables_list_pre = str_replace("-j DROP", "-j <font color='red' title='阻止'>DROP</font>", $iptables_list_pre);
$iptables_list_pre = str_replace("-j ACCEPT", "-j <font color='green' title='通过'>ACCEPT</font>", $iptables_list_pre);

print_r($iptables_list_pre);
?>
</pre>

<div style="clear:both;"></div>
</div>
</div>

<?php include('footer.php'); ?>	
