<?php
	$blocks = array();
	/*
	$blocks[]=array(
		'bkey' => '',      // admin, user, or blank, but leave it blank
		'title' => '',     // block title
		'content' => '',
		'url' => '',
		'position' => 'l', // l for left, r for right, c for center
		'active' => 1,     // should always be 1
		'refresh' => 0,
		'time' => '',
		'blanguage' => '',
		'blockfile' => 'block-Amazon.php',
		'view' => 0        // 0=everybody, 1=admins 2=phpgw users 3=anonymous
	);
	*/
	$blocks[]=array(
		'title' => 'Site Index',
		'position' => 'l',
		'blockfile' => 'block-SiteIndex.php',
		'view' => 0
	);
	$blocks[]=array(
		'title' => 'Table of Contents',
		'position' => 'l',
		'blockfile' => 'block-Table_of_Contents.php',
		'view' => 0
	);
	$blocks[]=array(
		'title' => 'Your Calendar',
		'position' => 'l',
		'blockfile' => 'block-phpgwCalendar.php',
		'view' => 2
	);
	$blocks[]=array(
		'title' => 'Login',
		'position' => 'r',
		'blockfile' => 'block-Login.php',
		'view' => 3
	);
	/*
	$blocks[]=array(
		'title' => 'Stock Quotes',     // block title
		'position' => 'l', // l for left, r for right, c for center
		'active' => 1,     // should always be 1
		'blockfile' => 'block-ystock.php',
		'view' => 0        // 0=everybody, 1=admins 2=phpgw users
	);
	*/
	$blocks[]=array(
		'title' => 'Google Search',
		'position' => 'r',
		'blockfile' => 'block-GoogleSearch.php',
		'view' => 0
	);
	/*
	$blocks[]=array(
		'bkey' => '',
		'title' => 'Headlines',
		'position' => 'r',
		'active' => 1,
		'refresh' => 0,
		'blockfile' => 'block-Msn.php',
		'view' => 0 
	);
	*/
	$blocks[]=array(
		'bkey' => 'admin',
		'title' => 'Administration',
		'content' => '&nbsp;&nbsp;<strong><big>&middot;</big></strong><a href="'.phpgw_link('/index.php','menuaction=sitemgr.MainMenu_UI.DisplayMenu').'">Content Manager</a>',
		'position' => 'l',
		'active' => 1,
		'refresh' => 0,
		'time' => 985591188,
		'blockfile' => '',
		'view' => 2
	);
		
	$blocks[]=array(
		'title' => 'Amazon',
		'position' => 'r',
		'refresh' => 3600,
		'blockfile' => 'block-Amazon.php',
		'view' => 0
	);
