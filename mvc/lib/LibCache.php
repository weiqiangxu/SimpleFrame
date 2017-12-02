<?php
/**
 * @author HzqGhost  QQ:313143468
 * @version 1.0.0
 *
*/
class LibCache{

	public static function GetCachePath($type){
		return mvc::$cfg['PATH_CACHE'].mvc::$cfg['SITE_NAME'].'/'. $type .'/';
	}
   
    /**页面缓存文件名
     * @param  [string....] :参数来源于 mvc主类提供的URL分析结构
     * @return  带目录的文件名
     */
    public static function GetPageFilename($subpath,$class,$method,$param){
		$path =   self::GetCachePath('page').LibDir::FormatDir($subpath).LibDir::FormatDir($class).LibDir::FormatDir($method);
		$filename = md5($subpath.$class.$method.$param).'.html';
		$subpath = substr($filename,0,2);
		return $path.$subpath.'/'.$filename;
	}

    /**写页面缓存
     * @param  [string....] :参数来源于 mvc主类提供的URL分析结构
     * @return  
     */
    public static function SetPageCache($subpath,$class,$method,$param,$data){
		$filename =self::GetPageFilename($subpath,$class,$method,$param);
		$file = new LibFile($filename);
		$file->PutDataGz($data);
		return true;
	}

    /**判断缓存时间，输出页面缓存
     * @param  [string....] :参数来源于 mvc主类提供的URL分析结构
     * @return  
     */
    public static function GetPageCache($subpath,$class,$method,$param, $cachetime){
		$filename =  self::GetPageFilename($subpath,$class,$method,$param);
		if(file_exists($filename)){
			$file = new LibFile($filename);
			if( $cachetime==9 || (time() - $file->FileTime()) < $cachetime ){
				header("Content-Encoding: gzip");
				header("Vary: Accept-Encoding");
				echo $file->GetData();
				exit;
			}
		}
	}

    /**读数组/对象缓存
     * @param filename [string] :
     * @return  
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

    /**写数组/对象缓存
     * @param  filename [string] :
     * @param  filename [data] :
     * @return  
     */
	public static function SetObj($filename,$data){
		$filename =   self::GetCachePath('object').$filename;
		$file = new LibFile($filename);
		$file->PutData(serialize($data));
		return true;

	}
}