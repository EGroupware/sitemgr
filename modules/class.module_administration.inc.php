<?php 

class module_administration extends Module
{
	function module_administration()
	{
		$this->arguments = array();
		$this->properties = array();
		$this->title = lang('Administration');
		$this->description = lang('This module presents a link back to the sitemgr\'s administration menu. It is meant for registered users');
	}

	function get_content(&$arguments,$properties)
	{
			return '&nbsp;&nbsp;<strong><big>&middot;</big></strong><a href="'.phpgw_link('/sitemgr/index.php').'">' . lang('Content Manager') . '</a>';
	}

}
