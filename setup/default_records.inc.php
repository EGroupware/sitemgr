<?php
/**
 * SiteMgr - Default records for a new installation
 *
 * @link http://www.egroupware.org
 * @package sitemgr
 * @subpackage setup
 * @author RalfBecker@outdoor-training.de
 * @copyright (c) 2004-10 by RalfBecker@outdoor-training.de
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

$sitemgr_table_prefix = 'egw_sitemgr';
/*
$global_cat_owner = categories::GLOBAL_ACCOUNT;
$oProc->query("INSERT INTO {$GLOBALS['egw_setup']->cats_table} (cat_parent,cat_owner,cat_access,cat_appname,cat_name,cat_description,last_mod) VALUES (0,$global_cat_owner,'public','sitemgr','Default Website','This website has been added by setup',".time().")");
$site_id = $oProc->m_odb->get_last_insert_id($GLOBALS['egw_setup']->cats_table,'cat_id');
$oProc->query("UPDATE {$GLOBALS['egw_setup']->cats_table} SET cat_main = $site_id WHERE cat_id = $site_id",__LINE__,__FILE__);

$oProc->query("SELECT config_value FROM {$GLOBALS['egw_setup']->config_table} WHERE config_name='webserver_url'");
$oProc->next_record();
$siteurl = $oProc->f('config_value') . '/sitemgr/sitemgr-site/';	// url always uses slashes, dont use SEP!!!
$sitedir = $GLOBALS['egw_setup']->db->db_addslashes('sitemgr' . SEP . 'sitemgr-site');
$oProc->query("INSERT INTO {$sitemgr_table_prefix}_sites (site_id,site_name,site_url,site_dir,themesel,site_languages,home_page_id,anonymous_user,anonymous_passwd) VALUES ($site_id,'Default Website','$siteurl','$sitedir','idots','en,de',0,'anonymous','anonymous')");
*/
// give Admins group rights vor sitemgr and for the created default-site
$admingroup = $GLOBALS['egw_setup']->add_account('Admins','Admin','Group',False,False);
$GLOBALS['egw_setup']->add_acl('sitemgr','run',$admingroup);
$GLOBALS['egw_setup']->add_acl('sitemgr',"L$site_id",$admingroup);
// give Default group rights vor sitemgr-link
$defaultgroup = $GLOBALS['egw_setup']->add_account('Default','Default','Group',False,False);
$GLOBALS['egw_setup']->add_acl('sitemgr-link','run',$defaultgroup);

// Create anonymous user for sitemgr
$GLOBALS['egw_setup']->add_account('NoGroup','No','Rights',False,False);
$anonymous = $GLOBALS['egw_setup']->add_account('anonymous','SiteMgr','User','anonymous','NoGroup');
// give the anonymous user only sitemgr-link-rights
$GLOBALS['egw_setup']->add_acl('sitemgr-link','run',$anonymous);
$GLOBALS['egw_setup']->add_acl('phpgwapi','anonymous',$anonymous);

