<?php

/*
 * Klasa odpowiedzialna za generowanie elementów strony (nagłówek, stopka, ...)
 */

class Elements
{
	private $db;
	private $site_header;
	private $site_footer;

	public function __construct($db)
	{
		$this->db = $db;
	}
	
	public function set_header()
	{
		$header = NULL;

		$setting = new Settings($this->db);
		
		$logo_image = $setting->get_config_key('logo_image');
		$logo_width = $setting->get_config_key('logo_width');
		$logo_height = $setting->get_config_key('logo_height');
		$page_title = $setting->get_config_key('page_title');
		$page_subtitle = $setting->get_config_key('page_subtitle');

		$logo_image = !empty($logo_image) ? $logo_image : PAGE_LOGO;
		$page_title = !empty($page_title) ? $page_title : PAGE_TITLE;
		$page_subtitle = !empty($page_subtitle) ? $page_subtitle : PAGE_SUBTITLE;

		$header .= '<table cellpadding="0" cellspacing="0">';
		$header .= '<tr>';
		$header .= '<td class="LogoImage" rowspan="2">';
		$header .= '<a href="index.php"><img src="' . $logo_image . '" width="'.$logo_width.'" height="'.$logo_height.'" class="Logo" alt="logo" /></a>';
		$header .= '</td>';
		$header .= '<td class="LogoTitle">' . $page_title . '</td>';
		$header .= '</tr>';
		$header .= '<tr>';
		$header .= '<td class="LogoSubTitle">' . $page_subtitle . '</td>';
		$header .= '</tr>';
		$header .= '</table>';
		
		$this->site_header = $header;
	}

	public function set_footer()
	{
		$footer = NULL;
		
		$setting = new Settings($this->db);
		
		$page_footer = $setting->get_config_key('page_footer');
		if (empty($page_footer)) $page_footer = 'Copyright &copy; {_year_} MyCMS';

		$footer .= '<table class="Footer" width="100%" cellpadding="0">';
		$footer .= '<tr>';
		$footer .= '<td width="100%" style="text-align: center">';
		$footer .= str_replace('{_year_}', date("Y"), $page_footer);
		$footer .= '</td>';
		$footer .= '</tr>';		
		$footer .= '</table>';
		
		$this->site_footer = $footer;
	}

	public function show_header()
	{
		return $this->site_header;
	}

	public function show_footer()
	{
		return $this->site_footer;
	}
}

?>
