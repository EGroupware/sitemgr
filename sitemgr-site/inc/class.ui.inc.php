<?php
	/**************************************************************************\
	* phpGroupWare - Web Content Manager                                       *
	* http://www.phpgroupware.org                                              *
	* -------------------------------------------------                        *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/

	/* $Id$ */

	class ui
	{
		var $t;
		

		function ui()
		{
			$themesel = $GLOBALS['sitemgr_info']['themesel'];
			$templateroot = $GLOBALS['sitemgr_info']['sitemgr-site-dir'] . SEP . 'templates' . SEP . $themesel;
			$this->t = new Template3($templateroot);
		}

		function displayPageByName($page_name)
		{
			global $objbo;
			global $page;
			$page = $GLOBALS['Common_BO']->pages->pageso->getPageByName($page_name,$GLOBALS['phpgw_info']['user']['preferences']['common']['lang']);
			$objbo->loadPage($page->id);
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

		function get_news()
		{
			$bonews = CreateObject('news_admin.bonews');
			$news = $bonews->get_NewsList(0, false);
			unset($bonews);
			//$themesel = $GLOBALS['sitemgr_info']['themesel'];
			//require_once($GLOBALS['sitemgr_info']['sitemgr-site_path'] . 'themes/' . $themesel . '/theme.php');
			foreach($news as $newsitem)
			{   
				$var = Array(
					'subject'   => $newsitem['subject'],
					'submittedby'    => 'Submitted by ' . $GLOBALS['phpgw']->accounts->id2name($newsitem['submittedby']) . ' on ' . $GLOBALS['phpgw']->common->show_date($newsitem['submissiondate']),
					'content'   => nl2br($newsitem['content'])
				);
				return themearticle($aid, $informant, $var['submittedby'], $var['subject'], $var['content'], $topic, $topicname, $topicimage, $topictext);
				
			}
		}

		function generatePage()
		{
			echo $this->t->parse();
		}

	}
?>
