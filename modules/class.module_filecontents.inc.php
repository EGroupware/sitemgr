<?php
/**
 * EGroupware SiteMgr CMS - filecontents module
 *
 * @link http://www.egroupware.org
 * @package sitemgr
 * @subpackage modules
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

/**
 * Filecontents module: displays/includes content of files or urls
 *
 * Regular expressions:
 * @see http://www.php.net/manual/en/reference.pcre.pattern.syntax.php
 *
 * To replace the read content with an in brackets enclosed part, you have to make sure the
 * regular expression matches the whole text, eg: '/^.*<body[^>]*>(.*)<\/body>.*$/si'
 * ('/<body[^>]*>(.*)<\/body>/si' will only remove the body tags, not the rest before and after!)
 * The 's' modifier is neccessary as .* does not match newlines without!
 *
 * The 'e' modifier (eval) is NOT allowed for security reasons!
 *
 * CSS query:
 * If Zend Framework is install, you can also use a css query to get a part of the included html.
 * Eg. "div#id" or "div.class"
 */
class module_filecontents extends Module
{
	/**
	 * Pearl regular expression to replace html page with content between body tags (replace='$1')
	 *
	 * @var string
	 */
	const GRAB_BODY = '/^.*<body[^>]*>(.*)<\/body>.*$/si';

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->arguments = array(
			'filepath' => array(
				'type' => 'textfield',
				'label' => lang('The complete URL or path to a file to be included'),
				'params' => array('size' => 50),
			),
			'preg' => array(
				'type' => 'textfield',
				'label' => lang('Regular expression, if only parts should be used').'<br />'.
					lang('default for html is "%1"',self::GRAB_BODY),
				'params' => array('size' => 50),
			),
			'replace' => array(
				'type' => 'textfield',
				'label' => lang('Replace above regular expressions with').'<br />'.
					lang('default %1 (content of first brackets)','$1'),
				'params' => array('size' => 50),
			),
		);
		// if Zend Framework is installed, allow css queries to get parts of html
		if (@include_once('Zend/Dom/Query.php'))
		{
			$this->arguments['css_query'] = array(
				'type' => 'textfield',
				'label' => lang('CSS selector for part of html (does NOT use regular expression)').'<br />'.
					lang('eg. %1 or %2','"div#id", "table.classname"','"div[attr=\'value\'] > h1"'),
				'params' => array('size' => 50),
			);
		}
		$this->arguments['cache_time'] = array(
			'type' => 'textfield',
			'label' => lang('How long to cache downloaded content (seconds)'),
			'params' => array('size' => 5),

		);
		$this->title = lang('File contents');
		$this->description = lang('This module includes the contents of an URL or file (readable by the webserver and in its docroot !)');
	}

	/**
	 * Get module content
	 *
	 * @see Module::get_content()
	 * @param array &$arguments
	 * @param array $properties
	 * @return string
	 */
	function get_content(&$arguments,$properties)
	{
		if ((int)$arguments['cache_time'] &&
			($ret = egw_cache::getInstance('sitemgr', $cache_token = md5(serialize($arguments)))))
		{
			return $ret;
		}
		$url = parse_url($path = $arguments['filepath']);

		if (empty($path))
		{
			return '';
		}
		if (!$this->validate($arguments))
		{
			return $this->validation_error;
		}
		$is_html = preg_match('/\.html?$/i',$path);

		if ($this->is_script($path) || @$url['scheme'])
		{
			if (!@$url['scheme'])
			{
				$path = ($_SERVER['HTTPS'] ? 'https://' : 'http://') .
					($url['host'] ? $url['host'] : $_SERVER['HTTP_HOST']) .
					str_replace($_SERVER['DOCUMENT_ROOT'],'',$path);
			}
			if ($fp = fopen($path,'rb'))
			{
				$ret = '';
				while (!feof($fp))
				{
					$ret .= fread($fp,1024);
				}
				fclose ($fp);
				$is_html = substr($path,-4) != '.txt';
			}
			else
			{
				$ret = lang('File %1 is not readable by the webserver !!!',$path);
			}
		}
		else
		{
			$ret = file_get_contents($path);
		}
		if ($is_html && preg_match('/<meta http-equiv="content-type" content="text\/html; ?charset=([^"]+)"/i',$ret,$parts))
		{
			$ret = translation::convert($ret,$parts[1]);
		}

		// for html use css query if given AND Zend Framework available
		if ($is_html && $arguments['css_query'] && @include_once('Zend/Dom/Query.php'))
		{
			$dom = new Zend_Dom_Query($ret);
			$ret = '';
			foreach($dom->query($arguments['css_query']) as $element)
			{
				$ret .= simplexml_import_dom($element)->asXML()."\n";
			}
		}
		else
		{
			// use given regular expression (or default) to use only part of the content
			$preg = $arguments['preg'];
			// html default: only use what's between the body tags
			if ($is_html && empty($preg)) $preg = self::GRAB_BODY;

			if (!empty($preg))
			{
				$ret = preg_replace($preg, empty($arguments['replace']) ? '$1' : $arguments['replace'], $ret);
			}
		}

		// replace images and links with correct host
		if ($is_html && ($url['scheme'] == 'http' || $url['scheme'] == 'https'))
		{
			$ret = strtr($ret,array(
				'src="/' => 'src="'.$url['scheme'].'://'.$url['host'].'/',
				'href="/' => 'href="'.$url['scheme'].'://'.$url['host'].'/',
			));
		}

		if(substr($path,-4) == '.txt')
		{
			$ret = "<pre>\n$ret\n</pre>\n";
		}
		if (isset($cache_token))
		{
			$ok = egw_cache::setInstance('sitemgr', $cache_token, $ret, (int)$arguments['cache_time']);
		}
		return $ret;
	}

	/**
	 * test if $path lies within the webservers document-root
	 *
	 * @param string $path
	 * @return boolean
	 */
	function in_docroot($path)
	{
		$docroots = array(EGW_SERVER_ROOT,$_SERVER['DOCUMENT_ROOT']);
		$path = realpath($path);

		foreach ($docroots as $docroot)
		{
			$len = strlen($docroot);

			if ($docroot == substr($path,0,$len))
			{
				$rest = substr($path,$len);

				if (!strlen($rest) || $rest[0] == DIRECTORY_SEPARATOR)
				{
					return True;
				}
			}
		}
		return False;
	}

	/**
	 * Check if url refers to a script
	 *
	 * @param string $url
	 * @return boolean
	 */
	function is_script($url)
	{
		$url = parse_url($url);

		return preg_match('/\.(php.?|pl|py)$/i',$url['path']);
	}

	/**
	 * Validate given parameters: url or path and regular expression
	 *
	 * @see Module::validate()
	 * @param array &$data arguments of block
	 * @return boolean
	 */
	function validate(&$data)
	{
		// check if regular expression contains /e modifier, we can NOT allow that!
		if (!empty($data['preg']))
		{
			$parts = explode($data['preg'][0],$data['preg']);
			if (strpos(array_pop($parts),'e') !== false)
			{
				$this->validation_error = lang('Regular expression modifier "%1" in "%2" is NOT allowed!','e',htmlspecialchars($data['preg']));
				return false;
			}
		}
		$url = parse_url($data['filepath']);
		$allow_url_fopen = ini_get('allow_url_fopen');

		if ($url['scheme'] || $this->is_script($data['filepath']) && !$allow_url_fopen)
		{
			if (!$allow_url_fopen)
			{
				$this->validation_error = lang("Can't open an URL or execute a script, because allow_url_fopen is not set in your php.ini !!!");
				return false;
			}
			return True;
		}
		if (!is_readable($url['path']))
		{
			$this->validation_error = lang('File %1 is not readable by the webserver !!!',$data['filepath']);
			return false;
		}
		if (!$this->in_docroot($data['filepath']))
		{
			$this->validation_error = lang('File %1 is outside the docroot of the webserver !!!<br>This module does NOT allow - for security reasons - to open files outside the docroot.',$data['filepath']);
			return false;
		}
		return true;
	}
}
