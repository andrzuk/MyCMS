<?php

/*
 * Klasa odpowiedzialna za pobieranie z bazy listy rekordów tabeli kategorie (menu główne)
 * kolejność rekordów jest ustalana na podstawie zależności parent - child
 */

class Menu
{
	private $db;
	
	private $rows_list;
	private $row_item;
	
	public function __construct($db)
	{
		$this->db = $db;
	}

	public function GetAll($parent)
	{
		$root_id = NULL;
		
		$this->rows_list = array();

		if ($parent) // aktywna jest jakaś kategoria
		{
			// szuka wspólnego rodzica:
			$query = "SELECT * FROM categories WHERE type = 2 AND id = " . intval($parent);
			$result = mysqli_query($this->db, $query);
			if ($result)
			{
				$row = mysqli_fetch_assoc($result); 
				$root_id = $row['parent_id'];
				if ($row['parent_id'])
				{
					$row['id'] = $row['parent_id'];
					$row['level'] = 0;
					$row['caption'] = '[ <b>..</b> ]';
					$row['link'] = 'index.php?route=category&id=' . $row['parent_id'];
					$this->rows_list[] = $row;
				}
				mysqli_free_result($result);
			}
		}

		$query = 	"SELECT id, parent_id, caption, link, type, level, permission, page_id, target FROM categories".
					" WHERE parent_id = " . intval($root_id) . " AND type = 2 AND visible = 1 ORDER BY item_order";

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$row['level'] = 1;
				$this->rows_list[] = $row;

				if ($parent == $row['id']) // aktywna jest dana kategoria
				{
					$sub_query = 	"SELECT id, parent_id, caption, link, type, level, permission, page_id, target FROM categories".
									" WHERE parent_id = ". $row['id'] ." AND type = 2 AND visible = 1 ORDER BY item_order";

					$sub_result = mysqli_query($this->db, $sub_query);
					
					while ($sub_row = mysqli_fetch_assoc($sub_result))
					{
						$sub_row['level'] = 2;
						$this->rows_list[] = $sub_row;
					}
				}
			}
			mysqli_free_result($result);
		}
				
		return $this->rows_list;
	}
}

?>