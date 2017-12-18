<?php
class pictureAction
{
	/**
		* 图片管理主页
		* @author xu
		* @copyright 2017-12-16
	*/
	function index()
	{
		LibTpl::Set('title', '图片管理-管理系统');
		LibTpl::Put();
	}
	
	/**
		* 查看图片
		* @author xu
		* @copyright 2017-12-16
	*/
	function pictureshow()
	{
		LibTpl::Set('title', '图片管理-查看图片');
		LibTpl::Put();
	}


}