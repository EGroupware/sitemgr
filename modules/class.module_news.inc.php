<?php 

	class news_transform
	{
		function news_transform(&$template)
		{
			$this->template = $template;
		}

		function apply_transform($title,$content)
		{
			$result ='';
			while (list(,$newsitem) = @each($content))
			{
				$this->template->set_var(array(
					'news_title' => $newsitem['subject'],
					'news_submitter' => $GLOBALS['phpgw']->accounts->id2name($newsitem['submittedby']),
					'news_date' => $GLOBALS['phpgw']->common->show_date($newsitem['submissiondate']),
					'news_content' => $newsitem['content']
				));
				$result .= $this->template->parse('out','news');
			}
			return $result;
		}
	}

	class module_news extends Module
	{
		function module_news()
		{
			//specification of options is postponed into the get_user_interface function
			$this->arguments = array('category' => array('type' => 'select', 'label' => lang('Choose a category'), 'options' => array()));
			$this->properties = array();
			$this->title = lang('News module');
			$this->description = ('This module is just a first trial of hooking news_admin into sitmgr\'s new architecture.');
		}

		function get_user_interface()
		{
			//we could put this into the module's constructor, but by putting it here, we make it execute only when the block is edited,
			//and not when it is generated for the web site, thus speeding the latter up slightly
			$cat = createobject('phpgwapi.categories','','news_admin');
			$cats = $cat->return_array('mains',0,False);
			$cat_ids = array(0 => lang('Mains'));
			while (list(,$category) = @each($cats))
			{
				$cat_ids[$category['id']] = $category['name'];
			}
			$this->arguments['category']['options'] = $cat_ids;
			return parent::get_user_interface();
		}

		function set_block($block,$produce=False)
		{
			parent::set_block($block,$produce);
			if ($produce)
			{
				$t = Createobject('phpgwapi.Template');
				$t->set_root($this->find_template_dir());
				$t->set_file('news','newsblock.tpl');
				$this->add_transformer(new news_transform($t));
			}
		}

		function get_content(&$arguments,$properties)
		{
			$bonews = CreateObject('news_admin.bonews');
			return $bonews->get_NewsList($arguments['category'], false);
		}
	}
