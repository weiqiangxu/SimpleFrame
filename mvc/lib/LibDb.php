<?php
/**
 * @method  	数据库操作
 * @remark 		PDO数据库类
 * @copyright  	Soul Hzq 2016/7/22
 * @version  	2.0.0 
 */
!defined('DEBUG') &&  define('DEBUG', true);
class LibDb
{
    /**
	 * @ pdo对象
	 * @param object
	 */
    protected $pdo = NULL;
	
    /**
	 * 当前结果集
	 * @param object
	 */
    protected $result = false;
	
    /**
	 * @ 配置数组,详见函数connect
	 * @param array
	 * $configs = array(
	 *	"db_type" => 'mysql'[string],   //连接数据库类型(mysql or sqlite or oracle)
	 *	"db_host" => 'localhost'[string], //地址
	 *	"db_port" => 3306[integer],     //端口
	 *	"db_name" => 'ypdb'[string],    //数据库名称
	 *	"db_user" => 'root'[string]],   //用户
	 *	"db_pass" => '123456'[string],  //密码
	 * 	"db_charset" => 'utf8'[string], //字符集
	 * 	"db_persistent" => false[bool], //是否为长连接
	 * );
	 */
    public $configs = array();
	
    /**
	 * @ 当执行出错时是否中断
	 * @param Boolean
	 */
    public $on_error_stop = false;
	
    /**
	 * @ 当前查询SQL语句
	 * @param string
	 */
    public $sql = '';

	/**
	 * @ 此连接的对应的唯一键
	 * @param string
	 */
    private $pdo_key;

    /**构造函数
     * @param $configs [array|string] :配置
     */    
    function __construct($configs=null){
        if($configs==null){
            $configs = isset($GLOBALS['db']['default']) ? $GLOBALS['db']['default']: mvc::$cfg['db']['default'];
        }else if(is_string($configs)){
            $configs = isset($GLOBALS['db'][$configs])? $GLOBALS['db'][$configs]: mvc::$cfg['db'][$configs];
        }
        $this->configs = $configs;
        $this->pdo_key = md5(serialize($configs));
		$this->connect();
    }
    
    /**析构函数*/ 
    function __destruct(){
        $this->FreeResult();
        //$this->Close();
    }
   
    /**执行查询
     * @param $sql [string] :SQL查询语句
     * @return  成功赋值并返回true; 失败返回 false 如果有事务则回滚
     */
    public function Query($sql)
    {
		$this->Connect();
        $this->sql = $sql;
		try {
            $this->result = $this->pdo->query($sql);
        } catch (PDOException $e) {
			$this->ToLog($e->getMessage());
            if($this->on_error_stop){
                exit;
            }
            return false;
        }
		return $this->result; //query 增加返回值。
    }


    /**返回执行的SQL语句*/
    public function Sql()
    {
        return $this->sql;
    }

    /**返回当前PDOKEY*/
    public function PdoKey()
    {
        return $this->pdo_key;
    }
    
    /**查询指定SQl 第一行，第一列 值
     * @param $sql [string] :SQL查询语句
     * @return  成功返回值,失败返回 null
     */
    public function One($sql)
    {
        if($res=$this->Query($sql)){
			return $res->fetchColumn();
        }else{
            return null;
        }
    }
    
    /**查询指定SQl 第一行 值
     * @param $sql [string] :SQL查询语句
     * @return  [array]
     */
    public function Row($sql,$res=null)
    {
        if($res=$this->Query($sql)){
			return $res->fetch(PDO::FETCH_ASSOC);
        }else{
			return array();
	    }
    }
    
    /**查询指定SQl 所有记录
     * @param $sql [string] :SQL查询语句
     * @return  [array][array]
     */
    public function Table($sql)
    {
        if($res=$this->Query($sql)){
            return $res->fetchAll(PDO::FETCH_ASSOC);
        }else{
			return array();
	    }
    }
    
    /**取一行数据
     * @param $type [string] :取数据类型[PDO::FETCH_ASSOC, PDO::FETCH_NUM, PDO::FETCH_BOTH]
     * @return  [array]
     */
    public function Fetch($res=null,$type=PDO::FETCH_ASSOC)
    {
		if ($res==null)
	        return $this->result->fetch($type);
		else
			return $res->fetch($type);
    }
    
    
     /**
     * 执行更新数据操作
     * @param $table [string] 数据库表名称
     * @param $data [array] 待更新的数据
     * @param $where [string] 更新条件
     * @return  成功 true; 失败 false
     */
    public function Update($table, $data, $where)
    {
        $set = '';
        if(is_array($data)){
            foreach ($data as $k => $v){
                $this->FormatValue($v);
                $set .= empty($set) ? ("{$k} = {$v}") : (", {$k} = {$v}");
            }
        }else{
            $set = $data;
        }
        return $this->Query("UPDATE {$table} SET {$set} WHERE {$where}");
    }
    
    /**
     * 执行插入数据操作
     * @param $table [string] 数据库表名称
     * @param $data [array] 待插入数据,一维或二维数组
     * @return 成功 true; 失败 false
     */
    public function Insert($table, $data)
    {
        if(!isset($data[0])){
            $data = array($data);
        }
        $fields = array_keys($data[0]);
        $fields = '' . implode(', ', $fields) . '';
        $datastr = $this->FormatInsertData($data);
        return $this->Query("INSERT INTO {$table} ({$fields}) VALUES {$datastr}");
    } 
    
     /**格式化插入数据
     * @param $data [array] 待格式化的插入数据
     * @return insert 中 values 后的 SQL格式 */
    protected function FormatInsertData($data)
    {
        $output = '';
        foreach ($data as $value)
        {
            $tmp = '';
            foreach ($value as $v){
                $this->FormatValue($v);
                $tmp .= !empty($tmp) ? ", {$v}" : $v;
            }
            $tmp = "(" . $tmp . ")";
            $output .= ($output!='') ? ", {$tmp}" : $tmp;
        }
        return $output;
    }
    
