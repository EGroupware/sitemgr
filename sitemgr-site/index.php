<?php
	/**************************************************************************\
	* phpGroupWare - Web Content Manager                                       *
	* http://www.phpgroupware.org                                              *
	* -----------------------------------------------                          *
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
	require_once('./security.inc.php');

	if (file_exists('./config.inc.php'))
	{
		require_once('./config.inc.php');
	}
	else
	{
		die ("You need to copy config.inc.php.template to config.inc.php and edit the file before continuing.");
	}

	require_once('./functions.inc.php');

	$Common_BO = CreateObject('sitemgr.Common_BO',True);

	include './inc/class.ui.inc.php';
	include './inc/class.sitebo.inc.php';
	include './inc/class.Template3.inc.php';

	$objui = new ui;
	//I move the creation of the bo here, so that in the template we have access to it without creating it a second time
	$objbo = new sitebo;
	$objbo->setsitemgrPreferredLanguage();

	$page = CreateObject('sitemgr.Page_SO');

	$page_id = $_GET['page_id'];
	$page_name = $_GET['page_name'];
	$category_id = $_GET['category_id'];
	$toc = $_GET['toc'];
	$index = $_GET['index'];

	if ($page_name)
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
		$objsp_so = CreateObject('sitemgr.sitePreference_SO');
		$home_page = $objsp_so->getPreference('home-page-id');
		unset($objsp_so);
		if ($home_page)
		{
			$objui->displayPage($home_page);
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
