<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

class module_validator extends Module
{
  function module_validator()
	{
		$this->arguments = array();
		$this->properties = array();
		$this->title = lang('Validator');
		$this->description = lang('Helps you respect HTML/XHTML standards.');
	}

	function get_content(&$arguments,$properties)
	{
    $content = '    <p>'."\n";
    $content .= '      <a href="http://validator.w3.org/check?uri=referer"><img border="0"'."\n";
    $content .= '          src="http://www.w3.org/Icons/valid-xhtml11"'."\n";
    $content .= '          alt="Valid XHTML 1.1!" height="31" width="88"></a>'."\n";
    $content .= '    </p>'."\n";

		return $content;
	}
}
