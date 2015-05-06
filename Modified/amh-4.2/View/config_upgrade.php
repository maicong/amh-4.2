<?php include('header.php'); ?>

<div id="body">
<?php 
$c_name = 'config';
include('category_list.php'); 
?>

<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>
<p>在线升级&程序更新:</p>
<table border="0" cellspacing="1"  id="STable" style="width:1000px;">
	<tr>
	<th width="130">名称</th>
	<th width="60">级别</th>
	<th width="550">升级 & 更新描述</th>
	<th width="100">作者 & 发布时间</th>
	<th width="120">操作</th>
	</tr>
	<?php 
	if(!is_array($upgrade_list) || count($upgrade_list) < 1)
	{
	?>
		<tr><td colspan="5" style="padding:10px;">目前暂无程序更新数据</td></tr>
	<?php	
	}
	else
	{
		foreach ($upgrade_list as $key=>$val)
		{
	?>
			<tr>
				<td><?php echo $val['AMH-UpgradeName'];?></td>
				<td style="color:<?php echo $val['AMH-UpgradeGradeColor'];?>"><b><?php echo $val['AMH-UpgradeGradeCN'];?></b></td>
				<td class="description_block"><?php echo $val['AMH-UpgradeDescription'];?>
				<br />查看详情: <a href="<?php echo $val['AMH-UpgradeUrl'];?>" target="_blank"><?php echo $val['AMH-UpgradeUrl'];?></a>
				</td>
				<td><?php echo $val['AMH-UpgradeScriptBy'];?> 
				<br /> <i><?php echo $val['AMH-UpgradeDate'];?> </i>
				</td>
				<td>
				<?php if ($val['AMH-UpgradeAvailableStatus'] == 'false') { ?>
					<button type="button" class="primary button" disabled >不可用</button>
				<?php }elseif ($val['AMH-UpgradeInstallStatus'] == 'true') { ?>
					<button type="button" class="primary button" disabled >已更新</button>
				<?php } else {?>
					<button type="button" class="primary button" onclick="return (confirm('确认安装更新：<?php echo $val['AMH-UpgradeName'];?> 吗?') && (WindowLocation('/index.php?c=config&a=config_upgrade&install=<?php echo $val['AMH-UpgradeName'];?>')) && (this.innerHTML=' 请稍等...') && (this.disabled=true))" > 安装更新</button>
				<?php }?>
				</td>
			</tr>
	<?php
		}
	}
	?>
</table>


<div id="notice_message" style="width:700px;">
<h3>» WEB 在线升级</h3>
1) 每一次升级更新前，请您先查看升级详情信息，阅读官方网站发布的升级说明。<br />
2) 升级更新级别分别有：较低、一般、重要。可选择性更新升级。<br />
3) 更新程序如果存在上下依赖性(如，需完成旧的更新)或是当前存在冲突或不兼容情况，这一更新可能会处于"不可用"状态。<br />

<h3>» SSH Upgrade</h3>
1) 有步骤提示操作: <br />
ssh执行命令: amh upgrade <br />
然后选择对应的更新名称进行安装升级。<br />
2) 或直接操作: <br />
<ul>
<li>更新列表: amh upgrade list</li>
<li>更新说明: amh upgrade [更新名称] info</li>
<li>安装升级: amh upgrade [更新名称] install</li>
<li>安装状态: amh upgrade [更新名称] install_status</li>
<li>可用状态: amh upgrade [更新名称] available_status</li>
</ul>
</div>
</div>
<script>
upgrade_notice();
</script>
<?php include('footer.php'); ?>