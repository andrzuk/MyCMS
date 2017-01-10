<?php

/*
 * Kontroler podstawowych operacji na modułach - lista, podgląd, nowy, edycja, usuwanie.
 */

class Operator
{
	private $content_title;
	private $content_options;
	private $site_content;
	private $site_message;
	private $site_dialog;
	
	private $user_status;
	
	private $model_object;
	private $view_object;
	private $record_object;
	private $file_object;
	private $navi_object;
	private $db_params;
	private $list_params;
	private $list_columns;
	
	private $data_import;

	private $records_list;

	// inicjalizacja:
	
	public function __construct($objects)
	{
		$this->user_status = isset($_SESSION['user_status']) ? $_SESSION['user_status'] : NULL;
		
		$this->model_object = isset($objects['model_object']) ? $objects['model_object'] : NULL;
		$this->view_object = isset($objects['view_object']) ? $objects['view_object'] : NULL;
		$this->record_object = isset($objects['record_object']) ? $objects['record_object'] : NULL;
		$this->navi_object = isset($objects['navi_object']) ? $objects['navi_object'] : NULL;
		$this->db_params = isset($objects['db_params']) ? $objects['db_params'] : NULL;
		$this->list_params = isset($objects['list_params']) ? $objects['list_params'] : NULL;
		$this->list_columns = isset($objects['list_columns']) ? $objects['list_columns'] : NULL;
		
		$this->data_import = isset($objects['data_import']) ? $objects['data_import'] : NULL;
		
		$this->records_list = array();
	}
	
	// zwraca odpowiednie dane:
	
	public function Get($object_name)
	{
		$object = array(
			'content_title' => $this->content_title,
			'content_options' => $this->content_options,
			'site_content' => $this->site_content,
			'site_message' => $this->site_message,
			'site_dialog' => $this->site_dialog,
		);
		
		return $object[$object_name];
	}

	// komunikat o braku prawa dostępu:
		
	private function AccessDenied()
	{
		$this->content_options = array (
			array (
				'address' => 'index.php',
				'caption' => 'Zamknij',
				'icon' => 'img/stop.png'
			),
		);
		
		$this->site_dialog = array(
			'ERROR',
			'Brak uprawnień',
			'Uruchomiona funkcja wymaga zalogowania do serwisu na konto o profilu administratora lub posiadania uprawnień określonych za pomocą systemu Access Control List.',
			array(
				array(
					'index.php?route=login', 'Zaloguj'
				),
				array(
					'index.php', 'Zamknij'
				),
			)
		);
	}
	
	// dodawanie:
	
	public function Add($params, $access, $acl)
	{
		$failed_fields = array();
		
		$this->content_title = $params['content_title'];

		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];

