<?php
/**
 * http get
 * @author HzqGhost QQ:313143468
 * @version 1.0.3
 *
*/
class LibHttp{
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
	var $use_cookes = ''; #是否记录cookes
	var $rand_ip = ''; #是否随机IP
	function __construct($use_cookes=true,$rand_ip=true){
        if($use_cookes){
            if(!is_dir($this->cookespath)) mkdir($this->cookespath);
            $this->cookie_jar = tempnam($this->cookespath,'cookie');
        }
        if($rand_ip){
            $this->clientip = mt_rand(5, 252).'.'.mt_rand(5, 252).'.'.mt_rand(5, 252).'.'.mt_rand(5, 252);
            $this->forwarded = mt_rand(5, 252).'.'.mt_rand(5, 252).'.'.mt_rand(5, 252).'.'.mt_rand(5, 252);
        }
        $this->use_cookes = $use_cookes;
        $this->rand_ip = $rand_ip;
	}
    function __destruct(){
        if($this->use_cookes){
            @unlink($this->cookie_jar);
        }
    }
	public function _header(){
		$this->httpheader = array('Accept:application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5'        
								 ,'USER_AGENT:Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0)');
        if($this->rand_ip){
            $this->httpheader[] =  'CLIENT-IP:'.$this->clientip;  
            $this->httpheader[] =  'X-FORWARDED-FOR:'.$this->forwarded;  
        }
	}
	private function _fetch($url,$post,$referer,&$info ,$ca = false){
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
        if($this->use_cookes){
            curl_setopt($curl, CURLOPT_COOKIEJAR,$this->cookie_jar);
            curl_setopt($curl, CURLOPT_COOKIEFILE,$this->cookie_jar);
        }
		curl_setopt($curl, CURLOPT_URL,$url);
		if($this->proxyuse && !empty($this->proxyiplist)){
			$proxyip = $this->proxyiplist[mt_rand(0,count($this->proxyiplist)-1)];
			curl_setopt($curl, CURLOPT_PROXY,$proxyip);
			#curl_setopt($curl, CURLOPT_PROXYNO,$this->proxynodomain);
		}
		if(!empty($post)){
			if(is_array($post)){
				$post = http_build_query($post);
			}
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		}
        $ssl = substr($url, 0, 8) == "https://" ? true : false;
        if($ssl){
            if($ca){
                $cacert = getcwd() . '/cacert.pem'; //CA根证书      //证书下载 http://curl.haxx.se/ca/cacert.pem
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书  
                curl_setopt($curl, CURLOPT_CAINFO, $cacert);        // CA根证书（用来验证的网站证书是否是CA颁布）  
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);      // 检查证书中是否设置域名，并且是否与提供的主机名匹配  
            }else{
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);// 信任任何证书
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);// 检查证书中是否设置域名
            }
        }
		$content = curl_exec($curl);
		if($content===false)
			return curl_error($curl);
		$info = curl_getinfo($curl);
        curl_close($curl);
		if($this->src_codepage != $this->target_codepage)
			$content = iconv($this->src_codepage,$this->target_codepage."//IGNORE",$content);
		return $content;
	}
		
	public function fetch($url,$post=array(),$referer='',$ca = false){
		return $this->_fetch($url,$post,'',$info, $ca);
	}

	/**
	  * @method 上传文件到文件中心
	  * @param  $url 如http://192.168.1.115/www/file_center/center/write.php
	  * @param  $dataArr [
				'token'=>'....',
				'cate'=>'logo', //类别
				'path'=>/='shop/1214/', //自定义路径
				//如 'file'=> $_FILES['file']
				'file'=>[
					'name'=>'Hydrangeas.jpg',
					'tmp_name'=>'C:\Windows\Temp\phpC18B.tmp',
				],
				'filename'=>'my.jpg', //自定义名称
				'error_lang'=>'cn|en'
			]
	  * @author soul
	  * @copyright 2017/6/15
	  * @return 
		 成功：["status"=>true, "data"=>["size"=>595284, "path"=>"45/163/", "ext"=>"jpg", "name"=>"f8345bdef23fd7f4", "crc32"=>"462F6303", "etime"=>1497518727,....]]
		 失败：["status"=>false, "data"=>'错误原因', 'code'=>110]
	  */
    public function upload($url, $data_arr)
	{
		$temp = explode('.', $data_arr['file']['name']);
        $extension = '.'.strtolower(end($temp));
		$temp = explode('/', $data_arr['file']['name']);
		$data_arr['oriname'] = str_ireplace($extension, '', end($temp));
        if(empty($data_arr['filename']))
		{
            $data_arr['filename'] = strtolower(substr(md5(uniqid('',true)),8,16)).$extension;
        }

		$data_arr['data'] = @file_get_contents($data_arr['file']['tmp_name']);
		unset($data_arr['file']);

        $referer = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		$res = $this->fetch($url, $data_arr, $referer);
        $res_arr =  json_decode($res, true);
		if(is_array($res_arr) && isset($res_arr['status']))
		{
			return $res_arr;
		}
		else
		{
			return array('status'=>false, 'data'=>$res, 'code'=>'');
		}
	}
	
	/**
	  * @method 从文件中心删除文件
	  * @param  $url 如http://192.168.1.115/www/file_center/center/delete.php
	  * @param  $dataArr [
				'token'=>'....',
				'cate'=>'logo',
				'files'=>['shop/100/1.jpg', 'shop/100/xxx.jpg', ....] 
				如果删除的图片有缩略图，地址必须含有某个缩略图文件夹名
				'error_lang'=>'cn|en'
			]
	  * @author soul
	  * @copyright 2017/6/15
	  * @return 
		 成功：["status"=>true, "data"=>'']
		 失败：["status"=>false, "data"=>'错误原因', 'code'=>110]
	  */
    public function delete_file($url, $data_arr)
	{
        $referer = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		$res = $this->fetch($url, $data_arr, $referer);
		$res_arr =  json_decode($res, true);
		if(is_array($res_arr) && isset($res_arr['status']))
		{
			return $res_arr;
		}
		else
		{
			return array('status'=>false, 'data'=>$res, 'code'=>'');
		}
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

