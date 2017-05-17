<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Stats_Model
{
	private $db;
	
	private $rows_list;
	private $table_name;
	private $setting;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'visitors'; // nazwa głównej tabeli modelu w bazie
		$this->setting = new Settings($db);
	}
	
	public function GetStats()
	{
		$this->rows_list = array();

		// odczytuje z konfiguracji domenę serwisu:
		$base_domain = $this->setting->get_config_key('base_domain');
		$base_domain = str_replace(array('http:', 'https:', '/'), array(NULL, NULL, NULL), $base_domain);

		$query = "
			SELECT DISTINCT http_referer AS caption, COUNT(*) AS licznik FROM " . $this->table_name . "
			WHERE http_referer LIKE 'http%'
			AND http_referer NOT LIKE '%". $base_domain ."%'
			GROUP BY http_referer
			ORDER BY Licznik DESC
		";

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$row['licznik'] = number_format($row['licznik'], 0, '', '.');
				$this->rows_list[] = $row;
			} 
			mysqli_free_result($result);
		}

		return $this->rows_list;
	}	
}

?>
