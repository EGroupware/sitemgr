<?php
	/**
	* sitemgr - search show module
	*
	* @link http://www.egroupware.org
	* @author Jose Luis Gordo Romero <jgordor@gmail.com>
	* @package sitemgr
	* @copyright Jose Luis Gordo Romero <jgordor@gmail.com>
	* @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
	* @version $Id$
	*/
	
class module_searchshow extends Module
{
	function module_searchshow()
	{
		$this->arguments = array();
		$this->properties = array();
		$this->title = lang('Show Search Results');
		$this->description = lang('This shows the search results');
	}

	function get_content(&$arguments,$properties)
	{
		$search_result = $arguments['search_result'];
		$content = $search_result['result'];
		
		return $content;
	}

}
?>
