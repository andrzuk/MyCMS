<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Category_Model
{
	private $db;
	
	private $row_item;
	private $rows_list;
	private $table_name;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'pages'; // nazwa głównej tabeli modelu w bazie
	}
	
	public function GetPageContent($id)
	{
		$this->row_item = array();

		$this->UpdatePreviews($id);

		$query = "SELECT " . $this->table_name . ".*" . 
				" FROM " . $this->table_name . 
				" INNER JOIN categories ON categories.id = " . $this->table_name . ".category_id" .
				" WHERE " . $this->table_name . ".visible=1 AND categories.visible=1 AND category_id=" . intval($id) .
				" ORDER BY " . $this->table_name . ".id DESC LIMIT 0, 1";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$this->row_item = $row;
			mysqli_free_result($result);
		}
		
		if (isset($this->row_item['contents']))
		{
			// jeśli mamy znacznik importu innej strony:
			if (substr($this->row_item['contents'], 0, strlen(PAGE_IMPORT_TEMPLATE)) == PAGE_IMPORT_TEMPLATE)
			{
				$import_page_id = substr($this->row_item['contents'], strlen(PAGE_IMPORT_TEMPLATE));
				
				$query = "SELECT * FROM " . $this->table_name . " WHERE visible=1 AND id=" . intval($import_page_id);
				$result = mysqli_query($this->db, $query);
				if ($result)
				{
					$row = mysqli_fetch_assoc($result); 
					$this->row_item = $row;
					mysqli_free_result($result);
				}
			}
		}
		
		return $this->row_item;
	}	

	private function UpdatePreviews($id)
	{
		$query = "UPDATE " . $this->table_name . 
					" SET previews = previews + 1" .
					" WHERE visible=1 AND category_id=" . intval($id);
		mysqli_query($this->db, $query);
		
		return mysqli_affected_rows($this->db);
	}
	
	public function GetAuthors()
	{
		$this->rows_list = array();

		$query = 	"SELECT id, user_login FROM users" .
					" ORDER BY id";

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
	
	public function GetCategory($id)
	{
		$this->row_item = array();

		$query = "SELECT * FROM categories WHERE id=" . intval($id);
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$this->row_item = $row;
			mysqli_free_result($result);
		}
		return $this->row_item;
	}
	
	public function GetPath($id)
	{
		$idx = $id;
		$parent_id = NULL;
		$link = NULL;
		$caption = NULL;

		$this->row_item = array();
		$this->rows_list = array();
		
		$all_categories = array();
		$step_categories = array();

		$query = "SELECT * FROM categories ORDER BY id";

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$all_categories[] = $row;
			} 
			mysqli_free_result($result);
		}

		// buduje ścieżkę od danej kategorii do root-a:
		do
		{
			foreach ($all_categories as $k => $v)
			{
				if ($v['id'] == $idx)
				{
					$parent_id = $v['parent_id'];
					$caption = $v['caption'];
					$link = $v['link'];
					break;
				}
			}
			if (!empty($link)) // kategoria istnieje
			{
				$this->row_item = array('link' => $link, 'caption' => $caption);
				$this->rows_list[] = $this->row_item;
			}
			else // kategoria nie istnieje
			{
				$this->row_item = array('link' => 'index.php?route=category&id='.$id, 'caption' => 'Strona nie znaleziona');
				$this->rows_list[] = $this->row_item;
				break;
			}
			$idx = $v['parent_id'];
			// gdy ścieżka nieprawidłowa (loop-corrupt):
			if (in_array($idx, $step_categories)) break;
			$step_categories[] = $idx;			
		}
		while ($parent_id);

		$this->row_item = array('link' => 'index.php', 'caption' => 'Strona główna');
		$this->rows_list[] = $this->row_item;
				
		return array_reverse($this->rows_list);
	}
}

?>