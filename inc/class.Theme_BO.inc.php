<?php
	class Theme_BO
	{
		var $acl;
		var $preferenceso;
		var $theme;

		function Theme_BO()
		{
			$this->acl = CreateObject('ACL', True);
			$this->preferenceso = CreateObject('sitemgr.sitePreferences_SO', True);
		}

		function setTheme($theme)
		{
			if ($this->preferenceso->setPreference($theme))
				echo "theme set.";
			else
				echo "Error, theme not set.";
		}
		
		function getTheme($theme)
		{
			return $this->preferenceso->getPreference($theme);
		}

		function getAvailableTheme()
		{
			// TBD
		}
		
	}

?>
