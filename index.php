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
			'currentapp' => 'sitemgr',
			'noheader'   => True,
			'nonavbar'   => True,
			'noapi'      => False
	);
	include('../header.inc.php');

	$CommonUI = CreateObject('sitemgr.Common_UI');
	$CommonUI->DisplayIFrame();

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
