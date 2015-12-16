<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Sites_Model
{
	private $db;
	
	private $rows_list;
	private $row_item;
	private $table_name;
	
	private $mySqlDateTime;
	
	private $user_id;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'pages'; // nazwa głównej tabeli modelu w bazie
		
		$status = new Status($db);
		$this->user_id = $status->get_value('user_id');

		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SetPages($value, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND ' . $this->table_name . '.id = ' . intval($limit);
		
		$filter = empty($value) ? NULL : " AND (title LIKE '%" . $value . "%' OR contents LIKE '%" . $value . "%')";

		$query = 	"SELECT COUNT(*) AS licznik FROM " . $this->table_name . 
					" INNER JOIN users ON users.id = " . $this->table_name . ".author_id" .
					" WHERE system_page = 1" . $condition . $filter;
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
		$archives_items = array();

		$query = 	"SELECT pages.*, users.user_login" .
					" FROM " . $this->table_name . 
					" INNER JOIN users ON users.id = " . $this->table_name . ".author_id" .
					" WHERE " . $this->table_name . ".id=" . intval($id);
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$this->row_item = $row;
			mysqli_free_result($result);
		}
		
		$query = 	"SELECT id, modified AS caption FROM archives" .
					" WHERE page_id = " . intval($id) .
					" ORDER BY modified";

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$archives_items[] = $row;
			} 
			mysqli_free_result($result);
		}
		
		$this->row_item['archives'] = $archives_items;

		return $this->row_item;
	}
	
	public function GetAll($limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND ' . $this->table_name . '.id = ' . intval($limit);
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (title LIKE '%" . $_SESSION['list_filter'] . "%' OR contents LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT pages.id, pages.main_page, pages.title, pages.contents," .
					" users.user_login, pages.modified, pages.visible" .
					" FROM " . $this->table_name . 
					" INNER JOIN users ON users.id = " . $this->table_name . ".author_id" .
					" WHERE system_page = 1" . $condition . $filter .
					" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
					" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];		

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
	
	public function Add($record_item)
	{
		$record_item['author_id'] = $this->user_id;
		
		if ($record_item['main_page'])
		{
			$query = "UPDATE " . $this->table_name . 
					" SET main_page=0 WHERE main_page=" . $record_item['main_page'];
			mysqli_query($this->db, $query);
		}

		$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
					$record_item['main_page'] . "', '" . 
					$record_item['system_page'] . "', '" . 
					$record_item['category_id'] . "', '" . 
					mysqli_real_escape_string($this->db, trim($record_item['title'])) . "', '" . 
					mysqli_real_escape_string($this->db, trim($record_item['contents'])) . "', '" . 
					$record_item['author_id'] . "', '" . 
					$record_item['visible'] . "', '" . 
					$this->mySqlDateTime . "', 0)";
		mysqli_query($this->db, $query);
		
		return mysqli_affected_rows($this->db);
	}
	
	public function Edit($record_item, $id)
	{
		$record_item['author_id'] = $this->user_id;
		
		if ($record_item['main_page'])
		{
			$query = "UPDATE " . $this->table_name . 
					" SET main_page=0 WHERE main_page=" . $record_item['main_page'];
			mysqli_query($this->db, $query);
		}

		$query = "UPDATE " . $this->table_name . 
					" SET main_page='" . $record_item['main_page'] . 
					"', system_page='" . $record_item['system_page'] . 
					"', category_id='" . $record_item['category_id'] . 
					"', title='" . mysqli_real_escape_string($this->db, trim($record_item['title'])) . 
					"', contents='" . mysqli_real_escape_string($this->db, trim($record_item['contents'])) . 
					"', author_id='" . $record_item['author_id'] . 
					"', visible='" . $record_item['visible'] . 
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
		$query = "DELETE FROM archives WHERE page_id=" . intval($id);
		mysqli_query($this->db, $query);

		$query = "DELETE FROM " . $this->table_name . " WHERE id=" . intval($id);
		mysqli_query($this->db, $query);

		return mysqli_affected_rows($this->db);
	}
		
	public function Archive($id)
	{
		$original_row_item = array();
		$counter = 0;

		$query = "SELECT * FROM " . $this->table_name . " WHERE id=" . intval($id);
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$original_row_item = $row;
			mysqli_free_result($result);
		}

		$query = "SELECT COUNT(*) AS licznik FROM archives WHERE page_id=" . intval($id) . " AND modified='" . $original_row_item['modified'] . "'";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$counter = $row['licznik'];
			mysqli_free_result($result);
		}
		
		if ($counter == 0) // nie ma jeszcze tej kopii
		{
			$query = "INSERT INTO archives VALUES (NULL, '" . 
						$original_row_item['id'] . "', '" . 
						$original_row_item['main_page'] . "', '" . 
						$original_row_item['system_page'] . "', '" . 
						$original_row_item['category_id'] . "', '" . 
						mysqli_real_escape_string($this->db, $original_row_item['title']) . "', '" . 
						mysqli_real_escape_string($this->db, $original_row_item['contents']) . "', '" . 
						$original_row_item['author_id'] . "', '" . 
						$original_row_item['visible'] . "', '" . 
						$original_row_item['modified'] . "', '" .
						$original_row_item['previews'] . "')";
			mysqli_query($this->db, $query);
			
			return mysqli_affected_rows($this->db);
		}
		else // ta kopia już istnieje
		{
			return FALSE;
		}
	}
	
	public function Restore($id)
	{
		$record_item = array();

		$query = "SELECT * FROM archives WHERE id=" . intval($id);
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$record_item = $row;
			mysqli_free_result($result);
		}

		$query = "UPDATE " . $this->table_name . 
					" SET main_page='" . $record_item['main_page'] . 
					"', system_page='" . $record_item['system_page'] . 
					"', category_id='" . $record_item['category_id'] . 
					"', title='" . mysqli_real_escape_string($this->db, $record_item['title']) . 
					"', contents='" . mysqli_real_escape_string($this->db, $record_item['contents']) . 
					"', author_id='" . $record_item['author_id'] . 
					"', visible='" . $record_item['visible'] . 
					"', modified='" . $record_item['modified'] . 
					"', previews='" . $record_item['previews'] . 
					"' WHERE id=" . intval($record_item['page_id']);
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
}

?>