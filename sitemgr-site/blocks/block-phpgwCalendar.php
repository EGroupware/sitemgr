<?php

	global $sitemgr_info;

	define(PHPGW_TEMPLATE_DIR,$sitemgr_info['phpgw_path'].'phpgwapi/templates/default');
	$calui = CreateObject('calendar.uicalendar');

	$appstore = $GLOBALS['phpgw_info']['flags']['currentapp'];
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'calendar';
	$content=$calui->mini_calendar(array('day'=>(int) date('d'),'month'=>(int) date('m'),'year'=>(int)date('Y'),'buttons'=>'both','link'=>'day'));
	//$content=$calui->print_day(array('day'=>(int) date('d'),'month'=>(int) date('m'),'year'=>(int)date('Y'),'link'=>'day'));

	$GLOBALS['phpgw_info']['flags']['currentapp'] = $appstore;

	unset($calui);

	$content = ereg_replace('cellspacing="7"','cellspacing="3"',$content);
	$content = ereg_replace('<font size="-2"><b>(..)</b></font>','<span class="minicalendarheader">\1</span>',$content);
	$title = 'Mini Calendar';
?>
