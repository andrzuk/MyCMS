<?php

/*
 * Klasa odpowiedzialna za wyświetlanie opcji kontekstowych (dla listy, widoku, edycji, ...)
 */

class Options
{
	private $module_name;
	private $item_id;

	public function __construct($module, $item)
	{
		$this->module_name = $module;
		$this->item_id = $item;
	}

	public function get_options($mode)
	{
		switch($mode)
		{
			case 'add':
				$content_options = array (
					array (
						'address' => 'index.php?route=' . $this->module_name,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png'
					),
				);
				break;
			
			case 'edit':
				$content_options = array (
					array (
						'address' => 'index.php?route=' . $this->module_name . '&action=view&id=' . $this->item_id,
						'caption' => 'Podgląd',
						'icon' => 'img/info.png'
					),
					array (
						'address' => 'index.php?route=' . $this->module_name . '&action=delete&id=' . $this->item_id,
						'caption' => 'Usuń',
						'icon' => 'img/trash.png'
					),
					array (
						'address' => 'index.php?route=' . $this->module_name,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png'
					)
				);
				break;

			case 'view':
				$content_options = array (
					array (
						'address' => 'index.php?route=' . $this->module_name . '&action=edit&id=' . $this->item_id,
						'caption' => 'Edytuj',
						'icon' => 'img/edit.png'
					),
					array (
						'address' => 'index.php?route=' . $this->module_name . '&action=delete&id=' . $this->item_id,
						'caption' => 'Usuń',
						'icon' => 'img/trash.png'
					),
					array (
						'address' => 'index.php?route=' . $this->module_name,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png'
					),
				);
				break;
			
			case 'preview':
				$content_options = array (
					array (
						'address' => 'index.php?route=' . $this->module_name . '&action=view&id=' . $this->item_id,
						'caption' => 'Podgląd',
						'icon' => 'img/info.png'
					),
					array (
						'address' => 'index.php?route=' . $this->module_name . '&action=edit&id=' . $this->item_id,
						'caption' => 'Edytuj',
						'icon' => 'img/edit.png'
					),
					array (
						'address' => 'index.php?route=' . $this->module_name . '&action=delete&id=' . $this->item_id,
						'caption' => 'Usuń',
						'icon' => 'img/trash.png'
					),
					array (
						'address' => 'index.php?route=' . $this->module_name,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png'
					),
				);
				break;
			
			case 'details':
				$content_options = array (
					array (
						'address' => 'index.php?route=' . $this->module_name,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png'
					),
				);
				break;
				
			case 'delete':
				$content_options = array (
					array (
						'address' => 'index.php?route=' . $this->module_name . '&action=view&id=' . $this->item_id,
						'caption' => 'Podgląd',
						'icon' => 'img/info.png'
					),
					array (
						'address' => 'index.php?route=' . $this->module_name . '&action=edit&id=' . $this->item_id,
						'caption' => 'Edytuj',
						'icon' => 'img/edit.png'
					),
					array (
						'address' => 'index.php?route=' . $this->module_name,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png'
					),
				);
				break;
			
			case 'list':
				$content_options = array (
					array (
						'address' => 'index.php?route=' . $this->module_name . '&action=add',
						'caption' => 'Nowy wpis',
						'icon' => 'img/category.png'
					),
					array (
						'address' => 'index.php?route=admin',
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png'
					),
				);
				break;
			
			case 'multi':
				$content_options = array (
					array (
						'address' => 'index.php?route=' . $this->module_name . '&action=add',
						'caption' => 'Nowy wpis',
						'icon' => 'img/category.png'
					),
					array (
						'address' => 'index.php?route=' . $this->module_name . '&action=add-multi',
						'caption' => 'Jednoczesne wpisy',
						'icon' => 'img/files.png'
					),
					array (
						'address' => 'index.php?route=admin',
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png'
					),
				);
				break;
			
			case 'simple':
				$content_options = array (
					array (
						'address' => 'index.php?route=admin',
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png'
					),
				);
				break;
			
			default:
				$content_options = array (
					array (
						'address' => 'index.php',
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png'
					),
				);
				break;
		}
		
		return $content_options;
	}
}

?>
