<?php
/**
  * @name PageHome
  * @remark  前台分页类
  * @author Soul 2013/10/15
  */
! defined ( 'LibPage' ) && define ( 'LibPage', '' );
class LibPage
{
	private static $Total = 0;
	private static $PerPage = 10;
	//url的分页参数
	private static $PageName = 'page';
	
	/**
	  * @name ShowPage
	  * @remark  显示分页
	  * @param  $Total            int  总记录数
	  * @param  $PerPage           int  每页数量
	  * @author Soul 2013/10/12
	  */
	public static function Show($Total = 0, $PerPage = 10)
	{
		self::$Total = $Total > 0 ? (int) $Total: self::$Total;
		self::$PerPage = $PerPage > 0 ? (int) $PerPage: self::$PerPage;
		$TotalPage = ceil(self::$Total/self::$PerPage);
		$NowPage = !empty($_GET[self::$PageName]) && $_GET[self::$PageName] > 0 ? (int) $_GET[self::$PageName] : 1;
		$NowPage = $NowPage > $TotalPage? $TotalPage: $NowPage;
		//url
		//$UrlParamArr = $_SERVER ['QUERY_STRING'];
		$UrlParamArr = array();
		//全部小写
		if(is_array($_GET))
		{
			foreach($_GET as $K=>$V)
			{
				$UrlParamArr[$K] = $V;
			}
		}
		$UrlParamArr[self::$PageName] = '-_-page_-_';
		$Url = $_SERVER['PHP_SELF'].'?'.http_build_query($UrlParamArr);
		$PageStr = '';
		if($TotalPage > 0)
		{
			$PageStr .= $NowPage == 1 ? '<li class="page-disabled"><a href="javascript:">上一页</a></li>' : '<li><a href="'.self::ReplaceUrl($Url, $NowPage - 1).'">上一页</a></li>';
		}
		if($TotalPage <= 7)
		{
			for($I = 1; $I <=$TotalPage; $I++)
			{
				if($NowPage == $I)
				{
					$PageStr .= '<li class="page-choose"><a href="javascript:">'.$I.'</a></li>';
				}
				else
				{
					$PageStr .= '<li><a href="'.self::ReplaceUrl($Url, $I).'">'.$I.'</a></li>';
				}
			}
		}
		else
		{//上一页 5 6 7 8 9 10 11 12 下一页
			//计算开始页
			if($NowPage < 4)
			{//[1] [2] 3 [4]...
				$BPage = 1;
			}
			else if(($TotalPage-$NowPage) < 3)
			{ 
				$BPage = $NowPage-6+($TotalPage-$NowPage);
			}
			else
			{
				$BPage = $NowPage-3;
			}
			for($I = $BPage; $I < $BPage+7; $I++)
			{
				if($I > $TotalPage)
				{
					break;
				}
				if($NowPage == $I)
				{
					$PageStr .= '<li class="page-choose"><a href="javascript:">'.$I.'</a></li>';
				}
				else
				{
					$PageStr .= '<li><a href="'.self::ReplaceUrl($Url, $I).'">'.$I.'</a></li>';
				}
			}
		}
		if($TotalPage > 0)
		{
			$PageStr .= $NowPage == $TotalPage ? '<li class="page-disabled"><a href="javascript:">下一页</a></li>' : '<li><a href="'.self::ReplaceUrl($Url, $NowPage + 1).'">下一页</a></li>';
		}
		//跳转到
		$GoHtml = '';
		if($TotalPage > 10)
		{
			$GoHtml .= '<div class="page-go">共<b>'.$TotalPage.'</b>页，到第 <input type="text" class="to_page_txt form-control" size="2" base="'.$Url.'" onkeydown="javascript:if(event.keyCode==13){PAGE.jump();return false;}" value="'.$NowPage.'"/> 页 <button type="button" class="btn btn-info btn-sm to_page_btn" onclick="PAGE.jump()">跳转</button ></div>';
		}
		
		$per = '';
		if(LibPage == 'all')
		{
			
			$per = '<div class="page-all">共<b>'.$Total.'</b>条记录，每页 <input type="text" class="to_pageall_txt form-control" size="2" onkeydown="javascript:if(event.keyCode==13){PAGE.perPage(this.value);return false;}" value="'.$PerPage.'"/> 条</div>';
		}
		return '<div class="page-box">'.$per.'<ul class="page">'.$PageStr.'</ul>'.$GoHtml.'</div>';
	}


	/**
	  * @name 
	  * @remark  显示总记录数 和上下页按钮
	  * @param  $Total            int  总记录数
	  * @param  $PerPage           int  每页数量
	  * @author Soul 2013/10/12
	  */
	public static function Samll($Total = 0, $PerPage = 10, $totalName = '条记录')
	{
		self::$Total = $Total > 0 ? (int) $Total: self::$Total;
		self::$PerPage = $PerPage > 0 ? (int) $PerPage: self::$PerPage;
		$TotalPage = ceil(self::$Total/self::$PerPage);
		$NowPage = !empty($_GET[self::$PageName]) && $_GET[self::$PageName] > 0 ? (int) $_GET[self::$PageName] : 1;
		$NowPage = $NowPage > $TotalPage? $TotalPage: $NowPage;
		//url
		//$UrlParamArr = $_SERVER ['QUERY_STRING'];
		$UrlParamArr = array();
		//全部小写
		if(is_array($_GET))
		{
			foreach($_GET as $K=>$V)
			{
				$UrlParamArr[$K] = $V;
			}
		}
		$UrlParamArr[self::$PageName] = '-_-page_-_';
		$Url = $_SERVER['PHP_SELF'].'?'.http_build_query($UrlParamArr);
		$PageStr = $TotalPage > 0 && ($NowPage -1) > 0 && $TotalPage >= ($NowPage -1) ? '<a href="'.self::ReplaceUrl($Url, $NowPage - 1).'" class="small-pagebtn"><</a>' : '<span class="page-disabled small-pagebtn"><</span>';
		$PageStr .= $TotalPage > 0 && ($NowPage + 1) <= $TotalPage ? '<a href="'.self::ReplaceUrl($Url, $NowPage + 1).'" class="small-pagebtn">></a>' : '<span class="page-disabled small-pagebtn">></span>';
		return '<div class="small-page"><span class="small-page-msg">共<b>'.$Total.'</b>'.$totalName.'</span><span class="small-page-now"><b>'.$NowPage.'</b>/<font>'.$TotalPage.'</font></span><span class="page-change">'.$PageStr.'</span></div>';
	}

	private static function ReplaceUrl($Url, $Page)
	{
		return str_replace('-_-page_-_', $Page, $Url);
	}
}