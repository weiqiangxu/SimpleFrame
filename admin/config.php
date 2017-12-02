<?php

//公共配置文件
$com_config = require(str_replace('\\','/', dirname ( dirname ( __FILE__ ) ) . '/'). '/mvc/common.config.php');

//当前站点根路径
$SitePath = str_replace('\\','/', dirname(__FILE__) . '/');
! defined ( 'LibPage' ) && define ( 'LibPage', 'all' );
$config = [
	'DEBUG' => true, //调试模式总开关
	'DEBUG_TPL' => false, //模板调试模式
	'SITE_NAME' => 'b2c_admin', //站点名称,必须，于于自动生成时的目录创建(例如缓存)
	'ADMIN_URL' => '/SimpleFrame/admin/',
	'ADMIN_URI' => '/SimpleFrame/admin/index.php/',
	'SHOW_RUN_TIME' => false, //显示运行花费时间

    /*
     *如果开启LANGUAGE，第一个为语言单元
    'LANGUAGE' => ['DEFAULT'=>'cn', 'LIST'=>['cn','en','de','fr','py','es']],
	*/

	/*深度路径，如action和tpl下的子目录
	'DEPTH_PATH' => [
			'admin_easyui'       => 'admin_easyui',
			'smart'        => 'admin_smart',
			'test'               => 'test',
	],*/

	/*自定义规则
	  * 规则 => [目标，缓存时间]
	  * 缓存时间 单位 秒，0 为不缓存, 9 为永久缓存.
	 */
	'ROUTE' => [
			'models\/(\w+)\/(\d+)\/(\w+)_(\d+)\/' => ['test/index/index/$1/$2/$3/$4/',0],
			'test\/tpl(\/)?' => ['test/index/tpl/',0],
	],
	

    /*远程数据调用
    'RPC'=>[
        //标签 =>['URL'=>网址接口, 'IS_GZ'=>是否启用gzip传输(0不启用，1启用)]
        'TCD' => ['URL'=>'http://tcd.hzqghost.com/api','IS_GZ'=> 0],
    ],*/

	/*相对不太变化的配置*/
	'PATH_ROOT' => $SitePath,                     //网站根目录
	'PATH_MVC' => $com_config['ROOT_MVC'],        //MVC目录
	'PATH_CACHE' => $SitePath.'../cache/',        //缓存目录
	'PATH_ACTION' => $SitePath.'action/',         //视图目录
	'ACTION_FILE_TAG' => 'Action',                //视图类与文件名后缀标识
	'PATH_MODULE' => $SitePath.'module/',         //模块目录
	'PATH_TPL' => $SitePath.'tpl/'                //模板目录
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

return array_merge_recursive($com_config, $config);
