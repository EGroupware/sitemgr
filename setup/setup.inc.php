<?php
	/**************************************************************************\
	* phpGroupWare - Notes                                                     *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$setup_info['sitemgr']['name']      = 'sitemgr';
	$setup_info['sitemgr']['title']     = 'Web Content Manager';
	$setup_info['sitemgr']['version']   = '0.9.13.001';
	$setup_info['sitemgr']['app_order'] = 8;
	$setup_info['sitemgr']['tables']    = array('phpgw_sitemgr_pages','phpgw_sitemgr_categories','phpgw_sitemgr_blocks','phpgw_sitemgr_preferences');
	$setup_info['sitemgr']['enable']    = 1;

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['sitemgr']['hooks'][] = 'preferences';
	$setup_info['sitemgr']['hooks'][] = 'about';
	$setup_info['sitemgr']['hooks'][] = 'admin';

	/* Dependacies for this app to work */
	$setup_info['sitemgr']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.11','0.9.12','0.9.13','0.9.14')
	);
?>
