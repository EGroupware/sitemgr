<?php 

class Transformer
{
	var $arguments;

	function Transformer($arguments=array())
	{
		$this->arguments = $arguments;
	}

	function apply_transform($title,$content)
	{
		return $content;
	}
}


class Module 
{
	var $validation_error;
	var $transformer_chain;
	var $arguments;
	var $properties;
	var $block;

	function Module()
	{
		
		$this->arguments = array();
		$this->properties = array();
		$this->transformer_chain = array();
		$this->title = "Standard module";
		$this->description = "Parent class that all modules should extend";
	}

	function add_transformer($transformer)
	{
		$this->transformer_chain[] = $transformer;
	}

	//before calling the functions get_user_interface, get_output,
	//the function set_block is used, so that we know in what scope we are, know the arguments, 
	//and can retrieve the properties
	//this function can be overriden (but do not forget to call parent::set_block) in order to do some configuration
	//that depends on the blocks arguments
	//the produce argument is set when content is generated, so we can do some stuff we do not need when editing the block
	function set_block($block,$produce=False)
	{
		if ($produce)
		{
			if ($this->session)
			{
				$sessionarguments = $GLOBALS['phpgw']->session->appsession($this->name,'sitemgr-site');
				while (list(,$argument) = @each($this->session))
				{
					if (isset($sessionarguments[$argument]))
					{
						$block->arguments[$argument] = $sessionarguments[$argument];
					}
				}
			}
			while (list(,$argument) = @each($this->get))
			{
				if (isset($_GET[$this->name][$argument]))
				{
					$block->arguments[$argument] = $_GET[$this->name][$argument];
				}
			}
			//contrary to $this->get, cookie and session, the argument name is the key in $this->post because this array also
			//defines the form element
			while (list($argument,) = @each($this->post))
			{
				if (isset($_POST[$this->name][$argument]))
				{
					$block->arguments[$argument] = $_POST[$this->name][$argument];
				}
			}
			while (list(,$argument) = @each($this->cookie))
			{
				if (isset($_COOKIE[$this->name][$argument]))
				{
					$block->arguments[$argument] = $_COOKIE[$this->name][$argument];
				}
			}
		}
		$this->block = $block;
	}

	function link($modulevars)
	{
		while (list($key,$value) = @each($modulevars))
		{
			$extravars[$this->name.'['.$key.']'] = $value;
		}
		$extravars['page_id'] = $this->block->page_id;
		return sitemgr_link2('/index.php',$extravars);
	}

	function get_properties($cascading=True)
	{
		if ($cascading)
		{
			return $GLOBALS['Common_BO']->modules->getcascadingmoduleproperties(
				$this->block->module_id,
				$this->block->area,
				$this->block->cat_id,
				$this->block->app_name,
				$this->block->module_name
			);
		}
		else
		{
			return $GLOBALS['Common_BO']->modules->getmoduleproperties(
				$this->block->module_id,
				$this->block->area,
				$this->block->cat_id
			);
		}
	}

	function get_user_interface()
	{
		//if you override this function you can fetch properties and adapt the interface accordingly
		//$properties = $this->get_properties();
		$interface = array();
		reset($this->arguments);
		while (list($key,$input) = @each($this->arguments))
		{
			$elementname = ($input['i18n'] ? ('element[i18n][' .$key . ']') : ('element[' .$key . ']'));
			//arrays of input elements are only implemented for the user interface
			if ($input['type'] == 'array')
			{
				$i = 0;
				while (isset($input[$i]))
				{
					$element['label'] = $input[$i]['label'];
					$element['form'] = $this->build_input_element($input[$i],$this->block->arguments[$key][$i],$elementname.'[]');
					$interface[] = $element;
					$i++;
				}
			}
			else
			{
				$element['label'] = $input['label'];
				$element['form'] = $this->build_input_element($input,$this->block->arguments[$key],$elementname);
				$interface[] = $element;
			}
		}
		return $interface;
	}


