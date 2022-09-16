<?php

define ('MODULE_NAME', 'roles');

$content_title = 'Role użytkowników';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Roles_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Roles_View($db);

$status = new Status($db);
$user_status = $status->get_value('user_status');

$list_columns = array(
	array('db_name' => 'user_id',		'column_name' => 'Id', 					'sorting' => 1),
	array('db_name' => 'user_login',	'column_name' => 'Login', 				'sorting' => 1),
	array('db_name' => 'user_name',		'column_name' => 'Imię i nazwisko',		'sorting' => 1),
	array('db_name' => 'status',		'column_name' => 'Grupa', 				'sorting' => 1),
	array('db_name' => 'function_id',	'column_name' => 'Dostępne funkcje',	'sorting' => 1),
);

include 'main/navi.php';

$navi_object = new Navi($db);

$navi_params = $navi_object->init($list_columns);

$record_object = $navi_params['record_object'];
$db_params = $navi_params['db_params'];
$list_params = $navi_params['list_params'];

$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;

// filtrowanie według frazy:
if (isset($_POST['ListSearchButton']))
{
	$_SESSION['list_filter'] = htmlspecialchars(substr(trim($_POST['ListSearchText']), 0, 32));
}

// usuwanie filtrowania:
if (isset($_POST['ListSearchClose']))
{
	$_SESSION['list_filter'] = NULL;
}

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

include APP_DIR . 'view/template/options.php';

$page_options = new Options(MODULE_NAME, $id);

$access = array(ADMIN);

$acl = new AccessControlList(MODULE_NAME, $db);
			
