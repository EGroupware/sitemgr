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
	$setup_info['sitemgr']['title']     = 'SiteMgr Web Content Manager'; // note to mr_e: left for 0.9.14, need to go into the lang-file for HEAD
	$setup_info['sitemgr']['version']   = '0.9.14.005';
	$setup_info['sitemgr']['app_order'] = 8;
	$setup_info['sitemgr']['tables']    = array('phpgw_sitemgr_pages','phpgw_sitemgr_pages_lang','phpgw_sitemgr_categories_lang','phpgw_sitemgr_blocks','phpgw_sitemgr_preferences');
	$setup_info['sitemgr']['enable']    = 1;
	$setup_info['sitemgr']['description'] =
		'<u>Overview</u><br>
		This program will generate a dynamic web site with discrete sections that various
		phpGroupWare users may edit, if the administrator gives them permission to do so.
		In effect, the generated website can have sections which independent departments are
		in charge of maintaining.  The site administrator can choose a theme and create headers,
		footers, and sidebars to enforce a sitewide look and feel.  Site sections can be viewable
		public (viewable by anonymous users) or private (viewable by specified users and groups only).
		<p>
		<u>Background</u><br>
		Team 10 in the UC Irvine Systems Design Course, ICS 125, chose this as their project.
		Seek3r served as the project\'s "customer" and the team wrote extensive requirements and
		design documents followed by the actual coding of the project.  The course is ten weeks
		long, but coding doesn\'t start until part-way through week 6, so version 1.0 of sitemgr
		was programmed in an intensive 3 weeks.';
	
	$setup_info['sitemgr']['author'] = array(
		array (
			'name'  => 'Tina Alinaghian',
			'email' => 'tina@checkyour6.net'
		), array(
			'name'  => 'Austin Lee',
			'email' => 'anhjah@hotmail.com'
		), array(
			'name'  => 'Siu Leung',
			'email' => 'rurouni_master@hotmail.com'
		), array(
			'name'  => 'Fang Ming Lo',
			'email' => 'flo@uci.edu'
		), array(
			'name'  => 'Patrick Walsh',
			'email' => 'mr_e@phpgroupware.org'
		));
	
	$setup_info['sitemgr']['maintainer'] = array(
		'name'  => 'Patrick Walsh',
		'email' => 'mr_e@phpgroupware.org'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['sitemgr']['hooks'][] = 'preferences';
	$setup_info['sitemgr']['hooks'][] = 'about';
	$setup_info['sitemgr']['hooks'][] = 'admin';

	/* Dependacies for this app to work */
	$setup_info['sitemgr']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.11','0.9.12','0.9.13','0.9.14','0.9.15')
	);
?>
