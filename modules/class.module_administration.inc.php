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
		$content = '<form name="modeselect" method="post">' .
			'<select onChange="location.href=this.value" name="mode">';
		foreach(array(
			'Production' => lang('Production mode'),
			'Draft'      => lang('Draft mode'),
			'Edit'       => lang('Edit mode')) as $mode => $label)
		{
			$selected = ($GLOBALS['sitemgr_info']['mode'] == $mode) ? ' selected="selected"' : '';
			$content .=	'<option value="' .$this->link(array(),array('mode'=>$mode)) .'"' . $selected  . '>' . $label . '</option>';
		}
		$content .= '</select></form>' .
			'&nbsp;&nbsp;<strong><big>&middot;</big></strong><a href="' . phpgw_link('/sitemgr/index.php') .
			'">' . lang('Content Manager') . '</a>';
		return $content;
	}

}
