<?php

/*
 * Klasa odpowiedzialna za tworzenie formularzy - Generator Formularzy
 */

class FormBuilder
{
	private $image;
	private $title;
	private $width;
	private $widths = Array();
	private $columns;
	private $action;
	private $enctype;
	private $inputs = Array();	
	private $hiddens = Array();	
	private $left_buttons = Array();
	private $right_buttons = Array();
	private $links = Array();
	private $required = Array();
	private $failed = Array();
	private $editor;
	
	function __construct()
	{
	}
	
	public function init($form_title, $form_image, $form_width, $form_widths)
	{
		$this->image = $form_image;
		$this->title = $form_title;
		$this->width = $form_width;
		$this->columns = 0;
		
		foreach ($form_widths as $k => $v)
		{
			$this->widths[] = $v;
			$this->columns++;
		}
	}
	
	public function set_action($form_address)
	{
		$this->action = $form_address;
	}

	public function set_enctype($form_enctype)
	{
		if (!empty($form_enctype))
		{
			$this->enctype = 'enctype="'.$form_enctype.'"';
		}
	}

	public function set_required($inputs_required)
	{
		$this->required = $inputs_required;
	}

	public function set_failed($inputs_failed)
	{
		$this->failed = $inputs_failed;
	}

	public function set_inputs($form_rows)
	{
		$this->inputs = Array();
		
		foreach ($form_rows as $k => $v)
		{
			$this->inputs[] = $v;
		}
	}
	
	public function set_hiddens($form_rows)
	{
		$this->hiddens = Array();
		
		foreach ($form_rows as $k => $v)
		{
			$this->hiddens[] = $v;
		}
	}
	
	public function set_buttons($form_submits, $buttons_position)
	{
		switch ($buttons_position)
		{
			case 'left':
				$this->left_buttons = Array();
				foreach ($form_submits as $k => $v) $this->left_buttons[] = $v;
				break;
			case 'right':
				$this->right_buttons = Array();
				foreach ($form_submits as $k => $v) $this->right_buttons[] = $v;
				break;
			default:
				$this->left_buttons = Array();
				$this->right_buttons = Array();
				foreach ($form_submits as $k => $v) $this->right_buttons[] = $v;
				break;
		}
	}
	
	public function set_links($form_links)
	{
		$this->links = Array();
		
		foreach ($form_links as $k => $v)
		{
			$this->links[] = $v;
		}
	}

	public function set_editor($form_editor)
	{
		$this->editor = $form_editor;
	}