// register all modules and allow them in the following contentareas
// note '__PAGE__' is used for contentareas with NO module specialy selected, eg. only 'center' in this example !!!
$areas = array(
	'administration' => array('left','right'),
	'amazon' => array('left','right'),
	'bookmarks' => array('__PAGE__'),
	'calendar' => array('left','right'),
	'currentsection' => array('left','right'),
	'download' => array('__PAGE__'),
	'filecontents' => array('left','right','header','footer','__PAGE__'),
	'frame' => array('__PAGE__'),
	'forum' => array('__PAGE__'),
	'gallery' => array('left','right','__PAGE__'),
	'gallery_imageblock' => array('left','right','__PAGE__'),
	'google' => array('left','right'),
	'html' => array('left','right','header','footer','__PAGE__'),
	'lang_block' => array('left','right'),
	'login' => array('left','right'),
	'navigation' => array('left','right','__PAGE__'),
	'news_admin' => array('left','right','__PAGE__'),
	'notify' => array('left','right'),
	'phpbrain' => array('__PAGE__'),
	'polls' => array('left','right','__PAGE__'),
	'redirect' => array('__PAGE__'),
	'resources' => array('__PAGE__'),
	'search' => array('left','right','header','footer','__PAGE__'),
	'template' => array('left','right','__PAGE__'),
	'tracker' => array('__PAGE__'),
	'validator' => array('footer'),
	'wiki' => array('__PAGE__'),
);
$dir = dir(EGW_SERVER_ROOT);
while(($app = $dir->read()))
{
	$moddir = EGW_SERVER_ROOT . '/' . $app . ($app == 'sitemgr' ? '/modules' : '/sitemgr');
	if (is_dir($moddir))
	{
		$d = dir($moddir);
		while (($file = $d->read()))
		{
			if (preg_match ('/class\.module_(.*)\.inc\.php$/', $file, $module))
			{
				$module = $module[1];

				if (preg_match('/\$this->description = lang\([\'"]([^'."\n".']*)[\'"]\);/',file_get_contents($moddir.'/'.$file),$parts))
				{
					$description = $GLOBALS['egw_setup']->db->db_addslashes(str_replace("\\'","'",$parts[1]));
				}
				else
				{
					$description = '';
				}
				$oProc->query("INSERT INTO {$sitemgr_table_prefix}_modules (module_name,description) VALUES ('$module','$description')",__LINE__,__FILE__);
				$id = $module_id[$module] = $oProc->m_odb->get_last_insert_id($sitemgr_table_prefix.'_modules','module_id');

				// allow to display all modules, not mentioned above, on __PAGE__
				if (!isset($areas[$module]) && !in_array($module,array('hello','translation_status','xml')))
				{
					$areas[$module] = array('__PAGE__');
				}
				foreach((array)$areas[$module] as $area)
				{
					$oProc->query("INSERT INTO {$sitemgr_table_prefix}_active_modules (area,cat_id,module_id) VALUES ('$area',$site_id,$id)",__LINE__,__FILE__);
				}
			}
		}
		$d->close();
	}
}
$dir->close();
/*
// create some sample categories for the site
foreach(array(
	'other'  => 'one more',
	'sample' => 'sample category',
	'sub-sample' => 'just a sub for sample'
) as $name => $descr)
{
	$parent = substr($name,0,4) == 'sub-' ? $cats[substr($name,4)] : $site_id;
	$level  = substr($name,0,4) == 'sub-' ? 2 : 1;
	$oProc->query("INSERT INTO {$GLOBALS['egw_setup']->cats_table} (cat_main,cat_parent,cat_level,cat_owner,cat_access,cat_appname,cat_name,cat_description,cat_data,last_mod) VALUES ($site_id,$parent,$level,$global_cat_owner,'public','sitemgr','$name','$descr','0',".time().")");
	$cat_id = $cats[$name] = $oProc->m_odb->get_last_insert_id($GLOBALS['egw_setup']->cats_table,'cat_id');
	$oProc->query("INSERT INTO {$sitemgr_table_prefix}_categories_lang (cat_id,lang,name,description) VALUES ($cat_id,'en','$name','$descr')");
	$oProc->query("INSERT INTO {$sitemgr_table_prefix}_categories_state (cat_id,state) VALUES ($cat_id,2)");
	foreach(array($admingroup => 3,$defaultgroup => 1,$anonymous => 1) as $account => $rights)
	{
		$GLOBALS['egw_setup']->add_acl('sitemgr',"L$cat_id",$account,$rights);
	}
}
foreach(array(
	'sample-page' => array($cats['sample'],'Sample page','just a sample',
)) as $name => $data)
{
	list($cat_id,$title,$subtitle) = $data;
	$oProc->query("INSERT INTO {$sitemgr_table_prefix}_pages (cat_id,sort_order,hide_page,name,state) VALUES ($cat_id,0,0,'$name',2)");
	$page_id = $pages[$name] = $oProc->m_odb->get_last_insert_id($sitemgr_table_prefix.'_pages','page_id');
	$oProc->query("INSERT INTO {$sitemgr_table_prefix}_pages_lang (page_id,lang,title,subtitle) VALUES ($page_id,'en','$title','$subtitle')");
	// please note: this pages have no own content so far, we add it in the following paragraph
}

// set up some site- and page-wide content
$visibility = array('all' => 0,'user' => 1,'admin' => 2,'anon' => 3);
foreach(array(
	array($module_id['navigation'],'left',$site_id,0,$visibility['all'],'Root Site Index',NULL,
		array('sub_cats' => 'on', 'nav_type' => 3)),
	array($module_id['template'],'left',$site_id,0,$visibility['all'],'Choose template',NULL,
		array('show' => 8, 'zip' => 'zip')),
	array($module_id['navigation'],'right',$site_id,0,$visibility['all'],'Current Section',NULL,
		array('nav_type' => 1)),
	array($module_id['administration'],'right',$site_id,0,$visibility['admin'],'Administration'),
	array($module_id['lang_block'],'right',$site_id,0,$visibility['all'],'Select language'),
	array($module_id['calendar'],'right',$site_id,0,$visibility['user'],'Calendar'),
	array($module_id['goggle'],'right',$site_id,0,$visibility['all'],'Goggle'),
	array($module_id['login'],'right',$site_id,0,$visibility['anon'],'Login'),
	array($module_id['amazon'],'right',$site_id,0,$visibility['all'],False,'Amazon.com',array('search' => 1)),
	array($module_id['html'],'header',$site_id,0,$visibility['all'],'HTML Module',
		array('htmlcontent' => '<h1>SiteMgr Demo</h1>')),
	array($module_id['html'],'footer',$site_id,0,$visibility['all'],'HTML Module',
		array('htmlcontent' => 'Powered by eGroupWare\'s <b>SiteMgr</b>. Please visit our Homepage <a href="http://www.egroupware.org" target="_blank">www.eGroupWare.org</a> and our <a href="http://www.sourceforge.net/projects/egroupware/" target="_blank">Sourceforge Project page</a>.')),
	array($module_id['html'],'center',$cats['sample'],$pages['sample-page'],$visibility['all'],'HTML Module',
		array('htmlcontent' => 'some sample <b>HTML</b> content ...')),
) as $order => $block)
{
	list($module,$area,$cat_id,$page_id,$visible,$title_en,$content_en,$content) = $block;
	if (!$module) continue;
	$oProc->query("INSERT INTO {$sitemgr_table_prefix}_blocks (area,cat_id,page_id,module_id,sort_order,viewable) VALUES ('$area',$cat_id,$page_id,$module,$order,$visible)",__LINE__,__FILE__);
	$block_id = $oProc->m_odb->get_last_insert_id($sitemgr_table_prefix.'_blocks','block_id');
	$oProc->query("INSERT INTO {$sitemgr_table_prefix}_blocks_lang (block_id,lang,title) VALUES ($block_id,'en','$title_en')",__LINE__,__FILE__);
	$oProc->query("INSERT INTO {$sitemgr_table_prefix}_content (block_id,arguments,state) VALUES ($block_id,".
		($content ? $GLOBALS['egw_setup']->db->quote(serialize($content)) : 'NULL').",2)",__LINE__,__FILE__);
	$version_id = $oProc->m_odb->get_last_insert_id($sitemgr_table_prefix.'_content','version_id');
	if ($content_en)
	{
		$oProc->query("INSERT INTO {$sitemgr_table_prefix}_content_lang (version_id,lang,arguments_lang) VALUES ($version_id,'en','".$GLOBALS['egw_setup']->db->db_addslashes(serialize($content_en))."')",__LINE__,__FILE__);
	}
}
*/
$reader = new XMLReader();
$reader->open(dirname(__FILE__).'/default-site.xml');
$import = new sitemgr_import_xml($reader);
$import->import_record(array($admingroup),array(
	$admingroup   => EGW_ACL_READ|EGW_ACL_ADD,
	$defaultgroup => EGW_ACL_READ,
	$anonymous    => EGW_ACL_READ,
),true);	// true = ignore acl
