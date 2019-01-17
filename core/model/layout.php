<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Layout_Model
{
	private $orig_file;
	private $layout_file;
	private $contents;
	private $size;
	
	public function __construct($db)
	{
		$this->orig_file = 'layout/original.php';
		$this->layout_file = 'layout/default.php';
	}
	
	public function GetSize()
	{
		$this->size = filesize($this->layout_file);

		return $this->size;
	}
	
	public function GetContents()
	{
		$fh = fopen($this->layout_file, 'r');
		$this->contents = fread($fh, filesize($this->layout_file));
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

		$fh = fopen($this->layout_file, 'w');
		fwrite($fh, $this->contents);
		fclose($fh);
	}
	
	public function RestoreContents()
	{
		$fh = fopen($this->orig_file, 'r');
		$this->contents = fread($fh, filesize($this->orig_file));
		fclose($fh);
		
		$fh = fopen($this->layout_file, 'w');
		fwrite($fh, $this->contents);
		fclose($fh);
	}
}

?>
