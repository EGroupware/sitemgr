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
					'params' => Array('style' => 'width:100%; min-width:500px; height:300px')
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
				return $GLOBALS['phpgw']->strip_html($arguments['htmlcontent']);
			}
			// spamsaver emailaddress
			$result = preg_replace('/mailto:([a-z0-9._-]+)@([a-z0-9_-]+)\.([a-z0-9._-]+)/i',
					'<a href="#" onclick="document.location=\'mai\'+\'lto:\\1\'+unescape(\'%40\')+\'\\2.\\3\'; return false;">\\1 AT \\2 DOT \\3</a>',
					$arguments['htmlcontent']);

			//  First match things beginning with http:// (or other protocols)
			$NotAnchor = '(?<!"|href=|href\s=\s|href=\s|href\s=)';
			$Protocol = '(http|ftp|https):\/\/';
			$Domain = '([\w]+.[\w]+)';
			$Subdir = '([\w\-\.,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?';
			$Expr = '/' . $NotAnchor . $Protocol . $Domain . $Subdir . '/i';

			$result = preg_replace( $Expr, "<a href=\"$0\" target=\"_blank\">$2$3</a>", $result );

			//  Now match things beginning with www.
			$NotAnchor = '(?<!"|href=|href\s=\s|href=\s|href\s=)';
			$NotHTTP = '(?<!:\/\/)';
			$Domain = 'www(.[\w]+)';
			$Subdir = '([\w\-\.,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?';
			$Expr = '/' . $NotAnchor . $NotHTTP . $Domain . $Subdir . '/i';

			return preg_replace( $Expr, "<a href=\"http://$0\" target=\"_blank\">$0</a>", $result );
		}
	}
