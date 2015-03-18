<?php

class Init_Model
{
	public function __construct()
	{
	}
	
	public function GetIntro()
	{
		include 'intro.php';
		
		return $intro_content;
	}
}

?>
