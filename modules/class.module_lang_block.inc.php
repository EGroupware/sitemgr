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

class module_lang_block extends Module
{
	function module_lang_block()
	{
		$this->arguments = array(
			'layout' => array(
				'type' => 'select',
				'label' => lang('Select layout for lang selection'),
				'options' => array(
					'plain' => lang('Plain selectbox'),
					'flags' => lang('Flag symbols').' (images/*.gif)',
					'flags.png' => lang('Flag symbols').' (images/*.png)',
				),
			),
			'flag_path' => array(
				'type' => 'textfield',
				'label' => lang('URL to directory containing the flag images.'),
				'default' => 'images/',
			),
		);

		$this->properties = array();
		$this->title = lang('Choose language');
		$this->description = lang('This module lets users choose language');
	}

	function get_content(&$arguments,$properties)
	{
		if ($GLOBALS['sitemgr_info']['sitelanguages'])
		{
			if (substr($arguments['layout'],0,5) == 'flags')
			{
				$content = '
					<div id="langsel_flags">
				 		<div class="langsel_flag">';
				foreach ($GLOBALS['sitemgr_info']['sitelanguages'] as $lang)
				{
					$imagepath = ($arguments['flag_path']) ? $arguments['flag_path'] : 'images/';
					// Ensure the URL to contain a trailing slash
					if (strrchr($imagepath, '/') != '/')
					{
						$imagepath .= '/';
					}
					$content .= '
							<a href="'. $this->link(array(),array('lang'=>$lang)) .
								'">'. '<img src="'. $imagepath . $lang.
								($arguments['layout'] == 'flags' ? '.gif' : '.png').
								'" class="langsel_flags_image"></a>';
				}
				$content .= '
						</div>
					</div>';
			}
			else
			{
				$content = '<form name="langselect" method="post" action="">';
				$content .= '<select onChange="location.href=this.value" name="language">';
				foreach ($GLOBALS['sitemgr_info']['sitelanguages'] as $lang)
				{
					$selected='';
					if ($lang == $GLOBALS['sitemgr_info']['userlang'])
					{
						$selected = 'selected="1" ';
					}
					$content .= '<option ' . $selected . 'value="' . str_replace('&','&amp;',$this->link(array(),array('lang'=>$lang))) . '">'.$GLOBALS['Common_BO']->getlangname($lang) . '</option>';
				}
				$content .= '</select>';
				$content .= '</form>';
			}

			return $content;
		}
		return lang('No sitelanguages configured');
	}
}
