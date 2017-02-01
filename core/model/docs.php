<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Docs_Model
{
	private $db;
	
	private $rows_list;
	private $row_item;
	private $table_name;
	
	private $mySqlDateTime;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'documents'; // nazwa głównej tabeli modelu w bazie
		
		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SetPages($value, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$data_type = isset($_SESSION['mode']) ? ' AND section_id = ' . $_SESSION['mode'] : NULL;
		
		$filter = empty($value) ? NULL : " AND (doc_description LIKE '%" . $value . "%' OR file_name LIKE '%" . $value . "%')";

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
		
		$data_type = isset($_SESSION['mode']) ? ' AND section_id = ' . $_SESSION['mode'] : NULL;
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (doc_description LIKE '%" . $_SESSION['list_filter'] . "%' OR file_name LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT * FROM " . $this->table_name . " WHERE 1" . $condition . $data_type . $filter .
					" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
					" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];		

		$result = mysqli_query($this->db, $query);

		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				$row['file_size'] = number_format($row['file_size'] / 1024, 0, ',', '.') .' KB';
				$this->rows_list[] = $row;
			} 
			mysqli_free_result($result);
		}
		
		return $this->rows_list;
	}
	
	public function Add($record_item)
	{
		// dopisuje rekord do bazy:
		$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
					$record_item['section_id'] . "', '" . 
					$record_item['owner_id'] . "', '" . 
					NULL . "', '" . 
					NULL . "', '" . 
					NULL . "', '" . 
					mysqli_real_escape_string($this->db, $record_item['doc_description']) . "', '" . 
					$record_item['active'] . "', '" . 
					$this->mySqlDateTime . "')";
		mysqli_query($this->db, $query);
					
		return mysqli_affected_rows($this->db);
	}
	
	public function AddFile($record_item, $file_item)
	{
		$sub_dir = $record_item['section_id'] == 1 ? DOC_DIR : SND_DIR;
		$doc_type = $record_item['section_id'] == 1 ? array('application/pdf') : array('audio/mp3', 'audio/mpeg');

		if (in_array($file_item['type'], $doc_type)) // plik dokumentu pdf lub mp3
		{
			// odczytuje rozmiar oryginalnego obrazka:
			list($width, $height) = getimagesize($file_item['tmp_name']); 
			
			// dopisuje rekord do bazy:
			$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
						$record_item['section_id'] . "', '" . 
						$record_item['owner_id'] . "', '" . 
						$file_item['type'] . "', '" . 
						$file_item['name'] . "', '" . 
						$file_item['size'] . "', '" . 
						mysqli_real_escape_string($this->db, $record_item['doc_description']) . "', '" . 
						$record_item['active'] . "', '" . 
						$this->mySqlDateTime . "')";
			mysqli_query($this->db, $query);
			
			// odczytuje id dodanego rekordu:
			$query = "SELECT id FROM " . $this->table_name . " ORDER BY id DESC LIMIT 0, 1";
			$result = mysqli_query($this->db, $query);
			if ($result)
			{
				$row = mysqli_fetch_assoc($result); 
				$doc_id = $row['id'];
				mysqli_free_result($result);
			}
			
			// tworzy katalog dla dokumentów:
			if (!file_exists(GALLERY_DIR . $sub_dir)) mkdir(GALLERY_DIR . $sub_dir, 0777, true);

			// zapisuje plik na dysku:
			$target = GALLERY_DIR . $sub_dir . $doc_id;

			// zapisuje oryginalny obrazek na serwer:
			move_uploaded_file($file_item['tmp_name'], $target);
			
			return mysqli_affected_rows($this->db);
		}
		else // nie dokument
		{
			return -1;
		}
	}
	
	public function Edit($record_item, $id)
	{
		$query = "UPDATE " . $this->table_name . 
					" SET section_id='" . $record_item['section_id'] . 
					"', owner_id='" . $record_item['owner_id'] . 
					"', doc_description='" . mysqli_real_escape_string($this->db, $record_item['doc_description']) . 
					"', active='" . $record_item['active'] . 
					"', modified='" . $this->mySqlDateTime . 
					"' WHERE id=" . intval($id);
		mysqli_query($this->db, $query);

		return mysqli_affected_rows($this->db);
	}
	
	public function EditFile($record_item, $file_item, $id)
	{
		$sub_dir = $this->GetDir($id);
		
		if (in_array($file_item['type'], $this->GetType($id))) // plik dokumentu pdf lub mp3
		{
			// uaktualnia rekord w bazie:
			$query = "UPDATE " . $this->table_name . 
						" SET section_id='" . $record_item['section_id'] . 
						"', owner_id='" . $record_item['owner_id'] . 
						"', file_format='" . $file_item['type'] . 
						"', file_name='" . $file_item['name'] . 
						"', file_size='" . $file_item['size'] . 
						"', doc_description='" . mysqli_real_escape_string($this->db, $record_item['doc_description']) . 
						"', active='" . $record_item['active'] . 
						"', modified='" . $this->mySqlDateTime . 
						"' WHERE id=" . intval($id);
			mysqli_query($this->db, $query);
			
			// tworzy katalog dla dokumentów:
			if (!file_exists(GALLERY_DIR . $sub_dir)) mkdir(GALLERY_DIR . $sub_dir, 0777, true);

			// zapisuje plik na dysku:
			$target = GALLERY_DIR . $sub_dir . $id;

			// zapisuje oryginalny obrazek na serwer:
			move_uploaded_file($file_item['tmp_name'], $target);
			
			return mysqli_affected_rows($this->db);
		}
		else // nie dokument
		{
			return -1;
		}
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
		$sub_dir = $this->GetDir($id);
		
		$query = "DELETE FROM " . $this->table_name . " WHERE id=" . intval($id);
		mysqli_query($this->db, $query);

		// usuniecie pliku z dysku serwera:
		$delete_result = unlink(GALLERY_DIR . $sub_dir . $id);
		
		return mysqli_affected_rows($this->db);
	}	
	
	public function Download($id)
	{
		$sub_dir = $this->GetDir($id);
		
		// pobiera informacje o pliku:
		$query = "SELECT file_name, file_format FROM " . $this->table_name . " WHERE id=" . intval($id);
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$file_name = $row['file_name'];
			$file_format = $row['file_format'];
			mysqli_free_result($result);
		}
		$file_name = str_replace(" ", "_", $file_name);
		
		$doc_name = GALLERY_DIR . $sub_dir . $id;
		
		// wczytuje plik z serwera:
		$fp = fopen($doc_name, 'rb');
		$doc_data = fread($fp, filesize($doc_name));
		fclose($fp);
		
		// wysyła plik do przeglądarki:
		header('Content-disposition: attachment; filename='. $file_name);
		header('Content-type: '. $file_format .'; charset=utf-8');
		
		// wysyła dane:
		if (IsSet($doc_data)) echo $doc_data;
		
		// przerywa, aby nie dołączać treści strony:
		die;
	}

	public function GetDir($id)
	{
		$sub_dir = NULL;

		$query = "SELECT section_id FROM " . $this->table_name . " WHERE id=" . intval($id);
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$section_id = $row['section_id'];
			mysqli_free_result($result);
		}

		$sub_dir = $section_id == 1 ? DOC_DIR : SND_DIR;
		
		return $sub_dir;
	}
	
	public function GetType($id)
	{
		$doc_type = NULL;

		$query = "SELECT section_id FROM " . $this->table_name . " WHERE id=" . intval($id);
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$section_id = $row['section_id'];
			mysqli_free_result($result);
		}
		
		$doc_type = $section_id == 1 ? array('application/pdf') : array('audio/mp3', 'audio/mpeg');
		
		return $doc_type;
	}
}

?>