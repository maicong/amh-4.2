<?php

/************************************************
 *
 * Amysql PHPMVC - AMP 1.5
 * Amysql.com 
 * @param Object $AmysqlProcess 网站进程
 *
 */
class Amysql {

	public $AmysqlProcess;

	public function Amysql() 
	{
		global $Config;
		ini_set("magic_quotes_runtime", false);
		($Config['DebugPhp'] && error_reporting(E_ALL)) || error_reporting(0);						// 是否报错
		($Config['SessionStart'] && session_start());												// SESSION
		(!empty($Config['CharSet']) && header('Content-type: text/html;charset=' . $Config['CharSet']));

	}

	static public function AmysqlNotice($notice)
	{
		$AmysqlController = new AmysqlController();
		$AmysqlController -> notice = $notice;
		$AmysqlController -> _view('AmysqlNotice');
		exit();
	}

	static public function filter(&$array, $function) 
	{
		if (!is_array($array)) Return $array = $function($array);
		foreach ($array as $key => $value) 
			(is_array($value) && $array[$key] = Amysql::filter($value, $function)) || $array[$key] = $function($value);
		Return $array;
	}
}


/************************************************
 *
 * 总进程对象
 * @param Object $ControllerName	控制器对象
 * @param string $ControllerName	控制器名称
 * @param string $ActionName		方法名称
 * @param string $ControllerFile	控制器文件
 *
 */
class AmysqlProcess {

	public $AmysqlController;	
	public $ControllerName;
	public $ActionName;
	public $ControllerFile;

	function ProcessStart()
	{
		global $Config;
		if ($Config['HttpPath'])
		{
			$GETParam = (strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['SCRIPT_NAME'])) ? explode('/', trim(str_ireplace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']), '/')) : '';
			$GETCount = count($GETParam);
			if($GETCount > 1)
				for ($i=2; $i<$GETCount; ++$i) $_GET[$GETParam[$i]] = isset($GETParam[++$i]) ?  $GETParam[$i] : '';
		}

		$magic_quotes = function_exists('get_magic_quotes_gpc') ? get_magic_quotes_gpc() : false;	// 环境是否有过滤
		if($Config['Filter'])	// 开启过滤
		{
			( !$magic_quotes && (Amysql::filter($_GET, 'addslashes') && Amysql::filter($_POST, 'addslashes') && Amysql::filter($_COOKIE, 'addslashes') && Amysql::filter($_FILES, 'addslashes')) );	
		}
		else
		{	
		    ( $magic_quotes && (Amysql::filter($_GET, 'stripslashes') && Amysql::filter($_POST, 'stripslashes') && Amysql::filter($_COOKIE, 'stripslashes') && Amysql::filter($_FILES, 'stripslashes')) );	
		}

		$this -> ControllerName = !empty($GETParam[0]) ? $GETParam[0] : ( (isset($_GET[$Config['UrlControllerName']]) && !empty($_GET[$Config['UrlControllerName']])) ? $_GET[$Config['UrlControllerName']] : 'index'); 
		$this -> ControllerName = str_replace(_PathTag, DIRECTORY_SEPARATOR, $this -> ControllerName);
		$this -> ActionName = !empty($GETParam[1]) ? $GETParam[1] : ( (isset($_GET[$Config['UrlActionName']]) && !empty($_GET[$Config['UrlActionName']])) ? $_GET[$Config['UrlActionName']] : 'IndexAction'); 
		$this -> ControllerFile = _Controller . $this -> ControllerName . '.php';
	}

	function ControllerStart()
	{
		((is_file($this -> ControllerFile) && include_once($this -> ControllerFile)) || 
		(is_file(_Controller . 'index.php') && include_once(_Controller . 'index.php')) ||
		Amysql::AmysqlNotice($this -> ControllerFile . ' 控制器文件不存在'));

		(class_exists($this -> ControllerName) || (($this -> ControllerName = 'index') && class_exists('index')) || 
		Amysql::AmysqlNotice($this -> ControllerName . ' 控制器不存在'));

		$methods = get_class_methods($this -> ControllerName);			// 获取类中的方法名 

		(in_array($this -> ActionName, $methods, true) || 
		(($this -> ActionName = 'IndexAction') && in_array($this -> ActionName, $methods, true)) ||
		Amysql::AmysqlNotice($this -> ActionName . ' 方法不存在'));

		$this -> AmysqlController = new $this->ControllerName($_GET);	// 实例控制器
		$this -> AmysqlController -> {$this -> ActionName}();			// 执行方法
	}


}


/************************************************
 *
 * 控制器逻辑层 (C)
 * @param Object $DB				数据库对象
 * @param Array	 $DBConfig			链接配置
 * @param Array $AmysqlModel		模型对象集
 * @param Object $AmysqlTemplates	模板对象
 * @param Array $EchoData			调试数据
 *
 */
class AmysqlController {

