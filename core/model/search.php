<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Search_Model
{
	private $db;
	
	private $user_status;
	private $rows_list;
	
	private $mySqlDateTime;
	
	public function __construct($db)
	{
		$this->db = $db;

		$status = new Status($db);
		$this->user_status = $status->get_value('user_status');

		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SetPages($value, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		if ($this->user_status) // zalogowany
		{
			$condition .= ' AND categories.permission >= ' . intval($this->user_status);
		}
		else // gość
		{
			$condition .= ' AND categories.permission = 4';
		}

		$query = 	"SELECT COUNT(*) AS licznik" . 
					" FROM pages " . 
					" INNER JOIN categories ON categories.id = pages.category_id" .
					" INNER JOIN users ON users.id = pages.author_id" .
					" WHERE pages.contents LIKE '%" . str_replace(' ', '%', mysqli_real_escape_string($this->db, $value)) . "%' AND pages.system_page = 0 AND pages.visible = 1" . $condition;
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$_SESSION['result_capacity'] = $row['licznik'];
			$_SESSION['page_counter'] = intval($row['licznik'] / $show_rows) + ($row['licznik'] % $show_rows > 0 ? 1 : 0);
			mysqli_free_result($result);
		}
	}
	
	public function Search($value, $limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		if ($this->user_status) // zalogowany
		{
			$condition .= ' AND categories.permission >= ' . intval($this->user_status);
		}
		else // gość
		{
			$condition .= ' AND categories.permission = 4';
		}

		$this->rows_list = array();
		
		$query = 	"SELECT pages.title, pages.contents, pages.category_id," . 
					" categories.caption, users.user_login, pages.modified " . 
					" FROM pages " . 
					" INNER JOIN categories ON categories.id = pages.category_id" .
					" INNER JOIN users ON users.id = pages.author_id" .
					" WHERE pages.contents LIKE '%" . str_replace(' ', '%', mysqli_real_escape_string($this->db, $value)) . "%' AND pages.system_page = 0 AND pages.visible = 1" . $condition .
					" ORDER BY title" . 
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

	public function Store($record_object, $search_object)
	{
		foreach ($record_object as $k => $v)
		{
			if ($k == 'search_text') $search_text = $v;
		}
		foreach ($search_object as $k => $v)
		{
			if ($k == 'server') $record_item = $v;
			if ($k == 'session') $session_item = $v;
		}
		
		$query = "INSERT INTO searches VALUES (NULL, '" . 
					$record_item['HTTP_USER_AGENT'] . "', '" . 
					$record_item['REMOTE_ADDR'] . "', '" . 
					mysqli_real_escape_string($this->db, $search_text) . "', '" . 
					$this->mySqlDateTime . "')";
		mysqli_query($this->db, $query);
		
		return mysqli_affected_rows($this->db);
	}
}

?>
