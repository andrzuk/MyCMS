<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Contact_Model
{
	private $db;
	
	private $row_item;
	private $table_name;
	
	private $mySqlDateTime;
	
	private $setting;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->table_name = 'user_messages'; // nazwa głównej tabeli modelu w bazie
		
		$this->setting = new Settings($db);
		
		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function GetPageContent()
	{
		$visitor_ip = $_SERVER['REMOTE_ADDR'];
		$this->row_item = array();

		// odczytuje z konfiguracji limit wejść blokujący żądania:
		$black_list_contact_limit = intval($this->setting->get_config_key('black_list_contact_limit'));

		if ($this->DetectRobots($visitor_ip, $black_list_contact_limit)) return $this->row_item;

		$this->UpdatePreviews();

		$query = "SELECT * FROM pages WHERE visible=1 AND main_page=2";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result); 
			$this->row_item = $row;
			mysqli_free_result($result);
		}
		if (isset($this->row_item['contents']))
		{
			// jeśli mamy znacznik importu innej strony:
			if (substr($this->row_item['contents'], 0, strlen(PAGE_IMPORT_TEMPLATE)) == PAGE_IMPORT_TEMPLATE)
			{
				$import_page_id = substr($this->row_item['contents'], strlen(PAGE_IMPORT_TEMPLATE));
				
				$query = "SELECT * FROM pages WHERE visible=1 AND id=" . intval($import_page_id);
				$result = mysqli_query($this->db, $query);
				if ($result)
				{
					$row = mysqli_fetch_assoc($result); 
					$this->row_item = $row;
					mysqli_free_result($result);
				}
			}
		}
		return $this->row_item;
	}
	
	private function UpdatePreviews()
	{
		$query = "UPDATE pages" .
				" SET previews = previews + 1" .
				" WHERE visible=1 AND main_page=2";
		mysqli_query($this->db, $query);
		
		return mysqli_affected_rows($this->db);
	}
	
	private function DetectRobots($author_ip, $max_range)
	{
		$query = "SELECT COUNT(*) AS counter FROM visitors" .
				 " WHERE visitor_ip = '". $author_ip ."' AND request_uri LIKE '%route=contact' AND visited > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result);
			$this->row_item = $row;
			mysqli_free_result($result);
			if (intval($this->row_item['counter']) > $max_range)
			{
				$query = "UPDATE configuration" .
						" SET key_value = CONCAT(key_value, ', \'". $author_ip ."\''), modified='". $this->mySqlDateTime ."'".
						" WHERE key_name = 'black_list_visitors'";
				mysqli_query($this->db, $query);
			}
		}
		return intval($this->row_item['counter']) > $max_range;
	}
	
	private function LockRobots($author_ip, $max_messages)
	{
		$counter = 0;
		$max_range = $max_messages * 10;
		$query = "SELECT client_ip FROM " . $this->table_name .
				 " ORDER BY id DESC LIMIT " . $max_range;
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				if ($row['client_ip'] == $author_ip) $counter++;
			} 
			mysqli_free_result($result);
		}
		if ($counter >= $max_messages)
		{
			$query = "UPDATE configuration" .
					 " SET key_value = CONCAT(key_value, ', \'". $author_ip ."\''), modified='". $this->mySqlDateTime ."'".
					 " WHERE key_name = 'black_list_visitors'";
			mysqli_query($this->db, $query);
		}
		return $counter == $max_messages;
	}
	
	private function CheckAuthors($author_name)
	{
		$query = "SELECT key_value FROM configuration" .
				 " WHERE key_name = 'black_list_messages_authors'";
		$result = mysqli_query($this->db, $query);
		if ($result)
		{
			$row = mysqli_fetch_assoc($result);
			$this->row_item = $row;
			mysqli_free_result($result);
		}
		return strpos($this->row_item['key_value'], $author_name);
	}
	
	public function Receive($record_item, $send_object, $send_copy)
	{
		foreach ($send_object as $k => $v)
		{
			if ($k == 'server') $server_item = $v;
			if ($k == 'session') $session_item = $v;
		}
		
		if ($record_item['robot'] != NULL) return 0; // anti-robots protection

		// odczytuje z konfiguracji liczbę wiadomości blokującą nadawcę:
		$black_list_messages_limit = intval($this->setting->get_config_key('black_list_messages_limit'));

		// sprawdza, czy nadawca jest seryjnym autorem wiadomości, i jeśli tak, dopisuje go do czarnej listy:
		if ($black_list_messages_limit)
			if ($this->LockRobots($server_item['REMOTE_ADDR'], $black_list_messages_limit))
				return 0;
		
		// odczytuje z konfiguracji listę zablokowanych nadawców:
		$black_list_messages_authors = $this->setting->get_config_key('black_list_messages_authors');

		// sprawdza, czy nadawca jest seryjnym autorem wiadomości, i jeśli tak, dopisuje go do czarnej listy:
		if ($black_list_messages_authors)
		{
			if ($this->CheckAuthors(trim($record_item['autor'])))
				return 0;
			if ($this->CheckAuthors(trim($record_item['email'])))
				return 0;
		}

		$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
					$server_item['REMOTE_ADDR'] . "', '" . 
					mysqli_real_escape_string($this->db, trim($record_item['autor'])) . "', '" . 
					mysqli_real_escape_string($this->db, trim($record_item['email'])) . "', '" . 
					mysqli_real_escape_string($this->db, trim($record_item['message'])) . "', '1', '" . 
					$this->mySqlDateTime . "', NOW())";
		mysqli_query($this->db, $query);
		
		// odczytuje z konfiguracji opcje wysylania raportow:
		$send_new_message_report = $this->setting->get_config_key('send_new_message_report');

		$base_domain = $this->setting->get_config_key('base_domain');
		$email_sender_name = $this->setting->get_config_key('email_sender_name');
		$email_sender_address = $this->setting->get_config_key('email_sender_address');
		$email_report_address = $this->setting->get_config_key('email_report_address');
		$email_report_subject = $this->setting->get_config_key('email_report_subject');
		$email_report_body_1 = $this->setting->get_config_key('email_report_body_1');
		$email_report_body_2 = $this->setting->get_config_key('email_report_body_2');
		
		if ($send_new_message_report == 'true')
		{
			// wysyła e-mailem informację do admina o napisaniu wiadomosci przez usera:
			$recipient = $email_report_address;
			$mail_body = $email_report_body_1 ."\n\nUżytkownik {".$record_item['autor']."} (e-mail: ".$record_item['email'].") napisał do serwisu wiadomość:\n\n\"".$record_item['message']."\"\n\n".$base_domain."\n";
			$subject = $email_report_subject;
			$header = "From: ". $email_sender_name . " <" . $email_sender_address . ">\r\n";
			$header = "MIME-Versio: 1.0\r\n" . "Content-type: text/html; charset=UTF-8\r\n" . $header;
			$mail_body = $this->convert_to_html($subject, $mail_body);
			mail($recipient, $subject, $mail_body, $header);
		}

		if ($send_copy)
		{
			// wysyła e-mailem kopie wiadomosci do autora:
			$recipient = $record_item['email'];
			$mail_body = "Drogi Użytkowniku,\n\nPodając się jako {".$record_item['autor']."} napisałe(a)ś do serwisu wiadomość:\n\n\"".$record_item['message']."\"\n\nBardzo dziękujemy.\n\n".$base_domain."\n";
			$subject = $email_report_subject;
			$header = "From: ". $email_sender_name . " <" . $email_sender_address . ">\r\n";
			$header = "MIME-Versio: 1.0\r\n" . "Content-type: text/html; charset=UTF-8\r\n" . $header;
			$mail_body = $this->convert_to_html($subject, $mail_body);
			mail($recipient, $subject, $mail_body, $header);
		}

		return mysqli_affected_rows($this->db);
	}

	private function convert_to_html($subject, $content)
	{
		$main_text = "<html><head><title>" . $subject . "</title></head><body><p>" . $content . "</p></body></html>";
		$main_text = str_replace("\n", "<br />", $main_text);
		return $main_text;
	}
}

?>