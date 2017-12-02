<?php
/**
	* @method 历史
	* @author 许
	* @copyright 2017/7/5
	*/
class B2cHistory extends B2cBase
{
	/**
	* @method 查询浏览历史
	* @param  $partId 分类ID int
	* @param  $PerPage 每页条数 int
	* @param  $NowPage 当前页码 int
	* @param  $userId 用户ID int
	* @author xu
	* @copyright 2017/07/05
	* @return  array('status'=>true,'data'=>'','code'=>)/array('status'=>true,'data'=>array(),'code'=>)
	*/
	public function getProHistory($partId,$PerPage,$NowPage,$userId) 
	{	
		//参数判断
		if(!LibFc::Int($PerPage)||!LibFc::Int($NowPage)||!LibFc::Int($userId))
		{
			return LibFc::ReturnData(false,'参数错误！',0);
		}

		$data = array();
		return LibFc::ReturnData(true,$data);
	}

	


}