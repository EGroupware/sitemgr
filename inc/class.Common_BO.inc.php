<?php

	class Common_BO
	{
		var $acl,$theme,$pages,$cats,$content,$modules,$headerfooter;

		function Common_BO()
		{
			$this->sites = CreateObject('sitemgr.Sites_BO',True);
			$this->acl = CreateObject('sitemgr.ACL_BO',True);
			$this->theme = CreateObject('sitemgr.Theme_BO',True);
			$this->pages = CreateObject('sitemgr.Pages_BO',True);
			$this->cats = CreateObject('sitemgr.Categories_BO',True);
			$this->content = CreateObject('sitemgr.Content_BO',True);
			$this->modules = CreateObject('sitemgr.Modules_BO',True);
//			$this->headerfooter = CreateObject('sitemgr.headerFooter_BO', True);
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

		function getlangname($lang)
		  {
		    $GLOBALS['phpgw']->db->query("select lang_name from phpgw_languages where lang_id = '$lang'",__LINE__,__FILE__);
		    $GLOBALS['phpgw']->db->next_record();
		    return $GLOBALS['phpgw']->db->f('lang_name');
		  }
	}
?>
