<?php
	/***************************************************************************\
	* http://www.phpgroupware.org                                               *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id$ */
	
	class Contributor_ManagePage_UI
	{
		var $cat_id;
		var $page_id;
		var $t;
		var $pagebo;
		var $categorybo;
		var $pageso; // page class
		var $category;
		var $cat_list;
		var $page_list;

		var $public_functions=array
		(
			'_managePage' => True
		);
		
		function Contributor_ManagePage_UI()			
		{
			$this->t = $GLOBALS["phpgw"]->template;
			$this->pagebo = CreateObject('sitemgr.Pages_BO', True);
			$this->categorybo = CreateObject('sitemgr.Categories_BO',True);
		}
		
		function _addPage($category_id)
		{
			$this->_editPage($category_id, 0);
		}
	
		function _deletePage($category_id, $page_id)
		{
			$this->pagebo->removePage($category_id, $page_id);
		}
		
		function _editPage($category_id, $page_id,$cname='',$ctitle='',$csubtitle='',$cmain='')
		{
			global $title;
			global $name;
			global $subtitle;
			global $main;
			global $sort_order;
			global $hidden;
			global $btnEditPage;
		
			$this->t->set_file('EditPage', 'page_editor.tpl');
			if($page_id)
			{
				$this->page = $this->pagebo->getPage($page_id);
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
				if ($cmain)
				{
					$this->page->content=$cmain;
				}
				$this->t->set_var('add_edit','Edit Page');
			}
			else
			{
				$this->page->title = $title;
				$this->page->subtitle = $subtitle;
				$this->page->content = $main;
				$this->page->name = $name;
				$this->page->sort_order = $sort_order;
				$this->t->set_var('add_edit','Add Page');
			}
			
			$trans = array("{" => "&#123;", "}" => "&#125;");
	                if($this->page->hidden)
                        {
                                $this->t->set_var('hidden', 'CHECKED');
                        }
                        else
                        {   
                                $this->t->set_var('hidden', '');
                        }
			$this->t->set_var(array(
				'title' =>$this->page->title,
				'subtitle' => $this->page->subtitle,
				'main'=>strtr($this->page->content,$trans),
				'name'=>$this->page->name,
				'sort_order'=>$this->page->sort_order,
				'pageid'=>$page_id,
				'category_id' => $category_id
			));
			
			$this->t->set_var('actionurl', $GLOBALS['phpgw']->link('/index.php',
				'menuaction=sitemgr.contributor_ManagePage_UI._managePage'));
			$this->t->set_var('goback', $GLOBALS['phpgw']->link('/index.php',
                                'menuaction=sitemgr.contributor_ManagePage_UI._managePage'));
			$this->t->pfp('out','EditPage');
		
		}
		
		function _managePage()
		{
			global $hidden;
			global $btnAddPage;
			global $btnDelete;
			global $btnEditPage;
			global $btnPrev;
			global $pageid;
			global $btnSave;
			global $btnReset;
			global $pageid;
			global $category_id;
			global $sort_order;
			global $title;
			global $name;
			global $subtitle;
			global $main;
			global $error;

			$common_ui = CreateObject('sitemgr.Common_UI',True);
			$common_ui->DisplayHeader();
			//echo PHPGW_TEMPLATE_DIR;
			
			if($btnSave && !$error)
			{
				if ($name == '' || $title == '' || $main == '')
				{
					$this->t->set_var('message','You failed to fill in one or more required fields.');
					$this->_editPage($category_id,$pageid,$name,$title,$subtitle,$main);
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
						$save_msg = "You don't have permission to write in the category";
					}
				}

				if (!$save_msg)
				{
					$this->page->title = $title;
					$this->page->name = $name;
					$this->page->subtitle = $subtitle;
					$this->page->content = $main;
					$this->page->cat_id = $category_id;
					$this->page->sort_order = $sort_order;

					if($hidden)
					{
						$this->page->hidden = 1;
					}
					else
					{
						$this->page->hidden = 0;
					}
					$save_msg = $this->pagebo->savePageInfo($category_id, $this->page);
				}
				if (!is_string($save_msg))
				{
					echo('<p><b><font color="red">Page saved.</font></b></p>');
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
				echo "Go back to the category manager";
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
				$this->cat_list = $this->categorybo->getPermittedCategoryIDWriteList();
			
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
								$this->page = $this->pagebo->getPage($this->page_id);
								$page_description = 'Name: '.$this->page->name.'<br>Title: '.$this->page->title.'<br>ID: '.$this->page->id;
								$this->t->set_var('page', $page_description);
								$this->t->set_var('edit',
									'<form action="'.
									$GLOBALS['phpgw']->link('/index.php',
										'menuaction=sitemgr.contributor_ManagePage_UI._managePage').
										'" method="POST">
									<input type="submit" name="btnEditPage" value = "Edit">
									<input type="hidden" name="category_id" value="'.
										$this->cat_id.'">
									<input type="hidden" name="pageid" value="'. 
										$this->page_id .'">
									</form>');
								$this->t->set_var('msg','');
								$this->t->set_var('remove', 
									'<form action="'.$GLOBALS['phpgw']->link('/index.php',
									'menuaction=sitemgr.contributor_ManagePage_UI._managePage').
										'" method="POST">
									<input type="submit" name="btnDelete" value="Delete">
									<input type="hidden" name="pageid" value="'.$this->page_id.'">
									<input type="hidden" name="category_id" value="'.
										$this->cat_id.'">
									</form>');
								$this->t->parse('PBlock', 'PageBlock', true);
							}
						}
						else
						{
							$this->t->set_var('msg' , 'This category has no pages.');
						}
						$this->t->set_var('number', $i+1);
						$this->t->set_var('category', $this->category->name); 
						$this->t->set_var('add', 
							'<form action="'.
							$GLOBALS['phpgw']->link('/index.php',
							'menuaction=sitemgr.contributor_ManagePage_UI._managePage').
							'" method="POST">
							<input type=submit name="btnAddPage" value ="Add new page to this category">
							<input type=hidden name="category_id" value ="'.$this->cat_id .'">
							</form>');
					
						$this->t->parse('CBlock', 'CategoryBlock', true); 
					}
					$this->t->pfp('out','ManagePage');
				}
				else
				{
					echo "I'm sorry, you do not have write permissions for any site categories.<br><br>";
				}
			}
			$common_ui->DisplayFooter();
		}
	}	
?>
