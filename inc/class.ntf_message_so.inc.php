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

	class NtfMessages_SO
	{
		var $db;

		function NtfMessages_SO()
		{
			$this->db = clone($GLOBALS['egw']->db);
			$this->db->app = 'sitemgr_module_notify';  // as we run as sitemgr !
			$this->messages_table = 'phpgw_sitemgr_notify_messages';
		}

		function list_languages($site_id)
		{
			$this->db->select($this->messages_table,array('language'),
				'site_id='.$site_id,__LINE__,__FILE__);

			while($this->db->next_record())
			{
				$result[] = $this->db->f('language');
			}
			return $result;
		}

	}

