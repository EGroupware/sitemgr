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

	class Common_UI
	{
		var $t, $acl, $do_sites_exist, $menu;
		/**
		 * Instance of theme class
		 * 
		 * @var Theme_BO
		 */
		var $theme;
		var $public_functions = array
		(
			'DisplayPrefs' => True,
			'DisplayMenu' => True,
			'DisplayIFrame' => True
		);

		function Common_UI()
		{
			$GLOBALS['Common_BO'] =& CreateObject('sitemgr.Common_BO');
			$this->do_sites_exist = $GLOBALS['Common_BO']->sites->set_currentsite(False,'Administration');
			$this->t = new Template($GLOBALS['egw']->common->get_tpl_dir('sitemgr'));
			$this->acl = &$GLOBALS['Common_BO']->acl;
			$this->theme = &$GLOBALS['Common_BO']->theme;
			$this->pages_bo = &$GLOBALS['Common_BO']->pages;
			$this->cat_bo = &$GLOBALS['Common_BO']->cats;
		}


		function DisplayMenu()
		{
			$this->DisplayHeader();
			$this->t->set_file('MainMenu','mainmenu.tpl');
			$this->t->set_block('MainMenu','switch','switchhandle');
			$this->t->set_block('MainMenu','menuentry','entry');
			$this->t->set_var('lang_sitemenu',lang('Website') . ' ' . $GLOBALS['Common_BO']->sites->current_site['site_name']);
			foreach($GLOBALS['Common_BO']->get_sitemenu() as $display => $value)
			{
				if ($display == '_NewLine_')
				{
					continue;
				}
				$this->t->set_var(array('value'=>$value,'display'=>lang($display)));
				$this->t->parse('sitemenu','menuentry', true);
			}
			if (($othermenu = $GLOBALS['Common_BO']->get_othermenu()))
			{
				$this->t->set_var('lang_othermenu',lang('Other websites'));
				foreach($othermenu as $display => $value)
				{
					if ($display === '_NewLine_')
					{
						continue;
					}
					if (is_array($value))
					{
						$this->t->set_var(array(
							'display' => $value['no_lang'] ? $value['text'] : lang($value['text']),
							'value'   => $value['link']
						));
					}
					else
					{
						$this->t->set_var(array(
							'display' => lang($display),
							'value'   => $value
						));
					}
					$this->t->parse('othermenu','menuentry', true);
				}
				$this->t->parse('switchhandle','switch');
			}
			else
			{
				$this->t->set_var('switchhandle','testtesttest');
			}
			$this->t->pfp('out','MainMenu');
			$this->DisplayFooter();
		}

		function DisplayIFrame()
		{
			if (($site = $GLOBALS['Common_BO']->sites->read(CURRENT_SITE_ID)) && $site['site_url'])
			{
				$site['site_url'] .= '?mode=Edit&sessionid='.@$GLOBALS['egw_info']['user']['sessionid'] .
					'&kp3=' . @$GLOBALS['egw_info']['user']['kp3'] .
					'&domain=' . @$GLOBALS['egw_info']['user']['domain'];
				
				common::egw_header();
				parse_navbar();
				// jdots already uses an iframe, so no need to create an other one
				if ($GLOBALS['egw']->framework->template == 'jdots')
				{
					echo "<script type='text/javascript'>\nwindow.setTimeout(\"location='{$site['site_url']}';\",10);\n</script>\n";
				}
				else
				{
					echo "\n".'<div style="width: 100%; height: 100%; min-width: 800px; height: 600px">';
					echo "\n\t".'<iframe src="'.$site['site_url'].'" name="site" width="100%" height="100%" frameborder="0" marginwidth="0" marginheight="0"><a href="'.$site['site_url'].'">'.$site['site_url'].'</a></iframe>';
					echo "\n</div>\n";
				}
			}
			else
			{
				$this->DisplayMenu();
			}
		}

		function DisplayPrefs()
		{
			$this->DisplayHeader();
			if ($this->acl->is_admin())
			{
				if ($_POST['btnlangchange'])
				{
					echo '<p>';
					while (list($oldlang,$newlang) = each($_POST['change']))
					{
						if ($newlang == "delete")
						{
							echo '<b>' . lang('Deleting all data for %1',$GLOBALS['Common_BO']->getlangname($oldlang)) . '</b><br>';
							$this->pages_bo->removealllang($oldlang);
							$this->cat_bo->removealllang($oldlang);
						}
						else
						{
							echo '<b>' . lang('Migrating data for %1 to %2',
									$GLOBALS['Common_BO']->getlangname($oldlang),
									$GLOBALS['Common_BO']->getlangname($newlang)) .
							'</b><br>';
							$this->pages_bo->migratealllang($oldlang,$newlang);
							$this->cat_bo->migratealllang($oldlang,$newlang);
						}
					}
					echo '</p>';
				}

				if ($_POST['btnSave'])
				{
					$oldsitelanguages = $GLOBALS['Common_BO']->sites->current_site['site_languages'];

					if ($oldsitelanguages && ($oldsitelanguages != $_POST['pref']['site_languages']))
					{
						$oldsitelanguages = explode(',',$oldsitelanguages);
						$newsitelanguages = preg_split('/ ?, ?/',trim($_POST['pref']['site_languages']));
						$replacedlang = array_diff($oldsitelanguages,$newsitelanguages);
						$addedlang = array_diff($newsitelanguages,$oldsitelanguages);
						if ($replacedlang)
						{
							echo lang('You removed one ore more languages from your site languages.') . '<br>' .
							lang('What do you want to do with existing translations of categories and pages for this language?') . '<br>';
							if ($addedlang)
							{
								echo lang('You can either migrate them to a new language or delete them') . '<br>';
							}
							else
							{
								echo lang('Do you want to delete them?'). '<br>';
							}
							echo '<form action="' .
							$GLOBALS['egw']->link('/index.php','menuaction=sitemgr.Common_UI.DisplayPrefs') .
							'" method="post"><table>';
							foreach ($replacedlang as $oldlang)
							{
								$oldlangname = $GLOBALS['Common_BO']->getlangname($oldlang);
								echo "<tr><td>" . $oldlangname . "</td>";
								if ($addedlang)
								{
									foreach ($addedlang as $newlang)
									{
										echo '<td><input type="radio" name="change[' . $oldlang .
										']" value="' . $newlang . '"> Migrate to ' .
										$GLOBALS['Common_BO']->getlangname($newlang) . "</td>";
									}
								}
								echo '<td><input type="radio" name="change[' . $oldlang . ']" value="delete"> delete</td></tr>';
							}
							echo '<tr><td><input type="submit" name="btnlangchange" value="' .
							lang('Submit') . '"></td></tr></table></form>';
						}
					}

					$oldsitelanguages = $oldsitelanguages ? explode(',',$oldsitelanguages) : array("en");

					$prefs = $_POST['pref'];
					if ($prefs['default_theme'] == 'custom')
					{
						$themedir = $GLOBALS['egw_info']['server']['files_dir'].'/'.$prefs['themedir'];
						if (empty($prefs['themedir']) || !file_exists($themedir) || !is_dir($themedir))
						{
							$error = lang('Custom template directory does NOT exist or is NOT accessible by webserver!');
						}
						$prefs['themesel'] = '/'.$prefs['themedir'];
					}
					else
					{
						$prefs['themesel'] = $prefs['default_theme'];
					}
					unset($prefs['default_theme']);
					unset($prefs['themedir']);
					if (!$error) $GLOBALS['Common_BO']->sites->saveprefs($prefs);

					echo '<p><b>' . ($error ? '<font color="red">'.$error.'</font>' : lang('Changes Saved.')) . '</b></p>';
				}

				foreach ($GLOBALS['Common_BO']->sites->current_site['sitelanguages'] as $lang)
				{
					$langname = $GLOBALS['Common_BO']->getlangname($lang);
					$preferences['site_name_' . $lang] = array(
						'title'=>lang('Site name'). ' ' . $langname,
						'note'=>lang('This is used chiefly for meta data and the title bar. If you change the site languages below you have to save before being able to set this preference for a new language.'),
						'default'=>lang('New sitemgr site')
					);
					 $preferences['site_desc_' . $lang] = array(
						'title'=>lang('Site description'). ' ' . $langname,
						'note'=>lang('This is used chiefly for meta data. If you change the site languages below you have to save before being able to set this preference for a new language.'),
						'input'=>'textarea'
					);
				}

				$preferences['home_page_id'] = array(
					'title'=>lang('Default home page ID number'),
					'note'=>lang('This should be a page that is readable by everyone. If you leave this blank, the site index will be shown by default.'),
					'input'=>'option',
					'options'=>$this->pages_bo->getPageOptionList()
				);
				$theme = $GLOBALS['Common_BO']->sites->current_site['default_theme'];
				if ($theme[0] == '/') $GLOBALS['Common_BO']->sites->current_site['default_theme'] = 'custom';
				$theme_info = $GLOBALS['egw']->link('/sitemgr/theme_info.php');
				$theme_info .= (strpos($theme_info,'?') !== false ? '&' : '?').'theme=';
				$preferences['default_theme'] = array(
					'title'=>lang('Template select'),
					'note'=>lang('Choose your site\'s theme or template.  Note that if you changed the above checkbox you need to save before choosing a theme or template.').'<br /><br />'.
						lang('<b>Want more templates?</b><br />Just download one from the template gallery at %1www.eGroupWare.org%2 or use a %3Mambo Open Source%4 Version 4.5 compatible template eg. from %5. Unpack the downloaded template in your templates directory (%6).',
							'<a href="http://www.eGroupWare.org/sitemgr" target="_blank">','</a>',
							'<a href="http://www.mamboserver.com" target="_blank">','</a>',
							'<a href="http://templates.mamboserver.com/" target="_blank">www.mamboserver.com</a>',
							$GLOBALS['Common_BO']->sites->current_site['site_dir'] . SEP . 'templates'),
					'input'=>'option',
					'options'=>$this->theme->getAvailableThemes()+array(
							'custom' => array(
								'value' => 'custom',
								'display' => lang('Custom template directory'),
							),
					),
					'extra'=> 'onchange="'.
						" document.getElementById('themedir').style.display=this.value=='custom'?'block':'none';".
						" document.getElementById('TemplateInfoIframe').style.display=this.value!='custom'?'block':'none';".
						" if(this.value!='custom')frames.TemplateInfo.location='$theme_info'+this.value;".'"',
					'below' => '<iframe name="TemplateInfo" id="TemplateInfoIframe" style="display:'.
						($theme[0]=='/'?'none':'block').'" width="100%" height="180" src="'.
						$theme_info.($theme ? $theme : 'idots').'" frameborder="0" scrolling="auto"></iframe>'.
						'<div id="themedir"'.($theme[0] != '/' ? ' style="display: none"' : '').'>'.
						'<p>'.lang("Template directory is relative to EGroupware's files directory. You have to map that direcotory with an alias, so it is accessible like any stock template!")."</p>\n".
						htmlspecialchars($GLOBALS['egw_info']['server']['files_dir']).'/'.
						'<input name="pref[themedir]" value="'.($theme[0] == '/' ? htmlspecialchars(substr($theme,1)) : '').'" maxlength="55" size="55" />'.
						($error ? '<br /><font color="red">'.$error.'</font>' : '').
						'</div>',
					'default'=>'idots'
				);
				$preferences['upload_dir'] = array(                                                                                                           
					'title' => lang('Startdirectory in VFS for image uploads'),                                            
					'note'  => lang("This directory is in EGroupware's virtual filesystem, NOT in the filesystem of the webserver!").'<br />'.
						lang('The given directory should be readable by the anonymous user for a regular public website!'),
				);
				$preferences['htaccess_rewrite'] = array(
					'title' => lang('Generate search engine friendly URLs'),
					'note'  => lang('Generates "%1" instead of "%2" URLs.','/name','/index.php?page_name=name').'<br />'.
						'<b>'.lang('You have to enable URL rewriting on the webserver!').'</b><br />'.
						lang('Eg. be renaming %1 to %2 in the %3 directory for an Apache.','htacces','.htaccess','sitemgr-site'),
					'input' => 'checkbox',
				);
				$preferences['site_languages'] = array(
					'title'=>lang('Languages the site user can choose from'),
					'note'=>lang('This should be a comma-separated list of language-codes.'),
					'default'=>'en'
				);

				$this->t->set_file('sitemgr_prefs','sitemgr_preferences.tpl');
				$this->t->set_var('formaction',$GLOBALS['egw']->link(
					'/index.php','menuaction=sitemgr.Common_UI.DisplayPrefs'));
				$this->t->set_var(Array('setup_instructions' => lang('SiteMgr Setup Instructions'),
							'options' => lang('SiteMgr Options'),
							'lang_save' => lang('Save'),
 							'lang_subdir' => lang('There are two subdirectories off of your sitemgr directory that you should move before you do anything else.  You don\'t <i>have</i> to move either of these directories, although you will probably want to.'),
							'lang_first_directory' => lang('The first directory to think about is sitemgr-link.  If you move this to the parent directory of sitemgr (your egroupware root directory) then you can use setup to install the app and everyone with access to the app will get an icon on their navbar that links them directly to the public web site.  If you don\'t want this icon, there\'s no reason to ever bother with the directory.'),
							'lang_second_directory' => lang('The second directory is the sitemgr-site directory.  This can be moved <i>anywhere</i>.  It can also be named <i>anything</i>.  Wherever it winds up, when you point a web browser to it, you will get the generated website.  Assuming, of course, that you\'ve accurately completed the setup fields below and also <b><i>edited the config.inc.php</i></b> file.'),
							'lang_edit_config_inc}' => lang('The config.inc.php file needs to be edited to point to the eGroupWare directory. Copy the config.inc.php.template file to config.inc.php and then edit it.')
				));

				$this->t->set_block('sitemgr_prefs','PrefBlock','PBlock');
				foreach($preferences as $name => $details)
				{
					$inputbox = '';
					switch($details['input'])
					{
						case 'htmlarea':
							$inputbox = $this->inputhtmlarea($name);
							break;
						case 'textarea':
							$inputbox = $this->inputtextarea($name);
							break;
						case 'checkbox':
							$inputbox = $this->inputCheck($name);
							break;
						case 'option':
							$inputbox = $this->inputOption($name,
								$details['options'],$details['default'],@$details['extra']);
							break;
						case 'inputbox':
						default:
							$inputbox = $this->inputText($name,
								$details['input_size'],$details['default']);
					}
					if ($inputbox)
					{
						if (isset($details['below']))
						{
							$inputbox .= "<br />".$details['below'];
						}
						$this->PrefBlock($details['title'],$inputbox,$details['note']);
					}
				}
				$this->t->pfp('out','sitemgr_prefs');
			}
			else
			{
				echo lang("You must be an administrator to setup the Site Manager.") . "<br><br>";
			}
			$this->DisplayFooter();
		}

		function inputText($name='',$size=40,$default='')
		{
			if (!is_int($size))
			{
				$size=40;
			}
			$val = $GLOBALS['Common_BO']->sites->current_site[$name];
			if (!$val)
			{
				$val = $default;
			}

			return '<input type="text" size="'.$size.
				'" name="pref['.$name.']" value="'.htmlspecialchars($val).'">';
		}

		function inputtextarea($name,$cols=80,$rows=5,$default='')
		{
			$val = $GLOBALS['Common_BO']->sites->current_site[$name];
			if (!$val)
			{
				$val = $default;
			}

			return '<textarea cols="' . $cols . '" rows="' . $rows .
				'" name="pref['.$name.']">'. $GLOBALS['egw']->strip_html($val).'</textarea>';
		}

		function inputhtmlarea($name,$cols=80,$rows=5,$default='')
		{
			return html::htmlarea("pref[$name]",$default,'',$GLOBALS['Common_BO']->sites->current_site['site_url']);
		}

		function inputCheck($name = '')
		{
			$val = $GLOBALS['Common_BO']->sites->current_site[$name];
			if ($val)
			{
				$checked_yes = ' CHECKED';
				$checked_no = '';
			}
			else
			{
				$checked_yes = '';
				$checked_no = ' CHECKED';
			}
			return '<INPUT TYPE="radio" NAME="pref['.$name.']" VALUE="1"'.
				$checked_yes.'>Yes</INPUT>'."\n".
				'<INPUT TYPE="radio" NAME="'.$name.'" VALUE="0"'.
				$checked_no.'>No</INPUT>'."\n";

		}

		function inputOption($name = '', $options='', $default = '',$extra='')
		{
			if (!is_array($options) || count($options)==0)
			{
				return lang('No options available.');
			}
			$val = $GLOBALS['Common_BO']->sites->current_site[$name];
			if(!$val)
			{
				$val = $default;
			}
			$returnValue = '<select name="pref['.$name.']" '.$extra.'>'."\n";

			foreach($options as $option)
			{
				$selected='';
				if ($val == $option['value'])
				{
					$selected = 'selected="1" ';
				}
				$returnValue.='<option '.($val == $option['value'] ? 'selected="1" ':'').
					(isset($option['title']) ? 'title="'.$option['title'].'" ':'').
					'value="'.$option['value'].'">'.$option['display'].'</option>'."\n";
			}
			$returnValue .= '</select>';
			return $returnValue;
		}

		function PrefBlock($title,$input,$note)
		{
			//$this->t->set_var('PBlock','');
			$this->t->set_var('pref-title',$title);
			$this->t->set_var('pref-input',$input);
			$this->t->set_var('pref-note',$note);
			$this->t->parse('PBlock','PrefBlock',true);
		}

		function DisplayHeader($extra_title='')
		{
			$GLOBALS['egw_info']['flags']['app_header'] = $GLOBALS['egw_info']['apps']['sitemgr']['title'].
				($extra_title ? ' - '.$extra_title : '');
			common::egw_header();

			if ($this->do_sites_exist && $GLOBALS['egw_info']['server']['template_set'] != 'idots')
			{
				$this->t->set_file('sitemgr_header','sitemgr_header.tpl');
				$this->t->set_block('sitemgr_header','switch','switchhandle');
				$this->t->set_var('menulist',$this->menuselectlist());
				if ($GLOBALS['Common_BO']->get_othermenu())
				{
					$this->t->set_var('sitelist',$this->siteselectlist());
					$this->t->parse('switchhandle','switch');
				}
				else
				{
					$this->t->set_var('switchhandle','');
				}
				$GLOBALS['egw_info']['flags']['app_header'] .= $this->t->parse('out','sitemgr_header');
			}
			parse_navbar();
		}

		function DisplayFooter()
		{
			// is empty atm
			// $this->t->set_file('sitemgr_footer','sitemgr_footer.tpl');
			// $this->t->pfp('out','sitemgr_footer');
		}

		function siteselectlist()
		{
			$selectlist= '<option>' . lang('Other websites') . '</option>';
			while(list($display,$value) = @each($GLOBALS['Common_BO']->othermenu))
			{
				if ($display == '_NewLine_')
				{
					continue;
				}
				else
				{
					$selectlist .= '<option onClick="location.href=this.value" value="' . $value . '">' . lang($display) . '</option>' . "\n";
				}
			}
			return $selectlist;
		}

		function menuselectlist()
		{
			reset($GLOBALS['Common_BO']->sitemenu);
			$selectlist= '<option>' . lang('Website') . ' ' . $GLOBALS['Common_BO']->sites->current_site['site_name'] . '</option>';
			while(list($display,$value) = @each($GLOBALS['Common_BO']->sitemenu))
			{
				if ($display == '_NewLine_')
				{
					continue;
				}
				$selectlist .= '<option onClick="location.href=this.value" value="' . $value . '">' . lang($display) . '</option>' . "\n";
			}
			return $selectlist;
		}
	}
?>
