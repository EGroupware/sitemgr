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
		var $cat_id;
		var $page_id;
		var $t;
		var $pagebo;
		var $categorybo;
		var $pageso; // page class
		var $category;
		var $cat_list;
		var $page_list;
		var $sitelanguages;
		
		var $public_functions=array
		(
			'_managePage' => True
		);
		
		function Pages_UI()			
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS['phpgw']->template;
			$this->pagebo = &$GLOBALS['Common_BO']->pages;
			$this->categorybo = &$GLOBALS['Common_BO']->cats;
			$this->sitelanguages = $GLOBALS['Common_BO']->sites->current_site['sitelanguages'];
		}
		
		function _addPage($category_id)
		{
			$this->_editPage($category_id, 0);
		}
	
		function _deletePage($category_id, $page_id)
		{
			$this->pagebo->removePage($category_id, $page_id);
		}

		function _editPage($category_id, $page_id,$cname='',$ctitle='',$csubtitle='')
		{
			$GLOBALS['Common_BO']->globalize(array('title','name','subtitle','sort_order','parent','hidden','btnEditPage','savelanguage'));
			global $title;
			global $name;
			global $subtitle;
			global $sort_order;
			global $parent;
			global $hidden;
			global $btnEditPage;
			global $savelanguage;
		
			$this->t->set_file('EditPage', 'page_editor.tpl');
			if($page_id)
			{
				$this->page = $this->pagebo->getPage($page_id,$this->sitelanguages[0]);
				if ($cname)
				{
					$this->page->name=$cname;
				}
				if ($ctitle)
				{
					$this->page->title=$ctitle;
				}
				if ($csubtitle)
				{
					$this->page->subtitle=$csubtitle;
				}
				$this->t->set_var('add_edit',lang('Edit Page'));
				$this->t->set_var('move_to',$this->getParentOptions($this->page->cat_id));
			}
			else
			{
				$this->page->title = $title;
				$this->page->subtitle = $subtitle;
				$this->page->name = $name;
				$this->page->sort_order = $sort_order;
				$this->page->cat_id = $category_id;
				$this->t->set_var('add_edit',lang('Add Page'));
				$move_msg = lang('Cannot move page until it has been saved.');
				$move_msg .= '<INPUT TYPE="hidden" name="parent" value="'.
					$category_id.'">';
				$this->t->set_var('move_to',$move_msg);
			}
			
			$trans = array('{' => '&#123;', '}' => '&#125;');
	                if($this->page->hidden)
                        {
                                $this->t->set_var('hidden', 'CHECKED');
                        }
                        else
                        {   
                                $this->t->set_var('hidden', '');
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
			
			$this->t->set_var(array(
				'title' =>$this->page->title,
				'subtitle' => $this->page->subtitle,
//				'main'=>strtr($GLOBALS['phpgw']->strip_html($this->page->content),$trans),
				'name'=>$this->page->name,
				'sort_order'=>$this->page->sort_order,
				'pageid'=>$page_id,
				'category_id' => $category_id,
				'lang_name' => lang('Name'),
				'lang_title' => lang('Title'),
				'lang_subtitle' => lang('Subtitle'),
				'lang_sort' => lang('Sort order'),
				'lang_move' => lang('Move to'),
				'lang_hide' => lang('Check to hide from condensed site index.'),
				'lang_required' => lang('Required Fields'),
				'lang_goback' => lang('Go back to Page Manager'),
				'lang_reset' => lang('Reset'),
				'lang_save' => lang('Save')
			));

			
			
			$this->t->set_var('actionurl', $GLOBALS['phpgw']->link('/index.php',
				'menuaction=sitemgr.Pages_UI._managePage'));
			$this->t->set_var('goback', $GLOBALS['phpgw']->link('/index.php',
                                'menuaction=sitemgr.Pages_UI._managePage'));
			$this->t->pfp('out','EditPage');
		
		}
		
		function _managePage()
		{
			$GLOBALS['Common_BO']->globalize(array('hidden','btnAddPage','btnDelete','btnEditPage','btnPrev','pageid','btnSave','category_id','sort_order','parent','title','name','subtitle','error','savelanguage'));
			global $hidden;
			global $btnAddPage, $btnDelete, $btnEditPage;
			global $btnPrev;
			global $btnSave;
			global $pageid;
			global $category_id;
			global $sort_order;
			global $parent;
			global $title;
			global $name;
			global $subtitle;
			global $error;
			global $savelanguage;

			$this->common_ui->DisplayHeader();
			
			if($btnSave && !$error)
			{
				if ($name == '' || $title == '')
				{
					$this->t->set_var('message',lang('You failed to fill in one or more required fields.'));
					$this->_editPage($category_id,$pageid,$name,$title,$subtitle);
					exit;
				}
				if($pageid)
				{
					$this->page->id = $pageid;
				}
				else
				{		
					$this->page->id = $this->pagebo->addPage($category_id);
					$pageid = $this->page->id;
					if(!$this->page->id)
					{
						$save_msg = lang("You don't have permission to write in the category");
					}
				}

				if (!$save_msg)
				{
					$this->page->title = $title;
					$this->page->name = $name;
					$this->page->subtitle = $subtitle;
					$this->page->sort_order = $sort_order;
					$this->page->cat_id = $parent;

					if($hidden)
					{
						$this->page->hidden = 1;
					}
					else
					{
						$this->page->hidden = 0;
					}
					$savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[0];
					$save_msg = $this->pagebo->savePageInfo($this->page,$savelanguage);
				}
				if (!is_string($save_msg))
				{
					echo('<p><b><font color="red">' . lang('Page saved.') . '</font></b></p>');
				}
				else
				{
					$this->t->set_var('message',$save_msg);
					$this->_editPage($category_id,$this->page->id); //,$name,$title,$subtitle,$main);
					exit;
				}
				$btnEditPage = False;
				$btnSave = False;
			}
			if($btnPrev)
			{
				echo lang('Go back to the category manager.');
				$btnEditPage = False;
				$btnPrev = False;
			}
			if($btnAddPage)
			{
				$this->_addPage($category_id);
			}
			else if($btnEditPage)
			{
				$this->_editPage($category_id, $pageid);
			}
			else
			{
				if($btnDelete)
				{
					$this->_deletePage($category_id, $pageid);
				}
				
				$this->t->set_file('ManagePage','page_manager.tpl');
				$this->t->set_block('ManagePage', 'PageBlock', 'PBlock');
				$this->t->set_block('ManagePage', 'CategoryBlock', 'CBlock');
				$this->t->set_var('page_manager', lang('Page Manager'));
				$this->cat_list = $this->categorybo->getPermittedCatWriteNested();

				if($this->cat_list)
				{
					for ($i=0; $i<sizeof($this->cat_list); $i++)
					{
						$this->category = $this->categorybo->getCategory($this->cat_list[$i]);					
						$this->t->set_var('PBlock', '');
						$this->page_list = $this->pagebo->getPageIDList($this->cat_list[$i]);
						$this->cat_id = $this->cat_list[$i];
						if($this->page_list && sizeof($this->page_list)>0)
						{
							for($j = 0; $j < sizeof($this->page_list); $j++)
							{
								$this->page_id =$this->page_list[$j];
								$this->page = $this->pagebo->getPage($this->page_id,$this->sitelanguages[0]);
								$page_description = sprintf(
									'<b>%s</b>: %s &nbsp;&nbsp;<b>ID</b>: %s<br><b>%s</b>: %s',
									lang('Name'),
									$this->page->name,
									$this->page_id,
									lang('Title'),
									$this->page->title
								);
								$this->t->set_var('page', $page_description);
								$this->t->set_var('edit',
									'<form action="'.
									$GLOBALS['phpgw']->link('/index.php',
										'menuaction=sitemgr.Pages_UI._managePage').
										'" method="POST">
									<input type="submit" name="btnEditPage" value="' . lang('Edit') .'">
									<input type="hidden" name="category_id" value="'.
										$this->cat_id.'">
									<input type="hidden" name="parent" value="'.
										$this->cat_id.'">
									<input type="hidden" name="pageid" value="'. 
										$this->page_id .'">
									</form>');
								$this->t->set_var('msg','');
								$link_data['menuaction'] = "sitemgr.Content_UI._manageContent";
								$link_data['page_id'] = $this->page_id;
								$this->t->set_var('manage',
									'<form action="'.
									$GLOBALS['phpgw']->link('/index.php',$link_data).
									'" method="POST">
									<input type="submit" value="' . lang('Manage Content') .'"></form>');
								$this->t->set_var('remove', 
									'<form action="'.$GLOBALS['phpgw']->link('/index.php',
									'menuaction=sitemgr.Pages_UI._managePage').
										'" method="POST">
									<input type="submit" name="btnDelete" value="' . lang('Delete') .'">
									<input type="hidden" name="pageid" value="'.$this->page_id.'">
									<input type="hidden" name="category_id" value="'.
										$this->cat_id.'">
									<input type="hidden" name="parent" value="'.
										$this->cat_id.'">
									</form>');
								$this->t->parse('PBlock', 'PageBlock', true);
							}
						}
						else
						{
							$this->t->set_var('msg' , lang('This category has no pages.'));
						}
						$this->t->set_var('indent', $this->category->depth * 5);
						$this->t->set_var('category',$this->category->name); 
						$this->t->set_var('add', 
							'<form action="'.
							$GLOBALS['phpgw']->link('/index.php',
							'menuaction=sitemgr.Pages_UI._managePage').
							'" method="POST">
							<input type=submit name="btnAddPage" value="' . lang('Add new page to this category') . '">
							<input type=hidden name="category_id" value ="'.$this->cat_id .'">
							</form>');
					
						$this->t->parse('CBlock', 'CategoryBlock', true); 
					}
					$this->t->pfp('out','ManagePage');
				}
				else
				{
					echo lang("I'm sorry, you do not have write permissions for any site categories.") . '<br><br>';
				}
			}
			$this->common_ui->DisplayFooter();
		}

		function getParentOptions($selected_id=0)
		{
			$option_list=$this->categorybo->getCategoryOptionList();
			if (!$selected_id)
			{
				$selected=' SELECTED'; 
			}       
			$retval="\n".'<SELECT NAME="parent">'."\n";
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
