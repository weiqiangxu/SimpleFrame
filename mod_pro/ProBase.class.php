<?php
/**
  * @method 广告库基本操作
  * @author xu
  * @copyright 2016/3/31
  */
class ProBase
{
	public $PRODb = null;
	public function __construct($PRODb = null)
	{
		$this->PRODb = $PRODb==null ?  new LibDb (mvc::$cfg['DB']['PRO']): $PRODb;
	}
	
	/**
	  * @method 查询表信息
	  * @param  $Table 表明 如：yp_product 或者 yp_product as a join yp_number as b on a.pid=b.pid
	  * @param  $FieldArr 需要查询的字段名称数组
	  * @param  $Where 查询条件
	  * @param  $OneRow true|false
	  * @author xu
	  * @copyright 2016/3/30
	  * @return array(3) { ["status"]=> bool(true) ["data"]=> $OneRow=false 二维数组 ; $OneRow=true 一维数组["code"]=> int(0) } 
	  */
	public function get($Table, $FieldArr, $Where = '', $OneRow = false)
	{
		if(!empty($FieldArr) && is_array($FieldArr))
		{
			$Sql = sprintf("SELECT %s FROM %s WHERE 1=1 %s", implode(',', $FieldArr), LibFc::Escape($Table), $Where);
			//echo $Sql, '<br>';
			$ResFlag = $this->PRODb->Query($Sql);
			if($ResFlag)
			{
				$Arr = array();
				while($Row = $this->PRODb->Fetch())
				{
					if($OneRow)
					{
						$Arr = $Row;
						break;
					}
					else
					{
						$Arr[] = $Row;
					}
				}
				return LibFc::ReturnData(true, $Arr);
			}
			return LibFc::ReturnData(false, $Table.'查询SQL语句执行失败。');
		}
		return LibFc::ReturnData(false, $Table.'查询提供的参数不正确。');
	}

	/**
	  * @method 添加记录
	  * @param  $Table 表明 如：yp_product 或者 yp_product as a join yp_number as b on a.pid=b.pid
	  * @param  $DataArr 基本信息字段=>值 数组
	  * @author xu
	  * @copyright 2016/3/30
	  * @return array(3) { ["status"]=> bool(true) ["data"]=> ID ["code"]=> int(0) } 
	  */
	public function add($Table, $DataArr)
	{
		if(empty($Table) || !is_array($DataArr) || empty($DataArr))
		{
			return LibFc::ReturnData(false, $Table.'添加记录提供的参数不正确。');
		}
		
		$FiledArr = array();
		$ValArr = array();
		foreach($DataArr as $K=>$V)
		{
			$FiledArr[] = LibFc::Escape($K);
			$ValArr[] = sprintf("'%s'", LibFc::Escape(trim($V)));
		}
		$addSql = sprintf("INSERT INTO %s (%s) VALUES(%s)", LibFc::Escape($Table), implode(',', $FiledArr), implode(',', $ValArr));
		$this->PRODb->Begin();
		$ResFlag = $this->PRODb->Query($addSql);
		$Rows = $this->PRODb->RowCount();
		if($ResFlag && $Rows > 0)
		{
			$Id = $this->PRODb->InsertID();
			$this->PRODb->End();
			return LibFc::ReturnData(true, $Id);
		}
		return LibFc::ReturnData(false, $Table.'添加记录SQL语句执行失败。');
	}

	/**
	  * @method 根据条件修改记录
	  * @param  $Table 表明 如：yp_product 或者 yp_product as a join yp_number as b on a.pid=b.pid
	  * @param  $DataArr 基本信息字段=>值 数组
	  * @param  $Where 修改条件
	  * @author xu
	  * @copyright 2016/3/30
	  * @return array(3) { ["status"]=> bool(true) ["data"]=> 影响行数  ["code"]=> int(0) } 
	  */
	public function set($Table, $DataArr, $Where)
	{
		if(empty($Table) || !is_array($DataArr) || empty($DataArr))
		{
			return LibFc::ReturnData(false, $Table.'修改记录提供的参数不正确。');
		}
		$ValArr = array();
		foreach($DataArr as $K=>$V)
		{
			$ValArr[] = sprintf("%s='%s'", LibFc::Escape($K), LibFc::Escape(trim($V)));
		}
		$setSql = sprintf("UPDATE %s SET %s WHERE 1 %s ", LibFc::Escape($Table), implode(',', $ValArr), $Where);
		$this->PRODb->Begin();
		$ResFlag = $this->PRODb->Query($setSql);
		if($ResFlag)
		{
			$Rows = $this->PRODb->RowCount();
			$this->PRODb->End();
			return LibFc::ReturnData(true, $Rows);
		}
		return LibFc::ReturnData(false, $Table.'修改记录SQL语句执行失败。');
	}

	/**
	  * @method 根据条件删除记录
	  * @param  $Table 表明 如：yp_product 或者 yp_product as a join yp_number as b on a.pid=b.pid
	  * @param  $Where 修改条件
	  * @author xu
	  * @copyright 2016/3/30
	  * @return array(3) { ["status"]=> bool(true) ["data"]=> 影响行数  ["code"]=> int(0) } 
	  */
	public function del($Table, $Where)
	{
		if(empty($Table) || empty($Where))
		{
			return LibFc::ReturnData(false, $Table.'删除记录提供的参数不正确。');
		}
		$delSql = sprintf("DELETE FROM %s WHERE 1=1 %s",  LibFc::Escape($Table), $Where );
		$this->PRODb->Begin();
		$ResFlag = $this->PRODb->Query($delSql);
		if($ResFlag)
		{
			$Rows = $this->PRODb->RowCount();
			$this->PRODb->End();
			return LibFc::ReturnData(true, $Rows);
		}
		return LibFc::ReturnData(false,  $Table.'删除记录SQL语句执行失败。');
	}


	/**
	  * @method 获取指定条件下的某字段唯一值
	  * @param  $table 表明 如：yp_product 或者 yp_product as a join yp_number as b on a.pid=b.pid
	  * @param  $field 字段名称
	  * @param  $where 查询条件
	  * @author xu
	  * @copyright 2016/3/30
	  * @return 
			成功 ["status"=>true, "data"=> [111,222,....]] 
			失败 ["status"=>false, "data"=> 错误信息]  
	  */
	public function getFieldUniVal($table, $field, $where = '')
	{
		if(!empty($table) && !empty($field))
		{
			$field1 = stripos($where, ' GROUP BY ') !== false || stripos($table, ' GROUP BY ') !== false? $field: ' DISTINCT '.$field;
			$sql = sprintf("SELECT %s FROM %s WHERE 1=1 %s", $field1, $table, $where);
			$resFlag = $this->PRODb->Query($sql);
			if($resFlag)
			{
				$arr = array();
				while($row = $this->PRODb->Fetch())
				{
					$arr[$row[$field]] = $row[$field];
				}
				return LibFc::ReturnData(true, array_values($arr));
			}
			return LibFc::ReturnData(false, $table.'查询SQL语句执行失败。');
		}
		return LibFc::ReturnData(false, $table.'查询提供的参数不正确。');
	}
}