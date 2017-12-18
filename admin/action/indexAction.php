<?php
class indexAction
{

	//由此变量限定当前tpl/index文件下面的模板专用的js以及cs文件
	private $HEAD_CSSJS = ['css'=>['css/index.css'], 'js'=>['js/index.js'], 'plugin'=>[]];

	function __construct()
	{
		LibTpl::Set('HEAD_CSSJS', $this->HEAD_CSSJS);
	}

	/**
		* 后台管理首页
		* @author xu
		* @copyright 2017-12-16
	*/
	function index()
	{

		// 给将当前function输出对应模板加上某css或者js文件
		$this->HEAD_CSSJS['js'][] = 'js/index.js';
		LibTpl::Set('HEAD_CSSJS', $this->HEAD_CSSJS);

		//设置meta信息,用于seo优化
		$headArr = [
			'title'=>'后台管理系统-首页',	
			'keyword'=>'一个简单的框架-后台管理首页',	
			'des'=>'简单的框架，精简原生，MVC模式，敏捷开发。'
		];

		LibTpl::Put();
	}


	/**
		* 后台管理欢迎页面
		* @author xu
		* @link  /index/welcome
		* @copyright 2017-12-16
	*/
    function welcome()
    {
        LibTpl::Put();
    }
}