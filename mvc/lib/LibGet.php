<?php
class LibCurl{
	var $httpheader;
	var $connnecttimeout = 60; #获取联接超时(秒)
	var $timeout = 300; #读定超时(秒) 
	var $returnheader = false; #是否返回服务器http头信息(一般用于测试)	
	var $nobody = false; #是否禁止返回html内容
	var $cookespath = "./tmp"; #cookes文件目录
	var $cookie_jar = '';
	var $proxyuse = false;  #是否使用代理服务器
	var $proxyiplist = array();  #代理服务器[IP:PORT]列表，随机使用列表中的IP
	var $proxynodomain = array('localhost','127.0.0.1');  #不使用代理服务器的域
	var $clientip = ''; #设置客户端ＩＰ
	var $forwarded = ''; #设置代理跳转ＩＰ
	var $src_codepage = ''; #源页面编辑
	var $target_codepage = ''; #要转换的编码
	function __construct(){
		if(!is_dir($this->cookespath)) mkdir($this->cookespath);
		$this->cookie_jar = tempnam($this->cookespath,'cookie');
		$this->clientip = mt_rand(5, 252).'.'.mt_rand(5, 252).'.'.mt_rand(5, 252).'.'.mt_rand(5, 252);
		$this->forwarded = mt_rand(5, 252).'.'.mt_rand(5, 252).'.'.mt_rand(5, 252).'.'.mt_rand(5, 252);
	}
	function _header(){
		$this->httpheader = array('Accept:application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5'        
								 ,'USER_AGENT:Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0)'
								 ,'CLIENT-IP:'.$this->clientip
								 ,'X-FORWARDED-FOR:'.$this->forwarded
								 );
	}
	function _fetch($url,$post,$referer,&$info){
		$this->_header();
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,$this->connnecttimeout);
		curl_setopt($curl, CURLOPT_TIMEOUT,$this->timeout);
		curl_setopt($curl, CURLOPT_HTTPHEADER,$this->httpheader);
		curl_setopt($curl, CURLOPT_HEADER,$this->returnheader);
		curl_setopt($curl, CURLOPT_NOBODY,$this->nobody);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1); #设置为1告诉libcurl遵循任何访问
		curl_setopt($curl, CURLOPT_MAXREDIRS,5); #设定重定向的数目限制,设置为-1表示无限的重定向（默认）
		if($referer=='')
			curl_setopt($curl, CURLOPT_AUTOREFERER,1); #libcurl自动设置Referer
		else
			curl_setopt($curl, CURLOPT_REFERER,$referer); #设置来源路径
		curl_setopt($curl, CURLOPT_COOKIEJAR,$this->cookie_jar);
		curl_setopt($curl, CURLOPT_COOKIEFILE,$this->cookie_jar);
		curl_setopt($curl, CURLOPT_URL,$url);
		if($this->proxyuse && !empty($this->proxyiplist)){
			$proxyip = $this->proxyiplist[mt_rand(0,count($this->proxyiplist)-1)];
			curl_setopt($curl, CURLOPT_PROXY,$proxyip);
			#curl_setopt($curl, CURLOPT_PROXYNO,$this->proxynodomain);
		}
		if(!empty($post)){
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		}
		$content = curl_exec($curl);
		if($content===false)
			return curl_error($curl);
		$info = curl_getinfo($curl);
		if($this->src_codepage != $this->target_codepage)
			$content = iconv($this->src_codepage,$this->target_codepage."//IGNORE",$content);
		return $content;
	}
		
	function fetch($url,$post=array(),$referer=''){
		return $this->_fetch($url,$post,$referer,$info);
	}

}
#示例
#$get = new get();

#简单
#echo $get->fetch('http://www.baidu.com');

#示例 POST
#echo $get->fetch('http://get.hzqghost.com/hzqget/test.php',array('a'=>1,'b'=>'g'));

#示例 返回测试信息
#$get->_fetch('http://get.hzqghost.com/hzqget/test.php',array(),'',$info);
#print_r($info);

#示例 指定来源
#echo $get->fetch('http://get.hzqghost.com/hzqget/test.php',array('a'=>1,'b'=>'g'),'http://www.baidu.com');

#示例 返回头信息
#$get->returnheader = true;
#echo $get->fetch('http://www.baidu.com');

#示例 只返回头信息
#$get->returnheader = true;
#$get->nobody = true;
#echo $get->fetch('http://www.baidu.com');


#示例 转码
#$get->src_codepage = 'utf-8';
#$get->target_codepage='gbk';
#header("Content-type: text/html; charset=".$get->target_codepage);
#echo $get->fetch('http://www.baidu.com');

#示例 使用代理
#$get->proxyuse = true;
#$get->proxyiplist = array('120.192.92.99:80'); //$get->proxyiplist = file($this->proxyipfile);
#echo $get->fetch('http://www.baidu.com');

#示例 定义客户端ＩＰ和代理跳转ＩＰ
#$get->clientip = '202.103.96.112'
#$get->forwarded = '68.65.7.201'
#echo $get->fetch('http://www.baidu.com');


/**调用接口***********************************************************************************************
 * Class LibRpc
 */
/* LibRpc 服务端处理
     function api($class, $method, $params, $is_gz=0){
        //echo base64_encode(json_encode(['BRA_ID'=>878,'BRA_MFC_CODE'=>'MENAR']));
        $params = json_decode(base64_decode($params), true);
		$obj = new $class;
        $data = call_user_func_array ( array ( $obj, $method ), $params );
        $data = json_encode($data);
        echo ((int)$is_gz===1) ? gzcompress($data , 9) : $data;
	}
 */
class LibRpc{
	private $url;
	private $is_gz;
	private $timeout;
	function __construct($tag,$timeout=5)
	{
		$this->url = mvc::$cfg['RPC'][strtoupper($tag)]['URL'];
		$this->is_gz = mvc::$cfg['RPC'][strtoupper($tag)]['IS_GZ'];
		$this->timeout = $timeout;
	}

	function API($class,$method,$params,$is_gz=-1){
		if($is_gz == -1) $is_gz = (int)$this->is_gz;
		$params =  base64_encode(json_encode($params));
		$url = sprintf('%s?class=%s&method=%s&params=%s&is_gz=%d',$this->url, $class, $method, $params, (int)$is_gz);
		$curl = new LibCurl();
		$curl->connnecttimeout = $this->timeout;
		$curl->timeout = $this->timeout;
		$data = $curl->fetch($url);
		if((int)$is_gz==1){
			$result = @gzuncompress($data);
		}else{
			$result = $data;
		}
		$result = json_decode($result, true);
		if(!is_array($result)){
			mvc_echo('[NET-CALL-ERROR]: 远程调用出错('.$this->url.') <br/>'.$data);
		}else{
			return $result;
		}
	}
}
