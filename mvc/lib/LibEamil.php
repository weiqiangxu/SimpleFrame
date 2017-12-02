<?php
ini_set("display_errors", "On");

error_reporting(E_ALL);


/*
	错误信息返回值说明:
						 -1  				枚举类型参数不是字符串
						 -2  				枚举类型参数不是默认值
						 -3 				参数不能为空
						 -4 				参数不是整型
						 -5					缺少必须参数
	这是一个封装函数：
						ambient:
							true:			    会将收集到的错误信息，直接输出，并终止脚本
							flase:			会将收集到的错误信息，存放在类成员中，需要输出错误代码号，可直接调用getErrinfo()
							需要全面错误信息直接调用getErrinfoAll();
						debug_level			
							1:				会检测输入值，是否符合默认配置，
							2：				会检测数据库需要的值，是否已全部输入
							3:				会检测输入值是否符合数据库字段设置，并检测输入值是否为全部输入完毕	
 */
class LibEamil{

	/**
	 * 初始化时字段信息
	 * @var array
	 */
	private $eamil_info=array();						//邮件信息

	private $mail_type='';								//枚举类型

	private $mail_level='';								//发送等级权重

	private $mail_sender='';							//发送者邮件

	private $mail_sendername='';						//发送人

	private $mail_to='';								//接收人

	private $mail_subject='';							//发送标题

	private $mail_body='';								//发送文本内容(富文本编辑器) 

	private $mail_failtotal='';							//发送失败统计
	
	private $url='http://www.yiparts.com/API/mail/getPost.php';	//post传递url地址

	/**
	 * 记录最新一条的错误提示信息
	 * @var [String]
	 */
	private $err_info=0;								//当前错误信息

	private $err_msg=['-1'=>'枚举类型参数不是字符串','-2'=>'枚举类型参数不是默认值','-3'=>'参数不能为空','-4'=>'参数不是整型','-5'=>'缺少必须参数'];

	/**
	 * 错误的错误信息都将被记录在这个成员属性下
	 * @var [array] 
	 */
	private $err_log=array();								



	/**
	 * 	抛出信息的调试等级，根据不同级别，在输出过程中，会抛出不同信息
	 * 	生产环境，根据生产环境等级不同，进行不同级别的报错机制，决定是否终止或者选择性抛出错误
	 * 	true:使用throw 抛出错误，终于脚本运行
	 * 	false:值会记录错误信息，并不会终于脚本的运行
	 */
	private $ambient=false;		


	/**
	 * 输入变量检测等级
	 * 1:只检测输入的数据是否为空
	 * 2:检测输入数据是否有缺省值，+输入值是否为空
	 * 3:检测输入数据是否符合db_set_2 设置+检测缺省值
	 * @var [int]
	 */
	private $debug_level=3;
	

	/**
	 * 需要忽略空值检测的字段
	 * @var [array]
	 */
	private $db_set_1=['mail_sendername'];

	/**
	 * 缺省值检测
	 * 需要检测缺省的数据库字段，都在这里设置
	 */
	private $default_value=['mail_sender','mail_to','mail_subject','mail_body'];

	/**
	 * 数据库字段设置信息,检测值是否符合数据库设计要求
	 * type下是需要进行的检测
	 * int=>进行整型检测
	 * _empty=>进行为空的检测
	 * _enum =>进行枚举类型 输入值是否符合设置值检测
	 * @var [array]
	 */
	private $db_set_2=array('mail_type'		=>['type'=>'_enum','set'=>['主站紧要','主站普通','主站延迟','客户网紧要','客户网普通','客户网延迟','CRM紧要','CRM普通','CRM延迟','A紧要','A普通','A延迟']],
						  'mail_level'		=>['type'=>'_int,_empty'],
						  'mail_sender'		=>['type'=>'_empty']  ,
						  'mail_sendername'	=>['type'=>'_continue'],
						  'mail_to'			=>['type'=>'_empty'],
						  'mail_subject'	=>['type'=>'_empty'],
						  'mail_failtotal'	=>['type'=>'_int'],
						  'mail_body'		=>['type'=>'_empty'],
						);

	private $arr=array();					//一个中转变量，没有实际意义		
	/**
	 * 使用post 进行访问
	 * @param  $str 访问方式,$url 访问路径
	 * @return 返回访问的页面输出值
	 */
	// public function __construct(){
	// 	$arr['mail_level']='5';
	// }

