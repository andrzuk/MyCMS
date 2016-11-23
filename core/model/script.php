<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Script_Model
{
	private $orig_file;
	private $js_file;
	private $contents;
	private $size;
	
	public function __construct($db)
	{
		$this->orig_file = 'js/original.js';
		$this->js_file = 'js/default.js';
	}
	
	public function GetSize()
	{
		$this->size = filesize($this->js_file);

		return $this->size;
	}
	
	public function GetContents()
	{
		$fh = fopen($this->js_file, 'r');
		$this->contents = fread($fh, filesize($this->js_file));
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

		$fh = fopen($this->js_file, 'w');
		fwrite($fh, $this->contents);
		fclose($fh);
	}
	
	public function RestoreContents()
	{
		$fh = fopen($this->orig_file, 'r');
		$this->contents = fread($fh, filesize($this->orig_file));
		fclose($fh);
		
		$fh = fopen($this->js_file, 'w');
		fwrite($fh, $this->contents);
		fclose($fh);
	}
}

?>
