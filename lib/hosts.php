<?php

/*
 * Klasa odpowiedzialna za obsługę nazw hostów na podst. adresów IP
 */

class Hosts
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function find_host_name($host_address)
	{
		$host_name = NULL;

		$query = "SELECT server_name FROM hosts WHERE server_ip = '". $host_address ."'";
		$result = mysqli_query($this->db, $query);
		if ($result) 
		{
			if (mysqli_num_rows($result) == 1)
			{
				$row = mysqli_fetch_assoc($result);
				$host_name = $row['server_name'];
			}
			mysqli_free_result($result);
		}
		if (!empty($host_address) && empty($host_name)) // nie znalazł w tablicy - trzeba dopisać
		{
			$host_name = gethostbyaddr($host_address);
			$query = "INSERT INTO hosts VALUES (NULL, '". $host_address ."', '". $host_name ."')";
			mysqli_query($this->db, $query);
		}
		
		$host_name = str_replace(array("."), array(". "), $host_name);
		
		return $host_name;
	}
}

?>