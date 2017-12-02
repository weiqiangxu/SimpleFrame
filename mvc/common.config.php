<?php
/**
  * @method 公共配置文件，例如数据、公共接口.....
  * @remark 调用方法：mvc::$cfg['ROOT'] ($comConfig['ROOT'])
  * @author soul
  * @copyright 2017/3/18
  */

//根路径（mvc上一级路径）
$comConfig['ROOT'] = str_replace ( '\\', '/', dirname ( dirname ( __FILE__ ) ) . '/' );
$comConfig['ROOT_MVC'] = $comConfig['ROOT'].'mvc/';
$comConfig['ROOT_SHOP_SYS'] = $comConfig['ROOT'].'shop/system/';

//外部网点配置
$comConfig['HOST'] = [
	'smarty'=>'http://localhost:8090/',
];

//公用cookie
$comConfig['COOKIE_DOMAIN'] = '127.0.0.1';


//文件中心
$comConfig['FILE_CENTER'] = [
	//商城
	'smartyframe'=>[
		'token'=>'e4365082cc6c46d78c12e699a2017smartyframe',
		'url_del'=>'http://127.0.0.1/www/file_center/center/delete.php?site=smartyframe',
		'url_put'=>'http://127.0.0.1/www/file_center/center/write.php?site=smartyframe',
		'url_get'=>'http://127.0.0.1/www/file_center/center/read.php?site=smartyframe',
		'get_uri'=>'&i=115'
	]
];


//数据库配置
$comConfig['DB'] = [
	//smartyframe 主库    
	'smartyframe' => [
			'db_type'	=>'oci',        //连接数据库类型
			'db_host'	=>'192.168.1.5', //地址
			'db_port'	=>'1521',        //端口
			'db_name'	=>'',            //数据库名称
			'db_user'	=>'SimpleFrame',       //用户
			'db_pass'	=>'SimpleFrame007',     //密码
			'db_charset'=>'AL32UTF8',       //字符集tecdoc_2012_11
	],
	//smartyframe 公共库 （每天自动同步并切库）   
	'COM' => [
			'db_type'	=>'oci',           //连接数据库类型
			'db_host'	=>'192.168.1.5',   //地址
			'db_port'	=>'1521',          //端口
			'db_name'	=>'',              //数据库名称
			'db_user'	=>'SimpleFrame_COMMON_A',  //用户  YP2BC_COMMON_A / YP2BC_COMMON_B两个用户自动切换
			'db_pass'	=>'SimpleFrame_COMMON_007',        //密码
			'db_charset'=>'AL32UTF8',       //字符集tecdoc_2012_11
	]
];

//自动加载类处理,如果系统CLASSMAP中有相同的类名，则该类名后面的值将替换系统的
$comConfig['CLASSMAP'] = [
    //类名=> 类文件名物理位置
    //例 'LibXXX'  => $comConfig['ROOT_MVC'] . 'lib/LibXXX.php'
];

//接口配置
$comConfig['AUTOLOAD'] = [
    //标识=> ['path'=>路径,'ext'=>文件后缀]
    'smartyframe' =>  ['path'=>$comConfig['ROOT'].'mod_smartyframe/', 'ext'=> '.class.php'],
    'Com' =>  ['path'=>$comConfig['ROOT'].'mod_com/', 'ext'=> '.class.php'],
    'Yp' =>  ['path'=>$comConfig['ROOT'].'mod_yp/', 'ext'=> '.class.php'],
    'Ypw' => ['path'=>$comConfig['ROOT'].'mod_ypw/', 'ext'=> '.class.php'],
    'Oth' => ['path'=>$comConfig['ROOT'].'mod_oth/', 'ext'=> '.class.php'],
	'Ads' => ['path'=>$comConfig['ROOT'].'mod_ads/', 'ext'=> '.class.php'],
	'Shop' => ['path'=>$comConfig['ROOT_SHOP_SYS'].'module/', 'ext'=> '.class.php'],
	
];

return $comConfig;