			if (isset($_POST["save_button"]) || isset($_POST["update_button"])) // obsługa formularza
			{
				// zabezpieczenie przed odświeżeniem formularza:

				if (!isset($_SESSION['form_sent']) || $_POST['form_hash'] != $_SESSION['form_sent']) // wysłano formularz
				{
					$_SESSION['form_sent'] = $_POST['form_hash'];

					$values = array();
					
					foreach ($_POST as $key => $value)
					{
						$values[$key] = trim($value);
						$_SESSION['form_fields'][$key] = trim($value);
						$check_field[$key] = trim($value);
					}

					foreach ($_FILES as $key => $value)
					{
						if ($key == 'user_file')
						{
							foreach ($_FILES[$key] as $k => $v)
							{
								$check_field[$k] = trim($v);
								if ($k == 'name') $check_field[$key] = trim($v);
								$this->file_object[$k] = trim($v);
							}
						}						
					}

					// sprawdzamy, czy wszystkie wymagane pola zostały wypełnione:
					
					$all_required_present = TRUE;
					
					foreach ($params['required'] as $key => $value)
					{
						if (empty($check_field[$value]))
						{
							$failed_fields[] = $value;
							$all_required_present = FALSE;
						}
					}
			
					if ($all_required_present) // podano wymagane dane
					{
						// dane pobrane z formularza wstawia do record_object:
						
						foreach ($this->record_object as $k => $v)
						{
							foreach ($values as $key => $value)
							{
								if ($v == $key)
								{
									$this->record_object[$v] = $value;
									break;
								}
							}						
						}
						
						// sprawdzanie pól formularza pod kątem bezpieczeństwa:
						
						$input_check = NULL;
						
						foreach ($values as $key => $value)
						{
							if (in_array($key, $params['check']))
							{
								$input_check .= ' ' . $value;
							}
						}
						
						// sprawdzanie poprawności e-maila:
						
						$email_check = NULL;

						if (isset($params['email']))
						{
							foreach ($values as $key => $value)
							{
								if ($key == $params['email'])
								{
									$email_check = $value;
									break;
								}
							}
						}

						// sprawdzanie poprawności pesela:
						
						$pesel_check = NULL;
						
						if (isset($params['pesel']))
						{
							foreach ($values as $key => $value)
							{
								if ($key == $params['pesel'])
								{
									$pesel_check = $value;
									break;
								}
							}
						}
						
						// sprawdzanie unikalności rekordu:
						
						$unique = NULL;
						
						if (isset($params['unique']))
						{
							$fields = array();
							
							foreach ($params['unique'] as $k => $v)
							{
								if (isset($this->record_object[$v]))
								{
									$fields[$v] = empty($this->record_object[$v]) ? 'NULL' : $this->record_object[$v];
								}
							}
							$unique = $this->model_object->Exist($fields, NULL);
						}

						include LIB_DIR . 'validator.php';
						
						$validator_object = new Validator();
						
						$check_result = $validator_object->check_security($input_check);
						
						$email_result = $validator_object->check_email($email_check);
						
						$pesel_result = $validator_object->check_pesel($pesel_check);
						
						if ($check_result) // kontrola bezpieczeństwa poprawna
						{
							if ($email_result) // email poprawny
							{
								if ($pesel_result) // pesel poprawny
								{
									if (!$unique) // unikalne wartości pól
									{
										if (isset($_FILES['user_file']) && !$this->file_object['error']) // formularz z plikiem
										{
											// dopisuje rekord do bazy i zapisuje plik na dysk:
											$result = $this->model_object->AddFile($this->record_object, $this->file_object);
										}
										else // formularz zwykły
										{
											// dopisuje rekord do bazy:
											$result = $this->model_object->Add($this->record_object);
										}
										
										if ($result) // zapis się powiódł
										{
											if (isset($_POST["save_button"])) // zapisz i kontynuuj
											{
												// pobiera ostatnio dopisany rekord:
												$this->record_object = $this->model_object->GetLast();
												
												// wyświetla formularz wypełniony danymi:
												$this->site_content = $this->view_object->ShowForm($this->record_object, $params['required'], $failed_fields, $this->data_import);

												// wyświetla komunikat:
												$this->site_message = array(
													'INFORMATION', 'Szczegóły bieżącego rekordu zostały poprawnie zapisane.'
												);
											}
											if (isset($_POST["update_button"])) // zapisz i zamknij
											{
												// ograniczenia dla usera:
												$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

												// pobiera listę rekordów:
												$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
												
												// aktualizuje statystykę listy:
												$this->navi_object->update($this->model_object, $this->list_params);
				
												// wyświetla listę rekordów:
												$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);

												// wyświetla komunikat:
												$this->site_message = array(
													'INFORMATION', 'Rekord został poprawnie dopisany do bazy.'
												);
											}
										}
										else // zapis się nie powiódł
										{
											// wyświetla pusty formularz:
											$this->site_content = $this->view_object->ShowForm(NULL, $params['required'], $failed_fields, $this->data_import);
											
											// wyświetla komunikat:
											$this->site_message = array(
												'ERROR', 'Zapis rekordu się nie powiódł. Proszę spróbować ponownie.'
											);
										}
									}
									else // unikalna wartość już występuje
									{
										// wyświetla pusty formularz:
										$this->site_content = $this->view_object->ShowForm(NULL, $params['required'], $failed_fields, $this->data_import);
										
										// wyświetla komunikat:
										$this->site_message = array(
											'ERROR', 'Rekord o takim loginie lub adresie e-mail lub numerze PESEL już występuje.'
										);
									}
								}
								else // pesel niepoprawny
								{
									// oznacza niepoprawne pole:
									$failed_fields[] = 'pesel';
								
									// wyświetla pusty formularz:
									$this->site_content = $this->view_object->ShowForm(NULL, $params['required'], $failed_fields, $this->data_import);
									
									// wyświetla komunikat:
									$this->site_message = array(
										'ERROR', 'Nieprawidłowy numer PESEL. Proszę poprawić.'
									);
								}
							}
							else // email niepoprawny
							{
								// oznacza niepoprawne pole:
								$failed_fields[] = 'email';
								
								// wyświetla pusty formularz:
								$this->site_content = $this->view_object->ShowForm(NULL, $params['required'], $failed_fields, $this->data_import);
								
								// wyświetla komunikat:
								$this->site_message = array(
									'ERROR', 'Nieprawidłowy adres e-mail. Proszę poprawić.'
								);
							}
						}
						else // nie przeszło kontroli bezpieczeństwa
						{
							// wyświetla pusty formularz:
							$this->site_content = $this->view_object->ShowForm(NULL, $params['required'], $failed_fields, $this->data_import);
							
							// wyświetla komunikat:
							$this->site_message = array(
								'ERROR', 'Do pól formularza wprowadzono zabronione wyrażenia.'
							);
						}
					}
					else // nie uzupełniono wszystkich pól
					{
						// wyświetla pusty formularz:
						$this->site_content = $this->view_object->ShowForm(NULL, $params['required'], $failed_fields, $this->data_import);
						
						// wyświetla komunikat:
						$this->site_message = array(
							'WARNING', 'Nie wypełniono wszystkich wymaganych pól. Proszę uzupełnić.'
						);
					}
				}
				else // odświeżono formularz
				{
					if (isset($_POST["save_button"])) // zapisz i kontynuuj
					{
						// wyświetla pusty formularz:
						$this->site_content = $this->view_object->ShowForm(NULL, $params['required'], $failed_fields, $this->data_import);
					}
					if (isset($_POST["update_button"])) // zapisz i zamknij
					{
						// ograniczenia dla usera:
						$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

						// pobiera listę rekordów:
						$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
						
						// aktualizuje statystykę listy:
						$this->navi_object->update($this->model_object, $this->list_params);

						// wyświetla listę rekordów:
						$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);
					}
					
