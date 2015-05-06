
/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Javascript 主机
 * Update:2013-11-01
 * 
 */

window.onload = function ()
{
	var host_edit_dom = G('host_edit');
	var host_domain_dom = G('host_domain');
	var host_root_dom = G('host_root');
	var host_log_dom = G('host_log');

	var min_spare_servers_dom = G('min_spare_servers');
	var start_servers_dom = G('start_servers');
	var max_spare_servers_dom = G('max_spare_servers');
	var max_children_dom = G('max_children');

	var php_fpm_pm_dom = G('php_fpm_pm');
	var php_fpm_arr = [min_spare_servers_dom, start_servers_dom, max_spare_servers_dom, max_children_dom];
	var php_fpm_click_name = '';
	var php_fpm_val_tmp = [];


	// 路径显示
	host_domain_dom.onkeyup = function ()
	{
		var v = (this.value == '') ? '主标识域名' : this.value;
		host_root_dom.innerHTML = v;
		host_log_dom.innerHTML = v;

	}
	host_domain_dom.onkeyup();

	// PHP-FPM
	var php_fpm_run = function ()
	{
		for (var k in php_fpm_arr)
		{
			if (php_fpm_arr[k].name == php_fpm_click_name)
			{
				k = parseInt(k);
				var notice = '';
				if(php_fpm_arr[k+1] && parseInt(php_fpm_arr[k].value) > parseInt(php_fpm_arr[k+1].value))
					notice = php_fpm_arr[k].title + '(' + php_fpm_arr[k].value + ')必须小于或等于' + php_fpm_arr[k+1].title + '(' + php_fpm_arr[k+1].value + ')';
					
				if(php_fpm_arr[k-1] && parseInt(php_fpm_arr[k].value) < parseInt(php_fpm_arr[k-1].value))
					notice = php_fpm_arr[k].title + '(' + php_fpm_arr[k].value + ')必须大于或等于' + php_fpm_arr[k-1].title + '(' + php_fpm_arr[k-1].value + ')';
				
				if (parseInt(php_fpm_arr[k].value) == 0)
					notice = php_fpm_arr[k].title + '(' + php_fpm_arr[k].value + ')必须大于0';

				if (notice != '')
				{
					alert(notice);
					php_fpm_arr[k].value = php_fpm_val_tmp[k];
					break;
				}
			}
			php_fpm_val_tmp[k] = php_fpm_arr[k].value;
		}
	}
	for (var k in php_fpm_arr)
	{
		php_fpm_arr[k].onblur = php_fpm_run;
		php_fpm_arr[k].onchange = function ()
		{
			php_fpm_click_name = this.name;
		}
	}
	php_fpm_run();



	// 选择动静态方式
	php_fpm_pm_dom.onchange = function ()
	{
		var disabled = (this.value == 'static') ? true : false;
		var className = (this.value == 'static') ? 'input_text disabled' : 'input_text';
		for (var k in php_fpm_arr)
		{
			if (php_fpm_arr[k] != max_children_dom)
			{
				php_fpm_arr[k].disabled = disabled;
				php_fpm_arr[k].className = className;
			}
		}
	}
	php_fpm_pm_dom.onchange();

	// 提交取消disabled
	host_edit_dom.onsubmit = function ()
	{
		for (var k in php_fpm_arr)
			php_fpm_arr[k].disabled = false;
		return true;
	}

}