<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		// currentapp set in config.inc.php
		'disable_template_class' => True,
		'currentapp' => 'sitemgr-site',
		'nosessionverify' => True,
		'noheader'   => True,
		'noappheader' => True,
		'noapi' => True,
		'nonavbar'   => True
	);

	if (file_exists('./config.inc.php'))
	{
		include('./config.inc.php');
	}
	else
	{
		die ("You need to copy config.inc.php.template to config.inc.php and edit the file before continuing.");
	}

	include('./functions.inc.php');

	$Common_BO = CreateObject('sitemgr.Common_BO');
	require_once './inc/class.sitebo.inc.php';
	$objbo = new sitebo;
	$Common_BO->sites->set_currentsite($site_url,$objbo->getmode());
	$sitemgr_info = array_merge($sitemgr_info,$Common_BO->sites->current_site);
	$sitemgr_info['sitelanguages'] = explode(',',$sitemgr_info['site_languages']);
	$objbo->setsitemgrPreferredLanguage();
	$GLOBALS['phpgw']->translation->add_app('common');	// as we run as sitemgr-site
	$GLOBALS['phpgw']->translation->add_app('sitemgr');	// as we run as sitemgr-site

	$templateroot = $GLOBALS['sitemgr_info']['site_dir'] . SEP . 'templates' . SEP . $GLOBALS['sitemgr_info']['themesel'];

	include_once './inc/class.Template3.inc.php';
	if (file_exists($templateroot.'/main.tpl'))			// native sitemgr template
	{
		include_once './inc/class.ui.inc.php';
	}
	elseif (file_exists($templateroot.'/index.php'))	// mambo open source template
	{
		include_once './inc/class.mos_ui.inc.php';
	}
	$objui = new ui;

	$page = CreateObject('sitemgr.Page_SO');

	$page_id = $_GET['page_id'];
	$page_name = $_GET['page_name'];
	$category_id = $_GET['category_id'];
	$toc = $_GET['toc'];
	$index = $_GET['index'];

	if ($page_name && $page_name != 'index.php')
	{
		$objui->displayPageByName($page_name);
	}
	elseif($category_id)
	{
		$objui->displayTOC($category_id);
	}
	elseif ($page_id)
	{
		$objui->displayPage($page_id);
	}
	elseif (isset($index))
	{
		$objui->displayIndex();
	}
	elseif (isset($toc))
	{
		$objui->displayTOC();
	}
	else
	{
		if ($sitemgr_info['home_page_id'])
		{
			$objui->displayPage($sitemgr_info['home_page_id']);
		}
		else
		{
			$index = 1; 
			$objui->displayIndex();
		}
	}
	if (DEBUG_TIMER)
	{
		$GLOBALS['debug_timer_stop'] = perfgetmicrotime();
		echo 'Page loaded in ' . ($GLOBALS['debug_timer_stop'] - $GLOBALS['debug_timer_start']) . ' seconds.';
	}
?>
