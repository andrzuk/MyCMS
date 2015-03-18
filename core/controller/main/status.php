<?php

/*
 * Klasa odpowiedzialna za odczyt statusu użytkownika, jego id, nazwy itd.
 */

class Status
{
	private $user_id;
	private $user_status;
	private $user_imie;
	private $user_nazwisko;
	
	public function __construct($db)
	{
		$this->init();
	}
	
	public function init()
	{
		$this->user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
		$this->user_status = isset($_SESSION['user_status']) ? $_SESSION['user_status'] : NULL;
		$this->user_imie = isset($_SESSION['user_imie']) ? $_SESSION['user_imie'] : NULL;
		$this->user_nazwisko = isset($_SESSION['user_nazwisko']) ? $_SESSION['user_nazwisko'] : NULL;
	}
	
	public function get_status()
	{
		$status = array(
			'user_id' => $this->user_id,
			'user_status' => $this->user_status,
			'user_imie' => $this->user_imie,
			'user_nazwisko' => $this->user_nazwisko,
		);
		
		return $status;
	}

	public function get_value($key)
	{
		$status = array(
			'user_id' => $this->user_id,
			'user_status' => $this->user_status,
			'user_imie' => $this->user_imie,
			'user_nazwisko' => $this->user_nazwisko,
		);
		
		return $status[$key];
	}
}

?>