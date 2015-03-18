<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Password_Model
{
	private $db;
	
	private $row_item;
	
	private $setting;
	
	private $mySqlDateTime;
	
	public function __construct($db)
	{
		$this->db = $db;

		$this->setting = new Settings($db);
		
		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function Reset($record_item)
	{
		// weryfikuje uzytkownika:
		
		$query = 	"SELECT * FROM users".
					" WHERE user_login='". $record_item['login'] ."'".
					" AND email='". $record_item['email'] ."'".
					" AND active='1'";

		$result = mysqli_query($this->db, $query);
		
		if ($result)
		{
			$num_rows = mysqli_num_rows($result);
			
			if ($num_rows == 1) // pomyslne odnalezienie (weryfikacja OK)
			{
				$row = mysqli_fetch_assoc($result); 
				$this->row_item = $row;
				mysqli_free_result($result);

				// ustawia nowe hasło i rejestruje date i czas modyfikacji uzytkownika:
				
				$phrase = NULL;
				$length = 8;
				$code = md5(uniqid(rand(), true));
				$phrase = substr($code, 0, $length);
		
				$query = "UPDATE users SET user_password='" . sha1($phrase) .
							"', data_modyfikacji='" . $this->mySqlDateTime .
							"' WHERE id=" . intval($row['id']);
						 
				mysqli_query($this->db, $query);

				$base_domain = $this->setting->get_config_key('base_domain');
				$email_sender_name = $this->setting->get_config_key('email_sender_name');
				$email_sender_address = $this->setting->get_config_key('email_sender_address');
				$email_remindpwd_subject = $this->setting->get_config_key('email_remindpwd_subject');
				$email_remindpwd_body_1 = $this->setting->get_config_key('email_remindpwd_body_1');
				$email_remindpwd_body_2 = $this->setting->get_config_key('email_remindpwd_body_2');
				
				// wysyła e-mailem nowe hasło:
				$recipient = $row['email'];
				$mail_body = "Szanowny użytkowniku,\n\n" . $email_remindpwd_body_1 . "\n\n login: <b>". $row['user_login']. "</b>\n hasło: <b>". $phrase . "</b>\n\n" . $email_remindpwd_body_2 . "\n\nPozdrawiamy,\n\n" . $base_domain . "\n";
				$subject = $email_remindpwd_subject;
				$header = "From: ". $email_sender_name . " <" . $email_sender_address . ">\r\n";
				$header = "MIME-Versio: 1.0\r\n" . "Content-type: text/html; charset=UTF-8\r\n" . $header;
				$mail_body = $this->convert_to_html($subject, $mail_body);
				mail($recipient, $subject, $mail_body, $header);

				return $this->row_item;
			}
			else // nieudana weryfikacja
			{
				return NULL;
			}
		}
		else // nieudana weryfikacja
		{
			return NULL;
		}
	}
	
	private function convert_to_html($subject, $content)
	{
		$main_text = "<html><head><title>" . $subject . "</title></head><body><p>" . $content . "</p></body></html>";
		$main_text = str_replace("\n", "<br />", $main_text);
		return $main_text;
	}

	public function Store($record_object, $login_object)
	{
		foreach ($record_object as $k => $v)
		{
			if ($k == 'login') $user_login = $v;
			if ($k == 'email') $user_email = $v;
		}
		foreach ($login_object as $k => $v)
		{
			if ($k == 'server') $record_item = $v;
			if ($k == 'session') $session_item = $v;
			if ($k == 'result') $result = empty($v) ? 0 : 1;
		}
		
		$query = "INSERT INTO reminds VALUES (NULL, '" . 
					$record_item['HTTP_USER_AGENT'] . "', '" . 
					$record_item['REMOTE_ADDR'] . "', '" . 
					mysqli_real_escape_string($this->db, $user_login) . "', '" . 
					mysqli_real_escape_string($this->db, $user_email) . "', '" . 
					$result . "', '" . 
					$this->mySqlDateTime . "')";
		mysqli_query($this->db, $query);
		
		return mysqli_affected_rows($this->db);
	}
}

?>
