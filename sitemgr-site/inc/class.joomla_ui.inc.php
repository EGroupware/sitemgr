<?php
/**
 * EGroupware SiteMgr CMS - Joomla 1.5 template support
 *
 * @link http://www.egroupware.org
 * @package sitemgr
 * @subpackage sitemgr-site
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @copyright Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @version $Id$
 */

/**
 * UI Object for Joomla 1.5 templates
 *
 * It also emulates some of the JDocumentHTML methods
 */
class ui extends dummy_obj
{
	/**
	 * Instance of template object
	 *
	 * @var Template3
	 */
	protected $t;
	/**
	 * Directory of current template
	 *
	 * @var string
	 */
	public $templateroot;
	/**
	 * Directory for MOS compatibilty files
	 *
	 * @ToDo is this still needed?
	 * @var string
	 */
	protected $mos_compat_dir;
	
	/**
	 * Template name
	 * 
	 * @var string
	 */
	public $template;
	
	/**
	 * Url of SiteMgr site
	 *
	 * @var string
	 */
	public $baseurl;
	
	/**
	 * Param store
	 *
	 * @var JParameter
	 */
	public $params;
	
	/**
	 * Current site language
	 *
	 * @var string
	 */
	public $language = 'en';
	
	/**
	 * Language direction: ltr or rtl
	 *
	 * @var string
	 */
	public $direction = 'ltr';
	
	/**
	 * Site name
	 *
	 * @var string
	 */
	public $sitename;
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		$themesel = $GLOBALS['sitemgr_info']['themesel'];
		if ($themesel[0] == '/')
		{
			$this->templateroot = $GLOBALS['egw_info']['server']['files_dir'] . $themesel;
		}
		else
		{
			$this->templateroot = $GLOBALS['sitemgr_info']['site_dir'] . SEP . 'templates' . SEP . $themesel;
		}
		$this->t = new Template3($this->templateroot);
		$this->t->transformer_root = $this->mos_compat_dir = realpath(dirname(__FILE__).'/../mos-compat');
		
		// attributes used by Joomla 1.5
		$this->template = basename($themesel);
		$this->baseurl = $GLOBALS['sitemgr_info']['site_url'];
		$this->sitename = $this->t->get_meta('sitename').': '.$this->t->get_meta('title');
		if (in_array($dir=lang('language_direction_rtl'),array('rtl','ltr'))) $this->direction = $dir;
		$this->language = $this->t->get_meta('lang');

