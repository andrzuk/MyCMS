<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Logins_Model
{
	private $db;
	
	private $rows_list;
	private $row_item;
	private $table_name;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'logins'; // nazwa głównej tabeli modelu w bazie
	}
	
	public function SetPages($value, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
				
		$data_type = isset($_SESSION['mode']) ? ($_SESSION['mode'] == 1 ? ' AND user_id > 0' : ' AND user_id = 0') : NULL;
		
		$date_range = isset($_SESSION['date_from']) && isset($_SESSION['date_to']) ? " AND login_time >= '" . $_SESSION['date_from'] . " 00:00:00' AND login_time <= '" . $_SESSION['date_to'] . " 23:59:59'" : NULL;
		
		$filter = empty($value) ? NULL : " AND (login LIKE '%" . $value . "%' OR agent LIKE '%" . $value . "%' OR user_ip LIKE '%" . $value . "%' OR password LIKE '%" . $value . "%')";

		$query = "SELECT COUNT(*) AS licznik FROM " . $this->table_name . " WHERE 1" . $condition . $data_type . $filter . $date_range;
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
			if ($row)
			{
				$row['password'] = PASS_MASK;
			}
			$this->row_item = $row;
			mysqli_free_result($result);
		}
		return $this->row_item;
	}
	
	public function GetAll($limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$data_type = isset($_SESSION['mode']) ? ($_SESSION['mode'] == 1 ? ' AND user_id > 0' : ' AND user_id = 0') : NULL;
		
		$date_range = isset($_SESSION['date_from']) && isset($_SESSION['date_to']) ? " AND login_time >= '" . $_SESSION['date_from'] . " 00:00:00' AND login_time <= '" . $_SESSION['date_to'] . " 23:59:59'" : NULL;
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (login LIKE '%" . $_SESSION['list_filter'] . "%' OR agent LIKE '%" . $_SESSION['list_filter'] . "%' OR user_ip LIKE '%" . $_SESSION['list_filter'] . "%' OR password LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT id, agent, user_ip, login, password, user_id, login_time" .
					" FROM " . $this->table_name . " WHERE 1" . $condition . $data_type . $filter . $date_range .
					" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
					" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];		

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$row['password'] = PASS_MASK;
				$this->rows_list[] = $row;
			} 
			mysqli_free_result($result);
		}
		
		return $this->rows_list;
	}	
}

?>