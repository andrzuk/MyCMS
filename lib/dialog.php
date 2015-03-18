<?php

/*
 * Klasa odpowiedzialna za obsługę okien dialogowych
 */

class Dialog
{
	private $dlg_type;
	private $dlg_title;
	private $dlg_text;
	private $dlg_buttons;
	private $path;

	function __construct($path)
	{
		$this->path = $path;
	}
	
	public function set_type($type)
	{
		$this->dlg_type = $type;
	}
	
	public function set_title($title)
	{
		$this->dlg_title = $title;
	}
	
	public function set_content($content)
	{
		$this->dlg_text = $content;
	}
	
	public function set_buttons($buttons)
	{
		$this->dlg_buttons = $buttons;
	}
	
	public function show_dialog_box($dialog)
	{
		$type = $dialog[0];
		$title = $dialog[1];
		$content = $dialog[2];
		$buttons = $dialog[3];
		
		$idx = 0;
		$main_dialog_body = NULL;
		
		switch ($type)
		{
			case 'ERROR':
				$class_name = 'MessageError';
				$icon_name = 'msg_error.png';
				break;
			case 'WARNING':
				$class_name = 'MessageWarning';
				$icon_name = 'msg_warning.png';
				break;
			case 'INFORMATION':
				$class_name = 'MessageInformation';
				$icon_name = 'msg_information.png';
				break;
			case 'QUESTION':
				$class_name = 'MessageQuestion';
				$icon_name = 'msg_question.png';
				break;
			default:
				$class_name = '';
				$icon_name = 'on_off.png';
				break;
		}

		$main_dialog_body .= '<p style="text-align: center;">';
		
		$main_dialog_body .= '<table width="400px" class="'.$class_name.'" align="center">';
		$main_dialog_body .= '<tr>';
		$main_dialog_body .= '<th class="MessageTitle" colspan="2">'.$title.'</th>';
		$main_dialog_body .= '</tr>';
		$main_dialog_body .= '<tr>';
		$main_dialog_body .= '<th class="" colspan="2"> &nbsp; </th>';
		$main_dialog_body .= '</tr>';
		$main_dialog_body .= '<tr>';
		$main_dialog_body .= '<td class="MessageIcon" style="width: 20%;"> <img src="'.$this->path.'img/msg/'.$icon_name.'" alt="ico" /> </td>';
		$main_dialog_body .= '<td class="MessageText" style="width: 80%;">'.$content.'</td>';
		$main_dialog_body .= '</tr>';
		$main_dialog_body .= '<tr>';
		$main_dialog_body .= '<td class="MessageSeparator" colspan="2"> &nbsp; </td>';
		$main_dialog_body .= '</tr>';
		$main_dialog_body .= '<tr>';
		$main_dialog_body .= '<td colspan="2" class="">';
		$main_dialog_body .= '<table cellpadding="0" cellspacing="0" align="right">';
		$main_dialog_body .= '<tr>';
		
		$max_len = 0;
		$link = NULL;
		$caption = NULL;
		$onclick = NULL;
		
		foreach ($buttons as $key => $value)
		{
			foreach ($value as $k => $v)
			{
				if ($k == 0) $link = $v;
				if ($k == 1) $caption = $v;
				if ($k == 2) $onclick = $v;
				$max_len = strlen($caption) > $max_len ? strlen($caption) : $max_len;
			}
			$button_width = 10 * $max_len;
			$button_width = $button_width > 80 ? $button_width : 80;
			$link = isset($link) ? $link : NULL;
			$caption = isset($caption) ? $caption : NULL;
			$onclick = isset($onclick) ? $onclick : NULL;
			$idx++;
			$main_dialog_body .= '<td>';
			$main_dialog_body .= '<form action="'.$link.'" method="post">';
			$main_dialog_body .= '&nbsp;';
			$main_dialog_body .= '<input type="submit" value="'.$caption.'" name="confirm_'.$idx.'" onClick="'.$onclick.'" style="width: '.$button_width.'px;" />';
			$main_dialog_body .= '</form>';
			$main_dialog_body .= '</td>';
		}
		
		$main_dialog_body .= '</tr>';
		$main_dialog_body .= '</table>';
		$main_dialog_body .= '</td>';
		$main_dialog_body .= '</tr>';
		$main_dialog_body .= '</table>';

		$main_dialog_body .= '</p>';
		
		return $main_dialog_body;
	}
}

?>
