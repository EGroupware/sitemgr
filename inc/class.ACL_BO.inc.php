<?php
	/***************************************************************************\
	* phpGroupWare - Web Content Manager                                        *
	* http://www.phpgroupware.org                                               *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/

	class ACL_BO
	{
		var $acct;
		var $acl;
		var $acl_so;
		var $pages_so;
		var $logged_in_user;

		function ACL_BO()
		{
			$this->logged_in_user = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->acct = CreateObject('phpgwapi.accounts',$this->logged_in_user);
			$this->acl = CreateObject('phpgwapi.acl',$this->logged_in_user);
			$this->pages_so = CreateObject('sitemgr.Pages_SO');
			$this->acl_so = CreateObject('sitemgr.ACL_SO');
		}

		function can_read_page($page_id)
		{
			/*!
			$acl->check_specific calls $acl->get_specific_rights which is for this->account_id
			*/
			$page = $this->pages_so->getPage($page_id);
			if ($page)
			{
				if ($this->is_admin())
				{
					return true;
				}
				$category_id = $page->cat_id;
				//$this->acl = CreateObject('phpgwapi.acl',$this->logged_in_user);
				//return ($this->acl->get_rights('L'.$category_id,'sitemgr') & PHPGW_ACL_READ);
				return ($this->acl_so->get_permission('L'.$category_id) & PHPGW_ACL_READ);
			}
			else
			{
				return false;
			}
		}

		function is_admin()
		{
			if($GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				define('HAS_ADMIN_RIGHTS',1);
			}
			if(HAS_ADMIN_RIGHTS == 1)
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		function remove_location($category_id)
		{
			// Used when a category_id is deleted
			$this->acl_so->remove_location('L'.$category_id);
		}

		function copy_permissions($fromcat,$tocat)
		{
			$this->remove_location($tocat);
			$this->acl_so->copy_rights('L'.$fromcat,'L'.$tocat);
		}

		function grant_permissions($user, $category_id, $can_read, $can_write)
		{
			$rights = 0;
			if($can_read)
			{
				$rights = PHPGW_ACL_READ;
			}
			if($can_write)
			{
				$rights = ($rights | PHPGW_ACL_ADD);
			}

			$this->acl = CreateObject('phpgwapi.acl',$user);
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

		function get_permission_list($category_id, $acct_type='')
		{
			/* 
			   Though this is not the place for making database lookups, particularly
			   ones that look for things in the phpgwapi tables, the stupid get_rights
			   and get_specific_rights and other lookup functions DON'T WORK.
			*/
			$users = $this->acct->get_list($acct_type);

			$permissions = Array();

			reset($users);
			while(list($k,$v) = each($users))
			{
				$account_id = $v['account_id'];
				//unset($this->acl);
				//$this->acl = CreateObject('phpgwapi.acl',$account_id);
				//$rights = $this->acl->get_specific_rights('L'.$category_id,'sitemgr');
				$rights = $this->acl_so->get_rights($account_id, 'L'.$category_id);
				$permissions[] = array('account_id'=>$account_id, 'rights'=>$rights);
			}
			return $permissions;
		}

		function can_read_category($category_id)
		{
			if ($this->is_admin())
			{
				return true;
			}
			else
			{
				//$this->acl = CreateObject('phpgwapi.acl',$this->logged_in_user);
				//return ($this->acl->get_rights('L'.$category_id,'sitemgr') & PHPGW_ACL_READ);
				return ($this->acl_so->get_permission('L'.$category_id) & PHPGW_ACL_READ);
			}
		}

		function can_write_category($category_id)
		{
			if ($this->is_admin())
			{
				return true;
			}
			else
			{
				//$this->acl = CreateObject('phpgwapi.acl',$this->logged_in_user);
				//return ($this->acl->get_rights($account_id,'L'.$category_id) & PHPGW_ACL_ADD);
				// if category_id = 0, we are in site-wide scope, and only admin can add content
				return $category_id ? 
					($this->acl_so->get_permission('L'.$category_id) & PHPGW_ACL_ADD) :
					$this->is_admin();
			}
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
			$full_details = $this->acct->get_list($acct_type);
			reset($full_details);
			$group=array();
			while(list($k,$v) = each($full_details))
			{
				$group['i'.$v['account_id']] = array();
			}
			return $group;
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
