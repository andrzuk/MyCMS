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
		$this->row_item = array();

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
	
	public function Receive($record_item, $send_object, $send_copy)
	{
		foreach ($send_object as $k => $v)
		{
			if ($k == 'server') $server_item = $v;
			if ($k == 'session') $session_item = $v;
		}

		$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
					$server_item['REMOTE_ADDR'] . "', '" . 
					mysqli_real_escape_string($this->db, trim($record_item['autor'])) . "', '" . 
					mysqli_real_escape_string($this->db, trim($record_item['email'])) . "', '" . 
					mysqli_real_escape_string($this->db, trim($record_item['message'])) . "', '1', '" . 
					$this->mySqlDateTime . "', '')";
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