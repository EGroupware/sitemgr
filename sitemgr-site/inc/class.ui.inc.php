<?php
	/***************************************************************************\
	* phpGroupWare - Web Content Manager                                        *
	* http://www.phpgroupware.org                                               *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id$ */

	class ui
	{
		var $bo;

		function ui()
		{
			$this->bo = new bo;
		}

		function displayPageByName($page_name)
		{
			$pages_so = CreateObject('sitemgr.Pages_SO');
			$page = $pages_so->getPageByName($page_name);
			$this->bo->loadPage($page->id);
			$this->generatePage();
		}

		function displayPage($page_id)
		{
			$this->bo->loadPage($page_id);
			$this->generatePage();
		}

		function displayIndex()
		{
			$this->bo->loadIndex();
			$this->generatePage();
		}

		function displayTOC($categoryid=false)
		{
			$this->bo->loadTOC($categoryid);
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
			/* Note: much of this func was taken from phpNuke -- it
			   is there template system so don't blame me for the mess */
			global $header,$foot1,$user,$sitename,$index;

			$index = 1;

			$themesel = $GLOBALS['sitemgr_info']['themesel'];
			if (file_exists($GLOBALS['sitemgr_info']['sitemgr-site_path'].'/themes/'.$themesel.'/theme.php'))
			{
				require_once($GLOBALS['sitemgr_info']['sitemgr-site_path'] . '/themes/' . $themesel . '/theme.php');
			}
			else
			{
				die("Selected theme '$themesel' does not exist.");
			}

			echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
			echo "\n<html>\n<head>\n<title>" . $this->bo->get_siteName() . ': ' . 
				$this->bo->get_title() . "</title>\n";
			add_theme_var('sitename',$this->bo->get_siteName());
			add_theme_var('user',$GLOBALS['phpgw_info']['user']['account_lid']);
			add_theme_var('header', $this->bo->get_header());
			add_theme_var('footer', $this->bo->get_footer());
			include $GLOBALS['sitemgr_info']['sitemgr-site_path'] . '/inc/meta.ui.inc.php';
			echo '<LINK REL="StyleSheet" HREF="themes/' . $themesel . 
				'/style/style.css" TYPE="text/css">' . "\n\n\n";
			echo '</head>' . "\n";
			themeheader();
			blocks('c');
			echo OpenTable();
			echo "<h1>" . $this->bo->get_title() . "</h1>";
			echo "<h3>" . $this->bo->get_subtitle() . "</h3>";
			echo "<p>".parse_theme_vars($this->bo->get_content())."</p>";
			echo CloseTable();
			themefooter();
			echo "</body></html>";
		}
	}
?>
