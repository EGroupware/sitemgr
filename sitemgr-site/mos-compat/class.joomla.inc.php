<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - SiteMgr support for Mambo Open Source templates     *
	* http://www.egroupware.org                                                *
	* Written and (c) by StefanBecker@outdoor-training.de                        *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: mosmenu.inc.php 13831 2004-02-22 01:46:27Z stefanbecker $ */

class joomla {
	//require that files in the joomla template to get the menu working
	//require_once($GLOBALS['objui']->mos_compat_dir.'/class.joomla.inc.php');
	//require_once($GLOBALS['objui']->mos_compat_dir.'/class.JFilterOutput.inc.php');
	//require_once($GLOBALS['objui']->mos_compat_dir.'/joomla_Legacy_function.inc.php');
	//to get the menu from eGW:
	//$joomla = new joomla();
	//$rows= $joomla->getmenu();
	function getmenu()
	{
		$topcats = $GLOBALS['objbo']->getIndex(null,False);
		foreach($topcats as $cat_id => $cat)
		{
			if ($catidscheck[$cat['cat_id']]) continue;
			$catidscheck[$cat['cat_id']] = true;
			$tree = array();
			if ($cat['catdepth'] == 1)
			{
				$tree[0] =$cat['cat_id'];
				$uptree = $cat['cat_id'];
				$sublevel=0;
				$parent = 0;
			}
			if ($cat['catdepth'] == 2)
			{
				$tree[0] = $uptree;
				$tree[1] = $cat['cat_id'];
				$sublevel=1;
				$parent = $uptree;
			}

			$arr = array(
			'id' => $cat['cat_id'],
			'menutype' => 'mainmenu',
			'name' => $cat['catname'],
     		'alias' => $cat['catname'],
      		'type' => 'component_item_link',
    		'published' => 1,
    		'parent' => $parent,
    		'componentid' => 20,
    		'sublevel' => $sublevel,
    		'ordering' => 1,
    		'checked_out' => 62,
    		'checked_out_time' => '2008-06-30 16:52:29',
    		'pollid' => 0,
    		'browserNav' => 0,
    		'access' => 0,
    		'utaccess' => 3,
    		'params' => 'num_leading_articles=1
			num_intro_articles=4
			num_columns=2
			num_links=4
			orderby_pri=
			orderby_sec=front
			show_pagination=2
			show_pagination_results=1
			show_feed_link=1
			show_noauth=
			show_title=
			link_titles=
			show_intro=
			show_section=
			link_section=
			show_category=
			link_category=
			show_author=
			show_create_date=
			show_modify_date=
			show_item_navigation=
			show_readmore=
			show_vote=
			show_icons=
			show_pdf_icon=
			show_print_icon=
			show_email_icon=
			show_hits=
			feed_summary=
			page_title=
			show_page_title=1
			pageclass_sfx=
			menu_image=-1
			secure=0'

			,
    		'lft' => 0,
    		'rgt' => 0,
    		'home' => 1,
    		'component' => 'com_content',
    		'tree' => $tree,
 	  		'route' => 'home',
 	  		'query' => Array('option' => 'com_content','view' => 'frontpage'),
			'url'  => $cat['url'],
    		'_idx' => 0,
			);
			$rows[] = (object)$arr;
		}

		return $rows;

	}

}