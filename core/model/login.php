<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Login_Model
{
	private $db;
	
	private $row_item;
	
	private $mySqlDateTime;
	
	public function __construct($db)
	{
		$this->db = $db;

		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function Login($record_item)
	{
		// weryfikuje uzytkownika:
		
		$query = 	"SELECT * FROM users".
					" WHERE (user_login='". $record_item['user_login'] ."'".
					" OR email='". $record_item['user_login'] ."'".
					" OR pesel='". $record_item['user_login'] ."')".
					" AND user_password='". sha1($record_item['user_password']) ."'".
					" AND active='1'";

		$result = mysqli_query($this->db, $query);
		
		if ($result)
		{
			$num_rows = mysqli_num_rows($result);

			if ($num_rows == 1) // pomyslne zalogowanie (weryfikacja OK)
			{
				$row = mysqli_fetch_assoc($result); 
				$this->row_item = $row;
				mysqli_free_result($result);

				// rejestruje date i czas logowania uzytkownika:
				
				$query = "UPDATE users SET data_logowania='".$this->mySqlDateTime."'".
						 " WHERE id=".intval($row['id']);
						 
				mysqli_query($this->db, $query);
				
				return $this->row_item;
			}
			else // nieudana weryfikacja
			{
				return NULL;
			}
		}
		else // nieudana weryfikacja
		{
			return NULL;
		}
	}
	
	public function Store($record_object, $login_object)
	{
		foreach ($record_object as $k => $v)
		{
			if ($k == 'user_login') $user_login = $v;
			if ($k == 'user_password') $user_password = $v;
		}
		foreach ($login_object as $k => $v)
		{
			if ($k == 'server') $record_item = $v;
			if ($k == 'session') $session_item = $v;
		}
		
		$query = "INSERT INTO logins VALUES (NULL, '" . 
					$record_item['HTTP_USER_AGENT'] . "', '" . 
					$record_item['REMOTE_ADDR'] . "', '" . 
					$session_item['user_id'] . "', '" . 
					mysqli_real_escape_string($this->db, $user_login) . "', '" . 
					mysqli_real_escape_string($this->db, $user_password) . "', '" . 
					$this->mySqlDateTime . "')";
		mysqli_query($this->db, $query);
		
		return mysqli_affected_rows($this->db);
	}
}

?>
