<?php

/*
 * Klasa odpowiedzialna za obsługę ustawień konfiguracyjnych w bazie
 */

class Settings
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}
	
	public function get_config_key($key)
	{
		$config_value = NULL;

		$query = "SELECT * FROM configuration WHERE key_name='" . $key . "'";
		$result = mysqli_query($this->db, $query);
		if ($result) 
		{
			$row = mysqli_fetch_assoc($result);
			$config_value = isset($row['key_value']) ? $row['key_value'] : NULL;
			mysqli_free_result($result);
		}
		return $config_value;
	}
}

?>