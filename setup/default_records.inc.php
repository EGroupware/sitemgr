<?php
	foreach (array('html','index','toc') as $module)
	{
		$oProc->query("INSERT INTO phpgw_sitemgr_modules (module_name) VALUES ('$module')",__LINE__,__FILE__);
		$module_id = $oProc->m_odb->get_last_insert_id('phpgw_sitemgr_modules','module_id');
		$oProc->query("INSERT INTO phpgw_sitemgr_active_modules (area,cat_id,module_id) VALUES ('__PAGE__',0,$module_id)",__LINE__,__FILE__);
	}
	$oProc->query("select config_value FROM phpgw_config WHERE config_name='webserver_url'");
	$oProc->next_record();
	$siteurl = $oProc->f('config_value') . SEP . 'sitemgr' . SEP . 'sitemgr-site' . SEP;
	$oProc->query("INSERT INTO phpgw_sitemgr_preferences (name,value) VALUES ('sitemgr-site-url','$siteurl')");
	$sitedir = PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'sitemgr-site';
	$oProc->query("INSERT INTO phpgw_sitemgr_preferences (name,value) VALUES ('sitemgr-site-dir','$sitedir')");
	$oProc->query("INSERT INTO phpgw_sitemgr_preferences (name,value) VALUES ('themesel','phpgroupware')");
	$oProc->query("INSERT INTO phpgw_sitemgr_preferences (name,value) VALUES ('sitelanguages','en')");
