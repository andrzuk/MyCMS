<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Map_Model
{
	private $db;
	
	private $row_item;
	private $rows_list;
	private $table_name;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'categories'; // nazwa głównej tabeli modelu w bazie
	}
	
	public function GetTree()
	{
		$this->rows_list = array();

		$query = "SELECT * FROM " . $this->table_name . " ORDER BY id";

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