    /**格式化值
     * @param &$value [string] 待格式化的字符串,格式成可被数据库接受的格式
     */
    protected function FormatValue(&$value)
    {
		$value = trim($value);
        if($value===false){
            $value = 0;
        }elseif($value===true){
            $value = 1;
        }elseif($value===null || $value === ''){
            $value = 'NULL';
        }elseif(preg_match('/^(\w+)\((.*?)\)$/',$value,$out)){ //mysql函数, HEX(123)
            if($out[1]=='call'){ //call(money+link)
                $value = $out[2];
            }
        }else{
            //$value = "'" . addslashes($value) ."'";
            $value = $this->pdo->quote($value);
        }
    }
    
    /**返回最后一次插入的ID*/
    public function InsertID()
    {
        return $this->pdo->lastInsertId();
    }
    
    /**返回当前查询影响的记录数*/
    public function RowCount()
    {
        return $this->result->rowCount();
    }
    
    /**选择数据库
     * @param $dbname [string] 数据库名称*/
    public function SelectDB($dbname)
    {
        $this->connect();
        return $this->Query('use '.$dbname);
    }
    
    /**释放数据集*/
    protected function FreeResult()
    {
        if($this->result){
            $this->result = null;
        }
    }
    
    /**关闭数据库*/
    public function Close()
    {
        $this->pdo = null;
    }

	/**开始事物处理*/
	public function Begin()
	{
		if(!isset($GLOBALS['libdb_transaction']['register']))
		{
			$GLOBALS['libdb_transaction']['register']=0;
		}
		$GLOBALS['libdb_transaction']['register']++;
        //此连接的事务已经存在，直接返回
		if(isset($GLOBALS['libdb_transaction']['key'][$this->pdo_key]) && $GLOBALS['libdb_transaction']['key'][$this->pdo_key]['is_open'])
		{
            return;
        }
        $this->Connect();
        $this->pdo->beginTransaction();
		$GLOBALS['libdb_transaction']['key'][$this->pdo_key]['is_open'] = true;
	}

	/**提交事物处理*/
	public function End()
	{
		$GLOBALS['libdb_transaction']['register']--;
		if($GLOBALS['libdb_transaction']['register']==0)
		{
			foreach($GLOBALS['libdb_transaction']['key'] as $k=>$v)
			{
				if($GLOBALS['libdb_transaction']['key'][$k]['is_open'])
				{
					$pdo = $GLOBALS['libdb_pdo'][$k];
					$pdo->commit();
					$GLOBALS['libdb_transaction']['key'][$k]['is_open'] = false;
				}
			}
		}
	}


    /**只针对单库 回滚事物处理*/
    public function Rollback($flag= false)
    {
		if($flag) $this->pdo->rollBack();
    }  
	
	/**创建连接，如果连接已经存在直接返回***
     * @param $IsReConnect [bool] 是否为重新连接
    */
	protected function Connect($IsReConnect = false)
    {
		if (!is_null($this->pdo)) return;
        if(isset($GLOBALS['libdb_pdo'][$this->pdo_key]))
		{
            $this->pdo = $GLOBALS['libdb_pdo'][$this->pdo_key];
            return;
        }
        $this->configs['db_type'] = strtolower($this->configs['db_type']);
        switch($this->configs['db_type'])
        {
            case 'sqlite':
                $this->dsn = "sqlite:".$this->configs['db_name'];
                try{
                    $this->pdo = new PDO($this->dsn);
                } catch (PDOException $e) {
                    $this->ToLog($e->getMessage());
                    exit;
               }
                break;
            case 'mysql':
                $this->dsn = "mysql:host=".$this->configs['db_host'].";port=".$this->configs['db_port'].";dbname=".$this->configs['db_name'];
                try{
                    $driver_options = array();
                    if($this->configs['db_persistent']) //是否定义为 长连接
                        $driver_options[PDO::ATTR_PERSISTENT] = true;
                    $this->pdo = new PDO($this->dsn, $this->configs['db_user'], $this->configs['db_pass'],$driver_options);
                    if($this->configs['db_charset']<>''){
                        $this->pdo->exec("SET character_set_connection=" . $this->configs['db_charset'] . ", character_set_results=" . $this->configs['db_charset'] . ", character_set_client=binary");
                    }
                } catch (PDOException $e) {
                    $this->ToLog($e->getMessage());
                    exit;
                }
                break;
			case 'oci':
                $this->dsn = "oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=".$this->configs['db_host'].")(PORT=".$this->configs['db_port']."))(CONNECT_DATA=(SID=yiparts)))";
                try{
					if($this->configs['db_charset']<>''){
                        $this->dsn .= ';charset='.$this->configs['db_charset'];
                    }
					$this->pdo = new PDO($this->dsn, $this->configs['db_user'], $this->configs['db_pass']);
                    /*if($this->configs['db_charset']<>''){
                        $this->pdo->exec("SET character_set_connection=" . $this->configs['db_charset'] . ", character_set_results=" . $this->configs['db_charset'] . ", character_set_client=binary");
                    }*/
                } catch (PDOException $e) {
                    $this->ToLog($e->getMessage());
                    exit;
                }
                break;
        }
        $GLOBALS['libdb_pdo'][$this->pdo_key] = $this->pdo;
        if (DEBUG && !is_null($this->pdo))
		{
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    /**日志处理
     * @param $message [string] 产生的日志消息*/
    protected function ToLog($message)
    {
		if ( DEBUG )
		{
            printf("%s\r\n[SQL]%s\r\n", $message, $this->sql);
        }
    }
}