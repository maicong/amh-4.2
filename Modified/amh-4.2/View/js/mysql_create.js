
/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Javascript MySQL快速建库
 * Update:2013-11-01
 * 
 */

window.onload = function ()
{
	var database_name = G('database_name');
	var user_name = G('user_name');
	var create_user = G('create_user');
	var user_password = G('user_password');
	var create_password = G('create_password');

	var grant_read = G('grant_read');
	var grant_write = G('grant_write');
	var grant_admin = G('grant_admin');
	var grant_all = G('grant_all');
	var user_tr = getElementByClassName('user_tr', 'tr');

	// 数据库名输入
	database_name.onkeyup = function ()
	{
		user_name.value = this.value + '_user';
	}

	// 是否创建用户
	create_user.onchange = function ()
	{
		for (var k in user_tr )
			user_tr[k].className = this.checked ? 'user_tr' : 'user_tr none';
		
	}
	create_user.onchange();
	
	// 生成密码
	create_password.onclick = function ()
	{
		user_password.value = CreatePassword(15);
	}
	create_password.onclick();

	// 全部权限
	grant_all.onchange = function ()
	{
		grant_read.checked =  grant_write.checked = grant_admin.checked = this.checked;
	}
	var un_grant_all = function ()
	{
		grant_all.checked = (!grant_read.checked || !grant_write.checked || !grant_admin.checked) ? false : true;
	}
	grant_read.onchange = un_grant_all;
	grant_write.onchange = un_grant_all;
	grant_admin.onchange = un_grant_all;

}