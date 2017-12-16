<?php
/**
  * @method 公共配置文件，例如数据、公共接口.....
  * @remark 调用方法：mvc::$cfg['ROOT'] ($comConfig['ROOT'])
  * @author xu
  * @copyright 2017/12/09
  */

//根路径（mvc上一级路径）
$comConfig['ROOT'] = str_replace ( '\\', '/', dirname ( dirname ( __FILE__ ) ) . '/' );
$comConfig['ROOT_MVC'] = $comConfig['ROOT'].'mvc/';


//文件中心,在另mvc框架之中,http接口,用token校验接口权限
$comConfig['FILE_CENTER'] = [
	//商城
	'smartyframe'=>[
		'token'=>'iamtoken',
		'url_del'=>'http://127.0.0.1/www/file_center/center/delete.php?site=smartyframe',
		'url_put'=>'http://127.0.0.1/www/file_center/center/write.php?site=smartyframe',
		'url_get'=>'http://127.0.0.1/www/file_center/center/read.php?site=smartyframe',
		'get_uri'=>'&type=product'
	]
];

//数据库配置
$comConfig['DB'] = [
	//Oracle数据库    
	'OR' => [
			'db_type'	=>'oci',        //连接数据库类型
			'db_host'	=>'127.0.0.1', //地址
			'db_port'	=>'1521',        //端口
			'db_name'	=>'db_name',            //数据库名称
			'db_user'	=>'root',       //用户
			'db_pass'	=>'123456',     //密码
			'db_charset'=>'AL32UTF8',       //字符集tecdoc_2012_11
	],
	//MySQL数据库   
	'PRO' => [
		'db_type'   =>'mysql',        //连接数据库类型
		'db_host'   =>'127.0.0.1',    //地址
		'db_port'   =>'3306',        //端口
		'db_name'   =>'user_database',//数据库名称
		'db_user'   =>'root',       //用户
		'db_pass'   =>'123456',     //密码
		'db_charset'=>'utf8'       //字符集
	],
];

//自动加载类处理,如果系统CLASSMAP中有相同的类名，则该类名后面的值将替换系统的
$comConfig['CLASSMAP'] = [
    //类名=> 类文件名物理位置
    //例 'LibXXX'  => $comConfig['ROOT_MVC'] . 'lib/LibXXX.php'
];

//接口配置(与MVC同级的文件夹需要在此设置否则不会加载进来)
$comConfig['AUTOLOAD'] = [
    //标识=> ['path'=>路径,'ext'=>文件后缀]
    'Pro' =>  ['path'=>$comConfig['ROOT'].'mod_pro/', 'ext'=> '.class.php']
];

return $comConfig;