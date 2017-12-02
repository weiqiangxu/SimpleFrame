<?php
/**
 * @author HzqGhost <admin@whiledo.com> QQ:313143468
 * @version 1.0.0
 *
*/
class LibFile{
    protected $FileName = '';
    protected $PathParts = null;
    function __construct($FileName = null)
    {
        if($FileName != null)
		{
			$this->filename = $FileName;
		}
    }
    
    public function Info(){
        if(is_null($this->PathParts)){
            $this->PathParts = pathinfo($this->filename);
        }
    }
    
    public function Crc32($FileName=''){
        if($FileName=='') $FileName = $this->filename;
        return strtoupper(dechex(crc32(file_get_contents($FileName))));
    }    
    public function Dir(){
        $this->Info();
        return $this->PathParts['dirname'];
    }
    
    public function BaseName(){
        $this->Info();
        return $this->PathParts['basename'];
    }

    public function ExtName(){
        $this->Info();
        return $this->PathParts['extension'];
    }

    public function FileTime(){
        return @filemtime($this->filename);
    }

    public function PutData($data){
        if(LibDir::CreateDir($this->Dir())){
            file_put_contents($this->filename,$data);
        }
    }
    
    public function PutDataGz($data){
        if(LibDir::CreateDir($this->Dir())){
     		$gz = gzopen ( $this->filename, 'w9' );
			gzwrite ( $gz, $data );
			gzclose ( $gz );
        }
    }
    
    public function GetData(){
        return @file_get_contents($this->filename);
    }

	/**
	  * @name WriteData(将数据写入文件)
	  * @remark  将数据写入文件
	  * @exp  WriteData('mylog.txt', 1, "I am a boy!")
	  * @param  $FileName string 文件名
	  * @param  $Mode   int  0=>r+, 1=>w, 2=>w+, 3=>a, 4=>a+, 5=>x, 6=>x+ （键）
	  * @param  $Data   string  数据
	  * @author Soul
	  * @copyright 2013-3-25
	  * @return 
	  		成功：true	
			失败：false

	  */	
	public function WriteData($FileName, $Mode, $Data)
	{
		$ModeArray =  array('write'=>'w','append'=>'a','w'=>'w','a'=>'a');
		if(array_key_exists($Mode, $ModeArray))
		{
			$this->filename = $FileName;
			if(LibDir::CreateDir($this->Dir()))
			{
				$Handle = fopen($this->filename, $ModeArray[strtolower($Mode)]);
				$Data = sprintf("%s\r\n", $Data);
				try
				{
					fwrite($Handle, $Data);
					fclose($Handle);
					return true;
				}
				catch (Exception $e)
				{
					fclose($Handle);
				}
			}
		}
		return false;
	}

	/*写*/
}