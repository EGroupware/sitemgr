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

	class ManageBlocks_UI
   	{
		var $t;
		var $block_bo;
		var $common_ui;
		var $public_functions = array
		(
		 '_manageBlocks' => True
		);

		function ManageBlocks_UI()
		{
			$this->t = $GLOBALS['phpgw']->template;
			$this->block_bo = CreateObject('sitemgr.Blocks_BO', True);
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
		}

		function globalize($varname)
		{
			if (is_array($varname))
			{
				foreach($varname as $var)
				{
					$GLOBALS[$var] = $_POST[$var];
				}
			}
			else
			{
				$GLOBALS[$varname] = $_POST[$varname];
			}
		}

		function inputOption($name = '', $options='', $default = '')
		{
			$returnValue = '<SELECT NAME="'.$name.'">'."\n";
			
			while (list($value,$display) = each($options))
			{
				$selected='';
				if ($default == $value)
				{
					$selected = 'SELECTED ';
				}
				$returnValue.='<OPTION '.$selected.'VALUE="'.$value.'">'.
					$display.'</OPTION>'."\n";
			}
			$returnValue .= '</SELECT>';
			return $returnValue;
		}

		function _manageBlocks()
		{
			$this->globalize(array('blocktitle','blockactif','blockpos','blockside','blockview','btnSaveBlock'));
			global $blocktitle, $blockactif, $blockpos, $blockside, $blockview, $btnSaveBlock;

			$this->common_ui->DisplayHeader();

			if ($btnSaveBlock)
			{
				$blockinfo = CreateObject('sitemgr.Block_SO', True);
				foreach ($blocktitle as $id => $title)
				{
					$blockinfo->id = $id;
					$blockinfo->title = $title;
					$blockinfo->actif = $blockactif[$id] ? 1 : 0;
					$blockinfo->pos = $blockpos[$id];
					$blockinfo->side = $blockside[$id];
					$blockinfo->view = $blockview[$id];
					$this->block_bo->saveblockinfo($blockinfo);
				}
			}

			$blocks = $this->block_bo->getavailableblocks();

			if (is_array($blocks))
			{
				$this->t->set_file('ManageBlocks', 'manage_blocks.tpl');
				$this->t->set_block('ManageBlocks', 'BlockBlock', 'BBlock');
				$this->t->set_var(Array('block_manager' => lang('Block Manager'),
					'lang_description' => lang('Description'),
					'lang_actif' => lang('actif'),
					'lang_name' => lang('Name'),
					'lang_title' => lang('Title'),
					'lang_side' => lang('side'),
					'lang_view' => lang('seen by'),
					'lang_reset' => lang('Reset'),
					'lang_save' => lang('Save'),
					'lang_position' => lang('Position')));
	
				foreach ($blocks as $blockname)
				{
					$blockinfo = $this->block_bo->getblockinfo($blockname);
					preg_match("/block-(.*).php$/", $blockname, $match);
					$this->t->set_var(Array(
						'blockname' => $match[1],
						'blockdescription' => $blockinfo->description,
						'blockid' => $blockinfo->id,
						'blockactif' => ($blockinfo->actif ? 'checked="checked"' : ''),
						'blocktitle' => $blockinfo->title,
						'sideselect' => $this->inputOption(
									'blockside['.$blockinfo->id.']',
									array('0' => 'left','1' => 'center','2' => 'right'),
									$blockinfo->side),
						'viewselect' => $this->inputOption(
									'blockview['.$blockinfo->id.']',
									array(
									  '0' => 'everybody',
									  '1' => 'phpgw users',
									  '2' => 'administrators',
									  '3' => 'anonymous'),
									$blockinfo->view),
						'blockpos'  => (int)$blockinfo->pos
						)
					);
					$this->t->parse('BBlock','BlockBlock', true);
				}
				$this->t->pfp('out', 'ManageBlocks');
			}
			else
			{
				echo '<b>' .
				     (($blocks == 1) ? 
					lang('Blockfile directory not found') : 
					lang('No blockfiles found in blockfile directory')) .
				     '</b>';
			}
			$this->common_ui->DisplayFooter();
		}
}
