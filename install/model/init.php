<?php

class Init_Model
{
	private $db;

	private $row_item;
	private $sql_script;
	private $num_rows;
	
	private $mySqlDateTime;

	public function __construct($db)
	{
		$this->db = $db;

		$this->num_rows = 0;

		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SaveSettings($record_item)
	{
		include 'script.php';
		
		$this->sql_script = $sql;

		foreach($this->sql_script as $k => $v)
		{
			foreach($v as $key => $val)
			{
				if (is_array($val))
				{
					foreach($val as $i => $query)
					{
						mysqli_query($this->db, $query);
						$this->num_rows += mysqli_affected_rows($this->db);
					}
				}
			}
		}
		
		return $this->num_rows;
	}
	
	public function GetIntro()
	{
		include dirname(__FILE__) . '/../../' . HELP_DIR . 'model/intro.php';
		
		return $intro_content;
	}
}

?>
