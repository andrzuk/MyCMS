<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Roles_Model
{
	private $db;
	
	private $rows_list;
	private $row_item;
	private $table_name;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'user_roles'; // nazwa głównej tabeli modelu w bazie
	}
	
	public function SetPages($value, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND ' . $this->table_name . '.id = ' . intval($limit);
		
		$filter = empty($value) ? NULL : " AND (user_login LIKE '%" . $value . "%' OR imie LIKE '%" . $value . "%' OR nazwisko LIKE '%" . $value . "%')";

		$query =	"SELECT COUNT(DISTINCT user_id) AS licznik FROM " . $this->table_name .
					" INNER JOIN users ON users.id = " . $this->table_name . ".user_id" .
					" WHERE access <= 1" . $condition . $filter;
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$_SESSION['result_capacity'] = $row['licznik'];
			$_SESSION['page_counter'] = intval($row['licznik'] / $show_rows) + ($row['licznik'] % $show_rows > 0 ? 1 : 0);
			mysqli_free_result($result);
		}
	}
	
	public function GetOne($user_id)
	{
		$this->row_item = array();

		$query =	"SELECT user_id AS id, user_id, NULL AS function_id, user_login," .
					" CONCAT(imie, ' ', nazwisko) AS user_name, status " .
					" FROM " . $this->table_name .
					" INNER JOIN users ON users.id = " . $this->table_name . ".user_id" .
					" WHERE user_id = " . intval($user_id);
					
		$result = mysqli_query($this->db, $query);
		
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 			
			
			$sub_query =	"SELECT admin_functions.function" .
							" FROM " . $this->table_name .
							" INNER JOIN admin_functions ON admin_functions.id = " . $this->table_name . ".function_id" .
							" WHERE " . $this->table_name . ".user_id = " . $row['user_id'] .
							" AND " . $this->table_name . ".access = 1" . 
							" ORDER BY " . $this->table_name . ".function_id";

			$sub_result = mysqli_query($this->db, $sub_query);
			
			if ($sub_result)
			{
				$row['function_id'] = array();
				
				while ($sub_row = mysqli_fetch_assoc($sub_result))
				{
					$row['function_id'][] = $sub_row['function'];
				}
				mysqli_free_result($sub_result);
			}				
			
			switch ($row['status'])
			{
				case 1:
					$status = 'Administratorzy';
					break;
				case 2:
					$status = 'Operatorzy';
					break;
				case 3:
					$status = 'Użytkownicy';
					break;
			}
			$row['status'] = $status;
			
			$this->row_item = $row;
			
			mysqli_free_result($result);
		}
		return $this->row_item;
	}
	
	public function GetAll($limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND ' . $this->table_name . '.id = ' . intval($limit);
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (user_login LIKE '%" . $_SESSION['list_filter'] . "%' OR imie LIKE '%" . $_SESSION['list_filter'] . "%' OR nazwisko LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query =	"SELECT users.id, users.user_login," .
					" CONCAT(users.imie, ' ', users.nazwisko) AS user_name," .
					" users.status, NULL AS function" .
					" FROM " . $this->table_name . 
					" INNER JOIN users ON users.id = " . $this->table_name . ".user_id" .
					" INNER JOIN admin_functions ON admin_functions.id = " . $this->table_name . ".function_id" .
					" WHERE " . $this->table_name . ".access <= 1" . $condition . $filter .
					" GROUP BY users.id" .
					" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
					" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$sub_query =	"SELECT admin_functions.function" .
								" FROM " . $this->table_name .
								" INNER JOIN admin_functions ON admin_functions.id = " . $this->table_name . ".function_id" .
								" WHERE " . $this->table_name . ".user_id = " . $row['id'] .
								" AND " . $this->table_name . ".access = 1" . 
								" ORDER BY " . $this->table_name . ".function_id";

				$sub_result = mysqli_query($this->db, $sub_query);
				
				if ($sub_result)
				{
					$row['function'] = array();
					
					while ($sub_row = mysqli_fetch_assoc($sub_result))
					{
						$row['function'][] = $sub_row['function'];
					}
					mysqli_free_result($sub_result);
				}				
				
				switch ($row['status'])
				{
					case 1:
						$status = 'Administratorzy';
						break;
					case 2:
						$status = 'Operatorzy';
						break;
					case 3:
						$status = 'Użytkownicy';
						break;
				}
				$row['status'] = $status;

				$this->rows_list[] = $row;
			} 
			mysqli_free_result($result);
		}
		
		return $this->rows_list;
	}
	
	public function Add($record_item)
	{
		$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
					$record_item['user_id'] . "', '" . 
					$record_item['function_id'] . "', '" . 
					$record_item['access'] . "')";
		mysqli_query($this->db, $query);

		return mysqli_affected_rows($this->db);
	}
	
	public function GetLast()
	{
		$this->row_item = array();

		$query = "SELECT user_id FROM " . $this->table_name . " ORDER BY id DESC LIMIT 0, 1";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$this->row_item = $row;
			mysqli_free_result($result);
		}
		return $this->row_item;
	}
	
	public function Remove($user_id)
	{
		$query = "DELETE FROM " . $this->table_name . " WHERE user_id=" . intval($user_id);
		mysqli_query($this->db, $query);

		return mysqli_affected_rows($this->db);
	}

	public function GetAllUsers()
	{
		$this->rows_list = array();

		$query =	"SELECT users.id, users.user_login, users.imie, users.nazwisko, users.status" .
					" FROM users" .
					" LEFT JOIN user_roles ON user_roles.user_id = users.id" .
					" GROUP BY users.id" .
					" ORDER BY users.id";

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				switch ($row['status'])
				{
					case 1:
						$status = 'Administratorzy';
						break;
					case 2:
						$status = 'Operatorzy';
						break;
					case 3:
						$status = 'Użytkownicy';
						break;
				}
				$row['status'] = $status;

				$this->rows_list[] = $row;
			} 
			mysqli_free_result($result);
		}
		
		return $this->rows_list;
	}
	
	public function GetNewUsers()
	{
		$this->rows_list = array();

		$query =	"SELECT users.id, users.user_login, users.imie, users.nazwisko, users.status" .
					" FROM users" .
					" LEFT JOIN user_roles ON user_roles.user_id = users.id" .
					" WHERE users.id NOT IN (SELECT user_id FROM user_roles)" .
					" GROUP BY users.id" .
					" ORDER BY users.id";

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				switch ($row['status'])
				{
					case 1:
						$status = 'Administratorzy';
						break;
					case 2:
						$status = 'Operatorzy';
						break;
					case 3:
						$status = 'Użytkownicy';
						break;
				}
				$row['status'] = $status;

				$this->rows_list[] = $row;
			} 
			mysqli_free_result($result);
		}
		
		return $this->rows_list;
	}

	public function GetFunctions($user_id)
	{
		$this->rows_list = array();

		if ($user_id) // edit user roles
		{
			$query =	"SELECT admin_functions.id, admin_functions.function, admin_functions.meaning, admin_functions.module," .
						" user_roles.user_id, user_roles.function_id, user_roles.access" .
						" FROM admin_functions" .
						" INNER JOIN user_roles ON user_roles.function_id = admin_functions.id" .
						" WHERE user_roles.user_id = " . intval($user_id) .
						" ORDER BY admin_functions.id";
		}
		else // new user roles
		{
			$query =	"SELECT admin_functions.id, admin_functions.function, admin_functions.meaning, admin_functions.module," .
						" NULL AS user_id, admin_functions.id AS function_id, NULL AS access" .
						" FROM admin_functions" .
						" ORDER BY admin_functions.id";
		}

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
