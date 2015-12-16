<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Index_Model
{
	private $db;
	private $id;
	private $row_item;
	private $rows_list;
	private $table_name;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'pages'; // nazwa głównej tabeli modelu w bazie
	}
	
	public function GetPageContent()
	{
		$this->row_item = array();

		$this->UpdatePreviews();

		$query = "SELECT * FROM " . $this->table_name . " WHERE visible=1 AND main_page=1";
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
	
	private function UpdatePreviews()
	{
		$query = "UPDATE " . $this->table_name . 
					" SET previews = previews + 1" .
					" WHERE visible=1 AND main_page=1";
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
	
	public function IsInstalled()
	{
		$exist = NULL;
		$query = "SELECT COUNT(*) AS licznik FROM " . $this->table_name . " WHERE visible=1 AND main_page=1";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$exist = $row['licznik'];
			mysqli_free_result($result);
		}
		return $exist;
	}
}

?>