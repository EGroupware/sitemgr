<?php 

class module_meta extends Module
{
	function module_meta()
	{
		$this->arguments = array(
			'get' => array(
				'type' => 'select', 
				'label' => 'The type of metainformation to display',
				'options' => array(
					'title' => 'Page title',
					'subtitle' => 'Page subtitle',
					'sitename' => 'Site name',
					'footer' => 'Site footer',
					'header' => 'Site header',
					'user' => 'User name'
				)
			)
		);
		$this->title = "Metainformation";
		$this->description = "This module provides meta information about the site and the current page";
	}

	function get_content(&$arguments,$properties)
	{
		global $page;

		switch ($arguments['get'])
		{
			case 'title':
				return $page->title;
			case 'subtitle':
				return $page->subtitle;
			case 'sitename':
				$prefs = CreateObject('sitemgr.sitePreference_SO');
				return $prefs->getPreference('sitemgr-site-name-' . $GLOBALS['phpgw_info']['user']['preferences']['common']['lang']);
			case 'footer':
				return $GLOBALS['Common_BO']->headerfooter->getsitefooter($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']);
			case 'header':
				return $GLOBALS['Common_BO']->headerfooter->getsiteheader($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']);
			case 'user':
				return $GLOBALS['phpgw_info']['user']['account_lid'];
		}
	}
}