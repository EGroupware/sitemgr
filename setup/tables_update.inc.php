<?php
	/**************************************************************************\
	* phpGroupWare                                                             *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$test[] = '0.9.13.001';
	function sitemgr_upgrade0_9_13_001()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.14.001';

		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_pages',
			'sort_order',array('type'=>int, 'precision'=>4));
		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_categories',
			'sort_order',array('type'=>int, 'precision'=>4));

		return $setup_info['sitemgr']['currentver'];
	}
	$test[] = '0.9.14.001';
	function sitemgr_upgrade0_9_14_001()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.14.002';

		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_pages',
			'hide_page',array('type'=>int, 'precision'=>4));
		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_categories',
			'parent',array('type'=>int, 'precision'=>4));

		return $setup_info['sitemgr']['currentver'];
	}
	$test[] = '0.9.14.002';
	function sitemgr_upgrade0_9_14_002()
	{
		/******************************************************\
		* Purpose of this upgrade is to switch to phpgw        *
		* categories from the db categories.  So the           *
		* sql data will be moved to the cat stuff and the sql  *
		* categories table will be deleted.                    *
		*                                                      *
		* It would be nice if we could just run an UPDATE sql  *
		* query, but then you run the risk of this scenario:   *
		* old_cat_id = 5, new_cat_id = 2 --> update all pages  *
		* old_cat_id = 2, new_cat_id = 3 --> update all pages  *
		*  now all old_cat_id 5 pages are cat_id 3....         *
		\******************************************************/
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.14.003';

		//$cat_db_so = CreateObject('sitemgr.Categories_db_SO');

		//$cat_db_so->convert_to_phpgwapi();

		// Finally, delete the categories table
		//$phpgw_setup->oProc->DropTable('phpgw_sitemgr_categories');
		
		// Turns out that convert_to_phpgwapi() must be run under 
		// the normal phpgw environment and not the setup env.
		// This upgrade routine has been moved to the main body 
		// of code.

		return $setup_info['sitemgr']['currentver'];
	}

	$test[] = '0.9.14.003';
	function sitemgr_upgrade0_9_14_003()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.14.004';

		if (file_exists(PHPGW_SERVER_ROOT .'/sitemgr/setup/sitemgr_sitelang'))
		{
			$langfile = file(PHPGW_SERVER_ROOT . '/sitemgr/setup/sitemgr_sitelang');
			$lang = rtrim($langfile[0]);
			if (strlen($lang) != 2)
			{
				$lang = "en";
			}
		  }
		else
		  {
		    $lang = "en";
		  }

		echo 'Updating sitemgr to a multilingual architecture with existing site language ' . $lang;

		$db2 = $phpgw_setup->db;

		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_sitemgr_pages_lang',
			array(
				'fd' => array(
					'page_id' => array('type' => 'auto', 'nullable' => false),
					'lang' => array('type' => 'varchar', 'precision' => 2, 
						'nullable' => false),
					'title' => array('type' => 'varchar', 'precision' => 256),
					'subtitle' => array('type' => 'varchar', 
						'precision' => 256),
					'content' => array('type' => 'text')
				),
				'pk' => array('page_id','lang'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_sitemgr_categories_lang',
			array(
				'fd' => array(
					'cat_id' => array('type' => 'auto', 'nullable' => false),
					'lang' => array('type' => 'varchar', 'precision' => 2, 
						'nullable' => false),
					'name' => array('type' => 'varchar', 'precision' => 100),
					'description' => array('type' => 'varchar', 
						'precision' => 256)
				),
				'pk' => array('cat_id','lang'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		$GLOBALS['phpgw_setup']->oProc->query("select * from phpgw_categories where cat_appname='sitemgr'");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$cat_id = $GLOBALS['phpgw_setup']->oProc->f('cat_id');
			$name = $GLOBALS['phpgw_setup']->oProc->f('cat_name');
			$description = $GLOBALS['phpgw_setup']->oProc->f('cat_description');
			$db2->query("INSERT INTO phpgw_sitemgr_categories_lang (cat_id, lang, name, description) VALUES ($cat_id, '$lang', '$name', '$description')");
		}

		$GLOBALS['phpgw_setup']->oProc->query("select * from phpgw_sitemgr_pages");
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$page_id = $GLOBALS['phpgw_setup']->oProc->f('page_id');
			$title = $GLOBALS['phpgw_setup']->oProc->f('title');
			$subtitle = $GLOBALS['phpgw_setup']->oProc->f('subtitle');
			$content =  $GLOBALS['phpgw_setup']->oProc->f('content');
		      
			$db2->query("INSERT INTO phpgw_sitemgr_pages_lang (page_id, lang, title, subtitle, content) VALUES ($page_id, '$lang', '$title', '$subtitle', '$content')");
		}
	  
		$newtbldef = array(
			'fd' => array(
				'page_id' => array('type' => 'auto', 'nullable' => false),
				'cat_id' => array('type' => 'int', 'precision' => 4),
				'sort_order' => array('type' => 'int', 'precision' => 4),
				'hide_page' => array('type' => 'int', 'precision' => 4),
				'name' => array('type' => 'varchar', 'precision' => 100),
				'subtitle' => array('type' => 'varchar', 'precision' => 256),
				'content' => array('type' => 'text')
			),
			'pk' => array('page_id'),
			'fk' => array(),
			'ix' => array('cat_id'),
			'uc' => array()
		);
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_sitemgr_pages',
			$newtbldef,'title');
		$newtbldef = array(
			'fd' => array(
				'page_id' => array('type' => 'auto', 'nullable' => false),
				'cat_id' => array('type' => 'int', 'precision' => 4),
				'sort_order' => array('type' => 'int', 'precision' => 4),
				'hide_page' => array('type' => 'int', 'precision' => 4),
				'name' => array('type' => 'varchar', 'precision' => 100),
				'content' => array('type' => 'text')
			),
			'pk' => array('page_id'),
			'fk' => array(),
			'ix' => array('cat_id'),
			'uc' => array()
		);
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_sitemgr_pages',
			$newtbldef,'subtitle');
		$newtbldef = array(
			'fd' => array(
				'page_id' => array('type' => 'auto', 'nullable' => false),
				'cat_id' => array('type' => 'int', 'precision' => 4),
				'sort_order' => array('type' => 'int', 'precision' => 4),
				'hide_page' => array('type' => 'int', 'precision' => 4),
				'name' => array('type' => 'varchar', 'precision' => 100)
			),
			'pk' => array('page_id'),
			'fk' => array(),
			'ix' => array('cat_id'),
			'uc' => array()
		);
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_sitemgr_pages',
			$newtbldef,'content');

		// try to set the sitelanguages preference. 
		// if it already exists do nothing
		$db2->query("SELECT pref_id FROM phpgw_sitemgr_preferences WHERE name='sitelanguages'");
		if ($db2->next_record())
		{
		}
		else
		{
			$db2->query("INSERT INTO phpgw_sitemgr_preferences (name, value) VALUES ('sitelanguages', '$lang')");
		}

		//internationalize the names for site-name, header and footer 
		//preferences
		$prefstochange = array('sitemgr-site-name','siteheader','sitefooter');
	  
		foreach ($prefstochange as $oldprefname)
		{
			$newprefname = $oldprefname . '-' . $lang;
			//echo "DEBUG: Changing $oldprefname to $newprefname. ";
			$db2->query("UPDATE phpgw_sitemgr_preferences SET name='$newprefname' where name='$oldprefname'");
		}

		return $setup_info['sitemgr']['currentver'];	  
	}	

	$test[] = '0.9.14.004';
	function sitemgr_upgrade0_9_14_004()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.14.005';

		echo 'Fixing column names.';
		$phpgw_setup->oProc->RenameColumn('phpgw_sitemgr_blocks', 'position', 'pos');

		return $setup_info['sitemgr']['currentver'];                             
	}

	$test[] = '0.9.14.005';
	function sitemgr_upgrade0_9_14_005()
	{
		global $setup_info,$phpgw_setup;
		$setup_info['sitemgr']['currentver'] = '0.9.14.006';

		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_blocks',
			'description', array('type' => 'varchar', 'precision' => 256));
		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_blocks',
			'view', array('type' => 'int', 'precision' => 4));
		$phpgw_setup->oProc->AddColumn('phpgw_sitemgr_blocks',
			'actif', array('type' => 'int', 'precision' => 2));
		return $setup_info['sitemgr']['currentver'];
	}
?>
