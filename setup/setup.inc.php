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
	$setup_info['sitemgr']['version']   = '0.9.14.002';
	$setup_info['sitemgr']['app_order'] = 8;
	$setup_info['sitemgr']['tables']    = array('phpgw_sitemgr_pages','phpgw_sitemgr_blocks','phpgw_sitemgr_preferences');
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
	$setup_info['sitemgr']['author'] =
		'<u>ICS 125 Team 10</u>:<br>
		Tina Alinaghian (tina -AT- checkyour6.net)<br>
		Austin Lee (anhjah -AT- hotmail.com)<br>
		Siu Leung (rurouni_master -AT- hotmail.com)<br>
		Fang Ming Lo (flo -AT- uci.edu)<br>
		Patrick Walsh (mr_e -AT- phpgroupware.org)<br>
		<u>Professor</u>:<br>
		Hadar Ziv (profziv -AT- aol.com)<br>
		<u>TA</u>:<br>
		Arijit Ghosh (arijitg -AT- uci.edu)';
	$setup_info['sitemgr']['maintainer'] = 'Patrick Walsh';
	$setup_info['sitemgr']['maintainer_email'] = 'mr_e@phpgroupware.org';

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
