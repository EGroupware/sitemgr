<?php
	/*************************************************************************\
	* http://www.phpgroupware.org                                             *
	* -------------------------------------------------                       *
	* This program is free software; you can redistribute it and/or modify it *
	* under the terms of the GNU General Public License as published by the   *
	* Free Software Foundation; either version 2 of the License, or (at your  *
	* option) any later version.                                              *
	\*************************************************************************/
	/* $Id$ */
	
	class Pages_UI
	{
		var $common_ui;
		var $t;
		var $pagebo;
		var $categorybo;
		var $pageso; // page class
		var $sitelanguages;
		
		var $public_functions=array
		(
			'manage' => True,
			'edit' => True,
			'delete' => True
		);
		
		function Pages_UI()			
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS['phpgw']->template;
			$this->pagebo = &$GLOBALS['Common_BO']->pages;
			$this->categorybo = &$GLOBALS['Common_BO']->cats;
			$this->sitelanguages = $GLOBALS['Common_BO']->sites->current_site['sitelanguages'];
		}
	
		function delete()
		{
			$page_id = $_GET['page_id'];
			$this->pagebo->removePage($page_id);
			$this->manage();
			return;
		}

		function edit()
		{
			$GLOBALS['Common_BO']->globalize(array(
				'inputhidden','btnAddPage','btnDelete','btnEditPage','btnSave','inputsort','inputstate',
				'inputtitle','inputname','inputsubtitle','savelanguage','inputpageid','inputcategoryid'));

			global $inputpageid,$inputcategoryid, $inputhidden, $inputstate;
			global $btnAddPage, $btnDelete, $btnEditPage, $btnSave;
			global $inputsort,$inputtitle, $inputname, $inputsubtitle;
			global $savelanguage;
			$page_id = $inputpageid ? $inputpageid : $_GET['page_id'];
			$category_id = $inputcategoryid ? $inputcategoryid : $_GET['category_id'];

			$this->t->set_file('EditPage', 'page_editor.tpl');

			if($btnSave)
			{
				if ($inputname == '' || $inputtitle == '')
				{
					$error = lang('You failed to fill in one or more required fields.');
				}
				if(!$page_id)
				{		
					$page_id = $this->pagebo->addPage($inputcategoryid);
					if(!$page_id)
					{
						echo lang("You don't have permission to write in the category");
						$this->manage();
						return;
					}
				}
				if (!$error)
				{
					$page->id = $page_id;
					$page->title = $inputtitle;
					$page->name = $inputname;
					$page->subtitle = $inputsubtitle;
					$page->sort_order = $inputsort;
					$page->cat_id = $category_id;
					$page->hidden = $inputhidden ? 1: 0;
					$page->state = $inputstate;
					$savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[0];
					$save_msg = $this->pagebo->savePageInfo($page,$savelanguage);
					if (!is_string($save_msg))
					{
						$this->manage();
						return;
					}
					$this->t->set_var('message',$save_msg);
				}
				else
				{
					$this->t->set_var('message',$error);
				}
			}

			$this->common_ui->DisplayHeader();

			if($page_id)
			{
				$page = $this->pagebo->getPage($page_id,$this->sitelanguages[0]);
				$this->t->set_var('add_edit',lang('Edit Page'));
				$this->t->set_var('move_to',$this->getParentOptions($page->cat_id));
			}
			else
			{
				$this->t->set_var('add_edit',lang('Add Page'));
				$move_msg = lang('Cannot move page until it has been saved.');
				$this->t->set_var('move_to',$move_msg);
			}

			if (count($this->sitelanguages) > 1)
			{
				$select = lang('as') . ' <select name="savelanguage">';
			
				foreach ($this->sitelanguages as $lang)
				{
					$selected= '';
					if ($lang == $page->lang)
					{
						$selected = 'selected="selected" ';
					}
					$select .= '<option ' . $selected .'value="' . $lang . '">'. $GLOBALS['Common_BO']->getlangname($lang) . '</option>';
				}
				$select .= '</select> ';
				$this->t->set_var('savelang',$select);
			}

			$link_data['page_id'] = $page_id;
			$link_data['category_id'] = $inputcategoryid;
			$this->t->set_var(array(
				'title' =>$page->title,
				'subtitle' => $page->subtitle,
				'name'=>$page->name,
				'sort_order'=>$page->sort_order,
				'page_id'=>$page_id,
				'hidden' => $page->hidden ? 'CHECKED' : '',
				'stateselect' => $GLOBALS['Common_BO']->inputstateselect($page->state),
				'lang_name' => lang('Name'),
				'lang_title' => lang('Title'),
				'lang_subtitle' => lang('Subtitle'),
				'lang_sort' => lang('Sort order'),
				'lang_move' => lang('Move to'),
				'lang_hide' => lang('Check to hide from condensed site index.'),
				'lang_required' => lang('Required Fields'),
				'lang_goback' => lang('Go back to Page Manager'),
				'lang_reset' => lang('Reset'),
				'lang_save' => lang('Save'),
				'lang_state' => lang('State'),
				'goback' => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Pages_UI.manage'),
			));
			
			$this->t->pfp('out','EditPage');
			$this->common_ui->DisplayFooter();
		}

		function manage()
		{
			$this->common_ui->DisplayHeader();

			$this->t->set_file('ManagePage','page_manager.tpl');
			$this->t->set_block('ManagePage', 'PageBlock', 'PBlock');
			$this->t->set_block('ManagePage', 'CategoryBlock', 'CBlock');
			$this->t->set_var('page_manager', lang('Page Manager'));

			$cat_list = $this->categorybo->getpermittedcatsWrite();

			if (!$cat_list)
			{
				 echo lang("You do not have write permissions for any site categories.") . '<br><br>';
			}

			while (list(,$cat_id) = @each($cat_list))
			{
				$category = $this->categorybo->getCategory($cat_id);
				$this->t->set_var('PBlock', '');
				$page_list = $this->pagebo->getPageIDList($cat_id);

				while (list(,$page_id) = @each($page_list))
				{
					$page = $this->pagebo->getPage($page_id,$this->sitelanguages[0]);
					$page_description = sprintf(
						'<b>%s</b>: %s &nbsp;&nbsp;<b>ID</b>: %s<br><b>%s</b>: %s',
						lang('Name'),
						$page->name,
						$page_id,
						lang('Title'),
						$page->title
					);
					$this->t->set_var('page', $page_description);
					$link_data['page_id'] = $page_id;
					$link_data['menuaction'] = "sitemgr.Pages_UI.edit";
					$this->t->set_var('edit','<form action="'. $GLOBALS['phpgw']->link('/index.php',$link_data) .
						'" method="POST"><input type="submit" name="btnEditPage" value="' . lang('Edit') .'"></form>'
					);
					$link_data['menuaction'] = "sitemgr.Content_UI.manage";
					$this->t->set_var('content','<form action="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .
						'" method="POST"><input type="submit" value="' . lang('Manage Content') .'"></form>'
					);
					$link_data['menuaction'] = "sitemgr.Pages_UI.delete";
					$this->t->set_var('remove','<form action="'.$GLOBALS['phpgw']->link('/index.php',$link_data) .
						'" method="POST"><input type="submit" name="btnDelete" value="' . lang('Delete') .'"></form>'
					);
					$this->t->parse('PBlock', 'PageBlock', true);
				}

				$link_data = array('menuaction' => "sitemgr.Pages_UI.edit",'category_id' => $cat_id);
				$this->t->set_var(array(
					'indent' => $category->depth * 5,
					'category' => $category->name,
					'add' => '<form action="'. $GLOBALS['phpgw']->link('/index.php',$link_data) .
						'" method="POST"><input type=submit name="btnAddPage" value="' . 
						lang('Add new page to this category') . '"></form>'
				));
				$this->t->parse('CBlock', 'CategoryBlock', true); 
			}
			$this->t->pfp('out','ManagePage');
			$this->common_ui->DisplayFooter();
		}

		function getParentOptions($selected_id=0)
		{
			$option_list=$this->categorybo->getCategoryOptionList();
			if (!$selected_id)
			{
				$selected=' SELECTED'; 
			}
			$retval="\n".'<SELECT NAME="inputcategoryid">'."\n";
			foreach($option_list as $option)
			{
				if ((int) $option['value']!=0)
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
	}	
?>
