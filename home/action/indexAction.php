<?php
class indexAction
{
	//只能调用/static/目录下的 js | css | 控件[webuploader、datetimepicker、ueditor]
	private $HEAD_CSSJS = ['css'=>['css/style.css'], 'js'=>['js/index.js'], 'plugin'=>[]];

	function __construct()
	{
		LibTpl::Set('HEAD_CSSJS', $this->HEAD_CSSJS);
	}

	function index()
	{
		$Params = mvc::$URL_PARAMS;

		//页面head
		$headArr = [
			'title'=>'标题',	
			'keyword'=>'关键词',	
			'des'=>'描述'
		];

		LibTpl::Set('headArr', $headArr);
		$this->HEAD_CSSJS['js'][] = 'js/slider.js';
		LibTpl::Set('HEAD_CSSJS', $this->HEAD_CSSJS);
		LibTpl::Put();
	}
}