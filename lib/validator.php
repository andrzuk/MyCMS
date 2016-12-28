<?php

/*
 * Klasa odpowiedzialna za sprawdzanie i walidację pól formularzy
 */

class Validator
{
	public function check_security($query)
	{
		/*
		$restricted_words = array('use', 'grant', 'revoke', 'variables', 'outfile', 'shutdown', 'reload', 'process', 'replication', 'execute', 'insert', 'update', 'rename', 'delete', 'drop', 'alter', 'truncate', 'create', 'database', 'union', 'select', 'unhex', 'hex', 'concat', 'char');
		$restricted_schema = array('%20Union%20', '%20Select%20', 'UNHEX(', 'HEX(', 'concat(', 'char(', 'table_schema', 'table_name', 'column_name', 'INFORMATION_SCHEMA.tables', 'INFORMATION_SCHEMA.columns');
		*/
		$restricted_words = array();
		$restricted_schema = array();
		
		foreach ($restricted_words as $i => $item)
		{
			if (stristr(substr($query, 3, strlen($query)), ' '.$item.' ') !== FALSE)
			{
				return FALSE;
			}
		}
		
		foreach ($restricted_schema as $i => $item)
		{
			if (stristr($query, $item) !== FALSE)
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	public function check_email($email)
	{
		if (empty($email)) return TRUE;

		if (preg_match("/^[0-9a-zA-Z\.\-\_]+\@[0-9a-zA-Z\.\-\_]+\.[0-9a-zA-Z\.\-\_]+$/is", trim($email)))
			return TRUE;
		else 
			return FALSE;		
	}

	public function check_pesel($pesel)
	{
		if (empty($pesel)) return TRUE;

		$pesel = str_replace(" ", "", $pesel);
		$pesel = str_replace("-", "", $pesel);
		
		if (strlen($pesel) != 11 || !is_numeric($pesel)) return FALSE;
			
		$steps = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3);
		
		$sum_nb = 0;
		
		for ($i = 0; $i < 10; $i++) 
			$sum_nb += $steps[$i] * $pesel[$i];
			
		$sum_m = 10 - $sum_nb % 10;
		
		if ($sum_m == 10) $sum_c = 0;
		else $sum_c = $sum_m;
		
		if ($sum_c == $pesel[10]) return TRUE;
			
		return FALSE;
	}
}

?>