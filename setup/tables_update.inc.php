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
?>
