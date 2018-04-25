<?php
include 'lib/vendor/autoload.php';
use Overtrue\Pinyin\Pinyin;

/**
  * @name LibFc(公共函数类)
  * @author Soul
  * @copyright 2013-3-21
  */
class LibFc
{
	/**
	  * @name Escape(编码特殊字符)
	  * @remark  编码特殊字符
	  * @exp 	Escape("a'b")
	  * @param  $Data    string  需要处理的字符串
	  * @param  $Filter    string  是否过来敏感词语
	  * @author Soul
	  * @copyright 2013-3-18
	  * @return 编码后的字符串
	  */
	static function Escape($Data, $Filter = false)
	{
		if($Filter)
		{//敏感词语
			$FilterArr = include('lib/Filter.php');
			$Data = str_replace($FilterArr, '***', $Data);
		}

		$TrimHtmlTag = 'script|i?frame|style|html|body|title|link|meta';
		$FindArr = array(
			sprintf("/<(%s)([^>]*?)>/isU", $TrimHtmlTag),
			sprintf("/<\/(%s)>/isU", $TrimHtmlTag)
		);
		$ToArr = array('&lt;$1$2&gt;', '&lt;/$1&gt;');
		$Data = preg_replace($FindArr, $ToArr, $Data);

		$ReplaceArr = array('\\'=>'\\\\', "'"=>"''");
		return trim(strtr($Data, $ReplaceArr));
	}

	/**
	  * @name Int(数据是否为整型)
	  * @remark 数据是否为整型
	  * @exp Int("123")
	  * @param  $Data  string 需要处理的字符串
	  * @author Soul
	  * @copyright 2013-3-26
	  * @return
	  		是整形：整数
			非整形：false
	  */
	static function Int($Data)
	{
		$Data = trim($Data);
		if($Data != '' && preg_match('/^[-+]?\d+$/', $Data, $Matches))
		{
			return $Matches[0];
		}
		return false;
	}



	/**
	  * @name Float(数据是否为浮点型)
	  * @remark  数据是否为浮点型
	  * @exp  Float("123.2312")
	  * @param  $data     string 需要处理的字符串
	  * @param  $Precision    int 十进制小数点后数字的数目-进行四舍五入的结果
	  * @author Soul
	  * @copyright 2013-3-26
	  * @return
	  		是整形：浮点数
			非整形：false
	  */	
	static function Float($Data, $Precision=2)
	{
		$Precision = self::Int($Precision);
		if($Precision >= 0)
		{
			if(preg_match('/^([-+]?\d+)\.?\d*$/', trim($Data), $Matches))
			{
				return round($Matches[0], $Precision);
			}
		}
		return false;
	}

	/**
     * 字符串截取，支持中文和其他编码
     * @static
     * @access public
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param string $suffix 截断显示字符
     * @return string
     */
    static public function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
        if(function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice.'...' : $slice;
    }
	

	/**
	  * @name ReturnData(接口返回数据)
	  * @remark 接口返回数据
	  * @exp
	  		ReturnData() 返回false、错误代码0、无错误信息
			ReturnData(true, array(1,2,3)) 返回true、数据为array(1,2,3)
	  * @param  $Status     bool  true| 默认false [可选]
	  * @param  $Data     all  所有类型 默认空 [可选]
	  * @param  $Code     int  错误代码，默认0:无错误代码 [可选]
	  * @author Soul
	  * @copyright 2013-3-30
	  * @return
	  			返回值：array('status'=>false, 'data'=>'错误信息', 'code'=>0);
				array('status'=>true, 'data'=>array(1,2,3,4), 'code'=>0);
	  */
	static function ReturnData($Status=false, $Data='', $Code=0)
	{
		$Status = $Status == true? true:false;
		if($Status)
		{
			$Code = 0;
		}
		else
		{
			$Code = self::Int($Code);
			if($Code === false)
			{
				$Code = 0;
			}
		}
		$Arr = ['status'=>$Status, 'data'=>$Data, 'code'=>$Code];
		if($Status)
		{
			return $Arr;
		}
		else
		{
			//异步请求
			self::ajaxJsonEncode($Arr);
			//费异步请求
			print_r($Arr);exit;
		}
	}

