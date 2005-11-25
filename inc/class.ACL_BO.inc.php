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

	define('SITEMGR_ACL_IS_ADMIN',1);

	class ACL_BO
	{
		var $acct;
		var $acl;

		function ACL_BO()
		{
			$this->acct =& $GLOBALS['egw']->accounts;
			$this->acl  =& $GLOBALS['egw']->acl;
		}

		/**
		 * Checks if user is admin of the specified site
		 *
		 * @param int $site_id=0 id of the site, default to current site
		 * @return boolean
		 */
		function is_admin($site_id=0)
		{
			static $cache;
			
			if (!$site_id) $site_id = CURRENT_SITE_ID;
			
			if (isset($cache[$site_id])) return $cache[$site_id];
			
			return $cache[$site_id] = !!($this->acl->get_rights('L'.$site_id,'sitemgr') & SITEMGR_ACL_IS_ADMIN);
		}

		function set_adminlist($site_id,$account_list)
		{
			$this->remove_location($site_id);
			while (list($null,$account_id) = @each($account_list))
			{
				$this->acl->add_repository('sitemgr','L'.$site_id,$account_id,SITEMGR_ACL_IS_ADMIN);
			}
		}

		function remove_location($category_id)
		{
			// Used when a category_id is deleted
			$this->acl->delete_repository('sitemgr','L'.$category_id,false);
		}

		function copy_permissions($fromcat,$tocat)
		{
			$this->remove_location($tocat);
			
			foreach($this->acl->get_all_rights('L'.$fromcat,'sitemgr') as $account_id => $right)
			{
				$this->add_repository('sitemgr','L'.$tocat,$account_id,$right);
			}
		}

		function grant_permissions($user, $category_id, $can_read, $can_write)
		{
			$rights = 0;
			if($can_read)
			{
				$rights = EGW_ACL_READ;
			}
			if($can_write)
			{
				$rights = ($rights | EGW_ACL_ADD);
			}

			if ($rights == 0)
			{
				return $this->acl->delete_repository('sitemgr','L'.$category_id,$user);
			}
			else
			{
				return $this->acl->add_repository('sitemgr','L'.$category_id,$user,$rights);
			}
		}

		function get_user_permission_list($category_id)
		{
			return $this->get_permission_list($category_id, 'accounts');
		}

		function get_group_permission_list($category_id)
		{
			return $this->get_permission_list($category_id, 'groups');
		}

		function get_permission_list($category_id, $acct_type='both')
		{
			static $cache;
			
			if (isset($cache[$category_id.'-'.$acct_type])) return $cache[$category_id.'-'.$acct_type];

			$permissions = Array();
			foreach($this->acct->get_list($acct_type) as $user)
			{
				$permissions[$user['account_id']] = $this->acl->get_specific_rights_for_account($user['account_id'],'L'.$category_id,'sitemgr');
			}
			return $cache[$category_id.'-'.$acct_type] = $permissions;
		}

		/**
		 * retrives the ACL for a given category
		 *
		 * for the toplevel site_category, there is only an implicit ACL: everybody can read it, site-Admins can change it
		 *
		 * @param int $category_id
		 * @return int EGW_ACL_READ | EGW_ACL_ADD depending on the rights
		 */
		function category_acl($category_id)
		{
			static $cache;
			
			if (isset($cache[$category_id])) return $cache[$category_id];
			
			if ($category_id == CURRENT_SITE_ID)
			{
				$cache[$category_id] = $this->is_admin() ? EGW_ACL_READ | EGW_ACL_ADD : 0;
			}
			else
			{
				$cache[$category_id] = $this->acl->get_rights('L'.$category_id,'sitemgr');
			}
			return $cache[$category_id];
		}
			
		/**
		 * checks if user can read a given category
		 *
		 * for the toplevel site_category, there is only an implicit ACL: everybody can read it, site-Admins can change it
		 *
		 * @param int $category_id
		 * @return boolean
		 */
		function can_read_category($category_id)
		{
			return !!($this->category_acl($category_id) & EGW_ACL_READ);
		}

		/**
		 * checks if user can write / change a given category
		 *
		 * for the toplevel site_category, there is only an implicit ACL: everybody can read it, site-Admins can change it
		 *
		 * @param int $category_id
		 * @return boolean
		 */
		function can_write_category($category_id)
		{
			if ($this->is_admin())
			{
				return true;
			}
			return !!($this->category_acl($category_id) & EGW_ACL_ADD);
		}

		function get_group_list()
		{
			return $this->acct->get_list('groups');
		}

		function get_simple_group_list()
		{
			return $this->get_simple_list('groups');
		}

		function get_simple_list($acct_type='')
		{
			$accounts=array();
			foreach($this->acct->get_list($acct_type) as $data)
			{
				$accounts['i'.$data['account_id']] = array();
			}
			return $accounts;
		}
				
		function get_simple_user_list()
		{
			return $this->get_simple_list('accounts');
		}

		function get_user_list()
		{
			return $this->acct->get_list('accounts');
		}
	}
?>
