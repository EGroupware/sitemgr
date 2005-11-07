<?php
/**************************************************************************\
* eGroupWare SiteMgr - Web Content Management                              *
* Module : download                                                        *
* Author : Cornelius Weiss <egw@von-und-zu-weiss.de>                        *
*          based on an old SiteMgr module                                  *
* http://www.egroupware.org                                                *
* --------------------------------------------                             *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id$ */

class module_download extends Module 
{
	function module_download() 
	{
		$this->arguments = array (
			'format' => array (
				'type'  => 'select', 
				'label' => lang('Choose a format'), 
				'options' => array (
					'file' => lang('Single file download'), 
					'dir' => lang('Show contents of a directory'), 
					'dirnsub' => lang('Show contents of a directory with subdirectories'),
				),
			), 
			'showpath' => array (
				'type' => 'checkbox', 
				'label' => lang('show path?'),
			), 
			'path' => array (
				'type' => 'textfield', 
				'label' => lang('The path to the file to be downloaded'),
			), 
			'file' => array (
				'type' => 'textfield', 
				'label' => lang('The file to be downloaded').' '.lang('(only used in case of single file)'),
			), 
			'text' => array (
				'type' => 'textfield', 
				'label' => lang('The text for the link, if empty the module returns the raw URL (without a link)').' '.
					lang('(only used in case of single file)'),
			), 
			'op' => array (
				'type' => 'select', 
				'label' => lang('Should the file be viewed in the browser or downloaded'), 
				'options' => array (
					1 => lang('viewed'), 
					2 => lang('downloaded'),
				),
			),
		);
		$this->post = array (
			'subdir' => array ('type' => 'textfield'),
		);
		$this->get = array ('subdir');
		$this->title = lang('File download');
		$this->description = lang('This module create a link for downloading a file(s) from the VFS');
	}

	function get_content(&$arguments, $properties) 
	{
		if ($arguments['op'] == 2) 
		{
			$linkdata['download'] = 1;
		}
		if (substr($arguments['path'],-1) == '/') 
		{
			$arguments['path'] = substr($arguments['path'], 0, -1);
		}
		$linkdata['path'] = rawurlencode(base64_encode($arguments['path']));
		$linkdata['menuaction'] = 'filemanager.uifilemanager.view';

		switch ($arguments['format']) 
		{
			case 'dirnsub' :
				if ($arguments['subdir']) {
					$arguments['path'] = $arguments['path'].'/'.$arguments['subdir'];
				}
				if ($arguments['showpath']) {
					$out = lang('Path').': '.$arguments['path'].'<hr>';
				}

			case 'dir' :
				$this->vfs =& Createobject('phpgwapi.vfs');
				$data = array (
					'string' => $arguments['path'], 
					'relatives' => array (RELATIVE_ROOT), 
					'checksubdirs' => false,
					//'mime'	=> ,
					'nofiles' => false,
				);
				$ls_dir = $this->vfs->ls($data);

				$out .= '<table class="moduletable">
						<tr>
							<td width="1%">'./*mime png*/ ''.'</td>
							<td>'.lang('Filename').'</td>
							<td>'.lang('Comment').'</td>
							<td>'.lang('Size').'</td>
							<td>'.lang('Date').'</td>
							<td>'./*action*/ ''.'</td>
						</tr>
						<tr><td height="1px" colspan="6"><hr></td></tr>';

				if ($arguments['subdir'] && $arguments['format'] == 'dirnsub') 
				{
					$out .= '<tr>
							<td>..</td>
							<td><a href="'.$this->link(array ('subdir' => strrchr($arguments['subdir'], '/') ? 
								substr($arguments['subdir'], 0, strlen($arguments['subdir']) - strlen(strrchr($arguments['subdir'], '/'))) :
								 false)).'">'.lang('parent directory').'</a>
							</td><td></td><td></td><td></td><td></td>
						</tr>';
				}

				foreach ($ls_dir as $num => $file) 
				{
					if ($file['mime_type'] == 'Directory') 
					{
						if ($arguments['format'] == 'dirnsub') 
						{
							$out .= '<tr>
									<td>'.$this->mime_icon($file['mime_type']).'</td>
									<td><a href="'.$this->link(array ('subdir' => $arguments['subdir'] ? 
										$arguments['subdir'].'/'.$file['name'] : $file['name'])).'">'.$file['name'].'</a>
									</td>
									<td>'.$file['comment'].'</td>
									<td>'.$file['size'].'</td>
									<td>'. ($file['modified'] ? $file['modified'] : $file['created']).'</td>
									<td></td>
								</tr>';
						}
						unset ($ls_dir[$num]);
					}
				}

				foreach ($ls_dir as $num => $file) 
				{
					$linkdata['file'] = rawurlencode(base64_encode($file['name']));
					$out .= '<tr>
							<td>'.$this->mime_icon($file['mime_type']).'</td>
							<td><a href="'.phpgw_link('/index.php', $linkdata).'">'.$file['name'].'</a></td>
							<td>'.$file['comment'].'</td>
							<td>'.$file['size'].'</td>
							<td>'. ($file['modified'] ? $file['modified'] : $file['created']).'</td>
							<td></td>
						</tr>';
				}
				$out .= '</table>';
				return $out;

			case 'file' :
			default :
				$linkdata['file'] = rawurlencode(base64_encode($arguments['file']));
				return $arguments['text'] ? ('<a href="'.phpgw_link('/index.php', $linkdata).'">'.
					$arguments['text'].'</a>') : phpgw_link('/index.php', $linkdata);
		}
	}

	function mime_icon($mime_type, $size = 16) 
	{
		if (!$mime_type) $mime_type = 'unknown';
		$mime_type = str_replace('/', '_', $mime_type);

		$img = $GLOBALS['phpgw']->common->image('filemanager', 'mime'.$size.'_'.strtolower($mime_type));
		if (!$img) $img = $GLOBALS['phpgw']->common->image('filemanager', 'mime'.$size.'_unknown');
		$icon = '<img src="'.$img.' "alt="'.lang($mime_type).'" />';

		return $icon;
	}
}
