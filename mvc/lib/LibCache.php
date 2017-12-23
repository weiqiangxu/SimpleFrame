<?php
/**
 * @author xu  QQ:435861851
 * @version 2.0.0
 *
*/
class LibCache{

	/**
	 * 缓存路径
     * @param   type  [string....] :缓存数据类型
     * @return  服务器缓存路径
     */
	public static function GetCachePath($type){
		return mvc::$cfg['PATH_CACHE'].'/'. $type .'/';
	}
   
	/**
	 * 缓存路径
     * @param   subpath  [string] :深度路径
     * @param   class  [string] :action名
     * @param   method  [string] :方法名
     * @param   param  [string] :访问参数
     * @return  缓存文件路径+名称
     */
    public static function GetPageFilename($subpath,$class,$method,$param){
    	// 缓存路径全路径
		$path =   self::GetCachePath('page').LibDir::FormatDir($subpath).LibDir::FormatDir($class).LibDir::FormatDir($method);
		// 将参数拼接为文件名
		$filename = md5($subpath.$class.$method.$param).'.html';
		return $path.$filename;
	}

	/**
	 * 写页面缓存
     * @param   subpath  [string] :深度路径
     * @param   class  [string] :action名
     * @param   method  [string] :方法名
     * @param   param  [string] :访问参数
     * @param   data [string] 缓存区文件（渲染好的tpl）
     * @return  true(bool)
     */
    public static function SetPageCache($subpath,$class,$method,$param,$data){
		$filename =self::GetPageFilename($subpath,$class,$method,$param);
		$file = new LibFile($filename);
		// gz压缩存储.不压缩请使用PutData
		$file->PutDataGz($data);
		return true;
	}

	/**
	 * 输出缓存
     * @param   subpath  [string] :深度路径
     * @param   class  [string] :action名
     * @param   method  [string] :方法名
     * @param   param  [string] :访问参数
     * @return  已经输出界面
     */
    public static function GetPageCache($subpath,$class,$method,$param)
    {
		$filename =  self::GetPageFilename($subpath,$class,$method,$param);
		// 去除缓存时间限定只要存在同样action同样参数的缓存就输出缓存
		if(file_exists($filename)){
			$file = new LibFile($filename);
			// 不再linux定时更新，缓存更新触发机制更改，时间校验去除
			// if( $cachetime==9 || (time() - $file->FileTime()) < $cachetime ){

				// tell浏览器压缩形式
				header("Content-Encoding: gzip");
				// tell网站需要启用GZip
				header("Vary: Accept-Encoding");
				echo $file->GetData();
				exit;

			// }
		}
	}

    /**读数组/对象缓存
     * @param filename [string] :
     * @return  
     */
	/**
	 * 读数组/对象缓存
     * @param   filename  [string] :文件名
     * @param   cachetime [int] 缓存时间 
     * @return  echo html
     */
	public static function GetObj($filename, $cachetime){
		$filename =   self::GetCachePath('object').$filename;
		if(file_exists($filename)){
			$file = new LibFile($filename);
			if( $cachetime==9 || (time() - $file->FileTime()) < $cachetime ){
				return unserialize($file->GetData());
			}
		}
		return null;
	}

    /**
 	 * 写数组/对象缓存
     * @param  filename [string] :
     * @param  data [data] :
     * @return  true
     */
	public static function SetObj($filename,$data){
		$filename =   self::GetCachePath('object').$filename;
		$file = new LibFile($filename);
		$file->PutData(serialize($data));
		return true;

	}
}