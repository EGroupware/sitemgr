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
			$this->preferenceso->setPreference('themesel',$theme);
		}
		
		function getTheme()
		{
			return $this->preferenceso->getPreference('themesel');
		}

		function getAvailableThemes()
		{
			$pref = CreateObject('sitemgr.sitePreference_SO', True);
			$sitemgr_dir = $pref->getPreference('sitemgr-site-dir');
			$themes = $pref->getPreference('interface');
			if ((int) $themes)
			{
				$interface = 'themes';
			}
			else
			{
				$interface = 'templates';
			}
			$dirname = $sitemgr_dir . '/' . $interface . '/';
			$result_array=array();
			@$handle=opendir($dirname);
		
			if ($handle)
			{
				while (($file = readdir($handle)) !== false)
				{
					if (is_dir($dirname.$file) && substr($file,0,1)!='.' && strcmp($file,'index.html') != 0 
						&& strcmp($file,'CVS') != 0)
					{
						$result_array[]=array('value'=>$file,'display'=>$file);
					}	
				}        
			}
			else
			{
				return array(array('value'=>'','display'=>'No '.$interface.' found.'));
			}
			closedir($handle);
			return $result_array;                        
		}
		
	}

?>