	public $DB;
	public $DBConfig;				// 控制器调用Mysql的链接配置
	public $AmysqlModel;			// 控制器关联的数据模型对象集
	public $AmysqlClass;			// 控制器关联的类对象集
	public $AmysqlTemplates;		// 控制器关联的模板对象
	public $EchoData = array();		// 调试数据

	function AmysqlController()
	{
		global $Config;
		$this -> DBConfig = $Config;

		$this -> DB = new DB();
		$this -> AmysqlTemplates = new AmysqlTemplates();
	}

	/**
	 * 设置数据库配置
	 * @param Array $Config	链接配置
	 */
	function _DBSet($_Config = NULL)
	{
		global $Config;
		$_Config = ($_Config == NULL) ? $Config : $_Config;
		foreach ($Config as $key=>$val)
			(empty($_Config[$key]) && $_Config[$key] = $val);
		$this -> DBConfig = $_Config;
	}

	/**
	 * 模板赋值
	 * @param string $name	名称
	 * @param string $value	值
	 */
	public function __set($name, $value = NULL)
	{
		$this -> AmysqlTemplates -> TemplateValue[$name] = $value;
	}

	/**
	 * 数组模板赋值
	 * @param Array $array	数据
	 */
	public function _array($array) 
	{
		if (is_array($array))
		{
			foreach ($array as $key=>$val)
				$this  -> AmysqlTemplates -> TemplateValue[$key] = $val;
		}
	}

	/**
	 * 加载自定义类文件
	 * @param	string $file			文件名
	 * @param	string $ClassName		类名(可选,默认为文件名)
	 * @return	Object $ClassName()		类对象
	 */
	public function _class($file, $ClassName = NULL) 
	{
		$file = str_replace(_PathTag, DIRECTORY_SEPARATOR, $file);
		$ClassName = ($ClassName == NULL) ? $file : $ClassName;
		$file = _Class . $file . '.php';
		if(is_file($file)) 
		{
			include_once($file);
			if(!class_exists($ClassName))
				Amysql::AmysqlNotice($ClassName . ' 类对象不存在');

			if(!isset($this -> AmysqlClass[$ClassName]))						// 不存在类对象
				$this -> AmysqlClass[$ClassName] = new $ClassName();
			Return $this -> AmysqlClass[$ClassName];
		}
		
		Amysql::AmysqlNotice($file . ' 类文件不存在');
	}

	/**
	 * 加载模型文件
	 * @param	string $file			文件名
	 * @param	string $ClassName		模型名(可选,默认为文件名)
	 * @return	Object $ClassName()		模型对象
	 */
	public function _model($file, $ClassName = NULL) 
	{
		$file = str_replace(_PathTag, DIRECTORY_SEPARATOR, $file);
		$ClassName = ($ClassName == NULL) ? $file : $ClassName;
		$file = _Model . $file . '.php';
		if(is_file($file)) 
		{
			include_once($file);
			if(!class_exists($ClassName))
				Amysql::AmysqlNotice($ClassName . ' 模型对象不存在');
			
			$this -> DBConfig['ModelTag'] = $ModelTag = $ClassName . '_' . $this -> DBConfig['ConnectTag'];		// 模型标识
			if(!isset($this -> AmysqlModel[$ModelTag]))															// 不存在模型
			{
				$this -> AmysqlModel[$ModelTag] = new $ClassName();
				if(!isset($this -> DB -> DBS[$ModelTag])) $this -> DB -> DBConnect($this -> DBConfig);	// 没连接就进行数据库连接
				$this -> AmysqlModel[$ModelTag] -> MysqlConnect = $this -> DB -> DBS[$ModelTag];		// 模型使用的数据库连接
			}
			Return $this -> AmysqlModel[$ModelTag];
		}

		Amysql::AmysqlNotice($file . ' 模型文件不存在');
	}

