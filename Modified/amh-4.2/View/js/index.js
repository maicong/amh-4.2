
/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Javascript 面板常用函数
 * Update:2013-11-01
 * 
 */

// 创建元素
var C = function (tag, attr, CssOrHtml)
{
	var o = (typeof(tag) != 'object') ? document.createElement(tag) : tag;
	if (attr == 'In')
	{
		if(CssOrHtml  && typeof(CssOrHtml) == 'object') 
		{
			if(CssOrHtml.length > 1 && CssOrHtml.constructor == Array )
			{
				for (x in CssOrHtml)
					if(CssOrHtml[x]) o.appendChild(CssOrHtml[x]);
			}
			else
			    o.appendChild(CssOrHtml);
		}
		else
			o.innerHTML = CssOrHtml;
		return o;
	}

	if (typeof(attr) == 'object')
	{
		for (k in attr )
			if(attr[k] != '') o[k] = attr[k];
	}

	if (typeof(CssOrHtml) == 'object')
	{
	    for (k in CssOrHtml )
			if(CssOrHtml[k] != '') o.style[k] = CssOrHtml[k];
	}
	return o;
}
// 取得元素
var G = function (id) {return document.getElementById(id); }

// 获取class元素
var getElementByClassName = function (cls,elm) 
{  
	var arrCls = new Array();  
	var seeElm = elm;  
	var rexCls = new RegExp('(|\\\\s)' + cls + '(\\\\s|)','i');  
	var lisElm = document.getElementsByTagName(seeElm);  
	for (var i=0; i<lisElm.length; i++ ) 
	{  
		var evaCls = lisElm[i].className;  
		if(evaCls.length > 0 && (evaCls == cls || rexCls.test(evaCls))) 
			arrCls.push(lisElm[i]);  
	}  
	return arrCls;  
}
// 生成下拉框
function CreatesSelect (arr, name)
{
	if(typeof(name) != 'string') name = '';
	name = name.toUpperCase();
	var selected = false;
	var S = C('select');
	for (var x in arr )
	{
		if (typeof(arr[x]) != 'object')
		{
			var O = C('option');
			S.options.add(O);
			var arr_split = arr[x].split('|');
			if(arr_split.length > 1)
			{
				O.text = arr_split[0];
				O.value = arr_split[1];
			}
			else
				O.text = O.value = arr[x];

			if(O.text.toUpperCase() == name) 
				selected = O.selected = true;
		}
		else
		{
		    var O = C('optgroup');
			O.label = arr[x][0];
			for (var xx in arr[x][1] )
			{
				var SO = C('option');
				O.appendChild(SO);
				var arr_split = arr[x][1][xx].split('|');
				if(arr_split.length > 1)
				{
					SO.text = arr_split[0];
					SO.value = arr_split[1];
				}
				else
					SO.value = SO.text = arr[x][1][xx];
				if(SO.text.toUpperCase() == name && !selected) SO.selected = true;
			}
			S.appendChild(O);
		}
	}
	return S;
}

// CSRF防范
var url_token = function (url)
{
	if(OpenCSRF != 'on') return url;
	var index = url.indexOf('#'); 
	var fragment = null; 
	if(index != -1)
	{ 
		fragment = url.substring(index); 
		url = url.substring(0,index); 
	}
	url += ((url.indexOf('?') != -1) ? '&' : '?') + 'amh_token=' + amh_token; 
	if(fragment != null) url += fragment; 
	return url;
}
var AMH_CSRF = function ()
{
	if(OpenCSRF != 'on') return;
	var f_arr = document.getElementsByTagName('form'); 
	var len = f_arr.length; 
	for(var i=0; i<len; i++) 
	{ 
		var url = f_arr[i].action; 
		f_arr[i].appendChild(C('input', {'name':'amh_token', 'value':amh_token, 'type':'hidden'})); 
	}

	var a_arr = document.getElementsByTagName('a'); 
	var len = a_arr.length; 
	for(var i=0; i<len; i++) 
	{
		var url = a_arr[i].getAttribute('href');
		if (url && url != '')
		{
			url = url.replace(/(^\s*)|(\s*$)/g, "");
			if (url != null && url != '' && url.indexOf('javascript:') == -1 && url.indexOf('amh_token=') == -1 && ((url.indexOf('http') == 0 && url.indexOf(HTTP_HOST) != -1) || url.indexOf('http') == -1) )
				a_arr[i].setAttribute('href', url_token(url)); 
		}
	}
}

