<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - SiteMgr support for Mambo Open Source templates     *
	* http://www.egroupware.org                                                *
	* Written and copyright by RalfBecker@outdoor-training.de                  *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	function mosCountModules($contentarea)
	{
		global $objui;

		return (int)!!$objui->t->process_blocks($contentarea);
	}

	function mosLoadModules($contentarea)
	{
		global $objui;

		echo $objui->t->process_blocks($contentarea);
	}

	function mosLoadComponent($component)
	{
		return '';
	}

	function initEditor()
	{
	}

	function sefreltoabs($url)
	{
		echo $url;
	}
	
	function mosShowHead()
	{
		global $objui,$mosConfig_sitename;

		$objui->t->process_blocks('center');	// we need to render the center area now, to get all javascript included

		echo "\t\t<title>$mosConfig_sitename</title>\n";
		$objui->t->loadfile(realpath(dirname(__FILE__).'/../mos-compat/metadata.tpl'));
		echo $objui->t->parse();
	}

	// this is just to make some templates work, it does nothing actually atm.
	class mos_database
	{
		function setQuery($query)
		{
		}

		function loadObjectList()
		{
			return array();
		}
	}

	class ui
	{
		var $t;

		function ui()
		{
			$themesel = $GLOBALS['sitemgr_info']['themesel'];
			$this->templateroot = $GLOBALS['sitemgr_info']['site_dir'] . SEP . 'templates' . SEP . $themesel;
			$this->t =& new Template3($this->templateroot);
			$this->t->transformer_root = $this->mos_compat_dir = realpath(dirname(__FILE__).'/../mos-compat');
		}

		function displayPageByName($page_name)
		{
			global $objbo;
			global $page;
			$objbo->loadPage($GLOBALS['Common_BO']->pages->so->PageToID($page_name));
			$this->generatePage();
		}

		function displayPage($page_id)
		{
			global $objbo;
			$objbo->loadPage($page_id);
			$this->generatePage();
		}

		function displayIndex()
		{
			global $objbo;
			$objbo->loadIndex();
			$this->generatePage();
		}

		function displayTOC($categoryid=false)
		{
			global $objbo;
			$objbo->loadTOC($categoryid);
			$this->generatePage();
		}

		function generatePage()
		{
			global $database;
			$database =& new mos_database;

			// add a content-type header to overwrite an existing default charset in apache (AddDefaultCharset directiv)
			header('Content-type: text/html; charset='.$GLOBALS['egw']->translation->charset());

			// define global $mosConfig vars
			global $mosConfig_sitename,$mosConfig_live_site,$mosConfig_absolute_path,$mosConfig_offset,$cur_template;
			$mosConfig_sitename = $this->t->get_meta('sitename').': '.$this->t->get_meta('title');
			$mosConfig_live_site = substr($GLOBALS['sitemgr_info']['site_url'],0,-1);
			$mosConfig_offset = (int) $GLOBALS['egw_info']['user']['preferences']['common']['tz_offset'];
			$mosConfig_absolute_path = $GLOBALS['sitemgr_info']['site_dir'];
			$cur_template = $GLOBALS['sitemgr_info']['themesel'];
			define('_DATE_FORMAT_LC',str_replace(array('d','m','M','Y'),array('%d','%m','%b','%Y'),
				$GLOBALS['egw_info']['user']['preferences']['common']['dateformat']).
				($GLOBALS['egw_info']['user']['preferences']['common']['timeformat']=='12'?' %I:%M %p' : ' %H:%M'));
			define('_DATE_FORMAT',$GLOBALS['egw_info']['user']['preferences']['common']['dateformat'].
				($GLOBALS['egw_info']['user']['preferences']['common']['timeformat']=='12'?' h:i a' : ' H:i'));
			define('_SEARCH_BOX',lang('Search').' ...');
			define( '_ISO','charset='.$GLOBALS['egw']->translation->charset());
			define( '_VALID_MOS',True );
			define( '_VALID_MYCSSMENU',True );
			ini_set('include_path',$this->mos_compat_dir.(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? ';' : ':').ini_get('include_path'));

			ob_start();		// else some modules like the redirect wont work
			include($this->templateroot.'/index.php');
			ob_end_flush();
		}
	}
?>
