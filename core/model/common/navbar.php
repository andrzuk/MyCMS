<?php

/*
 * Klasa odpowiedzialna za pobieranie z bazy listy rekordów tabeli kategorie (menu navbar)
 */

class Navbar
{
	private $db;
	
	private $rows_list;
	
	public function __construct($db)
	{
		$this->db = $db;
	}

	public function GetAll()
	{
		$this->rows_list = array();

		$query = 	"SELECT id, parent_id, caption, link, type, level, permission, page_id, target FROM categories".
					" WHERE parent_id = 0 AND type = 1 AND visible = 1 ORDER BY item_order";

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$this->rows_list[] = $row;
			}
			mysqli_free_result($result);
		}
				
		return $this->rows_list;
	}
}

?>