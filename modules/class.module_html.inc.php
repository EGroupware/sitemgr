<?php

	class module_html extends Module
	{
		function module_html()
		{
			$this->i18n = true;
			$this->arguments = array(
				'htmlcontent' => array(
					'type' => 'htmlarea',
					'label' => lang('Enter the block content here'),
					'large' => True,	// show label above content
					'i18n' => True,
					'params' => Array('style' => 'width:100%; min-width:500px; height:300px')
				)
			);
			$this->properties = array('striphtml' => array('type' => 'checkbox', 'label' => lang('Strip HTML from block content?')));
			$this->title = lang('HTML module');
			$this->description = lang('This module is a simple HTML editor');
		}

		function get_content(&$arguments,$properties)
		{
			return $properties['striphtml'] ? $GLOBALS['phpgw']->strip_html($arguments['htmlcontent']) : $arguments['htmlcontent'];
		}
	}