		// init JParameter from site or ini.file
		if (!empty($GLOBALS['Common_BO']->sites->current_site['params_ini']))
		{
			$ini_string = $GLOBALS['Common_BO']->sites->current_site['params_ini'];
		}
		else
		{
			$ini_string = file_get_contents($this->templateroot.SEP.'params.ini');
		}
		$this->params = new JParameter($ini_string);
	}

	/**
	 * Displays page by name (SiteMgr UI method)
	 *
	 * @param string $page_name
	 */
	function displayPageByName($page_name)
	{
		global $objbo;
		global $page;
		$objbo->loadPage($GLOBALS['Common_BO']->pages->so->PageToID($page_name));
		$this->generatePage();
	}

	/**
	 * Displays page by id (SiteMgr UI method)
	 *
	 * @param int $page_id
	 */
	function displayPage($page_id)
	{
		global $objbo;
		$objbo->loadPage($page_id);
		$this->generatePage();
	}

	/**
	 * Displays index (SiteMgr UI method)
	 */
	function displayIndex()
	{
		global $objbo;
		$objbo->loadIndex();
		$this->generatePage();
	}

	/**
	 * Displays TOC (SiteMgr UI method)
	 *
	 * @param int $categoryid=false
	 */
	function displayTOC($categoryid=false)
	{
		global $objbo;
		$objbo->loadTOC($categoryid);
		$this->generatePage();
	}

	/**
	 * Displays search (SiteMgr UI method)
	 */
	function displaySearch($search_result,$lang,$mode,$options)
	{
		global $objbo;
		$objbo->loadSearchResult($search_result,$lang,$mode,$options);
		$this->generatePage();
	}

	/**
	 * Generate page using the template (SiteMgr UI method)
	 */
	function generatePage()
	{
		// add a content-type header to overwrite an existing default charset in apache (AddDefaultCharset directiv)
		header('Content-type: text/html; charset='.translation::charset());

		// Joomla 1.5 defines
		define( '_JEXEC', True );
		define('DS',DIRECTORY_SEPARATOR);
		
		ini_set('include_path',$this->mos_compat_dir.(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? ';' : ':').ini_get('include_path'));

		ob_start();
		include($this->templateroot.'/index.php');
		$website = ob_get_contents();
		ob_clean();
	
		// replace <jdoc:include type="modules" name="XXXX" /> with content of content-area XXXX
		$website = preg_replace_callback('/<jdoc:([a-z]+) type="([^"]+)" (name="([^"]+)")?[^>]*>/', array($this,'jdoc_replace'), $website);
		
		// regenerate header (e.g. js includes)
		$this->t->loadfile(realpath(dirname(__FILE__).'/../mos-compat/metadata.tpl'));
		if (file_exists($this->templateroot.'/metadata.tpl'))
		{
			$this->t->loadfile($this->templateroot.'/metadata.tpl');
		}
		
		// inject custom CSS (incl. site logo)
		$custom_css = $GLOBALS['Common_BO']->get_custom_css();
		if (!empty($custom_css))
		{
			$website = str_replace('</head>',"\t".'<style type="text/css">'."\n".$custom_css."\n\t</style>\n</head>",$website);
		}
		echo preg_replace('@<!-- metadata.tpl starts here -->.*?<!-- metadata.tpl ends here -->@si',$this->t->parse(),$website);
	}
	
	/**
	 * Replaces <jdoc:include 
	 * 
	 * @param array $matches 0: whole jdoc tag, 1: jdoc:type, eg. "include", 2: type, eg. "module", 4: name of content-area
	 */
	public function jdoc_replace($matches)
	{
		list($all,$jdoc_type,$type,,$name) = $matches;
		
		if ($jdoc_type == 'include')
		{
			switch($type)
			{
				case 'modules':		// content-area $name
					return "<!-- BEGIN: CONTENTAREA $name -->\n".$this->t->process_blocks($name)."\n<!-- END: CONTENTAREA $name -->";
					
				case 'component':	// load the center module
					if (!file_exists($file = $objui->templateroot.'/mainbody.tpl'))
					{
						$file = realpath(dirname(__FILE__).'/../mos-compat/mainbody.tpl');
					}
					$this->t->loadfile($file);
					return $this->t->parse();
					
				case 'head':
					$this->t->loadfile(realpath(dirname(__FILE__).'/../mos-compat/metadata.tpl'));
					return "\t\t<title>".$this->t->get_meta('sitename').': '.$this->t->get_meta('title')."</title>\n".
						$this->t->parse();
					
				case 'module':
					switch($name)
					{
						case 'breadcrumbs':
							//if ($suppress_hide_pages) $suppress_hide='&suppress_hide_pages=on';
							$module_navigation_path = array('','navigation','nav_type=8&no_show_sep=on'.$suppress_hide);
							return $this->t->exec_module($module_navigation_path);
					}
			}
		}
		// log unkown types and return them unchanged
		error_log(__METHOD__.'('.array2string($matches).') unknown jdoc tag!');
		return $matches[0];
	}
	
	/**
	 * JDocumentHTML compatibility methods
	 */
	
	/**
	 * Count blocks in content-area
	 * 
	 * @param string $contentarea
	 * @return int
	 */
	function countModules($contentarea)
	{
		return (int)$this->t->count_blocks($contentarea);
	}
	
	/**
	 * Get URL of template directory
	 * 
	 * @return string
	 */
	function templateurl()
	{
		$GLOBALS['sitemgr_info']['site_url'].$this->template.'/';
	}
}

