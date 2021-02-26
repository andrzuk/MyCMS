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

		// odczytuje z konfiguracji wykluczone domeny raportu:
		$excluded_domains = $this->setting->get_config_key('excluded_domains');
		$domains = explode(', ', $excluded_domains);
		$condition = '';
		foreach ($domains as $key => $value)
		{
			$condition .= " AND http_referer NOT LIKE '%". $value ."%'";
		}
		$links_length_min = $this->setting->get_config_key('links_length_min');
		$links_length_max = $this->setting->get_config_key('links_length_max');

		$query = "
			SELECT DISTINCT http_referer AS caption, COUNT(*) AS licznik FROM " . $this->table_name . "
			WHERE http_referer LIKE 'http%'
			". $condition ." AND LENGTH(http_referer) >= ". $links_length_min ." AND LENGTH(http_referer) <= ". $links_length_max ."
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