	public function build_form()
	{
		$main_text = NULL;
		
		$this->failed = is_array($this->failed) ? $this->failed : Array();
		
		$main_text .= '<form action="'. $this->action .'" method="post" '.$this->enctype.' class="FormPanel">';
		$main_text .= '<table style="width: '. $this->width .';" class="Table" cellpadding="2" cellspacing="1" align="center">';

		$main_text .= '<tr>';
		$main_text .= '<th class="FormTitleBar" colspan="'. $this->columns .'">';
		$main_text .= '<span class="FormIcon">';
		$main_text .= '<img src="'.$this->image.'" width="32" height="32" alt="" />';
		$main_text .= '</span>';
		$main_text .= '<span class="FormTitle">';
		$main_text .= $this->title;
		$main_text .= '</span>';
		$main_text .= '</th>';
		$main_text .= '</tr>';
		
		$main_text .= '<tr>';
		foreach ($this->widths as $k => $v) $main_text .= '<th class="" style="width: '.$v.';"></th>';		
		$main_text .= '</tr>';
		
		foreach ($this->inputs as $k => $v)
		{
			$main_text .= '<tr class="FormRow">';
			foreach ($v as $key => $value)
			{
				if ($key == 'caption')
				{
					$main_text .= '<td class="FormCell"> '. $value .': </td>';
				}
				else if ($key == 'data' || $key == 'caption_data')
				{
					switch ($key)
					{
						case 'data':
							$main_text .= '<td class="FormCell">';						
							break;
						case 'caption_data':
							$main_text .= '<td colspan="'. $this->columns .'" class="FormCell">';						
							break;
					}
					foreach ($value as $arr_key => $arr_val)
					{
						if ($arr_val['type'] == 'radio')
						{
							$main_text .= '<input ';
							foreach ($arr_val as $atr_key => $atr_value)
							{
								if ($atr_key == 'type') $main_text .= 'type="'.$atr_value.'" ';
								if ($atr_key == 'id') { $main_text .= 'id="'.$atr_value.'" '; $input_id = $atr_value; }
								if ($atr_key == 'name') { $main_text .= 'name="'.$atr_value.'" '; $input_name = $atr_value; }
								if ($atr_key == 'value') $main_text .= 'value="'.$atr_value.'" ';
								if ($atr_key == 'checked') $main_text .= 'checked="'.$atr_value.'" ';
								if ($atr_key == 'onclick') $main_text .= 'onclick="'.$atr_value.'" ';
								if ($atr_key == 'style') $main_text .= 'style="'.$atr_value.'" ';
							}
							$main_text .= 'class="" />';
							$main_text .= '&nbsp;<label for="'.$input_id.'">'. $arr_val['caption'] .'</label> &nbsp; ';
						}
						else if ($arr_val['type'] == 'checkbox')
						{
							$main_text .= '<input ';
							foreach ($arr_val as $atr_key => $atr_value)
							{
								if ($atr_key == 'type') $main_text .= 'type="'.$atr_value.'" ';
								if ($atr_key == 'id') { $main_text .= 'id="'.$atr_value.'" '; $input_id = $atr_value; }
								if ($atr_key == 'name') { $main_text .= 'name="'.$atr_value.'" '; $input_name = $atr_value; }
								if ($atr_key == 'checked') $main_text .= 'checked="'.$atr_value.'" ';
								if ($atr_key == 'onclick') $main_text .= 'onclick="'.$atr_value.'" ';
								if ($atr_key == 'style') $main_text .= 'style="'.$atr_value.'" ';
							}
							$main_text .= 'class="" />';
							$main_text .= '&nbsp;<label for="'.$input_id.'">'. $arr_val['caption'] .'</label> &nbsp; ';
						}	
						else if ($arr_val['type'] == 'textarea')
						{
							$input_name = NULL;
							$indicate = ' border: 1px solid #c00;';
							if ($this->editor === 'true') // CkEditor włączony
							{
								$main_text .= '</td>';
								$main_text .= '</tr>';
								$main_text .= '<tr class="FormRow">';
								$main_text .= '<td class="FormCell" colspan="'. $this->columns .'">';
							}
							$main_text .= '<textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" ';
							foreach ($arr_val as $atr_key => $atr_value)
							{
								if ($atr_key == 'id') $main_text .= 'id="'.$atr_value.'" ';
								if ($atr_key == 'name') { $main_text .= 'name="'.$atr_value.'" '; $input_name = $atr_value; }
								if ($atr_key == 'style') $main_text .= 'style="'.$atr_value.(in_array($input_name, $this->failed) ? $indicate : NULL).'" ';
								if ($atr_key == 'readonly') $main_text .= 'readonly="'.$atr_value.'" ';
								if ($atr_key == 'onclick') $main_text .= 'onclick="'.$atr_value.'" ';
							}
							if ($this->editor === 'true') // CkEditor włączony
								$main_text .= 'class="ckeditor">';
							else // tryb HTML
								$main_text .= 'class="FormInput">';
							$main_text .= $arr_val['value'];
							$main_text .= '</textarea>';
							if (isset($this->required))
								foreach ($this->required as $req_key => $req_val)
									if ($req_val == $input_name)
										$main_text .= '<a style="color: #f00;">*</a>';
						}	
						else if ($arr_val['type'] == 'select')
						{
							$indicate = ' border: 1px solid #c00;';
							$main_text .= '<select ';
							foreach ($arr_val as $atr_key => $atr_value)
							{
								if ($atr_key == 'id') $main_text .= 'id="'.$atr_value.'" ';
								if ($atr_key == 'name') { $main_text .= 'name="'.$atr_value.'" '; $input_name = $atr_value; }
								if ($atr_key == 'multiple') $main_text .= 'multiple="'.$atr_value.'" ';
								if ($atr_key == 'size') $main_text .= 'size="'.$atr_value.'" ';
								if ($atr_key == 'style') $main_text .= 'style="'.$atr_value.(in_array($input_name, $this->failed) ? $indicate : NULL).'" ';
							}
							$main_text .= 'class="FormComboBox">';
							foreach ($arr_val['option'] as $opt_key => $opt_value)
							{
								$main_text .= '<option ';
								foreach ($opt_value as $o_key => $o_value)
								{
									if ($o_key == 'value') $main_text .= 'value="'.$o_value.'" ';
									if ($o_key == 'selected') $main_text .= 'selected="'.$o_value.'" ';
								}
								$main_text .= '>'.$opt_value['caption'].'</option>';
							}
							$main_text .= '</select>';
							if (isset($this->required))
								foreach ($this->required as $req_key => $req_val)
									if ($req_val == $input_name)
										$main_text .= '<a style="color: #f00;">*</a>';
							$main_text .= '&nbsp; '. $arr_val['description'];
						}	
						else if ($arr_val['type'] == 'label')
						{
							$main_text .= '<span style="'.$arr_val['style'].'">';
							$main_text .= $arr_val['value'];
							$main_text .= '</span>';
						}
						else if ($arr_val['type'] == 'file')
						{
							$indicate = ' border: 1px solid #c00;';
							$main_text .= '<input ';
							foreach ($arr_val as $atr_key => $atr_value)
							{
								if ($atr_key == 'type') $main_text .= 'type="'.$atr_value.'" ';
								if ($atr_key == 'id') $main_text .= 'id="'.$atr_value.'" ';
								if ($atr_key == 'name') { $main_text .= 'name="'.$atr_value.'" '; $input_name = $atr_value; }
								if ($atr_key == 'size') $main_text .= 'size="'.$atr_value.'" ';
								if ($atr_key == 'multiple') $main_text .= 'multiple="'.$atr_value.'" ';
								if ($atr_key == 'onChange') $main_text .= 'onChange="'.$atr_value.'" ';
								if ($atr_key == 'style') $main_text .= 'style="'.$atr_value.(in_array($input_name, $this->failed) ? $indicate : NULL).'" ';
							}
							$main_text .= 'class="FormInput" />';
						}	
						else if ($arr_val['type'] == 'submit')
						{
							$main_text .= '<input ';
							foreach ($arr_val as $atr_key => $atr_value)
							{
								if ($atr_key == 'type') $main_text .= 'type="'.$atr_value.'" ';
								if ($atr_key == 'id') $main_text .= 'id="'.$atr_value.'" ';
								if ($atr_key == 'name') $main_text .= 'name="'.$atr_value.'" ';
								if ($atr_key == 'value') $main_text .= 'value="'.$atr_value.'" ';
								if ($atr_key == 'style') $main_text .= 'style="'.$atr_value.'" ';
							}
							$main_text .= ' />';
						}	
						else // głównie: type == 'text'
						{
							$input_name = NULL;							
							$indicate = ' border: 1px solid #c00;';
							$main_text .= $arr_val['caption'];
							$main_text .= '<input ';
							foreach ($arr_val as $atr_key => $atr_value)
							{
								if ($atr_key == 'type') $main_text .= 'type="'.$atr_value.'" ';
								if ($atr_key == 'id') $main_text .= 'id="'.$atr_value.'" ';
								if ($atr_key == 'name') { $main_text .= 'name="'.$atr_value.'" '; $input_name = $atr_value; }
								if ($atr_key == 'value') $main_text .= 'value="'.$atr_value.'" ';
								if ($atr_key == 'onclick') $main_text .= 'onclick="'.$atr_value.'" ';
								if ($atr_key == 'style') $main_text .= 'style="'.$atr_value.(in_array($input_name, $this->failed) ? $indicate : NULL).'" ';
							}
							$main_text .= 'class="FormInput" />';
							if (isset($this->required))
								foreach ($this->required as $req_key => $req_val)
									if ($req_val == $input_name)
										$main_text .= '<a style="color: #f00;">*</a>';
						}
					}
					$main_text .= '</td>';
				}
				else
				{
					$main_text .= '<td class="TbDisabled"> '. $value .'</td>';
				}
			}
			$main_text .= '</tr>';
		}
		
		foreach ($this->hiddens as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				$main_text .= '<input ';
				foreach ($value as $atr_key => $atr_value)
				{
					if ($atr_key == 'type') $main_text .= 'type="'.$atr_value.'" ';
					if ($atr_key == 'id') $main_text .= 'id="'.$atr_value.'" ';
					if ($atr_key == 'name') $main_text .= 'name="'.$atr_value.'" ';
					if ($atr_key == 'value') $main_text .= 'value="'.$atr_value.'" ';
				}
				$main_text .= '/>';
			}
		}

