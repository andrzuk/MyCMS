<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Visitors_Model
{
	private $db;
	
	private $rows_list;
	private $row_item;
	private $table_name;
	
	private $mySqlDateTime;
	
	private $host_name;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'visitors'; // nazwa głównej tabeli modelu w bazie
		
		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
		
		include LIB_DIR . 'hosts.php';
		$this->host_name = new Hosts($db);
	}
	
	public function SetPages($value, $limit, $show_rows)
	{
		$_SESSION['result_capacity'] = 0;
		$_SESSION['page_counter'] = 0;
		
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);

		$filter = empty($value) ? NULL : " AND (visitor_ip LIKE '%" . $value . "%' OR http_referer LIKE '%" . $value . "%' OR request_uri LIKE '%" . $value . "%')";
		
		$query_params = $this->GetQueryParams();
		
		$query = "SELECT COUNT(*) AS licznik FROM " . $this->table_name . " WHERE 1" . $condition . $query_params . $filter;
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
			$row['visitor_ip'] = array(
				'ip' => $row['visitor_ip'],
				'host' => $this->host_name->find_host_name($row['visitor_ip']),
			);
			$row['http_referer'] = array(
				'original' => $row['http_referer'],
				'converted' => str_replace(array("=", "%", ","), array(" = ", " % ", ", "), $row['http_referer']),
			);
			$row['request_uri'] = array(
				'original' => $row['request_uri'],
				'converted' => str_replace(array("=", "%", ","), array(" = ", " % ", ", "), $row['request_uri']),
			);
			$this->row_item = $row;
			mysqli_free_result($result);
		}
		return $this->row_item;
	}
	
	public function GetAll($limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$query_params = $this->GetQueryParams();
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (visitor_ip LIKE '%" . $_SESSION['list_filter'] . "%' OR http_referer LIKE '%" . $_SESSION['list_filter'] . "%' OR request_uri LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT * FROM " . $this->table_name . " WHERE 1" . $condition . $query_params . $filter .
					" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
					" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];		

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$row['visitor_ip'] = array(
					'ip' => $row['visitor_ip'],
					'host' => $this->host_name->find_host_name($row['visitor_ip']),
				);
				$row['http_referer'] = str_replace(array("=", "%", ","), array(" = ", " % ", ", "), $row['http_referer']);
				$row['request_uri'] = str_replace(array("=", "%", ","), array(" = ", " % ", ", "), $row['request_uri']);
				$this->rows_list[] = $row;
			}
			mysqli_free_result($result);
		}
		return $this->rows_list;
	}
	
	public function GetParams()
	{
		$this->rows_list = array();

		$query = "SELECT * FROM query_set ORDER BY id";
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
	
	public function SetParams($record_item)
	{
		$query = "DELETE FROM query_set";
		mysqli_query($this->db, $query);

		foreach ($record_item as $key => $value)
		{
			if ($key == 'filters')
			{
				foreach ($value as $k => $v)
				{
					$query = "INSERT INTO query_set (field, operator, value) VALUES ('" . $v['field'] . "', '" . $v['operator'] . "', '" . addslashes($v['value']) . "')";
					mysqli_query($this->db, $query);
				}
			}
			else
			{
				$query = "INSERT INTO query_set (field, operator, value) VALUES ('" . $key . "', '=', '" . addslashes($value) . "')";
				mysqli_query($this->db, $query);
			}
		}		
		$query = "INSERT INTO query_set (field, operator, value) VALUES ('modified', '=', NOW())";
		mysqli_query($this->db, $query);

		return mysqli_affected_rows($this->db);
	}
	
	private function GetQueryParams()
	{
		$sub_query = NULL;
		
		$query = "SELECT * FROM query_set ORDER BY id";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				if ($row['field'] == 'period_from') $sub_query .= " AND visited >= '" . $row['value'] . " 00:00:00'";
				if ($row['field'] == 'period_to') $sub_query .= " AND visited <= '" . $row['value'] . " 23:59:59'";
				if (in_array($row['field'], array('id', 'visitor_ip', 'http_referer', 'request_uri', 'visited')))
				{
					switch ($row['operator'])
					{
						case 'equal':
							$main_operator = "=";
							$type = 1;
							break;
						case 'like':
							$main_operator = "LIKE";
							$type = 2;
							break;
						case 'less':
							$main_operator = "<";
							$type = 1;
							break;
						case 'great':
							$main_operator = ">";
							$type = 1;
							break;
						case 'between':
							$main_operator = "BETWEEN";
							$type = 3;
							break;
						case 'differ':
							$main_operator = "NOT LIKE";
							$type = 2;
							break;
					}
					if ($type == 1)
						$sub_query .= " AND ". $row['field'] ." ". $main_operator ." '". $row['value'] ."'";
					if ($type == 2)
						$sub_query .= " AND ". $row['field'] ." ". $main_operator ." '%". $row['value'] ."%'";
					if ($type == 3)
						$sub_query .= " AND ". $row['field'] ." ". $main_operator ." ". $row['value'];
				}
				if ($row['field'] == 'exceptions') $sub_query .= " AND visitor_ip NOT IN (". $row['value'] .")";
			}
			mysqli_free_result($result);
		}
		return $sub_query;
	}	
	
	public function AddExclude($record_item)
	{
		$query = "UPDATE query_set" .
		         " SET value = CONCAT(value, ', \'". $record_item['visitor_ip']['ip'] ."\'')" .
		         " WHERE field = 'exceptions'";
		mysqli_query($this->db, $query);
		
		$query = "UPDATE query_set" .
		         " SET value = NOW()" .
		         " WHERE field = 'modified'";
		mysqli_query($this->db, $query);

		return mysqli_affected_rows($this->db);
	}
}

?>