// AJAX请求
var Ajax={};
Ajax._xmlHttp = function(){ return new (window.ActiveXObject||window.XMLHttpRequest)("Microsoft.XMLHTTP");}
Ajax._AddEventToXHP = function(xhp,fun,isxml){
	xhp.onreadystatechange=function(){
		if(xhp.readyState==4&&xhp.status==200)
			fun(isxml?xhp.responseXML:xhp.responseText);
	}	
}
Ajax.get=function(url,fun,isxml,bool){
	var _xhp = this._xmlHttp();	
	this._AddEventToXHP(_xhp, fun || function(){} ,isxml);
	_xhp.open("GET",url_token(url),bool);
	_xhp.send(null);
	
}
Ajax.post=function(url,data,fun,isxml,bool){
	var _xhp = this._xmlHttp();	
	this._AddEventToXHP(_xhp, fun || function(){},isxml);
	_xhp.open("POST",url,bool);
	_xhp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	_xhp.send(data);
}

// Cookie操作对象
var Cookies = {};
Cookies.set = function(name, value){
     var argv = arguments;
     var argc = arguments.length;
     var expires = (argc > 2) ? argv[2] : null;
     var path = (argc > 3) ? argv[3] : '/';
     var domain = (argc > 4) ? argv[4] : null;
     var secure = (argc > 5) ? argv[5] : false;
     document.cookie = name + "=" + escape (value) +
       ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) +
       ((path == null) ? "" : ("; path=" + path)) +
       ((domain == null) ? "" : ("; domain=" + domain)) +
       ((secure == true) ? "; secure" : "");
};



// 升级更新提示
var upgrade_notice = function ()
{
	Ajax.get('/index.php?c=config&a=upgrade_notice&tag=' + Math.random(),function (msg){
		if (G('header'))
		{
			if(G('upgrade_notice')) G('header').removeChild(G('upgrade_notice'));
			if(msg != '0') C(G('header'), 'In', C('a', {'href':url_token('/index.php?c=config&a=config_upgrade'), 'id':'upgrade_notice', 'innerHTML': '您现在有' + msg + '个更新'}));
		}
	}, false, true)
}


// 命令进行中 **************************************************************************
// 实时进度DOM
var show_result_dom = null;
var amh_cmd_ing = function ()	
{
	if(!show_result_dom) show_result_dom = G('show_result');
	if(parseInt(Math.max(show_result_dom.scrollHeight, show_result_dom.scrollHeight)) > 430 ) _amh_cmd_ing();
}
var _amh_cmd_ing = function ()
{
	temp_scrollTop = show_result_dom.scrollTop;
	show_result_dom.scrollTop = parseInt(show_result_dom.scrollTop) + 15;
	if(temp_scrollTop != parseInt(show_result_dom.scrollTop))
	{
		setTimeout(function ()
		{
			_amh_cmd_ing()
		}, 100);
	}
}


