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
		global $objui,$mos_content_cache;

		if (!isset($mos_content_cache[$contentarea]))
		{
			 $mos_content_cache[$contentarea] = $objui->t->process_blocks($contentarea);
		}
		return !empty($mos_content_cache[$contentarea]);
	}

	function mosLoadModules($contentarea)
	{
		global $objui,$mos_content_cache;

		if (!isset($mos_content_cache[$contentarea]))
		{
			 $mos_content_cache[$contentarea] = $objui->t->process_blocks($contentarea);
		}
		echo $mos_content_cache[$contentarea];
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
			$this->t = new Template3($this->templateroot);
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
			$database = new mos_database;

			// define global $mosConfig vars
			global $mosConfig_sitename,$mosConfig_live_site,$modConfig_offset;
			$mosConfig_sitename = $this->t->get_meta('sitename').': '.$this->t->get_meta('title');
			$mosConfig_live_site = $GLOBALS['sitemgr_info']['site_url'];
			$mosConfig_offset = (int) $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
			define('_DATE_FORMAT_LC',str_replace(array('d','m','M','Y'),array('%d','%m','%b','%Y'),
				$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']).
				($GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat']=='12'?' %I:%M %p' : ' %H:%M'));
			define('_SEARCH_BOX',lang('Search').' ...');
			define( '_ISO','charset='.$GLOBALS['phpgw']->translation->charset());
			define( '_VALID_MOS',True );
			ini_set('include_path',$this->mos_compat_dir.(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? ';' : ':').ini_get('include_path'));
			include($this->templateroot.'/index.php');
		}
	}
?>
