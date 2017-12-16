<?php
class IndexAction
{
	/**
		* 后台管理首页
		* @author xu
		* @copyright 2017-12-16
	*/
	function index()
	{
		LibTpl::Set('title', '首页-管理系统');
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