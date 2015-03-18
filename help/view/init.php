<?php

class Init_View
{
	public function __construct()
	{
	}
	
	/*
	 * Pomoc
	 */
	 
	public function ShowIntro($content)
	{
		$site_content = NULL;
		
		$site_content .= '<p class="Intro">';
		$site_content .= $content;
		$site_content .= '</p>';
		
		return $site_content;
	}
}

?>