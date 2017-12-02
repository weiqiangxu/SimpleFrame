<?php
class IndexAction
{
	
	function index()
	{

		LibTpl::Set('title', 'Smartyframe-管理系统');
		LibTpl::Put();
	}

}