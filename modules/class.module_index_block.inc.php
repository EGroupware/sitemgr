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

	class module_index_block extends Module
	{
		function module_index_block()
		{
			$this->arguments = array(
				'sub_cats' => array(
					'type' => 'checkbox',
					'label' => lang('Show subcategories')
				),
				'no_full_index' => array(
					'type' => 'checkbox',
					'label' => lang('No link to full index')
				),
			);
			$this->title = 'Root Site Index';
			$this->description = lang('This module displays the root categories, its pages and evtl. subcategories. It is meant for side areas');
		}

		function get_content(&$arguments,$properties)
		{
			global $objbo;
			$indexarray = $objbo->getIndex(False,!@$arguments['sub_cats'],True);
			$subcatname = $catname = '';
			if (count($indexarray))
			{
				$content = '<ul id="index_block" style="padding-left: 1em;">';
			}
			$last_catdepth = 1;
			foreach($indexarray as $temppage)
			{
				if ($catname != $temppage['catname'] && $temppage['catdepth'] == 1) //category name change
				{
					if ($catname != '') // not the first name change
					{
						$content .= "\n</ul>\n"; // finish listing the pages in last category
					}
					$content .= "<li>$temppage[catlink]</li>\n"; // display this category name
					$content .= '<ul style="padding-left: 1em;">'; // begin to display page list of this category
					$catname = $temppage['catname'];
					$subcatname = '';
				}
				if ($temppage['catdepth'] == 1)
				{
					// dont show no pages availible in Production mode, just ignore it
					if ($GLOBALS['sitemgr_info']['mode'] == 'Edit' ||
						$temppage['page_id'] && $temppage['pagelink'] != lang('No pages available'))
					{
						$content .= "<li> $temppage[pagelink] </li>";
					}
				}
				elseif ($subcatname != $temppage['catname'] && $temppage['catdepth'] == 2)
				{
					$content .= '<li>'.str_replace('</a>',' ...</a>',$temppage[catlink]).'</li>';
					$subcatname = $temppage['catname'];
				}
			}
			if (count($indexarray))
			{
				$content .= "\n</ul>\n</ul>";

				if (!$arguments['no_full_index'])
				{
					$content .= "\n".'<br /><i><a href="'.sitemgr_link2('/index.php','index=1').'"><font size="1">(' . lang('View full index') . ')</font></a></i>';
				}
			}
			else
			{
				$content=lang('You do not have access to any content on this site.');
			}
			return $content ;
		}
	}
?>
