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
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp' => 'sitemgr-link',
		'noheader'   => True,
		'nonavbar'   => True,
		'noapi'      => False
	);
	include('../header.inc.php');

	$pref_so = CreateObject('sitemgr.sitePreference_SO', True);
	$location = $pref_so->getPreference('sitemgr-gen-url');
	$dir = $pref_so->getPreference('sitemgr-gen-dir');
	if ($location && file_exists($dir . '/config.inc.php'))
	{
		require_once ($dir . '/config.inc.php');
		Header('Location: ' . sitemgr_link2('/index.php'));
		exit;
	}
	else
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		$aclbo = CreateObject('sitemgr.ACL_BO', True);
		echo '<table width="50%"><tr><td>';
		if ($aclbo->is_admin())
		{
			echo 'Before the public web site can be viewed, you must configure the various locations and preferences.  Please go to the sitemgr setup page by following this link: <a href="' . $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Common_UI.DisplayPrefs') . '">sitemgr setup page</a>.  Note that you may get this message if your preferences are incorrect.  For example, if config.inc.php is not found in the directory that you specified.';
		}
		else
		{
			echo 'Your administrator has not yet setup the web content manager for public viewing.  Go bug your administrator to get their butt in gear.';
		}
		echo '</td></tr></table>';
		$GLOBALS['phpgw']->common->phpgw_footer();
	}
?>
