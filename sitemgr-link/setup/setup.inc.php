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

	$setup_info['sitemgr-link']['name']      = 'sitemgr-link';
	$setup_info['sitemgr-link']['title']     = 'SiteMgr Public Web Site';
	$setup_info['sitemgr-link']['version']   = '0.9.13.001';
	$setup_info['sitemgr-link']['app_order'] = 9;
	$setup_info['sitemgr-link']['tables']    = array();
	$setup_info['sitemgr-link']['enable']    = 1;

	/* Dependacies for this app to work */
	$setup_info['sitemgr-link']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.11','0.9.12','0.9.13','0.9.14','0.9.15')
	);
	$setup_info['sitemgr-link']['depends'][] = array(
		'appname' => 'sitemgr',
		'versions' => array('0.9.13','0.9.13.001','0.9.14','0.9.14.001')
	);
?>
