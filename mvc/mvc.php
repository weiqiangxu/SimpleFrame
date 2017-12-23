<?php
/*
<class>
	<cate></cate>
	<name>MVC(MVC框架)</name>
	<remark>
		框架入口文件
	</remark>
	<author>hzq 2013-4-4</author>
</class>
*/
final class mvc 
{
	/* 配置 */
	public static $cfg 	        = null;
	/* 加载类名 */
	public static $URL_CLASS 	    = null;
	/* 加载类路径 */
	public static $URL_CLASS_PATH = null;
	/* 加载类方法 */
	public static $URL_METHOD     = null;
    /* 加载传参 */
    public static $URL_PARAMS     = null;
    /* 当前语言 */
    public static $LANGUAGE       = '';
    /* 实际执行路径 */
    public static $PATH_INFO       = '';
	/* 类地图 */
	private static $classmap      = null;
	/* 当前页面缓存时间 */
	// private static $cachetime     = 0;
	
	/*
	<method>
		<name>Init(初始化)</name>
		<remark>自动加载,初始化类地图</remark>
		<author>xu 2017-12-18</author>
	</method>
	*/
	private static function init() 
	{
		// 自动装载 # 注册__autoload()加载器函数。（__autolad魔术方法已经不被推荐使用并于PHP7.2弃用）
		spl_autoload_register ( array ( __CLASS__, 'autoload' ) );
		
		 #公共类映射地图
		self::$classmap = array (
			/* 公共函数库 */
			'LibFc' 	=> self::$cfg['PATH_MVC'] . 'lib/LibFc.php',
			/* 数据库DBhelper */
			'LibDb' 	=> self::$cfg['PATH_MVC'] . 'lib/LibDb.php',
			/* 目录处理 */
			'LibDir' 	=> self::$cfg['PATH_MVC'] . 'lib/LibDir.php',
			/* 目录与文件处理 */
			'LibFile' 	=> self::$cfg['PATH_MVC'] . 'lib/LibFile.php',
			/* 图片处理 */
			'LibImage' 	=> self::$cfg['PATH_MVC'] . 'lib/LibImage.php',
            /* 日志处理 */
			'LibLog' 	=> self::$cfg['PATH_MVC'] . 'lib/LibLog.php',
			/* 模板类 */
			'LibTpl'    => self::$cfg['PATH_MVC'] . 'lib/LibTpl.php',
            /* 缓存 */
            'LibCache'  => self::$cfg['PATH_MVC'] . 'lib/LibCache.php',
			/* 分页 */
            'LibPage'  => self::$cfg['PATH_MVC'] . 'lib/LibPage.php',
			/* SimpleFrameAPI */
            'LibYpApi'  => self::$cfg['PATH_MVC'] . 'lib/LibYpApi.php',
			/* 采集 */
            'LibCurl'    => self::$cfg['PATH_MVC'] . 'lib/LibGet.php',
			'LibRpc'     => self::$cfg['PATH_MVC'] . 'lib/LibGet.php',
			'LibHttp'    => self::$cfg['PATH_MVC'] . 'lib/LibHttp.php'
		);

		// 以上classsmap为公共类，调用类名触发autoload自动加载器引用对应的路径类文件并实例化
		// 一下merge进来的CLASSMAP是模块需要自动加载的类，在模块config引用即可
        if( isset(self::$cfg['CLASSMAP'])) {
            array_merge(self::$classmap, self::$cfg['CLASSMAP']);
        }
	}
	
	/*
	<method>
		<name>autoload(自动装载)</name>
		<remark>实例化类的时候会调用此函数</remark>
		<Parameter>
			<para name="$var" type="string">路径/类/方法 (如 pro/test/do1)</para>
		</Parameter>
		<author>xu 2017-12-18</author>
	</method>
	*/
	public static function autoload($class)
	{
		/* 判断地图(类)是否已加载 */
		if (isset(self::$classmap[$class])) 
		{
			/* 自动加载主库 */
			$classfile = self::$classmap [$class];
			require_once ($classfile);
			return;
		}
		else
		{
			/* 项目模块运行时候，项目模块的接口类的自动加载 */
            foreach(self::$cfg['AUTOLOAD'] as $k => $v){
                if(strpos($class, $k)===0) {
                    $classfile = $v['path'] . $class . $v['ext'];
                }
            }
			/* 判定文件是否存在 */
			if (is_file ($classfile)){
				require_once ($classfile);
			}else{
				// 抛出异常，这里diy异常处理函数，以更为友好的形式的内容呈现与用户
				throw new Exception ( $classfile .' 不存在' );
			}
		}
	}

