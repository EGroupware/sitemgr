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

	class Admin_ManageCategories_UI
   	{
		var $cat;
		var $cat_id;
		var $cat_list;
		var $cat_bo;
		var $acl;
		var $t;
		var $preferenceso;
		var $sitelanguages;
		
		var $public_functions = array
		(
			'_manageCategories' => True
		);
			
		function Admin_ManageCategories_UI()
		{
			$this->t = $GLOBALS['phpgw']->template;
			$this->cat_bo = CreateObject('sitemgr.Categories_BO', True);
			$this->acl = CreateObject('sitemgr.ACL_BO', True);
			$this->preferenceso = CreateObject('sitemgr.sitePreference_SO', true);
			$this->sitelanguages = explode(',',$this->preferenceso->getPreference('sitelanguages'));
		}

		function globalize($varname)
		{
			if (is_array($varname))
			{
				foreach($varname as $var)
				{
					$GLOBALS[$var] = $_POST[$var];
				}
			}
			else
			{
				$GLOBALS[$varname] = $_POST[$varname];
			}
		}

		//this has to be moved somewhere else later
		function getlangname($lang)
		  {
		    $GLOBALS['phpgw']->db->query("select lang_name from languages where lang_id = '$lang'",__LINE__,__FILE__);
		    $GLOBALS['phpgw']->db->next_record();
		    return $GLOBALS['phpgw']->db->f('lang_name');
		  }

		function _manageCategories()
		{
			$this->globalize(array('btnSaveCategory','btnAddCategory','btnEditCategory','btnDelete','btnPermission','category_id','catname','catdesc','catid','sort_order','parent','parent_old','groupaccessread','groupaccesswrite','individualaccessread','individualaccesswrite','savelanguage'));
			global $btnSaveCategory,$btnAddCategory,$btnEditCategory,$btnDelete,$btnPermission;
			global $category_id,$catname,$catdesc,$catid,$sort_order,$parent,$parent_old;
			global $groupaccessread, $groupaccesswrite, $individualaccessread, $individualaccesswrite;
			global $savelanguage;

			$common_ui = CreateObject('sitemgr.Common_UI',True);
			$common_ui->DisplayHeader();

			if (!$this->acl->is_admin())
			{
				echo lang('You must be an admin to edit categories.');
				$common_ui->DisplayFooter();
			}
			else
			{
				if($btnAddCategory)
				{
					$this->_addCategory();
				}
				else if($btnEditCategory)
				{
					$this->_editCategory($category_id);
				}
				else if($btnSaveCategory && ($catname == '' || $catdesc == ''))
				{
					$this->_editCategory($catid,True,$catname,$catdesc,$sort_order, $parent);
				}
				else
				{
					if($btnDelete)
					{
						$this->_deleteCategory($category_id);
					}
					else if($btnSaveCategory)
					{
						if(!$catid)
						{
							$catid=$this->cat_bo->addCategory('','');
						}
						$groupaccess = array_merge_recursive($groupaccessread, $groupaccesswrite);
						$individualaccess = array_merge_recursive($individualaccessread, $individualaccesswrite);
						$savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[0];
						$this->cat_bo->saveCategoryInfo($catid, $catname, $catdesc, $savelanguage, $sort_order, $parent, $parent_old);
						$this->cat_bo->saveCategoryPerms($catid, $groupaccess, $individualaccess);
					}
	
					$this->t->set_var(Array('category_manager' => lang('Category Manager'),
								'lang_catname' => lang('Category Name'),
								'lang_goto' => lang('Go to Page Manager')));			
					$this->t->set_file('ManageCategories', 'manage_categories.tpl');
					$this->t->set_block('ManageCategories', 'CategoryBlock', 'CBlock');
					//$this->cat_list = $this->cat_bo->getPermittedCategoryIDReadList();
					$this->cat_list = $this->cat_bo->getPermittedCatWriteNested();
					if($this->cat_list)
					{
						for($i = 0; $i < sizeof($this->cat_list); $i++)
						{
							$this->cat = $this->cat_bo->getCategory($this->cat_list[$i],$this->sitelanguages[0]);
							if ($this->cat->depth)
							{
								$buffer = '-';
							}
							else
							{
								$buffer = '';
							}
							$buffer = str_pad('',$this->cat->depth*18,
								'&nbsp;',STR_PAD_LEFT).$buffer;
							$this->t->set_var('buffer', $buffer);
							$this->t->set_var('category', $this->cat->name);
							$category_id = $this->cat_list[$i];
						
							$this->t->set_var('edit', 
								'<form action="'.
								$GLOBALS['phpgw']->link('/index.php',
								'menuaction=sitemgr.Admin_ManageCategories_UI._manageCategories').
								'" method="POST">
								<input type="submit" name="btnEditCategory" value="' . lang('Edit') .'">
								<input type="hidden" name="category_id" value="'.$category_id.'">
								</form>');
					
							$this->t->set_var('remove',
								'<form action="'.
								$GLOBALS['phpgw']->link('/index.php',
								'menuaction=sitemgr.Admin_ManageCategories_UI._manageCategories').
								'" method="POST">
								<input type=submit name=btnDelete value="' . lang('Delete') .'">
								<input type= hidden name = "category_id" value="'. $category_id  .'">
								</form>');
								
							$this->t->parse('CBlock', 'CategoryBlock', True);
						}
					} 
					else
					{
						$this->t->set_var('category','No category is available');
					}
					$this->t->set_var('add', 
						'<form action="'.
						$GLOBALS['phpgw']->link('/index.php',
						'menuaction=sitemgr.Admin_ManageCategories_UI._manageCategories').
						'" method="POST">
						<input type=submit name=btnAddCategory value = "' . lang('Add a category') .'">
						</form>'
					);
					$this->t->set_var('managepageslink',$GLOBALS['phpgw']->link(
						'/index.php',
						'menuaction=sitemgr.contributor_ManagePage_UI._managePage')
					);
					$this->t->pfp('out', 'ManageCategories');	
				}
				$common_ui->DisplayFooter();
			}
			
		}		


		function _addCategory()
		{
			$this->_editCategory(0);
		}
		


		function _editCategory($cat_id,$error=False,$catname='',$catdesc='',$sort_order=0, $parent=0)
		{
			$this->t->set_file('EditCategory', 'edit_category.tpl');
			$grouplist = $this->acl->get_group_list();

			if($error)
			{
				$this->t->set_var('error_msg',lang('You failed to fill in one or more required fields.'));
				$this->cat->name = $catname;
				$this->cat->description = $catdesc;
			}
			else
			{
				if ($cat_id)
				{
			  		$this->cat = $this->cat_bo->getCategory($cat_id,$this->sitelanguages[0]); 
					$permissionlist = $this->acl->get_group_permission_list($cat_id);
					//print_r($permissionlist);
					$this->t->set_var('add_edit',lang('Edit Category'));
				}
				else
				{
					$this->cat->name = '';
					$this->cat->description = '';
					$this->t->set_var('add_edit',lang('Add Category'));
					$permissionlist = array();
				}
			}

			if (count($this->sitelanguages))
			  {
			    $select = lang('as') . ' <select name="savelanguage">';
			    foreach ($this->sitelanguages as $lang)
			      {
				$select .= '<option value="' . $lang . '">'. $this->getlangname($lang) . '</option>';
			      }
			    $select .= '</select> ';
			    $this->t->set_var('savelang',$select);
			  }

			$this->t->set_var(array(
				'catid' => $cat_id,
				'catname' => $this->cat->name,
				'catdesc' => $this->cat->description,
				'sort_order' => $this->cat->sort_order,
				'parent_dropdown' => $this->getParentOptions($this->cat->parent,$cat_id),
				'old_parent' => $this->cat->parent,
				'actionurl' => $GLOBALS['phpgw']->link('/index.php',
				         'menuaction=sitemgr.Admin_ManageCategories_UI._manageCategories'),
				'lang_basic' => lang('Basic Settings'),
				'lang_catname' => lang('Category Name'),
				'lang_catsort' => lang('Sort Order'),
				'lang_catparent' => lang('Parent'),
				'lang_catdesc' => lang('Category Description'),
				'lang_groupaccess' => lang('Group Access Permissions'),
				'lang_groupname' => lang('Group Name'),
				'lang_readperm' => lang('Read Permission'),
				'lang_writeperm' => lang('Write Permission'),
				'lang_useraccess' => lang('Individual Access Permission'),
				'lang_username' => lang('User Name'),
				'lang_reset' => lang('Reset'),
				'lang_save' => lang('Save')
			));
			
			$this->t->set_file('EditCategory', 'edit_category.tpl');
			$this->t->set_block('EditCategory','GroupBlock', 'GBlock');

			$acct = CreateObject('phpgwapi.accounts');
			if($grouplist)
			{
				for($i = 0; $i < count($grouplist); $i++ )
				{
					//$account_name = $acct->id2name($permissionlist[$i]['account_id']);
					//$this->t->set_var('group_id',$permissionlist[$i]['account_id']);
					$account_name = $grouplist[$i]['account_lid'];
					$this->t->set_var('group_id',$grouplist[$i]['account_id']);
					if ($cat_id)
					{
						$permission_id = $permissionlist[$i]['rights'];
					}
					else
					{
						$permission_id = 0;
					}

					$this->t->set_var('groupname', $account_name);
					if ($permission_id == PHPGW_ACL_ADD)
					{
						$permission_id = PHPGW_ACL_ADD | PHPGW_ACL_READ;
					}
					if ($permission_id & PHPGW_ACL_READ)  
					{
						$this->t->set_var('checkedgroupread','CHECKED');
					}
					else
					{
						$this->t->set_var('checkedgroupread','');
					}
					if ($permission_id & PHPGW_ACL_ADD)
					{
						$this->t->set_var('checkedgroupwrite','CHECKED');
					}
					else
					{
						$this->t->set_var('checkedgroupwrite','');
					}

					$this->t->parse('GBlock', 'GroupBlock', True);
				}
			}
			else
			{
				$this->t->set_var('groupname',lang("No groups defined."));
			}

			$this->t->set_block('EditCategory','UserBlock', 'UBlock');

			$userlist = $this->acl->get_user_list();
			$userpermissionlist = $this->acl->get_user_permission_list($cat_id);
			if($userlist)
			{
				for($i = 0; $i < count($userlist); $i++ )
				{
					$user_name = $userlist[$i]['account_lid'];
					if ($cat_id)
					{
						$user_permission_id = $userpermissionlist[$i]['rights'];
					}
					else
					{
						$user_permission_id = 0;
					}
					$this->t->set_var('user_id', $userlist[$i]['account_id']);
					
					$this->t->set_var('username', $user_name);
					if ($user_permission_id == PHPGW_ACL_ADD)
					{
						$user_permission_id = PHPGW_ACL_ADD | PHPGW_ACL_READ;
					}
					if ($user_permission_id & PHPGW_ACL_READ )
					{
						$this->t->set_var('checkeduserread','CHECKED');
					}
					else
					{
						$this->t->set_var('checkeduserread','');
					}
					if ($user_permission_id & PHPGW_ACL_ADD )
					{
						$this->t->set_var('checkeduserwrite','CHECKED');
					}
					else
					{
						$this->t->set_var('checkeduserwrite','');
					}
					$this->t->parse('UBlock', 'UserBlock', True);
				}
			}
			else
			{
				$this->t->set_var('username',lang("No users defined."));
			}

			$this->t->pfp('out','EditCategory');
		}

		function getParentOptions($selected_id=0,$skip_id=0)
		{
			$option_list=$this->cat_bo->getCategoryOptionList();
			if (!$selected_id)
			{
				$selected=' SELECTED';
			}
			if (!$skip_id)
			{
				$skip_id = -1;
			}
			$retval="\n".'<SELECT NAME="parent">'."\n";
			foreach($option_list as $option)
			{
				if ($option['value']!=$skip_id)
				{
					$selected='';
					if ($option['value']==$selected_id)
					{
						$selected=' SELECTED';
					}
					$retval.='<OPTION VALUE="'.$option['value'].'"'.$selected.'>'.
						$option['display'].'</OPTION>'."\n";
				}
			}
			$retval.='</SELECT>';
			return $retval;
		}

		function _deleteCategory($cat_id)
		{
			$this->globalize('deleteconfirmed');
			global $deleteconfirmed;
			if ($deleteconfirmed==$cat_id)
			{
				$this->cat_bo->removeCategory($cat_id);
			}
			else
			{
				$cat = $this->cat_bo->getCategory($cat_id,$this->sitelanguages[0]);
				$this->t->set_file('ConfirmDelete','confirmdelete.tpl');
				$this->t->set_var('deleteheader',lang('Are you sure you want to delete the category %1 and all of its associated pages?  You cannot retrieve the deleted pages if you continue.',$cat->name));
				$this->t->set_var('category_id',$cat_id);
				$this->t->set_var('lang_yes',lang('Yes, please delete it'));
				$this->t->set_var('lang_no',lang('Cancel the delete'));
				$this->t->set_var('actionurl',
					$GLOBALS['phpgw']->link('/index.php',
					'menuaction=sitemgr.Admin_ManageCategories_UI._manageCategories')
				);
				$this->t->pfp('out','ConfirmDelete');
			}
		}

	}
?>
