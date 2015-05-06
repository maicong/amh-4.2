
/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Javascript 模块列表
 * Update:2013-11-01
 * 
 */

window.onload = function ()
{
	// 评分星
	var stars_arr = getElementByClassName('stars', 'a');
	for (var k in stars_arr )
	{
		(function (obj)
		{
			obj.fonts = obj.getElementsByTagName('font')[0];
			obj.spans = obj.getElementsByTagName('span')[0];
			obj.onmousemove = function (e)
			{
				var e1 = e || window.event;
				var val = e1.clientX - obj.offsetLeft - 55;
				obj.fonts.style.width = val + 'px';
				if(val > 0 && val < 101)
				{
					obj.spans.innerHTML = '点击评价: ' + val + '分';
					obj.val = val;
				}
			}
			obj.onmouseout = function ()
			{
				if (obj.fonts.getAttribute('name') != '')
				{
					obj.fonts.style.width = obj.fonts.getAttribute('name') + 'px';
					obj.spans.innerHTML = '';
				}
			}
			obj.onclick = function ()
			{
				obj.fonts.setAttribute('name', '');
				obj.onmousemove = null;
				Ajax.get('/index.php?c=module&a=module_list&fraction=' + obj.name + '&val=' + obj.val + '&tag=' + Math.random(),function (msg){
					obj.getElementsByTagName('span')[0].innerHTML = (msg == '0' ? '您已评价。 ' : '评价成功: ' + obj.val + '分');
					obj.onclick = null;
				}, false, true)
			}
			
		})(stars_arr[k]);
	}

	// 模块
	var item_arr = getElementByClassName('item', 'div');
	var temp_obj;	// 避免冒泡
	var t;
	for (var k in item_arr)
	{
		(function (obj)
		{
			obj.onmouseover = function ()
			{
				if(temp_obj == obj) clearTimeout(t);
				obj.className = 'item_hover';
			}

			obj.onmouseout = function ()
			{
				temp_obj = obj;
				t = setTimeout(function ()
				{
					obj.className = 'item';
				}, 100)
			}
		})(item_arr[k])
	}
}