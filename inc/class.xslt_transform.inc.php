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

class xslt_transform
{
	var $arguments;

	function xslt_transform($xsltfile,$xsltparameters=NULL)
	{
		$this->xsltfile = $xsltfile;
		$this->xsltparameters = $xsltparameters;
	}

	function apply_transform($title,$content)
	{
		$xh = xslt_create();
		$xsltarguments = array('/_xml' => $content);
		$result = xslt_process($xh, 'arg:/_xml', $this->xsltfile, NULL, $xsltarguments,$this->xsltparameters);
		xslt_free($xh);
		return $result;
	}
}
