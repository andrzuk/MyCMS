<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Style_Model
{
	private $orig_file;
	private $css_file;
	private $contents;
	private $size;
	
	public function __construct($db)
	{
		$this->orig_file = CSS_DIR . 'original.css';
		$this->css_file = CSS_DIR . 'default.css';
	}
	
	public function GetSize()
	{
		$this->size = filesize($this->css_file);

		return $this->size;
	}
	
	public function GetContents()
	{
		$fh = fopen($this->css_file, 'r');
		$this->contents = fread($fh, filesize($this->css_file));
		fclose($fh);
		
		return $this->contents;
	}
	
	public function SaveContents($record_object)
	{
		foreach ($record_object as $k => $v)
		{
			if ($k == 'contents') $contents = $v;
		}
		$this->contents = $contents;

		$result = is_writable($this->css_file);
		if ($result)
		{
			$fh = fopen($this->css_file, 'w');
			if ($fh)
			{
				$res = fwrite($fh, $this->contents);
				fclose($fh);
			}
		}
		return $result && $res;
	}
	
	public function RestoreContents()
	{
		$fh = fopen($this->orig_file, 'r');
		$this->contents = fread($fh, filesize($this->orig_file));
		fclose($fh);
		
		$result = is_writable($this->css_file);
		if ($result)
		{
			$fh = fopen($this->css_file, 'w');
			if ($fh)
			{
				$res = fwrite($fh, $this->contents);
				fclose($fh);
			}
		}
		return $result && $res;
	}
}

?>
