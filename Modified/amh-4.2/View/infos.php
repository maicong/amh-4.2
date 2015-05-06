<?php !defined('_Amysql') && exit; ?>

<?php
	preg_match("/Linux:(.*)\n/", $infos, $Linux);
	preg_match("/UpTime:(.*)\n/", $infos, $UpTime);
	preg_match("/\nTime:(.*)\n/", $infos, $Time);
	preg_match("/LA:(.*)\n/", $infos, $LA);
	preg_match("/IP:(.*)\n/", $infos, $IP);
	preg_match("/CPU:(.*)\n/", $infos, $CPU);
	preg_match("/RAM:(.*)\n/", $infos, $RAM);
	preg_match("/HD:(.*)/", $infos, $HD);
	
	$os = (stripos($Linux[1], 'centos') !== false) ? 'centos' : ((stripos($Linux[1], 'debian') !== false) ? 'debian' : 'ubuntu');


	$ram_arr = explode('/', str_replace('MB', '', $RAM[1]));
	$ram_percentage = $ram_arr[0]/$ram_arr[1]*100;
	$ram_color = ($ram_percentage > 80) ? 'red' : 'green';

	$hd_arr = explode('/', str_replace('GB', '', $HD[1]));
	$hd_percentage = $hd_arr[0]/$hd_arr[1]*100;
	$hd_color = ($hd_percentage > 80) ? 'red' : 'green';

	$cpu_arr = explode('%us,', $CPU[1]);
	$cpu_arr[0] = $cpu_arr[0] + 0.01;
	$cpu_arr[0] = ($cpu_arr[0] > 99) ? 99 : $cpu_arr[0];
	$cpu_color = ($cpu_arr[0] > 80) ? 'red' : 'green';
?>
<p class="ico <?php echo $os;?>"><font>操作系统 </font> <?php echo $Linux[1];?></p>
<p class="ico online"><font>运行时间 </font>  <?php echo $UpTime[1];?></p>
<p class="ico time"><font>系统时间 </font> <?php echo $Time[1];?></p>
<p class="ico load"><font>系统负载 </font> <?php echo $LA[1];?></p>
<p class="ico ip"><font>IP地址 </font> 
<a title="点击隐藏/显示" onclick="hide_ip(this);" href="javascript:;"><?php echo $IP[1];?></a>
</p>
<p class="ico cpu"><font>CPU </font> <span class="lines <?php echo $cpu_color;?>"><span class="lines_val" style="width:<?php echo $cpu_arr[0];?>%;"></span><span class="lines_txt"><?php echo $cpu_arr[0];?>%</span></span>
<span class="cpu_info"><?php echo $cpu_arr[1];?></span>
<span style="clear:both"></span>
</p>
<p class="ico ram"><font>内存 </font> <span class="lines <?php echo $ram_color;?>"><span class="lines_val" style="width:<?php echo $ram_percentage;?>%;"></span><span class="lines_txt"><?php echo $RAM[1];?></span></span></p>
<p class="ico hd"><font>硬盘 </font> <span class="lines <?php echo $hd_color;?>"><span class="lines_val" style="width:<?php echo $hd_percentage;?>%;"></span><span class="lines_txt"><?php echo $HD[1];?></span></span></p>

