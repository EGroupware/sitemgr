<?php
	$oProc->query("INSERT INTO phpgw_categories (cat_parent,cat_owner,cat_access,cat_appname,cat_name,cat_description,last_mod) VALUES (0,-1,'public','sitemgr','Default Website','This website has been added by setup',0)");
	$site_id = $oProc->m_odb->get_last_insert_id('phpgw_categories','cat_id');
	$oProc->query("UPDATE phpgw_categories SET cat_main = $site_id WHERE cat_id = $site_id",__LINE__,__FILE__);

	$oProc->query("select config_value FROM phpgw_config WHERE config_name='webserver_url'");
	$oProc->next_record();
	$siteurl = $oProc->f('config_value') . SEP . 'sitemgr' . SEP . 'sitemgr-site' . SEP;
	$sitedir = PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'sitemgr-site';
	$oProc->query("INSERT INTO phpgw_sitemgr_sites (site_id,site_name,site_url,site_dir,themesel,site_languages,home_page_id,anonymous_user,anonymous_passwd) VALUES ($site_id,'Default Website','$siteurl','$sitedir','3D-Fantasy','en,de',0,'anonymous','anonymous')");

	// give Admins group rights vor sitemgr and for the created default-site
	$admingroup = $GLOBALS['phpgw_setup']->add_account('Admins','Admin','Group',False,False);
	$GLOBALS['phpgw_setup']->add_acl('sitemgr','run',$admingroup);
	$GLOBALS['phpgw_setup']->add_acl('sitemgr',"L$site_id",$admingroup);
	// give Default group rights vor sitemgr-link
	$defaultgroup = $GLOBALS['phpgw_setup']->add_account('Default','Default','Group',False,False);
	$GLOBALS['phpgw_setup']->add_acl('sitemgr-link','run',$defaultgroup);

	// Create anonymous user for sitemgr
	$GLOBALS['phpgw_setup']->add_account('NoGroup','No','Rights',False,False);
	$anonymous = $GLOBALS['phpgw_setup']->add_account('anonymous','SiteMgr','User','anonymous','NoGroup');
	// give the anonymous user only sitemgr-link-rights
	$GLOBALS['phpgw_setup']->add_acl('sitemgr-link','run',$anonymous);
	$GLOBALS['phpgw_setup']->add_acl('phpgwapi','anonymous',$anonymous);

	// register all modules and allow the everywhere on the page
	$dir = opendir(PHPGW_SERVER_ROOT.'/sitemgr/modules');
	while($file = readdir($dir))
	{
		if (!eregi('class.module_([^.]*).inc.php',$file,$parts))
		{
			continue;
		}
		$module = $parts[1];
		$oProc->query("INSERT INTO phpgw_sitemgr_modules (module_name) VALUES ('$module')",__LINE__,__FILE__);
		$id = $module_id[$module] = $oProc->m_odb->get_last_insert_id('phpgw_sitemgr_modules','module_id');
		$oProc->query("INSERT INTO phpgw_sitemgr_active_modules (area,cat_id,module_id) VALUES ('__PAGE__',$site_id,$id)",__LINE__,__FILE__);
	}

	// create some sample categories for the site
	foreach(array(
		'other'  => 'one more',
		'sample' => 'sample category',
	) as $name => $descr)
	{
		$oProc->query("INSERT INTO phpgw_categories (cat_main,cat_parent,cat_level,cat_owner,cat_access,cat_appname,cat_name,cat_description,cat_data,last_mod) VALUES ($site_id,$site_id,1,-1,'public','sitemgr','$name','$descr','0',".time().")");
		$cat_id = $cats[$name] = $oProc->m_odb->get_last_insert_id('phpgw_categories','cat_id');
		$oProc->query("INSERT INTO phpgw_sitemgr_categories_lang (cat_id,lang,name,description) VALUES ($cat_id,'en','$name','$descr')");
		$oProc->query("INSERT INTO phpgw_sitemgr_categories_state (cat_id,state) VALUES ($cat_id,2)");
		foreach(array($admingroup => 3,$defaultgroup => 1,$anonymous => 1) as $account => $rights)
		{
			$GLOBALS['phpgw_setup']->add_acl('sitemgr',"L$cat_id",$account,$rights);
		}
	}
	foreach(array(
		'sample-page' => array($cats['sample'],'Sample page','just a sample',
	)) as $name => $data)
	{
		list($cat_id,$title,$subtitle) = $data;
		$oProc->query("INSERT INTO phpgw_sitemgr_pages (cat_id,sort_order,hide_page,name,state) VALUES ($cat_id,0,0,'$name',2)");
		$page_id = $pages[$name] = $oProc->m_odb->get_last_insert_id('phpgw_pages','page_id');
		$oProc->query("INSERT INTO phpgw_sitemgr_pages_lang (page_id,lang,title,subtitle) VALUES ($page_id,'en','$title','$subtitle')");
		// please note: this pages have no own content so far, we add it in the following paragraph
	}

	// set up some site- and page-wide content
	$visibility = array('all' => 0,'user' => 1,'admin' => 2,'anon' => 3);
	$blocks = array(
		array($module_id['administration'],'right',$site_id,0,$visibility['admin'],'Administration'),
		array($module_id['lang_block'],'right',$site_id,0,$visibility['all'],'Select language'),
		array($module_id['toc'],'right',$site_id,0,$visibility['all'],'Content areas'),
		array($module_id['calendar'],'right',$site_id,0,$visibility['user'],'Calendar'),
		array($module_id['goggle'],'right',$site_id,0,$visibility['all'],'Goggle'),
		array($module_id['login'],'right',$site_id,0,$visibility['anon'],'Login'),
		array($module_id['html'],'header',$site_id,0,$visibility['all'],'HTML Module','a:1:{s:11:"htmlcontent";s:21:"<h1>SiteMgr Demo</h1>";}'),
		array($module_id['html'],'footer',$site_id,0,$visibility['all'],'HTML Module','a:1:{s:11:"htmlcontent";s:213:"Please visit our Homepage <a href="http://www.egroupware.org" target="_blank">www.eGroupWare.org</a> and our <a href="http://www.sourcefourge.net/projects/egroupware/" target="_blank">Sourceforge Project page</a>.";}'),
		array($module_id['html'],'center',$cats['sample'],$pages['sample-page'],$visibility['all'],'HTML Module','a:1:{s:11:"htmlcontent";s:35:"some sample <b>HTML</b> content ...";}'),
	);
	foreach($blocks as $order => $block)
	{
		list($module,$area,$cat_id,$page_id,$visible,$title_en,$content_en) = $block;
		if (!$module) continue;
		$oProc->query("INSERT INTO phpgw_sitemgr_blocks (area,cat_id,page_id,module_id,sort_order,viewable) VALUES ('$area',$cat_id,$page_id,$module,$order,$visible)",__LINE__,__FILE__);
		$block_id = $oProc->m_odb->get_last_insert_id('phpgw_sitemgr_blocks','block_id');
		$oProc->query("INSERT INTO phpgw_sitemgr_blocks_lang (block_id,lang,title) VALUES ($block_id,'en','$title_en')",__LINE__,__FILE__);
		$oProc->query("INSERT INTO phpgw_sitemgr_content (block_id,arguments,state) VALUES ($block_id,NULL,2)",__LINE__,__FILE__);
		$version_id = $oProc->m_odb->get_last_insert_id('phpgw_sitemgr_content','version_id');
		if ($content_en)
		{
			$oProc->query("INSERT INTO phpgw_sitemgr_content_lang (version_id,lang,arguments_lang) VALUES ($version_id,'en','$content_en')",__LINE__,__FILE__);
		}
	}
	echo "SiteMgr demo site installed<br>";

	// install sitemgr-link via symlink or copy (windows)
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

