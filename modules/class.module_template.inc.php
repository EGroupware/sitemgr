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
			$this->title = lang('Template chooser and/or gallery');
			$this->themes = $GLOBALS['Common_BO']->theme->getAvailableThemes();
			$this->arguments = array(
				'allowed' => array(
					'type' => 'select',
					'multiple' => True,
					'label' => lang('Select the templates the user is allowed to see'),
					'options' => $this->themes,
					'default' => array_keys($this->themes),		// all
				),
				'show' => array(
					'type' => 'select',
					'label' => lang('Show a template-gallery (thumbnail and informations)'),
					'options' => array(
						1 => lang('No, chooser only (for side-areas)'),
						3 => lang('Gallery plus chooser'),
						7 => lang('Gallery plus chooser and download'),
						2 => lang('Only gallery'),
						6 => lang('Gallery plus download'),
					),
				),
				'zip' => array(
					'type' => 'textfield',
					'label' => lang('Path to zip binary if not in path of the webserver'),
					'default' => 'zip',
				),
			);
			$this->description = lang('This module lets the users choose a template');
		}
	
		function get_content(&$arguments,$properties)
		{
			$show = $arguments['show'] ? $arguments['show'] : 1;
			$download = @$_GET['download'];

			if (($show & 4) && $download && in_array($download,$arguments['allowed']))
			{
				$zip = @$arguments['zip'] ? $arguments['zip'] : 'zip';
				ob_end_clean();	// discard all previous output
				$browser = CreateObject('phpgwapi.browser');
				$browser->content_header($download.'.zip','application/zip');
				passthru('cd '.$GLOBALS['sitemgr_info']['site_dir'].'/templates; '.$zip.' -qr - '.$download);
				exit;
			}
			if (count($arguments['allowed']) > 1)
			{
				if ($show == 1)	// only chooser
				{
					$content .= '<form name="themeselect" method="post">';
					$content .= '<select onChange="location.href=this.value" name="themesel">';
					foreach ($this->themes as $name => $info)
					{
						if (!in_array($name,$arguments['allowed']))
						{
							continue;
						}
						$selected='';
						if ($name == $GLOBALS['sitemgr_info']['themesel'])
						{
							$selected = 'selected="selected" ';
						}
						$title = $info['title'] ? ' title="'.$info['title'].'"' : '';
						$content .= '<option ' . $selected . 'value="' . $this->link(array(),array('themesel'=>$name)) . '"'.
							($info['title'] ? ' title="'.$info['title'].'"' : '').'>'. $info['value'] . '</option>';
					}
					$content .= '</select>';
					$content .= '</form>';
				}
				if ($show & 2)	// gallery
				{
					$t = CreateObject('phpgwapi.Template',PHPGW_SERVER_ROOT.'/sitemgr/templates/');
					$t->set_file('theme_info','theme_info.tpl');
					$t->set_block('theme_info','info_block');
					$content .= '<table>'."\n";
					foreach ($this->themes as $name => $info)
					{
						if ($further) $content .= '<tr><td colspan="2"><hr style="width: 30%" /></td></tr>'."\n";
						$further = True;

						if (!in_array($name,$arguments['allowed']))
						{
							continue;
						}
						if ($info['thumbnail'])
						{
							$info['thumbnail'] = '<img src="'.$info['thumbnail'].'" border="0" hspace="5"/>';
							if ($show & 1)	// chooser
							{
								$info['thumbnail'] = '<a href="'.sitemgr_link(array('themesel'=>$name)+$_GET).'" title="'.
									lang('View template on this site').'">'.$info['thumbnail'].'</a>';
							}
						}
						if ($show & 4)	// download
						{
							$info['copyright'] .= '<p style="font-size: 10pt;"><a href="'.
								sitemgr_link(array('download'=>$name)+$_GET).'">'.'<img src="images/zip.gif" border="0" /> '.
								lang('download as ZIP-archiv').'</a></p>'."\n";
						}
						$t->set_var($info);
						$t->set_var(array(
							'lang_author' => lang('Author'),
							'lang_copyright' => lang('Copyright'),
						));
						$content .= $t->parse('out','info_block');
					}
					$content .= '</table>'."\n";
				}
				return $content;
			}
			return lang('No templates found.');
		}
	}
