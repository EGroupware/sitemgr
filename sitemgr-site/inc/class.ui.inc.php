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
		var $bo;
		var $t;
		var $blocks_bo;
		

		function ui()
		{
			$this->bo = new bo;
			$this->t = new Template2;

			$this->bo->setsitemgrPreferredLanguage();
			
			require_once($GLOBALS['sitemgr_info']['sitemgr-site_path'] .
				'/inc/class.blocks_bo.inc.php');
			$this->blocks_bo = new blocks_bo;
			
			
		}

		function displayPageByName($page_name)
		{
			$pages_so = CreateObject('sitemgr.Pages_SO');
			$page = $pages_so->getPageByName($page_name,$GLOBALS['phpgw_info']['user']['preferences']['common']['lang']);
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
			if ($GLOBALS['sitemgr_info']['usethemes'])
			{
				$this->generatePageTheme();
			}
			else
			{
				$this->generatePageTemplate();
			}
		}

		function generatePageTheme()
		{
			/* Note: much of this func was taken from phpNuke -- it
			   is their template system so don't blame me for the mess */
			global $header,$foot1,$user,$sitename,$index;

			require_once('./inc/phpnuke.compat.inc.php');
			$index = 1;

			$themesel = $GLOBALS['sitemgr_info']['themesel'];
			if (file_exists($GLOBALS['sitemgr_info']['sitemgr-site_path'].'/themes/'.$themesel.'/theme.php'))
			{
				require_once($GLOBALS['sitemgr_info']['sitemgr-site_path'] . '/themes/' . $themesel . '/theme.php');
			}
			else
			{
				die(lang("Selected theme %1 does not exist.",$themesel));
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
			$this->blocks_bo->blocks('c',$this->t);
			echo OpenTable();
			echo "<h1>" . $this->bo->get_title() . "</h1>";
			echo "<h3>" . $this->bo->get_subtitle() . "</h3>";
			echo "<p>".parse_theme_vars($this->bo->get_content())."</p>";
			echo CloseTable();
			themefooter();
			echo "</body></html>";
		}

		function generatePageTemplate()
		{
			$themesel = $GLOBALS['sitemgr_info']['themesel'];
			$templatedir = $GLOBALS['sitemgr_info']['sitemgr-site_path'].'/templates/';
			if (!file_exists($templatedir.$themesel.'/main.tpl'))
			{
				die(lang("Selected template %1 does not exist.",$themesel));
			}
			$this->t->set_root($templatedir);
			$this->t->set_unknowns('keep');

			$this->t->set_file('header','header.tpl');
			$this->t->set_var('themesel',$themesel);
			$this->t->set_var('site_name',$this->bo->get_siteName());
			$this->t->set_var('page_title',$this->bo->get_title());
			$this->t->pfp('out','header');
			$this->t->set_file('body',$themesel.'/main.tpl');

			$this->t->set_var('opencurly','{');
			$this->t->set_var('closecurly','}');
			$this->t->set_var('user', 
				$GLOBALS['phpgw_info']['user']['account_lid']);
			$this->t->set_var('site_name',$this->bo->get_siteName());
			$this->t->set_var('site_header', $this->bo->get_header());
			$this->t->set_var('site_footer', $this->bo->get_footer());
			$this->t->set_var('page_title', $this->bo->get_title());
			$this->t->set_var('page_subtitle', $this->bo->get_subtitle());
			$this->t->set_var('page_content', $this->bo->get_content());

			$this->t->set_file('sideblocks',$themesel.'/sideblock.tpl');
			$this->t->set_block('sideblocks','SideBlock','SBlock');
			$this->blocks_bo->blocks('l',$this->t);
			$this->t->set_var('left_blocks',$this->t->get_var('SBlock'));
			$this->t->set_var('SBlock','');
			$this->blocks_bo->blocks('r',$this->t);
			$this->t->set_var('right_blocks',$this->t->get_var('SBlock'));
			$this->t->set_var('SBlock','');

			$this->t->set_file('centerblocks',$themesel.'/centerblock.tpl');
			$this->t->set_block('centerblocks','SideBlock','SBlock');
			$this->blocks_bo->blocks('c',$this->t);
			$this->t->set_var('center_blocks',$this->t->get_var('SBlock'));
			$this->t->set_var('SBlock','');

			$this->t->pfp('out','body');
		}
	}
?>
