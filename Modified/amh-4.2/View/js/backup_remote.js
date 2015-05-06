
/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Javascript 备份远程设置
 * Update:2013-11-01
 * 
 */

var ftp_list = [['1','密码']];
var ssh_list = [['1','密码'],['2','密匙']];
// 账号验证方式改变
var remote_pass_type_fun = function (obj)
{
	G('remote_password1').style.display = (obj.value == '1') ? 'inline-block' : 'none';
	G('remote_password2').style.display = (obj.value == '2') ? 'inline-block' : 'none';
	G('remote_password').value = G('remote_password' + obj.value).value;
	return true;
}
// 类型改变
var remote_type_fun = function (obj)
{
	var arr =  (obj.value == 'FTP') ? ftp_list : ssh_list;

	var dom = G('remote_pass_type_dom');
	var dom_parent = dom.parentNode;
	dom_parent.removeChild(dom);

	var dom = C('select');
	dom.id="remote_pass_type_dom";
	dom.name="remote_pass_type";
	dom.onchange = function ()
	{
		remote_pass_type_fun(this);
	}
	for (var k in arr )
		dom.options[k] = new Option(arr[k][1], arr[k][0]);
	dom_parent.appendChild(dom);
	remote_pass_type_fun(dom);
}

var connect_check = function (obj)
{
	if(obj.ing) return false;

	obj.setTime = setTimeout(function ()
	{
		obj.innerHTML = '<span class="loop icon"></span><font color="red">请求超时</font>';
		obj.className = 'button';
		obj.ing = false;
	}, 30*1000);

	obj.ing = true;
	obj.className = '';
	obj.innerHTML = '<img src="View/images/loading.gif" style="padding:0px 29px 0px 32px;_padding:0px 18px 0px 18px;"/>';
	Ajax.get(obj.href + '&tag=' + Math.random(),function (msg){
		var status = ( msg.indexOf('OK') != -1 ) ? '<font color="green">连接成功</font>' : '<font color="red">连接失败</font>';
		clearTimeout(obj.setTime);
		obj.innerHTML = '<span class="loop icon"></span>' + status;
		obj.style.color = '';
		obj.className = 'button';
		obj.ing = false;
	}, false, true)
	return false;
}