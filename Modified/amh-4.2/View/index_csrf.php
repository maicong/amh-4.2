<?php include('header.php'); ?>

<div id="body">
<h2>AMH » CSRF</h2>
<br />

<div style="line-height:22px;">
当前amh系统已开启CSRF攻击防范(面板设置)，<br />
您当前请求数据验证失败，已被拒绝。<br />
<br />
问题发生可能：<br />
1) 外部非法请求，amh_token参数错误。<br />
2) 请求缺少amh_token参数。<br />
3) 面板页面Javascript 脚本未加载完成时发起请求，amh_token数据未载入。
<br /><br />

您可以尝试：<button type="button" class="primary button" onclick="WindowLocation('<?php echo $_SESSION['CSRF_Url'];?>');"><span class="check icon"></span>恢复当前服务</button>


</div>
</div>
<?php include('footer.php'); ?>
