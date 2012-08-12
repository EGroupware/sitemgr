<?php
/**
 * EGroupware SiteMgr CMS - Controler
 *
 * @link http://www.egroupware.org
 * @package sitemgr
 * @subpackage sitemgr-site
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @license http://opensource.org/licenses/gpl-license.php GPL2+ - GNU General Public License version 2, or (at your option) any later version
 * @version $Id$
 */

/**
 * Controler containing all logic to detect which page or index to display, load it from cache or render it anew
 */
class site_controler
{
	/**
	 * Result from objbo->getmode(): 'Production', 'Edit'
	 * @var string
	 */
	public $mode;

	public function __construct()
	{
		global $Common_BO, $objbo, $sitemgr_info;

		$Common_BO = CreateObject('sitemgr.Common_BO');

		require_once __DIR__.'/class.sitebo.inc.php';
		$objbo = new sitebo;
		$this->mode = $objbo->getmode();

		$Common_BO->sites->set_currentsite($site_url, $this->mode);
		if($this->mode != 'Production')
		{
			// we need this to avoid the "attempt to access ..." errors if users contribute to multiple websites.
			// This does not solve the Problem if they work simultanus in two browsers :-(
			$GLOBALS['egw']->preferences->change('sitemgr','currentsite', $Common_BO->sites->urltoid($site_url));
			$GLOBALS['egw']->preferences->save_repository(True);
		}
		$sitemgr_info = $sitemgr_info + $Common_BO->sites->current_site;
		if ($Common_BO->sites->current_site['htaccess_rewrite'])
		{
			$sitemgr_info['htaccess_rewrite'] = $Common_BO->sites->current_site['htaccess_rewrite'];
		}
		$sitemgr_info['sitelanguages'] = explode(',',$sitemgr_info['site_languages']);
		$objbo->setsitemgrPreferredLanguage();
		translation::add_app('common');	// as we run as sitemgr-site
		translation::add_app('sitemgr');	// as we run as sitemgr-site
	}

	/**
	 * Factory method to get renderer for a given template
	 *
	 * @param string $themesel template-name
	 * @return site_renderer
	 */
	public function instanciateRenderer($themesel)
	{
		global $Common_BO;

		if ($themesel[0] == '/')
		{
			$templateroot = $GLOBALS['egw_info']['server']['files_dir'] . $themesel;
		}
		else
		{
			$templateroot = $GLOBALS['sitemgr_info']['site_dir'] . SEP . 'templates' . SEP . $themesel;
		}
		require_once __DIR__.'/class.Template3.inc.php';
		if (file_exists($templateroot.'/main.tpl'))			// native sitemgr template
		{
			include_once __DIR__.'/class.ui.inc.php';
		}
		elseif (file_exists($templateroot.'/index.php'))	// Joomla or Mambo Open Source template
		{
			$version =& egw_cache::getSession('sitemgr', 'template_version');	// cache it in session
			if (is_null($version) || !isset($version[$themesel]))
			{
				$theme_info = $Common_BO->theme->getThemeInfos($themesel);
				$version[$themsel] = $theme_info['joomla-version'];
			}
			if (version_compare($version[$themsel], '1.3','>='))	// joomla 1.5+ template
			{
				include_once __DIR__.'/class.joomla_ui.inc.php';
			}
			else
			{
				include_once __DIR__.'/class.mos_ui.inc.php';

				if (file_exists($templateroot.'/joomla.xml.php'))	// Joomla 1.0 template
				{
					include_once '../mos-compat/class.joomla.inc.php';
					include_once '../mos-compat/class.JFilterOutput.inc.php';
					include_once '../mos-compat/joomla_Legacy_function.inc.php';
				}
			}
		}
		else
		{
			echo '<h3>'.lang("Invalid template directory '%1' !!!",$templateroot)."</h3>\n";
			if (!is_dir($GLOBALS['sitemgr_info']['site_dir'] . SEP . 'templates') || !is_readable($GLOBALS['sitemgr_info']['site_dir'] . SEP . 'templates'))
			{
				echo lang("The filesystem path to your sitemgr-site directory '%1' is probably wrong. Go to SiteMgr --> Define Websites and edit/fix the concerned Site.",$GLOBALS['sitemgr_info']['site_dir']);
			}
			elseif (!is_dir($templateroot) || !is_readable($templateroot))
			{
				echo lang("You may have deinstalled the used template '%1'. Reinstall it or go to SiteMgr --> Configure Website and select an other one.",$themsel);
			}
			common::egw_exit();
		}
		return new ui;
	}

