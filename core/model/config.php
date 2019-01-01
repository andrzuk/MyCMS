<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Config_Model
{
	private $db;
	
	private $rows_list;
	private $row_item;
	private $table_name;
	
	private $mySqlDateTime;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'configuration'; // nazwa głównej tabeli modelu w bazie
		
		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SetPages($value, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$filter = empty($value) ? NULL : " AND (key_name LIKE '%" . $value . "%' OR key_value LIKE '%" . $value . "%' OR meaning LIKE '%" . $value . "%')";

		$query = "SELECT COUNT(*) AS licznik FROM " . $this->table_name . " WHERE 1" . $condition . $filter;
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$_SESSION['result_capacity'] = $row['licznik'];
			$_SESSION['page_counter'] = intval($row['licznik'] / $show_rows) + ($row['licznik'] % $show_rows > 0 ? 1 : 0);
			mysqli_free_result($result);
		}
	}
	
	public function GetOne($id)
	{
		$this->row_item = array();

		$query = "SELECT * FROM " . $this->table_name . " WHERE id=" . intval($id);
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$this->row_item = $row;
			mysqli_free_result($result);
		}
		return $this->row_item;
	}
	
	public function GetAll($limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (key_name LIKE '%" . $_SESSION['list_filter'] . "%' OR key_value LIKE '%" . $_SESSION['list_filter'] . "%' OR meaning LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT * FROM " . $this->table_name . " WHERE 1" . $condition . $filter .
					" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
					" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];		

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$row['field_type'] = 'img/16x16/setting_type_'.$row['field_type'].'.png';
				$this->rows_list[] = $row;
			} 
			mysqli_free_result($result);
		}
		
		return $this->rows_list;
	}
	
	public function Add($record_item)
	{
		$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
					mysqli_real_escape_string($this->db, $record_item['key_name']) . "', '" . 
					mysqli_real_escape_string($this->db, $record_item['key_value']) . "', '" . 
					mysqli_real_escape_string($this->db, $record_item['meaning']) . "', '" . 
					$record_item['field_type'] . "', '" . 
					$record_item['active'] . "', '" . 
					$this->mySqlDateTime . "')";
		mysqli_query($this->db, $query);
		
		return mysqli_affected_rows($this->db);
	}
	
	public function Edit($record_item, $id)
	{
		$query = "UPDATE " . $this->table_name . 
					" SET key_name='" . mysqli_real_escape_string($this->db, $record_item['key_name']) . 
					"', key_value='" . mysqli_real_escape_string($this->db, $record_item['key_value']) . 
					"', meaning='" . mysqli_real_escape_string($this->db, $record_item['meaning']) . 
					"', field_type='" . $record_item['field_type'] . 
					"', active='" . $record_item['active'] . 
					"', modified='" . $this->mySqlDateTime . 
					"' WHERE id=" . intval($id);
		mysqli_query($this->db, $query);
		
		return mysqli_affected_rows($this->db);
	}
	
	public function GetLast()
	{
		$this->row_item = array();

		$query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC LIMIT 0, 1";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$this->row_item = $row;
			mysqli_free_result($result);
		}
		return $this->row_item;
	}
	
	public function Remove($id)
	{
		$query = "DELETE FROM " . $this->table_name . " WHERE id=" . intval($id);
		mysqli_query($this->db, $query);

		return mysqli_affected_rows($this->db);
	}
}

?>