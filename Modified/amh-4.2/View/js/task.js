
/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object Javascript 任务计划
 * Update:2013-11-01
 * 
 */

var amh_crontab_config = {
	'item' : {
		'minute':{'name':'分钟', 'min':0, 'max': 59, 'OnlyTxt':'分钟'},
		'hour':{'name':'小时', 'min':0, 'max': 23, 'OnlyTxt':'点钟'},
		'day':{'name':'天', 'min':1, 'max': 31, 'OnlyTxt':'号'},
		'month':{'name':'月', 'min':1, 'max': 12, 'OnlyTxt':'月'},
		'week':{'name':'星期段', 'min':0, 'max': 6, 'txt':['星期天','星期一','星期二','星期三','星期四','星期五','星期六']}
	}
}

var crontab_object = {};
window.onload = function ()
{
	var crontab = G('crontab');
	var crontab_line = [];
	var crontab_select = ['定时|*', '期间|-', '平均|/', '选择|,'];

	for (var k in amh_crontab_config.item )
	{
		var item = {};

		
		item.myname = C('span', {'innerHTML':amh_crontab_config.item[k].name + ': '});	// 名字
		item.myname.className = 'name';
		item.select = CreatesSelect(crontab_select);	// 选择
		item.select.name = k + '_select';

		var val = [];
		var val2 = [];
		val.push('*|*');
		for (var i=amh_crontab_config.item[k].min;  i<=amh_crontab_config.item[k].max; i++)
		{
			var txt = (amh_crontab_config.item[k].txt && amh_crontab_config.item[k].txt[i]) ? amh_crontab_config.item[k].txt[i] : i;
			var txt = (amh_crontab_config.item[k].OnlyTxt) ? i + amh_crontab_config.item[k].OnlyTxt : txt;
			val.push(txt + '|' + i);
			val2.push(txt + '|' + i);
		}
		
		// 定时
		item.time = CreatesSelect(val);
		item.time.name = k + '_time';
		
		// 期间
		item.period = C('font');
		item.period_start = CreatesSelect(val2);
		item.period_start.name = k + '_period_start';
		item.period_end = CreatesSelect(val2);
		item.period_end.name = k + '_period_end';
		C(item.period, 'In', [item.period_start, C('span', 'In', '到'), item.period_end, C('span', 'In', ' 之间每一' + amh_crontab_config.item[k].name)]);
		
		// 平均
		item.average = C('font');
		item.average_start = CreatesSelect(val2);
		item.average_start.name = k + '_average_start';
		item.average_end = CreatesSelect(val2);
		item.average_end.name = k + '_average_end';
		item.average_input = C('input', {'value':'1', 'name':k + '_average_input'});
		item.average_input.className = 'input_text average_input';
		C(item.average, 'In', [item.average_start, C('span', 'In', '到'), item.average_end, C('span', 'In', ' 之间平均 '), item.average_input, C('span', 'In', ' ' + amh_crontab_config.item[k].name + '一次')]);
		
		// 分别选择
		item.respectively = C(CreatesSelect(val2), {'multiple':'multiple'});
		item.respectively.className = 'respectively';
		item.respectively.size = 5;
		item.respectively.name = k + '_respectively[]';
		item.respectively.amh_name = k + '_respectively';

		item.html = C('div', 'In', [item.myname, item.select, item.time, item.period, item.average, item.respectively]);
		item.html.className = 'crontab_item';

		(function (item)
		{
			item.select.onchange = function ()
			{
				var change_dom = [item.time, item.period, item.period_start, item.period_end, item.average, item.average_start, item.average_end, item.average_input, item.respectively];
				for (var k in change_dom )
				{
					change_dom[k].style.display = 'none';
					change_dom[k].disabled = true;
				}
				if (this.value == '*') 
				{
					item.time.style.display = '';
					item.time.disabled = false;
				}
				else if(this.value == '-')
				{
					item.period.style.display = '';
					item.period.disabled = false;
					item.period_start.style.display = '';
					item.period_start.disabled = false;
					item.period_end.style.display = '';
					item.period_end.disabled = false;
				}
				else if(this.value == '/')
				{
					item.average.style.display = '';
					item.average.disabled = false;
					item.average_start.style.display = '';
					item.average_start.disabled = false;
					item.average_end.style.display = '';
					item.average_end.disabled = false;
					item.average_input.style.display = '';
					item.average_input.disabled = false;
				}
				else
				{
					item.respectively.style.display = '';
					item.respectively.disabled = false;
				}
				
				
				
			}
		})(item);
		item.select.onchange();
		
		crontab_line.push(item.html);
		crontab_object[k] = item;
	}

	C(crontab, 'In',  crontab_line);

}