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
		var $public_functions = array
		(
			'DisplayPrefs' => True
		);

		function Common_UI()
		{
			$this->t = $GLOBALS['phpgw']->template;
			$this->acl = CreateObject('sitemgr.ACL_BO');
		}

		function DisplayPrefs()
		{
			$this->DisplayHeader();
			if ($this->acl->is_admin())
			{
				$prefs_so = CreateObject('sitemgr.sitePreference_SO', True);
				if ($GLOBALS['btnSave'])
				{
					$prefs_so->setPreference('sitemgr-gen-url',$GLOBALS['sitemgr_gen_url']);
					$prefs_so->setPreference('sitemgr-gen-dir',$GLOBALS['sitemgr_gen_dir']);
					$prefs_so->setPreference('home-page-id',$GLOBALS['home_page_id']);
					$prefs_so->setPreference('sitemgr-site-name',$GLOBALS['sitemgr_site_name']);
					echo '<p><b>Changes Saved.</b></p>';
				}
				$sitemgr_gen_url = $prefs_so->getPreference('sitemgr-gen-url');
				$sitemgr_gen_dir = $prefs_so->getPreference('sitemgr-gen-dir');
				$home_page_id = $prefs_so->getPreference('home-page-id');
				$sitemgr_site_name = $prefs_so->getPreference('sitemgr-site-name');
				$this->t->set_file('sitemgr_prefs','sitemgr_preferences.tpl');
				$this->t->set_var('formaction',$GLOBALS['phpgw']->link(
					'/index.php','menuaction=sitemgr.Common_UI.DisplayPrefs'));
				$this->t->set_var('sitemgr-gen-url',$sitemgr_gen_url);
				$this->t->set_var('sitemgr-gen-dir',$sitemgr_gen_dir);
				$this->t->set_var('home-page-id',$home_page_id);
				$this->t->set_var('sitemgr-site-name',$sitemgr_site_name);
				$this->t->pfp('out','sitemgr_prefs');
			}
			else
			{
				echo "You must be an administrator to setup the Site Manager.<br><br>";
			}
			$this->DisplayFooter();
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
			$this->t->set_var('sitemgr-site', $GLOBALS['phpgw']->link('/sitemgr-site/'));
			$this->t->pfp('out','sitemgr_header');
		}

		function DisplayFooter()
		{
			$this->t->set_file('sitemgr_footer','sitemgr_footer.tpl');
			$this->t->pfp('out','sitemgr_footer');
		}
	}	
?>