	/**
	 * 获得相关文件数据(模板目录)
	 * @param	string $file	文件名
	 * @return	string 			数据
	 */
	public function _file($file) 
	{
		$file = str_replace(_PathTag, DIRECTORY_SEPARATOR, $file);
		$file = _View . $file . '.php';
		if(is_file($file)) 
			Return file_get_contents($file);

		Amysql::AmysqlNotice($file . ' 数据文件不存在');
	}

	/**
	 * 数据调试
	 * @param  $data	数据
	 */
	 public function _echo($data)
	{
		$this -> EchoData[] = $data;
	}

	/**
	 * 模板显示
	 */
	public function _view($file = null) 
	{
		// 控制器属性共享于模板 2012-4-20
		$AmysqlSelfPropertyKey = array_keys(get_class_vars('AmysqlController'));
		$ControllerPropertyParam = get_object_vars($this);
		foreach ($ControllerPropertyParam as $key=>$val)
		{
			if(!in_array($key, $AmysqlSelfPropertyKey))	// 非Amysql控制器属性
				$this -> AmysqlTemplates -> TemplateValue[$key] = $val;
		}

		$this -> AmysqlTemplates -> _view($file);

		// 信息调试输出
		if (is_array($this -> AmysqlModel))
		{
			foreach ($this -> AmysqlModel as $key=>$val)		// Sql调试
				echo $this -> AmysqlModel[$key] -> SqlBug;
		}
		foreach ($this -> EchoData as $val)
			echo "<!--<pre>\n" , print_r($val, true) , "\n</pre>-->";
	}
}

/************************************************
 *
 * 视图表现层 (V)
 * @param Array	 $TemplateValue		模板数据
 *
 */
class AmysqlTemplates
{
	public $TemplateValue;		// 模板数据

	function AmysqlTemplates()
	{
		// 自定义模板常用参数数据
		$this -> TemplateValue['_date'] = date('Y-m-d H:i:s', time());
	}

	public function _view($file = null) 
	{
		global $Config;
		// 2012-10-8
		($Config['XSS'] && Amysql::filter($this -> TemplateValue, 'htmlspecialchars') && Amysql::filter($_GET, 'htmlspecialchars') && Amysql::filter($_POST, 'htmlspecialchars') && Amysql::filter($_COOKIE, 'htmlspecialchars') );

		@extract($this -> TemplateValue);
		$file = str_replace(_PathTag, DIRECTORY_SEPARATOR, $file);
		$file = _View . $file . '.php';
		if(is_file($file)) 
			Return include($file);

		Amysql::AmysqlNotice($file . ' 模板文件不存在');
	}
}

/************************************************
 *
 * 模型数据层 (M)
 * @param Object	$MysqlConnect	当前数据链接
 * @param String	$Affected		影响数
 * @param String	$InsertId		新增ID
 * @param String	$ResultSum		结果集数量
 * @param String	$QueryStatus	查询状态
 *
 */
class AmysqlModel {
	
	public $MysqlConnect;
	public $Affected;
	public $InsertId;
	public $ResultSum;
	public $QueryStatus;
	public $SqlBug = '';