// 模块实时进程
var module_ing_name;			// 模块名称
var module_ing_actionName;		// 动作名称
var temp_scrollTop = 0;			// 当前滚动条上方高度
var ing_status;
var module_ing_status = false;	// 最终运行状态
var module_ing_button;			// 按钮
var module_end = function ()
{
	module_ing_button = G('module_ing_button');
	ing_status = G('ing_status');

	// ssh返回false即为成功
	if (!module_ing_status)
	{
		ing_status.id = 'success';
		ing_status.innerHTML = module_ing_name + ' ' + module_ing_actionName + '成功。'
		Ajax.get('/index.php?c=index&a=module_ajax&tag=' + Math.random(), function (msg)
		{
			up_module_menu(msg);
			Ajax.get('./index.php?c=host&a=host&run=amh-web&m=php&g=reload&confirm=y', false, false, true);
		}, false, true);
	}
	else
	{
		ing_status.id = 'error';
		ing_status.innerHTML = module_ing_name + ' ' + module_ing_actionName + '失败。'
	}
	module_ing_button.disabled = false;
	module_ing_button.value = '返回模块程序列表';
	module_ing_button.onclick = function ()
	{
		WindowLocation('./index.php?c=module&a=module_list&page=' + page);
	}
}

// 升级更新实时进程
var UpgradeName;					// 更新名称
var upgrade_ing_status = false;		// 最终运行状态
var upgrade_ing_button;				// 按钮
var upgrade_end = function ()
{
	upgrade_ing_button = G('upgrade_ing_button');
	ing_status = G('ing_status');

	// ssh返回false即为成功
	if (!upgrade_ing_status)
	{
		ing_status.id = 'success';
		ing_status.innerHTML = UpgradeName + ' 更新升级成功。';
		Ajax.get('./index.php?c=host&a=host&run=amh-web&m=php&g=reload&confirm=y');
		setTimeout(function (){
			upgrade_notice();
		}, 588);
	}
	else
	{
		ing_status.id = 'error';
		ing_status.innerHTML = UpgradeName + ' 更新升级失败。';
	}
	upgrade_ing_button.disabled = false;
	upgrade_ing_button.value = '返回列表';
	upgrade_ing_button.onclick = function ()
	{
		WindowLocation('./index.php?c=config&a=config_upgrade');
	}
}

// 面板数据备份实时进程
var backup_result;
var backup_ing_status = false;		// 最终运行状态
var backup_ing_button;				// 按钮
var backup_end = function ()
{
	backup_ing_button = G('backup_ing_button');
	ing_status = G('ing_status');

	// ssh返回false即为成功
	if (!backup_ing_status)
	{
		ing_status.id = 'success';
		ing_status.innerHTML = backup_result;
	}
	else
	{
		ing_status.id = 'error';
		ing_status.innerHTML = backup_result;
	}
	backup_ing_button.disabled = false;
	backup_ing_button.value = '查看数据备份列表';
	backup_ing_button.onclick = function ()
	{
		WindowLocation('./index.php?c=backup&a=backup_list');
	}
}


// 面板数据还原实时进程
var revert_result;
var revert_ing_status = false;		// 最终运行状态
var revert_ing_button;				// 按钮
var revert_end = function ()
{
	revert_ing_button = G('revert_ing_button');
	ing_status = G('ing_status');

	// ssh返回false即为成功
	if (!revert_ing_status)
	{
		ing_status.id = 'success';
		ing_status.innerHTML = revert_result;
		revert_ing_button.value = '一键还原已完成';
		// 面板php与所有虚拟主机php重载
		Ajax.get('./index.php?c=host&a=host&run=amh-web&m=php&g=reload&confirm=y');
		Ajax.get('./index.php?m=php&g=reload');
	}
	else
	{
		ing_status.id = 'error';
		ing_status.innerHTML = revert_result;
		revert_ing_button.value = '一键还原已失败';
	}
}