    /**
     * 匹配网址路由规则
     */
    private static function route($PathInfo){
    	// 路由为数组的情况
    	// example：
		// if(is_array($PathInfo))
		// {
		// 	foreach(self::$cfg['ROUTE'] as $pattern=>$replace)
		// 	{
		// 		$pattern = '/'.$pattern.'/i';
		// 		$tmp = preg_replace($pattern, $replace[0],$PathInfo);
		// 		if($tmp!=$PathInfo){
		// 			$PathInfo = $tmp;
		// 			self::$cachetime = $replace[1];
		// 			break;
		// 		}
		// 	}
		// }
        return $PathInfo;
    }

	/*
	<method>
		<name>AnalyzeUrl(分析网址)</name>
		<remark>edit 2013/10/12 Soul 添加前台路由</remark>
		<Parameter>
			<para name="$ClassMethod" type="string">路径/类/方法 (如 pro/test/do1)</para>
			<para name="$QueryString" type="string">网址传参 ( 如 b=2&c=3&a=1 )</para>
		</Parameter>
		<exp.>
			$ClassMethod = 'pro/test/do1';
			$QueryString = 'b=2&c=3&a=1';
			$ReturnData  = MVC::AnalyzeUrl($ClassMethod, $QueryString);
			// array(
			//	'ClassPath' => 'pro', 'Class' => 'test', 'Func' => 'do1',
			//	'Params' => array ( 'b' => 2, 'a' => 1)
			//);
		</exp.>
		<return>
			@return array(
				'ClassPath'=>'路径','Class'=>'类名','Func'=>'类模块','Params'=>'传参数组'
			);
		</return>
		<author>hzq 2013-4-4</author>
	</method>
	*/
    public static function analyze_url($PathInfo, $QueryString)
	{
		/*
		*URL
		*1、part/show?name=abc 
		*2、path/part/show/abc/?name=abc
		*3、en/path/part/show/abc/?name=abc
		*
		*/
        self::$PATH_INFO = self::route($PathInfo);
        $ClassPath = '';
		$Params    = array ();

		// 以下后缀.html用于伪静态化，已经去除，当前不再添加.html后缀
        //self::$PATH_INFO = trim(str_replace('.html','',self::$PATH_INFO), '/');

        // '/picture/index'=>'picture/index'
        self::$PATH_INFO = trim(self::$PATH_INFO, '/');
       
		/* 对Url网址进行拆分 */
		// array('picture','index')
		$path_info_arr = explode ( '/', self::$PATH_INFO );


        /* 判断语言单元*/
        if( isset(self::$cfg['LANGUAGE']))
        {
            $routekey = $path_info_arr[0];
            if(in_array(strtolower($routekey), self::$cfg['LANGUAGE']['LIST'])) {
                array_shift($path_info_arr);
                self::$LANGUAGE = $routekey;
                $routekey = $path_info_arr[0];
                self::$PATH_INFO = implode('/',$path_info_arr);
            }else{
                self::$LANGUAGE = self::$cfg['LANGUAGE']['DEFAULT'];
            }
		}

		/*判断子目录单元,深度路径,使用子目录*/
		if( isset(self::$cfg['DEPTH_PATH']) && array_key_exists($routekey,self::$cfg['DEPTH_PATH'])){
			$ClassPath = self::$cfg['DEPTH_PATH'][$routekey];
			array_shift($path_info_arr);
		}

        /* 返回类名 */
        $Class = array_shift ( $path_info_arr );

        /* 返回方法名 */
        $Func = array_shift ( $path_info_arr );
       

		//网址传参
		// 当路由形式入thinkPHP，index.php/picture/index/id/12/catid/14时候
		// 解析为数组Array ( [0] => id [1] => 12 [2] => catid [3] => 14 )
		// 之所以对每一个urldecode因为有时候链接参数被url编码
		foreach($path_info_arr as $v)
		{
			$Params[] = urldecode($v);
		}

		// 处理路由？后面参数
		if ($QueryString != '')
		{
			/* 初始化传参数组 */
			/* 对传参字符进行拆分 */
			$query_str_arr = explode ( '&', trim ( $QueryString, '&' ) );
			foreach ( $query_str_arr as $v ) 
			{
				// 将参数与参数名组成对应数组
				$tmp = explode ( '=', $v );
				$Params [$tmp [0]] = urldecode($tmp [1]);
			}
		}

		/* return 返回包含路径, 类名, 类模块, 和传参数组 */
		return array (
			'ClassPath' => $ClassPath,
			'Class'     => $Class,
			'Func'      => $Func,
			'Params'    => $Params 
		);
	}

