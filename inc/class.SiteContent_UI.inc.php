<?php

	class SiteContent_UI
	{
		var $t;
		var $headerfooterbo;
		var $acl;
		var $prefs_so;
		
		var $public_functions = array
		(
			'_editHeaderAndFooter' => True,
		);

		function SiteContent_UI()
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->t = $GLOBALS["phpgw"]->template;
			$this->headerfooterbo = &$GLOBALS['Common_BO']->headerfooter;
			$this->acl = &$GLOBALS['Common_BO']->acl;
			$this->prefs_so = CreateObject('sitemgr.sitePreference_SO', True);
			$this->sitelanguages = explode(',',$this->prefs_so->getPreference('sitelanguages'));
		}
		
		function _editHeaderAndFooter()
		{
			$GLOBALS['Common_BO']->globalize(array('btnSave','header','footer'));
			global $btnSave;
			global $header;
			global $footer;

			$this->common_ui->DisplayHeader();

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
				    $this->t->set_var(array('header_editor' => lang('Header Editor') . ' - ' . $GLOBALS['Common_BO']->getlangname($lang),
							    'footer_editor' => lang('Footer Editor') . ' - ' . $GLOBALS['Common_BO']->getlangname($lang),
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
	
				$this->t->set_var('actionurl', $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.SiteContent_UI._editHeaderAndFooter'));
				$this->t->pfp('out', 'EditHeaderAndFooter');
			}
			else
			{
				echo lang("You must be an admin to edit the site header and footer.") ."<br><br>";
			}
			$this->common_ui->DisplayFooter();
		}
	}
?>
