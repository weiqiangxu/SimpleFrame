<?php
//公共配置文件
$comConfig = require(str_replace('\\','/', dirname ( dirname ( __FILE__ ) ) . '/'). '/mvc/common.config.php');
//当前站点根路径
$sitePath = $comConfig['ROOT'].'home/';
$config = [
	'DEBUG' => true, //调试模式总开关
	'SITE_NAME' => 'b2c', //站点名称,必须，于于自动生成时的目录创建(例如缓存)
	'SHOW_RUN_TIME' => false, //显示运行花费时间

	/*自定义规则
	  * 规则 => [目标，缓存时间]
	  * 缓存时间 单位 秒，0 为不缓存, 9 为永久缓存.
	 */
	'ROUTE' => [],

	/*相对不太变化的配置*/
	'PATH_ROOT' => $sitePath,                     //网站根目录
	'PATH_MVC' => $comConfig['ROOT_MVC'],        //MVC目录
	'PATH_CACHE' => $sitePath.'cache/',        //缓存目录
	'PATH_ACTION' => $sitePath.'action/',         //视图目录
	'ACTION_FILE_TAG' => 'Action',                //视图类与文件名后缀标识
	'PATH_TPL' => $sitePath.'tpl/'                //模板目录
];
return array_merge($comConfig, $config);