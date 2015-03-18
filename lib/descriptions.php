<?php

/*
 * Klasa odpowiedzialna za odczyt informacji o stronie
 */

class Descriptions
{
	private $setting;

	public function __construct($db)
	{
		$this->setting = new Settings($db);
	}

	public function get_description()
	{
		$description = $this->setting->get_config_key('main_description');
		return $description;
	}

	public function get_keywords()
	{
		$keywords = $this->setting->get_config_key('main_keywords');
		return $keywords;
	}

	public function get_title()
	{
		$title = $this->setting->get_config_key('main_title');
		return $title;
	}

	public function get_domain()
	{
		$domain = $this->setting->get_config_key('base_domain');

		$domain_prefix = 'http://';
		$domain_suffix = '/';

		if (stristr($domain, $domain_prefix) === FALSE)
		{
			$domain = $domain_prefix . $domain;
		}
		if (substr($domain, strlen($domain) - 1, 1) != $domain_suffix)
		{
			$domain .= $domain_suffix;
		}
		
		return $domain;
	}

	public function get_site_width()
	{
		$site_width = $this->setting->get_config_key('main_site_width');
		return $site_width;
	}

	public function get_menu_width()
	{
		$panel_width = $this->setting->get_config_key('menu_panel_width');
		return $panel_width;
	}

	public function get_content_width()
	{
		$panel_width = $this->setting->get_config_key('content_panel_width');
		return $panel_width;
	}

	public function get_author()
	{
		$author = $this->setting->get_config_key('main_author');
		return $author;
	}

	public function get_copyright()
	{
		$copyright = $this->setting->get_config_key('main_copyright');
		return $copyright;
	}

	public function get_classification()
	{
		$classification = $this->setting->get_config_key('main_classification');
		return $classification;
	}

	public function get_publisher()
	{
		$publisher = $this->setting->get_config_key('main_publisher');
		return $publisher;
	}

	public function get_topic()
	{
		$topic = $this->setting->get_config_key('main_page_topic');
		return $topic;
	}

	public function get_editor()
	{
		$editor = $this->setting->get_config_key('office_editor_location');
		return $editor;
	}
}

?>