	/**
	 * Process request: either from cache or by rendering it
	 */
	public function processRequest()
	{
		global $page, $objbo, $objui, $Common_BO;

		$page = CreateObject('sitemgr.Page_SO');

		// Check for explicit modules calls
		if ($_GET['module'])
		{
			// we (miss-)use the addcontent handler to call the module
			$GLOBALS['egw']->session->appsession('addcontent','sitemgr',array(array(
				'module_name' => $_GET['module'],
				'arguments' => array(),
				'page' => false,
				'area' => false,
				'sort_order' => false
			)));
		}

		if ($_GET['page_name'] && $_GET['page_name'] != 'index.php')
		{
			$cache_name = 'page-'.($page_id = $Common_BO->pages->so->PageToID($_GET['page_name']));
		}
		elseif((int) $_GET['category_id'] && ($cat = $Common_BO->cats->getCategory($_GET['category_id'])))
		{
			if ($cat->index_page_id > 0 && ($page = $Common_BO->pages->getPage($cat->index_page_id)) && $page->id)
			{
				$cache_name = 'page-'.$page->id;
			}
			else
			{
				$cache_name = 'toc-'.(int)$_GET['category_id'];
			}
		}
		elseif ((int)$_GET['page_id'])
		{
			$cache_name = 'page-'.(int)$_GET['page_id'];
		}
		elseif (isset($index))
		{
			$cache_name = 'index';
		}
		elseif (isset($toc))
		{
			$cache_name = 'toc';
		}
		elseif ($_REQUEST['searchword'])
		{
			// Make compatibility with mos search boxes, if not lang, mode and view option, default all languages
			// any words mode and view options advgoogle
			$search_lang = $_POST['search_lang'] ? $_POST['search_lang'] : "all";
			$search_mode = $_POST['search_mode'] ? $_POST['search_mode'] : "any";
			$search_options = $_POST['search_options'] ? $_POST['search_options'] : "advgoogle";
			$search_ui = CreateObject('sitemgr.search_ui');
			$search_result = $search_ui->search($_REQUEST['searchword'], $search_lang, $search_mode, $search_options);
			$objbo->loadSearch($search_result, $search_lang, $search_mode, $search_options);
		}
		else
		{
			if ($sitemgr_info['home_page_id'])
			{
				$cache_name = 'page-'.$sitemgr_info['home_page_id'];
			}
			else
			{
				$cache_name = 'index';
			}
		}
		// check if we have a cached version of the page
		if (isset($cache_name))
		{
			// cache need to be language specific
			$cache_name = $GLOBALS['egw_info']['user']['preferences']['common']['lang'].'-'.$cache_name;

			// only use cache for Production site and GET requests
			if ($this->mode == 'Production' && $_SERVER['REQUEST_METHOD'] == 'GET' &&
				($cache = egw_cache::getInstance(__CLASS__, $cache_name)))
			{
				// add a content-type header to overwrite an existing default charset in apache (AddDefaultCharset directiv)
				header('Content-type: text/html; charset='.translation::charset());

				echo $cache;
				error_log(__METHOD__."() loaded cached page $cache_name in ".number_format(microtime(true)-$GLOBALS['egw_info']['flags']['page_start_time'], 3).'sec');

				common::egw_exit();
			}
			ob_start();
			list(, $type, $id) = explode('-', $cache_name);
			switch($type)
			{
				case 'index':
					$objbo->loadIndex();
					break;
				case 'toc':
					$objbo->loadTOC($id);
					break;
				case 'page':
					$objbo->loadPage($id);
					break;
			}
		}
		$objui = $this->instanciateRenderer($GLOBALS['sitemgr_info']['themesel']);
		$objui->generatePage();
		error_log(__METHOD__."() rendered page $cache_name in ".number_format(microtime(true)-$GLOBALS['egw_info']['flags']['page_start_time'], 3).'sec');

		// only use cache for Production site and GET requests
//		if (isset($cache_name) && $this->mode == 'Production' && $_SERVER['REQUEST_METHOD'] == 'GET')
		{
			egw_cache::setInstance(__CLASS__, $cache_name, ob_get_contents(), $cache_time = 7200);
		}
	}
}

/**
 * Renders depending on a template a page, index or TOC
 *
 * Current implementation render:
 * - SiteMgr's own template format based on Template class
 * - Joomla 1.5 templates
 * - old MamboOpenSource templates
 */
interface site_renderer
{
	/**
	 * Generate page using the template (SiteMgr UI method)
	 */
	public function generatePage();
}