if (in_array($user_status, $access) && $acl->available()) // są uprawnienia
{
	if (isset($_GET['action'])) // add, view, edit, delete
	{
		switch ($_GET['action'])
		{
			// dodawanie:
			
			case 'add':
			{
				$content_options = $page_options->get_options('add');

				if (isset($_POST['update_button']) || isset($_POST['cancel_button'])) 
				{
					$content_options = isset($_SESSION['content_options']) ? $_SESSION['content_options'] : $page_options->get_options('add');
				}
				
				// dane z bazy potrzebne do kontrolek formularza:
				$data_import = array(
					'users' => $model_object->GetNewUsers(),
					'functions' => $model_object->GetFunctions(NULL),
				);

				if (isset($_POST['save_button']) || isset($_POST['update_button'])) // obsługa formularza
				{
					if (isset($_POST['user_id'])) // wybrano usera
					{
						$record_object['user_id'] = $_POST['user_id'];

						// usuwa poprzednią rolę:
						$model_object->Remove($record_object['user_id']);
						
						// tworzy nową rolę:
						foreach ($data_import as $i => $j)
						{
							if ($i == 'functions')
							{
								foreach ($j as $key => $value)
								{
									foreach ($value as $k => $v)
									{
										if ($k == 'function_id') $f_id = $v;
									}
									$record_object['function_id'] = $f_id;
									$record_object['access'] = isset($_POST['function_'.$f_id]) ? 1 : 0;

									// dopisuje dostęp do danej funkcji:
									$result = $model_object->Add($record_object);
								}
							}
						}
						if ($result != -1) // zapis się powiódł
						{
							if (isset($_POST["save_button"])) // zapisz i kontynuuj
							{
								// pobiera ostatnio dopisany rekord:
								$record_object = $model_object->GetLast();
								
								// dane dla formularza:
								$data_import = array(
									'users' => $model_object->GetAllUsers(),
									'functions' => $model_object->GetFunctions($record_object['user_id']),
								);
								// wyświetla formularz wypełniony danymi:
								$site_content = $view_object->ShowForm($record_object, NULL, NULL, $data_import);

								// wyświetla komunikat:
								$site_message = array(
									'SUCCESS', 'Szczegóły bieżącego rekordu zostały poprawnie zapisane.'
								);
							}
							if (isset($_POST["update_button"])) // zapisz i zamknij
							{
								// pobiera listę rekordów:
								$record_list = $model_object->GetAll(NULL, $db_params);
								
								// aktualizuje statystykę listy:
								$navi_object->update($model_object, $list_params);
								
								// wyświetla listę rekordów:
								$site_content = $view_object->ShowList($record_list, $list_columns, $list_params);

								// wyświetla komunikat:
								$site_message = array(
									'SUCCESS', 'Rekord został poprawnie dopisany do bazy.'
								);
							}
						}
						else // zapis się nie powiódł
						{
							// wyświetla pusty formularz:
							$site_content = $view_object->ShowForm(NULL, NULL, NULL, $data_import);
							
							// wyświetla komunikat:
							$site_message = array(
								'ERROR', 'Zapis rekordu się nie powiódł. Proszę spróbować ponownie.'
							);
						}
					}
					else // nie wybrano usera
					{
						$site_content = $view_object->ShowForm(NULL, NULL, NULL, $data_import);
						
						$site_message = array(
							'WARNING', 'Proszę wybrać użytkownika oraz odpowiednie dla niego uprawnienia.'
						);
					}
				}
				else if (isset($_POST['cancel_button'])) // obsługa formularza
				{
					$content_options = isset($_SESSION['content_options']) ? $_SESSION['content_options'] : $page_options->get_options('add');
					
					// pobiera listę rekordów:
					$record_list = $model_object->GetAll(NULL, $db_params);
					
					// aktualizuje statystykę listy:
					$navi_object->update($model_object, $list_params);
					
					// wyświetla listę rekordów:
					$site_content = $view_object->ShowList($record_list, $list_columns, $list_params);
				}
				else // pusty formularz
				{
					$site_content = $view_object->ShowForm(NULL, NULL, NULL, $data_import);
				}
			}
			break;

			// edycja:
			
			case 'edit':
			{
				$content_options = $page_options->get_options('edit');

				if (isset($_POST['update_button']) || isset($_POST['cancel_button'])) 
				{
					$content_options = isset($_SESSION['content_options']) ? $_SESSION['content_options'] : $page_options->get_options('edit');
				}
				
				// dane z bazy potrzebne do kontrolek formularza:
				$data_import = array(
					'users' => $model_object->GetAllUsers(),
					'functions' => $model_object->GetFunctions($id),
				);

				if (isset($_POST['save_button']) || isset($_POST['update_button'])) // obsługa formularza
				{
					if (isset($_POST['user_id'])) // wybrano usera
					{
						$record_object['user_id'] = $_POST['user_id'];

						// usuwa poprzednią rolę:
						$model_object->Remove($record_object['user_id']);
						
						// tworzy nową rolę:
						foreach ($data_import as $i => $j)
						{
							if ($i == 'functions')
							{
								foreach ($j as $key => $value)
								{
									foreach ($value as $k => $v)
									{
										if ($k == 'function_id') $f_id = $v;
									}
									$record_object['function_id'] = $f_id;
									$record_object['access'] = isset($_POST['function_'.$f_id]) ? 1 : 0;

									// dopisuje dostęp do danej funkcji:
									$result = $model_object->Add($record_object);
								}
							}
						}
						if ($result != -1) // zapis się powiódł
						{
							if (isset($_POST["save_button"])) // zapisz i kontynuuj
							{
								// pobiera ostatnio dopisany rekord:
								$record_object = $model_object->GetLast();
								
								// dane dla formularza:
								$data_import = array(
									'users' => $model_object->GetAllUsers(),
									'functions' => $model_object->GetFunctions($record_object['user_id']),
								);
								// wyświetla formularz wypełniony danymi:
								$site_content = $view_object->ShowForm($record_object, NULL, NULL, $data_import);

								// wyświetla komunikat:
								$site_message = array(
									'SUCCESS', 'Szczegóły bieżącego rekordu zostały poprawnie zapisane.'
								);
							}
							if (isset($_POST["update_button"])) // zapisz i zamknij
							{
								// pobiera listę rekordów:
								$record_list = $model_object->GetAll(NULL, $db_params);
								
								// aktualizuje statystykę listy:
								$navi_object->update($model_object, $list_params);
								
								// wyświetla listę rekordów:
								$site_content = $view_object->ShowList($record_list, $list_columns, $list_params);

								// wyświetla komunikat:
								$site_message = array(
									'SUCCESS', 'Rekord został poprawnie zaktualizowany.'
								);
							}
						}
						else // zapis się nie powiódł
						{
							$record_object = array('user_id' => $id);

							// wyświetla wypełniony formularz:
							$site_content = $view_object->ShowForm($record_object, NULL, NULL, $data_import);
							
							// wyświetla komunikat:
							$site_message = array(
								'ERROR', 'Zapis rekordu się nie powiódł. Proszę spróbować ponownie.'
							);
						}
					}
					else // nie wybrano usera
					{
						$record_object = array('user_id' => $id);

						// wyświetla wypełniony formularz:
						$site_content = $view_object->ShowForm($record_object, NULL, NULL, $data_import);
						
						// wyświetla komunikat:
						$site_message = array(
							'WARNING', 'Proszę wybrać użytkownika oraz odpowiednie dla niego uprawnienia.'
						);
					}
				}
				else if (isset($_POST['cancel_button'])) // obsługa formularza
				{
					$content_options = isset($_SESSION['content_options']) ? $_SESSION['content_options'] : $page_options->get_options('edit');
					
					// pobiera listę rekordów:
					$record_list = $model_object->GetAll(NULL, $db_params);
					
					// aktualizuje statystykę listy:
					$navi_object->update($model_object, $list_params);
					
					// wyświetla listę rekordów:
					$site_content = $view_object->ShowList($record_list, $list_columns, $list_params);
				}
				else // wypełniony formularz
				{
					$record_object = array('user_id' => $id);

					// wyświetla wypełniony formularz:
					$site_content = $view_object->ShowForm($record_object, NULL, NULL, $data_import);
				}
			}
			break;

			// podgląd:
			
			case 'view':
			{
				$content_options = $page_options->get_options('view');

				// pobiera listę dostępnych funkcji dla danego usera:
				$record_object = $model_object->GetOne($id);
				
				// wyświetla formularz wypełniony danymi:
				$site_content = $view_object->ShowRecord($record_object, $list_columns);
			}
			break;

			// usuwanie:
			
			case 'delete':
			{
				$content_options = $page_options->get_options('delete');

				if (isset($_GET['confirm'])) // usuwanie zatwierdzone
				{
					// usuwa rekord z bazy:
					$result = $model_object->Remove($id);

					if ($result != -1) // zapis się powiódł
					{
						// wyświetla komunikat:
						$site_message = array(
							'SUCCESS', 'Bieżący rekord został poprawnie usunięty.'
						);
					}
					else // zapis się nie powiódł
					{
						// wyświetla komunikat:
						$site_message = array(
							'ERROR', 'Usuwanie rekordu się nie powiodło. Proszę spróbować ponownie.'
						);
					}
					
					// pobiera listę rekordów:
					$record_list = $model_object->GetAll(NULL, $db_params);
					
					// aktualizuje statystykę listy:
					$navi_object->update($model_object, $list_params);
					
					// wyświetla listę rekordów:
					$site_content = $view_object->ShowList($record_list, $list_columns, $list_params);
				}
				else // przystąpienie do usuwania
				{
					$site_dialog = array(
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
			break;
		}
	}
	else // list of all
	{
		$list_options = $page_options->get_options('list');

		$function_options = array (
			array (
				'address' => 'index.php?route=functions',
				'caption' => 'Funkcje serwisu',
				'icon' => 'img/tree.png'
			),
		);

		$content_options = array_merge($function_options, $list_options);
		
		$_SESSION['content_options'] = $content_options;
		
		// pobiera listę rekordów:
		$record_list = $model_object->GetAll(NULL, $db_params);
		
		// aktualizuje statystykę listy:
		$navi_object->update($model_object, $list_params);
		
		// wyświetla listę rekordów:
		$site_content = $view_object->ShowList($record_list, $list_columns, $list_params);		
	}
}
else // brak uprawnień
{
	$content_options = $page_options->get_options(NULL);

	$site_dialog = array(
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

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'main/route.php';

?>
