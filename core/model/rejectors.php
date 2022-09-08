<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Rejectors_Model
{
	private $db;
	
	private $rows_list;
	private $row_item;
	private $table_name;
	
	private $mySqlDateTime;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'rejectors'; // nazwa głównej tabeli modelu w bazie
		
		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);

		include LIB_DIR . 'hosts.php';
		$this->host_name = new Hosts($db);
	}
	
	public function SetPages($value, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$date_range = isset($_SESSION['date_from']) && isset($_SESSION['date_to']) ? " AND visited >= '" . $_SESSION['date_from'] . " 00:00:00' AND visited <= '" . $_SESSION['date_to'] . " 23:59:59'" : NULL;
		
		$filter = empty($value) ? NULL : " AND (visitor_ip LIKE '%" . $value . "%' OR request_uri LIKE '%" . $value . "%')";

		$query = "SELECT COUNT(*) AS licznik FROM " . $this->table_name . " WHERE 1" . $condition . $filter . $date_range;
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$_SESSION['result_capacity'] = $row['licznik'];
			$_SESSION['page_counter'] = intval($row['licznik'] / $show_rows) + ($row['licznik'] % $show_rows > 0 ? 1 : 0);
			mysqli_free_result($result);
		}
	}
	
	public function GetAll($limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$date_range = isset($_SESSION['date_from']) && isset($_SESSION['date_to']) ? " AND visited >= '" . $_SESSION['date_from'] . " 00:00:00' AND visited <= '" . $_SESSION['date_to'] . " 23:59:59'" : NULL;
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (visitor_ip LIKE '%" . $_SESSION['list_filter'] . "%' OR request_uri LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT * FROM " . $this->table_name . " WHERE 1" . $condition . $filter . $date_range .
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
				$row['request_uri'] = str_replace(array("=", "%", ","), array(" = ", " % ", ", "), $row['request_uri']);
				$this->rows_list[] = $row;
			}
			mysqli_free_result($result);
		}
		return $this->rows_list;
	}
	
	public function GetSummaryData($range_days)
	{
		$this->rows_list = array('index' => array(), 'contact' => array());

		for ($day = $range_days; $day > 0; $day--)
		{
			$str_days = '-' . strval($day - 1) . ' days';
			$date_range = date("Y-m-d", strtotime($str_days));
			$query = "SELECT COUNT(*) AS counter, '" . $date_range . "' AS visited FROM " . $this->table_name . 
			         " WHERE request_uri IN ('/', '/index.php') AND visited BETWEEN '" . $date_range . " 00:00:00' AND '" . $date_range . " 23:59:59'";
			$result = mysqli_query($this->db, $query);
			if ($result)
			{
				$row = mysqli_fetch_assoc($result);
				$this->rows_list['index'][] = $row;
				mysqli_free_result($result);
			}
		}
		for ($day = $range_days; $day > 0; $day--)
		{
			$str_days = '-' . strval($day - 1) . ' days';
			$date_range = date("Y-m-d", strtotime($str_days));
			$query = "SELECT COUNT(*) AS counter, '" . $date_range . "' AS visited FROM " . $this->table_name . 
			         " WHERE request_uri LIKE '%route=contact' AND visited BETWEEN '" . $date_range . " 00:00:00' AND '" . $date_range . " 23:59:59'";
			$result = mysqli_query($this->db, $query);
			if ($result)
			{
				$row = mysqli_fetch_assoc($result);
				$this->rows_list['contact'][] = $row;
				mysqli_free_result($result);
			}
		}
		
		return $this->rows_list;
	}

	public function GetStatsData($range_days)
	{
		$this->rows_list = array('range' => array(), 'total' => array());

		$str_days = '-' . strval($range_days - 1) . ' days';
		$date_range = date("Y-m-d", strtotime($str_days));
		$query = "SELECT visitor_ip, COUNT(*) AS counter FROM " . $this->table_name . 
				 " WHERE visited BETWEEN '" . $date_range . "' AND NOW()" . 
				 " GROUP BY visitor_ip ORDER BY counter DESC";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$this->rows_list['range'][] = $row;
			}
			mysqli_free_result($result);
		}
		$query = "SELECT visitor_ip, COUNT(*) AS counter FROM " . $this->table_name . 
				 " GROUP BY visitor_ip ORDER BY counter DESC";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$this->rows_list['total'][] = $row;
			}
			mysqli_free_result($result);
		}
		
		return $this->rows_list;
	}
}

?>