	/**
	  * @method 异步请求输出返回json格式
				mvc常量ISAJAX == 1  echo json_encode($Arr);exit;
				mvc常量ISAJAX == 0  不执行任何东西
	  * @author soul 2017/6/22
	  */
	static function ajaxJsonEncode($dataArr)
	{
		if(ISAJAX){
			if(isset($_GET['callback']))
			{
				echo $_GET['callback'].'('.json_encode($dataArr).')';
			}
			else
			{
				echo json_encode($dataArr);
			}
			exit;
		}
	}


	/**
	  * @name RandNum(生成指定范围的随机数)
	  * @remark  生成指定范围的随机数
	  * @exp  RandNum(100, 200) 返回 100 到 200 之间的随机数
	  * @param  $Min      int 最小值
	  * @param  $Max      int  最大值
	  * @author Soul
	  * @copyright 2013-3-30
	  * @return 指定范围的随机数
	  */
	static function RandNum($Min, $Max)
	{	
		$Min = (int) $Min;
		$Max = (int) $Max;
		return mt_rand($Min, $Max);
	}


	/**
	  * @name GetCrc32(获取十六进制非负数的CRC32)
	  * @remark  获取十六进制的CRC32
	  * @exp  GetCrc32(123);
	  * @param  $Data    string 数据
	  * @author Soul
	  * @copyright 2013-6-17
	  * @return 十六进制的CRC32
	  */
	static function GetCrc32($Data)
	{
		return strtoupper(dechex(crc32($Data)));
	}

	/**
	  * @name GetPathInfo(获取路径的名称和后缀)
	  * @remark  获取路径的名称和后缀
	  * @exp GetPathInfo('我的文件a.jpg');
	  * @param  $FileDir   string 文件路径
	  * @author Soul
	  * @copyright 2013-4-1
	  * @return
	  		成功:array('name'=>'我的文件a', 'ext'=>'jpg')
			失败:false
	  */
	static function GetPathInfo($FileDir)
	{
		$FileDir = trim($FileDir);
		if(!empty($FileDir))
		{
			$ExtStart = strrpos($FileDir, '.');
			if($ExtStart)
			{
				$Ext = strtolower(substr($FileDir, $ExtStart+1));
				$NameStart = strrpos($FileDir, '/');
				if($NameStart === false)
				{
					$NameStart = -1;
				}
				$Name = substr($FileDir, $NameStart+1, $ExtStart-$NameStart-1);
				return array('name'=>$Name, 'ext'=>$Ext);
			}
		}
		return false;
	}
	

	/**
	  * @name GetFileType
	  * @remark  获取文件的类型，需要开启extension=php_fileinfo.dll
	  * @exp  GetFileType('我的文件a.jpg');
	  * @param  $FileDir    string  文件路径
	  * @author Soul
	  * @copyright 2013/7/8
	  * @return image/jpeg...
	  */
	static function GetFileType($FileDir)
	{
		$Finfo = finfo_open(FILEINFO_MIME_TYPE);
		$Type = finfo_file($Finfo, $FileDir);
		finfo_close($Finfo);
		return $Type;
	}

	
	/**
	  * @name Format Number(格式化号码)
	  * @remark 格式化号码
	  * @exp  LibFc::FormatNum("a'b")//ab
	  * @param  $string     string  需要处理的字符串
	  * @author Cell
	  * @copyright 2013-3-18
	  * @return 过滤后的字符串
	  */
	static function FormatNum( $string, $b = '')
	{
		$strmat = self::GetSemiangle($string);
		$strTemp = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ". $b;
		for ($i=0; $i<strlen($string); $i++)
		{
			$c = substr($string,$i,1);
			$pos = strpos($strTemp, $c); 
			if ($pos === false) {
				$strmat = str_replace($c,"", $strmat);
			}
		}
		return strtoupper($strmat);
	}
	
	/**
	  * @method 格式字符串
	  * @param  $string 字符串
	  * @param  $type  0:英文+数字  1:英文+数字+中文
	  * @author soul
	  * @copyright 2017/6/5
	  * @return [["status"]=> bool(true) ["data"]=> [1=>['PART_ID'=>1, 'PART_NAME'=>''], ....] ["code"]=> int(0) ]
	  */
	static function formatStr($string, $type = 0)
	{
		$regArr = [
			0=>'/[^a-zA-Z0-9]/',
			1=>'/[^a-zA-Z0-9\x{4e00}-\x{9fa5}]/u',
		];
		return  preg_replace($regArr[$type], '', $string);
	}

