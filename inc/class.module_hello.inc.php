<?php

class module_hello extends Module 
{
	function module_hello()
	{
		$this->arguments = array(
			'name' => array(
				'type' => 'textfield', 
				'label' => 'The person to say hello to'
			)
		);
		$this->post = array('name' => array('type' => 'textfield'));
		$this->session = array('name');
		$this->title = "Hello world";
		$this->description = "This is a simple sample module";
	}

	function get_content(&$arguments,$properties) 
	{
		$this->validate($arguments);
		return lang('Hello') . ' ' . $arguments['name'] . '<br><form action="' . 
			$_SERVER['REQUEST_URI'] . '" method="post">' . 
			$this->build_post_element('name',lang('Enter a name')) .
			'</form>';
	}

	function validate(&$data)
	{
		if (preg_match("/[[:upper:]]/",$data['name']))
		{
			$data['name'] = strtolower($data['name']);
			$this->validation_error = "Name has been translated to lower case";
		}
		return true;
	}
}