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

	class Translations_UI
	{
		var $t;
		var $cat_bo;
		var $acl;
		var $preferenceso;
		var $sitelanguages;
		var $common_ui;
		var $pagebo;
		var $contentbo;
		var $modulebo;

		var $public_functions = array
		(
			'_manageTranslations' => True,
			'_translateCategory' => True,
			'_translatePage' => True,
			'_translateSitecontent' => True,
		);

		function Translations_UI()
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS['phpgw']->template;
			$this->cat_bo = &$GLOBALS['Common_BO']->cats;
			$this->acl = &$GLOBALS['Common_BO']->acl;
			$this->preferenceso = CreateObject('sitemgr.sitePreference_SO', true);
			$this->sitelanguages = explode(',',$this->preferenceso->getPreference('sitelanguages'));
			$this->pagebo = &$GLOBALS['Common_BO']->pages;
			$this->contentbo = &$GLOBALS['Common_BO']->content;
			$this->modulebo = &$GLOBALS['Common_BO']->modules;
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

			$this->t->set_var(Array(
				'translation_manager' => lang('Translation Manager'),
				'lang_catname' => lang('Category Name'),
				'translate_site_content' => $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Translations_UI._translateSitecontent'),
				'lang_site_content' => lang('Translate site-wide content blocks'),
				'colspan' => (count($this->sitelanguages) + 2)
			));
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
						$GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Translations_UI._translateCategory').
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
								$GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Translations_UI._translatePage').
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
			$GLOBALS['Common_BO']->globalize(array('category_id','changelanguage','showlanguage','savelanguage','btnSaveCategory','savecatname','savecatdesc','btnSaveBlock','element','blockid','blocktitle'));
			global $category_id, $changelanguage, $showlanguage, $savelanguage, $btnSaveCategory, $savecatname, $savecatdesc,$btnSaveBlock;

			if ($btnSaveCategory)
			{
				$this->cat_bo->saveCategoryLang($category_id, $savecatname, $savecatdesc, $savelanguage);
				$this->_manageTranslations();
				return;
			}
			elseif ($btnSaveBlock)
			{
				$this->save_block();
			}

			$this->common_ui->DisplayHeader();
			$this->t->set_file('TranslateCategory', 'translate_category.tpl');
			$this->t->set_block('TranslateCategory','Blocktranslator','Tblock');
			$this->t->set_block('Blocktranslator','EditorElement','Eblock');
			
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

				$this->templatehelper();
				$this->t->set_var(Array(
					'translate' => lang('Translate Category'),
					'catid' => $category_id,
					'lang_catname' => lang('Category Name'),
					'showcatname' => $showlangdata->name,
					'savecatname' => $savelangdata->name,
					'lang_catdesc' => lang('Category Description'),
					'showcatdesc' => $showlangdata->description,
					'savecatdesc' => $savelangdata->description,
				));

				//Content blocks
				$this->process_blocks($this->contentbo->getblocksforscope($category_id,0));
				$this->t->pfp('out','TranslateCategory');
			}
			$this->common_ui->DisplayFooter();
		}

		function _translatePage()
		{
			$GLOBALS['Common_BO']->globalize(array('page_id','changelanguage','showlanguage','savelanguage','btnSavePage','savepagetitle','savepagesubtitle','btnSaveBlock','element','blockid','blocktitle'));
			global $page_id, $changelanguage, $showlanguage, $savelanguage, $btnSavePage, $savepagetitle, $savepagesubtitle,$btnSaveBlock;
			
			if ($btnSavePage)
			{
				$this->page->id = $page_id;
				$this->page->title = $savepagetitle;
				$this->page->subtitle = $savepagesubtitle;
				$this->pagebo->savePageLang($this->page, $savelanguage);
				$this->_manageTranslations();
				return;
			}
			elseif ($btnSaveBlock)
			{
				$this->save_block();
			}
			$this->common_ui->DisplayHeader();

			$this->t->set_file('TranslatePage', 'translate_page.tpl');
			$this->t->set_block('TranslatePage','Blocktranslator','Tblock');
			$this->t->set_block('Blocktranslator','EditorElement','Eblock');

			//TODO: error handling seems not correct
			if($error)
			{
				$this->t->set_var('error_msg',lang('You failed to fill in one or more required fields.'));
				$this->page->title = $savepagetitle;
				$this->page->subtitle = $savepagesubtitle;
			}
			else
			{
				$this->page = $this->pagebo->getPage($page_id);
				$showlanguage = $showlanguage ? $showlanguage : $this->sitelanguages[0];
				$showlangdata = $this->pagebo->getPage($page_id,$showlanguage);
				$savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[1]; 
				$savelangdata = $this->pagebo->getPage($page_id,$savelanguage);

				$this->templatehelper();
				$this->t->set_var(Array(
					'translate' => lang('Translate Page'),
					'pageid' => $page_id,
					'lang_pagename' => lang('Page Name'),
					'pagename' => $this->page->name,
					'lang_pagetitle' => lang('Page Title'),
					'showpagetitle' => $showlangdata->title,
					'savepagetitle' => $savelangdata->title,
					'lang_pagesubtitle' => lang('Page Subtitle'),
					'showpagesubtitle' => $showlangdata->subtitle,
					'savepagesubtitle' => $savelangdata->subtitle,
				));

				//Content blocks
				$this->process_blocks($this->contentbo->getblocksforscope($this->page->cat_id,$page_id));
				$this->t->pfp('out','TranslatePage');
			}
			$this->common_ui->DisplayFooter();
		}

		function _translateSitecontent()
		{
			$GLOBALS['Common_BO']->globalize(array('changelanguage','showlanguage','savelanguage','btnSaveBlock','element','blockid','blocktitle'));
			global $changelanguage, $showlanguage, $savelanguage, $btnSaveBlock;

			if ($btnSaveBlock)
			{
				$this->save_block();
			}

			$this->common_ui->DisplayHeader();
			$this->t->set_file('TranslateSitecontent', 'translate_sitecontent.tpl');
			$this->t->set_block('TranslateSitecontent','Blocktranslator','Tblock');
			$this->t->set_block('Blocktranslator','EditorElement','Eblock');

			$showlanguage = $showlanguage ? $showlanguage : $this->sitelanguages[0];
			$savelanguage = $savelanguage ? $savelanguage : $this->sitelanguages[1]; 

			$this->templatehelper();

			$this->process_blocks($this->contentbo->getblocksforscope(0,0));
			$this->t->pfp('out','TranslateSitecontent');
		}

		function process_blocks($blocks)
		{
			global $showlanguage,$savelanguage;

			while (list($id,$block) = @each($blocks))
			{
				$moduleobject = $this->modulebo->createmodule($block->app_name,$block->module_name);
				$this->t->set_var('moduleinfo',($block->app_name.'.'.$block->module_name));

				$savelangdata = $this->contentbo->getlangblockdata($id,$savelanguage);
				$showlangdata = $this->contentbo->getlangblockdata($id,$showlanguage);
				$translatorstandardelements = array(
					array('label' => lang('Title'),
						  'value' => ($showlangdata->title ? $showlangdata->title : $moduleobject->title),
						  'form' => ('<input type="text" name="blocktitle" value="' . 
							($savelangdata->title ? $savelangdata->title : $moduleobject->title) . '" />')
					)
				);
				$block->arguments = $showlangdata->arguments;
				$moduleobject->set_block($block);
				$saveblock = $block;
				$saveblock->arguments = $savelangdata->arguments;
				$translatormoduleelements = $moduleobject->get_translation_interface($block,$saveblock);

				$interface = array_merge($translatorstandardelements,$translatormoduleelements);

				$this->t->set_var('Eblock','');
				while (list(,$element) = each($interface))
				{
					$this->t->set_var(Array(
						'label' => $element['label'],
						'value' => $element['value'],
						'form' => $element['form']
					));
					$this->t->parse('Eblock','EditorElement', true);
				}
				$this->t->set_var(Array(
					'blockid' => $id,
				));
				$this->t->parse('Tblock','Blocktranslator', true);
			}
		}

		function save_block()
		{
			global $blockid, $element,$blocktitle,$savelanguage;

			$moduleobject = $this->contentbo->getblockmodule($blockid);

			if ($moduleobject->validate($element))
			{
				$block = CreateObject('sitemgr.Block_SO',True);
				$block->id = $blockid;
				$block->title = $blocktitle;
				if (!$this->contentbo->saveblockdatalang($block,$element,$savelanguage))
				{
					$this->t->set_var('validationerror', lang("You are not entitled to edit block %1",$blockid));;
				}
			}
			else
			{
				$this->t->set_var('validationerror', $module->get_validationerror());
			}
		}

		function templatehelper()
		{
			global $showlanguage,$savelanguage;
			
			$this->t->set_var(Array(
				'lang_refresh' => '<input type="submit" value="' . lang('Refresh') .'" name="changelanguage">',
				'savebutton' => '<input type="submit" value="Save" name="btnSaveBlock" />',
				'lang_reset' => lang('Reset'),
				'lang_save' => lang('Save')
			));
			$select = '<select name="showlanguage">';
			foreach ($this->sitelanguages as $lang)
			{
				$selected= '';
				if ($lang == $showlanguage)
				{
					$selected = 'selected="selected" ';
				}
				$select .= '<option ' . $selected .'value="' . $lang . '">'. $GLOBALS['Common_BO']->getlangname($lang) . '</option>';
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
				$select .= '<option ' . $selected .'value="' . $lang . '">'. $GLOBALS['Common_BO']->getlangname($lang) . '</option>';
			}
			$select .= '</select>';
			$this->t->set_var('savelang', $select);
		}
	}