	/**
	  * @name 全角转半角
	  * @param  $str       string 待转换的字符串
	  * @author 强哥
	  * @copyright 2013-06-18
	  * @return string
	  */
	static function GetSemiangle($str)
	{
		$arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4','５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9', 
		'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E','Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J', 'Ｋ' => 'K', 
		'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O','Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T','Ｕ' => 'U', 'Ｖ' => 'V', 
		'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y','Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd','ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 
		'ｈ' => 'h', 'ｉ' => 'i','ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n','ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 
		'ｓ' => 's', 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x', 'ｙ' => 'y', 'ｚ' => 'z', '～'=>'~', '·'=>'`', '！'=>'!',
		'＠'=>'@', '＃'=>'#', '￥'=>'$', '％'=>'%', '……'=>'^', '＆'=>'&', '×'=>'*', '（'=>'(', '）'=>')', '——'=>'_', '－'=>'-', '＋'=>'+', '＝'=>'=', 
		'｛'=>'{', '｝'=>'}', '【'=>'[', '】'=>']', '｜'=>'|', '＼'=>'\\', '：'=>':', '；'=>';', '”'=>'"', '’'=>'\'', '《'=>'<', '，'=>',', '》'=>'>',
		'。'=>'.', '？'=>'?', '、'=>'/'); 
		return strtr($str, $arr); 
	}


	static function StrRed($str,$tag)
	{
		$tag = strtoupper($tag);
		$strTemp="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$fstr= self::FormatNum($str);
		$fstr=str_replace($tag,'{'.$tag.'}',$fstr);
		$j=0;
		$one=false;
		for ($i=0;$i<strlen($str.'|||||');$i++)
		{
			if (substr($str,$i,1)!=substr($fstr,$j,1))
			{
				if ((substr($fstr,$j,1)=='{'||substr($fstr,$j,1)=='}') && strpos($strTemp,substr($str,$i,1))!==false)
				{
					if ($one==false)
					{
						$j++;
					}
					$one=true;
				}
				elseif(strpos($strTemp,substr($str,$i,1))===false)
				{
					$fstr=substr($fstr,0,$j).substr($str,$i,1).substr($fstr,$j);
					$one=false;
				}
			}
			$j++;
		}
		$fstr=str_replace('{','<span class="red">',$fstr);
		$fstr=str_replace('}','</span>',$fstr);
		return $fstr;//.'||'.$str.'||'.$tt;
	}


	
	/**
	  * @name array_save(保存数组文件)
	  * @remark  格式化号码
	  * @exp LibFc::ArraySave("a'b")//ab
	  * @param  $array  array 数组
	  * @param  $file string  文件名
	  * @param  $arrayname   string  数组名
	  * @author Cell
	  * @copyright 2013-3-18
	  * @return 生成数组文件
	  */
	static function ArraySave($array, $file, $arrayname = false)
	{ 
		$data = var_export($array, TRUE);
		if (!$arrayname) {
		   $data = "<?php\n return " .$data.";\n?>";
		} else {
		   $data = "<?php\n " .$arrayname . "=\n" .$data . ";\n?>";
		}
		//$data = str_replace(array("\r\n", "\r", "\n"), "", $data);
		if (PHP5) {
		   return file_put_contents($file,$data);
		}
	}



	/**
	  * @name 汉字转换拼音原始类
	  * @remark
        	汉字转换拼音 Pinyin('汉字转换拼音', 'utf-8') 得到 'hanzizhuanhuanpinyin'
        	原始的拼音函数只能适应大小写类, 又已经在在项目中使用, 为了不造成项目的问题，抽出其中的公共部分,重写 2013/11/18 Cell
	  * @param  $_String     string 待转换的汉字
	  * @param  $_Code       string 需要转换的编码，如果该参数不传则默认要转换的汉字为'utf-8'编码,如需转换成'gb2312'可以将该参数设为除'utf-8'或'UTF-8'的任何字符
	  * @author Cell
	  * @copyright 2013/11/18
	  * @return array
	  */
	static function GetPinYin( $_String, $_Code = 'utf-8' )
	{
        $oPinyin = new Pinyin();
        $_String = mb_convert_encoding($_String, 'utf-8', 'auto');
        return $oPinyin->name($_String);
	}


	/**
	  * @name PinYin() 汉字转换拼音
	  * @remark  汉字转换拼音 Pinyin('汉字转换拼音', true, 'utf-8') 得到 'hanzizhuanhuanpinyin'
	  * @param  $_String  string 待转换的汉字
	  * @param  $_Lower  bool  拼音是否需要小写，默认返回的为汉字的小写字母，如果需要拼音大写，将该参数设为false 
	  * @param  $_Code   string 需要转换的编码，如果该参数不传则默认要转换的汉字为'utf-8'编码,如需转换成'gb2312'可以将该参数设为除'utf-8'或'UTF-8'的任何字符 
	  * @author jian
	  * @copyright 2013/7/24
	  * @return string
	  */
    static function PinYin($_String, $_Lower = true, $_Code='utf-8')
    {
    	$_Res = self::GetPinYin( $_String, $_Code );
        $_R = preg_replace("/[^a-z0-9]*/", '', implode( '', $_Res ) );
        if($_Lower) {
            return $_R;
        } else {
			return strtoupper($_R);
        }
    }

	/**
	  * @name GetInitial
	  * @remark  获取首字母
	  * @param  $Str  string
	  * @author Soul
	  * @copyright 2015-08-15
	  * @return 大写首字母
	  */
	static function GetInitial($Str, $Charest = 'utf-8')
	{
		$Arr = array('・', ' ', '~', '!', '@', '#', '$', '%', '&', '*', '(', ')', '-', '——', '_', '+', '=', '{', '[', '}', ']', '|', '\\', ':', ';', '"', '\'', '<',',', '>', '?', '/');
		$Str = str_replace($Arr, '', $Str);
		$PinYin = self::GetPinYin( $Str, $Charest );
		$Result = '';
		if(!empty($PinYin))
		{
			foreach($PinYin as $V)
			{
				$T = substr($V, 0, 1);
				if(preg_match('/[a-zA-Z]/', $T, $M))
				{
					$Result .= substr($V, 0, 1);
				}
			}
		}
		return strtoupper($Result);
	}

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @return mixed
     */
    static function GetClientIp($type = 0)
    {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

	/**
     * 跳转
     * @param $Url 
     */
	static function Go($Url)
	{
		if(stripos(trim($Url), 'http://') !== 0 && stripos(trim($Url), 'https://') !== 0)
		{
			$Http = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';  
			$Url= $Http.$_SERVER['HTTP_HOST'].$Url;
		}
		header('Location: '.$Url);
		exit;
	}
	
	/**
     * 当前的URL
     * @return http://.....
     */
	static function nowUrl()
	{
		$Http = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';  
		return $Http.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}


	/**
     * 重置某一维数组的对应值
	 * @arr1 ['a'=>111, 'b'=>xxx]
	 * @arr2 ['a'=>22]
     * @return ['a'=>222, 'b'=>xxx]
     */
	static function resetArrVal($arr1, $arr2)
	{
		$Http = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';  
		return $Http.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}


	/**
     * 获取客户端IP地址
     * @param startYear 2016|201601...
     * @param endYear  2017|201701....
     * @param type  1|2
     * @return 1:1988/02-1989/12  2: 1988-1989
     */
	static function formatViewYear($startYear, $endYear, $type=1)
	{
		$out = '';
		switch($type)
		{
			case 1: 
				$startYear = strlen($startYear) == 6? substr($startYear, 0, 4).'/'.substr($startYear, 4, 2): '';
				$endYear = strlen($endYear) == 6? substr($endYear, 0, 4).'/'.substr($endYear, 4, 2): '';
				if(!empty($startYear) || !empty($endYear))
				{
					$out = $startYear == $endYear? $startYear : $startYear.'-'.$endYear;
				}
			break;
			case 2:
				$startYear = strlen($startYear) >= 4? substr($startYear, 0, 4): '';
				$endYear = strlen($endYear) >= 4? substr($endYear, 0, 4): '';
				if(!empty($startYear) || !empty($endYear))
				{
					$out = $startYear == $endYear? $startYear : $startYear.'-'.$endYear;
				}
			break;
		}
		return $out;
	}

	/**
     * 分词
     * @param word  现代瑞纳瑞奕悦动朗动领动名图索纳塔双铱金火花塞DH7RTII
     * @param dictFile  额外的分词词典
     * @return ['现代', '火花塞', ....]
	 * @copyright soul 2017/9/20
     */
	static function fenCi($word, $dictFile = [])
	{
		$wordArr = [];
        $word = trim($word);
        if($word == '') return $wordArr;

		$dictFileArr = [mvc::$cfg['ROOT_MVC'].'lib/lib/dict/selfdict.txt'];
        if(is_array($dictFile) && !empty($dictFile))
        {
            foreach($dictFile as $v)
            {
                $DictFileArr[] = $v;
            }
        }
        $so = scws_new();
		//公共词库地址
        $so->add_dict(mvc::$cfg['ROOT_MVC'].'lib/lib/dict/publicxdb.xdb');
        foreach($dictFileArr as $dict)
        {
            $flag = $so->add_dict($dict, SCWS_XDICT_TXT);//自定义词库地址 （txt格式）
            if(!$flag)exit('Input Dict Error:'.$dict);
        }
        $so->set_charset('utf8');
        $so->set_ignore(true);
        $so->send_text($word);
        while ($tmp = $so->get_result())
        {
            foreach($tmp as $V)
            {
                //舍弃单字
                if(mb_strlen($V['word'])<2)continue;
                $wordArr[]=$V['word'];
            }
        }
        $wordArr=array_unique($wordArr);
        $so->close();
        return $wordArr;
	}


	/**
     * 生成关键字
     * @param word  现代瑞纳瑞奕悦动朗动领动名图索纳塔双铱金火花塞DH7RTII
     * @param paramArr  ['fenci'=>true]
     * @return ['现代', '火花塞', ....]
	 * @copyright soul 2017/9/20
     */
    static function keyword($word, $paramArr = ['fenci'=>true])
    {
		$wordArr = [];
        $ignoreArr = array('.', '-', '_');
        $trimChar = ' '.str_replace('.', '', implode('', $ignoreArr));
        if(mb_strlen(trim($word, $trimChar), 'UTF8') < 2 ) return $wordArr;

        //特殊字符转换
        $arr = array('&NBSP;', '~', '!', '@', '#', '$', '%', '&', '*', '(', ')', '+', '=', '{', '[', '}', ']', '|', '\\', ':', ';', '"', '\'', '<',',', '>', '?', '/');
        $word = str_replace($arr, ' ', $word);
        $arr = array('`', '\'', '´', '^', '·');
        $word = str_replace($arr, '', $word);

