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
		var $t;

		function ui()
		{
			$this->bo = new bo;
			$this->t = new Template2;
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

		function generatePageTemplate()
		{
			$themesel = $GLOBALS['sitemgr_info']['themesel'];
			$templatedir = $GLOBALS['sitemgr_info']['sitemgr-site_path'].'/templates/';
			if (!file_exists($templatedir.$themesel.'/main.tpl'))
			{
				die("Selected template '$themesel' does not exist.");
			}
			$this->t->set_root($templatedir);
			$this->t->set_unknowns('keep');

			$this->t->set_file('header','header.tpl');
			$this->t->set_var('themesel',$themesel);
			$this->t->set_var('site_name',$this->bo->get_siteName());
			$this->t->set_var('page_title',$this->bo->get_title());
			$this->t->pfp('out','header');
			$this->t->set_file('body',$themesel.'/main.tpl');

			$this->t->set_var('user', $GLOBALS['phpgw_info']['user']['account_lid']);
			$this->t->set_var('site_name',$this->bo->get_siteName());
			$this->t->set_var('site_header', $this->bo->get_header());
			$this->t->set_var('site_footer', $this->bo->get_footer());
			$this->t->set_var('page_title', $this->bo->get_title());
			$this->t->set_var('page_subtitle', $this->bo->get_subtitle());
			$this->t->set_var('page_content', $this->bo->get_content());

			$this->t->set_file('sideblocks',$themesel.'/sideblock.tpl');
			$this->t->set_block('sideblocks','SideBlock','SBlock');
			$this->blocks('l');
			$this->t->set_var('left_blocks',$this->t->get_var('SBlock'));
			$this->t->set_var('SBlock','');
			$this->blocks('r');
			$this->t->set_var('right_blocks',$this->t->get_var('SBlock'));
			$this->t->set_var('SBlock','');


			$this->t->pfp('out','body');
		}

		function block_allowed($block)
		{
			switch($block['view'])
			{
				case 0:
					return true;
				case 1:
					return $this->bo->is_user();
				case 2:
					return $this->bo->is_admin();
				case 3:
					return (! $this->bo->is_user());
			}
			return false;
		}

		function get_blocktitle($block)
		{
			return $block['title'];
		}

		function get_blockcontent($block)
		{
			$content='';
			if (file_exists('blocks/'.$block['blockfile']) && trim($block['blockfile']))
			{
				include('blocks/'.$block['blockfile']);
				if (!$content)
				{
					$content = 'No content found';
				}
			}
			elseif ($block['content'])
			{
				$content = $block['content'];
			}
			else
			{
				$content = 'Block not found';
			}
			return $content;
		}

		function blocks($side)
		{
			global $blocks;
			//echo "<pre>";
			//print_r($blocks);
			//echo "</pre>";
			foreach($blocks as $block)
			{
				if($block['position']==$side)
				{
					if ($this->block_allowed($block))
					{
						$title = $this->get_blocktitle($block);
						$content = $this->get_blockcontent($block);
						$this->t->set_var('block_title',$title);
						$this->t->set_var('block_content',$content);
						$this->t->parse('SBlock','SideBlock',true);
					}
				}
			}
		}
	}
?>
