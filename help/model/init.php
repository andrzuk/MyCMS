<?php

class Init_Model
{
	public function __construct()
	{
	}
	
	public function GetIntro($install_exists)
	{
		include 'intro.php';
		
		if ($install_exists) // tryb instalacji
		{
			return $intro_content;
		}
		else // tryb normalnej pracy
		{
			return $connection_content;
		}
	}
}

?>
