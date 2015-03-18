<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Profile_View
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}
	
	/*
	 * Formularz
	 */
	 
	public function ShowDetails($row, $columns)
	{
		include APP_DIR . 'view/users.php';

		$user_object = new Users_View($this->db);

		$result = $user_object->ShowDetails($row, $columns);
		
		return $result;
	}	
}

?>
