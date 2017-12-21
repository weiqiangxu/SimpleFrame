<?php

//加载MVC公共配置文件
$com_config = require(str_replace('\\','/', dirname ( dirname ( __FILE__ ) ) . '/'). '/mvc/common.config.php');

//当前模块根路径 D:/webserver/www/SimpleFrame/admin/
$SitePath = str_replace('\\','/', dirname(__FILE__) . '/');

// $_SERVER['SCRIPT_NAME']输出/Frame/admin/index.php
// trim(dirname($_SERVER['SCRIPT_NAME']), '\/')输出Frame/admin


$config = [
	//调试模式总开关
	'DEBUG' => true, 
	//模板调试模式
	'DEBUG_TPL' => false, 
	//站点名称,必须，于于自动生成时的目录创建(例如缓存)
	// 'SITE_NAME' => 'simple_admin', 
	//显示运行花费时间
	'SHOW_RUN_TIME' => false, 

	// 当前模块-入口文件HTTP地址，任何Action路由需要由此前缀
	'ADMIN_ACTION_URL'=>'http://'.$_SERVER['HTTP_HOST'].'/'.trim(dirname($_SERVER['SCRIPT_NAME']), '\/').'/index.php/',

	// 管理模块用的H-UI admin静态资源路径(在此感谢h-admin提供技术支持的H-ui admin模板)
	'PATH_HUI_ADMIN' => 'http://'.$_SERVER['HTTP_HOST'].'/'.trim(dirname($_SERVER['SCRIPT_NAME']), '\/').'/static/huiadmin/',


	// 管理模块用的admin静态资源路径,只想admin->static,
	// 为什么与ADMIN_ACTION_URL相比去除了index.php呢
	// 那么路由就会调用jsAction/janame或者cssAction/cssname方法
	// 这里路由是：http地址被apache解析到服务器之中admin目录，加上static文件夹指向，若admin后缀为null才会自动索引到index前缀的文件输出
	'PATH_STATIC_ADMIN' => 'http://'.$_SERVER['HTTP_HOST'].'/'.trim(dirname($_SERVER['SCRIPT_NAME']), '\/').'/static/',


    /*
     *如果开启LANGUAGE，第一个为语言单元
    'LANGUAGE' => ['DEFAULT'=>'cn', 'LIST'=>['cn','en','de','fr','py','es']],
	*/

	/*自定义规则
	  * 规则 => [目标，缓存时间]
	  * 缓存时间 单位 秒，0 为不缓存, 9 为永久缓存.
	 */
	'ROUTE' => [
			'models\/(\w+)\/(\d+)\/(\w+)_(\d+)\/' => ['test/index/index/$1/$2/$3/$4/',0],
			'test\/tpl(\/)?' => ['test/index/tpl/',0],
	],

	/*相对不太变化的配置*/
	'PATH_ROOT' => $SitePath,                     //当前模块-根目录
	'PATH_MVC' => $com_config['ROOT_MVC'],        //MVC目录
	'PATH_CACHE' => $SitePath.'cache/',        //缓存目录
	'PATH_ACTION' => $SitePath.'action/',         //当前模块-控制器目录
	'ACTION_FILE_TAG' => 'Action',                //视图类与文件名后缀标识
	'PATH_MODULE' => $SitePath.'module/',         //模块目录
	'PATH_TPL' => $SitePath.'tpl/',               //当前模板-HTML模板目录
];

//本站自动加载类处理,如果系统CLASSMAP中有相同的类名，则该类名后面的值将替换系统的
$config['CLASSMAP'] = [
    //类名=> 类文件名物理位置
    //例 'LibXXX'  => $config['PATH_LIB'] . 'sublibX/LibXXX.php'
];
$config['AUTOLOAD'] = [
    //标识=> ['path'=>路径,'ext'=>文件后缀]
    'Mod' => ['path'=>$config['PATH_MODULE'], 'ext'=> '.php'],
];

// 定义一个变量声明需要实现静态化的action,比如凡是商品详情item/index就校验有无缓存
$config['CACHE_MODULE_TPL'] = [
	'/item/index',
	'/picture/pictureshow'
];


return array_merge_recursive($com_config, $config);
