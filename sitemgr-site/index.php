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
	require_once('./config.inc.php');
	include './blockconfig.inc.php';

	include './inc/class.ui.inc.php';
	include './inc/class.bo.inc.php';
	include './inc/class.so.inc.php';
	include './inc/phpnuke.compat.inc.php';

	global $page_id;
	global $page_name;
	global $category_id;
	global $toc;
	global $index;

	$objui = new ui;
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
	elseif ($index)
	{
		$objui->displayIndex();
	}
	elseif ($toc)
	{
		$objui->displayTOC();
	}
	else
	{
		$objsp_so = CreateObject('sitemgr.sitePreference_SO');
		$home_page = $objsp_so->getPreference('home-page-id');
		if ($home_page)
		{
			$objui->displayPage($home_page);
		}
		else
		{
			$objui->displayIndex();
		}
	}
?>
