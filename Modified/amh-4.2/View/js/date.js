/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Javascript Date
 * Update:2013-11-01
 * 
 */

// Date
var showDateList = [];
var getByClass = function (oPare, cla) {
	var oChild = oPare.getElementsByTagName("*");
	var arr = [];
	for (var i = 0; i < oChild.length; i++) {
		var arrCla = oChild[i].className.split(" ");
		var j;
		for (var j in arrCla) {
			if (arrCla[j] == cla) {
				arr.push(oChild[i]);
				break;
			}
		}
	}
	return arr;
}
function showDate(option) {
	this.obj = G(option.id);
}
showDate.prototype.init = function (_name, _val) {
	var _self = this;
	showDateList.push(_self);
	_self.obj.innerHTML = '<input class="showDate input_text" type="text" name="' + _name + '" value="' + _val + '"/><div class="sel_date dn"> <div class="clearfix"> <span class="prev_y fl"><<</span><span class="prev_m fl"><</span> <span class="next_y fr">>></span><span class="next_m fr">></span> <div class="show_mn"> <input type="text" class="showDate2 year" value="选择年份" /> <span class="ml5 mr5">年</span> <input type="text" class="showDate2 month" value="选择月份" /> <span class="ml5">月</span> </div> </div> <table class="data_table"> <thead> <tr> <td>日</td><td>一</td><td>二</td><td>三</td><td>四</td><td>五</td><td>六</td> </tr> </thead> <tbody> <tr> <td>1</td><td></td><td></td><td></td><td></td><td></td><td></td> </tr> <tr> <td>1</td><td></td><td></td><td></td><td></td><td></td><td></td> </tr> <tr> <td></td><td></td><td></td><td></td><td></td><td></td><td></td> </tr> <tr> <td></td><td></td><td></td><td></td><td></td><td></td><td></td> </tr> <tr> <td></td><td></td><td></td><td></td><td></td><td></td><td></td> </tr> <tr> <td></td><td></td><td></td><td></td><td></td><td></td><td></td> </tr> </tbody> </table> </div>';
	_self.dateText = getByClass(_self.obj, "showDate")[0];
	_self.dateBox = getByClass(_self.obj, "sel_date")[0];
	_self.yearBox = getByClass(_self.obj, "year")[0];
	_self.mnBox = getByClass(_self.obj, "month")[0];

	_self.dataTable = getByClass(_self.dateBox, "data_table")[0];
	_self.tbody = _self.dataTable.tBodies[0];
	_self.td = _self.tbody.getElementsByTagName("td");
	_self.prevM = getByClass(_self.dateBox, "prev_m")[0];
	_self.nextM = getByClass(_self.dateBox, "next_m")[0];
	_self.prevY = getByClass(_self.dateBox, "prev_y")[0];
	_self.nextY = getByClass(_self.dateBox, "next_y")[0];

	//显示日历
	_self.dateText.onclick = function () {
		_self.changeDefault(this);
		_self.show();
	}

	//点击空白 隐藏日历
	document.onclick = function (event) {
		event = event || window.event;
		var Target = event.target || event.srcElement;
		_self.hide(event, Target, this);
	}
	//点击选择日期
	for (var i = 0; i < _self.td.length; i++) {
		_self.td[i].onclick = function () {
			var newDd = this.innerHTML;
			var newYear = parseInt(_self.yearBox.value, 10);
			var newMn = parseInt(_self.mnBox.value, 10);
			if (newDd.match(/^\s{0}$/g)) {  //如果td有值;
				return false;
			}
			if (isNaN(newYear) || isNaN(newMn) || newYear < 1900 || newYear > 2099 || newMn < 1 || newMn > 12) {  //如果td有值;
				return false;
			}
			_self.dateText.value = newYear + "-" + newMn + "-" + newDd;
			_self.dateBox.className += " ";
			_self.dateBox.className += "dn";
		}
		_self.td[i].onmouseover = function () {
			if (this.className.indexOf("hove") == -1) {
				this.className += " hover";
			}
		}
		_self.td[i].onmouseout = function () {
			this.className = this.className.replace("hover", "")
		}
	}
	//点击切换月份
	_self.prevM.onclick = _self.nextM.onclick = function () {
		_self.changeMn(this);
		return this;
	}
	//点击切换年份
	_self.prevY.onclick = _self.nextY.onclick = function () {
		_self.changeYr(this);
		return this;
	}
	_self.yearBox.onkeyup = _self.mnBox.onkeyup = function () {
		this.value = this.value.replace(/\D/g, "");
		var year = parseInt(_self.yearBox.value, 10);
		var month = parseInt(_self.mnBox.value, 10);
		if (!isNaN(year) && year <= 2099 && year >= 1900 && !isNaN(month) && month <= 12 && month >= 1) {
			_self.showAllDay(year, month - 1);
		}
		if (this == _self.yearBox && year >= 1900 && year <= 2099) {
			_self.mnBox.focus();
		} else if (this == _self.mnBox && (month < 1 || month > 12)) {
			if (this.value.slice(0, 1) > 1) { //如果第一位大于1
				this.value = this.value.slice(0, 1);
			} else if (month > 12) { //
				this.value = 12;
			}
			year = parseInt(_self.yearBox.value, 10);
			month = parseInt(_self.mnBox.value, 10);
			_self.showAllDay(year, month - 1);
		}
	}
	_self.yearBox.onblur = function () {
		var year = parseInt(this.value, 10);
		if (year < 1900 || year > 2099 || isNaN(year)) {
			this.focus();
			alert("请输入正确年份！");
			this.value = "";
		}
	}
	_self.mnBox.onfocus = function () {
		var year = parseInt(_self.yearBox.value, 10);
		if (isNaN(year)) {
			_self.yearBox.focus();
		}
	}
	_self.mnBox.onblur = function () {
		var month = parseInt(this.value, 10);
		var year = parseInt(_self.yearBox.value, 10);
		if (month < 1 || month > 12 || isNaN(month)) {
			alert("请输入正确月份！");
			this.value = "";
		}
	}
}
//点击切换月份
showDate.prototype.changeMn = function (obj) {
	var _self = this;
	var NewMn = parseInt(_self.mnBox.value, 10);
	var newYear = parseInt(_self.yearBox.value, 10);
	if (isNaN(NewMn) || isNaN(newYear)) {
		alert("请填写正确的年和月！");
		return false;
	}
	if (obj == _self.nextM) {
		NewMn++;
	} else {
		NewMn--;
	}
	if (NewMn < 1) {
		NewMn = 12;
		newYear -= 1;
	} else if (NewMn > 12) {
		NewMn = 1;
		newYear += 1;
	}
	_self.mnBox.value = NewMn;
	_self.showNow(newYear, NewMn);
}
//点击切换年份
showDate.prototype.changeYr = function (obj) {
	var _self = this;
	var NewMn = parseInt(_self.mnBox.value, 10);
	var newYear = parseInt(_self.yearBox.value, 10);
	if (isNaN(NewMn) || isNaN(newYear)) {
		alert("请填写正确的年和月！");
		return false;
	}
	if (obj == _self.nextY) {
		newYear++;
	} else {
		newYear--;
	}
	if (newYear < 1900) {
		newYear = 1900;
	} else if (newYear > 2099) {
		newYear = 2099;
	}
	_self.mnBox.value = NewMn;
	_self.showNow(newYear, NewMn);
}
//文本框 清空初始值
showDate.prototype.changeDefault = function (obj) {
	var _self = this;
	var deVal = obj.value;
	var regExp = /^\s{0,}$/g;
	if (deVal == "") {
		_self.showNow();
	} else if (deVal.match(regExp) === null && _self.dateBox.className.indexOf("dn") != -1) {
		var dayArr = deVal.match(/[0-9]{1,4}/g);
		_self.showNow(dayArr[0], dayArr[1], dayArr[2]); //刷新日期
	}
}