// 导航菜单
var navigation_t;		
var navigation_tmp_obj;			// 临时timeout对象
var navigation_activ_obj;		// 当前激活对象
var navigation_ul;
var navigation_li;
var navigation_run = function ()
{
	navigation_ul = G('navigation_ul');
	navigation_li = navigation_ul.getElementsByTagName('li');
	for (var k in navigation_li)
	{
		if(typeof(navigation_li[k]) != 'object') continue;
		navigation_li[k].div_arr = navigation_li[k].getElementsByTagName('div');				// DIV块
		navigation_li[k].a_link = navigation_li[k].div_arr[0].getElementsByTagName('a')[0];		// 导航A链接
		navigation_li[k].a_link.old_class = navigation_li[k].a_link.className;

		navigation_li[k].style.width = navigation_li[k].div_arr[0].offsetWidth + 'px';
		navigation_li[k].div_arr[0].style.position = 'absolute';
		// 开启导航 & 存在小导航
		if (OpenMenu == 'on' && navigation_li[k].div_arr[1] && navigation_li[k].div_arr[1].getElementsByTagName('a').length > 0)			
		{
			(function (obj)
			{
				obj.onmouseover = function ()
				{
					if(navigation_tmp_obj == obj)
						clearTimeout(navigation_t);
					obj.a_link.className = 'activ_hover';
					obj.div_arr[1].style.display = 'block';
				}
				obj.onmouseout = function ()
				{
					// 阻止IE冒泡
					navigation_tmp_obj = obj;
					navigation_t = setTimeout(function ()
					{
						obj.a_link.className = obj.a_link.old_class;
						obj.div_arr[1].style.display = 'none';
					}, 10)
				}
			})(navigation_li[k])
			
		}
		else
		{
			navigation_li[k].onmouseover = function ()
			{
				this.a_link.className = 'activ';
			}
			 navigation_li[k].onmouseout = function ()
			{
				this.a_link.className = this.a_link.old_class;
			}
		}
		
	}
}
// 取得模块信息
var amh_module = function ()
{
	Ajax.get('/index.php?c=index&a=module_ajax&tag=' + Math.random(), function (msg)
	{
		up_module_menu(msg);
	}, false, true);
}

// 更新模块导航菜单
var up_module_menu = function (obj)
{
	obj = eval('(' + obj + ')');
	var module = G('module');
	var module_ul = G('module_ul');
	var module_available = 0;
	if (module_ul)
	{
		var user_module_arr = getElementByClassName('user_module', 'a');
		for (var k in user_module_arr)
		{
			user_module_arr[k].parentNode.removeChild(user_module_arr[k]);
		}
		for (var k in obj)
		{
			var m_url = obj[k].ModuleAdmin;
			m_url = m_url.replace(/(^\s*)|(\s*$)/g, "");
			if (m_url != '')
			{
				var module_a = C('a', {'href': url_token(obj[k].ModuleAdmin), 'id':obj[k].ModuleID, 'className': 'user_module', 'target' : '_blank'});
				C(module_a, 'In', [C('em', {'id':obj[k].ModuleID + '_em'}), C('span', {'innerHTML' : obj[k].ModuleName, 'id':obj[k].ModuleID + '_span'})]);
				C(module_ul, 'In', module_a);
				++module_available;
			}
		}
		if(module.getElementsByTagName('i')[0])
			module.removeChild(module.getElementsByTagName('i')[0]);
		
		var old_w = module.parentNode.offsetWidth;	// 当前宽度
		if(module_available > 0)
		{
			module.i = C('i', 'In', '(' + module_available + ')');
			C(module, 'In', module.i);
			old_w += module.i.offsetWidth  + 2;		// 增加数量后宽度
		}
		module.parentNode.parentNode.style.width = old_w + 'px';
	}
}

// ******************************************************************************


var WindowLocation = function (url)
{
	window.location = url_token(url);
	return true;
}
var WindowOpen = function (url)
{
	window.open(url_token(url));
	return true;
}

// 创建密码
var CreatePassword = function (len)
{
	var str = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";
	var pass = '';
    for (var i = 0; i < len; i++ ) 
        pass += str.charAt(Math.floor( Math.random() * str.length));
	return pass;
}



// ********************************************************************

if (window.attachEvent)
	window.attachEvent('onload', function(){ navigation_run(); AMH_CSRF(); });
else
	window.addEventListener('load', function(){ navigation_run(); AMH_CSRF(); }, false);