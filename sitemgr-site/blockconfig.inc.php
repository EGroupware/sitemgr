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
		'view' => 0        // 0=everybody, 1=phpgw users 2=admins 3=anonymous
	);
	*/


        $blocks[]=array(
		'title' => lang('Choose language'),
		'blockfile' => 'block-Choose_lang.php',
		'position' => 'l',
		'refresh' => 0,
		'view' => 0
	);

	$blocks[]=array(
		'title' => lang('Root Site Index'),
		'position' => 'l',
		'blockfile' => 'block-SiteIndex.php',
		'view' => 0
	);
	$blocks[]=array(
		'title' => lang('Table of Contents'),
		'position' => 'r',
		'blockfile' => 'block-Table_of_Contents.php',
		'view' => 0
	);
	$blocks[]=array(
		'title' => lang('Your Calendar'),
		'position' => 'l',
		'blockfile' => 'block-phpgwCalendar.php',
		'view' => 2
	);
	$blocks[]=array(
		'title' => lang('Login'),
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
		'title' => lang('Google Search'),
		'position' => 'r',
		'blockfile' => 'block-GoogleSearch.php',
		'view' => 0
	);
	/*
	$blocks[]=array(
		'bkey' => '',
		'title' => lang('Headlines'),
		'position' => 'r',
		'active' => 1,
		'refresh' => 0,
		'blockfile' => 'block-Msn.php',
		'view' => 0 
	);
	*/
	$blocks[]=array(
		'bkey' => 'admin',
		'title' => lang('Administration'),
		'content' => '&nbsp;&nbsp;<strong><big>&middot;</big></strong><a href="'.phpgw_link('/index.php','menuaction=sitemgr.MainMenu_UI.DisplayMenu').'">' . lang('Content Manager') . '</a>',
		'position' => 'l',
		'active' => 1,
		'refresh' => 0,
		'time' => 985591188,
		'blockfile' => '',
		'view' => 2
	);
		
	$blocks[]=array(
		'title' => lang('Amazon'),
		'position' => 'r',
		'refresh' => 3600,
		'blockfile' => 'block-Amazon.php',
		'view' => 0
	);
