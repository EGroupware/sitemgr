<?php
	/**
	* sitemgr - search module
	*
	* @link http://www.egroupware.org
	* @author Jose Luis Gordo Romero <jgordor@gmail.com>
	* @package sitemgr
	* @copyright Jose Luis Gordo Romero <jgordor@gmail.com>
	* @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
	* @version $Id$
	*/
	
class module_search extends Module
{
	function module_search()
	{
		$this->arguments = array();
		$this->properties = array();
		$this->title = lang('Search');
		$this->description = lang('This module search throw the content (only Category/Page names and html content)');
	}

	function get_content(&$arguments,$properties)
	{
		$content = '<form name="search" method="post" action="'.sitemgr_link2('/index.php').'">' . "\n".
			lang('Search').':&nbsp <input type="text" name="searchword">'.
			'&nbsp;<input type="submit" name="search" value="'.lang('Find').'">';
		//'<a href="'.sitemgr_link2('/index.php','category_id='.$parent).'" title="'.$p->description.'">'.$p->name.'</a>';
		return $content;
	}

}
?>
