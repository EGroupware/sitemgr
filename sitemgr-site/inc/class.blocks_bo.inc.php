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

	class blocks_bo
	{
		var $b;
		var $bo;
		
		function blocks_bo()
		{
			global $blocks;
			require_once($GLOBALS['sitemgr_info']['sitemgr-site_path'] . '/blockconfig.inc.php');
			$this->b = $blocks;
			$this->bo = new bo;
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
					$content = lang('No content found');
				}
			}
			elseif ($block['content'])
			{
				$content = $block['content'];
			}
			else
			{
				$content = lang('Block not found');
			}
			return $content;
		}

		function blocks($side, &$t)
		{
			//echo "<pre>";
			//print_r($blocks);
			//echo "</pre>";
			foreach($this->b as $block)
			{
				if($block['position']==$side)
				{
					if ($this->block_allowed($block))
					{
						$title = $this->get_blocktitle($block);
						$content = $this->get_blockcontent($block);
						$t->set_var('block_title',$title);
						$t->set_var('block_content',$content);
						$t->parse('SBlock','SideBlock',true);
					}
				}
			}
		}
		
		function find_block($block_name)
		{
			foreach($this->b as $block)
			{
				if ($block['blockfile'] == 'block-'.$block_name.'.php')
				{
					return $block;
				}
			}
			return false;
		}
	}