<?php

class module_administration extends Module
{
	function module_administration()
	{
		$this->arguments = array();
		$this->properties = array();
		$this->title = lang('Administration');
		$this->description = lang('This module is a selectbox to change the mode (production, draft or edit) plus a link back to SiteMgr and to log out. It is meant for registered users only');
	}

	function get_content(&$arguments,$properties)
	{
		$content = '<form name="modeselect" method="post">' . "\n".
			'<select onChange="location.href=this.value" name="mode">'."\n";
		foreach(array(
			'Production' => lang('Production mode'),
			'Draft'      => lang('Draft mode'),
			'Edit'       => lang('Edit mode')) as $mode => $label)
		{
			$selected = ($GLOBALS['sitemgr_info']['mode'] == $mode) ? ' selected="selected"' : '';
			$content .=	'<option value="' .$this->link(array(),array('mode'=>$mode)) .'"' . $selected  . '>' . $label . "</option>\n";
		}
		$content .= "</select>\n</form>\n" .
			'<p>&nbsp;&nbsp;<strong><big>&middot;</big></strong><a href="' . phpgw_link('/sitemgr/') .
			'">' . lang('Content Manager') . "</a><br />\n".
			'&nbsp;&nbsp;<strong><big>&middot;</big></strong><a href="' . phpgw_link('/logout.php') .
			'">' . lang('Logout') . "</a></p>\n";
		return $content;
	}

}
