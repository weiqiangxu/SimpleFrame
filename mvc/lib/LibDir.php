<?php
/**
 * @author HzqGhost <admin@whiledo.com> QQ:313143468,xu QQ:435861851 
 * @version 1.0.1
 *
*/
class LibDir{

    /**
     * 格式化路径
     * @param $dir 待格式化的目录
     * @author xu
     * @copyright 2018-03-04
     */
    public static function FormatDir($dir){
        // 有时候深度路径为空这里就要去除调用
        if($dir!="")
        {
            $dir = str_replace('\\','/',$dir);
            $dir = str_replace('//','/',$dir);
            $dir = rtrim($dir,'/').'/';
        }
        else
        {
            $dir ="";
        }
        return $dir;
    }


    public static function SearchFile($dir, &$files){
        $dir = self::FormatDir($dir);
        $arr = scandir($dir);
        foreach($arr as $v){
          if($v=='.' || $v=='..') continue;
          if(is_dir($dir.$v)){
            self::SearchFile($dir.$v,$files);
          }else{
            $files[] = $dir.$v;
          }
        }
    }

    /**
     * 创建文件夹
     * @param $dir 目标文件夹
     * @author xu
     * @copyright 2018-03-04
     */
    public static function CreateDir($dir){
        if(is_dir($dir)) return true;
        $dir = self::FormatDir($dir);
        $arr = explode('/',$dir);
        $path = '';
        foreach($arr as $v){
            $path .= $v.'/';
            if (trim($v)=='' || in_array($v,array('..','.')) || is_dir($path)) continue;
            if (!@mkdir($path, 0777)){
                return false;
            }
        }
        @clearstatcache();
        return true;
    }

    /**
     * 清空文件夹
     * @param $dir 目标文件夹
     * @param $keep 是否保留被清空的文件夹
     * @author xu
     * @copyright 2018-03-04
     */
    public static function ClearDir($dir,$keep = false){
        if(!is_dir($dir)) return true;
        if($handle = opendir($dir)){
            while(($file = readdir($handle))!==false){
                if($file!='.' && $file!='..'){
                    if(is_dir($dir.'/'.$file)){
                        self::DeleteDir($dir.'/'.$file);
                    }else{
                        unlink($dir.'/'.$file);
                    }
                }
            }
            closedir($handle);
            @clearstatcache();
            if(!$keep) @rmdir($dir);
        }
        return true;
    }

    /**
     * 清除文件修改时间早于时间戳$time的文件
     * @param $dir 目标文件夹
     * @param $time 时间戳
     * @author xu
     * @copyright 2018-03-04
     */
	public static function ClearFile($dir, $time){
        if(!is_dir($dir)) return true;
        if($handle = opendir($dir)){
            while(($file = readdir($handle))!==false){
                if($file!='.' && $file!='..'){
                    if(is_dir($dir.'/'.$file)){
                        self::ClearFile($dir.'/'.$file, $time);
                    }else if (filemtime($dir.'/'.$file) < $time){
						unlink($dir.'/'.$file);
                    }
                }
            }
            closedir($handle);
            @clearstatcache();
        }
        return true;
    }

    /**
     * 复制文件夹
     * @param $source 目标文件夹
     * @param $dest 复制后生成的文件夹
     * @author xu
     * @copyright 2017-12-26
     */
    public static function copydir($source, $dest)
    {
        if (!file_exists($dest))
        {
            if (!@mkdir($dest, 0777)){
                return false;
            }
        }
        $handle = opendir($source);
        while (($item = readdir($handle)) !== false) {
            if ($item == '.' || $item == '..') continue;
            $_source = $source . '/' . $item;
            $_dest = $dest . '/' . $item;
            if (is_file($_source)) copy($_source, $_dest);
            if (is_dir($_source)) self::copydir($_source, $_dest);
        }
        closedir($handle);
    }

}