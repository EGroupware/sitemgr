<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */
include_once(EGW_INCLUDE_ROOT . '/sitemgr/inc/class.generic_list_so.inc.php');
$GLOBALS['egw_info']['flags']['included_classes']['generic_list_so'] = True;

	class Notifications_SO extends generic_list_so
	{

		function Notifications_SO($site_id='')
		{
			$this->generic_list_so('sitemgr', 'phpgw_sitemgr_notifications', 
			'Notification_SO', 'notification_id', 'site_id',$site_id);
			
		}

	}