					// wyświetla komunikat:
					$this->site_message = array(
						'WARNING', 'Formularz został już wysłany i nie należy go odświeżać.'
					);
				}
			}
			else if (isset($_POST["cancel_button"]))
			{
				// ograniczenia dla usera:
				$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

				// pobiera listę rekordów:
				$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
				
				// aktualizuje statystykę listy:
				$this->navi_object->update($this->model_object, $this->list_params);

				// wyświetla listę rekordów:
				$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);
			}
			else // pusty formularz
			{
				// czyści dane w formularzu:
				if (isset($_SESSION['form_fields']))
				{
					foreach ($_SESSION['form_fields'] as $key => $value)
					{
						$_SESSION['form_fields'][$key] = NULL;
					}
				}
				// wyświetla pusty formularz:
				$this->site_content = $this->view_object->ShowForm(NULL, $params['required'], $failed_fields, $this->data_import);
			}
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}		
	}
	
	// dodawanie wielu na raz:
	
	public function AddMulti($params, $access, $acl)
	{
		$failed_fields = array();
		$file_objects = array();
		
		$this->content_title = $params['content_title'];

		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];

			if (isset($_POST["upload_button"])) // obsługa formularza
			{
				// zabezpieczenie przed odświeżeniem formularza:

				if (!isset($_SESSION['form_sent']) || $_POST['form_hash'] != $_SESSION['form_sent']) // wysłano formularz
				{
					$_SESSION['form_sent'] = $_POST['form_hash'];

					foreach ($_POST as $key => $value)
					{
						$_SESSION['form_fields'][$key] = trim($value);
						$this->record_object[$key] = $value;
					}

					foreach ($_FILES as $key => $value)
					{
						foreach ($value as $k => $v)
						{
							foreach ($v as $i => $j)
							{
								$file_objects[$i][$k] = $j;
								$check_field[$key] = $j;
							}
						}
					}

					// sprawdzamy, czy wszystkie wymagane pola zostały wypełnione:
					
					$all_required_present = TRUE;

					foreach ($params['required'] as $key => $value)
					{
						if (empty($check_field[$value]))
						{
							$failed_fields[] = $value . '[]';
							$all_required_present = FALSE;
						}
					}

					if ($all_required_present) // podano wymagane dane
					{
						foreach ($file_objects as $k => $v)
						{
							foreach ($v as $i => $j)
							{
								$this->file_object[$i] = $j;
							}
							// dopisuje rekord do bazy i zapisuje plik na dysk:
							$result = $this->model_object->AddFile($this->record_object, $this->file_object);						
						}

						if ($result) // zapis się powiódł
						{
							// ograniczenia dla usera:
							$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

							// pobiera listę rekordów:
							$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
							
							// aktualizuje statystykę listy:
							$this->navi_object->update($this->model_object, $this->list_params);
				
							// wyświetla listę rekordów:
							$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);

							// wyświetla komunikat:
							$this->site_message = array(
								'INFORMATION', 'Rekordy zostały poprawnie dopisane do bazy.'
							);
						}
						else // zapis się nie powiódł
						{
							// wyświetla pusty formularz:
							$this->site_content = $this->view_object->ShowFormMulti(NULL, $params['required'], $failed_fields, $this->data_import);
							
							// wyświetla komunikat:
							$this->site_message = array(
								'ERROR', 'Zapis rekordu się nie powiódł. Proszę spróbować ponownie.'
							);
						}
					}
					else // nie uzupełniono wszystkich pól
					{
						// wyświetla pusty formularz:
						$this->site_content = $this->view_object->ShowFormMulti(NULL, $params['required'], $failed_fields, $this->data_import);
						
						// wyświetla komunikat:
						$this->site_message = array(
							'WARNING', 'Nie wypełniono wszystkich wymaganych pól. Proszę uzupełnić.'
						);
					}
				}
				else // odświeżono formularz
				{
					// ograniczenia dla usera:
					$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

					// pobiera listę rekordów:
					$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
					
					// aktualizuje statystykę listy:
					$this->navi_object->update($this->model_object, $this->list_params);
		
					// wyświetla listę rekordów:
					$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);
					
					// wyświetla komunikat:
					$this->site_message = array(
						'WARNING', 'Formularz został już wysłany i nie należy go odświeżać.'
					);
				}
			}
			else if (isset($_POST["cancel_button"]))
			{
				// ograniczenia dla usera:
				$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

				// pobiera listę rekordów:
				$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
				
				// aktualizuje statystykę listy:
				$this->navi_object->update($this->model_object, $this->list_params);

				// wyświetla listę rekordów:
				$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);
			}
			else // pusty formularz
			{
				// czyści dane w formularzu:
				if (isset($_SESSION['form_fields']))
				{
					foreach ($_SESSION['form_fields'] as $key => $value)
					{
						$_SESSION['form_fields'][$key] = NULL;
					}
				}
				// wyświetla pusty formularz:
				$this->site_content = $this->view_object->ShowFormMulti(NULL, $params['required'], $failed_fields, $this->data_import);
			}
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}		
	}
	
	// edycja:
	
	public function Edit($id, $params, $access, $acl)
	{
		$failed_fields = array();
		
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			if (isset($_POST["save_button"]) || isset($_POST["update_button"])) // obsługa formularza
			{
				// zabezpieczenie przed odświeżeniem formularza:

				if (!isset($_SESSION['form_sent']) || $_POST['form_hash'] != $_SESSION['form_sent']) // wysłano formularz
				{
					$_SESSION['form_sent'] = $_POST['form_hash'];

					$values = array();
					
					foreach ($_POST as $key => $value)
					{
						$values[$key] = trim($value);
						$_SESSION['form_fields'][$key] = trim($value);
						$check_field[$key] = trim($value);
					}
					
					foreach ($_FILES as $key => $value)
					{
						if ($key == 'user_file')
						{
							foreach ($_FILES[$key] as $k => $v)
							{
								$check_field[$k] = trim($v);
								if ($k == 'name') $check_field[$key] = trim($v);
								$this->file_object[$k] = trim($v);
							}
						}						
					}

					// sprawdzamy, czy wszystkie wymagane pola zostały wypełnione:
					
					$all_required_present = TRUE;

					foreach ($params['required'] as $key => $value)
					{
						if (empty($check_field[$value]))
						{
							$failed_fields[] = $value;
							$all_required_present = FALSE;
						}
					}
			
					if ($all_required_present) // podano wymagane dane				
					{
						// dane pobrane z formularza wstawia do record_object:
						
						foreach ($this->record_object as $k => $v)
						{
							foreach ($values as $key => $value)
							{
								if ($v == $key)
								{
									$this->record_object[$v] = $value;
									break;
								}
							}						
						}

						// sprawdzanie pól formularza pod kątem bezpieczeństwa:
						
						$input_check = NULL;
						
						foreach ($values as $key => $value)
						{
							if (in_array($key, $params['check']))
							{
								$input_check .= ' ' . $value;
							}
						}
						
						// sprawdzanie poprawności e-maila:
						
						$email_check = NULL;

						if (isset($params['email']))
						{
							foreach ($values as $key => $value)
							{
								if ($key == $params['email'])
								{
									$email_check = $value;
									break;
								}
							}
						}

						// sprawdzanie poprawności pesela:
						
						$pesel_check = NULL;
						
						if (isset($params['pesel']))
						{
							foreach ($values as $key => $value)
							{
								if ($key == $params['pesel'])
								{
									$pesel_check = $value;
									break;
								}
							}
						}

						// sprawdzanie unikalności rekordu:
						
						$unique = NULL;
						
						if (isset($params['unique']))
						{
							$fields = array();
							
							foreach ($params['unique'] as $k => $v)
							{
								if (isset($this->record_object[$v]))
								{
									$fields[$v] = empty($this->record_object[$v]) ? 'NULL' : $this->record_object[$v];
								}
							}
							$unique = $this->model_object->Exist($fields, $id);
						}

						include LIB_DIR . 'validator.php';
						
						$validator_object = new Validator();
						
						$check_result = $validator_object->check_security($input_check);

						$email_result = $validator_object->check_email($email_check);
						
						$pesel_result = $validator_object->check_pesel($pesel_check);
						
						if ($check_result) // kontrola bezpieczeństwa poprawna
						{
							if ($email_result) // email poprawny
							{
								if ($pesel_result) // pesel poprawny
								{
									if (!$unique) // unikalne wartości pól
									{
										if (isset($_FILES['user_file']) && !$this->file_object['error']) // formularz z plikiem
										{
											// dopisuje rekord do bazy i zapisuje plik na dysk:
											$result = $this->model_object->EditFile($this->record_object, $this->file_object, $id);
										}
										else // formularz zwykły
										{
											// zapisuje zmiany w rekordzie do bazy:
											$result = $this->model_object->Edit($this->record_object, $id);
										}
										
										if ($result) // zapis się powiódł
										{
											if (isset($_POST["save_button"])) // zapisz i kontynuuj
											{
												// pobiera rekord o danym Id:
												$this->record_object = $this->model_object->GetOne($id);
												
												// wyświetla formularz wypełniony danymi:
												$this->site_content = $this->view_object->ShowForm($this->record_object, $params['required'], $failed_fields, $this->data_import);

												// wyświetla komunikat:
												$this->site_message = array(
													'INFORMATION', 'Szczegóły bieżącego rekordu zostały poprawnie zapisane.'
												);
											}
											if (isset($_POST["update_button"])) // zapisz i zamknij
											{
												// ograniczenia dla usera:
												$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

												// pobiera listę rekordów:
												$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
												
												// aktualizuje statystykę listy:
												$this->navi_object->update($this->model_object, $this->list_params);
				
												// wyświetla listę rekordów:
												$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);

												// wyświetla komunikat:
												$this->site_message = array(
													'INFORMATION', 'Rekord został poprawnie zaktualizowany.'
												);
											}
										}
										else // zapis się nie powiódł
										{
											// pobiera rekord o danym Id:
											$this->record_object = $this->model_object->GetOne($id);
											
											// wyświetla formularz wypełniony danymi:
											$this->site_content = $this->view_object->ShowForm($this->record_object, $params['required'], $failed_fields, $this->data_import);
											
											// wyświetla komunikat:
											$this->site_message = array(
												'ERROR', 'Zapis rekordu się nie powiódł. Proszę spróbować ponownie.'
											);
										}
									}
									else // unikalna wartość już występuje
									{
										// pobiera rekord o danym Id:
										$this->record_object = $this->model_object->GetOne($id);
										
										// wyświetla formularz wypełniony danymi:
										$this->site_content = $this->view_object->ShowForm($this->record_object, $params['required'], $failed_fields, $this->data_import);

										// wyświetla komunikat:
										$this->site_message = array(
											'ERROR', 'Rekord o takim loginie lub adresie e-mail lub numerze PESEL już występuje.'
										);
									}
								}
								else // pesel niepoprawny
								{
									// oznacza niepoprawne pole:
									$failed_fields[] = 'pesel';
								
									// pobiera rekord o danym Id:
									$this->record_object = $this->model_object->GetOne($id);
									
									// wyświetla formularz wypełniony danymi:
									$this->site_content = $this->view_object->ShowForm($this->record_object, $params['required'], $failed_fields, $this->data_import);
									
									// wyświetla komunikat:
									$this->site_message = array(
										'ERROR', 'Nieprawidłowy numer PESEL. Proszę poprawić.'
									);
								}
							}
							else // email niepoprawny
							{
								// oznacza niepoprawne pole:
								$failed_fields[] = 'email';
								
								// pobiera rekord o danym Id:
								$this->record_object = $this->model_object->GetOne($id);
								
								// wyświetla formularz wypełniony danymi:
								$this->site_content = $this->view_object->ShowForm($this->record_object, $params['required'], $failed_fields, $this->data_import);
								
								// wyświetla komunikat:
								$this->site_message = array(
									'ERROR', 'Nieprawidłowy adres e-mail. Proszę poprawić.'
								);
							}						
						}
						else // nie przeszło kontroli bezpieczeństwa
						{
							// pobiera rekord o danym Id:
							$this->record_object = $this->model_object->GetOne($id);
							
							// wyświetla formularz wypełniony danymi:
							$this->site_content = $this->view_object->ShowForm($this->record_object, $params['required'], $failed_fields, $this->data_import);
													
							// wyświetla komunikat:
							$this->site_message = array(
								'ERROR', 'Do pól formularza wprowadzono zabronione wyrażenia.'
							);
						}
					}
					else // nie uzupełniono wszystkich pól
					{				
						// pobiera rekord o danym Id:
						$this->record_object = $this->model_object->GetOne($id);
						
						// wyświetla formularz wypełniony danymi:
						$this->site_content = $this->view_object->ShowForm($this->record_object, $params['required'], $failed_fields, $this->data_import);

						// wyświetla komunikat:
						$this->site_message = array(
							'WARNING', 'Nie wypełniono wszystkich wymaganych pól. Proszę uzupełnić.'
						);
					}
				}
				else // odświeżono formularz
				{
					if (isset($_POST["save_button"])) // zapisz i kontynuuj
					{
						// pobiera rekord o danym Id:
						$this->record_object = $this->model_object->GetOne($id);
						
						// wyświetla formularz wypełniony danymi:
						$this->site_content = $this->view_object->ShowForm($this->record_object, $params['required'], $failed_fields, $this->data_import);
					}
					if (isset($_POST["update_button"])) // zapisz i zamknij
					{
						// ograniczenia dla usera:
						$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

						// pobiera listę rekordów:
						$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
						
						// aktualizuje statystykę listy:
						$this->navi_object->update($this->model_object, $this->list_params);

						// wyświetla listę rekordów:
						$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);
					}
					
					// wyświetla komunikat:
					$this->site_message = array(
						'WARNING', 'Formularz został już wysłany i nie należy go odświeżać.'
					);
				}
			}
			else if (isset($_POST["cancel_button"]))
			{
				// ograniczenia dla usera:
				$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

				// pobiera listę rekordów:
				$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
				
				// aktualizuje statystykę listy:
				$this->navi_object->update($this->model_object, $this->list_params);

				// wyświetla listę rekordów:
				$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);
			}
			else // formularz z danymi wejściowymi
			{
				// ograniczenia dla usera:
				$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;
				
				$id = $restrict_id ? $restrict_id : $id;

				// pobiera rekord o danym Id:
				$this->record_object = $this->model_object->GetOne($id);
				
				// sprawdza, czy rekord zawiera dane:
				if (!isset($this->record_object['id'])) $this->record_object = NULL;
				
				// wyświetla formularz wypełniony danymi:
				$this->site_content = $this->view_object->ShowForm($this->record_object, $params['required'], $failed_fields, $this->data_import);
			}
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// podgląd:
	
	public function View($id, $params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			// ograniczenia dla usera:
			$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;
			
			$id = $restrict_id ? $restrict_id : $id;
			
			// pobiera rekord o danym Id:
			$this->record_object = $this->model_object->GetOne($id);
			
			// sprawdza, czy rekord zawiera dane:
			if (!isset($this->record_object['id'])) $this->record_object = NULL;
			
			// wyświetla formularz wypełniony danymi:
			$this->site_content = $this->view_object->ShowRecord($this->record_object, $this->list_columns);
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// drzewo:
	
	public function Tree($id, $params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			// ograniczenia dla usera:
			$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;
			
			$id = $restrict_id ? $restrict_id : $id;
			
			// pobiera rekord o danym Id:
			$this->record_object = $this->model_object->GetOne($id);

			// pobiera listę rekordów o danym ParentId:
			$this->records_list = $this->model_object->GetTree($id);
			
			// wyświetla formularz wypełniony danymi:
			$this->site_content = $this->view_object->ShowTree($this->record_object, $this->records_list);
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// szczegóły:
	
	public function Details($id, $params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			// ograniczenia dla usera:
			$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;
			
			$id = $restrict_id ? $restrict_id : $id;
			
			// pobiera rekord o danym Id:
			$this->record_object = $this->model_object->GetDetails($id);
			
			// sprawdza, czy rekord zawiera dane:
			if (!isset($this->record_object['id'])) $this->record_object = NULL;
			
			// wyświetla formularz wypełniony danymi:
			$this->site_content = $this->view_object->ShowDetails($this->record_object, $this->list_columns);
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// pobieranie:
	
	public function Download($id, $params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			// pobiera plik o danym Id:
			$this->record_object = $this->model_object->Download($id);
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// podgląd:
	
	public function Preview($id, $params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			// ograniczenia dla usera:
			$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;
			
			$id = $restrict_id ? $restrict_id : $id;
			
			// pobiera rekord o danym Id:
			$this->record_object = $this->model_object->GetOne($id);
			
			// sprawdza, czy rekord zawiera dane:
			if (!isset($this->record_object['id'])) $this->record_object = NULL;
			
			// wyświetla formularz wypełniony danymi:
			$this->site_content = $this->view_object->PreviewRecord($this->record_object, $this->list_columns);
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// usuwanie:
	
	public function Delete($id, $params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			if (isset($_GET['confirm'])) // usuwanie zatwierdzone
			{
				// ograniczenia dla usera:
				$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

				$id = $restrict_id ? $restrict_id : $id;
				
				// usuwa rekord z bazy:
				$result = $this->model_object->Remove($id);

				if ($result) // zapis się powiódł
				{
					// wyświetla komunikat:
					$this->site_message = array(
						'INFORMATION', 'Bieżący rekord został poprawnie usunięty.'
					);
				}
				else // zapis się nie powiódł
				{
					// wyświetla komunikat:
					$this->site_message = array(
						'ERROR', 'Usuwanie rekordu się nie powiodło. Proszę spróbować ponownie.'
					);
				}
				
				// pobiera listę rekordów:
				$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
				
				// aktualizuje statystykę listy:
				$this->navi_object->update($this->model_object, $this->list_params);
			
				// wyświetla listę rekordów:
				$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);
			}
			else // przystąpienie do usuwania
			{
				$this->site_dialog = array(
					'QUESTION',
					'Usuwanie rekordu',
					'Uwaga! Rekord zostanie bezpowrotnie usunięty. <br />Czy na pewno chcesz usunąć rekord?',
					array(
						array(
							'index.php?route=' . MODULE_NAME . '&action=delete&id=' . $id . '&confirm=1', 'Tak'
						),
						array(
							'index.php?route=' . MODULE_NAME, 'Nie'
						)
					)
				);
			}
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// archiwizacja:
	
	public function Archive($id, $params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			// ograniczenia dla usera:
			$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

			$id = $restrict_id ? $restrict_id : $id;
			
			// kopiuje rekord z tabeli do tabeli:
			$result = $this->model_object->Archive($id);

			if ($result) // zapis się powiódł
			{
				// wyświetla komunikat:
				$this->site_message = array(
					'INFORMATION', 'Bieżący rekord został poprawnie zarchiwizowany.'
				);
			}
			else // zapis się nie powiódł
			{
				// wyświetla komunikat:
				$this->site_message = array(
					'WARNING', 'Kopia bieżącego rekordu już istnieje w archiwum.'
				);
			}
			
			// pobiera listę rekordów:
			$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
			
			// wyświetla listę rekordów:
			$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// przywracanie:
	
	public function Restore($id, $params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			// ograniczenia dla usera:
			$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

			$id = $restrict_id ? $restrict_id : $id;
			
			// przywraca rekord z tabeli do tabeli:
			$result = $this->model_object->Restore($id);

			if ($result) // zapis się powiódł
			{
				// wyświetla komunikat:
				$this->site_message = array(
					'INFORMATION', 'Bieżący rekord został poprawnie przywrócony.'
				);
			}
			else // zapis się nie powiódł
			{
				// wyświetla komunikat:
				$this->site_message = array(
					'WARNING', 'Przywrócony z archiwum rekord nie został zmieniony.'
				);
			}
			
			// pobiera listę rekordów:
			$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
			
			// wyświetla listę rekordów:
			$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// podgląd wersji:
	
	public function ShowPreview($id, $params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			// ograniczenia dla usera:
			$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;
			
			$id = $restrict_id ? $restrict_id : $id;
			
			// pobiera rekord o danym Id:
			$this->record_object = $this->model_object->GetArchiveContent($id);
			
			// wyświetla stronę w danej wersji:
			$this->site_content = $this->view_object->PreviewArchive($this->record_object);
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// lista:
	
	public function DrawList($params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			// ograniczenia dla usera:
			$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

			// filtrowanie według frazy:
			if (isset($_POST['ListSearchButton']))
			{
				include LIB_DIR . 'validator.php';
				
				$validator_object = new Validator();
				
				$input_check = ' validator ' . $_POST['ListSearchText'] . ' ';
				
				if ($validator_object->check_security($input_check))
				{
					$_SESSION['list_filter'] = htmlspecialchars(substr(trim($_POST['ListSearchText']), 0, 32));
				}
			}

			// usuwanie filtrowania:
			if (isset($_POST['ListSearchClose']))
			{
				$_SESSION['list_filter'] = NULL;
			}

			// pobiera listę rekordów:
			$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
			
			// aktualizuje statystykę listy:
			$this->navi_object->update($this->model_object, $this->list_params);
			
			// wyświetla listę rekordów:
			$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// lista znalezionych:
	
	public function FoundList($params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		$search_text = isset($params['search_text']) ? $params['search_text'] : NULL;

		$search_caption = empty($search_text) ? '(wszystkie)' : $search_text;
		
		$this->content_title .= ' - Wyniki dla: "' . $search_caption . '"';
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			// ograniczenia dla usera:
			$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

			// pobiera listę rekordów:
			$record_list = $this->model_object->Search($search_text, $restrict_id, $this->db_params);
			
			// aktualizuje statystykę listy:
			$this->navi_object->update($this->model_object, $this->list_params);

			// wyświetla listę rekordów:
			$this->site_content = $this->view_object->ShowFound($record_list, $this->list_columns, $this->list_params);
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// przesuwanie w górę:
	
	public function MoveUp($id, $params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			// modyfikuje rekord w bazie:
			$result = $this->model_object->MoveUp($id);

			if ($result) // zapis się powiódł
			{
				// wyświetla komunikat:
				$this->site_message = array(
					'INFORMATION', 'Rekord został poprawnie zaktualizowany.'
				);
			}
			else // zapis się nie powiódł
			{
				// wyświetla komunikat:
				$this->site_message = array(
					'ERROR', 'Zapis rekordu się nie powiódł. Proszę spróbować ponownie.'
				);
			}
			
			// ograniczenia dla usera:
			$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

			// pobiera listę rekordów:
			$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
			
			// wyświetla listę rekordów:
			$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}
	
	// przesuwanie w dół:
	
	public function MoveDown($id, $params, $access, $acl)
	{
		$this->content_title = $params['content_title'];
		
		if (in_array($this->user_status, $access) && $acl) // są uprawnienia
		{
			$this->content_options = $params['content_options'];
			
			// modyfikuje rekord w bazie:
			$result = $this->model_object->MoveDown($id);

			if ($result) // zapis się powiódł
			{
				// wyświetla komunikat:
				$this->site_message = array(
					'INFORMATION', 'Rekord został poprawnie zaktualizowany.'
				);
			}
			else // zapis się nie powiódł
			{
				// wyświetla komunikat:
				$this->site_message = array(
					'ERROR', 'Zapis rekordu się nie powiódł. Proszę spróbować ponownie.'
				);
			}
			
			// ograniczenia dla usera:
			$restrict_id = isset($params['restrict']) ? $params['restrict'] : NULL;

			// pobiera listę rekordów:
			$record_list = $this->model_object->GetAll($restrict_id, $this->db_params);
			
			// wyświetla listę rekordów:
			$this->site_content = $this->view_object->ShowList($record_list, $this->list_columns, $this->list_params);
		}
		else // brak uprawnień
		{
			$this->AccessDenied();
		}
	}	
}

?>