<?php

/*
 * Główny kontroler. Ładowany dla każdej strony.
 */

class Controller
{
	private $keywords;
	private $description;
	private $title;
	private $domain;
	private $site_width;
	private $menu_width;
	private $content_width;
	private $author;
	private $copyright;
	private $classification;
	private $publisher;
	private $page_topic;
	private $editor;

	public function load_info($db)
	{
		include LIB_DIR . 'descriptions.php';

		$descriptions = new Descriptions($db);
		
		$this->keywords = $descriptions->get_keywords();
		$this->description = $descriptions->get_description();
		$this->title = $descriptions->get_title();
		$this->domain = $descriptions->get_domain();
		$this->site_width = $descriptions->get_site_width();
		$this->menu_width = $descriptions->get_menu_width();
		$this->content_width = $descriptions->get_content_width();
		$this->author = $descriptions->get_author();
		$this->copyright = $descriptions->get_copyright();
		$this->classification = $descriptions->get_classification();
		$this->publisher = $descriptions->get_publisher();
		$this->page_topic = $descriptions->get_topic();
		$this->editor = $descriptions->get_editor();
	}

	public function get_keywords()
	{
		return $this->keywords;
	}

	public function get_description()
	{
		return $this->description;
	}

	public function get_title()
	{
		return $this->title;
	}

	public function get_domain()
	{
		return $this->domain;
	}
	
	public function get_site_width()
	{
		return $this->site_width;
	}

	public function get_menu_width()
	{
		return $this->menu_width;
	}

	public function get_content_width()
	{
		return $this->content_width;
	}

	public function get_author()
	{
		return $this->author;
	}

	public function get_copyright()
	{
		return $this->copyright;
	}

	public function get_classification()
	{
		return $this->classification;
	}

	public function get_publisher()
	{
		return $this->publisher;
	}
	
	public function get_topic()
	{
		return $this->page_topic;
	}
	
	public function get_editor()
	{
		return $this->editor;
	}
}

class PageController extends Controller
{
	private $db;
	private $links;
	private $path;
	private $navbar;
	private $menu;
	private $title;
	private $content;
	private $options;
	private $user;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function init($links, $path, $navbar, $menu)
	{
		$this->links = $links;
		$this->path = $path;
		$this->navbar = $navbar;
		$this->menu = $menu;

		parent::load_info($this->db);
	}

	public function set_content($title, $content, $options)
	{
		$this->title = $title;
		$this->content = $content;
		$this->options = $options;
	}
	
	public function set_user($user)
	{
		$this->user = $user;
	}

	public function add_content($content)
	{
		$this->content .= $content;
	}

	public function get_links()
	{
		$output = $this->links;

		return $output;
	}

	public function get_path()
	{
		$output = NULL;
		
		$output .= '<img src="img/home.png" class="SortIcon" alt="Home" /> &nbsp;';

		foreach ($this->path as $address => $caption) 
		{
			if (is_array($caption))
			{
				foreach ($caption as $key => $val)
				{
					if ($key == 'link') $item_link = $val;
					if ($key == 'caption') $item_caption = $val;
				}
				$output .= '<span class="PathItem">»</span>';
				$output .= '<span class="PathItem"><a href="'.$item_link.'">'.$item_caption.'</a></span>';
			}
			else
			{
				$output .= '<span class="PathItem">»</span>';
				$output .= '<span class="PathItem"><a href="'.$address.'">'.$caption.'</a></span>';
			}
		}
		
		return $output;
	}

	public function get_user()
	{
		$output = NULL;
		
		foreach ($this->user as $key => $value)
		{
			if ($key == 'user_id') $user_id = $value;
			if ($key == 'user_status') $user_status = $value;
			if ($key == 'user_imie') $user_imie = $value;
			if ($key == 'user_nazwisko') $user_nazwisko = $value;
		}
		
		if ($user_status)
		{
			$output .= 'Zalogowany: <b>' . $user_imie .' '. $user_nazwisko . '</b>&nbsp;-&nbsp;'.
				'<a href="index.php?route=logout" class="PathLink">'.
				'<img src="img/stop.png" class="TopLinkIcon" alt="Logout" />Wyloguj</a>';
		}
		else
		{
			$output .= 'Użytkownik nie zalogowany';
		}
		
		return $output;
	}

	public function get_menu()
	{
		return $this->menu;
	}

	public function get_navbar()
	{
		return $this->navbar;
	}

	public function get_content()
	{
		$output = NULL;
		
		$this->title .= '<span class="PageSignature">';
		
		if (is_array($this->options))
		{
			foreach ($this->options as $key => $val)
			{
				if (is_array($val))
				{
					foreach ($val as $k => $v)
					{
						if ($k == 'address') $address = $v;
						if ($k == 'caption') $caption = $v;
						if ($k == 'icon') $icon = $v;
					}				
					$this->title .= '<span class="PageAction">';
					$this->title .= '<a href="'.$address.'"><img src="'.$icon.'" class="IconSignature" />'.$caption.'</a>';
					$this->title .= '</span>';
					
					if ($caption == 'Zamknij') break;
				}
			}
		}
				
		$this->title .= '</span>';
		
		$output .= '<div class="WindowHeader">';
		$output .= $this->title;
		$output .= '</div>';

		$output .= $this->content;

		return $output;
	}
}

?>
