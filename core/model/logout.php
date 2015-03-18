<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Logout_Model
{
	private $db;
	
	private $mySqlDateTime;
	
	public function __construct($db)
	{
		$this->db = $db;

		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function Logout($user_id)
	{
		// rejestruje date i czas logowania uzytkownika:
		
		$query = "UPDATE users SET data_wylogowania='".$this->mySqlDateTime."'".
				 " WHERE id=".intval($user_id);
				 
		mysqli_query($this->db, $query);
		
		$result = mysqli_affected_rows($this->db);
			
		return $result;
	}
}

?>
