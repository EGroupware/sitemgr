<?php
	/*************************************************************************\
	* phpGroupWare - SiteMgr Preferences						                  *
	* http://www.phpgroupware.org											  *
	* --------------------------------------------							  *
	* This program is free software; you can redistribute it and/or modify it *
	* under the terms of the GNU General Public License as published by the   *
	* Free Software Foundation; either version 2 of the License, or (at your  *
	* option) any later version.											  *
	\*************************************************************************/
	/* $Id$ */

	{
// Only Modify the $file and $title variables.....

		$title = 'Web Content Manager';
		$file = Array
		(
			'Site Setup'     => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Common_UI.DisplayPrefs'),
			'Edit Categories and Permissions'    => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Admin_ManageCategories_UI._manageCategories'),
			'Edit Site Header and Footer' => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.admin_ManageSiteContent_UI._editHeaderAndFooter'),
			'Edit Individual Pages' => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.contributor_ManagePage_UI._managePage')
		);

//Do not modify below this line
		display_section($appname,$title,$file);
	}
?>
