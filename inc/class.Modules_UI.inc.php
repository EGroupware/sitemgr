<?php

	class Modules_UI
	{
		var $common_ui;
		var $t;
		var $bo;
		var $acl;
		var $modules;
		var $errormsg;

		var $public_functions = array
		(
			'_manageModules' => True,
			'findmodules' => True,
			'_configureModule' => True
		);

		function Modules_UI()
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS['phpgw']->template;
			$this->bo = &$GLOBALS['Common_BO']->modules;
			$this->acl = &$GLOBALS['Common_BO']->acl;
		}

		function _manageModules()
		{
			$this->common_ui->DisplayHeader();

			if ($this->acl->is_admin())
			{
				$GLOBALS['Common_BO']->globalize(array('btnselect','inputmodules','inputarea'));
				global $btnselect,$inputmodules,$inputarea;
				$cat_id = $_GET['cat_id'];
				$scopename = lang(($cat_id) ? 'Category' : 'Site');

				$this->modules = $this->bo->getallmodules();

				if ($cat_id)
				{
						$catbo = CreateObject('sitemgr.Categories_BO');
						$cat = $catbo->getCategory($cat_id);
						$cat_name = $cat->name;
						$managelink = $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Categories_UI._manageCategories');
						$goto = lang('Category manager');
				}

				$this->t->set_file('Managemodules', 'manage_modules.tpl');
				$this->t->set_block('Managemodules','Contentarea','CBlock');
				$this->t->set_var(array(
					'module_manager' => lang('Module manager'),
					'help' => 'You can choose the modules that can be used on the site. The first list is a sort of master list, that is consulted if you do not configure lists specific to contentareas or (sub)categories. Then you can choose lists specific to each content area. In the category manager these lists can be overriden for each (sub)category.',
					'lang_findmodules' => lang('Register new modules'),
					'cat_name' => ($cat_name ? (' - ' . $cat_name) : ''),
					'managelink' => ($managelink ? ('<a href="' . $managelink . '">&lt; ' . lang('Go to') . ' ' . $goto . ' &gt;</a>') : '')
				));
				$link_data['cat_id'] = $cat_id;
				$link_data['menuaction'] = "sitemgr.Modules_UI.findmodules";
				$this->t->set_var('findmodules', $GLOBALS['phpgw']->link('/index.php',$link_data));
				$link_data['menuaction'] = "sitemgr.Modules_UI._configureModule";
				$this->t->set_var('configureurl', $GLOBALS['phpgw']->link('/index.php',$link_data));
				$contentareas = $GLOBALS['Common_BO']->content->getContentAreas();
				array_unshift($contentareas,'__PAGE__');

				if ($btnselect)
				{
					$this->bo->savemodulepermissions($inputarea,$cat_id,$inputmodules);
				}

				foreach ($contentareas as $contentarea)
				{
					$permittedmodulesconfigured = $this->bo->getpermittedmodules($contentarea,$cat_id);
					$permittedmodulescascading = $this->bo->getcascadingmodulepermissions($contentarea,$cat_id);

					$this->t->set_var(Array(
						'title' => ($contentarea == '__PAGE__') ? 
							lang('Master list of permitted modules') : 
							lang('List of permitted modules specific to content area %1',$contentarea),
						'contentarea' => $contentarea,
						'selectmodules' => $this->inputmoduleselect(array_keys($permittedmodulesconfigured)),
						'configuremodules' => $this->inputmoduleconfigure($permittedmodulescascading),
						'error' => ($contentarea == $inputarea && $this->errormsg) ? $this->errormsg : '',
					));
					$this->t->parse('CBlock','Contentarea', true);
				}
				$this->t->pfp('out', 'Managemodules');
			}
			else
			{
				echo lang("You must be an admin to manage module properties.") ."<br><br>";
			}
			$this->common_ui->DisplayFooter();
		}

		function findmodules()
		{
			$this->bo->findmodules();
			$this->_manageModules();
		}

		function _configureModule()
		{
			if ($this->acl->is_admin())
			{
				$GLOBALS['Common_BO']->globalize(array('btnSaveProperties','btnDeleteProperties','inputmodule_id','inputarea','element'));
				global $btnSaveProperties,$btnDeleteProperties,$inputarea,$inputmodule_id,$element;

				if (!$inputmodule_id)
				{
					$this->errormsg = lang("You did not choose a module.");
					$this->_manageModules();
					return;
				}
				$cat_id = $_GET['cat_id'];
				$scopename = lang(($cat_id) ? 'Category' : 'Site');


				if ($btnSaveProperties)
				{
					$this->bo->savemoduleproperties($inputmodule_id,$element,$inputarea,$cat_id);
					$this->_manageModules();
					return;
				}
				elseif ($btnDeleteProperties)
				{
					$this->bo->deletemoduleproperties($inputmodule_id,$inputarea,$cat_id);
					$this->_manageModules();
					return;
				}

				$this->common_ui->DisplayHeader();
				
				if ($cat_id)
				{
						$catbo = CreateObject('sitemgr.Categories_BO');
						$cat = $catbo->getCategory($cat_id);
						$cat_name = $cat->name;
				}

				$this->t->set_file('Editproperties', 'edit_properties.tpl');
				$this->t->set_block('Editproperties','EditorElement','EBlock');

				$module = $this->bo->getmodule($inputmodule_id);
				$moduleobject = $this->bo->createmodule($module['app_name'],$module['module_name']);
				$blockcontext = CreateObject('sitemgr.Block_SO',True);
				$blockcontext->module_id = $inputmodule_id;
				$blockcontext->area = $inputarea;
				$blockcontext->cat_id = $cat_id;
				$moduleobject->set_block($blockcontext);

				$editorstandardelements = array(
					array('label' => lang('Title'),
						  'form' => $moduleobject->title
					)
				);
				$editormoduleelements = $moduleobject->properties ? $moduleobject->get_admin_interface() : False;
				$interface = array_merge($editorstandardelements,$editormoduleelements);
				while (list(,$element) = each($interface))
				{
					$this->t->set_var(Array(
						'label' => $element['label'],
						'form' => $element['form'])
					);
					$this->t->parse('EBlock','EditorElement', true);				
				}

				$this->t->set_var(Array(
					'module_edit' => lang(
						'Edit properties of module %1 for %2 with scope %3',
						$module['app_name'].'.'.$module['module_name'],
						($inputarea == '__PAGE__' ? 'the whole page' : ('Contentarea ' . $inputarea)),
						($cat_id ? ('category ' . $cat_name) : ' the whole site')
					),
					'module_id' => $inputmodule_id,
					'contentarea' => $inputarea,
					'savebutton' => ($editormoduleelements ? 
						'<input type="submit" value="Save" name="btnSaveProperties" />' :
						lang('There are no properties defined for this module')
					),
					'deletebutton' => $properties === False ? '' : '<input type="submit" value="Delete" name="btnDeleteProperties" />'
					)
				);
				$link_data['cat_id'] = $cat_id;
				$link_data['menuaction'] = "sitemgr.Modules_UI._manageModules";
				$this->t->set_var('backlink',
					'<a href="' . $GLOBALS['phpgw']->link('/index.php',$link_data) . 
					'">&lt; ' . lang('Back to module manager') . ' &gt;</a>'
				);

				$this->t->pfp('out', 'Editproperties');
			}
			else
			{
				$this->common_ui->DisplayHeader();
				echo lang("You must be an admin to manage module properties.") ."<br><br>";
			}
			$this->common_ui->DisplayFooter();
		}

		function inputmoduleselect($permitted)
		{
			$returnValue = '';
			reset($this->modules);
			while (list($id,$module) = @each($this->modules))
			{ 
				$selected = (in_array($id,$permitted)) ? $selected = 'selected="selected" ' : '';
				$returnValue.='<option title="' . $module['description'] . '" ' . $selected . 'value="' .$id . '">' .
					$module['app_name'].'.'.	$module['module_name'].'</option>'."\n";
			}
			return $returnValue;
		}

		function inputmoduleconfigure($permitted)
		{
			$returnValue = '';
			while (list($id,$module) = @each($permitted))
			{ 
				$returnValue.='<option title="' . $module['description'] . '" value="'.$id.'">'.
					$module['app_name'].'.'.	$module['module_name'].'</option>'."\n";
			}
			return $returnValue;
		}
	}