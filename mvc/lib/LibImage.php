<?php
/**
 * php图像处理 class - WhileDo.com
 * @author HzqGhost admin@whiledo.com QQ:313143468
 * @version 1.2.0
 *
 */
class LibImage
{
    /*错误信息*/
    protected $error_message = null;
    /*水印缓存*/
    protected $_mark_file = null;
    protected $_mark_img = null;

    /**初始化*/
    protected function Init()
    {
        $this->error_message = null;
    }
    /**记录错误
     * @param $code [int] :错误代码
     * @param $target [string] :目标名称
     * @return [bool]  false
     */
    protected function ErrorTo($code,$target)
    {
        $errorArr = array(
            '-1' => '%s 不存在或者不是一个JPG图片',
            '-2' => '%s 文件夹不存在',
            '-3' => '%s 不存在或者不是一个PNG图片',
            '-4' => '%s 不存在或者不是一个有效的图片(jpg, gif, png, bmp)',
        );
        $this->error_message = sprintf($errorArr[$code],$target);
        return false;
    }
    /**返回错误
     * @return [string]  返回错误信息
     */
    public function Error()
    {
        return $this->error_message;
    }
    /**创建JPG缩略图
     * @param $src [string] :源图片文件名
     * @param $dest [string] :目标图片文件名,为空时则为$src
     * @param $width [int] : 缩略宽度
     * @param $height [int] :缩略高度
     * @return [bool]  成功: true, 失败 false
     */
    public function Cut($src, $dest, $width = 150, $height = 150)
    {
        $this->Init();
        if(empty($dest)) $dest = $src;
        $src_img = @imagecreatefromjpeg($src);
        if(!$src_img){
            return $this->ErrorTo(-1,$src);
        }
        $init_width = @imagesx($src_img);
        $init_height = @imagesy($src_img);
        $ratio = $width / $height;				   //改变后的图象的比例
        $init_ratio = $init_width / $init_height;  //实际图象的比例
        if($init_ratio >= $ratio)
        {
            /*宽度优先*/
            $des_height = ceil($width / $init_ratio);
            $dest_img = @imagecreatetruecolor($width,$des_height);
            @imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $width, $des_height, $init_width, $init_height);
        }else{
            /*高度优先*/
            $des_width = ceil($height * $init_ratio);
            $dest_img = @imagecreatetruecolor($des_width,$height);
            @imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $des_width, $height, $init_width, $init_height);
        }
        @imagedestroy($src_img);
        if(!@imagejpeg($dest_img,$dest)){
            return $this->ErrorTo(-2,$dest);
        }
        return true;
    }
    /**将图片修改成指定大小，图片不够大的用空白真补
     * @param $src [string] :源图片文件名
     * @param $dest [string] :目标图片文件名,为空时则为$src
     * @param $width [int] : 宽度
     * @param $height [int] : 高度
     * @return [bool]  成功: true, 失败 false
     */
    public function Resize($src, $dest ,$width, $height)
    {
        $this->Init();
        if(empty($dest)) $dest = $src;
        if( ($info = @getimagesize($src)) === false){
            return $this->ErrorTo(-1,$src);
        }
        $init_width = $info[0];
        $init_height = $info[1];
		$height = empty($height)? ceil($width*$init_height/$init_width): $height;
        if($init_width > $width || $init_height > $height)
        {
            if($this->Cut($src, $dest, $width, $height)){
                return $this->Resize($dest, null, $width, $height);
            }else{
                return false;
            }
        }
        else if($init_width < $width || $init_height < $height)
        {
            $dest_img = @imagecreatetruecolor($width,$height);
            $white = imagecolorallocate($dest_img,255,255,255);
            @imagefilledrectangle($dest_img, 0, 0, $width, $height, $white);
            $src_img = @imagecreatefromjpeg($src);
            $x = ($width - $init_width) / 2;
            $y = ($height - $init_height) / 2;
            @imagecopy($dest_img,$src_img, $x, $y, 0, 0, $init_width, $init_height);
            @imagedestroy($src_img);
            if(!@imagejpeg($dest_img,$dest)){
                return $this->ErrorTo(-2,$dest);
            }
        }
		else if($src != $dest)
		{
			//update by Soul 2013-5-25
			@copy($src, $dest);
		}
        return true;
    }
    /**给图片加PNG水印
     * @param $src [string] :源图片文件名
     * @param $dest [string] :目标图片文件名,为空时则为$src
     * @param $watermark [string] :水印图片文件名(png格式)
     * @param $pos [string] : 加载位置
     * @return [bool]  成功: true, 失败 false
     */
    public function Watermark($src, $dest, $watermark, $pos="center")
    {
        $this->Init();
        if(empty($dest)) $dest = $src;
        $src_img =  @imagecreatefromjpeg($src);
        if(!$src_img){
            return $this->ErrorTo(-1,$src);
        }
        if($this->_mark_file != $watermark){
            $mark_img =  @imagecreatefrompng($watermark);
            if(!$mark_img){
                return $this->ErrorTo(-3,$watermark);
            }
            $this->_mark_file = $watermark;
            $this->_mark_img = $mark_img;
        }else{
            $mark_img = $this->_mark_img;
        }
        $src_width   = @imagesx($src_img);
        $src_height  = @imagesy($src_img);
        $mark_width  = @imagesx($mark_img);
        $mark_height = @imagesy($mark_img);
        if($src_width < $mark_width || $src_height < $mark_height){
            /*当图片小于水印图时，修改水印自适应*/
            $mark_ratio = $mark_width / $mark_height;  //水印图象的比例
            if($src_width < $mark_width){
                $tmp_width = $src_width - 20;
                $tmp_height = ceil($tmp_width / $mark_ratio);
            }elseif($src_height < $mark_height){
                $tmp_height = $src_height - 20;
                $tmp_width = ceil($tmp_height * $mark_ratio);
            }
            $tmp_img = @ImageCreate($tmp_width,$tmp_height);
            $black = imagecolorallocate($tmp_img,0,0,0);
            $bgcolortrans = ImageColorTransparent($tmp_img,$black);
            @imagecopyresampled($tmp_img, $mark_img, 0, 0, 0, 0, $tmp_width, $tmp_height, $mark_width, $mark_height);
            $mark_img = $tmp_img;
            $mark_width = $tmp_width;
            $mark_height = $tmp_height;
        }
        switch($pos)
        {
            //中央
            case 'center':
                $x = ($src_width - $mark_width) / 2;
                $y = ($src_height - $mark_height) / 2;
                break;
            //右下
            case 'right-bottom':
                $x = $src_width - $mark_width;
                $y = $src_height - $mark_height;
                break;
            //右上
            case 'right-top':
                $x = $src_width - $mark_width;
                $y = 0;
                break;
            //左上
            case 'left-top':
                $x = 0;
                $y = 0;
                break;
            //左下
            case 'left-bottom':
                $x = 0;
                $y = $src_height - $mark_height;
                break;
            //随机
            case 'rand':
                $x = rand(0, $src_width - $mark_width);
                $y = rand(0, $src_height - $mark_height);
                break;
            default:
                $x = ($src_width - $mark_width) / 2;
                $y = ($src_height - $mark_height) / 2;
                break;
        }
        @imagecopy($src_img, $mark_img, $x, $y, 0, 0, $mark_width, $mark_height);
        @imagedestroy($mark_img);
        if(!@imagejpeg($src_img,$dest)){
            return $this->ErrorTo(-2,$dest);
        }
        return true;
    }
    /**图片转JPG
     * @param $src [string] :源图片文件名
     * @param $dest [string] :目标图片文件名,为空时则为 $src.jpg
     * @param $isDel [bool] :转换后是否删除源图片
     * @return [bool]  成功: true, 失败 false
     */
    public function ToJPG($src, $dest, $isDel = false)
    {
        $this->Init();
        if( ($info = @getimagesize($src)) == false){
            return $this->ErrorTo(-4,$src);
        }
        if(empty($dest)){
            $dest = basename($src);
            $p = strrpos($dest,'.');
            $dest = substr($dest,0,$p).'.jpg';
        }
        switch($info['mime']){
            case 'image/gif':
                $src_img = imagecreatefromgif($src);
                break;
            case 'image/jpeg':
                $src_img = imagecreatefromjpeg($src);
                break;
            case 'image/png':
                $src_img = imagecreatefrompng($src);
                break;
            case 'image/bmp':
                if(!function_exists('imagecreatefrombmp')) {
                    require_once 'php_bmp.php';
                }
                $src_img = imagecreatefrombmp($src);
                break;
            case 'image/vnd.wap.wbmp':
                $src_img = imagecreatefromwbmp($src);
                break;
            case 'image/xbm':
                $src_img = imagecreatefromxbm($src);
                break;
            case 'image/xpm':
                $src_img = imagecreatefromxpm($src);
                break;
        }
        $width   = @imagesx($src_img);
        $height  = @imagesy($src_img);
        $dest_img = @imagecreatetruecolor($width,$height);
        $white = @imagecolorallocate($dest_img,255,255,255);
        @imagefilledrectangle($dest_img, 0, 0, $width, $height, $white);
        @imagecopy($dest_img,$src_img, 0, 0, 0, 0, $width, $height);
        @imagedestroy($src_img);
        if(!@imagejpeg($dest_img,$dest)){
            return $this->ErrorTo(-2,$dest);
        }
        if($isDel) @unlink($src);
        return true;
    }
	
	/**给图片修改大小、加PNG水印
     * @param $src [string] :源图片文件名
     * @param $dest [string] :目标图片文件名,为空时则为$src
	 * @param $width [int] : 宽度
     * @param $height [int] : 高度
     * @param $watermark [string] :水印图片文件名(png格式)
     * @param $pos [string] : 加载位置
     * @return [bool]  成功: true, 失败 false
     */
	public function ResizeWatermark($src, $dest, $width, $height,  $watermark, $pos="center")
	{
		if($this->Resize($src, $dest ,$width, $height))
		{
			if($this->Watermark($dest, $dest, $watermark, $pos))
			{
				return true;
			}
		}
		return false;
	}
}