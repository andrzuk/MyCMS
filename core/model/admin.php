<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Admin_Model
{
	private $db;
	private $table_params;
	private $table_counter;
	private $rows_list;
	private $table_name;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'visitors';

		$this->table_counter = array();
		
		$this->table_params = array(
			array(
				'module' => 'Konfiguracja',
				'table' => 'configuration',
				'condition' => '1',
			),
			array(
				'module' => 'Użytkownicy',
				'table' => 'users',
				'condition' => '1',
			),
			array(
				'module' => 'Odwiedziny',
				'table' => 'visitors',
				'condition' => '1',
			),
			array(
				'module' => 'Galeria',
				'table' => 'images',
				'condition' => '1',
			),
			array(
				'module' => 'Dokumenty',
				'table' => 'documents',
				'condition' => '1',
			),
			array(
				'module' => 'Kategorie',
				'table' => 'categories',
				'condition' => '1',
			),
			array(
				'module' => 'Strony',
				'table' => 'pages',
				'condition' => 'system_page = 0',
			),
			array(
				'module' => 'Opisy',
				'table' => 'pages',
				'condition' => 'system_page = 1',
			),
			array(
				'module' => 'Wiadomości',
				'table' => 'user_messages',
				'condition' => 'requested = 1',
			),
			array(
				'module' => 'Wyszukiwania',
				'table' => 'searches',
				'condition' => '1',
			),
			array(
				'module' => 'Rejestracje',
				'table' => 'registers',
				'condition' => 'result = 1',
			),
			array(
				'module' => 'Logowania',
				'table' => 'logins',
				'condition' => 'user_id > 0',
			),
			array(
				'module' => 'Hasła',
				'table' => 'reminds',
				'condition' => '1',
			),
			array(
				'module' => 'Funkcje',
				'table' => 'admin_functions',
				'condition' => '1',
			),
			array(
				'module' => 'Role',
				'table' => 'user_roles',
				'condition' => 'access = 1',
			),
			array(
				'module' => 'Odrzucenia',
				'table' => 'rejectors',
				'condition' => '1',
			),
		);
	}
	
	public function GetTablesCounts()
	{
		foreach ($this->table_params as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				if ($key == 'module') $module = $value;
				if ($key == 'table') $table = $value;
				if ($key == 'condition') $condition = $value;
			}
			
			$query = "SELECT COUNT(*) AS licznik FROM " . $table . " WHERE " . $condition;
			$result = mysqli_query($this->db, $query);
			if ($result)
			{
				$row = mysqli_fetch_assoc($result); 
				$this->table_counter[] = $row['licznik'];
				mysqli_free_result($result);
			}
		}
		
		return $this->table_counter;
	}

	public function GetSummaryData($range_days)
	{
		$this->rows_list = array('index' => array(), 'contact' => array());

		for ($day = $range_days; $day > 0; $day--)
		{
			$str_days = '-' . strval($day - 1) . ' days';
			$date_range = date("Y-m-d", strtotime($str_days));
			$query = "SELECT COUNT(*) AS counter, '" . $date_range . "' AS visited FROM " . $this->table_name . 
			         " WHERE request_uri IN ('/', '/index.php') AND visited BETWEEN '" . $date_range . " 00:00:00' AND '" . $date_range . " 23:59:59'";
			$result = mysqli_query($this->db, $query);
			if ($result)
			{
				$row = mysqli_fetch_assoc($result);
				$this->rows_list['index'][] = $row;
				mysqli_free_result($result);
			}
		}
		for ($day = $range_days; $day > 0; $day--)
		{
			$str_days = '-' . strval($day - 1) . ' days';
			$date_range = date("Y-m-d", strtotime($str_days));
			$query = "SELECT COUNT(*) AS counter, '" . $date_range . "' AS visited FROM " . $this->table_name . 
			         " WHERE request_uri LIKE '%route=contact' AND visited BETWEEN '" . $date_range . " 00:00:00' AND '" . $date_range . " 23:59:59'";
			$result = mysqli_query($this->db, $query);
			if ($result)
			{
				$row = mysqli_fetch_assoc($result);
				$this->rows_list['contact'][] = $row;
				mysqli_free_result($result);
			}
		}
		
		return $this->rows_list;
	}
}

?>