	function get_translation_interface($fromblock,$toblock)
	{
		//if you override this function you can fetch properties and adapt the interface accordingly
		//$properties = $this->get_properties();
		$interface = array();
		reset($this->arguments);
		while (list($key,$input) = @each($this->arguments))
		{
			if ($input['i18n'])
			{
				$elementname = 'element[i18n][' .$key . ']';
				//arrays of input elements are only implemented for the user interface
				if ($input['type'] == 'array')
				{
					$i = 0;
					while (isset($input[$i]))
					{
						$element['label'] = $input[$i]['label'];
						$element['form'] = $this->build_input_element($input[$i],$toblock->arguments[$key][$i],$elementname.'[]');
						$element['value'] = $fromblock->arguments[$key][$i];
						$interface[] = $element;
						$i++;
					}
				}
				else
				{
					$element['label'] = $input['label'];
					$element['form'] = $this->build_input_element($input,$toblock->arguments[$key],$elementname);
					$element['value'] = $fromblock->arguments[$key];
					$interface[] = $element;
				}
			}
		}
		return $interface;
	}


	function get_admin_interface()
	{
		//we set the blockarguments to the properties so that the build_input_element function can retrieve the right defaults
		$properties = $this->get_properties(False);
		$elementname = 'element[' .$key . ']';
		$interface = array();
		while (list($key,$input) = @each($this->properties))
		{
			$element['label'] = $input['label'];
			$element['form'] = $this->build_input_element($input,$properties[$key],$elementname);
			$interface[$key] = $element;
		}
		return $interface;
	}

	function build_post_element($key,$default=False)
	{
		return $this->build_input_element(
			$this->post[$key],
			($default !== False) ? $default : $this->block->arguments[$key],
			($this->name . '[' . $key . ']')
		);
	}

	function build_input_element($input,$default,$elementname)
	{
		$trans = array('{' => '&#123;', '}' => '&#125;');
		if ($default)
		{
			$default = strtr($GLOBALS['phpgw']->strip_html($default),$trans);
		}
		$paramstring = '';
		while (list($param,$value) = @each($input['params']))
		{
			$paramstring .= $param . '="' . $value . '" ';
		}
		$inputdef = $paramstring . ' name="' . $elementname . '"';
		switch($input['type'])
		{
			case 'textarea':
				return '<textarea ' . $inputdef . '>' . $default . '</textarea>';
			case 'textfield':
				return '<input type="text" ' . $inputdef . ' value ="' . $default . '" />';
			case 'checkbox':
				return '<input type="checkbox" ' . $inputdef . ($default ? 'checked="checked"' :'') . '" />';
			case 'select':
				$select = '<select name="' . $elementname . '">';
				foreach ($input['options'] as $value => $display)
				{
					$selected='';
					if ($default == $value)
					{
						$selected = 'selected="selected"';
					}
						$select .= '<option value="'. $value . '" ' . $selected . '>' . $display . '</option>';
				}
				$select .= '</select>';
				return $select;
			case 'submit':
				return '<input type="submit" ' . $inputdef .' value ="' . $input['value'] . '" />';
			case 'image':
				return '<input type="image" ' . $inputdef .' src ="' . $input['src'] . '" />';
		}
	}

	function validate(&$data)
	{
		return true;
	}

	//never call get_content directly, get_output takes care of passing it the right arguments
	function get_content(&$arguments,$properties)
	{

	}

	function get_output($type='html')
	{
		$content= $this->get_content($this->block->arguments,$this->get_properties());
		if (!$content)
		{
			return '';
		}
		if ($type == 'raw')
		{
			return $content;
		}
		else
		{
			if ($this->transformer_chain)
			{
				foreach ($this->transformer_chain as $transformer)
				{
					$content = $transformer->apply_transform($this->block->title,$content);
				}
			}
			//store session variables
			if ($this->session)
			{
				reset($this->session);
				while (list(,$argument) = each($this->session))
				{
					if (isset($this->block->arguments[$argument]))
					{
						$sessionarguments[$argument] = $this->block->arguments[$argument];
					}
				}
				$GLOBALS['phpgw']->session->appsession($this->name,'sitemgr-site',$sessionarguments);
			}
			return $content;
		}
	}
}
