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

	class module_html extends Module
	{
		function module_html()
		{
			$this->i18n = true;
			$this->arguments = array(
				'htmlcontent' => array(
					'type' => 'htmlarea',
					'label' => lang('Enter the block content here'),
					'large' => True,	// show label above content
					'i18n' => True,
					'params' => Array(
						'style' => 'width:100%; min-width:500px; height:280px',
						'plugins' => '
							theme : "advanced",
							theme_advanced_toolbar_location : "top",
							theme_advanced_toolbar_align : "left",
							theme_advanced_disable : "styleselect",
							plugins : "filemanager,table,contextmenu,paste,fullscreen,advhr,advimage,advlink,iespell,insertdatetime,searchreplace,flash",
							theme_advanced_buttons1_add : "fontselect,fontsizeselect,separator,search,replace",
							theme_advanced_buttons2_add : "separator,iespell,insertdate,inserttime,separator,flash",
							theme_advanced_buttons3_add : "separator,filemanager,fullscreen",
							theme_advanced_buttons3_add_before : "tablecontrols,separator,advhr",
							extended_valid_elements : "hr[class|width|size|noshade], a[name|href|target|title|onclick], img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],font[*]",
							document_base_url : "'. $site_url. '",
							relative_urls : true,
							flash_wmode : "transparent",
							flash_quality : "high",
							flash_menu : "false"'
					)
				)
			);
			$this->properties = array('striphtml' => array('type' => 'checkbox', 'label' => lang('Strip HTML from block content?')));
			$this->title = lang('HTML module');
			$this->description = lang('This module is a simple HTML editor');
		}

		function get_content(&$arguments,$properties)
		{
			if ($properties['striphtml'])
			{
				return $GLOBALS['egw']->strip_html($arguments['htmlcontent']);
			}
			// spamsaver emailaddress and activating the links
			if (!is_object($GLOBALS['egw']->html))
			{
				$GLOBALS['egw']->html =& CreateObject('phpgwapi.html');
			}
			return $GLOBALS['egw']->html->activate_links($arguments['htmlcontent']);
		}
	}
