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

		function getSiteHeader()
		{
			return $this->preferenceso->getPreference('siteheader');
		}

		function getSiteFooter()
		{	
			return $this->preferenceso->getPreference('sitefooter');	
		}

		function setSiteHeader($header)
		{
			if ($this->acl->is_admin())
			{
				if($this->preferenceso->setPreference('siteheader',$header))
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

		function setSiteFooter($footer)
		{
			if ($this->acl->is_admin())
			{
				if($this->preferenceso->setPreference('sitefooter',$footer))
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
