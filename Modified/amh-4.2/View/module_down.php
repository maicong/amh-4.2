<?php include('header.php'); ?>
<style>
#STable td.module_name {
	font-size:14px;
	padding:12px 0px;
}
</style>
<div id="body">
<?php 
$c_name = 'module';
include('category_list.php'); 
?>


<p>下载新的模块程序:</p>
<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>
<div id="module_down">
<form action="/index.php" method="GET" />
<input type="hidden" name="c" value="module"/>
<input type="hidden" name="a" value="module_down"/>
搜索模块 
<select name="search_type" id="search_type" style="width:112px">
<option value="search_module_name">模块信息</option>
<option value="search_module_by">模块作者</option>
</select>
<script>
<?php echo (isset($_GET['search_type']) && !empty($_GET['search_type'])) ? "G('search_type').value = " . json_encode($_GET['search_type']) : '';?>;
</script>
<input type="text" class="input_text" name="m_txt" value="<?php echo isset($_GET['m_txt']) ? $_GET['m_txt'] : '';?>" style="width:251px"/>&nbsp;
<button type="submit" name="download_search" class="primary button"> 搜索 </button> &nbsp; <span style="color:#8F8F8F">( 支持模糊搜索 )</span>
</form>
</div>
<table border="0" cellspacing="1"  id="STable" style="width:1000px;">
	<tr>
	<th width="230">模块名称 & 评分</th>
	<th width="580">模块描述</th>
	<th width="120">模块开发者</th>
	<th width="120">操作</th>
	</tr>
	<?php 
	if(!is_array($new_module_list['data']) || count($new_module_list['data']) < 1)
	{
	?>
		<tr><td colspan="5" style="padding:10px;">没找到新模块扩展下载</td></tr>
	<?php	
	}
	else
	{
		foreach ($new_module_list['data'] as $key=>$val)
		{
			$fraction = number_format($val['module_stars'] / $val['module_starts_sum'], 2)
	?>
			<tr>
				<td class="module_name">
				<div>
					<img src="<?php echo !empty($val['module_ico']) ? $val['module_ico'] : '/View/images/module.gif';?>" /> <br />
					<?php echo $val['module_name'];?>
				</div>
				<div class="stars" title="得分<?php echo $fraction;?> 总<?php echo $val['module_starts_sum'];?>次评价">
					<div class="stars_val" style="width:<?php echo $fraction;?>px">
					</div>
				</div>
				</td>
				<td class="description_block">
				<?php echo $val['module_description'];?>
				<br /> 模块开发者网站: <a href="http://<?php echo $val['module_website'];?>" target="_blank"><?php echo $val['module_website'];?></a>
				</td>
				<td><?php echo $val['module_by'];?><br />
				<i><?php echo $val['module_time'];?></i>
				</td>
				<td>
				<?php if($val['module_download'] == 'y') {?>
					<button type="button" class="primary button" disabled=""> 已下载 </button>
				<?php } else {?>
					<button type="button" class="primary button" onclick="return (confirm('确认下载：<?php echo $val['module_name'];?> 模块吗?') && (window.location = '/index.php?<?php echo $_SERVER['QUERY_STRING'];?>&module_name=<?php echo $val['module_name'];?>') && (this.innerHTML=' 请稍等...') && (this.disabled=true))" > 下载 </button>
				<?php } ?>
				</td>
			</tr>
	<?php
		}
	}
	?>
</table>
<div id="page_list">总<?php echo $total_page;?>页 - 在线共<?php echo $new_module_list['sum'];?>个模块扩展 » 页码 <?php echo htmlspecialchars_decode($page_list);?> </div>



<div id="notice_message" style="width:460px;line-height:25px">
<h3>» Download Module</h3>
1) 所有发布的模块都已经过官方审核，可以输入模块名字直接搜索下载。 <br />
2) 模块脚本保存目录：/root/amh/modules <br />
3) 支持用户创建编写新的功能模块，您也可以把模块提交给我们，<br />
审核通过后将列入官方下载列表或会收录为默认安装模块提供给用户使用，<br />
模块编程规范请查阅官方论坛文档。<br />
4) 更多丰富功能模块与模块开发交流请登录AMH官方论坛。<br />
</div>

</div>
<?php include('footer.php'); ?>