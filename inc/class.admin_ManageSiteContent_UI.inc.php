<?php
	class Admin_ManageSiteContent_UI
	{
		var $t;
		var $headerfooterbo;
		var $acl;
		var $prefs_so;
		
		var $public_functions = array
		(
			'_editHeaderAndFooter' => True
		);

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

		function admin_ManageSiteContent_UI()
		{	
			$this->globalize(array('btnSave','btnReset','btnHome','header','footer'));
			global $btnSave;
			global $btnReset;
			global $btnHome;
			global $header;
			global $footer;
			
			$this->t = $GLOBALS["phpgw"]->template;
			$this->headerfooterbo = CreateObject('sitemgr.headerFooter_BO', True);
			$this->acl = CreateObject('sitemgr.ACL_BO');
			$this->prefs_so = CreateObject('sitemgr.sitePreference_SO', True);
			$this->sitelanguages = explode(',',$this->prefs_so->getPreference('sitelanguages'));
		}
		
		function _editHeaderAndFooter()
		{
			$this->globalize(array('btnSave','header','footer'));
			global $btnSave;
			global $header;
			global $footer;

			$common_ui = CreateObject('sitemgr.Common_UI',True);
			$common_ui->DisplayHeader();

			if ($this->acl->is_admin())
			{
				$this->t->set_file('EditHeaderAndFooter', 'site_format_manager.tpl');
				$this->t->set_block('EditHeaderAndFooter','Header_Editor','HBlock');
				$this->t->set_block('EditHeaderAndFooter','Footer_Editor','FBlock');

				if ($btnSave)
				{
				  foreach ($this->sitelanguages as $lang)
				  {
					$this->headerfooterbo->SetSiteHeader($header[$lang],$lang);
					$this->headerfooterbo->SetSiteFooter($footer[$lang],$lang);
				  }
					$this->t->set_var('message','<b>' .lang('Saved') . '</b>.  <a href="'.$GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.MainMenu_UI.DisplayMenu').'">' . lang('Return to main menu.') . '</a><br>');
					//$this->headerfooterbo->SetTheme($this->theme);
				}

				$this->t->set_var(array
				(
					'site_format_manager' => lang('Site Format Manager'),
					'lang_reset' => lang('Reset'),
					'lang_save' => lang('Save')
				));

				foreach ($this->sitelanguages as $lang)
				  {
				    $this->t->set_var(array('header_editor' => lang('Header Editor') . ' - ' . $this->getlangname($lang),
							    'footer_editor' => lang('Footer Editor') . ' - ' . $this->getlangname($lang),
							    'header' => $this->headerfooterbo->getSiteHeader($lang),
							    'footer' => $this->headerfooterbo->getSiteFooter($lang),
							    'textarea_header' => 'header[' . $lang . ']',
							    'textarea_footer' => 'footer[' . $lang . ']'));
				    $this->t->parse('HBlock','Header_Editor',True);
				    $this->t->parse('FBlock','Footer_Editor',True);
				  }
				
				if ($btnReset)
				{
					$btnReset = false;
				}
	
				$this->t->set_var('actionurl', $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.admin_ManageSiteContent_UI._editHeaderAndFooter'));
				$this->t->pfp('out', 'EditHeaderAndFooter');
			}
			else
			{
				echo lang("You must be an admin to edit the site header and footer.") ."<br><br>";
			}
			$common_ui->DisplayFooter();
		}
	}
?>
