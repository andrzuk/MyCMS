<?php

/*
 * Klasa odpowiedzialna za tworzenie szczegółów - Generator Szczegółów
 */

class TreeBuilder
{
	private $title;
	private $image;
	private $width;
	
	private $module;
	
	private $row;
	private $rows;
	private $columns;
	
	private $content;
	
	function __construct()
	{
		$this->content = NULL;
	}
	
	public function init($view_title, $view_image, $view_width)
	{
		$this->image = $view_image;
		$this->title = $view_title;
		$this->width = $view_width;
	}
	
	public function set_module($module)
	{
		$this->module = $module;
	}
	
	public function set_row($row)
	{
		$this->row = $row;
	}
	
	public function set_rows($rows)
	{
		$this->rows = $rows;
	}
	
	public function set_columns($columns)
	{
		$this->columns = $columns;
	}
	
	public function build_view()
	{
		$main_text = NULL;
		
		$field_names = array();
		
		// kolumny pól:
		foreach ($this->columns as $key => $value)
		{
			foreach ($value as $k => $v)
			{
				if ($k == 'db_name') $db_name = $v;
				if ($k == 'column_name') $column_name = $v;
				if ($k == 'sorting') $sorting = $v;
			}
			if ($column_name)
				$field_names[] = array($db_name, $column_name);
		}
		
		$col_attrib = array(
			array('width' => '35%', 'align' => 'left'),
			array('width' => '65%', 'align' => 'left'),
		);
		
		$main_text .= '<table class="Table" width="'.$this->width.'" cellpadding="2" cellspacing="1" align="center">';

		$main_text .= '<tr>';
		$main_text .= '<th class="FormTitleBar" colspan="2">';
		$main_text .= '<span class="FormIcon">';
		$main_text .= '<img src="'.$this->image.'" alt="'.$this->title.'" />';
		$main_text .= '</span>';
		$main_text .= '<span class="FormTitle">';
		$main_text .= $this->title;
		$main_text .= '</span>';
		$main_text .= '</th>';
		$main_text .= '</tr>';
		
		foreach ($field_names as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				if ($key == 0) $db_name = $value;
				if ($key == 1) $column_name = $value;
			}

			$main_text .= '<tr>';
			$main_text .= '<td class="DataCell" width="' . $col_attrib[0]['width'] .
									'" style="text-align: ' . $col_attrib[0]['align'] .
									';">';
			$main_text .= $column_name .':';
			$main_text .= '</td>';
			$main_text .= '<td class="DataCell" width="' . $col_attrib[1]['width'] .
									'" style="text-align: ' . $col_attrib[1]['align'] .
									';">';
			if (is_array($this->row[$db_name])) // dane tablicowe
			{
				foreach ($this->row[$db_name] as $ik => $iv)
				{
					$main_text .= '<div>';
					$main_text .= $iv;
					$main_text .= '</div>';
				}
			}
			else // zwykłe dane
			{
				$main_text .= $this->row[$db_name];
			}
			$main_text .= '</td>';
			$main_text .= '</tr>';
		}
		
		$main_text .= '<tr>';
		$main_text .= '<td colspan="2" class="ButtonBar">';
		$main_text .= '<table cellpadding="0" cellspacing="0" align="right">';
		$main_text .= '<tr>';
		$main_text .= '<td>';
		$main_text .= '<form action="index.php?route=' . $this->module . '&action=edit&id=' . $this->row['id'] . '" method="post">';
		$main_text .= '<input type="submit" name="edit" value="Edytuj" class="Button" style="width: 80px;" />';
		$main_text .= '</form>';
		$main_text .= '</td>';
		$main_text .= '<td>';
		$main_text .= '<form action="index.php?route=' . $this->module . '" method="post">';
		$main_text .= '<input type="submit" name="cancel" value="Anuluj" class="Button" style="width: 80px;" />';
		$main_text .= '</form>';
		$main_text .= '</td>';
		$main_text .= '</tr>';
		$main_text .= '</table>';
		$main_text .= '</td>';
		$main_text .= '</tr>';
		
		$main_text .= '</table>';

		return $main_text;
	}
	
	private function GetChildren($id)
	{
		$this->content .= '<ol>';
		
		foreach ($this->rows as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				if ($key == 'id') $i_id = $value;
				if ($key == 'type') $i_type = $value;
				if ($key == 'parent_id') $i_parent = $value;
				if ($key == 'name') $i_name = $value;
			}
			if ($i_parent == $id)
			{
				$this->content .= '<li>' . $i_name . '</li>';
				if ($i_type == 'Folder')
				{
					$this->GetChildren($i_id);
				}
			}
		}
		
		$this->content .= '</ol>';
	}
	
	public function build_tree()
	{
		$main_text = NULL;
		
		$main_text .= '<table class="Table" width="'.$this->width.'" cellpadding="2" cellspacing="1" align="center">';

		$main_text .= '<tr>';
		$main_text .= '<th class="FormTitleBar" colspan="2">';
		$main_text .= '<span class="FormIcon">';
		$main_text .= '<img src="'.$this->image.'" alt="'.$this->title.'" />';
		$main_text .= '</span>';
		$main_text .= '<span class="FormTitle">';
		$main_text .= $this->title;
		$main_text .= '</span>';
		$main_text .= '</th>';
		$main_text .= '</tr>';
		
		$main_text .= '<tr>';
		$main_text .= '<td colspan="2" class="DataCell">';
		$main_text .= '<b>' . $this->row['disc_name'] . ' (' . $this->row['scan_date'] . ')' . '</b>';
		$main_text .= '</td>';
		$main_text .= '</tr>';

		$main_text .= '<tr>';
		$main_text .= '<td colspan="2" class="DataCell">';
		
		foreach ($this->rows[0] as $key => $value) if ($key == 'id') $i_id = $value;
		
		$this->GetChildren($i_id);
		
		$main_text .= $this->content;
		
		$main_text .= '</td>';
		$main_text .= '</tr>';
		
		$main_text .= '<tr>';
		$main_text .= '<td colspan="2" class="ButtonBar">';
		$main_text .= '<table cellpadding="0" cellspacing="0" align="right">';
		$main_text .= '<tr>';
		$main_text .= '<td>';
		$main_text .= '<form action="index.php?route=' . $this->module . '&action=edit&id=' . $this->row['id'] . '" method="post">';
		$main_text .= '<input type="submit" name="edit" value="Edytuj" class="Button" style="width: 80px;" />';
		$main_text .= '</form>';
		$main_text .= '</td>';
		$main_text .= '<td>';
		$main_text .= '<form action="index.php?route=' . $this->module . '" method="post">';
		$main_text .= '<input type="submit" name="cancel" value="Anuluj" class="Button" style="width: 80px;" />';
		$main_text .= '</form>';
		$main_text .= '</td>';
		$main_text .= '</tr>';
		$main_text .= '</table>';
		$main_text .= '</td>';
		$main_text .= '</tr>';
		
		$main_text .= '</table>';

		return $main_text;
	}
}

?>
