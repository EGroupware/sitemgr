<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* Written and (c) by RalfBecker@outdoor-training.de                        *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class module_template extends Module
	{
		function module_template()
		{
			$this->arguments = array();
			$this->title = lang('Choose template');
			$this->description = lang('This module lets the users choose a template');
		}
	
		function get_content(&$arguments,$properties)
		{
			if ($templates = $GLOBALS['Common_BO']->theme->getAvailableThemes())
			{
				$content = '<form name="langselect" method="post">';
				$content .= '<select onChange="location.href=this.value" name="themesel">';
				foreach ($templates as $name => $data)
				{
					$selected='';
					if ($name == $GLOBALS['sitemgr_info']['themesel'])
					{
						$selected = 'selected="selected" ';
					}
					$title = $data['title'] ? ' title="'.$data['title'].'"' : '';
					$content .= '<option ' . $selected . 'value="' . $this->link(array(),array('themesel'=>$name)) . '"'.
						($data['title'] ? ' title="'.$data['title'].'"' : '').'>'. $data['value'] . '</option>';
				}
				$content .= '</select>';
				$content .= '</form>';

				return $content;
			}
			return lang('No templates found.');
		}
	}
