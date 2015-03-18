<?php

/*
 * Klasa odpowiedzialna za generowanie komunikatów
 */

class Message
{
	private $msg_type;
	private $msg_text;
	private $path;

	function __construct($path)
	{
		$this->path = $path;
	}
	
	public function set_type($type)
	{
		$this->msg_type = $type;
	}
	
	public function set_content($content)
	{
		$this->msg_text = $content;
	}
	
	public function show_message_box($message)
	{
		$type = $message[0];
		$content = $message[1];
		
		$main_message_body = NULL;
		
		switch ($type)
		{
			case 'ERROR':
				$class_name = 'MessageBoxError';
				$icon_name = 'msg_error.png';
				break;
			case 'WARNING':
				$class_name = 'MessageBoxWarning';
				$icon_name = 'msg_warning.png';
				break;
			case 'INFORMATION':
				$class_name = 'MessageBoxInformation';
				$icon_name = 'msg_information.png';
				break;
			case 'QUESTION':
				$class_name = 'MessageBoxQuestion';
				$icon_name = 'msg_question.png';
				break;
			default:
				$class_name = '';
				$icon_name = 'on_off.png';
				break;
		}

		$main_message_body .= '<p class="Message">';

		$main_message_body .= '<table class="'. $class_name .'" width="100%" cellspacing="0" cellpadding="0" align="left">';
		$main_message_body .= '<tr>';
		$main_message_body .= '<td class="MsgIcon" width="50">';
		$main_message_body .= '<img src="'. $this->path .'img/msg/'. $icon_name .'" alt="" width="48" height="48" />';
		$main_message_body .= '</td>';
		$main_message_body .= '<td class="MsgMessage" style="text-align: center;">';
		$main_message_body .= $content;
		$main_message_body .= '</td>';
		$main_message_body .= '</tr>';
		$main_message_body .= '</table>';

		$main_message_body .= '</p>';

		return $main_message_body;
	}
}

?>