/**
 * Object which allows to call every method (returning null) and set and read every property
 * 
 * All access or calls can be logged via error_log()
 */
class dummy_obj
{
	/**
	 * Store for attribute values
	 */
	protected $data = array();
	
	/**
	 * Enable or disable logging
	 *
	 * @var boolean
	 */
	protected $debug = false;
	/**
	 * Enable or disable logging for static calls
	 *
	 * @var boolean
	 */
	static protected $debug_static = false;
	
	public function __get($name)
	{
		if ($this->debug) error_log(__METHOD__."('$name') returning ".array2string($this->data[$name]).' '.function_backtrace());

		return $this->data[$name];
	}
	
	public function __set($name,$value)
	{
		if ($this->debug) error_log(__METHOD__."('$name',".array2string($value).') '.function_backtrace());

		$this->data[$name] = $value;
	}
	
	public function __isset($name)
	{
		if ($this->debug) error_log(__METHOD__."('$name') returning ".array2string(isset($this->data[$name])).' '.function_backtrace());
		
		return isset($this->data[$name]);
	}
	
	public function __call($name,$params)
	{
		if ($this->debug) error_log(__METHOD__."('$name',".array2string($params).') '.function_backtrace());
		
		return null;
	}
		
	/**
	 * Called for all static method calls, requires PHP5.3 !!!
	 *
	 * @param string $name
	 * @param array $params
	 */
	public static function __callstatic($name,$params)
	{
		if (self::$debug_static) error_log(__METHOD__."('$name',".array2string($params).') '.function_backtrace());
		
		return null;
	}
}

/**
 * Joomla 1.5 compatibilty classes
 */
class JFactory extends dummy_obj
{
	/*public static function getApplication($what)
	{
		return null;
	}*/
	
	/*public static function getDBO()
	{
		if (self::$debug_static) error_log(__METHOD__."('$name',".array2string($params).') '.function_backtrace());
		
		return new dummy_obj();
	}*/
	
	/**
	 * JFactory static method return only objects, requires PHP5.3 !!!
	 *
	 * @param string $name
	 * @param array $params
	 */
	public static function __callstatic($name,$params)
	{
		if (self::$debug_static) error_log(__METHOD__."('$name',".array2string($params).') '.function_backtrace());
		
		return new dummy_obj();
	}

	private $instance;
	
	public static function getInstance()
	{
		if (is_null($instance))
		{
			$instance = new JURI();
		}
		return $instance;
	}
}

class JParameter extends dummy_obj
{
	protected $_defaultNameSpace = '_default';
	
	/**
	 * Store for parameter values
	 *
	 * @var array
	 */
	private $values = array();

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	string The raw parms text
	 * @param	string Path to the xml setup file
	 * @since	1.5
	 */
	function __construct($data, $path = '')
	{
		if ($this->debug) error_log(__METHOD__."('$data','$path') ".function_backtrace());

		if (trim( $data )) {
			$this->loadINI($data);
		}
	}

	/**
	 * Load an INI string into the registry into the given namespace [or default if a namespace is not given]
	 *
	 * @access	public
	 * @param	string	$data		INI formatted string to load into the registry
	 * @param	string	$namespace	Namespace to load the INI string into [optional]
	 * @return	boolean True on success
	 * @since	1.5
	 */
	function loadINI($data, $namespace = null)
	{
		if ($data === false) return false;

		foreach(preg_split("/[\n\r]+/", (string)$data) as $line)
		{
			if ($line)
			{
				list($key,$value) = explode('=',$line,2);
				$this->set($key,$value,$namespace ? $namespace : $this->_defaultNameSpace);
			}
		}
		return true;
	}
	
