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

class module_filecontents extends Module 
{
	function module_filecontents()
	{
		$this->arguments = array(
			'filepath' => array(
				'type' => 'textfield', 
				'label' => lang('The complete path to the file to be included')
			)
		);
		$this->title = lang('File contents');
		$this->description = lang('This module includes the contents of a file (readable by the webserver !)');
	}

	function get_content(&$arguments,$properties)
	{
		if (empty($arguments['filepath']))
		{
			return '';
		}
		if ($this->validate($arguments))
		{
			return implode('', file($arguments['filepath']));
		}
		else
		{
			return $this->validation_error;
		}
	}

	function validate(&$data)
	{
		if (!is_readable($data['filepath']))
		{
			$this->validation_error = lang('File %1 is not readable by the webserver !!!',$data['filepath']);
			return false;
		}
		else
		{
			return true;
		}
	}
}
