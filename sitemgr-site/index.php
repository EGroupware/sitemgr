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

	include('./config.inc.php');

	$site_id=0;
	/**
	 * Determine the site from the URL ($_SERVER['PHP_SELF'])
	 * 
	 * @param array &$anon_account anon account_info with keys 'user', 'passwd' and optional 'passwd_type'
	 * @return int site_id or dies if not site defined for the URL
	 */
	function sitemgr_get_site(&$anon_account)
	{
		global $site_url, $site_id, $sitemgr_info;

		$site_urls[] = $path = preg_replace('/\/[^\/]*$/','',$_SERVER['PHP_SELF']) . '/';
		$site_urls[] = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['SERVER_ADDR'] . $path;
		$site_urls[] = $site_url  = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $path;

		//echo "<p>sitemgr_get_site('$site_url')</p>\n";
		$GLOBALS['egw']->db->select('egw_sitemgr_sites','anonymous_user,anonymous_passwd,site_id',
			array('site_url' => $site_urls),__LINE__,__FILE__,false,'','sitemgr');

		if ($GLOBALS['egw']->db->next_record())
		{
			$anon_account = array(
				'login'  => $GLOBALS['egw']->db->f('anonymous_user'),
				'passwd' => $GLOBALS['egw']->db->f('anonymous_passwd'),
				'passwd_type' => 'text',
			);
			$sitemgr_info['anonymous_user'] = $anon_account['login'];
			
			if($GLOBALS['egw_info']['server']['allow_cookie_auth'])
			{
				$eGW_remember = unserialize(stripslashes($_COOKIE['eGW_remember']));
				if($eGW_remember['login'] && $eGW_remember['passwd'] && $eGW_remember['passwd_type'])
				{
					$anon_account = $eGW_remember;
				}
			}
			if (!$anon_account['login'])
			{
				die(lang('NO ANONYMOUS USER ACCOUNTS INSTALLED.  NOTIFY THE ADMINISTRATOR.'));
			}
			$site_id = $GLOBALS['egw']->db->f('site_id');
			//echo "<p>sitemgr_get_site('$site_url') site_id=$site_id, anon_account=".print_r($anon_account,true)."</p>\n";
			return $site_id;
		}
		die(lang('THERE IS NO WEBSITE CONFIGURED FOR URL %1.  NOTIFY THE ADMINISTRATOR.',$site_url));
	}

	$GLOBALS['egw_info'] = array(
		'flags' => array(
			'disable_Template_class' => True,
			'noheader'   => True,
			'currentapp' => 'sitemgr-link',
			'autocreate_session_callback' => 'sitemgr_get_site',
	));

	// do we use a different domain and are already loged in?
	if (isset($GLOBALS['egw_info']['server']['default_domain']) && (isset($_GET['domain']) || isset($_COOKIE['domain'])))
	{
		// force our default domain
		$_GET['domain'] = $GLOBALS['egw_info']['server']['default_domain'];
	}
	if (!file_exists($sitemgr_info['egw_path'] . 'header.inc.php'))
	{
		die("Header file not found.  Either your path to eGroupWare in the config.inc.php file is bad, or you have not setup eGroupWare.");
	}

	include($sitemgr_info['egw_path'] . 'header.inc.php');

	if (!$site_id)
	{
		sitemgr_get_site($anon_account);
	}
	else
	{	
		if($GLOBALS['egw_info']['server']['allow_cookie_auth'])
		{
			$eGW_remember = unserialize(stripslashes($_COOKIE['eGW_remember']));
			if($eGW_remember['login'] && $eGW_remember['passwd'] && $eGW_remember['passwd_type'])
			{
				// switch to current website. This is needed to let contributers work on currentsite
				$GLOBALS['egw_info']['user']['preferences']['sitemgr']['currentsite'] = $GLOBALS['egw']->db->f('site_id');
				$GLOBALS['egw']->preferences->change('sitemgr','currentsite', $GLOBALS['egw']->db->f('site_id'));
				$GLOBALS['egw']->preferences->save_repository(True);
			}
		}
	}
	if($GLOBALS['egw_info']['server']['usecookies'] && $_COOKIE['sessionid'] != $GLOBALS['egw_info']['user']['sessionid'])
	{
		// happens if eGW runs on cookies and sitemgr has to use an URL to forward the session to the other site/domain
		$GLOBALS['egw']->session->phpgw_setcookie('sessionid',$GLOBALS['egw_info']['user']['sessionid']);
		$GLOBALS['egw']->session->phpgw_setcookie('kp3',$GLOBALS['egw_info']['user']['kp3']);
		$GLOBALS['egw']->session->phpgw_setcookie('domain',$GLOBALS['egw_info']['user']['domain']);
	}
	include('./functions.inc.php');

	$Common_BO =& CreateObject('sitemgr.Common_BO');
	require_once './inc/class.sitebo.inc.php';
	$objbo =& new sitebo;
	$Common_BO->sites->set_currentsite($site_url,$objbo->getmode());
	if($objbo->getmode() != 'Production')
	{
		// we need this to avoid the "attempt to access ..." errors if users contribute to multiple websites.
		// This does not solve the Problem if they work simultanus in two browsers :-(
		$GLOBALS['egw']->preferences->change('sitemgr','currentsite', $Common_BO->sites->urltoid($site_url));
		$GLOBALS['egw']->preferences->save_repository(True);
	}
	$sitemgr_info = array_merge($sitemgr_info,$Common_BO->sites->current_site);
	$sitemgr_info['sitelanguages'] = explode(',',$sitemgr_info['site_languages']);
	$objbo->setsitemgrPreferredLanguage();
	$GLOBALS['egw']->translation->add_app('common');	// as we run as sitemgr-site
	$GLOBALS['egw']->translation->add_app('sitemgr');	// as we run as sitemgr-site

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
	if (!class_exists('ui'))
	{
		echo '<h3>'.lang("Invalid template directory '%1' !!!",$templateroot)."</h3>\n";
		if (!is_dir($GLOBALS['sitemgr_info']['site_dir'] . SEP . 'templates') || !is_readable($GLOBALS['sitemgr_info']['site_dir'] . SEP . 'templates'))
		{
			echo lang("The filesystem path to your sitemgr-site directory '%1' is probably wrong. Go to SiteMgr --> Define Websites and edit/fix the concerned Site.",$GLOBALS['sitemgr_info']['site_dir']);
		}
		elseif (!is_dir($templateroot) || !is_readable($templateroot))
		{
			echo lang("You may have deinstalled the used template '%1'. Reinstall it or go to SiteMgr --> Configure Website and select an other one.",$GLOBALS['sitemgr_info']['themesel']);
		}
		$GLOBALS['egw']->common->egw_exit();
	}
	$objui =& new ui;

	$page =& CreateObject('sitemgr.Page_SO');

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
		$cat = $Common_BO->cats->getCategory($category_id);
		if ($cat->index_page_id > 0)
		{
			$page = $Common_BO->pages->getPage($cat->index_page_id);
			if ($page->id)
			{
				$objui->displayPage($page->id);
			}
		}
		if (!$cat->index_page_id || !is_object($page) || !$page->id)	// fallback to regular toc if index-page is missing
		{
			$objui->displayTOC($category_id);
		}
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