        $allKeywordArr = array();
		if(!empty($paramArr['fenci']))
		{
			$allKeywordArr = self::fenCi($word, $paramArr['cidian']);
		}

		//中文英文分隔
		$Tword = preg_replace('/([^\x7f-\xff]*)([\x7f-\xff]*)([^\x7f-\xff]*)/', '$1,$2,$3', $word);
		$TempArr = preg_split('/[\s,@]/', $Tword);
		foreach($TempArr as $V)
		{
			$V = trim($V, $trimChar);
			if(mb_strlen($V, 'UTF8') > 1 && $word != $V)
			{
				$allKeywordArr[] = $V;
			}
		}

        $allKeywordArr = array_unique($allKeywordArr);
        foreach($allKeywordArr as $K=>$V)
        {
            $V = trim($V, $trimChar);
            if(mb_strlen($V, 'UTF8') < 2 || mb_strlen($V, 'UTF8') > 10)
            {
                unset($allKeywordArr[$K]);
            }
            else
            {
                $allKeywordArr[$K] = $V;
            }
        }
        //sort($allKeywordArr);
        return $allKeywordArr;
    }

	
	/**
     * 生成全球唯一guid
	 * @param type  0数字型 [默认]| 1 字符型
	 * @copyright soul 2017/11/1
	 * @return 32位字符  2017-11-01 18 28 20    069186096191406248  || CCF1D138D0262E2DBD1A01F187C4965F
     */
    static function guid($type = 0)
    {
		$guid = date('YmdHis');
		list($usec, $sec) = explode(" ", microtime());
		$usec = sprintf("%d", $usec*10000000);
		$guid .= $usec.rand(1000, 9999);
		$randLen = 32 - strlen($guid);
		$guid .= rand(pow(10,($randLen-1)), pow(10,$randLen)-1);
		$guid = empty($type)? $guid: strtoupper(md5($guid));
		return $guid;
	}


	/**
     * 数组转换
	 * @param arr array(0=>'a', 1=>'b', 2=>'c', 3=>1)
	 * @copyright soul 2017/11/1
	 * @return array('a'=>array('b'=>array('c'=>array('@end'=>1))))
     */
    static function arrToKeyArr($Arr)
    {
        $SortArr = array();
        foreach($Arr as $V)
        {
            $V = trim($V);
            if($V != '' && !in_array($V, $SortArr))
            {
                $SortArr[] = trim($V);
            }
        }
        $Out = array();
        for($I = count($SortArr)-1; $I >=0 ;$I--)
        {
            if(isset($SortArr[$I-1]))
            {
                if($I == count($SortArr)-1)
                {
                    $Out[$SortArr[$I-1]] = array('@end'=>$SortArr[$I]);
                }
                else
                {
                    $Temp = $Out;
                    $Out = array();
                    $Out[$SortArr[$I-1]] = $Temp;
                }
            }
        }
        return $Out;
    }

	/**
     * 
	 * @param KeyArr 键是数组
	 * @param Arr 需要搜索的数组
	 * @copyright soul 2017/11/1
	 * @return  array('keys'=>匹配的键一维数组, 'value'=>值);
     */
    static function keyArrExists($KeyArr, $Arr)
    {
        $OutArr = array('keys'=>array(), 'value'=>'');
        foreach($KeyArr as $V)
        {

            if(isset($Arr[$V]))
            {
                $OutArr['keys'][] = $V;
                $Arr = $Arr[$V];
                if(isset($Arr['@end']))
                {//匹配到
                    $OutArr['value'] = $Arr['@end'];
                    break;
                }
            }
            else if(isset($Arr['@end']))
            {//匹配到
                $OutArr['value'] = $Arr['@end'];
                break;
            }
            else
            {
                $OutArr['keys'] = array();
                 break;
            }
        }
        return $OutArr;
    }


	/**
     * 
     * @method 将字符串编码转换为utf-8
	 * @param file 文件路径 /tmp/test.csv
	 * @copyright xu 2018/04/25
	 * @return  str[type => utf-8]
     */
	static function charaset($str)
	{
		if(empty($str))
			return '';
		$fileType = mb_detect_encoding($str , array('UTF-8','GBK','LATIN1','BIG5')) ;   
		if( $fileType != 'UTF-8'){   
			$str = mb_convert_encoding($str ,'utf-8' , $fileType);   
		}
		return $str;
	}


	/**
     * 
     * @method 解析csv文件为一个key-value数组
	 * @param file 文件路径 /tmp/test.csv
	 * @copyright xu 2018/03/01
	 * @return  array
     */
	static function readCsvToArr($file)
	{
		$temp = array();
		
		if(!file_exists($file))
			return 'file dose not exist!';

		$res = array_map('str_getcsv', file($file));
		foreach ($res as $k => $v) {
			$res[$k][0] = LibFc::charaset($v[0]);
		}
		// 获取所有键值:
		$keyArr = $res[1];
		// 获取csv文件配件值
		if (($handle = fopen($file, "r")) !== FALSE) {
			$row = 1;
		    while (($data = fgetcsv($handle)) !== FALSE) {
		    	// 空行跳出循环
		    	if(empty(current($data)) && count($data)==1)
		    	{
		    		continue;
		    	}
		    	// 非空行存储数值
		    	if($row > 2)
		    	{	
			        $num = count($data);
			        for ($c=0; $c < $num; $c++) {
			            $temp[$row][$keyArr[$c]] = LibFc::charaset($data[$c]);
			        }
		    	}
		        $row++;
		    }
		    fclose($handle);
		}
		return $temp;
	}


	/**
     * 
     * @method 
	 * @param [string] $str 需要截取的字符串
	 * @param [int]    $len  需要保留的长度
	 * @copyright xu 2018/03/01
	 * @return  array
     */
	static function CutStr($str,$len)
	{
		$temp = mb_substr($str, 0 , $len);
		if(mb_strlen($str,'utf-8') > $len) $temp.='...';
		return $temp;
	}

	/**
     * 
     * @method 
	 * @param [string] $str 需要正则校验的字符串
	 * @param [string] $type 字段名称
	 * @copyright xu 2018/04/13
	 * @return  array
     */
	static function RegularValida($str,$type)
	{
		
		$data = false;
		switch($type)
		{
			// 信用代码
			case 'societyCode': 
				$data = preg_match("/^[0-9a-zA-Z]{14,18}$/", $str);
				break;
			// 公司名称
			case 'companyName':
				$data = preg_match("/^[\x{4e00}-\x{9fa5}]{5,}$/u",$str);
			break;
		}
		return $data;
	}
}