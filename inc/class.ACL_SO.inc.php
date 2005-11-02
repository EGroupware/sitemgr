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

class ACL_SO
{
	/**
	 * Get rights for location, including the rights by group-membership
	 *
	 * @param string $location
	 * @return int
	 */
	function get_permission($location)
	{
		return $GLOBALS['egw']->acl->get_rights($location,'sitemgr');
	}

	/**
	 * Get rights for location and a specified account
	 *
	 * @param int $account_id
	 * @param string $location
	 * @return int
	 */
	function get_rights($account_id, $location)
	{
		return $GLOBALS['egw']->acl->get_specific_rights_for_account($account_id,$location,'sitemgr');
	}

	/**
	 * copy all rights from one location to an other one
	 *
	 * @param string $fromlocation 
	 * @param string $tolocation
	 */
	function copy_rights($fromlocation,$tolocation)
	{
		foreach($GLOBALS['egw']->acl->get_all_rights($fromlocation,'sitemgr') as $account_id => $right)
		{
			$this->add_repository('sitemgr',$tolocation,$account_id,$right);
		}
	}

	function remove_location($location)
	{
		$GLOBALS['egw']->acl->delete_repository('sitemgr', $location,false);
	}
}
