<?php
	class headerFooter_BO
	{
		var $acl;
		var $preferenceso;
		var $header;
		var $footer;

		function headerFooter_BO()
		{
			$this->preferenceso = CreateObject('sitemgr.sitePreference_SO', true);
			$this->acl = CreateObject('sitemgr.ACL_BO', True);
		}

		function getSiteHeader($lang)
		{
			return $this->preferenceso->getPreference('siteheader-'. $lang);
		}

		function getSiteFooter($lang)
		{	
			return $this->preferenceso->getPreference('sitefooter-'. $lang);	
		}

		function setSiteHeader($header,$lang)
		{
			if ($this->acl->is_admin())
			{
				if($this->preferenceso->setPreference('siteheader-'. $lang,$header))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		function setSiteFooter($footer,$lang)
		{
			if ($this->acl->is_admin())
			{
				if($this->preferenceso->setPreference('sitefooter-'. $lang,$footer))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

	}

?>
