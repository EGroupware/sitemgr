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

	class Categories_UI
   	{
		var $common_ui;
		var $cat;
		var $cat_list;
		var $cat_bo;
		var $acl;
		var $isadmin;
		var $t;
		var $sitelanguages;
		
		var $public_functions = array
		(
			'_manageCategories' => True,
			'_editCategory' => True,
			'_deleteCategory' => True
		);
			
		function Categories_UI()
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS['phpgw']->template;
			$this->cat_bo = $GLOBALS['Common_BO']->cats;
			$this->cat = CreateObject('sitemgr.Category_SO', True);
			$this->acl = $GLOBALS['Common_BO']->acl;
			$this->isadmin = $this->acl->is_admin();
			$this->sitelanguages = $GLOBALS['Common_BO']->sites->current_site['sitelanguages'];
		}

		function _manageCategories()
		{
			$this->common_ui->DisplayHeader();

			$this->t->set_var(Array('category_manager' => lang('Category Manager'),
				'lang_catname' => lang('Category Name'),
				'lang_goto' => lang('Go to Page Manager')));			
			$this->t->set_file('ManageCategories', 'manage_categories.tpl');
			$this->t->set_block('ManageCategories', 'CategoryBlock', 'CBlock');

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
					$cat_id = $this->cat_list[$i];
					$this->t->set_var('buffer', $buffer);
					$this->t->set_var('category', sprintf('%s : %d',$this->cat->name,$cat_id));


					$link_data['page_id'] = 0;
					$link_data['cat_id'] = $cat_id;
					if ($this->isadmin)
					{
						$this->t->set_var('edit', 
							'<form action="'.
							$GLOBALS['phpgw']->link('/index.php',
							'menuaction=sitemgr.Categories_UI._editCategory').
							'" method="POST"><input type="submit" value="' . lang('Edit') .'"><input type="hidden" name="cat_id" value="'.$cat_id.'">
							</form>');
					
						$this->t->set_var('remove',
							'<form action="'.
							$GLOBALS['phpgw']->link('/index.php',
							'menuaction=sitemgr.Categories_UI._deleteCategory').
							'" method="POST">
							<input type="submit" value="' . lang('Delete') .'">
							<input type="hidden" name="cat_id" value="'. $cat_id  .'">
							</form>');

						$link_data['menuaction'] = "sitemgr.Modules_UI._manageModules";
						$this->t->set_var('moduleconfig',
							'<form action="'.
							$GLOBALS['phpgw']->link('/index.php',$link_data).
							'" method="POST">
							<input type="submit" value="' . lang('Manage Modules') .'"></form>');
					}

					$link_data['menuaction'] = "sitemgr.Content_UI._manageContent";
					$this->t->set_var('content',
						'<form action="'.
						$GLOBALS['phpgw']->link('/index.php',$link_data).
						'" method="POST">
						<input type="submit" value="' . lang('Manage Content') .'"></form>');

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
				'menuaction=sitemgr.Categories_UI._editCategory').
				'" method="POST">
				<input type=submit value = "' . lang('Add a category') .'">
				</form>'
			);
			$this->t->set_var('managepageslink',$GLOBALS['phpgw']->link(
				'/index.php',
				'menuaction=sitemgr.Pages_UI._managePage')
			);
			$this->t->pfp('out', 'ManageCategories');	

			$this->common_ui->DisplayFooter();
		}

		function _editCategory()
		{
			if (!$this->isadmin)
			{
				$this->_manageCategories();
				return False;
			}

			$GLOBALS['Common_BO']->globalize(array('btnSave','catname','catdesc','cat_id','sort_order','parent','parent_old','groupaccessread','groupaccesswrite','individualaccessread','individualaccesswrite','savelanguage','inputgetparentpermissions','inputapplypermissionstosubs'));

			global $btnSave, $cat_id,$catname,$catdesc,$sort_order,$parent,$parent_old;
			global $groupaccessread, $groupaccesswrite, $individualaccessread, $individualaccesswrite;
			global $savelanguage, $inputgetparentpermissions,$inputapplypermissionstosubs;

			if ($btnSave && $catname && $catdesc)
			{
				if (!$cat_id)
				{
					$cat_id=$this->cat_bo->addCategory('','');
				}
				$groupaccess = array_merge_recursive($groupaccessread, $groupaccesswrite);
				$individualaccess = array_merge_recursive($individualaccessread, $individualaccesswrite);
				$savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[0];
				$this->cat_bo->saveCategoryInfo($cat_id, $catname, $catdesc, $savelanguage, $sort_order, $parent, $parent_old);
				if ($inputgetparentpermissions)
				{
					$this->cat_bo->saveCategoryPermsfromparent($cat_id);
				}
				else
				{
					$this->cat_bo->saveCategoryPerms($cat_id, $groupaccess, $individualaccess);
				}
				if ($inputapplypermissionstosubs)
				{
					$this->cat_bo->applyCategoryPermstosubs($cat_id);
				}
				$this->_manageCategories();
				return;
			}

			$this->common_ui->DisplayHeader();

			if ($cat_id)
			{
				$this->cat = $this->cat_bo->getCategory($cat_id,$this->sitelanguages[0]); 
			}

			//if the user tried to save, but catname or catdesc were empty, we remember the modified values
			if ($btnSave)
			{
				$this->t->set_var('error_msg',lang('You failed to fill in one or more required fields.'));
				$this->cat->name = $catname;
				$this->cat->description = $catdesc;
			}

			$this->t->set_file('EditCategory', 'edit_category.tpl');
			$this->t->set_block('EditCategory','GroupBlock', 'GBlock');

			if (count($this->sitelanguages) > 1)
			{
				$select = lang('as') . ' <select name="savelanguage">';
				foreach ($this->sitelanguages as $lang)
				{
					$select .= '<option value="' . $lang . '">'. $GLOBALS['Common_BO']->getlangname($lang) . '</option>';
				}
				$select .= '</select> ';
				$this->t->set_var('savelang',$select);
			}

			$this->t->set_var(array(
				'add_edit' => ($cat_id ? lang('Edit Category') : lang('Add Category')),
				'cat_id' => $cat_id,
				'catname' => $this->cat->name,
				'catdesc' => $this->cat->description,
				'sort_order' => $this->cat->sort_order,
				'parent_dropdown' => $this->getParentOptions($this->cat->parent,$cat_id),
				'old_parent' => $this->cat->parent,
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
				'lang_save' => lang('Save'),
				'lang_getparentpermissions' => lang('Fill in permissions from parent category? If you check this, below values will be ignored'),
				'lang_applypermissionstosubs' => lang('Apply permissions also to subcategories?')
			));
		
			$acct = CreateObject('phpgwapi.accounts');
			$grouplist = $this->acl->get_group_list();
			$permissionlist = ($cat_id ? $this->acl->get_group_permission_list($cat_id) : array());
			if($grouplist)
			{
				for($i = 0; $i < count($grouplist); $i++ )
				{
					//$account_name = $acct->id2name($permissionlist[$i]['account_id']);
					//$this->t->set_var('group_id',$permissionlist[$i]['account_id']);
					$account_name = $grouplist[$i]['account_lid'];
					$account_id = $grouplist[$i]['account_id'];
					$this->t->set_var('group_id',$account_id);
					if ($cat_id)
					{
						$permission_id = $permissionlist[$account_id];
					}
					else
					{
						$permission_id = 0;
					}

					$this->t->set_var('groupname', $account_name);
//I comment out the assumption, that when you write, you should necessarily read,
// 					if ($permission_id == PHPGW_ACL_ADD)
// 					{
// 						$permission_id = PHPGW_ACL_ADD | PHPGW_ACL_READ;
// 					}
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
					$user_id = $userlist[$i]['account_id'];
					if ($cat_id)
					{
						$user_permission_id = $userpermissionlist[$user_id];
					}
					else
					{
						$user_permission_id = 0;
					}
					$this->t->set_var('user_id', $user_id);
					
					$this->t->set_var('username', $user_name);
// 					if ($user_permission_id == PHPGW_ACL_ADD)
// 					{
// 						$user_permission_id = PHPGW_ACL_ADD | PHPGW_ACL_READ;
// 					}
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

			$this->common_ui->DisplayFooter();
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

		function _deleteCategory()
		{
			if (!$this->isadmin)
			{
				$this->_manageCategories();
				return;
			}

			$GLOBALS['Common_BO']->globalize(array('cat_id','btnDelete','btnCancel'));
			global $cat_id,$btnDelete,$btnCancel;
			if ($btnDelete)
			{
				$this->cat_bo->removeCategory($cat_id);
				$this->_manageCategories();
				return;
			}
			if ($btnCancel)
			{
				$this->_manageCategories();
				return;
			}

			$this->common_ui->DisplayHeader();

			$cat = $this->cat_bo->getCategory($cat_id,$this->sitelanguages[0]);
			$this->t->set_file('ConfirmDelete','confirmdelete.tpl');
			$this->t->set_var('deleteheader',lang('Are you sure you want to delete the category %1 and all of its associated pages?  You cannot retrieve the deleted pages if you continue.',$cat->name));
			$this->t->set_var('cat_id',$cat_id);
			$this->t->set_var('lang_yes',lang('Yes, please delete it'));
			$this->t->set_var('lang_no',lang('Cancel the delete'));
			$this->t->pfp('out','ConfirmDelete');

			$this->common_ui->DisplayFooter();
		}
	}
?>
