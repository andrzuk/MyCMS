<?php

/*
 * Klasa odpowiedzialna za połączenie z bazą danych
 */

class Database
{
	private $host;
	private $database;
	private $user;
	private $password;
	
	public function __construct()
	{
		error_reporting(E_ALL);
	}
	
	public function init($host, $db, $usr, $pwd)
	{
		$this->host = $host;
		$this->database = $db;
		$this->user = $usr;
		$this->password = $pwd;
	}

	public function open()
	{
		$connection = mysqli_connect($this->host, $this->user, $this->password, $this->database);

		mysqli_query ($connection, 'SET NAMES utf8');

		return $connection;
	}

	public function close($connection)
	{
		mysqli_close($connection);
	}
}

?>
