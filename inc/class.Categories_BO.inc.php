<?php
	class Categories_BO
	{
		var $so;
		
		function Categories_BO()
		{
			//all sitemgr BOs should be instantiated via a globalized Common_BO object,
			$this->so = CreateObject('sitemgr.Categories_SO', True);
		}

		function getCategoryOptionList()
		{
			$retval[] = array('value'=>0,'display'=>'[No Parent]');
			$list = $this->getPermittedCatWriteNested();
			foreach($list as $cat_id)
			{
				$cat = $this->getCategory($cat_id);
				$padding = str_pad('',12*$cat->depth,'&nbsp;');
				$retval[] = array('value'=>$cat->id, 'display'=>$padding.$cat->name);
			}
			return $retval;
		}

		function getPermittedCatReadNested($cat_id=False)
		{
			if (!$cat_id)
			{
				$cat_id = CURRENT_SITE_ID;
			}
			return $this->getPermittedCatNested($cat_id,'read');
		}
		function getPermittedCatWriteNested($cat_id=False)
		{
			if (!$cat_id)
			{
				$cat_id = CURRENT_SITE_ID;
			}
			return $this->getPermittedCatNested($cat_id,'write');
		}

		// Don't call this function directly.  Use above funcs.
		function getPermittedCatNested($cat_id,$check)
		{
			$root_list = $this->so->getChildrenIDList($cat_id);
			$permitted_list=array();
			if (is_array($root_list))
			{
				foreach($root_list as $root_cat)
				{
					if ($check=='read')
					{
						$permitted = $GLOBALS['Common_BO']->acl->can_read_category($root_cat);
					}
					elseif ($check=='write')
					{
						$permitted = $GLOBALS['Common_BO']->acl->can_write_category($root_cat);
					}
					else
					{
						die("Illegal call of function getPermittedCatNested");
					}

					if ($permitted)
					{
						$permitted_list[]=$root_cat;
					}
					//subcategories can be readable/writeable even when parent is not
					$sub_list = $this->getPermittedCatNested($root_cat,$check);
					if (is_array($sub_list) && count($sub_list)>0)
					{
						//array_push($permitted_list, $sub_list);
						$permitted_list=array_merge($permitted_list, $sub_list);
					}
				}
			}
//print_r($permitted_list);
			return $permitted_list;
		}

		//the next two functions do not recurse!
		function getPermittedCategoryIDWriteList($cat_id=False)
		{
			if (!$cat_id)
			{
				$cat_id = CURRENT_SITE_ID;
			}

			$full_list = $this->so->getChildrenIDList($cat_id);

			$permitted_list=array();
			if (is_array($full_list))
			{
				foreach($full_list as $item)
				{
					if ($GLOBALS['Common_BO']->acl->can_write_category($item))
					{
						$permitted_list[]=$item;
					}
				}
			}
			return $permitted_list;
		}

		function getPermittedCategoryIDReadList($cat_id=False)
		{
			if (!$cat_id)
			{
				$cat_id = CURRENT_SITE_ID;
			}
			$full_list = $this->so->getChildrenIDList($cat_id);
			
			$permitted_list=array();
			if (is_array($full_list))
			{
				reset($full_list);
				foreach($full_list as $item)
				{
					if ($GLOBALS['Common_BO']->acl->can_read_category($item))
					{
						$permitted_list[]=$item;
					}
				}
			}
			return $permitted_list;
		}

		function addCategory($name, $description, $parent=False)		
		{
			if (!$parent)
			{
				$parent = CURRENT_SITE_ID;
			}

			if ($GLOBALS['Common_BO']->acl->is_admin())
			{
				return $this->so->addCategory($name, $description, $parent);
			}
			else
			{
				return false;
			}
		}

		//$force for use by Sites_BO, since when we are editing the files list, the concept of admin of a current site does not apply
		//$frecurse also removes subcats
		function removeCategory($cat_id,$force=False,$recurse=False)
		{
			if ($GLOBALS['Common_BO']->acl->is_admin() || $force)
			{
				if ($recurse)
				{
					$children = $this->so->getChildrenIDList($cat_id);
					while (list($null,$subcat) = @each($children))
					{
						$this->removeCategory($subcat,$force,$recurse);
					}
				}
				/********************************************\
				* We have to remove the category, all the    *
				* associated pages, and all the associated   *
				* acl stuff too.  not to forget blocks       *
				\********************************************/
				$this->so->removeCategory($cat_id);
				$GLOBALS['Common_BO']->acl->remove_location($cat_id);
				$GLOBALS['Common_BO']->pages->removePagesInCat($cat_id,$force);
				$GLOBALS['Common_BO']->content->removeBlocksInPageOrCat($cat_id,0,$force);
				return True;
			}
		}

		function saveCategoryInfo($cat_id, $cat_name, $cat_description, $lang, $sort_order=0, $parent=False, $old_parent=False)
		{
			if (!$parent)
			{
				$parent = CURRENT_SITE_ID;
			}
			$cat_info = CreateObject('sitemgr.Category_SO', True);
			$cat_info->id = $cat_id;
			$cat_info->name = $cat_name;
			$cat_info->description = $cat_description;
			$cat_info->sort_order = $sort_order;
			$cat_info->parent = $parent;
			$cat_info->old_parent = $old_parent ? $old_parent : $parent;

			if ($GLOBALS['Common_BO']->acl->can_write_category($cat_id))
			{	
			  if ($this->so->saveCategory($cat_info));
			  {
			    if ($this->so->saveCategoryLang($cat_id, $cat_name, $cat_description, $lang))
			      {
				return True;
			      }
			    return false;
			  }
			  return false;
			}
			else
			{
				return false;
			}
		}

		function saveCategoryLang($cat_id, $cat_name, $cat_description, $lang)
		  {
		    if ($this->so->saveCategoryLang($cat_id, $cat_name, $cat_description, $lang))
		      {
			return True;
		      }
		    return false;
		  }
		
		function getCategory($cat_id,$lang=False)
		{
			if ($GLOBALS['Common_BO']->acl->can_read_category($cat_id))
			{
				return $this->so->getCategory($cat_id,$lang);
			}
			else
			{
				return false;
			}
		}

		function getCategoryancestorids($cat_id,$permittedonly=False)
		{
			$cat_id = $cat_id ? $cat_id : CURRENT_SITE_ID;
			$result = array();
			while ($cat_id != CURRENT_SITE_ID)
			{
				if (!$permittedonly || $GLOBALS['Common_BO']->acl->can_read_category($cat_id))
				{
					$result[] = $cat_id;
				}
				$cat_info = $this->so->getCategory($cat_id);
				$cat_id = $cat_info->parent;
			}
			return $result;
		}

		function getlangarrayforcategory($cat_id)
		  {
		    return $this->so->getlangarrayforcategory($cat_id);
		  }

		function saveCategoryPerms($cat_id, $group_access, $user_access)
		{
			if ($GLOBALS['Common_BO']->acl->is_admin())
			{
				$group_access=array_merge_recursive($GLOBALS['Common_BO']->acl->get_simple_group_list(),$group_access);
				$user_access=array_merge_recursive($GLOBALS['Common_BO']->acl->get_simple_user_list(),$user_access);
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
					$GLOBALS['Common_BO']->acl->grant_permissions($acctid, $cat_id, $can_read, $can_write);
				}
			}
			else
			{
				echo 'wth!';
			}
		}

		function saveCategoryPermsfromparent($cat_id)
		{
			$cat=$this->getCategory($cat_id);
			$parent=$cat->parent;
			if ($parent)
			{
				$GLOBALS['Common_BO']->acl->copy_permissions($parent,$cat_id);
			}
		}

		function applyCategoryPermstosubs($cat_id)
		{
			$sublist = $this->getPermittedCatWriteNested($cat_id);

			while (list(,$sub) = @each($sublist))
			{
				$GLOBALS['Common_BO']->acl->copy_permissions($cat_id,$sub);
			}
		}

		function removealllang($lang)
		{
			$this->so->removealllang($lang);
		}

		function migratealllang($oldlang,$newlang)
		{
			$this->so->migratealllang($oldlang,$newlang);
		}
	}
?>
