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

		return $setup_info['sitemgr']['currentver'];
	}
		
?>
