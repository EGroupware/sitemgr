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
		var $t;
		var $acl;
		var $public_functions = array
		(
			'DisplayMenu'  => True
		);
															            
		function MainMenu_UI()
		{
			$this->t = $GLOBALS['phpgw']->template;
			$this->acl = CreateObject('sitemgr.ACL_BO');
		}

		function DisplayMenu()
		{
			$common_ui = CreateObject('sitemgr.Common_UI',True);
			$common_ui->DisplayHeader();

			$this->t->set_file('MainMenu','mainmenu.tpl');
			if ($this->acl->is_admin())
			{
				$this->t->set_var(Array('menutitle' => lang('Administrative Menu'),
							'lang_configure' => lang('Configure SiteMgr'),
							'lang_check' => lang('check here after every upgrade'),
							'lang_editheadfoot' => lang('Edit Site Header and Footer'),
							'lang_managecat' => lang('Manage Categories'),
							'lang_manageblocks' => lang('Manage Blocks')));
				$catbo = CreateObject('sitemgr.Categories_BO');
				if ($catbo->needUpdateCategories())
				{
					$updatemsg = $catbo->updateCategories();
					$updatemsg = "\n".'<br><b>' . lang('Updating to new category system') . ':</b><br>'.
						$updatemsg.'<br><b>' . lang('Done') . '</b><br>';
					$this->t->set_var('updatecats',$updatemsg);
				}
				else
				{
					$this->t->set_var('updatecats','');
				}
				unset($catbo);
			}
			else
			{
				$this->t->set_var('menutitle',lang('Contributor Menu'));
			}

			$this->t->set_var('managepage',
				$GLOBALS['phpgw']->link('/index.php',
				'menuaction=sitemgr.contributor_ManagePage_UI._managePage')
			);

			$this->t->set_var('managetranslations',
				$GLOBALS['phpgw']->link('/index.php',
				'menuaction=sitemgr.ManageTranslations_UI._manageTranslations')
			);
			$this->t->set_var(Array('lang_managepage' => lang('Manage Pages'),
						'lang_managetranslations' => lang('Manage Translations')));

			if ($this->acl->is_admin())
			{
				$this->t->set_var('managecategory',
					$GLOBALS['phpgw']->link('/index.php',
					'menuaction=sitemgr.Admin_ManageCategories_UI._manageCategories')
				);
				$this->t->set_var('manageblocks',
					$GLOBALS['phpgw']->link('/index.php',
					'menuaction=sitemgr.ManageBlocks_UI._manageBlocks')
				);
				$this->t->set_var('headerandfooter',
					$GLOBALS['phpgw']->link('/index.php',
					'menuaction=sitemgr.admin_ManageSiteContent_UI._editHeaderAndFooter')
				);
				$this->t->set_var('setup',
					$GLOBALS['phpgw']->link('/index.php',
					'menuaction=sitemgr.Common_UI.DisplayPrefs')
				);
			}
			else
			{
				$this->t->set_var('begincomment','<!--');
				$this->t->set_var('endcomment','-->');
			}
			$this->t->pfp('out','MainMenu');
			$common_ui->DisplayFooter();
		}

	}	
?>