	/**
	 * Get INI string from registry
	 * 
	 * @param string $namespace=null default $this->_defaultNameSpace
	 * @return string
	 */
	function getINI($namespace = null)
	{
		if (is_null($namespace)) $namespace = $this->_defaultNameSpace;
		
		$ini = '';
		foreach($this->values as $key => $value)
		{
			list($ns,$name) = explode('.',$key,2);
			if ($ns == $namespace)
			{
				$ini .= $name.'='.$value."\n";
			}
		}
		return $ini;
	}

	/**
	 * Set a value
	 *
	 * @access	public
	 * @param	string The name of the param
	 * @param	string The value of the parameter
	 * @return	string The set value
	 * @since	1.5
	 */
	function set($key, $value = '', $group = '_default')
	{
		if ($this->debug) error_log(__METHOD__."('$key','$value','$group')");
		
		return $this->values[$group.'.'.$key] = (string) $value;
	}

	/**
	 * Get a value
	 *
	 * @access	public
	 * @param	string The name of the param
	 * @param	mixed The default value if not found
	 * @return	string
	 * @since	1.5
	 */
	function get($key, $default = '', $group = '_default')
	{
		$value = $this->values[$group.'.'.$key];
		
		$result = (empty($value) && ($value !== 0) && ($value !== '0')) ? $default : $value;
		
		if ($this->debug) error_log(__METHOD__."('$key','$default','$group') returning ".array2string($result));
		
		return $result;
	}

	/**
	 * Transforms a namespace to an array
	 *
	 * @access	public
	 * @param	string	$namespace	Namespace to return [optional: null returns the default namespace]
	 * @return	array	An associative array holding the namespace data
	 * @since	1.5
	 */
	function toArray($namespace = null)
	{
		if (is_null($namespace)) $namespace = $this->_defaultNameSpace;
		
		$arr = array();
		foreach($this->values as $key => $value)
		{
			list($ns,$name) = explode('.',$key,2);
			if ($ns == $namespace)
			{
				$arr[$name] = $value;
			}
		}
		return $arr;
	}
}

class JRequest extends dummy_obj
{
	/*public static function getInt($what)
	{
		return null;
	}*/
}

class JHTML extends dummy_obj
{
	
}

class JURI extends dummy_obj
{

}

class JText extends dummy_obj
{
	public function _($str)
	{
		return lang($str);
	}
}

class JConfig extends dummy_obj
{
	function __get($name)
	{
		/**
		 * @var ui
		 */
		global $objbo;

		switch($name)
		{
			case 'sitename':
				return $objbo->sitename;
		}
		return parent::__get($name);
	}
}

class JSite extends dummy_obj
{
	public static function getRouter()
	{
		if (self::$debug_static) error_log(__METHOD__.substr(array2string(func_get_args()),5));
		
		return new dummy_obj();
	}
	
	public static function getMenu()
	{
		static $menu;
		
		if (self::$debug_static) error_log(__METHOD__.substr(array2string(func_get_args()),5));
		
		if (is_null($menu)) $menu = new JMenu();
		
		return $menu;
	}
}
	
class JMenu extends dummy_obj
{
	public $debug = true;
	
