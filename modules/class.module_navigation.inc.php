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

	/**
	 * Template based navigation module
	 *
	 * @author RalfBecker-AT-outdoor-training.de
	 * @package sitemgr
	 * 
	 * The module displays the root categories in one block each with pages and subcategories.
	 * Pages of subcategories are only shown, if the category gets activated.
	 *
	 * To change / adapt the design: copy the default template (templates/default/modules/navigation/navigation.tpl) 
	 * into your template-directory and adapt it to your needs.
	 */
	class module_navigation extends Module
	{
		function module_navigation()
		{
			$this->arguments = array();
			$this->title = 'Template based navigation module';
			$this->description = lang("This module displays the root categories in one block each, with pages and subcategories (incl. their pages if activated).");
		}

		function get_content(&$arguments,$properties)
		{
			global $objbo,$page;
			$index_pages = $objbo->getIndex(False,False,True);

			if (!count($index_pages))
			{
				return lang('You do not have access to any content on this site.');
			}
			$index_pages[] = array(	// this is used to correctly finish the last block
				'cat_id'	=> 0,
				'catdepth'	=> 1,
			);

			$this->template = CreateObject('phpgwapi.Template',$this->find_template_dir());
			$this->template->set_file('cat_block','navigation.tpl');
			$this->template->set_block('cat_block','block_start');
			$this->template->set_block('cat_block','level1');
			$this->template->set_block('cat_block','level2');
			$this->template->set_block('cat_block','block_end');
			
			$last_cat_id = 0;
			foreach($index_pages as $ipage)
			{
				preg_match('/href="([^"]+)"/i',$ipage['catlink'],$matches);
				$this->template->set_var(array(
					'item_link' => $matches[1],
					'item_name' => $ipage['catname'],
					'item_desc' => $ipage['catdescrip'],
				));
				if ($ipage['cat_id'] != $last_cat_id)	// new category
				{
					switch ($ipage['catdepth'])
					{
						case 1:	// start of a new level-1 block
							if ($last_cat_id)	// if there was a previous block, finish that one first
							{
								$content .= $this->template->parse('out','block_end');
							}
							// start the new block
							if ($ipage['cat_id'])
							{
								$content .= $this->template->parse('out','block_start');
							}
							break;
						case 2:
							$content .= $this->template->parse('out','level1');
					}
				}
				$last_cat_id = $ipage['cat_id'];
				
				// show the pages of the active cat or first-level pages
				if ($ipage['page_id'] && ($ipage['cat_id'] == $page->cat_id || $ipage['catdepth'] == 1))
				{
					preg_match('/href="([^"]+)"/i',$ipage['pagelink'],$matches);
					$this->template->set_var(array(
						'item_link'		=> $matches[1],
						'item_name'		=> $ipage['pagesubtitle'],
						'item_desc'		=> $ipage['pagetitle'],
					));
					$content .= $this->template->parse('out',$ipage['catdepth'] == 1 ? 'level1' : 'level2');
				}
			}
			return $content;
		}
	}
?>