
/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Javascript FTP
 * Update:2013-11-01
 * 
 */

// ftp
var ShowFtpTop = function ()
{
	var tr = getElementByClassName('ftptop', 'tr');
	for (var k in tr)
		tr[k].className = (tr[k].className == 'ftptop none') ? 'ftptop':'ftptop none';
}

window.onload = function ()
{
	var input_arr = getElementByClassName('input_text', 'input');
	var _input_arr = {};
	var _input_val = {};
	for (var k in input_arr )
	{
		if (G('checkbox_' + input_arr[k].name))
		{
			// dom与值
			_input_arr[input_arr[k].name] = input_arr[k];
			_input_val[input_arr[k].name] = input_arr[k].value;


			if (input_arr[k].value == '')
			{
				input_arr[k].disabled = true;
				input_arr[k].className = 'input_text disabled';
				G('checkbox_' + input_arr[k].name).checked = true;
			}

			// 更改值
			input_arr[k].onkeyup = function ()
			{
				_input_val[this.name] = this.value;
			}

			// 复选框事件
			G('checkbox_' + input_arr[k].name).onclick = function ()
			{
				var name = this.id.replace('checkbox_', '');
				if(this.checked)
				{
					_input_arr[name].value = '';
					_input_arr[name].className = 'input_text disabled';
					_input_arr[name].disabled = true;
				}
				else
				{
					_input_arr[name].className = 'input_text';
					_input_arr[name].disabled = false;
					_input_arr[name].value = _input_val[name];
				}
			}
		}
	}
}