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

	class ManageTranslations_UI
	{
	  var $t;
	  var $cat_bo;
	  var $acl;
	  var $preferenceso;
	  var $sitelanguages;
	  var $common_ui;
	  var $pagebo;
	  
	  var $public_functions = array
		(
		 '_manageTranslations' => True,
		 '_translateCategory' => True,
		 '_translatePage' => True,
		);

	  function ManageTranslations_UI()
	    {
	      $this->t = $GLOBALS['phpgw']->template;
	      $this->cat_bo = CreateObject('sitemgr.Categories_BO', True);
	      $this->acl = CreateObject('sitemgr.ACL_BO', True);
	      $this->preferenceso = CreateObject('sitemgr.sitePreference_SO', true);
	      $this->sitelanguages = explode(',',$this->preferenceso->getPreference('sitelanguages'));
	      $this->common_ui = CreateObject('sitemgr.Common_UI',True);
	      $this->pagebo = CreateObject('sitemgr.Pages_BO', True);
	      
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

		function _manageTranslations()
		  {
		    $this->common_ui->DisplayHeader();

		    $this->t->set_file('ManageTranslations', 'manage_translations.tpl');
		    $this->t->set_block('ManageTranslations', 'PageBlock', 'PBlock');
		    $this->t->set_block('PageBlock', 'langexistpage', 'langpageBlock');
		    $this->t->set_block('ManageTranslations', 'CategoryBlock', 'CBlock');
		    $this->t->set_block('CategoryBlock', 'langexistcat', 'langcatBlock');
		    $this->t->set_block('ManageTranslations', 'sitelanguages', 'slBlock');

		    foreach ($this->sitelanguages as $lang)
		      {
			$this->t->set_var('sitelanguage',$lang);
			$this->t->parse('slBlock', 'sitelanguages', true);
		      }

		    $this->t->set_var(Array('translation_manager' => lang('Translation Manager'),
					    'lang_catname' => lang('Category Name')));
		    $this->cat_list = $this->cat_bo->getPermittedCatWriteNested();
		    if($this->cat_list)
		      {
			for($i = 0; $i < sizeof($this->cat_list); $i++)
			  {			

			    //setup entry in categorblock for translations of categories
			    $this->cat = $this->cat_bo->getCategory($this->cat_list[$i]);
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

			    $availablelangsforcat = $this->cat_bo->getlangarrayforcategory($category_id);
			    $this->t->set_var('langcatBlock','');
			    foreach ($this->sitelanguages as $lang)
				{
				  $this->t->set_var('catexistsinlang', in_array($lang,$availablelangsforcat) ? 'ø' : '&nbsp;');
				  $this->t->parse('langcatBlock', 'langexistcat', true);
				}
			    
			    $this->t->set_var('translatecat', 
					      '<form action="'.
					      $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.ManageTranslations_UI._translateCategory').
					      '" method="POST"><input type="submit" name="btnTranslateCategory" value="' . lang('Translate') .'">'.
					      '<input type="hidden" name="category_id" value="'.$category_id.'"></form>');

			    //setup page list
			    $this->t->set_var('PBlock', '');
			    $this->page_list = $this->pagebo->getPageIDList($this->cat_list[$i]);
			    if($this->page_list && sizeof($this->page_list)>0)
			      {
				for($j = 0; $j < sizeof($this->page_list); $j++)
				  {
				    $this->page_id =$this->page_list[$j];
				    $this->page = $this->pagebo->getPage($this->page_id,$this->sitelanguages[0]);
				    $page_description = '<i>' . lang('Page') . ': </i>'.$this->page->name.'<br><i>' . lang('Title') . ': </i>'.$this->page->title;
				    $this->t->set_var('page', $page_description);

				    $availablelangsforpage = $this->pagebo->getlangarrayforpage($this->page_id);
				    $this->t->set_var('langpageBlock','');
				    foreach ($this->sitelanguages as $lang)
				      {
					$this->t->set_var('pageexistsinlang', in_array($lang,$availablelangsforpage) ? 'ø' : '&nbsp;');
					$this->t->parse('langpageBlock', 'langexistpage', true);
				      }

				    $this->t->set_var('translatepage', 
					      '<form action="'.
					      $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.ManageTranslations_UI._translatePage').
					      '" method="POST"><input type="submit" name="btnTranslatePage" value="' . lang('Translate') .'">'.
					      '<input type="hidden" name="page_id" value="'.$this->page_id.'"></form>');
				    $this->t->parse('PBlock', 'PageBlock', true);
				  }
			      }
				
			    $this->t->parse('CBlock', 'CategoryBlock', true); 
			  }
		      }
		    else
		      {
			$this->t->set_var('category','No category is available');
		      }
		    $this->t->pfp('out', 'ManageTranslations');

		    $this->common_ui->DisplayFooter();
		  }

		function _translateCategory()
		  {
		    
		    $this->globalize(array('category_id','changelanguage','showlanguage','savelanguage','btnSaveCategory','savecatname','savecatdesc'));
		    global $category_id, $changelanguage, $showlanguage, $savelanguage, $btnSaveCategory, $savecatname, $savecatdesc;
		    
		    if ($btnSaveCategory)
		      {
			$this->cat_bo->saveCategoryLang($category_id, $savecatname, $savecatdesc, $savelanguage);
			$this->_manageTranslations();
			return;
		      }

		    $this->common_ui->DisplayHeader();
		    $this->t->set_file('TranslateCategory', 'translate_category.tpl');
		    
		    if($error)
			{
			  $this->t->set_var('error_msg',lang('You failed to fill in one or more required fields.'));
			  $this->cat->name = $savecatname;
			  $this->cat->description = $savecatdesc;
			}
			else
			{
			  $this->cat = $this->cat_bo->getCategory($category_id);
			  $showlanguage = $showlanguage ? $showlanguage : $this->sitelanguages[0];
			  $showlangdata = $this->cat_bo->getCategory($category_id,$showlanguage);
			  $savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[1]; 
			  $savelangdata = $this->cat_bo->getCategory($category_id,$savelanguage);

			  $this->t->set_var(Array('translate' => lang('Translate Category'),
						  'lang_refresh' => '<input type="submit" value="' . lang('Refresh') .'" name="changelanguage">'));
			  $select = '<select name="showlanguage">';
			    foreach ($this->sitelanguages as $lang)
			      {
				$selected= '';
				if ($lang == $showlanguage)
				  {
				    $selected = 'selected="selected" ';
				  }
				$select .= '<option ' . $selected .'value="' . $lang . '">'. $this->getlangname($lang) . '</option>';
			      }
			    $select .= '</select> ';
			    $this->t->set_var('showlang', $select);

			    $select = '<select name="savelanguage">';
			    foreach ($this->sitelanguages as $lang)
			      {
				$selected= '';
				if ($lang == $savelanguage)
				  {
				    $selected = 'selected="selected" ';
				  }
				$select .= '<option ' . $selected .'value="' . $lang . '">'. $this->getlangname($lang) . '</option>';
			      }
			    $select .= '</select>';
			    $this->t->set_var('savelang', $select);
			    
			    $this->t->set_var(Array('catid' => $category_id,
						    'lang_catname' => lang('Category Name'),
						    'showcatname' => $showlangdata->name,
						    'savecatname' => $savelangdata->name,
						    'lang_catdesc' => lang('Category Description'),
						    'showcatdesc' => $showlangdata->description,
						    'savecatdesc' => $savelangdata->description,
						    'lang_reset' => lang('Reset'),
						    'lang_save' => lang('Save')));
			    $this->t->pfp('out','TranslateCategory');
			}
		    $this->common_ui->DisplayFooter();
		  }

		function _translatePage()
		  {

		    $this->globalize(array('page_id','changelanguage','showlanguage','savelanguage','btnSavePage','savepagetitle','savepagesubtitle','savepagecontent'));
		    global $page_id, $changelanguage, $showlanguage, $savelanguage, $btnSavePage, $savepagetitle, $savepagesubtitle, $savepagecontent;
		    
		    if ($btnSavePage)
		      {
			$this->page->id = $page_id;
			$this->page->title = $savepagetitle;
			$this->page->subtitle = $savepagesubtitle;
			$this->page->content = $savepagecontent; 
			$this->pagebo->savePageLang($this->page, $savelanguage);
			$this->_manageTranslations();
			return;
		      }

		    $this->common_ui->DisplayHeader();

		    $this->t->set_file('TranslatePage', 'translate_page.tpl');

		    if($error)
		      {
			$this->t->set_var('error_msg',lang('You failed to fill in one or more required fields.'));
			$this->page->tite = $savepagetitle;
			$this->page->subtitle = $savepagesubtitle;
			$this->page->content = $savepagecontent;
		      }
		    else
		      {
			 $this->page = $this->pagebo->getPage($page_id);
			 $showlanguage = $showlanguage ? $showlanguage : $this->sitelanguages[0];
			 $showlangdata = $this->pagebo->getPage($page_id,$showlanguage);
			 $savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[1]; 
			 $savelangdata = $this->pagebo->getPage($page_id,$savelanguage);

			 $this->t->set_var(Array('translate' => lang('Translate Page'),
						  'lang_refresh' => '<input type="submit" value="' . lang('Refresh') .'" name="changelanguage">'));
			 $select = '<select name="showlanguage">';
			 foreach ($this->sitelanguages as $lang)
			   {
			     $selected= '';
			     if ($lang == $showlanguage)
			       {
				 $selected = 'selected="selected" ';
			       }
			     $select .= '<option ' . $selected .'value="' . $lang . '">'. $this->getlangname($lang) . '</option>';
			   }
			 $select .= '</select> ';
			 $this->t->set_var('showlang', $select);
			 
			 $select = '<select name="savelanguage">';
			 foreach ($this->sitelanguages as $lang)
			   {
			     $selected= '';
			     if ($lang == $savelanguage)
			       {
				 $selected = 'selected="selected" ';
			       }
			     $select .= '<option ' . $selected .'value="' . $lang . '">'. $this->getlangname($lang) . '</option>';
			   }
			 $select .= '</select>';
			 $this->t->set_var('savelang', $select);

			 $trans = array('{' => '&#123;', '}' => '&#125;');
			 $this->t->set_var(Array('pageid' => $page_id,
						 'lang_pagename' => lang('Page Name'),
						 'pagename' => $this->page->name,
						 'lang_pagetitle' => lang('Page Title'),
						 'showpagetitle' => $showlangdata->title,
						 'savepagetitle' => $savelangdata->title,
						 'lang_pagesubtitle' => lang('Page Subtitle'),
						 'showpagesubtitle' => $showlangdata->subtitle,
						 'savepagesubtitle' => $savelangdata->subtitle,
						 'lang_pagecontent' => lang('Page Content'),
						 'showpagecontent' => strtr($GLOBALS['phpgw']->strip_html($showlangdata->content),$trans),
						 'savepagecontent' => strtr($GLOBALS['phpgw']->strip_html($savelangdata->content),$trans),
						 'lang_reset' => lang('Reset'),
						 'lang_save' => lang('Save')));
			 $this->t->pfp('out','TranslatePage');
		      }
		    $this->common_ui->DisplayFooter();
		  }
	}