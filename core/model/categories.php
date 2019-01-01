<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Categories_Model
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
		$this->table_name = 'categories'; // nazwa głównej tabeli modelu w bazie
		
		$status = new Status($db);
		$this->user_id = $status->get_value('user_id');

		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SetPages($value, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$data_type = isset($_SESSION['mode']) ? ' AND type = ' . $_SESSION['mode'] : NULL;

		$filter = empty($value) ? NULL : " AND (caption LIKE '%" . $value . "%' OR link LIKE '%" . $value . "%')";

		$query = "SELECT COUNT(*) AS licznik FROM " . $this->table_name . " WHERE 1" . $condition . $data_type . $filter;
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
		
		$data_type = isset($_SESSION['mode']) ? ' AND type = ' . $_SESSION['mode'] : NULL;
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (caption LIKE '%" . $_SESSION['list_filter'] . "%' OR link LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT * FROM " . $this->table_name . " WHERE 1" . $condition . $data_type . $filter .
					" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
					" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];		

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$row['type'] = 'img/16x16/menu_type_'.$row['type'].'.png';
				$this->rows_list[] = $row;
			} 
			mysqli_free_result($result);
		}
		
		return $this->rows_list;
	}
	
	public function Add($record_item)
	{
		$target = isset($record_item['target']) ? 1 : 0;

		$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
					$record_item['type'] . "', '" . 
					$record_item['level'] . "', '" . 
					$record_item['parent_id'] . "', '" . 
					$record_item['permission'] . "', '" . 
					$record_item['item_order'] . "', '" . 
					mysqli_real_escape_string($this->db, trim($record_item['caption'])) . "', '" . 
					mysqli_real_escape_string($this->db, trim($record_item['link'])) . "', '" . 
					$record_item['icon_id'] . "', '" . 
					$record_item['page_id'] . "', '" . 
					$record_item['visible'] . "', '" . 
					$target . "', '" . 
					$this->mySqlDateTime . "')";
		mysqli_query($this->db, $query);
		
		if (mysqli_affected_rows($this->db)) $result = $this->UpdateLink();
		
		$query = "SELECT id FROM " . $this->table_name . " ORDER BY id DESC LIMIT 0, 1";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$category_id = $row['id'];
			mysqli_free_result($result);
		}

		$query = "INSERT INTO pages VALUES (NULL, '0', '0', '" . $category_id . "', '" . 
					mysqli_real_escape_string($this->db, trim($record_item['caption'])) . "', '', '" . 
					$this->user_id . "', '1', '" . 
					$this->mySqlDateTime . "', '0')";
		mysqli_query($this->db, $query);
		
		return $result;
	}
	
	public function Edit($record_item, $id)
	{
		$target = isset($record_item['target']) ? 1 : 0;
		
		$query = "UPDATE " . $this->table_name . 
					" SET type='" . $record_item['type'] . 
					"', level='" . $record_item['level'] . 
					"', parent_id='" . $record_item['parent_id'] . 
					"', permission='" . $record_item['permission'] . 
					"', item_order='" . $record_item['item_order'] . 
					"', caption='" . mysqli_real_escape_string($this->db, trim($record_item['caption'])) . 
					"', link='" . mysqli_real_escape_string($this->db, trim($record_item['link'])) . 
					"', visible='" . $record_item['visible'] . 
					"', target='" . $target . 
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
	
	public function UpdateLink()
	{
		$query = "SELECT id, link FROM " . $this->table_name . " ORDER BY id DESC LIMIT 0, 1";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$id = $row['id'];
			$link = $row['link'];
			mysqli_free_result($result);
		}
		
		$sub_link = 'index.php?route=category&id=';
		
		if (strpos($link, $sub_link) !== FALSE)
		{
			$link = $sub_link . intval($id);

			$query = "UPDATE " . $this->table_name . " SET link='". $link ."' WHERE id=" . intval($id);
			mysqli_query($this->db, $query);
		}

		return mysqli_affected_rows($this->db);
	}
	
	public function Remove($id)
	{
		$query = "DELETE FROM " . $this->table_name . " WHERE id=" . intval($id);
		mysqli_query($this->db, $query);

		/*
		$query = "DELETE FROM archives WHERE category_id=" . intval($id);
		mysqli_query($this->db, $query);

		$query = "DELETE FROM pages WHERE category_id=" . intval($id);
		mysqli_query($this->db, $query);
		*/

		return mysqli_affected_rows($this->db);
	}
	
	public function MoveUp($id)
	{
		$query = "SELECT item_order FROM " . $this->table_name . " WHERE id = ". intval($id);
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result);
			$item_order = $row['item_order'];
			mysqli_free_result($result);
		}
		
		if ($item_order > 1)
		{
			$query = "UPDATE " . $this->table_name . " SET item_order = ". intval($item_order - 1) . 
						", modified='" . $this->mySqlDateTime . "' WHERE id = ". intval($id);
			$result = mysqli_query($this->db, $query);
		}

		return mysqli_affected_rows($this->db);
	}
	
	public function MoveDown($id)
	{
		$query = "SELECT item_order FROM " . $this->table_name . " WHERE id = ". intval($id);
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result);
			$item_order = $row['item_order'];
			mysqli_free_result($result);
		}

		$query = "SELECT COUNT(*) as items_count FROM " . $this->table_name;
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result);
			$items_count = $row['items_count'];
			mysqli_free_result($result);
		}
		
		if ($item_order < $items_count)
		{
			$query = "UPDATE " . $this->table_name . " SET item_order = ". intval($item_order + 1) . 
						", modified='" . $this->mySqlDateTime . "' WHERE id = ". intval($id);
			$result = mysqli_query($this->db, $query);
		}

		return mysqli_affected_rows($this->db);
	}
	
	public function GetParents()
	{
		$this->rows_list = array();

		$query = 	"SELECT id, caption FROM " . $this->table_name .
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
	
	public function GetOrders()
	{
		$this->rows_list = array();

		$query = 	"SELECT item_order FROM " . $this->table_name .
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
	
	public function GetPageId($id)
	{
		$query = "SELECT id FROM pages WHERE category_id=" . intval($id) . " ORDER BY id DESC LIMIT 0, 1";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$page_id = $row['id'];
			mysqli_free_result($result);
		}
		return $page_id;
	}	
}

?>