	/**
	 * Gets menu items by attribute
	 *
	 * @access public
	 * @param string 	The field name
	 * @param string 	The value of the field
	 * @param boolean 	If true, only returns the first item found
	 * @return array
	 */
	function getItems($attribute, $value, $firstonly = false)
	{
		if ($this->debug) error_log(__METHOD__.substr(array2string(func_get_args()),5));

		$cat_id = 0;
		$depth = 0;
		$tree = array();	// stack/path mit cat_id's
		foreach($GLOBALS['objbo']->getIndex(false,false,true) as $page)
		{
			//_debug_array($page);
			$id = 2 * $page['cat_id'];			// cat's have even id's
			if ($id != $cat_id)		// new cat
			{
				$cat_id = $id;
				if($depth == $page['catdepth'])	// same level -> remove last element
				{
					array_pop($tree);
				}
				elseif($depth > $page['catdepth'])	// we are going back (maybe multiple levels)
				{
					$tree = array_slice($tree,0,$page['catdepth']-1);
				}
				$tree[] = $cat_id;
				$depth = $page['catdepth'];
				$cat_path = implode('/',$tree);
				$parent = (int)$tree[$depth-2];
				$name = $page['catname'];
				$title = $page['catdescrip']?$page['catdescrip']:$page['catname'];
				$url = $page['cat_url'];
				$rows[] = (object)$this->set_menu($page,$parent,$depth,$tree,$name,$id,$url,$title);
				//echo "<p>new cat $page[cat_id]=$cat_id ($depth: /$cat_path, parent=$parent): $page[catname]:</p>\n";
			}
			if ($page['page_id'])
			{
				$id = 2 * $page['page_id'] + 1;	// pages have odd id's
				$page_path = $cat_path.'/'.$id;
				$name=$page['pagetitle']?$page['pagetitle']:$page['pagename'];
				$parent = (int)$tree[$depth-1];
				$url= $page['page_url'];
				$rows[] = (object)$this->set_menu($page,$parent,$depth,$tree,$name,$id,$url);
				//echo "- page: $page[page_id]=$id (".($depth+1).": /$page_path, parent=$cat_id): $page[pagename]<br/>\n";
			}
		}
		//_debug_array($rows);
		return $rows;
	}
	
	/**
	 * Generate one category or page entry
	 *
	 * @param array $page as returned by objbo->getIndex()
	 * @param int $parent parent cat_id
	 * @param int $sublevel
	 * @param array $tree cat_id "path"
	 * @param string $name page or category name
	 * @param int $id unique id (cat's use event, pages odd numbers)
	 * @param string $url for the a href
	 * @param string $title
	 * @return array
	 */
	private function set_menu($page,$parent,$sublevel,$tree,$name,$id,$url,$title=null)
	{
		return array(
			'id' => $id,
			'menutype' => 'mainmenu',
			'name' => $name,
			'alias' => $name,
			'link' => $url,
			'title' => $title ? $title : $name,
	  		'type' => 'url',	//'component' uses JSite::getRouter()->getMode(), while 'url' is left alone!
			'published' => 1,
			'parent' => $parent,
			'componentid' => 20,
			'sublevel' => $sublevel,
			'ordering' => 1,
			'checked_out' => 0,
			'checked_out_time' => '0000-00-00 00:00:00',
			'pollid' => 0,
			'browserNav' => 0,
			'access' => 0,
			'utaccess' => 3,
			/* seems NOT to be used
 			'params' => 'show_page_title=1
page_title=Welcome to the Frontpage
show_description=0
show_description_image=0
num_leading_articles=1
num_intro_articles=4
num_columns=2
num_links=4
show_title=1
pageclass_sfx=
menu_image=-1
secure=0
orderby_pri=
orderby_sec=front
show_pagination=2
show_pagination_results=1
show_noauth=0
link_titles=0
show_intro=1
show_section=0
link_section=0
show_category=0
link_category=0
show_author=1
show_create_date=1
show_modify_date=1
show_item_navigation=0
show_readmore=1
show_vote=0
show_icons=1
show_pdf_icon=1
show_print_icon=1
show_email_icon=1
show_hits=1


',*/
			'lft' => 0,
			'rgt' => 0,
			'home' => 0,//1,	// home == link to home-page, replaces link with JURI::base()
			'component' => 'com_content',
			'tree' => $tree,
		  	'route' => 'home',
		  	'query' => array('option' => 'com_content','view' => 'frontpage'),
			'url'  => $url,	// will be overwritten by 'link' value!
			'_idx' => 0,
		);
	}
}

class JRoute extends dummy_obj
{
	
}