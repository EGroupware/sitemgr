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
		var $t;
		var $acl;
		var $theme;
		var $prefs_so;
		var $public_functions = array
		(
			'DisplayPrefs' => True
		);

		function Common_UI()
		{
			$this->t = $GLOBALS['phpgw']->template;
			$this->acl = CreateObject('sitemgr.ACL_BO');
			$this->theme = CreateObject('sitemgr.Theme_BO',True);
			$this->prefs_so = CreateObject('sitemgr.sitePreference_SO', True);
		}

		function DisplayPrefs()
		{
			$this->DisplayHeader();
			if ($this->acl->is_admin())
			{
				$preferences['sitemgr-site-name'] = array(
					'title'=>'Site name',
					'note'=>'(This is used chiefly for meta data and the title bar.)',
					'default'=>'New sitemgr site'
				);
				$preferences['sitemgr-site-url']=array(
					'title'=>'URL to sitemgr-site',
					'note'=>'(The URL can be relative or absolute.  Name must end in a slash.)'
				);
				$preferences['sitemgr-site-dir']=array(
					'title'=>'Filesystem path to sitemgr-site directory',
					'note'=>'(This must be an absolute directory location.  <b>No trailing slash</b>.)'
				);
				$preferences['home-page-id'] = array(
					'title'=>'Default home page ID number',
					'note'=>'(This should be a page that is readable by everyone. If you leave this blank, the site index will be shown by default.)',
					'input_size'=>10
				);
				$preferences['login-domain'] = array(
					'title'=>'Anonymous user login domain',
					'note'=>'If you\'re not sure, enter Default.',
					'default'=>'Default'
				);
				$preferences['anonymous-user'] = array(
					'title'=>'Anonymous user\'s username',
					'note'=>'(If you haven\'t done so already, create a user that will be used for public viewing of the site.  Recommended name: anonymous.)',
					'default'=>'anonymous'
				);
				$preferences['anonymous-passwd'] = array(
					'title'=>'Anonymous user\'s password',
					'note'=>'(Password that you assigned for the aonymous user account.)',
					'default'=>'anonymous'
				);
				$preferences['interface'] = array(
					'title'=>'Use phpNuke themes instead of templates',
					'note'=>'(This is NOT recommended.)',
					'input'=>'checkbox'
				);
				$preferences['themesel'] = array(
					'title'=>'Theme or template select',
					'note'=>'(Choose your site\'s theme or template.  Note that if you changed the above checkbox you need to save before choosing a theme or template.)',
					'input'=>'option',
					'options'=>$this->theme->getAvailableThemes(),
					'default'=>'NukeNews'
				);
				if ($GLOBALS['btnSave'])
				{
					reset($preferences);
					while (list($name,$details) = each($preferences))
					{
						$this->prefs_so->setPreference($name,$GLOBALS[$name]);
					}
					echo '<p><b>Changes Saved.</b></p>';
				}
				
				$this->t->set_file('sitemgr_prefs','sitemgr_preferences.tpl');
				$this->t->set_var('formaction',$GLOBALS['phpgw']->link(
					'/index.php','menuaction=sitemgr.Common_UI.DisplayPrefs'));
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
				echo "You must be an administrator to setup the Site Manager.<br><br>";
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
				return 'No options available.';
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
			$this->t->pfp('out','sitemgr_header');
		}

		function DisplayFooter()
		{
			$this->t->set_file('sitemgr_footer','sitemgr_footer.tpl');
			$this->t->pfp('out','sitemgr_footer');
		}
	}	
?>