		// zabezpieczenie przed odświeżeniem formularza:		
		$main_text .= '<input type="hidden" id="form_hash" name="form_hash" value="'. md5(time()) .'" />';

		// sekcja przycisków:
		$main_text .= '<tr>';
		$main_text .= '<td colspan="'. $this->columns .'" class="ButtonBar">';
		$main_text .= '<table width="55%" cellpadding="5" cellspacing="0" align="left">';
		$main_text .= '<tr>';
		$main_text .= '<td style="text-align: left;">';
		foreach ($this->left_buttons as $k => $v)
		{
			$main_text .= ' <input ';
			foreach ($v as $atr_key => $atr_value)
			{
				if ($atr_key == 'type') $main_text .= 'type="'.$atr_value.'" ';
				if ($atr_key == 'id') $main_text .= 'id="'.$atr_value.'" ';
				if ($atr_key == 'name') $main_text .= 'name="'.$atr_value.'" ';
				if ($atr_key == 'value') $main_text .= 'value="'.$atr_value.'" ';
				if ($atr_key == 'onclick') $main_text .= 'onclick="'.$atr_value.'" ';
				if ($atr_key == 'style') $main_text .= 'style="'.$atr_value.'" ';
			}
			$main_text .= 'class="Button" />';
		}
		$main_text .= '</td>';
		$main_text .= '</tr>';
		$main_text .= '</table>';
		$main_text .= '<table width="45%" cellpadding="5" cellspacing="0" align="right">';
		$main_text .= '<tr>';
		$main_text .= '<td style="text-align: right;">';
		foreach ($this->right_buttons as $k => $v)
		{
			$main_text .= ' <input ';
			foreach ($v as $atr_key => $atr_value)
			{
				if ($atr_key == 'type') $main_text .= 'type="'.$atr_value.'" ';
				if ($atr_key == 'id') $main_text .= 'id="'.$atr_value.'" ';
				if ($atr_key == 'name') $main_text .= 'name="'.$atr_value.'" ';
				if ($atr_key == 'value') $main_text .= 'value="'.$atr_value.'" ';
				if ($atr_key == 'onclick') $main_text .= 'onclick="'.$atr_value.'" ';
				if ($atr_key == 'style') $main_text .= 'style="'.$atr_value.'" ';
			}
			$main_text .= 'class="Button" />';
		}
		$main_text .= '</td>';
		$main_text .= '</tr>';
		$main_text .= '</table>';
		$main_text .= '</td>';
		$main_text .= '</tr>';

		foreach ($this->links as $k => $v)
		{
			$main_text .= '<tr>';
			$main_text .= '<td colspan="'. $this->columns .'" class="DataLink" style="text-align: center;">';
			foreach ($v as $link_key => $link_value)
			{
				if ($link_key == 'address') $link_address = $link_value;
				if ($link_key == 'caption') $link_caption = $link_value;
			}
			$main_text .= '<a href="'.$link_address.'" class="FormLinkSeparated">'.$link_caption.'</a>';
			$main_text .= '</td>';
			$main_text .= '</tr>';
		}

		$main_text .= '</table>';
		$main_text .= '</form>';
		
		return $main_text;
	}
}

?>