	/*
	<method>
		<name>Execute(执行MVC)</name>
		<remark>执行MVC函数,调用类模块,并将参数传递过去</remark>
		<return>@return void</return>
		<author>xu 2017-12-18</author>
	</method>
	*/
	public static function execute(array $config) 
	{

		//开始运行时间
		$time_start = time(); 
		self::$cfg = $config;
		/* 初始化类地图 */
		self::init ();
		
		/* 分拆网址 */
		$Info = self::analyze_url ( $_SERVER['PATH_INFO'], $_SERVER['QUERY_STRING'] );
		self::$URL_CLASS_PATH = $Info ['ClassPath'] ;
		self::$URL_CLASS 	  = ( $Info ['Class'] == '' ) ? 'index' : $Info ['Class'];
		self::$URL_METHOD     = ( $Info ['Func']  == '' ) ? 'index' : $Info ['Func'];
		self::$URL_PARAMS     = $Info ['Params'];


		// 为路由分析结果类名称加上特定action标志后缀Action获取控制器名称
		$ClassName = self::$URL_CLASS . self::$cfg['ACTION_FILE_TAG'];


		//校验当前请求的action是否为需要缓存的模块，如果是，则输出缓存
		if(in_array($_SERVER['PATH_INFO'], $config['CACHE_MODULE_TPL'])){
			// LibCache::GetPageCache( self::$URL_CLASS_PATH,$ClassName,self::$URL_METHOD,serialize(self::$URL_PARAMS), self::$cachetime);
			LibCache::GetPageCache( self::$URL_CLASS_PATH,$ClassName,self::$URL_METHOD,serialize(self::$URL_PARAMS));
		}

		// 装载控制器类文件,增加模块配置文件定义的该模块action所在文件夹之服务器磁盘地址路由
		$classfile = self::$cfg['PATH_ACTION'];

		// 加入深度路由
		if (self::$URL_CLASS_PATH != '') {
			$classfile .= self::$URL_CLASS_PATH . '/';
		}

		// 拼接获取控制器文件所在路径
		$classfile .= $ClassName.'.php';
        @require_once ($classfile);
		// 执行调用
		$view = new $ClassName ();

		/* 检查类模块方法是否存在 */
		if (false == method_exists ( $ClassName, self::$URL_METHOD )) {
            /* 检查是否是静态单页输出(静态单页要求以 .html 为后缀),如果方法不存在，模板存在直接输出模板，类的初始化方法起作用。 */
            // 就是重定向的时候，允许解析出来的func不存在，直接输出对应的html静态页面
            if( substr($_SERVER['REDIRECT_URL'], -1 * strlen(self::$URL_METHOD.'.html')) == self::$URL_METHOD.'.html'){
                LibTpl::Put();
                exit;
            }
			throw new Exception ( self::$URL_CLASS_PATH.'/'.$ClassName.' 类中方法 '.self::$URL_METHOD.' 不存在' );
		}

		$params = array ();
		if (! empty ( self::$URL_PARAMS )) 
		{
			// 判定此时路由有无?后接参数
			if($_SERVER ['QUERY_STRING']!=''){
				/* 反射, 获取执行的function参数对应关系 */
				$reflector   = new ReflectionMethod ( $ClassName, self::$URL_METHOD );
				$func_params = $reflector->getParameters ();
				// example:picture/index => index($id,$zid)
				// res:Array ( [0] => ReflectionParameter Object ( [name] => id ) [1] => ReflectionParameter Object ( [name] => zid ) )
				// 调用反射去除在url传递的不需要的参数
				foreach ( $func_params as $k => $v ) 
				{
					$pv = self::$URL_PARAMS [$v->name];
					if ($pv != '') {
						$params [$k] = $pv;
					}
				}
			}else{
				$params = self::$URL_PARAMS;
			}
		}

		// 这里需要保证或php.ini里面的缓冲区足够大否则会出现缓存下来的静态页面部分缺失
		/* 调用类模块 */
		ob_start ();
		ob_end_clean ();
		// 回调执行action->method
        call_user_func_array ( array ( $view, self::$URL_METHOD ), $params );
        $out  =  ob_get_contents ();
		ob_end_flush();

		//如果属于需要静态化的模块就写缓存
		if(in_array($_SERVER['PATH_INFO'], $config['CACHE_MODULE_TPL'])){
			LibCache::SetPageCache(self::$URL_CLASS_PATH,$ClassName,self::$URL_METHOD,serialize(self::$URL_PARAMS),$out);
		}
		//显示运行花费时间
		if(self::$cfg['DEBUG'] && self::$cfg['SHOW_RUN_TIME']){
            $msg = '总运行花费时间：'. (time() - $time_start) . ' 秒<br/>';
            $msg.= '实际执行路径：'.mvc::$PATH_INFO;
            $msg.= '<pre>参数mvc::$URL_PARAMS：';
            $msg.= var_export(mvc::$URL_PARAMS, true);
            $msg.= '</pre>';
			mvc_echo($msg ,false);
		}
	}
}

