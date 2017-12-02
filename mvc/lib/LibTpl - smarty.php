<?php
/**
 * @author HzqGhost  QQ:313143468
 * @version 1.0.0
 *
*/
require_once (mvc::$cfg['PATH_MVC'] . 'smarty/libs/Autoloader.php');
require_once (mvc::$cfg['PATH_MVC'] . 'smarty/libs/SmartyBC.class.php');
class LibTpl
{
	/* 模板目录 */
	protected static $tplpath;
	
	/* Smarty实例对象 */
	protected static $smarty = null;

	protected static function Init(){
		if ( is_null ( self::$smarty ) ) 
		{
			self::$tplpath = mvc::$cfg['PATH_TPL'];
			Smarty_Autoloader::registerBC(true); //注册smarty自动加载函数
			//self::$smarty = new Smarty ();
			//允许原生php Soul 2013/12/18
			self::$smarty = new SmartyBC ();
			/* 模板目录 */
			self::$smarty->setTemplateDir ( self::$tplpath . 'templates/' );
			/* 编译目录 */
			self::$smarty->setCompileDir ( self::$tplpath . 'templates_c/' );
			/* 配置目录 */
			self::$smarty->setConfigDir ( self::$tplpath . 'configs/' );
			/* 缓存目录 */
			self::$smarty->setCacheDir ( self::$tplpath . 'cache/' );

			/* 是否开启调试 */
			self::$smarty->debugging = mvc::$cfg['DEBUG'] && mvc::$cfg['DEBUG_TPL'];
			/* 缓存时间 */
			self::$smarty->caching = false;
			self::$smarty->setCacheLifetime ( 3600 );
			/* Smarty左边界符 */
			self::$smarty->left_delimiter = '{';
			/* Smarty右边界符 */
			self::$smarty->right_delimiter = '}';
			
			self::$smarty->php_handling = 3;
			self::SetSession();
		}
	}

	public static function Set($key, $value = null) 
	{
		self::Init ();
		self::$smarty->assign ( $key, $value );
	}

	public static function Put($TplFile=''){
		self::Init ();
        /* 当参数为空时，取默认的 action/function.html 模板路径 */
        if(empty($TplFile)){
            if(!empty(mvc::$URL_CLASS_PATH)) $TplFile = mvc::$URL_CLASS_PATH . '/';
            $TplFile .= mvc::$URL_CLASS .'/'. mvc::$URL_METHOD . '.html';
        }
		self::$smarty->display($TplFile);
		/* 调试模式下 清除所有模板编译与缓存 ,便于测试 */
		if (mvc::$cfg['DEBUG']) {
			self::Clear();
		}
	}
	
	/*
	*得到处理模板后的结果(HTML)
	*add by soul 2016/3/8
	*/
	public static function Fetch($TplFile){
		self::Init ();
		$output = self::$smarty->fetch ($TplFile);
		/* 调试模式下 清除所有模板编译与缓存 ,便于测试 */
		if (mvc::$cfg['DEBUG']) {
			self::Clear();
		}
		return $output;
	}

	protected static function SetSession() 
	{
		$MyGlobals = array ();
		foreach ( $GLOBALS as $k => $v ) 
		{
			/* 注册session变量 */
			if ($k== '_SESSION') 
			{
				$MyGlobals [$k] = $v;
			}
		}
		self::$smarty->assign ( $MyGlobals );
	}

	protected static function Clear() 
	{
		self::Init ();
		/**
		 * 清除模板编译文件
		 * http://www.smarty.net/docs/zh_CN/api.clear.compiled.tpl.tpl
		 */
		self::$smarty->clearCompiledTemplate ();
		
		/**
		 * 清除全部缓存
		 * http://www.smarty.net/docs/zh_CN/api.clear.all.cache.tpl
		 */
		self::$smarty->clearAllCache ();
	}

	/**
	  * @name jump(jump)
	  * @remark  页面跳转
	  * @param  $message    string 提示信息
	  * @param  $status       int     状态
	  * @param  $jumpUrl     string      跳转地址
	  * @param  $wait     int      等待时间	  
	  * @author jian
	  * @copyright 2013/4/3
	  */
    protected static function Jump($message,$status=1,$jumpUrl='',$wait=0)
    {
        //一般跳转提示
        $time = $wait*1000;
        self::Set('waitSecond',$time);// 错误后默认停留3秒
        self::Set('second',$wait);
        self::Set('jumpUrl',$jumpUrl);
		self::Set('PageTitle','温馨提醒');
		self::Set('message',$message);
		$tpl = $status ? 'public/success.html': 'public/error.html';
		if(empty($wait))    
		{
			// 成功操作后默认停留1秒
			$time = $status ? 1000 : 3000;
			self::Set('waitSecond',$time);// 错误后默认停留3秒
			self::Set('second', $time/1000);
		}
		if(empty($jumpUrl)) 
		{
			self::Set("jumpUrl",$_SERVER["HTTP_REFERER"]);// 默认操作成功自动返回操作前页面
		}
		self::put($tpl);
        exit;
    }

	/**
	  * @name Error(Error)
	  * @remark  错误跳转提示
	  * @param  $message    string 提示信息
	  * @param  $jumpUrl     string      跳转地址
	  * @param  $wait     int      等待时间	  
	  * @author jian
	  * @copyright 2013/4/3
	  */
    public static function Error($message,$jumpUrl='',$wait=0)
    {
        self::Jump($message,0,$jumpUrl,$wait);
		exit;
    }
    
    
	/**
	  * @name Success(Success)
	  * @remark  成功跳转提示
	  * @param  $message    string 提示信息
	  * @param  $jumpUrl     string 跳转地址
	  * @param  $wait     int 等待时间	  
	  * @author jian
	  * @copyright 2013/4/3
	  */
    public static function Success($message,$jumpUrl='',$wait=0)
    {
		/*$jumpUrl = empty($jumpUrl)? $_SERVER["HTTP_REFERER"]: $jumpUrl;
		header("Location:".$jumpUrl);
		exit;*/
		self::Jump($message,1,$jumpUrl,$wait);
    }
}