	/**
	 * 基本查询
	 * @param	string	$sql	SQL语句
	 * @return	Object 			结果集
	 */
	public function _query($sql) 
	{
		global $Config;
		if(!$this -> MysqlConnect) Return false;
		$this -> QueryStatus = '(0)';
		$this -> Affected = 0;
		if($Config['DebugSql']) $this -> SqlBug .= "\n". '<!--DebugSql: ' . $sql . '-->' . "\n";
		$result = mysql_query($sql, $this -> MysqlConnect);
		if (!$result) Return false;
		$this -> Affected = mysql_affected_rows();
		$this -> QueryStatus = '(ok)';
		Return $result;
	}

	/**
	 * 插入数据
	 * @param	string	$table		表名
	 * @param	Array	$data		数据
	 * @return	int 	InsertId	新增ID
	 */
	public function _insert($table, $data)
	{
		$this -> InsertId = 0;
		if (!is_array($data) || count($data) == 0)  Return 0;

		$field_arr = array();
		foreach ($data as $key=>$val)
			$field_arr[] = " `$key` = '$val' ";

		$sql = "INSERT INTO `$table` SET " . implode(',', $field_arr);
		$this -> _query($sql);
		$this -> InsertId = mysql_insert_id();
		Return $this -> InsertId;
	}

	/**
	 * 更新数据
	 * @param	string	$table		表名
	 * @param	Array	$data		数据
	 * @param	string	$where		更新条件
	 * @return	int 	Affected	影响数
	 */
	public function _update($table, $data, $where = '')
	{
		$this -> Affected = 0;
		if (!is_array($data) || count($data) == 0)  Return 0;

		$field_arr = array();
		foreach ($data as $key=>$val)
			$field_arr[] = " `$key` = '$val' ";

		$sql = "UPDATE `$table` SET " . implode(',', $field_arr) . $where;
		$this -> _query($sql);
		Return $this -> Affected;
	}

	/**
	 * 取得一排数据
	 * @param	string	$sql				SQL语句
	 * @param	string	$result_type		类型
	 * @return	Array 	$row				数据
	 */
	public function _row($sql, $result_type = MYSQL_ASSOC) 
	{
		$result = $this -> _query($sql);
		if (!$result) Return false;
		$row = mysql_fetch_array($result, $result_type);
		$this -> ResultSum = 1;
		$this -> _free($result);
		Return $row;
	}

	/**
	 * 取得所有数据
	 * @param	string	$sql				SQL语句
	 * @param	string	$result_type		类型
	 * @return	Array 	$row				数据
	 */
	public function _all($sql, $result_type = MYSQL_ASSOC) 
	{
		$result = $this -> _query($sql);
		if (!$result) Return false;
		$data = array();
		$this -> ResultSum = 0;
		while ($row = mysql_fetch_array($result, $result_type))
		{
			$data[] = $row;
			++$this -> ResultSum;
		}
		$this -> _free($result);
		Return $data;
	}
	
	/**
	 * 取得数据总数
	 * @param	string	$sql	SQL语句
	 * @return	int 			数量
	 */
	public function _sum($sql)
	{
		$result = $this -> _query($sql);
		if (!$result) Return false;
		Return mysql_num_rows($result);
	}
	
	/**
	 * 释放结果集
	 * @param	Object	$rs	释放
	 */
	public function _free($rs) 
	{
		Return mysql_free_result($rs);
	}

}

/************************************************
 *
 * 数据库对象
 * @param	Object	$DBS	链接对象集
 */
class DB
{
	public $DBS;
	public function DBConnect($Config) 
	{
		$this -> DBS[$Config['ModelTag']] = @mysql_connect($Config['Host'], $Config['User'], $Config['Password']);
		(!$this -> DBS[$Config['ModelTag']] && Amysql::AmysqlNotice(mysql_error() . ' Mysql链接出错，请配置/Amysql/config.php文件。'));

		(!empty($Config['DBname']) && mysql_select_db($Config['DBname'], $this -> DBS[$Config['ModelTag']]));
		$CharSet = str_replace('-', '', $Config['CharSet']);
		mysql_query("SET NAMES '$CharSet'", $this -> DBS[$Config['ModelTag']]);		
	}
}
