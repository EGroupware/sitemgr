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

	/**************************************************************\
	* Edit the values in the following array to configure SiteMgr, *
	* to run in a differen directory/URL as sitemgr/sitemgr-site.  *
	\**************************************************************/

	// If you are working on a copy of sitemgr-site, configure the following path
	$GLOBALS['sitemgr_info'] = array(
		// relative path between the sitemgr-site's index.php absolute file location and
		// eGroupware's source installation directory or
		// absolute path to eGroupware source installation directory
		// WITH a trailing slash
		'egw_path'         => '../../',
	);

	// If sitemgr should use a eGW domain different from the first one defined in your header.inc.php
	// the domain needs to be configured (e.g. your.egroupware.domain).

	// Option A:
	// If you are working on a copy of sitemgr-site, uncomment the following three lines
	// and replace the domain to the name configured in header.inc.php
	//$GLOBALS['egw_info']['user']['domain'] =
	//	$GLOBALS['egw_info']['server']['default_domain'] =
	// 'your.egroupware.domain';

	// Option B:
	// In case symbolic links are used to make the sitemgr-site available at the
	// location of the website (which keeps the sitemgr-instances in sync with updates),
	// the following code can be used to select an eGroupware domain
	// different from the first one defined in header.inc.php.
	//   eGroupware domain: your.egroupware.domain
	//   http path        : /cms
	//if (strstr("/cms/index.php", $_SERVER['PHP_SELF'])) {
	//	$GLOBALS['egw_info']['user']['domain'] =
	//		$GLOBALS['egw_info']['server']['default_domain'] =
	//		'your.egroupware.domain';
	//}

	// Option C:
	// If you use the apache web server (an either symbolic links or
	// a copy of the sitemgr-site directory), use a <Directory> directive to set the
	// variable EGW_SITEMGR_DOMAIN, see the htaccess next to this file.
	// Additionally, this approach can be used to enable search engine freindly URLs
	// in the same directive.
