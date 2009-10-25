<?php
/**
 * sitemgr - Download from VFS block
 *
 * @link http://www.egroupware.org
 * @author Cornelius Weiss <egw@von-und-zu-weiss.de> based on old sitemgr module
 * @author Ralf Becker <RalfBecker(at)outdoor-training.de> updated to new vfs
 * @package sitemgr
 * @subpackage modules
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

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
					'recursive' => lang('Show files including the ones from subdirectories'),
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
			'upload' => array(
				'type' => 'checkbox',
				'label' => lang('Show a file upload (if user has write rights to current directory)'),
			),
/*			disabled, because currently not working
			'op' => array (
				'type' => 'select',
				'label' => lang('Should the file be viewed in the browser or downloaded'),
				'options' => array (
					1 => lang('viewed'),
					2 => lang('downloaded'),
				),
			),
*/
		);
		$this->post = array (
			'subdir' => array ('type' => 'textfield'),
		);
		$this->get = array ('subdir','uploading');
		$this->title = lang('File download');
		$this->description = lang('This module create a link for downloading a file(s) from the VFS');
	}

	function get_content(&$arguments, $properties)
	{
		translation::add_app('filemanager');

		if (substr($arguments['path'],-1) == '/')
		{
			$arguments['path'] = substr($arguments['path'], 0, -1);
		}
		$out = '';
		switch ($arguments['format'])
		{
			case 'dirnsub' :
				if ($arguments['subdir'])
				{
					$arguments['path'] = $arguments['path'].'/'.$arguments['subdir'];
				}
				// fall through
			case 'dir' :
			case 'recursive':
				if (!egw_vfs::file_exists($arguments['path']) || !egw_vfs::is_readable($arguments['path']))
				{
					return '<p style="color: red;"><i>'.lang('The requested path %1 is not available.',htmlspecialchars($query['path']))."</i></p>\n";
				}
				//$out .= '<pre>'.print_r($arguments,true)."</pre>\n";
				if ($arguments['uploading'] && $arguments['upload'] && egw_vfs::is_writable($arguments['path']))
				{
					foreach((array)$_FILES['upload'] as $name => $data)
					{
						$upload[$name] = $data[$this->block->id];
					}
					$to = $arguments['path'].'/'.$upload['name'];
					if (is_uploaded_file($upload['tmp_name']) &&
						(egw_vfs::is_writable($arguments['path']) || egw_vfs::is_writable($to)) &&
						copy($upload['tmp_name'],egw_vfs::PREFIX.$to))
					{
						$out .= '<p style="color: red;"><i>'.lang('File successful uploaded.')."</i></p>\n";
					}
					else
					{
						$out .= '<p style="color: red;"><i>'.lang('Error uploading file!').'<br />'.filemanager_ui::max_upload_size_message()."</i></p>\n";
					}
				}
				if ($arguments['showpath'])
				{
					$out .= '<p>'.lang('Path').': '.htmlspecialchars($arguments['path']).'</p><hr />';
				}
				$ls_dir = egw_vfs::find($arguments['path'],array(
					'need_mime' => true,
					'maxdepth' => $arguments['format'] != 'recursive' ? 1 : null,
					'type' => $arguments['format'] != 'dirnsub' ? 'f' : null,
				),true);

				$out .= '<table class="moduletable">
						<tr>
							<td width="1%">'./*mime png*/ ''.'</td>
							<td>'.lang('Filename').'</td>
							<!--td>'.lang('Comment').'</td-->
							<td align="right">'.lang('Size').'</td>
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
							</td><!--td></td--><td></td><td></td><td></td>
						</tr>';
				}

				$dateformat = $GLOBALS['egw_info']['user']['preferences']['common']['dateformat'].
					($GLOBALS['egw_info']['user']['preferences']['common']['timeformat'] != 12 ? ' H:i' : 'h:ia');

				foreach ($ls_dir as $num => $file)
				{
					if ($file['mime'] == egw_vfs::DIR_MIME_TYPE)
					{
						if ($arguments['format'] == 'dirnsub' && $file['name'])
						{
							$out .= '<tr>
									<td>'.egw_vfs::mime_icon($file['mime'],false).'</td>
									<td><a href="'.$this->link(array ('subdir' => $arguments['subdir'] ?
										$arguments['subdir'].'/'.$file['name'] : $file['name'])).'">'.$file['name'].'</a>
									</td>
									<!--td>'.$file['comment'].'</td-->
									<td align="right">'./*egw_vfs::hsize($file['size']).*/'</td>
									<td>'. date($dateformat,$file['mtime'] ? $file['mtime'] : $file['ctime']).'</td>
									<td></td>
								</tr>';
						}
						unset ($ls_dir[$num]);
					}
				}

				foreach ($ls_dir as $num => $file)
				{
					$link = $GLOBALS['egw']->link(egw_vfs::download_url($arguments['path'].'/'.$file['name'],$arguments['op'] == 2));
					$out .= '<tr>
							<td>'.egw_vfs::mime_icon($file['mime'],false).'</td>
							<td><a href="'.$link.'">'.$file['name'].'</a></td>
							<!--td>'.$file['comment'].'</td-->
							<td align="right">'.egw_vfs::hsize($file['size']).'</td>
							<td>'. date($dateformat,$file['mtime'] ? $file['mtime'] : $file['ctime']).'</td>
							<td></td>
						</tr>';
				}
				$out .= '</table>';

				if ($arguments['upload'] && egw_vfs::is_writable($arguments['path']))
				{
					$out .= '<hr />';
					$out .= '<form name="upload" action="'.$this->link(array(
						'subdir' => $arguments['subdir'],
						'uploading' => 1,	// mark form submit as fileupload, to be able to detect when it failed (eg. because of upload limits)
					)).'" method="POST" enctype="multipart/form-data">';
					$out .= html::input('upload['.$this->block->id.']','','file',' onchange="this.form.submit();"');
					$out .= "</form>\n";
				}
				return $out;

			case 'file' :
			default :
				$link = $GLOBALS['egw']->link(egw_vfs::download_url($arguments['path'].'/'.$arguments['file'],$arguments['op'] == 2));
				return $arguments['text'] ? ('<a href="'.$link.'">'.$arguments['text'].'</a>') : $link;
		}
	}
}
