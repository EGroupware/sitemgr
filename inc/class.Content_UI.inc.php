<?php

	class Content_UI
	{
		var $common_ui;
		var $t;
		var $bo;
		var $modulebo;
		var $acl;
		var $viewable;
		var $sitelanguages;
		var $worklanguage;
		var $errormsg;

		var $public_functions = array
		(
			'_manageContent' => True
		);

		function Content_UI()
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS["phpgw"]->template;
			$this->bo = &$GLOBALS['Common_BO']->content;
			$this->acl = &$GLOBALS['Common_BO']->acl;
			$this->modulebo = &$GLOBALS['Common_BO']->modules;
			$this->viewable = array(
				'0' => lang('everybody'),
				'1' => lang('phpgw users'),
				'2' => lang('administrators'),
				'3' => lang('anonymous')
			);
			$this->sitelanguages = $GLOBALS['Common_BO']->sites->current_site['site_languages'];
			$sessionlang = $GLOBALS['phpgw']->session->appsession('worklanguage','sitemgr');
			$this->worklanguage = $sessionlang ? $sessionlang : $this->sitelanguages[0];
			$this->errormsg = array();
		}

		function _manageContent()
		{
			$this->common_ui->DisplayHeader();

			$GLOBALS['Common_BO']->globalize(array('blockid','blocktitle','blocksort','blockview','blockactif','btnSaveBlock','btnDeleteBlock','module_id','area','btnAddBlock','element','savelanguage'));
			global $blockid, $blocktitle, $blocksort,$blockview,$blockactif,$btnSaveBlock,$btnDeleteBlock, $module_id, $area, $btnAddBlock, $element, $savelanguage;
			$page_id = $_GET['page_id'];
			$cat_id = $_GET['cat_id'];

			if ($page_id)
			{
				$page = $GLOBALS['Common_BO']->pages->getPage($page_id);
				$page_or_cat_name = $page->name;
				$cat_id = $page->cat_id;
				$managelink = $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Pages_UI._managePage');
				$goto = lang('Page manager');
				$scopename = lang('Page');
			}
			elseif ($cat_id != CURRENT_SITE_ID)
			{
				$cat = $GLOBALS['Common_BO']->cats->getCategory($cat_id);
				$page_or_cat_name = $cat->name;
				$page_id = 0;
				$managelink = $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Categories_UI._manageCategories');
				$goto = lang('Category manager');
				$scopename = lang('Category');
			}
			else
			{
				$page_id = 0;
				$scopename = lang('Site');
			}

			$this->t->set_file('Managecontent', 'manage_content.tpl');
			$this->t->set_block('Managecontent','Contentarea','CBlock');
			$this->t->set_block('Contentarea','Module','MBlock');
			$this->t->set_block('Module','Moduleeditor','MeBlock');
			$this->t->set_block('Module','Moduleview','MvBlock');
			$this->t->set_block('Moduleeditor','EditorElement','EeBlock');
			$this->t->set_block('Moduleview','ViewElement','VeBlock');
			$this->t->set_var(Array(
				'lang_reset' => lang('Reset'),
				'lang_save' => lang('Save'),
				'content_manager' => lang('%1 content manager', $scopename),
				'page_or_cat_name' => ($page_or_cat_name ? (' - ' . $page_or_cat_name) : ''),
				'managelink' => ($managelink ? ('<a href="' . $managelink . '">&lt; ' . lang('Go to') . ' ' . $goto . ' &gt;</a>') : '')
				));

			if ($btnAddBlock)
			{
				if ($module_id)
				{
					$block = CreateObject('sitemgr.Block_SO',True);
					$block->module_id = $module_id;
					$block->area = $area;
					$block->page_id = $page_id;
					$block->cat_id = $cat_id;

					if (!$this->bo->addblock($block))
					{
						$this->errormsg[] = lang("You are not entitled to create module %1 on this scope",$module_id);
					}
				}
				else
				{
					$this->errormsg[] = lang("You did not choose a module.");
				}
			}
			elseif ($btnSaveBlock)
			{
				$moduleobject = $this->bo->getblockmodule($blockid);

				if ($moduleobject->validate($element))
				{
					if ($savelanguage)
					{
						//we save the language the user chooses as session variable
						$this->worklanguage = $savelanguage;
						$GLOBALS['phpgw']->session->appsession('worklanguage','sitemgr',$savelanguage);
					}

					$block = CreateObject('sitemgr.Block_SO',True);
					$block->id = $blockid;
					$block->title = $blocktitle;
					$block->sort_order = $blocksort;
					$block->view = $blockview;
					$block->actif = $blockactif ? 1 : 0;
					if (!$this->bo->saveblockdata($block,$element,$this->worklanguage))
					{
						$this->errormsg[] = lang("You are not entitled to edit block %1",$blockid);
					}
				}
				if ($moduleobject->validation_error)
				{
					$this->errormsg[] = $moduleobject->validation_error;
				}
			}
			elseif ($btnDeleteBlock)
			{
				if (!$this->bo->removeblock($blockid))
				{
					$this->errormsg[] =  lang("You are not entitled to edit block %1",$blockid);
				}
			}

			$contentareas = $this->bo->getContentAreas();
			if (is_array($contentareas))
			{
				$this->t->set_var('help', lang('You can override each content blocks default title. Be aware that not in all content areas the block title will be visible.'));

				foreach ($contentareas as $contentarea)
				{
					$permittedmodules = $this->modulebo->getcascadingmodulepermissions($contentarea,$cat_id);

					$this->t->set_var(Array(
						'area' => $contentarea,
						'addblockform' => 
							($permittedmodules ?
								('<form method="POST"><input type="hidden" value="' . $contentarea . '" name="area" />' .
									'<select style="vertical-align:middle" size="10" name="module_id">' .
									$this->inputmoduleselect($permittedmodules) .
									'</select><input type="submit" name="btnAddBlock" value="' .
									lang('Add block to content area %1',$contentarea) .
									'" /></form>') :
								lang('No modules permitted for this content area/category')
							),
						'error' => ($contentarea == $area && $this->errormsg) ? join('<br>',$this->errormsg) : '',
					));

					//we get all blocks for the page and its category, and site wide,
					//but only the current scope is editable
					//if we have just edited a block in a certain language, we request all blocks in this language
					$blocks = $this->bo->getallblocksforarea($contentarea,$cat_id,$page_id,$this->worklanguage);

					$this->t->set_var('MBlock','');

					if ($blocks)
					{
						if (count($this->sitelanguages) > 1)
						{
							$select = lang('as') . ' <select name="savelanguage">';

							foreach ($this->sitelanguages as $lang)
							{
								$selected= '';
								if ($lang == $this->worklanguage)
								{
									$selected = 'selected="selected" ';
								}
									$select .= '<option ' . $selected .'value="' . $lang . '">'. $GLOBALS['Common_BO']->getlangname($lang) . '</option>';
								}
								$select .= '</select> ';
								$this->t->set_var('savelang',$select);
						}
						while (list($id,$block) = each($blocks))
						{
							//TODO: wrap a module storage around createmodule as in template3, 
							//TODO: so that we do not create the same module object twice
							$moduleobject = $this->modulebo->createmodule($block->module_name);
							$this->t->set_var(array(
								'moduleinfo' => ($block->module_name),
								'description' => $moduleobject->description,
							));

							//if the block is in our scope and we are entitled we edit it
							if ($block->page_id == $page_id && 
								$block->cat_id == $cat_id)
							{
								$editorstandardelements = array(
									array('label' => lang('Title'),
										  'form' => ('<input type="text" name="blocktitle" value="' . 
											($block->title ? $block->title : $moduleobject->title) . '" />')
									),
									array('label' => lang('Actif'),
										  'form' => ('<input type="checkbox" name="blockactif"' .
											 ($block->actif ? 'checked="checked"' : '') . '">')
									),
									array('label' => lang('Seen by'),
										  'form' => ('<select name="blockview">' .
											$this->inputviewselect((int)$block->view) . '</select>')
									),
									array('label' => lang('Sort order'),
										  'form' => ('<input type="text" name="blocksort" size="2" value="' .
											(int)$block->sort_order . '">')
									)
								);
								$moduleobject->set_block($block);
								$editormoduleelements = $moduleobject->get_user_interface();
								$interface = array_merge($editorstandardelements,$editormoduleelements);
								$this->t->set_var(Array(
									'blockid' => $id,
									'savebutton' => lang('Save'),
									'deletebutton' => lang('Delete'),
									'contentarea' => lang('Contentarea'),
									'validationerror' => ($id == $blockid && $this->errormsg) ? join('<br>',$this->errormsg) : '',
								));
								$this->t->set_var('EeBlock','');
								while (list(,$element) = each($interface))
								{
									$this->t->set_var(Array(
										'label' => $element['label'],
										'form' => $element['form']
									));
									$this->t->parse('EeBlock','EditorElement', true);
								}
								$this->t->parse('MeBlock','Moduleeditor');
								$this->t->set_var('MvBlock','');
							}
							//otherwise we only show it
							else
							{
								if ($block->page_id)
								{
									$blockscope = lang('Page');
								}
								elseif ($block->cat_id)
								{
									$cat = $GLOBALS['Common_BO']->cats->getCategory($block->cat_id);
									$blockscope =  lang('Category') . ' - ' . $cat->name;
								}
								else
								{
									$blockscope =  lang('Site');
								}

								$viewstandardelements = array(
									array('label' => lang('Scope'),
										  'value' => $blockscope
									),
									array('label' => lang('Title'),
										  'value' => ($block->title ? $block->title : $moduleobject->title)
									),
									array('label' => lang('Actif'),
										  'value' => ($block->actif ? lang('Yes') : lang('No'))
									),
									array('label' => lang('Seen by'),
										  'value' => $this->viewable[(int)$block->view]
									),
									array('label' => lang('Sort order'),
										  'value' => (int)$block->sort_order
									)
								);
								$viewmoduleelements = array();
								while (list($argument,$argdef) = @each($moduleobject->arguments))
								{
									$value = $block->arguments[$argument];
									$viewmoduleelements[] = array(
										'label' => $argdef['label'],
										'value' => $GLOBALS['phpgw']->strip_html($value)
									);
								}
								$interface = array_merge($viewstandardelements,$viewmoduleelements);
								$this->t->set_var('VeBlock','');
								while (list(,$element) = each($interface))
								{
									$this->t->set_var(Array(
										'label' => $element['label'],
										'value' => $element['value'])
									);
									$this->t->parse('VeBlock','ViewElement', true);
								}
								$this->t->parse('MvBlock','Moduleview');
								$this->t->set_var('MeBlock','');
							}
						$this->t->parse('MBlock','Module', true);
						}
					}
					$this->t->parse('CBlock','Contentarea', true);
				}
			}
			else
			{
				$this->t->set_var('CBlock',$contentareas);
			}
			$this->t->pfp('out', 'Managecontent');
			$this->common_ui->DisplayFooter();	
		}

		function inputmoduleselect($modules)
		{
			$returnValue = '';
			while (list($id,$module) = each($modules))
			{ 
				$returnValue.='<option title="' . $module['description'] . '" value="'.$id.'">'.
					$module['module_name'].'</option>'."\n";
			}
			return $returnValue;
		}

		function inputviewselect($default)
		{
			$returnValue = '';
			foreach($this->viewable as $value => $display)
			{
				$selected = ($default == $value) ? $selected = 'selected="selected" ' : '';
				$returnValue.='<option '.$selected.'value="'.$value.'">'.
					$display.'</option>'."\n";
			}
			return $returnValue;
		}
	}

