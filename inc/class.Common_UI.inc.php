<?php
	/***************************************************************************\
	* http://www.phpgroupware.org                                               *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id$ */
	
	class Common_UI
	{
		var $t, $acl, $theme, $prefs_so;
		var $public_functions = array
		(
			'DisplayPrefs' => True
		);

		function Common_UI()
		{
			global $Common_BO;
			$Common_BO = CreateObject('sitemgr.Common_BO',True);
			$this->t = $GLOBALS['phpgw']->template;
			$this->acl = &$Common_BO->acl;
			$this->theme = &$Common_BO->theme;
			$this->prefs_so = CreateObject('sitemgr.sitePreference_SO', True);
			$this->pages_bo = &$Common_BO->pages;
			$this->cat_bo = &$Common_BO->cats;
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
					$preferences = array(
						'sitemgr-site-url','sitemgr-site-dir','home-page-id',
						'anonymous-user','anonymous-passwd','themesel','sitelanguages');

					$oldsitelanguages = $this->prefs_so->getPreference('sitelanguages');
					if ($oldsitelanguages && ($oldsitelanguages != $_POST['sitelanguages']))
					{
						$oldsitelanguages = explode(',',$oldsitelanguages);
						$newsitelanguages = explode(',',$_POST['sitelanguages']);
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
							  $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Common_UI.DisplayPrefs') .
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
					foreach ($oldsitelanguages as $lang)
					{
						array_push($preferences,'sitemgr-site-name-' . $lang);
					}

					foreach ($preferences as $name)
					{
						$this->prefs_so->setPreference($name,$_POST[$name]);
					}
					echo '<p><b>' . lang('Changes Saved.') . '</b></p>';
					unset($preferences);
				}
				
				$sitelanguages = explode(',',$this->prefs_so->getPreference('sitelanguages'));
				$sitelanguages = $sitelanguages ? $sitelanguages : array("en");
				
				foreach ($sitelanguages as $lang)
				  {
				    $preferences['sitemgr-site-name-' . $lang] = array(
					'title'=>lang('Site name'). ' ' . $GLOBALS['Common_BO']->getlangname($lang),
					'note'=>'(This is used chiefly for meta data and the title bar. If you change the site languages below you have to save before being able to set this preference for a new language.)',
					'default'=>'New sitemgr site'
				    );
				  }

				$preferences['sitemgr-site-url']=array(
					'title'=>lang('URL to sitemgr-site'),
					'note'=>'(The URL can be relative or absolute.  Name must end in a slash.)'
				);
				$preferences['sitemgr-site-dir']=array(
					'title'=>lang('Filesystem path to sitemgr-site directory'),
					'note'=>'(This must be an absolute directory location.  <b>No trailing slash</b>.)'
				);
				$preferences['home-page-id'] = array(
					'title'=>lang('Default home page ID number'),
					'note'=>'(This should be a page that is readable by everyone. If you leave this blank, the site index will be shown by default.)',
					'input'=>'option',
					'options'=>$this->pages_bo->getPageOptionList()
				);
// this does not seem to be used anywhere
// 				$preferences['login-domain'] = array(
// 					'title'=>lang('Anonymous user login domain'),
// 					'note'=>'If you\'re not sure, enter Default.',
// 					'default'=>'Default'
// 				);
				$preferences['anonymous-user'] = array(
					'title'=>lang('Anonymous user\'s username'),
					'note'=>'(If you haven\'t done so already, create a user that will be used for public viewing of the site.  Recommended name: anonymous.)',
					'default'=>'anonymous'
				);
				$preferences['anonymous-passwd'] = array(
					'title'=>lang('Anonymous user\'s password'),
					'note'=>'(Password that you assigned for the aonymous user account.)',
					'default'=>'anonymous'
				);
				$preferences['themesel'] = array(
					'title'=>lang('Template select'),
					'note'=>'(Choose your site\'s theme or template.  Note that if you changed the above checkbox you need to save before choosing a theme or template.)',
					'input'=>'option',
					'options'=>$this->theme->getAvailableThemes(),
					'default'=>'NukeNews'
				);
				$preferences['sitelanguages'] = array(
					'title'=>lang('Languages the site user can choose from'),
					'note'=>'(This should be a comma-separated list of language-codes.)',
					'default'=>'en'
				);

				$this->t->set_file('sitemgr_prefs','sitemgr_preferences.tpl');
				$this->t->set_var('formaction',$GLOBALS['phpgw']->link(
					'/index.php','menuaction=sitemgr.Common_UI.DisplayPrefs'));
				$this->t->set_var(Array('setup_instructions' => lang('SiteMgr Setup Instructions'),
							'options' => lang('SiteMgr Options'),
							'lang_save' => lang('Save')
				));
						       
				$this->t->set_block('sitemgr_prefs','PrefBlock','PBlock');
				reset($preferences);
				while (list($name,$details) = each($preferences))
				{
					$inputbox = '';
					switch($details['input'])
					{
						case 'checkbox':
							$inputbox = $this->inputCheck($name);
							break;
						case 'option':
							$inputbox = $this->inputOption($name,
								$details['options'],$details['default']);
							break;
						case 'inputbox':
						default:
							$inputbox = $this->inputText($name,
								$details['input_size'],$details['default']);
					}
					if ($inputbox)
					{
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
			$val = $this->prefs_so->getPreference($name);
			if (!$val)
			{
				$val = $default;
			}

			return '<input type="text" size="'.$size.
				'" name="'.$name.'" value="'.$val.'">';
		}

		function inputCheck($name = '')
		{
			$val = $this->prefs_so->getPreference($name);
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
			return '<INPUT TYPE="radio" NAME="'.$name.'" VALUE="1"'.
				$checked_yes.'>Yes</INPUT>'."\n".
				'<INPUT TYPE="radio" NAME="'.$name.'" VALUE="0"'.
				$checked_no.'>No</INPUT>'."\n";
				
		}

		function inputOption($name = '', $options='', $default = '')
		{
			if (!is_array($options) || count($options)==0)
			{
				return lang('No options available.');
			}
			$val = $this->prefs_so->getPreference($name);
			if(!$val)
			{
				$val = $default;
			}
			$returnValue = '<SELECT NAME="'.$name.'">'."\n";
			
			foreach($options as $option)
			{
				$selected='';
				if ($val == $option['value'])
				{
					$selected = 'SELECTED ';
				}
				$returnValue.='<OPTION '.$selected.'VALUE="'.$option['value'].'">'.
					$option['display'].'</OPTION>'."\n";
			}
			$returnValue .= '</SELECT>';
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

		function DisplayHeader()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->t->set_file('sitemgr_header','sitemgr_header.tpl');
			$this->t->set_var('mainmenu',
				$GLOBALS['phpgw']->link('/index.php',
				'menuaction=sitemgr.MainMenu_UI.DisplayMenu')
			);
			$this->t->set_var('sitemgr-site', $GLOBALS['phpgw']->link('/sitemgr-link/'));
			$this->t->set_var(Array('sitemgr_administration' => lang('Web Content Manager Administration'),
						'view_menu' => lang('View Administrative Menu'),
						'view_site' => lang('View Generated Site')
			));
			$this->t->pfp('out','sitemgr_header');
		}

		function DisplayFooter()
		{
			$this->t->set_file('sitemgr_footer','sitemgr_footer.tpl');
			$this->t->pfp('out','sitemgr_footer');
		}
	}	
?>
