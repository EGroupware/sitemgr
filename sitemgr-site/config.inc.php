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

	/***********************************************************\
	* Edit the values in the following array to configure       *
	* the site generator.                                       *
	\***********************************************************/

	$sitemgr_info = array(
		// add trailing slash
		'phpgw_path'           => '../../',
		'htaccess_rewrite'         => False,
	);
	// uncomment the next line if sitemgr should use a eGW domain different from the first one defined in your header.inc.php
	// and of cause change the name accordingly ;-)
	//$GLOBALS['phpgw_info']['server']['default_domain'] = 'other';

	/***********************************************************\
	* Leave the rest of this file alone.                        *
	\***********************************************************/

		// do we use a different domain and are already loged in?
		if (isset($GLOBALS['phpgw_info']['server']['default_domain']) && (isset($_GET['domain']) || isset($_COOKIE['domain'])))
		{
			// force our default domain
			$_GET['domain'] = $GLOBALS['phpgw_info']['server']['default_domain'];
		}

		if (!file_exists($sitemgr_info['phpgw_path'] . 'header.inc.php'))
		{
			die("Header file not found.  Either your path to eGroupWare in the config.inc.php file is bad, or you have not setup eGroupWare.");
		}

		include($sitemgr_info['phpgw_path'] . 'header.inc.php');

		$GLOBALS['phpgw_info']['flags']['currentapp'] = 'login';
		include(PHPGW_SERVER_ROOT . '/phpgwapi/inc/functions.inc.php');
		$GLOBALS['phpgw_info']['flags']['currentapp'] = 'sitemgr-site';

		$site_url2 = $GLOBALS['phpgw']->db->db_addslashes(preg_replace('/\/[^\/]*$/','',$_SERVER['PHP_SELF'])) . '/';
		$site_url3 = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['SERVER_ADDR'] . $site_url2;
		$site_url  = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $site_url2;
		$GLOBALS['phpgw']->db->query("SELECT anonymous_user,anonymous_passwd FROM phpgw_sitemgr_sites WHERE site_url='$site_url' OR site_url='$site_url2' OR site_url='$site_url3'");
		if ($GLOBALS['phpgw']->db->next_record())
		{
			$anonymous_user = $GLOBALS['phpgw']->db->f('anonymous_user');
			$anonymous_passwd = $GLOBALS['phpgw']->db->f('anonymous_passwd');
		}
		else
		{
			die(lang('THERE IS NO WEBSITE CONFIGURED FOR URL %1.  NOTIFY THE ADMINISTRATOR.',$site_url));
		}
		//this is useful when you changed the API session class to not overgeneralize the session cookies
		if ($_GET['PHPSESSID'])
		{
			$GLOBALS['phpgw']->session->phpgw_setcookie('PHPSESSID',$_GET['PHPSESSID']);
		}


		if (! $GLOBALS['phpgw']->session->verify())
		{
			$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($anonymous_user,$anonymous_passwd, 'text');
			if (!$GLOBALS['sessionid'])
			{
				die(lang('NO ANONYMOUS USER ACCOUNTS INSTALLED.  NOTIFY THE ADMINISTRATOR.'));
				//exit;
			}
			//$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link($sitemgr_url . 'index.php'));
		}
		?>