//mvc信息输出
function mvc_echo($msg,$isShowServer=false){
	echo '<div style="clear:both;word-wrap: break-word;font-family: Arial;font-size: 14px;border: 2px solid #c00;border-radius: 20px;margin:20px;padding: 20px;background-color:#FFFFE1">';
	echo $msg;
	if($isShowServer){
		echo '<pre>';
		print_r($_SERVER);
		echo '</pre>';
	}
	echo '</div>';
}

//在调试模式错误处理
function _rare_shutdown_catch_error_debug(){

	$_error=error_get_last();
	if($_error && in_array($_error['type'],array(1,4,16,64,256,4096,E_ALL))){
		$msg = '';
		$msg .= '<div>网址:'.$_SERVER['REDIRECT_URL'].'</div>';
		$msg .= '<div>入口: <strong>'.mvc::$URL_CLASS_PATH.'/'.mvc::$URL_CLASS.'/'.mvc::$URL_METHOD.'</strong></div>';
		$msg .= '<div>子目录: <strong>'.mvc::$URL_CLASS_PATH.'</strong></div>';
		$msg .= '<div>类: <strong>'.mvc::$URL_CLASS.'</strong></div>';
		$msg .= '<div>方法: <strong>'.mvc::$URL_METHOD.'</strong></div>';
		$msg .= '<div>参数: <strong>'. json_encode(mvc::$URL_PARAMS) .'</strong></div>';
		$msg .= '<div style="color:#c00">'.$_error['message'].'</div>';
		$msg .= '文件: <strong>'.$_error['file'].'</strong></br>';
		$msg .= '在第: <strong>'.$_error['line'].'</strong> 行</br>';
		mvc_echo($msg,true);
		exit;
	}
}


//非调试模式错误处理
function _rare_shutdown_catch_error(){
	$_error=error_get_last();
	$str = sprintf('[%s] file:%s, line:%s,msg:%s',date ( "Y-m-d H:i:s" ),$_error['file'],$_error['line'],$_error['message']);
	//error_log ( $str ,  3 ,  "my-errors.log" );//需要指定绝对路径,当需要调试线上站点时，可以开启
	exit;
}

if($config['DEBUG']){
	register_shutdown_function("_rare_shutdown_catch_error_debug");
	//set_exception_handler('exception_handler');
	error_reporting(E_ALL& ~E_NOTICE);
}else{
	register_shutdown_function("_rare_shutdown_catch_error");
	error_reporting(0);
}
