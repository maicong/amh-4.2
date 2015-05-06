<?php

/************************************************
 * Amysql Host - AMH 4.2
 * Amysql.com 
 * @param Object task 任务计划控制器
 * Update:2013-11-01
 * 
 */

class task extends AmysqlController
{
	public $indexs = null;
	public $tasks = null;
	public $notice = null;
	public $top_notice = null;

	// 载入数据模型(Model)
	function AmysqlModelBase()
	{
		if($this -> indexs) return;
		$this -> _class('Functions');
		$this -> indexs = $this ->  _model('indexs');
		$this -> tasks = $this ->  _model('tasks');
	}

	// 默认访问
	function IndexAction()
	{
		$this -> task_set();
	}
	
	// 任务计划设置
	function task_set()
	{
		$this -> title = '任务计划 - AMH';
		$this -> AmysqlModelBase();
		Functions::CheckLogin();

		$this -> status = 'error';
		if (isset($_POST['save_submit']))
		{
			$id = (int)$_POST['crontab_id'];
			$save_status = $this -> tasks -> save_task($id);
			if($save_status)
			{
				$this -> status = 'success';
				$this -> notice = 'ID' . $id . ' : 编辑保存任务计划成功。';
				$_POST = array();
			}
			else
				$this -> notice = '编辑保存任务计划失败。';
		}
		elseif (isset($_POST['task_submit']))
		{
			$insert_status = $this -> tasks -> insert_task();
			if($insert_status)
			{
				$this -> status = 'success';
				$this -> notice = 'ID' . $insert_status . ' : 新增任务计划成功。';
				$_POST = array();
			}
			else
				$this -> notice = '新增任务计划失败。';
		}
		elseif (isset($_GET['del']))
		{
			$id = (int)$_GET['del'];
			$del_status = $this -> tasks -> del_task($id);
			if($del_status)
				{
					$this -> status = 'success';
					$this -> top_notice = 'ID' . $id . ' : 删除任务计划成功。';
				}
				else
					$this -> top_notice = '删除任务计划失败。';
		}
		elseif (isset($_GET['edit']))
		{
			$id = (int)$_GET['edit'];
			$edit_task = $this -> tasks -> get_task($id);
			if(is_array($edit_task) && $edit_task['crontab_type'] != 'ssh')
			{
				foreach ($edit_task as $key=>$val)
				{
					if (in_array($key, array('crontab_minute', 'crontab_hour', 'crontab_day', 'crontab_month', 'crontab_week')))
					{
						$_key = str_replace('crontab_', '', $key);
						if (strpos($val, '/') !== false)
						{
							$_val = explode('/', $val);
							$_val2 = explode('-', $_val[0]);
							$_POST[$_key.'_average_start'] = $_val2[0];
							$_POST[$_key.'_average_end'] = isset($_val2[1]) ? $_val2[1] : '*';
							$_POST[$_key.'_average_input'] = $_val[1];
							$_POST[$_key.'_select'] = '/';
						}
						elseif (strpos($val, '-') !== false)
						{
							$_val = explode('-', $val);
							$_POST[$_key.'_period_start'] = $_val[0];
							$_POST[$_key.'_period_end'] = $_val[1];
							$_POST[$_key.'_select'] = '-';
						}
						elseif (strpos($val, ',') !== false)
						{
							$_POST[$_key.'_respectively'] = explode(',', $val);
							$_POST[$_key.'_select'] = ',';
						}
						else
						{
							$_POST[$_key.'_time'] = $val;
							$_POST[$_key.'_select'] = '*';
						}
					}
					else
					{
						$_POST[$key] = $val;
					}
				}
				$this -> edit_task = true;
			}
			else
			{
				$this -> top_notice = 'WEB不可编辑的任务计划。';
			}
		}
		
		$this -> crontab_list = $this -> tasks -> get_task_list();
		$this -> indexs -> log_insert($this -> top_notice . $this -> notice);
		$this -> _view('crontab');
	}

}

?>