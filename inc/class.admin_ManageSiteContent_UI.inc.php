<?php
	class Admin_ManageSiteContent_UI
	{
		var $t;
		var $headerfooterbo;
		var $acl;
		
		var $public_functions = array
		(
			'_editHeaderAndFooter' => True
		);

		function admin_ManageSiteContent_UI()
		{	
			global $btnSave;
			global $btnReset;
			global $btnHome;
			global $header;
			global $footer;
			
			$this->t = $GLOBALS["phpgw"]->template;
			$this->headerfooterbo = CreateObject('sitemgr.headerFooter_BO', True);
			$this->acl = CreateObject('sitemgr.ACL_BO');
		}
		
		function _editHeaderAndFooter()
		{
			global $btnSave;
			global $header;
			global $footer;

			$common_ui = CreateObject('sitemgr.Common_UI',True);
			$common_ui->DisplayHeader();

			if ($this->acl->is_admin())
			{
				$this->t->set_file('EditHeaderAndFooter', 'site_format_manager.tpl');

				if ($btnSave)
				{
					$this->headerfooterbo->SetSiteHeader($header);
					$this->headerfooterbo->SetSiteFooter($footer);
					$this->t->set_var('message','<b>Saved.</b>  <a href="'.$GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.MainMenu_UI.DisplayMenu').'">Return to main menu.</a><br>');
					//$this->headerfooterbo->SetTheme($this->theme);
				}
	
				$this->header = $this->headerfooterbo->getSiteHeader();
				$this->footer = $this->headerfooterbo->getSiteFooter();
				
				$this->t->set_var(array
				(
					'header' => $this->header,
					'footer' => $this->footer
				));
	
				if ($btnReset)
				{
					$btnReset = false;
				}
	
				$this->t->set_var('actionurl', $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.admin_ManageSiteContent_UI._editHeaderAndFooter'));
				$this->t->pfp('out', 'EditHeaderAndFooter');
			}
			else
			{
				echo "You must be an admin to edit the site header and footer.<br><br>";
			}
			$common_ui->DisplayFooter();
		}
	}
?>