	public function send_msg(){
		$url=$this->url;
		$this->validation_data();
		// var_dump($this->arr);
		// var_dump($this->eamil_info);exit;
		$data=$this->post_curl($this->eamil_info,$url);
		return $data;
	}


	/**
	 * 数据验证，根据debug_levrl 提出不同等级验证
	 * @return [type] [description]
	 */
	private function validation_data(){
		$debug_level=$this->debug_level;
		$validation_data=$this->arr;
		$ambient=$this->ambient;
		switch($debug_level){
			case '1':
				$db_set_1=$this->db_set_1;
				foreach($validation_data as $key=>$value){
					if(array_key_exists($key,$db_set_1)){
						$this->eamil_info[$key]=$value;
					}else{
						$this->_empty($key,$value);
					}
				}		
			break;
			case '2':
				$default_value=$this->default_value;
				$inter=array_intersect($default_value,array_flip($validation_data));
				$db_set_1=$this->db_set_1;
				foreach($validation_data as $key=>$value){
					if(array_key_exists($key,$db_set_1)){
						$this->eamil_info[$key]=$value;
					}else{
						$this->_empty($key,$value);
					}
				}	
				if(count($default_value)!=count($inter)){
					$this->err_info='-5';
				}else{
					$this->eamil_info=$validation_data;
				}
			break;
			case '3':
				$default_value=$this->default_value;
				$db_set_2=$this->db_set_2;
				$inter=array_intersect($default_value,array_flip($validation_data));
				if(count($default_value)!=count($inter)){
					$this->err_info='-5';
				}
				foreach($validation_data as $key=>$value){
					$arr=explode(',',$db_set_2[$key]['type']);
					if(array_key_exists($key,$db_set_2)){
						foreach($arr as $v){
							//调用检测方法，反回true和false,因为设置了ambient,所以这里既是返回false,没有终止程序，只是不在将值装入
							if($this->$v($key,$value)){			
								$this->eamil_info[$key]=$value;
							}
						}
					}
				}	
			break;
		}
		if($ambient){
			try{
				if($this->err_info<0){
					throw new Exception($this->err_msg[$this->err_info]);
				}
			}catch(Exception $e){
				echo $e->getMessage(), "\n";
				exit;
			}
		}
	}


	/**
	 * 数组库插入字段自动注册
	 * @param [type] $property_name [description]
	 * @param [type] $value         [description]
	 */
	public function __set($property_name,$value){
		$this->arr[$property_name]=$value;
	}			

	private function _enum($property_name,$value){
		$enum=$this->db_set_2[$property_name]['set'];
		if(gettype($value)!='string'){
			$this->err_info='-1';
			$this->err_log[]=$property_name.'?是数组，需要string';
			return false;
		}

		if(in_array($value,$enum)){
			return true;
		}else{
			$this->err_info='-2';
			$this->err_log[]=$property_name.'?输入参数与枚举类型设置有差异';
			return false;
		}

	}

	/**
	 * 这里是示例，表示如何在加装一个检测的方式
	 * @return [type] [description]
	 */
	private function _continue(){//常规检查就可以了
		return true;
	}

	private function _empty($property_name,$value){
		if(empty($value)){
			$this->err_info='-3';
			$this->err_log=[$property_name.'='.$value.'?不能为空'];
			return false;
		}else{
			return true;
		}
	}	



	private function _int($property_name,$value){
		if(gettype($value+0)=='integer' && ($value+0)==$value){
			return true;
		}else{
			$this->err_info='-4';
			$this->err_log[]=$property_name.'='.$value.'?不是整型';
			return false;
		}
	}		 		
	
	public function getErrinfo(){
		return $this->err_info;
	}

	public function getErrinfoAll(){
		return $this->err_log;
	}

	private function post_curl($data,$url){
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}						
}


$test=new LibEamil();
$test->mail_to = '254131389@qq.com;313143468@qq.com';			//接收人邮箱
//admin@yiparts.com
$test->mail_sender='whiledo@163.com';				//发送人邮箱
$test->mail_type='A紧要';					//发送级别
$test->mail_level='1';
$test->mail_sendername='逆天';		
// $test->mail_sendername='逆天';
$test->mail_subject='标题头部测试信息';					//邮件标题
$test->mail_body='文本内容测试信息';						//邮件内容
$data=$test->send_msg();						//发送邮件

var_dump($data);								//查看返回的信息
