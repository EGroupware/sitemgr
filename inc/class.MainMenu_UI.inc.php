<?php
	/*************************************************************************\
	* http://www.phpgroupware.org                                             *
	* -------------------------------------------------                       *
	* This program is free software; you can redistribute it and/or modify it *
	* under the terms of the GNU General Public License as published by the   *
	* Free Software Foundation; either version 2 of the License, or (at your  *
	* option) any later version.                                              *
	\*************************************************************************/
	/* $Id$ */
	
	class MainMenu_UI
	{
		var $common_ui;
		var $t;
		var $acl;
		var $public_functions = array
		(
			'DisplayMenu'  => True
		);
															            
		function MainMenu_UI()
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS['phpgw']->template;
			$this->acl = &$GLOBALS['Common_BO']->acl;
		}

		function DisplayMenu()
		{
			$this->common_ui->DisplayHeader();

			$this->t->set_file('MainMenu','mainmenu.tpl');

			$this->t->set_var(Array(
				'managepage' => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Pages_UI._managePage'),
				'managetranslations' => $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Translations_UI._manageTranslations'),
				'managecategory' => $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Categories_UI._manageCategories'),
				'lang_managecat' => lang('Manage Categories'),
				'lang_managepage' => lang('Manage Pages'),
				'lang_managetranslations' => lang('Manage Translations')
			));

			if ($this->acl->is_admin())
			{
				$this->t->set_var(Array(
					'menutitle'	=> lang('Administrative Menu'),
					'lang_configure' => lang('Configure SiteMgr'),
					'lang_check' => lang('check here after every upgrade'),
					'lang_editheadfoot' => lang('Edit Site Header and Footer'),
					'lang_managesitemodules' => lang('Manage site-wide module properties'),
					'lang_managesitecontent' => lang('Manage Site Content'),
					'headerandfooter' => $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.SiteContent_UI._editHeaderAndFooter'),
					'setup'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Common_UI.DisplayPrefs')
				));
				$link_data['cat_id'] = 0;
				$link_data['menuaction'] = "sitemgr.Modules_UI._manageModules";
				$this->t->set_var('managesitemodules',$GLOBALS['phpgw']->link('/index.php',$link_data));
				$link_data['page_id'] = 0;
				$link_data['menuaction'] = "sitemgr.Content_UI._manageContent";
				$this->t->set_var('managesitecontent',$GLOBALS['phpgw']->link('/index.php',$link_data));
			}
			else
			{
				$this->t->set_var(Array(
					'menutitle' => lang('Contributor Menu'),
					'begincomment' => '<!--',
					'endcomment' => '-->'
				));
			}

			$this->t->pfp('out','MainMenu');
			$this->common_ui->DisplayFooter();
		}

	}
?>
