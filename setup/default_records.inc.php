<?php
	$oProc->query("INSERT INTO phpgw_categories (cat_parent,cat_owner,cat_access,cat_appname,cat_name,cat_description,last_mod) VALUES (0,-1,'public','sitemgr','Default Website','This website has been added by setup',0)");
	$site_id = $oProc->m_odb->get_last_insert_id('phpgw_categories','cat_id');
	$oProc->query("UPDATE phpgw_categories SET cat_main = $site_id WHERE cat_id = $site_id",__LINE__,__FILE__);

	$oProc->query("select config_value FROM phpgw_config WHERE config_name='webserver_url'");
	$oProc->next_record();
	$siteurl = $oProc->f('config_value') . SEP . 'sitemgr' . SEP . 'sitemgr-site' . SEP;
	$sitedir = PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'sitemgr-site';
	$oProc->query("INSERT INTO phpgw_sitemgr_sites (site_id,site_name,site_url,site_dir,themesel,site_languages,home_page_id,anonymous_user,anonymous_passwd) VALUES ($site_id,'Default Website','$siteurl','$sitedir','3D-Fantasy','en',0,'anonymous','anonymous')");

	$dir = opendir(PHPGW_SERVER_ROOT.'/sitemgr/modules');
	while($file = readdir($dir))
	{
		$default_active = array('html','index','toc');

		if (!eregi('class.module_([^.]*).inc.php',$file,$module))
		{
			continue;
		}
		$oProc->query("INSERT INTO phpgw_sitemgr_modules (module_name) VALUES ('$module[1]')",__LINE__,__FILE__);
		if (in_array($module[1],$default_active))
		{
			$module_id = $oProc->m_odb->get_last_insert_id('phpgw_sitemgr_modules','module_id');
			$oProc->query("INSERT INTO phpgw_sitemgr_active_modules (area,cat_id,module_id) VALUES ('__PAGE__',$site_id,$module_id)",__LINE__,__FILE__);
		}
	}

	function cp_r($from,$to)
	{
		//echo "<p>cp_r($from,$to)<br>";
		if (is_file($from))
		{
			//echo "copy($from,$to)<br>";
			if (is_dir($to))
			{
				$to .= '/'.basename($from);
			}
			return copy($from,$to);
		}
		if (is_dir($from))
		{
			$to .= '/'.basename($from);
			if (!is_dir($to) && !mkdir($to))
			{
				echo "Can't mkdir($to) !!!";
				return False;
			}
			if (!($dir = opendir($from)))
			{
				echo "Can't open $from !!!";
				return False;
			}
			while($file = readdir($dir))
			{
				if ($file != '.' && $file != '..')
				{
					if (!cp_r($from.'/'.$file,$to))
					{
						return False;
					}
				}
			}
		}
		return True;
	}

	if (!file_exists(PHPGW_SERVER_ROOT.'/sitemgr-link') && is_writable(PHPGW_SERVER_ROOT))
	{
		chdir(PHPGW_SERVER_ROOT);
		if (function_exists('symlink'))
		{
			symlink('sitemgr/sitemgr-link','sitemgr-link');
			echo "Symlink to sitemgr-link created and ";
		}
		else
		{
			// copy the whole dir for our windows friends ;-)
			cp_r('sitemgr/sitemgr-link','.');
			echo "sitemgr/sitemgr-link copied to eGroupWare dir and ";
		}
	}

	if (file_exists($sitemgr_link_setup = PHPGW_SERVER_ROOT.'/sitemgr-link/setup/setup.inc.php'))
	{
		include($sitemgr_link_setup);
		$GLOBALS['setup_info']['sitemgr-link'] = $setup_info['sitemgr-link'];
		$GLOBALS['phpgw_setup']->register_app('sitemgr-link');
		echo "sitemgr-link installed\n";
	}
	else
	{
		echo "sitemgr-link NOT installed, you need to copy it from egroupware/sitemgr/sitemgr-link to egroupware/sitemgr-link and install it manually !!!";
	}

