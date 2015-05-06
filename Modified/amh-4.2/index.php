<?php

/************************************************
 * Amysql PHPMVC - AMP 1.5
 * Amysql.com 
 * @param Object $Amysql 总线程
 * Update:2012-10-08
 * 
 */

define ('_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);				// 网站根目录
define ('_Amysql', _ROOT . 'Amysql' . DIRECTORY_SEPARATOR);				// 系统目录
define ('_Controller', _ROOT . 'Controller' . DIRECTORY_SEPARATOR);		// 控制器目录
define ('_Model', _ROOT . 'Model' . DIRECTORY_SEPARATOR);				// 模型目录
define ('_Class', _ROOT . 'Class' . DIRECTORY_SEPARATOR);				// 对象类目录
define ('_View', _ROOT . 'View' . DIRECTORY_SEPARATOR);					// 视图模板目录
define ('_PathTag', '/');												// 载入下级目录文件标识 例如: 需载入 Model/user/vip.php 即使用 _mode('user/vip');

define ('_Host', (empty($_SERVER["HTTPS"]) || $_SERVER['HTTPS'] == 'off' ? 'http://' : 'https://') . $_SERVER['HTTP_HOST']);		// 主机网址
define ('_Http', _Host . str_ireplace('/index.php', '', $_SERVER['SCRIPT_NAME']) . '/');			// 网站根目录网址

include(_Amysql . 'Config.php');
include(_Amysql . 'Amysql.php');


// 总线程 **********************************************************
$Amysql = new Amysql();

// URL分析开启网站进程
$Amysql -> AmysqlProcess = new AmysqlProcess();	
$Amysql -> AmysqlProcess -> ProcessStart();
$Amysql -> AmysqlProcess -> ControllerStart();


?>
