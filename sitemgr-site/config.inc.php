<?php
	/***********************************************************\
	* Edit the values in the following array to configure       *
	* the site generator.                                       *
	\***********************************************************/
	$sitemgr_info = array(
		// add trailing slash
		'phpgw_path'           => '../../',
		'htaccess_404'         => 'disabled'
	);

	/***********************************************************\
	* Leave the rest of this file alone.                        *
	\***********************************************************/

		if (!file_exists($sitemgr_info['phpgw_path'] . 'header.inc.php'))
		{
			die("Header file not found.  Either your path to phpGroupWare in the config.inc.php file is bad, or you have not setup phpGroupWare.");
		}

		include($sitemgr_info['phpgw_path'] . 'header.inc.php');

		$GLOBALS['phpgw_info']['flags']['currentapp'] = 'login';
		include(PHPGW_SERVER_ROOT . '/phpgwapi/inc/functions.inc.php');
		$GLOBALS['phpgw_info']['flags']['currentapp'] = 'sitemgr-site';

		$pref = CreateObject('sitemgr.sitePreference_SO');
		$sitemgr_info = array_merge($sitemgr_info,$pref->getallprefs());
		unset($pref);
		$sitemgr_info['sitelanguages'] = explode(',',$sitemgr_info['sitelanguages']);

		//this is useful when you changed the API session class to not overgeneralize the session cookies
		if ($GLOBALS['HTTP_GET_VARS']['PHPSESSID'])
		{
			$GLOBALS['phpgw']->session->phpgw_setcookie('PHPSESSID',$GLOBALS['HTTP_GET_VARS']['PHPSESSID']);
		}


		if (! $GLOBALS['phpgw']->session->verify())
		{
			$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($sitemgr_info['anonymous-user'],$sitemgr_info['anonymous-passwd'], 'text');
			if (!$GLOBALS['sessionid'])
			{
				die(lang('NO ANONYMOUS USER ACCOUNTS INSTALLED.  NOTIFY THE ADMINISTRATOR.'));
				//exit;
			}
			//$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link($sitemgr_url . 'index.php'));
		}
		?>
