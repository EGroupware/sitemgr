<?php

class module_download extends Module 
{
	function module_download()
	{
		$this->arguments = array(
			'path' => array(
				'type' => 'textfield', 
				'label' => 'The path to the file to be downloaded'
			),
			'file' => array(
				'type' => 'textfield', 
				'label' => 'The file to be downloaded'
			),
			'text' => array(
				'type' => 'textfield',
				'label' => 'The text for the link, if empty the filename is used'
			),
			'op' => array(
				'type' => 'select',
				'label' => 'Should the file be viewed in the browser or downloaded',
				'options' => array(1 => 'viewed', 2 => 'downloaded')
			)
		);
		$this->post = array('name' => array('type' => 'textfield'));
		$this->session = array('name');
		$this->title = "File download";
		$this->description = "This module create a link for downloading a file from the VFS";
	}

	function get_content(&$arguments,$properties) 
	{
		$linkdata['path'] = rawurlencode(base64_encode($arguments['path']));
		if ($arguments['op'] == 2)
		{
			$linkdata['download'] = 1;
			$linkdata['fileman[0]'] = rawurlencode(base64_encode($arguments['file']));
		}
		else
		{
			$linkdata['op'] = rawurlencode(base64_encode('view'));
			$linkdata['file'] = rawurlencode(base64_encode($arguments['file']));
		}
		return '<a href="' . 
			phpgw_link('/phpwebhosting/index.php',$linkdata) . 
			'">' .
			($arguments['text'] ? $arguments['text'] : $arguments['file']) .
			'</a>';
	}
}