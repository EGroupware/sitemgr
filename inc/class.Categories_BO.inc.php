<?php
	class Categories_BO
	{
		var $so;
		var $acl;
		
		function Categories_BO()
		{
			$this->so = CreateObject('sitemgr.Categories_SO', True);
			$this->acl = CreateObject('sitemgr.ACL_BO');
		}

		function getPermittedCategoryIDWriteList()
		{
			$full_list = $this->so->getFullcategoryIDList();
			$permitted_list=array();
			if (is_array($full_list))
			{
				reset($full_list);
				foreach($full_list as $item)
				{
					if ($this->acl->can_write_category($item))
					{
						$permitted_list[]=$item;
					}
				}
			}
			return $permitted_list;
		}

		function getPermittedCategoryIDReadList()
		{
			$full_list = $this->so->getFullcategoryIDList();
			$permitted_list=array();
			if (is_array($full_list))
			{
				reset($full_list);
				foreach($full_list as $item)
				{
					if ($this->acl->can_read_category($item))
					{
						$permitted_list[]=$item;
					}
				}
			}
			return $permitted_list;
		}

		function addCategory($name, $description)		
		{
			if ($this->acl->is_admin())
			{
				return $this->so->addCategory($name, $description);
			}
			else
			{
				return false;
			}
		}

		function removeCategory($cat_id)
		{
			if ($this->acl->is_admin())
			{
				/********************************************\
				* We have to remove the category, all the    *
				* associated pages, and all the associated   *
				* acl stuff too.                             *
				\********************************************/
				$this->so->removeCategory($cat_id);
				$this->acl->remove_location($cat_id);
				$pages_so = CreateObject('sitemgr.Pages_SO');
				$pages_so->removePagesInCat($cat_id);
				return True;
			}
		}

		function saveCategoryInfo($cat_id, $cat_name, $cat_description, $sort_order=0)
		{
			$cat_info = CreateObject('sitemgr.Category_SO', True);
			$cat_info->id = $cat_id;
			$cat_info->name = $cat_name;
			$cat_info->description = $cat_description;
			$cat_info->sort_order = $sort_order;

			if ($this->acl->can_read_category($cat_id))
			{	
				if($this->so->saveCategory($cat_info))
				{
					return True;
				}
				return False;
			}
			else
			{
				return false;
			}
		}

		function getCategory($cat_id)
		{
			if ($this->acl->can_read_category($cat_id))
			{
				return $this->so->getCategory($cat_id);
			}
			else
			{
				return false;
			}
		}
		
		function saveCategoryPerms($cat_id, $group_access, $user_access)
		{
			if ($this->acl->is_admin())
			{
				$group_access=array_merge_recursive($this->acl->get_simple_group_list(),$group_access);
				$user_access=array_merge_recursive($this->acl->get_simple_user_list(),$user_access);
				$this->saveCatPermsGeneric($cat_id, $group_access);
				$this->saveCatPermsGeneric($cat_id, $user_access);
				return true;
			}
			else
			{
				return false;
			}
		}

		function saveCatPermsGeneric($cat_id, $user_access)
		{
			if (is_array($user_access))
			{
				reset($user_access);
				while (list($acctid, $perm_array) = each($user_access))
				{
					if (substr($acctid,0,1))
					{
						$acctid = (int) substr($acctid,1);
					}
					if (is_array($perm_array))
					{
						reset($perm_array);
						$can_read = 0;
						$can_write = 0;
						while(list($permtype, $permvalue) = each($perm_array))
						{
							switch($permtype)
							{
								case 'read':
									$can_read = true;
									break;
								case 'write':
									$can_write = true;
									break;
								default:
									echo 'hmmmmmm: ' . $permtype . '<br>';
							}
						}
					}
					$this->acl->grant_permissions($acctid, $cat_id, $can_read, $can_write);
				}
			}
			else
			{
				echo 'wth!';
			}
		}
	}
?>
