<?php
        /**************************************************************************\
        * phpGroupWare - Web Content Manager                                       *
        * http://www.phpgroupware.org                                              *
        * -----------------------------------------------                          *
        *  This program is free software; you can redistribute it and/or modify it *
        *  under the terms of the GNU General Public License as published by the   *
        *  Free Software Foundation; either version 2 of the License, or (at your  *
        *  option) any later version.                                              *
        \**************************************************************************/
        $GLOBALS['phpgw_info']['flags'] = array
        (
                'currentapp' => 'sitemgr',
                'noheader'   => True,
                'nonavbar'   => True,
                'noapi'      => False
        );
        include('../header.inc.php');

	$MainMenu = CreateObject('sitemgr.MainMenu_UI');
	$MainMenu->DisplayMenu();
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