//显示日历
showDate.prototype.show = function () {
	var _self = this;
	if (_self.dateBox.className.indexOf("dn") != -1) {
		var cls = _self.dateBox.className;
		_self.dateBox.className = cls.replace("dn", "");
	}
}
//隐藏日历
showDate.prototype.hide = function (event, Target, obj) {
	for (var k in showDateList )
	{
		var _self = showDateList[k];
		var oPare = Target.parentNode;
		var isChild = true; //默认是子元素
		if (oPare == obj || Target == obj) {
			isChild = false;
		} else {
			loop: while (oPare != _self.obj) {
				oPare = oPare.parentNode;
				if (oPare == obj) {
					isChild = false;
					break loop;
				}
			}
		}
		if (!isChild && _self.dateBox.className.indexOf("dn") == -1) {
			_self.dateBox.className += " ";
			_self.dateBox.className += "dn";
		}
	}
}
//填充年、月
showDate.prototype.showNow = function (yr, mn, date) {
	var _self = this;
	var now = new Date();
	var year = yr || now.getFullYear();
	var month = mn - 1 || now.getMonth();
	var dd = date || now.getDate();
	//填充 年 和 月
	_self.yearBox.value = year;
	_self.mnBox.value = mn || now.getMonth() + 1;
	//填充日期
	_self.showAllDay(year, month, dd);
}
//填充当月的所有日期
showDate.prototype.showAllDay = function (Yr, Mn, Dd) {

	var _self = this;
	var arr31 = [1, 3, 5, 7, 8, 10, 12];
	var is31 = true;
	var newDd = new Date();  
	Dd = Dd ? Dd : newDd.getDate();
	newDd.setFullYear(Yr, Mn, Dd);
	var year = newDd.getFullYear(),
		month = newDd.getMonth(),
		dd = newDd.getDate();

	var firstD = new Date();
	firstD.setFullYear(year, month, 1);
	var firstDay = firstD.getDay();

	var str31 = arr31.join(",");
	var regExp = eval("/" + (month + 1) + ",|," + (month + 1) + ",|," + (month + 1) + "/g");
	var dayLen = 31;
	//判断每个月有多少天
	if (str31.search(regExp) == -1) {
		dayLen = 30;
	}
	//清空日期
	for (var i = 0; i < _self.td.length; i++) {
		_self.td[i].innerHTML = "";
		_self.td[i].className = _self.td[i].className.replace("active", "");
	}
	//如果有31天
	for (var j = 0; j < dayLen; j++) {
		_self.td[j + firstDay].innerHTML = j + 1;
		if (j + 1 == dd && _self.td[j + firstDay].className.indexOf("active") == -1) {
			_self.td[j + firstDay].className += " ";
			_self.td[j + firstDay].className += "active";
		}
	}
}